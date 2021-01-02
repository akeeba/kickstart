<?php
/**
 * Akeeba Kickstart
 * An AJAX-powered archive extraction tool
 *
 * @package   kickstart
 * @copyright Copyright (c)2008-2021 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

/**
 * Akeeba Kickstart Import from URL add-on feature
 */
class AKFeatureURLImport
{
	private $params = array();

	/**
	 * Echoes extra CSS to the head of the page
	 */
	public function onExtraHeadCSS()
	{

	}

	/**
	 * Echoes extra Javascript to the head of the page
	 */
	public function onExtraHeadJavascript()
	{
		echo <<< JS
var akeeba_url_filename = null;
    
akeeba.System.documentReady(function() {
	akeeba.System.addEventListener('ak-url-showgui', 'click', function(e) {
        document.getElementById('ak-url-gui').style.display = 'block';
        document.getElementById('ak-url-progress').style.display = 'none';
        document.getElementById('ak-url-complete').style.display = 'none';
        document.getElementById('ak-url-error').style.display = 'none';
        document.getElementById('page1-content').style.display = 'none';
	});

    akeeba.System.addEventListener('ak-url-hidegui', 'click', function(e) {
        document.getElementById('ak-url-gui').style.display = 'none';
        document.getElementById('ak-url-progress').style.display = 'none';
        document.getElementById('ak-url-complete').style.display = 'none';
        document.getElementById('ak-url-error').style.display = 'none';
        document.getElementById('page1-content').style.display = 'block';
    });

    akeeba.System.addEventListener('ak-url-reload', 'click', function(e) {
        window.location.reload();
    });

    akeeba.System.addEventListener('ak-url-gotoStart', 'click', function(e) {
        document.getElementById('ak-url-gui').style.display = 'block';
        document.getElementById('ak-url-progress').style.display = 'none';
        document.getElementById('ak-url-complete').style.display = 'none';
        document.getElementById('ak-url-error').style.display = 'none';
    });
});

function onAKURLImport()
{
    akeeba_url_filename = document.getElementById('url.filename').value;
    ak_urlimport_start();
}

function AKURLsetProgressBar(percent)
{
    var newValue = percent;

	if (percent <= 1)
	{
		newValue = 100 * percent;
	}

    document.getElementById('ak-url-progressbar-inner').style.width = newValue + '%';
}

function ak_urlimport_start()
{
    akeeba_error_callback = AKURLerrorHandler;

    document.getElementById('ak-url-gui').style.display = 'none';
    document.getElementById('ak-url-progress').style.display = 'block';
    document.getElementById('ak-url-complete').style.display = 'none';
    document.getElementById('ak-url-error').style.display = 'none';

    AKURLsetProgressBar(0);
    
    document.getElementById('ak-url-progresstext').innerHTML = '';
    
    akeeba_next_step_post = {
        'task' : 'urlimport',
        'json' : JSON.stringify({
            'file'        : akeeba_url_filename,
            'frag'        : "-1",
            'totalSize'    : "-1"
        })
    };

    setTimeout(ak_urlimport_doStep, 10);
}

function ak_urlimport_doStep()
{
    akeeba.System.doAjax(akeeba_next_step_post, function(ret){
        ak_urlimport_step(ret);
    });
}

function ak_urlimport_step(data)
{
    // Look for errors
    if (!data.status)
    {
        AKURLerrorHandler(data.error);
        
        return;
    }
    
    var totalSize = 0;
    var doneSize = 0;
    var percent = 0;
    var frag = -1;
    
    // get running stats
    if (array_key_exists('totalSize', data))
    {
        totalSize = data.totalSize;
    }
    
    if (array_key_exists('doneSize', data))
    {
        doneSize = data.doneSize;
    }
    
    if (array_key_exists('percent', data))
    {
        percent = data.percent;
    }
    
    if (array_key_exists('frag', data))
    {
        frag = data.frag;
    }
    
    var humanDoneSize = doneSize;
    
    if (array_key_exists('humanDoneSize', data))
    {
        humanDoneSize = data.humanDoneSize;
    }
    
    // Update GUI
    AKURLsetProgressBar(percent);
    document.getElementById('ak-url-progresstext').innerText =  percent + '% (' + humanDoneSize  + ')';
    
    
    if (percent < 100) {
        akeeba_next_step_post = {
            'task'    : 'urlimport',
            'json'    : JSON.stringify({
                'file'        : akeeba_url_filename,
                'frag'        : frag,
                'totalSize'    : totalSize,
                'doneSize'  : doneSize
            })
        };
        
        setTimeout(ak_urlimport_doStep, 10);
        
        return;
    }
    
    // Done!
    document.getElementById('ak-url-gui').style.display = 'none';
    document.getElementById('ak-url-progress').style.display = 'none';
    document.getElementById('ak-url-complete').style.display = 'block';
    document.getElementById('ak-url-error').style.display = 'none';
}

function AKURLerrorHandler(msg)
{
    document.getElementById('ak-url-gui').style.display = 'none';
    document.getElementById('ak-url-progress').style.display = 'none';
    document.getElementById('ak-url-complete').style.display = 'none';
    document.getElementById('ak-url-error').style.display = 'block';

    document.getElementById('ak-url-errorMessage').innerHTML = msg;
}

JS;

	}

