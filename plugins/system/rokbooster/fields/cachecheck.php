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
class JFormFieldCacheCheck extends JFormField
{
	/**
	 * @var string
	 */
	protected $type = 'CacheCheck';

	public function __construct($form = null)
	{
		parent::__construct($form);
		$config = JFactory::getConfig();
		if (!$config->get('caching', 0)) {
			JFactory::getApplication()->enqueueMessage(JText::_('ROKBOOSTER_CACHE_NOT_ENABLED_WARNING'), 'notice');
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
