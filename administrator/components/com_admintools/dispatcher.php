<?php
/**
 *  @package AdminTools
 *  @copyright Copyright (c)2010-2014 Nicholas K. Dionysopoulos
 *  @license GNU General Public License version 3, or later
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

class AdmintoolsDispatcher extends FOFDispatcher
{
	public function onBeforeDispatch() {
		$result = parent::onBeforeDispatch();

		if($result) {
			// Merge the language overrides
			$paths = array(JPATH_ROOT, JPATH_ADMINISTRATOR);
			$jlang = JFactory::getLanguage();
			$jlang->load($this->component, $paths[0], 'en-GB', true);
			$jlang->load($this->component, $paths[0], null, true);
			$jlang->load($this->component, $paths[1], 'en-GB', true);
			$jlang->load($this->component, $paths[1], null, true);

			$jlang->load($this->component.'.override', $paths[0], 'en-GB', true);
			$jlang->load($this->component.'.override', $paths[0], null, true);
			$jlang->load($this->component.'.override', $paths[1], 'en-GB', true);
			$jlang->load($this->component.'.override', $paths[1], null, true);

			// Load Akeeba Strapper
			if(!defined('ADMINTOOLSMEDIATAG')) {
				$staticFilesVersioningTag = md5(ADMINTOOLS_VERSION.ADMINTOOLS_DATE);
				define('ADMINTOOLSMEDIATAG', $staticFilesVersioningTag);
			}
			include_once JPATH_ROOT.'/media/akeeba_strapper/strapper.php';
			AkeebaStrapper::$tag = ADMINTOOLSMEDIATAG;
			AkeebaStrapper::bootstrap();
			AkeebaStrapper::jQueryUI();
			AkeebaStrapper::addCSSfile('media://com_admintools/css/backend.css');

			// Work around non-transparent proxy and reverse proxy IP issues
			include_once JPATH_ADMINISTRATOR.'/components/com_admintools/helpers/ip.php';
			if(class_exists('AdmintoolsHelperIp', false)) {
				AdmintoolsHelperIp::workaroundIPIssues();
			}

			// Control Check
			$view = FOFInflector::singularize($this->input->getCmd('view',$this->defaultView));

			if ($view == 'liveupdate')
			{
				$url = JUri::base() . 'index.php?option=com_admintools';
				JFactory::getApplication()->redirect($url);
				return;
			}

			// ========== Master PW check ==========
			$model = FOFModel::getAnInstance('Masterpw','AdmintoolsModel');
			if(!$model->accessAllowed($view))
			{
				$url = ($view == 'cpanel') ? 'index.php' : 'index.php?option=com_admintools&view=cpanel';
				JFactory::getApplication()->redirect($url, JText::_('ATOOLS_ERR_NOTAUTHORIZED'), 'error');
				return;
			}
		}

		return $result;
	}
}