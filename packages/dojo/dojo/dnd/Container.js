/*
	Copyright (c) 2004-2008, The Dojo Foundation
	All Rights Reserved.

	Licensed under the Academic Free License version 2.1 or above OR the
	modified BSD license. For more information on Dojo licensing, see:

		http://dojotoolkit.org/book/dojo-book-0-9/introduction/licensing
*/


if(!dojo._hasResource["dojo.dnd.Container"]){
dojo._hasResource["dojo.dnd.Container"]=true;
dojo.provide("dojo.dnd.Container");
dojo.require("dojo.dnd.common");
dojo.require("dojo.parser");
dojo.declare("dojo.dnd.Container",null,{skipForm:false,constructor:function(_1,_2){
this.node=dojo.byId(_1);
if(!_2){
_2={};
}
this.creator=_2.creator||null;
this.skipForm=_2.skipForm;
this.defaultCreator=dojo.dnd._defaultCreator(this.node);
this.map={};
this.current=null;
this.containerState="";
dojo.addClass(this.node,"dojoDndContainer");
if(!(_2&&_2._skipStartup)){
this.startup();
}
this.events=[dojo.connect(this.node,"onmouseover",this,"onMouseOver"),dojo.connect(this.node,"onmouseout",this,"onMouseOut"),dojo.connect(this.node,"ondragstart",this,"onSelectStart"),dojo.connect(this.node,"onselectstart",this,"onSelectStart")];
},creator:function(){
},getItem:function(_3){
return this.map[_3];
},setItem:function(_4,_5){
this.map[_4]=_5;
},delItem:function(_6){
delete this.map[_6];
},forInItems:function(f,o){
o=o||dojo.global;
var m=this.map,e=dojo.dnd._empty;
for(var i in this.map){
if(i in e){
continue;
}
f.call(o,m[i],i,m);
}
},clearItems:function(){
this.map={};
},getAllNodes:function(){
return dojo.query("> .dojoDndItem",this.parent);
},insertNodes:function(_c,_d,_e){
if(!this.parent.firstChild){
_e=null;
}else{
if(_d){
if(!_e){
_e=this.parent.firstChild;
}
}else{
if(_e){
_e=_e.nextSibling;
}
}
}
if(_e){
for(var i=0;i<_c.length;++i){
var t=this._normalizedCreator(_c[i]);
this.setItem(t.node.id,{data:t.data,type:t.type});
this.parent.insertBefore(t.node,_e);
}
}else{
for(var i=0;i<_c.length;++i){
var t=this._normalizedCreator(_c[i]);
this.setItem(t.node.id,{data:t.data,type:t.type});
this.parent.appendChild(t.node);
}
}
return this;
},destroy:function(){
dojo.forEach(this.events,dojo.disconnect);
this.clearItems();
this.node=this.parent=this.current;
},markupFactory:function(_11,_12){
_11._skipStartup=true;
return new dojo.dnd.Container(_12,_11);
},startup:function(){
this.parent=this.node;
if(this.parent.tagName.toLowerCase()=="table"){
var c=this.parent.getElementsByTagName("tbody");
if(c&&c.length){
this.parent=c[0];
}
}
this.getAllNodes().forEach(function(_14){
if(!_14.id){
_14.id=dojo.dnd.getUniqueId();
}
var _15=_14.getAttribute("dndType"),_16=_14.getAttribute("dndData");
this.setItem(_14.id,{data:_16?_16:_14.innerHTML,type:_15?_15.split(/\s*,\s*/):["text"]});
},this);
},onMouseOver:function(e){
var n=e.relatedTarget;
while(n){
if(n==this.node){
break;
}
try{
n=n.parentNode;
}
catch(x){
n=null;
}
}
if(!n){
this._changeState("Container","Over");
this.onOverEvent();
}
n=this._getChildByEvent(e);
if(this.current==n){
return;
}
if(this.current){
this._removeItemClass(this.current,"Over");
}
if(n){
this._addItemClass(n,"Over");
}
this.current=n;
},onMouseOut:function(e){
for(var n=e.relatedTarget;n;){
if(n==this.node){
return;
}
try{
n=n.parentNode;
}
catch(x){
n=null;
}
}
if(this.current){
this._removeItemClass(this.current,"Over");
this.current=null;
}
this._changeState("Container","");
this.onOutEvent();
},onSelectStart:function(e){
if(!this.skipForm||!dojo.dnd.isFormElement(e)){
dojo.stopEvent(e);
}
},onOverEvent:function(){
},onOutEvent:function(){
},_changeState:function(_1c,_1d){
var _1e="dojoDnd"+_1c;
var _1f=_1c.toLowerCase()+"State";
dojo.removeClass(this.node,_1e+this[_1f]);
dojo.addClass(this.node,_1e+_1d);
this[_1f]=_1d;
},_addItemClass:function(_20,_21){
dojo.addClass(_20,"dojoDndItem"+_21);
},_removeItemClass:function(_22,_23){
dojo.removeClass(_22,"dojoDndItem"+_23);
},_getChildByEvent:function(e){
var _25=e.target;
if(_25){
for(var _26=_25.parentNode;_26;_25=_26,_26=_25.parentNode){
if(_26==this.parent&&dojo.hasClass(_25,"dojoDndItem")){
return _25;
}
}
}
return null;
},_normalizedCreator:function(_27,_28){
var t=(this.creator?this.creator:this.defaultCreator)(_27,_28);
if(!dojo.isArray(t.type)){
t.type=["text"];
}
if(!t.node.id){
t.node.id=dojo.dnd.getUniqueId();
}
dojo.addClass(t.node,"dojoDndItem");
return t;
}});
dojo.dnd._createNode=function(tag){
if(!tag){
return dojo.dnd._createSpan;
}
return function(_2b){
var n=dojo.doc.createElement(tag);
n.innerHTML=_2b;
return n;
};
};
dojo.dnd._createTrTd=function(_2d){
var tr=dojo.doc.createElement("tr");
var td=dojo.doc.createElement("td");
td.innerHTML=_2d;
tr.appendChild(td);
return tr;
};
dojo.dnd._createSpan=function(_30){
var n=dojo.doc.createElement("span");
n.innerHTML=_30;
return n;
};
dojo.dnd._defaultCreatorNodes={ul:"li",ol:"li",div:"div",p:"div"};
dojo.dnd._defaultCreator=function(_32){
var tag=_32.tagName.toLowerCase();
var c=tag=="table"?dojo.dnd._createTrTd:dojo.dnd._createNode(dojo.dnd._defaultCreatorNodes[tag]);
return function(_35,_36){
var _37=dojo.isObject(_35)&&_35;
var _38=(_37&&_35.data)?_35.data:_35;
var _39=(_37&&_35.type)?_35.type:["text"];
var t=String(_38),n=(_36=="avatar"?dojo.dnd._createSpan:c)(t);
n.id=dojo.dnd.getUniqueId();
return {node:n,data:_38,type:_39};
};
};
}
