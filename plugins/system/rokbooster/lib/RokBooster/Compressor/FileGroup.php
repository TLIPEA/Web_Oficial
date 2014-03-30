<?php
/**
 * @version   $Id: FileGroup.php 18872 2014-02-19 20:21:15Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2014 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

defined('ROKBOOSTER_LIB') or die('Restricted access');

/**
 *
 */
class RokBooster_Compressor_FileGroup extends RokBooster_Compressor_AbstractGroup implements IteratorAggregate, ArrayAccess, Countable
{


	/**
	 * @var RokBooster_Compressor_File[]
	 */
	protected $files = array();

	/**
	 * @return string
	 */
	public function getChecksum()
	{
		$files_copy = $this->files;
		if (!isset($this->checksum)) {
			array_multisort($files_copy, SORT_ASC, SORT_STRING);
			$server_check = '';
			if (isset($_SERVER['LOCAL_ADDR'])) $server_check = $_SERVER["LOCAL_ADDR"];
			if (isset($_SERVER['SERVER_ADDR'])) $server_check = $_SERVER["SERVER_ADDR"];
			$this->checksum = md5(implode('', $files_copy).$server_check);
		}
		return $this->checksum;
	}

	/**
	 * @param RokBooster_Compressor_File $file
	 */
	public function addItem(RokBooster_Compressor_File $file)
	{
		if (!in_array($file->file, $this->files)) {
			$this->files[] = $file;
		}
	}


	/**
	 * @param RokBooster_Compressor_File $file
	 *
	 * @return bool
	 */
	public function fileBelongs(RokBooster_Compressor_File $file)
	{
		$belong     = false;
		$file_state = self::STATE_IGNORE;
		if (!$file->isExternal() && !$file->isIgnored()) {
			$file_state = self::STATE_INCLUDE;
		}
		if ($this->status == $file_state) {
			$belong = true;
		}
		return $belong;
	}

	/**
	 * @return ArrayIterator
	 */
	public function getIterator()
	{
		return new ArrayIterator($this->files);
	}

	/**
	 * @param $offset
	 *
	 * @return bool
	 */
	public function offsetExists($offset)
	{
		return isset($this->files);
	}

	/**
	 * @param $offset
	 * @param $value
	 */
	public function offsetSet($offset, $value)
	{
		$this->files[$offset] = $value;
	}

	/**
	 * @param $offset
	 *
	 * @return RokBooster_Compressor_File
	 */
	public function offsetGet($offset)
	{
		return $this->files[$offset];
	}

	/**
	 * @param $offset
	 */
	public function offsetUnset($offset)
	{
		unset($this->files[$offset]);
	}

	/**
	 * @return int|void
	 */
	public function count()
	{
		return count($this->files);
	}


	/**
	 *
	 */
	public function cleanup()
	{
		parent::cleanup();
		foreach ($this->files as &$file) {
			$file->content = null;
		}
	}
}