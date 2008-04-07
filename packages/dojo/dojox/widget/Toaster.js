/*
	Copyright (c) 2004-2008, The Dojo Foundation
	All Rights Reserved.

	Licensed under the Academic Free License version 2.1 or above OR the
	modified BSD license. For more information on Dojo licensing, see:

		http://dojotoolkit.org/book/dojo-book-0-9/introduction/licensing
*/


if(!dojo._hasResource["dojox.widget.Toaster"]){
dojo._hasResource["dojox.widget.Toaster"]=true;
dojo.provide("dojox.widget.Toaster");
dojo.require("dojo.fx");
dojo.require("dijit._Widget");
dojo.require("dijit._Templated");
dojo.declare("dojox.widget.Toaster",[dijit._Widget,dijit._Templated],{templateString:"<div dojoAttachPoint=\"clipNode\"><div dojoAttachPoint=\"containerNode\" dojoAttachEvent=\"onclick:onSelect\"><div dojoAttachPoint=\"contentNode\"></div></div></div>",messageTopic:"",_uniqueId:0,messageTypes:{MESSAGE:"message",WARNING:"warning",ERROR:"error",FATAL:"fatal"},defaultType:"message",positionDirection:"br-up",positionDirectionTypes:["br-up","br-left","bl-up","bl-right","tr-down","tr-left","tl-down","tl-right"],duration:"2000",separator:"<hr></hr>",postCreate:function(){
this.inherited(arguments);
this.hide();
this.clipNode.className="dijitToasterClip";
this.containerNode.className+=" dijitToasterContainer";
this.contentNode.className="dijitToasterContent";
if(this.messageTopic){
dojo.subscribe(this.messageTopic,this,"_handleMessage");
}
},_handleMessage:function(_1){
if(dojo.isString(_1)){
this.setContent(_1);
}else{
this.setContent(_1.message,_1.type,_1.duration);
}
},_capitalize:function(w){
return w.substring(0,1).toUpperCase()+w.substring(1);
},setContent:function(_3,_4,_5){
_5=_5||this.duration;
if(this.slideAnim){
if(this.slideAnim.status()!="playing"){
this.slideAnim.stop();
}
if(this.slideAnim.status()=="playing"||(this.fadeAnim&&this.fadeAnim.status()=="playing")){
setTimeout(dojo.hitch(this,function(){
this.setContent(_3,_4,_5);
}),50);
return;
}
}
for(var _6 in this.messageTypes){
dojo.removeClass(this.containerNode,"dijitToaster"+this._capitalize(this.messageTypes[_6]));
}
dojo.style(this.containerNode,"opacity",1);
if(_3&&this.isVisible){
_3=this.contentNode.innerHTML+this.separator+_3;
}
this.contentNode.innerHTML=_3;
dojo.addClass(this.containerNode,"dijitToaster"+this._capitalize(_4||this.defaultType));
this.show();
var _7=dojo.marginBox(this.containerNode);
this._cancelHideTimer();
if(this.isVisible){
this._placeClip();
if(!this._stickyMessage){
this._setHideTimer(_5);
}
}else{
var _8=this.containerNode.style;
var pd=this.positionDirection;
if(pd.indexOf("-up")>=0){
_8.left=0+"px";
_8.top=_7.h+10+"px";
}else{
if(pd.indexOf("-left")>=0){
_8.left=_7.w+10+"px";
_8.top=0+"px";
}else{
if(pd.indexOf("-right")>=0){
_8.left=0-_7.w-10+"px";
_8.top=0+"px";
}else{
if(pd.indexOf("-down")>=0){
_8.left=0+"px";
_8.top=0-_7.h-10+"px";
}else{
throw new Error(this.id+".positionDirection is invalid: "+pd);
}
}
}
}
this.slideAnim=dojo.fx.slideTo({node:this.containerNode,top:0,left:0,duration:450});
this.connect(this.slideAnim,"onEnd",function(_a,_b){
this.fadeAnim=dojo.fadeOut({node:this.containerNode,duration:1000});
this.connect(this.fadeAnim,"onEnd",function(_c){
this.isVisible=false;
this.hide();
});
this._setHideTimer(_5);
this.connect(this,"onSelect",function(_d){
this._cancelHideTimer();
this._stickyMessage=false;
this.fadeAnim.play();
});
this.isVisible=true;
});
this.slideAnim.play();
}
},_cancelHideTimer:function(){
if(this._hideTimer){
clearTimeout(this._hideTimer);
this._hideTimer=null;
}
},_setHideTimer:function(_e){
this._cancelHideTimer();
if(_e>0){
this._cancelHideTimer();
this._hideTimer=setTimeout(dojo.hitch(this,function(_f){
if(this.bgIframe&&this.bgIframe.iframe){
this.bgIframe.iframe.style.display="none";
}
this._hideTimer=null;
this._stickyMessage=false;
this.fadeAnim.play();
}),_e);
}else{
this._stickyMessage=true;
}
},_placeClip:function(){
var _10=dijit.getViewport();
var _11=dojo.marginBox(this.containerNode);
var _12=this.clipNode.style;
_12.height=_11.h+"px";
_12.width=_11.w+"px";
var pd=this.positionDirection;
if(pd.match(/^t/)){
_12.top=_10.t+"px";
}else{
if(pd.match(/^b/)){
_12.top=(_10.h-_11.h-2+_10.t)+"px";
}
}
if(pd.match(/^[tb]r-/)){
_12.left=(_10.w-_11.w-1-_10.l)+"px";
}else{
if(pd.match(/^[tb]l-/)){
_12.left=0+"px";
}
}
_12.clip="rect(0px, "+_11.w+"px, "+_11.h+"px, 0px)";
if(dojo.isIE){
if(!this.bgIframe){
this.clipNode.id="__dojoXToaster_"+this._uniqueId++;
this.bgIframe=new dijit.BackgroundIframe(this.clipNode);
}
var _14=this.bgIframe.iframe;
if(_14){
_14.style.display="block";
}
}
},onSelect:function(e){
},show:function(){
dojo.style(this.domNode,"display","block");
this._placeClip();
if(!this._scrollConnected){
this._scrollConnected=dojo.connect(window,"onscroll",this,this._placeClip);
}
},hide:function(){
dojo.style(this.domNode,"display","none");
if(this._scrollConnected){
dojo.disconnect(this._scrollConnected);
this._scrollConnected=false;
}
dojo.style(this.containerNode,"opacity",1);
}});
}
