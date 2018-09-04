<?php
/**
 * @copyright Copyright (c)2008-2018 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL v2 or - at your option - any later version
 * @package     akeebabackup
 * @subpackage  kickstart
 */

define('KSROOTDIR', __DIR__);
define('KSLANGDIR', __DIR__ . '/jslang');
define('MINIBUILD', __DIR__ . '/minibuild');
define('KSSELFNAME', 'joomlastart_test.php');
//define('KSDEBUG', 1);

include_once MINIBUILD . '/minibuild.php';

$minibuild = new AkeebaMinibuild;
$minibuild->minibuild(MINIBUILD . '/joomlastart.build', false);
