/*
	Copyright (c) 2004-2008, The Dojo Foundation
	All Rights Reserved.

	Licensed under the Academic Free License version 2.1 or above OR the
	modified BSD license. For more information on Dojo licensing, see:

		http://dojotoolkit.org/book/dojo-book-0-9/introduction/licensing
*/


if(!dojo._hasResource["dijit._editor.range"]){
dojo._hasResource["dijit._editor.range"]=true;
dojo.provide("dijit._editor.range");
dijit.range={};
dijit.range.getIndex=function(_1,_2){
var _3=[],_4=[];
var _5=_2;
var _6=_1;
var _7,n;
while(_1!=_5){
var i=0;
_7=_1.parentNode;
while((n=_7.childNodes[i++])){
if(n===_1){
--i;
break;
}
}
if(i>=_7.childNodes.length){
dojo.debug("Error finding index of a node in dijit.range.getIndex");
}
_3.unshift(i);
_4.unshift(i-_7.childNodes.length);
_1=_7;
}
if(_3.length>0&&_6.nodeType==3){
n=_6.previousSibling;
while(n&&n.nodeType==3){
_3[_3.length-1]--;
n=n.previousSibling;
}
n=_6.nextSibling;
while(n&&n.nodeType==3){
_4[_4.length-1]++;
n=n.nextSibling;
}
}
return {o:_3,r:_4};
};
dijit.range.getNode=function(_a,_b){
if(!dojo.isArray(_a)||_a.length==0){
return _b;
}
var _c=_b;
dojo.every(_a,function(i){
if(i>=0&&i<_c.childNodes.length){
_c=_c.childNodes[i];
}else{
_c=null;
console.debug("Error: can not find node with index",_a,"under parent node",_b);
return false;
}
return true;
});
return _c;
};
dijit.range.getCommonAncestor=function(n1,n2,_10){
var _11=function(n,_13){
var as=[];
while(n){
as.unshift(n);
if(n!=_13&&n.tagName!="BODY"){
n=n.parentNode;
}else{
break;
}
}
return as;
};
var _15=_11(n1,_10);
var _16=_11(n2,_10);
var m=Math.min(_15.length,_16.length);
var com=_15[0];
for(var i=1;i<m;i++){
if(_15[i]===_16[i]){
com=_15[i];
}else{
break;
}
}
return com;
};
dijit.range.getAncestor=function(_1a,_1b,_1c){
_1c=_1c||_1a.ownerDocument.body;
while(_1a&&_1a!==_1c){
var _1d=_1a.nodeName.toUpperCase();
if(_1b.test(_1d)){
return _1a;
}
_1a=_1a.parentNode;
}
return null;
};
dijit.range.BlockTagNames=/^(?:P|DIV|H1|H2|H3|H4|H5|H6|ADDRESS|PRE|OL|UL|LI|DT|DE)$/;
dijit.range.getBlockAncestor=function(_1e,_1f,_20){
_20=_20||_1e.ownerDocument.body;
_1f=_1f||dijit.range.BlockTagNames;
var _21=null,_22;
while(_1e&&_1e!==_20){
var _23=_1e.nodeName.toUpperCase();
if(!_21&&_1f.test(_23)){
_21=_1e;
}
if(!_22&&(/^(?:BODY|TD|TH|CAPTION)$/).test(_23)){
_22=_1e;
}
_1e=_1e.parentNode;
}
return {blockNode:_21,blockContainer:_22||_1e.ownerDocument.body};
};
dijit.range.atBeginningOfContainer=function(_24,_25,_26){
var _27=false;
var _28=(_26==0);
if(!_28&&_25.nodeType==3){
if(dojo.trim(_25.nodeValue.substr(0,_26))==0){
_28=true;
}
}
if(_28){
var _29=_25;
_27=true;
while(_29&&_29!==_24){
if(_29.previousSibling){
_27=false;
break;
}
_29=_29.parentNode;
}
}
return _27;
};
dijit.range.atEndOfContainer=function(_2a,_2b,_2c){
var _2d=false;
var _2e=(_2c==(_2b.length||_2b.childNodes.length));
if(!_2e&&_2b.nodeType==3){
if(dojo.trim(_2b.nodeValue.substr(_2c))==0){
_2e=true;
}
}
if(_2e){
var _2f=_2b;
_2d=true;
while(_2f&&_2f!==_2a){
if(_2f.nextSibling){
_2d=false;
break;
}
_2f=_2f.parentNode;
}
}
return _2d;
};
dijit.range.adjacentNoneTextNode=function(_30,_31){
var _32=_30;
var len=(0-_30.length)||0;
var _34=_31?"nextSibling":"previousSibling";
while(_32){
if(_32.nodeType!=3){
break;
}
len+=_32.length;
_32=_32[_34];
}
return [_32,len];
};
dijit.range._w3c=Boolean(window["getSelection"]);
dijit.range.create=function(){
if(dijit.range._w3c){
return dojo.doc.createRange();
}else{
return new dijit.range.W3CRange;
}
};
dijit.range.getSelection=function(win,_36){
if(dijit.range._w3c){
return win.getSelection();
}else{
var id=win.__W3CRange,s;
if(!id||!dijit.range.ie.cachedSelection[id]){
s=new dijit.range.ie.selection(win);
id=(new Date).getTime();
while(id in dijit.range.ie.cachedSelection){
id=id+1;
}
id=String(id);
dijit.range.ie.cachedSelection[id]=s;
}else{
s=dijit.range.ie.cachedSelection[id];
}
if(!_36){
s._getCurrentSelection();
}
return s;
}
};
if(!dijit.range._w3c){
dijit.range.ie={cachedSelection:{},selection:function(win){
this._ranges=[];
this.addRange=function(r,_3b){
this._ranges.push(r);
if(!_3b){
r._select();
}
this.rangeCount=this._ranges.length;
};
this.removeAllRanges=function(){
this._ranges=[];
this.rangeCount=0;
};
var _3c=function(){
var r=win.document.selection.createRange();
var _3e=win.document.selection.type.toUpperCase();
if(_3e=="CONTROL"){
return new dijit.range.W3CRange(dijit.range.ie.decomposeControlRange(r));
}else{
return new dijit.range.W3CRange(dijit.range.ie.decomposeTextRange(r));
}
};
this.getRangeAt=function(i){
return this._ranges[i];
};
this._getCurrentSelection=function(){
this.removeAllRanges();
var r=_3c();
if(r){
this.addRange(r,true);
}
};
},decomposeControlRange:function(_41){
var _42=_41.item(0),_43=_41.item(_41.length-1);
var _44=_42.parentNode,_45=_43.parentNode;
var _46=dijit.range.getIndex(_42,_44).o;
var _47=dijit.range.getIndex(_43,_45).o+1;
return [[_44,_46],[_45,_47]];
},getEndPoint:function(_48,end){
var _4a=_48.duplicate();
_4a.collapse(!end);
var _4b="EndTo"+(end?"End":"Start");
var _4c=_4a.parentElement();
var _4d,_4e,_4f;
if(_4c.childNodes.length>0){
dojo.every(_4c.childNodes,function(_50,i){
var _52;
if(_50.nodeType!=3){
_4a.moveToElementText(_50);
if(_4a.compareEndPoints(_4b,_48)>0){
_4d=_50.previousSibling;
if(_4f&&_4f.nodeType==3){
_4d=_4f;
_52=true;
}else{
_4d=_4c;
_4e=i;
return false;
}
}else{
if(i==_4c.childNodes.length-1){
_4d=_4c;
_4e=_4c.childNodes.length;
return false;
}
}
}else{
if(i==_4c.childNodes.length-1){
_4d=_50;
_52=true;
}
}
if(_52&&_4d){
var _53=dijit.range.adjacentNoneTextNode(_4d)[0];
if(_53){
_4d=_53.nextSibling;
}else{
_4d=_4c.firstChild;
}
var _54=dijit.range.adjacentNoneTextNode(_4d);
_53=_54[0];
var _55=_54[1];
if(_53){
_4a.moveToElementText(_53);
_4a.collapse(false);
}else{
_4a.moveToElementText(_4c);
}
_4a.setEndPoint(_4b,_48);
_4e=_4a.text.length-_55;
return false;
}
_4f=_50;
return true;
});
}else{
_4d=_4c;
_4e=0;
}
if(!end&&_4d.nodeType!=3&&_4e==_4d.childNodes.length){
if(_4d.nextSibling&&_4d.nextSibling.nodeType==3){
_4d=_4d.nextSibling;
_4e=0;
}
}
return [_4d,_4e];
},setEndPoint:function(_56,_57,_58){
var _59=_56.duplicate(),_5a,len;
if(_57.nodeType!=3){
_59.moveToElementText(_57);
_59.collapse(true);
if(_58==_57.childNodes.length){
if(_58>0){
_5a=_57.lastChild;
len=0;
while(_5a&&_5a.nodeType==3){
len+=_5a.length;
_57=_5a;
_5a=_5a.previousSibling;
}
if(_5a){
_59.moveToElementText(_5a);
}
_59.collapse(false);
_58=len;
}else{
_59.moveToElementText(_57);
_59.collapse(true);
}
}else{
if(_58>0){
_5a=_57.childNodes[_58-1];
if(_5a.nodeType==3){
_57=_5a;
_58=_5a.length;
}else{
_59.moveToElementText(_5a);
_59.collapse(false);
}
}
}
}
if(_57.nodeType==3){
var _5c=dijit.range.adjacentNoneTextNode(_57);
var _5d=_5c[0];
len=_5c[1];
if(_5d){
_59.moveToElementText(_5d);
_59.collapse(false);
if(_5d.contentEditable!="inherit"){
len++;
}
}else{
_59.moveToElementText(_57.parentNode);
_59.collapse(true);
}
_58+=len;
if(_58>0){
if(_59.moveEnd("character",_58)!=_58){
alert("Error when moving!");
}
_59.collapse(false);
}
}
return _59;
},decomposeTextRange:function(_5e){
var _5f=dijit.range.ie.getEndPoint(_5e);
var _60=_5f[0],_61=_5f[1];
var _62=_5f[0],_63=_5f[1];
if(_5e.htmlText.length){
if(_5e.htmlText==_5e.text){
_63=_61+_5e.text.length;
}else{
_5f=dijit.range.ie.getEndPoint(_5e,true);
_62=_5f[0],_63=_5f[1];
}
}
return [[_60,_61],[_62,_63],_5e.parentElement()];
},setRange:function(_64,_65,_66,_67,_68,_69){
var _6a=dijit.range.ie.setEndPoint(_64,_65,_66);
_64.setEndPoint("StartToStart",_6a);
if(!this.collapsed){
var _6b=dijit.range.ie.setEndPoint(_64,_67,_68);
_64.setEndPoint("EndToEnd",_6b);
}
return _64;
}};
dojo.declare("dijit.range.W3CRange",null,{constructor:function(){
if(arguments.length>0){
this.setStart(arguments[0][0][0],arguments[0][0][1]);
this.setEnd(arguments[0][1][0],arguments[0][1][1],arguments[0][2]);
}else{
this.commonAncestorContainer=null;
this.startContainer=null;
this.startOffset=0;
this.endContainer=null;
this.endOffset=0;
this.collapsed=true;
}
},_simpleSetEndPoint:function(_6c,_6d,end){
var r=(this._body||_6c.ownerDocument.body).createTextRange();
if(_6c.nodeType!=1){
r.moveToElementText(_6c.parentNode);
}else{
r.moveToElementText(_6c);
}
r.collapse(true);
_6d.setEndPoint(end?"EndToEnd":"StartToStart",r);
},_updateInternal:function(_70){
if(this.startContainer!==this.endContainer){
if(!_70){
var r=(this._body||this.startContainer.ownerDocument.body).createTextRange();
this._simpleSetEndPoint(this.startContainer,r);
this._simpleSetEndPoint(this.endContainer,r,true);
_70=r.parentElement();
}
this.commonAncestorContainer=dijit.range.getCommonAncestor(this.startContainer,this.endContainer,_70);
}else{
this.commonAncestorContainer=this.startContainer;
}
this.collapsed=(this.startContainer===this.endContainer)&&(this.startOffset==this.endOffset);
},setStart:function(_72,_73,_74){
_73=parseInt(_73);
if(this.startContainer===_72&&this.startOffset==_73){
return;
}
delete this._cachedBookmark;
this.startContainer=_72;
this.startOffset=_73;
if(!this.endContainer){
this.setEnd(_72,_73,_74);
}else{
this._updateInternal(_74);
}
},setEnd:function(_75,_76,_77){
_76=parseInt(_76);
if(this.endContainer===_75&&this.endOffset==_76){
return;
}
delete this._cachedBookmark;
this.endContainer=_75;
this.endOffset=_76;
if(!this.startContainer){
this.setStart(_75,_76,_77);
}else{
this._updateInternal(_77);
}
},setStartAfter:function(_78,_79){
this._setPoint("setStart",_78,_79,1);
},setStartBefore:function(_7a,_7b){
this._setPoint("setStart",_7a,_7b,0);
},setEndAfter:function(_7c,_7d){
this._setPoint("setEnd",_7c,_7d,1);
},setEndBefore:function(_7e,_7f){
this._setPoint("setEnd",_7e,_7f,0);
},_setPoint:function(_80,_81,_82,ext){
var _84=dijit.range.getIndex(_81,_81.parentNode).o;
this[_80](_81.parentNode,_84.pop()+ext);
},_getIERange:function(){
var r=(this._body||this.endContainer.ownerDocument.body).createTextRange();
dijit.range.ie.setRange(r,this.startContainer,this.startOffset,this.endContainer,this.endOffset);
return r;
},getBookmark:function(_86){
this._getIERange();
return this._cachedBookmark;
},_select:function(){
var r=this._getIERange();
r.select();
},deleteContents:function(){
var r=this._getIERange();
r.pasteHTML("");
this.endContainer=this.startContainer;
this.endOffset=this.startOffset;
this.collapsed=true;
},cloneRange:function(){
var r=new dijit.range.W3CRange([[this.startContainer,this.startOffset],[this.endContainer,this.endOffset]]);
r._body=this._body;
return r;
},detach:function(){
this._body=null;
this.commonAncestorContainer=null;
this.startContainer=null;
this.startOffset=0;
this.endContainer=null;
this.endOffset=0;
this.collapsed=true;
}});
}
}
