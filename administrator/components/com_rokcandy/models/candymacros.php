<?php
/**
 * @version $Id: candymacros.php 5112 2012-11-08 23:59:29Z btowles $
 * @author RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

require_once (JPATH_COMPONENT_ADMINISTRATOR.'/helpers/rokcandy.php' );

class RokCandyModelCandyMacros extends JModelList
{
    public function __construct($config = array())
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                'id', 'a.id',
                'macro', 'a.macro',
                'html', 'a.html',
                'checked_out', 'a.checked_out',
                'checked_out_time', 'a.checked_out_time',
                'catid', 'a.catid', 'category_title',
                'published', 'a.published',
                'ordering', 'a.ordering',
                'params', 'a.params',
            );
        }

        parent::__construct($config);
    }

    protected function populateState($ordering = null, $direction = null)
    {
        // Initialise variables.
        $app = JFactory::getApplication();

        // Adjust the context to support modal layouts.
        if ($layout = JFactory::getApplication()->input->get('layout')) {
            $this->context .= '.'.$layout;
        }

        $search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
        $this->setState('filter.search', $search);

        $published = $this->getUserStateFromRequest($this->context.'.filter.published', 'filter_published', '');
        $this->setState('filter.published', $published);

        $categoryId = $this->getUserStateFromRequest($this->context.'.filter.category_id', 'filter_category_id');
        $this->setState('filter.category_id', $categoryId);

        // List state information.
        parent::populateState('a.ordering', 'asc');
    }

    protected function getStoreId($id = '')
    {
        // Compile the store id.
        $id	.= ':'.$this->getState('filter.search');
        $id	.= ':'.$this->getState('filter.published');
        $id	.= ':'.$this->getState('filter.category_id');

        return parent::getStoreId($id);
    }

    protected function getListQuery()
    {
        // Create a new query object.
        $db		= $this->getDbo();
        $query	= $db->getQuery(true);
        $user	= JFactory::getUser();

        // Select the required fields from the table.
        $query->select(
            $this->getState(
                'list.select',
                'a.id, a.macro, a.html, a.checked_out, a.checked_out_time, a.catid, a.published, a.ordering'
            )
        );
        $query->from('#__rokcandy AS a');

        // Join over the users for the checked out user.
        $query->select('uc.name AS editor');
        $query->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');

        // Join over the categories.
        $query->select('c.title AS category_title');
        $query->join('LEFT', '#__categories AS c ON c.id = a.catid');

        // Filter by access level.
        if ($access = $this->getState('filter.access')) {
            $query->where('a.access = ' . (int) $access);
        }

        // Filter by published state
        $published = $this->getState('filter.published');
        if (is_numeric($published)) {
            $query->where('a.published = ' . (int) $published);
        }
        elseif ($published === '') {
            $query->where('(a.published = 0 OR a.published = 1)');
        }

        // Filter by a single or group of categories.
        $categoryId = $this->getState('filter.category_id');
        if (is_numeric($categoryId)) {
            $query->where('a.catid = '.(int) $categoryId);
        }
        elseif (is_array($categoryId)) {
            JArrayHelper::toInteger($categoryId);
            $categoryId = implode(',', $categoryId);
            $query->where('a.catid IN ('.$categoryId.')');
        }

        // Filter by search in name.
        $search = $this->getState('filter.search');
        if (!empty($search)) {
            if (stripos($search, 'id:') === 0) {
                $query->where('a.id = '.(int) substr($search, 3));
            }
            else {
                $search = $db->Quote('%'.$db->escape($search, true).'%');
                $query->where('(a.macro LIKE '.$search.' OR a.html LIKE '.$search.')');
            }
        }

        // Add the list ordering clause.
        $orderCol	= $this->state->get('list.ordering');
        $orderDirn	= $this->state->get('list.direction');
        if ($orderCol == 'a.ordering' || $orderCol == 'category_title') {
            $orderCol = 'category_title '.$orderDirn.', a.ordering';
        }
        $query->order($db->escape($orderCol.' '.$orderDirn));

        //echo nl2br(str_replace('#__','jos_',$query));
        return $query;
    }


	function getTemplate()
	{
	    return RokCandyHelper::getCurrentTemplate();
	}

	function getTemplateOverrides()
	{
        return RokCandyHelper::getTemplateOverrides();

	}
}
