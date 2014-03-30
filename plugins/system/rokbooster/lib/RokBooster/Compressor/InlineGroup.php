<?php
/**
 * @version   $Id: InlineGroup.php 6811 2013-01-28 04:25:48Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2014 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
defined('ROKBOOSTER_LIB') or die('Restricted access');

/**
 *
 */
class RokBooster_Compressor_InlineGroup extends RokBooster_Compressor_AbstractGroup
{
	/**
	 * @var string
	 */
	protected $content = '';

	/**
	 * @var array
	 */
	protected $partial_checksums = array();

	/**
	 * @return string
	 */
	public function getChecksum()
	{
		if (!isset($this->checksum)) {
			$this->checksum = md5($this->content);
		}
		return $this->checksum;
	}

	/**
	 * @param string $inlineContent
	 */
	public function addItem($inlineContent)
	{
		$this->partial_checksums[] = md5($inlineContent);
		$this->content .= $inlineContent . "\n";
	}


	/**
	 * @param string $content
	 */
	public function setContent($content)
	{
		$this->content = $content;
	}

	/**
	 * @return string
	 */
	public function getContent()
	{
		return $this->content;
	}

	/**
	 * @return array
	 */
	public function getPartialChecksums()
	{
		return $this->partial_checksums;
	}
}