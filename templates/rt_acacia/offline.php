<?php
/**
* @version   $Id: offline.php 14292 2013-10-08 11:13:09Z arifin $
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
$gantry->addLess('offline.less', 'offline.css', 7);

if ($gantry->browser->name == 'ie') {
	if ($gantry->browser->shortversion == 8) {
		$gantry->addScript('html5shim.js');
	}
}
$gantry->addScript('rokmediaqueries.js');

ob_start();
?>
<body <?php echo $gantry->displayBodyTag(); ?>>
	<div id="rt-showcase-surround">
		<div class="rt-container">
			<div id="rt-showcase">
				<div class="rt-showcase-pattern">
					<div class="rt-logo-block rt-offline-logo">
					    <a id="rt-logo" href=""></a>
					</div>					
					<div class="rt-block rt-offline-image">
						<h1 class="rt-sitename"><?php echo JText::_("RT_OFFLINE_TITLE"); ?></h1>
						<?php if ($app->getCfg('display_offline_message', 1) == 1 && str_replace(' ', '', $app->getCfg('offline_message')) != ''): ?>
						<h3>
							<?php echo $app->getCfg('offline_message'); ?>
						</h3>
							<?php elseif ($app->getCfg('display_offline_message', 1) == 2 && str_replace(' ', '', JText::_('JOFFLINE_MESSAGE')) != ''): ?>
						<h3>
							<?php echo JText::_('JOFFLINE_MESSAGE'); ?>
						</h3>
						<?php  endif; ?>						
						<?php if ($app->getCfg('offline_image')) : ?>
						<img src="<?php echo $app->getCfg('offline_image'); ?>" alt="<?php echo htmlspecialchars($app->getCfg('sitename')); ?>" />
						<?php endif; ?>

						<p class="rt-offline-additional-message">
							<?php echo JText::_("RT_OFFLINE_MESSAGE"); ?>
						</p>

						<form class="rt-offline-form" action="#">
							<input type="text" onblur="if(this.value=='') { this.value='<?php echo JText::_('RT_EMAIL') ?>'; return false; }" onfocus="if (this.value=='<?php echo JText::_('RT_EMAIL') ?>') this.value=''" value="<?php echo JText::_('RT_EMAIL') ?>" size="18" alt="<?php echo JText::_('RT_EMAIL') ?>" class="inputbox" name="email">
							<input type="submit" name="Submit" class="button" value="<?php echo JText::_('RT_GET_UPDATE') ?>" />
						</form>


						<h2 class="rt-login-form-title"><?php echo JText::_("AUTHORIZED_LOGIN"); ?></h2>
						<form class="rt-offline-login-form" action="<?php echo JRoute::_('index.php', true); ?>" method="post" id="form-login">
							<input name="username" id="username" class="inputbox" type="text" alt="<?php echo JText::_('JGLOBAL_USERNAME') ?>" onblur="if(this.value=='') { this.value='<?php echo JText::_('JGLOBAL_USERNAME') ?>'; return false; }" onfocus="if (this.value=='<?php echo JText::_('JGLOBAL_USERNAME') ?>') this.value=''" value="<?php echo JText::_('JGLOBAL_USERNAME') ?>" />
							<input type="password" name="password" class="inputbox" alt="<?php echo JText::_('JGLOBAL_PASSWORD') ?>" id="passwd" onblur="if(this.value=='') { this.value='<?php echo JText::_('JGLOBAL_PASSWORD') ?>'; return false; }" onfocus="if (this.value=='<?php echo JText::_('JGLOBAL_PASSWORD') ?>') this.value=''" value="<?php echo JText::_('JGLOBAL_PASSWORD') ?>" />
							<input type="hidden" name="remember" class="inputbox" value="yes" id="remember" />
							<input type="submit" name="Submit" class="button" value="<?php echo JText::_('JLOGIN') ?>" />
							<input type="hidden" name="option" value="com_users" />
							<input type="hidden" name="task" value="user.login" />
							<input type="hidden" name="return" value="<?php echo base64_encode(JURI::base()) ?>" />
							<?php echo JHtml::_('form.token'); ?>
						</form>

					</div>
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