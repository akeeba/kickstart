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
 * @copyright   2010-2016 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL v2 or - at your option - any later version
 * @package     joomla
 * @subpackage  joomlastart
 */

/**
 * A simple INI-based i18n engine
 */

class AKText extends AKAbstractObject
{
	/**
	 * The default (en_GB) translation used when no other translation is available
	 * @var array
	 */
	private $default_translation = array(
		'JS_APPTITLE'						=> "Joomla! Start",
		'JS_ERR_GETTINGJOOMLAURL' 			=> "There was an error while trying to get the download URL of the latest version of Joomla!. The error was:",
		'JS_ERR_IE7_TITLE' 					=> "Obsolete Internet Explorer version detected",
		'JS_ERR_IE7_DETAILS' 				=> "This script is not designed to work properly on Internet Explorer 8 or previous versions, or on Internet Explorer 9 and higher running in compatibility mode.",
		'JS_ERR_IE7_INFO' 					=> "Please use Internet Explorer 9 or any later version in native mode (the &quot;broken page&quot; icon next to the address bar must not be enabled). Alternatively, you may use the latest versions of most modern browsers such as Firefox, Safari, Google Chrome or Opera.",
		'JS_ERR_GENERICERROR_HEADER' 		=> "An error occurred",
		'JS_LBL_INTROTOTHISAPP_HEADER' 		=> "What is Joomla! Start?",
		'JS_LBL_INTROTOTHISAPP' 			=> "This script will download the latest version of Joomla!, extract it on your server and let you proceed with its web-based installer. After installing Joomla! you can come back to this script and remove this script and Joomla!'s package for security reasons. For your information, the download URL of the installation package is shown below. Just press the &quot;Download and Install Joomla!&quot; button to start the download and installation process.",
		'JS_LBL_PROCENGINE' 				=> "How to write files",
		'JS_LBL_WRITE_DIRECTLY' 			=> "Directly",
		'JS_LBL_WRITE_FTP' 					=> "Using FTP",
		'JS_WARNING_FTPINFO_REQUIRED' 		=> "Please provide your FTP information below. This script will use this information to write files to your site by uploading them via FTP.",
		'JS_LBL_FTP_HOST' 					=> "FTP Hostname",
		'JS_LBL_FTP_HOST_HELP' 				=> "The domain name or IP address of your FTP server, <em>without</em> the <tt>ftp://</tt> protocol prefix, e.g. <code>ftp.example.com</code> or <code>12.34.56.78</code>",
		'JS_LBL_FTP_PORT' 					=> "FTP Port",
		'JS_LBL_FTP_PORT_HELP' 				=> "The FTP port number. Your host will give you that. Usually it's 21.",
		'JS_LBL_FTP_USER' 					=> "FTP Username",
		'JS_LBL_FTP_USER_HELP' 				=> "Enter the username to connect to your FTP server. If unsure, please ask your host.",
		'JS_LBL_FTP_PASS' 					=> "FTP Password",
		'JS_LBL_FTP_PASS_HELP' 				=> "Enter the password to connect to your FTP server. If unsure, please ask your host.",
		'JS_LBL_FTP_DIR' 					=> "FTP Directory",
		'JS_LBL_FTP_DIR_HELP' 				=> "Enter the FTP path to your site's root directory. If unsure, please ask your host.",
		'JS_LBL_FTP_PASSIVE' 				=> "Use Passive mode",
		'JS_LBL_FTP_PASSIVE_HELP' 			=> "Check if your FTP server requires Passive Mode to be enabled. If unsure, leave this checked.",
		'JS_LBL_FTP_SSL' 					=> "Use SSL",
		'JS_LBL_FTP_SSL_HELP' 				=> "Check if you are using an FTPS server. If unsure leave unchecked. Please note that FTPS is different than SFTP. SFTP is currently not supported by this script.",
		'JS_LBL_FTP_TEMPDIR' 				=> "Temporary Directory",
		'JS_LBL_FTP_TEMPDIR_HELP' 			=> "This script needs a writeable temporary directory when using the FTP file writing mode. You need to specify the full filesystem directory to it. If you are unsure, leave it blank and this script will do its best to create such a directory automatically.",
		'JS_BTN_TESTFTP' 					=> "Test FTP Connection",
		'JS_LBL_JDLURL' 					=> "Download URL",
		'JS_BTN_INSTALLJOOMLA' 				=> "Download and Install Joomla!",
		'JS_WARNING_DONTCLOSEDOWNLOAD' 		=> "We are currently downloading Joomla! on your server. This may take up to a few minutes. Please do not close this browser tab, do not navigate away and do not let your device go to sleep mode while the download is in progress.",
		'JS_LBL_DOWNLOADPROGRESS' 			=> "Download progress",
		'JS_ERR_DOWNLOADERROR_HEADER' 		=> "An error occurred during download",
		'JS_WARNING_DONTCLOSEEXTRACT' 		=> "We are currently extracting Joomla! files on your server. This may take several minutes. Please do not close this browser tab, do not navigate away and do not let your device go to sleep mode while the extraction is in progress.",
		'JS_LBL_EXTRACTPROGRESS' 			=> "Extraction progress",
		'JS_ERR_EXTRACTERROR_HEADER' 		=> "An error occurred during extraction",
		'JS_LBL_RUNINSTALLER_HEADER' 		=> "Ready to install",
		'JS_LBL_RUNINSTALLER' 				=> "Click the button below to open the Joomla! installation in a new tab on your browser. Do not close the tab of this script! When you are finished installing Joomla! come back here to perform post-installation clean up. This step is required for security reasons.",
		'JS_BTN_RUNINSTALLER' 				=> "Run the installer",
		'JS_LBL_CLEANUP_HEADER' 			=> "Almost there",
		'JS_LBL_CLEANUP' 					=> "After the Joomla! installation is complete, please press the button below. It will remove this script, the Joomla! package (you no longer need it) and the <code>installation</code> directory if it's not already deleted. This is a very important security step, so please don't forget to do it.",
		'JS_BTN_CLEANUP' 					=> "Clean Up",
		'JS_LBL_FINISHED_HEADER' 			=> "Enjoy your new Joomla! site",
		'JS_LBL_FINISHED' 					=> "Joomla! has now been installed on your site. Use the buttons below to visit your new site's public front page or the administrator control panel respectively. You may want to note down the URL to your administrator control panel; you will need it to manage your site from now on. Thank you for using Joomla! Start to install Joomla! and welcome to the Joomla! family!",
		'JS_BTN_FRONTEND' 					=> "Open your site's public front page",
		'JS_BTN_BACKEND' 					=> "Open your site's administrator control panel",
		'ERR_NOT_A_JPA_FILE' 				=> "The file is not a valid Joomla! package",
		'ERR_CORRUPT_ARCHIVE' 				=> "The archive file is corrupt, truncated or archive parts are missing",
		'ERR_INVALID_LOGIN' 				=> "Invalid FTP login",
		'COULDNT_CREATE_DIR' 				=> "Could not create %s folder",
		'COULDNT_WRITE_FILE' 				=> "Could not open %s for writing.",
		'WRONG_FTP_HOST' 					=> "Wrong FTP host or port",
		'WRONG_FTP_USER' 					=> "Wrong FTP username or password",
		'WRONG_FTP_PATH1' 					=> "Wrong FTP initial directory - the directory doesn't exist",
		'WRONG_FTP_PATH2' 					=> "Wrong FTP initial directory - the directory doesn't correspond to your site's web root",
		'FTP_CANT_CREATE_DIR' 				=> "Could not create directory %s",
		'FTP_TEMPDIR_NOT_WRITABLE' 			=> "Could not find or create a writable temporary directory",
		'FTP_COULDNT_UPLOAD' 				=> "Could not upload %s",
		'FTP_CONNECTION_OK' 				=> "FTP Connection Established",
		'FTP_CONNECTION_FAILURE' 			=> "The FTP Connection Failed",
		'FTP_TEMPDIR_WRITABLE' 				=> "The temporary directory is writable.",
		'FTP_TEMPDIR_UNWRITABLE' 			=> "The temporary directory is not writable. Please check the permissions.",
		'INVALID_FILE_HEADER' 				=> "Invalid header in Joomla! package file, part %s, offset %s",
	);

