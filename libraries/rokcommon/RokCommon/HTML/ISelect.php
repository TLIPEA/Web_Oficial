<?php
/**
 * @version   $Id: ISelect.php 10831 2013-05-29 19:32:17Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2014 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

interface RokCommon_HTML_ISelect
{
    /**
     * @abstract
     *
     * @param string                         $name
     * @param RokCommon_HTML_Select_Option[] $options
     * @param array                          $attribs
     *
     * @return string the html rendered select list
     */
    public function getList($name, array $options = array(), $attribs = array());
}
