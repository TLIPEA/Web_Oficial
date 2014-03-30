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
class JFormFieldInit extends JFormField
{
	/**
	 * @var string
	 */
	protected $type = 'Init';
	public static $assets_loaded = false;

	/**
	 *
	 */
	public function __construct()
	{
		if (!self::$assets_loaded){
			$doc = JFactory::getDocument();
			$doc->addStyleSheet(JURI::root() . 'plugins/system/rokbooster/assets/css/fields.css');
			self::$assets_loaded = true;
		}
	}

	/**
	 * @return mixed
	 */
	protected function getLabel()
	{
		return;
	}

	/**
	 * @return mixed
	 */
	protected function getInput()
	{
		return;
	}

}
