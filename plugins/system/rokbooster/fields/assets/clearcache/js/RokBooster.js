/*
 * @version   $Id: RokBooster.js 4588 2012-10-27 02:10:21Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2014 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
(function(){var a=this.RokBooster={init:function(){a.ClearCache();
},ClearCache:function(){var b=document.getElement("[data-action=clearCache]");b.store("ajax",new Request({url:"../plugins/system/rokbooster/ajax.php",onRequest:function(c){a.ClearCacheRequest(this,b,c);
},onSuccess:function(c){a.ClearCacheSuccess(this,b,c);}}));b.addEvent("click",function(d){d.preventDefault();var c=this.retrieve("ajax");if(!c.isRunning()){c.send();
}});},ClearCacheRequest:function(d,c,b){c.addClass("boost-spinner").set("title","");},ClearCacheSuccess:function(d,c,b){c.removeClass("boost-spinner");
b=b.clean();if(!b.length||!JSON.validate(b)){c.getElement(".count").set("text","!");c.set("title","Invalid JSON response: "+b);throw new Error('RokBooster: Invalid JSON response: "'+b+'"');
}else{b=JSON.decode(b);if(b.status=="error"){c.getElement(".count").set("text","!");c.set("title","Unable to purge cache: "+b.message);throw new Error('RokBooster: Error while purging the cache: "'+b.message||'no_message"');
}if(b.status=="success"){c.getElement(".count").set("text",b.message);}}}};window.addEvent("domready",a.init);})();