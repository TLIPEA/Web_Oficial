<?php
/**
 * @version   $Id: AbstractStrategy.php 18893 2014-02-20 17:54:46Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2014 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

defined('ROKBOOSTER_LIB') or die('Restricted access');

/**
 *
 */
abstract class RokBooster_Compressor_AbstractStrategy implements RokBooster_Compressor_IStragety
{

	/**
	 * @var object
	 */
	protected $options;

	/**
	 * @var string
	 */
	protected $current_css_url = '';

	/**
	 * @var string
	 */
	protected $current_key = '';

	/**
	 * @var array of ignored files and / or declarations
	 */
	protected $ignored = array();

	/**
	 * @var array
	 */
	protected $found = array();


	/**
	 * @var array
	 */
	protected $checksums = array();

	/**
	 * @var array
	 */
	protected $results = array();

	/**
	 * @var array
	 */
	protected $fileinfo = array();

	/**
	 * @var RokBooster_Compressor_ISort
	 */
	protected $script_file_sorter;
	/**
	 * @var RokBooster_Compressor_ISort
	 */
	protected $style_file_sorter;

	/**
	 * @var RokBooster_Compressor_InlineGroup[]
	 */
	protected $inline_scripts = array();

	/**
	 * @var RokBooster_Compressor_InlineGroup[]
	 */
	protected $inline_styles = array();

	/**
	 * @param $options
	 */
	public function __construct($options)
	{
		$this->options            = $options;
		$script_sorter            = (string)$this->options->script_sort;
		$style_sorter             = (string)$this->options->style_sort;
		$this->script_file_sorter = new $script_sorter();
		$this->style_file_sorter  = new $style_sorter();
	}


	/**
	 * @param RokBooster_Compressor_File $file
	 */
	protected function addJsFile(RokBooster_Compressor_File &$file)
	{
		$this->script_file_sorter->addFile($file);
	}

	/**
	 * @param RokBooster_Compressor_File $file
	 */
	protected function addCssFile(RokBooster_Compressor_File &$file)
	{
		$this->style_file_sorter->addFile($file);
	}


	/**
	 * @param RokBooster_Compressor_FileGroup $files
	 */
	protected function processScriptFiles(RokBooster_Compressor_FileGroup &$files)
	{

		$content = '';
		//get links data
		foreach ($files as &$file) {
			/** @var $file RokBooster_Compressor_File */
			$tmp_content =  $file->getContent();
			if ($this->options->minify_js == 'compress'){
				$tmp_content = RokBooster_Compressor_Processor_JSMin::_minify($tmp_content);
			}
			$content .= $this->cleanEndLines($tmp_content) . ' ';
		}
		$files->setResult($content);
	}


	/**
	 * @param \RokBooster_Compressor_InlineGroup $inlinegroup
	 *
	 * @return string
	 */
	protected function processInlineScript(RokBooster_Compressor_InlineGroup &$inlinegroup)
	{
		$content = $this->cleanEndLines($inlinegroup->getContent());
		$inlinegroup->setResult(RokBooster_Compressor_Processor_JSMin::_minify($content));

	}


	/**
	 * @param RokBooster_Compressor_FileGroup $files
	 */
	protected function processStyleFiles(RokBooster_Compressor_FileGroup &$files)
	{
		$content = '';
		foreach ($files as $file) {
			/** @var $file RokBooster_Compressor_File */
			if ($file->getContent()) {
				$this->current_css_url = $file->getFile();
				if ($this->options->imported_css) {
					try {
						$compiled_content = RokBooster_Compressor_CssAggregator::combine($file->content, dirname($file->getPath()));
						$file->content    = $compiled_content;
					} catch (Exception $e) {
						JLog::add(JText::sprintf('PLG_SYSTEM_ROKBOOSTER_CSS_FILE_COMPILE_ERROR',$e->getMessage(),$file->path), JLog::ERROR, 'rokbooster');
					}
				}
				$file->content = preg_replace('~@import\s?[\'"]([^\'"]+?)[\'"];~', '@import url("$1");', $file->content);
				$file->content = preg_replace_callback('~url\s?\([\'"]?(?![a-z]+:|/+)([^\'")]+)[\'"]?\)~i', array(
				                                                                                                 $this,
				                                                                                                 'correctUrl'
				                                                                                            ), $file->content);
				unset($this->current_css_url);
				$content .= $file->content . ' ';
				preg_match('/@import\s+(?:url\()?["\\\']?(?:[^"\\\'\)]+)["\\\']?(?:\))?(?:[\s\w\d\-._,]*);/i', $content, $array);
				$fixed_content = implode($array).preg_replace('/@import\s+(?:url\()?["\\\']?(?:[^"\\\'\)]+)["\\\']?(?:\))?(?:[\s\w\d\-._,]*);/i','', $content);
				$content=$fixed_content;
			}
		}


		if ($this->options->minify_css == 'compress'){
			$files->setResult(RokBooster_Compressor_Processor_YUI::_minify($content));
		}
		else
		{
			$files->setResult($content);
		}
	}

