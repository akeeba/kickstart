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
 * Utility class to parse CLI parameters in a POSIX way
 */
class AKCliParams
{
	/**
	 * POSIX-style CLI options. Access them with through the getOption method.
	 *
	 * @var   array
	 */
	protected static $cliOptions = array();

	/**
	 * Parses POSIX command line options and sets the self::$cliOptions associative array. Each array item contains
	 * a single dimensional array of values. Arguments without a dash are silently ignored.
	 *
	 * @return  void
	 */
	public static function parseOptions()
	{
		global $argc, $argv;

		// Workaround for PHP-CGI
		if (!isset($argc) && !isset($argv))
		{
			$query = "";

			if (!empty($_GET))
			{
				foreach ($_GET as $k => $v)
				{
					$query .= " $k";

					if ($v != "")
					{
						$query .= "=$v";
					}
				}
			}

			$query = ltrim($query);
			$argv  = explode(' ', $query);
			$argc  = count($argv);
		}

		$currentName = "";
		$options     = array();

		for ($i = 1; $i < $argc; $i++)
		{
			$argument = $argv[$i];

			$value = $argument;

			if (strpos($argument, "-") === 0)
			{
				$argument = ltrim($argument, '-');

				$name  = $argument;
				$value = null;

				if (strstr($argument, '='))
				{
					list($name, $value) = explode('=', $argument, 2);
				}

				$currentName = $name;

				if (!isset($options[$currentName]) || ($options[$currentName] == null))
				{
					$options[$currentName] = array();
				}
			}

			if ((!is_null($value)) && (!is_null($currentName)))
			{
				$key = null;

				if (strstr($value, '='))
				{
					$parts = explode('=', $value, 2);
					$key   = $parts[0];
					$value = $parts[1];
				}

				$values = $options[$currentName];

				if (is_null($values))
				{
					$values = array();
				}

				if (is_null($key))
				{
					$values[] = $value;
				}
				else
				{
					$values[$key] = $value;
				}

				$options[$currentName] = $values;
			}
		}

		self::$cliOptions = $options;
	}

	/**
	 * Returns the value of a command line option
	 *
	 * @param   string $key     The full name of the option, e.g. "foobar"
	 * @param   mixed  $default The default value to return
	 * @param   bool   $array   Should I return an array parameter?
	 *
	 * @return  mixed  The value of the option
	 */
	public static function getOption($key, $default = null, $array = false)
	{
		// If the key doesn't exist set it to the default value
		if (!array_key_exists($key, self::$cliOptions))
		{
			self::$cliOptions[$key] = is_array($default) ? $default : array($default);
		}

		if ($array)
		{
			return self::$cliOptions[$key];
		}

		return self::$cliOptions[$key][0];
	}

	/**
	 * Is the specified param key used in the command line?
	 *
	 * @param   string $key The full name of the option, e.g. "foobar"
	 *
	 * @return  mixed  The value of the option
	 */
	public static function hasOption($key)
	{
		return array_key_exists($key, self::$cliOptions);
	}
}

class CLIExtractionObserver extends ExtractionObserver
{
	public static $silent = false;

	public function update($object, $message)
	{
		parent::update($object, $message);

		if (self::$silent)
		{
			return;
		}

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
			echo $message->content->file . "\n";
		}
	}

}

class CLIDeletionObserver extends ExtractionObserver
{
	public static $silent = false;

	public function update($object, $message)
	{
		if (self::$silent)
		{
			return;
		}

		if (!is_object($message))
		{
			return;
		}

		if (!array_key_exists('type', get_object_vars($message)))
		{
			return;
		}

		switch ($message->type)
        {
            case 'setup':
                echo "I will delete existing files and folders\n";
                break;

            case 'deleteFile':
                echo "DELETE FILE  : $message->file\n";
                break;

            case 'deleteFolder':
                echo "DELETE FOLDER: $message->file\n";
                break;
        }
	}

}

/**
 * Routes the Kickstart CLI application
 */
