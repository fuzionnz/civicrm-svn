/*
	Copyright (c) 2004-2008, The Dojo Foundation
	All Rights Reserved.

	Licensed under the Academic Free License version 2.1 or above OR the
	modified BSD license. For more information on Dojo licensing, see:

		http://dojotoolkit.org/book/dojo-book-0-9/introduction/licensing
*/


if(!dojo._hasResource["dojox.data.FlickrStore"]){
dojo._hasResource["dojox.data.FlickrStore"]=true;
dojo.provide("dojox.data.FlickrStore");
dojo.require("dojo.data.util.simpleFetch");
dojo.require("dojo.io.script");
dojo.require("dojo.date.stamp");
dojo.declare("dojox.data.FlickrStore",null,{constructor:function(_1){
if(_1&&_1.label){
this.label=_1.label;
}
},_flickrUrl:"http://api.flickr.com/services/feeds/photos_public.gne",_storeRef:"_S",label:"title",_assertIsItem:function(_2){
if(!this.isItem(_2)){
throw new Error("dojox.data.FlickrStore: a function was passed an item argument that was not an item");
}
},_assertIsAttribute:function(_3){
if(typeof _3!=="string"){
throw new Error("dojox.data.FlickrStore: a function was passed an attribute argument that was not an attribute name string");
}
},getFeatures:function(){
return {"dojo.data.api.Read":true};
},getValue:function(_4,_5){
var _6=this.getValues(_4,_5);
if(_6){
return _6[0];
}
return undefined;
},getAttributes:function(_7){
return ["title","description","author","datePublished","dateTaken","imageUrl","imageUrlSmall","imageUrlMedium","tags","link"];
},hasAttribute:function(_8,_9){
if(this.getValue(_8,_9)){
return true;
}
return false;
},isItemLoaded:function(_a){
return this.isItem(_a);
},loadItem:function(_b){
},getLabel:function(_c){
return this.getValue(_c,this.label);
},getLabelAttributes:function(_d){
return [this.label];
},containsValue:function(_e,_f,_10){
var _11=this.getValues(_e,_f);
for(var i=0;i<_11.length;i++){
if(_11[i]===_10){
return true;
}
}
return false;
},getValues:function(_13,_14){
this._assertIsItem(_13);
this._assertIsAttribute(_14);
if(_14==="title"){
return [this._unescapeHtml(_13.title)];
}else{
if(_14==="author"){
return [this._unescapeHtml(_13.author)];
}else{
if(_14==="datePublished"){
return [dojo.date.stamp.fromISOString(_13.published)];
}else{
if(_14==="dateTaken"){
return [dojo.date.stamp.fromISOString(_13.date_taken)];
}else{
if(_14==="imageUrlSmall"){
return [_13.media.m.replace(/_m\./,"_s.")];
}else{
if(_14==="imageUrl"){
return [_13.media.m.replace(/_m\./,".")];
}else{
if(_14==="imageUrlMedium"){
return [_13.media.m];
}else{
if(_14==="link"){
return [_13.link];
}else{
if(_14==="tags"){
return _13.tags.split(" ");
}else{
if(_14==="description"){
return [this._unescapeHtml(_13.description)];
}
}
}
}
}
}
}
}
}
}
return undefined;
},isItem:function(_15){
if(_15&&_15[this._storeRef]===this){
return true;
}
return false;
},close:function(_16){
},_fetchItems:function(_17,_18,_19){
if(!_17.query){
_17.query={};
}
var _1a={format:"json",tagmode:"any"};
if(_17.query.tags){
_1a.tags=_17.query.tags;
}
if(_17.query.tagmode){
_1a.tagmode=_17.query.tagmode;
}
if(_17.query.userid){
_1a.id=_17.query.userid;
}
if(_17.query.userids){
_1a.ids=_17.query.userids;
}
if(_17.query.lang){
_1a.lang=_17.query.lang;
}
var _1b=this;
var _1c=null;
var _1d={url:this._flickrUrl,preventCache:true,content:_1a};
var _1e=function(_1f){
if(_1c!==null){
dojo.disconnect(_1c);
}
_18(_1b._processFlickrData(_1f),_17);
};
_1c=dojo.connect("jsonFlickrFeed",_1e);
var _20=dojo.io.script.get(_1d);
_20.addErrback(function(_21){
dojo.disconnect(_1c);
_19(_21,_17);
});
},_processFlickrData:function(_22){
var _23=[];
if(_22.items){
_23=_22.items;
for(var i=0;i<_22.items.length;i++){
var _25=_22.items[i];
_25[this._storeRef]=this;
}
}
return _23;
},_unescapeHtml:function(str){
str=str.replace(/&amp;/gm,"&").replace(/&lt;/gm,"<").replace(/&gt;/gm,">").replace(/&quot;/gm,"\"");
str=str.replace(/&#39;/gm,"'");
return str;
}});
dojo.extend(dojox.data.FlickrStore,dojo.data.util.simpleFetch);
if(!jsonFlickrFeed){
var jsonFlickrFeed=function(_27){
};
}
}
