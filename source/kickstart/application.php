<?php
/**
 * Akeeba Kickstart
 * An AJAX-powered archive extraction tool
 *
 * @package   kickstart
 * @copyright Copyright (c)2008-2021 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

// Register additional feature classes
callExtraFeature();

// Is this a CLI call?
$isCli = !isset($_SERVER) || !is_array($_SERVER);

if (isset($_SERVER) && is_array($_SERVER))
{
	$isCli = !array_key_exists('REQUEST_METHOD', $_SERVER);
}

if (isset($_GET) && is_array($_GET) && !empty($_GET))
{
	if (isset($_GET['cli']))
	{
		$isCli = $_GET['cli'] == 1;
	}
	elseif (isset($_GET['web']))
	{
		$isCli = $_GET['web'] != 1;
	}
}

// Route the application
if ($isCli)
{
	kickstart_application_cli();
}
else
{
	kickstart_application_web();
}
