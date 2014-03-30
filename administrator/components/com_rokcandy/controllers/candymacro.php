<?php
/**
 * @version $Id: candymacro.php 5112 2012-11-08 23:59:29Z btowles $
 * @author RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

class RokCandyControllerCandyMacro extends JControllerForm
{
	protected function allowAdd($data = array())
	{
		// Initialise variables.
		$user		= JFactory::getUser();
		$categoryId	= JArrayHelper::getValue($data, 'catid', JFactory::getApplication()->input->getInt('filter_category_id'), 'int');
		$allow		= null;

		if ($categoryId) {
			// If the category has been passed in the URL check it.
			$allow	= $user->authorise('core.create', $this->option.'.category.'.$categoryId);
		}

		if ($allow === null) {
			// In the absense of better information, revert to the component permissions.
			return parent::allowAdd($data);
		} else {
			return $allow;
		}
	}
    
    protected function allowEdit($data = array(), $key = 'id')
    {
        // Initialise variables.
        $user		= JFactory::getUser();
        $candymacroId	= (int) isset($data[$key]) ? $data[$key] : 0;
        $categoryId = 0;

        if ($candymacroId) {
            $categoryId = (int) $this->getModel()->getItem($candymacroId)->catid;
        }

        if ($categoryId) {
            // The category has been set. Check the category permissions.
            return $user->authorise('core.edit', $this->option.'.category.'.$categoryId);
        } else {
            // Since there is no asset tracking, revert to the component permissions.
            return parent::allowEdit($data, $key);
        }
    }

}