	/**
	 * @param \RokBooster_Compressor_InlineGroup $inlinegroup
	 *
	 * @return string
	 */
	protected function processInlineStyle(RokBooster_Compressor_InlineGroup &$inlinegroup)
	{
		$content = $inlinegroup->getContent();
		if ($this->options->imported_css) {
			try {
				$compiled_content = RokBooster_Compressor_CssAggregator::combine($content, $this->options->root_path);
				$content          = $compiled_content;
			} catch (Exception $e) {
				JLog::add(JText::sprintf('PLG_SYSTEM_ROKBOOSTER_INLIN_CSS_COMPILE_ERROR',$e->getMessage()), JLog::ERROR, 'rokbooster');
			}
		}
		$content = $this->cleanEndLines($content);
		$inlinegroup->setResult(RokBooster_Compressor_Processor_YUI::_minify($content));

	}

	/**
	 * @param $matches
	 *
	 * @return string
	 */
	protected function correctUrl($matches)
	{
		if (!preg_match('~^(/|http)~', $matches[1])) {
			$current_uri = parse_url($this->current_css_url);
			$cssRootPath = preg_replace('~/[^/]+\.css~', '/', $current_uri['path']);
			$url_path   = $cssRootPath . $matches[1];
			$url_path   = $this->normalize_path($url_path);

			$fullPath = RokBooster_Compressor_File::getFileLink($url_path, $this->options->root_url, $this->options->root_path);
			$ext      = mb_strtolower(pathinfo(basename($fullPath), PATHINFO_EXTENSION));

			if (in_array($ext, array('gif', 'jpg', 'jpeg', 'png')) && is_file($fullPath) && $this->options->convert_css_images) {
				list(, , , , , , , $size, , $mtime, $ctime, ,) = @stat($fullPath);
				if ($size <= $this->options->max_data_uri_image_size) {
					$url_path = $this->encodeFile($fullPath);
				}
			}
			if (in_array($ext, array('woff', 'ttf', 'svg', 'otf', 'eot')) && is_file($fullPath) && $this->options->convert_css_fonts) {
				list(, , , , , , , $size, , $mtime, $ctime, ,) = @stat($fullPath);
				if ($size <= $this->options->max_data_uri_font_size) {
					$url_path = $this->encodeFile($fullPath);
				}
			}
			return 'url(\'' . $url_path . '\')';
		} else {
			return $matches[0];
		}
	}

	protected function encodeFile($fullPath)
	{
		$encoded = base64_encode(file_get_contents($fullPath));
		$mime    = RokBooster_Compressor_File::mime_content_type($fullPath);
		return sprintf('data:%s;base64,%s', $mime, $encoded);

	}

	/**
	 * This function is a proper replacement for realpath
	 * It will _only_ normalize the path and resolve indirections (.. and .)
	 * Normalization includes:
	 * - directiory separator is always /
	 * - there is never a trailing directory separator
	 *
	 * @param  $path
	 *
	 * @throws Exception
	 * @return String
	 */
	protected function normalize_path($path)
	{
		$parts = preg_split(":[\\\/]:", $path); // split on known directory separators

		// resolve relative paths
		for ($i = 0; $i < count($parts); $i += 1) {
			if ($parts[$i] === "..") { // resolve ..
				if ($i === 0) {
					throw new Exception("Cannot resolve path, path seems invalid: `" . $path . "`");
				}
				unset($parts[$i - 1]);
				unset($parts[$i]);
				$parts = array_values($parts);
				$i -= 2;
			} else if ($parts[$i] === ".") { // resolve .
				unset($parts[$i]);
				$parts = array_values($parts);
				$i -= 1;
			}
			if ($i > 0 && $parts[$i] === "") { // remove empty parts
				unset($parts[$i]);
				$parts = array_values($parts);
			}
		}
		return implode("/", $parts);
	}


	/**
	 * @param $data
	 *
	 * @return string
	 */
	protected function cleanEndLines($data)
	{
		$file_lines = explode("\n", $data);
		while (($line = array_pop($file_lines)) != null) {
			$clean_line = rtrim($line);
			if (mb_strlen($clean_line) > 0) {
				$end_char = mb_substr($line, mb_strlen($clean_line), 1);
				array_push($file_lines, $line);
				if ($end_char != ';') {
					array_push($file_lines, ";");
				}
				break;
			}
		}
		return implode($file_lines, "\n");
	}

}
