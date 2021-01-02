<?php

/**
 * Akeeba Kickstart
 * An AJAX-powered archive extraction tool
 *
 * @package   kickstart
 * @copyright Copyright (c)2008-2021 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */
class AKKickstartUtils
{
	/**
	 * Guess the best path containing backup archives. The default strategy is check in the current directory first,
	 * then attempt to find an Akeeba Backup for Joomla!, Akeeba Solo or Akeeba Backup for WordPress default backup
	 * output directory under the current root. The first one containing backup archives wins.
	 *
	 * @return string The path to get archives from
	 */
	public static function getBestArchivePath()
	{
		$basePath      = self::getPath();
		$basePathSlash = (empty($basePath) ? '.' : rtrim($basePath, '/\\')) . '/';

		$paths = array(
			// Root, same as the directory we're in
			$basePath,
			// Standard temporary directory
			$basePath . '/kicktemp',
			// Akeeba Backup for Joomla!, default output directory
			$basePathSlash . 'administrator/components/com_akeeba/backup',
			// Akeeba Solo, default output directory
			$basePathSlash . 'backups',
			// Akeeba Backup for WordPress, default output directory
			$basePathSlash . 'wp-content/plugins/akeebabackupwp/app/backups',
		);

		foreach ($paths as $path)
		{
			$archives = self::findArchives($path);

			if (!empty($archives))
			{
				return $path;
			}
		}

		return $basePath;
	}

	/**
	 * Gets the directory the file is in
	 *
	 * @return string
	 */
	public static function getPath()
	{
		$path = KSROOTDIR;
		$path = rtrim(str_replace('\\', '/', $path), '/');

		if (!empty($path))
		{
			$path .= '/';
		}

		return $path;
	}

	/**
	 * Scans the current directory for archive files (JPA, JPS and ZIP format)
	 *
	 * @param string $path The path to look for archives. null for automatic path
	 *
	 * @return array
	 */
	public static function findArchives($path)
	{
		$ret = array();

		if (empty($path))
		{
			$path = self::getPath();
		}

		if (empty($path))
		{
			$path = '.';
		}

		$dh = @opendir($path);

		if ($dh === false)
		{
			return $ret;
		}

		while (false !== $file = @readdir($dh))
		{
			$dotpos = strrpos($file, '.');

			if ($dotpos === false)
			{
				continue;
			}

			if ($dotpos == strlen($file))
			{
				continue;
			}

			$extension = strtolower(substr($file, $dotpos + 1));

			if (in_array($extension, array('jpa', 'zip', 'jps')))
			{
				$ret[] = $file;
			}
		}

		closedir($dh);

		if (!empty($ret))
		{
			return $ret;
		}

		// On some hosts using opendir doesn't work. Let's try Dir instead
		$d = dir($path);

		while (false != ($file = $d->read()))
		{
			$dotpos = strrpos($file, '.');

			if ($dotpos === false)
			{
				continue;
			}

			if ($dotpos == strlen($file))
			{
				continue;
			}

			$extension = strtolower(substr($file, $dotpos + 1));

			if (in_array($extension, array('jpa', 'zip', 'jps')))
			{
				$ret[] = $file;
			}
		}

		return $ret;
	}

	/**
	 * Gets the most appropriate temporary path
	 *
	 * @return string
	 */
	public static function getTemporaryPath()
	{
		$path = self::getPath();

		$candidateDirs = array(
			$path,
			$path . '/kicktemp',
		);

		if (function_exists('sys_get_temp_dir'))
		{
			$candidateDirs[] = sys_get_temp_dir();
		}

		foreach ($candidateDirs as $dir)
		{
			if (is_dir($dir) && is_writable($dir))
			{
				return $dir;
			}
		}

		// Failsafe
		return $path;
	}

	/**
	 * Scans the current directory for archive files and returns them as <OPTION> tags
	 *
	 * @param string $path The path to look for archives. null for automatic path
	 *
	 * @return string
	 */
	public static function getArchivesAsOptions($path = null)
	{
		$ret = '';

		$archives = self::findArchives($path);

		if (empty($archives))
		{
			return $ret;
		}

		foreach ($archives as $file)
		{
			//$file = htmlentities($file);
			$ret .= '<option value="' . $file . '">' . $file . '</option>' . "\n";
		}

		return $ret;
	}

	/**
	 * Extract the PHP handler configuration from a .htaccess file.
	 *
	 * This method supports AddHandler lines and SetHandler blocks.
	 *
	 * @param   string  $htaccess
	 *
	 * @return  string|null  NULL when not found
	 */
	public static function extractHandler($htaccess)
	{
		// Normalize the .htaccess
		$htaccess = self::normalizeHtaccess($htaccess);

		// Look for SetHandler and AddHandler in Files and FilesMatch containers
		foreach (['Files', 'FilesMatch'] as $container)
		{
			$result = self::extractContainer($container, $htaccess);

			if (!is_null($result))
			{
				return $result;
			}
		}

		// Fallback: extract an AddHandler line
		$found = preg_match('#^AddHandler\s?.*\.php.*$#mi', $htaccess, $matches);

		if ($found >= 1)
		{
			return $matches[0];
		}

		return null;
	}

	/**
	 * Extracts a Files or FilesMatch container with an AddHandler or SetHandler line
	 *
	 * @param   string  $container  "Files" or "FilesMatch"
	 * @param   string  $htaccess   The .htaccess file content
	 *
	 * @return  string|null  NULL when not found
	 */
	protected static function extractContainer($container, $htaccess)
	{
		// Try to find the opening container tag e.g. <Files....>
		$pattern = sprintf('#<%s\s*.*\.php.*>#m', $container);
		$found   = preg_match($pattern, $htaccess, $matches, PREG_OFFSET_CAPTURE);

		if (!$found)
		{
			return null;
		}

		// Get the rest of the .htaccess sample
		$openContainer = $matches[0][0];
		$htaccess      = trim(substr($htaccess, $matches[0][1] + strlen($matches[0][0])));

		// Try to find the closing container tag
		$pattern = sprintf('#</%s\s*>#m', $container);
		$found   = preg_match($pattern, $htaccess, $matches, PREG_OFFSET_CAPTURE);

		if (!$found)
		{
			return null;
		}

		// Get the rest of the .htaccess sample
		$htaccess       = trim(substr($htaccess, 0, $matches[$found - 1][1]));
		$closeContainer = $matches[$found - 1][0];

		if (empty($htaccess))
		{
			return null;
		}

		// Now we'll explode remaining lines and find the first SetHandler or AddHandler line
		$lines = array_map('trim', explode("\n", $htaccess));
		$lines = array_filter($lines, function ($line) {
			return preg_match('#(Add|Set)Handler\s?#i', $line) >= 1;
		});

		if (empty($lines))
		{
			return null;
		}

		return $openContainer . "\n" . array_shift($lines) . "\n" . $closeContainer;
	}

	/**
	 * Normalize the .htaccess file content, making it suitable for handler extraction
	 *
	 * @param   string  $htaccess  The original file
	 *
	 * @return  string  The normalized file
	 */
	private static function normalizeHtaccess($htaccess)
	{
		// Convert all newlines into UNIX style
		$htaccess = str_replace("\r\n", "\n", $htaccess);
		$htaccess = str_replace("\r", "\n", $htaccess);

		// Return only non-comment, non-empty lines
		$isNonEmptyNonComment = function ($line) {
			$line = trim($line);

			return !empty($line) && (substr($line, 0, 1) !== '#');
		};

		$lines = array_map('trim', explode("\n", $htaccess));

		return implode("\n", array_filter($lines, $isNonEmptyNonComment));
	}
}
