<?php
/**
 * @version   $Id: ICache.php 9561 2013-04-23 06:02:52Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
defined('ROKBOOSTER_LIB') or die('Restricted access');

interface RokBooster_Compressor_ICache
{
    /**
     * @abstract
     *
     * @param $checksum
     *
     * @return mixed
     */
    public function isCacheExpired($checksum);

    /**
     * @abstract
     *
     * @param $checksum
     *
     * @return mixed
     */
    public function doesCacheExist($checksum);

	/**
	 * @abstract
	 *
	 * @param        $checksum
	 * @param        $file_content
	 * @param bool   $addheaders
	 *
	 * @param string $mimetype
	 *
	 * @internal param string $type
	 * @return mixed
	 */
    public function write($checksum, $file_content, $addheaders = true, $mimetype ='application/x-javascript');

    /**
     * @abstract
     *
     * @param $checksum
     *
     * @return mixed
     */
    public function getCacheUrl($checksum);

    /**
     * @abstract
     *
     * @param $checksum
     *
     * @return mixed
     */
    public function getCacheContent($checksum);


	/**
	 * @abstract
	 *
	 * @param $checksum
	 *
	 * @return mixed
	 */
	public function setCacheAsValid($checksum);
}
