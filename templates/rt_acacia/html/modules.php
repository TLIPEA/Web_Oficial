<?php
/**
* @version   $Id: modules.php 13277 2013-09-05 15:49:44Z arifin $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
* Gantry uses the Joomla Framework (http://www.joomla.org), a GNU/GPLv2 content management system
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * This is a file to add template specific chrome to module rendering.  To use it you would
 * set the style attribute for the given module(s) include in your template to use the style
 * for each given modChrome function.
 *
 * eg.  To render a module mod_test in the sliders style, you would use the following include:
 * <jdoc:include type="module" name="test" style="slider" />
 *
 * This gives template designers ultimate control over how modules are rendered.
 *
 * NOTICE: All chrome wrapping methods should be named: modChrome_{STYLE} and take the same
 * two arguments.
 */

function modChrome_submenu($module, &$params, &$attribs)
{
global $Itemid;
		global $gantry;
	
	$start	= intval($params->get('submenu-startLevel'));

	$tabmenu = JFactory::getApplication()->getMenu();
	$item = $tabmenu->getActive();
	
	$level = (isset($item) && isset($item->tree)) ? sizeof($item->tree) : 0;

	if (isset($item) && $start <= $level) {
        $menu_title = "";

        if ($start == 0) $start = 1;

        $menu_title_item_id = $item->tree[$start-1];
        $menu_title_item = $tabmenu->getItem($menu_title_item_id);
	
	if (!empty ($module->content) && $module->content != '') : ?>
	<div class="rt-block <?php if ($params->get('submenu-module_sfx')!='') : ?><?php echo $params->get('submenu-module_sfx'); ?><?php endif; ?>">
		<div class="module-surround">
			<?php if ($gantry->get('menu-splitmenu-submenu-title')): ?>
			<div class="module-title">
		        <h2 class="title"><?php echo $menu_title_item->title.' '.JText::_('Menu'); ?></h2>
			</div>
			<?php endif; ?>
			<div class="module-content submenu">
		    	<?php echo $module->content; ?>
			</div>
		</div>
	</div>
	<?php endif;
	}
}

function modChrome_basic($module, &$params, &$attribs)
{
	if (!empty ($module->content)) : ?>
		<?php echo $module->content; ?>
	<?php endif;
}

function modChrome_standard($module, &$params, &$attribs)
{
 	if (!empty ($module->content)) : ?>
 			<?php
 				$suffixes = $params->get('moduleclass_sfx');
 			?>
            <div class="rt-block <?php if ($suffixes!='') : ?><?php echo $suffixes; ?><?php endif; ?>">
           	<div class="module-surround">
	           	<?php if ($module->showtitle != 0) : ?>
			<div class="module-title">
					<?php
		            echo '<h2 class="title">';
						if (preg_match("/icon[-]{1,}/i", $suffixes)) :
		     				echo '<span class="title-icon ' .getIconClass($suffixes). '"></span>';
		     			endif;
		     			echo $module->title;
		     		echo '</h2>';
					?>
			</div>
	                	<?php endif; ?>
	                	<div class="module-content">
	                		<?php echo $module->content; ?>
	                	</div>
                	</div>
           </div>
	<?php endif;
}

function modChrome_menu($module, &$params, &$attribs)
{
    	
	if (!empty ($module->content)) : ?>
	<div class="menu-block">
		<?php echo $module->content; ?>
		<div class="clear"></div>
	</div>
	<?php endif;
}

function modChrome_popup($module, &$params, &$attribs)
{
 	if (!empty ($module->content)) : ?>
	<div class="rt-block basic">
		<div class="module-content">
			<?php if ($module->showtitle != 0) : ?>
			<h2 class="title"><?php echo $module->title; ?></h2>
			<?php endif; ?>
			<div class="module-inner">
               	<?php echo $module->content; ?>
			</div>
		</div>
	</div>
	<?php endif;
}

function getIconClass($modsfx){
	$class = ""; 
	$parts = explode(" ", $modsfx); 
	foreach($parts as $part){
		if(strpos($part, "icon-")!==false){
			$class = " ".$part;
		}
	} 
	return $class;
}

?>
