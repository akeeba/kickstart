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