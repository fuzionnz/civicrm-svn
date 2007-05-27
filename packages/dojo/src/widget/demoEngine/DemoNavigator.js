/*
	Copyright (c) 2004-2006, The Dojo Foundation
	All Rights Reserved.

	Licensed under the Academic Free License version 2.1 or above OR the
	modified BSD license. For more information on Dojo licensing, see:

		http://dojotoolkit.org/community/licensing.shtml
*/


dojo.provide("dojo.widget.demoEngine.DemoNavigator");
dojo.require("dojo.widget.*");
dojo.require("dojo.widget.HtmlWidget");
dojo.require("dojo.widget.Button");
dojo.require("dojo.widget.demoEngine.DemoItem");
dojo.require("dojo.io.*");
dojo.require("dojo.lfx.*");
dojo.require("dojo.lang.common");
dojo.widget.defineWidget("my.widget.demoEngine.DemoNavigator",dojo.widget.HtmlWidget,{templateString:"<div dojoAttachPoint=\"domNode\">\n\t<table width=\"100%\" cellspacing=\"0\" cellpadding=\"5\">\n\t\t<tbody>\n\t\t\t<tr dojoAttachPoint=\"navigationContainer\">\n\t\t\t\t<td dojoAttachPoint=\"categoriesNode\" valign=\"top\" width=\"1%\">\n\t\t\t\t\t<h1>Categories</h1>\n\t\t\t\t\t<div dojoAttachPoint=\"categoriesButtonsNode\"></div>\n\t\t\t\t</td>\n\n\t\t\t\t<td dojoAttachPoint=\"demoListNode\" valign=\"top\">\n\t\t\t\t\t<div dojoAttachPoint=\"demoListWrapperNode\">\n\t\t\t\t\t\t<div dojoAttachPoint=\"demoListContainerNode\">\n\t\t\t\t\t\t</div>\n\t\t\t\t\t</div>\n\t\t\t\t</td>\n\t\t\t</tr>\n\t\t\t<tr>\n\t\t\t\t<td colspan=\"2\">\n\t\t\t\t\t<div dojoAttachPoint=\"demoNode\"></div>\n\t\t\t\t</td>\n\t\t\t</tr>\n\t\t</tbody>\n\t</table>\n</div>\n",templateCssString:".demoNavigatorListWrapper {\n\tborder:1px solid #dcdbdb;\n\tbackground-color:#f8f8f8;\n\tpadding:2px;\n}\n\n.demoNavigatorListContainer {\n\tborder:1px solid #f0f0f0;\n\tbackground-color:#fff;\n\tpadding:1em;\n}\n\n.demoNavigator h1 {\n\tmargin-top: 0px;\n\tmargin-bottom: 10px;\n\tfont-size: 1.2em;\n\tborder-bottom:1px dotted #a9ccf5;\n}\n\n.demoNavigator .dojoButton {\n\tmargin-bottom: 5px;\n}\n\n.demoNavigator .dojoButton .dojoButtonContents {\n\tfont-size: 1.1em;\n\twidth: 100px;\n\tcolor: black;\n}\n",templateCssPath:dojo.uri.moduleUri("dojo.widget","demoEngine/templates/DemoNavigator.css"),postCreate:function(){
dojo.html.addClass(this.domNode,this.domNodeClass);
dojo.html.addClass(this.demoListWrapperNode,this.demoListWrapperClass);
dojo.html.addClass(this.demoListContainerNode,this.demoListContainerClass);
if(dojo.render.html.ie){
dojo.debug("render ie");
dojo.html.hide(this.demoListWrapperNode);
}else{
dojo.debug("render non-ie");
dojo.lfx.html.fadeHide(this.demoListWrapperNode,0).play();
}
this.getRegistry(this.demoRegistryUrl);
this.demoContainer=dojo.widget.createWidget("DemoContainer",{returnImage:this.returnImage},this.demoNode);
dojo.event.connect(this.demoContainer,"returnToDemos",this,"returnToDemos");
this.demoContainer.hide();
},returnToDemos:function(){
this.demoContainer.hide();
if(dojo.render.html.ie){
dojo.debug("render ie");
dojo.html.show(this.navigationContainer);
}else{
dojo.debug("render non-ie");
dojo.lfx.html.fadeShow(this.navigationContainer,250).play();
}
dojo.lang.forEach(this.categoriesChildren,dojo.lang.hitch(this,function(_1){
_1.checkSize();
}));
dojo.lang.forEach(this.demoListChildren,dojo.lang.hitch(this,function(_2){
_2.checkSize();
}));
},show:function(){
dojo.html.show(this.domNode);
dojo.html.setOpacity(this.domNode,1);
dojo.html.setOpacity(this.navigationContainer,1);
dojo.lang.forEach(this.categoriesChildren,dojo.lang.hitch(this,function(_3){
_3.checkSize();
}));
dojo.lang.forEach(this.demoListChildren,dojo.lang.hitch(this,function(_4){
_4.checkSize();
}));
},getRegistry:function(_5){
dojo.io.bind({url:_5,load:dojo.lang.hitch(this,this.processRegistry),mimetype:"text/json"});
},processRegistry:function(_6,_7,e){
dojo.debug("Processing Registry");
this.registry=_7;
dojo.lang.forEach(this.registry.navigation,dojo.lang.hitch(this,this.addCategory));
},addCategory:function(_9){
var _a=dojo.widget.createWidget("Button",{caption:_9.name});
if(!dojo.lang.isObject(this.registry.categories)){
this.registry.categories=function(){
};
}
this.registry.categories[_9.name]=_9;
this.categoriesChildren.push(_a);
this.categoriesButtonsNode.appendChild(_a.domNode);
_a.domNode.categoryName=_9.name;
dojo.event.connect(_a,"onClick",this,"onSelectCategory");
},addDemo:function(_b){
var _c=this.registry.definitions[_b];
if(dojo.render.html.ie){
dojo.html.show(this.demoListWrapperNode);
}else{
dojo.lfx.html.fadeShow(this.demoListWrapperNode,250).play();
}
var _d=dojo.widget.createWidget("DemoItem",{viewDemoImage:this.viewDemoImage,name:_b,description:_c.description,thumbnail:_c.thumbnail});
this.demoListChildren.push(_d);
this.demoListContainerNode.appendChild(_d.domNode);
dojo.event.connect(_d,"onSelectDemo",this,"onSelectDemo");
},onSelectCategory:function(e){
catName=e.currentTarget.categoryName;
dojo.debug("Selected Category: "+catName);
dojo.lang.forEach(this.demoListChildren,function(_f){
_f.destroy();
});
this.demoListChildren=[];
dojo.lang.forEach(this.registry.categories[catName].demos,dojo.lang.hitch(this,function(_10){
this.addDemo(_10);
}));
},onSelectDemo:function(e){
dojo.debug("Demo Selected: "+e.target.name);
if(dojo.render.html.ie){
dojo.debug("render ie");
dojo.html.hide(this.navigationContainer);
this.demoContainer.show();
this.demoContainer.showDemo();
}else{
dojo.debug("render non-ie");
dojo.lfx.html.fadeHide(this.navigationContainer,250,null,dojo.lang.hitch(this,function(){
this.demoContainer.show();
this.demoContainer.showDemo();
})).play();
}
this.demoContainer.loadDemo(this.registry.definitions[e.target.name].url);
this.demoContainer.setName(e.target.name);
this.demoContainer.setSummary(this.registry.definitions[e.target.name].description);
}},"",function(){
this.demoRegistryUrl="demoRegistry.json";
this.registry=function(){
};
this.categoriesNode="";
this.categoriesButtonsNode="";
this.navigationContainer="";
this.domNodeClass="demoNavigator";
this.demoNode="";
this.demoContainer="";
this.demoListWrapperNode="";
this.demoListWrapperClass="demoNavigatorListWrapper";
this.demoListContainerClass="demoNavigatorListContainer";
this.returnImage="images/dojoDemos.gif";
this.viewDemoImage="images/viewDemo.png";
this.demoListChildren=[];
this.categoriesChildren=[];
});
