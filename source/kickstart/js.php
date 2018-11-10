<?php
/**
 * Akeeba Kickstart
 * A JSON-powered archive extraction tool
 *
 * @copyright   Copyright (c)2008-2018 Nicholas K. Dionysopoulos / Akeeba Ltd
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
		var akeeba_pro       = <?php echo KICKSTARTPRO ? 1 : 0 ?>;
		var sftp_path        = '<?php echo TranslateWinPath(defined('KSROOTDIR') ? KSROOTDIR : dirname(__FILE__)); ?>/';
		var akeeba_ajax_url  = '<?php echo defined('KSSELFNAME') ? KSSELFNAME : basename(__FILE__); ?>';
		var default_temp_dir = '<?php echo addcslashes(AKKickstartUtils::getPath(), '\\\'"') ?>';
		var translation      = {
			<?php echoTranslationStrings(); ?>
		};
		var isJoomla         = true;

		//##MINIBUILD_JAVASCRIPT##

		<?php callExtraFeature('onExtraHeadJavascript'); ?>
    </script>
	<?php
}
