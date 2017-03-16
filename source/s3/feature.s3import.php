<?php
/**
 * Akeeba Kickstart
 * A JSON-powered archive extraction tool
 *
 * @copyright   2008-2017 Nicholas K. Dionysopoulos / AkeebaBackup.com
 * @license     GNU GPL v2 or - at your option - any later version
 * @package     akeebabackup
 * @subpackage  kickstart
 */

/**
 * Akeeba Kickstart Professional's S3 Import add-on feature
 */
class AKFeatureS3Import
{
	private $params = array();

	/**
	 * Echoes extra CSS to the head of the page
	 */
	public function onExtraHeadCSS()
	{
		echo <<< CSS

		ul.breadcrumbs,
		ul.breadcrumbs li {
		list-style-type:none;
		display:inline-block;
		margin: 0;
		padding: 0;
		}

		ul.breadcrumbs {
		width: 100%;
		margin: 0.5em 0 1em;
		border: thin solid #ddd;
		background: #F2F5F6;
		background: -moz-linear-gradient(top, #F2F5F6 0%, #E3EAED 37%, #C8D7DC 100%);
		background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#F2F5F6), color-stop(37%,#E3EAED), color-stop(100%,#C8D7DC));
		background: -webkit-linear-gradient(top, #F2F5F6 0%,#E3EAED 37%,#C8D7DC 100%);
		background: -o-linear-gradient(top, #F2F5F6 0%,#E3EAED 37%,#C8D7DC 100%);
		background: -ms-linear-gradient(top, #F2F5F6 0%,#E3EAED 37%,#C8D7DC 100%);
		background: linear-gradient(top, #F2F5F6 0%,#E3EAED 37%,#C8D7DC 100%);

		}

		ul.breadcrumbs li {
		margin: 0 -0.3em 0 0;
		padding: 0.5em 0 0.5em 0.3em;
		height: 1em;
		cursor: pointer;
		}

		ul.breadcrumbs li:hover {
		color: navy;
		}

		ul.breadcrumbs li:after {
		content: "";
		display: block;
		width:1.45em;
		height:1.45em;
		border-top:1px solid #ccc;
		border-right:1px solid #bbb;
		-webkit-transform:rotate(45deg);
		-moz-transform:rotate(45deg);
		transform:rotate(45deg);
		position:relative;
		top:-0.3em;
		right:0.5em;
		z-index:1;
		float: right;
		}

		.listpane {
		width: 46%;
		float: left;
		border: thin solid #DDD;
		margin-bottom: 1em;
		}

		.listpane legend {
		font-size: 1.3rem;
		font-weight: normal;
		line-height: 1.3;
		text-shadow: 0 1px white;
		padding: 0.5em 1em;
		border-left: thin solid #ddd;
		border-right: thin solid #ddd;
		}

		.listpane div.fslist {
		height: 250px;
		overflow-y: scroll;
		}

		.listpane div.fslist div {
		background-color: #F2F5F6;
		border-bottom: 1px solid #ddd;
		padding: 0.3em 0.3em;
		cursor: pointer;
		}

		.listpane div.fslist div:hover {
		color: navy;
		background-color: #E3EAED;
		}

CSS;

	}

	/**
	 * Echoes extra Javascript to the head of the page
	 */
	public function onExtraHeadJavascript()
	{
		echo <<< JS

		var akeeba_s3_filename = null;

		$(document).ready(function(){
		$('#ak-s3-showgui').click(function(e){
		$('#ak-s3-gui').show('fast');
		$('#ak-s3-progress').hide('fast');
		$('#ak-s3-complete').hide('fast');
		$('#ak-s3-error').hide('fast');
		$('#page1-content').hide('fast');
		});
		$('#ak-s3-hidegui').click(function(e){
		$('#ak-s3-gui').hide('fast');
		$('#ak-s3-progress').hide('fast');
		$('#ak-s3-complete').hide('fast');
		$('#ak-s3-error').hide('fast');
		$('#page1-content').show('fast');
		});
		$('#s3\\\\.bucket').change(function(e){
		onAKS3BucketChange();
		});
		$('#ak-s3-reload').click(function(e){
		window.location.reload();
		});
		$('#ak-s3-gotoStart').click(function(e){
		$('#ak-s3-gui').show('fast');
		$('#ak-s3-progress').hide('fast');
		$('#ak-s3-complete').hide('fast');
		$('#ak-s3-error').hide('fast');
		});
		});

		function AKS3setProgressBar(percent)
		{
		var newValue = 0;

		if(percent <= 1) {
		newValue = 100 * percent;
		} else {
		newValue = percent;
		}

		$('#ak-s3-progressbar-inner').css('width',percent+'%');
		}

		function onAKS3Connect()
		{
		// Clear the bucket list
		$('#s3\\\\.bucket').html('');
		$('#ak-s3-bucketselect').css('display','none');
		$('ul.breadcrumbs').hide('fast');
		$('#ak-s3-folderlist').hide('fast');
		$('#ak-s3-filelist').hide('fast');

		akeeba_error_callback = AKS3errorHandler;
		var data = {
		'task' : 's3connect',
		'json' : JSON.stringify({
		'access': $('#s3\\\\.access').val(),
		'secret': $('#s3\\\\.secret').val(),
		})
		};
		doAjax(data, function(ret){
		onAKS3Connect_cb(ret);
		});
		}

		function onAKS3Connect_cb(data)
		{
		// Look for errors
		if(!data.status)
		{
		AKS3errorHandler(data.error);
		return;
		}

		$.each(data.buckets, function(counter, value){
		var option = $(document.createElement('option')).attr('value', value).html(value);
		option.appendTo( $('#s3\\\\.bucket') );
		});

		$('#ak-s3-bucketselect').css('display','inline-block');
		}

		function onAKS3BucketChange()
		{
		$('#breadcrumbs').hide('fast');
		$('#ak-s3-folderlist').html('');
		$('#ak-s3-filelist').html('');

		ak_s3import_chdir('/');
		}

		function ak_s3import_chdir(toWhere)
		{
		akeeba_error_callback = AKS3errorHandler;

		var data = {
		'task' : 's3list',
		'json' : JSON.stringify({
		'access': $('#s3\\\\.access').val(),
		'secret': $('#s3\\\\.secret').val(),
		'bucket': $('#s3\\\\.bucket').val(),
		'newdir': toWhere,
		})
		};
		doAjax(data, function(ret){
		ak_s3import_chdir_cb(ret);
		});
		}

		function ak_s3import_chdir_cb(data)
		{
		// Look for errors
		if(!data.status)
		{
		AKS3errorHandler(data.error);
		return;
		}

		// Update breadcrumbs
		$('ul.breadcrumbs').html('');
		$.each(data.crumbs, function(label, subdir){
		var li = $(document.createElement('li'));
		var a = $(document.createElement('a')).html(label);
		a.click(function(e){
		ak_s3import_chdir(subdir);
		});
		a.appendTo( li );
		li.appendTo( $('#breadcrumbs') );
		});

		$('#breadcrumbs').show('fast');

		// Update folder list
		$('#ak-s3-folderlist').html('');
		$.each(data.folders, function(label, subdir){
		var div = $(document.createElement('div')).html(label);
		div.click(function(e){
		ak_s3import_chdir(subdir);
		});
		div.appendTo( $('#ak-s3-folderlist') );
		});

		$('#ak-s3-folderlist').show('fast');

		// Update file list
		$('#ak-s3-filelist').html('');
		$.each(data.files, function(label, filepath){
		var div = $(document.createElement('div')).html(label);
		div.click(function(e){
		ak_s3import_start(filepath);
		});
		div.appendTo( $('#ak-s3-filelist') );
		});

		$('#ak-s3-filelist').show('fast');
		}

		function ak_s3import_start(filename)
		{
		akeeba_s3_filename = filename;
		akeeba_error_callback = AKS3errorHandler;

		$('#ak-s3-gui').hide('fast');
		$('#ak-s3-progress').show('fast');
		$('#ak-s3-complete').hide('fast');
		$('#ak-s3-error').hide('fast');

		AKS3setProgressBar(0);
		$('#ak-s3-progresstext').html('');

		var data = {
		'task' : 's3import',
		'json' : JSON.stringify({
		'access'    : $('#s3\\\\.access').val(),
		'secret'    : $('#s3\\\\.secret').val(),
		'bucket'    : $('#s3\\\\.bucket').val(),
		'file'        : akeeba_s3_filename,
		'part'        : "-1",
		'frag'        : "-1",
		'totalSize'    : "-1",
		'totalParts': "-1"

		})
		};
		doAjax(data, function(ret){
		ak_s3import_step(ret);
		});
		}

		function ak_s3import_step(data)
		{
		// Look for errors
		if(!data.status)
		{
		AKS3errorHandler(data.error);
		return;
		}

		var totalSize = 0;
		var doneSize = 0;
		var percent = 0;
		var part = -1;
		var frag = -1;

		// get running stats
		if(array_key_exists('totalSize', data)) {
		totalSize = data.totalSize;
		}
		if(array_key_exists('totalParts', data)) {
		totalParts = data.totalParts;
		}
		if(array_key_exists('doneSize', data)) {
		doneSize = data.doneSize;
		}
		if(array_key_exists('percent', data)) {
		percent = data.percent;
		}
		if(array_key_exists('part', data)) {
		part = data.part;
		}
		if(array_key_exists('frag', data)) {
		frag = data.frag;
		}

		// Update GUI
		AKS3setProgressBar(percent);
		$('#ak-s3-progresstext').text( percent+'% ('+doneSize+' / '+totalSize+' bytes)' );

		post = {
		'task'    : 's3import',
		'json'    : JSON.stringify({
		'access'    : $('#s3\\\\.access').val(),
		'secret'    : $('#s3\\\\.secret').val(),
		'bucket'    : $('#s3\\\\.bucket').val(),
		'file'        : akeeba_s3_filename,
		'part'        : part,
		'frag'        : frag,
		'totalSize'    : totalSize,
		'totalParts': totalParts,
		'doneSize'  : doneSize
		})
		};

		if((doneSize < totalSize) && (percent < 100)) {
		// More work to do
		doAjax(post, function(ret){
		ak_s3import_step(ret);
		});
		} else {
		// Done!
		$('#ak-s3-gui').hide('fast');
		$('#ak-s3-progress').hide('fast');
		$('#ak-s3-complete').show('fast');
		$('#ak-s3-error').hide('fast');
		}
		}

		function AKS3errorHandler(msg)
		{
		$('#ak-s3-gui').hide('fast');
		$('#ak-s3-progress').hide('fast');
		$('#ak-s3-complete').hide('fast');
		$('#ak-s3-error').show('fast');

		$('#ak-s3-errorMessage').html(msg);
		}
		
JS;

	}

	/**
	 * Echoes extra HTML on page 1 of Kickstart
	 */
	public function onPage1()
	{
		echo <<< HTML
		<div id="ak-s3-gui" style="display: none">
			<div class="step1">
				<div class="circle">1</div>
				<h2>AKS3_TITLE_STEP1</h2>
				<div class="area-container">
					<label for="s3.access">AKS3_ACCESS</label>
					<span class="field"><input type="text" id="s3.access" value=""/></span><br/>
					<label for="s3.secret">AKS3_SECRET</label>
					<span class="field"><input type="password" id="s3.secret" value=""/></span><br/>

					<div class="clr"></div>
					<a id="ak-s3-connect" class="button" onclick="onAKS3Connect()">AKS3_CONNECT</a>
					<a id="ak-s3-hidegui" class="button bluebutton">AKS3_CANCEL</a>
				</div>
			</div>
			<div class="clr"></div>

			<div class="step2">
				<div class="circle">2</div>
				<h2>AKS3_TITLE_STEP2</h2>
				<div class="area-container">
					<label for="s3.bucket">AKS3_BUCKET</label>
					<span class="field"><select id="s3.bucket"></select></span>
			<span id="ak-s3-bucketselect" style="display: none">
				<a class="button loprofile" onclick="onAKS3BucketChange()">AKS3_LISTCONTENTS</a>
			</span>
				</div>
			</div>
			<div class="clr"></div>

			<div class="step3">
				<div class="circle">3</div>
				<h2>AKS3_TITLE_STEP3</h2>
				<div class="area-container">
					<ul class="breadcrumbs" id="breadcrumbs" style="display: none">
						<li>
							<a>&lt; Root &gt;</a>
						</li>
					</ul>

					<div class="clr"></div>
					<fieldset id="ak-s3-folderlist-container" class="listpane">
						<legend>AKS3_FOLDERS</legend>
						<div class="folderlist fslist" id="ak-s3-folderlist" style="display: none">
						</div>
					</fieldset>
					<fieldset id="ak-s3-filelist-container" class="listpane">
						<legend>AKS3_FILES</legend>
						<div class="filelist fslist" id="ak-s3-filelist" style="display: none">
						</div>
					</fieldset>

				</div>
			</div>
			<div class="clr"></div>
		</div>

		<div id="ak-s3-progress" style="display: none">
			<div class="circle">4</div>
			<h2>AKS3_TITLE_STEP4</h2>
			<div class="area-container">
				<div id="ak-s3-importing">
					<div class="warn-not-close">AKS3_DO_NOT_CLOSE</div>
					<div id="ak-s3-progressbar" class="progressbar">
						<div id="ak-s3-progressbar-inner" class="progressbar-inner">&nbsp;</div>
					</div>
					<div id="ak-s3-progresstext"></div>
				</div>
			</div>
		</div>

		<div id="ak-s3-complete" style="display: none">
			<div class="circle">5</div>
			<h2>AKS3_TITLE_STEP5</h2>
			<div class="area-container">
				<div id="ak-s3-reload" class="button">AKS3_BTN_RELOAD</div>
			</div>
		</div>

		<div id="ak-s3-error" class="error" style="display: none;">
			<h3>ERROR_OCCURED</h3>
			<p id="ak-s3-errorMessage" class="errorMessage"></p>
			<div id="ak-s3-gotoStart" class="button">BTN_GOTOSTART</div>
		</div>

HTML;
	}

	/**
	 * Outputs HTML to be shown before Step 1's archive selection pane
	 */
	public function onPage1Step1()
	{
		?>
		<a id="ak-s3-showgui" class="button bluebutton loprofile">AKS3_IMPORT</a>
		<?php
	}

	public function s3connect($params)
	{
		$retArray = array(
			'status'  => true,
			'error'   => '',
			'buckets' => array()
		);

		try
		{
			debugMsg('Connecting to S3');
			debugMsg('  accessKey: ' . $params['access']);
			debugMsg('  secretKey: ' . $params['secret']);

			$s3Config = new AKS3Configuration($params['access'], $params['secret'], 'v4', 'us-east-1');
			$s3 = new AKS3Connector($s3Config);

			$retArray['buckets'] = $s3->listBuckets();
		}
		catch (Exception $e)
		{
			debugMsg('S3 connection error:');
			debugMsg($e->getMessage());
			$retArray['status'] = false;
			$retArray['error']  = $e->getMessage();
		}

		return $retArray;
	}

	public function s3list($params)
	{
		$retArray = array(
			'status'  => true,
			'error'   => '',
			'crumbs'  => array(),
			'folders' => array(),
			'files'   => array(),
		);

		try
		{
			$s3Config = new AKS3Configuration($params['access'], $params['secret'], 'v4', 'us-east-1');
			$region = $this->getBucketRegion($params, $s3Config);
			$s3Config->setRegion($region);
			$s3 = new AKS3Connector($s3Config);

			$bucket = $params['bucket'];

			$directory = rtrim($params['newdir'], '/');

			debugMsg('Preparing to list S3 bucket');
			debugMsg('  accessKey : ' . $params['access']);
			debugMsg('  secretKey : ' . $params['access']);
			debugMsg('  bucket    : ' . $params['bucket']);
			debugMsg('  region    : ' . $region);
			debugMsg('  directory : ' . $directory);

			list($files, $folders) = $this->listBucketContents($s3, $bucket, $directory);

			$retArray['crumbs']['&lt; Root &gt;'] = '/';

			if (!empty($directory))
			{
				$retArray['folders']['&lt; Up &gt;'] = '/';

				$folderParts = explode('/', $directory);
				$stack       = array();

				foreach ($folderParts as $fp)
				{
					$stack[]                 = $fp;
					$retArray['crumbs'][$fp] = implode('/', $stack);
				}

				$directory .= '/';

			}

			foreach ($folders as $f)
			{
				if (empty($f))
				{
					continue;
				}

				$retArray['folders'][$f] = $directory . $f;
			}

			foreach ($files as $f)
			{
				$filename = $f['filename'];

				if (!in_array(substr($filename, -4), array('.zip', '.jpa', '.jps')))
				{
					continue;
				}

				$retArray['files'][$filename] = $directory . $filename;
			}
		}
		catch (Exception $e)
		{
			$retArray['status'] = false;
			$retArray['error']  = $e->getMessage();
		}

		return $retArray;
	}

	public function s3import($params)
	{
		$this->params = $params;

		// Fetch data
		$accessKey      = $this->getParam('access');
		$secretKey      = $this->getParam('secret');
		$bucket         = $this->getParam('bucket');
		$remoteFilename = $this->getParam('file');
		$part           = $this->getParam('part', -1);
		$frag           = $this->getParam('frag', -1);
		$totalSize      = $this->getParam('totalSize', -1);
		$totalParts     = $this->getParam('totalParts', -1);
		$doneSize       = $this->getParam('doneSize', -1);

		debugMsg('Importing from S3');
		debugMsg('  accessKey : ' . $accessKey);
		debugMsg('  secretKey : ' . $secretKey);
		debugMsg('  bucket    : ' . $bucket);
		debugMsg('  file      : ' . $remoteFilename);
		debugMsg('  part      : ' . $part);
		debugMsg('  frag      : ' . $frag);
		debugMsg('  totalSize : ' . $totalSize);
		debugMsg('  totalParts: ' . $totalParts);
		debugMsg('  doneSize  : ' . $doneSize);

		// Init retArray
		$retArray = array(
			"status"     => true,
			"error"      => '',
			"part"       => $part,
			"frag"       => $frag,
			"totalSize"  => $totalSize,
			"totalParts" => $totalParts,
			"doneSize"   => $doneSize,
			"percent"    => 0,
		);

		try
		{
			$s3Config = new AKS3Configuration($accessKey, $secretKey, 'v4', 'us-east-1');
			$region = $this->getBucketRegion($params, $s3Config);
			$s3Config->setRegion($region);
			$s3 = new AKS3Connector($s3Config);

			debugMsg('  region  : ' . $region);

			$bucket = $params['bucket'];

			if (($totalParts < 0) || (($part < 0) && ($frag < 0)))
			{
				debugMsg('- Counting files');
				$filePrefix = substr($remoteFilename, 0, -3);
				$remoteExt  = pathinfo($remoteFilename, PATHINFO_EXTENSION);
				$allFiles   = $s3->getBucket($bucket, $filePrefix);
				$totalSize  = 0;
				$totalParts = 0;
				if (count($allFiles))
				{
					foreach ($allFiles as $name => $file)
					{
						$ext = pathinfo($name, PATHINFO_EXTENSION);

						// Make sure the first character of the extension matches
						if (substr($ext, 0, 1) != substr($remoteExt, 0, 1))
						{
							continue;
						}

						// Make sure that either the extension matches, or it's a number
						if (($ext != $remoteExt) && !is_numeric(substr($ext, 1)))
						{
							continue;
						}

						// Take into account
						debugMsg('- File with extension ' . $ext . ', size ' . $file['size']);

						$totalSize += $file['size'];
						$totalParts++;
					}
				}

				$doneSize               = 0;
				$retArray['totalParts'] = $totalParts;
				$retArray['totalSize']  = $totalSize;
				$retArray['doneSize']   = $doneSize;

				debugMsg('- Updating information:');
				debugMsg("    totalParts: " . $totalParts);
				debugMsg("    totalSize : " . $totalSize);
				debugMsg("    doneSize  : " . $doneSize);
			}

			AKFactory::set('kickstart.tuning.max_exec_time', '5');
			AKFactory::set('kickstart.tuning.run_time_bias', '75');

			$timer = new AKCoreTimer();
			$start = $timer->getRunningTime(); // Mark the start of this download
			$break = false; // Don't break the step

			while (($timer->getTimeLeft() > 0) && !$break && ($part < $totalParts))
			{
				// Get the remote and local filenames
				$basename  = basename($remoteFilename);
				$extension = strtolower(str_replace(".", "", strrchr($basename, ".")));

				debugMsg("- Setting up import for part $part");

				if ($part > 0)
				{
					$new_extension = substr($extension, 0, 1) . sprintf('%02u', $part);
					debugMsg("-- Original/new extension: $extension / $new_extension");
				}
				else
				{
					$new_extension = $extension;
					debugMsg("-- Keep original extension: $extension / $new_extension");
				}

				$filename = $basename . '.' . $new_extension;
				debugMsg("-- Filename is $filename");

				$remoteFilename = substr($remoteFilename, 0, -strlen($extension)) . $new_extension;
				debugMsg("-- Remote filename is $remoteFilename");

				// Figure out where on Earth to put that file
				$local_file = KSROOTDIR . '/' . basename($remoteFilename);

				debugMsg("- Importing from $remoteFilename");

				// Do we have to initialize the process?
				if ($part == -1)
				{
					debugMsg("-- First part, resetting doneSize and part");

					// Currently downloaded size
					$doneSize = 0;

					// Init
					$part = 0;
				}

				// Do we have to initialize the file?
				if ($frag == -1)
				{
					debugMsg("-- First frag in part $part, killing local file");
					// Delete and touch the output file

					@unlink($local_file);

					$fp = @fopen($local_file, 'wb');

					if ($fp !== false)
					{
						@fclose($fp);
					}

					// Init
					$frag = 0;
				}

				// Calculate from and length
				$length = 1048576;
				$from   = $frag * $length;
				$to     = $length + $from - 1;
				//if($from == 0) $from = 1;

				// Try to download the first frag
				$temp_file = $local_file . '.tmp';
				@unlink($temp_file);
				$required_time = 1.0;

				debugMsg("-- Importing part $part, frag $frag, byte position from/to: $from / $to");
				debugMsg("-- Temp file: $temp_file");

				try
				{
					$s3->getObject($bucket, $remoteFilename, $temp_file, $from, $to);
					$result = true;
				}
				catch (Exception $e)
				{
					$result = false;
				}

				if (!$result)
				{
					@unlink($temp_file);

					// Failed download
					if (
					(
						(($part < $totalParts) || (($totalParts == 1) && ($part == 0))) &&
						($frag == 0)
					)
					)
					{
						// Failure to download the part's beginning = failure to download. Period.
						$retArray['status'] = false;
						$retArray['error']  = 'Could not download the file';

						debugMsg("-- Download FAILED");

						return $retArray;
					}
					elseif ($part >= $totalParts)
					{
						// What?! We're already done here!
						debugMsg("-- We are already done.");

						$doneSize = $totalSize;
						$break    = true;

						continue;
					}
					else
					{
						// Since this is a staggered download, consider this normal and go to the next part.
						$part++;
						$frag = -1;

						if ($part >= $totalParts)
						{
							debugMsg("-- Import complete - there is no next part ($part >= $totalParts)");
							$doneSize = $totalSize;
							$break    = true;
							continue;
						}
						else
						{
							debugMsg("-- Part complete, moving to next part ($part)");
						}
					}
				}

				// Add the currently downloaded frag to the total size of downloaded files
				if ($result)
				{
					clearstatcache();
					$filesize = (int) @filesize($temp_file);

					debugMsg("-- Successful download of $filesize bytes");
					$doneSize += $filesize;

					// Append the file
					$fp = @fopen($local_file, 'ab');

					if ($fp === false)
					{
						debugMsg("-- Can't open local file for writing");

						// Can't open the file for writing
						@unlink($temp_file);
						$retArray['status'] = false;
						$retArray['error']  = 'Can\'t write to the local file';

						return false;
					}

					$tf = fopen($temp_file, 'rb');

					while (!feof($tf))
					{
						$data = fread($tf, 262144);
						fwrite($fp, $data);
					}

					fclose($tf);
					fclose($fp);
					@unlink($temp_file);
					debugMsg("-- Temporary file merged and removed");

					$frag++;
					debugMsg("-- Proceeding to next fragment, frag $frag");
				}

				// Advance the frag pointer and mark the end
				$end = $timer->getRunningTime();

				// Do we predict that we have enough time?
				$required_time = max(1.1 * ($end - $start), $required_time);

				if ($required_time > (10 - $end + $start))
				{
					$break = true;
				}

				$start = $end;
			}

			if ($doneSize <= 0)
			{
				$percent = 0;
			}
			else
			{
				$percent = 100 * ($doneSize / $totalSize);
			}

			// Update $retArray
			$retArray = array(
				"status"     => true,
				"error"      => '',
				"part"       => $part,
				"frag"       => $frag,
				"totalSize"  => $totalSize,
				"totalParts" => $totalParts,
				"doneSize"   => $doneSize,
				"percent"    => $percent,
			);
		}
		catch (Exception $e)
		{
			debugMsg("EXCEPTION RAISED:");
			debugMsg($e->getMessage());

			$retArray['status'] = false;
			$retArray['error']  = $e->getMessage();
		}

		return $retArray;
	}

	private function getParam($key, $default = null)
	{
		if (array_key_exists($key, $this->params))
		{
			return $this->params[$key];
		}
		else
		{
			return $default;
		}
	}

	public function onLoadTranslations()
	{
		$translation = AKText::getInstance();
		$translation->addDefaultLanguageStrings(array(
			'AKS3_IMPORT'       => "Import from Amazon S3",
			'AKS3_TITLE_STEP1'  => "Connect to Amazon S3",
			'AKS3_ACCESS'       => "Access Key",
			'AKS3_SECRET'       => "Secret Key",
			'AKS3_CONNECT'      => "Connect to Amazon S3",
			'AKS3_CANCEL'       => "Cancel import",
			'AKS3_TITLE_STEP2'  => "Select your Amazon S3 bucket",
			'AKS3_BUCKET'       => "Bucket",
			'AKS3_LISTCONTENTS' => "List contents",
			'AKS3_TITLE_STEP3'  => "Select archive to import",
			'AKS3_FOLDERS'      => "Folders",
			'AKS3_FILES'        => "Archive Files",
			'AKS3_TITLE_STEP4'  => "Importing...",
			'AKS3_DO_NOT_CLOSE' => "Please do not close this window while your backup archives are being imported",
			'AKS3_TITLE_STEP5'  => "Import is complete",
			'AKS3_BTN_RELOAD'   => "Reload Kickstart",
		));
	}

	/**
	 * Returns the region for the bucket
	 *
	 * @param   array  $params
	 *
	 * @return  string
	 */
	protected function getBucketRegion(&$params, AKS3Configuration $config)
	{
		$bucket = $params['bucket'];
		$bucketForRegion = $params['bucketForRegion'];
		$region = $params['region'];

		if (!empty($bucket) && (($bucketForRegion != $bucket) || empty($region)))
		{
			$config->setRegion('us-east-1');

			$s3 = new AKS3Connector($config);
			$region = $s3->getBucketLocation($bucket);

			$params['bucketForRegion'] = $bucket;
			$params['region'] = $region;
		}

		return $region;
	}

	/**
	 * Get the contents of a specific directory of a bucket separated into files and folders.
	 *
	 * @param   AKS3Connector  $s3
	 * @param   string         $bucket
	 * @param   string         $directory
	 *
	 * @return  array  [files, folders]
	 */
	public function listBucketContents(AKS3Connector $s3, $bucket, $directory)
	{
		$directory = trim($directory, '/') . '/';

		if ($directory == '/')
		{
		    $directory = '';
		}

		$everything = $s3->getBucket($bucket, $directory, null, null, '/', true);

		$files = array();
		$folders = array();
		$dirLength = strlen($directory);

		if (count($everything)) {
			foreach ($everything as $path => $info) {
				if (array_key_exists('size', $info) && (substr($path, -1) != '/')) {
					if (substr($path, 0, $dirLength) == $directory) {
						$path = substr($path, $dirLength);
					}
					$path = trim($path, '/');
					$files[] = array(
						'filename' => $path,
						'time' => $info['time'],
						'size' => $info['size']
					);
				}
			}

			foreach ($everything as $path => $info) {
				if (!array_key_exists('size', $info) && (substr($path, -1) == '/')) {
					if (substr($path, 0, $dirLength) == $directory) {
						$path = substr($path, $dirLength);
					}
					$path = trim($path, '/');
					$folders[] = $path;
				}
			}
		}

		return array($files, $folders);
	}
}

