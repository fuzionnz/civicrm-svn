/*
	Copyright (c) 2004-2008, The Dojo Foundation
	All Rights Reserved.

	Licensed under the Academic Free License version 2.1 or above OR the
	modified BSD license. For more information on Dojo licensing, see:

		http://dojotoolkit.org/book/dojo-book-0-9/introduction/licensing
*/


if(!dojo._hasResource["dojox.charting.plot2d.Pie"]){
dojo._hasResource["dojox.charting.plot2d.Pie"]=true;
dojo.provide("dojox.charting.plot2d.Pie");
dojo.require("dojox.charting.Element");
dojo.require("dojox.charting.axis2d.common");
dojo.require("dojox.charting.plot2d.common");
dojo.require("dojox.lang.functional");
dojo.require("dojox.gfx");
(function(){
var df=dojox.lang.functional,du=dojox.lang.utils,dc=dojox.charting.plot2d.common,da=dojox.charting.axis2d.common,g=dojox.gfx;
dojo.declare("dojox.charting.plot2d.Pie",dojox.charting.Element,{defaultParams:{labels:true,ticks:false,fixed:true,precision:1,labelOffset:20,labelStyle:"default",htmlLabels:true},optionalParams:{font:"",fontColor:"",radius:0},constructor:function(_6,_7){
this.opt=dojo.clone(this.defaultParams);
du.updateWithObject(this.opt,_7);
du.updateWithPattern(this.opt,_7,this.optionalParams);
this.run=null;
this.dyn=[];
},clear:function(){
this.dirty=true;
this.dyn=[];
return this;
},setAxis:function(_8){
return this;
},addSeries:function(_9){
this.run=_9;
return this;
},calculateAxes:function(_a){
return this;
},getRequiredColors:function(){
return this.run?this.run.data.length:0;
},render:function(_b,_c){
if(!this.dirty){
return this;
}
this.dirty=false;
this.cleanGroup();
var s=this.group,_e,t=this.chart.theme;
var rx=(_b.width-_c.l-_c.r)/2,ry=(_b.height-_c.t-_c.b)/2,r=Math.min(rx,ry),_13="font" in this.opt?this.opt.font:t.axis.font,_14=_13?g.normalizedLength(g.splitFontString(_13).size):0,_15="fontColor" in this.opt?this.opt.fontColor:t.axis.fontColor,_16=0,_17,sum,_19,_1a,_1b,_1c,run=this.run.data;
if(typeof run[0]=="number"){
sum=df.foldl1(run,"+");
_19=dojo.map(run,function(x){
return x/sum;
});
if(this.opt.labels){
_1a=dojo.map(_19,function(x){
return this._getLabel(x*100)+"%";
},this);
}
}else{
sum=df.foldl1(run,function(a,b){
return {y:a.y+b.y};
}).y;
_19=df.map(run,function(x){
return x.y/sum;
});
if(this.opt.labels){
_1a=dojo.map(_19,function(x,i){
var v=run[i];
return "text" in v?v.text:this._getLabel(x*100)+"%";
},this);
}
}
if(this.opt.labels){
_1b=df.foldl1(df.map(_1a,function(_26){
return dojox.gfx._base._getTextBox(_26,{font:_13}).w;
},this),"Math.max(a, b)")/2;
if(this.opt.labelOffset<0){
r=Math.min(rx-2*_1b,ry-_14)+this.opt.labelOffset;
}
_1c=r-this.opt.labelOffset;
}
if("radius" in this.opt){
r=this.opt.radius;
_1c=r-this.opt.labelOffset;
}
var _27={cx:_c.l+rx,cy:_c.t+ry,r:r};
this.dyn=[];
if(!this.run||!run.length){
return this;
}
if(run.length==1){
_e=new dojo.Color(t.next("color"));
var _28=s.createCircle(_27).setFill(dc.augmentFill(t.run.fill,_e)).setStroke(dc.augmentStroke(t.series.stroke,_e));
this.dyn.push({color:_e,fill:_28.getFill(),stroke:_28.getStroke()});
if(this.opt.labels){
var _29=da.createText[this.opt.htmlLabels&&dojox.gfx.renderer!="vml"?"html":"gfx"](this.chart,s,_27.cx,_27.cy+_14/2,"middle","100%",_13,_15);
if(this.opt.htmlLabels){
this.htmlElements.push(_29);
}
}
return this;
}
dojo.forEach(_19,function(x,i){
var end=_16+x*2*Math.PI,v=run[i];
if(i+1==_19.length){
end=2*Math.PI;
}
var _2e=end-_16,x1=_27.cx+r*Math.cos(_16),y1=_27.cy+r*Math.sin(_16),x2=_27.cx+r*Math.cos(end),y2=_27.cy+r*Math.sin(end);
var _33,_34,_35;
if(typeof v=="object"){
_33="color" in v?v.color:new dojo.Color(t.next("color"));
_34="fill" in v?v.fill:dc.augmentFill(t.series.fill,_33);
_35="stroke" in v?v.stroke:dc.augmentStroke(t.series.stroke,_33);
}else{
_33=new dojo.Color(t.next("color"));
_34=dc.augmentFill(t.series.fill,_33);
_35=dc.augmentStroke(t.series.stroke,_33);
}
var _36=s.createPath({}).moveTo(_27.cx,_27.cy).lineTo(x1,y1).arcTo(r,r,0,_2e>Math.PI,true,x2,y2).lineTo(_27.cx,_27.cy).closePath().setFill(_34).setStroke(_35);
this.dyn.push({color:_33,fill:_34,stroke:_35});
_16=end;
},this);
if(this.opt.labels){
_16=0;
dojo.forEach(_19,function(_37,i){
var end=_16+_37*2*Math.PI,v=run[i];
if(i+1==_19.length){
end=2*Math.PI;
}
var _3b=(_16+end)/2,x=_27.cx+_1c*Math.cos(_3b),y=_27.cy+_1c*Math.sin(_3b)+_14/2;
var _3e=da.createText[this.opt.htmlLabels&&dojox.gfx.renderer!="vml"?"html":"gfx"](this.chart,s,x,y,"middle",_1a[i],_13,(typeof v=="object"&&"fontColor" in v)?v.fontColor:_15);
if(this.opt.htmlLabels){
this.htmlElements.push(_3e);
}
_16=end;
},this);
}
return this;
},_getLabel:function(_3f){
return this.opt.fixed?_3f.toFixed(this.opt.precision):_3f.toString();
}});
})();
}
