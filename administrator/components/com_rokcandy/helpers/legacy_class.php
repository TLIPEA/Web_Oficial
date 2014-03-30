<?php
/**
 * @version   $Id: legacy_class.php 6335 2013-01-08 04:29:33Z steph $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
defined('_JEXEC') or die('Restricted access');

if (method_exists('JSession','checkToken')) {
	function rokcandy_checktoken($method = 'post')
	{
		if ($method == 'default')
		{
			$method = 'request';
		}
		return JSession::checkToken($method);
	}
} else {
	function rokcandy_checktoken($method = 'post')
	{
		return JRequest::checkToken($method);
	}
}

if (!class_exists('RokCandyLegacyJView', false)) {
  $jversion = new JVersion();
  if (version_compare($jversion->getShortVersion(), '2.5.5', '>')) {
    class RokCandyLegacyJView extends JViewLegacy
    {
    }

    class RokCandyLegacyJController extends JControllerLegacy
    {
    }

    class RokCandyLegacyJModel extends JModelLegacy
    {
    }
  } else {
    jimport('joomla.application.component.view');
    jimport('joomla.application.component.controller');
    jimport('joomla.application.component.model');
    class RokCandyLegacyJView extends JView
    {
    }

    class RokCandyLegacyJController extends JController
    {
    }

    class RokCandyLegacyJModel extends JModel
    {
    }
  }
}
