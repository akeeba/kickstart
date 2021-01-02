<?php
/**
 * Akeeba Kickstart
 * An AJAX-powered archive extraction tool
 *
 * @package   kickstart
 * @copyright Copyright (c)2008-2021 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

define('KICKSTART', 1);
define('KSDEBUG', 1);
//define('KSDEBUGCLI', 1);
//define('VERBOSEOBSERVER', 1);

require_once __DIR__ . '/../restore/preamble.php';
require_once __DIR__ . '/../restore/abstract.object.php';
require_once __DIR__ . '/../restore/abstract.part.php';
require_once __DIR__ . '/../restore/abstract.unarchiver.php';
require_once __DIR__ . '/../restore/abstract.postproc.php';
require_once __DIR__ . '/../restore/abstract.part.observer.php';
require_once __DIR__ . '/../restore/postproc.direct.php';
require_once __DIR__ . '/../restore/postproc.ftp.php';
require_once __DIR__ . '/../restore/postproc.sftp.php';
require_once __DIR__ . '/../restore/postproc.hybrid.php';
require_once __DIR__ . '/../restore/unarchiver.jpa.php';
require_once __DIR__ . '/../restore/unarchiver.zip.php';
require_once __DIR__ . '/../restore/unarchiver.jps.php';
require_once __DIR__ . '/../restore/core.timer.php';
require_once __DIR__ . '/../restore/utils.lister.php';
require_once __DIR__ . '/../restore/text.php';
require_once __DIR__ . '/../restore/factory.php';
require_once __DIR__ . '/../restore/encryption.interface.php';
require_once __DIR__ . '/../restore/encryption.adapter.php';
require_once __DIR__ . '/../restore/encryption.mcrypt.php';
require_once __DIR__ . '/../restore/encryption.openssl.php';
require_once __DIR__ . '/../restore/encryption.aes.php';
require_once __DIR__ . '/../restore/mastersetup.php';
require_once __DIR__ . '/../restore/application.php';

$sourcefile = $argv[1];
$fileInfo = new SplFileInfo($sourcefile);

$targetPath = isset($argv[2]) ? $argv[2] : null;

$ksOptions  = array(
	'kickstart.tuning.max_exec_time' => 5,
	'kickstart.tuning.run_time_bias' => 75,
	'kickstart.tuning.min_exec_time' => 0,
	'kickstart.procengine' => 'direct',
	'kickstart.setup.sourcefile' => $sourcefile,
	'kickstart.setup.destdir' => empty($targetPath) ? sys_get_temp_dir() : $targetPath,
	'kickstart.setup.restoreperms' => '0',
	'kickstart.setup.filetype' => strtolower($fileInfo->getExtension()),
	'kickstart.setup.dryrun' => empty($targetPath) ? 1 : 0,
	'kickstart.jps.password' => 'test',
	'kickstart.setup.extract_list' => 'installation/README.html, images/*',
);

// The observer class, used to report number of files and bytes processed
class RestorationObserver extends AKAbstractPartObserver
{
	public $compressedTotal = 0;
	public $uncompressedTotal = 0;
	public $filesProcessed = 0;

	public function update($object, $message)
	{
		if (!is_object($message))
		{
			return;
		}

		if (!array_key_exists('type', get_object_vars($message)))
		{
			return;
		}

		if ($message->type == 'startfile')
		{
			if (defined('VERBOSEOBSERVER'))
			{
				echo "\tReal file:    {$message->content->realfile}\n";
				echo "\tFile:         {$message->content->file}\n";
				echo "\tCompressed:   {$message->content->compressed}\n";
				echo "\tUncompressed: {$message->content->uncompressed}\n";
			}

			$this->filesProcessed++;
			$this->compressedTotal += $message->content->compressed;
			$this->uncompressedTotal += $message->content->uncompressed;
		}
	}

	/**
	 * @return int
	 */
	public function getCompressedTotal()
	{
		return $this->compressedTotal;
	}

	/**
	 * @return int
	 */
	public function getUncompressedTotal()
	{
		return $this->uncompressedTotal;
	}

	/**
	 * @return int
	 */
	public function getFilesProcessed()
	{
		return $this->filesProcessed;
	}

	public function __toString()
	{
		return __CLASS__;
	}

}

AKFactory::nuke();

foreach ($ksOptions as $k => $v)
{
	AKFactory::set($k, $v);
}

AKFactory::set('kickstart.enabled', true);
/** @var \AKAbstractUnarchiver $engine */
$engine = \AKFactory::getUnarchiver();
$observer = new RestorationObserver();

$done = false;

while (!$done)
{
	echo date('d/m/Y H:i:s') . " Tick\n";

	$engine = \AKFactory::getUnarchiver();
	$engine->attach($observer);

	$engine->tick();
	$ret = $engine->getStatusArray();

	if ($ret['Error'] != '')
	{
		echo "ERROR\n" . $ret['Error'] . "\n";

		unlink(__DIR__ . '/debug.txt');

		die;
	}

	if (!$ret['HasRun'])
	{
		break;
	}

	echo "Compressed:   " . $observer->getCompressedTotal() . "\n";
	echo "Uncompressed: " . $observer->getUncompressedTotal() . "\n";
	echo "Processed:    " . $observer->getFilesProcessed() . "\n";

	$serializedFactory = AKFactory::serialize();

	AKFactory::nuke();
	AKFactory::unserialize($serializedFactory);
	AKFactory::getTimer()->resetTime();
	unset($engine);
}

echo "Done\n";
