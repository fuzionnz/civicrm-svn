/*
	Copyright (c) 2004-2008, The Dojo Foundation
	All Rights Reserved.

	Licensed under the Academic Free License version 2.1 or above OR the
	modified BSD license. For more information on Dojo licensing, see:

		http://dojotoolkit.org/book/dojo-book-0-9/introduction/licensing
*/


if(!dojo._hasResource["dojox.charting.axis2d.Default"]){
dojo._hasResource["dojox.charting.axis2d.Default"]=true;
dojo.provide("dojox.charting.axis2d.Default");
dojo.require("dojox.charting.scaler");
dojo.require("dojox.charting.axis2d.common");
dojo.require("dojox.charting.axis2d.Base");
dojo.require("dojo.colors");
dojo.require("dojox.gfx");
dojo.require("dojox.lang.functional");
dojo.require("dojox.lang.utils");
(function(){
var dc=dojox.charting,df=dojox.lang.functional,du=dojox.lang.utils,g=dojox.gfx,_5=4;
var eq=function(a,b){
return Math.abs(a-b)<=0.000001*(Math.abs(a)+Math.abs(b));
};
dojo.declare("dojox.charting.axis2d.Default",dojox.charting.axis2d.Base,{defaultParams:{vertical:false,fixUpper:"none",fixLower:"none",natural:false,leftBottom:true,includeZero:false,fixed:true,majorLabels:true,minorTicks:true,minorLabels:true,microTicks:false,htmlLabels:true},optionalParams:{"min":0,"max":1,"majorTickStep":4,"minorTickStep":2,"microTickStep":1,"labels":[],"stroke":{},"majorTick":{},"minorTick":{},"font":"","fontColor":""},constructor:function(_9,_a){
this.opt=dojo.clone(this.defaultParams);
du.updateWithObject(this.opt,_a);
du.updateWithPattern(this.opt,_a,this.optionalParams);
},dependOnData:function(){
return !("min" in this.opt)||!("max" in this.opt);
},clear:function(){
delete this.scaler;
this.dirty=true;
return this;
},initialized:function(){
return "scaler" in this;
},calculate:function(_b,_c,_d,_e){
if(this.initialized()){
return this;
}
this.labels="labels" in this.opt?this.opt.labels:_e;
if("min" in this.opt){
_b=this.opt.min;
}
if("max" in this.opt){
_c=this.opt.max;
}
if(this.opt.includeZero){
if(_b>0){
_b=0;
}
if(_c<0){
_c=0;
}
}
var _f=0,ta=this.chart.theme.axis,_11="font" in this.opt?this.opt.font:ta.font,_12=_11?g.normalizedLength(g.splitFontString(_11).size):0;
if(this.vertical){
if(_12){
_f=_12+_5;
}
}else{
if(_12){
var _13,i;
if(this.labels){
_13=df.foldl(df.map(this.labels,function(_15){
return dojox.gfx._base._getTextBox(_15.text,{font:_11}).w;
}),"Math.max(a, b)",0);
}else{
var _16=Math.ceil(Math.log(Math.max(Math.abs(_b),Math.abs(_c)))/Math.LN10),t=[];
if(_b<0||_c<0){
t.push("-");
}
for(i=0;i<_16;++i){
t.push("9");
}
var _18=Math.floor(Math.log(_c-_b)/Math.LN10);
if(_18>0){
t.push(".");
for(i=0;i<_18;++i){
t.push("9");
}
}
_13=dojox.gfx._base._getTextBox(t.join(""),{font:_11}).w;
}
_f=_13+_5;
}
}
var _19={fixUpper:this.opt.fixUpper,fixLower:this.opt.fixLower,natural:this.opt.natural};
if("majorTickStep" in this.opt){
_19.majorTick=this.opt.majorTickStep;
}
if("minorTickStep" in this.opt){
_19.minorTick=this.opt.minorTickStep;
}
if("microTickStep" in this.opt){
_19.microTick=this.opt.microTickStep;
}
this.scaler=dojox.charting.scaler(_b,_c,_d,_19);
this.scaler.minMinorStep=_f;
return this;
},getScaler:function(){
return this.scaler;
},getOffsets:function(){
var _1a={l:0,r:0,t:0,b:0},s,_1c,gtb,a,b,c,d;
var _22=0,ta=this.chart.theme.axis,_24="font" in this.opt?this.opt.font:ta.font,_25="majorTick" in this.opt?this.opt.majorTick:ta.majorTick,_26="minorTick" in this.opt?this.opt.minorTick:ta.minorTick,_27=_24?g.normalizedLength(g.splitFontString(_24).size):0;
if(this.vertical){
if(_27){
s=this.scaler;
if(this.labels){
_1c=df.foldl(df.map(this.labels,function(_28){
return dojox.gfx._base._getTextBox(_28.text,{font:_24}).w;
}),"Math.max(a, b)",0);
}else{
gtb=dojox.gfx._base._getTextBox;
a=gtb(this._getLabel(s.major.start,s.major.prec),{font:_24}).w;
b=gtb(this._getLabel(s.major.start+s.major.count*s.major.tick,s.major.prec),{font:_24}).w;
c=gtb(this._getLabel(s.minor.start,s.minor.prec),{font:_24}).w;
d=gtb(this._getLabel(s.minor.start+s.minor.count*s.minor.tick,s.minor.prec),{font:_24}).w;
_1c=Math.max(a,b,c,d);
}
_22=_1c+_5;
}
_22+=_5+Math.max(_25.length,_26.length);
_1a[this.opt.leftBottom?"l":"r"]=_22;
_1a.t=_1a.b=_27/2;
}else{
if(_27){
_22=_27+_5;
}
_22+=_5+Math.max(_25.length,_26.length);
_1a[this.opt.leftBottom?"b":"t"]=_22;
if(_27){
s=this.scaler;
if(this.labels){
_1c=df.foldl(df.map(this.labels,function(_29){
return dojox.gfx._base._getTextBox(_29.text,{font:_24}).w;
}),"Math.max(a, b)",0);
}else{
gtb=dojox.gfx._base._getTextBox;
a=gtb(this._getLabel(s.major.start,s.major.prec),{font:_24}).w;
b=gtb(this._getLabel(s.major.start+s.major.count*s.major.tick,s.major.prec),{font:_24}).w;
c=gtb(this._getLabel(s.minor.start,s.minor.prec),{font:_24}).w;
d=gtb(this._getLabel(s.minor.start+s.minor.count*s.minor.tick,s.minor.prec),{font:_24}).w;
_1c=Math.max(a,b,c,d);
}
_1a.l=_1a.r=_1c/2;
}
}
return _1a;
},render:function(dim,_2b){
if(!this.dirty){
return this;
}
var _2c,_2d,_2e,_2f,_30,_31,ta=this.chart.theme.axis,_33="stroke" in this.opt?this.opt.stroke:ta.stroke,_34="majorTick" in this.opt?this.opt.majorTick:ta.majorTick,_35="minorTick" in this.opt?this.opt.minorTick:ta.minorTick,_36="font" in this.opt?this.opt.font:ta.font,_37="fontColor" in this.opt?this.opt.fontColor:ta.fontColor,_38=Math.max(_34.length,_35.length),_39=_36?g.normalizedLength(g.splitFontString(_36).size):0;
if(this.vertical){
_2c={y:dim.height-_2b.b};
_2d={y:_2b.t};
_2e={x:0,y:-1};
if(this.opt.leftBottom){
_2c.x=_2d.x=_2b.l;
_2f={x:-1,y:0};
_31="end";
}else{
_2c.x=_2d.x=dim.width-_2b.r;
_2f={x:1,y:0};
_31="start";
}
_30={x:_2f.x*(_38+_5),y:_39*0.4};
}else{
_2c={x:_2b.l};
_2d={x:dim.width-_2b.r};
_2e={x:1,y:0};
_31="middle";
if(this.opt.leftBottom){
_2c.y=_2d.y=dim.height-_2b.b;
_2f={x:0,y:1};
_30={y:_38+_5+_39};
}else{
_2c.y=_2d.y=_2b.t;
_2f={x:0,y:-1};
_30={y:-_38-_5};
}
_30.x=0;
}
this.cleanGroup();
var s=this.group,c=this.scaler,_3c,_3d,_3e=c.major.start,_3f=c.minor.start,_40=c.micro.start;
s.createLine({x1:_2c.x,y1:_2c.y,x2:_2d.x,y2:_2d.y}).setStroke(_33);
if(this.opt.microTicks&&c.micro.tick){
_3c=c.micro.tick,_3d=_40;
}else{
if(this.opt.minorTicks&&c.minor.tick){
_3c=c.minor.tick,_3d=_3f;
}else{
if(c.major.tick){
_3c=c.major.tick,_3d=_3e;
}else{
return this;
}
}
}
while(_3d<=c.bounds.upper+1/c.scale){
var _41=(_3d-c.bounds.lower)*c.scale,x=_2c.x+_2e.x*_41,y=_2c.y+_2e.y*_41,_44;
if(Math.abs(_3e-_3d)<_3c/2){
s.createLine({x1:x,y1:y,x2:x+_2f.x*_34.length,y2:y+_2f.y*_34.length}).setStroke(_34);
if(this.opt.majorLabels){
_44=dc.axis2d.common.createText[this.opt.htmlLabels&&dojox.gfx.renderer!="vml"?"html":"gfx"](this.chart,s,x+_30.x,y+_30.y,_31,this._getLabel(_3e,c.major.prec),_36,_37);
if(this.opt.htmlLabels){
this.htmlElements.push(_44);
}
}
_3e+=c.major.tick;
_3f+=c.minor.tick;
_40+=c.micro.tick;
}else{
if(Math.abs(_3f-_3d)<_3c/2){
if(this.opt.minorTicks){
s.createLine({x1:x,y1:y,x2:x+_2f.x*_35.length,y2:y+_2f.y*_35.length}).setStroke(_35);
if(this.opt.minorLabels&&(c.minMinorStep<=c.minor.tick*c.scale)){
_44=dc.axis2d.common.createText[this.opt.htmlLabels&&dojox.gfx.renderer!="vml"?"html":"gfx"](this.chart,s,x+_30.x,y+_30.y,_31,this._getLabel(_3f,c.minor.prec),_36,_37);
if(this.opt.htmlLabels){
this.htmlElements.push(_44);
}
}
}
_3f+=c.minor.tick;
_40+=c.micro.tick;
}else{
if(this.opt.microTicks){
s.createLine({x1:x,y1:y,x2:x+_2f.x*_35.length,y2:y+_2f.y*_35.length}).setStroke(_35);
}
_40+=c.micro.tick;
}
}
_3d+=_3c;
}
this.dirty=false;
return this;
},_getLabel:function(_45,_46){
if(this.opt.labels){
var l=this.opt.labels,lo=0,hi=l.length;
while(lo<hi){
var mid=Math.floor((lo+hi)/2),val=l[mid].value;
if(val<_45){
lo=mid+1;
}else{
hi=mid;
}
}
if(lo<l.length&&eq(l[lo].value,_45)){
return l[lo].text;
}
--lo;
if(lo<l.length&&eq(l[lo].value,_45)){
return l[lo].text;
}
lo+=2;
if(lo<l.length&&eq(l[lo].value,_45)){
return l[lo].text;
}
}
return this.opt.fixed?_45.toFixed(_46<0?-_46:0):_45.toString();
}});
})();
}
