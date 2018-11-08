/**
 * @copyright   Copyright (c)2008-2018 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL v2 or - at your option - any later version
 * @package     akeebabackup
 * @subpackage  kickstart
 *
 * Common UI callbacks
 */

/**
 * Generic error handler
 *
 * @param   {string}  msg  Error message to display
 */
function onGenericError(msg)
{
	document.getElementById("genericerrorInner").innerHTML = msg;
	document.getElementById("genericerror").style.display  = "block";
	document.getElementById("fade").style.display          = "block";

	akeeba.System.addEventListener(document, "keyup", closeLightbox());
}

/**
 * Set the progress bar to a specific percentage
 *
 * @param   {int}  percent  Percentage (or float 0.0 to 1.0) to display in the progress bar.
 */
function setProgressBar(percent)
{
	var newValue = percent;

	if (percent <= 1)
	{
		newValue = 100 * percent;
	}

	document.getElementById("progressbar-inner").style.width = newValue + "%";
}

/**
 * Close the lightbox
 *
 * @param   {KeyboardEvent|MouseEvent}  event
 */
function closeLightbox(event)
{
	var closeMe = false;

	if ((event == null) || (event === undefined))
	{
		closeMe = true;
	}
	else if (event.keyCode === "27")
	{
		closeMe = true;
	}

	if (!closeMe)
	{
		return;
	}

	document.getElementById("preextraction").style.display = "none";
	document.getElementById("genericerror").style.display  = "none";
	document.getElementById("fade").style.display          = "none";

	akeeba.System.removeEventListener(document, "keyup", closeLightbox);
}
