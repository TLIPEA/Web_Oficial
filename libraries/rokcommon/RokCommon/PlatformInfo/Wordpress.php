<?php
/**
 * @version   $Id: Wordpress.php 19616 2014-03-11 16:58:23Z jakub $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2014 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

class RokCommon_PlatformInfo_Wordpress extends RokCommon_PlatformInfo_AbstractPlatformInfo
{
    /**
     * Returns the URL for a given file based on the full file path passed in
     * @param $filepath
     * @return string
     */
    public function getUrlForPath($filepath)
    {
        $base = WP_CONTENT_URL;
        $file_real_path = self::clean($filepath,'/');
        $site_real_path = self::clean(WP_CONTENT_DIR,'/');
        $url_path = $base.str_replace($site_real_path,'',$file_real_path);
        return $url_path;
    }

    protected function clean($path, $ds=DS)
    {
        $path = trim($path);
        if (empty($path)) {
                $path = WP_CONTENT_DIR;
        } else {
                // Remove double slashes and backslahses and convert all slashes and backslashes to DS
                $path = preg_replace('#[/\\\\]+#', $ds, $path);
        }
        return $path;
    }

    /**
     * @param bool $admin
     *
     * @return string the name of the current template
     */
    public function getDefaultTemplate($admin = false)
    {
        global $wp_version;
        if (version_compare($wp_version,"3.4","<=")) {
            return get_current_theme();
        } else {
            $theme = wp_get_theme();
            return $theme->Name;
        }
    }

    /**
     * @param bool $admin
     *
     * @return string the path to the current template/theme root
     */
    public function getDefaultTemplatePath($admin = false)
    {
        return get_template_directory();
    }

    /**
     * @return string the path to the current platform root
     */
    public function getRootPath()
    {
        return rtrim(ABSPATH,'/\\');
    }

	public function getUrlBase()
	{
        if( is_multisite() ) {
            return trailingslashit( network_site_url() );
        } else {
            return trailingslashit( site_url() );
        }
	}


    /**
     * @param RokCommon_Service_Container $container
     */
    public function setPlatformParameters(RokCommon_Service_Container &$container){

        $container['platform.name'] = 'wordpress';
        $container['platform.displayname'] = 'Wordpress';
        $container['platform.version'] = get_bloginfo('version');
        $container['platform.root'] = $this->getRootPath();
        $container['template.name'] = $this->getDefaultTemplate();
        $container['template.path'] = $this->getDefaultTemplatePath();

    }

	/**
	 * @return string
	 */
	public function getInstanceId()
	{
		$siteurl = get_option('siteurl','');
		$home = get_option('home','');
		return md5($siteurl.$home);
	}

	/**
	 * Is the current platform RTL?
	 * @return mixed
	 */
	public function isRTL()
	{
		//TODO implement this
		return false;
	}

	/**
	 * @return string the root url to the current platform
	 */
	public function getRootUrl()
	{
		return get_site_url();
	}

	public function getPathForTemplate($template, $admin = false)
	{
		return get_template_directory();
	}

	/**
	 * Gets the seo version of a URL
	 *
	 * @param           $url
	 * @param bool      $xhtml
	 * @param bool|null $ssl
	 *
	 * @return mixed
	 */
	public function getSEOUrl($url, $xhtml = true, $ssl = null)
	{
		// TODO set this for SEO/NAMING/Routeing
		return $url;
	}


}
