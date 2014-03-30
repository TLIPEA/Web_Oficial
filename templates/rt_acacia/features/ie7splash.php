<?php
/**
* @version   $Id: ie7splash.php 12813 2013-08-16 21:57:09Z arifin $
* @author    RocketTheme http://www.rockettheme.com
* @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*
* Gantry uses the Joomla Framework (http://www.joomla.org), a GNU/GPLv2 content management system
*
*/

defined('JPATH_BASE') or die();

gantry_import('core.gantryfeature');
/**
 * @package     gantry
 * @subpackage  features
 */
class GantryFeatureIE7Splash extends GantryFeature {
    var $_feature_name = 'ie7splash';

    function isEnabled(){
    	if ($this->get('enabled')) {
        	return true;
        }
    }
    
    function isInPosition($position) {
        return false;
    }
    function isOrderable(){
        return true;
    }
    
    function init() {
        global $gantry;
        
        if (JFactory::getApplication()->input->getString('tmpl')!=='unsupported' && $gantry->browser->name == 'ie' && $gantry->browser->shortversion == '7') {
            header("Location: ".$gantry->baseUrl."?tmpl=unsupported");
        }
    }
}
