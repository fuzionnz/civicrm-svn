/*
	Copyright (c) 2004-2008, The Dojo Foundation
	All Rights Reserved.

	Licensed under the Academic Free License version 2.1 or above OR the
	modified BSD license. For more information on Dojo licensing, see:

		http://dojotoolkit.org/book/dojo-book-0-9/introduction/licensing
*/


if(!dojo._hasResource["dojox.layout.RadioGroup"]){
dojo._hasResource["dojox.layout.RadioGroup"]=true;
dojo.provide("dojox.layout.RadioGroup");
dojo.experimental("dojox.layout.RadioGroup");
dojo.require("dijit._Widget");
dojo.require("dijit._Templated");
dojo.require("dijit._Container");
dojo.require("dijit.layout.StackContainer");
dojo.require("dojox.fx.easing");
dojo.declare("dojox.layout.RadioGroup",[dijit.layout.StackContainer,dijit._Templated],{duration:750,hasButtons:true,templateString:"<div class=\"dojoxRadioGroup\">"+" \t<div dojoAttachPoint=\"buttonHolder\" style=\"display:none;\">"+"\t\t<table class=\"dojoxRadioButtons\"><tbody><tr class=\"dojoxRadioButtonRow\" dojoAttachPoint=\"buttonNode\"></tr></tbody></table>"+"\t</div>"+"\t<div class=\"dojoxRadioView\" dojoAttachPoint=\"containerNode\"></div>"+"</div>",startup:function(){
this.inherited("startup",arguments);
this._children=this.getChildren();
this._buttons=this._children.length;
this._size=dojo.coords(this.containerNode);
if(this.hasButtons){
dojo.style(this.buttonHolder,"display","block");
dojo.forEach(this._children,this._makeButton,this);
}
},_makeButton:function(n){
dojo.style(n.domNode,"position","absolute");
var _2=document.createElement("td");
this.buttonNode.appendChild(_2);
var _3=_2.appendChild(document.createElement("div"));
var _4=new dojox.layout._RadioButton({label:n.title,page:n},_3);
_4.startup();
},_transition:function(_5,_6){
this._showChild(_5);
if(_6){
this._hideChild(_6);
}
if(this.doLayout&&_5.resize){
_5.resize(this._containerContentBox||this._contentBox);
}
},_showChild:function(_7){
var _8=this.getChildren();
_7.isFirstChild=(_7==_8[0]);
_7.isLastChild=(_7==_8[_8.length-1]);
_7.selected=true;
_7.domNode.style.display="";
if(_7._loadCheck){
_7._loadCheck();
}
if(_7.onShow){
_7.onShow();
}
},_hideChild:function(_9){
_9.selected=false;
_9.domNode.style.display="none";
if(_9.onHide){
_9.onHide();
}
}});
dojo.declare("dojox.layout.RadioGroupFade",dojox.layout.RadioGroup,{_hideChild:function(_a){
dojo.fadeOut({node:_a.domNode,duration:this.duration,onEnd:this.inherited("_hideChild",arguments)}).play();
},_showChild:function(_b){
this.inherited("_showChild",arguments);
dojo.style(_b.domNode,"opacity",0);
dojo.fadeIn({node:_b.domNode,duration:this.duration}).play();
}});
dojo.declare("dojox.layout.RadioGroupSlide",dojox.layout.RadioGroup,{easing:dojox.fx.easing.easeOut,startup:function(){
this.inherited("startup",arguments);
dojo.forEach(this._children,this._positionChild,this);
},_positionChild:function(_c){
var rA=Math.round(Math.random());
var rB=Math.round(Math.random());
dojo.style(_c.domNode,rA?"top":"left",(rB?"-":"")+this._size[rA?"h":"w"]+"px");
},_showChild:function(_f){
this.inherited("_showChild",arguments);
if(this._anim&&this._anim.status()=="playing"){
this._anim.gotoPercent(100,true);
}
this._anim=dojo.animateProperty({node:_f.domNode,properties:{left:{end:0,unit:"px"},top:{end:0,unit:"px"}},duration:this.duration,easing:this.easing});
this._anim.play();
},_hideChild:function(_10){
this.inherited("_hideChild",arguments);
this._positionChild(_10);
}});
dojo.declare("dojox.layout._RadioButton",[dijit._Widget,dijit._Templated,dijit._Contained],{label:"",page:null,templateString:"<div dojoAttachPoint=\"focusNode\" class=\"dojoxRadioButton\"><span dojoAttachPoint=\"titleNode\" class=\"dojoxRadioButtonLabel\">${label}</span></div>",startup:function(){
this.connect(this.domNode,"onmouseover","_onMouse");
},_onMouse:function(e){
this.getParent().selectChild(this.page);
this._clearSelected();
dojo.addClass(this.domNode,"dojoxRadioButtonSelected");
},_clearSelected:function(){
dojo.query(".dojoxRadioButtonSelected",this.domNode.parentNode.parentNode).forEach(function(n){
dojo.removeClass(n,"dojoxRadioButtonSelected");
});
}});
}
