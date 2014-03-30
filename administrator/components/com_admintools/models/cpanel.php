<?php
/**
 *  @package AdminTools
 *  @copyright Copyright (c)2010-2014 Nicholas K. Dionysopoulos
 *  @license GNU General Public License version 3, or later
 *  @version $Id$
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

JLoader::import('joomla.application.component.model');

/**
 * The Control Panel model
 *
 */
class AdmintoolsModelCpanels extends FOFModel
{
	/**
	 * Constructor; dummy for now
	 *
	 */
	public function __construct()
	{
		parent::__construct();
	}

	public function getPluginID()
	{
		$db = $this->getDBO();

		$query = $db->getQuery(true)
			->select($db->qn('extension_id'))
			->from($db->qn('#__extensions'))
			->where($db->qn('enabled').' >= '.$db->quote('1'))
			->where($db->qn('folder').' = '.$db->quote('system'))
			->where($db->qn('element').' = '.$db->quote('admintools'))
			->where($db->qn('type').' = '.$db->quote('plugin'))
			->order($db->qn('ordering').' ASC');
		$db->setQuery( $query );
		$id = $db->loadResult();

		return $id;
	}

	/**
	 * Automatically migrates settings from the component's parameters storage
	 * to our version 2.1+ dedicated storage table.
	 */
	public function autoMigrate()
	{
		// First, load the component parameters
		// FIX 2.1.13: Load the component parameters WITHOUT using JComponentHelper
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select($db->qn('params'))
			->from($db->qn('#__extensions'))
			->where($db->qn('type').' = '.$db->quote('component'))
			->where($db->qn('element').' = '.$db->quote('com_admintools'));
		$db->setQuery($query);
		$rawparams = $db->loadResult();
		$cparams = new JRegistry();
		if(version_compare(JVERSION, '3.0', 'ge')) {
			$cparams->loadString($rawparams, 'JSON');
		} else {
			$cparams->loadJSON($rawparams);
		}

		// Migrate parameters
		$allParams = $cparams->toArray();
		$safeList = array(
			'downloadid', 'lastversion', 'minstability',
			'scandiffs', 'scanemail', 'htmaker_folders_fix_at240',
			'acceptlicense', 'acceptsupport', 'sitename',
			'showstats',);
		if(interface_exists('JModel')) {
			$params = JModelLegacy::getInstance('Storage','AdmintoolsModel');
		} else {
			$params = JModel::getInstance('Storage','AdmintoolsModel');
		}
		$modified = 0;
		foreach($allParams as $k => $v) {
			if(in_array($k, $safeList)) continue;
			if($v == '') continue;

			$modified++;

			if(version_compare(JVERSION, '3.0', 'ge')) {
				$cparams->set($k, null);
			} else {
				$cparams->setValue($k, null);
			}
			$params->setValue($k, $v);
		}

		if($modified == 0) return;

		// Save new parameters
		$params->save();

		// Save component parameters
		$db = JFactory::getDBO();
		$data = $cparams->toString();

		$sql = $db->getQuery(true)
			->update($db->qn('#__extensions'))
			->set($db->qn('params').' = '.$db->q($data))
			->where($db->qn('element').' = '.$db->q('com_admintools'))
			->where($db->qn('type').' = '.$db->q('component'));

		$db->setQuery($sql);
		$db->execute();
	}

	public function needsDownloadID()
	{
		JLoader::import('joomla.application.component.helper');

		// Do I need a Download ID?
		$ret = false;
		$isPro = ADMINTOOLS_PRO;
		if(!$isPro) {
			$ret = true;
		} else {
			$ret = false;
			$params = JComponentHelper::getParams('com_admintools');
			if(version_compare(JVERSION, '3.0', 'ge')) {
				$dlid = $params->get('downloadid', '');
			} else {
				$dlid = $params->getValue('downloadid', '');
			}
			if(!preg_match('/^([0-9]{1,}:)?[0-9a-f]{32}$/i', $dlid)) {
				$ret = true;
			}
		}

		return $ret;
	}

