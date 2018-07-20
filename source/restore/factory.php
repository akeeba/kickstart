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
 * The Akeeba Kickstart Factory class
 *
 * This class is reponssible for instantiating all Akeeba Kickstart classes
 */
class AKFactory
{
	/** @var   array  A list of instantiated objects */
	private $objectlist = array();

	/** @var   array  Simple hash data storage */
	private $varlist = array();

	/** @var   self   Static instance */
	private static $instance = null;

	/**
	 * AKFactory constructor.
	 *
	 * This is a private constructor makes sure we can't instantiate the class unless we go through the static
	 * getInstance singleton method. This is different than making the class abstract (preventing any kind of object
	 * instantiation).
	 */
	private function __construct()
	{
	}

	/**
	 * Gets a serialized snapshot of the Factory for safekeeping (hibernate)
	 *
	 * @return string The serialized snapshot of the Factory
	 */
	public static function serialize()
	{
		$engine = self::getUnarchiver();
		$engine->shutdown();
		$serialized = serialize(self::getInstance());

		if (function_exists('base64_encode') && function_exists('base64_decode'))
		{
			$serialized = base64_encode($serialized);
		}

		return $serialized;
	}

	/**
	 * Gets the unarchiver engine
	 *
	 * @return AKAbstractUnarchiver
	 */
	public static function &getUnarchiver($configOverride = null)
	{
		static $class_name;

		if (!empty($configOverride) && isset($configOverride['reset']) && $configOverride['reset'])
		{
			$class_name = null;
		}

		if (empty($class_name))
		{
			$filetype = self::get('kickstart.setup.filetype', null);

			if (empty($filetype))
			{
				$filename      = self::get('kickstart.setup.sourcefile', null);
				$basename      = basename($filename);
				$baseextension = strtoupper(substr($basename, -3));

				switch ($baseextension)
				{
					case 'JPA':
						$filetype = 'JPA';
						break;

					case 'JPS':
						$filetype = 'JPS';
						break;

					case 'ZIP':
						$filetype = 'ZIP';
						break;

					default:
						die('Invalid archive type or extension in file ' . $filename);
						break;
				}
			}

			$class_name = 'AKUnarchiver' . ucfirst($filetype);
		}

		$destdir = self::get('kickstart.setup.destdir', null);

		if (empty($destdir))
		{
			$destdir = KSROOTDIR;
		}

		/** @var AKAbstractUnarchiver $object */
		$object = self::getClassInstance($class_name);

		if ($object->getState() == 'init')
		{
			$sourcePath = self::get('kickstart.setup.sourcepath', '');
			$sourceFile = self::get('kickstart.setup.sourcefile', '');

			if (!empty($sourcePath))
			{
				$sourceFile = rtrim($sourcePath, '/\\') . '/' . $sourceFile;
			}

			// Initialize the object –– Any change here MUST be reflected to echoHeadJavascript (default values)
			$config = array(
				'filename'            => $sourceFile,
				'restore_permissions' => self::get('kickstart.setup.restoreperms', 0),
				'post_proc'           => self::get('kickstart.procengine', 'direct'),
				'add_path'            => self::get('kickstart.setup.targetpath', $destdir),
				'remove_path'         => self::get('kickstart.setup.removepath', ''),
				'rename_files'        => self::get('kickstart.setup.renamefiles', array(
					'.htaccess' => 'htaccess.bak', 'php.ini' => 'php.ini.bak', 'web.config' => 'web.config.bak',
					'.user.ini' => '.user.ini.bak',
				)),
				'skip_files'          => self::get('kickstart.setup.skipfiles', array(
					basename(__FILE__), 'kickstart.php', 'abiautomation.ini', 'htaccess.bak', 'php.ini.bak',
					'cacert.pem',
				)),
				'ignoredirectories'   => self::get('kickstart.setup.ignoredirectories', array(
					'tmp', 'log', 'logs',
				)),
			);

			if (!defined('KICKSTART'))
			{
				// In restore.php mode we have to exclude the restoration.php files
				$moreSkippedFiles     = array(
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
				);

				$config['skip_files'] = array_merge($config['skip_files'], $moreSkippedFiles);
			}

			if (!empty($configOverride))
			{
				$config = array_merge($config, $configOverride);
			}

			$object->setup($config);
		}

		return $object;
	}

	// ========================================================================
	// Public factory interface
	// ========================================================================

	public static function get($key, $default = null)
	{
		$self = self::getInstance();

		if (array_key_exists($key, $self->varlist))
		{
			return $self->varlist[$key];
		}

		return $default;
	}

	/**
	 * Gets a single, internally used instance of the Factory
	 *
	 * @param string $serialized_data [optional] Serialized data to spawn the instance from
	 *
	 * @return AKFactory A reference to the unique Factory object instance
	 */
	protected static function &getInstance($serialized_data = null)
	{
		if (!is_object(self::$instance) || !is_null($serialized_data))
		{
			if (!is_null($serialized_data))
			{
				self::$instance = unserialize($serialized_data);

				return self::$instance;
			}

			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Internal function which instantiates a class named $class_name.
	 * The autoloader
	 *
	 * @param string $class_name
	 *
	 * @return object
	 */
	protected static function &getClassInstance($class_name)
	{
		$self = self::getInstance();

		if (!isset($self->objectlist[$class_name]))
		{
			$self->objectlist[$class_name] = new $class_name;
		}

		return $self->objectlist[$class_name];
	}

	// ========================================================================
	// Public hash data storage interface
	// ========================================================================

	/**
	 * Regenerates the full Factory state from a serialized snapshot (resume)
	 *
	 * @param string $serialized_data The serialized snapshot to resume from
	 */
	public static function unserialize($serialized_data)
	{
		if (function_exists('base64_encode') && function_exists('base64_decode'))
		{
			$serialized_data = base64_decode($serialized_data);
		}

		self::getInstance($serialized_data);
	}

	/**
	 * Reset the internal factory state, freeing all previously created objects
	 */
	public static function nuke()
	{
		self::$instance = null;
	}

	// ========================================================================
	// Akeeba Kickstart classes
	// ========================================================================

	public static function set($key, $value)
	{
		$self                = self::getInstance();
		$self->varlist[$key] = $value;
	}

	/**
	 * Gets the post processing engine
	 *
	 * @param string $proc_engine
	 *
	 * @return AKAbstractPostproc
	 */
	public static function &getPostProc($proc_engine = null)
	{
		static $class_name;

		if (empty($class_name))
		{
			if (empty($proc_engine))
			{
				$proc_engine = self::get('kickstart.procengine', 'direct');
			}

			$class_name = 'AKPostproc' . ucfirst($proc_engine);
		}

		return self::getClassInstance($class_name);
	}

	/**
	 * Get the a reference to the Akeeba Engine's timer
	 *
	 * @return AKCoreTimer
	 */
	public static function &getTimer()
	{
		return self::getClassInstance('AKCoreTimer');
	}

	/**
	 * Get an instance of the filesystem zapper
	 *
	 * @return AKUtilsZapper
	 */
	public static function &getZapper()
	{
		return self::getClassInstance('AKUtilsZapper');
	}
}
