/**
   Copyright (c) 2004-2008, The Dojo Foundation
   All Rights Reserved.
   
   Licensed under the Academic Free License version 2.1 or above OR the
   modified BSD license. For more information on Dojo licensing, see:
   
   http://dojotoolkit.org/book/dojo-book-0-9/introduction/licensing

   Custom modified TitlePane for CiviCRM

*/


if(!dojo._hasResource["civicrm.TitlePane"]){
dojo._hasResource["civicrm.TitlePane"]=true;
dojo.provide("civicrm.TitlePane");
dojo.require("dojo.fx");
dojo.require("dijit._Templated");
dojo.require("dojox.layout.ContentPane");
dojo.declare("civicrm.TitlePane",[dojox.layout.ContentPane,dijit._Templated],{title:"",open:true,duration:250,baseClass:"dijitTitlePane",templateString:"<div class=\"dijitTitlePane\">\n\t<div dojoAttachEvent=\"onclick:toggle,onkeypress: _onTitleKey,onfocus:_handleFocus,onblur:_handleFocus\" tabindex=\"0\"\n\t\t\twaiRole=\"button\" class=\"dijitTitlePaneTitle\" dojoAttachPoint=\"focusNode\">\n\t\t<div dojoAttachPoint=\"arrowNode\" class=\"dijitInline dijitArrowNode\"><span dojoAttachPoint=\"arrowNodeInner\" class=\"dijitArrowNodeInner\"></span></div>\n\t\t<div dojoAttachPoint=\"titleNode\" class=\"dijitTitlePaneTextNode\"></div>\n\t</div>\n\t<div class=\"dijitTitlePaneContentOuter\" dojoAttachPoint=\"hideNode\">\n\t\t<div class=\"dijitReset\" dojoAttachPoint=\"wipeNode\">\n\t\t\t<div class=\"dijitTitlePaneContentInner\" dojoAttachPoint=\"containerNode\" waiRole=\"region\" tabindex=\"-1\">\n\t\t\t\t<!-- nested divs because wipeIn()/wipeOut() doesn't work right on node w/padding etc.  Put padding on inner div. -->\n\t\t\t</div>\n\t\t</div>\n\t</div>\n</div>\n",postCreate:function(){
this.setTitle(this.title);
if(!this.open){
this.hideNode.style.display=this.wipeNode.style.display="none";
}
this._setCss();
dojo.setSelectable(this.titleNode,false);
this.inherited("postCreate",arguments);
dijit.setWaiState(this.containerNode,"labelledby",this.titleNode.id);
dijit.setWaiState(this.focusNode,"haspopup","true");
var _1=this.hideNode,_2=this.wipeNode;
this._wipeIn=dojo.fx.wipeIn({node:this.wipeNode,duration:this.duration,beforeBegin:function(){
_1.style.display="";
}});
this._wipeOut=dojo.fx.wipeOut({node:this.wipeNode,duration:this.duration,onEnd:function(){
_1.style.display="none";
}});
},setContent:function(_3){
if(this._wipeOut.status()=="playing"){
this.inherited("setContent",arguments);
}else{
if(this._wipeIn.status()=="playing"){
this._wipeIn.stop();
}
dojo.marginBox(this.wipeNode,{h:dojo.marginBox(this.wipeNode).h});
this.inherited("setContent",arguments);
this._wipeIn.play();
}
},toggle:function(){
dojo.forEach([this._wipeIn,this._wipeOut],function(_4){
if(_4.status()=="playing"){
_4.stop();
}
});
this[this.open?"_wipeOut":"_wipeIn"].play();
this.open=!this.open;
this._loadCheck();
this._setCss();
},_setCss:function(){
var _5=["dijitClosed","dijitOpen"];
var _6=this.open;
dojo.removeClass(this.focusNode,_5[!_6+0]);
this.focusNode.className+=" "+_5[_6+0];
this.arrowNodeInner.innerHTML=this.open?"-":"+";
},_onTitleKey:function(e){
if(e.keyCode==dojo.keys.ENTER||e.charCode==dojo.keys.SPACE){
this.toggle();
}else{
if(e.keyCode==dojo.keys.DOWN_ARROW){
if(this.open){
this.containerNode.focus();
e.preventDefault();
}
}
}
},_handleFocus:function(e){
dojo[(e.type=="focus"?"addClass":"removeClass")](this.focusNode,this.baseClass+"Focused");
},setTitle:function(_9){
this.titleNode.innerHTML=_9;
}});
}
