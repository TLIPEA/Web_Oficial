<?php
/**
* @version   $Id: branding.php 12813 2013-08-16 21:57:09Z arifin $
* @author    RocketTheme http://www.rockettheme.com
* @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*
* Gantry uses the Joomla Framework (http://www.joomla.org), a GNU/GPLv2 content management system
*
*/
defined('JPATH_BASE') or die();

gantry_import('core.gantryfeature');

class GantryFeatureBranding extends GantryFeature {
    var $_feature_name = 'branding';

	function render($position) {
	    ob_start();
	    ?>
	    <div class="rt-branding">
			<a href="http://www.gantry-framework.org/" title="Gantry Framework" class="rt-powered-by"></a>
		</div>
		<?php
	    return ob_get_clean();
	}
}