	/**
	 * Echoes extra HTML on page 1 of Kickstart
	 */
	public function onPage1()
	{

		?>
        <div id="ak-url-gui" style="display: none">
            <div class="step1">
                <div class="circle">1</div>
                <h2>AKURL_TITLE_STEP1</h2>
                <div class="area-container">
                    <label for="url.filename">AKURL_FILENAME</label>
                    <span class="field"><input type="text" style="width: 45%" id="url.filename" value=""/></span>

                    <div class="clr"></div>
                    <a id="ak-url-connect" class="button" onclick="onAKURLImport()">AKURL_IMPORT</a>
                    <a id="ak-url-hidegui" class="button bluebutton">AKURL_CANCEL</a>
                </div>
            </div>
            <div class="clr"></div>
        </div>

        <div id="ak-url-progress" style="display: none">
            <div class="circle">2</div>
            <h2>AKURL_TITLE_STEP2</h2>
            <div class="area-container">
                <div id="ak-url-importing">
                    <div class="warn-not-close">AKURL_DO_NOT_CLOSE</div>
                    <div id="ak-url-progressbar" class="progressbar">
                        <div id="ak-url-progressbar-inner" class="progressbar-inner">&nbsp;</div>
                    </div>
                    <div id="ak-url-progresstext"></div>
                </div>
            </div>
        </div>

        <div id="ak-url-complete" style="display: none">
            <div class="circle">3</div>
            <h2>AKURL_TITLE_STEP3</h2>
            <div class="area-container">
                <div id="ak-url-reload" class="button">AKURL_BTN_RELOAD</div>
            </div>
        </div>

        <div id="ak-url-error" class="error" style="display: none;">
            <h3>ERROR_OCCURED</h3>
            <p id="ak-url-errorMessage" class="errorMessage"></p>
            <div id="ak-url-gotoStart" class="button">BTN_GOTOSTART</div>
        </div>
		<?php
	}

	/**
	 * Outputs HTML to be shown before Step 1's archive selection pane
	 */
	public function onPage1Step1()
	{
		?>
        <a id="ak-url-showgui" class="button bluebutton loprofile">AKURL_IMPORT</a>
		<?php
	}

