/*
	Copyright (c) 2004-2008, The Dojo Foundation
	All Rights Reserved.

	Licensed under the Academic Free License version 2.1 or above OR the
	modified BSD license. For more information on Dojo licensing, see:

		http://dojotoolkit.org/book/dojo-book-0-9/introduction/licensing
*/


if(!dojo._hasResource["dojox.data.FlickrRestStore"]){
dojo._hasResource["dojox.data.FlickrRestStore"]=true;
dojo.provide("dojox.data.FlickrRestStore");
dojo.require("dojox.data.FlickrStore");
dojo.declare("dojox.data.FlickrRestStore",dojox.data.FlickrStore,{constructor:function(_1){
if(_1&&_1.label){
if(_1.label){
this.label=_1.label;
}
if(_1.apikey){
this._apikey=_1.apikey;
}
}
this._cache=[];
this._prevRequests={};
this._handlers={};
this._prevRequestRanges=[];
this._maxPhotosPerUser={};
this._id=dojox.data.FlickrRestStore.prototype._id++;
},_id:0,_requestCount:0,_flickrRestUrl:"http://www.flickr.com/services/rest/",_apikey:null,_storeRef:"_S",_cache:null,_prevRequests:null,_handlers:null,_sortAttributes:{"date-posted":true,"date-taken":true,"interestingness":true},_fetchItems:function(_2,_3,_4){
var _5={};
if(!_2.query){
_2.query=_5={};
}else{
dojo.mixin(_5,_2.query);
}
var _6=[];
var _7=[];
var _8="FlickrRestStoreCallback_"+this._id+"_"+(++this._requestCount);
var _9={format:"json",method:"flickr.photos.search",api_key:this._apikey,extras:"owner_name,date_upload,date_taken",jsoncallback:_8};
var _a=false;
if(_5.userid){
_a=true;
_9.user_id=_2.query.userid;
_6.push("userid"+_2.query.userid);
}
if(_5.apikey){
_a=true;
_9.api_key=_2.query.apikey;
_7.push("api"+_2.query.apikey);
}else{
throw Error("dojox.data.FlickrRestStore: An API key must be specified.");
}
_2._curCount=_2.count;
if(_5.page){
_9.page=_2.query.page;
_7.push("page"+_9.page);
}else{
if(typeof (_2.start)!="undefined"&&_2.start!=null){
if(!_2.count){
_2.count=20;
}
var _b=_2.start%_2.count;
var _c=_2.start,_d=_2.count;
if(_b!=0){
if(_c<_d/2){
_d=_c+_d;
_c=0;
}else{
var _e=20,_f=2;
for(var i=_e;i>0;i--){
if(_c%i==0&&(_c/i)>=_d){
_f=i;
break;
}
}
_d=_c/_f;
}
_2._realStart=_2.start;
_2._realCount=_2.count;
_2._curStart=_c;
_2._curCount=_d;
}else{
_2._realStart=_2._realCount=null;
_2._curStart=_2.start;
_2._curCount=_2.count;
}
_9.page=(_c/_d)+1;
_7.push("page"+_9.page);
}
}
if(_2._curCount){
_9.per_page=_2._curCount;
_7.push("count"+_2._curCount);
}
if(_5.lang){
_9.lang=_2.query.lang;
_6.push("lang"+_2.lang);
}
var url=this._flickrRestUrl;
if(_5.setid){
_9.method="flickr.photosets.getPhotos";
_9.photoset_id=_2.query.set;
_6.push("set"+_2.query.set);
}
if(_5.tags){
if(_5.tags instanceof Array){
_9.tags=_5.tags.join(",");
}else{
_9.tags=_5.tags;
}
_6.push("tags"+_9.tags);
if(_5["tag_mode"]&&(_5.tag_mode.toLowerCase()=="any"||_5.tag_mode.toLowerCase()=="all")){
_9.tag_mode=_5.tag_mode;
}
}
if(_5.text){
_9.text=_5.text;
_6.push("text:"+_5.text);
}
if(_5.sort&&_5.sort.length>0){
if(!_5.sort[0].attribute){
_5.sort[0].attribute="date-posted";
}
if(this._sortAttributes[_5.sort[0].attribute]){
if(_5.sort[0].descending){
_9.sort=_5.sort[0].attribute+"-desc";
}else{
_9.sort=_5.sort[0].attribute+"-asc";
}
}
}else{
_9.sort="date-posted-asc";
}
_6.push("sort:"+_9.sort);
_6=_6.join(".");
_7=_7.length>0?"."+_7.join("."):"";
var _12=_6+_7;
_2={query:_5,count:_2._curCount,start:_2._curStart,_realCount:_2._realCount,_realStart:_2._realStart,onBegin:_2.onBegin,onComplete:_2.onComplete,onItem:_2.onItem};
var _13={request:_2,fetchHandler:_3,errorHandler:_4};
if(this._handlers[_12]){
this._handlers[_12].push(_13);
return;
}
this._handlers[_12]=[_13];
var _14=this;
var _15=null;
var _16={url:this._flickrRestUrl,preventCache:true,content:_9};
var _17=function(_18,_19,_1a){
var _1b=_1a.request.onBegin;
_1a.request.onBegin=null;
var _1c;
var req=_1a.request;
if(typeof (req._realStart)!=undefined&&req._realStart!=null){
req.start=req._realStart;
req.count=req._realCount;
req._realStart=req._realCount=null;
}
if(_1b){
if(_19&&typeof (_19.photos.perpage)!="undefined"&&typeof (_19.photos.pages)!="undefined"){
if(_19.photos.perpage*_19.photos.pages<=_1a.request.start+_1a.request.count){
_1c=_1a.request.start+_19.photos.photo.length;
}else{
_1c=_19.photos.perpage*_19.photos.pages;
}
_14._maxPhotosPerUser[_6]=_1c;
_1b(_1c,_1a.request);
}else{
if(_14._maxPhotosPerUser[_6]){
_1b(_14._maxPhotosPerUser[_6],_1a.request);
}
}
}
_1a.fetchHandler(_18,_1a.request);
if(_1b){
_1a.request.onBegin=_1b;
}
};
var _1e=function(_1f){
if(_1f.stat!="ok"){
_4(null,_2);
}else{
var _20=_14._handlers[_12];
if(!_20){
console.log("FlickrRestStore: no handlers for data",_1f);
return;
}
_14._handlers[_12]=null;
_14._prevRequests[_12]=_1f;
var _21=_14._processFlickrData(_1f,_2,_6);
if(!_14._prevRequestRanges[_6]){
_14._prevRequestRanges[_6]=[];
}
_14._prevRequestRanges[_6].push({start:_2.start,end:_2.start+_1f.photos.photo.length});
for(var i=0;i<_20.length;i++){
_17(_21,_1f,_20[i]);
}
}
};
var _23=this._prevRequests[_12];
if(_23){
this._handlers[_12]=null;
_17(this._cache[_6],_23,_13);
return;
}else{
if(this._checkPrevRanges(_6,_2.start,_2.count)){
this._handlers[_12]=null;
_17(this._cache[_6],null,_13);
return;
}
}
dojo.global[_8]=function(_24){
_1e(_24);
dojo.global[_8]=null;
};
var _25=dojo.io.script.get(_16);
_25.addErrback(function(_26){
dojo.disconnect(_15);
_4(_26,_2);
});
},getAttributes:function(_27){
return ["title","author","imageUrl","imageUrlSmall","imageUrlMedium","imageUrlThumb","link","dateTaken","datePublished"];
},getValues:function(_28,_29){
this._assertIsItem(_28);
this._assertIsAttribute(_29);
if(_29==="title"){
return [this._unescapeHtml(_28.title)];
}else{
if(_29==="author"){
return [_28.ownername];
}else{
if(_29==="imageUrlSmall"){
return [_28.media.s];
}else{
if(_29==="imageUrl"){
return [_28.media.l];
}else{
if(_29==="imageUrlMedium"){
return [_28.media.m];
}else{
if(_29==="imageUrlThumb"){
return [_28.media.t];
}else{
if(_29==="link"){
return ["http://www.flickr.com/photos/"+_28.owner+"/"+_28.id];
}else{
if(_29==="dateTaken"){
return _28.datetaken;
}else{
if(_29==="datePublished"){
return _28.datepublished;
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
},_processFlickrData:function(_2a,_2b,_2c){
if(_2a.items){
return dojox.data.FlickrStore.prototype._processFlickrData.apply(this,arguments);
}
var _2d=["http://farm",null,".static.flickr.com/",null,"/",null,"_",null];
var _2e=[];
if(_2a.stat=="ok"&&_2a.photos&&_2a.photos.photo){
_2e=_2a.photos.photo;
for(var i=0;i<_2e.length;i++){
var _30=_2e[i];
_30[this._storeRef]=this;
_2d[1]=_30.farm;
_2d[3]=_30.server;
_2d[5]=_30.id;
_2d[7]=_30.secret;
var _31=_2d.join("");
_30.media={s:_31+"_s.jpg",m:_31+"_m.jpg",l:_31+".jpg",t:_31+"_t.jpg"};
}
}
var _32=_2b.start?_2b.start:0;
var arr=this._cache[_2c];
if(!arr){
this._cache[_2c]=arr=[];
}
for(var _34=0;_34<_2e.length;_34++){
arr[_34+_32]=_2e[_34];
}
return arr;
},_checkPrevRanges:function(_35,_36,_37){
var end=_36+_37;
var arr=this._prevRequestRanges[_35];
if(!arr){
return false;
}
for(var i=0;i<arr.length;i++){
if(_36>=arr[i].start&&end<=arr[i].end){
return true;
}
}
return false;
}});
}
