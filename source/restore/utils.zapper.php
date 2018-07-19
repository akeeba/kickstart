<?php
/**
 * Akeeba Restore
 * A JSON-powered JPA, JPS and ZIP archive extraction library
 *
 * @copyright Copyright (c)2008-2018 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL v2 or - at your option - any later version
 * @package     akeebabackup
 * @subpackage  kickstart
 */

/**
 * A filesystem zapper - removes all files and folders under a root
 */
class AKUtilsZapper extends AKAbstractPart
{
	/** @var array Directories left to be deleted */
	private $directory_list;

	/** @var array Files left to be deleted */
	private $file_list;

	/**
	 * Have we finished scanning all subdirectories of the current directory?
	 *
	 * @var   boolean
	 */
	private $done_subdir_scanning = false;

	/**
	 * Have we finished scanning all files of the current directory?
	 *
	 * @var   boolean
	 */
	private $done_file_scanning = true;

	/**
	 * Is the current directory completely excluded?
	 *
	 * @var boolean
	 */
	private $excluded_folder = false;

	/** @var   integer  How many files have been processed in the current step */
	private $processed_files_counter;

	/** @var   string  Current directory being scanned */
	private $current_directory;

	/** @var   string  Current root directory being processed */
	private $root = '';

	/** @var   integer  Total files to process */
	private $total_files = 0;

	/** @var   integer  Total files already processed */
	private $done_files = 0;

	/** @var   integer  Total folders to process */
	private $total_folders = 0;

	/** @var   integer  Total folders already processed */
	private $done_folders = 0;

	/** @var array Absolute filesystem patterns to never delete (e.g. /var/www/html/*.jpa) */
	private $excluded = array();

	/**
	 * Implements the _prepare() abstract method
	 *
	 * Configuration parameters:
	 *
	 * root      The root under which we are going to be deleting files
	 * excluded  Absolute filesystem patterns to never delete (e.g. /var/www/html/*.jpa)
	 *
	 * @return  void
	 */
	protected function _prepare()
	{
		debugMsg(__CLASS__ . " :: Starting _prepare()");

		$defaultExcluded = $this->getDefaultExclusions();

		$parameters = array_merge(array(
			'root'     => AKFactory::get('kickstart.setup.destdir'),
			'excluded' => $defaultExcluded,
		), $this->_parametersArray);

		$this->root                 = $parameters['root'];
		$this->excluded             = $parameters['excluded'];
		$this->directory_list[]     = $this->root;
		$this->done_subdir_scanning = true;
		$this->done_file_scanning   = true;
		$this->total_files          = 0;
		$this->done_files           = 0;
		$this->total_folders        = 0;
		$this->done_folders         = 0;

		if (empty($this->root))
		{
			$error = "The folder to delete was not specified.";

			debugMsg(__CLASS__ . " :: " . $error);
			$this->setError($error);

			return;
		}

		if (!is_dir($this->root))
		{
			$error = sprintf("Folder %s does not exist", $this->root);

			debugMsg(__CLASS__ . " :: " . $error);
			$this->setError($error);

			return;
		}

		$this->setState('prepared');

		debugMsg(__CLASS__ . " :: prepared");
	}

	protected function _run()
	{
		if ($this->getState() == 'postrun')
		{
			debugMsg(__CLASS__ . " :: Already finished");
			$this->setStep("-");
			$this->setSubstep("");

			return true;
		}

		// If I'm done scanning files and subdirectories and there are no more files to pack get the next
		// directory. This block is triggered in the first step in a new root.
		if (empty($this->file_list) && $this->done_subdir_scanning && $this->done_file_scanning)
		{
			$this->progressMarkFolderDone();

			if (!$this->getNextDirectory())
			{
				return true;
			}
		}

		// If I'm not done scanning for files and the file list is empty then scan for more files
		if (!$this->done_file_scanning && empty($this->file_list))
		{
			$this->scanFiles();
		}
		// If I have files left, delete them
		elseif (!empty($this->file_list))
		{
			$this->delete_files();
		}
		// If I'm not done scanning subdirectories, go ahead and scan some more of them
		elseif (!$this->done_subdir_scanning)
		{
			$this->scanSubdirs();
		}
		/**
		 * If we have NOT excluded the entire folder AND removed all of its files THEN try to delete it.
		 *
		 * No deletion will happen in the following cases:
		 *
		 * - The file is excluded from deletion
		 * - One or more files under the folder are excluded from deletion
		 *
		 * The deletion will fail (it's expected to fail) when one of the contained folders has an excluded folder or
		 * file. We can gracefully ignore these errors, though :)
		 */
		elseif (!$this->excluded_folder && empty($this->file_list))
		{
			debugMsg("Deleting directory " . $this->current_directory);

			$postProc = AKFactory::getPostProc();
            $this->setSubstep($this->current_directory);
            $this->notify((object) array(
                'type' => 'deleteFolder',
                'file' => $this->current_directory
            ));

            $postProc->rmdir($this->current_directory);
		}

		// Do I have an error?
		if ($this->getError())
		{
			return false;
		}

		return true;
	}

