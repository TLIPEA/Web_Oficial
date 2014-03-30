<?php
/**
 * @version   $Id: AbstractGroup.php 6811 2013-01-28 04:25:48Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2014 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

defined('ROKBOOSTER_LIB') or die('Restricted access');

/**
 *
 */
abstract class RokBooster_Compressor_AbstractGroup implements RokBooster_Compressor_IGroup
{


	/**
	 * @var string
	 */
	protected $status;

	/**
	 * @var string the mime type of the files in the group
	 */
	protected $mime;

	/**
	 * @var string[] the name/value pair attributes for the file group
	 */
	protected $attributes = array();


	/**
	 * @var string
	 */
	protected $result;

	/**
	 * @var string
	 */
	protected $checksum;


	/**
	 * @param       $status
	 * @param       $mime
	 * @param array $attributes
	 */
	public function __construct($status, $mime, $attributes = array())
	{
		$this->status     = $status;
		$this->mime       = $mime;
		$this->attributes = $attributes;
	}

	/**
	 * @param $status
	 */
	public function setStatus($status)
	{
		$this->status = $status;
	}

	/**
	 * @return mixed
	 */
	public function getStatus()
	{
		return $this->status;
	}

	/**
	 * @param $attributes
	 */
	public function setAttributes($attributes)
	{
		$this->attributes = $attributes;
	}

	/**
	 * @param $key
	 * @param $value
	 */
	public function addAttribute($key, $value)
	{
		$this->attributes[$key] = $value;
	}

	/**
	 * @return array|string[]
	 */
	public function getAttributes()
	{
		return $this->attributes;
	}

	/**
	 * @param string $mime
	 */
	public function setMime($mime)
	{
		$this->mime = $mime;
	}

	/**
	 * @return string
	 */
	public function getMime()
	{
		return $this->mime;
	}

	/**
	 * @param string $result
	 */
	public function setResult($result)
	{
		$this->result = $result;
	}

	/**
	 * @return string
	 */
	public function getResult()
	{
		return $this->result;
	}

	/**
	 *
	 */
	public function cleanup()
	{
		$this->result = '';
	}
}

