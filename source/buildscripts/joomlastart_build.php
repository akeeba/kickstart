<?php
/**
 * @copyright   2008-2017 Nicholas K. Dionysopoulos / AkeebaBackup.com
 * @license     GNU GPL v2 or - at your option - any later version
 * @package     akeebabackup
 * @subpackage  kickstart
 */

define('MINIBUILD', __DIR__);
include_once __DIR__ . '/../minibuild.php';

$minibuild = new AkeebaMinibuild;

$buffer = $minibuild->minibuild(MINIBUILD . '/../joomlastart.build', true);
file_put_contents(__DIR__ . '/../output/joomlastart.php', $buffer);