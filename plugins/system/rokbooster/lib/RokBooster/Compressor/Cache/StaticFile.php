<?php
/**
 * @version   $Id: StaticFile.php 11423 2013-06-13 16:34:23Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
defined('ROKBOOSTER_LIB') or die('Restricted access');

/**
 *
 */
class RokBooster_Compressor_Cache_StaticFile implements RokBooster_Compressor_ICache
{
	const FILE_PERMS_USER_ONLY   = "0644";
	const FILE_PERMS_GROUP_WRITE = "0664";
	const FILE_EXTENSION         = '.php';
	const DATA_FILE_EXTENSION    = '_data.php';

	/**
	 * @var object
	 */
	protected $options;

	/**
	 * @param $checksum
	 *
	 * @return bool
	 */
	public function isCacheExpired($checksum)
	{
		//see if file is stale
		$expired    = true;
		$cache_file = $this->options->cache_path . $checksum . self::FILE_EXTENSION;
		if (file_exists($cache_file)) {
			$expired = ((int)strtotime('now') > ((int)filectime($cache_file) + (int)$this->options->cache_time)) ? true : false;
		}
		return $expired;
	}

	/**
	 * @param $checksum
	 *
	 * @return bool
	 */
	public function doesCacheExist($checksum)
	{
		if (file_exists($this->options->cache_path . $checksum . self::FILE_EXTENSION)) {
			return true;
		}
		return false;
	}

	/**
	 * @param $checksum
	 *
	 * @return string
	 */
	public function getCacheUrl($checksum)
	{
		return $this->options->cache_url . $checksum . self::FILE_EXTENSION;
	}

	/**
	 *
	 * @param $checksum
	 *
	 * @return mixed
	 */
	public function setCacheAsValid($checksum)
	{
		$cache_file = $this->getFilePath($checksum . self::FILE_EXTENSION);
		if (file_exists($cache_file)) {
			touch($cache_file);
		}
	}


	/**
	 * @param $checksum
	 *
	 * @return bool|string
	 */
	public function getCacheContent($checksum)
	{
		$cache_file = $this->getFilePath($checksum . self::FILE_EXTENSION);
		if (file_exists($cache_file)) {
			return file_get_contents_utf8($cache_file);
		}
		return '';
	}


	/**
	 * @param $options
	 */
	public function __construct($options)
	{
		$this->options = $options;
	}


