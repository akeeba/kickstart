<?php
/**
 * Akeeba Kickstart
 * A JSON-powered archive extraction tool
 *
 * @copyright Copyright (c)2008-2018 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL v2 or - at your option - any later version
 * @package     akeebabackup
 * @subpackage  kickstart
 */

function echoHeadJavascript()
{
	?>
	<script type="text/javascript" language="javascript">
        var akeeba = {};

		var akeeba_debug     = <?php echo defined('KSDEBUG') ? 'true' : 'false' ?>;
		var sftp_path        = '<?php echo TranslateWinPath(defined('KSROOTDIR') ? KSROOTDIR : dirname(__FILE__)); ?>/';
        var akeeba_ajax_url  = '<?php echo defined('KSSELFNAME') ? KSSELFNAME : basename(__FILE__); ?>';
		var default_temp_dir = '<?php echo addcslashes(AKKickstartUtils::getPath(), '\\\'"') ?>';
		var translation      = {
			<?php echoTranslationStrings(); ?>
		};
		var isJoomla         = true;

//##MINIBUILD_JAVASCRIPT##

		function onStartExtraction()
		{
			$('#page1').hide('fast');
			$('#page2').show('fast');

			$('#currentFile').text('');

			akeeba_error_callback = errorHandler;

			var zapBefore = 0;

			if ($('#kickstart\\.setup\\.zapbefore').length == 1)
            {
				zapBefore = $('#kickstart\\.setup\\.zapbefore').is(':checked');
			}

			var data = {
				'task': 'startExtracting',
				'json': JSON.stringify({
					'kickstart.setup.sourcepath':     $('#kickstart\\.setup\\.sourcepath').val(),
					'kickstart.setup.sourcefile':     $('#kickstart\\.setup\\.sourcefile').val(),
					'kickstart.jps.password':         $('#kickstart\\.jps\\.password').val(),
					'kickstart.tuning.min_exec_time': $('#kickstart\\.tuning\\.min_exec_time').val(),
					'kickstart.tuning.max_exec_time': $('#kickstart\\.tuning\\.max_exec_time').val(),
					'kickstart.stealth.enable':       $('#kickstart\\.stealth\\.enable').is(':checked'),
					'kickstart.stealth.url':          $('#kickstart\\.stealth\\.url').val(),
					'kickstart.setup.zapbefore':      zapBefore,
					'kickstart.tuning.run_time_bias': 75,
					'kickstart.setup.restoreperms':   $('#kickstart\\.restorepermissions\\.enable').is(':checked'),
					'kickstart.setup.dryrun':         0,
					'kickstart.setup.ignoreerrors':   $('#kickstart\\.setup\\.ignoreerrors').is(':checked'),
					'kickstart.enabled':              1,
					'kickstart.security.password':    '',
					'kickstart.setup.renameback':     $('#kickstart\\.setup\\.renameback').is(':checked'),
					'kickstart.procengine':           $('#kickstart\\.procengine').val(),
					'kickstart.ftp.host':             $('#kickstart\\.ftp\\.host').val(),
					'kickstart.ftp.port':             $('#kickstart\\.ftp\\.port').val(),
					'kickstart.ftp.ssl':              $('#kickstart\\.ftp\\.ssl').is(':checked'),
					'kickstart.ftp.passive':          $('#kickstart\\.ftp\\.passive').is(':checked'),
					'kickstart.ftp.user':             $('#kickstart\\.ftp\\.user').val(),
					'kickstart.ftp.pass':             $('#kickstart\\.ftp\\.pass').val(),
					'kickstart.ftp.dir':              $('#kickstart\\.ftp\\.dir').val(),
					'kickstart.ftp.tempdir':          $('#kickstart\\.ftp\\.tempdir').val(),
					'kickstart.setup.extract_list':   $('#kickstart\\.setup\\.extract_list').val()
				})
			};
			akeeba.System.doAjax(data, function (ret)
			{
				processRestorationStep(ret);
			});
		}

		function processRestorationStep(data)
		{
			// Look for errors
			if (!data.status)
			{
				errorHandler(data.message);
				return;
			}

			// Propagate warnings to the GUI
			if (!empty(data.Warnings))
			{
				$.each(data.Warnings, function (i, item)
				{
					$('#warnings').append(
						$(document.createElement('div'))
							.html(item)
					);
					$('#warningsBox').show('fast');
				});
			}

			// Parse total size, if exists
			if (array_key_exists('totalsize', data))
			{
				if (is_array(data.filelist))
				{
					akeeba_restoration_stat_total = 0;
					$.each(data.filelist, function (i, item)
					{
						akeeba_restoration_stat_total += item[1];
					});
				}
				akeeba_restoration_stat_outbytes = 0;
				akeeba_restoration_stat_inbytes  = 0;
				akeeba_restoration_stat_files    = 0;
			}

			// Update GUI
			akeeba_restoration_stat_inbytes += data.bytesIn;
			akeeba_restoration_stat_outbytes += data.bytesOut;
			akeeba_restoration_stat_files += data.files;
			var percentage = 0;
			if (akeeba_restoration_stat_total > 0)
			{
				percentage = 100 * akeeba_restoration_stat_inbytes / akeeba_restoration_stat_total;
				if (percentage < 0)
				{
					percentage = 0;
				}
				else if (percentage > 100)
				{
					percentage = 100;
				}
			}
			if (data.done) percentage = 100;
			setProgressBar(percentage);
			$('#currentFile').text(data.lastfile);

			if (!empty(data.factory)) akeeba_factory = data.factory;

			post = {
				'task': 'continueExtracting',
				'json': JSON.stringify({factory: akeeba_factory})
			};

			if (!data.done)
			{
				akeeba.System.doAjax(post, function (ret)
				{
					processRestorationStep(ret);
				});
			}
			else
			{
				$('#page2a').hide('fast');
				$('#extractionComplete').show('fast');

				$('#runInstaller').css('display', 'inline-block');
			}
		}

		function onGotoStartClick(event)
		{
			$('#page2').hide('fast');
			$('#error').hide('fast');
			$('#page1').show('fast');
		}

		function onRunInstallerClick(event)
		{
			var windowReference = window.open('installation/index.php', 'installer');
			if (!windowReference.opener)
			{
				windowReference.opener = this.window;
			}
			$('#runCleanup').css('display', 'inline-block');
			$('#runInstaller').hide('fast');
		}

		function onRunCleanupClick(event)
		{
			post = {
				'task': 'isJoomla',
				// Passing the factory preserves the renamed files array
				'json': JSON.stringify({factory: akeeba_factory})
			};

			akeeba.System.doAjax(post, function (ret)
			{
				isJoomla = ret;
				onRealRunCleanupClick();
			});
		}

		function onRealRunCleanupClick()
		{
			post = {
				'task': 'cleanUp',
				// Passing the factory preserves the renamed files array
				'json': JSON.stringify({factory: akeeba_factory})
			};

			akeeba.System.doAjax(post, function (ret)
			{
				$('#runCleanup').hide('fast');
				$('#gotoSite').css('display', 'inline-block');
				if (isJoomla)
				{
					$('#gotoAdministrator').css('display', 'inline-block');
				}
				else
				{
					$('#gotoAdministrator').css('display', 'none');
				}
				$('#gotoPostRestorationRroubleshooting').css('display', 'block');
			});

		}

		function errorHandler(msg)
		{
			$('#errorMessage').html(msg);
			$('#error').show('fast');
		}



		/**
		 * Akeeba Kickstart Update Check
		 */

		var akeeba_update  = {version: '0'};
		var akeeba_version = '##VERSION##';

		function checkUpdates()
		{
			var structure =
			    {
				    type:        "GET",
				    url:         'http://query.yahooapis.com/v1/public/yql',
				    data:        {
					    <?php if(KICKSTARTPRO): ?>
					    q:        'SELECT * FROM xml WHERE url="http://nocdn.akeebabackup.com/updates/kickstart.xml"',
					    <?php else: ?>
					    q:        'SELECT * FROM xml WHERE url="http://nocdn.akeebabackup.com/updates/kickstartpro.xml"',
					    <?php endif; ?>
					    format:   'json',
					    callback: 'updatesCallback'
				    },
				    cache:       true,
				    crossDomain: true,
				    jsonp:       'updatesCallback',
				    timeout:     15000
			    };
			$.ajax(structure);
		}

		function updatesCallback(msg)
		{
			$.each(msg.query.results.updates.update, function (i, el)
			{
				var myUpdate = {
					'version': el.version,
					'infourl': el.infourl['content'],
					'dlurl':   el.downloads.downloadurl.content
				}
				if (version_compare(myUpdate.version, akeeba_update.version, 'ge'))
				{
					akeeba_update = myUpdate;
				}
			});

			if (version_compare(akeeba_update.version, akeeba_version, 'gt'))
			{
				notifyAboutUpdates();
			}
		}

		function notifyAboutUpdates()
		{
			$('#update-version').text(akeeba_update.version);
			$('#update-dlnow').attr('href', akeeba_update.dlurl);
			$('#update-whatsnew').attr('href', akeeba_update.infourl);
			$('#update-notification').show('slow');
		}

		<?php callExtraFeature('onExtraHeadJavascript'); ?>
	</script>
	<?php
}