	/**
	 * Implements the _finalize() abstract method
	 *
	 */
	protected function _finalize()
	{
		// No finalization is required
		$this->setState('finished');
	}

	// ============================================================================================
	// PRIVATE METHODS
	// ============================================================================================

	/**
	 * Gets the next directory to scan from the stack. It also applies folder
	 * filters (directory exclusion, subdirectory exclusion, file exclusion),
	 * updating the operation toggle properties of the class.
	 *
	 * @return   boolean  True if we found a directory, false if the directory
	 *                    stack is empty. It also returns true if the folder is
	 *                    filtered (we are told to skip it)
	 */
	private function getNextDirectory()
	{
		// Reset the file / folder scanning positions
		$this->done_file_scanning   = false;
		$this->done_subdir_scanning = false;
		$this->excluded_folder      = false;

		if (count($this->directory_list) == 0)
		{
			// No directories left to scan
			return false;
		}

		// Get and remove the last entry from the $directory_list array
		$this->current_directory = array_pop($this->directory_list);
		$this->setStep($this->current_directory);
		$this->processed_files_counter = 0;

		// Apply directory exclusion filters
		if ($this->isFiltered($this->current_directory))
		{
			debugMsg("Skipping directory " . $this->current_directory);
			$this->done_subdir_scanning = true;
			$this->done_file_scanning   = true;
			$this->excluded_folder      = true;

			return true;
		}

		return true;
	}

	/**
	 * Try to delete some files from the $file_list
	 *
	 * @return   boolean   True if there were files deleted , false otherwise
	 *                     (empty filelist or fatal error)
	 */
	protected function delete_files()
	{
		// Get a reference to the archiver and the timer classes
		$timer = AKFactory::getTimer();

		// Normal file removal loop; we keep on processing the file list, removing files as we go.
		if (count($this->file_list) == 0)
		{
			// No files left to pack. Return true and let the engine loop
			$this->progressMarkFolderDone();

			return true;
		}

		debugMsg("Deleting files");

		$numberOfFiles = 0;
		$postProc = AKFactory::getPostProc();

		while ((count($this->file_list) > 0))
		{
			$file = @array_shift($this->file_list);

			$numberOfFiles++;

			// Remove the file
            $this->setSubstep($file);
            $this->notify((object) array(
                'type' => 'deleteFile',
                'file' => $file
            ));
			$postProc->unlink($file);

			// Mark a done file
			$this->progressMarkFileDone();

			if ($this->getError())
			{
				return false;
			}

			// I am running out of time.
			if (!$timer->getTimeLeft())
			{
				return true;
			}
		}

		// True if we have more files, false if we're done packing
		return (count($this->file_list) > 0);
	}

	protected function progressAddFile()
	{
		$this->total_files++;
	}

	protected function progressMarkFileDone()
	{
		$this->done_files++;
	}

	protected function progressAddFolder()
	{
		$this->total_folders++;
	}

	protected function progressMarkFolderDone()
	{
		$this->done_folders++;
	}

	/**
	 * Returns the site root, the translated site root and the translated current directory
	 *
	 * @return array
	 */
	protected function getCleanDirectoryComponents()
	{
		$root            = $this->root;
		$translated_root = $root;
		$dir             = TrimTrailingSlash($this->current_directory);

		if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN')
		{
			$translated_root = TranslateWinPath($translated_root);
			$dir             = TranslateWinPath($dir);
		}

		if (substr($dir, 0, strlen($translated_root)) == $translated_root)
		{
			$dir = substr($dir, strlen($translated_root));
		}
		elseif (in_array(substr($translated_root, -1), array('/', '\\')))
		{
			$new_translated_root = rtrim($translated_root, '/\\');

			if (substr($dir, 0, strlen($new_translated_root)) == $new_translated_root)
			{
				$dir = substr($dir, strlen($new_translated_root));
			}
		}

		if (substr($dir, 0, 1) == '/')
		{
			$dir = substr($dir, 1);
		}

		return array($root, $translated_root, $dir);
	}

