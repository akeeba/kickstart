/*
 * Akeeba Kickstart
 * An AJAX-powered archive extraction tool
 *
 * @package   kickstart
 * @copyright Copyright (c)2008-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

/**
 * Event handler for changing the Archive directory.
 */
function onArchiveListReload()
{
	post = {
		'task': 'listArchives',
		'json': JSON.stringify({
			path: document.getElementById('kickstart\.setup\.sourcepath').value
		})
	};

	akeeba.System.doAjax(post, function (ret)
	{
		document.getElementById('sourcefileContainer').innerHTML = ret;
	});
}

/**
 * Event handler for switching the Write To File method
 *
 * @param   {Event}  event
 */
function onChangeProcengine(event)
{
	var elProcEngine = document.getElementById("kickstart.procengine");
	var procEngine   = elProcEngine.value;
	var elFtpOptions = document.getElementById("ftp-options");
	var elPassive    = document.getElementById("ftp-ssl-passive");
	var elTestBtn    = document.getElementById("testFTP");

	// Only hide the (S)FTP options when using direct file writes
	elFtpOptions.style.display = (procEngine === "direct") ? "none" : "block";

	// Set up the interface for a plain FTP or Hybrid extraction engine
	elPassive.style.display = "block";
	elTestBtn.innerHTML     = trans("BTN_TESTFTPCON");

	// If the SFTP engine is selected I need to make some interface changes
	if (procEngine === "sftp")
	{
		// Insert the SFTP path if none is currently specified
		var elFtpDir = document.getElementById("kickstart.ftp.dir");

		if (elFtpDir.value === "")
		{
			elFtpDir.value = sftp_path;
		}

		// Hide the passive mode (it's an FTP-only thing) and change the button label.
		elPassive.style.display = "none";
		elTestBtn.innerHTML     = trans("BTN_TESTSFTPCON");
	}
}

/**
 * Event handler for the Check button next to the Temporary Directory
 *
 * @param   {MouseEvent}  event
 */
function oncheckFTPTempDirClick(event)
{
	var data = {
		'task': 'checkTempdir',
		'json': JSON.stringify({
			'kickstart.ftp.tempdir': document.getElementById('kickstart\.ftp.tempdir').value
		})
	};

	akeeba.System.doAjax(data, function (ret)
	{
		var key = ret.status ? 'FTP_TEMPDIR_WRITABLE' : 'FTP_TEMPDIR_UNWRITABLE';

		alert(trans(key));
	});
}

/**
 * Event handler for the Reset button next to the Temporary Directory
 *
 * @param   {MouseEvent}  event
 */
function onresetFTPTempDir(event)
{
	document.getElementById('kickstart\.ftp\.tempdir').value = default_temp_dir;
}

/**
 * Event handler for the Test FTP Connection button
 *
 * @param   {MouseEvent}  event
 */
function onTestFTPClick(event)
{
	var type = 'ftp';

	if (document.getElementById('kickstart.procengine').value === 'sftp')
	{
		type = 'sftp';
	}

	var data = {
		'task': 'checkFTP',
		'json': JSON.stringify({
			'type':                  type,
			'kickstart.ftp.host':    document.getElementById('kickstart.ftp.host').value,
			'kickstart.ftp.port':    document.getElementById('kickstart.ftp.port').value,
			'kickstart.ftp.ssl':     document.getElementById('kickstart.ftp.ssl').checked,
			'kickstart.ftp.passive': document.getElementById('kickstart.ftp.passive').checked,
			'kickstart.ftp.user':    document.getElementById('kickstart.ftp.user').value,
			'kickstart.ftp.pass':    document.getElementById('kickstart.ftp.pass').value,
			'kickstart.ftp.dir':     document.getElementById('kickstart.ftp.dir').value,
			'kickstart.ftp.tempdir': document.getElementById('kickstart.ftp.tempdir').value
		})
	};

	akeeba.System.doAjax(data, function (ret)
	{
		var key = ret.status ? 'FTP_CONNECTION_OK' : 'FTP_CONNECTION_FAILURE';

		if (type === 'sftp')
		{
			key = ret.status ? 'SFTP_CONNECTION_OK' : 'SFTP_CONNECTION_FAILURE';
		}


		alert(trans(key) + "\n\n" + (ret.status ? '' : ret.message));
	});
}