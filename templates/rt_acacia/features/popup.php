<?php
/**
* @version   $Id: popup.php 13276 2013-09-05 15:02:30Z arifin $
* @author    RocketTheme http://www.rockettheme.com
* @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*
* Gantry uses the Joomla Framework (http://www.joomla.org), a GNU/GPLv2 content management system
*
*/

defined('JPATH_BASE') or die();

gantry_import('core.gantryfeature');

class GantryFeaturePopup extends GantryFeature 
{
    var $_feature_name = 'popup';

	function render($position)
	{
		global $gantry;
	    ob_start();

	    $user = JFactory::getUser();
	    
	    ?>
		<div class="rt-popupmodule-button">
			<?php echo $this->_renderRokBoxLink(); ?>
				<span class="desc"><?php echo $this->get('text'); ?></span>
			</a>
		</div>
		<?php
	    return ob_get_clean();
	}

	function _renderRokBoxLink(){
		$isRokBox2 = @file_exists(JPATH_BASE . '/plugins/editors-xtd/rokbox/rokbox.xml');
		$output = array();

		if ($isRokBox2){
			$output[] = '<a class="buttontext button" data-rokbox href="#" data-rokbox-element="#rt-popupmodule">';
		} else {
			$output[] = '<a href="#" class="buttontext button" rel="rokbox['.$this->get('width').' '.$this->get('height').'][module=rt-popupmodule]">';
		}

		return implode("\n", $output);
	}
}