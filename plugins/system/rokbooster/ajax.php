<?php
/**
 * @version   $Id: ajax.php 18872 2014-02-19 20:21:15Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2014 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

@ini_set('magic_quotes_runtime', 0);
@ini_set('zend.ze1_compatibility_mode', '0');

function get_absolute_path($path)
{
    $path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
    $parts = array_filter(explode(DIRECTORY_SEPARATOR, $path), 'strlen');
    $absolutes = array();
    foreach ($parts as $part) {
        if ('.' == $part) continue;
        if ('..' == $part) {
            array_pop($absolutes);
        } else {
            $absolutes[] = $part;
        }
    }
    $prefix = '';
    if (DIRECTORY_SEPARATOR == '/') $prefix = '/';
    return $prefix . implode(DIRECTORY_SEPARATOR, $absolutes);
}


//we know the path from root so we remove it to get the root
// '/../../..' for plugins; '/../..' for modules
define('_JEXEC', 1);
define('JPATH_BASE', get_absolute_path(dirname($_SERVER['SCRIPT_FILENAME']) . '/../../..'));
define('JPATH_MYWEBAPP', dirname($_SERVER['SCRIPT_FILENAME']));

require_once(JPATH_BASE . '/includes/defines.php');
require_once(JPATH_BASE . '/includes/framework.php');
require_once (JPATH_LIBRARIES . '/joomla/factory.php');
require_once (JPATH_LIBRARIES . '/import.php');
require_once (JPATH_LIBRARIES.'/cms.php');

// Pre-Load configuration.
ob_start();
require_once JPATH_CONFIGURATION.'/configuration.php';
ob_end_clean();

// Now that you have it, use jimport to get the specific packages your application needs.
jimport('joomla.environment.uri');
jimport('joomla.utilities.date');
jimport('joomla.utilities.utility');
jimport('joomla.event.dispatcher');
jimport('joomla.utilities.arrayhelper');
jimport('joomla.user.user');

//It's an application, so let's get the application helper.
jimport('joomla.application.helper');

$app = JFactory::getApplication('administrator');
$app->initialise();

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once (JPATH_MYWEBAPP . '/includes/application.php');

// load rokbooster libs
//require_once (dirname(__FILE__) . '/lib/include.php');
$renderer = new RTRenderer();
$renderer->render();