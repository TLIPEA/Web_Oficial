<?php
/**
 * @version $Id: view.html.php 5112 2012-11-08 23:59:29Z btowles $
 * @author RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
jimport( 'joomla.application.component.view');

include_once(JPATH_COMPONENT_ADMINISTRATOR.'/helpers/legacy_class.php');


//require_once (JPATH_COMPONENT.'/helpers/rokcandy.php' );

class RokCandyViewCandyMacro extends RokCandyLegacyJView
{
    protected $form;
    protected $item;
    protected $state;

    /**
     * Display the view
     */
    public function display($tpl = null)
    {
        $doc =  JFactory::getDocument();

        // Initialiase variables.
        $this->form		= $this->get('Form');
        $this->item		= $this->get('Item');
        $this->state	= $this->get('State');

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            JError::raiseError(500, implode("\n", $errors));
            return false;
        }

        $doc->addStyleSheet('components/com_rokcandy/assets/rokcandy.css');

        $this->addToolbar();
        parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     *
     * @since	1.6
     */
    protected function addToolbar()
    {
        JFactory::getApplication()->input->set('hidemainmenu', 1);
        $option = JFactory::getApplication()->input->get('option');
        $view = JFactory::getApplication()->input->get('view');
        $lang       = JFactory::getLanguage();
        $user		= JFactory::getUser();
        $userId		= $user->get('id');
        $isNew		= ($this->item->id == 0);
        $checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $userId);
        $canDo		= RokCandyHelper::getActions($this->state->get('filter.category_id'));

        JToolBarHelper::title(JText::_('COM_ROKCANDY_MANAGER_MACRO'), 'rokcandy.png');

        // Build the actions for new and existing records.
        if ($isNew)  {
            // For new records, check the create permission.
            if ($isNew && (count($user->getAuthorisedCategories('com_rokcandy', 'core.create')) > 0)) {
                JToolBarHelper::apply('candymacro.apply');
                JToolBarHelper::save('candymacro.save');
                JToolBarHelper::save2new('candymacro.save2new');
            }

            JToolBarHelper::cancel('candymacro.cancel');
        }
        else {
            // Can't save the record if it's checked out.
            if (!$checkedOut) {
                // Since it's an existing record, check the edit permission, or fall back to edit own if the owner.
                if ($canDo->get('core.edit') || ($canDo->get('core.edit.own') && $this->item->created_by == $userId)) {
                    JToolBarHelper::apply('candymacro.apply');
                    JToolBarHelper::save('candymacro.save');

                    // We can save this record, but check the create permission to see if we can return to make a new one.
                    if ($canDo->get('core.create')) {
                        JToolBarHelper::save2new('candymacro.save2new');
                    }
                }
            }

            // If checked out, we can still save
            if ($canDo->get('core.create')) {
                JToolBarHelper::save2copy('candymacro.save2copy');
            }

            JToolBarHelper::cancel('candymacro.cancel', 'JTOOLBAR_CLOSE');
        }

        JToolBarHelper::divider();

		JToolBarHelper::help(strtoupper($option).'_'.strtoupper($view).'_HELP_URL', TRUE, '', $option);
    }
}
