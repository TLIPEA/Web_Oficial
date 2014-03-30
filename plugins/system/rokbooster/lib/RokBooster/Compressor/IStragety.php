<?php
/**
 * @version   $Id: IStragety.php 314 2012-04-27 02:48:00Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2014 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

defined('ROKBOOSTER_LIB') or die('Restricted access');

/**
 *
 */
interface RokBooster_Compressor_IStragety
{

	/**
	 * @abstract
	 */
	public function identify();

	/**
	 * @abstract
	 */
	public function populate();

	/**
	 * @abstract
	 */
	public function process();
}
