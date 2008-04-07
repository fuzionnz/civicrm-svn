/*
	Copyright (c) 2004-2008, The Dojo Foundation
	All Rights Reserved.

	Licensed under the Academic Free License version 2.1 or above OR the
	modified BSD license. For more information on Dojo licensing, see:

		http://dojotoolkit.org/book/dojo-book-0-9/introduction/licensing
*/


if(!dojo._hasResource["dojox.av._base.quicktime"]){
dojo._hasResource["dojox.av._base.quicktime"]=true;
dojo.provide("dojox.av._base.quicktime");
(function(){
var _1,_2,_3,_4={width:320,height:240,redirect:null,params:[]};
var _5="dojox-av-quicktime-",_6=0;
var _7=dojo.moduleUrl("dojox","av/resources/version.mov");
function prep(_8){
_8=dojo.mixin(dojo.clone(_4),_8||{});
if(!("path" in _8)){
console.error("dojox.av._base.quicktime(ctor):: no path reference to a QuickTime movie was provided.");
return null;
}
if(!("id" in _8)){
_8.id=(_5+_6++);
}
return _8;
};
var _9="This content requires the <a href=\"http://www.apple.com/quicktime/download/\" title=\"Download and install QuickTime.\">QuickTime plugin</a>.";
if(dojo.isIE){
_3=(function(){
try{
var o=new ActiveXObject("QuickTimeCheckObject.QuickTimeCheck.1");
if(o!==undefined){
return o.IsQuickTimeAvailable(0);
}
}
catch(e){
}
return false;
})();
_1=function(_b){
if(!_3){
return {id:null,markup:_9};
}
_b=prep(_b);
if(!_b){
return null;
}
var s="<object classid=\"clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B\" "+"codebase=\"http://www.apple.com/qtactivex/qtplugin.cab#version=6,0,2,0\" "+"id=\""+_b.id+"\" "+"width=\""+_b.width+"\" "+"height=\""+_b.height+"\">"+"<param name=\"src\" value=\""+_b.path+"\" />";
for(var i=0,l=_b.params.length;i<l;i++){
s+="<param name=\""+_b.params[i].key+"\" value=\""+_b.params[i].value+"\" />";
}
s+="</object>";
return {id:_b.id,markup:s};
};
}else{
_3=(function(){
for(var i=0,l=navigator.plugins.length;i<l;i++){
if(navigator.plugins[i].name.indexOf("QuickTime")>-1){
return true;
}
}
return false;
})();
_1=function(_11){
if(!_3){
return {id:null,markup:_9};
}
_11=prep(_11);
if(!_11){
return null;
}
var s="<embed type=\"video/quicktime\" src=\""+_11.path+"\" "+"id=\""+_11.id+"\" "+"name=\""+_11.id+"\" "+"pluginspage=\"www.apple.com/quicktime/download\" "+"enablejavascript=\"true\" "+"width=\""+_11.width+"\" "+"height=\""+_11.height+"\"";
for(var i=0,l=_11.params.length;i<l;i++){
s+=" "+_11.params[i].key+"=\""+_11.params[i].value+"\"";
}
s+="></embed>";
return {id:_11.id,markup:s};
};
}
_2={major:0,minor:0,rev:0};
dojo.addOnLoad(function(){
var n=document.createElement("div");
n.style.cssText="top:0;left:0;width:1px;height:1px;overflow:hidden;position:absolute;";
var o=_1({path:_7,width:4,height:4});
document.body.appendChild(n);
n.innerHTML=o.markup;
var qt=(dojo.isIE)?dojo.byId(o.id):document[o.id];
setTimeout(function(){
var v=[0,0,0];
if(qt){
try{
v=qt.GetQuickTimeVersion().split(".");
_2={major:parseInt(v[0]||0),minor:parseInt(v[1]||0),rev:parseInt(v[2]||0)};
}
catch(e){
_2={major:0,minor:0,rev:0};
}
}
dojox.av.quicktime.supported=v[0];
dojox.av.quicktime.version=_2;
if(dojox.av.quicktime.supported){
dojox.av.quicktime.onInitialize();
}
if(!dojo.isIE){
document.body.removeChild(n);
}else{
n.style.top="-10000px";
n.style.visibility="hidden";
}
},10);
});
dojox.av.quicktime={minSupported:6,available:_3,supported:_3,version:_2,initialized:false,onInitialize:function(){
dojox.av.quicktime.initialized=true;
},place:function(_19,_1a){
_19=dojo.byId(_19);
var o=_1(_1a);
if(o){
_19.innerHTML=o.markup;
if(o.id){
return (dojo.isIE)?dojo.byId(o.id):document[o.id];
}
}
return null;
}};
})();
}
