/*
	Copyright (c) 2004-2008, The Dojo Foundation
	All Rights Reserved.

	Licensed under the Academic Free License version 2.1 or above OR the
	modified BSD license. For more information on Dojo licensing, see:

		http://dojotoolkit.org/book/dojo-book-0-9/introduction/licensing
*/


if(!dojo._hasResource["dijit.form.Button"]){
dojo._hasResource["dijit.form.Button"]=true;
dojo.provide("dijit.form.Button");
dojo.require("dijit.form._FormWidget");
dojo.require("dijit._Container");
dojo.declare("dijit.form.Button",dijit.form._FormWidget,{label:"",showLabel:true,iconClass:"",type:"button",baseClass:"dijitButton",templateString:"<div class=\"dijit dijitReset dijitLeft dijitInline\"\n\tdojoAttachEvent=\"onclick:_onButtonClick,onmouseenter:_onMouse,onmouseleave:_onMouse,onmousedown:_onMouse\"\n\twaiRole=\"presentation\"\n\t><button class=\"dijitReset dijitStretch dijitButtonNode dijitButtonContents\" dojoAttachPoint=\"focusNode,titleNode\"\n\t\ttype=\"${type}\" waiRole=\"button\" waiState=\"labelledby-${id}_label\"\n\t\t><span class=\"dijitReset dijitInline ${iconClass}\" dojoAttachPoint=\"iconNode\" \n \t\t\t><span class=\"dijitReset dijitToggleButtonIconChar\">&#10003;</span \n\t\t></span\n\t\t><div class=\"dijitReset dijitInline\"><center class=\"dijitReset dijitButtonText\" id=\"${id}_label\" dojoAttachPoint=\"containerNode\">${label}</center></div\n\t></button\n></div>\n",_onChangeMonitor:"",_onClick:function(e){
if(this.disabled||this.readOnly){
dojo.stopEvent(e);
return false;
}
this._clicked();
return this.onClick(e);
},_onButtonClick:function(e){
if(this._onClick(e)===false){
dojo.stopEvent(e);
}else{
if(this.type=="submit"&&!this.focusNode.form){
for(var _3=this.domNode;_3.parentNode;_3=_3.parentNode){
var _4=dijit.byNode(_3);
if(_4&&typeof _4._onSubmit=="function"){
_4._onSubmit(e);
break;
}
}
}
}
},postCreate:function(){
if(this.showLabel==false){
var _5="";
this.label=this.containerNode.innerHTML;
_5=dojo.trim(this.containerNode.innerText||this.containerNode.textContent||"");
this.titleNode.title=_5;
dojo.addClass(this.containerNode,"dijitDisplayNone");
}
dojo.setSelectable(this.focusNode,false);
this.inherited(arguments);
},onClick:function(e){
return true;
},_clicked:function(e){
},setLabel:function(_8){
this.containerNode.innerHTML=this.label=_8;
this._layoutHack();
if(this.showLabel==false){
this.titleNode.title=dojo.trim(this.containerNode.innerText||this.containerNode.textContent||"");
}
}});
dojo.declare("dijit.form.DropDownButton",[dijit.form.Button,dijit._Container],{baseClass:"dijitDropDownButton",templateString:"<div class=\"dijit dijitReset dijitLeft dijitInline\"\n\tdojoAttachEvent=\"onmouseenter:_onMouse,onmouseleave:_onMouse,onmousedown:_onMouse,onclick:_onDropDownClick,onkeydown:_onDropDownKeydown,onblur:_onDropDownBlur,onkeypress:_onKey\"\n\twaiRole=\"presentation\"\n\t><div class='dijitReset dijitRight' waiRole=\"presentation\"\n\t><button class=\"dijitReset dijitStretch dijitButtonNode dijitButtonContents\" type=\"${type}\"\n\t\tdojoAttachPoint=\"focusNode,titleNode\" waiRole=\"button\" waiState=\"haspopup-true,labelledby-${id}_label\"\n\t\t><div class=\"dijitReset dijitInline ${iconClass}\" dojoAttachPoint=\"iconNode\" waiRole=\"presentation\"></div\n\t\t><div class=\"dijitReset dijitInline dijitButtonText\"  dojoAttachPoint=\"containerNode,popupStateNode\" waiRole=\"presentation\"\n\t\t\tid=\"${id}_label\">${label}</div\n\t\t><div class=\"dijitReset dijitInline dijitArrowButtonInner\" waiRole=\"presentation\">&thinsp;</div\n\t\t><div class=\"dijitReset dijitInline dijitArrowButtonChar\" waiRole=\"presentation\">&#9660;</div\n\t></button\n></div></div>\n",_fillContent:function(){
if(this.srcNodeRef){
var _9=dojo.query("*",this.srcNodeRef);
dijit.form.DropDownButton.superclass._fillContent.call(this,_9[0]);
this.dropDownContainer=this.srcNodeRef;
}
},startup:function(){
if(this._started){
return;
}
if(!this.dropDown){
var _a=dojo.query("[widgetId]",this.dropDownContainer)[0];
this.dropDown=dijit.byNode(_a);
delete this.dropDownContainer;
}
dijit.popup.prepare(this.dropDown.domNode);
this.inherited(arguments);
},destroyDescendants:function(){
if(this.dropDown){
this.dropDown.destroyRecursive();
delete this.dropDown;
}
this.inherited(arguments);
},_onArrowClick:function(e){
if(this.disabled||this.readOnly){
return;
}
this._toggleDropDown();
},_onDropDownClick:function(e){
var _d=dojo.isFF&&dojo.isFF<3&&navigator.appVersion.indexOf("Macintosh")!=-1;
if(!_d||e.detail!=0||this._seenKeydown){
this._onArrowClick(e);
}
this._seenKeydown=false;
},_onDropDownKeydown:function(e){
this._seenKeydown=true;
},_onDropDownBlur:function(e){
this._seenKeydown=false;
},_onKey:function(e){
if(this.disabled||this.readOnly){
return;
}
if(e.keyCode==dojo.keys.DOWN_ARROW){
if(!this.dropDown||this.dropDown.domNode.style.visibility=="hidden"){
dojo.stopEvent(e);
this._toggleDropDown();
}
}
},_onBlur:function(){
this._closeDropDown();
this.inherited(arguments);
},_toggleDropDown:function(){
if(this.disabled||this.readOnly){
return;
}
dijit.focus(this.popupStateNode);
var _11=this.dropDown;
if(!_11){
return;
}
if(!this._opened){
if(_11.href&&!_11.isLoaded){
var _12=this;
var _13=dojo.connect(_11,"onLoad",function(){
dojo.disconnect(_13);
_12._openDropDown();
});
_11._loadCheck(true);
return;
}else{
this._openDropDown();
}
}else{
this._closeDropDown();
}
},_openDropDown:function(){
var _14=this.dropDown;
var _15=_14.domNode.style.width;
var _16=this;
dijit.popup.open({parent:this,popup:_14,around:this.domNode,orient:this.isLeftToRight()?{"BL":"TL","BR":"TR","TL":"BL","TR":"BR"}:{"BR":"TR","BL":"TL","TR":"BR","TL":"BL"},onExecute:function(){
_16._closeDropDown(true);
},onCancel:function(){
_16._closeDropDown(true);
},onClose:function(){
_14.domNode.style.width=_15;
_16.popupStateNode.removeAttribute("popupActive");
this._opened=false;
}});
if(this.domNode.offsetWidth>_14.domNode.offsetWidth){
var _17=null;
if(!this.isLeftToRight()){
_17=_14.domNode.parentNode;
var _18=_17.offsetLeft+_17.offsetWidth;
}
dojo.marginBox(_14.domNode,{w:this.domNode.offsetWidth});
if(_17){
_17.style.left=_18-this.domNode.offsetWidth+"px";
}
}
this.popupStateNode.setAttribute("popupActive","true");
this._opened=true;
if(_14.focus){
_14.focus();
}
},_closeDropDown:function(_19){
if(this._opened){
dijit.popup.close(this.dropDown);
if(_19){
this.focus();
}
this._opened=false;
}
}});
dojo.declare("dijit.form.ComboButton",dijit.form.DropDownButton,{templateString:"<table class='dijit dijitReset dijitInline dijitLeft'\n\tcellspacing='0' cellpadding='0' waiRole=\"presentation\"\n\t><tbody waiRole=\"presentation\"><tr waiRole=\"presentation\"\n\t\t><td\tclass=\"dijitReset dijitStretch dijitButtonContents dijitButtonNode\"\n\t\t\ttabIndex=\"${tabIndex}\"\n\t\t\tdojoAttachEvent=\"ondijitclick:_onButtonClick,onmouseenter:_onMouse,onmouseleave:_onMouse,onmousedown:_onMouse\"  dojoAttachPoint=\"titleNode\"\n\t\t\twaiRole=\"button\" waiState=\"labelledby-${id}_label\"\n\t\t\t><div class=\"dijitReset dijitInline ${iconClass}\" dojoAttachPoint=\"iconNode\" waiRole=\"presentation\"></div\n\t\t\t><div class=\"dijitReset dijitInline dijitButtonText\" id=\"${id}_label\" dojoAttachPoint=\"containerNode\" waiRole=\"presentation\">${label}</div\n\t\t></td\n\t\t><td class='dijitReset dijitStretch dijitButtonNode dijitArrowButton dijitDownArrowButton'\n\t\t\tdojoAttachPoint=\"popupStateNode,focusNode\"\n\t\t\tdojoAttachEvent=\"ondijitclick:_onArrowClick, onkeypress:_onKey,onmouseenter:_onMouse,onmouseleave:_onMouse\"\n\t\t\tstateModifier=\"DownArrow\"\n\t\t\ttitle=\"${optionsTitle}\" name=\"${name}\"\n\t\t\twaiRole=\"button\" waiState=\"haspopup-true\"\n\t\t\t><div class=\"dijitReset dijitArrowButtonInner\" waiRole=\"presentation\">&thinsp;</div\n\t\t\t><div class=\"dijitReset dijitArrowButtonChar\" waiRole=\"presentation\">&#9660;</div\n\t\t></td\n\t></tr></tbody\n></table>\n",attributeMap:dojo.mixin(dojo.clone(dijit.form._FormWidget.prototype.attributeMap),{id:"",name:""}),optionsTitle:"",baseClass:"dijitComboButton",_focusedNode:null,postCreate:function(){
this.inherited(arguments);
this._focalNodes=[this.titleNode,this.popupStateNode];
dojo.forEach(this._focalNodes,dojo.hitch(this,function(_1a){
if(dojo.isIE){
this.connect(_1a,"onactivate",this._onNodeFocus);
this.connect(_1a,"ondeactivate",this._onNodeBlur);
}else{
this.connect(_1a,"onfocus",this._onNodeFocus);
this.connect(_1a,"onblur",this._onNodeBlur);
}
}));
},focusFocalNode:function(_1b){
this._focusedNode=_1b;
dijit.focus(_1b);
},hasNextFocalNode:function(){
return this._focusedNode!==this.getFocalNodes()[1];
},focusNext:function(){
this._focusedNode=this.getFocalNodes()[this._focusedNode?1:0];
dijit.focus(this._focusedNode);
},hasPrevFocalNode:function(){
return this._focusedNode!==this.getFocalNodes()[0];
},focusPrev:function(){
this._focusedNode=this.getFocalNodes()[this._focusedNode?0:1];
dijit.focus(this._focusedNode);
},getFocalNodes:function(){
return this._focalNodes;
},_onNodeFocus:function(evt){
this._focusedNode=evt.currentTarget;
var fnc=this._focusedNode==this.focusNode?"dijitDownArrowButtonFocused":"dijitButtonContentsFocused";
dojo.addClass(this._focusedNode,fnc);
},_onNodeBlur:function(evt){
var fnc=evt.currentTarget==this.focusNode?"dijitDownArrowButtonFocused":"dijitButtonContentsFocused";
dojo.removeClass(evt.currentTarget,fnc);
},_onBlur:function(){
this.inherited(arguments);
this._focusedNode=null;
}});
dojo.declare("dijit.form.ToggleButton",dijit.form.Button,{baseClass:"dijitToggleButton",checked:false,_onChangeMonitor:"checked",attributeMap:dojo.mixin(dojo.clone(dijit.form.Button.prototype.attributeMap),{checked:"focusNode"}),_clicked:function(evt){
this.setAttribute("checked",!this.checked);
},setAttribute:function(_21,_22){
this.inherited(arguments);
switch(_21){
case "checked":
dijit.setWaiState(this.focusNode||this.domNode,"pressed",this.checked);
this._setStateClass();
this._handleOnChange(this.checked,true);
}
},setChecked:function(_23){
dojo.deprecated("setChecked("+_23+") is deprecated. Use setAttribute('checked',"+_23+") instead.","","2.0");
this.setAttribute("checked",_23);
},postCreate:function(){
this.inherited(arguments);
this.setAttribute("checked",this.checked);
}});
}
