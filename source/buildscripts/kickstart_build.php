<?php
define('MINIBUILD', __DIR__);
include_once __DIR__ . '/../minibuild.php';

$minibuild = new AkeebaMinibuild;

$buffer = "<?php\n" . $minibuild->minibuild(MINIBUILD . '/../restore.build', true);
file_put_contents(__DIR__ . '/../output/restore.php', $buffer);

$buffer = "<?php\n" . $minibuild->minibuild(MINIBUILD . '/../kickstart_core.build', true);
file_put_contents(__DIR__ . '/../output/kickstart.php', $buffer);

$buffer = "<?php\n" . $minibuild->minibuild(MINIBUILD . '/../kickstart_pro.build', true);
file_put_contents(__DIR__ . '/../output/kickstart_pro.php', $buffer);