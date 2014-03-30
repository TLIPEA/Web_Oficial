/*
 * @author RocketTheme, LLC http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2014 RocketTheme, LLC
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
(function(){var a=this.RokBox={init:function(){a.fixOverflow();},fixOverflow:function(){var c=document.getElement(".rokbox-break"),b;if(c){b=c.getParent(".pane-slider");
}if(b){b.setStyle("overflow","visible");}}};window.addEvent("load",a.init);})();