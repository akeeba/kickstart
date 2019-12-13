<?php
/**
 * Akeeba Kickstart
 * An AJAX-powered archive extraction tool
 *
 * @package   kickstart
 * @copyright Copyright (c)2008-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
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
