<?php
/**
 * Akeeba Kickstart
 * An AJAX-powered archive extraction tool
 *
 * @package   kickstart
 * @copyright Copyright (c)2008-2021 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

// Make sure the file has been renamed
$myself   = strtolower(__FILE__);
$basename = basename(__FILE__, '.php');
if (strpos($basename, 'kickstart') === 0)
{
	echo <<< HTML
<html>
<head>
	<title>Insecure setup detected</title>
</head>
<body>
	<h1>Akeeba Kickstart Professional – Insecure setup detected</h1>
	<p>
		Akeeba Kickstart has detected that its file name is <code>{$basename}.php</code>. Please change the file name to
		something which <em>does not</em> begin with <code>kickstart</code> and ends with <code>.php</code>. For example,
		you could rename the file to <code>myexample.php</code>  Then you can access this file by replacing
		<code>kickstart.php</code> with the new name in the address bar of your browser.
	</p>
	<h3>Why do you need to do that?</h3>
	<p>
		Due to its nature, Akeeba Kickstart will execute commands sent to it by any web visitor. There is no way to
		verify the visitor's identity. Since Akeeba Kickstart Professional allows you to import ZIP archives from
		arbitrary URLs an attacker can use it to load malware to your site while you are restoring your site. Your only
		protection is to rename Kickstart's file to prevent the attacker from using Akeeba Kickstart Professional
		against you.
	</p>
	<p>
		If you do not need the additional features of Akeeba Kickstart Professional you are strongly advised to use
		Akeeba Kickstart Core. Since it lacks the ability to import remote files it's safe to use without renaming the
		file.
	</p>
</body>
</html>
HTML;
	die;
}
