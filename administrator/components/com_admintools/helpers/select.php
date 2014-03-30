<?php
/**
 * @package AkeebaReleaseSystem
 * @copyright Copyright (c)2010-2014 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 3, or later
 * @version $Id$
 */

defined('_JEXEC') or die();

class AdmintoolsHelperSelect
{
	protected static function genericlist($list, $name, $attribs, $selected, $idTag)
	{
		if(empty($attribs))
		{
			$attribs = null;
		}
		else
		{
			$temp = '';
			foreach($attribs as $key=>$value)
			{
				$temp .= $key.' = "'.$value.'"';
			}
			$attribs = $temp;
		}

		return JHTML::_('select.genericlist', $list, $name, $attribs, 'value', 'text', $selected, $idTag);
	}

	public static function valuelist($options, $name, $attribs = null, $selected = null, $ignoreKey = false)
	{
		$list = array();
		foreach($options as $k => $v) {
			if($ignoreKey) $k = $v;
			$list[] = JHTML::_('select.option', $k, $v );
		}
		return self::genericlist($list, $name, $attribs, $selected, $name);
	}

	public static function booleanlist( $name, $attribs = null, $selected = null )
	{
		$options = array(
			JHTML::_('select.option','-1','---'),
			JHTML::_('select.option',  '0', JText::_( 'JNO' ) ),
			JHTML::_('select.option',  '1', JText::_( 'JYES' ) )
		);
		return self::genericlist($options, $name, $attribs, $selected, $name);
	}

	public static function csrflist( $name, $attribs = null, $selected = null )
	{
		$options = array(
			JHTML::_('select.option','-1','---'),
			JHTML::_('select.option',  '0', JText::_( 'ATOOLS_LBL_WAF_OPT_CSRFSHIELD_NO' ) ),
			JHTML::_('select.option',  '1', JText::_( 'ATOOLS_LBL_WAF_OPT_CSRFSHIELD_BASIC' ) ),
			JHTML::_('select.option',  '2', JText::_( 'ATOOLS_LBL_WAF_OPT_CSRFSHIELD_ADVANCED' ) )
			);
		return self::genericlist($options, $name, $attribs, $selected, $name);
	}

	public static function autoroots( $name, $attribs = null, $selected = null )
	{
		$options = array(
			JHTML::_('select.option','-1','---'),
			JHTML::_('select.option',  '0', JText::_( 'ATOOLS_LBL_HTMAKER_AUTOROOT_OFF' ) ),
			JHTML::_('select.option',  '1', JText::_( 'ATOOLS_LBL_HTMAKER_AUTOROOT_STD' ) ),
			JHTML::_('select.option',  '2', JText::_( 'ATOOLS_LBL_HTMAKER_AUTOROOT_ALT' ) )
		);
		return self::genericlist($options, $name, $attribs, $selected, $name);
	}

	public static function published($selected = null, $id = 'enabled', $attribs = array())
	{
		$options = array();
		$options[] = JHTML::_('select.option','','- '.JText::_('ATOOLS_LBL_SELECT_STATE').' -');
		$options[] = JHTML::_('select.option',0,JText::_('UNPUBLISHED'));
		$options[] = JHTML::_('select.option',1,JText::_('PUBLISHED'));

		return self::genericlist($options, $id, $attribs, $selected, $id);
	}

	public static function reasons($selected = null, $id='reason', $attribs=array())
	{
		$reasons = array('other','adminpw','ipwl','ipbl','sqlishield','antispam',
			'tpone','tmpl','template','muashield','csrfshield','badbehaviour',
			'geoblocking','rfishield','dfishield','uploadshield','xssshield',
			'httpbl', 'loginfailure', 'securitycode');
		$options = array();
		$options[] = JHTML::_('select.option','','- '.JText::_('ATOOLS_LBL_REASON_SELECT').' -');
		foreach($reasons as $reason)
		{
			$options[] = JHTML::_('select.option',$reason,JText::_('ATOOLS_LBL_REASON_'.strtoupper($reason)));
		}

		return self::genericlist($options, $id, $attribs, $selected, $id);
	}

	public static function wwwredirs( $name, $attribs = null, $selected = null )
	{
		$options = array(
			JHTML::_('select.option','-1','---'),
			JHTML::_('select.option',  '0', JText::_( 'ATOOLS_LBL_HTMAKER_WWWREDIR_NO' ) ),
			JHTML::_('select.option',  '1', JText::_( 'ATOOLS_LBL_HTMAKER_WWWREDIR_WWW' ) ),
			JHTML::_('select.option',  '2', JText::_( 'ATOOLS_LBL_HTMAKER_WWWREDIR_NONWWW' ) )
		);
		return self::genericlist($options, $name, $attribs, $selected, $name);
	}

