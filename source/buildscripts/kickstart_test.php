<?php
define('KSROOTDIR', __DIR__);
define('KSLANGDIR', __DIR__ . '/kslang');
define('MINIBUILD', __DIR__ . '/minibuild');
define('KSSELFNAME', 'kickstart_test.php');
//define('KSDEBUG', 1);

include_once MINIBUILD . '/minibuild.php';

$minibuild = new AkeebaMinibuild;
$minibuild->minibuild(MINIBUILD . '/kickstart_pro.build', false);