	/**
	 * Refreshes the Joomla! update sites for this extension as needed
	 *
	 * @return  void
	 */
	public function refreshUpdateSite()
	{
		$isPro = defined('ADMINTOOLS_PRO') ? ADMINTOOLS_PRO : 0;

		JLoader::import('joomla.application.component.helper');
		$params = JComponentHelper::getParams('com_admintools');

		if(version_compare(JVERSION, '3.0', 'ge'))
		{
			$dlid = $params->get('downloadid', '');
		}
		else
		{
			$dlid = $params->getValue('downloadid', '');
		}

		$extra_query = null;

		// If I have a valid Download ID I will need to use a non-blank extra_query in Joomla! 3.2+
		if (preg_match('/^([0-9]{1,}:)?[0-9a-f]{32}$/i', $dlid))
		{
			// Even if the user entered a Download ID in the Core version. Let's switch his update channel to Professional
			$isPro = true;

			$extra_query = 'dlid=' . $dlid;
		}

		// Create the update site definition we want to store to the database
		$update_site = array(
			'name'		=> 'Admin Tools ' . ($isPro ? 'Professional' : 'Core'),
			'type'		=> 'extension',
			'location'	=> 'http://cdn.akeebabackup.com/updates/at' . ($isPro ? 'pro' : 'core') . '.xml',
			'enabled'	=> 1,
			'last_check_timestamp'	=> 0,
			'extra_query'	=> $extra_query
		);

		if (version_compare(JVERSION, '3.0.0', 'lt'))
		{
			unset($update_site['extra_query']);
		}

		$db = $this->getDbo();

		// Get the extension ID to ourselves
		$query = $db->getQuery(true)
			->select($db->qn('extension_id'))
			->from($db->qn('#__extensions'))
			->where($db->qn('type') . ' = ' . $db->q('component'))
			->where($db->qn('element') . ' = ' . $db->q('com_admintools'));
		$db->setQuery($query);

		$extension_id = $db->loadResult();

		if (empty($extension_id))
		{
			return;
		}

		// Get the update sites for our extension
		$query = $db->getQuery(true)
			->select($db->qn('update_site_id'))
			->from($db->qn('#__update_sites_extensions'))
			->where($db->qn('extension_id') . ' = ' . $db->q($extension_id));
		$db->setQuery($query);

		$updateSiteIDs = $db->loadColumn(0);

		if (!count($updateSiteIDs))
		{
			// No update sites defined. Create a new one.
			$newSite = (object)$update_site;
			$db->insertObject('#__update_sites', $newSite);

			$id = $db->insertid();

			$updateSiteExtension = (object)array(
				'update_site_id'	=> $id,
				'extension_id'		=> $extension_id,
			);
			$db->insertObject('#__update_sites_extensions', $updateSiteExtension);
		}
		else
		{
			// Loop through all update sites
			foreach ($updateSiteIDs as $id)
			{
				$query = $db->getQuery(true)
					->select('*')
					->from($db->qn('#__update_sites'))
					->where($db->qn('update_site_id') . ' = ' . $db->q($id));
				$db->setQuery($query);
				$aSite = $db->loadObject();

				// Does the name and location match?
				if (($aSite->name == $update_site['name']) && ($aSite->location == $update_site['location']))
				{
					// Do we have the extra_query property (J 3.2+) and does it match?
					if (property_exists($aSite, 'extra_query'))
					{
						if ($aSite->extra_query == $update_site['extra_query'])
						{
							continue;
						}
					}
					else
					{
						// Joomla! 3.1 or earlier. Updates may or may not work.
						continue;
					}
				}

				$update_site['update_site_id'] = $id;
				$newSite = (object)$update_site;
				$db->updateObject('#__update_sites', $newSite, 'update_site_id', true);
			}
		}
	}

	/**
	 * Checks if the download ID provisioning plugin for the updates of this extension is published. If not, it will try
	 * to publish it automatically. It reports the status of the plugin as a boolean.
	 *
	 * @return  bool
	 */
	public function isUpdatePluginEnabled()
	{
		// We can't be bothered about the plugin in Joomla! 2.5.0 through 2.5.19
		if (version_compare(JVERSION, '2.5.19', 'lt'))
		{
			return true;
		}

		// We can't be bothered about the plugin in Joomla! 3.x
		if (version_compare(JVERSION, '3.0.0', 'gt'))
		{
			return true;
		}

		$db = $this->getDBO();

		// Let's get the information of the update plugin
		$query = $db->getQuery(true)
			->select('*')
			->from($db->qn('#__extensions'))
			->where($db->qn('folder').' = '.$db->quote('installer'))
			->where($db->qn('element').' = '.$db->quote('admintools'))
			->where($db->qn('type').' = '.$db->quote('plugin'))
			->order($db->qn('ordering').' ASC');
		$db->setQuery($query);
		$plugin = $db->loadObject();

		// If the plugin is missing report it as unpublished (of course!)
		if (!is_object($plugin))
		{
			return false;
		}

		// If it's enabled there's nothing else to do
		if ($plugin->enabled)
		{
			return true;
		}

		// Otherwise, try to enable it and report false (so the user knows what he did wrong)
		$pluginObject = (object)array(
			'extension_id'	=> $plugin->extension_id,
			'enabled'		=> 1
		);

		try
		{
			$result = $db->updateObject('#__extensions', $pluginObject, 'extension_id');
			// Do not remove this line. We need to tell the user he's doing something wrong.
			$result = false;
		}
		catch (Exception $e)
		{
			$result = false;
		}

		return $result;
	}

}