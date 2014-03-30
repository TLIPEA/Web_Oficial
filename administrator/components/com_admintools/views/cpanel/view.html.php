<?php
/**
 *  @package AdminTools
 *  @copyright Copyright (c)2010-2014 Nicholas K. Dionysopoulos
 *  @license GNU General Public License version 3, or later
 *  @version $Id$
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

// Load framework base classes
JLoader::import('joomla.application.component.view');

class AdmintoolsViewCpanel extends FOFViewHtml
{
	protected function onBrowse($tpl = null)
	{
		// Is this the Professional release?
		JLoader::import('joomla.filesystem.file');
		$isPro = (ADMINTOOLS_PRO == 1);

		$this->isPro = $isPro;

		// Should we show the stats and graphs?
		JLoader::import('joomla.html.parameter');
		JLoader::import('joomla.application.component.helper');

		$db = JFactory::getDbo();
		$sql = $db->getQuery(true)
			->select($db->qn('params'))
			->from($db->qn('#__extensions'))
			->where($db->qn('type').' = '.$db->q('component'))
			->where($db->qn('element').' = '.$db->q('com_admintools'));
		$db->setQuery($sql);
		$rawparams = $db->loadResult();
		$params = new JRegistry();
		if(version_compare(JVERSION, '3.0', 'ge')) {
			$params->loadString($rawparams, 'JSON');
		} else {
			$params->loadJSON($rawparams);
		}
		$this->showstats = $params->get('showstats', 1);

		// Load the models
		/** @var AdmintoolsModelCpanels $model */
		$model = $this->getModel();
		$adminpwmodel = FOFModel::getAnInstance('Adminpw','AdmintoolsModel');
		$mpModel = FOFModel::getAnInstance('Masterpw','AdmintoolsModel');
		$geoModel = FOFModel::getAnInstance('Geoblock','AdmintoolsModel');

		// Decide on the administrator password padlock icon
		$adminlocked = $adminpwmodel->isLocked();
		$this->adminLocked = $adminlocked;

		// Do we have to show a master password box?
		$this->hasValidPassword = $mpModel->hasValidPassword();

		// Is this MySQL?
		$dbType = JFactory::getDbo()->name;
		$isMySQL = stristr($dbType, 'mysql');

		// If the user doesn't have a valid master pw for some views, don't show
		// the buttons.
		$this->enable_cleantmp =		$mpModel->accessAllowed('cleantmp');
		$this->enable_fixperms =		$mpModel->accessAllowed('fixperms');
		$this->enable_purgesessions =	$mpModel->accessAllowed('purgesessions');
		$this->enable_dbtools = 		$mpModel->accessAllowed('dbtools');
		$this->enable_dbchcol = 		$mpModel->accessAllowed('dbchcol');

		$this->isMySQL = 				$isMySQL;

		$this->pluginid = 				$model->getPluginID();

		$this->hasplugin = 				$geoModel->hasGeoIPPlugin();
		$this->pluginNeedsUpdate =		$geoModel->dbNeedsUpdate();

		$this->update_plugin =			$model->isUpdatePluginEnabled();

		if(version_compare(JVERSION, '3.0', 'ge')) {
			JHTML::_('behavior.framework');
		} else {
			JHTML::_('behavior.mootools');
		}

		return true;
	}
}