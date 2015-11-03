<?php
/**
 * Joomla! Start
 *
 * Allows you to download and install Joomla! on your server, without having
 * to manually upload / download anything.
 *
 * This tool is derived from Akeeba Kickstart, the on-line archive extraction
 * tool by Akeeba Ltd.
 *
 * @copyright   2010-2014 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL v2 or - at your option - any later version
 * @package     joomla
 * @subpackage  joomlastart
 */

/**
 * Shows the interface page of the application
 */
function echoPage()
{
	$translationStrings = getTranslationStrings();

	$downloadHelper = new JoomlastartDownload();
	$downloadRet = $downloadHelper->getJoomlaDownloadURL();

	$vars = array(
		'download-error'	=> $downloadRet['error'],
		'download-url'		=> $downloadRet['url'],
		'ftp-checked'		=> $downloadRet['needftp'] ? 'selected="selected"' : '',
		'copyright-year' 	=> date('Y'),
		'self_filename'		=> defined('KSSELFNAME') ? KSSELFNAME : basename(__FILE__),
	);

	echo <<< HTML

<!DOCTYPE html>
<html>
<head>
	<title>Joomla! Start</title>

	<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<!-- Bootstrap -->
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" integrity="sha512-dTfge/zgoMYpP7QbHy4gWMEGsbsdZeCXz7irItjcC3sPUFtf0kuFbDz/ixG7ArTxmDjLXDmezHubeNikyKGVyQ==" crossorigin="anonymous">
	<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!--[if lt IE 9]>
	<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
	<script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
	<![endif]-->

	<style type="text/css">
body {
  margin-top: 60px;
}
	</style>
</head>
<body>
<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
	<div class="container">
		<div class="navbar-header">
			<a class="navbar-brand" href="https://www.joomla.org" target="_blank">
				<img alt="Joomla!" src="https://cdn.joomla.org/images/site_header.png" height="24" style="display: inline-block">
				<span>JS_APPTITLE</span> <span>##VERSION##</span>
			</a>
		</div>
	</div>
</div>

<div class="container">
	<div id="ie7Warning" class="panel panel-warning" style="display: none;">
		<div class="panel-heading">
			<span class="glyphicon glyphicon-warning-sign"></span>
			<span>JS_ERR_IE7_TITLE</span>
		</div>
		<div class="panel-body">
			<p>JS_ERR_IE7_DETAILS</p>
			<p>JS_ERR_IE7_INFO</p>
		</div>
	</div>

	<div id="genericerror" class="panel panel-danger" style="display: none;">
		<div class="panel-heading">JS_ERR_GENERICERROR_HEADER</div>
		<div class="panel-body" id="genericerrorInner"></div>
	</div>

	<div id="pageDownload">
		<div id="downloadInformation">
			<div class="panel panel-default">
				<div class="panel-heading">JS_LBL_INTROTOTHISAPP_HEADER</div>
				<div class="panel-body">
					<p>JS_LBL_INTROTOTHISAPP</p>
				</div>
			</div>
			<div class="form form-horizontal" role="form">
				<div class="form-group">
					<label class="control-label col-sm-2 col-xs-12" for="filename">JS_LBL_JDLURL</label>
					<div class="col-sm-10 col-xs-12">
						<input name="filename" id="filename" class="form-control" value="{$vars['download-url']}" disabled="disabled">
					</div>
				</div>

				<div class="form-group">
					<label class="control-label col-sm-2 col-xs-12" for="procengine">JS_LBL_PROCENGINE</label>
					<div class="col-sm-10 col-xs-12">
						<select name="procengine" id="procengine" class="form-control">
							<option value="direct">JS_LBL_WRITE_DIRECTLY</option>
							<option value="ftp" {$vars['ftp-checked']}>JS_LBL_WRITE_FTP</option>
						</select>
					</div>
				</div>

				<div id="downloadInformationFTP">
					<div class="col-sm-offset-2 col-sm-10 col-xs-12">
						<div class="alert alert-info">JS_WARNING_FTPINFO_REQUIRED</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-2 col-xs-12" for="ftp_host">JS_LBL_FTP_HOST</label>
						<div class="col-sm-10 col-xs-12">
							<input name="ftp_host" id="ftp_host" type="text" class="form-control" value="">
							<p class="help-block">JS_LBL_FTP_HOST_HELP</p>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-2 col-xs-12" for="ftp_port">JS_LBL_FTP_PORT</label>
						<div class="col-sm-10 col-xs-12">
							<input name="ftp_port" id="ftp_port" type="text" class="form-control" value="21">
							<p class="help-block">JS_LBL_FTP_PORT_HELP</p>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-2 col-xs-12" for="ftp_user">JS_LBL_FTP_USER</label>
						<div class="col-sm-10 col-xs-12">
							<input name="ftp_user" id="ftp_user" type="text" class="form-control" value="">
							<p class="help-block">JS_LBL_FTP_USER_HELP</p>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-2 col-xs-12" for="ftp_pass">JS_LBL_FTP_PASS</label>
						<div class="col-sm-10 col-xs-12">
							<input name="ftp_pass" id="ftp_pass" type="password" class="form-control" value="">
							<p class="help-block">JS_LBL_FTP_PASS_HELP</p>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-2 col-xs-12" for="ftp_dir">JS_LBL_FTP_DIR</label>
						<div class="col-sm-10 col-xs-12">
							<input name="ftp_dir" id="ftp_dir" type="text" class="form-control" value="">
							<p class="help-block">JS_LBL_FTP_DIR_HELP</p>
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-offset-2 col-sm-10 col-xs-12">
							<div class="checkbox">
								<label>
									<input type="checkbox" name="ftp_passive" checked="checked">
									<span>JS_LBL_FTP_PASSIVE</span>
								</label>
							</div>
							<p class="help-block">JS_LBL_FTP_PASSIVE_HELP</p>
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-offset-2 col-sm-10 col-xs-12">
							<div class="checkbox">
								<label>
									<input type="checkbox" name="ftp_ssl">
									<span>JS_LBL_FTP_SSL</span>
								</label>
							</div>
							<p class="help-block">JS_LBL_FTP_SSL_HELP</p>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-2 col-xs-12" for="ftp_tempdir">JS_LBL_FTP_TEMPDIR</label>
						<div class="col-sm-10 col-xs-12">
							<input name="ftp_tempdir" id="ftp_tempdir" type="text" class="form-control" value="">
							<p class="help-block">JS_LBL_FTP_TEMPDIR_HELP</p>
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-offset-2 col-sm-10 col-xs-12">
							<button class="btn btn-default" id="testFTP" onclick="onTestFTPClick(); return false;">
								<span class="glyphicon glyphicon-check"></span>
								<span>JS_BTN_TESTFTP</span>
							</button>
						</div>
					</div>
				</div>

				<div class="form-group">
					<div class="col-sm-offset-2 col-sm-10 col-xs-12">
						<button class="btn btn-lg btn-primary" id="startDownload" onclick="onStartDownload(); return false;">
							<span class="glyphicon glyphicon-cloud-download"></span>
							<span>JS_BTN_INSTALLJOOMLA</span>
						</button>
					</div>
				</div>
			</div>
		</div>

		<div id="downloadProgress">
			<div class="alert alert-warning">
				<span class="glyphicon glyphicon-warning-sign"></span>
				<span>JS_WARNING_DONTCLOSEDOWNLOAD</span>
			</div>

			<div class="progress progress-striped active">
				<div id="downloadProgressBar" class="progress-bar" role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: 45%">
					<span class="sr-only" id="downloadProgressBarInfo">45% Complete</span>
				</div>
			</div>
			<div class="panel panel-info">
				<div class="panel-heading">JS_LBL_DOWNLOADPROGRESS</div>
				<div class="panel-body" id="downloadProgressBarText"></div>
			</div>
		</div>

		<div id="downloadError">
			<div class="panel panel-danger">
				<div class="panel-heading">JS_ERR_DOWNLOADERROR_HEADER</div>
				<div class="panel-body" id="downloadErrorText"></div>
			</div>
		</div>
	</div>

	<div id="pageExtract">
		<div id="extractProgress">
			<div class="alert alert-warning">
				<span class="glyphicon glyphicon-warning-sign"></span>
				<span>JS_WARNING_DONTCLOSEEXTRACT</span>
			</div>

			<div class="progress progress-striped active">
				<div id="extractProgressBar" class="progress-bar" role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: 45%">
					<span class="sr-only" id="extractProgressBarInfo">45% Complete</span>
				</div>
			</div>
			<div class="panel panel-info">
				<div class="panel-heading">JS_LBL_EXTRACTPROGRESS</div>
				<div class="panel-body" id="extractProgressBarText"></div>
			</div>
		</div>

		<div id="extractError">
			<div class="panel panel-danger">
				<div class="panel-heading">JS_ERR_EXTRACTERROR_HEADER</div>
				<div class="panel-body" id="extractErrorText"></div>
			</div>
		</div>
	</div>

	<div id="pageInstaller">
		<div class="panel panel-default">
			<div class="panel-heading">JS_LBL_RUNINSTALLER_HEADER</div>
			<div class="panel-body">
				<p>JS_LBL_RUNINSTALLER</p>
				<p>
					<button id="runInstaller" class="btn btn-primary btn-lg" onclick="onRunInstaller(); return false;">
						<span class="glyphicon glyphicon-hdd"></span>
						<span>JS_BTN_RUNINSTALLER</span>
					</button>
				</p>
			</div>
		</div>
	</div>

	<div id="pageCleanUp">
		<div class="panel panel-default">
			<div class="panel-heading">JS_LBL_CLEANUP_HEADER</div>
			<div class="panel-body">
				<p>JS_LBL_CLEANUP</p>
				<p>
					<button id="cleanup" class="btn btn-danger btn-lg" onclick="onRunCleanupClick(); return false;">
						<span class="glyphicon glyphicon-fire"></span>
						<span>JS_BTN_CLEANUP</span>
					</button>
				</p>
			</div>
		</div>
	</div>

	<div id="pageFinish">
		<div class="panel panel-success">
			<div class="panel-heading">JS_LBL_FINISHED_HEADER</div>
			<div class="panel-body">
				<p>JS_LBL_FINISHED</p>
				<p>
					<a href="index.php" class="btn btn-default">
						<span class="glyphicon glyphicon-globe"></span>
						<span>JS_BTN_FRONTEND</span>
					</a>
					<a href="index.php" class="btn btn-primary btn-lg">
						<span class="glyphicon glyphicon-dashboard"></span>
						<span>JS_BTN_FRONTEND</span>
					</a>
				</p>
			</div>
		</div>
	</div>
</div>

<!-- jQuery -->
<script src="//code.jquery.com/jquery-1.10.1.min.js"></script>
<script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
<!-- JSON2 library -->
<script type="text/javascript" src="//yandex.st/json2/2011-10-19/json2.min.js"></script>
<!-- Bootstrap JS -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js" integrity="sha512-K1qjQ+NcF2TYO/eI3M6v8EiNYZfA95pQumfvcVrTHtwQVDG+aHRqLi/ETn2uB+1JqwYqVG3LIvdm9lj6imS/pQ==" crossorigin="anonymous"></script>
<!-- Joomla! Start application -->
<script type="text/javascript" language="javascript">
	var translation = {
		$translationStrings
	}

	var joomlastart_url_filename = $('#filename').val();
	var joomlastart_ajax_url = '{$vars['self_filename']}';
	var joomlastart_error_callback = onGenericError;
	var joomlastart_restoration_stat_inbytes = 0;
	var joomlastart_restoration_stat_outbytes = 0;
	var joomlastart_restoration_stat_files = 0;
	var joomlastart_restoration_stat_total = 0;
	var joomlastart_factory = null;

	function getJsonParamsObject()
	{
		var ret = {
				'kickstart.setup.sourcefile':		'joomla.zip',
				'kickstart.jps.password':			'',
				'kickstart.tuning.min_exec_time':	1,
				'kickstart.tuning.max_exec_time':	5,
				'kickstart.stealth.enable': 		0,
				'kickstart.stealth.url': 			'',
				'kickstart.tuning.run_time_bias':	75,
				'kickstart.setup.restoreperms':		0,
				'kickstart.setup.dryrun':			0,
				'kickstart.setup.ignoreerrors':		0,
				'kickstart.enabled':				1,
				'kickstart.security.password':		'',
				'kickstart.procengine':				$('#procengine').val(),
				'kickstart.ftp.host':				$('#ftp_host').val(),
				'kickstart.ftp.port':				$('#ftp_port').val(),
				'kickstart.ftp.ssl':				$('#ftp_ssl').is(':checked'),
				'kickstart.ftp.passive':			$('#ftp_passive').is(':checked'),
				'kickstart.ftp.user':				$('#ftp_user').val(),
				'kickstart.ftp.pass':				$('#ftp_pass').val(),
				'kickstart.ftp.dir':				$('#ftp_dir').val(),
				'kickstart.ftp.tempdir':			$('#ftp_tempdir').val(),
				'file':								joomlastart_url_filename,
				'localFile':						"joomla.zip"
		}

		return ret;
	}

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

	/**
	 * Applies the translation strings to the page
	 *
	 * @return  void
	 */
	function translateGUI()
	{
		$('*').each(function(i,e){
			transKey = $(e).text();
			if(translation[transKey] != undefined)
			{
				$(e).html( translation[transKey] );
			}
		});
	}

	/**
	 * Returns a translated string
	 *
	 * @param   string  The translation key
	 *
	 * @return  string  The translated string, or the key itself if a translation is not available
	 */
	function trans(key)
	{
		if(translation[key] != undefined)
		{
			return translation[key];
		}
		else
		{
			return key;
		}
	}

	function empty (mixed_var)
	{
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

	    if (typeof mixed_var == 'object')
	    {
	        for (key in mixed_var)
	        {
	            return false;
	        }

	        return true;
	    }

	    return false;
	}

	function is_array (mixed_var)
	{
	    var key = '';
	    var getFuncName = function (fn) {
	        var name = (/\W*function\s+([\w\$]+)\s*\(/).exec(fn);

	        if (!name) {
	            return '(Anonymous)';
	        }

	        return name[1];
	    };

	    if (!mixed_var)
	    {
	        return false;
	    }

	    // BEGIN REDUNDANT
	    this.php_js = this.php_js || {};
	    this.php_js.ini = this.php_js.ini || {};
	    // END REDUNDANT

	    if (typeof mixed_var === 'object')
	    {
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

	        if (mixed_var.hasOwnProperty)
	        {
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

	/**
	 * Performs an AJAX request and returns the parsed JSON output.
	 * The global joomlastart_ajax_url is used as the AJAX proxy URL.
	 * If there is no errorCallback, the global joomlastart_error_callback is used.
	 * @param data An object with the query data, e.g. a serialized form
	 * @param successCallback A function accepting a single object parameter, called on success
	 * @param errorCallback A function accepting a single string parameter, called on failure
	 */
	function doAjax(data, successCallback, errorCallback)
	{
		var structure =
		{
			type: "POST",
			url: joomlastart_ajax_url,
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
						if(joomlastart_error_callback != null)
						{
							joomlastart_error_callback(msg);
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
					var msg = err.message + "<br/><pre>\\n" + message + "\\n</pre>";
					if(errorCallback == null)
					{
						if(joomlastart_error_callback != null)
						{
							joomlastart_error_callback(msg);
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
				message = message + 'XHR ReadyState: ' + Response.readyState + '<br/>';
				message = message + 'Raw server response:<br/>'+Request.responseText;
				if(errorCallback == null)
				{
					if(joomlastart_error_callback != null)
					{
						joomlastart_error_callback(message);
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

	function onGenericError(msg)
	{
		$('#genericerrorInner').html(msg);
		$('#genericerror').css('display','block');
	}

	function setProgressBar(percent, progressBarId)
	{
		if (progressBarId == undefined)
		{
			progressBarId = 'downloadProgressBar';
		}

		var newValue = 0;

		if(percent <= 1)
		{
			newValue = 100 * percent;
		}
		else
		{
			newValue = percent;
		}

		if (newValue < 0)
		{
			newValue = 0;
		}
		else if (newValue > 100)
		{
			newValue = 100;
		}

		$('#' + progressBarId).css('width',percent + '%');
		$('#' + progressBarId + 'Info').text(percent + '%');
	}

	function oncheckFTPTempDirClick(event)
	{
		var data = {
			'task' : 'checkTempdir',
			'json': JSON.stringify({
				'kickstart.ftp.tempdir': $('#ftp_tempdir').val()
			})
		};

		doAjax(data, function(ret){
			var key = ret.status ? 'FTP_TEMPDIR_WRITABLE' : 'FTP_TEMPDIR_UNWRITABLE';
			alert( trans(key) );
		});
	}

	function onTestFTPClick(event)
	{
		var jsonObject = getJsonParamsObject();

		var data = {
			'task' : 'checkFTP',
			'json': JSON.stringify(jsonObject)
		};
		doAjax(data, function(ret){
			var key = ret.status ? 'FTP_CONNECTION_OK' : 'FTP_CONNECTION_FAILURE';
			alert( trans(key) + "\\n\\n" + (ret.status ? '' : ret.message) );
		});
	}

	function humanFileSize(bytes, si) {
		var thresh = si ? 1000 : 1024;
		if(bytes < thresh) return bytes + ' B';
		var units = si ? ['kB','MB','GB','TB','PB','EB','ZB','YB'] : ['KiB','MiB','GiB','TiB','PiB','EiB','ZiB','YiB'];
		var u = -1;
		do {
			bytes /= thresh;
			++u;
		} while(bytes >= thresh);
		return bytes.toFixed(1)+' '+units[u];
	};

	function onStartDownload()
	{
		resetGUI();
		$('#pageDownload').css('display', 'block');
		$('#downloadInformation').css('display', 'none');
		$('#downloadProgress').css('display', 'block');
		$('#downloadError').css('display', 'none');

		joomlastart_url_filename = $('#filename').val();
		joomlastart_start_download();
	}

	function joomlastart_start_download()
	{
		joomlastart_error_callback = onDownloadError;

		setProgressBar(0, 'downloadProgressBar');
		$('#downloadProgressBarText').html('');

		var jsonObject = getJsonParamsObject();
		jsonObject.frag = -1;
		jsonObject.totalSize = -1;

		var data = {
			'task' : 'urlimport',
			'json' : JSON.stringify(jsonObject)
		};

		doAjax(data, function(ret){
			joomlastart_download_step(ret);
		});
	}

	function joomlastart_download_step(data)
	{
		// Look for errors
		if(!data.status)
		{
			onDownloadError(data.error);
			return;
		}

		var totalSize = 0;
		var doneSize = 0;
		var percent = 0;
		var frag = -1;

		// get running stats
		if(data.totalSize != undefined) {
			totalSize = data.totalSize;
		}

		if(data.doneSize != undefined) {
			doneSize = data.doneSize;
		}

		if(data.percent != undefined) {
			percent = data.percent;
		}

		if(data.frag != undefined) {
			frag = data.frag;
		}

		// Update GUI
		setProgressBar(percent, 'downloadProgressBar');
		$('#downloadProgressBarText').text( percent.toFixed(1) + '% (' + humanFileSize(doneSize, 0) + ' / ' + humanFileSize(totalSize, 0) + ')' );

		var jsonObject = getJsonParamsObject();
		jsonObject.frag = frag;
		jsonObject.totalSize = totalSize;
		jsonObject.doneSize = doneSize;

		post = {
			'task'	: 'urlimport',
			'json'	: JSON.stringify(jsonObject)
		};

		if(percent < 100)
		{
			// More work to do
			doAjax(post, function(ret){
				joomlastart_download_step(ret);
			});
		} else {
			// Done!
			setProgressBar(100, 'downloadProgressBar');
			$('#pageDownload').css('display', 'none');
			$('#pageExtract').css('display', 'block');
			onStartExtraction();
		}
	}

	function onDownloadError(msg)
	{
		resetGUI();
		$('#pageDownload').css('display', 'block');
		$('#downloadInformation').css('display', 'none');
		$('#downloadProgress').css('display', 'none');
		$('#downloadError').css('display', 'block');

		$('#downloadErrorText').html(msg);
	}


	function onStartExtraction()
	{
		resetGUI();
		$('#pageExtract').css('display', 'block');
		$('#extractProgress').css('display', 'block');
		$('#extractError').css('display', 'none');

		setProgressBar(0, 'extractProgressBar');
		$('#extractProgressBarText').html('');

		joomlastart_error_callback = onExtractError;

		var jsonObject = getJsonParamsObject();

		var data = {
			'task' : 'startExtracting',
			'json': JSON.stringify(jsonObject)
		};

		doAjax(data, function(ret){
			joomlastart_extract_step(ret);
		});
	}

	function joomlastart_extract_step(data)
	{
		// Look for errors
		if(!data.status)
		{
			onExtractError(data.message);
			return;
		}

		// Propagate warnings to the GUI
		if( !empty(data.Warnings) )
		{
			/*
			$.each(data.Warnings, function(i, item){
				$('#warnings').append(
					$(document.createElement('div'))
					.html(item)
				);
				$('#warningsBox').show('fast');
			});
			*/
		}

		// Parse total size, if exists
		if(data.totalsize != undefined)
		{
			if(is_array(data.filelist))
			{
				joomlastart_restoration_stat_total = 0;
				$.each(data.filelist,function(i, item)
				{
					joomlastart_restoration_stat_total += item[1];
				});
			}
			joomlastart_restoration_stat_outbytes = 0;
			joomlastart_restoration_stat_inbytes = 0;
			joomlastart_restoration_stat_files = 0;
		}

		// Update GUI
		joomlastart_restoration_stat_inbytes += data.bytesIn;
		joomlastart_restoration_stat_outbytes += data.bytesOut;
		joomlastart_restoration_stat_files += data.files;
		var percentage = 0;
		if (joomlastart_restoration_stat_total > 0)
		{
			percentage = 100 * joomlastart_restoration_stat_inbytes / joomlastart_restoration_stat_total;

			if(percentage < 0)
			{
				percentage = 0;
			}
			else if (percentage > 100)
			{
				percentage = 100;
			}
		}

		if(data.done) percentage = 100;

		setProgressBar(percentage, 'extractProgressBar');
		$('#extractProgressBarText').html('<span class="glyphicon glyphicon-stats"></span> ' + percentage.toFixed(1) + '%<br/><span class="glyphicon glyphicon-floppy-open"></span> ' + humanFileSize(joomlastart_restoration_stat_inbytes, 0) + ' / ' + humanFileSize(joomlastart_restoration_stat_total, 0) + '<br/><span class="glyphicon glyphicon-floppy-save"></span> ' + humanFileSize(joomlastart_restoration_stat_outbytes, 0) + '<br/><span class="glyphicon glyphicon-file"></span>' + data.lastfile);

		if(!empty(data.factory)) joomlastart_factory = data.factory;

		post = {
			'task'	: 'continueExtracting',
			'json'	: JSON.stringify({factory: joomlastart_factory})
		};

		if(!data.done)
		{
			doAjax(post, function(ret){
				joomlastart_extract_step(ret);
			});
		}
		else
		{
			setProgressBar(100, 'extractProgressBar');
			onShowInstallerPage();
		}
	}

	function onExtractError(msg)
	{
		resetGUI();
		$('#pageExtract').css('display', 'block');
		$('#extractProgress').css('display', 'none');
		$('#extractError').css('display', 'block');

		$('#extractErrorText').html(msg);
	}

	function onShowInstallerPage()
	{
		resetGUI();

		$('#pageInstaller').css('display', 'block');
	}

	function onRunInstaller()
	{
		var windowReference = window.open('installation/index.php', 'installer');
		if (!windowReference.opener)
		{
			windowReference.opener = this.window;
		}

		resetGUI();
		$('#pageCleanUp').css('display', 'block');
	}

	function onRunCleanupClick(event)
	{
		joomlastart_error_callback = onGenericError;

		post = {
			'task'	: 'cleanUp',
			// Passing the factory preserves the renamed files array
			'json'	: JSON.stringify({factory: joomlastart_factory})
		};

		doAjax(post, function(ret)
		{
			resetGUI();
			$('#pageFinish').css('display', 'block');
		});
	}

	/*
	 * Resets the GUI to its initial state
	 */
	function resetGUI()
	{
		var IEVersion = getInternetExplorerVersion();
		$('#ie7Warning').css('display', 'none');

		if ((IEVersion > 0) && (IEVersion < 9))
		{
			$('#ie7Warning').css('display', 'block');
		}

		$('#genericerror').css('display', 'none');
		$('#genericerrorInner').html('');

		$('#pageDownload').css('display', 'none');
		$('#pageExtract').css('display', 'none');
		$('#pageInstaller').css('display', 'none');
		$('#pageCleanUp').css('display', 'none');
		$('#pageFinish').css('display', 'none');

		resetPageDownload();

		onPageDownloadWriteMethod();
	}

	function resetPageDownload()
	{
		$('#downloadInformation').css('display', 'block');
		$('#downloadInformationFTP').css('display', 'none');

		$('#downloadProgress').css('display', 'none');
		resetPageDownloadProgress();

		$('#downloadError').css('display', 'none');
		resetPageDownloadError();
	}

	function resetPageDownloadProgress()
	{
		$('#downloadProgressBar').css('width', '0%');
		$('#downloadProgressBarInfo').text('');
		$('#downloadProgressBarText').text('');
	}

	function resetPageDownloadError()
	{
		$('#downloadErrorText').html('');
	}

	function onPageDownloadWriteMethod()
	{
		var method = $('#procengine').val();

		if (method == 'ftp')
		{
			$('#downloadInformationFTP').css('display', 'block');
		}
		else
		{
			$('#downloadInformationFTP').css('display', 'none');
		}
	}

	// Start the application
	$(document).ready(function(){
		resetGUI();
		translateGUI();

		if (empty(joomlastart_url_filename))
		{
			var blurb = trans('JS_ERR_GETTINGJOOMLAURL');
			$('#genericerrorInner').html(blurb + '<br/>{$vars['download-error']}');
			$('#genericerror').css('display', 'block');
			return;
		}

		$('#pageDownload').css('display', 'block');

		$('#procengine').click(onPageDownloadWriteMethod);
	});
</script>

</body>
</html>

HTML;
}