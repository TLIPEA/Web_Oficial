<?php
/**
 *  @package AdminTools
 *  @copyright Copyright (c)2010-2014 Nicholas K. Dionysopoulos
 *  @license GNU General Public License version 3, or later
 *  @version $Id$
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

defined('_JEXEC') or die();

JDEBUG ? define('AKEEBADEBUG', 1) : null;

// Check for PHP4
if(defined('PHP_VERSION')) {
	$version = PHP_VERSION;
} elseif(function_exists('phpversion')) {
	$version = phpversion();
} else {
	// No version info. I'll lie and hope for the best.
	$version = '5.0.0';
}

// Old PHP version detected. EJECT! EJECT! EJECT!
if(!version_compare($version, '5.3.0', '>='))
{
	return JError::raise(E_ERROR, 500, 'PHP '.$version.' is not supported by Admin Tools.<br/><br/>The version of PHP used on your site is obsolete and contains known security vulenrabilities. Moreover, it is missing features required by Admin Tools to work properly or at all. Please ask your host to upgrade your server to the latest PHP 5.3 release. Thank you!');
}

JLoader::import('joomla.application.component.model');

// Timezone fix; avoids errors printed out by PHP 5.3.3+ (thanks Yannick!)
if(function_exists('date_default_timezone_get') && function_exists('date_default_timezone_set')) {
	if(function_exists('error_reporting')) {
		$oldLevel = error_reporting(0);
	}
	$serverTimezone = @date_default_timezone_get();
	if(empty($serverTimezone) || !is_string($serverTimezone)) $serverTimezone = 'UTC';
	if(function_exists('error_reporting')) {
		error_reporting($oldLevel);
	}
	@date_default_timezone_set( $serverTimezone);
}

// Load FOF
include_once JPATH_ADMINISTRATOR.'/components/com_admintools/fof/include.php';
if(!defined('FOF_INCLUDED') || !class_exists('FOFForm', true))
{
	JError::raiseError ('500', 'Your Admin Tools installation is broken; please re-install. Alternatively, extract the installation archive and copy the fof directory inside your site\'s libraries directory.');
}

// Load version.php
JLoader::import('joomla.filesystem.file');
$version_php = JPATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR.'version.php';
if(!defined('ADMINTOOLS_VERSION') && JFile::exists($version_php)) {
	require_once $version_php;
}

// Fix Pro/Core
$isPro = (ADMINTOOLS_PRO == 1);
if(!$isPro) {
	JLoader::import('joomla.filesystem.folder');
	$pf = JPATH_BASE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'admintools'.DIRECTORY_SEPARATOR.'pro.php';
	if(JFile::exists($pf)) JFile::delete($pf);

	$pf = JPATH_BASE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'system'.DIRECTORY_SEPARATOR.'admintools'.DIRECTORY_SEPARATOR.'admintools'.DIRECTORY_SEPARATOR.'pro.php';
	if(JFile::exists($pf)) JFile::delete($pf);

	$files = array('controllers/geoblock.php','controllers/htmaker.php','controllers/log.php','controllers/redires.php',
		'controllers/wafconfig.php','helpers/geoip.php','models/badwords.php','models/geoblock.php','models/htmaker.php',
		'models/ipbl.php','models/ipwl.php','models/log.php','models/redirs.php','models/wafconfig.php');
	$dirs = array('assets/geoip','views/badwords','views/geoblock','views/htmaker','views/ipbl','views/ipwl',
		'views/log','views/masterpw','views/redirs','views/waf','views/wafconfig');

	foreach($files as $fname) {
		$file = JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.$fname;
		if(JFile::exists($file)) {
			JFile::delete($file);
		}
	}

	foreach($dirs as $fname) {
		$dir = JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.$fname;
		if(JFolder::exists($dir)) {
			JFolder::delete($dir);
		}
	}
}

// Joomla! 1.6 detection
if(!defined('ADMINTOOLS_JVERSION'))
{
	if(!version_compare( JVERSION, '1.6.0', 'ge' )) {
		define('ADMINTOOLS_JVERSION','15');
	} else {
		define('ADMINTOOLS_JVERSION','16');
	}
}

// If JSON functions don't exist, load our compatibility layer
if( (!function_exists('json_encode')) || (!function_exists('json_decode')) )
{
	require_once JPATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'jsonlib.php';
}

JLoader::import('joomla.application.component.model');
require_once JPATH_COMPONENT_ADMINISTRATOR.'/models/storage.php';

// Access check, Joomla! 1.6 style.
if (!JFactory::getUser()->authorise('core.manage', 'com_admintools')) {
	return JError::raiseError(403, JText::_('JERROR_ALERTNOAUTHOR'));
}

FOFDispatcher::getTmpInstance('com_admintools')->dispatch();