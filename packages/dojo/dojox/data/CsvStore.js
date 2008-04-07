/*
	Copyright (c) 2004-2008, The Dojo Foundation
	All Rights Reserved.

	Licensed under the Academic Free License version 2.1 or above OR the
	modified BSD license. For more information on Dojo licensing, see:

		http://dojotoolkit.org/book/dojo-book-0-9/introduction/licensing
*/


if(!dojo._hasResource["dojox.data.CsvStore"]){
dojo._hasResource["dojox.data.CsvStore"]=true;
dojo.provide("dojox.data.CsvStore");
dojo.require("dojo.data.util.filter");
dojo.require("dojo.data.util.simpleFetch");
dojo.declare("dojox.data.CsvStore",null,{constructor:function(_1){
this._attributes=[];
this._attributeIndexes={};
this._dataArray=[];
this._arrayOfAllItems=[];
this._loadFinished=false;
if(_1.url){
this.url=_1.url;
}
this._csvData=_1.data;
if(_1.label){
this.label=_1.label;
}else{
if(this.label===""){
this.label=undefined;
}
}
this._storeProp="_csvStore";
this._idProp="_csvId";
this._features={"dojo.data.api.Read":true,"dojo.data.api.Identity":true};
this._loadInProgress=false;
this._queuedFetches=[];
},url:"",label:"",_assertIsItem:function(_2){
if(!this.isItem(_2)){
throw new Error("dojox.data.CsvStore: a function was passed an item argument that was not an item");
}
},_assertIsAttribute:function(_3){
if(!dojo.isString(_3)){
throw new Error("dojox.data.CsvStore: a function was passed an attribute argument that was not an attribute object nor an attribute name string");
}
},getValue:function(_4,_5,_6){
this._assertIsItem(_4);
this._assertIsAttribute(_5);
var _7=_6;
if(this.hasAttribute(_4,_5)){
var _8=this._dataArray[this.getIdentity(_4)];
_7=_8[this._attributeIndexes[_5]];
}
return _7;
},getValues:function(_9,_a){
var _b=this.getValue(_9,_a);
return (_b?[_b]:[]);
},getAttributes:function(_c){
this._assertIsItem(_c);
var _d=[];
var _e=this._dataArray[this.getIdentity(_c)];
for(var i=0;i<_e.length;i++){
if(_e[i]!=""){
_d.push(this._attributes[i]);
}
}
return _d;
},hasAttribute:function(_10,_11){
this._assertIsItem(_10);
this._assertIsAttribute(_11);
var _12=this._attributeIndexes[_11];
var _13=this._dataArray[this.getIdentity(_10)];
return (typeof _12!="undefined"&&_12<_13.length&&_13[_12]!="");
},containsValue:function(_14,_15,_16){
var _17=undefined;
if(typeof _16==="string"){
_17=dojo.data.util.filter.patternToRegExp(_16,false);
}
return this._containsValue(_14,_15,_16,_17);
},_containsValue:function(_18,_19,_1a,_1b){
var _1c=this.getValues(_18,_19);
for(var i=0;i<_1c.length;++i){
var _1e=_1c[i];
if(typeof _1e==="string"&&_1b){
return (_1e.match(_1b)!==null);
}else{
if(_1a===_1e){
return true;
}
}
}
return false;
},isItem:function(_1f){
if(_1f&&_1f[this._storeProp]===this){
var _20=_1f[this._idProp];
if(_20>=0&&_20<this._dataArray.length){
return true;
}
}
return false;
},isItemLoaded:function(_21){
return this.isItem(_21);
},loadItem:function(_22){
},getFeatures:function(){
return this._features;
},getLabel:function(_23){
if(this.label&&this.isItem(_23)){
return this.getValue(_23,this.label);
}
return undefined;
},getLabelAttributes:function(_24){
if(this.label){
return [this.label];
}
return null;
},_fetchItems:function(_25,_26,_27){
var _28=this;
var _29=function(_2a,_2b){
var _2c=null;
if(_2a.query){
_2c=[];
var _2d=_2a.queryOptions?_2a.queryOptions.ignoreCase:false;
var _2e={};
for(var key in _2a.query){
var _30=_2a.query[key];
if(typeof _30==="string"){
_2e[key]=dojo.data.util.filter.patternToRegExp(_30,_2d);
}
}
for(var i=0;i<_2b.length;++i){
var _32=true;
var _33=_2b[i];
for(var key in _2a.query){
var _30=_2a.query[key];
if(!_28._containsValue(_33,key,_30,_2e[key])){
_32=false;
}
}
if(_32){
_2c.push(_33);
}
}
}else{
if(_2b.length>0){
_2c=_2b.slice(0,_2b.length);
}
}
_26(_2c,_2a);
};
if(this._loadFinished){
_29(_25,this._arrayOfAllItems);
}else{
if(this.url!==""){
if(this._loadInProgress){
this._queuedFetches.push({args:_25,filter:_29});
}else{
this._loadInProgress=true;
var _34={url:_28.url,handleAs:"text"};
var _35=dojo.xhrGet(_34);
_35.addCallback(function(_36){
_28._processData(_36);
_29(_25,_28._arrayOfAllItems);
_28._handleQueuedFetches();
});
_35.addErrback(function(_37){
_28._loadInProgress=false;
if(_27){
_27(_37,_25);
}else{
throw _37;
}
});
}
}else{
if(this._csvData){
this._processData(this._csvData);
this._csvData=null;
_29(_25,this._arrayOfAllItems);
}else{
var _38=new Error("dojox.data.CsvStore: No CSV source data was provided as either URL or String data input.");
if(_27){
_27(_38,_25);
}else{
throw _38;
}
}
}
}
},close:function(_39){
},_getArrayOfArraysFromCsvFileContents:function(_3a){
if(dojo.isString(_3a)){
var _3b=new RegExp("\r\n|\n|\r");
var _3c=new RegExp("^\\s+","g");
var _3d=new RegExp("\\s+$","g");
var _3e=new RegExp("\"\"","g");
var _3f=[];
var _40=_3a.split(_3b);
for(var i=0;i<_40.length;++i){
var _42=_40[i];
if(_42.length>0){
var _43=_42.split(",");
var j=0;
while(j<_43.length){
var _45=_43[j];
var _46=_45.replace(_3c,"");
var _47=_46.replace(_3d,"");
var _48=_47.charAt(0);
var _49=_47.charAt(_47.length-1);
var _4a=_47.charAt(_47.length-2);
var _4b=_47.charAt(_47.length-3);
if(_47.length===2&&_47=="\"\""){
_43[j]="";
}else{
if((_48=="\"")&&((_49!="\"")||((_49=="\"")&&(_4a=="\"")&&(_4b!="\"")))){
if(j+1===_43.length){
return null;
}
var _4c=_43[j+1];
_43[j]=_46+","+_4c;
_43.splice(j+1,1);
}else{
if((_48=="\"")&&(_49=="\"")){
_47=_47.slice(1,(_47.length-1));
_47=_47.replace(_3e,"\"");
}
_43[j]=_47;
j+=1;
}
}
}
_3f.push(_43);
}
}
this._attributes=_3f.shift();
for(var i=0;i<this._attributes.length;i++){
this._attributeIndexes[this._attributes[i]]=i;
}
this._dataArray=_3f;
}
},_processData:function(_4d){
this._getArrayOfArraysFromCsvFileContents(_4d);
this._arrayOfAllItems=[];
for(var i=0;i<this._dataArray.length;i++){
this._arrayOfAllItems.push(this._createItemFromIdentity(i));
}
this._loadFinished=true;
this._loadInProgress=false;
},_createItemFromIdentity:function(_4f){
var _50={};
_50[this._storeProp]=this;
_50[this._idProp]=_4f;
return _50;
},getIdentity:function(_51){
if(this.isItem(_51)){
return _51[this._idProp];
}
return null;
},fetchItemByIdentity:function(_52){
if(!this._loadFinished){
var _53=this;
if(this.url!==""){
if(this._loadInProgress){
this._queuedFetches.push({args:_52});
}else{
this._loadInProgress=true;
var _54={url:_53.url,handleAs:"text"};
var _55=dojo.xhrGet(_54);
_55.addCallback(function(_56){
var _57=_52.scope?_52.scope:dojo.global;
try{
_53._processData(_56);
var _58=_53._createItemFromIdentity(_52.identity);
if(!_53.isItem(_58)){
_58=null;
}
if(_52.onItem){
_52.onItem.call(_57,_58);
}
_53._handleQueuedFetches();
}
catch(error){
if(_52.onError){
_52.onError.call(_57,error);
}
}
});
_55.addErrback(function(_59){
this._loadInProgress=false;
if(_52.onError){
var _5a=_52.scope?_52.scope:dojo.global;
_52.onError.call(_5a,_59);
}
});
}
}else{
if(this._csvData){
_53._processData(_53._csvData);
_53._csvData=null;
var _5b=_53._createItemFromIdentity(_52.identity);
if(!_53.isItem(_5b)){
_5b=null;
}
if(_52.onItem){
var _5c=_52.scope?_52.scope:dojo.global;
_52.onItem.call(_5c,_5b);
}
}
}
}else{
var _5b=this._createItemFromIdentity(_52.identity);
if(!this.isItem(_5b)){
_5b=null;
}
if(_52.onItem){
var _5c=_52.scope?_52.scope:dojo.global;
_52.onItem.call(_5c,_5b);
}
}
},getIdentityAttributes:function(_5d){
return null;
},_handleQueuedFetches:function(){
if(this._queuedFetches.length>0){
for(var i=0;i<this._queuedFetches.length;i++){
var _5f=this._queuedFetches[i];
var _60=_5f.filter;
var _61=_5f.args;
if(_60){
_60(_61,this._arrayOfAllItems);
}else{
this.fetchItemByIdentity(_5f.args);
}
}
this._queuedFetches=[];
}
}});
dojo.extend(dojox.data.CsvStore,dojo.data.util.simpleFetch);
}
