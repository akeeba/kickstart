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
 * This is an Easter Egg feature. Run Kickstart as kickstart.php?george and a hideous orange template with the font face
 * everyone loves to hate will burn your eyes out of their socket. HAHAHAHAHA!
 */
class AKFeatureGeorgeWSpecialEdition
{
	/**
	 * Echoes extra CSS to the head of the page
	 */
	public function onExtraHeadCSS()
	{
		if (!isset($_REQUEST['george']))
		{
			return;
		}

		echo <<< CSS
html {
    background: #FFA500;
}

body {
	font-family: "Comic Sans MS";
}

#header {
	background: #FFA500;
	color: red;
	font-weight: bold;
}

#footer {
	color: #333;
	background: #FFA500;
}

#footer a {
	color: #f33;
}

h2 {
	background: #FFA500;
	color: #993300;
}

.button:active {
	border: 1px solid #FFA500;
}

.ui-button {
    font-family: "Comic Sans MS";
}

.ribbon-box {
   width:100%;
   height:400px;
   margin-top: -4.5em;
   right: 5px;
   position: absolute;
}
.ribbon {
   position: absolute;
   right: -5px; top: -5px;
   z-index: 1;
   overflow: hidden;
   width: 450px; height: 450px;
   text-align: right;
}
.ribbon span {
   font-size: 12pt;
   color: red;
   text-transform: uppercase;
   text-align: center;
   font-weight: bold;
   line-height: 20px;
   transform: rotate(45deg);
   -webkit-transform: rotate(45deg); /* Needed for Safari */
   width: 300px; display: block;
   background: #FFA500;
   background: linear-gradient(#FFA500 0%, #FFA500 100%);
   position: absolute;
   border: thin solid firebrick;
   top: 80px;
   right: -55px;
   box-shadow: yellow 1px 1px 20px;
   text-shadow: 1px 1px 2px white;
}
CSS;

	}

	public function onExtraHeadJavascript()
	{
		if (!isset($_REQUEST['george']))
		{
			return;
		}

		echo <<< JS
// Matrix effect from http://thecodeplayer.com/walkthrough/matrix-rain-animation-html5-canvas-javascript
var c = null;
var ctx = null;

//japanese characters - taken from the unicode charset
var japanese = "あいうえおかきくけこさしすせそがぎぐげごぱぴぷぺぽ";
//converting the string into an array of single characters
japanese = japanese.split("");

var font_size = 14;
var columns = 0; //number of columns for the rain
//an array of drops - one per column
var drops = [];

//drawing the characters
function draw()
{
	//Black BG for the canvas
	//translucent BG to show trail
	ctx.fillStyle = "rgba(0, 0, 0, 0.05)";
	ctx.fillRect(0, 0, c.width, c.height);

	ctx.fillStyle = "#0F0"; //green text
	ctx.font = font_size + "px Arial";

	//looping over drops
	for(var i = 0; i < drops.length; i++)
	{
		//a random japanese character to print
		var text = japanese[Math.floor(Math.random()*japanese.length)];
		//x = i*font_size, y = value of drops[i]*font_size
		ctx.fillText(text, i*font_size, drops[i]*font_size);

		//sending the drop back to the top randomly after it has crossed the screen
		//adding a randomness to the reset to make the drops scattered on the Y axis
		if(drops[i]*font_size > c.height && Math.random() > 0.975)
			drops[i] = 0;

		//incrementing Y coordinate
		drops[i]++;
	}
}

/*
 * Konami Code Javascript Object
 * 1.3.0, 7 March 2014
 *
 * Using the Konami code, easily configure an Easter Egg for your page or any element on the page.
 *
 * Options:
 * - code : set your own custom code, takes array of keycodes / default is original Konami code
 * - cheat : the function to call when the proper sequence is entered
 * - elem : the element to set the instance on
 *
 * Copyright 2013 - 2014 Kurtis Kemple, http://kurtiskemple.com
 * Released under the MIT License
 */

var KONAMI = function ( options ) {
	var elem, ret, defaults, keycode, config, cache;

	// set the default code,function, and element
	defaults = {
		code : [38,38,40,40,37,39,37,39,66,65],
		cheat : null,
		elem : window
	};

	// build our return object
	ret = {

		/**
		 * handles the initialization of the KONAMI instance
		 *
		 * @param  {object} options the config to pass in to the instance
		 * @return {none}
		 * @method  init
		 * @public
		 */
		init : function ( options ) {
			cache = [], config = {};

			if ( options ) {

				for ( var key in defaults ) {

					if ( defaults.hasOwnProperty( key ) ) {

						if ( !options[ key ] ) {

							config[ key ] = defaults[ key ];
						} else {

							config[ key ] = options[ key ];
						}
					}
				}
			} else {

				config = defaults;
			}

			ret.bind( config.elem, 'keyup', ret.konami );
		},

		/**
		 * handles disassembling of the instance
		 *
		 * @return {none}
		 * @method  destroy
		 * @public
		 */
		destroy : function () {
			ret.unbind( config.elem, 'keyup', ret.konami );
			cache = config = null;
		},

		/**
		 * handles adding events to elements
		 *
		 * @param   {elem}     elem  DOM element to attach to
		 * @param   {string}   evt   the event type to bind to
		 * @param   {Function} fn    the function to bind
		 * @return  {none}
		 * @method  bind
		 * @private
		 */
		bind : function ( elem, evt, fn ) {
			if ( elem.addEventListener ) {

				elem.addEventListener( evt, fn, false );
			} else if ( elem.attachEvent ) {

				elem.attachEvent( 'on'+ evt, function( e ) {
					fn( e || window.event );
				});
			}
		},

		/**
		 * handles removing events from elements
		 *
		 * @param   {elem}     elem DOM element to remove from
		 * @param   {string}   evt  the event type to unbind
		 * @param   {Function} fn   the function to unbind
		 * @return  {none}
		 * @method  unbind
		 * @private
		 */
		unbind : function ( elem, evt, fn ) {
			if ( elem.removeEventListener ) {

				elem.removeEventListener( evt, fn, false );
			} else if ( elem.detachEvent ) {

				elem.detachEvent( 'on' + evt, function( e ) {
					fn( e || window.event );
				});
			}
		},

		/**
		 * handles the business logic for checking for valid konami code
		 *
		 * @param   {object} e the event object
		 * @return  {none}
		 * @method  konami
		 * @private
		 */
		konami : function( e ) {
			keycode = e.keyCode || e.which;

			if ( config.code.length > cache.push( keycode ) ) {

				return;
			}

			if ( config.code.length < cache.length ) {

				cache.shift();
			}

			if ( config.code.toString() !== cache.toString() ) {

				return;
			}

			config.cheat();
		}
	};

	ret.init( options );
	return ret;
};

var options = {
	cheat : function() {
		c = document.createElement('canvas');
		window.jQuery('html').html('').append(c);
		window.jQuery(c).attr('id', 'c');

		//making the canvas full screen
		c.height = window.innerHeight;
		c.width = window.innerWidth;
		c.left = 0;
		c.top = 0;

		ctx = c.getContext("2d");
		columns = c.width/font_size; //number of columns for the rain

		p1 = document.getElementById('page1');
		window.jQuery(p1).hide();

		//x below is the x coordinate
		//1 = y co-ordinate of the drop(same for every drop initially)
		for(var x = 0; x < columns; x++)
			drops[x] = 1;

		setInterval(draw, 33);
	}
};

var konamiCode = new KONAMI( options );

JS;

	}

	/**
	 * Echoes extra HTML on page 1 of Kickstart
	 */
	public function onPage1()
	{
		if (!isset($_REQUEST['george']))
		{
			return;
		}

		echo <<< HTML
<div class="ribbon-box">
	<div class="ribbon">
		<span>George W. Edition</span>
	</div>
</div>
HTML;

	}

	/**
	 * Outputs HTML to be shown before Step 1's archive selection pane
	 */
	public function onPage1Step1()
	{
	}
}

