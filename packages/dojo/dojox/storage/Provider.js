/*
	Copyright (c) 2004-2008, The Dojo Foundation
	All Rights Reserved.

	Licensed under the Academic Free License version 2.1 or above OR the
	modified BSD license. For more information on Dojo licensing, see:

		http://dojotoolkit.org/book/dojo-book-0-9/introduction/licensing
*/


if(!dojo._hasResource["dojox.storage.Provider"]){
dojo._hasResource["dojox.storage.Provider"]=true;
dojo.provide("dojox.storage.Provider");
dojo.declare("dojox.storage.Provider",null,{constructor:function(){
},SUCCESS:"success",FAILED:"failed",PENDING:"pending",SIZE_NOT_AVAILABLE:"Size not available",SIZE_NO_LIMIT:"No size limit",DEFAULT_NAMESPACE:"default",onHideSettingsUI:null,initialize:function(){
console.warn("dojox.storage.initialize not implemented");
},isAvailable:function(){
console.warn("dojox.storage.isAvailable not implemented");
},put:function(_1,_2,_3,_4){
console.warn("dojox.storage.put not implemented");
},get:function(_5,_6){
console.warn("dojox.storage.get not implemented");
},hasKey:function(_7,_8){
return !!this.get(_7,_8);
},getKeys:function(_9){
console.warn("dojox.storage.getKeys not implemented");
},clear:function(_a){
console.warn("dojox.storage.clear not implemented");
},remove:function(_b,_c){
console.warn("dojox.storage.remove not implemented");
},getNamespaces:function(){
console.warn("dojox.storage.getNamespaces not implemented");
},isPermanent:function(){
console.warn("dojox.storage.isPermanent not implemented");
},getMaximumSize:function(){
console.warn("dojox.storage.getMaximumSize not implemented");
},putMultiple:function(_d,_e,_f,_10){
console.warn("dojox.storage.putMultiple not implemented");
},getMultiple:function(_11,_12){
console.warn("dojox.storage.getMultiple not implemented");
},removeMultiple:function(_13,_14){
console.warn("dojox.storage.remove not implemented");
},isValidKeyArray:function(_15){
if(_15===null||_15===undefined||!dojo.isArray(_15)){
return false;
}
return !dojo.some(_15,function(key){
return !this.isValidKey(key);
});
},hasSettingsUI:function(){
return false;
},showSettingsUI:function(){
console.warn("dojox.storage.showSettingsUI not implemented");
},hideSettingsUI:function(){
console.warn("dojox.storage.hideSettingsUI not implemented");
},isValidKey:function(_17){
if(_17===null||_17===undefined){
return false;
}
return /^[0-9A-Za-z_]*$/.test(_17);
},getResourceList:function(){
return [];
}});
}
