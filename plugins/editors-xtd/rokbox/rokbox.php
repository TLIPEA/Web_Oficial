<?php
/**
 * @version   $Id: rokbox.php 17060 2013-12-18 02:06:16Z djamil $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2014 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
jimport('joomla.plugin.plugin');

class plgButtonRokBox extends JPlugin{
    public function __construct(&$subject, $config)
    {
        parent::__construct($subject, $config);
    }

    function onDisplay($name, $asset, $author)
    {
        global $app;
        JHtml::_('behavior.modal');
        $doc = JFactory::getDocument();

        $link = $app->isAdmin() ? '..' : '';
        $link .= '/plugins/editors-xtd/rokbox/views/rokbox-picker.php?';
        $link .= 'textarea=' . $name;
        $link .= '&asset='.$asset;
        $link .= '&author='.$author;
        $link .= '&bp=' . urlencode(JURI::root());

        $version = new JVersion();
        $image_path = JURI::root(true) . '/plugins/editors-xtd/rokbox/assets/images/';

        if (version_compare($version->getShortVersion(), '3.0', '>=')){
            $style = ".btn .icon-linkrokbox {background: url(".$image_path."rokbox_14x14.png) 100% 0 no-repeat;}";
        } else {
            $style = ".button2-left .linkrokbox {background: url(".$image_path."rokbox-button.png) 100% 0 no-repeat;}";
        }

        $doc->addStyleDeclaration($style);

        $button = new JObject();
        $button->set('modal', true);
        $button->set('class', 'btn');
        $button->set('link', $link);
        $button->set('text', JText::_('RokBox'));
        $button->set('name', 'linkrokbox');
        $button->set('options', "{handler: 'iframe', size: {x: 520, y: 430}}");

        return $button;
    }

    /**
     * @return mixed
     */
    function onAfterRender()
    {
        $app = JFactory::getApplication();

        if ($app->isAdmin()) return;
    }
}
