/*
	Copyright (c) 2004-2008, The Dojo Foundation
	All Rights Reserved.

	Licensed under the Academic Free License version 2.1 or above OR the
	modified BSD license. For more information on Dojo licensing, see:

		http://dojotoolkit.org/book/dojo-book-0-9/introduction/licensing
*/


if(!dojo._hasResource["dojox.charting.plot2d.Default"]){
dojo._hasResource["dojox.charting.plot2d.Default"]=true;
dojo.provide("dojox.charting.plot2d.Default");
dojo.require("dojox.charting.plot2d.common");
dojo.require("dojox.charting.plot2d.Base");
dojo.require("dojox.lang.utils");
dojo.require("dojox.lang.functional");
dojo.require("dojox.lang.functional.reversed");
(function(){
var df=dojox.lang.functional,du=dojox.lang.utils,dc=dojox.charting.plot2d.common,_4=df.lambda("item.purgeGroup()");
dojo.declare("dojox.charting.plot2d.Default",dojox.charting.plot2d.Base,{defaultParams:{hAxis:"x",vAxis:"y",lines:true,areas:false,markers:false,shadows:0,tension:0},optionalParams:{},constructor:function(_5,_6){
this.opt=dojo.clone(this.defaultParams);
du.updateWithObject(this.opt,_6);
this.series=[];
this.hAxis=this.opt.hAxis;
this.vAxis=this.opt.vAxis;
},calculateAxes:function(_7){
this._calc(_7,dc.collectSimpleStats(this.series));
return this;
},render:function(_8,_9){
if(this.dirty){
dojo.forEach(this.series,_4);
this.cleanGroup();
var s=this.group;
df.forEachRev(this.series,function(_b){
_b.cleanGroup(s);
});
}
var t=this.chart.theme,_d,_e,_f,_10;
for(var i=this.series.length-1;i>=0;--i){
var run=this.series[i];
if(!this.dirty&&!run.dirty){
continue;
}
run.cleanGroup();
if(!run.data.length){
run.dirty=false;
continue;
}
function curve(arr,_14){
var p=dojo.map(arr,function(_16,i){
if(i==0){
return "M"+_16.x+","+_16.y;
}
var dx=_16.x-arr[i-1].x,dy=arr[i-1].y;
return "C"+(_16.x-(_14-1)*(dx/_14))+","+dy+" "+(_16.x-(dx/_14))+","+_16.y+" "+_16.x+","+_16.y;
});
return p.join(" ");
};
var s=run.group,_1a;
if(typeof run.data[0]=="number"){
_1a=dojo.map(run.data,function(v,i){
return {x:this._hScaler.scale*(i+1-this._hScaler.bounds.lower)+_9.l,y:_8.height-_9.b-this._vScaler.scale*(v-this._vScaler.bounds.lower)};
},this);
}else{
_1a=dojo.map(run.data,function(v,i){
return {x:this._hScaler.scale*(v.x-this._hScaler.bounds.lower)+_9.l,y:_8.height-_9.b-this._vScaler.scale*(v.y-this._vScaler.bounds.lower)};
},this);
}
if(!run.fill||!run.stroke){
_f=run.dyn.color=new dojo.Color(t.next("color"));
}
var _1f="";
if(this.opt.tension){
var _1f=curve(_1a,this.opt.tension);
}
if(this.opt.areas){
var _20=run.fill?run.fill:dc.augmentFill(t.series.fill,_f);
var _21=dojo.clone(_1a);
if(this.opt.tension){
var _22="L"+(_21[_21.length-1].x)+","+(_8.height-_9.b)+" "+"L"+_21[0].x+","+(_8.height-_9.b)+" "+"L"+_21[0].x+","+_21[0].y;
run.dyn.fill=s.createPath(_1f+" "+_22).setFill(_20).getFill();
}else{
_21.push({x:_1a[_1a.length-1].x,y:_8.height-_9.b});
_21.push({x:_1a[0].x,y:_8.height-_9.b});
_21.push(_1a[0]);
run.dyn.fill=s.createPolyline(_21).setFill(_20).getFill();
}
}
if(this.opt.lines||this.opt.markers){
_d=run.stroke?dc.makeStroke(run.stroke):dc.augmentStroke(t.series.stroke,_f);
if(run.outline||t.series.outline){
_e=dc.makeStroke(run.outline?run.outline:t.series.outline);
_e.width=2*_e.width+_d.width;
}
}
if(this.opt.markers){
_10=run.dyn.marker=run.marker?run.marker:t.next("marker");
}
if(this.opt.shadows&&_d){
var sh=this.opt.shadows,_24=new dojo.Color([0,0,0,0.3]),_25=dojo.map(_1a,function(c){
return {x:c.x+sh.dx,y:c.y+sh.dy};
}),_27=dojo.clone(_e?_e:_d);
_27.color=_24;
_27.width+=sh.dw?sh.dw:0;
if(this.opt.lines){
if(this.opt.tension){
s.createPath(curve(_25,this.opt.tension)).setStroke(_27);
}else{
s.createPolyline(_25).setStroke(_27);
}
}
if(this.opt.markers){
dojo.forEach(_25,function(c){
s.createPath("M"+c.x+" "+c.y+" "+_10).setStroke(_27).setFill(_24);
},this);
}
}
if(this.opt.lines){
if(_e){
if(this.opt.tension){
run.dyn.outline=s.createPath(_1f).setStroke(_e).getStroke();
}else{
run.dyn.outline=s.createPolyline(_1a).setStroke(_e).getStroke();
}
}
if(this.opt.tension){
run.dyn.stroke=s.createPath(_1f).setStroke(_d).getStroke();
}else{
run.dyn.stroke=s.createPolyline(_1a).setStroke(_d).getStroke();
}
}
if(this.opt.markers){
dojo.forEach(_1a,function(c){
var _2a="M"+c.x+" "+c.y+" "+_10;
if(_e){
s.createPath(_2a).setStroke(_e);
}
s.createPath(_2a).setStroke(_d).setFill(_d.color);
},this);
}
run.dirty=false;
}
this.dirty=false;
return this;
}});
})();
}
