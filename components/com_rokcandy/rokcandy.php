<?php
/**
  * @version   $Id: rokcandy.php 5112 2012-11-08 23:59:29Z btowles $
  * @author    RocketTheme http://www.rockettheme.com
  * @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
  * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
  */
// no direct access
defined('_JEXEC') or die('Restricted access');

// Make sure the user is authorized to view this page
$user = JFactory::getUser();


// Get the media component configuration settings
$params =JComponentHelper::getParams('com_rokcandy');

// Require the base controller
require_once (JPATH_COMPONENT . '/controller.php');

$task = JFactory::getApplication()->input->get('task', null);


$controller = new RokCandyController();

// Set the model and view paths to the administrator folders
$controller->addViewPath(JPATH_COMPONENT_ADMINISTRATOR.'/views');
$controller->addModelPath(JPATH_COMPONENT_ADMINISTRATOR.'/models');

// Perform the Request task
$controller->execute($task);

// Redirect if set by the controller
$controller->redirect();
