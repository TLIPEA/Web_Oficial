<?php
/**
 *  @package AdminTools
 *  @copyright Copyright (c)2010-2014 Nicholas K. Dionysopoulos
 *  @license GNU General Public License version 3, or later
 *  @version $Id$
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

$option = 'com_admintools';
$os = strtoupper(PHP_OS);
$isWindows = substr($os,0,3) == 'WIN';

$script = <<<ENDSCRIPT
window.addEvent( 'domready' ,  function() {
	$('protect').addEvent('click',function(e){
		e.preventDefault();
		$('task').setProperty('value','protect');
		document.forms.adminForm.submit();
	});
	$('unprotect').addEvent('click',function(e){
		e.preventDefault();
		$('task').setProperty('value','unprotect');
		document.forms.adminForm.submit();
	});
});
ENDSCRIPT;
$document = JFactory::getDocument();
$document->addScriptDeclaration($script,'text/javascript');

if(version_compare(JVERSION, '3.0', 'ge')) {
	JHTML::_('behavior.framework');
} else {
	JHTML::_('behavior.mootools');
}

?>
<?php if($isWindows): ?>
<div class="alert">
	<a class="close" data-dismiss="alert" href="#">×</a>
	<h3><?php echo JText::_('ATOOLS_LBL_ADMINPW_WINDETECTED'); ?></h3>
	<p><?php echo JText::_('ATOOLS_LBL_ADMINPW_NOTAVAILONWINDOWS'); ?></p>
</div>
<?php endif; ?>

<p class="alert alert-info">
	<?php echo JText::_('ATOOLS_LBL_ADMINPW_INTRO'); ?>
</p>
<p class="alert">
	<?php echo JText::_('ATOOLS_LBL_ADMINPW_WARN'); ?>
</p>

<form action="index.php" name="adminForm" id="adminForm" method="post" class="form form-horizontal">
	<input type="hidden" name="option" value="com_admintools" />
	<input type="hidden" name="view" value="adminpw" />
	<input type="hidden" name="task" id="task" value="" />
	<input type="hidden" name="<?php echo JFactory::getSession()->getFormToken();?>" value="1" />

	<p class="help-block"><?php echo JText::_('ATOOLS_LBL_ADMINPW_INFO'); ?></p>
	
	<div class="control-group">
		<label class="control-label" for="username"><?php echo JText::_('ATOOLS_LBL_ADMINPW_USERNAME') ?></label>
		<div class="controls">
			<input type="text" name="username" id="username" value="<?php echo $this->username ?>" autocomplete="off" />
		</div>
    </div>

	<div class="control-group">
		<label class="control-label" for="password"><?php echo JText::_('ATOOLS_LBL_ADMINPW_PASSWORD') ?></label>
		<div class="controls">
			<input type="password" name="password" id="password" value="<?php echo $this->password?>" autocomplete="off" />
		</div>
    </div>

	<div class="control-group">
		<label class="control-label" for="password2"><?php echo JText::_('ATOOLS_LBL_ADMINPW_PASSWORD2') ?></label>
		<div class="controls">
			<input type="password" name="password2" id="password2" value="<?php echo $this->password?>" autocomplete="off" />
		</div>
    </div>

	<div class="form-actions">
		<input type="submit" class="btn btn-warning" id="protect" value="<?php echo JText::_('ATOOLS_LBL_ADMINPW_PROTECT') ?>" />
		<?php if($this->adminLocked): ?>
		&nbsp;&nbsp;
		<input type="submit" class="btn btn-success" id="unprotect" value="<?php echo JText::_('ATOOLS_LBL_ADMINPW_UNPROTECT') ?>" />
		<?php endif; ?>
	</div>
</form>