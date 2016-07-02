<?php
/**
 * Akeeba Restore
 * A JSON-powered JPA, JPS and ZIP archive extraction library
 *
 * @copyright   2010-2016 Nicholas K. Dionysopoulos / Akeeba Ltd.
 * @license     GNU GPL v2 or - at your option - any later version
 * @package     akeebabackup
 * @subpackage  kickstart
 */

// Mini-controller for restore.php
if(!defined('KICKSTART'))
{
	// The observer class, used to report number of files and bytes processed
	class RestorationObserver extends AKAbstractPartObserver
	{
		public $compressedTotal = 0;
		public $uncompressedTotal = 0;
		public $filesProcessed = 0;

		public function update($object, $message)
		{
			if(!is_object($message)) return;

			if( !array_key_exists('type', get_object_vars($message)) ) return;

			if( $message->type == 'startfile' )
			{
				$this->filesProcessed++;
				$this->compressedTotal += $message->content->compressed;
				$this->uncompressedTotal += $message->content->uncompressed;
			}
		}

		public function __toString()
		{
			return __CLASS__;
		}

	}

	// Import configuration
	masterSetup();

	$retArray = array(
		'status'	=> true,
		'message'	=> null
	);

	$enabled = AKFactory::get('kickstart.enabled', false);

	if($enabled)
	{
		$task = getQueryParam('task');

		switch($task)
		{
			case 'ping':
				// ping task - realy does nothing!
				$timer = AKFactory::getTimer();
				$timer->enforce_min_exec_time();
				break;

			case 'startRestore':
				AKFactory::nuke(); // Reset the factory

				// Let the control flow to the next step (the rest of the code is common!!)

			case 'stepRestore':
				$engine = AKFactory::getUnarchiver(); // Get the engine
				$observer = new RestorationObserver(); // Create a new observer
				$engine->attach($observer); // Attach the observer
				$engine->tick();
				$ret = $engine->getStatusArray();

				if( $ret['Error'] != '' )
				{
					$retArray['status'] = false;
					$retArray['done'] = true;
					$retArray['message'] = $ret['Error'];
				}
				elseif( !$ret['HasRun'] )
				{
					$retArray['files'] = $observer->filesProcessed;
					$retArray['bytesIn'] = $observer->compressedTotal;
					$retArray['bytesOut'] = $observer->uncompressedTotal;
					$retArray['status'] = true;
					$retArray['done'] = true;
				}
				else
				{
					$retArray['files'] = $observer->filesProcessed;
					$retArray['bytesIn'] = $observer->compressedTotal;
					$retArray['bytesOut'] = $observer->uncompressedTotal;
					$retArray['status'] = true;
					$retArray['done'] = false;
					$retArray['factory'] = AKFactory::serialize();
				}
				break;

			case 'finalizeRestore':
				$root = AKFactory::get('kickstart.setup.destdir');
				// Remove the installation directory
				recursive_remove_directory( $root.'/installation' );

				$postproc = AKFactory::getPostProc();

				// Rename htaccess.bak to .htaccess
				if(file_exists($root.'/htaccess.bak'))
				{
					if( file_exists($root.'/.htaccess')  )
					{
						$postproc->unlink($root.'/.htaccess');
					}
					$postproc->rename( $root.'/htaccess.bak', $root.'/.htaccess' );
				}

				// Rename htaccess.bak to .htaccess
				if(file_exists($root.'/web.config.bak'))
				{
					if( file_exists($root.'/web.config')  )
					{
						$postproc->unlink($root.'/web.config');
					}
					$postproc->rename( $root.'/web.config.bak', $root.'/web.config' );
				}

				// Remove restoration.php
				$basepath = KSROOTDIR;
				$basepath = rtrim( str_replace('\\','/',$basepath), '/' );
				if(!empty($basepath)) $basepath .= '/';
				$postproc->unlink( $basepath.'restoration.php' );

				// Import a custom finalisation file
				$filename = dirname(__FILE__) . '/restore_finalisation.php';
				if (file_exists($filename))
				{
					// opcode cache busting before including the filename
					if (function_exists('opcache_invalidate')) opcache_invalidate($filename);
					if (function_exists('apc_compile_file')) apc_compile_file($filename);
					if (function_exists('wincache_refresh_if_changed')) wincache_refresh_if_changed(array($filename));
					if (function_exists('xcache_asm')) xcache_asm($filename);
					include_once $filename;
				}

				// Run a custom finalisation script
				if (function_exists('finalizeRestore'))
				{
					finalizeRestore($root, $basepath);
				}
				break;

			default:
				// Invalid task!
				$enabled = false;
				break;
		}
	}

	// Maybe we weren't authorized or the task was invalid?
	if(!$enabled)
	{
		// Maybe the user failed to enter any information
		$retArray['status'] = false;
		$retArray['message'] = AKText::_('ERR_INVALID_LOGIN');
	}

	// JSON encode the message
	$json = json_encode($retArray);
	// Do I have to encrypt?
	$password = AKFactory::get('kickstart.security.password', null);
	if(!empty($password))
	{
		$json = AKEncryptionAES::AESEncryptCtr($json, $password, 128);
	}

	// Return the message
	echo "###$json###";

}

// ------------ lixlpixel recursive PHP functions -------------
// recursive_remove_directory( directory to delete, empty )
// expects path to directory and optional TRUE / FALSE to empty
// of course PHP has to have the rights to delete the directory
// you specify and all files and folders inside the directory
// ------------------------------------------------------------
function recursive_remove_directory($directory)
{
	// if the path has a slash at the end we remove it here
	if(substr($directory,-1) == '/')
	{
		$directory = substr($directory,0,-1);
	}
	// if the path is not valid or is not a directory ...
	if(!file_exists($directory) || !is_dir($directory))
	{
		// ... we return false and exit the function
		return FALSE;
	// ... if the path is not readable
	}elseif(!is_readable($directory))
	{
		// ... we return false and exit the function
		return FALSE;
	// ... else if the path is readable
	}else{
		// we open the directory
		$handle = opendir($directory);
		$postproc = AKFactory::getPostProc();
		// and scan through the items inside
		while (FALSE !== ($item = readdir($handle)))
		{
			// if the filepointer is not the current directory
			// or the parent directory
			if($item != '.' && $item != '..')
			{
				// we build the new path to delete
				$path = $directory.'/'.$item;
				// if the new path is a directory
				if(is_dir($path))
				{
					// we call this function with the new path
					recursive_remove_directory($path);
				// if the new path is a file
				}else{
					// we remove the file
					$postproc->unlink($path);
				}
			}
		}
		// close the directory
		closedir($handle);
		// try to delete the now empty directory
		if(!$postproc->rmdir($directory))
		{
			// return false if not possible
			return FALSE;
		}
		// return success
		return TRUE;
	}
}
