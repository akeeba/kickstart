<?php
/**
 * Joomla! Start
 *
 * Allows you to download and install Joomla! on your server, without having
 * to manually upload / download anything.
 *
 * This tool is derived from Akeeba Kickstart, the on-line archive extraction
 * tool by Akeeba Ltd.
 *
 * @copyright   2010-2014 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL v2 or - at your option - any later version
 * @package     joomla
 * @subpackage  joomlastart
 */

/*
    Joomla! Start - The quickest path to installing Joomla! on your server
    Copyright (C) 2008-2013  Nicholas K. Dionysopoulos / Akeeba Ltd

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

define('KICKSTART',1);
define('VERSION', '##VERSION##');
// Uncomment the following line to enable debug mode
define('KSDEBUG', 1);

if(!defined('KSROOTDIR'))
{
	define('KSROOTDIR', dirname(__FILE__));
}

if(defined('KSDEBUG')) {
	@ini_set('error_log', KSROOTDIR.'/joomlastart_error_log' );
	if(file_exists(KSROOTDIR.'/joomlastart_error_log')) {
		@unlink(KSROOTDIR.'/joomlastart_error_log');
	}
	error_reporting(E_ALL | E_STRICT);
	@ini_set('display_errors', 1);
} else {
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
		if(isset($_SERVER['SCRIPT_NAME']))
		$_SERVER['HTTP_REQUEST_URI'] = $_SERVER['SCRIPT_NAME'];
		else
		$_SERVER['HTTP_REQUEST_URI'] = $_SERVER['PHP_SELF'];
		if($_SERVER['QUERY_STRING']){
			$_SERVER['HTTP_REQUEST_URI'] .=  '?' . $_SERVER['QUERY_STRING'];
		}
		//WARNING: This is a workaround!
		//For guaranteed compatibility, HTTP_REQUEST_URI *MUST* be defined!
		//See product documentation for instructions!
		$_SERVER['REQUEST_URI'] = $_SERVER['HTTP_REQUEST_URI'];
	}
}

// Define the cacert.pem location, if it exists
$cacertpem = KSROOTDIR . '/cacert.pem';
if(is_file($cacertpem)) {
	if(is_readable($cacertpem)) {
		define('AKEEBA_CACERT_PEM', $cacertpem);
	}
}
else
{
	unset($cacertpem);
}