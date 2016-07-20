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

function callExtraFeature($method = null, array $params = array())
{
	static $extraFeatureObjects = null;

	if (!is_array($extraFeatureObjects))
	{
		$extraFeatureObjects = array();
		$allClasses          = get_declared_classes();
		foreach ($allClasses as $class)
		{
			if (substr($class, 0, 9) == 'AKFeature')
			{
				$extraFeatureObjects[] = new $class;
			}
		}
	}

	if (is_null($method))
	{
		return;
	}

	if (empty($extraFeatureObjects))
	{
		return;
	}

	$result = null;
	foreach ($extraFeatureObjects as $o)
	{
		if (!method_exists($o, $method))
		{
			continue;
		}
		$result = call_user_func(array($o, $method), $params);
	}

	return $result;
}
