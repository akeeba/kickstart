<?php
/**
 * Akeeba Kickstart
 * An AJAX-powered archive extraction tool
 *
 * @package   kickstart
 * @copyright Copyright (c)2008-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
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