	public function urlimport($params)
	{
		$this->params = $params;

		// Fetch data
		$filename  = $this->getParam('file');
		$frag      = $this->getParam('frag', -1);
		$totalSize = $this->getParam('totalSize', -1);
		$doneSize  = $this->getParam('doneSize', -1);

		debugMsg('Importing from URL');
		debugMsg('  file      : ' . $filename);
		debugMsg('  frag      : ' . $frag);
		debugMsg('  totalSize : ' . $totalSize);
		debugMsg('  doneSize  : ' . $doneSize);

		// Init retArray
		$retArray = array(
			"status"    => true,
			"error"     => '',
			"frag"      => $frag,
			"totalSize" => $totalSize,
			"doneSize"  => $doneSize,
			"percent"   => 0,
            'humanDoneSize'  => $this->formatByteSize($doneSize)
		);

		try
		{
			AKFactory::set('kickstart.tuning.max_exec_time', '5');
			AKFactory::set('kickstart.tuning.run_time_bias', '75');
			$timer = new AKCoreTimer();
			$start = $timer->getRunningTime(); // Mark the start of this download
			$break = false; // Don't break the step

			while (($timer->getTimeLeft() > 0) && !$break)
			{
				// Figure out where on Earth to put that file
				$fileparts  = explode('?', $filename, 2);
				$local_file = KSROOTDIR . '/' . basename($fileparts[0]);

				debugMsg("- Importing from $filename");

				// Do we have to initialize the file?
				if ($frag == -1)
				{
					debugMsg("-- First frag, killing local file");
					// Currently downloaded size
					$doneSize = 0;

					// Delete and touch the output file
					@unlink($local_file);
					$fp = @fopen($local_file, 'wb');

					if ($fp !== false)
					{
						@fclose($fp);
					}

					// Init
					$frag = 0;
				}

				// Calculate from and length
				$length = 1048576;
				$from   = $frag * $length;
				$to     = $length + $from - 1;
				//if($from == 0) $from = 1;

				// Try to download the first frag
				$temp_file = $local_file . '.tmp';
				@unlink($temp_file);
				$required_time = 1.0;
				debugMsg("-- Importing frag $frag, byte position from/to: $from / $to");

				$filesize = 0;

				try
				{
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $filename);
					curl_setopt($ch, CURLOPT_RANGE, "$from-$to");
					curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					@curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

					if (defined('AKEEBA_CACERT_PEM'))
					{
						curl_setopt($ch, CURLOPT_CAINFO, AKEEBA_CACERT_PEM);
					}

					$result = curl_exec($ch);

					$errno       = curl_errno($ch);
					$errmsg      = curl_error($ch);
					$http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

					if ($result === false)
					{
						$error = "cURL error $errno: $errmsg";
					}
                    elseif ($http_status > 299)
					{
						$result = false;
						$error  = "HTTP status $http_status";
					}
					else
					{
						$result = file_put_contents($temp_file, $result);

						if ($result === false)
						{
							$error = "Could not open temporary file $temp_file for writing";
						}
					}

					curl_close($ch);
				}
				catch (Exception $e)
				{
					$error = $e->getMessage();
				}

				if (!$result)
				{
					@unlink($temp_file);

					// Failed download
					if ($frag == 0)
					{
						// Failure to download first frag = failure to download. Period.
						$retArray['status'] = false;
						$retArray['error']  = $error;

						debugMsg("-- Download FAILED");

						return $retArray;
					}
					else
					{
						// Since this is a staggered download, consider this normal and finish
						$frag = -1;
						debugMsg("-- Import complete");
						$doneSize = $totalSize;
						$break    = true;
						continue;
					}
				}

				// Add the currently downloaded frag to the total size of downloaded files
				if ($result)
				{
					clearstatcache();
					$filesize = (int) @filesize($temp_file);
					debugMsg("-- Successful download of $filesize bytes");
					$doneSize += $filesize;

					// Append the file
					$fp = @fopen($local_file, 'ab');

					if ($fp === false)
					{
						debugMsg("-- Can't open local file for writing");
						// Can't open the file for writing
						@unlink($temp_file);
						$retArray['status'] = false;
						$retArray['error']  = 'Can\'t write to the local file';

						return false;
					}

					$tf = fopen($temp_file, 'rb');

					while (!feof($tf))
					{
						$data = fread($tf, 262144);
						fwrite($fp, $data);
					}

					fclose($tf);
					fclose($fp);
					@unlink($temp_file);

					debugMsg("-- Temporary file merged and removed");

					if ($filesize > $length)
					{
						debugMsg("-- Read more data than the requested length. I assume this file is complete.");
						$break = true;
						$frag  = -1;
					}
                    elseif ($filesize < $length)
					{
						debugMsg("-- Read less data than the requested length. I assume this file is complete.");
						$break = true;
						$frag  = -1;
					}
					else
					{
						$frag++;

						debugMsg("-- Proceeding to next fragment, frag $frag");
					}
				}

				// Advance the frag pointer and mark the end
				$end = $timer->getRunningTime();

				// Do we predict that we have enough time?
				$required_time = max(1.1 * ($end - $start), $required_time);

				if ($required_time > (10 - $end + $start))
				{
					$break = true;
				}

				$start = $end;
			}

			if ($frag == -1)
			{
				$percent = 100;
			}
            elseif ($doneSize <= 0)
			{
				$percent = 0;
			}
			else
			{
				if ($totalSize > 0)
				{
					$percent = 100 * ($doneSize / $totalSize);
				}
				else
				{
					$percent = 0;
				}
			}

			// Update $retArray
			$retArray = array(
				"status"    => true,
				"error"     => '',
				"frag"      => $frag,
				"totalSize" => $totalSize,
				"doneSize"  => $doneSize,
				"percent"   => $percent,
                'humanDoneSize'  => $this->formatByteSize($doneSize)
			);
		}
		catch (Exception $e)
		{
			debugMsg("EXCEPTION RAISED:");
			debugMsg($e->getMessage());
			$retArray['status'] = false;
			$retArray['error']  = $e->getMessage();
		}

		return $retArray;
	}

	public function onLoadTranslations()
	{
		$translation = AKText::getInstance();
		$translation->addDefaultLanguageStrings(array(
			'AKURL_IMPORT'       => "Import from URL",
			'AKURL_TITLE_STEP1'  => "Specify the URL",
			'AKURL_FILENAME'     => "URL to import",
			'AKURL_JOOMLA'       => "Latest Joomla! release",
			'AKURL_WORDPRESS'    => "Latest WordPress release",
			'AKURL_CANCEL'       => "Cancel import",
			'AKURL_TITLE_STEP2'  => "Importing...",
			'AKURL_DO_NOT_CLOSE' => "Please do not close this window while your backup archives are being imported",
			'AKURL_TITLE_STEP3'  => "Import is complete",
			'AKURL_BTN_RELOAD'   => "Reload Kickstart",
		));
	}

	/**
	 * Formats a number of bytes in human readable format
	 *
	 * @param   int  $size  The size in bytes to format, e.g. 8254862
	 *
	 * @return  string  The human-readable representation of the byte size, e.g. "7.87 Mb"
	 */
	protected function formatByteSize($size)
	{
		$unit	 = array('b', 'KB', 'MB', 'GB', 'TB', 'PB');
		return @round($size / pow(1024, ($i	= floor(log($size, 1024)))), 2) . ' ' . $unit[$i];
	}

	private function getParam($key, $default = null)
	{
		if (array_key_exists($key, $this->params))
		{
			return $this->params[$key];
		}
		else
		{
			return $default;
		}
	}
}

