<?php
/**
 * Akeeba Kickstart
 * A JSON-powered archive extraction tool
 *
 * @copyright   2010-2014 Nicholas K. Dionysopoulos / AkeebaBackup.com
 * @license     GNU GPL v2 or - at your option - any later version
 * @package     akeebabackup
 * @subpackage  kickstart
 */

function TranslateWinPath($p_path)
{
	$is_unc = false;

	if (KSWINDOWS)
	{
		// Is this a UNC path?
		$is_unc = (substr($p_path, 0, 2) == '\\\\') || (substr($p_path, 0, 2) == '//');
		// Change potential windows directory separator
		if ((strpos($p_path, '\\') > 0) || (substr($p_path, 0, 1) == '\\'))
		{
			$p_path = strtr($p_path, '\\', '/');
		}
	}

	// Remove multiple slashes
	$p_path = str_replace('///', '/', $p_path);
	$p_path = str_replace('//', '/', $p_path);

	// Fix UNC paths
	if ($is_unc)
	{
		$p_path = '//' . ltrim($p_path, '/');
	}

	return $p_path;
}

function echoCSS() {
	echo <<<CSS
html {
    background: #e9e9e9;
    font-size: 62.5%;
}
body {
	font-size: 14px;
    font-size: 1.4rem;
    font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
	text-rendering: optimizelegibility;
	background: transparent;
	color:#555;
	width:90%;
	max-width:980px;
	margin: 0 auto;
}

#page-container {
	position:relative;
	margin:5% 0;
	background: #f9f9f9;
	border: 1px solid #777;
	border: 1px solid rgba(0,0,0,.2);
	-webkit-box-shadow: 0px 0px 10px rgba(0,0,0,.1);
    -moz-box-shadow: 0px 0px 10px rgba(0,0,0,.1);
    box-shadow: 0px 0px 10px rgba(0,0,0,.1);
}

#header {
	color: #555;
	text-shadow: 0 1px #fff;
	background: #f2f5f6;
	background: -moz-linear-gradient(top, #f2f5f6 0%, #e3eaed 37%, #c8d7dc 100%);
	background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#f2f5f6), color-stop(37%,#e3eaed), color-stop(100%,#c8d7dc));
	background: -webkit-linear-gradient(top, #f2f5f6 0%,#e3eaed 37%,#c8d7dc 100%);
	background: -o-linear-gradient(top, #f2f5f6 0%,#e3eaed 37%,#c8d7dc 100%);
	background: -ms-linear-gradient(top, #f2f5f6 0%,#e3eaed 37%,#c8d7dc 100%);
	background: linear-gradient(top, #f2f5f6 0%,#e3eaed 37%,#c8d7dc 100%);
	-moz-background-clip: padding;
	-webkit-background-clip: padding-box;
	background-clip: padding-box;
	margin-bottom: 0.7em;
	border-bottom: 1px solid #ddd;
	border-bottom: 1px solid rgba(0,0,0,.2);
	padding:.25em;
    font-size: 32px;
    font-size: 3.2rem;
	line-height: 1.2;
	text-align: center;
}

#footer {
	font-size: 8pt;
	color: #233b53;
	text-align: center;
	border-top: 1px solid #ddd;
	border-top: 1px solid rgba(0,0,0,.05);
	padding: 1em 2em;
	background: #deefff;
	background: -moz-linear-gradient(top, #deefff 0%, #98bede 100%);
	background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#deefff), color-stop(100%,#98bede));
	background: -webkit-linear-gradient(top, #deefff 0%,#98bede 100%);
	background: -o-linear-gradient(top, #deefff 0%,#98bede 100%);
	background: -ms-linear-gradient(top, #deefff 0%,#98bede 100%);
	background: linear-gradient(top, #deefff 0%,#98bede 100%);
	clear: both;
}

#error, .error {
	display: none;
	border: solid #cc0000;
	border-width: 2px 0;
	background: rgb(255,255,136);
	background: -moz-linear-gradient(top, rgba(255,255,136,1) 0%, rgba(255,255,136,1) 100%);
	background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,rgba(255,255,136,1)), color-stop(100%,rgba(255,255,136,1)));
	background: -webkit-linear-gradient(top, rgba(255,255,136,1) 0%,rgba(255,255,136,1) 100%);
	background: -o-linear-gradient(top, rgba(255,255,136,1) 0%,rgba(255,255,136,1) 100%);
	background: -ms-linear-gradient(top, rgba(255,255,136,1) 0%,rgba(255,255,136,1) 100%);
	background: linear-gradient(top, rgba(255,255,136,1) 0%,rgba(255,255,136,1) 100%);
	color: #990000;
	padding:2em 2em 1em;
	margin-bottom: 1.15em;
	text-align:center;
	text-transform: uppercase;
}

#errorMessage, .errorMessage {
	text-transform: none;
}

#error h3, .error h3 {
	margin: 0;
	padding: 0;
	font-size: 12pt;
}

.clr {
	clear: both;
}

.circle {
	display: block;
	float: left;
	-moz-border-radius: 2em;
	-webkit-border-radius: 2em;
	border: 2px solid #e5e5e5;
	font-weight: bold;
	font-size: 18px;
    font-size: 1.8rem;
	line-height:1.5em;
	color: #fff;
	height: 1.5em;
	width: 1.5em;
    margin: 0.75em;
    text-align: center;
    background: rgb(35,83,138);
	background: -moz-linear-gradient(top, rgba(35,83,138,1) 0%, rgba(167,207,223,1) 100%);
	background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,rgba(35,83,138,1)), color-stop(100%,rgba(167,207,223,1)));
	background: -webkit-linear-gradient(top, rgba(35,83,138,1) 0%,rgba(167,207,223,1) 100%);
	background: -o-linear-gradient(top, rgba(35,83,138,1) 0%,rgba(167,207,223,1) 100%);
	background: -ms-linear-gradient(top, rgba(35,83,138,1) 0%,rgba(167,207,223,1) 100%);
	background: linear-gradient(top, rgba(35,83,138,1) 0%,rgba(167,207,223,1) 100%);
	-webkit-box-shadow: 0px 0px 2px rgba(0, 0, 0, 0.8) inset, 0px -1px 2px rgba(255,255,255,.9) inset,  0px 0px 1px rgba(0,0,0,.7); 0 -1px 1px rgba(0,0,0,.4);
    -moz-box-shadow: 0px 0px 2px rgba(0, 0, 0, 0.8) inset, 0px -1px 2px rgba(255,255,255,.9) inset,  0px 0px 1px rgba(0,0,0,.7); 0 -1px 1px rgba(0,0,0,.4);
    box-shadow: 0px 0px 2px rgba(0, 0, 0, 0.8) inset, 0px -1px 2px rgba(255,255,255,.9) inset,  0px 0px 1px rgba(0,0,0,.7); 0 -1px 1px rgba(0,0,0,.4);
}

.area-container {
	margin: 1em 4em;
}

#page2a .area-container {
	margin: 1em 0;
}

#runInstaller,
#runCleanup,
#gotoSite,
#gotoAdministrator,
#gotoPostRestorationRroubleshooting {
    margin: 0 2em 1.3em;
}

