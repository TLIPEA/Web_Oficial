<?php
/**
 * @version   $Id: ISort.php 6811 2013-01-28 04:25:48Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2014 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
defined('ROKBOOSTER_LIB') or die('Restricted access');

/**
 *
 */
interface RokBooster_Compressor_ISort
{
	/**
	 *
	 */
	const IGNORED = 'ignored';
	/**
	 *
	 */
	const EXTERNAL = 'external';
	/**
	 *
	 */
	const INCLUDED = 'included';

	/**
	 * @abstract
	 *
	 * @param RokBooster_Compressor_File $file
	 *
	 * @return mixed
	 */
	public function addFile(RokBooster_Compressor_File $file);

	/**
	 * @abstract
	 * @return RokBooster_Compressor_FileGroup[]
	 */
	public function getGroups();
}
