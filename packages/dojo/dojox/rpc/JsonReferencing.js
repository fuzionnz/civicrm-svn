/*
	Copyright (c) 2004-2008, The Dojo Foundation
	All Rights Reserved.

	Licensed under the Academic Free License version 2.1 or above OR the
	modified BSD license. For more information on Dojo licensing, see:

		http://dojotoolkit.org/book/dojo-book-0-9/introduction/licensing
*/


if(!dojo._hasResource["dojox.rpc.JsonReferencing"]){
dojo._hasResource["dojox.rpc.JsonReferencing"]=true;
dojo.provide("dojox.rpc.JsonReferencing");
dojo.require("dojo.date.stamp");
dojo.require("dojo._base.Deferred");
dojox.rpc._index={};
dojox.rpc.onUpdate=function(_1,_2,_3,_4){
};
dojox.rpc.resolveJson=function(_5,_6){
var _7,_8=[];
function makeIdInfo(_9){
if(_9){
var _a;
if(!(_a=_9._idAttr)){
for(var i in _9.properties){
if(_9.properties[i].unique){
_9._idAttr=_a=i;
}
}
}
if(_a||_9._idPrefix){
return {attr:_a||"id",prefix:_9._idPrefix};
}
}
return false;
};
function walk(it,_d,_e,_f,_10){
var val,i;
var id=it[_f.attr];
id=(id&&(_f.prefix+id))||_10;
var _14=it;
if(id){
it._id=id;
if(dojox.rpc._index[id]){
_14=dojox.rpc._index[id];
delete _14.$ref;
}
dojox.rpc._index[id]=_14;
if(_e&&dojox.validate&&dojox.validate.jsonSchema){
dojox.validate.jsonSchema._schemas[id]=_e;
}
}
for(i in it){
if(it.hasOwnProperty(i)&&(typeof (val=it[i])=="object")&&val){
_7=val.$ref;
if(_7){
var _15=_7.replace(/\\./g,"@").replace(/"[^"\\\n\r]*"/g,"");
if(/[\w\[\]\.\$ \/\r\n\t]/.test(_15)&&!/=|((^|\W)new\W)/.test(_15)){
var _16=_7.match(/(^\.*[^\.\[]+)([\.\[].*)?/);
if((_7=_16[1]=="$"?_5:dojox.rpc._index[new dojo._Url(_f.prefix,_16[1])])&&(_7=_16[2]?eval("ref"+_16[2]):_7)){
val=_7;
}else{
if(!_d){
if(!_17){
_8.push(it);
}
var _17=true;
}else{
_7=val.$ref;
val=new dojo.Deferred();
val._id=_f.prefix+_7;
(function(val,ref){
var _1a=dojo.connect(val,"addCallbacks",function(){
dojo.disconnect(_1a);
dojox.rpc.services[_f.prefix.substring(0,_f.prefix.length-1)](ref).addCallback(dojo.hitch(val,val.callback));
});
})(val,_7);
}
}
}
}else{
if(!_d){
var _1b=val.schema||(_e&&_e.properties&&_e.properties[i]);
if(_1b){
_f=makeIdInfo(_1b)||_f;
}
val=walk(val,_8==it,_1b,_f,id&&(id+("["+dojo._escapeString(i)+"]")));
}
}
}
if(dojo.isString(val)&&_e&&_e.properties&&_e.properties[i]&&_e.properties[i].format=="date-time"){
val=dojo.date.stamp.fromISOString(val);
}
it[i]=val;
var old=_14[i];
if(val!==old){
_14[i]=val;
propertyChange(i,old,val);
}
}
function propertyChange(key,old,_1f){
setTimeout(function(){
dojox.rpc.onUpdate(_14,i,old,_1f);
});
};
if(_14!=it){
for(i in _14){
if(!it.hasOwnProperty(i)&&i!="_id"&&!(_14 instanceof Array&&isNaN(i))){
propertyChange(i,_14[i],undefined);
delete _14[i];
}
}
}
return _14;
};
var _20=makeIdInfo(_6)||{attr:"id",prefix:""};
if(!_5){
return _5;
}
_5=walk(_5,false,_6,_20,dojox._newId&&(new dojo._Url(_20.prefix,dojox._newId)+""));
walk(_8,false,_6,_20);
return _5;
};
dojox.rpc.fromJson=function(str,_22){
root=eval("("+str+")");
if(root){
return this.resolveJson(root,_22);
}
return root;
};
dojox.rpc.toJson=function(it,_24,_25){
var _26=(_25&&_25._idPrefix)||"";
var _27={};
function serialize(it,_29,_2a){
if(it&&dojo.isObject(it)){
var _2b;
if(it instanceof Date){
return "\""+dojo.date.stamp.toISOString(it,{zulu:true})+"\"";
}
var id=it._id;
if(id){
if(_29!="$"){
return serialize({$ref:id.charAt(0)=="$"?id:id.substring(0,_26.length)==_26?id.substring(_26.length):"../"+id});
}
_29=id;
}else{
it._id=_29;
_27[_29]=it;
}
_2a=_2a||"";
var _2d=_24?_2a+dojo.toJsonIndentStr:"";
var _2e=_24?"\n":"";
var sep=_24?" ":"";
if(it instanceof Array){
var res=dojo.map(it,function(obj,i){
var val=serialize(obj,_29+"["+i+"]",_2d);
if(!dojo.isString(val)){
val="undefined";
}
return _2e+_2d+val;
});
return "["+res.join(","+sep)+_2e+_2a+"]";
}
var _34=[];
for(var i in it){
var _36;
if(typeof i=="number"){
_36="\""+i+"\"";
}else{
if(dojo.isString(i)&&i!="_id"){
_36=dojo._escapeString(i);
}else{
continue;
}
}
var val=serialize(it[i],_29+(i.match(/^[a-zA-Z]\w*$/)?("."+i):("["+dojo._escapeString(i)+"]")),_2d);
if(!dojo.isString(val)){
continue;
}
_34.push(_2e+_2d+_36+":"+sep+val);
}
return "{"+_34.join(","+sep)+_2e+_2a+"}";
}
return dojo.toJson(it);
};
var _38=serialize(it,"$","");
for(i in _27){
delete _27[i]._id;
}
return _38;
};
}
