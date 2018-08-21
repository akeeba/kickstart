<?php
/**
 * Akeeba Restore
 * A JSON-powered JPA, JPS and ZIP archive extraction library
 *
 * @copyright Copyright (c)2008-2018 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL v2 or - at your option - any later version
 * @package     akeebabackup
 * @subpackage  kickstart
 */

/**
 * A timing safe equals comparison
 *
 * @param   string  $safe  The internal (safe) value to be checked
 * @param   string  $user  The user submitted (unsafe) value
 *
 * @return  boolean  True if the two strings are identical.
 *
 * @see     http://blog.ircmaxell.com/2014/11/its-all-about-time.html
 */
function timingSafeEquals($safe, $user)
{
	$safeLen = strlen($safe);
	$userLen = strlen($user);

	if ($userLen != $safeLen)
	{
		return false;
	}

	$result = 0;

	for ($i = 0; $i < $userLen; $i++)
	{
		$result |= (ord($safe[$i]) ^ ord($user[$i]));
	}

	// They are only identical strings if $result is exactly 0...
	return $result === 0;
}

/**
 * The Master Setup will read the configuration parameters from restoration.php or
 * the JSON-encoded "configuration" input variable and return the status.
 *
 * @return bool True if the master configuration was applied to the Factory object
 */
