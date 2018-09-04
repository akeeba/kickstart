<?php
/**
 * @copyright Copyright (c)2008-2018 Nicholas K. Dionysopoulos / Akeeba Ltd
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
	public function minibuild($buildfile, $merge = true, $rootDir = null, $includeHeaders = true)
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

			if (substr($line, 0, 6) == 'BUILD:')
			{
				$newFilename = substr($line, 6);
				$newFilename = $buildFileDir . '/' . $newFilename . '.build';

				$ret = $this->minibuild($newFilename, $merge, $rootDir, false);

				if (is_string($ret))
				{
					$output .= $ret;
				}

				continue;
			}

			$path = $rootDir . '/' . $line;

			if (!is_file($path))
			{
				throw new Exception("Included file $path not found");
			}

			if ($merge)
			{
				if (substr($line, 0, 7) == 'header/')
				{
					if (!$includeHeaders)
					{
						continue;
					}

					$output .= file_get_contents($path);

					continue;
				}

				$output .= "\n" . $this->prepareFile($path);

				continue;
			}

			// Bust opcode caches
			if (function_exists('opcache_invalidate'))
			{
				opcache_invalidate($path);
			}

			if (function_exists('apc_compile_file'))
			{
				apc_compile_file($path);
			}

			if (function_exists('wincache_refresh_if_changed'))
			{
				wincache_refresh_if_changed(array($path));
			}

			if (function_exists('xcache_asm'))
			{
				xcache_asm($path);
			}

			@include_once $path;
		}

		if ($merge)
		{
			return $output;
		}

		return true;
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

	/**
	 * Prepares a .php file for merge
	 *
	 * @param   string $path The full path to the file to include
	 *
	 * @return  string
	 */
	protected function XXX_prepareFile($path)
	{
		// Remove comments
		$fileString = file_get_contents($path);
		$filteredString = '';

		$tokens = token_get_all($fileString);
		$flagInsideHeredoc = false;
		$forceNewLine = false;

		foreach ($tokens as $token)
		{
			$tokenType = null;

			if (is_array($token))
			{
				$tokenType = $token[0];
				$token = $token[1];
			}

			// Is this the start of a HEREDOC?
			if ($tokenType == T_START_HEREDOC)
			{
				$flagInsideHeredoc = true;
				$filteredString .= $token;

				continue;
			}

			// Are we still inside a HEREDOC?
			if ($flagInsideHeredoc)
			{
				$flagInsideHeredoc = ($tokenType != T_END_HEREDOC);

				if (!$flagInsideHeredoc)
				{
					$forceNewLine = true;
				}

				$filteredString .= $token;

				continue;
			}

			// Is this a comment we need to skip?
			if (in_array($tokenType, [T_DOC_COMMENT, T_COMMENT]))
			{
				continue;
			}

			// Anything else needs to be trimmed
			$trimToken = trim($token, "\r\n\t");

			// If it's an empty line, skip it
			if (strlen($trimToken) == 0)
			{
				continue;
			}

			$filteredString .= $token;

			if ($forceNewLine)
			{
				$filteredString .= "\n";
				$forceNewLine = false;
			}
		}

		unset($fileString);

		$lines = explode("\n", $filteredString);

		// Remove the first line (open php tag)
		$yanked = array_shift($lines);

		unset ($yanked);

		$ret = '';

		foreach ($lines as $l)
		{
			$ret .= rtrim($l) . "\n";
		}

		return $ret;
	}

}
