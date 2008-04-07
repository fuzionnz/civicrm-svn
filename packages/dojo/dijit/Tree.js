/*
	Copyright (c) 2004-2008, The Dojo Foundation
	All Rights Reserved.

	Licensed under the Academic Free License version 2.1 or above OR the
	modified BSD license. For more information on Dojo licensing, see:

		http://dojotoolkit.org/book/dojo-book-0-9/introduction/licensing
*/


if(!dojo._hasResource["dijit.Tree"]){
dojo._hasResource["dijit.Tree"]=true;
dojo.provide("dijit.Tree");
dojo.require("dojo.fx");
dojo.require("dijit._Widget");
dojo.require("dijit._Templated");
dojo.require("dijit._Container");
dojo.require("dojo.cookie");
dojo.declare("dijit._TreeNode",[dijit._Widget,dijit._Templated,dijit._Container,dijit._Contained],{item:null,isTreeNode:true,label:"",isExpandable:null,isExpanded:false,state:"UNCHECKED",templateString:"<div class=\"dijitTreeNode\" waiRole=\"presentation\"\n\t><div dojoAttachPoint=\"rowNode\" waiRole=\"presentation\"\n\t\t><span dojoAttachPoint=\"expandoNode\" class=\"dijitTreeExpando\" waiRole=\"presentation\"\n\t\t></span\n\t\t><span dojoAttachPoint=\"expandoNodeText\" class=\"dijitExpandoText\" waiRole=\"presentation\"\n\t\t></span\n\t\t><div dojoAttachPoint=\"contentNode\" class=\"dijitTreeContent\" waiRole=\"presentation\">\n\t\t\t<div dojoAttachPoint=\"iconNode\" class=\"dijitInline dijitTreeIcon\" waiRole=\"presentation\"></div>\n\t\t\t<span dojoAttachPoint=\"labelNode\" class=\"dijitTreeLabel\" wairole=\"treeitem\" tabindex=\"-1\" waiState=\"selected-false\" dojoAttachEvent=\"onfocus:_onNodeFocus\"></span>\n\t\t</div\n\t></div>\n</div>\n",postCreate:function(){
this.setLabelNode(this.label);
this._setExpando();
this._updateItemClasses(this.item);
if(this.isExpandable){
dijit.setWaiState(this.labelNode,"expanded",this.isExpanded);
}
},markProcessing:function(){
this.state="LOADING";
this._setExpando(true);
},unmarkProcessing:function(){
this._setExpando(false);
},_updateItemClasses:function(_1){
var _2=this.tree,_3=_2.model;
if(_2._v10Compat&&_1===_3.root){
_1=null;
}
this.iconNode.className="dijitInline dijitTreeIcon "+_2.getIconClass(_1,this.isExpanded);
this.labelNode.className="dijitTreeLabel "+_2.getLabelClass(_1,this.isExpanded);
},_updateLayout:function(){
var _4=this.getParent();
if(!_4||_4.rowNode.style.display=="none"){
dojo.addClass(this.domNode,"dijitTreeIsRoot");
}else{
dojo.toggleClass(this.domNode,"dijitTreeIsLast",!this.getNextSibling());
}
},_setExpando:function(_5){
var _6=["dijitTreeExpandoLoading","dijitTreeExpandoOpened","dijitTreeExpandoClosed","dijitTreeExpandoLeaf"];
var _7=_5?0:(this.isExpandable?(this.isExpanded?1:2):3);
dojo.forEach(_6,function(s){
dojo.removeClass(this.expandoNode,s);
},this);
dojo.addClass(this.expandoNode,_6[_7]);
this.expandoNodeText.innerHTML=_5?"*":(this.isExpandable?(this.isExpanded?"-":"+"):"*");
},expand:function(){
if(this.isExpanded){
return;
}
if(this._wipeOut.status()=="playing"){
this._wipeOut.stop();
}
this.isExpanded=true;
dijit.setWaiState(this.labelNode,"expanded","true");
dijit.setWaiRole(this.containerNode,"group");
this.contentNode.className="dijitTreeContent dijitTreeContentExpanded";
this._setExpando();
this._updateItemClasses(this.item);
this._wipeIn.play();
},collapse:function(){
if(!this.isExpanded){
return;
}
if(this._wipeIn.status()=="playing"){
this._wipeIn.stop();
}
this.isExpanded=false;
dijit.setWaiState(this.labelNode,"expanded","false");
this.contentNode.className="dijitTreeContent";
this._setExpando();
this._updateItemClasses(this.item);
this._wipeOut.play();
},setLabelNode:function(_9){
this.labelNode.innerHTML="";
this.labelNode.appendChild(dojo.doc.createTextNode(_9));
},setChildItems:function(_a){
var _b=this.tree,_c=_b.model;
this.getChildren().forEach(function(_d){
dijit._Container.prototype.removeChild.call(this,_d);
},this);
this.state="LOADED";
if(_a&&_a.length>0){
this.isExpandable=true;
if(!this.containerNode){
this.containerNode=this.tree.containerNodeTemplate.cloneNode(true);
this.domNode.appendChild(this.containerNode);
}
dojo.forEach(_a,function(_e){
var id=_c.getIdentity(_e),_10=_b._itemNodeMap[id],_11=(_10&&!_10.getParent())?_10:new dijit._TreeNode({item:_e,tree:_b,isExpandable:_c.mayHaveChildren(_e),label:_b.getLabel(_e)});
this.addChild(_11);
_b._itemNodeMap[id]=_11;
if(this.tree.persist){
if(_b._openedItemIds[id]){
_b._expandNode(_11);
}
}
},this);
dojo.forEach(this.getChildren(),function(_12,idx){
_12._updateLayout();
});
}else{
this.isExpandable=false;
}
if(this._setExpando){
this._setExpando(false);
}
if(!this.parent){
var fc=this.tree.showRoot?this:this.getChildren()[0],_15=fc?fc.labelNode:this.domNode;
_15.setAttribute("tabIndex","0");
}
if(this.containerNode&&!this._wipeIn){
this._wipeIn=dojo.fx.wipeIn({node:this.containerNode,duration:150});
this._wipeOut=dojo.fx.wipeOut({node:this.containerNode,duration:150});
}
},removeChild:function(_16){
this.inherited(arguments);
var _17=this.getChildren();
if(_17.length==0){
this.isExpandable=false;
this.collapse();
}
dojo.forEach(_17,function(_18){
_18._updateLayout();
});
},makeExpandable:function(){
this.isExpandable=true;
this._setExpando(false);
},_onNodeFocus:function(evt){
var _1a=dijit.getEnclosingWidget(evt.target);
this.tree._onTreeFocus(_1a);
}});
dojo.declare("dijit.Tree",[dijit._Widget,dijit._Templated],{store:null,model:null,query:null,label:"",showRoot:true,childrenAttr:["children"],openOnClick:false,templateString:"<div class=\"dijitTreeContainer\" waiRole=\"tree\"\n\tdojoAttachEvent=\"onclick:_onClick,onkeypress:_onKeyPress\">\n</div>\n",isExpandable:true,isTree:true,persist:true,dndController:null,dndParams:["onDndDrop","itemCreator","onDndCancel","checkAcceptance","checkItemAcceptance"],onDndDrop:null,itemCreator:null,onDndCancel:null,checkAcceptance:null,checkItemAcceptance:null,_publish:function(_1b,_1c){
dojo.publish(this.id,[dojo.mixin({tree:this,event:_1b},_1c||{})]);
},postMixInProperties:function(){
this.tree=this;
this._itemNodeMap={};
if(!this.cookieName){
this.cookieName=this.id+"SaveStateCookie";
}
},postCreate:function(){
if(this.persist){
var _1d=dojo.cookie(this.cookieName);
this._openedItemIds={};
if(_1d){
dojo.forEach(_1d.split(","),function(_1e){
this._openedItemIds[_1e]=true;
},this);
}
}
var div=dojo.doc.createElement("div");
div.style.display="none";
div.className="dijitTreeContainer";
dijit.setWaiRole(div,"presentation");
this.containerNodeTemplate=div;
if(!this.model){
this._store2model();
}
this.connect(this.model,"onChange","_onItemChange");
this.connect(this.model,"onChildrenChange","_onItemChildrenChange");
this._load();
this.inherited("postCreate",arguments);
if(this.dndController){
if(dojo.isString(this.dndController)){
this.dndController=dojo.getObject(this.dndController);
}
var _20={};
for(var i=0;i<this.dndParams.length;i++){
if(this[this.dndParams[i]]){
_20[this.dndParams[i]]=this[this.dndParams[i]];
}
}
this.dndController=new this.dndController(this,_20);
}
},_store2model:function(){
this._v10Compat=true;
dojo.deprecated("Tree: from version 2.0, should specify a model object rather than a store/query");
var _22={id:this.id+"_ForestStoreModel",store:this.store,query:this.query,childrenAttrs:this.childrenAttr};
if(this.params.mayHaveChildren){
_22.mayHaveChildren=dojo.hitch(this,"mayHaveChildren");
}
if(this.params.getItemChildren){
_22.getChildren=dojo.hitch(this,function(_23,_24,_25){
this.getItemChildren((this._v10Compat&&_23===this.model.root)?null:_23,_24,_25);
});
}
this.model=new dijit.tree.ForestStoreModel(_22);
this.showRoot=Boolean(this.label);
},_load:function(){
this.model.getRoot(dojo.hitch(this,function(_26){
var rn=this.rootNode=new dijit._TreeNode({item:_26,tree:this,isExpandable:true,label:this.label||this.getLabel(_26)});
if(!this.showRoot){
rn.rowNode.style.display="none";
}
this.domNode.appendChild(rn.domNode);
this._itemNodeMap[this.model.getIdentity(_26)]=rn;
rn._updateLayout();
this._expandNode(rn);
}),function(err){
console.error(this,": error loading root: ",err);
});
},mayHaveChildren:function(_29){
},getItemChildren:function(_2a,_2b){
},getLabel:function(_2c){
return this.model.getLabel(_2c);
},getIconClass:function(_2d,_2e){
return (!_2d||this.model.mayHaveChildren(_2d))?(_2e?"dijitFolderOpened":"dijitFolderClosed"):"dijitLeaf";
},getLabelClass:function(_2f,_30){
},_onKeyPress:function(e){
if(e.altKey){
return;
}
var _32=dijit.getEnclosingWidget(e.target);
if(!_32){
return;
}
if(e.charCode){
var _33=e.charCode;
if(!e.altKey&&!e.ctrlKey&&!e.shiftKey&&!e.metaKey){
_33=(String.fromCharCode(_33)).toLowerCase();
this._onLetterKeyNav({node:_32,key:_33});
dojo.stopEvent(e);
}
}else{
var map=this._keyHandlerMap;
if(!map){
map={};
map[dojo.keys.ENTER]="_onEnterKey";
map[dojo.keys.LEFT_ARROW]="_onLeftArrow";
map[dojo.keys.RIGHT_ARROW]="_onRightArrow";
map[dojo.keys.UP_ARROW]="_onUpArrow";
map[dojo.keys.DOWN_ARROW]="_onDownArrow";
map[dojo.keys.HOME]="_onHomeKey";
map[dojo.keys.END]="_onEndKey";
this._keyHandlerMap=map;
}
if(this._keyHandlerMap[e.keyCode]){
this[this._keyHandlerMap[e.keyCode]]({node:_32,item:_32.item});
dojo.stopEvent(e);
}
}
},_onEnterKey:function(_35){
this._publish("execute",{item:_35.item,node:_35.node});
this.onClick(_35.item,_35.node);
},_onDownArrow:function(_36){
var _37=this._getNextNode(_36.node);
if(_37&&_37.isTreeNode){
this.focusNode(_37);
}
},_onUpArrow:function(_38){
var _39=_38.node;
var _3a=_39.getPreviousSibling();
if(_3a){
_39=_3a;
while(_39.isExpandable&&_39.isExpanded&&_39.hasChildren()){
var _3b=_39.getChildren();
_39=_3b[_3b.length-1];
}
}else{
var _3c=_39.getParent();
if(!(!this.showRoot&&_3c===this.rootNode)){
_39=_3c;
}
}
if(_39&&_39.isTreeNode){
this.focusNode(_39);
}
},_onRightArrow:function(_3d){
var _3e=_3d.node;
if(_3e.isExpandable&&!_3e.isExpanded){
this._expandNode(_3e);
}else{
if(_3e.hasChildren()){
_3e=_3e.getChildren()[0];
if(_3e&&_3e.isTreeNode){
this.focusNode(_3e);
}
}
}
},_onLeftArrow:function(_3f){
var _40=_3f.node;
if(_40.isExpandable&&_40.isExpanded){
this._collapseNode(_40);
}else{
_40=_40.getParent();
if(_40&&_40.isTreeNode){
this.focusNode(_40);
}
}
},_onHomeKey:function(){
var _41=this._getRootOrFirstNode();
if(_41){
this.focusNode(_41);
}
},_onEndKey:function(_42){
var _43=this;
while(_43.isExpanded){
var c=_43.getChildren();
_43=c[c.length-1];
}
if(_43&&_43.isTreeNode){
this.focusNode(_43);
}
},_onLetterKeyNav:function(_45){
var _46=startNode=_45.node,key=_45.key;
do{
_46=this._getNextNode(_46);
if(!_46){
_46=this._getRootOrFirstNode();
}
}while(_46!==startNode&&(_46.label.charAt(0).toLowerCase()!=key));
if(_46&&_46.isTreeNode){
if(_46!==startNode){
this.focusNode(_46);
}
}
},_onClick:function(e){
var _49=e.target;
var _4a=dijit.getEnclosingWidget(_49);
if(!_4a||!_4a.isTreeNode){
return;
}
if((this.openOnClick&&_4a.isExpandable)||(_49==_4a.expandoNode||_49==_4a.expandoNodeText)){
if(_4a.isExpandable){
this._onExpandoClick({node:_4a});
}
}else{
this._publish("execute",{item:_4a.item,node:_4a});
this.onClick(_4a.item,_4a);
this.focusNode(_4a);
}
dojo.stopEvent(e);
},_onExpandoClick:function(_4b){
var _4c=_4b.node;
this.focusNode(_4c);
if(_4c.isExpanded){
this._collapseNode(_4c);
}else{
this._expandNode(_4c);
}
},onClick:function(_4d,_4e){
},_getNextNode:function(_4f){
if(_4f.isExpandable&&_4f.isExpanded&&_4f.hasChildren()){
return _4f.getChildren()[0];
}else{
while(_4f&&_4f.isTreeNode){
var _50=_4f.getNextSibling();
if(_50){
return _50;
}
_4f=_4f.getParent();
}
return null;
}
},_getRootOrFirstNode:function(){
return this.showRoot?this.rootNode:this.rootNode.getChildren()[0];
},_collapseNode:function(_51){
if(_51.isExpandable){
if(_51.state=="LOADING"){
return;
}
_51.collapse();
if(this.persist&&_51.item){
delete this._openedItemIds[this.model.getIdentity(_51.item)];
this._saveState();
}
}
},_expandNode:function(_52){
if(!_52.isExpandable){
return;
}
var _53=this.model,_54=_52.item;
switch(_52.state){
case "LOADING":
return;
case "UNCHECKED":
_52.markProcessing();
var _55=this;
_53.getChildren(_54,function(_56){
_52.unmarkProcessing();
_52.setChildItems(_56);
_55._expandNode(_52);
},function(err){
console.error(_55,": error loading root children: ",err);
});
break;
default:
_52.expand();
if(this.persist&&_54){
this._openedItemIds[_53.getIdentity(_54)]=true;
this._saveState();
}
}
},blurNode:function(){
var _58=this.lastFocused;
if(!_58){
return;
}
var _59=_58.labelNode;
dojo.removeClass(_59,"dijitTreeLabelFocused");
_59.setAttribute("tabIndex","-1");
dijit.setWaiState(_59,"selected",false);
this.lastFocused=null;
},focusNode:function(_5a){
_5a.labelNode.focus();
},_onBlur:function(){
this.inherited(arguments);
if(this.lastFocused){
var _5b=this.lastFocused.labelNode;
dojo.removeClass(_5b,"dijitTreeLabelFocused");
}
},_onTreeFocus:function(_5c){
if(_5c){
if(_5c!=this.lastFocused){
this.blurNode();
}
var _5d=_5c.labelNode;
_5d.setAttribute("tabIndex","0");
dijit.setWaiState(_5d,"selected",true);
dojo.addClass(_5d,"dijitTreeLabelFocused");
this.lastFocused=_5c;
}
},_onItemDelete:function(_5e){
var _5f=this.model.getIdentity(_5e);
var _60=this._itemNodeMap[_5f];
if(_60){
var _61=_60.getParent();
if(_61){
_61.removeChild(_60);
}
delete this._itemNodeMap[_5f];
_60.destroyRecursive();
}
},_onItemChange:function(_62){
var _63=this.model,_64=_63.getIdentity(_62),_65=this._itemNodeMap[_64];
if(_65){
_65.setLabelNode(this.getLabel(_62));
_65._updateItemClasses(_62);
}
},_onItemChildrenChange:function(_66,_67){
var _68=this.model,_69=_68.getIdentity(_66),_6a=this._itemNodeMap[_69];
if(_6a){
_6a.setChildItems(_67);
}
},_saveState:function(){
if(!this.persist){
return;
}
var ary=[];
for(var id in this._openedItemIds){
ary.push(id);
}
dojo.cookie(this.cookieName,ary.join(","));
},destroy:function(){
if(this.rootNode){
this.rootNode.destroyRecursive();
}
this.rootNode=null;
this.inherited(arguments);
},destroyRecursive:function(){
this.destroy();
}});
dojo.declare("dijit.tree.TreeStoreModel",null,{store:null,childrenAttrs:["children"],root:null,query:null,constructor:function(_6d){
dojo.mixin(this,_6d);
this.connects=[];
var _6e=this.store;
if(!_6e.getFeatures()["dojo.data.api.Identity"]){
throw new Error("dijit.Tree: store must support dojo.data.Identity");
}
if(_6e.getFeatures()["dojo.data.api.Notification"]){
this.connects=this.connects.concat([dojo.connect(_6e,"onNew",this,"_onNewItem"),dojo.connect(_6e,"onDelete",this,"_onDeleteItem"),dojo.connect(_6e,"onSet",this,"_onSetItem")]);
}
},destroy:function(){
dojo.forEach(this.connects,dojo.disconnect);
},getRoot:function(_6f,_70){
if(this.root){
_6f(this.root);
}else{
this.store.fetch({query:this.query,onComplete:dojo.hitch(this,function(_71){
if(_71.length!=1){
throw new Error(this.declaredClass+": query "+query+" returned "+_71.length+" items, but must return exactly one item");
}
this.root=_71[0];
_6f(this.root);
}),onError:_70});
}
},mayHaveChildren:function(_72){
return dojo.some(this.childrenAttrs,function(_73){
return this.store.hasAttribute(_72,_73);
},this);
},getChildren:function(_74,_75,_76){
var _77=this.store;
var _78=[];
for(var i=0;i<this.childrenAttrs.length;i++){
var _7a=_77.getValues(_74,this.childrenAttrs[i]);
_78=_78.concat(_7a);
}
var _7b=0;
dojo.forEach(_78,function(_7c){
if(!_77.isItemLoaded(_7c)){
_7b++;
}
});
if(_7b==0){
_75(_78);
}else{
var _7d=function _7d(_7e){
if(--_7b==0){
_75(_78);
}
};
dojo.forEach(_78,function(_7f){
if(!_77.isItemLoaded(_7f)){
_77.loadItem({item:_7f,onItem:_7d,onError:_76});
}
});
}
},getIdentity:function(_80){
return this.store.getIdentity(_80);
},getLabel:function(_81){
return this.store.getLabel(_81);
},newItem:function(_82,_83){
var _84={parent:_83,attribute:this.childrenAttrs[0]};
return this.store.newItem(_82,_84);
},pasteItem:function(_85,_86,_87,_88){
var _89=this.store,_8a=this.childrenAttrs[0];
if(_86){
dojo.forEach(this.childrenAttrs,function(_8b){
if(_89.containsValue(_86,_8b,_85)){
if(!_88){
var _8c=dojo.filter(_89.getValues(_86,_8b),function(x){
return x!=_85;
});
_89.setValues(_86,_8b,_8c);
}
_8a=_8b;
}
});
}
if(_87){
_89.setValues(_87,_8a,_89.getValues(_87,_8a).concat(_85));
}
},onChange:function(_8e){
},onChildrenChange:function(_8f,_90){
},_onNewItem:function(_91,_92){
if(!_92){
return;
}
this.getChildren(_92.item,dojo.hitch(this,function(_93){
this.onChildrenChange(_92.item,_93);
}));
},_onDeleteItem:function(_94){
},_onSetItem:function(_95,_96,_97,_98){
if(dojo.indexOf(this.childrenAttrs,_96)!=-1){
this.getChildren(_95,dojo.hitch(this,function(_99){
this.onChildrenChange(_95,_99);
}));
}else{
this.onChange(_95);
}
}});
dojo.declare("dijit.tree.ForestStoreModel",dijit.tree.TreeStoreModel,{rootId:"$root$",rootLabel:"ROOT",query:null,constructor:function(_9a){
this.root={store:this,root:true,id:_9a.rootId,label:_9a.rootLabel,children:_9a.rootChildren};
},mayHaveChildren:function(_9b){
return _9b===this.root||this.inherited(arguments);
},getChildren:function(_9c,_9d,_9e){
if(_9c===this.root){
if(this.root.children){
_9d(this.root.children);
}else{
this.store.fetch({query:this.query,onComplete:dojo.hitch(this,function(_9f){
this.root.children=_9f;
_9d(_9f);
}),onError:_9e});
}
}else{
this.inherited(arguments);
}
},getIdentity:function(_a0){
return (_a0===this.root)?this.root.id:this.inherited(arguments);
},getLabel:function(_a1){
return (_a1===this.root)?this.root.label:this.inherited(arguments);
},newItem:function(_a2,_a3){
if(_a3===this.root){
this.onNewRootItem(_a2);
return this.store.newItem(_a2);
}else{
return this.inherited(arguments);
}
},onNewRootItem:function(_a4){
},pasteItem:function(_a5,_a6,_a7,_a8){
if(_a6===this.root){
if(!_a8){
this.onLeaveRoot(_a5);
}
}
dijit.tree.TreeStoreModel.prototype.pasteItem.call(this,_a5,_a6===this.root?null:_a6,_a7===this.root?null:_a7);
if(_a7===this.root){
this.onAddToRoot(_a5);
}
},onAddToRoot:function(_a9){
console.log(this,": item ",_a9," added to root");
},onLeaveRoot:function(_aa){
console.log(this,": item ",_aa," removed from root");
},_requeryTop:function(){
var _ab=this,_ac=this.root.children;
this.store.fetch({query:this.query,onComplete:function(_ad){
_ab.root.children=_ad;
if(_ac.length!=_ad.length||dojo.some(_ac,function(_ae,idx){
return _ad[idx]!=_ae;
})){
_ab.onChildrenChange(_ab.root,_ad);
}
}});
},_onNewItem:function(_b0,_b1){
this._requeryTop();
this.inherited(arguments);
},_onDeleteItem:function(_b2){
if(dojo.indexOf(this.root.children,_b2)!=-1){
this._requeryTop();
}
this.inherited(arguments);
}});
}
