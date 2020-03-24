<?php
/**
 * Akeeba Kickstart
 * An AJAX-powered archive extraction tool
 *
 * @package   kickstart
 * @copyright Copyright (c)2008-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

function echoCSS()
{
	echo <<<CSS

:root {
    --teal-dark: #339092;
    --teal: #40B5B8;
    --teal-light: #62c6c9;

    --red-dark: #c81d23;
    --red: #E2363C;
    --red-light: #e86367;

    --grey-superdark: #272727;
    --grey-dark: #373637;
    --grey: #514F50;
    --grey-light: #6b686a;

    --green-dark: #79a638;;
    --green: #93C34E;
    --green-light: #aad074;

    --orange-dark: #ec971f;
    --orange: #F0AD4E;
    --orange-light: #f4c37d;

    --lightgrey-dark: #d6d6d6;
    --lightgrey: #EFEFEF;
    --lightgrey-light: #fcfcfc;

    --white: #ffffff;
    --black: #000000;
}

html {
    background: var(--white);
    font-size: 62.5%;
}

a, a:visited {
	color: var(--teal);
}

a:hover, a:active {
	color: var(--teal);
}

body {
    font-size: 12pt;
    font-family: Calibri, "Helvetica Neue", Helvetica, Arial, sans-serif;
    text-rendering: optimizeLegibility;
    background: transparent;
    color: var(--grey-dark);
    width: 100%;
    max-width: 980px;
    margin: 0 auto;
}

#page-container {
    position: relative;
    margin: 5% 0;
    background: var(--lightgrey-light);
    border: medium solid var(--lightgrey-dark);
}

#header {
    color: var(--grey-dark);
    background: var(--lightgrey);
    background-clip: padding-box;
    margin-bottom: 0.7em;
    border-bottom: 2px solid var(--lightgrey-dark);
    padding: .25em;
    font-size: 24pt;
    line-height: 1.2;
    text-align: center;
}

#logo {
	background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABkAAAAZCAYAAADE6YVjAAAACXBIWXMAAAHFAAABxQG6eNsrAAAA/0lEQVRIx+WVUQ2DMBCGv01B5wAHYw4qARxMwiQgYQ6YAyRMAjgAB+CgezmSSwOjZfRplzSXNE2/3t1/V/hXuwMjkKcC5ICTNQImBaRVEAfUKdLkFlaWMorDo8lUHXIvsvEoyEMubFai21TaOQByVeqaFWVUPewRkEz5FqjEa+Aus3KpAfqVojsvjXYtdacVSA8MAgqRaSd+AMrYzt6zgmriv3zaeNS08MhNiD5UArcvoA64AE+1FySEYiF0I939Fj/3iFVFd6F9g1KU33yNGiX+JDYCbmIkXHm1sd6YX/pT7pKF3VYrSB87fc8RZyfgJX5I8WEVsfn+5fs1/LV9AHnjYQzAbyUrAAAAAElFTkSuQmCC);
	display: inline-block;
	width: 24px;
	height: 24px;
}

#footer {
    font-size: 9pt;
    color: var(--grey-light);
    text-align: center;
    border-top: 1px solid var(--lightgrey-dark);
    padding: 1em 1em;
    background: var(--lightgrey);
    clear: both;
}

#footer a {
    color: var(--teal-dark);
    text-decoration: none;
}

#error, .error {
    x-display: none;
    border: solid var(--red-dark);
    border-width: 4px 0;
    background: var(--red-light);
    color: var(--grey-dark);
    padding: 1em 2em;
    margin-bottom: 1.15em;
    text-align: center;
}

#error h3, .error h3, .warning h3, .notice h3 {
    margin: 0;
    padding: 0;
    font-size: 12pt;
}

.warning {
    border: solid var(--orange-dark);
    border-width: 4px 0;
    background: var(--orange-light);
    color: var(--grey-dark);
    padding: 1em 2em;
    margin-bottom: 1.15em;
    text-align: center;
}

.notice {
    border: solid var(--teal-dark);
    border-width: 4px 0;
    background: var(--teal-light);
    color: var(--grey-dark);
    padding: 1em 2em;
    margin-bottom: 1.15em;
    text-align: center;
}

.clr {
    clear: both;
}

.circle {
    display: block;
    float: left;
    border-radius: 2em;
    border: 2px solid var(--lightgrey);
    font-weight: bold;
    font-size: 14pt;
    line-height: 1.5em;
    color: var(--lightgrey-light);
    height: 1.5em;
    width: 1.5em;
    margin: 0.75em;
    text-align: center;
    background: var(--teal);
}

.area-container {
    margin: 1em 2em;
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
    font-size: 18pt;
    font-weight: normal;
    line-height: 1.3;
    border: solid var(--lightgrey-dark);
    border-left: none;
    border-right: none;
    padding: 0.5em 0;
    background: var(--lightgrey);
}

#preextraction h2 {
    margin-top: 0;
    border-top: 0;
    text-align: center;
}

input,
select,
textarea {
    font-size: 100%;
    margin: 0;
    vertical-align: baseline;
    *vertical-align: middle;
}

button,
input {
    line-height: normal;
    font-weight: normal;
    *overflow: visible;
}

input,
select,
textarea {
    background: var(--white);
    color: var(--grey-dark);
    font-size: 12pt;
    border: 1px solid var(--lightgrey-dark);
    border-radius: .25em;
    box-sizing: border-box;
    width: 50%;
    padding: 0 0 0 .5em;
}

input[type="checkbox"] {
    width: auto;
}

.field {
    height: 1.5em;
}

label {
    display: inline-block;
    width: 30%;
    font-size: 95%;
    font-weight: normal;
    cursor: pointer;
    color: #333;
    margin: .5em 0;
}

.help {
    width: 60%;
    margin-left: 30%;
    margin-bottom: 1.5em;
    font-size: small;
    color: var(--grey);
}

input:focus, input:hover {
    background-color: var(--lightgrey);
}

.button {
    display: inline-block;
    margin: 1em .25em;
    padding: 1em 2em;
    background: var(--green-dark);
    color: var(--white);
    border: 1px solid var(--green);
    cursor: pointer;
    border-radius: .25em;
    transition: 0.3s linear all;
}

#checkFTPTempDir.button,
#resetFTPTempDir.button,
#testFTP.button,
#browseFTP,
#reloadArchives,
#notWorking.button {
    padding: .5em 1em;
}

.button:hover, .button:active {
    border: 1px solid var(--green-light);
    background: var(--green);
    color: var(--white);
}

#notWorking.button, .bluebutton {
    text-decoration: none;
    background: var(--teal);
    border-color: var(--teal-dark);
    color: var(--white);
}

#notWorking.button:hover, .bluebutton:hover {
    background: var(--teal-light);
    border-color: var(--teal);
    color: var(--white);
}

#notWorking.button:active, .bluebutton:active {
    background: var(--teal-light);
    border-color: var(--teal);
}

.loprofile {
    padding: 0.5em 1em;
    font-size: 80%;
}

.black_overlay {
    display: none;
    position: absolute;
    top: 0%;
    left: 0%;
    width: 100%;
    height: 100%;
    background-color: var(--black);
    z-index: 1001;
    -moz-opacity: 0.8;
    opacity: .80;
    filter: alpha(opacity=80);
}

.white_content {
    display: none;
    position: absolute;
    padding: 0 0 1em;
    background: var(--lightgrey-light);
    border: 1px solid rgba(0, 0, 0, .3);
    z-index: 1002;
    overflow: hidden;
}

.white_content a {
    margin-left: 4em;
}

ol {
    margin: 0 2em;
    padding: 0 2em 1em;
}

li {
    margin: 0 0 .5em;
}

#genericerror {
    background-color: var(--orange-light);
    border: 4px solid var(--orange) !important;
}

#genericerrorInner {
    font-size: 110%;
    color: var(--grey-dark);
}

#warn-not-close, .warn-not-close {
    padding: 0.2em 0.5em;
    text-align: center;
    background: var(--orange);
    font-size: smaller;
    font-weight: bold;
}

#progressbar, .progressbar {
    display: block;
    width: 80%;
    height: 32px;
    border: 1px solid var(--lightgrey-dark);
    margin: 1em 10% 0.2em;
    border-radius: .25em;
}

#progressbar-inner, .progressbar-inner {
    display: block;
    width: 100%;
    height: 100%;
    background: var(--teal-dark);
}

#currentFile {
    font-family: Consolas, "Courier New", Courier, monospace;
    font-size: 9pt;
    height: 10pt;
    overflow: hidden;
    text-overflow: ellipsis;
    background: var(--lightgrey-dark);
    margin: 0 10% 1em;
    padding: .125em;
}

#extractionComplete {
}

#warningsContainer {
    border-bottom: 2px solid var(--orange-dark);
    border-left: 2px solid var(--orange-dark);
    border-right: 2px solid var(--orange-dark);
    padding: 5px 0;
    background: var(--orange-light);
    border-bottom-right-radius: 5px;
    border-bottom-left-radius: 5px;
}

#warningsHeader h2 {
    color: var(--grey-dark);
    border-top: 2px solid var(--orange-dark);
    border-left: 2px solid var(--orange-dark);
    border-right: 2px solid var(--orange-dark);
    border-bottom: thin solid var(--orange-dark);
    border-top-right-radius: 5px;
    border-top-left-radius: 5px;
    background: var(--orange);
    font-size: large;
    padding: 2px 5px;
    margin: 0px;
}

#warnings {
    height: 200px;
    overflow-y: scroll;
}

#warnings div {
    background: var(--orange-light);
    font-size: small;
    padding: 2px 4px;
    border-bottom: thin solid var(--grey-dark);
}

.helpme,
#warn-not-close {
    background: var(--orange-light);
    padding: 0.75em 0.5em;
    border: solid var(--orange);
    border-width: 1px 0;
    text-align: center;
}

/* FTP / S3 Browser */
.breadcrumb {
    background-color: var(--lightgrey);
    border-radius: 4px;
    list-style: none outside none;
    margin: 0 0 18px;
    padding: 8px 15px;
}

