<?php
/**
 * Akeeba Restore
 * A JSON-powered JPA, JPS and ZIP archive extraction library
 *
 * @copyright   Copyright (c)2008-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL v2 or - at your option - any later version
 * @package     kickstart
 */

define('_AKEEBA_RESTORATION', 1);
defined('DS') or define('DS', DIRECTORY_SEPARATOR);

// Unarchiver run states
define('AK_STATE_NOFILE', 0); // File header not read yet
define('AK_STATE_HEADER', 1); // File header read; ready to process data
define('AK_STATE_DATA', 2); // Processing file data
define('AK_STATE_DATAREAD', 3); // Finished processing file data; ready to post-process
define('AK_STATE_POSTPROC', 4); // Post-processing
define('AK_STATE_DONE', 5); // Done with post-processing

/* Windows system detection */
if (!defined('_AKEEBA_IS_WINDOWS'))
{
	if (function_exists('php_uname'))
	{
		define('_AKEEBA_IS_WINDOWS', stristr(php_uname(), 'windows'));
	}
	else
	{
		define('_AKEEBA_IS_WINDOWS', DIRECTORY_SEPARATOR == '\\');
	}
}

// Get the file's root
if (!defined('KSROOTDIR'))
{
	define('KSROOTDIR', dirname(__FILE__));
}
if (!defined('KSLANGDIR'))
{
	define('KSLANGDIR', KSROOTDIR);
}

// Make sure the locale is correct for basename() to work
if (function_exists('setlocale'))
{
	@setlocale(LC_ALL, 'en_US.UTF8');
}

// fnmatch not available on non-POSIX systems
// Thanks to soywiz@php.net for this usefull alternative function [http://gr2.php.net/fnmatch]
if (!function_exists('fnmatch'))
{
	function fnmatch($pattern, $string)
	{
		return @preg_match(
			'/^' . strtr(addcslashes($pattern, '/\\.+^$(){}=!<>|'),
				array('*' => '.*', '?' => '.?')) . '$/i', $string
		);
	}
}

// Unicode-safe binary data length function
if (!function_exists('akstringlen'))
{
	if (function_exists('mb_strlen'))
	{
		function akstringlen($string)
		{
			return mb_strlen($string, '8bit');
		}
	}
	else
	{
		function akstringlen($string)
		{
			return strlen($string);
		}
	}
}

if (!function_exists('aksubstr'))
{
	if (function_exists('mb_strlen'))
	{
		function aksubstr($string, $start, $length = null)
		{
			return mb_substr($string, $start, $length, '8bit');
		}
	}
	else
	{
		function aksubstr($string, $start, $length = null)
		{
			return substr($string, $start, $length);
		}
	}
}

/**
 * Gets a query parameter from GET or POST data
 *
 * @param $key
 * @param $default
 */
function getQueryParam($key, $default = null)
{
	$value = $default;

	if (array_key_exists($key, $_REQUEST))
	{
		$value = $_REQUEST[$key];
	}

	if (get_magic_quotes_gpc() && !is_null($value))
	{
		$value = stripslashes($value);
	}

	return $value;
}

// Debugging function
function debugMsg($msg)
{
	if (!defined('KSDEBUG'))
	{
		return;
	}

	$fp = fopen('debug.txt', 'at');

	fwrite($fp, $msg . "\n");
	fclose($fp);

	// Echo to stdout if KSDEBUGCLI is defined
	if (defined('KSDEBUGCLI'))
	{
		echo $msg . "\n";
	}
}
