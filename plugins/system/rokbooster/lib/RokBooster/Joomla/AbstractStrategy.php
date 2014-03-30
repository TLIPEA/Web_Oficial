<?php
/**
 * @version   $Id: AbstractStrategy.php 18890 2014-02-20 15:46:17Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2014 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
defined('ROKBOOSTER_LIB') or die('Restricted access');

/**
 *
 */
abstract class RokBooster_Joomla_AbstractStrategy extends RokBooster_Compressor_AbstractStrategy
{

	/**
	 *
	 */
	const CACHE_GROUP = 'rokbooster';
	/**
	 *
	 */
	const GENERATOR_STATE_TIMEOUT = 2;

	/**
	 * @var JCache
	 */
	protected $generator_state_cache;

	/**
	 * @var JCache
	 */
	protected $file_info_cache;

	/**
	 * @var RokBooster_Compressor_FileGroup[]
	 */
	protected $render_script_file_groups;

	/**
	 * @var RokBooster_Compressor_FileGroup[]
	 */
	protected $render_style_file_groups;

	/**
	 * @var RokBooster_Compressor_InlineGroup[]
	 */
	protected $render_inline_scripts;

	/**
	 * @var RokBooster_Compressor_InlineGroup[]
	 */
	protected $render_inline_styles;

	/**
	 * @var RokBooster_Compressor_File[]
	 */
	protected $images = array();

	/**
	 * @var RokBooster_Compressor_FileGroup[]
	 */
	protected $encode_image_file_groups = array();

	/**
	 * @var RokBooster_Compressor_FileGroup[]
	 */
	protected $imageFileGroups = array();

	/**
	 * @param $options
	 */
	public function __construct($options)
	{
		parent::__construct($options);
		$conf = JFactory::getConfig();

		$generator_state_options = array(
			'cachebase'    => $conf->get('cache_path', JPATH_CACHE),
			'lifetime'     => self::GENERATOR_STATE_TIMEOUT,
			'storage'      => $conf->get('cache_handler', 'file'),
			'defaultgroup' => self::CACHE_GROUP,
			'locking'      => true,
			'locktime'     => 15,
			'checkTime'    => true,
			'caching'      => true
		);

		$this->generator_state_cache = new JCache($generator_state_options);

		$file_info_options     = array(
			'cachebase'    => $conf->get('cache_path', JPATH_CACHE),
			'storage'      => $conf->get('cache_handler', 'file'),
			'defaultgroup' => self::CACHE_GROUP,
			'locking'      => true,
			'locktime'     => 15,
			'checkTime'    => false,
			'caching'      => true
		);
		$this->file_info_cache = new JCache($file_info_options);
	}


	/**
	 * @param $checksum
	 *
	 * @return bool|mixed
	 */
	protected function doesOutputExist(RokBooster_Compressor_IGroup $group, $is_wrapped = true)
	{
		$oc = RokBooster_Compressor_OutputContainerFactory::create($group,$this->options);
		return $oc->doesExist($is_wrapped);
	}

	/**
	 * @param $checksum
	 *
	 * @return bool|mixed
	 */
	protected function isBeingRendered(RokBooster_Compressor_IGroup $group)
	{
		if (($rendering = $this->generator_state_cache->get($group->getChecksum()))) {
			return $rendering;
		}
		return false;
	}

	/**
	 * @param $checksum
	 */
	protected function setCurrentlyRendering(RokBooster_Compressor_IGroup $group)
	{
		$this->generator_state_cache->store(true, $group->getChecksum());
	}

	/**
	 * @param $checksum
	 */
	protected function finishedRendering(RokBooster_Compressor_IGroup $group)
	{
		$this->generator_state_cache->remove($group->getChecksum());
	}


