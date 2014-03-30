<?php
/**
* @version   $Id: comingsoon.php 14292 2013-10-08 11:13:09Z arifin $
* @author    RocketTheme http://www.rockettheme.com
* @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*
* Gantry uses the Joomla Framework (http://www.joomla.org), a GNU/GPLv2 content management system
*
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

// load and inititialize gantry class
require_once(dirname(__FILE__) . '/lib/gantry/gantry.php');
$gantry->init();

$doc = JFactory::getDocument();
$app = JFactory::getApplication();

$gantry->addStyle('grid-responsive.css', 5);
$gantry->addLess('bootstrap.less', 'bootstrap.css', 6);
$gantry->addLess('comingsoon.less', 'comingsoon.css', 7);

if ($gantry->browser->name == 'ie') {
	if ($gantry->browser->shortversion == 8) {
		$gantry->addScript('html5shim.js');
	}
}
$gantry->addScript('rokmediaqueries.js');

$gantry->addScript('simplecounter.js');

ob_start();
?>
<body <?php echo $gantry->displayBodyTag(); ?>>
	<div id="rt-showcase-surround">
		<div class="rt-container">
			<div id="rt-showcase">
				<div class="rt-showcase-pattern">
					<div class="rt-logo-block rt-comingsoon-logo">
					    <a id="rt-logo" href=""></a>
					</div>					
					<div class="rt-block">
						<h1 class="rt-sitename"><?php echo JText::_("RT_COMINGSOON_TITLE"); ?></h1>
					</div>					
					<div class="rt-block rt-counter-block">
						<div id="rt-comingsoon-counter"></div>
						<script type="text/javascript">
							/* Year (full year), Month (0 to 11), Day (1, 31) */
							/* For example: Date(2013,9,1) means 1 October 2013 */
							var counter = new SimpleCounter('rt-comingsoon-counter',new Date(2013,9,1));
						</script>						
					</div>
					<p class="rt-comingsoon-additional-message">
						<?php echo JText::_("RT_COMINGSOON_MESSAGE"); ?>
					</p>
					<form class="rt-comingsoon-form" action="#">
						<input type="text" onblur="if(this.value=='') { this.value='<?php echo JText::_('RT_EMAIL') ?>'; return false; }" onfocus="if (this.value=='<?php echo JText::_('RT_EMAIL') ?>') this.value=''" value="<?php echo JText::_('RT_EMAIL') ?>" size="18" alt="<?php echo JText::_('RT_EMAIL') ?>" class="inputbox" name="email">
						<a href="#" class="readon"><?php echo JText::_("RT_SUBSCRIBE"); ?> <span class="icon-signin"></span></a>
					</form>

					<div class="clear"></div>					
				</div>
			</div>
		</div>
	</div>	
	<footer id="rt-footer-surround">
		<div class="rt-footer-surround-pattern">
			<?php /** Begin Copyright **/ if ($gantry->countModules('copyright')) : ?>
			<div id="rt-copyright">
				<div class="rt-container">
					<?php echo $gantry->displayModules('copyright','standard','standard'); ?>
					<div class="clear"></div>
				</div>
			</div>
			<?php /** End Copyright **/ endif; ?>	
		</div>
	</footer>		
</body>


</html>
<?php

$body = ob_get_clean();
$gantry->finalize();

require_once(JPATH_LIBRARIES.'/joomla/document/html/renderer/head.php');
$header_renderer = new JDocumentRendererHead($doc);
$header_contents = $header_renderer->render(null);
ob_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>
	<?php echo $header_contents; ?>
	<?php if ($gantry->get('layout-mode') == '960fixed') : ?>
	<meta name="viewport" content="width=960px">
	<?php elseif ($gantry->get('layout-mode') == '1200fixed') : ?>
	<meta name="viewport" content="width=1200px">
	<?php else : ?>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<?php endif; ?>
</head>
<?php
$header = ob_get_clean();
echo $header.$body;