	/**
	 * Steps the subdirectory scanning of the current directory
	 *
	 * @return  boolean  True on success, false on fatal error
	 */
	protected function scanSubdirs()
	{
		$lister = new AKUtilsLister();

		list($root, $translated_root, $dir) = $this->getCleanDirectoryComponents();

		debugMsg("Scanning directories of " . $this->current_directory);

		// Get subdirectories
		$subdirectories = $lister->getFolders($this->current_directory);

		// Error propagation
		$this->propagateFromObject($lister);

		// Error control
		if ($this->getError())
		{
			return false;
		}

		// Start adding the subdirectories
		if (!empty($subdirectories) && is_array($subdirectories))
		{
			// Treat symlinks to directories as simple symlink files
			foreach ($subdirectories as $subdirectory)
			{
				if (is_link($subdirectory))
				{
					// Symlink detected; apply directory filters to it
					if (empty($dir))
					{
						$dirSlash = $dir;
					}
					else
					{
						$dirSlash = $dir . '/';
					}

					$check = $dirSlash . basename($subdirectory);
					debugMsg("Directory symlink detected: $check");

					if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN')
					{
						$check = TranslateWinPath($check);
					}

					$check = $translated_root . '/' . $check;

					// Check for excluded symlinks
					if ($this->isFiltered($check))
					{
						debugMsg("Skipping directory symlink " . $check);

						continue;
					}

					debugMsg('Adding folder symlink: ' . $check);

					$this->file_list[] = $subdirectory;
					$this->progressAddFile();
				}

				$this->directory_list[] = $subdirectory;
				$this->progressAddFolder();
			}
		}

		$this->done_subdir_scanning = true;

		return true;
	}

	/**
	 * Steps the files scanning of the current directory
	 *
	 * @return  boolean  True on success, false on fatal error
	 */
	protected function scanFiles()
	{
		$lister = new AKUtilsLister();

		list($root, $translated_root, $dir) = $this->getCleanDirectoryComponents();

		debugMsg("Scanning files of " . $this->current_directory);
		$this->processed_files_counter = 0;

		// Get file listing
		$fileList = $lister->getFiles($this->current_directory);

		// Error propagation
		$this->propagateFromObject($lister);

		// Error control
		if ($this->getError())
		{
			return false;
		}

		// Do I have an unreadable directory?
		if (($fileList === false))
		{
			$this->setWarning('Unreadable directory ' . $this->current_directory);

			$this->done_file_scanning = true;

			return true;
		}

		// Directory was readable, process the file list
		if (is_array($fileList) && !empty($fileList))
		{
			// Add required trailing slash to $dir
			if (!empty($dir))
			{
				$dir .= '/';
			}

			// Scan all directory entries
			foreach ($fileList as $fileName)
			{
				$check = $dir . basename($fileName);

				if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN')
				{
					$check = TranslateWinPath($check);
				}

				$check        = $translated_root . '/' . $check;
				$skipThisFile = $this->isFiltered($check);

				if ($skipThisFile)
				{
					debugMsg("Skipping file $fileName");

					continue;
				}

				$this->file_list[] = $fileName;
				$this->processed_files_counter++;
				$this->progressAddFile();
			}
		}

		$this->done_file_scanning = true;

		return true;
	}

