<?php
/**
 * @version   $Id: Wordpress.php 10831 2013-05-29 19:32:17Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2014 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

global $wp_did_header;

/**
 *
 */
class RokCommon_Header_Wordpress extends RokCommon_Header_AbstractHeader
{
	/**
	 *
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * @param     $file
	 * @param int $order
	 *
	 * @return void
	 */
	public function addScript($file, $order = self::DEFAULT_ORDER)
	{
		global $wp_scripts;

		$path_parts = pathinfo($file);

		//check if its a file or handle
		if ($path_parts['extension'] == 'js') {

			//differentiate between the plugin files and the widget files
			if ((strpos($file, 'widget')) === false) {
				$handle = 'rok_' . str_replace('.', '_', basename($file));
			} else {
				$handle = 'rok_widget_' . str_replace('.', '_', basename($file));
			}

			//check if wordpress head has run
			if (!did_action('wp_head')) {

				//check if its already registered or queued
				wp_register_script($handle, $file);
				wp_enqueue_script($handle);

			} else {
				//wordpress head already ran so...
				$file_root = str_replace($wp_scripts->base_url, ABSPATH, $file);
				if (file_exists($file_root)) {
					echo "<script type='text/javascript' src='$file'></script>\n";
				}
			}

		} else {
			//might be a handle
			wp_enqueue_script($file);
		}
	}

	/**
	 * @param     $text
	 * @param int $order
	 *
	 * @return void
	 */
	public function addInlineScript($text, $order = self::DEFAULT_ORDER)
	{
		echo "<script type=\"text/javascript\">\n" . (string)$text . "\n</script>";
	}

	/**
	 * @param     $file
	 * @param int $order
	 *
	 * @return void
	 */
	public function addStyle($file, $order = self::DEFAULT_ORDER)
	{
		global $wp_styles;

		$path_parts = pathinfo($file);

		//check if its a file or handle
		if ($path_parts['extension'] == 'css') {

			//differentiate between the plugin files and the widget files
			if ((strpos($file, 'widget')) === false) {
				$handle = 'rok_' . str_replace('.', '_', basename($file));
			} else {
				$handle = 'rok_widget_' . str_replace('.', '_', basename($file));
			}

			//check if wordpress head has run
			if (!did_action('wp_head')) {

				wp_register_style($handle, $file);
				wp_enqueue_style($handle);

			} else {
				//wordpress head already ran so...
				$file_root = str_replace($wp_styles->base_url, ABSPATH, $file);
				if (file_exists($file_root)) {
					echo "<link rel='stylesheet' id='$handle' href='$file' type='text/css' media='all' />\n";
				}
			}

		} else {
			//might be a handle
			wp_enqueue_style($file);
		}
	}


	/**
	 * @param     $text
	 * @param int $order
	 *
	 * @return void
	 */
	public function addInlineStyle($text, $order = self::DEFAULT_ORDER)
	{
		echo "<style type=\"text/css\">\n" . (string)$text . "\n</style>";
	}

	public function populate()
	{
		// get line endings
		$lnEnd   = "\13";
		$tab     = "\11";
		$strHtml = '';

		// Generate domready script
		if (!empty($this->domready_scripts)) {
			ksort($this->domready_scripts);
			$strHtml = 'window.addEvent(\'domready\', function() {';
			foreach ($this->domready_scripts as $order => $order_entries) {
				foreach ($order_entries as $entry_key => $entry) {
					$strHtml .= $tab . '<script type="text/javascript">//<![CDATA[' . $lnEnd;
					// This is for full XHTML support.
					$strHtml .= 'window.addEvent(\'domready\', function() {' . $entry . $lnEnd . '});';
					$strHtml .= $tab . '//]]></script>' . $lnEnd;
				}
			}
			$strHtml .= chr(13) . '});' . chr(13);
		}

		if (!empty($this->loadevent_scripts)) {
			ksort($this->loadevent_scripts);
			$strHtml = 'window.addEvent(\'load\', function() {';
			foreach ($this->loadevent_scripts as $order => $order_entries) {
				foreach ($order_entries as $entry_key => $entry) {
					$strHtml .= $tab . '<script type="text/javascript">//<![CDATA[' . $lnEnd;
					// This is for full XHTML support.
					$strHtml .= 'window.addEvent(\'load\', function() {' . $this->_loadevent_script . $lnEnd . '});';
					$strHtml .= $tab . '//]]></script>' . $lnEnd;
				}
			}
			$strHtml .= chr(13) . '});' . chr(13);
			$this->document->addScriptDeclaration($strHtml);
		}


		echo $strHtml;
	}

}