h2 {
	font-size: 24px;
    font-size: 2.4rem;
	font-weight: normal;
    line-height: 1.3;
	border: solid #ddd;
	text-shadow: 0px 1px #fff;
	border-top: 1px solid rgba(0,0,0,.05);
	border-bottom: 1px solid rgba(0,0,0,.2);
	border-left:none;
	border-right:none;
	padding: 0.5em 0;
    background: #f2f5f6;
	background: -moz-linear-gradient(top, #f2f5f6 0%, #e3eaed 37%, #c8d7dc 100%);
	background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#f2f5f6), color-stop(37%,#e3eaed), color-stop(100%,#c8d7dc));
	background: -webkit-linear-gradient(top, #f2f5f6 0%,#e3eaed 37%,#c8d7dc 100%);
	background: -o-linear-gradient(top, #f2f5f6 0%,#e3eaed 37%,#c8d7dc 100%);
	background: -ms-linear-gradient(top, #f2f5f6 0%,#e3eaed 37%,#c8d7dc 100%);
	background: linear-gradient(top, #f2f5f6 0%,#e3eaed 37%,#c8d7dc 100%);
}
#preextraction h2 {
	margin-top:0;
	border-top:0;
	text-align:center;
}

input,
select,
textarea {
    font-size : 100%;
    margin : 0;
    vertical-align : baseline;
    *vertical-align: middle;
}
button,
input {
    line-height : normal;
	font-weight:normal;
    *overflow: visible;
}
input,
select,
textarea {
	background:#fff;
	color:#777;
	font-size: 16px;
	font-size: 1.6rem;
	border:1px solid #d5d5d5;
    -webkit-border-radius: .25em;
    -moz-border-radius: .25em;
    border-radius: .25em;
	-webkit-box-sizing: border-box;
    -moz-box-sizing: border-box;
    box-sizing: border-box;
	width:50%;
	padding:0 0 0 .5em;
}
input[type="checkbox"] {
	width:auto;
}
.field {
	height:1.5em;
}
label {
	display:inline-block;
	width:30%;
	font-size: 85%;
    font-weight: normal;
	text-transform: uppercase;
    cursor : pointer;
	color: #777;
	margin:.5em 0;
}

input:focus, input:hover {
	background-color: #fffbb3;
}

.button {
	display: inline-block;
	margin: 1em .25em;
	text-transform: uppercase;
	padding: 1em 2em;
	background: #2cb12c;
	color:#fff;
	text-shadow: 0 1px 3px rgba(0, 0, 0, 0.2), 0 1px rgba(0, 0, 0, 0.4);
	-webkit-box-shadow: 0 1px 3px rgba(255, 255, 255, 0.5) inset, -1px 2px 2px rgba(0, 0, 0, 0.2);
    -moz-box-shadow: 0 1px 3px rgba(255, 255, 255, 0.5) inset, -1px 2px 2px rgba(0, 0, 0, 0.2);
    box-shadow: 0 1px 3px rgba(255, 255, 255, 0.5) inset, -1px 2px 2px rgba(0, 0, 0, 0.2);
	background: -moz-linear-gradient(top, #2cb12c 0%, #259625 100%);
	background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#2cb12c), color-stop(100%,#259625));
	background: -webkit-linear-gradient(top, #2cb12c 0%,#259625 100%);
	background: -o-linear-gradient(top, #2cb12c 0%,#259625 100%);
	background: -ms-linear-gradient(top, #2cb12c 0%,#259625 100%);
	background: linear-gradient(top, #2cb12c 0%,#259625 100%);
	border: solid #ddd;
	border: 1px solid rgba(0,0,0,.1);
	cursor: pointer;
	-webkit-border-radius: .25em;
	-moz-border-radius: .25em;
	border-radius: .25em;
	-webkit-transition: 0.3s linear all;
	-moz-transition: 0.3s linear all;
	-ms-transition: 0.3s linear all;
	-o-transition: 0.3s linear all;
  	transition: 0.3s linear all;
}
#checkFTPTempDir.button,
#resetFTPTempDir.button,
#testFTP.button,
#browseFTP,
#reloadArchives,
#notWorking.button {
	padding: .5em 1em;
	text-transform: none;
}

.button:hover {
	background: #259625;
	background: -moz-linear-gradient(top, #259625 0%, #2cb12c 100%);
	background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#259625), color-stop(100%,#2cb12c));
	background: -webkit-linear-gradient(top, #259625 0%,#2cb12c 100%);
	background: -o-linear-gradient(top, #259625 0%,#2cb12c 100%);
	background: -ms-linear-gradient(top, #259625 0%,#2cb12c 100%);
	background: linear-gradient(top, #259625 0%,#2cb12c 100%);
}
.button:active {
	background: #3c3;
	color: #444;
	text-shadow: 0 1px #fff;
	border: solid #ccc;
	border: 1px solid rgba(0,0,0,.3);
	-webkit-box-shadow: 0 1px 3px rgba(0,0,0, 0.5) inset;
    -moz-box-shadow: 0 1px 3px rgba(0,0,0, 0.5) inset;
    box-shadow: 0 1px 3px rgba(0,0,0, 0.5) inset;
}

#notWorking.button, .bluebutton {
	text-decoration: none;
	background: #7abcff;
	background: -moz-linear-gradient(top, #7abcff 0%, #60abf8 44%, #4096ee 100%);
	background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#7abcff), color-stop(44%,#60abf8), color-stop(100%,#4096ee));
	background: -webkit-linear-gradient(top, #7abcff 0%,#60abf8 44%,#4096ee 100%);
	background: -o-linear-gradient(top, #7abcff 0%,#60abf8 44%,#4096ee 100%);
	background: -ms-linear-gradient(top, #7abcff 0%,#60abf8 44%,#4096ee 100%);
	background: linear-gradient(top, #7abcff 0%,#60abf8 44%,#4096ee 100%);
}
#notWorking.button:hover, .bluebutton:hover {
	background: #4096ee;
	background: -moz-linear-gradient(top, #4096ee 0%, #60abf8 56%, #7abcff 100%);
	background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#4096ee), color-stop(56%,#60abf8), color-stop(100%,#7abcff));
	background: -webkit-linear-gradient(top, #4096ee 0%,#60abf8 56%,#7abcff 100%);
	background: -o-linear-gradient(top, #4096ee 0%,#60abf8 56%,#7abcff 100%);
	background: -ms-linear-gradient(top, #4096ee 0%,#60abf8 56%,#7abcff 100%);
	background: linear-gradient(top, #4096ee 0%,#60abf8 56%,#7abcff 100%);
}
#notWorking.button:active, .bluebutton:active {
	background: #7abcff;
}

.loprofile {
	padding: 0.5em 1em;
	font-size: 80%;
}

.black_overlay{
	display: none;
	position: absolute;
	top: 0%;
	left: 0%;
	width: 100%;
	height: 100%;
	background-color: black;
	z-index:1001;
	-moz-opacity: 0.8;
	opacity:.80;
	filter: alpha(opacity=80);
}

.white_content {
	display: none;
	position: absolute;
	padding: 0 0 1em;
	background: #fff;
	border: 1px solid #ddd;
	border: 1px solid rgba(0,0,0,.3);
	z-index:1002;
	overflow: hidden;
}
.white_content a{
	margin-left:4em;
}
ol {
	margin:0 2em;
	padding:0 2em 1em;
}
li {
	margin : 0 0 .5em;
}

#genericerror {
	background-color: #f0f000 !important;
	border: 4px solid #fcc !important;
}

#genericerrorInner {
	font-size: 110%;
	color: #33000;
}

#warn-not-close, .warn-not-close {
	padding: 0.2em 0.5em;
	text-align: center;
	background: #fcfc00;
	font-size: smaller;
	font-weight: bold;
}

#progressbar, .progressbar {
	display: block;
	width: 80%;
	height: 32px;
	border: 1px solid #ccc;
	margin: 1em 10% 0.2em;
	-moz-border-radius: .25em;
	-webkit-border-radius: .25em;
	border-radius: .25em;
}

#progressbar-inner, .progressbar-inner {
	display: block;
	width: 100%;
	height: 100%;
	background: #4096ee;
	background: -moz-linear-gradient(left, #4096ee 0%, #60abf8 56%, #7abcff 100%);
	background: -webkit-gradient(linear, left top, right top, color-stop(0%,#4096ee), color-stop(56%,#60abf8), color-stop(100%,#7abcff));
	background: -webkit-linear-gradient(left, #4096ee 0%,#60abf8 56%,#7abcff 100%);
	background: -o-linear-gradient(left, #4096ee 0%,#60abf8 56%,#7abcff 100%);
	background: -ms-linear-gradient(left, #4096ee 0%,#60abf8 56%,#7abcff 100%);
	background: linear-gradient(left, #4096ee 0%,#60abf8 56%,#7abcff 100%);
}

#currentFile {
	font-family: Consolas, "Courier New", Courier, monospace;
	font-size: 9pt;
	height: 10pt;
	overflow: hidden;
	text-overflow: ellipsis;
	background: #ccc;
	margin: 0 10% 1em;
	padding:.125em;
}

#extractionComplete {
}

#warningsContainer {
	border-bottom: 2px solid brown;
	border-left: 2px solid brown;
	border-right: 2px solid brown;
	padding: 5px 0;
	background: #ffffcc;
	-webkit-border-bottom-right-radius: 5px;
	-webkit-border-bottom-left-radius: 5px;
	-moz-border-radius-bottomleft: 5px;
	-moz-border-radius-bottomright: 5px;
}

#warningsHeader h2 {
	color: black;
	text-shadow: 2px 2px 5px #999999;
	border-top: 2px solid brown;
	border-left: 2px solid brown;
	border-right: 2px solid brown;
	border-bottom: thin solid brown;
	-webkit-border-top-right-radius: 5px;
	-webkit-border-top-left-radius: 5px;
	-moz-border-radius-topleft: 5px;
	-moz-border-radius-topright: 5px;
	background: yellow;
	font-size: large;
	padding: 2px 5px;
	margin: 0px;
}

#warnings {
	height: 200px;
	overflow-y: scroll;
}

#warnings div {
	background: #eeeeee;
	font-size: small;
	padding: 2px 4px;
	border-bottom: thin solid #333333;
}

#automode {
	display: inline-block;
	padding: 6pt 12pt;
	background-color: #cc0000;
	border: thick solid yellow;
	color: white;
	font-weight: bold;
	font-size: 125%;
	position: absolute;
	float: right;
	top: 1em;
	right: 1em;
}

.helpme,
#warn-not-close {
	background: rgb(255,255,136);
	background: -moz-linear-gradient(top, rgba(255,255,136,1) 0%, rgba(255,255,136,1) 100%);
	background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,rgba(255,255,136,1)), color-stop(100%,rgba(255,255,136,1)));
	background: -webkit-linear-gradient(top, rgba(255,255,136,1) 0%,rgba(255,255,136,1) 100%);
	background: -o-linear-gradient(top, rgba(255,255,136,1) 0%,rgba(255,255,136,1) 100%);
	background: -ms-linear-gradient(top, rgba(255,255,136,1) 0%,rgba(255,255,136,1) 100%);
	background: linear-gradient(top, rgba(255,255,136,1) 0%,rgba(255,255,136,1) 100%);
	padding: 0.75em 0.5em;
	border: solid #febf01;
	border-width: 1px 0;
	text-align: center;
}

#update-notification {
	margin: 1em;
	padding: 0.5em;
	background-color: #FF9;
	color: #F33;
	text-align: center;
	border-radius: 20px;
	border: medium solid red;
	box-shadow: 5px 5px 5px black;
}

.update-notify {
	font-size: 20pt;
	font-weight: bold;
}

.update-links {
	color: #333;
	font-size: 14pt;
}

#update-dlnow {
	text-decoration: none;
	color: #333;
	border: thin solid #333;
	padding: 0.5em;
	border-radius: 5px;
	background-color: #f0f0f0;
	text-shadow: 1px 1px 1px #999;
}

#update-dlnow:hover {
	background-color: #fff;
}

#update-whatsnew {
	font-size: 11pt;
	color: blue;
	text-decoration: underline;
}

.update-whyupdate {
	color: #333;
	font-size: 9pt;
}

/* FTP Browser */
.breadcrumb {background-color: #F5F5F5; border-radius: 4px; list-style: none outside none; margin: 0 0 18px; padding: 8px 15px;}
.breadcrumb > li {display: inline-block; text-shadow: 0 1px 0 #FFFFFF;}
#ak_crumbs span {padding: 1px 3px;}
#ak_crumbs a {cursor: pointer;}
#ftpBrowserFolderList a{cursor:pointer}

/* Bootstrap porting */
.table {margin-bottom: 18px;width: 100%;}
.table th, .table td {border-top: 1px solid #DDDDDD; line-height: 18px; padding: 8px; text-align: left; vertical-align: top;}
.table-striped tbody > tr:nth-child(2n+1) > td, .table-striped tbody > tr:nth-child(2n+1) > th { background-color: #F9F9F9;}

/* Layout helpers
----------------------------------*/
.ui-helper-hidden { display: none; }
.ui-helper-hidden-accessible { border: 0; clip: rect(0 0 0 0); height: 1px; margin: -1px; overflow: hidden; padding: 0; position: absolute; width: 1px; }
.ui-helper-reset { margin: 0; padding: 0; border: 0; outline: 0; line-height: 1.3; text-decoration: none; font-size: 100%; list-style: none; }
.ui-helper-clearfix:before, .ui-helper-clearfix:after { content: ""; display: table; }
.ui-helper-clearfix:after { clear: both; }
.ui-helper-clearfix { zoom: 1; }
.ui-helper-zfix { width: 100%; height: 100%; top: 0; left: 0; position: absolute; opacity: 0; filter:Alpha(Opacity=0); }


/* Interaction Cues
----------------------------------*/
.ui-state-disabled { cursor: default !important; }

/* Icons
----------------------------------*/

/* states and images */
.ui-icon { display: block; text-indent: -99999px; overflow: hidden; background-repeat: no-repeat; }

/* Misc visuals
----------------------------------*/

/* Overlays */
.ui-widget-overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; }
.ui-resizable { position: relative;}
.ui-resizable-handle { position: absolute;font-size: 0.1px; display: block; }
.ui-resizable-disabled .ui-resizable-handle, .ui-resizable-autohide .ui-resizable-handle { display: none; }
.ui-resizable-n { cursor: n-resize; height: 7px; width: 100%; top: -5px; left: 0; }
.ui-resizable-s { cursor: s-resize; height: 7px; width: 100%; bottom: -5px; left: 0; }
.ui-resizable-e { cursor: e-resize; width: 7px; right: -5px; top: 0; height: 100%; }
.ui-resizable-w { cursor: w-resize; width: 7px; left: -5px; top: 0; height: 100%; }
.ui-resizable-se { cursor: se-resize; width: 12px; height: 12px; right: 1px; bottom: 1px; }
.ui-resizable-sw { cursor: sw-resize; width: 9px; height: 9px; left: -5px; bottom: -5px; }
.ui-resizable-nw { cursor: nw-resize; width: 9px; height: 9px; left: -5px; top: -5px; }
.ui-resizable-ne { cursor: ne-resize; width: 9px; height: 9px; right: -5px; top: -5px;}
.ui-button { display: inline-block; position: relative; padding: 0; margin-right: .1em; cursor: pointer; text-align: center; zoom: 1; overflow: visible; } /* the overflow property removes extra width in IE */
.ui-button, .ui-button:link, .ui-button:visited, .ui-button:hover, .ui-button:active { text-decoration: none; }
.ui-button-icon-only { width: 2.2em; } /* to make room for the icon, a width needs to be set here */
button.ui-button-icon-only { width: 2.4em; } /* button elements seem to need a little more width */
.ui-button-icons-only { width: 3.4em; }
button.ui-button-icons-only { width: 3.7em; }

/*button text element */
.ui-button .ui-button-text { display: block; line-height: 1.4;  }
.ui-button-text-only .ui-button-text { padding: 0; }
.ui-button-icon-only .ui-button-text, .ui-button-icons-only .ui-button-text { padding: .4em; text-indent: -9999999px; }
.ui-button-text-icon-primary .ui-button-text, .ui-button-text-icons .ui-button-text { padding: .4em 1em .4em 2.1em; }
.ui-button-text-icon-secondary .ui-button-text, .ui-button-text-icons .ui-button-text { padding: .4em 2.1em .4em 1em; }
.ui-button-text-icons .ui-button-text { padding-left: 2.1em; padding-right: 2.1em; }
/* no icon support for input elements, provide padding by default */
input.ui-button { padding: .4em 1em; }

/*button icon element(s) */
.ui-button-icon-only .ui-icon, .ui-button-text-icon-primary .ui-icon, .ui-button-text-icon-secondary .ui-icon, .ui-button-text-icons .ui-icon, .ui-button-icons-only .ui-icon { position: absolute; top: 50%; margin-top: -8px; }
.ui-button-icon-only .ui-icon { left: 50%; margin-left: -8px; }
.ui-button-text-icon-primary .ui-button-icon-primary, .ui-button-text-icons .ui-button-icon-primary, .ui-button-icons-only .ui-button-icon-primary { left: .5em; }
.ui-button-text-icon-secondary .ui-button-icon-secondary, .ui-button-text-icons .ui-button-icon-secondary, .ui-button-icons-only .ui-button-icon-secondary { right: .5em; }
.ui-button-text-icons .ui-button-icon-secondary, .ui-button-icons-only .ui-button-icon-secondary { right: .5em; }

/*button sets*/
.ui-buttonset { margin-right: 7px; }
.ui-buttonset .ui-button { margin-left: 0; margin-right: -.3em; }

/* workarounds */
button.ui-button::-moz-focus-inner { border: 0; padding: 0; } /* reset extra padding in Firefox */
.ui-dialog { position: absolute; top: 0; left: 0; padding: .2em; width: 300px; overflow: hidden; }
.ui-dialog .ui-dialog-titlebar { padding: .4em 1em; position: relative;  }
.ui-dialog .ui-dialog-title { float: left; margin: .1em 16px .1em 0; }
.ui-dialog .ui-dialog-titlebar-close { position: absolute; right: .3em; top: 50%; width: 19px; margin: -10px 0 0 0; padding: 1px; height: 18px; display:none}
.ui-dialog .ui-dialog-titlebar-close span { display: none; margin: 1px; }
.ui-dialog .ui-dialog-titlebar-close:hover, .ui-dialog .ui-dialog-titlebar-close:focus { padding: 0; }
.ui-dialog .ui-dialog-content { position: relative; border: 0; padding: .5em 1em; background: none; overflow: auto; zoom: 1; }
.ui-dialog .ui-dialog-buttonpane { text-align: left; border-width: 1px 0 0 0; background-image: none; margin: .5em 0 0 0; padding: .3em 1em .5em .4em; }
.ui-dialog .ui-dialog-buttonpane .ui-dialog-buttonset { float: right; }
.ui-dialog .ui-dialog-buttonpane button { margin: .5em .4em .5em 0; cursor: pointer; }
.ui-dialog .ui-resizable-se { width: 14px; height: 14px; right: 3px; bottom: 3px; }
.ui-draggable .ui-dialog-titlebar { cursor: move; }

/* Component containers
----------------------------------*/
.ui-widget-content { border: 1px solid #a6c9e2; background: #fcfdfd; color: #222222; }
.ui-widget-content a { color: #222222; }
.ui-widget-header { border: 1px solid #4297d7; background: #5c9ccc ; color: #ffffff; font-weight: bold; }
.ui-widget-header a { color: #ffffff; }

/* Interaction states
----------------------------------*/
.ui-state-default a, .ui-state-default a:link, .ui-state-default a:visited {text-decoration: none; }
.ui-state-hover, .ui-widget-content .ui-state-hover, .ui-widget-header .ui-state-hover{
    background: #4096ee;
	background: -moz-linear-gradient(top, #4096ee 0%, #60abf8 56%, #7abcff 100%);
	background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#4096ee), color-stop(56%,#60abf8), color-stop(100%,#7abcff));
	background: -webkit-linear-gradient(top, #4096ee 0%,#60abf8 56%,#7abcff 100%);
	background: -o-linear-gradient(top, #4096ee 0%,#60abf8 56%,#7abcff 100%);
	background: -ms-linear-gradient(top, #4096ee 0%,#60abf8 56%,#7abcff 100%);
	background: linear-gradient(top, #4096ee 0%,#60abf8 56%,#7abcff 100%);
}
.ui-state-hover a, .ui-state-hover a:hover, .ui-state-hover a:link, .ui-state-hover a:visited { color: #1d5987; text-decoration: none; }
.ui-state-active a, .ui-state-active a:link, .ui-state-active a:visited { color: #e17009; text-decoration: none; }

/* Interaction Cues
----------------------------------*/
.ui-state-highlight, .ui-widget-content .ui-state-highlight, .ui-widget-header .ui-state-highlight  {border: 1px solid #fad42e; background: #fbec88 ; color: #363636; }
.ui-state-highlight a, .ui-widget-content .ui-state-highlight a,.ui-widget-header .ui-state-highlight a { color: #363636; }
.ui-state-error, .ui-widget-content .ui-state-error, .ui-widget-header .ui-state-error {border: 1px solid #cd0a0a; background: #fef1ec ; color: #cd0a0a; }
.ui-state-error a, .ui-widget-content .ui-state-error a, .ui-widget-header .ui-state-error a { color: #cd0a0a; }
.ui-state-error-text, .ui-widget-content .ui-state-error-text, .ui-widget-header .ui-state-error-text { color: #cd0a0a; }
.ui-priority-primary, .ui-widget-content .ui-priority-primary, .ui-widget-header .ui-priority-primary { font-weight: bold; }
.ui-priority-secondary, .ui-widget-content .ui-priority-secondary,  .ui-widget-header .ui-priority-secondary { opacity: .7; filter:Alpha(Opacity=70); font-weight: normal; }
.ui-state-disabled, .ui-widget-content .ui-state-disabled, .ui-widget-header .ui-state-disabled { opacity: .35; filter:Alpha(Opacity=35); background-image: none; }
.ui-state-disabled .ui-icon { filter:Alpha(Opacity=35); } /* For IE8 - See #6059 */

/* Icons
----------------------------------*/

/* states and images */
.ui-icon { display:none}

/* Misc visuals
----------------------------------*/

/* Corner radius */
.ui-corner-all, .ui-corner-top, .ui-corner-left, .ui-corner-tl { -moz-border-radius-topleft: 5px; -webkit-border-top-left-radius: 5px; -khtml-border-top-left-radius: 5px; border-top-left-radius: 5px; }
.ui-corner-all, .ui-corner-top, .ui-corner-right, .ui-corner-tr { -moz-border-radius-topright: 5px; -webkit-border-top-right-radius: 5px; -khtml-border-top-right-radius: 5px; border-top-right-radius: 5px; }
.ui-corner-all, .ui-corner-bottom, .ui-corner-left, .ui-corner-bl { -moz-border-radius-bottomleft: 5px; -webkit-border-bottom-left-radius: 5px; -khtml-border-bottom-left-radius: 5px; border-bottom-left-radius: 5px; }
.ui-corner-all, .ui-corner-bottom, .ui-corner-right, .ui-corner-br { -moz-border-radius-bottomright: 5px; -webkit-border-bottom-right-radius: 5px; -khtml-border-bottom-right-radius: 5px; border-bottom-right-radius: 5px; }

/* Overlays */
.ui-widget-overlay { background: #000000 ; opacity: .8;filter:Alpha(Opacity=80); }
.ui-widget-shadow { margin: -8px 0 0 -8px; padding: 8px; background: #000000 ; opacity: .8;filter:Alpha(Opacity=80); -moz-border-radius: 8px; -khtml-border-radius: 8px; -webkit-border-radius: 8px; border-radius: 8px; }


.ui-button {
    font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
    font-size: 1.4rem;
	display: inline-block;
	padding: .5em 1em;
	margin: 1em .25em;
	color:#fff;
	text-shadow: 0 1px 3px rgba(0, 0, 0, 0.2), 0 1px rgba(0, 0, 0, 0.4);
	-webkit-box-shadow: 0 1px 3px rgba(255, 255, 255, 0.5) inset, -1px 2px 2px rgba(0, 0, 0, 0.2);
    -moz-box-shadow: 0 1px 3px rgba(255, 255, 255, 0.5) inset, -1px 2px 2px rgba(0, 0, 0, 0.2);
    box-shadow: 0 1px 3px rgba(255, 255, 255, 0.5) inset, -1px 2px 2px rgba(0, 0, 0, 0.2);
    text-decoration: none;
	background: #7abcff;
	background: -moz-linear-gradient(top, #7abcff 0%, #60abf8 44%, #4096ee 100%);
	background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#7abcff), color-stop(44%,#60abf8), color-stop(100%,#4096ee));
	background: -webkit-linear-gradient(top, #7abcff 0%,#60abf8 44%,#4096ee 100%);
	background: -o-linear-gradient(top, #7abcff 0%,#60abf8 44%,#4096ee 100%);
	background: -ms-linear-gradient(top, #7abcff 0%,#60abf8 44%,#4096ee 100%);
	background: linear-gradient(top, #7abcff 0%,#60abf8 44%,#4096ee 100%);
	border: solid #ddd;
	border: 1px solid rgba(0,0,0,.1);
	cursor: pointer;
	-webkit-border-radius: .25em;
	-moz-border-radius: .25em;
	border-radius: .25em;
	-webkit-transition: 0.3s linear all;
	-moz-transition: 0.3s linear all;
	-ms-transition: 0.3s linear all;
	-o-transition: 0.3s linear all;
  	transition: 0.3s linear all;
}

CSS;

	callExtraFeature('onExtraHeadCSS');
}

function echoTranslationStrings()
{
	callExtraFeature('onLoadTranslations');
	$translation = AKText::getInstance();
	echo $translation->asJavascript();
}

function echoPage()
{
	$edition = KICKSTARTPRO ? 'Professional' : 'Core';
	$bestArchivePath = AKKickstartUtils::getBestArchivePath();
	$filelist = AKKickstartUtils::getArchivesAsOptions($bestArchivePath);
	?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Akeeba Kickstart <?php echo $edition?> <?php echo VERSION?></title>
<style type="text/css" media="all" rel="stylesheet">
<?php echoCSS();?>
</style>
<?php if(@file_exists('jquery.min.js')):?>
<script type="text/javascript" src="jquery.min.js"></script>
<?php else: ?>
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<?php endif; ?>
<?php if(@file_exists('json2.min.js')):?>
<script type="text/javascript" src="json2.min.js"></script>
<?php else: ?>
<script type="text/javascript" src="//yandex.st/json2/2011-10-19/json2.min.js"></script>
<?php endif; ?>
<?php if(@file_exists('jquery-ui.min.js')):?>
<script type="text/javascript" src="jquery-ui.min.js"></script>
<?php else: ?>
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js"></script>
<?php endif; ?>
<script type="text/javascript" language="javascript">
	var akeeba_debug = <?php echo defined('KSDEBUG') ? 'true' : 'false' ?>;
    var sftp_path = '<?php echo TranslateWinPath(defined('KSROOTDIR') ? KSROOTDIR : __DIR__); ?>/';
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
				message = message + 'XHR ReadyState: ' + Response.readyState + '<br/>';
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
							akeeba_ftpbrowser_create_link(akeeba_ftpbrowser_directory+'/'+item, item, $('#ftpBrowserFolderList'), callback );
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
                            akeeba_sftpbrowser_create_link(akeeba_sftpbrowser_directory+'/'+item, item, $('#ftpBrowserFolderList'), callback );
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
			or previous version, or on Internet Explorer 9 and higher running
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
		<div class="title">Akeeba Kickstart <?php echo $edition?> ##VERSION##</div>
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
		<span>NEEDSOMEHELPKS</span> <a href="https://www.akeebabackup.com/documentation/quick-start-guide/using-kickstart.html" target="_blank">QUICKSTART</a>
	</div>

	<div class="step1">
		<div class="circle">1</div>
		<h2>SELECT_ARCHIVE</h2>
		<div class="area-container">
			<?php callExtraFeature('onPage1Step1'); ?>
			<div class="clr"></div>

			<label for="kickstart.setup.sourcepath">ARCHIVE_DIRECTORY</label>
			<span class="field">
				<input type="text" id="kickstart.setup.sourcepath" value="<?php echo htmlentities($bestArchivePath); ?>" />
				<span class="button" id="reloadArchives" style="margin-top:0;margin-bottom:0">RELOAD_ARCHIVES</span>
			</span>
			<br/>

			<label for="kickstart.setup.sourcefile">ARCHIVE_FILE</label>
			<span class="field" id="sourcefileContainer">
				<?php if(!empty($filelist)):?>
					<select id="kickstart.setup.sourcefile">
					<?php echo $filelist; ?>
					</select>
				<?php else:?>
					<a href="https://www.akeebabackup.com/documentation/troubleshooter/ksnoarchives.html" target="_blank">NOARCHIVESCLICKHERE</a>
				<?php endif;?>
			</span>
			<br />
			<label for="kickstart.jps.password">JPS_PASSWORD</label>
			<span class="field"><input type="password" id="kickstart.jps.password" value="" /></span>
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
			<span class="field"><input type="checkbox" id="kickstart.setup.ignoreerrors" /></span>

			<div id="ftp-options">
				<label for="kickstart.ftp.host">FTP_HOST</label>
				<span class="field"><input type="text" id="kickstart.ftp.host" value="localhost" /></span><br />
				<label for="kickstart.ftp.port">FTP_PORT</label>
				<span class="field"><input type="text" id="kickstart.ftp.port" value="21" /></span><br />
                <div id="ftp-ssl-passive">
                    <label for="kickstart.ftp.ssl">FTP_FTPS</label>
                    <span class="field"><input type="checkbox" id="kickstart.ftp.ssl" /></span><br />
                    <label for="kickstart.ftp.passive">FTP_PASSIVE</label>
                    <span class="field"><input type="checkbox" id="kickstart.ftp.passive" checked="checked" /></span><br />
                </div>
				<label for="kickstart.ftp.user">FTP_USER</label>
				<span class="field"><input type="text" id="kickstart.ftp.user" value="" /></span><br />
				<label for="kickstart.ftp.pass">FTP_PASS</label>
				<span	class="field"><input type="password" id="kickstart.ftp.pass" value="" /></span><br />
				<label for="kickstart.ftp.dir">FTP_DIR</label>
				<span class="field">
                    <input type="text" id="kickstart.ftp.dir" value="" />
                    <span class="button" id="browseFTP" style="margin-top:0;margin-bottom:0">FTP_BROWSE</span>
                </span><br />

				<label for="kickstart.ftp.tempdir">FTP_TEMPDIR</label>
				<span class="field">
					<input type="text" id="kickstart.ftp.tempdir" value="<?php echo htmlentities(AKKickstartUtils::getPath()) ?>" />
					<span class="button" id="checkFTPTempDir">BTN_CHECK</span>
					<span class="button" id="resetFTPTempDir">BTN_RESET</span>
				</span><br />
				<label></label>
				<span class="button" id="testFTP">BTN_TESTFTPCON</span>
				<a id="notWorking" class="button" href="https://www.akeebabackup.com/documentation/troubleshooter/kscantextract.html" target="_blank">CANTGETITTOWORK</a>
				<br />
			</div>

		</div>
	</div>

	<div class="clr"></div>

	<div class="step3">
		<div class="circle">3</div>
		<h2>FINE_TUNE</h2>
		<div class="area-container">
			<label for="kickstart.tuning.min_exec_time">MIN_EXEC_TIME</label>
			<span class="field"><input type="text" id="kickstart.tuning.min_exec_time" value="1" /></span> <span>SECONDS_PER_STEP</span><br />
			<label for="kickstart.tuning.max_exec_time">MAX_EXEC_TIME</label>
			<span class="field"><input type="text" id="kickstart.tuning.max_exec_time" value="5" /></span> <span>SECONDS_PER_STEP</span><br />

			<label for="kickstart.stealth.enable">STEALTH_MODE</label>
			<span class="field"><input type="checkbox" id="kickstart.stealth.enable" /></span><br />
			<label for="kickstart.stealth.url">STEALTH_URL</label>
			<span class="field"><input type="text" id="kickstart.stealth.url" value="" /></span><br />
		</div>
	</div>

	<div class="clr"></div>

	<div class="step4">
		<div class="circle">4</div>
		<h2>EXTRACT_FILES</h2>
		<div class="area-container">
			<span></span>
			<span id="gobutton" class="button">BTN_START</span>
		</div>
	</div>

	<div class="clr"></div>

	</div>

	<div id="ftpdialog" style="display:none;">
		<p class="instructions alert alert-info">FTPBROWSER_LBL_INSTRUCTIONS</p>
		<div class="error alert alert-error" id="ftpBrowserErrorContainer">
			<h3>FTPBROWSER_LBL_ERROR</h3>
			<p id="ftpBrowserError"></p>
		</div>
		<ul id="ak_crumbs" class="breadcrumb"></ul>
		<div class="row-fluid">
			<div class="span12">
				<table id="ftpBrowserFolderList" class="table table-striped">
				</table>
			</div>
		</div>
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
			<a href="https://www.akeebabackup.com/documentation/troubleshooter/post-restoration.html" target="_blank">POSTRESTORATIONTROUBLESHOOTING</a>
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
			<a href="https://www.akeebabackup.com/documentation/troubleshooter/kscantextract.html" target="_blank">CANTGETITTOWORK</a>
		</div>
	</div>
</div>

<div id="footer">
	<div class="copyright">Copyright &copy; 2008&ndash;2013 <a	href="http://www.akeebabackup.com">Nicholas K.
		Dionysopoulos / Akeeba Backup</a>. All legal rights reserved.<br />

		This program is free software: you can redistribute it and/or modify it under the terms of
		the <a href="http://www.gnu.org/gpl-3.htmlhttp://www.gnu.org/copyleft/gpl.html">GNU General
		Public License</a> as published by the Free Software Foundation, either version 3 of the License,
		or (at your option) any later version.<br />
		Design credits: <a href="http://internet-inspired.com/">Internet Inspired</a>, slightly modified by AkeebaBackup.com
	</div>
</div>

</div>

</body>
</html>
	<?php
}

function createStealthURL()
{
	$filename = AKFactory::get('kickstart.stealth.url', '');
	// We need an HTML file!
	if(empty($filename)) return;
	// Make sure it ends in .html or .htm
	$filename = basename($filename);
	if( (strtolower(substr($filename,-5)) != '.html') && (strtolower(substr($filename,-4)) != '.htm') ) return;

	$filename_quoted = str_replace('.','\\.',$filename);
	$rewrite_base = trim(dirname(AKFactory::get('kickstart.stealth.url', '')),'/');

	// Get the IP
	$userIP = $_SERVER['REMOTE_ADDR'];
	$userIP = str_replace('.', '\.', $userIP);

	// Get the .htaccess contents
	$stealthHtaccess = <<<ENDHTACCESS
RewriteEngine On
RewriteBase /$rewrite_base
RewriteCond %{REMOTE_HOST}		!$userIP
RewriteCond %{REQUEST_URI}		!$filename_quoted
RewriteCond %{REQUEST_URI}		!(\.png|\.jpg|\.gif|\.jpeg|\.bmp|\.swf|\.css|\.js)$
RewriteRule (.*)				$filename	[R=307,L]

ENDHTACCESS;

	// Write the new .htaccess, removing the old one first
	$postproc = AKFactory::getpostProc();
	$postproc->unlink('.htaccess');
	$tempfile = $postproc->processFilename('.htaccess');
	@file_put_contents($tempfile, $stealthHtaccess);
	$postproc->process();
}

// Register additional feature classes
callExtraFeature();

$retArray = array(
	'status'	=> true,
	'message'	=> null
);

$task = getQueryParam('task', 'display');
$json = getQueryParam('json');
$ajax = true;

switch($task)
{
	case 'checkTempdir':
		$retArray['status'] = false;
		if(!empty($json))
		{
			$data = json_decode($json, true);
			$dir = @$data['kickstart.ftp.tempdir'];
			if(!empty($dir))
			{
				$retArray['status'] = is_writable($dir);
			}
		}
		break;

	case 'checkFTP':
		$retArray['status'] = false;
		if(!empty($json))
		{
			$data = json_decode($json, true);
			foreach($data as $key => $value)
			{
				AKFactory::set($key, $value);
			}

            if($data['type'] == 'ftp')
            {
                $ftp = new AKPostprocFTP();
            }
            else
            {
                $ftp = new AKPostprocSFTP();
            }

			$retArray['message'] = $ftp->getError();
			$retArray['status'] = empty($retArray['message']);
		}
		break;

    case 'ftpbrowse':
        if(!empty($json))
        {
            $data = json_decode($json, true);

            $retArray = getListing($data['directory'], $data['host'], $data['port'], $data['username'], $data['password'], $data['passive'], $data['ssl']);
        }
        break;

    case 'sftpbrowse':
        if(!empty($json))
        {
            $data = json_decode($json, true);

            $retArray = getSftpListing($data['directory'], $data['host'], $data['port'], $data['username'], $data['password']);
        }
        break;

	case 'startExtracting':
	case 'continueExtracting':
		// Look for configuration values
		$retArray['status'] = false;
		if(!empty($json))
		{
			if($task == 'startExtracting') AKFactory::nuke();

			$oldJSON = $json;
			$json = json_decode($json, true);
			if(is_null($json)) {
				$json = stripslashes($oldJSON);
				$json = json_decode($json, true);
			}
			if(!empty($json)) foreach($json as $key => $value)
			{
				if( substr($key,0,9) == 'kickstart' ) {
					AKFactory::set($key, $value);
				}
			}

			// A "factory" variable will override all other settings.
			if( array_key_exists('factory', $json) )
			{
				// Get the serialized factory
				$serialized = $json['factory'];
				AKFactory::unserialize($serialized);
				AKFactory::set('kickstart.enabled', true);
			}

			// Make sure that the destination directory is always set (req'd by both FTP and Direct Writes modes)
			$removePath = AKFactory::get('kickstart.setup.destdir','');
			if(empty($removePath)) AKFactory::set('kickstart.setup.destdir', AKKickstartUtils::getPath());

			if($task=='startExtracting')
			{
				// If the Stealth Mode is enabled, create the .htaccess file
				if( AKFactory::get('kickstart.stealth.enable', false) )
				{
					createStealthURL();
				}
			}

			$engine = AKFactory::getUnarchiver(); // Get the engine
			$observer = new ExtractionObserver(); // Create a new observer
			$engine->attach($observer); // Attach the observer
			$engine->tick();
			$ret = $engine->getStatusArray();

			if( $ret['Error'] != '' )
			{
				$retArray['status'] = false;
				$retArray['done'] = true;
				$retArray['message'] = $ret['Error'];
			}
			elseif( !$ret['HasRun'] )
			{
				$retArray['files'] = $observer->filesProcessed;
				$retArray['bytesIn'] = $observer->compressedTotal;
				$retArray['bytesOut'] = $observer->uncompressedTotal;
				$retArray['status'] = true;
				$retArray['done'] = true;
			}
			else
			{
				$retArray['files'] = $observer->filesProcessed;
				$retArray['bytesIn'] = $observer->compressedTotal;
				$retArray['bytesOut'] = $observer->uncompressedTotal;
				$retArray['status'] = true;
				$retArray['done'] = false;
				$retArray['factory'] = AKFactory::serialize();
			}

			if(!is_null($observer->totalSize))
			{
				$retArray['totalsize'] = $observer->totalSize;
				$retArray['filelist'] = $observer->fileList;
			}

			$retArray['Warnings'] = $ret['Warnings'];
			$retArray['lastfile'] = $observer->lastFile;
		}
		break;

	case 'cleanUp':
		if(!empty($json))
		{
			$json = json_decode($json, true);
			if( array_key_exists('factory', $json) )
			{
				// Get the serialized factory
				$serialized = $json['factory'];
				AKFactory::unserialize($serialized);
				AKFactory::set('kickstart.enabled', true);
			}
		}

		$unarchiver = AKFactory::getUnarchiver(); // Get the engine
		$engine = AKFactory::getPostProc();

		// 1. Remove installation
		recursive_remove_directory('installation');

		// 2. Run the renames, backwards
		$renames = $unarchiver->renameFiles;
		if(!empty($renames)) foreach( $renames as $original => $renamed ) {
			$engine->rename( $renamed, $original );
		}

		// 3. Delete the archive
		foreach( $unarchiver->archiveList as $archive )
		{
			$engine->unlink( $archive );
		}

		// 4. Suicide
		$engine->unlink( basename(__FILE__) );

		// 5. Delete translations
		$dh = opendir(AKKickstartUtils::getPath());
		if($dh !== false)
		{
			$basename = basename(__FILE__, '.php');
			while( false !== $file = @readdir($dh) )
			{
				if( strstr($file, $basename.'.ini') )
				{
					$engine->unlink($file);
				}
			}
		}

		// 6. Delete cacert.pem
		$engine->unlink('cacert.pem');

		// 7. Delete jquery.min.js and json2.min.js
		$engine->unlink('jquery.min.js');
		$engine->unlink('json2.min.js');

		break;

	case 'display':
		$ajax = false;
		echoPage();
		break;

	case 'isJoomla':
		$ajax = true;
		if(!empty($json))
		{
			$json = json_decode($json, true);
			if( array_key_exists('factory', $json) )
			{
				// Get the serialized factory
				$serialized = $json['factory'];
				AKFactory::unserialize($serialized);
				AKFactory::set('kickstart.enabled', true);
			}
		}
		$path = AKFactory::get('kickstart.setup.destdir','');
		$path = rtrim($path, '/\\');
		$isJoomla = @is_dir($path . '/administrator');
		if ($isJoomla)
		{
			$isJoomla = @is_dir($path . '/libraries/joomla');
		}
		$retArray = $isJoomla;

		break;

	case 'listArchives':
		$ajax = true;

		$path = null;

		if(!empty($json))
		{
			$json = json_decode($json, true);

			if( array_key_exists('path', $json) )
			{
				$path = $json['path'];
			}
		}

		if (empty($path) || !@is_dir($path))
		{
			$filelist = null;
		}
		else
		{
			$filelist = AKKickstartUtils::getArchivesAsOptions($path);
		}

		if (empty($filelist))
		{
			$retArray = '<a href="https://www.akeebabackup.com/documentation/troubleshooter/ksnoarchives.html" target="_blank">' .
				AKText::_('NOARCHIVESCLICKHERE')
				. '</a>';
		}
		else
		{
			$retArray = '<select id="kickstart.setup.sourcefile">' . $filelist . '</select>';
		}

		break;

	default:
		$ajax = true;
		if(!empty($json)) {
			$params = json_decode($json, true);
		} else {
			$params = array();
		}
		$retArray = callExtraFeature($task, $params);
		break;
}

if($ajax)
{
	// JSON encode the message
	$json = json_encode($retArray);
	// Do I have to encrypt?
	$password = AKFactory::get('kickstart.security.password', null);
	if(!empty($password))
	{
		$json = AKEncryptionAES::AESEncryptCtr($json, $password, 128);
	}

	// Return the message
	echo "###$json###";
}

/**
 * FTP Functions
 */
function getListing($directory, $host, $port, $username, $password, $passive, $ssl)
{
    $directory = resolvePath($directory);
    $dir       = $directory;

    // Parse directory to parts
    $parsed_dir = trim($dir,'/');
    $parts = empty($parsed_dir) ? array() : explode('/', $parsed_dir);

    // Find the path to the parent directory
    if(!empty($parts)) {
        $copy_of_parts = $parts;
        array_pop($copy_of_parts);
        if(!empty($copy_of_parts)) {
            $parent_directory = '/' . implode('/', $copy_of_parts);
        } else {
            $parent_directory = '/';
        }
    } else {
        $parent_directory = '';
    }

    // Connect to the server
    if($ssl) {
        $con = @ftp_ssl_connect($host, $port);
    } else {
        $con = @ftp_connect($host, $port);
    }
    if($con === false) {
        return array(
            'error' => 'FTPBROWSER_ERROR_HOSTNAME'
        );
    }

    // Login
    $result = @ftp_login($con, $username, $password);
    if($result === false) {
        return array(
            'error' => 'FTPBROWSER_ERROR_USERPASS'
        );
    }

    // Set the passive mode -- don't care if it fails, though!
    @ftp_pasv($con, $passive);

    // Try to chdir to the specified directory
    if(!empty($dir)) {
        $result = @ftp_chdir($con, $dir);
        if($result === false) {
            return array(
                'error' => 'FTPBROWSER_ERROR_NOACCESS'
            );
        }
    }

    // Get a raw directory listing (hoping it's a UNIX server!)
    $list = @ftp_rawlist($con,'.');
    ftp_close($con);

    if($list === false) {
        return array(
            'error' => 'FTPBROWSER_ERROR_UNSUPPORTED'
        );
    }

    // Parse the raw listing into an array
    $folders = parse_rawlist($list);

    return array(
        'error'			=> '',
        'list'			=> $folders,
        'breadcrumbs'	=> $parts,
        'directory'		=> $directory,
        'parent'		=> $parent_directory
    );
}

function parse_rawlist($list)
{
    $folders = array();
    foreach($list as $v)
    {
        $info = array();
        $vinfo = preg_split("/[\s]+/", $v, 9);
        if ($vinfo[0] !== "total") {
            $perms = $vinfo[0];
            if(substr($perms,0,1) == 'd') {
                $folders[] = $vinfo[8];
            }
        }
    }

    asort($folders);
    return $folders;
}

function getSftpListing($directory, $host, $port, $username, $password)
{
    $directory = resolvePath($directory);
    $dir       = $directory;

    // Parse directory to parts
    $parsed_dir = trim($dir,'/');
    $parts = empty($parsed_dir) ? array() : explode('/', $parsed_dir);

    // Find the path to the parent directory
    if(!empty($parts)) {
        $copy_of_parts = $parts;
        array_pop($copy_of_parts);
        if(!empty($copy_of_parts)) {
            $parent_directory = '/' . implode('/', $copy_of_parts);
        } else {
            $parent_directory = '/';
        }
    } else {
        $parent_directory = '';
    }

    // Initialise
    $connection = null;
    $sftphandle = null;

    // Open a connection
    if(!function_exists('ssh2_connect'))
    {
        return array(
            'error' => AKText::_('SFTP_NO_SSH2')
        );
    }

    $connection = ssh2_connect($host, $port);

    if ($connection === false)
    {
        return array(
            'error' => AKText::_('SFTP_WRONG_USER')
        );
    }

    if(!ssh2_auth_password($connection, $username, $password))
    {
        return array(
            'error' => AKText::_('SFTP_WRONG_USER')
        );
    }

    $sftphandle = ssh2_sftp($connection);

    if($sftphandle === false)
    {
        return array(
            'error' => AKText::_('SFTP_NO_FTP_SUPPORT')
        );
    }

    // Get a raw directory listing (hoping it's a UNIX server!)
    $list = array();
    $dir  = ltrim($dir, '/');

    $handle = opendir("ssh2.sftp://$sftphandle/$dir");

    if (!is_resource($handle))
    {
        return array(
            'error' => AKText::_('SFTPBROWSER_ERROR_NOACCESS')
        );
    }

    while (($entry = readdir($handle)) !== false)
    {
        if (!is_dir("ssh2.sftp://$sftphandle/$dir/$entry"))
        {
            continue;
        }

        $list[] = $entry;
    }

    closedir($handle);

    if (!empty($list))
    {
        asort($list);
    }

    return array(
        'error'			=> '',
        'list'			=> $list,
        'breadcrumbs'	=> $parts,
        'directory'		=> $directory,
        'parent'		=> $parent_directory
    );
}

/**
 * Simple function to resolve relative paths.
 * Note that it is unable to resolve pathnames any higher than the present working directory.
 * I.E. It doesn't know about any directory names that you don't tell it about; hence: ../../foo becomes foo.
 *
 * @param $filename
 *
 * @return string
 */
function resolvePath($filename)
{
    $filename = str_replace('//', '/', $filename);
    $parts = explode('/', $filename);
    $out = array();
    foreach ($parts as $part){
        if ($part == '.') continue;
        if ($part == '..') {
            array_pop($out);
            continue;
        }
        $out[] = $part;
    }
    return implode('/', $out);
}