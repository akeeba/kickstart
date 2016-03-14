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

function echoHeadJavascript()
{
?>
<script type="text/javascript" language="javascript">
	var akeeba_debug = <?php echo defined('KSDEBUG') ? 'true' : 'false' ?>;
	var sftp_path = '<?php echo TranslateWinPath(defined('KSROOTDIR') ? KSROOTDIR : dirname(__FILE__)); ?>/';
	var isJoomla = true;

	/**
	 * Returns the version of Internet Explorer or a -1
	 * (indicating the use of another browser).
	 *
	 * @return   integer  MSIE version or -1
	 */
	function getInternetExplorerVersion()
	{
		var rv = -1; // Return value assumes failure.
		if (navigator.appName == 'Microsoft Internet Explorer')
		{
			var ua = navigator.userAgent;
			var re  = new RegExp("MSIE ([0-9]{1,}[\.0-9]{0,})");
			if (re.exec(ua) != null)
			{
				rv = parseFloat( RegExp.$1 );
			}
		}
		return rv;
	}

	$(document).ready(function(){
		// Hide 2nd Page
		$('#page2').css('display','none');

		// Translate the GUI
		translateGUI();

		// Hook interaction handlers
		$(document).keyup( closeLightbox );
		$('#kickstart\\.procengine').change( onChangeProcengine );
		$('#kickstart\\.setup\\.sourcepath').change( onArchiveListReload );
		$('#reloadArchives').click ( onArchiveListReload );
		$('#checkFTPTempDir').click( oncheckFTPTempDirClick );
		$('#resetFTPTempDir').click( onresetFTPTempDir );
		$('#browseFTP').click( onbrowseFTP );
		$('#testFTP').click( onTestFTPClick );
		$('#gobutton').click( onStartExtraction );
		$('#runInstaller').click( onRunInstallerClick );
		$('#runCleanup').click( onRunCleanupClick );
		$('#gotoSite').click(function(event){window.open('index.php','finalstepsite'); window.close();});
		$('#gotoAdministrator').click(function(event){window.open('administrator/index.php','finalstepadmin'); window.close();});
		$('#gotoStart').click( onGotoStartClick );
        $('#showFineTune').click(function(){
            $('#fine-tune-holder').show();
            $(this).hide();
        });

		// Reset the progress bar
		setProgressBar(0);

		// Show warning
		var msieVersion = getInternetExplorerVersion();
		if((msieVersion != -1) && (msieVersion <= 8.99))
		{
			$('#ie7Warning').css('display','block');
		}
		if(!akeeba_debug) {
			$('#preextraction').css('display','block');
			$('#fade').css('display','block');
		}

		// Trigger change, so we avoid problems if the user refreshes the page
		$('#kickstart\\.procengine').change();
	});

	var translation = {
		<?php echoTranslationStrings(); ?>
	}

	var akeeba_ajax_url = '<?php echo defined('KSSELFNAME') ? KSSELFNAME : basename(__FILE__); ?>';
	var akeeba_error_callback = onGenericError;
	var akeeba_restoration_stat_inbytes = 0;
	var akeeba_restoration_stat_outbytes = 0;
	var akeeba_restoration_stat_files = 0;
	var akeeba_restoration_stat_total = 0;
	var akeeba_factory = null;

	var akeeba_ftpbrowser_host = null;
	var akeeba_ftpbrowser_port = 21;
	var akeeba_ftpbrowser_username = null;
	var akeeba_ftpbrowser_password = null;
	var akeeba_ftpbrowser_passive = 1;
	var akeeba_ftpbrowser_ssl = 0;
	var akeeba_ftpbrowser_directory = '';

	var akeeba_sftpbrowser_host = null;
	var akeeba_sftpbrowser_port = 21;
	var akeeba_sftpbrowser_username = null;
	var akeeba_sftpbrowser_password = null;
	var akeeba_sftpbrowser_pubkey = null;
	var akeeba_sftpbrowser_privkey = null;
	var akeeba_sftpbrowser_directory = '';

	function translateGUI()
	{
		$('*').each(function(i,e){
			transKey = $(e).text();
			if(array_key_exists(transKey, translation))
			{
				$(e).text( translation[transKey] );
			}
		});
	}

	function trans(key)
	{
		if(array_key_exists(key, translation)) {
			return translation[key];
		} else {
			return key;
		}
	}

	function array_key_exists ( key, search ) {
		if (!search || (search.constructor !== Array && search.constructor !== Object)){
			return false;
		}
		return key in search;
	}

	function empty (mixed_var) {
		var key;

		if (mixed_var === "" ||
			mixed_var === 0 ||
			mixed_var === "0" ||
			mixed_var === null ||
			mixed_var === false ||
			typeof mixed_var === 'undefined'
		){
			return true;
		}

		if (typeof mixed_var == 'object') {
			for (key in mixed_var) {
				return false;
			}
			return true;
		}

		return false;
	}

	function is_array (mixed_var) {
		var key = '';
		var getFuncName = function (fn) {
			var name = (/\W*function\s+([\w\$]+)\s*\(/).exec(fn);
			if (!name) {
				return '(Anonymous)';
			}
			return name[1];
		};

		if (!mixed_var) {
			return false;
		}

		// BEGIN REDUNDANT
		this.php_js = this.php_js || {};
		this.php_js.ini = this.php_js.ini || {};
		// END REDUNDANT

		if (typeof mixed_var === 'object') {

			if (this.php_js.ini['phpjs.objectsAsArrays'] &&  // Strict checking for being a JavaScript array (only check this way if call ini_set('phpjs.objectsAsArrays', 0) to disallow objects as arrays)
				(
				(this.php_js.ini['phpjs.objectsAsArrays'].local_value.toLowerCase &&
				this.php_js.ini['phpjs.objectsAsArrays'].local_value.toLowerCase() === 'off') ||
				parseInt(this.php_js.ini['phpjs.objectsAsArrays'].local_value, 10) === 0)
			) {
				return mixed_var.hasOwnProperty('length') && // Not non-enumerable because of being on parent class
				!mixed_var.propertyIsEnumerable('length') && // Since is own property, if not enumerable, it must be a built-in function
				getFuncName(mixed_var.constructor) !== 'String'; // exclude String()
			}

			if (mixed_var.hasOwnProperty) {
				for (key in mixed_var) {
					// Checks whether the object has the specified property
					// if not, we figure it's not an object in the sense of a php-associative-array.
					if (false === mixed_var.hasOwnProperty(key)) {
						return false;
					}
				}
			}

			// Read discussion at: http://kevin.vanzonneveld.net/techblog/article/javascript_equivalent_for_phps_is_array/
			return true;
		}

		return false;
	}

	function resolvePath(filename)
	{
		filename = filename.replace('\/\/g', '\/');
		var parts = filename.split('/');
		var out = [];

		$.each(parts, function(i, part){
			if (part == '.') return;
			if (part == '..') {
				out.pop();
				return;
			}
			out.push(part);
		});

		return out.join('/');
	}

	/**
	 * Performs an AJAX request and returns the parsed JSON output.
	 * The global akeeba_ajax_url is used as the AJAX proxy URL.
	 * If there is no errorCallback, the global akeeba_error_callback is used.
	 * @param data An object with the query data, e.g. a serialized form
	 * @param successCallback A function accepting a single object parameter, called on success
	 * @param errorCallback A function accepting a single string parameter, called on failure
	 */
	function doAjax(data, successCallback, errorCallback)
	{
		var structure =
		{
			type: "POST",
			url: akeeba_ajax_url,
			cache: false,
			data: data,
			timeout: 600000,
			success: function(msg) {
				// Initialize
				var junk = null;
				var message = "";

				// Get rid of junk before the data
				var valid_pos = msg.indexOf('###');
				if( valid_pos == -1 ) {
					// Valid data not found in the response
					msg = 'Invalid AJAX data received:<br/>' + msg;
					if(errorCallback == null)
					{
						if(akeeba_error_callback != null)
						{
							akeeba_error_callback(msg);
						}
					}
					else
					{
						errorCallback(msg);
					}
					return;
				} else if( valid_pos != 0 ) {
					// Data is prefixed with junk
					junk = msg.substr(0, valid_pos);
					message = msg.substr(valid_pos);
				}
				else
				{
					message = msg;
				}
				message = message.substr(3); // Remove triple hash in the beginning

				// Get of rid of junk after the data
				var valid_pos = message.lastIndexOf('###');
				message = message.substr(0, valid_pos); // Remove triple hash in the end

				try {
					var data = eval('('+message+')');
				} catch(err) {
					var msg = err.message + "\n<br/>\n<pre>\n" + message + "\n</pre>";
					if(errorCallback == null)
					{
						if(akeeba_error_callback != null)
						{
							akeeba_error_callback(msg);
						}
					}
					else
					{
						errorCallback(msg);
					}
					return;
				}

				// Call the callback function
				successCallback(data);
			},
			error: function(Request, textStatus, errorThrown) {
				var message = '<strong>AJAX Loading Error</strong><br/>HTTP Status: '+Request.status+' ('+Request.statusText+')<br/>';
				message = message + 'Internal status: '+textStatus+'<br/>';
				message = message + 'XHR ReadyState: ' + Request.readyState + '<br/>';
				message = message + 'Raw server response:<br/>'+Request.responseText;
				if(errorCallback == null)
				{
					if(akeeba_error_callback != null)
					{
						akeeba_error_callback(message);
					}
				}
				else
				{
					errorCallback(message);
				}
			}
		};
		$.ajax( structure );
	}

	function onChangeProcengine(event)
	{
		if( $('#kickstart\\.procengine').val() == 'direct' )
		{
			$('#ftp-options').hide('fast');
		} else {
			$('#ftp-options').show('fast');
		}

		if($('#kickstart\\.procengine').val() == 'sftp' )
		{
			$('#ftp-ssl-passive').hide('fast');

			if($('#kickstart\\.ftp\\.dir').val() == ''){
				$('#kickstart\\.ftp\\.dir').val(sftp_path);
			}

			$('#testFTP').html(trans('BTN_TESTSFTPCON'))
		}
		else
		{
			$('#ftp-ssl-passive').show('fast');
			$('#testFTP').html(trans('BTN_TESTFTPCON'))
		}
	}

	function closeLightbox(event)
	{
		var closeMe = false;

		if( (event == null) || (event == undefined) ) {
			closeMe = true;
		} else if(event.keyCode == '27') {
			closeMe = true;
		}

		if(closeMe)
		{
			document.getElementById('preextraction').style.display='none';
			document.getElementById('genericerror').style.display='none';
			document.getElementById('fade').style.display='none';
			$(document).unbind('keyup', closeLightbox);
		}
	}

	function onGenericError(msg)
	{
		$('#genericerrorInner').html(msg);
		$('#genericerror').css('display','block');
		$('#fade').css('display','block');
		$(document).keyup(closeLightbox);
	}

	function setProgressBar(percent)
	{
		var newValue = 0;

		if(percent <= 1) {
			newValue = 100 * percent;
		} else {
			newValue = percent;
		}

		$('#progressbar-inner').css('width',percent+'%');
	}

	function oncheckFTPTempDirClick(event)
	{
		var data = {
			'task' : 'checkTempdir',
			'json': JSON.stringify({
				'kickstart.ftp.tempdir': $('#kickstart\\.ftp\\.tempdir').val()
			})
		};

		doAjax(data, function(ret){
			var key = ret.status ? 'FTP_TEMPDIR_WRITABLE' : 'FTP_TEMPDIR_UNWRITABLE';
			alert( trans(key) );
		});
	}

	function onTestFTPClick(event)
	{
		var type = 'ftp';

		if($('#kickstart\\.procengine').val() == 'sftp')
		{
			type = 'sftp';
		}

		var data = {
			'task' : 'checkFTP',
			'json': JSON.stringify({
				'type' : type,
				'kickstart.ftp.host':		$('#kickstart\\.ftp\\.host').val(),
				'kickstart.ftp.port':		$('#kickstart\\.ftp\\.port').val(),
				'kickstart.ftp.ssl':		$('#kickstart\\.ftp\\.ssl').is(':checked'),
				'kickstart.ftp.passive':	$('#kickstart\\.ftp\\.passive').is(':checked'),
				'kickstart.ftp.user':		$('#kickstart\\.ftp\\.user').val(),
				'kickstart.ftp.pass':		$('#kickstart\\.ftp\\.pass').val(),
				'kickstart.ftp.dir':		$('#kickstart\\.ftp\\.dir').val(),
				'kickstart.ftp.tempdir':	$('#kickstart\\.ftp\\.tempdir').val()
			})
		};
		doAjax(data, function(ret){
			if(type == 'ftp'){
				var key = ret.status ? 'FTP_CONNECTION_OK' : 'FTP_CONNECTION_FAILURE';
			}
			else{
				var key = ret.status ? 'SFTP_CONNECTION_OK' : 'SFTP_CONNECTION_FAILURE';
			}


			alert( trans(key) + "\n\n" + (ret.status ? '' : ret.message) );
		});
	}

	function onbrowseFTP ()
	{
		if($('#kickstart\\.procengine').val() != 'sftp')
		{
			akeeba_ftpbrowser_host      = $('#kickstart\\.ftp\\.host').val();
			akeeba_ftpbrowser_port      = $('#kickstart\\.ftp\\.port').val();
			akeeba_ftpbrowser_username  = $('#kickstart\\.ftp\\.user').val();
			akeeba_ftpbrowser_password  = $('#kickstart\\.ftp\\.pass').val();
			akeeba_ftpbrowser_passive   = $('#kickstart\\.ftp\\.passive').is(':checked');
			akeeba_ftpbrowser_ssl       = $('#kickstart\\.ftp\\.ssl').is(':checked');
			akeeba_ftpbrowser_directory = $('#kickstart\\.ftp\\.dir').val();

			var akeeba_onbrowseFTP_callback = function(path) {
				var charlist = ('/').replace(/([\[\]\(\)\.\?\/\*\{\}\+\$\^\:])/g, '$1');
				var re = new RegExp('^[' + charlist + ']+', 'g');
				path = '/' + (path+'').replace(re, '');
				$('#kickstart\\.ftp\\.dir').val(path);
			};

			akeeba_ftpbrowser_hook( akeeba_onbrowseFTP_callback );
		}
		else
		{
			akeeba_sftpbrowser_host = $('#kickstart\\.ftp\\.host').val();
			akeeba_sftpbrowser_port = $('#kickstart\\.ftp\\.port').val();
			akeeba_sftpbrowser_username = $('#kickstart\\.ftp\\.user').val();
			akeeba_sftpbrowser_password = $('#kickstart\\.ftp\\.pass').val();
			akeeba_sftpbrowser_directory = $('#kickstart\\.ftp\\.dir').val();

			var akeeba_postprocsftp_callback = function(path) {
				var charlist = ('/').replace(/([\[\]\(\)\.\?\/\*\{\}\+\$\^\:])/g, '$1');
				var re = new RegExp('^[' + charlist + ']+', 'g');
				path = '/' + (path+'').replace(re, '');
				$('#kickstart\\.ftp\\.dir').val(path);
			};

			akeeba_sftpbrowser_hook( akeeba_postprocsftp_callback );
		}
	}

	akeeba_ftpbrowser_hook = function( callback )
	{
		var ftp_dialog_element = $("#ftpdialog");
		var ftp_callback = function() {
			callback(akeeba_ftpbrowser_directory);
			ftp_dialog_element.dialog("close");
		};

		ftp_dialog_element.css('display','block');
		ftp_dialog_element.removeClass('ui-state-error');
		ftp_dialog_element.dialog({
			autoOpen	: false,
			title		: trans('CONFIG_UI_FTPBROWSER_TITLE'),
			draggable	: false,
			height		: 500,
			width		: 500,
			modal		: true,
			resizable	: false,
			buttons		: {
				"OK": ftp_callback,
				"Cancel": function() {
					ftp_dialog_element.dialog("close");
				}
			}
		});

		$('#ftpBrowserErrorContainer').css('display','none');
		$('#ftpBrowserFolderList').html('');
		$('#ak_crumbs').html('');

		ftp_dialog_element.dialog('open');

		if(empty(akeeba_ftpbrowser_directory)) akeeba_ftpbrowser_directory = '';

		var data = {
			'task'      : 'ftpbrowse',
			'json': JSON.stringify({
				'host'		: akeeba_ftpbrowser_host,
				'port'		: akeeba_ftpbrowser_port,
				'username'	: akeeba_ftpbrowser_username,
				'password'	: akeeba_ftpbrowser_password,
				'passive'	: (akeeba_ftpbrowser_passive ? 1 : 0),
				'ssl'		: (akeeba_ftpbrowser_ssl ? 1 : 0),
				'directory'	: akeeba_ftpbrowser_directory
			})
		};

		// Do AJAX call and Render results
		doAjax(
			data,
			function(data) {
				if(data.error != false) {
					// An error occured
					$('#ftpBrowserError').html(trans(data.error));
					$('#ftpBrowserErrorContainer').css('display','block');
					$('#ftpBrowserFolderList').css('display','none');
					$('#ak_crumbs').css('display','none');
				} else {
					// Create the interface
					$('#ftpBrowserErrorContainer').css('display','none');

					// Display the crumbs
					if(!empty(data.breadcrumbs)) {
						$('#ak_crumbs').css('display','block');
						$('#ak_crumbs').html('');
						var relativePath = '/';

						akeeba_ftpbrowser_addcrumb(trans('UI-ROOT'), '/', callback);

						$.each(data.breadcrumbs, function(i, crumb) {
							relativePath += '/'+crumb;

							akeeba_ftpbrowser_addcrumb(crumb, relativePath, callback);
						});
					} else {
						$('#ak_crumbs').hide();
					}

					// Display the list of directories
					if(!empty(data.list)) {
						$('#ftpBrowserFolderList').show();

						$.each(data.list, function(i, item) {
							akeeba_ftpbrowser_create_link(data.directory+'/'+item, item, $('#ftpBrowserFolderList'), callback );
						});
					} else {
						$('#ftpBrowserFolderList').css('display','none');
					}
				}
			},
			function(message) {
				$('#ftpBrowserError').html(message);
				$('#ftpBrowserErrorContainer').css('display','block');
				$('#ftpBrowserFolderList').css('display','none');
				$('#ak_crumbs').css('display','none');
			}
		);
	};

	/**
	 * Creates a directory link for the FTP browser UI
	 */
	function akeeba_ftpbrowser_create_link(path, label, container, callback)
	{
		var row = $(document.createElement('tr'));
		var cell = $(document.createElement('td')).appendTo(row);

		var myElement = $(document.createElement('a'))
			.text(label)
			.click(function(){
				akeeba_ftpbrowser_directory = resolvePath(path);
				akeeba_ftpbrowser_hook(callback);
			})
			.appendTo(cell);
		row.appendTo($(container));
	}

	/**
	 * Adds a breadcrumb to the FTP browser
	 */
	function akeeba_ftpbrowser_addcrumb(crumb, relativePath, callback, last)
	{
		if(empty(last)) last = false;
		var li = $(document.createElement('li'));

		$(document.createElement('a'))
			.html(crumb)
			.click(function(e){
				akeeba_ftpbrowser_directory = relativePath;
				akeeba_ftpbrowser_hook(callback);
				e.preventDefault();
			})
			.appendTo(li);

		if(!last) {
			$(document.createElement('span'))
				.text('/')
				.addClass('divider')
				.appendTo(li);
		}

		li.appendTo('#ak_crumbs');
	}

	// FTP browser function
	akeeba_sftpbrowser_hook = function( callback )
	{
		var sftp_dialog_element = $("#ftpdialog");
		var sftp_callback = function() {
			callback(akeeba_sftpbrowser_directory);
			sftp_dialog_element.dialog("close");
		};

		sftp_dialog_element.css('display','block');
		sftp_dialog_element.removeClass('ui-state-error');
		sftp_dialog_element.dialog({
			autoOpen	: false,
			'title'		: trans('CONFIG_UI_SFTPBROWSER_TITLE'),
			draggable	: false,
			height		: 500,
			width		: 500,
			modal		: true,
			resizable	: false,
			buttons		: {
				"OK": sftp_callback,
				"Cancel": function() {
					sftp_dialog_element.dialog("close");
				}
			}
		});

		$('#ftpBrowserErrorContainer').css('display','none');
		$('#ftpBrowserFolderList').html('');
		$('#ak_crumbs').html('');

		sftp_dialog_element.dialog('open');

		if(empty(akeeba_sftpbrowser_directory)) akeeba_sftpbrowser_directory = '';

		var data = {
			'task'      : 'sftpbrowse',
			'json': JSON.stringify({
				'host'		: akeeba_sftpbrowser_host,
				'port'		: akeeba_sftpbrowser_port,
				'username'	: akeeba_sftpbrowser_username,
				'password'	: akeeba_sftpbrowser_password,
				'directory'	: akeeba_sftpbrowser_directory
			})
		};

		doAjax(
			data,
			function(data) {
				if(data.error != false) {
					// An error occured
					$('#ftpBrowserError').html(data.error);
					$('#ftpBrowserErrorContainer').css('display','block');
					$('#ftpBrowserFolderList').css('display','none');
					$('#ak_crumbs').css('display','none');
				} else {
					// Create the interface
					$('#ftpBrowserErrorContainer').css('display','none');

					// Display the crumbs
					if(!empty(data.breadcrumbs)) {
						$('#ak_crumbs').css('display','block');
						$('#ak_crumbs').html('');
						var relativePath = '/';

						akeeba_sftpbrowser_addcrumb(trans('UI-ROOT'), '/', callback);

						$.each(data.breadcrumbs, function(i, crumb) {
							relativePath += '/'+crumb;

							akeeba_sftpbrowser_addcrumb(crumb, relativePath, callback);
						});
					} else {
						$('#ftpBrowserCrumbs').css('display','none');
					}

					// Display the list of directories
					if(!empty(data.list)) {
						$('#ftpBrowserFolderList').css('display','block');

						$.each(data.list, function(i, item) {
							akeeba_sftpbrowser_create_link(data.directory+'/'+item, item, $('#ftpBrowserFolderList'), callback );
						});
					} else {
						$('#ftpBrowserFolderList').css('display','none');
					}
				}
			},
			function(message) {
				$('#ftpBrowserError').html(message);
				$('#ftpBrowserErrorContainer').css('display','block');
				$('#ftpBrowserFolderList').css('display','none');
				$('#ftpBrowserCrumbs').css('display','none');
			}
		);
	};

	/**
	 * Creates a directory link for the SFTP browser UI
	 */
	function akeeba_sftpbrowser_create_link(path, label, container, callback)
	{
		var row = $(document.createElement('tr'));
		var cell = $(document.createElement('td')).appendTo(row);

		var myElement = $(document.createElement('a'))
			.text(label)
			.click(function(){
				akeeba_sftpbrowser_directory = resolvePath(path);
				akeeba_sftpbrowser_hook(callback);
			})
			.appendTo(cell);
		row.appendTo($(container));
	}

	/**
	 * Adds a breadcrumb to the SFTP browser
	 */
	function akeeba_sftpbrowser_addcrumb(crumb, relativePath, callback, last)
	{
		if(empty(last)) last = false;
		var li = $(document.createElement('li'));

		$(document.createElement('a'))
			.html(crumb)
			.click(function(e){
				akeeba_sftpbrowser_directory = relativePath;
				akeeba_sftpbrowser_hook(callback);
				e.preventDefault();
			})
			.appendTo(li);

		if(!last) {
			$(document.createElement('span'))
				.text('/')
				.addClass('divider')
				.appendTo(li);
		}

		li.appendTo('#ak_crumbs');
	}

	function onStartExtraction()
	{
		$('#page1').hide('fast');
		$('#page2').show('fast');

		$('#currentFile').text( '' );

		akeeba_error_callback = errorHandler;

		var data = {
			'task' : 'startExtracting',
			'json': JSON.stringify({
				'kickstart.setup.sourcepath':		$('#kickstart\\.setup\\.sourcepath').val(),
				'kickstart.setup.sourcefile':		$('#kickstart\\.setup\\.sourcefile').val(),
				'kickstart.jps.password':			$('#kickstart\\.jps\\.password').val(),
				'kickstart.tuning.min_exec_time':	$('#kickstart\\.tuning\\.min_exec_time').val(),
				'kickstart.tuning.max_exec_time':	$('#kickstart\\.tuning\\.max_exec_time').val(),
				'kickstart.stealth.enable': 		$('#kickstart\\.stealth\\.enable').is(':checked'),
				'kickstart.stealth.url': 			$('#kickstart\\.stealth\\.url').val(),
				'kickstart.tuning.run_time_bias':	75,
				'kickstart.setup.restoreperms':		0,
				'kickstart.setup.dryrun':			0,
				'kickstart.setup.ignoreerrors':		$('#kickstart\\.setup\\.ignoreerrors').is(':checked'),
				'kickstart.enabled':				1,
				'kickstart.security.password':		'',
				'kickstart.setup.renameback':		$('#kickstart\\.setup\\.renameback').is(':checked'),
				'kickstart.procengine':				$('#kickstart\\.procengine').val(),
				'kickstart.ftp.host':				$('#kickstart\\.ftp\\.host').val(),
				'kickstart.ftp.port':				$('#kickstart\\.ftp\\.port').val(),
				'kickstart.ftp.ssl':				$('#kickstart\\.ftp\\.ssl').is(':checked'),
				'kickstart.ftp.passive':			$('#kickstart\\.ftp\\.passive').is(':checked'),
				'kickstart.ftp.user':				$('#kickstart\\.ftp\\.user').val(),
				'kickstart.ftp.pass':				$('#kickstart\\.ftp\\.pass').val(),
				'kickstart.ftp.dir':				$('#kickstart\\.ftp\\.dir').val(),
				'kickstart.ftp.tempdir':			$('#kickstart\\.ftp\\.tempdir').val()
			})
		};
		doAjax(data, function(ret){
			processRestorationStep(ret);
		});
	}

	function processRestorationStep(data)
	{
		// Look for errors
		if(!data.status)
		{
			errorHandler(data.message);
			return;
		}

		// Propagate warnings to the GUI
		if( !empty(data.Warnings) )
		{
			$.each(data.Warnings, function(i, item){
				$('#warnings').append(
					$(document.createElement('div'))
						.html(item)
				);
				$('#warningsBox').show('fast');
			});
		}

		// Parse total size, if exists
		if(array_key_exists('totalsize', data))
		{
			if(is_array(data.filelist))
			{
				akeeba_restoration_stat_total = 0;
				$.each(data.filelist,function(i, item)
				{
					akeeba_restoration_stat_total += item[1];
				});
			}
			akeeba_restoration_stat_outbytes = 0;
			akeeba_restoration_stat_inbytes = 0;
			akeeba_restoration_stat_files = 0;
		}

		// Update GUI
		akeeba_restoration_stat_inbytes += data.bytesIn;
		akeeba_restoration_stat_outbytes += data.bytesOut;
		akeeba_restoration_stat_files += data.files;
		var percentage = 0;
		if( akeeba_restoration_stat_total > 0 )
		{
			percentage = 100 * akeeba_restoration_stat_inbytes / akeeba_restoration_stat_total;
			if(percentage < 0) {
				percentage = 0;
			} else if(percentage > 100) {
				percentage = 100;
			}
		}
		if(data.done) percentage = 100;
		setProgressBar(percentage);
		$('#currentFile').text( data.lastfile );

		if(!empty(data.factory)) akeeba_factory = data.factory;

		post = {
			'task'	: 'continueExtracting',
			'json'	: JSON.stringify({factory: akeeba_factory})
		};

		if(!data.done)
		{
			doAjax(post, function(ret){
				processRestorationStep(ret);
			});
		}
		else
		{
			$('#page2a').hide('fast');
			$('#extractionComplete').show('fast');

			$('#runInstaller').css('display','inline-block');
		}
	}

	function onGotoStartClick(event)
	{
		$('#page2').hide('fast');
		$('#error').hide('fast');
		$('#page1').show('fast');
	}

	function onRunInstallerClick(event)
	{
		var windowReference = window.open('installation/index.php','installer');
		if(!windowReference.opener) {
			windowReference.opener = this.window;
		}
		$('#runCleanup').css('display','inline-block');
		$('#runInstaller').hide('fast');
	}

	function onRunCleanupClick(event)
	{
		post = {
			'task'	: 'isJoomla',
			// Passing the factory preserves the renamed files array
			'json'	: JSON.stringify({factory: akeeba_factory})
		};

		doAjax(post, function(ret){
			isJoomla = ret;
			onRealRunCleanupClick();
		});
	}

	function onRealRunCleanupClick()
	{
		post = {
			'task'	: 'cleanUp',
			// Passing the factory preserves the renamed files array
			'json'	: JSON.stringify({factory: akeeba_factory})
		};

		doAjax(post, function(ret){
			$('#runCleanup').hide('fast');
			$('#gotoSite').css('display','inline-block');
			if (isJoomla)
			{
				$('#gotoAdministrator').css('display','inline-block');
			}
			else
			{
				$('#gotoAdministrator').css('display','none');
			}
			$('#gotoPostRestorationRroubleshooting').css('display','block');
		});

	}

	function errorHandler(msg)
	{
		$('#errorMessage').html(msg);
		$('#error').show('fast');
	}

	function onresetFTPTempDir(event)
	{
		$('#kickstart\\.ftp\\.tempdir').val('<?php echo addcslashes(AKKickstartUtils::getPath(),'\\\'"') ?>');
	}

	function onArchiveListReload()
	{
		post = {
			'task'	: 'listArchives',
			'json'	: JSON.stringify({path: $('#kickstart\\.setup\\.sourcepath').val()})
		}

		doAjax(post, function(ret){
			$('#sourcefileContainer').html(ret);
		});
	}

	/**
	 * Akeeba Kickstart Update Check
	 */

	var akeeba_update = {version: '0'};
	var akeeba_version = '##VERSION##';

	function version_compare (v1, v2, operator) {
		// BEGIN REDUNDANT
		this.php_js = this.php_js || {};
		this.php_js.ENV = this.php_js.ENV || {};
		// END REDUNDANT
		// Important: compare must be initialized at 0.
		var i = 0,
			x = 0,
			compare = 0,
		// vm maps textual PHP versions to negatives so they're less than 0.
		// PHP currently defines these as CASE-SENSITIVE. It is important to
		// leave these as negatives so that they can come before numerical versions
		// and as if no letters were there to begin with.
		// (1alpha is < 1 and < 1.1 but > 1dev1)
		// If a non-numerical value can't be mapped to this table, it receives
		// -7 as its value.
			vm = {
				'dev': -6,
				'alpha': -5,
				'a': -5,
				'beta': -4,
				'b': -4,
				'RC': -3,
				'rc': -3,
				'#': -2,
				'p': -1,
				'pl': -1
			},
		// This function will be called to prepare each version argument.
		// It replaces every _, -, and + with a dot.
		// It surrounds any nonsequence of numbers/dots with dots.
		// It replaces sequences of dots with a single dot.
		//    version_compare('4..0', '4.0') == 0
		// Important: A string of 0 length needs to be converted into a value
		// even less than an unexisting value in vm (-7), hence [-8].
		// It's also important to not strip spaces because of this.
		//   version_compare('', ' ') == 1
			prepVersion = function (v) {
				v = ('' + v).replace(/[_\-+]/g, '.');
				v = v.replace(/([^.\d]+)/g, '.$1.').replace(/\.{2,}/g, '.');
				return (!v.length ? [-8] : v.split('.'));
			},
		// This converts a version component to a number.
		// Empty component becomes 0.
		// Non-numerical component becomes a negative number.
		// Numerical component becomes itself as an integer.
			numVersion = function (v) {
				return !v ? 0 : (isNaN(v) ? vm[v] || -7 : parseInt(v, 10));
			};
		v1 = prepVersion(v1);
		v2 = prepVersion(v2);
		x = Math.max(v1.length, v2.length);
		for (i = 0; i < x; i++) {
			if (v1[i] == v2[i]) {
				continue;
			}
			v1[i] = numVersion(v1[i]);
			v2[i] = numVersion(v2[i]);
			if (v1[i] < v2[i]) {
				compare = -1;
				break;
			} else if (v1[i] > v2[i]) {
				compare = 1;
				break;
			}
		}
		if (!operator) {
			return compare;
		}

		// Important: operator is CASE-SENSITIVE.
		// "No operator" seems to be treated as less than
		// Any other values seem to make the function return null.
		switch (operator) {
			case '>':
			case 'gt':
				return (compare > 0);
			case '>=':
			case 'ge':
				return (compare >= 0);
			case '<=':
			case 'le':
				return (compare <= 0);
			case '==':
			case '=':
			case 'eq':
				return (compare === 0);
			case '<>':
			case '!=':
			case 'ne':
				return (compare !== 0);
			case '':
			case '<':
			case 'lt':
				return (compare < 0);
			default:
				return null;
		}
	}

	function checkUpdates()
	{
		var structure =
		{
			type: "GET",
			url: 'http://query.yahooapis.com/v1/public/yql',
			data: {
				<?php if(KICKSTARTPRO): ?>
				q: 'SELECT * FROM xml WHERE url="http://nocdn.akeebabackup.com/updates/kickstart.xml"',
				<?php else: ?>
				q: 'SELECT * FROM xml WHERE url="http://nocdn.akeebabackup.com/updates/kickstartpro.xml"',
				<?php endif; ?>
				format: 'json',
				callback: 'updatesCallback'
			},
			cache: true,
			crossDomain: true,
			jsonp: 'updatesCallback',
			timeout: 15000
		};
		$.ajax( structure );
	}

	function updatesCallback(msg)
	{
		$.each(msg.query.results.updates.update, function(i, el){
			var myUpdate = {
				'version'	: el.version,
				'infourl'	: el.infourl['content'],
				'dlurl'		: el.downloads.downloadurl.content
			}
			if(version_compare(myUpdate.version, akeeba_update.version, 'ge')) {
				akeeba_update = myUpdate;
			}
		});

		if(version_compare(akeeba_update.version, akeeba_version, 'gt')) {
			notifyAboutUpdates();
		}
	}

	function notifyAboutUpdates()
	{
		$('#update-version').text(akeeba_update.version);
		$('#update-dlnow').attr('href', akeeba_update.dlurl);
		$('#update-whatsnew').attr('href', akeeba_update.infourl);
		$('#update-notification').show('slow');
	}

	<?php callExtraFeature('onExtraHeadJavascript'); ?>
</script>
<?php
}