<?php
/**
 * @copyright Copyright (c)2008-2018 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL v2 or - at your option - any later version
 * @package     akeebabackup
 * @subpackage  kickstart
 */

define('MINIBUILD', __DIR__);
include_once __DIR__ . '/../minibuild.php';

$minibuild = new AkeebaMinibuild;

$buffer = $minibuild->minibuild(MINIBUILD . '/../restore.build', true);
file_put_contents(__DIR__ . '/../../output/restore.php', $buffer);

$buffer = $minibuild->minibuild(MINIBUILD . '/../kickstart_core.build', true);
file_put_contents(__DIR__ . '/../../output/kickstart.php', $buffer);

$buffer = $minibuild->minibuild(MINIBUILD . '/../kickstart_pro.build', true);
file_put_contents(__DIR__ . '/../../output/kickstart_pro.php', $buffer);
