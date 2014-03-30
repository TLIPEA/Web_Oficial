<?php
/**
 * @version   $Id: File.php 18325 2014-01-31 17:05:11Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2014 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
defined('ROKBOOSTER_LIB') or die('Restricted access');

/**
 *
 */
class RokBooster_Compressor_File
{
	/**
	 * @var string
	 */
	public $file;
	/**
	 * @var string
	 */
	public $path;
	/**
	 * @var string
	 */
	public $url;

	/**
	 * @var string
	 */
	public $mime;

	/**
	 * @var string[] name/value attributes of a file for a script or style tag
	 */
	public $attributes = array();

	/**
	 * @var string
	 */
	public $type;

	/**
	 * @var int
	 */
	public $mtime = 0;

	/**
	 * @var int
	 */
	public $ctime = 0;

	/**
	 * @var int
	 */
	public $size = 0;

	/**
	 * @var bool
	 */
	public $external = false;

	/**
	 * @var bool
	 */
	public $ignored = false;

	/**
	 * @var string
	 */
	public $content;


	/**
	 * @param $file
	 * @param $root_url
	 * @param $root_path
	 */
	public function __construct($file, $root_url, $root_path)
	{
		$this->file     = str_replace("\\", "/", $file);
		$this->external = self::isLinkExternal($this->file, $root_url);
		$uri            = parse_url($file);
		$this->path     = (!$this->external) ? self::getFileLink($uri['path'], $root_url, $root_path) : $file;
		$this->url      = (!$this->external) ? self::getFileLink($uri['path'], $root_url, $root_path, false) : $file;
		if (isset($uri['query']) && !$this->external) {
			$this->url .= '?' . $uri['query'];
		}
		$this->type = self::getExt($uri['path']);
		if (!$this->external && $this->isFullUrl($this->file)) {
			$this->file = self::getRelativePath($this->file);
		}
		if ($this->external) {
			$this->ignored = true;
		} elseif (file_exists($this->path)) {
			list(, , , , , , , $this->size, , $this->mtime, $this->ctime, ,) = stat($this->path);
		}
	}


	/**
	 * @return bool
	 */
	public function hasChanged()
	{
		if ($this->external) return true;
		if (!file_exists($this->path)) return true;
		list(, , , , , , , $size, , $mtime, $ctime, ,) = stat($this->path);
		if ($this->mtime != $mtime || $this->ctime != $ctime || $this->size != $size) return true;
		return false;
	}


	protected static function isFullUrl($url)
	{
		$uri = parse_url($url);
		if (isset($uri['scheme']) && (strtolower($uri['scheme']) == 'http' || strtolower($uri['scheme'] == 'https'))) {
			return true;
		}
		return false;
	}

	protected static function getRelativePath($url)
	{
		$uri  = parse_url($url);
		$path = (isset($uri['path'])) ? $uri['path'] : '';
		return $path;
	}

	/**
	 * @param string $link   original relative url
	 * @param bool   $isPath specify path or url, path is default
	 *
	 * @param        $root_url
	 * @param        $root_path
	 *
	 * @return string $filepath return requested link as a full url or full path
	 */
	public static function getFileLink($link, $root_url, $root_path, $isPath = true)
	{
		$uri  = parse_url($root_url);
		$path = (isset($uri['path'])) ? $uri['path'] : '';
		$base = str_replace($path, '', $root_url);
		if ($link && $base && strpos($link, $base) !== false) $link = str_replace($base, "", $link);
		if ($link && $path && strpos($link, $path) !== false) $link = str_replace($path, "", $link);
		if (substr($link, 0, 1) != '/') $link = '/' . $link;
		$filepath = ($isPath) ? $root_path . $link : $root_url . $link;
		return $filepath;
	}

	protected static function isLinkExternal($url, $root_url)
	{
		$url_uri = parse_url($url);

		$ext = strtolower(pathinfo(basename($url_uri['path']), PATHINFO_EXTENSION));
		if (!in_array($ext, array('js', 'css', 'gif', 'jpg', 'jpeg', 'png'))) {
			return true;
		}

		//if the url does not have a scheme must be internal=
		if (isset($url_uri['scheme']) && (strtolower($url_uri['scheme']) == 'http' || strtolower($url_uri['scheme'] == 'https'))) {
			$site_uri = parse_url($root_url);
			if (isset($url_uri['host']) && strtolower($url_uri['host']) == strtolower($site_uri['host'])) return false;
		}
		// cover external urls like //foo.com/foo.js
		if (!isset($url_uri['host']) && !isset($url_uri['scheme']) && isset($url_uri['path']) && substr($url_uri['path'], 0, 2) != '//') return false;
		//the url has a host and it isn't internal
		return true;
	}

	/**
	 * @param $url
	 *
	 * @return string
	 */
	protected static function getExt($url)
	{
		$uri = pathinfo($url);
		return (isset($uri['extension'])) ? $uri['extension'] : '';
	}

	/**
	 * @param $content
	 */
	public function setContent($content)
	{
		$this->content = $content;
	}

	/**
	 * @return mixed
	 */
	public function getContent()
	{
		if (!isset($this->content) && !$this->external) {
			if (file_exists($this->path) && is_readable($this->path)) {
				$this->content = file_get_contents_utf8($this->path);
			}
		} else {
			$this->ignored = true;
		}
		return $this->content;
	}

