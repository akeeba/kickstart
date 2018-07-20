<?php
/**
 * Akeeba Kickstart
 * A JSON-powered archive extraction tool
 *
 * @copyright Copyright (c)2008-2018 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL v2 or - at your option - any later version
 * @package     akeebabackup
 * @subpackage  kickstart
 */

/*
    Akeeba Kickstart - The server-side archive extraction wizard
    Copyright (C) 2008-2017  Nicholas K. Dionysopoulos / AkeebaBackup.com

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

// Uncomment the following line to enable Kickstart's debug mode
//define('KSDEBUG', 1);

// =====================================================================================================================
// DO NOT MODIFY BELOW THIS LINE
// =====================================================================================================================
define('KICKSTART', 1);

if (!defined('VERSION'))
{
	define('VERSION', '##VERSION##');
}

if (!defined('KICKSTARTPRO'))
{
	define('KICKSTARTPRO', '##KICKSTARTPRO##');
}

// Used during development
if (!defined('KSDEBUG') && isset($_SERVER) && isset($_SERVER['HTTP_HOST']) && (strpos($_SERVER['HTTP_HOST'], 'local.web') !== false))
{
	define('KSDEBUG', 1);
}

define('KSWINDOWS', substr(PHP_OS, 0, 3) == 'WIN');

if (!defined('KSROOTDIR'))
{
	define('KSROOTDIR', dirname(__FILE__));
}

if (defined('KSDEBUG'))
{
	ini_set('error_log', KSROOTDIR . '/kickstart_error_log');
	if (file_exists(KSROOTDIR . '/kickstart_error_log'))
	{
		@unlink(KSROOTDIR . '/kickstart_error_log');
	}
	error_reporting(E_ALL | E_STRICT);
}
else
{
	@error_reporting(E_NONE);
}

// ==========================================================================================
// IIS missing REQUEST_URI workaround
// ==========================================================================================

/*
 * Based REQUEST_URI for IIS Servers 1.0 by NeoSmart Technologies
 * The proper method to solve IIS problems is to take a look at this:
 * http://neosmart.net/dl.php?id=7
 */

//This file should be located in the same directory as php.exe or php5isapi.dll

if (!isset($_SERVER['REQUEST_URI']))
{
	if (isset($_SERVER['HTTP_REQUEST_URI']))
	{
		$_SERVER['REQUEST_URI'] = $_SERVER['HTTP_REQUEST_URI'];
		//Good to go!
	}
	else
	{
		//Someone didn't follow the instructions!
		if (isset($_SERVER['SCRIPT_NAME']))
		{
			$_SERVER['HTTP_REQUEST_URI'] = $_SERVER['SCRIPT_NAME'];
		}
		else
		{
			$_SERVER['HTTP_REQUEST_URI'] = $_SERVER['PHP_SELF'];
		}
		if (isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING']))
		{
			$_SERVER['HTTP_REQUEST_URI'] .= '?' . $_SERVER['QUERY_STRING'];
		}
		//WARNING: This is a workaround!
		//For guaranteed compatibility, HTTP_REQUEST_URI *MUST* be defined!
		//See product documentation for instructions!
		$_SERVER['REQUEST_URI'] = $_SERVER['HTTP_REQUEST_URI'];
	}
}

// Define the cacert.pem location, if it exists
$cacertpem = KSROOTDIR . '/cacert.pem';
if (is_file($cacertpem))
{
	if (is_readable($cacertpem))
	{
		define('AKEEBA_CACERT_PEM', $cacertpem);
	}
}
unset($cacertpem);

/**
 * Loads other PHP files containing extra Kickstart features. You can do all sorts of tricks such as injecting HTML
 * and CSS code (see AKFeatureGeorgeWSpecialEdition), adding AJAX task handlers (see AKFeatureURLImport) etc.
 *
 * Feature files must follow one of the following naming conventions:
 *
 * - kickstart.SOMETHING.php
 * - script_basename.SOMETHING.php
 *
 * where script_basename is the base name of Kickstart's PHP file. If you have renamed kickstart.php to foobar.php this
 * means that feature files must be named foobar.SOMETHING.php.
 *
 * The file must contain a class whose name starts with "AKFeature". The rest of the name is irrelevant and does not
 * have to follow a convention. It is, however, prudent to name the class using something similar to the filename it is
 * stored in to preserve your sanity and avoid potential conflicts.
 *
 * The class is instantiated ONLY ONCE, when the first call to callExtraFeature() is made. Its methods are called using
 * callExtraFeature() from Kickstart's (non-user-modifiable) code.
 */
$dh = @opendir(KSROOTDIR);

if ($dh === false)
{
	return;
}

$selfBasename = basename(__FILE__, '.php');

while ($filename = readdir($dh))
{
	if (in_array($filename, array('.', '..')))
	{
		continue;
	}

	if (!is_file($filename))
	{
		continue;
	}

	// Feature files must be named either kickstart.something.php (legacy) or script_basename.something.php (preferred)
	$hasKickstartInName = substr($filename, 0, 10) == 'kickstart.';
	$hasSelfInName      = substr($filename, 0, strlen($selfBasename) + 1) == ($selfBasename . '.');

	if (!$hasKickstartInName && !$hasSelfInName)
	{
		continue;
	}

	if (substr($filename, -4) != '.php')
	{
		continue;
	}

	// We have to ignore ourselves or an unmodified copy of kickstart.php
	if (in_array($filename, array('kickstart.php', basename(__FILE__))))
	{
		continue;
	}

	// Op-code busting before loading the feature (in case it's self-modifying)
	if (function_exists('opcache_invalidate'))
	{
		opcache_invalidate($filename);
	}

	if (function_exists('apc_compile_file'))
	{
		apc_compile_file($filename);
	}

	if (function_exists('wincache_refresh_if_changed'))
	{
		wincache_refresh_if_changed(array($filename));
	}

	if (function_exists('xcache_asm'))
	{
		xcache_asm($filename);
	}

	include_once $filename;
}

// This is meant to be a temporary variable. Removing it so it doesn't pollute the top-level scope.
unset($selfBasename);