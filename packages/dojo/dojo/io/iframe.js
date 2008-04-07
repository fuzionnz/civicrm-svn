/*
	Copyright (c) 2004-2008, The Dojo Foundation
	All Rights Reserved.

	Licensed under the Academic Free License version 2.1 or above OR the
	modified BSD license. For more information on Dojo licensing, see:

		http://dojotoolkit.org/book/dojo-book-0-9/introduction/licensing
*/


if(!dojo._hasResource["dojo.io.iframe"]){
dojo._hasResource["dojo.io.iframe"]=true;
dojo.provide("dojo.io.iframe");
dojo.io.iframe={create:function(_1,_2,_3){
if(window[_1]){
return window[_1];
}
if(window.frames[_1]){
return window.frames[_1];
}
var _4=null;
var _5=_3;
if(!_5){
if(dojo.config["useXDomain"]&&!dojo.config["dojoBlankHtmlUrl"]){
console.debug("dojo.io.iframe.create: When using cross-domain Dojo builds,"+" please save dojo/resources/blank.html to your domain and set djConfig.dojoBlankHtmlUrl"+" to the path on your domain to blank.html");
}
_5=(dojo.config["dojoBlankHtmlUrl"]||dojo.moduleUrl("dojo","resources/blank.html"));
}
var _6=dojo.isIE?"<iframe name=\""+_1+"\" src=\""+_5+"\" onload=\""+_2+"\">":"iframe";
_4=dojo.doc.createElement(_6);
with(_4){
name=_1;
setAttribute("name",_1);
id=_1;
}
dojo.body().appendChild(_4);
window[_1]=_4;
with(_4.style){
if(dojo.isSafari<3){
position="absolute";
}
left=top="1px";
height=width="1px";
visibility="hidden";
}
if(!dojo.isIE){
this.setSrc(_4,_5,true);
_4.onload=new Function(_2);
}
return _4;
},setSrc:function(_7,_8,_9){
try{
if(!_9){
if(dojo.isSafari){
_7.location=_8;
}else{
frames[_7.name].location=_8;
}
}else{
var _a;
if(dojo.isIE||dojo.isSafari>2){
_a=_7.contentWindow.document;
}else{
if(dojo.isSafari){
_a=_7.document;
}else{
_a=_7.contentWindow;
}
}
if(!_a){
_7.location=_8;
return;
}else{
_a.location.replace(_8);
}
}
}
catch(e){
console.debug("dojo.io.iframe.setSrc: ",e);
}
},doc:function(_b){
var _c=_b.contentDocument||(((_b.name)&&(_b.document)&&(document.getElementsByTagName("iframe")[_b.name].contentWindow)&&(document.getElementsByTagName("iframe")[_b.name].contentWindow.document)))||((_b.name)&&(document.frames[_b.name])&&(document.frames[_b.name].document))||null;
return _c;
},send:function(_d){
if(!this["_frame"]){
this._frame=this.create(this._iframeName,dojo._scopeName+".io.iframe._iframeOnload();");
}
var _e=dojo._ioSetArgs(_d,function(_f){
_f.canceled=true;
_f.ioArgs._callNext();
},function(dfd){
var _11=null;
try{
var _12=dfd.ioArgs;
var dii=dojo.io.iframe;
var ifd=dii.doc(dii._frame);
var _15=_12.handleAs;
_11=ifd;
if(_15!="html"){
_11=ifd.getElementsByTagName("textarea")[0].value;
if(_15=="json"){
_11=dojo.fromJson(_11);
}else{
if(_15=="javascript"){
_11=dojo.eval(_11);
}
}
}
}
catch(e){
_11=e;
}
finally{
_12._callNext();
}
return _11;
},function(_16,dfd){
dfd.ioArgs._hasError=true;
dfd.ioArgs._callNext();
return _16;
});
_e.ioArgs._callNext=function(){
if(!this["_calledNext"]){
this._calledNext=true;
dojo.io.iframe._currentDfd=null;
dojo.io.iframe._fireNextRequest();
}
};
this._dfdQueue.push(_e);
this._fireNextRequest();
dojo._ioWatch(_e,function(dfd){
return !dfd.ioArgs["_hasError"];
},function(dfd){
return (!!dfd.ioArgs["_finished"]);
},function(dfd){
if(dfd.ioArgs._finished){
dfd.callback(dfd);
}else{
dfd.errback(new Error("Invalid dojo.io.iframe request state"));
}
});
return _e;
},_currentDfd:null,_dfdQueue:[],_iframeName:dojo._scopeName+"IoIframe",_fireNextRequest:function(){
try{
if((this._currentDfd)||(this._dfdQueue.length==0)){
return;
}
var dfd=this._currentDfd=this._dfdQueue.shift();
var _1c=dfd.ioArgs;
var _1d=_1c.args;
_1c._contentToClean=[];
var fn=dojo.byId(_1d["form"]);
var _1f=_1d["content"]||{};
if(fn){
if(_1f){
for(var x in _1f){
if(!fn[x]){
var tn;
if(dojo.isIE){
tn=dojo.doc.createElement("<input type='hidden' name='"+x+"'>");
}else{
tn=dojo.doc.createElement("input");
tn.type="hidden";
tn.name=x;
}
tn.value=_1f[x];
fn.appendChild(tn);
_1c._contentToClean.push(x);
}else{
fn[x].value=_1f[x];
}
}
}
var _22=fn.getAttributeNode("action");
var _23=fn.getAttributeNode("method");
var _24=fn.getAttributeNode("target");
if(_1d["url"]){
_1c._originalAction=_22?_22.value:null;
if(_22){
_22.value=_1d.url;
}else{
fn.setAttribute("action",_1d.url);
}
}
if(!_23||!_23.value){
if(_23){
_23.value=(_1d["method"])?_1d["method"]:"post";
}else{
fn.setAttribute("method",(_1d["method"])?_1d["method"]:"post");
}
}
_1c._originalTarget=_24?_24.value:null;
if(_24){
_24.value=this._iframeName;
}else{
fn.setAttribute("target",this._iframeName);
}
fn.target=this._iframeName;
fn.submit();
}else{
var _25=_1d.url+(_1d.url.indexOf("?")>-1?"&":"?")+_1c.query;
this.setSrc(this._frame,_25,true);
}
}
catch(e){
dfd.errback(e);
}
},_iframeOnload:function(){
var dfd=this._currentDfd;
if(!dfd){
this._fireNextRequest();
return;
}
var _27=dfd.ioArgs;
var _28=_27.args;
var _29=dojo.byId(_28.form);
if(_29){
var _2a=_27._contentToClean;
for(var i=0;i<_2a.length;i++){
var key=_2a[i];
if(dojo.isSafari<3){
for(var j=0;j<_29.childNodes.length;j++){
var _2e=_29.childNodes[j];
if(_2e.name==key){
dojo._destroyElement(_2e);
break;
}
}
}else{
dojo._destroyElement(_29[key]);
_29[key]=null;
}
}
if(_27["_originalAction"]){
_29.setAttribute("action",_27._originalAction);
}
if(_27["_originalTarget"]){
_29.setAttribute("target",_27._originalTarget);
_29.target=_27._originalTarget;
}
}
_27._finished=true;
}};
}