	/**
	 * The array holding the translation keys
	 * @var array
	 */
	private $strings;

	/**
	 * The currently detected language (ISO code)
	 * @var string
	 */
	private $language;

	/*
	 * Initializes the translation engine
	 * @return AKText
	 */
	public function __construct()
	{
		// Start with the default translation
		$this->strings = $this->default_translation;
		// Try loading the translation file in English, if it exists
		$this->loadTranslation('en-GB');
		// Try loading the translation file in the browser's preferred language, if it exists
		$this->getBrowserLanguage();
		if (!is_null($this->language))
		{
			$this->loadTranslation();
		}
	}

	/**
	 * Singleton pattern for Language
	 * @return Language The global Language instance
	 */
	public static function &getInstance()
	{
		static $instance;

		if (!is_object($instance))
		{
			$instance = new AKText();
		}

		return $instance;
	}

	public static function _($string)
	{
		$text = self::getInstance();

		$key = strtoupper($string);
		$key = substr($key, 0, 1) == '_' ? substr($key, 1) : $key;

		if (isset ($text->strings[$key]))
		{
			$string = $text->strings[$key];
		}
		else
		{
			if (defined($string))
			{
				$string = constant($string);
			}
		}

		return $string;
	}

	public static function sprintf($key)
	{
		$text = self::getInstance();
		$args = func_get_args();
		if (count($args) > 0)
		{
			$args[0] = $text->_($args[0]);

			return @call_user_func_array('sprintf', $args);
		}

		return '';
	}

