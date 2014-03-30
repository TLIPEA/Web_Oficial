<?php
/**
 * @version   $Id: Apc.php 18890 2014-02-20 15:46:17Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2014 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
defined('ROKBOOSTER_LIB') or die('Restricted access');

/**
 *
 */
class RokBooster_Compressor_OutputContainer_APC extends RokBooster_Compressor_AbstractOutputContainer
{

	protected $cache_entry_id;

	public function __construct(RokBooster_Compressor_IGroup $group, $options, $wrapped = true)
	{
		parent::__construct($group, $options);
		$config = JFactory::getConfig();
		$hash = md5($config->get('secret').serialize($this->options));
		$this->cache_entry_id = $hash.'-rokbooster-dataentry-' . $this->group->getChecksum();
	}

	public function isDataExpired()
	{
		$exists_in_apc = false;
		if (function_exists('apc_exists') && apc_exists($this->cache_entry_id)) {
			$exists_in_apc = true;
		}
		return !$exists_in_apc;
	}

	public function doesDataExist()
	{
		$exists_in_apc = false;
		if (function_exists('apc_exists') && apc_exists($this->cache_entry_id)) {
			$exists_in_apc = true;
		}
		return $exists_in_apc;
	}

	/**
	 * @return bool|string
	 */
	public function getContent()
	{
		if (function_exists('apc_fetch') && apc_exists($this->cache_entry_id)) {
			return apc_fetch($this->cache_entry_id);
		}
		return '';
	}

	protected function setDataAsValid()
	{
		return true;
	}

	/**
	 *  Write the data to a file
	 */
	protected function writeData($usingWrapper = true)
	{
		if (function_exists('apc_store')) {
			apc_store($this->cache_entry_id, $this->group->getResult());
		}
	}

	/**
	 * Get the text lines of PHP to include in the serving wrapper to pull the content
	 * when the serving wrapper is executed
	 *
	 * @return mixed
	 */
	protected function getServingWrapperContentLines()
	{
		return "if (function_exists('apc_fetch') && apc_exists('{$this->cache_entry_id}')){ echo apc_fetch('{$this->cache_entry_id}');}";
	}
}