function masterSetup()
{
	// ------------------------------------------------------------
	// 1. Import basic setup parameters
	// ------------------------------------------------------------

	$ini_data = null;

	// In restore.php mode, require restoration.php or fail
	if (!defined('KICKSTART'))
	{
		// This is the standalone mode, used by Akeeba Backup Professional. It looks for a restoration.php
		// file to perform its magic. If the file is not there, we will abort.
		$setupFile = 'restoration.php';

		if (!file_exists($setupFile))
		{
			AKFactory::set('kickstart.enabled', false);

			return false;
		}

		// Load restoration.php. It creates a global variable named $restoration_setup
		require_once $setupFile;

		$ini_data = $restoration_setup;

		if (empty($ini_data))
		{
			// No parameters fetched. Darn, how am I supposed to work like that?!
			AKFactory::set('kickstart.enabled', false);

			return false;
		}

		AKFactory::set('kickstart.enabled', true);
	}
	else
	{
		// Maybe we have $restoration_setup defined in the head of kickstart.php
		global $restoration_setup;

		if (!empty($restoration_setup) && !is_array($restoration_setup))
		{
			$ini_data = AKText::parse_ini_file($restoration_setup, false, true);
		}
		elseif (is_array($restoration_setup))
		{
			$ini_data = $restoration_setup;
		}
	}

	// Import any data from $restoration_setup
	if (!empty($ini_data))
	{
		foreach ($ini_data as $key => $value)
		{
			AKFactory::set($key, $value);
		}
		AKFactory::set('kickstart.enabled', true);
	}

	// Reinitialize $ini_data
	$ini_data = null;

	/**
	 * August 2018. Some third party developer with a dubious skill level (or complete lack thereof) wrote a piece of
	 * code which uses restore.php with an empty password (and never deleted the restoration.php file he created).
	 * According to his code comments he did this because he couldn't figure out how to make encrypted requests work,
	 * DESPITE THE FACT that com_joomlaupdate (part of Joomla! itself) has working code which does EXACTLY THAT. >:-o
	 *
	 * As a result of his actions all sites running his software have a massive vulnerability inflicted upon them. An
	 * attacker can absuse the (unlocked) restore.php to upload and install any arbitrary code in a ZIP archive,
	 * possibly overwriting core code. Discovering this problem takes a few seconds and there is code which is doing
	 * exactly that published years ago (during the active maintenance period of Joomla! 3.4, that long ago).
	 *
	 * This bit of code here detects an empty password and disables restore.php. His badly written software fails to
	 * execute and, most importantly, the unlucky users of his software will no longer have a remote code upload /
	 * remote code execution vulnerability on their sites.
	 *
	 * Remember, people, if you can't be bothered to take web application security seriously DO NOT SELL WEB SOFTWARE
	 * FOR A LIVING. There are other honest jobs you can do which don't involve using a computer in a dangerous and
	 * irresponsible manner.
	 */
	$password = AKFactory::get('kickstart.security.password', null);

	if (empty($password) || (trim($password) == '') || (strlen(trim($password)) < 10))
	{
		AKFactory::set('kickstart.enabled', false);

		return false;
	}


	// ------------------------------------------------------------
	// 2. Explode JSON parameters into $_REQUEST scope
	// ------------------------------------------------------------

	// Detect a JSON string in the request variable and store it.
	$json = getQueryParam('json', null);

	// Detect a password in the request variable and store it.
	$userPassword = getQueryParam('password', '');

	// Remove everything from the request, post and get arrays
	if (!empty($_REQUEST))
	{
		foreach ($_REQUEST as $key => $value)
		{
			unset($_REQUEST[$key]);
		}
	}

	if (!empty($_POST))
	{
		foreach ($_POST as $key => $value)
		{
			unset($_POST[$key]);
		}
	}

	if (!empty($_GET))
	{
		foreach ($_GET as $key => $value)
		{
			unset($_GET[$key]);
		}
	}

	// Authentication - Akeeba Restore 5.4.0 or later
	$password = AKFactory::get('kickstart.security.password', null);
	$isAuthenticated = false;

	/**
	 * Akeeba Restore 5.3.1 and earlier use a custom implementation of AES-128 in CTR mode to encrypt the JSON data
	 * between client and server. This is not used as a means to maintain secrecy (it's symmetrical encryption and the
	 * key is, by necessity, transmitted with the HTML page to the client). It's meant as a form of authentication, so
	 * that the server part can ensure that it only receives commands by an authorized client.
	 *
	 * The downside is that encryption in CTR mode (like CBC) is an all-or-nothing affair. This opens the possibility
	 * for a padding oracle attack (https://en.wikipedia.org/wiki/Padding_oracle_attack). While Akeeba Restore was
	 * hardened in 2014 to prevent the bulk of suck attacks it is still possible to attack the encryption using a very
	 * large number of requests (several dozens of thousands).
	 *
	 * Since Akeeba Restore 5.4.0 we have removed this authentication method and replaced it with the transmission of a
	 * very large length password. On the server side we use a timing safe password comparison. By its very nature, it
	 * will only leak the (well known, constant and large) length of the password but no more information about the
	 * password itself. See http://blog.ircmaxell.com/2014/11/its-all-about-time.html  As a result this form of
	 * authentication is many orders of magnitude harder to crack than regular encryption.
	 *
	 * Now you may wonder "how is sending a password in the clear hardier than encryption?". If you ask that question
	 * you were not paying attention. The password needs to be known by BOTH the server AND the client (browser). Since
	 * this password is generated programmatically by the server, it MUST be sent to the client by the server. If an
	 * attacker is able to intercept this transmission (man in the middle attack) using encryption is irrelevant: the
	 * attacker already knows your password. This situation also applies when the user sends their own password to the
	 * server, e.g. when logging into their site. The ONLY way to avoid security issues regarding information being
	 * stolen in transit is using HTTPS with a commercially signed SSL certificate. Unlike 2008, when Kickstart was
	 * originally written, obtaining such a certificate nowadays is trivial and costs absolutely nothing thanks to Let's
	 * Encrypt (https://letsencrypt.org/).
	 *
	 * TL;DR: Use HTTPS with a commercially signed SSL certificate, e.g. a free certificate from Let's Encrypt. Client-
	 * side cryptography does NOT protect you against an attacker (see
	 * https://www.nccgroup.trust/us/about-us/newsroom-and-events/blog/2011/august/javascript-cryptography-considered-harmful/).
	 * Moreover, sending a plaintext password is safer than relying on client-side encryption for authentication as it
	 * removes the possibility of an attacker inferring the contents of the authentication key (password) in a relatively
	 * easy and automated manner.
	 */
	if (!empty($password))
	{
		// Timing-safe password comparison. See http://blog.ircmaxell.com/2014/11/its-all-about-time.html
		if (!timingSafeEquals($password, $userPassword))
		{
			die('###{"status":false,"message":"Invalid login"}###');
		}
	}

	// No JSON data? Die.
	if (empty($json))
	{
		die('###{"status":false,"message":"Invalid JSON data"}###');
	}

	// Handle the JSON string
	$raw = json_decode($json, true);

	// Invalid JSON data?
	if (empty($raw))
	{
		die('###{"status":false,"message":"Invalid JSON data"}###');
	}

	// Pass all JSON data to the request array
	if (!empty($raw))
	{
		foreach ($raw as $key => $value)
		{
			$_REQUEST[$key] = $value;
		}
	}

	// ------------------------------------------------------------
	// 3. Try the "factory" variable
	// ------------------------------------------------------------
	// A "factory" variable will override all other settings.
	$serialized = getQueryParam('factory', null);

	if (!is_null($serialized))
	{
		// Get the serialized factory
		AKFactory::unserialize($serialized);
		AKFactory::set('kickstart.enabled', true);

		return true;
	}

	// ------------------------------------------------------------
	// 4. Try the configuration variable for Kickstart
	// ------------------------------------------------------------
	if (defined('KICKSTART'))
	{
		$configuration = getQueryParam('configuration');

		if (!is_null($configuration))
		{
			// Let's decode the configuration from JSON to array
			$ini_data = json_decode($configuration, true);
		}
		else
		{
			// Neither exists. Enable Kickstart's interface anyway.
			$ini_data = array('kickstart.enabled' => true);
		}

		// Import any INI data we might have from other sources
		if (!empty($ini_data))
		{
			foreach ($ini_data as $key => $value)
			{
				AKFactory::set($key, $value);
			}

			AKFactory::set('kickstart.enabled', true);

			return true;
		}
	}
}