	/**
	 * @return bool
	 */
	protected function isGzipEnabled()
	{
		//override param if gzip not available
		if ($this->options->use_gzip) {
			if (!isset($_SERVER['HTTP_ACCEPT_ENCODING'])) {
				return false;
			}

			$encoding = false;

			if (false !== strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) {
				$encoding = 'gzip';
			}

			if (false !== strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'x-gzip')) {
				$encoding = 'x-gzip';
			}

			if (!$encoding) {
				return false;
			}

			if (!extension_loaded('zlib') || ini_get('zlib.output_compression')) {
				return false;
			}

			return $encoding;
		}
		return false;
	}

	/**
	 * @param string $type
	 *
	 * @return string
	 */
	protected function getOutHeader($type, $datafile_name)
	{
		$cache_time = $this->options->cache_time;
		$use_gzip = ($this->isGzipEnabled() !== false)?'true':'false';
		$header = <<< HEADER
<?php function getFileMTime(\$filePath){
        \$time = filemtime(\$filePath);
        \$isDST     = (date('I', \$time) == 1);
        \$systemDST = (date('I') == 1);
        if (\$isDST == false && \$systemDST == true) \$adjustment = 3600;
        else if (\$isDST == true && \$systemDST == false) \$adjustment = -3600;
        else \$adjustment = 0;
        return (\$time + \$adjustment);
}
if (function_exists('mb_convert_encoding') && function_exists('mb_detect_encoding')) {
	function file_get_contents_utf8(\$fn)
	{
		\$content = file_get_contents(\$fn);
		return mb_convert_encoding(\$content, 'UTF-8', mb_detect_encoding(\$content, 'UTF-8, ISO-8859-1', true));
	}
} else {
	function file_get_contents_utf8(\$fn)
	{
		return file_get_contents(\$fn);
	}
}
header('Expires: '.gmdate('D, d M Y H:i:s', getFileMTime(__FILE__)+$cache_time));
header('Content-type:  $type; charset=UTF-8');
header('Cache-Control: public');
header('X-Content-Encoded-By: RokBooster');
if (isset(\$_SERVER['HTTP_IF_MODIFIED_SINCE']) && (strtotime(\$_SERVER['HTTP_IF_MODIFIED_SINCE']) == filemtime(__FILE__))){
        header('Last-Modified: '.gmdate('D, d M Y H:i:s', getFileMTime(__FILE__)).' GMT', true, 304);
        exit;
}
else {
        header('Last-Modified: '.gmdate('D, d M Y H:i:s', getFileMTime(__FILE__)).' GMT', true, 200);
}
if (!get_cfg_var("zlib.output_compression") && $use_gzip) ob_start ("ob_gzhandler"); else ob_start ();
echo file_get_contents_utf8(dirname(__FILE__) . DIRECTORY_SEPARATOR . '$datafile_name');
HEADER;

		return $header;
	}

	/**
	 * @param string $checksum
	 * @param string $file_content
	 * @param bool   $addheaders
	 * @param string $mimetype
	 *
	 * @return bool
	 */
	public function write($checksum, $file_content, $addheaders = true, $mimetype = 'application/x-javascript')
	{
		$output    = '';
		$file_name = $checksum . self::FILE_EXTENSION;

		if ($addheaders) {
			$this->writeHeaderFile($checksum, $mimetype);
			$file_name = $checksum . self::DATA_FILE_EXTENSION;
		}
		$output .= $file_content;
		$this->writeFile($file_name, $output);
		return true;
	}


	public function writeHeaderFile($checksum, $mimetype)
	{
		$header_file_name = $checksum . self::FILE_EXTENSION;
		$data_file_name   = $checksum . self::DATA_FILE_EXTENSION;

		$output = $this->getOutHeader($mimetype, $data_file_name);
		$this->writeFile($header_file_name, $output);

	}

	//public function refreshHeaderFile()

	protected function getFilePath($file_name)
	{
		$dir = preg_replace('#[/\\\\]+#', DIRECTORY_SEPARATOR, $this->options->cache_path);
		return $dir . $file_name;
	}

	protected function writeFile($file_name, $file_contents)
	{
		if (!empty($file_contents)) {
			$old_umask    = umask(0);
			$dir          = preg_replace('#[/\\\\]+#', DIRECTORY_SEPARATOR, $this->options->cache_path);
			$file_final   = $dir . $file_name;
			$file_working = $dir . $file_name . '_working';

			if ($this->options->file_perms == self::FILE_PERMS_GROUP_WRITE) {
				$umask = 0002;
			} else {
				$umask = 0022;
			}
			@umask($umask);

			if (!is_dir($dir)) @(mkdir($dir));

			if (($fh = @fopen($file_working, 'w')) === false) {
				throw new Exception(sprintf('Can not open file: \'%s\'', $file_working));
			}

			if (fwrite($fh, $file_contents)) {
				fclose($fh);
			} else {
				fclose($fh);
				throw new Exception(sprintf('Can not write to file: \'%s\'', $file_working));
			}

			if (file_exists($file_final)) {
				unlink($file_final);
			}
			rename($file_working, $file_final);
			@umask($old_umask);
		}
	}


	/**
	 * @param RokBooster_Compressor_FileGroup $filegroup
	 */
	public function writeScriptFile(RokBooster_Compressor_FileGroup $filegroup)
	{
		$this->write($filegroup->getChecksum(), $filegroup->getResult(), true, 'application/x-javascript');
	}

	/**
	 * @param RokBooster_Compressor_InlineGroup $inlinegroup
	 */
	public function writeInlineScriptFile(RokBooster_Compressor_InlineGroup $inlinegroup)
	{
		$this->write($inlinegroup->getChecksum(), $inlinegroup->getResult(), false);
	}

	/**
	 * @param RokBooster_Compressor_FileGroup $filegroup
	 */
	public function writeStyleFile(RokBooster_Compressor_FileGroup $filegroup)
	{
		$this->write($filegroup->getChecksum(), $filegroup->getResult(), true, 'text/css');
	}

	/**
	 * @param RokBooster_Compressor_InlineGroup $inlinegroup
	 */
	public function writeInlineStyleFile(RokBooster_Compressor_InlineGroup $inlinegroup)
	{
		$this->write($inlinegroup->getChecksum(), $inlinegroup->getResult(), false);
	}

	protected function getFileMTime($filePath)
	{
		$time       = filemtime($filePath);
		$isDST      = (date('I', $time) == 1);
		$systemDST  = (date('I') == 1);
		$adjustment = 0;
		if ($isDST == false && $systemDST == true) $adjustment = 3600; else if ($isDST == true && $systemDST == false) $adjustment = -3600; else
			$adjustment = 0;
		return ($time + $adjustment);
	}

//	protected function fopen_recursive($path, $mode, $chmod = 0755)
//	{
//		preg_match('`^(.+)/([a-zA-Z0-9]+\.[a-z]+)$`i', $path, $matches);
//		$directory = $matches[1];
//		$file      = $matches[2];
//
//		if (!is_dir($directory)) {
//			if (!mkdir($directory, $chmod, 1)) {
//				return FALSE;
//			}
//		}
//		return fopen($path, $mode);
//	}

}
