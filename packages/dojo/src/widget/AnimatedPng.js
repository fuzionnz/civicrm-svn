/*
	Copyright (c) 2004-2006, The Dojo Foundation
	All Rights Reserved.

	Licensed under the Academic Free License version 2.1 or above OR the
	modified BSD license. For more information on Dojo licensing, see:

		http://dojotoolkit.org/community/licensing.shtml
*/


dojo.provide("dojo.widget.AnimatedPng");
dojo.require("dojo.widget.*");
dojo.require("dojo.widget.HtmlWidget");
dojo.widget.defineWidget("dojo.widget.AnimatedPng",dojo.widget.HtmlWidget,{isContainer:false,width:0,height:0,aniSrc:"",interval:100,_blankSrc:dojo.uri.moduleUri("dojo.widget","templates/images/blank.gif"),templateString:"<img class=\"dojoAnimatedPng\" />",postCreate:function(){
this.cellWidth=this.width;
this.cellHeight=this.height;
var _1=new Image();
var _2=this;
_1.onload=function(){
_2._initAni(_1.width,_1.height);
};
_1.src=this.aniSrc;
},_initAni:function(w,h){
this.domNode.src=this._blankSrc;
this.domNode.width=this.cellWidth;
this.domNode.height=this.cellHeight;
this.domNode.style.backgroundImage="url("+this.aniSrc+")";
this.domNode.style.backgroundRepeat="no-repeat";
this.aniCols=Math.floor(w/this.cellWidth);
this.aniRows=Math.floor(h/this.cellHeight);
this.aniCells=this.aniCols*this.aniRows;
this.aniFrame=0;
window.setInterval(dojo.lang.hitch(this,"_tick"),this.interval);
},_tick:function(){
this.aniFrame++;
if(this.aniFrame==this.aniCells){
this.aniFrame=0;
}
var _5=this.aniFrame%this.aniCols;
var _6=Math.floor(this.aniFrame/this.aniCols);
var bx=-1*_5*this.cellWidth;
var by=-1*_6*this.cellHeight;
this.domNode.style.backgroundPosition=bx+"px "+by+"px";
}});
