<?php
/**
 * @version   $Id: AbstractOutputContainer.php 18890 2014-02-20 15:46:17Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2014 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
defined('ROKBOOSTER_LIB') or die('Restricted access');

/**
 *
 */
abstract class RokBooster_Compressor_AbstractOutputContainer implements RokBooster_Compressor_IOutputContainer
{
	/**
	 *
	 */
	const FILE_PERMS_USER_ONLY = "0644";
	/**
	 *
	 */
	const FILE_PERMS_GROUP_WRITE = "0664";
	/**
	 *
	 */
	const FILE_EXTENSION = '.php';
	/**
	 * @var object
	 */
	protected $options;
	/**
	 * @var RokBooster_Compressor_IGroup
	 */
	protected $group;
	protected $wrapper_file_name;
	protected $wrapper_file_path;
	protected $wrapped;


	/**
	 * @param RokBooster_Compressor_IGroup $group
	 * @param                              $options
	 * @param bool                         $wrapped
	 */
	public function __construct(RokBooster_Compressor_IGroup $group, $options, $wrapped = true)
	{
		$this->options           = $options;
		$this->group             = $group;
		$this->wrapped           = $wrapped;
		$this->wrapper_file_name = md5(serialize($this->options) . '-' . $this->group->getChecksum()) . self::FILE_EXTENSION;
		$this->wrapper_file_path = preg_replace('#[/\\\\]+#', DIRECTORY_SEPARATOR, $this->options->cache_path) . DIRECTORY_SEPARATOR . $this->wrapper_file_name;
	}

	/**
	 * @param string $mimetype
	 *
	 * @internal param bool $add_serving_wrapper
	 * @return bool
	 */
	public function write($mimetype = 'application/x-javascript')
	{
		if ($this->wrapped) {
			$this->writeServingWrapper($mimetype);
		}
		$this->writeData();
		return true;
	}

	/**
	 * Write the serving wrapper file to the file system
	 *
	 * @param $mimetype
	 */
	protected function writeServingWrapper($mimetype)
	{

		$output = $this->getServingWrapper($mimetype);
		$this->writeFile($this->wrapper_file_name, $output);
	}

	/**
	 * @param string $type
	 *
	 * @return string
	 */
	protected function getServingWrapper($type)
	{
		$cache_time = $this->options->cache_time;
		$use_gzip   = ($this->isGzipEnabled() !== false) ? 'true' : 'false';
		$header     = <<< HEADER
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
header('Expires: '.gmdate('D, d M Y H:i:s', getFileMTime(__FILE__)+{$cache_time}));
header('Content-type:  {$type}; charset=UTF-8');
header('Cache-Control: public');
header('X-Content-Encoded-By: RokBooster');
if (isset(\$_SERVER['HTTP_IF_MODIFIED_SINCE']) && (strtotime(\$_SERVER['HTTP_IF_MODIFIED_SINCE']) == filemtime(__FILE__))){
        header('Last-Modified: '.gmdate('D, d M Y H:i:s', getFileMTime(__FILE__)).' GMT', true, 304);
        exit;
}
else {
        header('Last-Modified: '.gmdate('D, d M Y H:i:s', getFileMTime(__FILE__)).' GMT', true, 200);
}
if (!get_cfg_var("zlib.output_compression") && {$use_gzip}) ob_start ("ob_gzhandler"); else ob_start ();
HEADER;
		$header .= $this->getServingWrapperContentLines();
		return $header;
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
	 * Get the text lines of PHP to include in the serving wrapper to pull the content
	 * when the serving wrapper is executed
	 *
	 * @abstract
	 * @return mixed
	 */
	abstract protected function getServingWrapperContentLines();

	/**
	 * @param $file_name
	 * @param $file_contents
	 *
	 * @throws Exception
	 */
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

	abstract protected function writeData();

	/**
	 *
	 * @return bool
	 */
	public function isExpired()
	{
		//see if file is stale
		$expired    = $this->isDataExpired();
		if ($expired && $this->wrapped && file_exists($this->wrapper_file_name)) {
			$expired = ((int)strtotime('now') > ((int)filectime($this->wrapper_file_path) + (int)$this->options->cache_time)) ? true : false;
		}
		return $expired;
	}

	abstract protected function isDataExpired();


	/**
	 *
	 * @return bool
	 */
	public function doesExist()
	{
		$exists = $this->doesDataExist();
		if ($exists && $this->wrapped && !file_exists($this->wrapper_file_path)) {
			$exists = false;
		}
		return $exists;
	}

	abstract protected function doesDataExist();

	/**
	 * @return string
	 */
	public function getUrl()
	{
		return $this->options->cache_url . $this->wrapper_file_name;
	}

	/**
	 *
	 */
	public function setAsValid()
	{
		$this->setDataAsValid();
		if ($this->wrapped && file_exists($this->wrapper_file_name)) {
			touch($this->wrapper_file_name);
		}
	}

	abstract protected function setDataAsValid();

	/**
	 * @param $file_name
	 *
	 * @return string
	 */
	protected function getFilePath($file_name)
	{
		$dir = preg_replace('#[/\\\\]+#', DIRECTORY_SEPARATOR, $this->options->cache_path);
		return $dir . $file_name;
	}
}
