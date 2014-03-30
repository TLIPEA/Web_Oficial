<?php
/**
 * @version $Id: categories.php 5112 2012-11-08 23:59:29Z btowles $
 * @author RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */// no direct access
defined('_JEXEC') or die('Restricted access');

class JElementRokCandyList
{
   	function getCategories( $name, $active = NULL, $javascript = NULL, $order = 'lft', $size = 1, $sel_cat = 1 )
	{
		$db =JFactory::getDBO();
        $extension = JApplicationHelper::getComponentName();

		$query = 'SELECT id AS value, title AS text'
		. ' FROM #__categories'
		. ' WHERE extension = '.$db->Quote($extension)
		. ' AND published = 1'
		. ' ORDER BY '. $order
		;
		$db->setQuery( $query );
		
		if ( $sel_cat and $name!='catid') {
			$categories[] = JHtml::_('select.option',  '0', '- '. JText::_( 'Select a Category' ) .' -' );
			$categories[] = JHtml::_('select.option', '-1', 'Template Overrides');
			$categories = array_merge( $categories, $db->loadObjectList() );
		} else {
			$categories = $db->loadObjectList();
		}

		$category = JHtml::_('select.genericlist',   $categories, $name, 'class="inputbox" size="'. $size .'" '. $javascript, 'value', 'text', $active );
		return $category;
	}
}