function kickstart_application_cli()
{
	AKCliParams::parseOptions();
	$silent = AKCliParams::hasOption('silent');
	$year   = gmdate('Y');

	if (!$silent)
	{
		$version = defined('VERSION') ? VERSION : '##VERSION##';
		echo <<< BANNER
Akeeba Kickstart CLI $version
Copyright (c) 2008-$year Akeeba Ltd / Nicholas K. Dionysopoulos
-------------------------------------------------------------------------------
Akeeba Kickstart is Free Software, distributed under the terms of the GNU General
Public License version 3 or, at your option, any later version.
This program comes with ABSOLUTELY NO WARRANTY as per sections 15 & 16 of the
license. See http://www.gnu.org/licenses/gpl-3.0.html for details.
-------------------------------------------------------------------------------


BANNER;
	}

	$phpCheckConfig = array(
		'minPHPVersion'         => defined('KICKSTART_MIN_PHP') ? KICKSTART_MIN_PHP : "5.6.0",
		'recommendedPHPVersion' => defined('KICKSTART_RECOMMENDED_PHP') ? KICKSTART_RECOMMENDED_PHP : '7.3',
		'softwareName'          => "Akeeba Kickstart",
		'silentResults'         => true
	);

	if (!akeeba_common_wrongphp($phpCheckConfig))
	{
		echo sprintf("%s requires PHP %s or later to function. Your current PHP version is %s which is older than the minimum requirement.", $phpCheckConfig['softwareName'], $phpCheckConfig['minPHPVersion'], PHP_VERSION);

		die;
	}

	$paths = AKCliParams::getOption('', array(), true);

	if (empty($paths))
	{
		global $argv;

		echo <<< HOWTOUSE
Usage: {$argv[0]} archive.jpa [output_path] [--password=yourPassword]
         [--silent] [--permissions] [--dry-run] [--ignore-errors]
         [--delete-before]
         [--extract=<pattern>[,<pattern>...]]


HOWTOUSE;

		die;
	}

	AKFactory::nuke();

	$targetPath  = isset($paths[1]) ? $paths[1] : getcwd();
	$targetPath  = realpath($targetPath);
	$archive     = $paths[0];
	$archive     = realpath($archive);
	$archivePath = dirname($archive);
	$archivePath = empty($archivePath) ? getcwd() : $archivePath;
	$archivePath = empty($archivePath) ? __DIR__ : $archivePath;
	$archiveName = basename($paths[0]);

	$archiveForDisplay = $archive;
	$cwd               = getcwd();

	if ($archivePath == realpath($cwd))
	{
		$archiveForDisplay = $archiveName;
	}

	if (!$silent)
	{
		echo <<< BANNER
Extracting $archiveForDisplay
to folder  $targetPath

BANNER;
	}

	// What am I extracting?
	AKFactory::set('kickstart.setup.sourcepath', $archivePath);
	AKFactory::set('kickstart.setup.sourcefile', $archiveName);
	// JPS password
	AKFactory::set('kickstart.jps.password', AKCliParams::getOption('password'));
	// Restore permissions?
	AKFactory::set('kickstart.setup.restoreperms', AKCliParams::hasOption('permissions'));
	// Dry run?
	AKFactory::set('kickstart.setup.dryrun', AKCliParams::hasOption('dry-run'));
	// Ignore errors?
	AKFactory::set('kickstart.setup.ignoreerrors', AKCliParams::hasOption('ignore-errors'));
	// Delete all files and folders before extraction?
	AKFactory::set('kickstart.setup.zapbefore', AKCliParams::hasOption('delete-before'));
	// Which files should I extract?
	AKFactory::set('kickstart.setup.extract_list', AKCliParams::getOption('extract', '', true));
	// Do not rename any files (this is the CLI...)
	AKFactory::set('kickstart.setup.renamefiles', array());
	// Optimize time limits
	AKFactory::set('kickstart.tuning.max_exec_time', 20);
	AKFactory::set('kickstart.tuning.run_time_bias', 75);
	AKFactory::set('kickstart.tuning.min_exec_time', 0);
	AKFactory::set('kickstart.procengine', 'direct');

	// Make sure that the destination directory is always set (req'd by both FTP and Direct Writes modes)
	if (empty($targetPath))
	{
		$targetPath = AKKickstartUtils::getPath();
	}

	AKFactory::set('kickstart.setup.destdir', $targetPath);

	$unarchiver = AKFactory::getUnarchiver();
	$observer   = new CLIExtractionObserver();
	$unarchiver->attach($observer);

	if ($silent)
	{
		CLIExtractionObserver::$silent = true;
	}

	if (!$silent)
	{
		echo "\n\n";
	}

	$retArray = array(
		'done' => false,
	);

	while (!$retArray['done'])
	{
	    $timer = AKFactory::getTimer();
	    $timer->resetTime();

        /**
         * First try to run the filesystem zapper (remove all existing files and folders). If the Zapper is
         * disabled or has already finished running we will get a FALSE result. Otherwise it's a status array
         * which we can pass directly back to the caller.
         */
        $ret = runZapper(new CLIDeletionObserver());

        // If the Zapper had a step to run we stop here and return its status array to the caller.
        if ($ret !== false)
        {
            continue;
        }

        $unarchiver->tick();
		$ret = $unarchiver->getStatusArray();

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
		}

		if (!is_null($observer->totalSize))
		{
			$retArray['totalsize'] = $observer->totalSize;
			$retArray['filelist']  = $observer->fileList;
		}

		$retArray['Warnings'] = $ret['Warnings'];
		$retArray['lastfile'] = $observer->lastFile;

		if (!empty($retArray['Warnings']) && !$silent)
		{
			echo "\n\n";

			foreach ($retArray['Warnings'] as $line)
			{
				echo "\t$line\n";
			}

			echo "\n";
		}
	}

	if (!$silent)
	{
		echo "\n\n";
	}

	if (!$retArray['status'])
	{
		if (!$silent)
		{
			echo "An error has occurred:\n{$retArray['message']}\n\n";
		}

		exit(255);
	}

	// Finalize
	$postProc = AKFactory::getPostProc();

	rollbackAutomaticRenames($unarchiver, $postProc);
	clearCodeCaches();
}
