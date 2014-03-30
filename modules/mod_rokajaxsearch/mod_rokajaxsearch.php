<?php
/**
 * RokAjaxSearch Module
 *
 * @package RocketTheme
 * @subpackage rokajaxsearch
 * @version   2.0.2 February 17, 2014
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2014 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 *
 * Inspired on PixSearch Joomla! module by Henrik Hussfelt <henrik@pixpro.net>
 */

defined('_JEXEC') or die('Restricted access');
require_once (dirname(__FILE__) . '/helper.php');
$helper = new modRokajaxsearchHelper();
$helper->inizialize($params->get('include_css'), $params->get('offset_search_result'), $params);

require(JModuleHelper::getLayoutPath('mod_rokajaxsearch'));