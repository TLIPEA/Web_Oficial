<?php
/**
 * @version $Id: rokcandy.php 6765 2013-01-26 00:39:52Z steph $
 * @author RocketTheme, LLC http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2013 RocketTheme, LLC
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.plugin.plugin');

require_once (JPATH_ADMINISTRATOR.'/components/com_rokcandy/helpers/rokcandy.php' );

class plgSystemRokCandy extends JPlugin {

	var $_library;
	var $_debug;

	function __construct(& $subject, $config = array()) {
		parent :: __construct($subject, $config);
		
		$this->_debug = JFactory::getApplication()->input->getBool('debug_rokcandy') == true ? true : false;
    }
	
	function onAfterRoute() {
		$this->_library = RokCandyHelper::getMacros();
	}
    
    // Do BBCode replacements on the whole page
	function onAfterRender() {

		// don't run if disabled overrides are true
	    if ($this->_shouldProcess()) return;

		$document = JFactory::getDocument();
		$doctype = $document->getType();
		if ($doctype == 'html') {
			$body = JResponse::getBody();
			if ($this->_replaceCode($body)) {
				JResponse::setBody($body);
			}
		}
	}
	
	//process on content items first
	function onContentPrepare($context, &$article, &$params, $page = 0)
	{
		// don't execute if contentPlugin disabled in system config
		$candy_params =JComponentHelper::getParams('com_rokcandy');

		if ($candy_params->get("contentPlugin",1)==0) return;
		
		// don't run if disabled overrides are true
	    if ($this->_shouldProcess()) return;

		if ($this->_replaceCode($article->text)) {
		    return $article->text;
		}
   
    }
	
	function _shouldProcess() {

        $app	= JFactory::getApplication();
	    $params =JComponentHelper::getParams('com_rokcandy');
	    
	    //don't run if in edit mode and flag enabled
	    if (JFactory::getApplication()->input->get('layout') == 'edit' && $params->get('editenabled',0) == 0)
            return true;
	    
	    // don't process if in model view:
        $modal = JFactory::getApplication()->input->get('layout');
        $option = JFactory::getApplication()->input->get('option');
	    if (JFactory::getApplication()->input->get('layout') == 'modal' && JFactory::getApplication()->input->get('option') == "com_rokcandy")
            return true;
	      
	    //don't run in admin
		if ($app->isAdmin() && $params->get('adminenabled',0)==0)
            return true;

	    // process manual overrides
	    $flag = false;
	    $is_disabled = $params->get('disabled');
	    if ($is_disabled != "") {
	        $disabled_entries = explode ("\n",$params->get('disabled'));
	        foreach ($disabled_entries as $entries) {
	            $checks = explode ("&",$entries);
	            if (count($checks) > 0) {
	                $flag = true;
    	            foreach ($checks as $check) {
    	                $bits = explode ("=",$check);
    	                if ((count($bits) == 2) && ($bits[1] != "") && (JFactory::getApplication()->input->get($bits[0]) == $bits[1])) {
    	                    $flag = true;
    	                }
    	                else {
    	                    $flag = false;
    	                    break;
    	                }
    	                
    	            }
                }
                if ($flag == true)
           			return true;
	        }
	    }
	    return $flag;
	}
	
	function _replaceCode(&$body) {

        if(empty($this->_library)) return true;

	    foreach ($this->_library as $key => $val) {
	    	
	    	$script_tag_matches = array();
	    	$search         = array();
			$replace        = array();
			$tokens         = array();
			 
	    	// create a working body 
	        $working_body = $body;
	            
	        // remove the script tag contents from the working body
			$find_scipt_tag = '#(<script.*type="text/javascript"[^>]*>(?!<script)(.*)</script>)#iUs';
			preg_match_all  (  $find_scipt_tag  ,  $working_body  ,  $script_tag_matches);
			foreach($script_tag_matches[2] as $scripttagbody) {
				if(!empty($scripttagbody)){ 
					$working_body = str_replace($scripttagbody,'',$working_body);
				}	
			}
		
			
        	// build the regexp for the tag    
			$opentag = substr($key,0,strpos($key,']')+1);
			$partial_open_tag = substr($opentag,0,(strpos($opentag,' '))?strpos($opentag,' '):strpos($opentag,']'));
			$tokened_opentag =  preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>.*?)',$opentag);
            if (strpos($opentag,"/]")){
                $escaped_key = $this->_addEscapes($tokened_opentag);
            }
            else {
                $tag_contents = substr($key, strpos($key,']')+1, strrpos($key,'[') - (strpos($key,']')+1));
			    $tokened_tag_contens = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>(?s:(?!'.$partial_open_tag.').)*?)',$tag_contents);
			    $closetag = substr($key,strrpos($key,'['),strrpos($key,']')-strrpos($key,'[')+1);
			    $escaped_key = $this->_addEscapes($tokened_opentag.$tokened_tag_contens.$closetag);
            }
			$final_tag_patern = "%".$escaped_key."%";
	        
	        // run the matching for the tag on the working body
	        if ($this->_debug) var_dump ($final_tag_patern);
	        preg_match_all($final_tag_patern, $working_body, $results);
	        if (!empty($results[0])) {
	            if ($this->_debug) var_dump ($results);
    	        $search = array_merge($search, $results[0]);
    	        foreach ($results as $k => $v) {
    	            if (!is_numeric($k)) {
    	                $tokens[] = $k;
    	            }
    	        }
                for($i=0;$i< count($results[0]);$i++) {
                    $tmpval = $val;
                    foreach ($tokens as $token) {
                        $tmpval = str_replace("{".$token."}",$results[$token][$i],$tmpval);
                    }
                    $replace[] = $tmpval;
                }
	        }
	        // do actual replacement on the real body
	        $body = str_replace($search,$replace,$body);
	    }
        
        return true;
	}
	
	function _addEscapes($fullstring) {
		$fullstring            = str_replace("\\","\\\\",$fullstring);
		$fullstring            = str_replace("[","\[",$fullstring);
		$fullstring            = str_replace("]","\\]",$fullstring);
		return $fullstring;
	}
	    
    
    
    
    function _readIniFile($path, $library) {
        jimport( 'joomla.filesystem.file' );
        $content = JFile::read($path);
        $data = explode("\n",$content);

		foreach ($data as $line) {
		    //skip comments
		    if (strpos($line,"#")!==0 and trim($line)!="" ) {
		       $div = strpos($line,"]=");
		       $library[substr($line,0,$div+1)] = substr($line,$div+2);
		    }
		}
		return $library;
    }

}