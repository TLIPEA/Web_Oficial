<?php
/**
 * @version   $Id: init.php 7764 2013-02-26 00:22:16Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2014 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

defined('JPATH_PLATFORM') or die;


/**
 *
 */
class JFormFieldInit extends JFormField
{
	/**
	 * @var string
	 */
	protected $type = 'Init';
	protected $_base_path = 'plugins/system/rokbox/assets/admin/';
	/**
	 *
	 */
	public function __construct()
	{
		$doc = JFactory::getDocument();
		$doc->addStyleSheet(JURI::root() . $this->_base_path . 'css/fields.css');
		$doc->addScript(JURI::root() . $this->_base_path . 'js/RokBox.js');
		$doc->addScript(JURI::root() . $this->_base_path . 'js/Dropdowns.js');
		$doc->addScriptDeclaration("window.addEvent('domready', function() { new Dropdowns(); });");

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
