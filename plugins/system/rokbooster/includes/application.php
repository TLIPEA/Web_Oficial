<?php
/**
 * @version   $Id: application.php 18876 2014-02-19 20:47:01Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2014 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

// no direct access
defined('JPATH_PLATFORM') or die;

class RTRenderer
{
	/**
	 * Display the application.
	 */
	public function render()
	{
		$user = JFactory::getUser();
		$conf = JFactory::getConfig();
		if ($user->id != 0) {
			// generate and empty object
			$plgParams = new JRegistry();
			// get plugin details
			$plugin = JPluginHelper::getPlugin('system', 'rokbooster');
			// load params into our params object
			if ($plugin && isset($plugin->params)) {
				$plgParams->loadString($plugin->params);
			}

			if ($user->authorise('core.admin', 'com_cache')) {
				$file_cache = new JCache(array(
					'defaultgroup' => 'rokbooster',
					'caching'      => true,
					'checkTime'    => true,
					'storage'      => 'file',
					'cachebase'    => JPATH_CACHE
				));

				$file_info_cache = new JCache(array(
					'defaultgroup' => 'rokbooster',
					'caching'      => true,
					'checkTime'    => false,
				));

				$generator_state_cache = new JCache(array(
					'cachebase'    => $conf->get('cache_path', JPATH_CACHE),
					'lifetime'     => 120,
					'storage'      => $conf->get('cache_handler', 'file'),
					'defaultgroup' => 'rokbooster',
					'locking'      => true,
					'locktime'     => 15,
					'checkTime'    => true,
					'caching'      => true
				));
				$generator_state_cache->clean();
				$file_cache->clean();
				$file_info_cache->clean();

				$files     = $file_cache->getAll();
				$filecount = 0;
				if (is_array($files) && array_key_exists('rokbooster', $files)) {
					$filecount = $files['rokbooster']->count;
				}

				if ($plgParams->get('data_storage', 'default') == 'apc' && function_exists('apc_store')) {
					$config = JFactory::getConfig();
					$hash   = preg_quote(md5($config->get('secret')));

					if (class_exists('APCIterator')) {
						$entries = new APCIterator('user', "/^{$hash}-rokbooster-dataentry-/");
						apc_delete($entries);
					} else {
						$info = apc_cache_info('user');
						foreach ($info['cache_list'] as $apc_cache_entry) {
							if (strpos($apc_cache_entry['info'], "{$hash}-rokbooster-dataentry-") === 0) {
								apc_delete($apc_cache_entry['info']);
							}
						}
					}
				}

				echo sprintf('{"status":"success","message":"%d"}', $filecount);
			} else {
				echo '{"status": "error","message":"You do not have permissions to clear cache."}';
			}
		}
	}
}