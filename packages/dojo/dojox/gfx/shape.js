/*
	Copyright (c) 2004-2008, The Dojo Foundation
	All Rights Reserved.

	Licensed under the Academic Free License version 2.1 or above OR the
	modified BSD license. For more information on Dojo licensing, see:

		http://dojotoolkit.org/book/dojo-book-0-9/introduction/licensing
*/


if(!dojo._hasResource["dojox.gfx.shape"]){
dojo._hasResource["dojox.gfx.shape"]=true;
dojo.provide("dojox.gfx.shape");
dojo.require("dojox.gfx._base");
dojo.declare("dojox.gfx.Shape",null,{constructor:function(){
this.rawNode=null;
this.shape=null;
this.matrix=null;
this.fillStyle=null;
this.strokeStyle=null;
this.bbox=null;
this.parent=null;
this.parentMatrix=null;
},getNode:function(){
return this.rawNode;
},getShape:function(){
return this.shape;
},getTransform:function(){
return this.matrix;
},getFill:function(){
return this.fillStyle;
},getStroke:function(){
return this.strokeStyle;
},getParent:function(){
return this.parent;
},getBoundingBox:function(){
return this.bbox;
},getTransformedBoundingBox:function(){
var b=this.getBoundingBox();
if(!b){
return null;
}
var m=this._getRealMatrix();
var r=[];
var g=dojox.gfx.matrix;
r.push(g.multiplyPoint(m,b.x,b.y));
r.push(g.multiplyPoint(m,b.x+b.width,b.y));
r.push(g.multiplyPoint(m,b.x+b.width,b.y+b.height));
r.push(g.multiplyPoint(m,b.x,b.y+b.height));
return r;
},getEventSource:function(){
return this.rawNode;
},setShape:function(_5){
this.shape=dojox.gfx.makeParameters(this.shape,_5);
this.bbox=null;
return this;
},setFill:function(_6){
if(!_6){
this.fillStyle=null;
return this;
}
var f=null;
if(typeof (_6)=="object"&&"type" in _6){
switch(_6.type){
case "linear":
f=dojox.gfx.makeParameters(dojox.gfx.defaultLinearGradient,_6);
break;
case "radial":
f=dojox.gfx.makeParameters(dojox.gfx.defaultRadialGradient,_6);
break;
case "pattern":
f=dojox.gfx.makeParameters(dojox.gfx.defaultPattern,_6);
break;
}
}else{
f=dojox.gfx.normalizeColor(_6);
}
this.fillStyle=f;
return this;
},setStroke:function(_8){
if(!_8){
this.strokeStyle=null;
return this;
}
if(typeof _8=="string"){
_8={color:_8};
}
var s=this.strokeStyle=dojox.gfx.makeParameters(dojox.gfx.defaultStroke,_8);
s.color=dojox.gfx.normalizeColor(s.color);
return this;
},setTransform:function(_a){
this.matrix=dojox.gfx.matrix.clone(_a?dojox.gfx.matrix.normalize(_a):dojox.gfx.matrix.identity);
return this._applyTransform();
},_applyTransform:function(){
return this;
},moveToFront:function(){
var p=this.getParent();
if(p){
p._moveChildToFront(this);
this._moveToFront();
}
return this;
},moveToBack:function(){
var p=this.getParent();
if(p){
p._moveChildToBack(this);
this._moveToBack();
}
return this;
},_moveToFront:function(){
},_moveToBack:function(){
},applyRightTransform:function(_d){
return _d?this.setTransform([this.matrix,_d]):this;
},applyLeftTransform:function(_e){
return _e?this.setTransform([_e,this.matrix]):this;
},applyTransform:function(_f){
return _f?this.setTransform([this.matrix,_f]):this;
},removeShape:function(_10){
if(this.parent){
this.parent.remove(this,_10);
}
return this;
},_setParent:function(_11,_12){
this.parent=_11;
return this._updateParentMatrix(_12);
},_updateParentMatrix:function(_13){
this.parentMatrix=_13?dojox.gfx.matrix.clone(_13):null;
return this._applyTransform();
},_getRealMatrix:function(){
var m=this.matrix;
var p=this.parent;
while(p){
if(p.matrix){
m=dojox.gfx.matrix.multiply(p.matrix,m);
}
p=p.parent;
}
return m;
}});
dojox.gfx.shape._eventsProcessing={connect:function(_16,_17,_18){
return arguments.length>2?dojo.connect(this.getEventSource(),_16,_17,_18):dojo.connect(this.getEventSource(),_16,_17);
},disconnect:function(_19){
dojo.disconnect(_19);
}};
dojo.extend(dojox.gfx.Shape,dojox.gfx.shape._eventsProcessing);
dojox.gfx.shape.Container={_init:function(){
this.children=[];
},add:function(_1a){
var _1b=_1a.getParent();
if(_1b){
_1b.remove(_1a,true);
}
this.children.push(_1a);
return _1a._setParent(this,this._getRealMatrix());
},remove:function(_1c,_1d){
for(var i=0;i<this.children.length;++i){
if(this.children[i]==_1c){
if(_1d){
}else{
_1c._setParent(null,null);
}
this.children.splice(i,1);
break;
}
}
return this;
},clear:function(){
this.children=[];
return this;
},_moveChildToFront:function(_1f){
for(var i=0;i<this.children.length;++i){
if(this.children[i]==_1f){
this.children.splice(i,1);
this.children.push(_1f);
break;
}
}
return this;
},_moveChildToBack:function(_21){
for(var i=0;i<this.children.length;++i){
if(this.children[i]==_21){
this.children.splice(i,1);
this.children.unshift(_21);
break;
}
}
return this;
}};
dojo.declare("dojox.gfx.shape.Surface",null,{constructor:function(){
this.rawNode=null;
},getEventSource:function(){
return this.rawNode;
},_getRealMatrix:function(){
return null;
}});
dojo.extend(dojox.gfx.shape.Surface,dojox.gfx.shape._eventsProcessing);
dojo.declare("dojox.gfx.Point",null,{});
dojo.declare("dojox.gfx.Rectangle",null,{});
dojo.declare("dojox.gfx.shape.Rect",dojox.gfx.Shape,{constructor:function(_23){
this.shape=dojo.clone(dojox.gfx.defaultRect);
this.rawNode=_23;
},getBoundingBox:function(){
return this.shape;
}});
dojo.declare("dojox.gfx.shape.Ellipse",dojox.gfx.Shape,{constructor:function(_24){
this.shape=dojo.clone(dojox.gfx.defaultEllipse);
this.rawNode=_24;
},getBoundingBox:function(){
if(!this.bbox){
var _25=this.shape;
this.bbox={x:_25.cx-_25.rx,y:_25.cy-_25.ry,width:2*_25.rx,height:2*_25.ry};
}
return this.bbox;
}});
dojo.declare("dojox.gfx.shape.Circle",dojox.gfx.Shape,{constructor:function(_26){
this.shape=dojo.clone(dojox.gfx.defaultCircle);
this.rawNode=_26;
},getBoundingBox:function(){
if(!this.bbox){
var _27=this.shape;
this.bbox={x:_27.cx-_27.r,y:_27.cy-_27.r,width:2*_27.r,height:2*_27.r};
}
return this.bbox;
}});
dojo.declare("dojox.gfx.shape.Line",dojox.gfx.Shape,{constructor:function(_28){
this.shape=dojo.clone(dojox.gfx.defaultLine);
this.rawNode=_28;
},getBoundingBox:function(){
if(!this.bbox){
var _29=this.shape;
this.bbox={x:Math.min(_29.x1,_29.x2),y:Math.min(_29.y1,_29.y2),width:Math.abs(_29.x2-_29.x1),height:Math.abs(_29.y2-_29.y1)};
}
return this.bbox;
}});
dojo.declare("dojox.gfx.shape.Polyline",dojox.gfx.Shape,{constructor:function(_2a){
this.shape=dojo.clone(dojox.gfx.defaultPolyline);
this.rawNode=_2a;
},setShape:function(_2b,_2c){
if(_2b&&_2b instanceof Array){
dojox.gfx.Shape.prototype.setShape.call(this,{points:_2b});
if(_2c&&this.shape.points.length){
this.shape.points.push(this.shape.points[0]);
}
}else{
dojox.gfx.Shape.prototype.setShape.call(this,_2b);
}
return this;
},getBoundingBox:function(){
if(!this.bbox&&this.shape.points.length){
var p=this.shape.points;
var l=p.length;
var t=p[0];
var _30={l:t.x,t:t.y,r:t.x,b:t.y};
for(var i=1;i<l;++i){
t=p[i];
if(_30.l>t.x){
_30.l=t.x;
}
if(_30.r<t.x){
_30.r=t.x;
}
if(_30.t>t.y){
_30.t=t.y;
}
if(_30.b<t.y){
_30.b=t.y;
}
}
this.bbox={x:_30.l,y:_30.t,width:_30.r-_30.l,height:_30.b-_30.t};
}
return this.bbox;
}});
dojo.declare("dojox.gfx.shape.Image",dojox.gfx.Shape,{constructor:function(_32){
this.shape=dojo.clone(dojox.gfx.defaultImage);
this.rawNode=_32;
},getBoundingBox:function(){
return this.shape;
},setStroke:function(){
return this;
},setFill:function(){
return this;
}});
dojo.declare("dojox.gfx.shape.Text",dojox.gfx.Shape,{constructor:function(_33){
this.fontStyle=null;
this.shape=dojo.clone(dojox.gfx.defaultText);
this.rawNode=_33;
},getFont:function(){
return this.fontStyle;
},setFont:function(_34){
this.fontStyle=typeof _34=="string"?dojox.gfx.splitFontString(_34):dojox.gfx.makeParameters(dojox.gfx.defaultFont,_34);
this._setFont();
return this;
}});
dojox.gfx.shape.Creator={createShape:function(_35){
switch(_35.type){
case dojox.gfx.defaultPath.type:
return this.createPath(_35);
case dojox.gfx.defaultRect.type:
return this.createRect(_35);
case dojox.gfx.defaultCircle.type:
return this.createCircle(_35);
case dojox.gfx.defaultEllipse.type:
return this.createEllipse(_35);
case dojox.gfx.defaultLine.type:
return this.createLine(_35);
case dojox.gfx.defaultPolyline.type:
return this.createPolyline(_35);
case dojox.gfx.defaultImage.type:
return this.createImage(_35);
case dojox.gfx.defaultText.type:
return this.createText(_35);
case dojox.gfx.defaultTextPath.type:
return this.createTextPath(_35);
}
return null;
},createGroup:function(){
return this.createObject(dojox.gfx.Group);
},createRect:function(_36){
return this.createObject(dojox.gfx.Rect,_36);
},createEllipse:function(_37){
return this.createObject(dojox.gfx.Ellipse,_37);
},createCircle:function(_38){
return this.createObject(dojox.gfx.Circle,_38);
},createLine:function(_39){
return this.createObject(dojox.gfx.Line,_39);
},createPolyline:function(_3a){
return this.createObject(dojox.gfx.Polyline,_3a);
},createImage:function(_3b){
return this.createObject(dojox.gfx.Image,_3b);
},createText:function(_3c){
return this.createObject(dojox.gfx.Text,_3c);
},createPath:function(_3d){
return this.createObject(dojox.gfx.Path,_3d);
},createTextPath:function(_3e){
return this.createObject(dojox.gfx.TextPath,{}).setText(_3e);
},createObject:function(_3f,_40){
return null;
}};
}
