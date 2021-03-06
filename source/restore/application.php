<?php
/**
 * Akeeba Restore
 * An AJAX-powered archive extraction library for JPA, JPS and ZIP archives
 *
 * @package   restore
 * @copyright Copyright (c)2008-2021 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

// Mini-controller for restore.php
if (!defined('KICKSTART'))
{
	// The observer class, used to report number of files and bytes processed
	class RestorationObserver extends AKAbstractPartObserver
	{
		public $compressedTotal = 0;
		public $uncompressedTotal = 0;
		public $filesProcessed = 0;

		public function update($object, $message)
		{
			if (!is_object($message))
			{
				return;
			}

			if (!array_key_exists('type', get_object_vars($message)))
			{
				return;
			}

			if ($message->type == 'startfile')
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
		'status'  => true,
		'message' => null
	);

	$enabled = AKFactory::get('kickstart.enabled', false);

	if ($enabled)
	{
		$task = getQueryParam('task');

		switch ($task)
		{
			case 'ping':
				// ping task - really does nothing!
				$timer = AKFactory::getTimer();
				$timer->enforce_min_exec_time();
				break;

			/**
			 * There are two separate steps here since we were using an inefficient restoration initialization method in
			 * the past. Now both startRestore and stepRestore are identical. The difference in behavior depends
			 * exclusively on the calling Javascript. If no serialized factory was passed in the request then we start a
			 * new restoration. If a serialized factory was passed in the request then the restoration is resumed. For
			 * this reason we should NEVER call AKFactory::nuke() in startRestore anymore: that would simply reset the
			 * extraction engine configuration which was done in masterSetup() leading to an error about the file being
			 * invalid (since no file is found).
			 */
			case 'startRestore':
			case 'stepRestore':
				if ($task == 'startRestore')
				{
					// Fetch path to the site root from the restoration.php file, so we can tell the engine where it should operate
					$siteRoot = AKFactory::get('kickstart.setup.destdir', '');

					// Before starting, read and save any custom AddHandler directive
					$phpHandlers = getPhpHandlers($siteRoot);
					AKFactory::set('kickstart.setup.phphandlers', $phpHandlers);

					// If the Stealth Mode is enabled, create the .htaccess file
					if (AKFactory::get('kickstart.stealth.enable', false))
					{
						createStealthURL($siteRoot);
					}
					// No stealth mode, but we have custom handler directives, must write our own file
					elseif ($phpHandlers)
					{
						writePhpHandlers($siteRoot);
					}
				}

				/**
				 * First try to run the filesystem zapper (remove all existing files and folders). If the Zapper is
				 * disabled or has already finished running we will get a FALSE result. Otherwise it's a status array
				 * which we can pass directly back to the caller.
				 */
				$ret = runZapper();

				// If the Zapper had a step to run we stop here and return its status array to the caller.
				if ($ret !== false)
				{
					$retArray = array_merge($retArray, $ret);

					break;
				}

				$engine   = AKFactory::getUnarchiver(); // Get the engine
				$observer = new RestorationObserver(); // Create a new observer
				$engine->attach($observer); // Attach the observer
				$engine->tick();
				$ret = $engine->getStatusArray();

				if ($ret['Error'] != '')
				{
					$retArray['status']  = false;
					$retArray['done']    = true;
					$retArray['message'] = $ret['Error'];
				}
				elseif (!$ret['HasRun'])
				{
					$retArray['files']    = $observer->filesProcessed;
					$retArray['bytesIn']  = $observer->compressedTotal;
					$retArray['bytesOut'] = $observer->uncompressedTotal;
					$retArray['status']   = true;
					$retArray['done']     = true;
				}
				else
				{
					$retArray['files']    = $observer->filesProcessed;
					$retArray['bytesIn']  = $observer->compressedTotal;
					$retArray['bytesOut'] = $observer->uncompressedTotal;
					$retArray['status']   = true;
					$retArray['done']     = false;
					$retArray['factory']  = AKFactory::serialize();
				}

				$timer = AKFactory::getTimer();
				$timer->enforce_min_exec_time();

				break;

			case 'finalizeRestore':
				$root = AKFactory::get('kickstart.setup.destdir');
				// Remove the installation directory
				recursive_remove_directory($root . '/installation');

				$postproc = AKFactory::getPostProc();

				/**
				 * Should I rename the htaccess.bak and web.config.bak files back to their live filenames...?
				 */
				$renameFiles = AKFactory::get('kickstart.setup.postrenamefiles', true);

				if ($renameFiles)
				{
					// Rename htaccess.bak to .htaccess
					if (file_exists($root . '/htaccess.bak'))
					{
						if (file_exists($root . '/.htaccess'))
						{
							$postproc->unlink($root . '/.htaccess');
						}

						$postproc->rename($root . '/htaccess.bak', $root . '/.htaccess');
					}

					// Rename htaccess.bak to .htaccess
					if (file_exists($root . '/web.config.bak'))
					{
						if (file_exists($root . '/web.config'))
						{
							$postproc->unlink($root . '/web.config');
						}

						$postproc->rename($root . '/web.config.bak', $root . '/web.config');
					}
				}

				// Remove restoration.php
				$basepath = KSROOTDIR;
				$basepath = rtrim(str_replace('\\', '/', $basepath), '/');

				if (!empty($basepath))
				{
					$basepath .= '/';
				}

				$postproc->unlink($basepath . 'restoration.php');

				// Import a custom finalisation file
				$filename = dirname(__FILE__) . '/restore_finalisation.php';

				if (file_exists($filename))
				{
					// opcode cache busting before including the filename
					if (function_exists('opcache_invalidate'))
					{
						opcache_invalidate($filename);
					}

					if (function_exists('apc_compile_file'))
					{
						apc_compile_file($filename);
					}

					if (function_exists('wincache_refresh_if_changed'))
					{
						wincache_refresh_if_changed([$filename]);
					}

					if (function_exists('xcache_asm'))
					{
						xcache_asm($filename);
					}

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
	if (!$enabled)
	{
		// Maybe the user failed to enter any information
		$retArray['status']  = false;
		$retArray['message'] = AKText::_('ERR_INVALID_LOGIN');
	}

	// JSON encode the message
	$json = json_encode($retArray);

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
	if (substr($directory, -1) == '/')
	{
		$directory = substr($directory, 0, -1);
	}

	// if the path is not valid or is not a directory ...
	if (!file_exists($directory) || !is_dir($directory))
	{
		// ... we return false and exit the function
		return false;
		// ... if the path is not readable
	}
	elseif (!is_readable($directory))
	{
		// ... we return false and exit the function
		return false;
		// ... else if the path is readable
	}
	else
	{
		// we open the directory
		$handle   = opendir($directory);
		$postproc = AKFactory::getPostProc();

		// and scan through the items inside
		while (false !== ($item = readdir($handle)))
		{
			// if the filepointer is not the current directory
			// or the parent directory

			if ($item != '.' && $item != '..')
			{
				// we build the new path to delete
				$path = $directory . '/' . $item;

				// if the new path is a directory
				if (is_dir($path))
				{
					// we call this function with the new path
					recursive_remove_directory($path);
					// if the new path is a file
				}
				else
				{
					// we remove the file
					$postproc->unlink($path);
				}
			}
		}

		// close the directory
		closedir($handle);

		// try to delete the now empty directory
		if (!$postproc->rmdir($directory))
		{
			// return false if not possible
			return false;
		}

		// return success
		return true;
	}
}

