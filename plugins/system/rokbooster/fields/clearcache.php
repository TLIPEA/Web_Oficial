<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Form Field class for the Joomla Platform.
 * Supports a one line text field.
 *
 * @package     Joomla.Platform
 * @subpackage  Form
 * @link        http://www.w3.org/TR/html-markup/input.text.html#input.text
 * @since       11.1
 */
class JFormFieldClearcache extends JFormField
{
	/**
	 * @var string
	 */
	protected $type = 'Clearcache';
	public static $assets_loaded = false;

	/**
	 * @return string
	 */
	protected function getInput()
	{

		if (!self::$assets_loaded){
			$doc = JFactory::getDocument();
			$doc->addStyleSheet(JURI::root(true).'/plugins/system/rokbooster/fields/assets/clearcache/css/clearcache.css');
			$doc->addScript(JURI::root(true).'/plugins/system/rokbooster/fields/assets/clearcache/js/RokBooster.js');
			self::$assets_loaded = true;
		}

		$file_cache = new JCache(array(
		                              'defaultgroup'   => 'rokbooster',
		                              'caching'        => true,
		                              'checkTime'      => true,
		                              'storage'        => 'file',
		                              'cachebase'      => JPATH_SITE . '/cache'
		                         ));
		$files      = $file_cache->getAll();
		$filecount  = 0;
		if (is_array($files) && array_key_exists('rokbooster', $files)) {
			$filecount = $files['rokbooster']->count;
		}
		return '<div class="clearcache btn btn-primary" data-action="clearCache"><i>' . JText::_('ROKBOOSTER_BUTTON_CLEAR_CACHE') . '<span class="count">' . $filecount . '</span></i></div>';
	}

}
