<?php
/**
 * @version   $Id: CssAggregator.php 11423 2013-06-13 16:34:23Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2014 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 * Based on lessphp v0.3.4-2
 * http://leafo.net/lessphp
 *
 * LESS css compiler, adapted from http://lesscss.org
 *
 * Copyright 2012, Leaf Corcoran <leafot@gmail.com>
 * Licensed under MIT or GPLv3, see LICENSE
 */

defined('ROKBOOSTER_LIB') or die('Restricted access');

/**
 * The less compiler and parser.
 *
 * Converting LESS to CSS is a two stage process. First the incoming document
 * must be parsed. Parsing creates a tree in memory that represents the
 * structure of the document. Then, the tree of the document is recursively
 * compiled into the CSS text. The compile step has an implicit step called
 * reduction, where values are brought to their lowest form before being
 * turned to text, eg. mathematical equations are solved, and variables are
 * dereferenced.
 *
 * The parsing stage produces the final structure of the document, for this
 * reason mixins are mixed in and attribute accessors are referenced during
 * the parse step. A reduction is done on the mixed in block as it is mixed in.
 *
 *  See the following:
 *    - entry point for parsing and compiling: RokBooster_Compressor_CssAggregator::parse()
 *    - parsing: RokBooster_Compressor_CssAggregator::parseChunk()
 *    - compiling: RokBooster_Compressor_CssAggregator::compileBlock()
 *
 */
class RokBooster_Compressor_CssAggregator
{
	/**
	 * @var null|string
	 */
	protected $buffer;

	/**
	 * @var null|string
	 */
	protected $rootDir;


	/** @var boolean */
	protected $importDisabled = false;

	/**
	 * @param $in
	 * @param $root_url
	 *
	 * @return string
	 */
	public static function combine($in, $root_url)
	{
		$css = new self($in, $root_url);
		return $css->parse();
	}

	/**
	 * Initialize any static state, can initialize parser for a file
	 */
	function __construct($css = null, $root_path = null, $opts = null)
	{
		$this->rootDir = $root_path;
		$this->buffer  = $css;
	}

	// attempts to find the path of an import url, returns null for css files
	/**
	 * @param $url
	 *
	 * @return null|string
	 */
	function findImport($url)
	{
		foreach ((array)$this->rootDir as $dir) {
			$full = $dir . (mb_substr($dir, -1) != '/' ? '/' : '') . $url;
			if ($this->fileExists($file = $full . '.css') || $this->fileExists($file = $full)) {
				return $file;
			}
		}

		return null;
	}

	/**
	 * @param $name
	 *
	 * @return bool
	 */
	function fileExists($name)
	{
		// sym link workaround
		return file_exists($name) || file_exists(realpath(preg_replace('/\w+\/\.\.\//', '', $name)));
	}


	/**
	 * @return string
	 */
	public function parse()
	{
		$this->buffer = $this->removeComments($this->buffer);
		if (!$this->importDisabled) {
			// Match all import types
			if (($match_count = preg_match_all('/@import\s+(?:url\()?["\\\']?([^"\\\'\)]+)["\\\']?(?:\))?(?:[\s\w\d\-._,]*);/i', $this->buffer, $matches)) > 0) {
				for ($i = 0; $i < $match_count; $i++) {
					if (!empty($matches[1][$i])) {
						$import_file = $this->findImport($matches[1][$i]);
						if ($import_file != null) {
							$child          = $this->createChild($import_file);
							$child_contents = $child->parse();
							$this->buffer   = mb_str_replace($matches[0][$i], $child_contents, $this->buffer);
						}
					}
				}
			}
		}
		return $this->buffer;
	}

	protected function removeComments($text)
	{
		$look = array(
			'url(',
			'//',
			'/*',
			'"',
			"'"
		);

		$out  = '';
		$min  = null;
		$done = false;
		while (true) {
			// find the next item
			foreach ($look as $token) {
				$pos = mb_strpos($text, $token);
				if ($pos !== false) {
					if (!isset($min) || $pos < $min[1]) $min = array($token, $pos);
				}
			}

			if (is_null($min)) break;

			$count    = $min[1];
			$skip     = 0;
			$newlines = 0;
			switch ($min[0]) {
				case 'url(':
					if (preg_match('/url\(.*?\)/', $text, $m, 0, $count)) $count += mb_strlen($m[0]) - mb_strlen($min[0]);
					break;
				case '"':
				case "'":
					if (preg_match('/' . $min[0] . '.*?' . $min[0] . '/', $text, $m, 0, $count)) $count += mb_strlen($m[0]) - 1;
					break;
				case '//':
					$skip = mb_strpos($text, "\n", $count);
					if ($skip === false) $skip = mb_strlen($text) - $count; else $skip -= $count;
					break;
				case '/*':
					if (preg_match('/\/\*.*?\*\//s', $text, $m, 0, $count)) {
						$skip     = mb_strlen($m[0]);
						$newlines = mb_substr_count($m[0], "\n");
					}
					break;
			}

			if ($skip == 0) $count += mb_strlen($min[0]);

			$out .= mb_substr($text, 0, $count) . str_repeat("\n", $newlines);
			$text = mb_substr($text, $count + $skip);

			$min = null;
		}

		return $out . $text;
	}

	/**
	 * create a child parser (for compiling an import)
	 * @param $fname
	 *
	 * @return RokBooster_Compressor_CssAggregator
	 */
	protected function createChild($fname)
	{
		$css = new self(@file_get_contents_utf8($fname), $this->rootDir);
		return $css;
	}

	/**
	 * @param boolean $importDisabled
	 */
	public function setImportDisabled($importDisabled)
	{
		$this->importDisabled = $importDisabled;
	}

	/**
	 * @return boolean
	 */
	public function isImportDisabled()
	{
		return $this->importDisabled;
	}


}