	/**
	 * @param $external
	 */
	public function setExternal($external = true)
	{
		$this->external = $external;
	}

	/**
	 * @return bool
	 */
	public function isExternal()
	{
		return $this->external;
	}

	/**
	 * @param $file
	 */
	public function setFile($file)
	{
		$this->file = $file;
	}

	/**
	 * @return mixed
	 */
	public function getFile()
	{
		return $this->file;
	}

	/**
	 * @param $ignored
	 */
	public function setIgnored($ignored = true)
	{
		$this->ignored = $ignored;
	}

	/**
	 * @return bool
	 */
	public function isIgnored()
	{
		return $this->ignored;
	}

	/**
	 * @param $path
	 */
	public function setPath($path)
	{
		$this->path = $path;
	}

	/**
	 * @return string
	 */
	public function getPath()
	{
		return $this->path;
	}

	/**
	 * @param $type
	 */
	public function setType($type)
	{
		$this->type = $type;
	}

	/**
	 * @return string
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * @param $url
	 */
	public function setUrl($url)
	{
		$this->url = $url;
	}

	/**
	 * @return string
	 */
	public function getUrl()
	{
		return $this->url;
	}

	/**
	 * @return mixed
	 */
	public function getCtime()
	{
		return $this->ctime;
	}

	/**
	 * @return mixed
	 */
	public function getSize()
	{
		return $this->size;
	}

	/**
	 * @return mixed
	 */
	public function getMtime()
	{
		return $this->mtime;
	}

	/**
	 * @return mixed
	 */
	function __toString()
	{
		return $this->file;
	}

	/**
	 * @param $attributes
	 */
	public function setAttributes($attributes)
	{
		if (is_array($attributes)) {
			$this->attributes = $attributes;
			ksort($this->attributes);
		}
	}

	/**
	 * @return array|string[]
	 */
	public function getAttributes()
	{
		return $this->attributes;
	}

	/**
	 * @param $key
	 * @param $value
	 */
	public function addAttribute($key, $value)
	{
		$this->attributes[$key] = $value;
		ksort($this->attributes);
		reset($this->attributes);
	}

	/**
	 * @param string $mime
	 */
	public function setMime($mime)
	{
		$this->mime = $mime;
	}

	/**
	 * @return string
	 */
	public function getMime()
	{
		return $this->mime;
	}

	public static function mime_content_type($filename)
	{

		$mime_types = array(

			'txt'  => 'text/plain',
			'htm'  => 'text/html',
			'html' => 'text/html',
			'php'  => 'text/html',
			'css'  => 'text/css',
			'js'   => 'application/javascript',
			'json' => 'application/json',
			'xml'  => 'application/xml',
			'swf'  => 'application/x-shockwave-flash',
			'flv'  => 'video/x-flv',
			// images
			'png'  => 'image/png',
			'jpe'  => 'image/jpeg',
			'jpeg' => 'image/jpeg',
			'jpg'  => 'image/jpeg',
			'gif'  => 'image/gif',
			'bmp'  => 'image/bmp',
			'ico'  => 'image/vnd.microsoft.icon',
			'tiff' => 'image/tiff',
			'tif'  => 'image/tiff',
			'svg'  => 'image/svg+xml',
			'svgz' => 'image/svg+xml',
			// archives
			'zip'  => 'application/zip',
			'rar'  => 'application/x-rar-compressed',
			'exe'  => 'application/x-msdownload',
			'msi'  => 'application/x-msdownload',
			'cab'  => 'application/vnd.ms-cab-compressed',
			// audio/video
			'mp3'  => 'audio/mpeg',
			'qt'   => 'video/quicktime',
			'mov'  => 'video/quicktime',
			// adobe
			'pdf'  => 'application/pdf',
			'psd'  => 'image/vnd.adobe.photoshop',
			'ai'   => 'application/postscript',
			'eps'  => 'application/postscript',
			'ps'   => 'application/postscript',
			// ms office
			'doc'  => 'application/msword',
			'rtf'  => 'application/rtf',
			'xls'  => 'application/vnd.ms-excel',
			'ppt'  => 'application/vnd.ms-powerpoint',
			// open office
			'odt'  => 'application/vnd.oasis.opendocument.text',
			'ods'  => 'application/vnd.oasis.opendocument.spreadsheet',

			// fonts
			'woff' => 'application/x-font-woff',
			'svg'  => 'image/svg+xml',
			'ttf'  => 'application/x-font-ttf',
			'otf'  => 'application/x-font-otf',
			'eot'  => 'application/vnd.ms-fontobject',

		);

		$filename_parts = explode('.', $filename);
		$ext            = strtolower(array_pop($filename_parts));

		if (array_key_exists($ext, $mime_types)) {
			return $mime_types[$ext];
		} elseif (function_exists('finfo_open')) {
			$finfo    = finfo_open(FILEINFO_MIME);
			$mimetype = @finfo_file($finfo, $filename);
			finfo_close($finfo);
			return $mimetype;
		} else {
			return 'application/octet-stream';
		}
	}

}
