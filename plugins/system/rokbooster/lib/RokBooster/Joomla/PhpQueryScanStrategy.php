<?php
/**
 * @version   $Id: PhpQueryScanStrategy.php 18890 2014-02-20 15:46:17Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2014 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

defined('ROKBOOSTER_LIB') or die('Restricted access');

/**
 *
 */
class RokBooster_Joomla_PhpQueryScanStrategy extends RokBooster_Joomla_AbstractStrategy
{
	/**
	 * @var string
	 */
	protected $original_content;

	/**
	 * @var phpQueryObject
	 */
	protected $document;

	/**
	 * @var string
	 */
	protected $prefix = '';


	/**
	 * @param $options
	 */
	public function __construct($options)
	{
		parent::__construct($options);
		if ($this->options->scan_method == 'header') {
			$this->prefix = 'head > ';
		}
	}

	/**
	 *
	 */
	public function identify()
	{
		$this->original_content = JResponse::getBody();
		$this->document         = phpQuery::newDocumentHTML($this->original_content);


		if ($this->options->minify_js && $this->options->minify_js !== 'disabled') {
			$this->identifyScriptFiles();
		}
		if ($this->options->minify_css && $this->options->minify_css !== 'disabled') {
			$this->identifyStyleFiles();
		}
		if ($this->options->inline_js) {
			$this->identifyInlineScripts();
		}
		if ($this->options->inline_css) {
			$this->identifyInlineStyles();
		}
		if ($this->options->convert_page_images) {
			$this->identifyImageSources();
		}

	}

	/**
	 *
	 */
	protected function identifyScriptFiles()
	{
		$header_links = pq($this->prefix . 'script[src]', $this->document);
		foreach ($header_links as $link) {
			$attribs = pq($link, $this->document)->attr('*');
			$href    = $attribs['src'];
			unset($attribs['src']);
			$mime = $attribs['type'];
			unset($attribs['type']);
			$file = new RokBooster_Compressor_File($href, $this->options->root_url, $this->options->root_path);
			$file->setMime($mime);
			$file->setType('js');
			$file->setAttributes($attribs);

			if ($this->strposArray($href, $this->options->ignored_files) !== false) {
				$file->setIgnored(true);
			}
			$this->script_file_sorter->addFile($file);
		}

		$script_file_groups = $this->script_file_sorter->getGroups();
		foreach ($script_file_groups as &$file_group) {
			if ($file_group->getStatus() != RokBooster_Compressor_IGroup::STATE_IGNORE && $this->isOutputExpired($file_group) && !$this->isBeingRendered($file_group)) {
				$this->render_script_file_groups[] = $file_group;
				$this->setCurrentlyRendering($file_group);
			}
		}
	}

	/**
	 *
	 */
	protected function identifyStyleFiles()
	{
		$header_links = pq($this->prefix . 'link[href][rel=stylesheet]', $this->document);
		foreach ($header_links as $link) {
			$attribs = pq($link, $this->document)->attr('*');
			$href    = $attribs['href'];
			unset($attribs['href']);
			$mime = $attribs['type'];
			unset($attribs['type']);
			unset($attribs['rel']);
			$file = new RokBooster_Compressor_File($href, $this->options->root_url, $this->options->root_path);
			$file->setMime($mime);
			$file->setType('css');
			$file->setAttributes($attribs);
			if ($this->strposArray($href, $this->options->ignored_files) !== false) {
				$file->setIgnored(true);
			}
			$this->style_file_sorter->addFile($file);
		}

		$file_groups = $this->style_file_sorter->getGroups();
		foreach ($file_groups as &$file_group) {
			if ($this->isOutputExpired($file_group) && !$this->isBeingRendered($file_group)) {
				$this->render_style_file_groups[] = $file_group;
				$this->setCurrentlyRendering($file_group);
			}
		}
	}

	/**
	 *
	 */
	protected function identifyInlineScripts()
	{
		$inlineGroup = new RokBooster_Compressor_InlineGroup(RokBooster_Compressor_InlineGroup::STATE_INCLUDE, 'text/javascript');
		$tags        = pq($this->prefix . 'script:not([src])[type=text/javascript]', $this->document);
		foreach ($tags as $tag) {
			$inlineGroup->addItem(pq($tag, $this->document)->text());

		}

		$this->inline_scripts[] = $inlineGroup;
		if ($this->isOutputExpired($inlineGroup, false) && !$this->isBeingRendered($inlineGroup)) {
			$this->render_inline_scripts[] = $inlineGroup;
			$this->setCurrentlyRendering($inlineGroup);
		}
	}

