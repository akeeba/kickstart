<?php
define('KICKSTART', 1);
define('KSDEBUG', 1);
define('KSDEBUGCLI', 1);

require_once __DIR__ . '/../restore/preamble.php';
require_once __DIR__ . '/../restore/json_services.php';
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
require_once __DIR__ . '/../restore/encryption.aes.php';
require_once __DIR__ . '/../restore/mastersetup.php';
require_once __DIR__ . '/../restore/application.php';

$sourcefile = $argv[1];
$fileInfo = new SplFileInfo($sourcefile);

$ksOptions  = array(
	'kickstart.tuning.max_exec_time' => 5,
	'kickstart.tuning.run_time_bias' => 75,
	'kickstart.tuning.min_exec_time' => 0,
	'kickstart.procengine' => 'direct',
	'kickstart.setup.sourcefile' => $sourcefile,
	'kickstart.setup.destdir' => sys_get_temp_dir(),
	'kickstart.setup.restoreperms' => '0',
	'kickstart.setup.filetype' => strtolower($fileInfo->getExtension()),
	'kickstart.setup.dryrun' => '1',
	'kickstart.jps.password' => 'test'
);

// The observer class, used to report number of files and bytes processed
class RestorationObserver extends AKAbstractPartObserver
{
	public $compressedTotal = 0;
	public $uncompressedTotal = 0;
	public $filesProcessed = 0;

	public function update($object, $message)
	{
		if(!is_object($message)) return;

		if( !array_key_exists('type', get_object_vars($message)) ) return;

		if( $message->type == 'startfile' )
		{
			echo "\tReal file:    {$message->content->realfile}\n";
			echo "\tFile:         {$message->content->file}\n";
			echo "\tCompressed:   {$message->content->compressed}\n";
			echo "\tUncompressed: {$message->content->uncompressed}\n";

			$this->filesProcessed++;
			$this->compressedTotal += $message->content->compressed;
			$this->uncompressedTotal += $message->content->uncompressed;
		}
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
$engine->attach($observer);

$done = false;

while (!$done)
{
	echo "Tick\n";

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

	AKFactory::getTimer()->resetTime();
}

echo "Done\n";
