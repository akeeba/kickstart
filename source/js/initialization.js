/*!
 * Akeeba Kickstart
 * An AJAX-powered archive extraction tool
 *
 * @package   kickstart
 * @copyright Copyright (c)2008-2021 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

var akeeba_error_callback            = onGenericError;
var akeeba_restoration_stat_inbytes  = 0;
var akeeba_restoration_stat_outbytes = 0;
var akeeba_restoration_stat_files    = 0;
var akeeba_restoration_stat_total    = 0;
var akeeba_factory                   = null;
var akeeba_next_step_post            = null;

var akeeba_ftpbrowser_modal     = null;
var akeeba_ftpbrowser_host      = null;
var akeeba_ftpbrowser_port      = 21;
var akeeba_ftpbrowser_username  = null;
var akeeba_ftpbrowser_password  = null;
var akeeba_ftpbrowser_passive   = 1;
var akeeba_ftpbrowser_ssl       = 0;
var akeeba_ftpbrowser_directory = "";

var akeeba_sftpbrowser_host      = null;
var akeeba_sftpbrowser_port      = 21;
var akeeba_sftpbrowser_username  = null;
var akeeba_sftpbrowser_password  = null;
var akeeba_sftpbrowser_pubkey    = null;
var akeeba_sftpbrowser_privkey   = null;
var akeeba_sftpbrowser_directory = "";

akeeba.System.documentReady(function () {
	// Hide 2nd Page
	document.getElementById("page2").style.display = "none";

	// Translate the GUI
	translateGUI();

	// Hook interaction handlers
	akeeba.System.addEventListener(document, "keyup", closeLightbox);
	akeeba.System.addEventListener(document.getElementById("kickstart.procengine"), "change", onChangeProcengine);
	akeeba.System.addEventListener(document.getElementById("kickstart.setup.sourcepath"), "change", onArchiveListReload);
	akeeba.System.addEventListener(document.getElementById("reloadArchives"), "click", onArchiveListReload);
	akeeba.System.addEventListener(document.getElementById("checkFTPTempDir"), "click", oncheckFTPTempDirClick);
	akeeba.System.addEventListener(document.getElementById("resetFTPTempDir"), "click", onresetFTPTempDir);
	akeeba.System.addEventListener(document.getElementById("testFTP"), "click", onTestFTPClick);
	akeeba.System.addEventListener(document.getElementById("gobutton_top"), "click", onStartExtraction);
	akeeba.System.addEventListener(document.getElementById("gobutton"), "click", onStartExtraction);
	akeeba.System.addEventListener(document.getElementById("gobutton"), "click", onStartExtraction);
	akeeba.System.addEventListener(document.getElementById("runCleanup"), "click", onRunCleanupClick);
	akeeba.System.addEventListener(document.getElementById("runInstaller"), "click", onRunInstallerClick);
	akeeba.System.addEventListener(document.getElementById("gotoStart"), "click", onGotoStartClick);
	akeeba.System.addEventListener(document.getElementById("retry"), "click", onRetryClick);

	akeeba.System.addEventListener(document.getElementById("gotoSite"), "click", function (event)
	{
		window.open("index.php", "finalstepsite");
		window.close();
	});

	akeeba.System.addEventListener(document.getElementById("gotoAdministrator"), "click", function (event)
	{
		window.open("administrator/index.php", "finalstepadmin");
		window.close();
	});

	// Reset the progress bar
	setProgressBar(0);

	if (!akeeba_debug)
	{
		document.getElementById("preextraction").style.display = "block";
		document.getElementById("fade").style.display          = "block";
	}

	// Trigger change, so we avoid problems if the user refreshes the page
	akeeba.System.triggerEvent(document.getElementById("kickstart.procengine", "change"));
});
