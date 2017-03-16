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
 * @copyright   2008-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL v2 or - at your option - any later version
 * @package     joomla
 * @subpackage  joomlastart
 */
class JoomlastartDownload
{
	/**
	 * @var  string  Where to get the latest Joomla! download from
	 */
	private static $downloadPageURL = 'http://www.joomla.org/download.html';
	/**
	 * @var  array  Parameters passed from the GUI when importing from URL
	 */
	private $params = array();

	/**
	 * @param   $params  A paramters array, as sent by the user interface
	 *
	 * @return  array|bool  A return status array
	 */
	public function importFromURL($params)
	{
		$this->params = $params;

		// Fetch data
		$filename      = $this->getParam('file');
		$frag          = $this->getParam('frag', -1);
		$totalSize     = $this->getParam('totalSize', -1);
		$doneSize      = $this->getParam('doneSize', -1);
		$localFilename = $this->getParam('localFile', basename($filename));

		debugMsg('Importing from URL');
		debugMsg('  file      : ' . $filename);
		debugMsg('  frag      : ' . $frag);
		debugMsg('  totalSize : ' . $totalSize);
		debugMsg('  doneSize  : ' . $doneSize);

		// Init retArray
		$retArray = array(
			"status"    => true,
			"error"     => '',
			"frag"      => $frag,
			"totalSize" => $totalSize,
			"doneSize"  => $doneSize,
			"percent"   => 0,
		);

		try
		{
			AKFactory::set('kickstart.tuning.max_exec_time', '5');
			AKFactory::set('kickstart.tuning.run_time_bias', '75');
			$timer = new AKCoreTimer();
			$start = $timer->getRunningTime(); // Mark the start of this download
			$break = false; // Don't break the step

			while (($timer->getTimeLeft() > 0) && !$break)
			{
				// Figure out where on Earth to put that file
				$local_file = KSROOTDIR . '/' . $localFilename;

				debugMsg("- Importing from $filename");

				// Do we have to initialize the file?
				if ($frag == -1)
				{
					debugMsg("-- First frag, killing local file");
					// Currently downloaded size
					$doneSize = 0;

					// Delete and touch the output file
					@unlink($local_file);
					$fp = @fopen($local_file, 'wb');

					if ($fp !== false)
					{
						@fclose($fp);
					}

					// Init
					$frag = 0;

					debugMsg("-- First frag, getting the file size");
					$retArray['totalSize'] = $this->getFilesize($filename);
					$totalSize             = $retArray['totalSize'];
				}

				// Calculate from and length
				$length = 1048576;
				$from   = $frag * $length;
				$to     = $length + $from - 1;

				// Try to download the first frag
				$required_time = 1.0;
				debugMsg("-- Importing frag $frag, byte position from/to: $from / $to");

				try
				{
					$result = $this->downloadAndReturn($filename, $from, $to);

					if ($result === false)
					{
						throw new Exception("Could not download from $filename", 1);
					}
				}
				catch (Exception $e)
				{
					$result = false;
					$error  = $e->getMessage();
				}

				if ($result === false)
				{
					// Failed download
					if ($frag == 0)
					{
						// Failure to download first frag = failure to download. Period.
						$retArray['status'] = false;
						$retArray['error']  = $error;

						debugMsg("-- Download FAILED");

						return $retArray;
					}
					else
					{
						// Since this is a staggered download, consider this normal and finish
						$frag = -1;
						debugMsg("-- Import complete");
						$doneSize = $totalSize;
						$break    = true;
						continue;
					}
				}

				// Add the currently downloaded frag to the total size of downloaded files
				if ($result)
				{
					clearstatcache();
					$filesize = strlen($result);
					debugMsg("-- Successful download of $filesize bytes");
					$doneSize += $filesize;

					// Append the file
					$fp = @fopen($local_file, 'ab');

					if ($fp === false)
					{
						debugMsg("-- Can't open local file $local_file for writing");
						// Can't open the file for writing
						$retArray['status'] = false;
						$retArray['error']  = 'Can\'t write to the local file ' . $local_file;

						return $retArray;
					}

					fwrite($fp, $result);
					fclose($fp);

					debugMsg("-- Appended data to local file $local_file");

					$frag++;

					debugMsg("-- Proceeding to next fragment, frag $frag");
				}

				// Advance the frag pointer and mark the end
				$end = $timer->getRunningTime();

				// Do we predict that we have enough time?
				$required_time = max(1.1 * ($end - $start), $required_time);

				if ($required_time > (10 - $end + $start))
				{
					$break = true;
				}

				$start = $end;
			}

			if ($frag == -1)
			{
				$percent = 100;
			}
			elseif ($doneSize <= 0)
			{
				$percent = 0;
			}
			else
			{
				if ($totalSize > 0)
				{
					$percent = 100 * ($doneSize / $totalSize);
				}
				else
				{
					$percent = 0;
				}
			}

			// Update $retArray
			$retArray = array(
				"status"    => true,
				"error"     => '',
				"frag"      => $frag,
				"totalSize" => $totalSize,
				"doneSize"  => $doneSize,
				"percent"   => $percent,
			);
		}
		catch (Exception $e)
		{
			debugMsg("EXCEPTION RAISED:");
			debugMsg($e->getMessage());
			$retArray['status'] = false;
			$retArray['error']  = $e->getMessage();
		}

		return $retArray;
	}

