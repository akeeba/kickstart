<?php
/**
 * Akeeba Kickstart
 * An AJAX-powered archive extraction tool
 *
 * @package   kickstart
 * @copyright Copyright (c)2008-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

/**
 * Used to repeat a web request under an easier to debug environment
 *
 * 1. Run Charles Proxy and enable recording AND the proxy (system or Firefox)
 * 2. Run Kickstart
 * 3. When it borks look at the Contents tab of the request in Charles Proxy
 * 4. Double click the value of the "json" parameter
 * 5. Copy all that stuff into the $json variable here
 */
$json = <<<JSON
{"factory":"SOME_BASE_64_ENCODED_STUFF_HERE"}
JSON;

define('KICKSTART', 1);
define('KSDEBUG', 1);
define('KSDEBUGCLI', 1);
define('VERBOSEOBSERVER', 1);

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

// The observer class, used to report number of files and bytes processed
class ExtractionObserver extends AKAbstractPartObserver
{
	public $compressedTotal   = 0;
	public $uncompressedTotal = 0;
	public $filesProcessed    = 0;

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

$json              = trim($json);
$jsonParams        = json_decode($json, true);
$serializedFactory = $jsonParams['factory'];

$observer = new ExtractionObserver();
$done     = false;

while (!$done)
{
	AKFactory::nuke();
	AKFactory::unserialize($serializedFactory);
	echo date('d/m/Y H:i:s') . " Tick\n";

	/** @var \AKAbstractUnarchiver $engine */
	$engine = \AKFactory::getUnarchiver();
	$engine->attach($observer);

	// TODO Comment out if you decide to detach the debugger and let everything run to their natural conclusion
	$timer = AKFactory::getTimer();
	$timer->setMaxExecTime(3600);

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

	AKFactory::getTimer()->resetTime();
	unset($engine);
}

echo "Done\n";
