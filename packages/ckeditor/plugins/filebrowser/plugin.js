﻿/*
Copyright (c) 2003-2009, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

(function(){function a(j,k){var l=[];if(!k)return j;else for(var m in k)l.push(m+'='+encodeURIComponent(k[m]));return j+(j.indexOf('?')!=-1?'&':'?')+l.join('&');};function b(j){j+='';var k=j.charAt(0).toUpperCase();return k+j.substr(1);};function c(j){var q=this;var k=q.getDialog(),l=k.getParentEditor();l._.filebrowserSe=q;var m=l.config['filebrowser'+b(k.getName())+'WindowWidth']||l.config.filebrowserWindowWidth||'80%',n=l.config['filebrowser'+b(k.getName())+'WindowHeight']||l.config.filebrowserWindowHeight||'70%',o=q.filebrowser.params||{};o.CKEditor=l.name;o.CKEditorFuncNum=l._.filebrowserFn;if(!o.langCode)o.langCode=l.langCode;var p=a(q.filebrowser.url,o);l.popup(p,m,n);};function d(j){var m=this;var k=m.getDialog(),l=k.getParentEditor();l._.filebrowserSe=m;if(!k.getContentElement(m['for'][0],m['for'][1]).getInputElement().$.value)return false;if(!k.getContentElement(m['for'][0],m['for'][1]).getAction())return false;return true;};function e(j,k,l){var m=l.params||{};m.CKEditor=j.name;m.CKEditorFuncNum=j._.filebrowserFn;if(!m.langCode)m.langCode=j.langCode;k.action=a(l.url,m);k.filebrowser=l;};function f(j,k,l,m){var n,o;for(var p in m){n=m[p];if(n.type=='hbox'||n.type=='vbox')f(j,k,l,n.children);if(!n.filebrowser)continue;if(typeof n.filebrowser=='string'){var q={action:n.type=='fileButton'?'QuickUpload':'Browse',target:n.filebrowser};n.filebrowser=q;}if(n.filebrowser.action=='Browse'){var r=n.filebrowser.url||j.config['filebrowser'+b(k)+'BrowseUrl']||j.config.filebrowserBrowseUrl;if(r){n.onClick=c;n.filebrowser.url=r;n.hidden=false;}}else if(n.filebrowser.action=='QuickUpload'&&n['for']){r=n.filebrowser.url||j.config['filebrowser'+b(k)+'UploadUrl']||j.config.filebrowserUploadUrl;if(r){n.onClick=d;n.filebrowser.url=r;n.hidden=false;e(j,l.getContents(n['for'][0]).get(n['for'][1]),n.filebrowser);}}}};function g(j,k){var l=k.getDialog(),m=k.filebrowser.target||null;j=j.replace(/#/g,'%23');if(m){var n=m.split(':'),o=l.getContentElement(n[0],n[1]);if(o){o.setValue(j);l.selectPage(n[0]);}}};function h(j,k,l){if(l.indexOf(';')!==-1){var m=l.split(';');for(var n=0;n<m.length;n++)if(h(j,k,m[n]))return true;return false;}return j.getContents(k).get(l).filebrowser&&j.getContents(k).get(l).filebrowser.url;};function i(j,k){var o=this;var l=o._.filebrowserSe.getDialog(),m=o._.filebrowserSe['for'],n=o._.filebrowserSe.filebrowser.onSelect;if(m)l.getContentElement(m[0],m[1]).reset();if(n&&n.call(o._.filebrowserSe,j,k)===false)return;if(typeof k=='string'&&k)alert(k);if(j)g(j,o._.filebrowserSe);
};CKEDITOR.plugins.add('filebrowser',{init:function(j,k){j._.filebrowserFn=CKEDITOR.tools.addFunction(i,j);CKEDITOR.on('dialogDefinition',function(l){for(var m in l.data.definition.contents){f(l.editor,l.data.name,l.data.definition,l.data.definition.contents[m].elements);if(l.data.definition.contents[m].hidden&&l.data.definition.contents[m].filebrowser)l.data.definition.contents[m].hidden=!h(l.data.definition,l.data.definition.contents[m].id,l.data.definition.contents[m].filebrowser);}});}});})();
