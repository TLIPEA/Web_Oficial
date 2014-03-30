<?php
/**
  * @version   $Id: controller.php 7081 2013-02-01 04:28:52Z steph $
  * @author    RocketTheme http://www.rockettheme.com
  * @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
  * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
  */
// no direct access
defined('_JEXEC') or die('Restricted access'); 
include_once(JPATH_COMPONENT_ADMINISTRATOR.'/helpers/legacy_class.php');

jimport( 'joomla.application.component.controller' ); 
 
class RokCandyController extends RokCandyLegacyJController
{
    function __construct($config = array())
	{
		// RokGallery image picker proxying:
		if (JFactory::getApplication()->input->get('view') === 'candymacros' &JFactory::getApplication()->input->get('layout') === 'list') {
            JHtml::_('stylesheet','system/adminlist.css', array(), true);
			$config['base_path'] = JPATH_COMPONENT_ADMINISTRATOR;
		}
        // Article frontpage Editor article proxying:
		elseif(JFactory::getApplication()->input->get('view') === 'candymacros' &JFactory::getApplication()->input->get('layout') === 'default') {
			JHtml::_('stylesheet','system/adminlist.css', array(), true);
			$config['base_path'] = JPATH_COMPONENT_ADMINISTRATOR;
		}

		parent::__construct($config);
	}

	function display($cachable = false, $urlparams = array())
	{
		$vName = JFactory::getApplication()->input->get('task', 'default');
		switch ($vName)
		{
			case 'default':
			default:
				$vLayout = JFactory::getApplication()->input->get( 'layout', 'default' );
				$mName = 'candymacros';
				$vName = 'candymacros';

				break;
		}

		$document = JFactory::getDocument();
		$vType		= $document->getType();

		// Get/Create the view
		$view = $this->getView( $vName, $vType);
		$view->addTemplatePath(JPATH_COMPONENT_ADMINISTRATOR.'/views/'.strtolower($vName).'/tmpl');

		// Get/Create the model
		if ($model = $this->getModel($mName)) {
			// Push the model into the view (as default)
			$view->setModel($model, true);
		}

		// Set the layout
		$view->setLayout($vLayout);

		// Display the view
		$view->display();
	}
}