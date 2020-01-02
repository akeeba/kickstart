/*
 * Akeeba Kickstart
 * An AJAX-powered archive extraction tool
 *
 * @package   kickstart
 * @copyright Copyright (c)2008-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

function translateGUI()
{
	var allElements = document.querySelectorAll('*');

	for (var i = 0; i < allElements.length; i++)
	{
		var e = allElements[i];

		if (typeof e.innerHTML === "undefined")
		{
			continue;
		}

		transKey = e.innerHTML;

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