	/**
	 * Is a file or folder filtered (protected from deletion)
	 *
	 * @param   string  $fileOrFolder
	 *
	 * @return  bool
	 */
	private function isFiltered($fileOrFolder)
	{
		foreach ($this->excluded as $pattern)
		{
			if (fnmatch($pattern, $fileOrFolder))
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Get the default exceptions from deletion
	 *
	 * @return  array
	 */
	private function getDefaultExclusions()
	{
		$ret     = array();
		$destDir = AKFactory::get('kickstart.setup.destdir');

		/**
		 * Exclude Kickstart / restore.php itself. Otherwise it'd crash!
		 */
		$ret[] = KSROOTDIR . '/' . KSSELFNAME;

		/**
		 * Cheat: exclude the directory used in development (see source/buildscripts/kickstart_test.php)
		 *
		 * This directory contains the non-concatenated source code for Kickstart. We need to keep it protected.
		 */
		if (defined('MINIBUILD') && (MINIBUILD != $destDir))
		{
			$ret[] = TranslateWinPath(MINIBUILD);
		}

		/**
		 * Exclude the backup archive directory if it's not the site's root. This prevents mindlessly deleting all your
		 * backups before you restore from a previous backup which might not be the one you actually wanted. I will call
		 * this feature "clumsy-proofing".
		 */
		$backupArchive   = AKFactory::get('kickstart.setup.sourcefile');
		$backupDirectory = AKFactory::get('kickstart.setup.sourcepath');
		$backupDirectory = empty($backupDirectory) ? dirname($backupArchive) : '';

		if ($backupDirectory != $destDir)
		{
			$ret[] = TranslateWinPath($backupDirectory);
		}

		/**
		 * Exclude the backup archive files
		 *
		 * This obviously only makes sense when the backup archives are stored in the extraction target folder which is
		 * the most common use of Kickstart. In this case the backups folder is not excluded above.
		 */
		$plainBackupName = basename($backupArchive, '.jpa');
		$plainBackupName = basename($plainBackupName, '.jps');
		$plainBackupName = basename($plainBackupName, '.zip');
		$ret[]           = TranslateWinPath($backupDirectory . '/' . $plainBackupName) . '.*';

		/**
		 * Exclude Kickstart language files. Only applies in Kickstart mode.
		 */
		if (defined('KICKSTART'))
		{
			$langDir = defined('KSLANGDIR') ? KSLANGDIR : KSROOTDIR;
			$ret[]   = $langDir . '/' . basename(KSSELFNAME, '.php') . '.*.ini';
		}

		/**
		 * Exclude Kickstart resources (cacert.pem, jquery.min.js, json2.min.js). Only applies in Kickstart mode.
		 */
		if (defined('KICKSTART'))
		{
			$ret[] = TranslateWinPath(KSROOTDIR . '/cacert.pem');
			$ret[] = TranslateWinPath(KSROOTDIR . '/jquery.min.js');
			$ret[] = TranslateWinPath(KSROOTDIR . '/json2.min.js');
		}

		// Exclude the Kickstart temporary directory, if one is used by the post-processing engine
		$postProc = AKFactory::getPostProc();
		$tempDir  = $postProc->getTempDir();

		if (!empty($tempDir))
		{
			$ret = TranslateWinPath($tempDir);
		}

		/**
		 * Exclude the configured Skipped Files ('kickstart.setup.skipfiles'). Also exclude the various restoration.php
		 * files if we are in restore.php mode and the files are present. These are required for the integrated
		 * restoration to actually work :)
		 */
		$skippedFiles = AKFactory::get('kickstart.setup.skipfiles', array(
			basename(__FILE__), 'kickstart.php', 'abiautomation.ini', 'htaccess.bak', 'php.ini.bak',
			'cacert.pem',
		));

		if (!defined('KICKSTART'))
		{
			// In restore.php mode we have to exclude the various restoration.php files
			$skippedFiles = array_merge(array(
				// Akeeba Backup for Joomla!
				'administrator/components/com_akeeba/restoration.php',
				// Joomla! Update
				'administrator/components/com_joomlaupdate/restoration.php',
				// Akeeba Backup for WordPress
				'wp-content/plugins/akeebabackupwp/app/restoration.php',
				'wp-content/plugins/akeebabackupcorewp/app/restoration.php',
				'wp-content/plugins/akeebabackup/app/restoration.php',
				'wp-content/plugins/akeebabackupwpcore/app/restoration.php',
				// Akeeba Solo
				'app/restoration.php',
			), $skippedFiles);
		}

		foreach ($skippedFiles as $file)
		{
			$checkFile = $destDir . '/' . $file;

			if (file_exists($checkFile))
			{
				$ret[] = TranslateWinPath($checkFile);
			}
		}

		/**
		 * Exclude .htaccess if the stealth feature is enabled. Otherwise we'd unset the stealth mode.
		 */
		if (AKFactory::get('kickstart.stealth.enable'))
		{
			$ret[] = $destDir . '/.htaccess';
		}

		return $ret;
	}
}

/**
 * Runs the Zapper and returns a status table. The Zapper only runs if the feature is enabled (kickstart.setup.zapbefore
 * is 1) and there are more Zapper steps to run (its state is not postrun). If any of these conditions is not met we
 * return boolean false.
 *
 * @return  bool|array  Boolean false or a status array
 */
function runZapper()
{
    $enabled = AKFactory::get('kickstart.setup.zapbefore', 0);

    if (!$enabled)
    {
        return false;
    }

    $zapper = AKFactory::getZapper();

    if ($zapper->getState() == 'postrun')
    {
        return false;
    }

    $ret = $zapper->tick();

    $retArray = array(
        'status'  => true,
        'message' => null,
        'done' => false,
    );

    if ($ret['Error'] != '')
    {
        $retArray['status']  = false;
        $retArray['done']    = true;
        $retArray['message'] = $ret['Error'];
    }
    else
    {
        $retArray['files']    = 0;
        $retArray['bytesIn']  = 0;
        $retArray['bytesOut'] = 0;
        $retArray['factory']  = AKFactory::serialize();
        $retArray['lastfile'] = $zapper->getSubstep();
    }

    return $retArray;
}