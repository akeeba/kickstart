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

/**
 * A high-level API abstraction for the Amazon S3 adapter
 */
class S3Engine
{
	private $accessKey = '';
	private $secretKey = '';
	private $useSSL = false;
	private $bucket = '';

	private $_lastDirectory = null;
	private $lastListing = null;

	private $_isConfigured = false;

	/**
	 * Creates and configures the engine class
	 *
	 * @param array $config
	 */
	public function __construct($config)
	{
		$configParams = array('accessKey', 'secretKey', 'useSSL', 'bucket');
		foreach ($configParams as $param)
		{
			if (array_key_exists($param, $config))
			{
				$this->$param = $config[$param];
			}
		}

		S3Adapter::setAuth($this->accessKey, $this->secretKey);
		S3Adapter::$useSSL = $this->useSSL;

		$this->_isConfigured = true;
	}

	/**
	 * Lists the files in a directory. Each file record is an array consisting
	 * of the following keys: filename, time, size.
	 *
	 * @param string $directory Directory relative to the bucket's root
	 * @param bool   $useCache  (optional, def. true) When true, S3Engine "remembers" the contents of the last
	 *                          directory and if you ask to list it again it will not contact S3.
	 *
	 * @return array
	 */
	public function getFiles($directory, $useCache = true)
	{
		// name, time, size, hash
		if (!$this->_isConfigured)
		{
			throw new S3Exception(__CLASS__ . ' is not configured yet');
		}
		$everything = $this->_listContents($directory, $useCache);

		if ($directory != '/')
		{
			$directory = trim($directory, '/') . '/';
		}

		$files     = array();
		$dirLength = strlen($directory);

		if (count($everything))
		{
			foreach ($everything as $path => $info)
			{
				if (array_key_exists('size', $info) && (substr($path, -1) != '/'))
				{
					if (substr($path, 0, $dirLength) == $directory)
					{
						$path = substr($path, $dirLength);
					}
					$path    = trim($path, '/');
					$files[] = array(
						'filename' => $path,
						'time'     => $info['time'],
						'size'     => $info['size']
					);
				}
			}
		}

		return $files;
	}

	/**
	 * Internal function to list the contents of a directory inside a bucket. To
	 * list the contents of the bucket's root, use a directory value of '/' or
	 * null.
	 *
	 * @param string|null $directory The directory to list
	 * @param bool        $useCache  If true (default), repeated requests with the same directory do not result in
	 *                               Amazon S3 API calls
	 *
	 * @return array
	 */
	private function _listContents($directory = null, $useCache = true)
	{
		if (($this->_lastDirectory != $directory) || !$useCache)
		{
			if ($directory == '/')
			{
				$directory = null;
			}
			else
			{
				$directory = trim($directory, '/') . '/';
			}
			$this->lastListing = S3Adapter::getBucket($this->bucket, $directory, null, null, '/', true);
		}

		return $this->lastListing;
	}

	/**
	 * Lists the folders in a directory.
	 *
	 * @param string $directory Directory relative to the bucket's root
	 * @param bool   $useCache  (optional, def. true) When true, S3Engine "remembers" the contents of the last
	 *                          directory and if you ask to list it again it will not contact S3.
	 *
	 * @return array List of folders
	 */
	public function getFolders($directory, $useCache = true)
	{
		if (!$this->_isConfigured)
		{
			throw new S3Exception(__CLASS__ . ' is not configured yet');
		}

		if ($directory != '/')
		{
			$directory = trim($directory, '/') . '/';
		}

		$everything = $this->_listContents($directory, $useCache);

		$folders   = array();
		$dirLength = strlen($directory);
		if (count($everything))
		{
			foreach ($everything as $path => $info)
			{
				if (!array_key_exists('size', $info) && (substr($path, -1) == '/'))
				{
					if (substr($path, 0, $dirLength) == $directory)
					{
						$path = substr($path, $dirLength);
					}
					$path      = trim($path, '/');
					$folders[] = $path;
				}
			}
		}

		return $folders;
	}

	/**
	 * Lists all the buckets owned by the current user
	 *
	 * @param bool $useCache If true, subsequent calls will not cause an Amazon S3 API call
	 *
	 * @return array List of bucket names
	 */
	public function listBuckets($useCache = true)
	{
		static $buckets = null;

		if (is_null($buckets) || !$useCache)
		{
			$buckets = S3Adapter::listBuckets(false);
		}

		return $buckets;
	}

	/**
	 * Change the active bucket
	 *
	 * @param string $bucket
	 */
	public function setBucket($bucket)
	{
		$this->bucket = $bucket;
	}

	/**
	 * Downloads a part of a file to disk. If it's the first part, the file is
	 * created afresh. If it is any other part, it is appended to the target file.
	 *
	 * @param string $file     The path of the S3 object (file) to download, relative to the bucket
	 * @param string $target   Absolute path of the file to be written to
	 * @param int    $part     The part to download, default 1, up to the max number of parts for this object
	 * @param int    $partSize Part size, in bytes, default is 512Kb
	 */
	public function downloadPart($file, $target, $part = 1, $partSize = 524378)
	{
		if ($part < 1)
		{
			throw new S3Exception('Invalid part number ' . $part);
		}
		$parts = $this->partsForFile($file, $partSize);
		if ($part > $parts)
		{
			throw new S3Exception('Invalid part number ' . $part);
		}

		if ($part == 1)
		{
			$fp = @fopen($target, 'wb');
		}
		else
		{
			$fp = @fopen($target, 'ab');
		}
		if ($fp === false)
		{
			throw new S3Exception("Can not open $target for writing; download failed");
		}

		$from = $partSize * ($part - 1);
		$to   = $from + $partSize - 1;

		$file = trim($file, '/');
		$data = S3Adapter::getObject($this->bucket, $file, false, $from, $to);

		if ($data === false)
		{
			throw new S3Exception('Unspecified error downloading ' . $file);
		}

		fwrite($fp, $data->body);
		unset($data);

		fclose($fp);
	}

	/**
	 * Return the number of parts you need to download a given file
	 *
	 * @param string $file     The file you want
	 * @param int    $partSize Part size, in bytes
	 */
	public function partsForFile($file, $partSize = 524378)
	{
		$file = trim($file, '/');
		$info = S3Adapter::getObjectInfo($this->bucket, $file, true);
		if ($info === false)
		{
			throw new S3Exception('File not found in S3 bucket', 404);
		}
		$size = $info['size'];

		return ceil($size / $partSize);
	}
}
