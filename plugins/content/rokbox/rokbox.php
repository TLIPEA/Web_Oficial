<?php
/**
 * @version   $Id: rokbox.php 14087 2013-10-03 01:30:39Z djamil $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2014 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

defined('_JEXEC') or die;

class plgContentRokbox extends JPlugin
{
    protected $_version = '2.0.7';
    protected $_basepath = '/plugins/content/rokbox/';

    function plgContentRokbox(&$subject, $params)
    {
        parent::__construct($subject, $params);
    }

    function onContentPrepare($context, &$article, &$params, $page = 0)
    {
        // simple performance check to determine whether bot should process further
        if (strpos($article->text, 'rokbox') === false) return true;
        // Get plugin info
        $plugin = JPluginHelper::getPlugin('content', 'rokbox');

        // define the regular expression for the bot
        $regex = "#{rokbox(.*?)}(.*?){/rokbox}#smi";
        $thumb_generator = "#data-rokbox-generate-thumbnail#smi";

        $pluginParams = new JRegistry($plugin->params);

        // check whether plugin has been unpublished
        if (!$pluginParams->get('enabled', 1)) {
            $article->text = preg_replace($regex, '', $article->text);
            return true;
        }

        preg_match_all($thumb_generator, $article->text, $matches);
        $count = count($matches[0]);
        if ($count){
            // Get plugin parameters
            $style = $pluginParams->def('style', -2);
            $this->plgContentGenerateThumbnails($article, $matches, $count, $thumb_generator, $pluginParams);
        }

        if (!$pluginParams->get('backwards_compat', false)) return true;
        // find all instances of plugin and put in $matches
        preg_match_all($regex, $article->text, $matches);

        // Number of plugins
        $count = count($matches[0]);

        // plugin only processes if there are any instances of the plugin in the text
        if ($count){
            // Get plugin parameters
            $style = $pluginParams->def('style', -2);
            $this->plgContentProcessSnippets($article, $matches, $count, $regex, $pluginParams);
        }
    }

    function plgContentGenerateThumbnails(&$row, &$matches, $count, $regex, &$botParams){
        if (!class_exists('phpQuery', false)) {
            require_once(JPATH_PLUGINS . '/system/rokbox/lib/pq.php');
        }

        //$html = str_get_html($body);
        $pq = phpQuery::newDocument($row->text);
        foreach ($pq->find('a[data-rokbox-generate-thumbnail]') as $element) {
            $element = pq($element);
            $href   = $element->attr('href');

            if (
                !preg_match("/\.(jpe?g|png|gif|bmp|tiff?)$/i", $href, $extension) ||
                !is_file($href)
            ) continue;

            if (substr($href, -10) == '_thumb.jpg' || substr($href, -10) == '-thumb.jpg'){
                $element->html($href);
                continue;
            }

            $extension = $extension[0];
            $basename = substr($href, 0, -(strlen($extension)));
            $input = JPATH_ROOT . '/' . $basename . $extension;
            $output = JPATH_ROOT . '/' . $basename . '_thumb.jpg';

            if (@file_exists($output)){
                $imageSize = @getimagesize($input);
                $element->html('<img class="rokbox-thumb" src="'.$basename.'_thumb.jpg" style="max-width:'.$imageSize[0].'px;max-height:'.$imageSize[1].'px;" />');
                continue;
            }

            @require_once(dirname(__FILE__) . '/libs/imagehandler.php');
            $sizeSettings = array(
                'width' => $botParams->get('thumb_width', 150),
                'height' => $botParams->get('thumb_height', 100)
            );
            $imageSize = @getimagesize($input);

            if ($imageSize[0] < $sizeSettings['width']) $sizeSettings['width'] = $imageSize[0];
            if ($imageSize[1] < $sizeSettings['height']) $sizeSettings['height'] = $imageSize[1];

            $thumb = new imgRedim(false, false, JPATH_CACHE);
            $thumb->loadImage($input);
            $thumb->redimToSize($sizeSettings['width'], $sizeSettings['height'], true);
            $thumb->saveImage($output, $botParams->get('thumb_quality', 90));

            $element->html('<img class="rokbox-thumb" src="'.$basename.'_thumb.jpg" style="max-width:'.$sizeSettings['width'].'px;max-height:'.$sizeSettings['height'].'px;" />');
        }

        $row->text = $pq->getDocument()->htmlOuter();
    }

    function plgContentProcessSnippets(&$row, &$matches, $count, $regex, &$botParams){
        $snippets = $matches[0];
        $snippets_settings = $matches[1];
        $snippets_links = $matches[2];

        $text = $row->text;

        foreach ($snippets as $index => $snippet) {
            $settings = isset($snippets_settings[$index]) ? trim($snippets_settings[$index]) : '';
            $href = isset($snippets_links[$index]) ? trim($snippets_links[$index]) : '#';

            $prefix = 'data-rokbox-';
            $content = '';
            $options = $link = array();
            preg_match_all( "#([a-z]{1,})=\|(.*?)\|#si", $settings, $data);

            // let's clean the data from unnecessary stuff
            //if ($needle = array_search('size', $data[1])) { unset($data[0][$needle]); unset($data[1][$needle]); unset($data[2][$needle]); }

            // logic for wildcards scan
            $items = @glob(ltrim($href, '/'));
            if (!preg_match("/\.(jpe?g|png|gif|bmp|tiff?)$/", $href)) $items = array();

            if (count($items)){
                if (!array_search('thumb', $data[1]) && count($items) > 1) array_push($data[1], 'thumb');
                elseif (!array_search('thumb', $data[1]) && !array_search('text', $data[1])) array_push($data[1], 'thumb');

                if (array_search('thumb', $data[1]) && $needle = array_search('text', $data[1])) { unset($data[0][$needle]); unset($data[1][$needle]); unset($data[2][$needle]); }
                $wildcards = array();
            }


            if (!count($items)){
                if (($needle = array_search('text', $data[1])) === false) $content = $data[2][$needle];
                elseif (($needle = array_search('title', $data[1])) === false) $content = $data[2][$needle];
                else $content = 'Image not found';

                if (!array_search('thumb', $data[1]) || (is_dir(dirname($href)) && !is_file($href))){
                    array_push($items, $href);
                }
            }

            foreach ($data[1] as $key_index => $key) {
                $value = isset($data[2][$key_index]) ? $data[2][$key_index] : null;
                //$key = $options[$key];

                switch ($key) {
                    case 'text':
                        $content = $value;
                        break;
                    case 'thumb': case 'thumbnail':
                        if (!count($items) || !$value) array_push($items, $value);

                        foreach ($items as $value) {
                            if (
                                !preg_match("/\.(jpe?g|png|gif|bmp|tiff?)$/", $value, $extension) ||
                                !is_file($value)
                                ) break;

                            if (substr($value, -10) != '_thumb.jpg' && substr($value, -10) != '-thumb.jpg'){
                                @require_once(dirname(__FILE__) . '/libs/imagehandler.php');

                                $extension = $extension[0];
                                $basename = substr($value, 0, -(strlen($extension)));
                                $input = JPATH_ROOT . '/' . $basename . $extension;
                                $output = JPATH_ROOT . '/' . $basename . '_thumb.jpg';

                                $sizeSettings = array(
                                    'width' => $botParams->get('thumb_width', 150),
                                    'height' => $botParams->get('thumb_height', 100)
                                );
                                $imageSize = @getimagesize($input);

                                if ($imageSize[0] < $sizeSettings['width']) $sizeSettings['width'] = $imageSize[0];
                                if ($imageSize[1] < $sizeSettings['height']) $sizeSettings['height'] = $imageSize[1];

                                $thumb = new imgRedim(false, false, JPATH_CACHE);
                                $thumb->loadImage($input);
                                $thumb->redimToSize($sizeSettings['width'], $sizeSettings['height'], true);
                                $thumb->saveImage($output, $botParams->get('thumb_quality', 90));

                                $img = '<img class="rokbox-thumb" src="'.$basename.'_thumb.jpg" style="max-width:'.$sizeSettings['width'].'px;max-height:'.$sizeSettings['height'].'px;" />';
                            } else {
                                $imageSize = @getimagesize($value);
                                $img = '<img class="rokbox-thumb" src="'.$value.'" style="max-width:'.$imageSize[0].'px;max-height:'.$imageSize[1].'px;" />';
                            }

                            if (count($items) == 1) $content = $img;
                            else $wildcards[$value] = $img;
                        }
                        break;
                    default;
                        if ($key == 'title') $key = 'caption';
                        $value = htmlentities($value, ENT_QUOTES);
                        array_push($link, $prefix.$key.'="'.$value.'"');
                        break;
                }

            }

            $dataset = implode(' ', $link);

            if (count($items) == 1){
                $link = '<a data-rokbox class="rokbox-link" href="'.$href.'" '.$dataset.'>'.$content.'</a>';
            } else {
                $link = '';
                if (isset($wildcards) && count($wildcards)) {

                    if (count($wildcards) > 1){
                        $link = array(
                            '<div class="rokbox-album-wrapper">',
                                '<div class="rokbox-album-top">',
                                    '<div class="rokbox-album-top2">',
                                        '<div class="rokbox-album-top3"></div>',
                                    '</div>',
                                '</div>',
                                '<div class="rokbox-album-inner">',
                        );

                        foreach($wildcards as $href => $content){
                            array_push($link, '<a data-rokbox class="rokbox-link" href="'.$href.'" '.$dataset.'>'.$content.'</a>');
                        }

                        array_push($link, '</div><div class="rokbox-album-bottom"><div class="rokbox-album-bottom2"><div class="rokbox-album-bottom3"></div></div></div></div>');
                    } else {
                        $link = array();
                        foreach($wildcards as $href => $content){
                            array_push($link, '<a data-rokbox class="rokbox-link" href="'.$href.'" '.$dataset.'>'.$content.'</a>');
                        }
                    }

                    $link = implode("\n", $link);
                }
            }

            $text = str_replace($snippet, $link, $text);
        }

        $row->text = $text;
    }

}
