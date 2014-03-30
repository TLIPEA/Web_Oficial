<?php
/**
 * @version   $Id: rokbox.php 14085 2013-10-03 01:07:39Z djamil $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2014 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

defined('_JEXEC') or die;

/**
 * Class plgSystemRokBox
 */
class plgSystemRokBox extends JPlugin
{
	/**
	 * @var string
	 */
	protected $_version = '2.0.7';
	/**
	 * @var string
	 */
	protected $_basepath = '/plugins/system/rokbox/';
	/**
	 * @var bool
	 */
	protected static $_assetsLoad = false;

	/**
	 * @param $subject
	 * @param $config
	 */
	public function plgSystemRokBox(&$subject, $config)
	{
		parent::__construct($subject, $config);
	}

	/**
	 *
	 */
	public function onAfterDispatch()
	{
		if (self::$_assetsLoad) return;

		$app = JFactory::getApplication();

		if ($app->isAdmin()) return; // We want RokBox only on frontend

		$document = JFactory::getDocument();
		$doctype  = $document->getType();

		if ($doctype !== 'html') return; // Only render for HTML output

		JHtml::_('behavior.framework', true);

		$this->compileLess();
		$this->compileJS();
		$document->addScriptDeclaration("if (typeof RokBoxSettings == 'undefined') RokBoxSettings = {pc: '" . $this->params->get('viewport_pc', 100) . "'};");

		self::$_assetsLoad = true;

	}

	/**
	 *
	 */
	public function onAfterRender()
	{
		if (!$this->params->get('backwards_compat', false)) return;

		$app = JFactory::getApplication();
		if ($app->isAdmin()) return; // We want RokBox only on frontend

		$document = JFactory::getDocument();
		$doctype  = $document->getType();
		if ($doctype == 'html') {
			$body = JResponse::getBody();

			if (!class_exists('phpQuery', false)) {
				require_once(JPATH_PLUGINS . '/system/rokbox/lib/pq.php');
			}

			//$html = str_get_html($body);
			$pq = phpQuery::newDocument($body);
			foreach ($pq->find('a[rel^="rokbox"]') as $element) {
				$element = pq($element);
				$title   = $element->attr('title');
				$rel     = $element->attr('rel');

				preg_match("/\((.+)\)/", $rel, $album);
				$album = isset($album[1]) ? $album[1] : false;

				//preg_match("/\[(\d{1,})\s(\d{1,})\]\[module=(.+)\]/", $rel, $module);
				preg_match("/\[module=(.+)\]/", $rel, $module);
				$module = isset($module[1]) ? '#' . $module[1] : false;

				preg_match("/\[(\d{1,})\s+(\d{1,})\]/", $rel, $size);
				$size = isset($size[1]) && isset($size[2]) ? $size[1] . ' ' . $size[2] : false;

				if (strlen($title)) {
					@list($title, $caption) = explode(' :: ', $title);
					$caption = '<h4>' . $title . '</h4>' . (isset($caption) && strlen($caption) ? '<span>' . $caption . '</span>' : '');
				}

				$element->removeAttr('rel');
				$element->attr('data-rokbox', '');
				if ($title) $element->attr('data-rokbox-caption', $caption);
				if ($album) $element->attr('data-rokbox-album', $album);
				if ($module) $element->attr('data-rokbox-element', $module);
				if ($size) $element->attr('data-rokbox-size', $size);
			}
			JResponse::setBody($pq->getDocument()->htmlOuter());

			//$links = preg_match_all("|<a[^>]+rel\s*=\s*["']([^"']+)["'][^>]*>(.*?)<\/a>|i", $body, matches);
			//var_dump($this->getAttribute(, $body));
		}
	}

	/**
	 *
	 */
	protected function compileLess()
	{
		global $app;

		$document = JFactory::getDocument();
		$assets   = JPATH_SITE . $this->_basepath . 'assets';

		if ((defined('ROKBOX_DEV') && ROKBOX_DEV) || @file_exists($assets . '/less/lessc.inc.php')) {
			//define("DEV", true); // to recompile/unify JS
			@include_once($assets . '/less/lessc.inc.php');
			try {
				$css_file = $assets . '/styles/rokbox.css';
				@unlink($css_file);
				lessc::ccompile($assets . '/less/global.less', $css_file);
			} catch (exception $e) {
				JError::raiseError('LESS Compiler', $e->getMessage());
			}
		}

		$document->addStyleSheet(JURI::root(true) . $this->_basepath . 'assets/styles/rokbox.css');
	}

	/**
	 *
	 */
	protected function compileJS()
	{
		$document = JFactory::getDocument();
		$assets   = JPATH_SITE . $this->_basepath . 'assets';

		if ((defined('ROKBOX_DEV') && ROKBOX_DEV) || @file_exists($assets . '/less/lessc.inc.php')) {
			$buffer = "";
			$assets = JPATH_SITE . $this->_basepath . 'assets';

			$app    = $assets . '/application/';
			$output = $assets . '/js/';

			$files = array(
				$app . 'moofx',
				$app . 'mootools-mobile',
				$app . 'RokBox.Media',
				$app . 'RokBox'
			);

			foreach ($files as $file) {
				$file    = $file . '.js';
				$content = false;

				if (file_exists($file)) $content = file_get_contents($file);

				$buffer .= (!$content) ? "\n\n !!! File not Found: " . $file . " !!! \n\n" : $content;
			}

			file_put_contents($output . 'rokbox.js', $buffer);
		}

		$document->addScript(JURI::root(true) . $this->_basepath . 'assets/js/rokbox.js');
	}

	/*protected function getAttribute($attrib, $tag = 'a'){
		//gets an attribute value from the html tag
		$regex = '/' . preg_quote($attrib) . '=([\'"])?((?(1).+?|[^\s>]+))(?(1)\1)/is';

		if (preg_match($regex, $tag, $match)){
			return urldecode($match[2]);
		}

		return false;
	}*/
}
