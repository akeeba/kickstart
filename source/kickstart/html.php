<?php
/**
 * Akeeba Kickstart
 * A JSON-powered archive extraction tool
 *
 * @copyright   Copyright (c)2008-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL v2 or - at your option - any later version
 * @package     kickstart
 */

function echoTranslationStrings()
{
	callExtraFeature('onLoadTranslations');
	$translation = AKText::getInstance();
	echo $translation->asJavascript();
}

function echoPage()
{
	$edition         = KICKSTARTPRO ? 'Professional' : 'Core';
	$bestArchivePath = AKKickstartUtils::getBestArchivePath();
	$filelist        = AKKickstartUtils::getArchivesAsOptions($bestArchivePath);
	?>
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
		"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<title>Akeeba Kickstart <?php echo $edition ?> <?php echo VERSION ?></title>
		<style type="text/css" media="all" rel="stylesheet">
			<?php echoCSS();?>
		</style>
		<?php echoHeadJavascript(); ?>
	</head>
	<body>

	<div id="automode" style="display:none;">
		AUTOMODEON
	</div>

	<div id="fade" class="black_overlay"></div>

	<div id="page-container">

		<div id="preextraction" class="white_content">
			<div id="ie7Warning" style="display:none;">
				<h2>Deprecated Internet Explorer version</h2>
				<p>
					This script is not guaranteed to work properly on Internet Explorer 8
					or earlier versions, or on Internet Explorer 9 and higher running
					in compatibility mode.
				</p>
				<p>
					Please use Internet Explorer 9 or later in native mode (the
					&quot;broken page&quot; icon next to the address bar should not be
					enabled). Alternatively, you may use the latest versions of Firefox,
					Safari, Google Chrome or Opera.
				</p>
			</div>

			<h2>THINGS_HEADER</h2>
			<ol>
				<li>THINGS_01</li>
				<li>THINGS_02</li>
				<li>THINGS_03</li>
				<li>THINGS_04</li>
				<li>THINGS_05</li>
				<li>THINGS_06</li>
				<li>THINGS_07</li>
				<li>THINGS_08</li>
				<li>THINGS_09</li>
			</ol>
			<a href="javascript:void(0)" onclick="closeLightbox();">CLOSE_LIGHTBOX</a>
		</div>

		<div id="genericerror" class="white_content">
			<pre id="genericerrorInner"></pre>
		</div>

		<div id="header">
			<div class="title">
				<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABkAAAAZCAYAAADE6YVjAAAACXBIWXMAAAHFAAABxQG6eNsrAAAA/0lEQVRIx+WVUQ2DMBCGv01B5wAHYw4qARxMwiQgYQ6YAyRMAjgAB+CgezmSSwOjZfRplzSXNE2/3t1/V/hXuwMjkKcC5ICTNQImBaRVEAfUKdLkFlaWMorDo8lUHXIvsvEoyEMubFai21TaOQByVeqaFWVUPewRkEz5FqjEa+Aus3KpAfqVojsvjXYtdacVSA8MAgqRaSd+AMrYzt6zgmriv3zaeNS08MhNiD5UArcvoA64AE+1FySEYiF0I939Fj/3iFVFd6F9g1KU33yNGiX+JDYCbmIkXHm1sd6YX/pT7pKF3VYrSB87fc8RZyfgJX5I8WEVsfn+5fs1/LV9AHnjYQzAbyUrAAAAAElFTkSuQmCC" alt="Akeeba Kickstart logo">
				Akeeba Kickstart <?php echo $edition ?> ##VERSION##
			</div>
		</div>

		<div id="update-notification" style="display: none">
			<p class="update-notify">UPDATE_HEADER</p>
			<p class="update-whyupdate">UPDATE_NOTICE</p>
			<p class="update-links">
				<a href="#" id="update-dlnow">UPDATE_DLNOW</a>
				<a href="#" id="update-whatsnew" target="_blank">UPDATE_MOREINFO</a>
			</p>
		</div>

		<div id="page1">
			<?php callExtraFeature('onPage1'); ?>

			<div id="page1-content">

				<div class="helpme">
					<span>NEEDSOMEHELPKS</span> <a
						href="https://www.akeebabackup.com/documentation/quick-start-guide/using-kickstart.html"
						target="_blank">QUICKSTART</a>
				</div>

				<div class="step1">
					<div class="circle">1</div>
					<h2>SELECT_ARCHIVE</h2>
					<div class="area-container">
						<?php callExtraFeature('onPage1Step1'); ?>
						<div class="clr"></div>

						<label for="kickstart.setup.sourcepath">ARCHIVE_DIRECTORY</label>
			<span class="field">
				<input type="text" id="kickstart.setup.sourcepath"
				       value="<?php echo htmlentities($bestArchivePath); ?>"/>
				<span class="button" id="reloadArchives" style="margin-top:0;margin-bottom:0">RELOAD_ARCHIVES</span>
			</span>
						<br/>

						<label for="kickstart.setup.sourcefile">ARCHIVE_FILE</label>
			<span class="field" id="sourcefileContainer">
				<?php if (!empty($filelist)): ?>
					<select id="kickstart.setup.sourcefile">
						<?php echo $filelist; ?>
					</select>
				<?php else: ?>
					<a href="https://www.akeebabackup.com/documentation/troubleshooter/ksnoarchives.html"
					   target="_blank">NOARCHIVESCLICKHERE</a>
				<?php endif; ?>
			</span>
						<br/>
						<label for="kickstart.jps.password">JPS_PASSWORD</label>
						<span class="field"><input type="password" id="kickstart.jps.password" value=""/></span>
					</div>
                    <div class="area-container">
                        <label for="gobutton_top"></label>
                        <span id="gobutton_top" class="button" style="padding: 0.5em 2em; margin: 0;">BTN_START</span>
                    </div>
                </div>

				<div class="clr"></div>

				<div class="step2">
					<div class="circle">2</div>
					<h2>SELECT_EXTRACTION</h2>
					<div class="area-container">
						<label for="kickstart.procengine">WRITE_TO_FILES</label>
			<span class="field">
				<select id="kickstart.procengine">
					<option value="hybrid">WRITE_HYBRID</option>
					<option value="direct">WRITE_DIRECTLY</option>
					<option value="ftp">WRITE_FTP</option>
					<option value="sftp">WRITE_SFTP</option>
				</select>
			</span><br/>

						<label for="kickstart.setup.ignoreerrors">IGNORE_MOST_ERRORS</label>
						<span class="field"><input type="checkbox" id="kickstart.setup.ignoreerrors"/></span>

						<div id="ftp-options">
							<label for="kickstart.ftp.host">FTP_HOST</label>
							<span class="field"><input type="text" id="kickstart.ftp.host"
							                           value="localhost"/></span><br/>
							<label for="kickstart.ftp.port">FTP_PORT</label>
							<span class="field"><input type="text" id="kickstart.ftp.port" value="21"/></span><br/>
							<div id="ftp-ssl-passive">
								<label for="kickstart.ftp.ssl">FTP_FTPS</label>
								<span class="field"><input type="checkbox" id="kickstart.ftp.ssl"/></span><br/>
								<label for="kickstart.ftp.passive">FTP_PASSIVE</label>
								<span class="field"><input type="checkbox" id="kickstart.ftp.passive"
								                           checked="checked"/></span><br/>
							</div>
							<label for="kickstart.ftp.user">FTP_USER</label>
							<span class="field"><input type="text" id="kickstart.ftp.user" value=""/></span><br/>
							<label for="kickstart.ftp.pass">FTP_PASS</label>
							<span class="field"><input type="password" id="kickstart.ftp.pass" value=""/></span><br/>
							<label for="kickstart.ftp.dir">FTP_DIR</label>
				<span class="field">
                    <input type="text" id="kickstart.ftp.dir" value=""/>
                    <?php //<span class="button" id="browseFTP" style="margin-top:0;margin-bottom:0">FTP_BROWSE</span> ?>
                </span><br/>

							<label for="kickstart.ftp.tempdir">FTP_TEMPDIR</label>
				<span class="field">
					<input type="text" id="kickstart.ftp.tempdir"
					       value="<?php echo htmlentities(AKKickstartUtils::getTemporaryPath()) ?>"/>
					<span class="button" id="checkFTPTempDir">BTN_CHECK</span>
					<span class="button" id="resetFTPTempDir">BTN_RESET</span>
				</span><br/>
							<label></label>
							<span class="button" id="testFTP">BTN_TESTFTPCON</span>
							<a id="notWorking" class="button"
							   href="https://www.akeebabackup.com/documentation/troubleshooter/kscantextract.html"
							   target="_blank">CANTGETITTOWORK</a>
							<br/>
						</div>

					</div>
				</div>

				<div class="clr"></div>

				<div class="step3">
					<div class="circle">3</div>
					<h2>FINE_TUNE</h2>
					<div id="fine-tune-holder" class="area-container">
						<label for="kickstart.tuning.min_exec_time">MIN_EXEC_TIME</label>
						<span class="field"><input type="text" id="kickstart.tuning.min_exec_time" value="1"/></span>
						<span>SECONDS_PER_STEP</span><br/>
						<label for="kickstart.tuning.max_exec_time">MAX_EXEC_TIME</label>
						<span class="field"><input type="text" id="kickstart.tuning.max_exec_time" value="5"/></span>
						<span>SECONDS_PER_STEP</span><br/>
                        <div class="help">TIME_SETTINGS_HELP</div>

						<label for="kickstart.stealth.enable">STEALTH_MODE</label>
						<span class="field"><input type="checkbox" id="kickstart.stealth.enable"/></span><br/>
						<label for="kickstart.stealth.url">STEALTH_URL</label>
						<span class="field"><input type="text" id="kickstart.stealth.url" value=""/></span><br/>
                        <div class="help">STEALTH_MODE_HELP</div>

                        <?php if (defined('KICKSTARTPRO') && KICKSTARTPRO): ?>
                        <label for="kickstart.setup.zapbefore">ZAPBEFORE</label>
                        <span class="field"><input type="checkbox" id="kickstart.setup.zapbefore"/></span><br/>
                        <div class="help">ZAPBEFORE_HELP</div>
                        <?php endif; ?>

                        <label for="kickstart.setup.renameback">RENAME_FILES</label>
						<span class="field"><input type="checkbox" id="kickstart.setup.renameback"
						                           checked="checked"/></span><br/>
                        <div class="help">RENAME_FILES_HELP</div>

						<label for="kickstart.setup.restoreperms">RESTORE_PERMISSIONS</label>
						<span class="field"><input type="checkbox" id="kickstart.setup.restoreperms"/></span><br/>
                        <div class="help">RESTORE_PERMISSIONS_HELP</div>

                        <label for="kickstart.setup.extract_list">EXTRACT_LIST</label>
                        <span class="field"><textarea id="kickstart.setup.extract_list" rows="5" cols="50"></textarea></span><br/>
                        <div class="help">EXTRACT_LIST_HELP</div>
					</div>
				</div>

				<div class="clr"></div>

				<div class="step4">
					<div class="circle">4</div>
					<h2>EXTRACT_FILES</h2>
					<div class="area-container">
                        <label for="gobutton"></label>
						<span id="gobutton" class="button">BTN_START</span>
					</div>
                </div>

				<div class="clr"></div>

			</div>
		</div>

		<div id="page2">
			<div id="page2a">
				<div class="circle">5</div>
				<h2>EXTRACTING</h2>
				<div class="area-container">
					<div id="warn-not-close">DO_NOT_CLOSE_EXTRACT</div>
					<div id="progressbar">
						<div id="progressbar-inner">&nbsp;</div>
					</div>
					<div id="currentFile"></div>
				</div>
			</div>

			<div id="extractionComplete" style="display: none">
				<div class="circle">6</div>
				<h2>RESTACLEANUP</h2>
				<div id="runInstaller" class="button">BTN_RUNINSTALLER</div>
				<div id="runCleanup" class="button" style="display:none">BTN_CLEANUP</div>
				<div id="gotoSite" class="button" style="display:none">BTN_SITEFE</div>
				<div id="gotoAdministrator" class="button" style="display:none">BTN_SITEBE</div>
				<div id="gotoPostRestorationRroubleshooting" style="display:none">
					<a href="https://www.akeebabackup.com/documentation/troubleshooter/post-restoration.html"
					   target="_blank">POSTRESTORATIONTROUBLESHOOTING</a>
				</div>
			</div>

			<div id="warningsBox" style="display: none;">
				<div id="warningsHeader">
					<h2>WARNINGS</h2>
				</div>
				<div id="warningsContainer">
					<div id="warnings"></div>
				</div>
			</div>

			<div id="error" style="display: none;">
				<h3>ERROR_OCCURED</h3>
				<p id="errorMessage"></p>
				<div id="gotoStart" class="button">BTN_GOTOSTART</div>
				<div>
					<a href="https://www.akeebabackup.com/documentation/troubleshooter/kscantextract.html"
					   target="_blank">CANTGETITTOWORK</a>
				</div>
			</div>
		</div>

		<div id="footer">
			<div class="copyright">Copyright &copy; 2008&ndash;<?php echo date('Y'); ?> <a
					href="http://www.akeebabackup.com">Nicholas K.
					Dionysopoulos / Akeeba Backup</a>. All legal rights reserved.<br/>

				This program is free software: you can redistribute it and/or modify it under the terms of
				the <a href="http://www.gnu.org/gpl-3.html">GNU General
					Public License</a> as published by the Free Software Foundation, either version 3 of the License,
				or (at your option) any later version.<br/>
				Design credits: <a href="http://internet-inspired.com/">Internet Inspired</a>, heavily modified by
				AkeebaBackup.com
			</div>
		</div>

	</div>

	</body>
	</html>
	<?php
}
