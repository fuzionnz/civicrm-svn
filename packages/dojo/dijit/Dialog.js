/*
	Copyright (c) 2004-2008, The Dojo Foundation
	All Rights Reserved.

	Licensed under the Academic Free License version 2.1 or above OR the
	modified BSD license. For more information on Dojo licensing, see:

		http://dojotoolkit.org/book/dojo-book-0-9/introduction/licensing
*/


if(!dojo._hasResource["dijit.Dialog"]){
dojo._hasResource["dijit.Dialog"]=true;
dojo.provide("dijit.Dialog");
dojo.require("dojo.dnd.TimedMoveable");
dojo.require("dojo.fx");
dojo.require("dijit._Widget");
dojo.require("dijit._Templated");
dojo.require("dijit.layout.ContentPane");
dojo.require("dijit.form.Form");
dojo.requireLocalization("dijit","common",null,"zh-tw,ROOT,pt,zh,de,ru,hu,sv,cs,gr,es,fr,ko,it,ja,pl");
dojo.declare("dijit.DialogUnderlay",[dijit._Widget,dijit._Templated],{templateString:"<div class='dijitDialogUnderlayWrapper' id='${id}_wrapper'><div class='dijitDialogUnderlay ${class}' id='${id}' dojoAttachPoint='node'></div></div>",attributeMap:{},postCreate:function(){
dojo.body().appendChild(this.domNode);
this.bgIframe=new dijit.BackgroundIframe(this.domNode);
},layout:function(){
var _1=dijit.getViewport();
var is=this.node.style,os=this.domNode.style;
os.top=_1.t+"px";
os.left=_1.l+"px";
is.width=_1.w+"px";
is.height=_1.h+"px";
var _4=dijit.getViewport();
if(_1.w!=_4.w){
is.width=_4.w+"px";
}
if(_1.h!=_4.h){
is.height=_4.h+"px";
}
},show:function(){
this.domNode.style.display="block";
this.layout();
if(this.bgIframe.iframe){
this.bgIframe.iframe.style.display="block";
}
this._resizeHandler=this.connect(window,"onresize","layout");
},hide:function(){
this.domNode.style.display="none";
if(this.bgIframe.iframe){
this.bgIframe.iframe.style.display="none";
}
this.disconnect(this._resizeHandler);
},uninitialize:function(){
if(this.bgIframe){
this.bgIframe.destroy();
}
}});
dojo.declare("dijit._DialogMixin",null,{attributeMap:dijit._Widget.prototype.attributeMap,execute:function(_5){
},onCancel:function(){
},onExecute:function(){
},_onSubmit:function(){
this.onExecute();
this.execute(this.getValues());
},_getFocusItems:function(_6){
var _7=dijit.getFirstInTabbingOrder(_6);
this._firstFocusItem=_7?_7:_6;
_7=dijit.getLastInTabbingOrder(_6);
this._lastFocusItem=_7?_7:this._firstFocusItem;
if(dojo.isMoz&&this._firstFocusItem.tagName.toLowerCase()=="input"&&dojo.attr(this._firstFocusItem,"type").toLowerCase()=="file"){
dojo.attr(_6,"tabindex","0");
this._firstFocusItem=_6;
}
}});
dojo.declare("dijit.Dialog",[dijit.layout.ContentPane,dijit._Templated,dijit.form._FormMixin,dijit._DialogMixin],{templateString:null,templateString:"<div class=\"dijitDialog\" tabindex=\"-1\" waiRole=\"dialog\" waiState=\"labelledby-${id}_title\">\n\t<div dojoAttachPoint=\"titleBar\" class=\"dijitDialogTitleBar\">\n\t<span dojoAttachPoint=\"titleNode\" class=\"dijitDialogTitle\" id=\"${id}_title\">${title}</span>\n\t<span dojoAttachPoint=\"closeButtonNode\" class=\"dijitDialogCloseIcon\" dojoAttachEvent=\"onclick: onCancel\">\n\t\t<span dojoAttachPoint=\"closeText\" class=\"closeText\">x</span>\n\t</span>\n\t</div>\n\t\t<div dojoAttachPoint=\"containerNode\" class=\"dijitDialogPaneContent\"></div>\n</div>\n",open:false,duration:400,refocus:true,_firstFocusItem:null,_lastFocusItem:null,doLayout:false,attributeMap:dojo.mixin(dojo.clone(dijit._Widget.prototype.attributeMap),{title:"titleBar"}),postCreate:function(){
dojo.body().appendChild(this.domNode);
this.inherited(arguments);
var _8=dojo.i18n.getLocalization("dijit","common");
if(this.closeButtonNode){
this.closeButtonNode.setAttribute("title",_8.buttonCancel);
}
if(this.closeText){
this.closeText.setAttribute("title",_8.buttonCancel);
}
var s=this.domNode.style;
s.visibility="hidden";
s.position="absolute";
s.display="";
s.top="-9999px";
this.connect(this,"onExecute","hide");
this.connect(this,"onCancel","hide");
this._modalconnects=[];
},onLoad:function(){
this._position();
this.inherited(arguments);
},_setup:function(){
if(this.titleBar){
this._moveable=new dojo.dnd.TimedMoveable(this.domNode,{handle:this.titleBar,timeout:0});
}
this._underlay=new dijit.DialogUnderlay({id:this.id+"_underlay","class":dojo.map(this["class"].split(/\s/),function(s){
return s+"_underlay";
}).join(" ")});
var _b=this.domNode;
this._fadeIn=dojo.fx.combine([dojo.fadeIn({node:_b,duration:this.duration}),dojo.fadeIn({node:this._underlay.domNode,duration:this.duration,onBegin:dojo.hitch(this._underlay,"show")})]);
this._fadeOut=dojo.fx.combine([dojo.fadeOut({node:_b,duration:this.duration,onEnd:function(){
_b.style.visibility="hidden";
_b.style.top="-9999px";
}}),dojo.fadeOut({node:this._underlay.domNode,duration:this.duration,onEnd:dojo.hitch(this._underlay,"hide")})]);
},uninitialize:function(){
if(this._fadeIn&&this._fadeIn.status()=="playing"){
this._fadeIn.stop();
}
if(this._fadeOut&&this._fadeOut.status()=="playing"){
this._fadeOut.stop();
}
if(this._underlay){
this._underlay.destroy();
}
},_position:function(){
if(dojo.hasClass(dojo.body(),"dojoMove")){
return;
}
var _c=dijit.getViewport();
var mb=dojo.marginBox(this.domNode);
var _e=this.domNode.style;
_e.left=Math.floor((_c.l+(_c.w-mb.w)/2))+"px";
_e.top=Math.floor((_c.t+(_c.h-mb.h)/2))+"px";
},_onKey:function(_f){
if(_f.keyCode){
var _10=_f.target;
if(_f.keyCode==dojo.keys.TAB){
this._getFocusItems(this.domNode);
}
var _11=(this._firstFocusItem==this._lastFocusItem);
if(_10==this._firstFocusItem&&_f.shiftKey&&_f.keyCode==dojo.keys.TAB){
if(!_11){
dijit.focus(this._lastFocusItem);
}
dojo.stopEvent(_f);
}else{
if(_10==this._lastFocusItem&&_f.keyCode==dojo.keys.TAB&&!_f.shiftKey){
if(!_11){
dijit.focus(this._firstFocusItem);
}
dojo.stopEvent(_f);
}else{
while(_10){
if(_10==this.domNode){
if(_f.keyCode==dojo.keys.ESCAPE){
this.hide();
}else{
return;
}
}
_10=_10.parentNode;
}
if(_f.keyCode!=dojo.keys.TAB){
dojo.stopEvent(_f);
}else{
if(!dojo.isOpera){
try{
this._firstFocusItem.focus();
}
catch(e){
}
}
}
}
}
}
},show:function(){
if(this.open){
return;
}
if(!this._alreadyInitialized){
this._setup();
this._alreadyInitialized=true;
}
if(this._fadeOut.status()=="playing"){
this._fadeOut.stop();
}
this._modalconnects.push(dojo.connect(window,"onscroll",this,"layout"));
this._modalconnects.push(dojo.connect(dojo.doc.documentElement,"onkeypress",this,"_onKey"));
dojo.style(this.domNode,"opacity",0);
this.domNode.style.visibility="";
this.open=true;
this._loadCheck();
this._position();
this._fadeIn.play();
this._savedFocus=dijit.getFocus(this);
this._getFocusItems(this.domNode);
setTimeout(dojo.hitch(this,function(){
dijit.focus(this._firstFocusItem);
}),50);
},hide:function(){
if(!this._alreadyInitialized){
return;
}
if(this._fadeIn.status()=="playing"){
this._fadeIn.stop();
}
this._fadeOut.play();
if(this._scrollConnected){
this._scrollConnected=false;
}
dojo.forEach(this._modalconnects,dojo.disconnect);
this._modalconnects=[];
if(this.refocus){
this.connect(this._fadeOut,"onEnd",dojo.hitch(dijit,"focus",this._savedFocus));
}
this.open=false;
},layout:function(){
if(this.domNode.style.visibility!="hidden"){
this._underlay.layout();
this._position();
}
},destroy:function(){
dojo.forEach(this._modalconnects,dojo.disconnect);
if(this.refocus&&this.open){
var fo=this._savedFocus;
setTimeout(dojo.hitch(dijit,"focus",fo),25);
}
this.inherited(arguments);
}});
dojo.declare("dijit.TooltipDialog",[dijit.layout.ContentPane,dijit._Templated,dijit.form._FormMixin,dijit._DialogMixin],{title:"",doLayout:false,_firstFocusItem:null,_lastFocusItem:null,templateString:null,templateString:"<div class=\"dijitTooltipDialog\" waiRole=\"presentation\">\n\t<div class=\"dijitTooltipContainer\" waiRole=\"presentation\">\n\t\t<div class =\"dijitTooltipContents dijitTooltipFocusNode\" dojoAttachPoint=\"containerNode\" tabindex=\"-1\" waiRole=\"dialog\"></div>\n\t</div>\n\t<div class=\"dijitTooltipConnector\" waiRole=\"presenation\"></div>\n</div>\n",postCreate:function(){
this.inherited(arguments);
this.connect(this.containerNode,"onkeypress","_onKey");
this.containerNode.title=this.title;
},orient:function(_13,_14,_15){
this.domNode.className="dijitTooltipDialog "+" dijitTooltipAB"+(_15.charAt(1)=="L"?"Left":"Right")+" dijitTooltip"+(_15.charAt(0)=="T"?"Below":"Above");
},onOpen:function(pos){
this._getFocusItems(this.containerNode);
this.orient(this.domNode,pos.aroundCorner,pos.corner);
this._loadCheck();
dijit.focus(this._firstFocusItem);
},_onKey:function(evt){
var _18=evt.target;
if(evt.keyCode==dojo.keys.TAB){
this._getFocusItems(this.containerNode);
}
var _19=(this._firstFocusItem==this._lastFocusItem);
if(evt.keyCode==dojo.keys.ESCAPE){
this.onCancel();
}else{
if(_18==this._firstFocusItem&&evt.shiftKey&&evt.keyCode==dojo.keys.TAB){
if(!_19){
dijit.focus(this._lastFocusItem);
}
dojo.stopEvent(evt);
}else{
if(_18==this._lastFocusItem&&evt.keyCode==dojo.keys.TAB&&!evt.shiftKey){
if(!_19){
dijit.focus(this._firstFocusItem);
}
dojo.stopEvent(evt);
}else{
if(evt.keyCode==dojo.keys.TAB){
evt.stopPropagation();
}
}
}
}
}});
}
