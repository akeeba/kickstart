/*
 * Akeeba Kickstart
 * A JSON-powered archive extraction tool
 *
 * @copyright   Copyright (c)2008-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL v2 or - at your option - any later version
 * @package     kickstart
 */

/**
 * Generic error handler
 *
 * @param   {string}  msg  The error message to display
 */
function errorHandler(msg)
{
	document.getElementById("errorMessage").innerHTML = msg;
	document.getElementById("error").style.display    = "block";
}

/**
 * Initialize the archive extraction
 */
function onStartExtraction()
{
	document.getElementById("page1").style.display   = "none";
	document.getElementById("page2").style.display   = "block";
	document.getElementById("currentFile").innerText = "";

	akeeba_error_callback = errorHandler;

	var zapBefore = 0;
	var elZap     = document.getElementById("kickstart\.setup\.zapbefore");

	if (elZap !== null)
	{
		zapBefore = elZap.checked;
	}

	var elRestorePermissions = document.getElementById("kickstart\.setup\.restoreperms");
	var restorePermissions   = false;

	if (elRestorePermissions !== null)
	{
		elRestorePermissions.checked;
	}

	akeeba_next_step_post = {
		"task": "startExtracting",
		"json": JSON.stringify({
			"kickstart.setup.sourcepath": document.getElementById("kickstart\.setup\.sourcepath").value,
			"kickstart.setup.sourcefile": document.getElementById("kickstart\.setup\.sourcefile").value,
			"kickstart.jps.password": document.getElementById("kickstart\.jps\.password").value,
			"kickstart.tuning.min_exec_time": document.getElementById("kickstart\.tuning\.min_exec_time").value,
			"kickstart.tuning.max_exec_time": document.getElementById("kickstart\.tuning\.max_exec_time").value,
			"kickstart.stealth.enable": document.getElementById("kickstart\.stealth\.enable").checked,
			"kickstart.stealth.url": document.getElementById("kickstart\.stealth\.url").value,
			"kickstart.setup.zapbefore": zapBefore,
			"kickstart.tuning.run_time_bias": 75,
			"kickstart.setup.restoreperms": restorePermissions,
			"kickstart.setup.dryrun": 0,
			"kickstart.setup.ignoreerrors": document.getElementById("kickstart\.setup\.ignoreerrors").checked,
			"kickstart.enabled": 1,
			"kickstart.security.password": "",
			"kickstart.setup.renameback": document.getElementById("kickstart\.setup\.renameback").checked,
			"kickstart.procengine": document.getElementById("kickstart\.procengine").value,
			"kickstart.ftp.host": document.getElementById("kickstart\.ftp\.host").value,
			"kickstart.ftp.port": document.getElementById("kickstart\.ftp\.port").value,
			"kickstart.ftp.ssl": document.getElementById("kickstart\.ftp\.ssl").checked,
			"kickstart.ftp.passive": document.getElementById("kickstart\.ftp\.passive").checked,
			"kickstart.ftp.user": document.getElementById("kickstart\.ftp\.user").value,
			"kickstart.ftp.pass": document.getElementById("kickstart\.ftp\.pass").value,
			"kickstart.ftp.dir": document.getElementById("kickstart\.ftp\.dir").value,
			"kickstart.ftp.tempdir": document.getElementById("kickstart\.ftp\.tempdir").value,
			"kickstart.setup.extract_list": document.getElementById("kickstart\.setup\.extract_list").value
		})
	};

	setTimeout(runNextExtractionStep, 10);
}

/**
 * Runs an extraction step.
 *
 * We call it through setTimeout to avoid crashing the JS due to stack exhaustion after a long list of chained function
 * calls.
 */
function runNextExtractionStep()
{
	akeeba.System.doAjax(akeeba_next_step_post, function (ret)
	{
		processRestorationStep(ret);
	});
}

/**
 * AJAX callback whenever a restoration step runs
 *
 * @param   {object}  data
 */
function processRestorationStep(data)
{
	// Look for errors
	if (!data.status)
	{
		errorHandler(data.message);

		return;
	}

	// Propagate warnings to the GUI
	if (!empty(data.Warnings))
	{
		var elWarnings    = document.getElementById("warnings");
		var elWarningsBox = document.getElementById("warningsBox");

		for (var i = 0; i < data.Warnings.length; i++)
		{
			var item         = data.Warnings[i];
			var elWarningRow = document.createElement("div");

			elWarningRow.innerHTML = item;
			elWarnings.appendChild(elWarningRow);
			elWarningsBox.style.display = "block";
		}
	}

	// Parse total size, if exists
	if (array_key_exists("totalsize", data))
	{
		if (is_array(data.filelist))
		{
			akeeba_restoration_stat_total = 0;

			for (var j = 0; j < data.filelist.length; j++)
			{
				var statItem = data.filelist[j];
				akeeba_restoration_stat_total += statItem[1];
			}
		}

		akeeba_restoration_stat_outbytes = 0;
		akeeba_restoration_stat_inbytes  = 0;
		akeeba_restoration_stat_files    = 0;
	}

	// Update GUI
	akeeba_restoration_stat_inbytes += data.bytesIn;
	akeeba_restoration_stat_outbytes += data.bytesOut;
	akeeba_restoration_stat_files += data.files;

	var percentage = 0;

	if (akeeba_restoration_stat_total > 0)
	{
		percentage = 100 * akeeba_restoration_stat_inbytes / akeeba_restoration_stat_total;
		percentage = Math.max(0, percentage);
		percentage = Math.min(percentage, 100);
	}

	if (data.done)
	{
		percentage = 100;
	}

	setProgressBar(percentage);

	document.getElementById("currentFile").innerText = data.lastfile;

	if (!empty(data.factory))
	{
		akeeba_factory = data.factory;
	}

	if (!data.done)
	{
		akeeba_next_step_post = {
			"task": "continueExtracting",
			"json": JSON.stringify({factory: akeeba_factory})
		};

		setTimeout(runNextExtractionStep, 10);

		return;
	}

	document.getElementById("page2a").style.display             = "none";
	document.getElementById("extractionComplete").style.display = "block";
	document.getElementById("runInstaller").style.display       = "inline-block";
}
