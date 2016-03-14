<?php
/**
 * Akeeba Kickstart
 * A JSON-powered archive extraction tool
 *
 * @copyright   2010-2016 Nicholas K. Dionysopoulos / AkeebaBackup.com
 * @license     GNU GPL v2 or - at your option - any later version
 * @package     akeebabackup
 * @subpackage  kickstart
 */

// Register additional feature classes
callExtraFeature();

$retArray = array(
	'status'	=> true,
	'message'	=> null
);

$task = getQueryParam('task', 'display');
$json = getQueryParam('json');
$ajax = true;

switch($task)
{
	case 'checkTempdir':
		$retArray['status'] = false;

		if(!empty($json))
		{
			$data = json_decode($json, true);
			$dir = @$data['kickstart.ftp.tempdir'];

			if(!empty($dir))
			{
				$retArray['status'] = is_writable($dir);
			}
		}
		break;

	case 'checkFTP':
		$retArray['status'] = false;

		if(!empty($json))
		{
			$data = json_decode($json, true);

			foreach($data as $key => $value)
			{
				AKFactory::set($key, $value);
			}

            if($data['type'] == 'ftp')
            {
                $ftp = new AKPostprocFTP();
            }
            else
            {
                $ftp = new AKPostprocSFTP();
            }

			$retArray['message'] = $ftp->getError();
			$retArray['status'] = empty($retArray['message']);
		}
		break;

    case 'ftpbrowse':
        if(!empty($json))
        {
            $data = json_decode($json, true);

            $retArray = getListing($data['directory'], $data['host'], $data['port'], $data['username'], $data['password'], $data['passive'], $data['ssl']);
        }
        break;

    case 'sftpbrowse':
        if(!empty($json))
        {
            $data = json_decode($json, true);

            $retArray = getSftpListing($data['directory'], $data['host'], $data['port'], $data['username'], $data['password']);
        }
        break;

	case 'startExtracting':
	case 'continueExtracting':
		// Look for configuration values
		$retArray['status'] = false;

		if(!empty($json))
		{
			if($task == 'startExtracting')
            {
                AKFactory::nuke();
            }

			$oldJSON = $json;
			$json = json_decode($json, true);

			if(is_null($json))
            {
				$json = stripslashes($oldJSON);
				$json = json_decode($json, true);
			}

			if(!empty($json))
            {
                foreach($json as $key => $value)
                {
                    if( substr($key,0,9) == 'kickstart' )
                    {
                        AKFactory::set($key, $value);
                    }
                }
            }

			// A "factory" variable will override all other settings.
			if( array_key_exists('factory', $json) )
			{
				// Get the serialized factory
				$serialized = $json['factory'];
				AKFactory::unserialize($serialized);
				AKFactory::set('kickstart.enabled', true);
			}

			// Make sure that the destination directory is always set (req'd by both FTP and Direct Writes modes)
			$removePath = AKFactory::get('kickstart.setup.destdir','');

			if(empty($removePath))
            {
                AKFactory::set('kickstart.setup.destdir', AKKickstartUtils::getPath());
            }

			if($task=='startExtracting')
			{
				// If the Stealth Mode is enabled, create the .htaccess file
				if( AKFactory::get('kickstart.stealth.enable', false) )
				{
					createStealthURL();
				}
			}

			$engine   = AKFactory::getUnarchiver(); // Get the engine
			$observer = new ExtractionObserver(); // Create a new observer
			$engine->attach($observer); // Attach the observer
			$engine->tick();
			$ret = $engine->getStatusArray();

			if( $ret['Error'] != '' )
			{
				$retArray['status']  = false;
				$retArray['done']    = true;
				$retArray['message'] = $ret['Error'];
			}
			elseif( !$ret['HasRun'] )
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

			if(!is_null($observer->totalSize))
			{
				$retArray['totalsize'] = $observer->totalSize;
				$retArray['filelist']  = $observer->fileList;
			}

			$retArray['Warnings'] = $ret['Warnings'];
			$retArray['lastfile'] = $observer->lastFile;
		}
		break;

	case 'cleanUp':
		if(!empty($json))
		{
			$json = json_decode($json, true);

			if( array_key_exists('factory', $json) )
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
		$renames = $unarchiver->renameFiles;
		if(!empty($renames))
        {
            foreach ($renames as $original => $renamed)
            {
                $engine->rename($renamed, $original);
            }
        }

		// 3. Delete the archive
		foreach( $unarchiver->archiveList as $archive )
		{
			$engine->unlink( $archive );
		}

		// 4. Remove self
		$engine->unlink( basename(__FILE__) );

		// 5. Delete translations
		$dh = opendir(AKKickstartUtils::getPath());
		if($dh !== false)
		{
			$basename = basename(__FILE__, '.php');

			while( false !== $file = @readdir($dh) )
			{
				if( strstr($file, $basename.'.ini') )
				{
					$engine->unlink($file);
				}
			}
		}

		// 6. Delete cacert.pem
		$engine->unlink('cacert.pem');

		// 7. Delete jquery.min.js and json2.min.js
		$engine->unlink('jquery.min.js');
		$engine->unlink('json2.min.js');

		// 8. If OPcache is installed we need to reset it
		if (function_exists('opcache_reset'))
		{
			opcache_reset();
		}

		break;

	case 'display':
		$ajax = false;
		echoPage();
		break;

	case 'isJoomla':
		$ajax = true;

		if(!empty($json))
		{
			$json = json_decode($json, true);

			if( array_key_exists('factory', $json) )
			{
				// Get the serialized factory
				$serialized = $json['factory'];
				AKFactory::unserialize($serialized);
				AKFactory::set('kickstart.enabled', true);
			}
		}

		$path = AKFactory::get('kickstart.setup.destdir','');
		$path = rtrim($path, '/\\');
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

		if(!empty($json))
		{
			$json = json_decode($json, true);

			if( array_key_exists('path', $json) )
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
			$retArray = '<a href="https://www.akeebabackup.com/documentation/troubleshooter/ksnoarchives.html" target="_blank">' .
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

		if(!empty($json))
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

if($ajax)
{
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
