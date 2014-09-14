<?php
define('MINIBUILD', __DIR__);
include_once __DIR__ . '/../minibuild.php';

$minibuild = new AkeebaMinibuild;

$buffer = "<?php\n" . $minibuild->minibuild(MINIBUILD . '/../joomlastart.build', true);
file_put_contents(__DIR__ . '/../output/joomlastart.php', $buffer);