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


require_once (JPATH_COMPONENT_ADMINISTRATOR.'/elements/categories.php' );

class RokCandyViewCandyMacros extends RokCandyLegacyJView
{
	protected $items;
	protected $pagination;
	protected $state;

	/**
	 * Display the view
	 *
	 * @return	void
	 */
	public function display($tpl = null)
	{
        $doc =  JFactory::getDocument();

        $this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');
        $this->template     = $this->get('Template');
        $this->overrides    = $this->get('TemplateOverrides');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		foreach ($this->items as &$item) {
			$item->order_up = true;
			$item->order_dn = true;
		}

        $doc->addStyleSheet(JURI::base(true).'/components/com_rokcandy/assets/rokcandy.css');

        $published = ($this->state->get('filter.published')==1 || $this->state->get('filter.published')=="" || $this->state->get('filter.published')=="*") ? true : false;
        $inCat = ($this->state->get('filter.category_id')=="" || $this->state->get('filter.category_id')==-1 ) ? true : false;
        $showOverrides = ($published && $inCat) ? true : false;

		$this->assign('showOverrides', $showOverrides);

        // We don't need toolbar in the modal window.
		if ($this->getLayout() !== 'modal') {
			$this->addToolbar();
            $version = new JVersion();
            if (version_compare($version->getShortVersion(), '3.0', '>=')) {
                $this->sidebar = JHtmlSidebar::render();
            }
		}
        
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		require_once JPATH_COMPONENT_ADMINISTRATOR.'/helpers/rokcandy.php';
        $option = JFactory::getApplication()->input->get('option');
        $view = JFactory::getApplication()->input->get('view');
        $lang   = JFactory::getLanguage();
		$canDo	= RokCandyHelper::getActions($this->state->get('filter.category_id'));
		$user	= JFactory::getUser();
		JToolBarHelper::title(JText::_('COM_ROKCANDY_MANAGER_MACRO'), 'rokcandy.png');

		if ($canDo->get('core.create') || (count($user->getAuthorisedCategories('com_rokcandy', 'core.create'))) > 0) {
			JToolBarHelper::addNew('candymacro.add');
		}

		if (($canDo->get('core.edit')) || ($canDo->get('core.edit.own'))) {
			JToolBarHelper::editList('candymacro.edit');
		}

		if ($canDo->get('core.edit.state')) {
			JToolBarHelper::divider();
			JToolBarHelper::publish('candymacros.publish', 'JTOOLBAR_PUBLISH', true);
			JToolBarHelper::unpublish('candymacros.unpublish', 'JTOOLBAR_UNPUBLISH', true);
			JToolBarHelper::divider();
			JToolBarHelper::archiveList('candymacros.archive');
			JToolBarHelper::checkin('candymacros.checkin');
		}

			if ($this->state->get('filter.published') == -2 && $canDo->get('core.delete')) {
			JToolBarHelper::deleteList('', 'candymacros.delete', 'JTOOLBAR_EMPTY_TRASH');
			JToolBarHelper::divider();
		}
		elseif ($canDo->get('core.edit.state')) {
			JToolBarHelper::trash('candymacros.trash');
			JToolBarHelper::divider();
		}

		if ($canDo->get('core.admin')) {
			JToolBarHelper::preferences('com_rokcandy');
			JToolBarHelper::divider();
		}

        JToolBarHelper::help(strtoupper($option).'_'.strtoupper($view).'_HELP_URL', TRUE, '', $option);

        $version = new JVersion();
        if (version_compare($version->getShortVersion(), '3.0', '>=')) {
            JHtmlSidebar::setAction('index.php?option=com_rokcandy');

            JHtmlSidebar::addFilter(
                JText::_('JOPTION_SELECT_PUBLISHED'),
                'filter_published',
                JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), 'value', 'text', $this->state->get('filter.published'), true)
            );

            JHtmlSidebar::addFilter(
                JText::_('JOPTION_SELECT_CATEGORY'),
                'filter_category_id',
                JHtml::_('select.options', JHtml::_('category.options', 'com_rokcandy'), 'value', 'text', $this->state->get('filter.category_id'))
            );
        }
	}

    /**
   	 * Returns an array of fields the table can be sorted by
   	 *
   	 * @return  array  Array containing the field name to sort by as the key and display text as value
   	 *
   	 * @since   3.0
   	 */
   	protected function getSortFields()
   	{
   		return array(
   			'a.ordering' => JText::_('JGRID_HEADING_ORDERING'),
   			'a.published' => JText::_('JSTATUS'),
   			'a.name' => JText::_('COM_ROKCANDY_MACRO'),
            'a.html' => JText::_('COM_ROKCANDY_HTML'),
   			'category_title' => JText::_('JCATEGORY'),
   			'a.id' => JText::_('JGRID_HEADING_ID')
   		);
   	}

}