	public function dumpLanguage()
	{
		$out = '';
		foreach ($this->strings as $key => $value)
		{
			$out .= "$key=$value\n";
		}

		return $out;
	}

	public function asJavascript()
	{
		$out = '';
		foreach ($this->strings as $key => $value)
		{
			$key = addcslashes($key, '\\\'"');
			$value = addcslashes($value, '\\\'"');
			if (!empty($out))
			{
				$out .= ",\n";
			}
			$out .= "'$key':\t'$value'";
		}

		return $out;
	}

	public function resetTranslation()
	{
		$this->strings = $this->default_translation;
	}

	public function getBrowserLanguage()
	{
		// Detection code from Full Operating system language detection, by Harald Hope
		// Retrieved from http://techpatterns.com/downloads/php_language_detection.php
		$user_languages = array();
		//check to see if language is set
		if (isset($_SERVER["HTTP_ACCEPT_LANGUAGE"]))
		{
			$languages = strtolower($_SERVER["HTTP_ACCEPT_LANGUAGE"]);
			// $languages = ' fr-ch;q=0.3, da, en-us;q=0.8, en;q=0.5, fr;q=0.3';
			// need to remove spaces from strings to avoid error
			$languages = str_replace(' ', '', $languages);
			$languages = explode(",", $languages);

			foreach ($languages as $language_list)
			{
				// pull out the language, place languages into array of full and primary
				// string structure:
				$temp_array = array();
				// slice out the part before ; on first step, the part before - on second, place into array
				$temp_array[0] = substr($language_list, 0, strcspn($language_list, ';')); //full language
				$temp_array[1] = substr($language_list, 0, 2); // cut out primary language
				if ((strlen($temp_array[0]) == 5) && ((substr($temp_array[0], 2, 1) == '-') || (substr($temp_array[0], 2, 1) == '_')))
				{
					$langLocation = strtoupper(substr($temp_array[0], 3, 2));
					$temp_array[0] = $temp_array[1] . '-' . $langLocation;
				}
				//place this array into main $user_languages language array
				$user_languages[] = $temp_array;
			}
		}
		else // if no languages found
		{
			$user_languages[0] = array('', ''); //return blank array.
		}

		$this->language = null;
		$basename = basename(__FILE__, '.php') . '.ini';

		// Try to match main language part of the filename, irrespective of the location, e.g. de_DE will do if de_CH doesn't exist.
		if (class_exists('AKUtilsLister'))
		{
			$fs = new AKUtilsLister();
			$iniFiles = $fs->getFiles(KSROOTDIR, '*.' . $basename);
			if (empty($iniFiles) && ($basename != 'joomlastart.ini'))
			{
				$basename = 'joomlastart.ini';
				$iniFiles = $fs->getFiles(KSROOTDIR, '*.' . $basename);
			}
		}
		else
		{
			$iniFiles = null;
		}

		if (is_array($iniFiles))
		{
			foreach ($user_languages as $languageStruct)
			{
				if (is_null($this->language))
				{
					// Get files matching the main lang part
					$iniFiles = $fs->getFiles(KSROOTDIR, $languageStruct[1] . '-??.' . $basename);
					if (count($iniFiles) > 0)
					{
						$filename = $iniFiles[0];
						$filename = substr($filename, strlen(KSROOTDIR) + 1);
						$this->language = substr($filename, 0, 5);
					}
					else
					{
						$this->language = null;
					}
				}
			}
		}

		if (is_null($this->language))
		{
			// Try to find a full language match
			foreach ($user_languages as $languageStruct)
			{
				if (@file_exists($languageStruct[0] . '.' . $basename) && is_null($this->language))
				{
					$this->language = $languageStruct[0];
				}
				else
				{

				}
			}
		}
		else
		{
			// Do we have an exact match?
			foreach ($user_languages as $languageStruct)
			{
				if (substr($this->language, 0, strlen($languageStruct[1])) == $languageStruct[1])
				{
					if (file_exists($languageStruct[0] . '.' . $basename))
					{
						$this->language = $languageStruct[0];
					}
				}
			}
		}

		// Now, scan for full language based on the partial match

	}

