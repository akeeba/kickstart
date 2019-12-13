<?php
/**
 * Akeeba Kickstart
 * An AJAX-powered archive extraction tool
 *
 * @package   kickstart
 * @copyright Copyright (c)2008-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

class AkeebaMinibuild
{
	private $javascript = '';

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

		$this->javascript = $this->collectJavaScript($buildFileDir);
		$output           = '';
		$lines            = file($buildfile);

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

			// Preprocess the PHP file and _then_ include it.
			$fakePath = 'kick://' . ltrim($path, '/\\');
			file_put_contents($fakePath, "<?php \n" . $this->prepareFile($path));

			@include_once $fakePath;
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
			// Insert JavaScript where required
			if (trim($l) === '//##MINIBUILD_JAVASCRIPT##')
			{
				$ret .= $this->javascript . "\n";

				continue;
			}

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
		$fileString     = file_get_contents($path);
		$filteredString = '';

		$tokens            = token_get_all($fileString);
		$flagInsideHeredoc = false;
		$forceNewLine      = false;

		foreach ($tokens as $token)
		{
			$tokenType = null;

			if (is_array($token))
			{
				$tokenType = $token[0];
				$token     = $token[1];
			}

			// Is this the start of a HEREDOC?
			if ($tokenType == T_START_HEREDOC)
			{
				$flagInsideHeredoc = true;
				$filteredString    .= $token;

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
			if (in_array($tokenType, array(T_DOC_COMMENT, T_COMMENT)))
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
				$forceNewLine   = false;
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
			// Insert JavaScript where required
			if (trim($l) === '//##MINIBUILD_JAVASCRIPT##')
			{
				$ret .= $this->javascript . "\n";

				continue;
			}

			$ret .= rtrim($l) . "\n";
		}

		return $ret;
	}

	/**
	 * Collect the JavaScript files' contents into one big string
	 *
	 * @param   string  $buildFileDir  The base directory where the js subfolder is in
	 *
	 * @return  string  All the JavaScript files
	 */
	private function collectJavaScript($buildFileDir)
	{
		$jsDir = realpath($buildFileDir . '/js');
		$files = file($jsDir . '/manifest.txt');
		$ret = '';

		foreach ($files as $file)
		{
			// Ignore comments
			if (substr($file, 0, 1) == ';')
			{
				continue;
			}

			$content = @file_get_contents($jsDir . '/' . trim($file));

			if (empty($content))
			{
				continue;
			}

			$ret .= $content . "\n\n";
		}

		return $ret;
	}
}

/**
 * Registers a fof:// stream wrapper
 */
class MiniBuildBuffer
{
	/**
	 * Stream position
	 *
	 * @var    integer
	 */
	public $position = 0;

	/**
	 * Buffer name
	 *
	 * @var    string
	 */
	public $name = null;

	/**
	 * Buffer hash
	 *
	 * @var    array
	 */
	public static $buffers = array();

	public static $canRegisterWrapper = null;

	/**
	 * Should I register the kick:// stream wrapper
	 *
	 * @return  bool  True if the stream wrapper can be registered
	 */
	public static function canRegisterWrapper()
	{
		if (is_null(static::$canRegisterWrapper))
		{
			static::$canRegisterWrapper = false;

			// Maybe the host has disabled registering stream wrappers altogether?
			if (!function_exists('stream_wrapper_register'))
			{
				return false;
			}

			// Check for Suhosin
			if (function_exists('extension_loaded'))
			{
				$hasSuhosin = extension_loaded('suhosin');
			}
			else
			{
				$hasSuhosin = -1; // Can't detect
			}

			if ($hasSuhosin !== true)
			{
				$hasSuhosin = defined('SUHOSIN_PATCH') ? true : -1;
			}

			if ($hasSuhosin === -1)
			{
				if (function_exists('ini_get'))
				{
					$hasSuhosin = false;

					$maxIdLength = ini_get('suhosin.session.max_id_length');

					if ($maxIdLength !== false)
					{
						$hasSuhosin = ini_get('suhosin.session.max_id_length') !== '';
					}
				}
			}

			// If we can't detect whether Suhosin is installed we won't proceed to prevent a White Screen of Death
			if ($hasSuhosin === -1)
			{
				return false;
			}

			// If Suhosin is installed but ini_get is not available we won't proceed to prevent a WSoD
			if ($hasSuhosin && !function_exists('ini_get'))
			{
				return false;
			}

			// If Suhosin is installed check if kick:// is whitelisted
			if ($hasSuhosin)
			{
				$whiteList = ini_get('suhosin.executor.include.whitelist');

				// Nothing in the whitelist? I can't go on, sorry.
				if (empty($whiteList))
				{
					return false;
				}

				$whiteList = explode(',', $whiteList);
				$whiteList = array_map(function ($x) { return trim($x); }, $whiteList);

				if (!in_array('kick://', $whiteList))
				{
					return false;
				}
			}

			static::$canRegisterWrapper = true;
		}

		return static::$canRegisterWrapper;
	}

