/**
 * @copyright   Copyright (c)2008-2018 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL v2 or - at your option - any later version
 * @package     akeebabackup
 * @subpackage  kickstart
 *
 * Update Check
 */

var akeeba_update  = {version: "0"};
var akeeba_version = "##VERSION##";

function checkUpdates()
{
	var query = "SELECT * FROM xml WHERE url=\"http://cdn.akeebabackup.com/updates/kickstart.xml";

	if (akeeba_pro)
	{
		query = "SELECT * FROM xml WHERE url=\"http://cdn.akeebabackup.com/updates/kickstartpro.xml";
	}

	var structure =
			{
				type: "GET",
				url: "http://query.yahooapis.com/v1/public/yql",
				data: {
					q: query,
					format: "json",
					callback: "updatesCallback"
				},
				cache: true,
				crossDomain: true,
				jsonp: "updatesCallback",
				timeout: 15000
			};

	$.ajax(structure);
}

function updatesCallback(msg)
{
	$.each(msg.query.results.updates.update, function (i, el)
	{
		var myUpdate = {
			"version": el.version,
			"infourl": el.infourl["content"],
			"dlurl": el.downloads.downloadurl.content
		};
		if (version_compare(myUpdate.version, akeeba_update.version, "ge"))
		{
			akeeba_update = myUpdate;
		}
	});

	if (version_compare(akeeba_update.version, akeeba_version, "gt"))
	{
		notifyAboutUpdates();
	}
}

function notifyAboutUpdates()
{
	$("#update-version").text(akeeba_update.version);
	$("#update-dlnow").attr("href", akeeba_update.dlurl);
	$("#update-whatsnew").attr("href", akeeba_update.infourl);
	$("#update-notification").show("slow");
}