<?php
/**
 * @version $Id: controller.php 5112 2012-11-08 23:59:29Z btowles $
 * @author RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.controller' );

/**
 * RokCandy Macros RokCandy Macro Controller
 *
 * @package		Joomla
 * @subpackage	RokCandy Macros
 * @since 1.5
 */


include_once(JPATH_COMPONENT_ADMINISTRATOR.'/helpers/legacy_class.php');

class RokCandyController extends RokCandyLegacyJController
{

    /**
	 * @var		string	The default view.
	 * @since	1.6
	 */
	protected $default_view = 'candymacros';

	public function display($cachable = false, $urlparams = false)
	{
		require_once JPATH_COMPONENT.'/helpers/rokcandy.php';
        $jinput = JFactory::getApplication()->input;


		// Load the submenu.
		RokCandyHelper::addSubmenu($jinput->get('view', 'candymacros'));

		$view	= $jinput->get('view', 'candymacros');
		$layout = $jinput->get('layout', 'default');
		$id		= $jinput->get('id', '', 'int');

		// Check for edit form.
		if ($view == 'candymacro' && $layout == 'edit' && !$this->checkEditId('com_rokcandy.edit.candymacro', $id)) {

			// Somehow the person just went to the form - we don't allow that.
			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
			$this->setMessage($this->getError(), 'error');
			$this->setRedirect(JRoute::_('index.php?option=com_rokcandy&view=candymacros', false));

			return false;
		}

		parent::display();

		return $this;
	}
}