	private function getParam($key, $default = null)
	{
		if (array_key_exists($key, $this->params))
		{
			return $this->params[$key];
		}
		else
		{
			return $default;
		}
	}

	/**
	 * Tries to find out the file size of the given URL
	 *
	 * @param   string $url The URL of the file we will determine the size
	 *
	 * @return  integer  The file size in bytes or -1 if we can't figure it out
	 */
	public function getFilesize($url)
	{
		$result = -1;

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_NOBODY, true);
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		@curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

		if (defined('AKEEBA_CACERT_PEM'))
		{
			curl_setopt($ch, CURLOPT_CAINFO, AKEEBA_CACERT_PEM);
		}

		$data = curl_exec($ch);
		curl_close($ch);

		if ($data)
		{
			$content_length = "unknown";
			$status         = "unknown";

			if (preg_match("/^HTTP\/1\.[01] (\d\d\d)/", $data, $matches))
			{
				$status = (int) $matches[1];
			}

			if (preg_match("/Content-Length: (\d+)/", $data, $matches))
			{
				$content_length = (int) $matches[1];
			}

			if ($status == 200 || ($status > 300 && $status <= 308))
			{
				$result = $content_length;
			}
		}

		return $result;
	}

	/**
	 * Downloads a (part of a) file and returns its contents
	 *
	 * @param   string  $url  The URL to download from
	 * @param   integer $from Byte position to start downloading, leave blank to download entire file
	 * @param   integer $to   Byte position to stop downloading, leave blank to download entire file
	 *
	 * @return  mixed  A string with the contents if it's successful
	 *
	 * @throws  Exception  An Exception describing the reason of download failure
	 */
	public function downloadAndReturn($url, $from = null, $to = null)
	{
		$ch = curl_init();

		if (empty($from))
		{
			$from = 0;
		}

		if (empty($to))
		{
			$to = 0;
		}

		if ($to < $from)
		{
			$temp = $to;
			$to   = $from;
			$from = $temp;
			unset($temp);
		}

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		@curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

		if (defined('AKEEBA_CACERT_PEM'))
		{
			curl_setopt($ch, CURLOPT_CAINFO, AKEEBA_CACERT_PEM);
		}

		if (!(empty($from) && empty($to)))
		{
			curl_setopt($ch, CURLOPT_RANGE, "$from-$to");
		}

		$result = curl_exec($ch);

		$errno       = curl_errno($ch);
		$errmsg      = curl_error($ch);
		$http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		if ($result === false)
		{
			$error = "cURL error $errno: $errmsg";
		}
		elseif ($http_status > 299)
		{
			$result = false;
			$errno  = $http_status;
			$error  = "Unexpected HTTP status $http_status";
		}

		curl_close($ch);

		if ($result === false)
		{
			throw new Exception($error, $errno);
		}
		else
		{
			return $result;
		}
	}

	public function getJoomlaDownloadURL()
	{
		// Init retArray
		$retArray = array(
			"status"  => true,
			"error"   => '',
			"url"     => 'http://joomlacode.org/gf/download/frsrelease/18838/86936/Joomla_3.2.0-Stable-Full_Package.zip',
			"needftp" => false,
		);

		// Do I need to enable the FTP mode?
		$fp = @fopen('joomla.zip', 'ab');
		if ($fp === false)
		{
			$retArray['needftp'] = true;
		}
		else
		{
			@fclose($fp);
		}

		try
		{
			$pageContent = $this->downloadAndReturn(self::$downloadPageURL);

			$pos_start = stripos($pageContent, 'class="download"');

			if ($pos_start === false)
			{
				throw new Exception('Could not find the link to the latest Joomla! version in ' . self::$downloadPageURL);
			}

			$pos_end = stripos($pageContent, '>', $pos_start);

			if ($pos_end === false)
			{
				throw new Exception('Could not find the link to the latest Joomla! version in ' . self::$downloadPageURL);
			}

			$innerContent = substr($pageContent, $pos_start, $pos_end - $pos_end - 1);
			$pos_start    = stripos($innerContent, 'href="');
			$pos_end      = stripos($innerContent, '"', $pos_start + 6);

			$retArray['url'] = substr($innerContent, $pos_start + 6, $pos_end - $pos_start - 6);
		}
		catch (Exception $e)
		{
			debugMsg("EXCEPTION RAISED:");
			debugMsg($e->getMessage());
			$retArray['status'] = false;
			$retArray['error']  = $e->getMessage();
		}

		return $retArray;
	}
}