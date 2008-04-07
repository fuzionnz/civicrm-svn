/*
	Copyright (c) 2004-2008, The Dojo Foundation
	All Rights Reserved.

	Licensed under the Academic Free License version 2.1 or above OR the
	modified BSD license. For more information on Dojo licensing, see:

		http://dojotoolkit.org/book/dojo-book-0-9/introduction/licensing
*/


if(!dojo._hasResource["dojo.io.script"]){
dojo._hasResource["dojo.io.script"]=true;
dojo.provide("dojo.io.script");
dojo.io.script={get:function(_1){
var _2=this._makeScriptDeferred(_1);
var _3=_2.ioArgs;
dojo._ioAddQueryToUrl(_3);
this.attach(_3.id,_3.url,_1.frameDoc);
dojo._ioWatch(_2,this._validCheck,this._ioCheck,this._resHandle);
return _2;
},attach:function(id,_5,_6){
var _7=(_6||dojo.doc);
var _8=_7.createElement("script");
_8.type="text/javascript";
_8.src=_5;
_8.id=id;
_7.getElementsByTagName("head")[0].appendChild(_8);
},remove:function(id){
dojo._destroyElement(dojo.byId(id));
if(this["jsonp_"+id]){
delete this["jsonp_"+id];
}
},_makeScriptDeferred:function(_a){
var _b=dojo._ioSetArgs(_a,this._deferredCancel,this._deferredOk,this._deferredError);
var _c=_b.ioArgs;
_c.id=dojo._scopeName+"IoScript"+(this._counter++);
_c.canDelete=false;
if(_a.callbackParamName){
_c.query=_c.query||"";
if(_c.query.length>0){
_c.query+="&";
}
_c.query+=_a.callbackParamName+"="+(_a.frameDoc?"parent.":"")+"dojo.io.script.jsonp_"+_c.id+"._jsonpCallback";
_c.canDelete=true;
_b._jsonpCallback=this._jsonpCallback;
this["jsonp_"+_c.id]=_b;
}
return _b;
},_deferredCancel:function(_d){
_d.canceled=true;
if(_d.ioArgs.canDelete){
dojo.io.script._deadScripts.push(_d.ioArgs.id);
}
},_deferredOk:function(_e){
if(_e.ioArgs.canDelete){
dojo.io.script._deadScripts.push(_e.ioArgs.id);
}
if(_e.ioArgs.json){
return _e.ioArgs.json;
}else{
return _e.ioArgs;
}
},_deferredError:function(_f,dfd){
if(dfd.ioArgs.canDelete){
if(_f.dojoType=="timeout"){
dojo.io.script.remove(dfd.ioArgs.id);
}else{
dojo.io.script._deadScripts.push(dfd.ioArgs.id);
}
}
console.debug("dojo.io.script error",_f);
return _f;
},_deadScripts:[],_counter:1,_validCheck:function(dfd){
var _12=dojo.io.script;
var _13=_12._deadScripts;
if(_13&&_13.length>0){
for(var i=0;i<_13.length;i++){
_12.remove(_13[i]);
}
dojo.io.script._deadScripts=[];
}
return true;
},_ioCheck:function(dfd){
if(dfd.ioArgs.json){
return true;
}
var _16=dfd.ioArgs.args.checkString;
if(_16&&eval("typeof("+_16+") != 'undefined'")){
return true;
}
return false;
},_resHandle:function(dfd){
if(dojo.io.script._ioCheck(dfd)){
dfd.callback(dfd);
}else{
dfd.errback(new Error("inconceivable dojo.io.script._resHandle error"));
}
},_jsonpCallback:function(_18){
this.ioArgs.json=_18;
}};
}
