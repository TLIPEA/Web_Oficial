<?php
/**
 * @version $Id: candymacros.php 5112 2012-11-08 23:59:29Z btowles $
 * @author RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');

class RokCandyControllerCandyMacros extends JControllerAdmin
{

	public function __construct($config = array())
	{
		parent::__construct($config);
	}

	public function getModel($name = 'CandyMacro', $prefix = 'RokCandyModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);

		return $model;
	}
}