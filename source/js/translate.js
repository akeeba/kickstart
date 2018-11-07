/**
 * @copyright   Copyright (c)2008-2018 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL v2 or - at your option - any later version
 * @package     akeebabackup
 * @subpackage  kickstart
 *
 * Kickstart interface translation
 */

function translateGUI()
{
	var allElements = document.querySelectorAll('*');

	for (var i = 0; i < allElements.length; i++)
	{
		var e = allElements[i];

		if (typeof e.innerText === "undefined")
		{
			continue;
		}

		transKey = e.innerText;

		if (!array_key_exists(transKey, translation))
		{
			continue;
		}

		e.innerHTML = translation[transKey];
	}
}

function trans(key)
{
	if (array_key_exists(key, translation))
	{
		return translation[key];
	}

	return key;
}