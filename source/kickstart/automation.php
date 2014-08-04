<?php
/**
 * Akeeba Kickstart
 * A JSON-powered archive extraction tool
 *
 * @copyright   2010-2014 Nicholas K. Dionysopoulos / AkeebaBackup.com
 * @license     GNU GPL v2 or - at your option - any later version
 * @package     akeebabackup
 * @subpackage  kickstart
 */

class AKAutomation
{
	/**
	 * @var bool Is there automation information available?
	 */
	private $_hasAutomation = false;

	/**
	 * @var array The abiautomation.ini contents, in array format
	 */
	private $_automation = array();

	/**
	 * Singleton implementation
	 * @return ABIAutomation
	 */
	public static function &getInstance()
	{
		static $instance;

		if(empty($instance))
		{
			$instance = new AKAutomation();
		}

		return $instance;
	}

	/**
	 * Loads and parses the automation INI file
	 * @return AKAutomation
	 */
	public function __construct()
	{
		// Initialize
		$this->_hasAutomation = false;
		$this->_automation = array();

		$filenames = array('abiautomation.ini', 'kickstart.ini', 'jpi4automation');

		foreach($filenames as $filename)
		{
			// Try to load the abiautomation.ini file
			if(@file_exists($filename))
			{
				$this->_automation = $this->_parse_ini_file($filename, true);
				if(!isset($this->_automation['kickstart']))
				{
					$this->_automation = array();
				}
				else
				{
					$this->_hasAutomation = true;
					break;
				}
			}
		}

	}

	/**
	 * Do we have automation?
	 * @return bool True if abiautomation.ini exists and has a abi section
	 */
	public function hasAutomation()
	{
		return $this->_hasAutomation;
	}

	/**
	 * Returns an automation section. If the section doesn't exist, it returns an empty array.
	 * @param string $section [optional] The name of the section to load, defaults to 'kickstart'
	 * @return array
	 */
	public function getSection($section = 'kickstart')
	{
		if(!$this->_hasAutomation)
		{
			return array();
		}
		else
		{
			if(isset($this->_automation[$section]))
			{
				return $this->_automation[$section];
			} else {
				return array();
			}
		}
	}

	private function _parse_ini_file($file, $process_sections = false, $rawdata = false)
	{
		$process_sections = ($process_sections !== true) ? false : true;

		if(!$rawdata)
		{
			$ini = file($file);
		}
		else
		{
			$file = str_replace("\r","",$file);
			$ini = explode("\n", $file);
		}

		if (count($ini) == 0) {return array();}

		$sections = array();
		$values = array();
		$result = array();
		$globals = array();
		$i = 0;
		foreach ($ini as $line) {
			$line = trim($line);
			$line = str_replace("\t", " ", $line);

			// Comments
			if (!preg_match('/^[a-zA-Z0-9[]/', $line)) {continue;}

			// Sections
			if ($line{0} == '[') {
				$tmp = explode(']', $line);
				$sections[] = trim(substr($tmp[0], 1));
				$i++;
				continue;
			}

			// Key-value pair
			list($key, $value) = explode('=', $line, 2);
			$key = trim($key);
			$value = trim($value);
			if (strstr($value, ";")) {
				$tmp = explode(';', $value);
				if (count($tmp) == 2) {
					if ((($value{0} != '"') && ($value{0} != "'")) ||
					preg_match('/^".*"\s*;/', $value) || preg_match('/^".*;[^"]*$/', $value) ||
					preg_match("/^'.*'\s*;/", $value) || preg_match("/^'.*;[^']*$/", $value) ){
						$value = $tmp[0];
					}
				} else {
					if ($value{0} == '"') {
						$value = preg_replace('/^"(.*)".*/', '$1', $value);
					} elseif ($value{0} == "'") {
						$value = preg_replace("/^'(.*)'.*/", '$1', $value);
					} else {
						$value = $tmp[0];
					}
				}
			}
			$value = trim($value);
			$value = trim($value, "'\"");

			if ($i == 0) {
				if (substr($line, -1, 2) == '[]') {
					$globals[$key][] = $value;
				} else {
					$globals[$key] = $value;
				}
			} else {
				if (substr($line, -1, 2) == '[]') {
					$values[$i-1][$key][] = $value;
				} else {
					$values[$i-1][$key] = $value;
				}
			}
		}

		for($j = 0; $j < $i; $j++) {
			if ($process_sections === true) {
				$result[$sections[$j]] = $values[$j];
			} else {
				$result[] = $values[$j];
			}
		}

		return $result + $globals;
	}
}

function autoVar($key, $default = '')
{
	$automation = AKAutomation::getInstance();
	$vars = $automation->getSection('kickstart');
	if(array_key_exists($key, $vars))
	{
		return "'".addcslashes($vars[$key], "'\"\\")."'";
	}
	else
	{
		return "'".addcslashes($default, "'\"\\")."'";
	}
}