	/**
	 * Function to open file or url
	 *
	 * @param   string  $path           The URL that was passed
	 * @param   string  $mode           Mode used to open the file @see fopen
	 * @param   integer $options        Flags used by the API, may be STREAM_USE_PATH and
	 *                                  STREAM_REPORT_ERRORS
	 * @param   string  &$opened_path   Full path of the resource. Used with STREAM_USE_PATH option
	 *
	 * @return  boolean
	 *
	 * @see     streamWrapper::stream_open
	 */
	public function stream_open($path, $mode, $options, &$opened_path)
	{
		$url            = parse_url($path);
		$this->name     = $url['host'] . $url['path'];
		$this->position = 0;

		if (!isset(static::$buffers[ $this->name ]))
		{
			static::$buffers[ $this->name ] = null;
		}

		return true;
	}

	public function unlink($path)
	{
		$url  = parse_url($path);
		$name = $url['host'];

		if (isset(static::$buffers[ $name ]))
		{
			unset (static::$buffers[ $name ]);
		}
	}

	public function stream_stat()
	{
		return array(
			'dev'     => 0,
			'ino'     => 0,
			'mode'    => 0644,
			'nlink'   => 0,
			'uid'     => 0,
			'gid'     => 0,
			'rdev'    => 0,
			'size'    => strlen(static::$buffers[ $this->name ]),
			'atime'   => 0,
			'mtime'   => 0,
			'ctime'   => 0,
			'blksize' => - 1,
			'blocks'  => - 1,
		);
	}

	/**
	 * Read stream
	 *
	 * @param   integer $count How many bytes of data from the current position should be returned.
	 *
	 * @return  mixed    The data from the stream up to the specified number of bytes (all data if
	 *                   the total number of bytes in the stream is less than $count. Null if
	 *                   the stream is empty.
	 *
	 * @see     streamWrapper::stream_read
	 * @since   11.1
	 */
	public function stream_read($count)
	{
		$ret = substr(static::$buffers[ $this->name ], $this->position, $count);
		$this->position += strlen($ret);

		return $ret;
	}

	/**
	 * Write stream
	 *
	 * @param   string $data The data to write to the stream.
	 *
	 * @return  integer
	 *
	 * @see     streamWrapper::stream_write
	 * @since   11.1
	 */
	public function stream_write($data)
	{
		$left                           = substr(static::$buffers[ $this->name ], 0, $this->position);
		$right                          = substr(static::$buffers[ $this->name ], $this->position + strlen($data));
		static::$buffers[ $this->name ] = $left . $data . $right;
		$this->position += strlen($data);

		return strlen($data);
	}

	/**
	 * Function to get the current position of the stream
	 *
	 * @return  integer
	 *
	 * @see     streamWrapper::stream_tell
	 * @since   11.1
	 */
	public function stream_tell()
	{
		return $this->position;
	}

	/**
	 * Function to test for end of file pointer
	 *
	 * @return  boolean  True if the pointer is at the end of the stream
	 *
	 * @see     streamWrapper::stream_eof
	 * @since   11.1
	 */
	public function stream_eof()
	{
		return $this->position >= strlen(static::$buffers[ $this->name ]);
	}

	/**
	 * The read write position updates in response to $offset and $whence
	 *
	 * @param   integer $offset   The offset in bytes
	 * @param   integer $whence   Position the offset is added to
	 *                            Options are SEEK_SET, SEEK_CUR, and SEEK_END
	 *
	 * @return  boolean  True if updated
	 *
	 * @see     streamWrapper::stream_seek
	 * @since   11.1
	 */
	public function stream_seek($offset, $whence)
	{
		switch ($whence)
		{
			case SEEK_SET:
				if ($offset < strlen(static::$buffers[ $this->name ]) && $offset >= 0)
				{
					$this->position = $offset;

					return true;
				}
				else
				{
					return false;
				}
				break;

			case SEEK_CUR:
				if ($offset >= 0)
				{
					$this->position += $offset;

					return true;
				}
				else
				{
					return false;
				}
				break;

			case SEEK_END:
				if (strlen(static::$buffers[ $this->name ]) + $offset >= 0)
				{
					$this->position = strlen(static::$buffers[ $this->name ]) + $offset;

					return true;
				}
				else
				{
					return false;
				}
				break;

			default:
				return false;
		}
	}
}

if (MiniBuildBuffer::canRegisterWrapper())
{
	stream_wrapper_register('kick', 'MiniBuildBuffer');
}
