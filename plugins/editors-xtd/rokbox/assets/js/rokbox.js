/*
 * @author RocketTheme, LLC http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2014 RocketTheme, LLC
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
((function(){var b=new Class({initialize:function(){this.attach();this.pickers();this.switchBlock(document.getElement("[data-switcher]:checked"));},attach:function(){var h=document.retrieve("rokbox:insertNew",function(j,i){if(j){j.preventDefault();
}if(this.insert()){this.reset();}}.bind(this)),g=document.retrieve("rokbox:insertClose",function(j,i){if(j){j.preventDefault();}if(this.insert()){this.close();
}}.bind(this)),f=document.retrieve("rokbox:cancel",function(j,i){if(j){j.preventDefault();}this.close();}.bind(this)),e=document.retrieve("rokbox:switcher",function(j,i){this.switchBlock(i.get("id"));
}.bind(this)),c=document.retrieve("rokbox:elementCheck",function(j,i){var k=i.get("value");document.getElement("#link").set("disabled",(k.length?"disabled":null)).set("value",(k.length?"#":""))[k.length?"addClass":"removeClass"]("disabled");
document.getElement(".picker.link").setStyle("display",(k.length?"none":"inline-block"));}.bind(this)),d=document.retrieve("rokbox:modal",function(j,i){if(j){j.preventDefault();
}this.squeezeResize({width:820,height:520});SqueezeBox.fromElement(i,{parse:"rel",onOpen:this.squeezeResize.bind(this,{width:800,height:500}),onClose:this.squeezeResize.bind(this,{width:520,height:430})});
}.bind(this));document.addEvents({"click:relay(#button-insert-new)":h,"click:relay(#button-insert-close)":g,"click:relay(#button-cancel)":f,"click:relay(a.modal-button)":d,"click:relay([data-switcher])":e,"keydown:relay(input#element)":c,"keyup:relay(input#element)":c});
},insert:function(){var k=document.getElements("[data-required]"),h=true,f=false;k.each(function(m){var n=!!m.get("value");h*=n;if(!n&&!f){f=m;}});if(!h){f.focus();
return false;}var i=document.getElement("[name=content]:checked");i=i?i.get("value"):false;var g=!i?"":", [name="+i+"]",c=document.getElements("input:not([class$=text]):not([name=content]):not([type=hidden])"+g),e="",l=[],d,j;
c.each(function(m){d=m.get("name");j=m.get("value");if(d=="link"){l.push('href="'+j+'"');}else{if(i==d){g=j;if(d=="thumbnail"){if(!j){l.push("data-rokbox-generate-thumbnail");
}else{g='<img src="'+j+'" alt="" />';}}}else{j=a(j);if(j){l.push("data-rokbox-"+d+'="'+j+'"');}}}},this);e+="<a data-rokbox "+l.join(" ")+">";e+=this.parseSelection(g,i=="text");
e+="</a>\n";window.parent.jInsertEditorText(e,document.getElement("input[name=editor_id]").get("value"));return true;},reset:function(){var c=document.getElements("input[name=link], input[name=caption], input[name=text], input[name=thumbnail]");
c.set("value","");},close:function(){if(window.parent.SqueezeBox){window.parent.SqueezeBox.close();}if(window.parent.tb_remove){window.parent.tb_remove();
}},parseSelection:function(d,e){if(d||!e){return d;}var c=document.getElement("input[name=editor_id]").get("value");if(window.parent.RokPad){return"{text}";
}else{if(window.parent.CodeMirror){return window.parent.Joomla.editors.instances[c].selection();}else{if(window.parent.tinymce){return window.parent.tinymce.activeEditor.selection.getContent();
}else{return d;}}}},pickers:function(){var d=document.getElements("[data-mediatype]"),e=document.getElements("[data-picker]"),g,f,c;d.forEach(function(j,h){g=j.get("value");
f=j.getElement("option[value="+g+"]");this.setPicker(e[h],g,f.get("rel"));},this);},setPicker:function(e,d,c){e.set("href",d).set("rel",c);},switchBlock:function(d){if(typeOf(d)=="element"){d=d.get("value");
}var c=document.getElements("[data-switcher]").get("id");c="."+c.join("_text, .")+"_text";document.getElements(c).setStyle("display","none");document.getElements("."+d+"_text").setStyle("display","inline-block");
},squeezeResize:function(c){window.parent.SqueezeBox.asset.setStyles(c);window.parent.SqueezeBox.resize({x:c.width,y:c.height},true);}});window.addEvent("domready",function(){new b();
});window.jInsertEditorText=function(d,c){if(d.contains("<img")){d=d.match(/src="(.*?[^])"/)[1];}document.getElement("#"+c).set("value",d);};function a(c){var d=new Element("div",{text:c}).get("html");
return d.replace(/&amp;quot;|"/g,"&quot;");}})());