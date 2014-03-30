<?php
/**
 * @version                              $Id: Image.php 10831 2013-05-29 19:32:17Z btowles $
 * @author                               RocketTheme http://www.rockettheme.com
 * @copyright                            Copyright (C) 2007 - 2014 RocketTheme, LLC
 * @license                              http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 * Derived from
 *
 * PhpThumb Library Definition File
 *
 * This file contains the definitions for the PhpThumbFactory class.
 * It also includes the other required base class files.
 *
 * If you've got some auto-loading magic going on elsewhere in your code, feel free to
 * remove the include_once statements at the beginning of this file... just make sure that
 * these files get included one way or another in your code.
 *
 * PHP Version 5 with GD 2.0+
 * PhpThumb : PHP Thumb Library <http://phpthumb.gxdlabs.com>
 * Copyright (c) 2009, Ian Selby/Gen X Design
 *
 * Author(s): Ian Selby <ian@gen-x-design.com>
 *
 * Licensed under the MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @author                               Ian Selby <ian@gen-x-design.com>
 * @copyright                            Copyright (c) 2009 Gen X Design
 * @link                                 http://phpthumb.gxdlabs.com
 * @license                              http://www.opensource.org/licenses/mit-license.php The MIT License
 * @version                              3.0
 * @package                              PhpThumb
 * @filesource
 */

// define some useful constants
define('ROKCOMMON_IMAGE_BASE_PATH', ROKCOMMON_LIB_PATH.'/RokCommon');
define('ROKCOMMON_IMAGE_PLUGIN_PATH', ROKCOMMON_IMAGE_BASE_PATH . '/Plugin/');

class RokCommon_Image_Exception extends Exception
{

}


/**
 * PhpThumbFactory Object
 *
 * This class is responsible for making sure everything is set up and initialized properly,
 * and returning the appropriate thumbnail class instance.  It is the only recommended way
 * of using this library, and if you try and circumvent it, the sky will fall on your head :)
 *
 * Basic use is easy enough.  First, make sure all the settings meet your needs and environment...
 * these are the static variables defined at the beginning of the class.
 *
 * Once that's all set, usage is pretty easy.  You can simply do something like:
 * <code>$thumb = PhpThumbFactory::create('/path/to/file.png');</code>
 *
 * Refer to the documentation for the create function for more information
 *
 * @package    PhpThumb
 * @subpackage Core
 */
class RokCommon_Image
{

	const DEFAULT_IMPLEMENTATION = 'gd';

	/**
	 * Where the plugins can be loaded from
	 *
	 * Note, it's important that this path is properly defined.  It is very likely that you'll
	 * have to change this, as the assumption here is based on a relative path.
	 *
	 * @var string
	 */
	public static $pluginPath = ROKCOMMON_IMAGE_PLUGIN_PATH;

	/**
	 * Factory Function
	 *
	 * This function returns the correct thumbnail object, augmented with any appropriate plugins.
	 * It does so by doing the following:
	 *  - Getting an instance of PhpThumb
	 *  - Loading plugins
	 *  - Validating the default implemenation
	 *  - Returning the desired default implementation if possible
	 *  - Returning the GD implemenation if the default isn't available
	 *  - Throwing an exception if no required libraries are present
	 *
	 * @return RokCommon_Image_ImageType
	 * @uses PhpThumb
	 *
	 * @param string $filename The path and file to load [optional]
	 * @param array  $options
	 * @param bool   $isDataStream
	 */
	public static function create($filename = null, array $options = array(), $isDataStream = false)
	{
		// map our implementation to their class names
		$implementationMap = array(
			'gd'		 => 'RokCommon_Image_ImageType_GD'
		);

		// grab an instance of PhpThumb
		$pt = self::getInstance();
		// load the plugins
		$pt->loadPlugins(self::$pluginPath);

		$toReturn       = null;
		$implementation = self::DEFAULT_IMPLEMENTATION;

		// attempt to load the default implementation
		if ($pt->isValidImplementation(self::DEFAULT_IMPLEMENTATION)) {
			$imp      = $implementationMap[self::DEFAULT_IMPLEMENTATION];
			$toReturn = new $imp($filename, $options, $isDataStream);
		} // load the gd implementation if default failed
		else if ($pt->isValidImplementation('gd')) {
			$imp            = $implementationMap['gd'];
			$implementation = 'gd';
			$toReturn       = new $imp($filename, $options, $isDataStream);
		} // throw an exception if we can't load
		else {
			throw new RokCommon_Image_Exception('You must have either the GD extension loaded to use this library');
		}

		$registry = $pt->getPluginRegistry($implementation);
		$toReturn->importPlugins($registry);
		return $toReturn;
	}


