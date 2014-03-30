<?php
/**
 *  @package AdminTools
 *  @copyright Copyright (c)2010-2014 Nicholas K. Dionysopoulos
 *  @license GNU General Public License version 3, or later
 *  @version $Id$
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

class AdmintoolsModelFixperms extends FOFModel
{
	/** @var float The time the process started */
	private $startTime = null;

	/** @var array The folders to process */
	private $folderStack = array();

	/** @var array The files to process */
	private $filesStack = array();

	/** @var int Total numbers of folders in this site */
	public $totalFolders = 0;

	/** @var int Numbers of folders already processed */
	public $doneFolders = 0;

	/** @var int Default directory permissions */
	private $dirperms = 0755;

	/** @var int Default file permissions */
	private $fileperms = 0644;

	/** @var array Custom permissions */
	private $customperms = array();
	
	/** @var array Skip subdirectories and files of these directories */
	private $skipDirs = array();
	
	public function  __construct($config = array()) {
		parent::__construct($config);

		if(interface_exists('JModel')) {
			$params = JModelLegacy::getInstance('Storage','AdmintoolsModel');
		} else {
			$params = JModel::getInstance('Storage','AdmintoolsModel');
		}

		$dirperms = '0'.ltrim(trim($params->getValue('dirperms', '0755')),'0');
		$fileperms = '0'.ltrim(trim($params->getValue('fileperms', '0644')),'0');

		$dirperms = octdec($dirperms);
		if( ($dirperms < 0400) || ($dirperms > 0777) ) $dirperms = 0755;
		$this->dirperms = $dirperms;

		$fileperms = octdec($fileperms);
		if( ($fileperms < 0400) || ($fileperms > 0777) ) $fileperms = 0755;
		$this->fileperms = $fileperms;

		$db = $this->getDBO();
		$query = $db->getQuery(true)
			->select(array(
				$db->quoteName('path'),
				$db->quoteName('perms')
			))->from($db->quoteName('#__admintools_customperms'))
			->order($db->quoteName('path').' ASC');
		$db->setQuery($query);
		$this->customperms = $db->loadAssocList('path');
		
		// Add cache, tmp and log to the exceptions
		$this->skipDirs[] = rtrim(JPATH_CACHE,'/');
		$this->skipDirs[] = rtrim(JPATH_ROOT.'/cache','/');
		if(version_compare(JVERSION, '3.0', 'ge')) {
			$this->skipDirs[] = rtrim(JFactory::getConfig()->get('tmp_path', JPATH_ROOT.'/tmp'), '/');
			$this->skipDirs[] = rtrim(JFactory::getConfig()->get('log_path', JPATH_ROOT.'/logs'), '/');
		} else {
			$this->skipDirs[] = rtrim(JFactory::getConfig()->getValue('tmp_path', JPATH_ROOT.'/tmp'), '/');
			$this->skipDirs[] = rtrim(JFactory::getConfig()->getValue('log_path', JPATH_ROOT.'/logs'), '/');
		}
	}

	/**
	 * Returns the current timestampt in decimal seconds
	 */
	private function microtime_float()
	{
		list($usec, $sec) = explode(" ", microtime());
		return ((float)$usec + (float)$sec);
	}

	/**
	 * Starts or resets the internal timer
	 */
	private function resetTimer()
	{
		$this->startTime = $this->microtime_float();
	}

	/**
	 * Makes sure that no more than 3 seconds since the start of the timer have
	 * elapsed
	 * @return bool
	 */
	private function haveEnoughTime()
	{
		$now = $this->microtime_float();
		$elapsed = abs($now - $this->startTime);
		return $elapsed < 2;
	}

	/**
	 * Saves the file/folder stack in the session
	 */
	private function saveStack()
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true)
			->delete($db->quoteName('#__admintools_storage'))
			->where($db->quoteName('key').' = '.$db->quote('fixperms_stack'));
		$db->setQuery($query);
		$db->execute();
		
		$object = (object)array(
			'key'	=> 'fixperms_stack',
			'value'	=> json_encode(array(
				'folders'	=> $this->folderStack,
				'files'		=> $this->filesStack,
				'total'		=> $this->totalFolders,
				'done'		=> $this->doneFolders
			))
		);
		$db->insertObject('#__admintools_storage', $object);
	}

	/**
	 * Resets the file/folder stack saved in the session
	 */
	private function resetStack()
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true)
			->delete($db->quoteName('#__admintools_storage'))
			->where($db->quoteName('key').' = '.$db->quote('fixperms_stack'));
		$db->setQuery($query);
		$db->execute();
		
		$this->folderStack = array();
		$this->filesStack = array();
		$this->totalFolders = 0;
		$this->doneFolders = 0;
	}

	/**
	 * Loads the file/folder stack from the session
	 */
	private function loadStack()
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true)
			->select(array($db->quoteName('value')))
			->from($db->quoteName('#__admintools_storage'))
			->where($db->quoteName('key').' = '.$db->quote('fixperms_stack'));
		$db->setQuery($query);
		$stack = $db->loadResult();

		if(empty($stack))
		{
			$this->folderStack = array();
			$this->filesStack = array();
			$this->totalFolders = 0;
			$this->doneFolders = 0;
			return;
		}

		$stack = json_decode($stack, true);

		$this->folderStack = $stack['folders'];
		$this->filesStack = $stack['files'];
		$this->totalFolders = $stack['total'];
		$this->doneFolders = $stack['done'];
	}

	/**
	 * Scans $root for directories and updates $folderStack
	 * @param string $root The full path of the directory to scan
	 */
	public function getDirectories($root = null)
	{
		if(empty($root)) $root = JPATH_ROOT;
		JLoader::import('joomla.filesystem.folder');
		
		if(in_array(rtrim($root,'/'), $this->skipDirs)) return;

		$folders = JFolder::folders($root,'.',false,true);
		$this->totalFolders += count($folders);
		if(!empty($folders)) $this->folderStack = array_merge($this->folderStack, $folders);
	}

	/**
	 * Scans $root for files and updates $filesStack
	 * @param string $root The full path of the directory to scan
	 */
	public function getFiles($root = null)
	{
		if(empty($root)) $root = JPATH_ROOT;

		if(empty($root))
		{
			$root = '..';
			$root = realpath($root);
		}
		
		if(in_array(rtrim($root,'/'), $this->skipDirs)) return;

		$root = rtrim($root,'/').'/';

		JLoader::import('joomla.filesystem.folder');

		$folders = JFolder::files($root,'.',false,true);
		$this->filesStack = array_merge($this->filesStack, $folders);

		$this->totalFolders += count($folders);
	}

	public function startScanning()
	{
		$this->resetStack();
		$this->resetTimer();
		$this->getDirectories();
		$this->getFiles();
		$this->saveStack();

		if(!$this->haveEnoughTime())
		{
			return true;
		}
		else
		{
			return $this->run(false);
		}
	}

	public function chmod($path, $mode)
	{
		if(is_string($mode))
		{
			$mode = octdec($mode);
			if( ($mode <= 0) || ($mode > 0777) ) $mode = 0755;
		}

		// Initialize variables
		JLoader::import('joomla.client.helper');
		$ftpOptions = JClientHelper::getCredentials('ftp');

		// Check to make sure the path valid and clean
		$path = JPath::clean($path);

		if ($ftpOptions['enabled'] == 1) {
			// Connect the FTP client
			JLoader::import('joomla.client.ftp');
			if(version_compare(JVERSION,'3.0','ge')) {
				$ftp = JClientFTP::getInstance(
					$ftpOptions['host'], $ftpOptions['port'], array(),
					$ftpOptions['user'], $ftpOptions['pass']
				);
			} else {
				$ftp = JFTP::getInstance(
					$ftpOptions['host'], $ftpOptions['port'], array(),
					$ftpOptions['user'], $ftpOptions['pass']
				);
			}
		}

		if(@chmod($path, $mode))
		{
			$ret = true;
		} elseif ($ftpOptions['enabled'] == 1) {
			// Translate path and delete
			JLoader::import('joomla.client.ftp');
			$path = JPath::clean(str_replace(JPATH_ROOT, $ftpOptions['root'], $path), '/');
			// FTP connector throws an error
			$ret = $ftp->chmod($path, $mode);
		} else {
			return false;
		}
	}

	public function run($resetTimer = true)
	{
		if($resetTimer) $this->resetTimer();

		$this->loadStack();

		$result = true;
		while($result && $this->haveEnoughTime())
		{
			$result = $this->RealRun();
		}

		$this->saveStack();

		return $result;
	}

	private function RealRun()
	{
		while(empty($this->filesStack) && !empty($this->folderStack))
		{
			// Get a directory
			$dir = null;

			while(empty($dir) && !empty($this->folderStack))
			{
				// Get the next directory
				$dir = array_shift($this->folderStack);
				// Skip over non-directories and symlinks
				if(!@is_dir($dir) || @is_link($dir))
				{
					$dir = null;
					continue;
				}
				// Skip over . and ..
				$checkDir = str_replace('\\','/',$dir);
				if( in_array(basename($checkDir), array('.','..')) || (substr($checkDir,-2) == '/.') || (substr($checkDir,-3) == '/..') )
				{
					$dir = null;
					continue;
				}
				// Check for custom permissions
				$reldir = $this->getRelativePath($dir);
				if(array_key_exists($reldir, $this->customperms)) {
					$perms = $this->customperms[$reldir]['perms'];
				} else {
					$perms = $this->dirperms;
				}

				// Apply new permissions
				$this->chmod($dir, $perms);
				$this->doneFolders++;
				$this->getDirectories($dir);
				$this->getFiles($dir);

				if(!$this->haveEnoughTime())
				{
					// Gotta continue in the next step
					return true;
				}
			}
		}

		if(empty($this->filesStack) && empty($this->folderStack))
		{
			// Just finished
			$this->resetStack();
			return false;
		}

		if(!empty($this->filesStack) && $this->haveEnoughTime())
		{
			while(!empty($this->filesStack))
			{
				$file = array_shift($this->filesStack);

				// Skip over symlinks and non-files
				if(@is_link($file) || !@is_file($file))
				{
					continue;
				}

				$reldir = $this->getRelativePath($file);
				if(array_key_exists($reldir, $this->customperms)) {
					$perms = $this->customperms[$reldir]['perms'];
				} else {
					$perms = $this->fileperms;
				}

				$this->chmod($file, $perms);
				$this->doneFolders++;
			}
		}

		if(empty($this->filesStack) && empty($this->folderStack))
		{
			// Just finished
			$this->resetStack();
			return false;
		}

		return true;
	}

	public function getRelativePath($somepath)
	{
		$path = JPath::clean($somepath,'/');

		// Clean up the root
		$root = JPath::clean(JPATH_ROOT, '/');

		// Find the relative path and get the custom permissions
		$relpath = ltrim(substr($path, strlen($root) ), '/');

		return $relpath;
	}

}