/*
	Copyright (c) 2004-2008, The Dojo Foundation
	All Rights Reserved.

	Licensed under the Academic Free License version 2.1 or above OR the
	modified BSD license. For more information on Dojo licensing, see:

		http://dojotoolkit.org/book/dojo-book-0-9/introduction/licensing
*/


if(!dojo._hasResource["dijit.form._DateTimeTextBox"]){
dojo._hasResource["dijit.form._DateTimeTextBox"]=true;
dojo.provide("dijit.form._DateTimeTextBox");
dojo.require("dojo.date");
dojo.require("dojo.date.locale");
dojo.require("dojo.date.stamp");
dojo.require("dijit.form.ValidationTextBox");
dojo.declare("dijit.form._DateTimeTextBox",dijit.form.RangeBoundTextBox,{regExpGen:dojo.date.locale.regexp,compare:dojo.date.compare,format:function(_1,_2){
if(!_1){
return "";
}
return dojo.date.locale.format(_1,_2);
},parse:function(_3,_4){
return dojo.date.locale.parse(_3,_4)||undefined;
},serialize:dojo.date.stamp.toISOString,value:new Date(""),popupClass:"",_selector:"",postMixInProperties:function(){
this.inherited(arguments);
if(!this.value||this.value.toString()==dijit.form._DateTimeTextBox.prototype.value.toString()){
this.value=undefined;
}
var _5=this.constraints;
_5.selector=this._selector;
_5.fullYear=true;
var _6=dojo.date.stamp.fromISOString;
if(typeof _5.min=="string"){
_5.min=_6(_5.min);
}
if(typeof _5.max=="string"){
_5.max=_6(_5.max);
}
},_onFocus:function(_7){
this._open();
},setValue:function(_8,_9,_a){
this.inherited(arguments);
if(this._picker){
if(!_8){
_8=new Date();
}
this._picker.setValue(_8);
}
},_open:function(){
if(this.disabled||this.readOnly||!this.popupClass){
return;
}
var _b=this;
if(!this._picker){
var _c=dojo.getObject(this.popupClass,false);
this._picker=new _c({onValueSelected:function(_d){
_b.focus();
setTimeout(dojo.hitch(_b,"_close"),1);
dijit.form._DateTimeTextBox.superclass.setValue.call(_b,_d,true);
},lang:_b.lang,constraints:_b.constraints,isDisabledDate:function(_e){
var _f=dojo.date.compare;
var _10=_b.constraints;
return _10&&(_10.min&&(_f(_10.min,_e,"date")>0)||(_10.max&&_f(_10.max,_e,"date")<0));
}});
this._picker.setValue(this.getValue()||new Date());
}
if(!this._opened){
dijit.popup.open({parent:this,popup:this._picker,around:this.domNode,onCancel:dojo.hitch(this,this._close),onClose:function(){
_b._opened=false;
}});
this._opened=true;
}
dojo.marginBox(this._picker.domNode,{w:this.domNode.offsetWidth});
},_close:function(){
if(this._opened){
dijit.popup.close(this._picker);
this._opened=false;
}
},_onBlur:function(){
this._close();
if(this._picker){
this._picker.destroy();
delete this._picker;
}
this.inherited(arguments);
},getDisplayedValue:function(){
return this.textbox.value;
},setDisplayedValue:function(_11,_12){
this.setValue(this.parse(_11,this.constraints),_12,_11);
},destroy:function(){
if(this._picker){
this._picker.destroy();
delete this._picker;
}
this.inherited(arguments);
},_onKeyPress:function(e){
if(dijit.form._DateTimeTextBox.superclass._onKeyPress.apply(this,arguments)){
if(this._opened&&e.keyCode==dojo.keys.ESCAPE&&!e.shiftKey&&!e.ctrlKey&&!e.altKey){
this._close();
dojo.stopEvent(e);
}
}
}});
}
