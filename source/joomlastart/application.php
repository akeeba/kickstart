<?php
/**
 * Joomla! Start
 *
 * Allows you to download and install Joomla! on your server, without having
 * to manually upload / download anything.
 *
 * This tool is derived from Akeeba Kickstart, the on-line archive extraction
 * tool by Akeeba Ltd.
 *
 * @copyright Copyright (c)2008-2018 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL v2 or - at your option - any later version
 * @package     joomla
 * @subpackage  joomlastart
 */

function getTranslationStrings()
{
	$translation = AKText::getInstance();

	return $translation->asJavascript();
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
			$ftp                 = new AKPostprocFTP();
			$retArray['message'] = $ftp->getError();
			$retArray['status']  = empty($retArray['message']);
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
			$retArray['lastfile'] = $observer->lastFile;
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
		$engine     = AKFactory::getPostProc();

		// 1. Remove installation
		recursive_remove_directory('installation');

		// 2. Run the renames, backwards
		$renameBack = AKFactory::get('kickstart.setup.renameback', true);

		if ($renameBack)
		{
			$renames = $unarchiver->renameFiles;

			if (!empty($renames))
			{
				foreach ($renames as $original => $renamed)
				{
					$engine->rename($renamed, $original);
				}
			}
		}

		// 3. Delete the archive
		foreach ($unarchiver->archiveList as $archive)
		{
			$engine->unlink($archive);
		}

		// 4. Remove ourselves
		$engine->unlink(basename(__FILE__));

		// 5. Delete translations
		$dh = opendir(AKKickstartUtils::getPath());
		if ($dh !== false)
		{
			$basename = basename(__FILE__, '.php');
			while (false !== $file = @readdir($dh))
			{
				if (strstr($file, $basename . '.ini'))
				{
					$engine->unlink($file);
				}
			}
		}

		// 6. Delete cacert.pem
		$engine->unlink('cacert.pem');

		// 7. If OPcache is installed we need to reset it
		if (function_exists('opcache_reset'))
		{
			opcache_reset();
		}
		// Also do that for APC cache
		elseif (function_exists('apc_clear_cache'))
		{
			@apc_clear_cache();
		}

		break;

	case 'display':
		$ajax = false;
		echoPage();
		break;

	case 'urlimport':
		$ajax = true;

		if (!empty($json))
		{
			$params = json_decode($json, true);
		}
		else
		{
			$params = array();
		}

		$downloadHelper = new JoomlastartDownload();
		$retArray       = $downloadHelper->importFromURL($params);

		break;

	default:
		$ajax = true;

		return $retArray;

		break;
}

if ($ajax)
{
	// JSON encode the message
	$json = json_encode($retArray);

	// Return the message
	echo "###$json###";
}
