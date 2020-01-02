/*
 * Akeeba Kickstart
 * An AJAX-powered archive extraction tool
 *
 * @package   kickstart
 * @copyright Copyright (c)2008-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

function onGotoStartClick(event)
{
	document.getElementById("page2").style.display = "none";
	document.getElementById("error").style.display = "none";
	document.getElementById("page1").style.display = "block";
}

function onRunInstallerClick(event)
{
	var windowReference = window.open("installation/index.php", "installer");

	if (!windowReference.opener)
	{
		windowReference.opener = this.window;
	}

	document.getElementById("runCleanup").style.display   = "inline-block";
	document.getElementById("runInstaller").style.display = "none";
}

function onRunCleanupClick(event)
{
	post = {
		"task": "isJoomla",
		// Passing the factory preserves the renamed files array
		"json": JSON.stringify({factory: akeeba_factory})
	};

	akeeba.System.doAjax(post, function (ret)
	{
		isJoomla = ret;
		onRealRunCleanupClick();
	});
}

function onRealRunCleanupClick()
{
	post = {
		"task": "cleanUp",
		// Passing the factory preserves the renamed files array
		"json": JSON.stringify({factory: akeeba_factory})
	};

	akeeba.System.doAjax(post, function (ret)
	{
		document.getElementById("runCleanup").style.display                         = "none";
		document.getElementById("gotoSite").style.display                           = "inline-block";
		document.getElementById("gotoAdministrator").style.display                  = "none";
		document.getElementById("gotoPostRestorationRroubleshooting").style.display = "block";

		if (isJoomla)
		{
			document.getElementById("gotoAdministrator").style.display = "inline-block";
		}
	});
}