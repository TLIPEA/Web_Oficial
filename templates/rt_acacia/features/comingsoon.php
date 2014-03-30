<?php
/**
* @version   $Id: comingsoon.php 13795 2013-09-25 21:45:45Z arifin $
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
class GantryFeatureComingSoon extends GantryFeature {
    var $_feature_name = 'comingsoon';

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
        
        if (JFactory::getApplication()->input->getString('tmpl')!=='comingsoon') {
            header("Location: ".$gantry->baseUrl."?tmpl=comingsoon");
        }
    }
}
