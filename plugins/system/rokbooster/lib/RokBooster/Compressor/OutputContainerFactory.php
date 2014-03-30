<?php

/**
 * @version   $Id: OutputContainerFactory.php 18873 2014-02-19 20:36:19Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2014 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
class RokBooster_Compressor_OutputContainerFactory
{


	/**
	 * @var RokBooster_Compressor_IOutputContainer[]
	 */
	protected static $outputContainers = array();

	/**
	 * @param RokBooster_Compressor_IGroup $group
	 * @param                              $options
	 * @param bool                         $wrapped
	 *
	 * @throws Exception
	 * @return RokBooster_Compressor_IOutputContainer
	 */
	public static function create(RokBooster_Compressor_IGroup $group, $options, $wrapped = true)
	{
		if (!array_key_exists($group->getChecksum(), self::$outputContainers)) {
			$output_container_class = 'RokBooster_Compressor_OutputContainer_' . ucfirst($options->data_storage);
			if (!class_exists($output_container_class)) {
				throw new Exception('Unable to find Output container for data storage ' . $options->data_storage, 500);
			}
			self::$outputContainers[$group->getChecksum()] = new $output_container_class($group, $options, $wrapped);
		}
		return self::$outputContainers[$group->getChecksum()];
	}
}
 