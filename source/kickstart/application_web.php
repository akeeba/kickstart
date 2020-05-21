<?php
/**
 * Akeeba Kickstart
 * An AJAX-powered archive extraction tool
 *
 * @package   kickstart
 * @copyright Copyright (c)2008-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

/**
 * Routes the Kickstart web application
 */
function kickstart_application_web()
{
	if (!akeeba_common_wrongphp(array(
		'minPHPVersion'         => defined('KICKSTART_MIN_PHP') ? KICKSTART_MIN_PHP : "5.6.0",
		'recommendedPHPVersion' => defined('KICKSTART_RECOMMENDED_PHP') ? KICKSTART_RECOMMENDED_PHP : '7.3',
		'softwareName'          => "Akeeba Kickstart",
	))) {
		die;
	}

	$retArray = array(
		'status'  => true,
		'message' => null
	);

	$task = getQueryParam('task', 'display');
	$json = getQueryParam('json');
	$ajax = true;

	switch ($task)
	{
		case 'checkTempdir':
			$retArray['status'] = false;

			if (!empty($json))
			{
				$data = json_decode($json, true);
				$dir  = @$data['kickstart.ftp.tempdir'];

				if (!empty($dir))
				{
					$retArray['status'] = is_writable($dir);
				}
			}
			break;

		case 'checkFTP':
			$retArray['status'] = false;

			if (!empty($json))
			{
				$data = json_decode($json, true);

				foreach ($data as $key => $value)
				{
					AKFactory::set($key, $value);
				}

				if ($data['type'] == 'ftp')
				{
					$ftp = new AKPostprocFTP();
				}
				else
				{
					$ftp = new AKPostprocSFTP();
				}

				$retArray['message'] = $ftp->getError();
				$retArray['status']  = empty($retArray['message']);
			}
			break;

		case 'ftpbrowse':
			if (!empty($json))
			{
				$data = json_decode($json, true);

				$retArray =
					getListing($data['directory'], $data['host'], $data['port'], $data['username'], $data['password'], $data['passive'], $data['ssl']);
			}
			break;

		case 'sftpbrowse':
			if (!empty($json))
			{
				$data = json_decode($json, true);

				$retArray =
					getSftpListing($data['directory'], $data['host'], $data['port'], $data['username'], $data['password']);
			}
			break;

		case 'startExtracting':
		case 'continueExtracting':
			// Look for configuration values
			$retArray['status'] = false;

			if (!empty($json))
			{
				if ($task == 'startExtracting')
				{
					AKFactory::nuke();
				}

				$oldJSON = $json;
				$json    = json_decode($json, true);

				if (is_null($json))
				{
					$json = stripslashes($oldJSON);
					$json = json_decode($json, true);
				}

				if (!empty($json))
				{
					foreach ($json as $key => $value)
					{
						if (substr($key, 0, 9) == 'kickstart')
						{
							AKFactory::set($key, $value);
						}
					}
				}

				// A "factory" variable will override all other settings.
				if (array_key_exists('factory', $json))
				{
					// Get the serialized factory
					$serialized = $json['factory'];
					AKFactory::unserialize($serialized);
					AKFactory::set('kickstart.enabled', true);
				}

				// Make sure that the destination directory is always set (req'd by both FTP and Direct Writes modes)
				$removePath = AKFactory::get('kickstart.setup.destdir', '');

				if (empty($removePath))
				{
					AKFactory::set('kickstart.setup.destdir', AKKickstartUtils::getPath());
				}

				if ($task == 'startExtracting')
				{
					// Before starting, read and save any custom AddHandler directive
					$phpHandlers = getPhpHandlers();
					AKFactory::set('kickstart.setup.phphandlers', $phpHandlers);

					// If the Stealth Mode is enabled, create the .htaccess file
					if (AKFactory::get('kickstart.stealth.enable', false))
					{
						createStealthURL();
					}
					// No stealth mode, but we have custom handler directives, must write our own file
					elseif ($phpHandlers)
					{
						writePhpHandlers();
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
				$observer = new ExtractionObserver(); // Create a new observer
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

				if (!is_null($observer->totalSize))
				{
					$retArray['totalsize'] = $observer->totalSize;
					$retArray['filelist']  = $observer->fileList;
				}

				$retArray['Warnings'] = $ret['Warnings'];
				$retArray['lastfile'] = empty($observer->lastFile) ? 'Extracting, please wait...' : $observer->lastFile;

				$timer = AKFactory::getTimer();
				$timer->enforce_min_exec_time();
			}
			break;

		case 'cleanUp':
			if (!empty($json))
			{
				$json = json_decode($json, true);

				if (array_key_exists('factory', $json))
				{
					// Get the serialized factory
					$serialized = $json['factory'];
					AKFactory::unserialize($serialized);
					AKFactory::set('kickstart.enabled', true);
				}
			}

			$unarchiver = AKFactory::getUnarchiver(); // Get the engine
			$postProc   = AKFactory::getPostProc();

			finalizeAfterRestoration($unarchiver, $postProc);
			removeKickstartFiles($postProc);
			clearCodeCaches();

			break;

		case 'display':
			$ajax = false;
			echoPage();
			break;

		case 'isJoomla':
			$ajax = true;

			if (!empty($json))
			{
				$json = json_decode($json, true);

				if (array_key_exists('factory', $json))
				{
					// Get the serialized factory
					$serialized = $json['factory'];
					AKFactory::unserialize($serialized);
					AKFactory::set('kickstart.enabled', true);
				}
			}

			$path     = AKFactory::get('kickstart.setup.destdir', '');
			$path     = rtrim($path, '/\\');
			$isJoomla = @is_dir($path . '/administrator');

			if ($isJoomla)
			{
				$isJoomla = @is_dir($path . '/libraries/joomla');
			}

			$retArray = $isJoomla;

			break;

		case 'listArchives':
			$ajax = true;

			$path = null;

			if (!empty($json))
			{
				$json = json_decode($json, true);

				if (array_key_exists('path', $json))
				{
					$path = $json['path'];
				}
			}

			if (empty($path) || !@is_dir($path))
			{
				$filelist = null;
			}
			else
			{
				$filelist = AKKickstartUtils::getArchivesAsOptions($path);
			}

			if (empty($filelist))
			{
				$retArray =
					'<a href="https://www.akeebabackup.com/documentation/troubleshooter/ksnoarchives.html" target="_blank">' .
					AKText::_('NOARCHIVESCLICKHERE')
					. '</a>';
			}
			else
			{
				$retArray = '<select id="kickstart.setup.sourcefile">' . $filelist . '</select>';
			}

			break;

		default:
			$ajax = true;

			if (!empty($json))
			{
				$params = json_decode($json, true);
			}
			else
			{
				$params = array();
			}

			$retArray = callExtraFeature($task, $params);

			break;
	}

	if ($ajax)
	{
		// JSON encode the message
		$json = json_encode($retArray);

		// Return the message
		echo "###$json###";
	}
}
