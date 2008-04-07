/*
	Copyright (c) 2004-2008, The Dojo Foundation
	All Rights Reserved.

	Licensed under the Academic Free License version 2.1 or above OR the
	modified BSD license. For more information on Dojo licensing, see:

		http://dojotoolkit.org/book/dojo-book-0-9/introduction/licensing
*/


if(!dojo._hasResource["dijit.ProgressBar"]){
dojo._hasResource["dijit.ProgressBar"]=true;
dojo.provide("dijit.ProgressBar");
dojo.require("dojo.fx");
dojo.require("dojo.number");
dojo.require("dijit._Widget");
dojo.require("dijit._Templated");
dojo.declare("dijit.ProgressBar",[dijit._Widget,dijit._Templated],{progress:"0",maximum:100,places:0,indeterminate:false,templateString:"<div class=\"dijitProgressBar dijitProgressBarEmpty\"\n\t><div waiRole=\"progressbar\" tabindex=\"0\" dojoAttachPoint=\"internalProgress\" class=\"dijitProgressBarFull\"\n\t\t><div class=\"dijitProgressBarTile\"></div\n\t\t><span style=\"visibility:hidden\">&nbsp;</span\n\t></div\n\t><div dojoAttachPoint=\"label\" class=\"dijitProgressBarLabel\" id=\"${id}_label\">&nbsp;</div\n\t><img dojoAttachPoint=\"inteterminateHighContrastImage\" class=\"dijitProgressBarIndeterminateHighContrastImage\"\n\t></img\n></div>\n",_indeterminateHighContrastImagePath:dojo.moduleUrl("dijit","themes/a11y/indeterminate_progress.gif"),postCreate:function(){
this.inherited("postCreate",arguments);
this.inteterminateHighContrastImage.setAttribute("src",this._indeterminateHighContrastImagePath);
this.update();
},update:function(_1){
dojo.mixin(this,_1||{});
var _2=1,_3;
if(this.indeterminate){
_3="addClass";
dijit.removeWaiState(this.internalProgress,"valuenow");
dijit.removeWaiState(this.internalProgress,"valuemin");
dijit.removeWaiState(this.internalProgress,"valuemax");
}else{
_3="removeClass";
if(String(this.progress).indexOf("%")!=-1){
_2=Math.min(parseFloat(this.progress)/100,1);
this.progress=_2*this.maximum;
}else{
this.progress=Math.min(this.progress,this.maximum);
_2=this.progress/this.maximum;
}
var _4=this.report(_2);
this.label.firstChild.nodeValue=_4;
dijit.setWaiState(this.internalProgress,"describedby",this.label.id);
dijit.setWaiState(this.internalProgress,"valuenow",this.progress);
dijit.setWaiState(this.internalProgress,"valuemin",0);
dijit.setWaiState(this.internalProgress,"valuemax",this.maximum);
}
dojo[_3](this.domNode,"dijitProgressBarIndeterminate");
this.internalProgress.style.width=(_2*100)+"%";
this.onChange();
},report:function(_5){
return dojo.number.format(_5,{type:"percent",places:this.places,locale:this.lang});
},onChange:function(){
}});
}
