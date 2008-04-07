/*
	Copyright (c) 2004-2008, The Dojo Foundation
	All Rights Reserved.

	Licensed under the Academic Free License version 2.1 or above OR the
	modified BSD license. For more information on Dojo licensing, see:

		http://dojotoolkit.org/book/dojo-book-0-9/introduction/licensing
*/


if(!dojo._hasResource["dojox.data.JsonRestStore"]){
dojo._hasResource["dojox.data.JsonRestStore"]=true;
dojo.provide("dojox.data.JsonRestStore");
dojo.require("dojox.rpc.Rest");
dojo.require("dojox.rpc.JsonReferencing");
dojox.data.ASYNC_MODE=0;
dojox.data.SYNC_MODE=1;
dojo.declare("dojox.data.JsonRestStore",null,{mode:dojox.data.ASYNC_MODE,constructor:function(_1){
this.byId=this.fetchItemByIdentity;
dojo.connect(dojox.rpc,"onUpdate",this,function(_2,_3,_4,_5){
var _6=this.service.serviceName+"/";
if(!_2._id){
console.log("no id on updated object ",_2);
}else{
if(_2._id.substring(0,_6.length)==_6){
this.onSet(_2,_3,_4,_5);
}
}
});
if(_1){
dojo.mixin(this,_1);
}
if(!this.service){
throw Error("A service is required for JsonRestStore");
}
if(!(this.service.contentType+"").match(/application\/json/)){
throw Error("A service must use a contentType of 'application/json' in order to be used in a JsonRestStore");
}
this.idAttribute=(this.service._schema&&this.service._schema._idAttr)||"id";
var _7=["splice","push","pop","unshift","shift","reverse","sort"];
this._arrayModifyingMethods={};
var _8=[];
var _9=this;
for(var i=0;i<_7.length;i++){
(function(_b){
var _c=_8[_b];
_9._arrayModifyingMethods[_b]=function(){
_9._setDirty(this);
return _c.apply(this,arguments);
};
_9._arrayModifyingMethods[_b]._augmented=1;
})(_7[i]);
}
this._deletedItems=[];
this._dirtyItems=[];
},_loadById:function(id,_e){
var _f=id.indexOf("/");
var _10=id.substring(0,_f);
var id=id.substring(_f+1);
(this.service.serviceName==_10?this.service:dojox.rpc.services[_10])(id).addCallback(_e);
},getValue:function(_11,_12,_13){
var _14=_11[_12];
if(_14&&_14.$ref){
dojox.rpc._sync=!_13;
this._loadById((_14&&_14._id)||(_11._id+"."+_12),_13);
delete dojox.rpc._sync;
}else{
if(_13){
_13(_14);
}
}
return _14;
},getValues:function(_15,_16){
var val=this.getValue(_15,_16);
return dojo.isArray(val)?val:[val];
},getAttributes:function(_18){
var res=[];
for(var i in _18){
res.push(i);
}
return res;
},hasAttribute:function(_1b,_1c){
return _1c in _1b;
},containsValue:function(_1d,_1e,_1f){
return getValue(_1d,_1e)==_1f;
},isItem:function(_20){
return !!(dojo.isObject(_20)&&_20._id);
},isItemLoaded:function(_21){
return !_21.$ref;
},loadItem:function(_22){
if(_22.$ref){
dojox.rpc._sync=true;
this._loadById(_22._id);
delete dojox.rpc._sync;
}
return true;
},_walk:function(_23,_24){
var _25=[];
function walk(_26){
if(_26&&typeof _26=="object"&&!_26.__walked){
_26.__walked=true;
_25.push(_26);
for(var i in _26){
if(walk(_26[i])){
_24(_26,i,_26[i]);
}
}
return true;
}
};
walk(_23);
_24({},null,_23);
for(var i=0;i<_25.length;i++){
delete _25[i].__walked;
}
},fetch:function(_29){
if(dojo.isString(_29)){
_2a=_29;
_29={query:_2a,mode:dojox.data.SYNC_MODE};
}
var _2a;
if(!_29||!_29.query){
if(!_29){
var _29={};
}
if(!_29.query){
_29.query="";
_2a=_29.query;
}
}
if(dojo.isObject(_29.query)){
if(_29.query.query){
_2a=_29.query.query;
}else{
_2a=_29.query="";
}
if(_29.query.queryOptions){
_29.queryOptions=_29.query.queryOptions;
}
}else{
_2a=_29.query;
}
if(_29.start||_29.count){
_2a+="["+(_29.start?_29.start:"")+":"+(_29.count?((_29.start||0)+_29.count):"")+"]";
}
var _2b=dojox.rpc._index[this.service.serviceName+"/"+_2a];
if(!_29.mode){
_29.mode=this.mode;
}
var _2c=this;
var _2d;
dojox.rpc._sync=this.mode;
dojox._newId=_2a;
if(_2b&&!("cache" in _29&&!_29.cache)){
_2d=new dojo.Deferred;
_2d.callback(_2b);
}else{
_2d=this.service(_2a);
}
_2d.addCallback(function(_2e){
delete dojox._newId;
if(_29.onBegin){
_29["onBegin"].call(_2c,_2e.length,_29);
}
_2c._walk(_2e,function(obj,i,_31){
if(_31 instanceof Array){
for(var i in _2c._arrayModifyingMethods){
if(!_31[i]._augmented){
_31[i]=_2c._arrayModifyingMethods[i];
}
}
}
});
if(_29.onItem){
for(var i=0;i<_2e.length;i++){
_29["onItem"].call(_2c,_2e[i],_29);
}
}
if(_29.onComplete){
_29["onComplete"].call(_2c,_2e,_29);
}
return _2e;
});
_2d.addErrback(_29.onError);
return _29;
},getFeatures:function(){
return {"dojo.data.api.Read":true,"dojo.data.api.Identity":true,"dojo.data.api.Write":true,"dojo.data.api.Notification":true};
},getLabel:function(_33){
return this.getValue(_33,"label");
},getLabelAttributes:function(_34){
return ["label"];
},sort:function(a,b){
console.log("TODO::implement default sort algo");
},getIdentity:function(_37){
var _38=this.service.serviceName+"/";
if(!_37._id){
_37._id=_38+Math.random().toString(16).substring(2,14)+Math.random().toString(16).substring(2,14);
}
if(_37._id.substring(0,_38.length)!=_38){
throw Error("Identity attribute not found");
}
return _37._id.substring(_38.length);
},getIdentityAttributes:function(_39){
return [this.idAttribute];
},fetchItemByIdentity:function(_3a){
return this.fetch(_3a);
},newItem:function(_3b,_3c){
if(this.service._schema&&this.service._schema.clazz&&_3b.constructor!=this.service._schema.clazz){
_3b=dojo.mixin(new this.service._schema.clazz,_3b);
}
this.getIdentity(_3b);
this._getParent(_3c).push(_3b);
this.onNew(_3b);
return _3b;
},_getParent:function(_3d){
var _3e=(_3d&&_3d.parentId)||this.parentId||"";
var _3f=(_3d&&_3d.parent)||dojox.rpc._index[this.service.serviceName+"/"+_3e]||[];
if(!_3f._id){
_3f._id=this.service.serviceName+"/"+_3e;
this._setDirty(_3f);
}
return _3f;
},deleteItem:function(_40,_41){
if(this.isItem(_40)){
this._deletedItems.push(_40);
}
var _42=this;
this._walk(((_41||this.parentId)&&this._getParent(_41))||dojox.rpc._index,function(obj,i,val){
if(obj[i]===_40){
if(_42.isItem(obj)){
if(isNaN(i)||!obj.splice){
_42.unsetAttribute(obj,i);
delete obj[i];
}else{
obj.splice(i,1);
}
}
}
});
this.onDelete(_40);
},_setDirty:function(_46){
var i;
if(!_46._id){
return;
}
for(i=0;i<this._dirtyItems.length;i++){
if(_46==this._dirtyItems[i].item){
return;
}
}
var old=_46 instanceof Array?[]:{};
for(i in _46){
if(_46.hasOwnProperty(i)){
old[i]=_46[i];
}
}
this._dirtyItems.push({item:_46,old:old});
},setValue:function(_49,_4a,_4b){
var old=_49[_4a];
if(old!=_4b){
this._setDirty(_49);
_49[_4a]=_4b;
this.onSet(_49,_4a,old,_4b);
}
},setValues:function(_4d,_4e,_4f){
if(!dojo.isArray(_4f)){
throw new Error("setValues expects to be passed an Array object as its value");
}
this._setDirty(_4d);
var old=_4d[_4e];
_4d[_4e]=_4f;
this.onSet(_4d,_4e,old,_4f);
},unsetAttribute:function(_51,_52){
this._setDirty(_51);
var old=_51[_52];
delete _51[_52];
this.onSet(_51,_52,old,undefined);
},_commitAppend:function(_54,_55){
return this.service.post(_54,_55);
},save:function(_56){
var _57=[];
var _58=0;
var _59=this;
function finishOne(){
if(!(--_58)){
_59.onSave(_57);
}
};
while(this._dirtyItems.length>0){
var _5a=this._dirtyItems.pop();
var _5b=_5a.item;
var _5c=false;
_58++;
var _5d;
if(_5b instanceof Array&&_5a.old instanceof Array){
_5c=true;
for(var i=0,l=_5a.old.length;i<l;i++){
if(_5b[i]!=_5a.old[i]){
_5c=false;
}
}
if(_5c){
for(;i<_5b.length;i++){
_5d=this._commitAppend(this.getIdentity(_5b),_5b[i]);
_5d.addCallback(finishOne);
}
}
}
if(!_5c){
_5d=this.service.put(this.getIdentity(_5b),_5b);
_5d.addCallback(finishOne);
}
_57.push(_5b);
}
while(this._deletedItems.length>0){
_58++;
this.service["delete"](this.getIdentity(this._deletedItems.pop())).addCallback(finishOne);
}
},revert:function(){
while(this._dirtyItems.length>0){
var i;
var d=this._dirtyItems.pop();
for(i in d.old){
d.item[i]=d.old[i];
}
for(i in d.item){
if(!d.old.hasOwnProperty(i)){
delete d.item[i];
}
}
}
this.onRevert();
},isDirty:function(_62){
for(var i=0,l=this._dirtyItems.length;i<l;i++){
if(this._dirtyItems[i]==_62){
return true;
}
}
},onSet:function(){
},onNew:function(){
},onDelete:function(){
},onSave:function(_65){
},onRevert:function(){
}});
}
