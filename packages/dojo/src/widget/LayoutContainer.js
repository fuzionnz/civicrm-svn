/*
	Copyright (c) 2004-2006, The Dojo Foundation
	All Rights Reserved.

	Licensed under the Academic Free License version 2.1 or above OR the
	modified BSD license. For more information on Dojo licensing, see:

		http://dojotoolkit.org/community/licensing.shtml
*/


dojo.provide("dojo.widget.LayoutContainer");
dojo.require("dojo.widget.*");
dojo.require("dojo.widget.html.layout");
dojo.widget.defineWidget("dojo.widget.LayoutContainer",dojo.widget.HtmlWidget,{isContainer:true,layoutChildPriority:"top-bottom",postCreate:function(){
dojo.widget.html.layout(this.domNode,this.children,this.layoutChildPriority);
},addChild:function(_1,_2,_3,_4,_5){
dojo.widget.LayoutContainer.superclass.addChild.call(this,_1,_2,_3,_4,_5);
dojo.widget.html.layout(this.domNode,this.children,this.layoutChildPriority);
},removeChild:function(_6){
dojo.widget.LayoutContainer.superclass.removeChild.call(this,_6);
dojo.widget.html.layout(this.domNode,this.children,this.layoutChildPriority);
},onResized:function(){
dojo.widget.html.layout(this.domNode,this.children,this.layoutChildPriority);
},show:function(){
this.domNode.style.display="";
this.checkSize();
this.domNode.style.display="none";
this.domNode.style.visibility="";
dojo.widget.LayoutContainer.superclass.show.call(this);
}});
dojo.lang.extend(dojo.widget.Widget,{layoutAlign:"none"});
