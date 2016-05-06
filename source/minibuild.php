<?php
/**
 * @copyright   2010-2016 Nicholas K. Dionysopoulos / AkeebaBackup.com
 * @license     GNU GPL v2 or - at your option - any later version
 * @package     akeebabackup
 * @subpackage  kickstart
 */

class AkeebaMinibuild
{
	/**
	 * Builds a .build file
	 *
	 * @param   string  $buildfile The full path to the .build file to be built
	 * @param   boolean $merge     Should I output a merged file or just include the file into the current context?
	 * @param   string  $rootDir   Where the source files live, default is the same directory as the .build file
	 *
	 * @return  boolean|string
	 *
	 * @throws  Exception
	 */
	public function minibuild($buildfile, $merge = true, $rootDir = null)
	{
		$buildFileDir = dirname($buildfile);

		if (empty($rootDir) || (!@is_dir($rootDir)))
		{
			$rootDir = $buildFileDir;
		}

		if (!file_exists($buildfile))
		{
			throw new Exception("Build file $buildfile not found");
		}

		$output = '';

		$lines = file($buildfile);

		foreach ($lines as $line)
		{
			$line = trim($line);
			$output .= "\n";

			if (substr($line, 0, 6) == 'BUILD:')
			{
				$newFilename = substr($line, 6);
				$newFilename = $buildFileDir . '/' . $newFilename . '.build';

				$ret = $this->minibuild($newFilename, $merge, $rootDir);

				if (is_string($ret))
				{
					$output .= $ret;
				}
			}
			else
			{
				$path = $rootDir . '/' . $line;

				if (!is_file($path))
				{
					throw new Exception("Included file $path not found");
				}

				if (!$merge)
				{
					// Bust opcode caches
					if (function_exists('opcache_invalidate')) opcache_invalidate($path);
					if (function_exists('apc_compile_file')) apc_compile_file($path);
					if (function_exists('wincache_refresh_if_changed')) wincache_refresh_if_changed(array($path));
					if (function_exists('xcache_asm')) xcache_asm($path);
					@include_once $path;
				}
				else
				{
					$output .= $this->prepareFile($path);
				}
			}
		}

		if ($merge)
		{
			return $output;
		}
		else
		{
			return true;
		}
	}

	/**
	 * Prepares a .php file for merge
	 *
	 * @param   string $path The full path to the file to include
	 *
	 * @return  string
	 */
	protected function prepareFile($path)
	{
		$lines = file($path);

		// Remove the first line (open php tag)
		$yanked = array_shift($lines);

		$ret = '';

		foreach ($lines as $l)
		{
			$ret .= rtrim($l) . "\n";
		}

		return $ret;
	}
}