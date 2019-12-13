/*
 * Akeeba Kickstart
 * An AJAX-powered archive extraction tool
 *
 * @package   kickstart
 * @copyright Copyright (c)2008-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

/**
 * Returns the version of Internet Explorer or a -1
 * (indicating the use of another browser).
 *
 * @return   integer  MSIE version or -1
 */
function getInternetExplorerVersion()
{
	var rv = -1; // Return value assumes failure.
	if (navigator.appName == "Microsoft Internet Explorer")
	{
		var ua = navigator.userAgent;
		var re = new RegExp("MSIE ([0-9]{1,}[\.0-9]{0,})");
		if (re.exec(ua) != null)
		{
			rv = parseFloat(RegExp.$1);
		}
	}
	return rv;
}

function resolvePath(filename)
{
	filename  = filename.replace("\/\/g", "\/");
	var parts = filename.split("/");
	var out   = [];

	for (var i = 0; i < parts.length; i++)
	{
		var part = parts[i];

		if (part === ".")
		{
			continue;
		}

		if (part === "..")
		{
			out.pop();

			continue;
		}

		out.push(part);
	}

	return out.join("/");
}

/*
 * Courtesy of PHPjs -- http://phpjs.org
 * @license GPL, version 2
 */

function version_compare(v1, v2, operator)
{
	// BEGIN REDUNDANT
	this.php_js     = this.php_js || {};
	this.php_js.ENV = this.php_js.ENV || {};
	// END REDUNDANT
	// Important: compare must be initialized at 0.
	var i           = 0,
		x           = 0,
		compare     = 0,
		// vm maps textual PHP versions to negatives so they're less than 0.
		// PHP currently defines these as CASE-SENSITIVE. It is important to
		// leave these as negatives so that they can come before numerical versions
		// and as if no letters were there to begin with.
		// (1alpha is < 1 and < 1.1 but > 1dev1)
		// If a non-numerical value can't be mapped to this table, it receives
		// -7 as its value.
		vm          = {
			'dev':   -6,
			'alpha': -5,
			'a':     -5,
			'beta':  -4,
			'b':     -4,
			'RC':    -3,
			'rc':    -3,
			'#':     -2,
			'p':     -1,
			'pl':    -1
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
		prepVersion = function (v)
		{
			v = ('' + v).replace(/[_\-+]/g, '.');
			v = v.replace(/([^.\d]+)/g, '.$1.').replace(/\.{2,}/g, '.');
			return (!v.length ? [-8] : v.split('.'));
		},
		// This converts a version component to a number.
		// Empty component becomes 0.
		// Non-numerical component becomes a negative number.
		// Numerical component becomes itself as an integer.
		numVersion  = function (v)
		{
			return !v ? 0 : (isNaN(v) ? vm[v] || -7 : parseInt(v, 10));
		};
	v1              = prepVersion(v1);
	v2              = prepVersion(v2);
	x               = Math.max(v1.length, v2.length);
	for (i = 0; i < x; i++)
	{
		if (v1[i] == v2[i])
		{
			continue;
		}
		v1[i] = numVersion(v1[i]);
		v2[i] = numVersion(v2[i]);
		if (v1[i] < v2[i])
		{
			compare = -1;
			break;
		}
		else if (v1[i] > v2[i])
		{
			compare = 1;
			break;
		}
	}
	if (!operator)
	{
		return compare;
	}

	// Important: operator is CASE-SENSITIVE.
	// "No operator" seems to be treated as less than
	// Any other values seem to make the function return null.
	switch (operator)
	{
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

function is_array(mixed_var)
{
	var key         = "";
	var getFuncName = function (fn)
	{
		var name = (/\W*function\s+([\w\$]+)\s*\(/).exec(fn);
		if (!name)
		{
			return "(Anonymous)";
		}
		return name[1];
	};

	if (!mixed_var)
	{
		return false;
	}

	// BEGIN REDUNDANT
	this.php_js     = this.php_js || {};
	this.php_js.ini = this.php_js.ini || {};
	// END REDUNDANT

	if (typeof mixed_var === "object")
	{

		if (this.php_js.ini["phpjs.objectsAsArrays"] &&  // Strict checking for being a JavaScript array (only check this way if
											 // call ini_set('phpjs.objectsAsArrays', 0) to disallow objects as arrays)
			(
				(this.php_js.ini["phpjs.objectsAsArrays"].local_value.toLowerCase &&
					this.php_js.ini["phpjs.objectsAsArrays"].local_value.toLowerCase() === "off") ||
				parseInt(this.php_js.ini["phpjs.objectsAsArrays"].local_value, 10) === 0)
		)
		{
			return mixed_var.hasOwnProperty("length") && // Not non-enumerable because of being on parent class
				!mixed_var.propertyIsEnumerable("length") && // Since is own property, if not enumerable, it must be a
															 // built-in function
				getFuncName(mixed_var.constructor) !== "String"; // exclude String()
		}

		if (mixed_var.hasOwnProperty)
		{
			for (key in mixed_var)
			{
				// Checks whether the object has the specified property
				// if not, we figure it's not an object in the sense of a php-associative-array.
				if (false === mixed_var.hasOwnProperty(key))
				{
					return false;
				}
			}
		}

		// Read discussion at: http://kevin.vanzonneveld.net/techblog/article/javascript_equivalent_for_phps_is_array/
		return true;
	}

	return false;
}

function array_key_exists(key, search)
{
	if (!search || (search.constructor !== Array && search.constructor !== Object))
	{
		return false;
	}
	return key in search;
}

function basename(path, suffix)
{
	var b = path.replace(/^.*[\/\\]/g, "");
	if (typeof(suffix) == "string" && b.substr(b.length - suffix.length) == suffix)
	{
		b = b.substr(0, b.length - suffix.length);
	}
	return b;
}

function number_format(number, decimals, dec_point, thousands_sep)
{
	var n = number, c = isNaN(decimals = Math.abs(decimals)) ? 2 : decimals;
	var d = dec_point == undefined ? "," : dec_point;
	var t = thousands_sep == undefined ? "." : thousands_sep, s = n < 0 ? "-" : "";
	var i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", j = (j = i.length) > 3 ? j % 3 : 0;

	return s + (j ? i.substr(0, j) + t : "") + i.substr(j)
		.replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
}

function size_format(filesize)
{
	if (filesize >= 1073741824)
	{
		filesize = number_format(filesize / 1073741824, 2, ".", "") + " GB";
	}
	else
	{
		if (filesize >= 1048576)
		{
			filesize = number_format(filesize / 1048576, 2, ".", "") + " MB";
		}
		else
		{
			filesize = number_format(filesize / 1024, 2, ".", "") + " KB";
		}
	}
	return filesize;
}

/**
 * Checks if a variable is empty. From the php.js library.
 */
function empty(mixed_var)
{
	var key;

	if (mixed_var === "" ||
		mixed_var === 0 ||
		mixed_var === "0" ||
		mixed_var === null ||
		mixed_var === false ||
		typeof mixed_var === "undefined"
	)
	{
		return true;
	}

	if (typeof mixed_var == "object")
	{
		for (key in mixed_var)
		{
			return false;
		}
		return true;
	}

	return false;
}

function ltrim(str, charlist)
{
	// Strips whitespace from the beginning of a string
	//
	// version: 1008.1718
	// discuss at: http://phpjs.org/functions/ltrim    // +   original by: Kevin van Zonneveld
	// (http://kevin.vanzonneveld.net) +      input by: Erkekjetter +   improved by: Kevin van Zonneveld
	// (http://kevin.vanzonneveld.net) +   bugfixed by: Onno Marsman *     example 1: ltrim('    Kevin van Zonneveld
	// ');    // *     returns 1: 'Kevin van Zonneveld    '
	charlist = !charlist ? " \\s\u00A0" : (charlist + "").replace(/([\[\]\(\)\.\?\/\*\{\}\+\$\^\:])/g, "$1");
	var re   = new RegExp("^[" + charlist + "]+", "g");
	return (str + "").replace(re, "");
}

function array_shift(inputArr)
{
	// http://kevin.vanzonneveld.net
	// +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	// +   improved by: Martijn Wieringa
	// %        note 1: Currently does not handle objects
	// *     example 1: array_shift(['Kevin', 'van', 'Zonneveld']);
	// *     returns 1: 'Kevin'

	var props                                                  = false,
		shift = undefined, pr = "", allDigits = /^\d$/, int_ct = -1,
		_checkToUpIndices                                      = function (arr, ct, key)
		{
			// Deal with situation, e.g., if encounter index 4 and try to set it to 0, but 0 exists later in loop (need
			// to increment all subsequent (skipping current key, since we need its value below) until find unused)
			if (arr[ct] !== undefined)
			{
				var tmp = ct;
				ct += 1;
				if (ct === key)
				{
					ct += 1;
				}
				ct      = _checkToUpIndices(arr, ct, key);
				arr[ct] = arr[tmp];
				delete arr[tmp];
			}
			return ct;
		};


	if (inputArr.length === 0)
	{
		return null;
	}
	if (inputArr.length > 0)
	{
		return inputArr.shift();
	}
}

function trim(str, charlist)
{
	var whitespace, l = 0, i = 0;
	str += "";

	if (!charlist)
	{
		// default list
		whitespace =
			" \n\r\t\f\x0b\xa0\u2000\u2001\u2002\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200a\u200b\u2028\u2029\u3000";
	}
	else
	{
		// preg_quote custom list
		charlist += "";
		whitespace = charlist.replace(/([\[\]\(\)\.\?\/\*\{\}\+\$\^\:])/g, "$1");
	}

	l = str.length;
	for (i = 0; i < l; i++)
	{
		if (whitespace.indexOf(str.charAt(i)) === -1)
		{
			str = str.substring(i);
			break;
		}
	}

	l = str.length;
	for (i = l - 1; i >= 0; i--)
	{
		if (whitespace.indexOf(str.charAt(i)) === -1)
		{
			str = str.substring(0, i + 1);
			break;
		}
	}

	return whitespace.indexOf(str.charAt(0)) === -1 ? str : "";
}

function array_merge()
{
	// Merges elements from passed arrays into one array
	//
	// version: 1103.1210
	// discuss at: http://phpjs.org/functions/array_merge
	// +   original by: Brett Zamir (http://brett-zamir.me)
	// +   bugfixed by: Nate
	// +   input by: josh
	// +   bugfixed by: Brett Zamir (http://brett-zamir.me)
	// *     example 1: arr1 = {"color": "red", 0: 2, 1: 4}
	// *     example 1: arr2 = {0: "a", 1: "b", "color": "green", "shape": "trapezoid", 2: 4}
	// *     example 1: array_merge(arr1, arr2)
	// *     returns 1: {"color": "green", 0: 2, 1: 4, 2: "a", 3: "b", "shape": "trapezoid", 4: 4}
	// *     example 2: arr1 = []
	// *     example 2: arr2 = {1: "data"}
	// *     example 2: array_merge(arr1, arr2)
	// *     returns 2: {0: "data"}
	var args   = Array.prototype.slice.call(arguments),
		retObj = {},
		k, j   = 0,
		i      = 0,
		retArr = true;

	for (i = 0; i < args.length; i++)
	{
		if (!(args[i] instanceof Array))
		{
			retArr = false;
			break;
		}
	}

	if (retArr)
	{
		retArr = [];
		for (i = 0; i < args.length; i++)
		{
			retArr = retArr.concat(args[i]);
		}
		return retArr;
	}
	var ct = 0;

	for (i = 0, ct = 0; i < args.length; i++)
	{
		if (args[i] instanceof Array)
		{
			for (j = 0; j < args[i].length; j++)
			{
				retObj[ct++] = args[i][j];
			}
		}
		else
		{
			for (k in args[i])
			{
				if (args[i].hasOwnProperty(k))
				{
					if (parseInt(k, 10) + "" === k)
					{
						retObj[ct++] = args[i][k];
					}
					else
					{
						retObj[k] = args[i][k];
					}
				}
			}
		}
	}
	return retObj;
}

function array_diff(arr1)
{ // eslint-disable-line camelcase
	//  discuss at: http://locutus.io/php/array_diff/
	// original by: Kevin van Zonneveld (http://kvz.io)
	// improved by: Sanjoy Roy
	//  revised by: Brett Zamir (http://brett-zamir.me)
	//   example 1: array_diff(['Kevin', 'van', 'Zonneveld'], ['van', 'Zonneveld'])
	//   returns 1: {0:'Kevin'}

	var retArr = {};
	var argl   = arguments.length;
	var k1     = "";
	var i      = 1;
	var k      = "";
	var arr    = {};

	arr1keys: for (k1 in arr1)
	{
		for (i = 1; i < argl; i++)
		{
			arr = arguments[i];
			for (k in arr)
			{
				if (arr[k] === arr1[k1])
				{
					// If it reaches here, it was found in at least one array, so try next value
					continue arr1keys;
				}
			}
			retArr[k1] = arr1[k1];
		}
	}

	return retArr;
}

//=============================================================================
// Object.keys polyfill
//=============================================================================

// From https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Object/keys
if (!Object.keys)
{
	Object.keys = (function () {
		"use strict";
		var hasOwnProperty  = Object.prototype.hasOwnProperty,
			hasDontEnumBug  = !({toString: null}).propertyIsEnumerable("toString"),
			dontEnums       = [
				"toString",
				"toLocaleString",
				"valueOf",
				"hasOwnProperty",
				"isPrototypeOf",
				"propertyIsEnumerable",
				"constructor"
			],
			dontEnumsLength = dontEnums.length;

		return function (obj) {
			if (typeof obj !== "object" && (typeof obj !== "function" || obj === null))
			{
				throw new TypeError("Object.keys called on non-object");
			}

			var result = [], prop, i;

			for (prop in obj)
			{
				if (hasOwnProperty.call(obj, prop))
				{
					result.push(prop);
				}
			}

			if (hasDontEnumBug)
			{
				for (i = 0; i < dontEnumsLength; i++)
				{
					if (hasOwnProperty.call(obj, dontEnums[i]))
					{
						result.push(dontEnums[i]);
					}
				}
			}
			return result;
		};
	}());
}