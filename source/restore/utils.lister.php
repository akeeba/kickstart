<?php
/**
 * Akeeba Restore
 * An AJAX-powered archive extraction library for JPA, JPS and ZIP archives
 *
 * @package   restore
 * @copyright Copyright (c)2008-2021 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

/**
 * A filesystem scanner which uses opendir()
 */
class AKUtilsLister extends AKAbstractObject
{
	public function &getFiles($folder, $pattern = '*')
	{
		// Initialize variables
		$arr   = array();
		$false = false;

		if (!is_dir($folder))
		{
			return $false;
		}

		$handle = @opendir($folder);
		// If directory is not accessible, just return FALSE
		if ($handle === false)
		{
			$this->setWarning('Unreadable directory ' . $folder);

			return $false;
		}

		while (($file = @readdir($handle)) !== false)
		{
			if (!fnmatch($pattern, $file))
			{
				continue;
			}

			if (($file != '.') && ($file != '..'))
			{
				$ds    =
					($folder == '') || ($folder == '/') || (@substr($folder, -1) == '/') || (@substr($folder, -1) == DIRECTORY_SEPARATOR) ?
						'' : DIRECTORY_SEPARATOR;
				$dir   = $folder . $ds . $file;
				$isDir = is_dir($dir);
				if (!$isDir)
				{
					$arr[] = $dir;
				}
			}
		}
		@closedir($handle);

		return $arr;
	}

	public function &getFolders($folder, $pattern = '*')
	{
		// Initialize variables
		$arr   = array();
		$false = false;

		if (!is_dir($folder))
		{
			return $false;
		}

		$handle = @opendir($folder);
		// If directory is not accessible, just return FALSE
		if ($handle === false)
		{
			$this->setWarning('Unreadable directory ' . $folder);

			return $false;
		}

		while (($file = @readdir($handle)) !== false)
		{
			if (!fnmatch($pattern, $file))
			{
				continue;
			}

			if (($file != '.') && ($file != '..'))
			{
				$ds    =
					($folder == '') || ($folder == '/') || (@substr($folder, -1) == '/') || (@substr($folder, -1) == DIRECTORY_SEPARATOR) ?
						'' : DIRECTORY_SEPARATOR;
				$dir   = $folder . $ds . $file;
				$isDir = is_dir($dir);
				if ($isDir)
				{
					$arr[] = $dir;
				}
			}
		}
		@closedir($handle);

		return $arr;
	}
}
