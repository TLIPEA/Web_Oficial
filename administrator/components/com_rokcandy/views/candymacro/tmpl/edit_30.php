<?php
/**
 * @version $Id: edit_30.php 5112 2012-11-08 23:59:29Z btowles $
 * @author RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

// no direct access
defined('_JEXEC') or die;

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('dropdown.init');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.formvalidation');
?>

<script type="text/javascript">
    Joomla.submitbutton = function (task) {
        if (task == 'candymacro.cancel' || document.formvalidator.isValid(document.id('adminForm'))) {
            Joomla.submitform(task, document.getElementById('adminForm'));
        }
        else {
            alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
        }
    }
</script>

<form action="<?php echo JRoute::_('index.php?option=com_rokcandy&layout=edit&id=' . (int)$this->item->id); ?>"
      method="post" name="adminForm" id="adminForm" class="form-validate form-horizontal">
    <div class="row-fluid">
        <!-- Begin RokMacros -->
        <div class="span10 form-horizontal">
            <fieldset>
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#details" data-toggle="tab">
                        <?php echo empty($this->item->id) ? JText::_('COM_ROKCANDY_NEW_MACRO') : JText::sprintf('COM_ROKCANDY_EDIT_MACRO', $this->item->id); ?></a>
                    </li>

                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="details">
                        <div class="control-group">
                            <div class="control-label">
                                <?php echo $this->form->getLabel('macro'); ?>
                            </div>
                            <?php echo JHtml::_('tooltip', JText::_('COM_ROKCANDY_TIP_DESC'), '', 'tooltip.png');?>
                            <div class="controls">
                                <?php echo $this->form->getInput('macro'); ?>
                            </div>
                        </div>

                        <div class="control-group">
                            <div class="control-label">
                                <?php echo $this->form->getLabel('html'); ?>
                            </div>
                            <?php echo JHtml::_('tooltip', JText::_('COM_ROKCANDY_TIP_DESC'), '', 'tooltip.png');?>
                            <div class="controls">
                                <?php echo $this->form->getInput('html'); ?>
                            </div>
                        </div>

                        <div class="control-group">
                            <div class="control-label"><?php echo $this->form->getLabel('catid'); ?>
                            </div>
                            <div class="controls"><?php echo $this->form->getInput('catid'); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </fieldset>
            <input type="hidden" name="task" value=""/>
            <?php echo JHtml::_('form.token'); ?>
        </div>
    <!-- End RokMacros -->
    <!-- Begin Sidebar -->
    <div class="span2">
        <h4><?php echo JText::_('JDETAILS');?></h4>
        <hr/>
        <fieldset class="form-vertical">
            <div class="control-group">
                <div class="control-group">
                    <div class="controls">
                        <?php echo $this->form->getValue('name'); ?>
                    </div>
                </div>
                <div class="control-label">
                    <?php echo $this->form->getLabel('published'); ?>
                </div>
                <div class="controls">
                    <?php echo $this->form->getInput('published'); ?>
                </div>
            </div>

            <div class="control-group">
                <div class="control-label"><?php echo $this->form->getLabel('ordering'); ?></div>
                <div class="controls"><?php echo $this->form->getInput('ordering'); ?></div>
            </div>

            <div class="control-group">
                <div class="control-label"><?php echo $this->form->getLabel('id'); ?></div>
                <div class="controls"><?php echo $this->form->getInput('id'); ?></div>
            </div>

        </fieldset>
    </div>
    <!-- End Sidebar -->
    </div>
</form>