	private function loadTranslation($lang = null)
	{
		if (defined('KSLANGDIR'))
		{
			$dirname = KSLANGDIR;
		}
		else
		{
			$dirname = KSROOTDIR;
		}

		$basename = basename(__FILE__, '.php') . '.ini';

		if (empty($lang))
		{
			$lang = $this->language;
		}

		$translationFilename = $dirname . DIRECTORY_SEPARATOR . $lang . '.' . $basename;

		if (!@file_exists($translationFilename) && ($basename != 'joomlastart.ini'))
		{
			$basename = 'joomlastart.ini';
			$translationFilename = $dirname . DIRECTORY_SEPARATOR . $lang . '.' . $basename;
		}

		if (!@file_exists($translationFilename))
		{
			return;
		}

		$temp = self::parse_ini_file($translationFilename, false);

		if (!is_array($this->strings))
		{
			$this->strings = array();
		}

		if (empty($temp))
		{
			$this->strings = array_merge($this->default_translation, $this->strings);
		}
		else
		{
			$this->strings = array_merge($this->strings, $temp);
		}
	}

	public function addDefaultLanguageStrings($stringList = array())
	{
		if (!is_array($stringList))
		{
			return;
		}
		if (empty($stringList))
		{
			return;
		}

		$this->strings = array_merge($stringList, $this->strings);
	}

	/**
	 * A PHP based INI file parser.
	 *
	 * Thanks to asohn ~at~ aircanopy ~dot~ net for posting this handy function on
	 * the parse_ini_file page on http://gr.php.net/parse_ini_file
	 *
	 * @param string $file             Filename to process
	 * @param bool   $process_sections True to also process INI sections
	 *
	 * @return array An associative array of sections, keys and values
	 * @access private
	 */
	public static function parse_ini_file($file, $process_sections = false, $raw_data = false)
	{
		$process_sections = ($process_sections !== true) ? false : true;

		if (!$raw_data)
		{
			$ini = @file($file);
		}
		else
		{
			$ini = $file;
		}
		if (count($ini) == 0)
		{
			return array();
		}

		$sections = array();
		$values = array();
		$result = array();
		$globals = array();
		$i = 0;
		if (!empty($ini))
		{
			foreach ($ini as $line)
			{
				$line = trim($line);
				$line = str_replace("\t", " ", $line);

				// Comments
				if (!preg_match('/^[a-zA-Z0-9[]/', $line))
				{
					continue;
				}

				// Sections
				if ($line{0} == '[')
				{
					$tmp = explode(']', $line);
					$sections[] = trim(substr($tmp[0], 1));
					$i++;
					continue;
				}

				// Key-value pair
				list($key, $value) = explode('=', $line, 2);
				$key = trim($key);
				$value = trim($value);
				if (strstr($value, ";"))
				{
					$tmp = explode(';', $value);
					if (count($tmp) == 2)
					{
						if ((($value{0} != '"') && ($value{0} != "'")) ||
							preg_match('/^".*"\s*;/', $value) || preg_match('/^".*;[^"]*$/', $value) ||
							preg_match("/^'.*'\s*;/", $value) || preg_match("/^'.*;[^']*$/", $value)
						)
						{
							$value = $tmp[0];
						}
					}
					else
					{
						if ($value{0} == '"')
						{
							$value = preg_replace('/^"(.*)".*/', '$1', $value);
						}
						elseif ($value{0} == "'")
						{
							$value = preg_replace("/^'(.*)'.*/", '$1', $value);
						}
						else
						{
							$value = $tmp[0];
						}
					}
				}
				$value = trim($value);
				$value = trim($value, "'\"");

				if ($i == 0)
				{
					if (substr($line, -1, 2) == '[]')
					{
						$globals[$key][] = $value;
					}
					else
					{
						$globals[$key] = $value;
					}
				}
				else
				{
					if (substr($line, -1, 2) == '[]')
					{
						$values[$i - 1][$key][] = $value;
					}
					else
					{
						$values[$i - 1][$key] = $value;
					}
				}
			}
		}

		for ($j = 0; $j < $i; $j++)
		{
			if ($process_sections === true)
			{
				$result[$sections[$j]] = $values[$j];
			}
			else
			{
				$result[] = $values[$j];
			}
		}

		return $result + $globals;
	}
}