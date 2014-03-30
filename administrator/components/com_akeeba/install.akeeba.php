<?php
/**
 * @package AkeebaBackup
 * @copyright Copyright (c)2009-2012 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 3, or later
 * @version $Id$
 * @since 3.0
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// =============================================================================
// Akeeba Component Installation Configuration
// =============================================================================
$installation_queue = array(
	// modules => { (folder) => { (module) => { (position), (published) } }* }*
	'modules' => array(
		'admin' => array(
			'akadmin' => array('cpanel', 1)
		),
		'site' => array(
		)
	),
	// plugins => { (folder) => { (element) => (published) }* }*
	'plugins' => array(
		'system' => array(
			'akeebaupdatecheck'		=> 0,
			'aklazy'				=> 0,
			'oneclickaction'		=> 0,
			'srp'					=> 0
		)
	)
);

// Obsolete files and folders to remove from the Core release only
$akeebaRemoveFilesCore = array(
	'files'	=> array(
		'administrator/components/com_akeeba/restore.php',
		'plugins/system/akeebaupdatecheck.php',
		'plugins/system/akeebaupdatecheck.xml',
		'plugins/system/oneclickaction.php',
		'plugins/system/oneclickaction.xml',
		'plugins/system/srp.php',
		'plugins/system/srp.xml'
	),
	'folders' => array(
		'administrator/components/com_akeeba/akeeba/engines/finalization',
		'plugins/system/akeebaupdatecheck',
		'plugins/system/oneclickaction',
		'plugins/system/srp'
	)
);

// Obsolete files and folders to remove from the Core and Pro releases
$akeebaRemoveFilesPro = array(
	'files'	=> array(
		'administrator/components/com_akeeba/akeeba/core/03.filters.ini',
		'administrator/components/com_akeeba/akeeba/engines/archiver/directftp.ini',
		'administrator/components/com_akeeba/akeeba/engines/archiver/directftp.php',
		'administrator/components/com_akeeba/akeeba/engines/archiver/directsftp.ini',
		'administrator/components/com_akeeba/akeeba/engines/archiver/directsftp.php',
		'administrator/components/com_akeeba/akeeba/engines/archiver/zipnative.ini',
		'administrator/components/com_akeeba/akeeba/engines/archiver/zipnative.php',
		'administrator/components/com_akeeba/akeeba/engines/proc/email.ini',
		'administrator/components/com_akeeba/akeeba/engines/proc/email.php',
		'administrator/components/com_akeeba/views/buadmin/restorepoint.php',
		'administrator/components/com_akeeba/controllers/installer.php',
		'administrator/components/com_akeeba/controllers/srprestore.php',
		'administrator/components/com_akeeba/controllers/stw.php',
		'administrator/components/com_akeeba/controllers/upload.php',
		'administrator/components/com_akeeba/models/installer.php',
		'administrator/components/com_akeeba/models/srprestore.php',
		'administrator/components/com_akeeba/models/stw.php'
	),
	'folders' => array(
		'administrator/components/com_akeeba/views/installer',
		'administrator/components/com_akeeba/views/profiles',
		'administrator/components/com_akeeba/views/srprestore',
		'administrator/components/com_akeeba/views/stw',
		'administrator/components/com_akeeba/views/upload',
	)
);

if(!function_exists('rrmdir')) {
	function rrmdir($dir) { 
		$result = true;

		if (@is_dir($dir)) { 
			$objects = @scandir($dir); 
			if($objects !== false) foreach ($objects as $object) { 
				if ($object != "." && $object != "..") { 
					if (filetype($dir."/".$object) == "dir") {
						$result &= rrmdir($dir."/".$object);		 
					} else {
						$result &= @unlink($dir."/".$object); 
					}
				} 
			}
			reset($objects); 
			$result = @rmdir($dir); 
		} else {
			$result = false;
		}

		return $result;
	}
}

// Joomla! 1.6 Beta 13+ hack
if( version_compare( JVERSION, '1.6.0', 'ge' ) && !defined('_AKEEBA_HACK') ) {
	return;
} else {
	global $akeeba_installation_has_run;
	if($akeeba_installation_has_run) return;
}

jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

// Schema modification -- BEGIN

$db = JFactory::getDBO();
$errors = array();

// Version 3.0 to 3.1 updates (performs autodection before running the commands)
$sql = 'SHOW CREATE TABLE `#__ak_stats`';
$db->setQuery($sql);
$ctableAssoc = $db->loadResultArray(1);
$ctable = empty($ctableAssoc) ? '' : $ctableAssoc[0];
if(!strstr($ctable, '`total_size`'))
{
	// Smart schema update - Updated for changes in 3.2.a1

	if($db->hasUTF())
	{
		$charset = 'CHARSET=utf8';
	}
	else
	{
		$charset = '';
	}

	$sql = <<<ENDSQL
DROP TABLE IF EXISTS `#__ak_stats_bak`;
ENDSQL;
	$db->setQuery($sql);
	$status = $db->query();
	if(!$status && ($db->getErrorNum() != 1060)) {
		$errors[] = $db->getErrorMsg(true);
	}

	$sql = <<<ENDSQL
CREATE TABLE `#__ak_stats_bak` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(255) NOT NULL,
  `comment` longtext,
  `backupstart` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `backupend` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` enum('run','fail','complete') NOT NULL DEFAULT 'run',
  `origin` varchar(30) NOT NULL DEFAULT 'backend',
  `type` varchar(30) NOT NULL DEFAULT 'full',
  `profile_id` bigint(20) NOT NULL DEFAULT '1',
  `archivename` longtext,
  `absolute_path` longtext,
  `multipart` int(11) NOT NULL DEFAULT '0',
  `tag` varchar(255) DEFAULT NULL,
  `filesexist` tinyint(1) NOT NULL DEFAULT '0',
  `remote_filename` varchar(1000) DEFAULT NULL,
  `total_size` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_fullstatus` (`filesexist`,`status`),
  KEY `idx_stale` (`status`,`origin`)
) ENGINE=MyISAM DEFAULT $charset;
ENDSQL;
	$db->setQuery($sql);
	$status = $db->query();
	if(!$status && ($db->getErrorNum() != 1060)) {
		$errors[] = $db->getErrorMsg(true);
	}

	if(strstr($ctable, '`tag`')) {
		// Upgrade from 3.1.3 or later (has tag and filesexist columns)
		$sql = <<<ENDSQL
INSERT IGNORE INTO `#__ak_stats_bak`
	(`id`,`description`,`comment`,`backupstart`,`backupend`,`status`,`origin`,`type`,`profile_id`,`archivename`,`absolute_path`,`multipart`,`tag`,`filesexist`)
SELECT
  `id`,`description`,`comment`,`backupstart`,`backupend`,`status`,`origin`,`type`,`profile_id`,`archivename`,`absolute_path`,`multipart`,`tag`,`filesexist`
FROM
  `#__ak_stats`;
ENDSQL;
	} else {
		// Upgrade from 3.1.2 or earlier
		$sql = <<<ENDSQL
INSERT IGNORE INTO `#__ak_stats_bak`
	(`id`,`description`,`comment`,`backupstart`,`backupend`,`status`,`origin`,`type`,`profile_id`,`archivename`,`absolute_path`,`multipart`)
SELECT
  `id`,`description`,`comment`,`backupstart`,`backupend`,`status`,`origin`,`type`,`profile_id`,`archivename`,`absolute_path`,`multipart`
FROM
  `#__ak_stats`;
ENDSQL;
	}
	$db->setQuery($sql);
	$status = $db->query();
	if(!$status && ($db->getErrorNum() != 1060)) {
		$errors[] = $db->getErrorMsg(true);
	}

	$sql = <<<ENDSQL
DROP TABLE IF EXISTS `#__ak_stats`;
ENDSQL;
	$db->setQuery($sql);
	$status = $db->query();
	if(!$status && ($db->getErrorNum() != 1060)) {
		$errors[] = $db->getErrorMsg(true);
	}

	$sql = <<<ENDSQL
CREATE TABLE `#__ak_stats` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(255) NOT NULL,
  `comment` longtext,
  `backupstart` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `backupend` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` enum('run','fail','complete') NOT NULL DEFAULT 'run',
  `origin` varchar(30) NOT NULL DEFAULT 'backend',
  `type` varchar(30) NOT NULL DEFAULT 'full',
  `profile_id` bigint(20) NOT NULL DEFAULT '1',
  `archivename` longtext,
  `absolute_path` longtext,
  `multipart` int(11) NOT NULL DEFAULT '0',
  `tag` varchar(255) DEFAULT NULL,
  `filesexist` tinyint(1) NOT NULL DEFAULT '0',
  `remote_filename` varchar(1000) DEFAULT NULL,
  `total_size` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_fullstatus` (`filesexist`,`status`),
  KEY `idx_stale` (`status`,`origin`)
) ENGINE=MyISAM DEFAULT $charset;
ENDSQL;
	$db->setQuery($sql);
	$status = $db->query();
	if(!$status && ($db->getErrorNum() != 1060)) {
		$errors[] = $db->getErrorMsg(true);
	}

	$sql = <<<ENDSQL
INSERT IGNORE INTO `#__ak_stats` SELECT * FROM `#__ak_stats_bak`;
ENDSQL;
	$db->setQuery($sql);
	$status = $db->query();
	if(!$status && ($db->getErrorNum() != 1060)) {
		$errors[] = $db->getErrorMsg(true);
	}

	$sql = <<<ENDSQL
DROP TABLE IF EXISTS `#__ak_stats_bak`;
ENDSQL;
	$db->setQuery($sql);
	$status = $db->query();
	if(!$status && ($db->getErrorNum() != 1060)) {
		$errors[] = $db->getErrorMsg(true);
	}

}

// Schema modification -- END

// Install modules and plugins -- BEGIN

// -- General settings
jimport('joomla.installer.installer');
$db = & JFactory::getDBO();
$status = new JObject();
$status->modules = array();
$status->plugins = array();
if( version_compare( JVERSION, '1.6.0', 'ge' ) ) {
	if(!isset($parent))
	{
		$parent = $this->parent;
	}
	$src = $parent->getPath('source');
} else {
	$src = $this->parent->getPath('source');
}

// Modules installation
if(count($installation_queue['modules'])) {
	foreach($installation_queue['modules'] as $folder => $modules) {
		if(count($modules)) foreach($modules as $module => $modulePreferences) {
			// Install the module
			if(empty($folder)) $folder = 'site';
			$path = "$src/modules/$folder/$module";
			if(!is_dir($path)) {
				$path = "$src/modules/$folder/mod_$module";
			}
			if(!is_dir($path)) {
				$path = "$src/modules/$module";
			}
			if(!is_dir($path)) {
				$path = "$src/modules/mod_$module";
			}
			if(!is_dir($path)) continue;
			// Was the module already installed?
			$sql = 'SELECT COUNT(*) FROM #__modules WHERE `module`='.$db->Quote('mod_'.$module);
			$db->setQuery($sql);
			$count = $db->loadResult();
			$installer = new JInstaller;
			$result = $installer->install($path);
			$status->modules[] = array('name'=>'mod_'.$module, 'client'=>$folder, 'result'=>$result);
			// Modify where it's published and its published state
			if(!$count) {
				// A. Position and state
				list($modulePosition, $modulePublished) = $modulePreferences;
				if(version_compare(JVERSION, '2.5.0', 'ge') && ($modulePosition == 'cpanel')) {
					$modulePosition = 'icon';
				}
				$sql = "UPDATE #__modules SET position=".$db->Quote($modulePosition);
				if($modulePublished) $sql .= ', published=1';
				$sql .= ' WHERE `module`='.$db->Quote('mod_'.$module);
				$db->setQuery($sql);
				$db->query();
				if(version_compare(JVERSION, '1.7.0', 'ge')) {
					// B. Change the ordering of back-end modules to 1 + max ordering in J! 1.7+
					if($folder == 'admin') {
						$query = $db->getQuery(true);
						$query->select('MAX('.$db->nq('ordering').')')
							->from($db->nq('#__modules'))
							->where($db->nq('position').'='.$db->q($modulePosition));
						$db->setQuery($query);
						$position = $db->loadResult();
						$position++;
						
						$query = $db->getQuery(true);
						$query->update($db->nq('#__modules'))
							->set($db->nq('ordering').' = '.$db->q($position))
							->where($db->nq('module').' = '.$db->q('mod_'.$module));
						$db->setQuery($query);
						$db->query();
					}
					// C. Link to all pages on Joomla! 1.7+
					$query = $db->getQuery(true);
					$query->select('id')->from($db->nq('#__modules'))
						->where($db->nq('module').' = '.$db->q('mod_'.$module));
					$db->setQuery($query);
					$moduleid = $db->loadResult();
					
					$query = $db->getQuery(true);
					$query->select('*')->from($db->nq('#__modules_menu'))
						->where($db->nq('moduleid').' = '.$db->q($moduleid));
					$db->setQuery($query);
					$assignments = $db->loadObjectList();
					$isAssigned = !empty($assignments);
					if(!$isAssigned) {
						$o = (object)array(
							'moduleid'	=> $moduleid,
							'menuid'	=> 0
						);
						$db->insertObject('#__modules_menu', $o);
					}
				}
			}
		}
	}
}

// Plugins installation
if(count($installation_queue['plugins'])) {
	foreach($installation_queue['plugins'] as $folder => $plugins) {
		if(count($plugins)) foreach($plugins as $plugin => $published) {
			$path = "$src/plugins/$folder/$plugin";
			if(!is_dir($path)) {
				$path = "$src/plugins/$folder/plg_$plugin";
			}
			if(!is_dir($path)) {
				$path = "$src/plugins/$plugin";
			}
			if(!is_dir($path)) {
				$path = "$src/plugins/plg_$plugin";
			}
			if(!is_dir($path)) continue;
			
			// Was the plugin already installed?
			if( version_compare( JVERSION, '1.6.0', 'ge' ) ) {
				$query = "SELECT COUNT(*) FROM  #__extensions WHERE element=".$db->Quote($plugin)." AND folder=".$db->Quote($folder);
			} else {
				$query = "SELECT COUNT(*) FROM  #__plugins WHERE element=".$db->Quote($plugin)." AND folder=".$db->Quote($folder);
			}
			$db->setQuery($query);
			$count = $db->loadResult();
			
			$installer = new JInstaller;
			$result = $installer->install($path);
			$status->plugins[] = array('name'=>'plg_'.$plugin,'group'=>$folder, 'result'=>$result);
			
			if($published && !$count) {
				if( version_compare( JVERSION, '1.6.0', 'ge' ) ) {
					$query = "UPDATE #__extensions SET enabled=1 WHERE element=".$db->Quote($plugin)." AND folder=".$db->Quote($folder);
				} else {
					$query = "UPDATE #__plugins SET published=1 WHERE element=".$db->Quote($plugin)." AND folder=".$db->Quote($folder);
				}
				$db->setQuery($query);
				$db->query();
			}
		}
	}
}

// Remove features from the Core edition
$isAkeebaPro = is_dir($src.'/plugins/system/srp');

if($isAkeebaPro) {
	$akeebaRemoveFiles = $akeebaRemoveFilesPro;
} else {
	$akeebaRemoveFiles['files'] = array_merge($akeebaRemoveFilesPro['files'], $akeebaRemoveFilesCore['files']);
	$akeebaRemoveFiles['folders'] = array_merge($akeebaRemoveFilesPro['folders'], $akeebaRemoveFilesCore['folders']);
}

// Remove files
jimport('joomla.filesystem.file');
if(!empty($akeebaRemoveFiles['files'])) foreach($akeebaRemoveFiles['files'] as $file) {
	$f = JPATH_BASE.'/'.$file;
	if(!file_exists($f)) continue;
	if(!@unlink($f)) {
		JFile::delete($f);
	}
}

// Remove folders
jimport('joomla.filesystem.file');
if(!empty($akeebaRemoveFiles['folders'])) foreach($akeebaRemoveFiles['folders'] as $folder) {
	$f = JPATH_BASE.'/'.$folder;
	if(!is_dir($f)) continue;
	if(!rrmdir($f)) {
		JFolder::delete($f);
	}
}

if(!$isAkeebaPro) {	
	// Remove plugins
	# ----- System - System Restore Points
	if(version_compare(JVERSION,'1.6.0','ge')) {
		$db->setQuery('SELECT `extension_id` FROM #__extensions WHERE `type` = "plugin" AND `element` = "srp" AND `folder` = "system"');
	} else {
		$db->setQuery('SELECT `id` FROM #__plugins WHERE `element` = "srp" AND `folder` = "system"');
	}
	$id = $db->loadResult();
	if($id)
	{
		$installer = new JInstaller;
		$result = $installer->uninstall('plugin',$id,1);
	}

	# ----- System - One Click Action
	if(version_compare(JVERSION,'1.6.0','ge')) {
		$db->setQuery('SELECT `extension_id` FROM #__extensions WHERE `type` = "plugin" AND `element` = "oneclickaction" AND `folder` = "system"');
	} else {
		$db->setQuery('SELECT `id` FROM #__plugins WHERE `element` = "oneclickaction" AND `folder` = "system"');
	}
	$id = $db->loadResult();
	if($id)
	{
		$installer = new JInstaller;
		$result = $installer->uninstall('plugin',$id,1);
	}

	# ----- System - Akeeba Update Check
	if(version_compare(JVERSION,'1.6.0','ge')) {
		$db->setQuery('SELECT `extension_id` FROM #__extensions WHERE `type` = "plugin" AND `element` = "akeebaupdatecheck" AND `folder` = "system"');
	} else {
		$db->setQuery('SELECT `id` FROM #__plugins WHERE `element` = "akeebaupdatecheck" AND `folder` = "system"');
	}
	$id = $db->loadResult();
	if($id)
	{
		$installer = new JInstaller;
		$result = $installer->uninstall('plugin',$id,1);
	}	
}

// Install modules and plugins -- END

// Finally, show the installation results form
?>
<?php if(!empty($errors)): ?>
<div style="background-color: #900; color: #fff; font-size: large;">
	<h1>MySQL errors during installation</h1>
	<p>The Akeeba Backup installation script detected MySQL error which will
		prevent the component from working properly. We suggest uninstalling
		any previous version of Akeeba Backup and trying a clean installation.
	</p>
	<p>
		The MySQL errors were:
	</p>
	<p style="font-size: normal;">
<?php echo implode("<br/>", $errors); ?>
	</p>
</div>
<?php endif; ?>

<h1>Akeeba Backup installation</h1>

<?php $rows = 0;?>
<div style="margin: 1em; font-size: 14pt; background-color: #fffff9; color: black">
	You can download translation files <a href="http://akeeba-cdn.s3-website-eu-west-1.amazonaws.com/language/akeebabackup/">directly from our CDN page</a>.
</div>

<img src="components/com_akeeba/assets/images/logo-48.png" width="48" height="48" alt="Akeeba Backup" align="right" />

<h2>Welcome to Akeeba Backup!</h2>
<table class="adminlist">
	<thead>
		<tr>
			<th class="title" colspan="2">Extension</th>
			<th width="30%">Status</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td colspan="3"></td>
		</tr>
	</tfoot>
	<tbody>
		<tr class="row0">
			<td class="key" colspan="2">Akeeba Backup component</td>
			<td><strong>Installed</strong></td>
		</tr>
		<?php if (count($status->modules)) : ?>
		<tr>
			<th>Module</th>
			<th>Client</th>
			<th></th>
		</tr>
		<?php foreach ($status->modules as $module) : ?>
		<tr class="row<?php echo (++ $rows % 2); ?>">
			<td class="key"><?php echo $module['name']; ?></td>
			<td class="key"><?php echo ucfirst($module['client']); ?></td>
			<td><strong><?php echo ($module['result'])?'Installed':'Not installed'; ?></strong></td>
		</tr>
		<?php endforeach;?>
		<?php endif;?>
		<?php if (count($status->plugins)) : ?>
		<tr>
			<th>Plugin</th>
			<th>Group</th>
			<th></th>
		</tr>
		<?php foreach ($status->plugins as $plugin) : ?>
		<tr class="row<?php echo (++ $rows % 2); ?>">
			<td class="key"><?php echo ucfirst($plugin['name']); ?></td>
			<td class="key"><?php echo ucfirst($plugin['group']); ?></td>
			<td><strong><?php echo ($plugin['result'])?'Installed':'Not installed'; ?></strong></td>
		</tr>
		<?php endforeach; ?>
		<?php endif; ?>
	</tbody>
</table>

<fieldset>
	<p>
		We strongly recommend reading the
		<a href="https://www.akeebabackup.com/documentation/quick-start-guide.html" target="_blank">Quick Start Guide</a>
		(short, suitable for beginners) or
		<a href="https://www.akeebabackup.com/documentation/akeeba-backup-documentation.html" target="_blank">Akeeba Backup User's Guide</a>
		(lengthy, technical) before proceeding with using this component. Alternatively, you can
		<a href="https://www.akeebabackup.com/documentation/video-tutorials.html" target="_blank">watch some video tutorials</a>
		which will get you up to speed with backing up and restoring your site.
	</p>
	<p>
		When you're done with the documentation, you can go ahead and run the
		<a href="index.php?option=com_akeeba">Post-Installation Wizard</a>
		which will help you configure Akeeba Backup's optional settings. If this
		is the first time you installed Akeeba Backup, we strongly recommend
		clicking the last checkbox, or click on the Configuration Wizard button
		in Akeeba Backup's control panel page.
	</p>
	<p>
		Should you get stuck somewhere, our
		<a href="https://www.akeebabackup.com/documentation/troubleshooter.html" target="_blank">Troubleshooting Wizard</a>
		is right there to help you. If you need one-to-one support, you can get
		it from our <a href="https://www.akeebabackup.com/support.html" target="_blank">support ticket system</a>,
		directly from Akeeba Backup's team.<br/>
		<?php if($isAkeebaPro): ?>
		As a subscriber to Akeeba Backup Professional (AKEEBAPRO or AKEEBADELUXE subscription level),
		you have full access to our ticket system for the term of your subscription period. If your
		subscription expires, you will have to renew it in order to request further support.<br/>
		<small>Note: if this component was installed on your site by a third party, e.g. your
		site developer, and you and/or your company do not have an active subscription with
		AkeebaBackup.com, please contact the person who installed the component on your site for
		support.
		<?php else: ?>
		While Akeeba Backup Core is free, access to its support is not. You will need an active
		subscription to request support. Support-only subscriptions are availabe from &euro;7<small>.79</small>
		(about $10 USD) and grant you the same high support priority as with all of our subscribers.
		<?php endif; ?>
	</p>
	<p>
		<strong>Remember, you can always get on-line help for the Akeeba Backup
		page you are currently viewing by clicking on the help icon in the top
		right corner of that page.</strong>
	</p>
</fieldset>
<?php
global $akeeba_installation_has_run;
$akeeba_installation_has_run = 1;
if(!defined('AKEEBA_PRO')) {
	require_once JPATH_ADMINISTRATOR.'/components/com_akeeba/version.php';
}
if(AKEEBA_PRO != 1)
{
	jimport('joomla.filesystem.folder');
	jimport('joomla.filesystem.file');
	if(JFolder::exists(JPATH_ADMINISTRATOR.'/components/com_akeeba/plugins')) {
		JFolder::delete(JPATH_ADMINISTRATOR.'/components/com_akeeba/plugins');
		JFolder::create(JPATH_ADMINISTRATOR.'/components/com_akeeba/plugins');
	}
	if(JFolder::exists(JPATH_ADMINISTRATOR.'/components/com_akeeba/akeeba/plugins')) {
		JFolder::delete(JPATH_ADMINISTRATOR.'/components/com_akeeba/akeeba/plugins');
		JFolder::create(JPATH_ADMINISTRATOR.'/components/com_akeeba/akeeba/plugins');	
	}
}
?>