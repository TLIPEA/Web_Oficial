<?php
/**
 * @version   $Id: Default.php 18890 2014-02-20 15:46:17Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2014 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
defined('ROKBOOSTER_LIB') or die('Restricted access');

/**
 *
 */
class RokBooster_Compressor_OutputContainer_Default extends RokBooster_Compressor_AbstractOutputContainer
{
	/**
	 *
	 */
	const DATA_FILE_EXTENSION = '_data.php';

	protected $datafile_name;
	protected $datafile_path;

	public function __construct(RokBooster_Compressor_IGroup $group, $options, $wrapped = true)
	{
		parent::__construct($group, $options, $wrapped);
		if($wrapped){
			$this->datafile_name = md5(serialize($this->options) . '-' . $this->group->getChecksum()).self::DATA_FILE_EXTENSION;
			$this->datafile_path = preg_replace('#[/\\\\]+#', DIRECTORY_SEPARATOR, $this->options->cache_path) . DIRECTORY_SEPARATOR . $this->datafile_name;
		}
		else {
			$this->datafile_name = $this->wrapper_file_name;
			$this->datafile_path = $this->wrapper_file_path;
		}
	}


	/**
	 * @return bool|string
	 */
	public function getContent()
	{
		if (file_exists($this->datafile_path)) {
			return file_get_contents_utf8($this->datafile_path);
		}
		return '';
	}

	protected function setDataAsValid()
	{
		if (file_exists($this->datafile_path)) {
			touch($this->datafile_path);
		}
	}

	protected function isDataExpired()
	{
		//see if file is stale
		$expired = true;
		if (file_exists($this->datafile_name)) {
			$expired = ((int)strtotime('now') > ((int)filectime($this->datafile_name) + (int)$this->options->cache_time)) ? true : false;
		}
		return $expired;
	}

	protected function doesDataExist()
	{
		$exists = false;
		if (file_exists($this->datafile_path)) {
			$exists = true;
		}
		return $exists;
	}

	/**
	 *  Write the data to a file
	 */
	protected function writeData()
	{
		$this->writeFile($this->datafile_name, $this->group->getResult());
	}

	/**
	 * Get the text lines of PHP to include in the serving wrapper to pull the content
	 * when the serving wrapper is executed
	 *
	 * @return mixed
	 */
	protected function getServingWrapperContentLines()
	{
		return "echo file_get_contents_utf8(dirname(__FILE__) . DIRECTORY_SEPARATOR . '{$this->datafile_name}');";
	}
}
