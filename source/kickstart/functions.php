<?php
/**
 * Akeeba Kickstart
 * An AJAX-powered archive extraction tool
 *
 * @package   kickstart
 * @copyright Copyright (c)2008-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

/**
 * Removes trailing slash or backslash from a pathname
 *
 * @param   string  $path  The path to treat
 *
 * @return  string  The path without the trailing slash/backslash
 */
function TrimTrailingSlash($path)
{
	$newpath = $path;

	if (substr($path, strlen($path) - 1, 1) == '\\')
	{
		$newpath = substr($path, 0, strlen($path) - 1);
	}

	if (substr($path, strlen($path) - 1, 1) == '/')
	{
		$newpath = substr($path, 0, strlen($path) - 1);
	}

	return $newpath;
}


function TranslateWinPath($p_path)
{
	$is_unc = false;

	if (KSWINDOWS)
	{
		// Is this a UNC path?
		$is_unc = (substr($p_path, 0, 2) == '\\\\') || (substr($p_path, 0, 2) == '//');
		// Change potential windows directory separator
		if ((strpos($p_path, '\\') > 0) || (substr($p_path, 0, 1) == '\\'))
		{
			$p_path = strtr($p_path, '\\', '/');
		}
	}

	// Remove multiple slashes
	$p_path = str_replace('///', '/', $p_path);
	$p_path = str_replace('//', '/', $p_path);

	// Fix UNC paths
	if ($is_unc)
	{
		$p_path = '//' . ltrim($p_path, '/');
	}

	return $p_path;
}


/**
 * FTP Functions
 */
function getListing($directory, $host, $port, $username, $password, $passive, $ssl)
{
	$directory = resolvePath($directory);
	$dir       = $directory;

	// Parse directory to parts
	$parsed_dir = trim($dir, '/');
	$parts      = empty($parsed_dir) ? array() : explode('/', $parsed_dir);

	// Find the path to the parent directory
	if (!empty($parts))
	{
		$copy_of_parts = $parts;
		array_pop($copy_of_parts);
		if (!empty($copy_of_parts))
		{
			$parent_directory = '/' . implode('/', $copy_of_parts);
		}
		else
		{
			$parent_directory = '/';
		}
	}
	else
	{
		$parent_directory = '';
	}

	// Connect to the server
	if ($ssl)
	{
		$con = @ftp_ssl_connect($host, $port);
	}
	else
	{
		$con = @ftp_connect($host, $port);
	}
	if ($con === false)
	{
		return array(
			'error' => 'FTPBROWSER_ERROR_HOSTNAME'
		);
	}

	// Login
	$result = @ftp_login($con, $username, $password);
	if ($result === false)
	{
		return array(
			'error' => 'FTPBROWSER_ERROR_USERPASS'
		);
	}

	// Set the passive mode -- don't care if it fails, though!
	@ftp_pasv($con, $passive);

	// Try to chdir to the specified directory
	if (!empty($dir))
	{
		$result = @ftp_chdir($con, $dir);
		if ($result === false)
		{
			return array(
				'error' => 'FTPBROWSER_ERROR_NOACCESS'
			);
		}
	}
	else
	{
		$directory = @ftp_pwd($con);

		$parsed_dir       = trim($directory, '/');
		$parts            = empty($parsed_dir) ? array() : explode('/', $parsed_dir);
		$parent_directory = $this->directory;
	}

	// Get a raw directory listing (hoping it's a UNIX server!)
	$list = @ftp_rawlist($con, '.');
	ftp_close($con);

	if ($list === false)
	{
		return array(
			'error' => 'FTPBROWSER_ERROR_UNSUPPORTED'
		);
	}

	// Parse the raw listing into an array
	$folders = parse_rawlist($list);

	return array(
		'error'       => '',
		'list'        => $folders,
		'breadcrumbs' => $parts,
		'directory'   => $directory,
		'parent'      => $parent_directory
	);
}

function parse_rawlist($list)
{
	$folders = array();
	foreach ($list as $v)
	{
		$info  = array();
		$vinfo = preg_split("/[\s]+/", $v, 9);
		if ($vinfo[0] !== "total")
		{
			$perms = $vinfo[0];
			if (substr($perms, 0, 1) == 'd')
			{
				$folders[] = $vinfo[8];
			}
		}
	}

	asort($folders);

	return $folders;
}

