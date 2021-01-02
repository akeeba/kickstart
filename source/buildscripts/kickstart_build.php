<?php
/**
 * Akeeba Kickstart
 * An AJAX-powered archive extraction tool
 *
 * @package   kickstart
 * @copyright Copyright (c)2008-2021 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
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
