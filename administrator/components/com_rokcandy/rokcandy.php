<?php
/**
 * @version $Id: rokcandy.php 5112 2012-11-08 23:59:29Z btowles $
 * @author RocketTheme, LLC http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

// no direct access
defined('_JEXEC') or die;

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_rokcandy')) {
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

// Include dependancies
jimport('joomla.application.component.controller');

include_once(JPATH_COMPONENT_ADMINISTRATOR.'/helpers/legacy_class.php');

$controller	= RokCandyLegacyJController::getInstance('rokcandy');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();