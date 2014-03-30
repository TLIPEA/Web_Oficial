<?php
/**
 * @version   $Id: IOutputContainer.php 18872 2014-02-19 20:21:15Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2014 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
defined('ROKBOOSTER_LIB') or die('Restricted access');

/**
 * Interface RokBooster_Compressor_IOutputContainer
 */
interface RokBooster_Compressor_IOutputContainer
{
	/**
	 * @abstract
	 *
	 * @param RokBooster_Compressor_IGroup $group
	 * @param                              $options
	 */
	public function __construct(RokBooster_Compressor_IGroup $group, $options, $wrapped = true);

	/**
	 * @abstract
	 *
	 * @return mixed
	 */
	public function isExpired();

	/**
	 * @abstract
	 *
	 * @return mixed
	 */
	public function doesExist();


	/**
	 * @abstract
	 *
	 * @return mixed
	 */
	public function getUrl();

	/**
	 * @abstract
	 *
	 * @return mixed
	 */
	public function getContent();


	/**
	 * @abstract
	 *
	 * @return mixed
	 */
	public function setAsValid();


	/**
	 * @abstract
	 *
	 * @param string $mimetype
	 *
	 * @return boolean
	 */
	public function write($mimetype = 'application/x-javascript');

}