	/**
	 *
	 */
	protected function identifyInlineStyles()
	{
		$inlineGroup = new RokBooster_Compressor_InlineGroup(RokBooster_Compressor_InlineGroup::STATE_INCLUDE, 'text/css');
		$tags        = pq($this->prefix . 'style[type=\'text/css\']', $this->document);


		foreach ($tags as $tag) {
			$inlineGroup->addItem(pq($tag, $this->document)->text());
		}
		$this->inline_styles[] = $inlineGroup;
		if ($this->isOutputExpired($inlineGroup, false) && !$this->isBeingRendered($inlineGroup)) {
			$this->render_inline_styles[] = $inlineGroup;
			$this->setCurrentlyRendering($inlineGroup);
		}
	}

	protected function identifyImageSources()
	{
		$image_links = pq('img[src]', $this->document);
		foreach ($image_links as $image) {
			$attribs = pq($image, $this->document)->attr('*');
			$src     = $attribs['src'];
			unset($attribs['src']);

			$file = new RokBooster_Compressor_File($src, $this->options->root_url, $this->options->root_path);
			$file->setAttributes($attribs);

			$ext = strtolower(pathinfo($file->getPath(), PATHINFO_EXTENSION));
			$file->setMime(RokBooster_Compressor_File::mime_content_type($file->getPath()));
			if (!$file->isExternal() && is_file($file->getPath())) {
				list(, , , , , , , $size, , $mtime, $ctime, ,) = @stat($file->getPath());
				if ($this->strposArray($src, $this->options->ignored_files) === false && $size <= $this->options->max_data_uri_image_size) {
					$this->images[$file->getFile()] = $file;
					$group                          = new RokBooster_Compressor_FileGroup(RokBooster_Compressor_FileGroup::STATE_INCLUDE, $file->getMime());
					$group->addItem($file);
					$this->imageFileGroups[$file->getFile()] = $group;
				}
			}
		}

		foreach ($this->imageFileGroups as $image_file_group) {
			if ($image_file_group->getStatus() != RokBooster_Compressor_IGroup::STATE_IGNORE && $this->isOutputExpired($image_file_group, false) && !$this->isBeingRendered($image_file_group)) {
				$this->encode_image_file_groups[] = $image_file_group;
				$this->setCurrentlyRendering($image_file_group);
			}
		}
	}

	/**
	 *
	 */
	public function process()
	{
		parent::process();
		if ($this->options->convert_page_images) {
			$this->processImages();
		}
	}

	/**
	 * @return mixed
	 */
	public function populate()
	{
		$this->populateStyleFiles();
		$this->populateInlineStyles();
		$this->populateScriptFiles();
		$this->populateInlineScripts();
		$this->populateImages();

		$markup = ($this->document->getDocument()->htmlOuter());
		//TODO fix so that regex works with data uris?
		$markup = preg_replace('/(<(base|img|br|meta|area|input|link|col|hr|param|frame|isindex)+([\s]+[\S]+[\s]*=[\s]*("([^"]*)"|\'([^\']*)\'))*[\s]*)>/imx', '$1/>', $markup);
		JResponse::setBody($markup);
	}

	/**
	 *
	 */
	protected function populateStyleFiles()
	{
		if ($this->options->minify_css && $this->options->minify_css !== 'disabled') {
			$outputlinks = array();
			pq($this->prefix . 'link[href][rel=stylesheet]', $this->document)->remove();
			foreach ($this->style_file_sorter->getGroups() as $styleGroup) {
				$oc = RokBooster_Compressor_OutputContainerFactory::create($styleGroup, $this->options);
				if ($styleGroup->getStatus() == RokBooster_Compressor_IGroup::STATE_INCLUDE && $oc->doesExist(true)) {
					$attribs = array();
					foreach ($styleGroup->getAttributes() as $attrib_key => $attrib_value) {
						$attribs[] = $attrib_key . '="' . $attrib_value . '"';
					}
					$oc            = RokBooster_Compressor_OutputContainerFactory::create($styleGroup, $this->options);
					$outputlinks[] = '<link rel="stylesheet" href="' . $oc->getUrl($styleGroup) . '" type="' . $styleGroup->getMime() . '" ' . implode(' ', $attribs) . ' />' . PHP_EOL;
				} else {
					foreach ($styleGroup as $file) {
						/** @var $file RokBooster_Compressor_File */
						$attribs = array();
						foreach ($file->getAttributes() as $attrib_key => $attrib_value) {
							$attribs[] = $attrib_key . '="' . $attrib_value . '"';
						}
						$outputlinks[] = '<link rel="stylesheet" href="' . $file->getUrl() . '" type="' . $file->getMime() . '" ' . implode(' ', $attribs) . ' />' . PHP_EOL;
					}
				}
			}
			pq('head', $this->document)->append(implode("\n", $outputlinks));
		} else {
			$links            = array();
			$stylesheet_links = pq($this->prefix . ' link[href][rel=stylesheet]', $this->document);
			foreach ($stylesheet_links as $stylesheet_link) {
				$foo = pq($stylesheet_link, $this->document)->markup();
			}
			pq('head', $this->document)->append($stylesheet_links);

		}
	}