function getSftpListing($directory, $host, $port, $username, $password)
{
	$directory = resolvePath($directory);
	$dir       = $directory;

	// Parse directory to parts
	$parsed_dir = trim($dir, '/');
	$parts      = empty($parsed_dir) ? array() : explode('/', $parsed_dir);

	// Find the path to the parent directory
	if (!empty($parts))
	{
		$copy_of_parts = $parts;
		array_pop($copy_of_parts);
		if (!empty($copy_of_parts))
		{
			$parent_directory = '/' . implode('/', $copy_of_parts);
		}
		else
		{
			$parent_directory = '/';
		}
	}
	else
	{
		$parent_directory = '';
	}

	// Initialise
	$connection = null;
	$sftphandle = null;

	// Open a connection
	if (!function_exists('ssh2_connect'))
	{
		return array(
			'error' => AKText::_('SFTP_NO_SSH2')
		);
	}

	$connection = ssh2_connect($host, $port);

	if ($connection === false)
	{
		return array(
			'error' => AKText::_('SFTP_WRONG_USER')
		);
	}

	if (!ssh2_auth_password($connection, $username, $password))
	{
		return array(
			'error' => AKText::_('SFTP_WRONG_USER')
		);
	}

	$sftphandle = ssh2_sftp($connection);

	if ($sftphandle === false)
	{
		return array(
			'error' => AKText::_('SFTP_NO_FTP_SUPPORT')
		);
	}

	// Get a raw directory listing (hoping it's a UNIX server!)
	$list = array();
	$dir  = ltrim($dir, '/');

	if (empty($dir))
	{
		$dir       = ssh2_sftp_realpath($sftphandle, ".");
		$directory = $dir;

		// Parse directory to parts
		$parsed_dir = trim($dir, '/');
		$parts      = empty($parsed_dir) ? array() : explode('/', $parsed_dir);

		// Find the path to the parent directory
		if (!empty($parts))
		{
			$copy_of_parts = $parts;
			array_pop($copy_of_parts);

			if (!empty($copy_of_parts))
			{
				$parent_directory = '/' . implode('/', $copy_of_parts);
			}
			else
			{
				$parent_directory = '/';
			}
		}
		else
		{
			$parent_directory = '';
		}
	}

	$handle = opendir("ssh2.sftp://$sftphandle/$dir");

	if (!is_resource($handle))
	{
		return array(
			'error' => AKText::_('SFTPBROWSER_ERROR_NOACCESS')
		);
	}

	while (($entry = readdir($handle)) !== false)
	{
		if (!is_dir("ssh2.sftp://$sftphandle/$dir/$entry"))
		{
			continue;
		}

		$list[] = $entry;
	}

	closedir($handle);

	if (!empty($list))
	{
		asort($list);
	}

	return array(
		'error'       => '',
		'list'        => $list,
		'breadcrumbs' => $parts,
		'directory'   => $directory,
		'parent'      => $parent_directory
	);
}

/**
 * Simple function to resolve relative paths.
 * Note that it is unable to resolve pathnames any higher than the present working directory.
 * I.E. It doesn't know about any directory names that you don't tell it about; hence: ../../foo becomes foo.
 *
 * @param $filename
 *
 * @return string
 */
function resolvePath($filename)
{
	$filename = str_replace('//', '/', $filename);
	$parts    = explode('/', $filename);
	$out      = array();
	foreach ($parts as $part)
	{
		if ($part == '.')
		{
			continue;
		}
		if ($part == '..')
		{
			array_pop($out);
			continue;
		}
		$out[] = $part;
	}

	return implode('/', $out);
}

function createStealthURL()
{
	$filename = AKFactory::get('kickstart.stealth.url', '');
	// We need an HTML file!
	if (empty($filename))
	{
		return;
	}
	// Make sure it ends in .html or .htm
	$filename = basename($filename);
	if ((strtolower(substr($filename, -5)) != '.html') && (strtolower(substr($filename, -4)) != '.htm'))
	{
		return;
	}

	$filename_quoted = str_replace('.', '\\.', $filename);
	$rewrite_base    = trim(dirname(AKFactory::get('kickstart.stealth.url', '')), '/');

	// Get the IP
	$userIP = $_SERVER['REMOTE_ADDR'];
	$userIP = str_replace('.', '\.', $userIP);

	// Get the .htaccess contents
	$stealthHtaccess = <<<ENDHTACCESS
RewriteEngine On
RewriteBase /$rewrite_base
RewriteCond %{REMOTE_ADDR}		!$userIP
RewriteCond %{REQUEST_URI}		!$filename_quoted
RewriteCond %{REQUEST_URI}		!(\.png|\.jpg|\.gif|\.jpeg|\.bmp|\.swf|\.css|\.js)$
RewriteRule (.*)				$filename	[R=307,L]

ENDHTACCESS;

	// Write the new .htaccess, removing the old one first
	$postproc = AKFactory::getpostProc();
	$postproc->unlink('.htaccess');
	$tempfile = $postproc->processFilename('.htaccess');
	@file_put_contents($tempfile, $stealthHtaccess);
	$postproc->process();
}