	public static function blockinstallopts( $name, $attribs = null, $selected = null )
	{
		$options = array(
			JHTML::_('select.option','-1','---'),
			JHTML::_('select.option',  '0', JText::_( 'ATOOLS_LBL_WAF_OPT_BLOCKINSTALL_NO' ) ),
			JHTML::_('select.option',  '1', JText::_( 'ATOOLS_LBL_WAF_OPT_BLOCKINSTALL_ADMIN' ) ),
			JHTML::_('select.option',  '2', JText::_( 'ATOOLS_LBL_WAF_OPT_BLOCKINSTALL_ALL' ) )
		);
		return self::genericlist($options, $name, $attribs, $selected, $name);
	}

	public static function perms( $name, $attribs = null, $selected = null )
	{
		$rawperms = array(0400,0440,0444,0600,0640,0644,0660,0664,0700,0740,0744,0750,0754,0755,0757,0770,0775,0777);

		$options = array();
		$options[] = JHTML::_('select.option','','---');

		foreach($rawperms as $perm)
		{
			$text = decoct($perm);
			$options[] = JHTML::_('select.option','0'.$text,$text);
		}
		return self::genericlist($options, $name, $attribs, $selected, $name);
	}

	public static function trsfreqlist( $name, $attribs = null, $selected = null )
	{
		$freqs = array('second','minute','hour','day');

		$options = array();
		$options[] = JHTML::_('select.option','','---');
		foreach($freqs as $freq)
		{
			$text = JText::_('ATOOLS_LBL_WAF_LBL_FREQ'.strtoupper($freq));
			$options[] = JHTML::_('select.option',$freq,$text);
		}
		return self::genericlist($options, $name, $attribs, $selected, $name);
	}

	public static function deliverymethod( $name, $attribs = null, $selected = null )
	{
		$options = array(
			JHTML::_('select.option','-1','---'),
			JHTML::_('select.option',  'plugin', JText::_( 'ATOOLS_LBL_SEOANDLINK_OPT_JSDELIVERY_PLUGIN' ) ),
			JHTML::_('select.option',  'direct', JText::_( 'ATOOLS_LBL_SEOANDLINK_OPT_JSDELIVERY_DIRECT' ) )
		);
		return self::genericlist($options, $name, $attribs, $selected, $name);
	}

	public static function httpschemes( $name, $attribs = null, $selected = null )
	{
		$options = array(
			JHTML::_('select.option',  'http', JText::_( 'ATOOLS_LBL_WAFCONFIG_IPLOOKUPSCHEME_HTTP' ) ),
			JHTML::_('select.option',  'https', JText::_( 'ATOOLS_LBL_WAFCONFIG_IPLOOKUPSCHEME_HTTPS' ) )
		);
		return self::genericlist($options, $name, $attribs, $selected, $name);
	}

	public static function scanresultstatus($name, $attribs = null, $selected = null)
	{
		$options = array(
			JHTML::_('select.option',	'',				'- '.JText::_('ATOOLS_LBL_SELECT_STATE').' -'),
			JHTML::_('select.option',	'new',			JText::_( 'COM_ADMINTOOLS_LBL_SCANALERTS_STATUS_NEW' ) ),
			JHTML::_('select.option',	'suspicious',	JText::_( 'COM_ADMINTOOLS_LBL_SCANALERTS_STATUS_SUSPICIOUS' ) ),
			JHTML::_('select.option',	'modified',		JText::_( 'COM_ADMINTOOLS_LBL_SCANALERTS_STATUS_MODIFIED' ) ),
		);
		return self::genericlist($options, $name, $attribs, $selected, $name);
	}

	public static function symlinks($name, $attribs = null, $selected = null)
	{
		$options = array(
			JHTML::_('select.option',	'0',	JText::_('COM_ADMINTOOLS_LBL_HTMAKER_SYMLINKS_OFF')),
			JHTML::_('select.option',	'1',	JText::_('COM_ADMINTOOLS_LBL_HTMAKER_SYMLINKS_FOLLOW')),
			JHTML::_('select.option',	'2',	JText::_('COM_ADMINTOOLS_LBL_HTMAKER_SYMLINKS_IFOWNERMATCH')),
		);
		return self::genericlist($options, $name, $attribs, $selected, $name);
	}
}