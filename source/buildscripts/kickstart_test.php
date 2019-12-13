<?php
/**
 * Akeeba Kickstart
 * An AJAX-powered archive extraction tool
 *
 * @package   kickstart
 * @copyright Copyright (c)2008-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

define('KSROOTDIR', __DIR__);
define('KSLANGDIR', __DIR__ . '/kslang');
define('MINIBUILD', __DIR__ . '/minibuild');
define('KSSELFNAME', 'kickstart_test.php');
define('KSDEBUG', 1);
define('VERSION', '0.0.0-dev');
define('KICKSTARTPRO', 1);

error_reporting(E_ALL | E_NOTICE | E_DEPRECATED);
ini_set('display_errors', 1);

include_once MINIBUILD . '/minibuild.php';

$minibuild = new AkeebaMinibuild;
$minibuild->minibuild(MINIBUILD . '/kickstart_pro.build', false);