.breadcrumb > li {
    display: inline-block;
    text-shadow: 0 1px 0 var(--lightgrey-light);
}

#ak_crumbs span {
    padding: 1px 3px;
}

#ak_crumbs a {
    cursor: pointer;
}

#ftpBrowserFolderList a {
    cursor: pointer
}

/* Bootstrap porting */
.table {
    margin-bottom: 18px;
    width: 100%;
}

.table th, .table td {
    border-top: 1px solid var(--lightgrey-dark);
    line-height: 18px;
    padding: 8px;
    text-align: left;
    vertical-align: top;
}

.table-striped tbody > tr:nth-child(2n+1) > td, .table-striped tbody > tr:nth-child(2n+1) > th {
    background-color: var(--lightgrey-light);
}

@media (prefers-color-scheme: dark) {
	html {
		background: var(--grey-superdark);
		color: var(--white);
	}
	
	body {
		background: var(--grey-superdark);
		color: var(--white);	
	}
	
	#logo {
		background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABkAAAAZCAYAAADE6YVjAAAACXBIWXMAAAHFAAABxQG6eNsrAAABFElEQVRIx+VVQZHDMAxcFYGPQRg0ZRAIKYODUAiFcAxyDAKhEFwGCYOEwfYjT1VPWjtt/Ko+mvFYXkm7koGvNJK/JCeSdSmAmnebSLoSIJ6P1pVo05JVJavYthqSleGhjiqbtgI56YP9k+qSSttl4OzV10FR6gMfzRYglfGe5BmABxAk7N5tUaNcOJIDX1tvYhZbJ09ABgCjZp8j06v6UUSOSRDNxr8rFBGRHE7izOfEu/NCkkkQe+kI4PAC6CoiPwD+zJnLIb0NjJozR7IjeVEfZqQxpDN3bmAUFQ9fH1ZJvIk1xscxKaCzXX5RptPSn6Krpv1ktXQGZFi7fXcr7s4A/gHMIjKW+LDaVf3+8Pt1+Gq7AZ5SjMx2RnT3AAAAAElFTkSuQmCC);
	}
	
	#page-container {
		background: var(--grey-dark);
		border: medium solid var(--grey);
	}
	
	#header {
	    color: var(--lightgrey-dark);
	    background: var(--grey-light);
	    border-bottom: 2px solid var(--grey);
	}
	
	#footer {
	    color: var(--lightgrey-light);
	    border-top: 1px solid var(--grey-dark);
	    background: var(--grey);
	}

	#footer a {
	    color: var(--teal);
	}
	
	h2 {
		border: solid var(--grey-light);
		background: var(--grey);
	}
	
	input,
	select,
	textarea {
	    background: var(--grey-dark);
	    color: var(--lightgrey);
	    border: 1px solid var(--grey);
	}
	
	label {
		color: var(--lightgrey)
	}
	
	.help {
	    color: var(--lightgrey-dark);
	}
	
	input:focus, input:hover {
	    background-color: var(--grey-light);
	}

	.white_content {
		background-color: var(--grey-dark);
	}

	#warn-not-close, .warn-not-close {
	    color: var(--white);
	}

	#progressbar, .progressbar {
	    border: 1px solid var(--grey-light);
	}
	
	#currentFile {
		background-color: var(--grey);
	}
	
	#warningsContainer {
		color: var(--grey-dark);
	}
	
	.helpme, #warn-not-close {
		color: var(--grey-dark);
	}
	
	.helpme a, #warn-not-close a {
		color: var(--teal-dark);
	}
}

CSS;

	callExtraFeature('onExtraHeadCSS');
}
