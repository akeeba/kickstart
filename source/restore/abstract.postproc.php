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
 * File post processor engines base class
 */
abstract class AKAbstractPostproc extends AKAbstractObject
{
	/** @var int The UNIX timestamp of the file's desired modification date */
	public $timestamp = 0;
	/** @var string The current (real) file path we'll have to process */
	protected $filename = null;
	/** @var int The requested permissions */
	protected $perms = 0755;
	/** @var string The temporary file path we gave to the unarchiver engine */
	protected $tempFilename = null;
	/** @var string The temporary directory where the data will be stored */
	protected $tempDir = '';

	/**
	 * Processes the current file, e.g. moves it from temp to final location by FTP
	 */
	abstract public function process();

	/**
	 * The unarchiver tells us the path to the filename it wants to extract and we give it
	 * a different path instead.
	 *
	 * @param string $filename The path to the real file
	 * @param int    $perms    The permissions we need the file to have
	 *
	 * @return string The path to the temporary file
	 */
	abstract public function processFilename($filename, $perms = 0755);

	/**
	 * Recursively creates a directory if it doesn't exist
	 *
	 * @param string $dirName The directory to create
	 * @param int    $perms   The permissions to give to that directory
	 */
	abstract public function createDirRecursive($dirName, $perms);

	abstract public function chmod($file, $perms);

	abstract public function unlink($file);

	abstract public function rmdir($directory);

	abstract public function rename($from, $to);

	/**
	 * Returns the configured temporary directory
	 *
	 * @return string
	 */
	public function getTempDir()
	{
		return $this->tempDir;
	}
}

