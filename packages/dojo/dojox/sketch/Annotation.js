/*
	Copyright (c) 2004-2008, The Dojo Foundation
	All Rights Reserved.

	Licensed under the Academic Free License version 2.1 or above OR the
	modified BSD license. For more information on Dojo licensing, see:

		http://dojotoolkit.org/book/dojo-book-0-9/introduction/licensing
*/


if(!dojo._hasResource["dojox.sketch.Annotation"]){
dojo._hasResource["dojox.sketch.Annotation"]=true;
dojo.provide("dojox.sketch.Annotation");
dojo.require("dojox.sketch.Anchor");
dojo.require("dojox.sketch._Plugin");
(function(){
var ta=dojox.sketch;
dojo.declare("dojox.sketch.AnnotationTool",ta._Plugin,{onMouseMove:function(e,_3){
if(this._cshape){
this._cshape.setShape(_3);
}else{
this._cshape=this.figure.surface.createRect(_3).setStroke({color:"#999",width:1,style:"ShortDot"}).setFill([255,255,255,0.7]);
this._cshape.getEventSource().setAttribute("shape-rendering","crispEdges");
}
},onMouseUp:function(e){
var f=this.figure;
if(!(f._startPoint.x==e.pageX&&f._startPoint.y==e.pageY)){
if(this._cshape){
var _6=40;
if(Math.max(_6,Math.abs(f._absEnd.x-f._start.x),Math.abs(f._absEnd.y-f._start.y))>_6){
this._create(f._start,f._end);
}
}
}
if(this._cshape){
f.surface.remove(this._cshape);
}
},_create:function(_7,_8){
var f=this.figure;
var _=f.nextKey();
var a=new (this.annotation)(f,"annotation-"+_);
a.transform={dx:_7.x/f.zoomFactor,dy:_7.y/f.zoomFactor};
a.end={x:_8.x/f.zoomFactor,y:_8.y/f.zoomFactor};
if(a.control){
a.control={x:Math.round((_8.x/2)/f.zoomFactor),y:Math.round((_8.y/2)/f.zoomFactor)};
}
f.onBeforeCreateShape(a);
a.initialize();
f.select(a);
f.onCreateShape(a);
f.history.add(ta.CommandTypes.Create,a);
}});
ta.Annotation=function(_c,id){
this.id=this._key=id;
this.figure=_c;
this.mode=ta.Annotation.Modes.View;
this.shape=null;
this.boundingBox=null;
this.hasAnchors=true;
this.anchors={};
this._properties={"stroke":{color:"blue",width:2},"fill":"blue","label":""};
if(this.figure){
this.figure.add(this);
}
};
var p=ta.Annotation.prototype;
p.constructor=ta.Annotation;
p.type=function(){
return "";
};
p.getType=function(){
return ta.Annotation;
};
p.remove=function(){
this.figure.history.add(ta.CommandTypes.Delete,this,this.serialize());
};
p.property=function(_f,_10){
var r;
_f=_f.toLowerCase();
if(this._properties[_f]!==undefined){
r=this._properties[_f];
}
if(arguments.length>1){
this._properties[_f]=_10;
}
if(r!=_10){
this.onPropertyChange(_f,r);
}
return r;
};
p.onPropertyChange=function(_12,_13){
};
p.onCreate=function(){
this.figure.history.add(ta.CommandTypes.Create,this);
};
p.onDblClick=function(_14){
var l=prompt("Set new text:",this.property("label"));
if(l!==false){
this.beginEdit(ta.CommandTypes.Modify);
this.property("label",l);
this.draw();
this.endEdit();
}
};
p.initialize=function(){
};
p.destroy=function(){
};
p.draw=function(){
};
p.apply=function(obj){
};
p.serialize=function(){
};
p.getBBox=function(){
};
p.beginEdit=function(_17){
this._type=_17||ta.CommandTypes.Move;
this._prevState=this.serialize();
};
p.endEdit=function(){
var _18=true;
if(this._type==ta.CommandTypes.Move){
var f=this.figure;
if(f._absEnd.x==f._start.x&&f._absEnd.y==f._start.y){
_18=false;
}
}
if(_18){
this.figure.history.add(this._type,this,this._prevState);
}
this._type=this._prevState="";
};
p.calculate={slope:function(p1,p2){
if(!(p1.x-p2.x)){
return 0;
}
return ((p1.y-p2.y)/(p1.x-p2.x));
},dx:function(p1,p2,dy){
var s=this.slope(p1,p2);
if(s==0){
return s;
}
return dy/s;
},dy:function(p1,p2,dx){
return this.slope(p1,p2)*dx;
}};
p.drawBBox=function(){
var r=this.getBBox();
if(!this.boundingBox){
this.boundingBox=this.shape.createRect(r).moveToBack().setStroke({color:"#999",width:1,style:"Dash"}).setFill([238,238,238,0.3]);
this.boundingBox.getEventSource().setAttribute("id",this.id+"-boundingBox");
this.boundingBox.getEventSource().setAttribute("shape-rendering","crispEdges");
this.figure._add(this);
}else{
this.boundingBox.setShape(r);
}
};
p.setBinding=function(pt){
this.transform.dx+=pt.dx;
this.transform.dy+=pt.dy;
this.draw();
};
p.doChange=function(pt){
};
p.getTextBox=function(){
return dojox.gfx._base._getTextBox(this.property("label"),ta.Annotation.labelFont);
};
p.setMode=function(m){
if(this.mode==m){
return;
}
this.mode=m;
var _27="disable";
if(m==ta.Annotation.Modes.Edit){
_27="enable";
}
if(_27=="enable"){
this.drawBBox();
this.figure._add(this);
}else{
if(this.boundingBox){
if(this.shape){
this.shape.remove(this.boundingBox);
}
this.boundingBox=null;
}
}
for(var p in this.anchors){
this.anchors[p][_27]();
}
};
p.writeCommonAttrs=function(){
return "id=\""+this.id+"\" dojoxsketch:type=\""+this.type()+"\""+" transform=\"translate("+this.transform.dx+","+this.transform.dy+")\""+(this.data?(" ><![CDATA[data:"+dojo.toJson(this.data)+"]]"):"");
};
p.readCommonAttrs=function(obj){
var i=0,cs=obj.childNodes,c;
while((c=cs[i++])){
if(c.nodeType==4){
if(c.nodeValue.substr(0,11)=="properties:"){
this._properties=dojo.fromJson(c.nodeValue.substr(11));
}else{
if(c.nodeValue.substr(0,5)=="data:"){
this.data=dojo.fromJson(c.nodeValue.substr(5));
}else{
console.error("unknown CDATA node in node ",obj);
}
}
}
}
if(obj.getAttribute("transform")){
var t=obj.getAttribute("transform").replace("translate(","");
var pt=t.split(",");
this.transform.dx=parseFloat(pt[0],10);
this.transform.dy=parseFloat(pt[1],10);
}
};
ta.Annotation.Modes={View:0,Edit:1};
ta.Annotation.labelFont={family:"Arial",size:"16px",weight:"bold"};
ta.Annotation.register=function(_2f){
var cls=ta[_2f+"Annotation"];
ta.registerTool(_2f,function(p){
dojo.mixin(p,{shape:_2f,annotation:cls});
return new ta.AnnotationTool(p);
});
};
})();
}