	/**
	 * @param $checksum
	 *
	 * @return bool|mixed
	 */
	protected function isOutputExpired(RokBooster_Compressor_IGroup $group, $is_wrapped = true)
	{
		$oc = RokBooster_Compressor_OutputContainerFactory::create($group,$this->options);
		if (!$oc->doesExist($is_wrapped)) {
			return true;
		}
		if (($expired = $oc->isExpired($is_wrapped))) {
			$files_changed = false;
			if (($file_group = $this->file_info_cache->get($group->getChecksum() . '_fileinfo'))) {
				$file_group = unserialize($file_group);
				/** @var $file RokBooster_Compressor_File */
				foreach ($file_group as $file) {
					if (file_exists($file->getPath()) && is_readable($file->getPath())) {
						if ($file->hasChanged()) {
							$files_changed = true;
							break;
						}
					} else {
						$this->file_info_cache->remove($group->getChecksum() . '_fileinfo');
						$files_changed = true;
						break;
					}
				}
			} else {
				$files_changed = true;
			}
			if (!$files_changed) {
				$oc->setAsValid();
				return false;
			}
		}
		return $expired;
	}

	/**
	 * @param RokBooster_Compressor_FileGroup $group
	 */
	protected function storeFileInfo(RokBooster_Compressor_FileGroup $group)
	{
		$group->cleanup();
		$this->file_info_cache->store(serialize($group), $group->getChecksum() . '_fileinfo');
	}


	/**
	 *
	 */
	public function process()
	{
		if ($this->options->minify_js && $this->options->minify_js !== 'disabled') {
			$this->processScripts();
		}
		if ($this->options->minify_css && $this->options->minify_css !== 'disabled') {
			$this->processStyles();
		}
		if ($this->options->inline_js) {
			$this->processInlineScripts();

		}
		if ($this->options->inline_css) {
			$this->processInlineStyles();
		}
	}



	/**
	 *
	 */
	protected function processScripts()
	{
		if (isset($this->render_script_file_groups) && is_array($this->render_script_file_groups)) {
			foreach ($this->render_script_file_groups as $filegroup) {
				parent::processScriptFiles($filegroup);
				$oc = RokBooster_Compressor_OutputContainerFactory::create($filegroup,$this->options);
				$oc->write('application/x-javascript');
				$this->storeFileInfo($filegroup);
				$this->finishedRendering($filegroup);
			}
		}
	}

	/**
	 *
	 */
	protected function processInlineScripts()
	{
		if (isset($this->render_inline_scripts) && is_array($this->render_inline_scripts)) {
			foreach ($this->render_inline_scripts as $inlinegroup) {
				parent::processInlineScript($inlinegroup);
				$oc = RokBooster_Compressor_OutputContainerFactory::create($inlinegroup,$this->options);
				$oc->write();
				$this->finishedRendering($inlinegroup);
			}
		}
	}

	/**
	 *
	 */
	protected function processStyles()
	{
		if (isset($this->render_style_file_groups) && is_array($this->render_style_file_groups)) {
			foreach ($this->render_style_file_groups as $filegroup) {
				parent::processStyleFiles($filegroup);
				$oc = RokBooster_Compressor_OutputContainerFactory::create($filegroup,$this->options);
				$oc->write('text/css');
				$this->storeFileInfo($filegroup);
				$this->finishedRendering($filegroup);
			}
		}
	}

	/**
	 *
	 */
	protected function processInlineStyles()
	{
		if (isset($this->render_inline_styles) && is_array($this->render_inline_styles)) {
			foreach ($this->render_inline_styles as $inlinegroup) {
				parent::processInlineStyle($inlinegroup);
				$oc = RokBooster_Compressor_OutputContainerFactory::create($inlinegroup,$this->options);
				$oc->write();
				$this->finishedRendering($inlinegroup);
			}
		}
	}

	protected function processImages()
	{
		foreach ($this->encode_image_file_groups as $image_group) {
			/** @var $file RokBooster_Compressor_File */
			$file = $image_group[0];
			$image_group->setResult(base64_encode(file_get_contents($file->getPath())));
			$oc = RokBooster_Compressor_OutputContainerFactory::create($image_group,$this->options);
			$oc->write();
			$this->storeFileInfo($image_group);
			$this->finishedRendering($image_group);
		}
	}

	protected function strposArray($haystack, $needles=array(), $offset=0) {
        $chr = array();
        foreach($needles as $needle) {
                $res = strpos($haystack, $needle, $offset);
                if ($res !== false) $chr[$needle] = $res;
        }
        if(empty($chr)) return false;
        return min($chr);
	}

}
