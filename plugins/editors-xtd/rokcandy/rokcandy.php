<?php
/**
 * @version $Id: rokcandy.php 5112 2012-11-08 23:59:29Z btowles $
 * @author RocketTheme, LLC http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

class plgButtonRokCandy extends JPlugin
{

    public function __construct(& $subject, $config)
    {
        parent::__construct($subject, $config);
        $this->loadLanguage();
    }

	function onDisplay($name)
	{

        JHtml::_('behavior.modal');
		$doc = JFactory::getDocument();
        $content = $this->_subject->getContent($name);

		$js = "
		function insertMacro(macro) {
			jInsertEditorText(macro, '".$name."');
			SqueezeBox.close();
		}";

		$doc->addScriptDeclaration($js);

        $version = new JVersion();

        if (version_compare($version->getShortVersion(), '3.0', '>=')) {
            $declaration =" .btn .icon-linkmacro 	{ background: url(".JURI::base(true)."/components/com_rokcandy/assets/rokcandy-icon-14.png) 100% 0 no-repeat; } ";
        } else {
		    $declaration =" .button2-left .linkmacro 	{ background: url(".JURI::base(true)."/components/com_rokcandy/assets/button.png) 100% 0 no-repeat; } ";
        }
		$doc->addStyleDeclaration($declaration);
		
		$link = 'index.php?option=com_rokcandy&amp;view=candymacros&amp;layout=modal&amp;tmpl=component&amp;function=insertMacro';
        
        $button = new JObject();
		$button->set('modal', true);
		$button->set('link', $link);
		$button->set('text', JText::_('RokCandy Macros'));
		$button->set('name', 'linkmacro');
        $button->set('options', "{handler: 'iframe', size: {x: 770, y: 400}}");

		return $button;
	}
}