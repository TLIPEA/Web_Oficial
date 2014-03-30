<?php
/**
 * @version   $Id: styledeclaration.php 13917 2013-09-29 17:26:46Z arifin $
 * @author		RocketTheme http://www.rockettheme.com
 * @copyright 	Copyright (C) 2007 - 2013 RocketTheme, LLC
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 * Gantry uses the Joomla Framework (http://www.joomla.org), a GNU/GPLv2 content management system
 *
 */
defined('JPATH_BASE') or die();

gantry_import('core.gantryfeature');

class GantryFeatureStyleDeclaration extends GantryFeature {
    var $_feature_name = 'styledeclaration';

    function isEnabled() {
		global $gantry;
        $menu_enabled = $this->get('enabled');

        if (1 == (int)$menu_enabled) return true;
        return false;
    }

	function init() {
		global $gantry;
		$browser = $gantry->browser;

        // Logo
    	$css = $this->buildLogo();

        // Section Background Images
    	$lessVariables = array(
            'showcase-overlay'        => $gantry->get('showcase-overlay',       'dark'),
            'showcase-background'     => $gantry->get('showcase-background',    '#70BDCD'),
            'feature-background'      => $gantry->get('feature-background',     '#F2EEE5'),
            'maintop-background'      => $gantry->get('maintop-background',     '#524243'),
            'main-body-style'         => $gantry->get('main-body-style',        'light'),
            'mainbottom-background'   => $gantry->get('mainbottom-background',  '#524243'),
            'main-accent'             => $gantry->get('main-accent',            '#EA453B'),
            'main-accent2'            => $gantry->get('main-accent2',           '#E0583E'),
            'footer-overlay'          => $gantry->get('footer-overlay',         'dark'),
            'footer-background'       => $gantry->get('footer-background',      '#3F3F3F')
    	);

        $positions = array('showcase-customshowcase', 'footer-customfooter');
        $source = "";

        foreach ($positions as $position) {
                $data = $gantry->get($position . '-image', false) ? json_decode(str_replace("'", '"', $gantry->get($position . '-image'))) : false;
                if ($data) $source = $data->path;
                if (!preg_match('/^\//', $source)) $source = JURI::root(true).'/'.$source;
                $lessVariables[$position . '-image'] = $data ? 'url(' . $source . ')' : 'none';
        }

       	$gantry->addLess('global.less', 'master.css', 8, $lessVariables);

	    $this->_disableRokBoxForiPhone();

        $gantry->addInlineStyle($css);
        if ($gantry->get('layout-mode')=="responsive") $gantry->addLess('mediaqueries.less');
        if ($gantry->get('layout-mode')=="960fixed")   $gantry->addLess('960fixed.less');
        if ($gantry->get('layout-mode')=="1200fixed")  $gantry->addLess('1200fixed.less');

        // RTL
        if ($gantry->get('rtl-enabled')) $gantry->addLess('rtl.less');

        // Demo Styling
        if ($gantry->get('demo')) $gantry->addLess('demo.less', 'demo.css', 9, $lessVariables);

        // Canvas Scripts
        if ($gantry->get('canvas-enabled')) $gantry->addScripts(array('moofx.js', 'progress.js', 'progress_init.js'));


        // Chart Script
        if ($gantry->get('chart-enabled')) $gantry->addScript('chart.js');

        // Third Party (k2)
        if ($gantry->get('k2')) $gantry->addLess('thirdparty-k2.less', 'thirdparty-k2.css', 10, $lessVariables);
	}

    function buildLogo(){
		global $gantry;

        if ($gantry->get('logo-type')!="custom") return "";

        $source = $width = $height = "";

        $logo = str_replace("&quot;", '"', str_replace("'", '"', $gantry->get('logo-custom-image')));
        $data = json_decode($logo);

        if (!$data){
            if (strlen($logo)) $source = $logo;
            else return "";
        } else {
            $source = $data->path;
        }

        if (substr($gantry->baseUrl, 0, strlen($gantry->baseUrl)) == substr($source, 0, strlen($gantry->baseUrl))){
            $file = JPATH_ROOT . '/' . substr($source, strlen($gantry->baseUrl));
        } else {
            $file = JPATH_ROOT . '/' . $source;
        }

        if (isset($data->width) && isset($data->height)){
            $width = $data->width;
            $height = $data->height;
        } else {
            $size = @getimagesize($file);
            $width = $size[0];
            $height = $size[1];
        }

        if (!preg_match('/^\//', $source))
        {
            $source = JURI::root(true).'/'.$source;
        }

        $source = str_replace(' ', '%20', $source);

        $output = "";
        $output .= "#rt-logo {background: url(".$source.") 50% 0 no-repeat !important;}"."\n";
        $output .= "#rt-logo {width: ".$width."px;height: ".$height."px;}"."\n";

        $file = preg_replace('/\//i', DIRECTORY_SEPARATOR, $file);

        return (file_exists($file)) ?$output : '';
    }

	function _disableRokBoxForiPhone() {
		global $gantry;

		if ($gantry->browser->platform == 'iphone' || $gantry->browser->platform == 'android') {
			$gantry->addInlineScript("window.addEvent('domready', function() {\$\$('a[rel^=rokbox]').removeEvents('click');});");
		}
	}
}
