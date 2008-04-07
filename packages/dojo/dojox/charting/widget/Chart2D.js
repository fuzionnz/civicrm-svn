/*
	Copyright (c) 2004-2008, The Dojo Foundation
	All Rights Reserved.

	Licensed under the Academic Free License version 2.1 or above OR the
	modified BSD license. For more information on Dojo licensing, see:

		http://dojotoolkit.org/book/dojo-book-0-9/introduction/licensing
*/


if(!dojo._hasResource["dojox.charting.widget.Chart2D"]){
dojo._hasResource["dojox.charting.widget.Chart2D"]=true;
dojo.provide("dojox.charting.widget.Chart2D");
dojo.require("dijit._Widget");
dojo.require("dojox.charting.Chart2D");
dojo.require("dojox.lang.functional");
(function(){
var _1,_2,_3,_4=function(o){
return o;
},df=dojox.lang.functional,du=dojox.lang.utils;
dojo.declare("dojox.charting.widget.Chart2D",dijit._Widget,{theme:null,margins:null,stroke:null,fill:null,buildRendering:function(){
var n=this.domNode=this.srcNodeRef;
var _9=dojo.filter(dojo.query("> .axis",n).map(_1),_4);
var _a=dojo.filter(dojo.query("> .plot",n).map(_2),_4);
var _b=dojo.filter(dojo.query("> .series",n).map(_3),_4);
n.innerHTML="";
var c=this.chart=new dojox.charting.Chart2D(n,{margins:this.margins,stroke:this.stroke,fill:this.fill});
if(this.theme){
c.setTheme(this.theme);
}
dojo.forEach(_9,function(_d){
c.addAxis(_d.name,_d.kwArgs);
});
dojo.forEach(_a,function(_e){
c.addPlot(_e.name,_e.kwArgs);
});
var _f=df.foldl(_b,function(_10,_11){
if(_11.type=="data"){
c.addSeries(_11.name,_11.data,_11.kwArgs);
_10=true;
}else{
c.addSeries(_11.name,[0],_11.kwArgs);
var kw={};
du.updateWithPattern(kw,_11.kwArgs,{"query":"","queryOptions":null,"start":0,"count":1},true);
if(_11.kwArgs.sort){
kw.sort=dojo.clone(_11.kwArgs.sort);
}
dojo.mixin(kw,{onComplete:function(_13){
var _14;
if("valueFn" in _11.kwArgs){
var fn=_11.kwArgs.valueFn;
_14=dojo.map(_13,function(x){
return fn(_11.data.getValue(x,_11.field,0));
});
}else{
_14=dojo.map(_13,function(x){
return _11.data.getValue(x,_11.field,0);
});
}
c.addSeries(_11.name,_14,_11.kwArgs).render();
}});
_11.data.fetch(kw);
}
return _10;
},false);
if(_f){
c.render();
}
},resize:function(box){
dojo.marginBox(this.domNode,box);
this.chart.resize();
}});
_1=function(_19){
var _1a=_19.getAttribute("name"),_1b=_19.getAttribute("type");
if(!_1a){
return null;
}
var o={name:_1a,kwArgs:{}},kw=o.kwArgs;
if(_1b){
if(dojox.charting.axis2d[_1b]){
_1b=dojox._scopeName+".charting.axis2d."+_1b;
}
var _1e=eval("("+_1b+")");
if(_1e){
kw.type=_1e;
}
}else{
_1b=dojox._scopeName+".charting.axis2d.Default";
}
var dp=eval("("+_1b+".prototype.defaultParams)");
for(var x in dp){
if(x in kw){
continue;
}
var _21=_19.getAttribute(x);
kw[x]=du.coerceType(dp[x],_21==null?dp[x]:_21);
}
var op=eval("("+_1b+".prototype.optionalParams)");
for(var x in op){
if(x in kw){
continue;
}
var _21=_19.getAttribute(x);
if(_21!=null){
kw[x]=du.coerceType(op[x],_21);
}
}
return o;
};
_2=function(_23){
var _24=_23.getAttribute("name"),_25=_23.getAttribute("type");
if(!_24){
return null;
}
var o={name:_24,kwArgs:{}},kw=o.kwArgs;
if(_25){
if(dojox.charting.plot2d[_25]){
_25=dojox._scopeName+".charting.plot2d."+_25;
}
var _28=eval("("+_25+")");
if(_28){
kw.type=_28;
}
}else{
_25=dojox._scopeName+".charting.plot2d.Default";
}
var dp=eval("("+_25+".prototype.defaultParams)");
for(var x in dp){
if(x in kw){
continue;
}
var _2b=_23.getAttribute(x);
kw[x]=du.coerceType(dp[x],_2b==null?dp[x]:_2b);
}
var op=eval("("+_25+".prototype.optionalParams)");
for(var x in op){
if(x in kw){
continue;
}
var _2b=_23.getAttribute(x);
if(_2b!=null){
kw[x]=du.coerceType(op[x],_2b);
}
}
return o;
};
_3=function(_2d){
var _2e=_2d.getAttribute("name");
if(!_2e){
return null;
}
var o={name:_2e,kwArgs:{}},kw=o.kwArgs,t;
t=_2d.getAttribute("plot");
if(t!=null){
kw.plot=t;
}
t=_2d.getAttribute("marker");
if(t!=null){
kw.marker=t;
}
t=_2d.getAttribute("stroke");
if(t!=null){
kw.stroke=eval("("+t+")");
}
t=_2d.getAttribute("fill");
if(t!=null){
kw.fill=eval("("+t+")");
}
t=_2d.getAttribute("data");
if(t!=null){
o.type="data";
o.data=dojo.map(String(t).split(","),Number);
return o;
}
t=_2d.getAttribute("array");
if(t!=null){
o.type="data";
o.data=eval("("+t+")");
return o;
}
t=_2d.getAttribute("store");
if(t!=null){
o.type="store";
o.data=eval("("+t+")");
t=_2d.getAttribute("field");
o.field=t!=null?t:"value";
t=_2d.getAttribute("query");
if(!!t){
kw.query=t;
}
t=_2d.getAttribute("queryOptions");
if(!!t){
kw.queryOptions=eval("("+t+")");
}
t=_2d.getAttribute("start");
if(!!t){
kw.start=Number(t);
}
t=_2d.getAttribute("count");
if(!!t){
kw.count=Number(t);
}
t=_2d.getAttribute("sort");
if(!!t){
kw.sort=eval("("+t+")");
}
t=_2d.getAttribute("valueFn");
if(!!t){
kw.valueFn=df.lambda(t);
}
return o;
}
return null;
};
})();
}