function createStealthURL($siteRoot = '')
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

	if ($siteRoot)
	{
		$siteRoot = rtrim($siteRoot, '/').'/';
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

	$customHandlers = portPhpHandlers();

	// Port any custom handlers in the stealth file
	if ($customHandlers)
	{
		$stealthHtaccess .= "\n".$customHandlers."\n";
	}

	// Write the new .htaccess, removing the old one first
	$postproc = AKFactory::getpostProc();
	$postproc->unlink($siteRoot.'.htaccess');
	$tempfile = $postproc->processFilename($siteRoot.'.htaccess');
	@file_put_contents($tempfile, $stealthHtaccess);
	$postproc->process();
}

/**
 * Checks if there is an .htaccess file and has any AddHandler directive in it.
 * In that case, we return the affected lines so they could be stored for later use
 *
 * @return  array
 */
function getPhpHandlers($root = null)
{
	if (!$root)
	{
		$root = AKKickstartUtils::getPath();
	}

	$htaccess   = $root.'/.htaccess';
	$directives = array();

	if (!file_exists($htaccess))
	{
		return $directives;
	}

	$contents   = file_get_contents($htaccess);
	$directives = AKUtilsHtaccess::extractHandler($contents);
	$directives = explode("\n", $directives);

	return $directives;
}

/**
 * Fetches any stored php handler directive stored inside the factory and creates a string with the correct markers
 *
 * @return string
 */
function portPhpHandlers()
{
	$phpHandlers = AKFactory::get('kickstart.setup.phphandlers', array());

	if (!$phpHandlers)
	{
		return '';
	}

	$customHandler  = "### AKEEBA_KICKSTART_PHP_HANDLER_BEGIN ###\n";
	$customHandler .= implode("\n", $phpHandlers)."\n";
	$customHandler .= "### AKEEBA_KICKSTART_PHP_HANDLER_END ###\n";

	return $customHandler;
}

function writePhpHandlers($siteRoot = '')
{
	$contents = portPhpHandlers();

	if (!$contents)
	{
		return;
	}

	if ($siteRoot)
	{
		$siteRoot = rtrim($siteRoot, '/').'/';
	}

	// Write the new .htaccess, removing the old one first
	$postproc = AKFactory::getpostProc();
	$postproc->unlink($siteRoot.'.htaccess');
	$tempfile = $postproc->processFilename($siteRoot.'.htaccess');
	@file_put_contents($tempfile, $contents);
	$postproc->process();
}
