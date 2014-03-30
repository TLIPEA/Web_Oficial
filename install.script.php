<?php
/**
 * @version   $Id: install.script.php 59893 2013-09-16 15:46:35Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - ${copyright_year} RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

/**
 *
 */
class JoomlaInstallerScript
{
	/**
	 * @param $type
	 * @param $parent
	 *
	 * @return bool
	 */
	public function preflight($type, $parent)
	{
		JError::raiseWarning(100, 'The RocketLauncher package should not be installed into an existing Joomla instance. It is a stand-alone Joomla installation.');
		return false;
	}
}