	/**
	 * Instance of self
	 *
	 * @var RokCommon_Image
	 */
	protected static $_instance;
	/**
	 * The plugin registry
	 *
	 * This is where all plugins to be loaded are stored.  Data about the plugin is
	 * provided, and currently consists of:
	 *  - loaded: true/false
	 *  - implementation: gd/imagick/both
	 *
	 * @var array
	 */
	protected $_registry;
	/**
	 * What implementations are available
	 *
	 * This stores what implementations are available based on the loaded
	 * extensions in PHP, NOT whether or not the class files are present.
	 *
	 * @var array
	 */
	protected $_implementations;

	/**
	 * Returns an instance of self
	 *
	 * This is the usual singleton function that returns / instantiates the object
	 *
	 * @return RokCommon_Image
	 */
	public static function getInstance()
	{
		if (!(self::$_instance instanceof self)) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Class constructor
	 *
	 * Initializes all the variables, and does some preliminary validation / checking of stuff
	 *
	 */
	private function __construct()
	{
		$this->_registry        = array();
		$this->_implementations = array(
			'gd'      => false,
			'imagick' => false
		);

		$this->getImplementations();
	}

	/**
	 * Finds out what implementations are available
	 *
	 * This function loops over $this->_implementations and validates that the required extensions are loaded.
	 *
	 * I had planned on attempting to load them dynamically via dl(), but that would provide more overhead than I
	 * was comfortable with (and would probably fail 99% of the time anyway)
	 *
	 */
	private function getImplementations()
	{
		foreach ($this->_implementations as $extension => $loaded) {
			if ($loaded) {
				continue;
			}

			if (extension_loaded($extension)) {
				$this->_implementations[$extension] = true;
			}
		}
	}

	/**
	 * Returns whether or not $implementation is valid (available)
	 *
	 * If 'all' is passed, true is only returned if ALL implementations are available.
	 *
	 * You can also pass 'n/a', which always returns true
	 *
	 * @return bool
	 *
	 * @param string $implementation
	 */
	public function isValidImplementation($implementation)
	{
		if ($implementation == 'n/a') {
			return true;
		}

		if ($implementation == 'all') {
			foreach ($this->_implementations as $imp => $value) {
				if ($value == false) {
					return false;
				}
			}

			return true;
		}

		if (array_key_exists($implementation, $this->_implementations)) {
			return $this->_implementations[$implementation];
		}

		return false;
	}

	/**
	 * Registers a plugin in the registry
	 *
	 * Adds a plugin to the registry if it isn't already loaded, and if the provided
	 * implementation is valid.  Note that you can pass the following special keywords
	 * for implementation:
	 *  - all - Requires that all implementations be available
	 *  - n/a - Doesn't require any implementation
	 *
	 * When a plugin is added to the registry, it's added as a key on $this->_registry with the value
	 * being an array containing the following keys:
	 *  - loaded - whether or not the plugin has been "loaded" into the core class
	 *  - implementation - what implementation this plugin is valid for
	 *
	 * @return bool
	 *
	 * @param string $pluginName
	 * @param string $implementation
	 */
	public function registerPlugin($pluginName, $implementation)
	{
		if (!array_key_exists($pluginName, $this->_registry) && $this->isValidImplementation($implementation)) {
			$this->_registry[$pluginName] = array(
				'loaded'         => false,
				'implementation' => $implementation
			);
			return true;
		}

		return false;
	}

	/**
	 * Loads all the plugins in $pluginPath
	 *
	 * All this function does is include all files inside the $pluginPath directory.  The plugins themselves
	 * will not be added to the registry unless you've properly added the code to do so inside your plugin file.
	 *
	 * @param string $pluginPath
	 */
	public function loadPlugins($pluginPath)
	{
		// strip the trailing slash if present
		if (substr($pluginPath, strlen($pluginPath) - 1, 1) == '/') {
			$pluginPath = substr($pluginPath, 0, strlen($pluginPath) - 1);
		}

		if (is_dir($pluginPath)) {
			if ($handle = opendir($pluginPath)) {
				while (false !== ($file = readdir($handle))) {
					$path_parts = @pathinfo($file);
					if (!empty($path_parts) && $path_parts['extension'] != 'php') {
						continue;
					}
					include_once($pluginPath . '/' . $file);
				}
			}
		}
	}

	/**
	 * Returns the plugin registry for the supplied implementation
	 *
	 * @return array
	 *
	 * @param string $implementation
	 */
	public function getPluginRegistry($implementation)
	{
		$returnArray = array();

		foreach ($this->_registry as $plugin => $meta) {
			if ($meta['implementation'] == 'n/a' || $meta['implementation'] == $implementation) {
				$returnArray[$plugin] = $meta;
			}
		}

		return $returnArray;
	}

}