	/**
	 *
	 */
	protected function populateInlineStyles()
	{
		if ($this->options->inline_css) {
			$outputblocks = array();
			pq($this->prefix . 'style[type=text/css]', $this->document)->remove();
			foreach ($this->inline_styles as $inlineGroup) {
				$oc = RokBooster_Compressor_OutputContainerFactory::create($inlineGroup, $this->options, false);
				if ($inlineGroup->getStatus() == RokBooster_Compressor_IGroup::STATE_INCLUDE && $oc->doesExist(false)) {
					$outputblocks[] = '<style type="text/css">' . $oc->getContent() . '</style>' . PHP_EOL;
				} else {
					$outputblocks[] = '<style type="text/css">' . $inlineGroup->getContent() . '</style>' . PHP_EOL;
				}
			}
			pq('head', $this->document)->append(implode("\n", $outputblocks));
		} else {
			$style_block = pq($this->prefix . 'style[type=text/css]', $this->document);
			pq('head', $this->document)->append($style_block);
		}
	}

	/**
	 *
	 */
	protected function populateScriptFiles()
	{
		if ($this->options->minify_js && $this->options->minify_js !== 'disabled') {
			$outputlinks = array();
			pq($this->prefix . 'script[src]', $this->document)->remove();
			foreach ($this->script_file_sorter->getGroups() as $filegroup) {
				$oc = RokBooster_Compressor_OutputContainerFactory::create($filegroup, $this->options);
				if ($filegroup->getStatus() == RokBooster_Compressor_IGroup::STATE_INCLUDE && $oc->doesExist(true)) {
					$attribs = array();
					foreach ($filegroup->getAttributes() as $attrib_key => $attrib_value) {
						$attribs[] = $attrib_key . '="' . $attrib_value . '"';
					}
					$outputlinks[] = '<script src="' . $oc->getUrl($filegroup) . '" type="' . $filegroup->getMime() . '" ' . implode(' ', $attribs) . '></script>' . PHP_EOL;
				} else {
					foreach ($filegroup as $file) {
						/** @var $file RokBooster_Compressor_File */
						$attribs = array();
						foreach ($file->getAttributes() as $attrib_key => $attrib_value) {
							$attribs[] = $attrib_key . '="' . $attrib_value . '"';
						}
						$outputlinks[] = '<script src="' . $file->getUrl() . '" type="' . $file->getMime() . '" ' . implode(' ', $attribs) . '></script>' . PHP_EOL;
					}
				}
			}
			pq('head', $this->document)->append(implode("\n", $outputlinks));
		} else {
			$links = pq($this->prefix . 'script[src]', $this->document);
			pq('head', $this->document)->append($links);
		}
	}

	/**
	 *
	 */
	protected function populateInlineScripts()
	{
		if ($this->options->inline_js) {
			$outputblocks = array();
			pq($this->prefix . 'script:not([src])[type=text/javascript]', $this->document)->remove();
			foreach ($this->inline_scripts as $inlineGroup) {
				$oc = RokBooster_Compressor_OutputContainerFactory::create($inlineGroup, $this->options, false);
				if ($inlineGroup->getStatus() == RokBooster_Compressor_IGroup::STATE_INCLUDE && $oc->doesExist(false)) {
					$outputblocks[] = '<script type="text/javascript">' . $oc->getContent() . '</script>' . PHP_EOL;

				} else {
					$outputblocks[] = '<script type="text/javascript">' . $inlineGroup->getContent() . '</script>' . PHP_EOL;
				}

			}
			pq('head', $this->document)->append(implode("\n", $outputblocks));
		} else {
			$script_block = pq($this->prefix . 'script:not([src])[type=text/javascript]', $this->document);
			pq('head', $this->document)->append($script_block);
		}
	}

	protected function populateImages()
	{
		if ($this->options->convert_page_images) {
			$image_links = pq('img[src]', $this->document);
			foreach ($image_links as $image) {
				$attribs = pq($image, $this->document)->attr('*');
				$src     = $attribs['src'];
				if (array_key_exists($src, $this->imageFileGroups)) {
					$image_file_group = $this->imageFileGroups[$src];
					$oc               = RokBooster_Compressor_OutputContainerFactory::create($image_file_group, $this->options, false);
					if ($oc->doesExist(false)) {
						/** @var $image_file RokBooster_Compressor_File */
						$image_file  = $image_file_group[0];
						$fileattribs = array();
						foreach ($image_file->getAttributes() as $attrib_key => $attrib_value) {
							$fileattribs[] = $attrib_key . '="' . $attrib_value . '"';
						}
						pq($image)->replaceWith(sprintf('<img src="data:%s;base64,%s" %s />', $image_file_group->getMime(), $oc->getContent($image_file_group), implode(' ', $fileattribs)));
					}
				}
			}
		}
	}
}
