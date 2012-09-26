/*
 +--------------------------------------------------------------------+
 | CiviCRM version 4.2                                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007 and the CiviCRM Licensing Exception.   |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License and the CiviCRM Licensing Exception along                  |
 | with this program; if not, contact CiviCRM LLC                     |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+

*
* Copyright (C) 2012 Xavier Dutoit
* Licensed to CiviCRM under the Academic Free License version 3.0.
*
*
* This offers two features:
* - crmEditable() edit in place of a single field 
*  (mostly a wrapper that binds jeditable features with the ajax api and replies on crm-entity crmf-{field} html conventions)
*  if you want to add an edit in place on a template:
*  - add a class crm-entity and id {EntityName}-{Entityid} higher in the dom
*  - add a class crm-editable and crmf-{FieldName} around the field (you can add a span if needed)
*  - add data-action=create if you need to specify the api action to call (default setvalue)
*  crmf- stands for crm field
* - crmForm()
*   this embed a civicrm form and make it in place (load+ajaxForm) 
*   to make it easier to customize the form (eg. hide a button...) it triggers a 'load' event on the form. you can then catch the load on your code (using the $('#id_of_the_form').on(function(){//do something
*/


(function($){

    $.fn.crmEditable = function (options) {

      // for a jquery object (the crm-editable), find the entity name and id to apply the changes to
      // call result function(entity,id). The caller is responsible to use these params and do the needed
      var getEntityID = function (field,result) {
        var domid= $(field).closest('.crm-entity');
        if (!domid) {
          console && console.log && console.log("Couldn't get the entity id. You need to set class='crm-entity' on a parent element of the field");
          return false;
        }
        // trying to extract using the html5 data
        if (domid.data('entity')) {
          result (domid.data('entity'),domid.data('id'));
          return true;
        }
        domid=domid.attr('id');
        if (!domid) {
          console && console.log && console.log("FATAL crm-editable: Couldn't get the entity id. You need to set class='crm-entity' id='{entityName}-{id}'");
          return false;
        }
        var e=domid.match(/(\S*)-(\S*)/);
        if (!e) {
           console && console.log && console.log("Couldn't get the entity id. You need to set class='crm-entity' id='{entityName}-{id}'");
           return false;
        }
        result(e[1],e[2]);
        return true;
      }
      // param in : a dom object that contains the field name as a class crmf-xxx
      var getFieldName = function (field) {
        if ($(field).data('field')) {
           return $(field).data('field');   
        }  
        var fieldName=field.className.match(/crmf-(\S*)/)[1];
        if (!fieldName) {
          console && console.log && console.log("Couldn't get the crm-editable field name to modify. You need to set crmf-{field_name} or data-{field_name}");
          return false;
        }
        return fieldName;
      }

      
      var checkable = function () {
        $(this).change (function() {
          var params={sequential:1};
          var entity = null;
          var checked = $(this).is(':checked');
          if  (!getEntityID (this,function (e,id) {
            entity=e;
            params.id = id;
            
          })) { return };

          params['field']=getFieldName(this);
          if (!params['field'])
            return false;
          params['value']=checked?'1':'0';//seems that the ajax backend gets lost with boolean

          //$().crmAPI.call(this,entity,'create',params,{ create is still too buggy & perf
          $().crmAPI.call(this,entity,'setvalue',params,{
            error: function (data) {
              editableSettings.error.call(this,entity,fieldName,checked,data);
            },
            success: function (data) {
              editableSettings.success.call(this,entity,fieldName,checked,data);
            }
          });
        });
      };

      var defaults = {
        form:{},
        callBack:function(data){
          if (data.is_error) {
            editableSettings.error.call (this,data);
          } else {
             return editableSettings.success.call (this,data);
          }
        },
        error: function(entity,field,value,data) {
          $(this).crmError(data.error_message);
          $(this).removeClass('crm-editable-saving');
        },
        success: function(entity,field,value,data) {
          var $i = $(this);
          $().crmAlert('', 'Saved', 'success');
          $i.removeClass ('crm-editable-saving crm-error');
          $i.html(value);
        }
      }

      var editableSettings = $.extend({}, defaults, options);
  	  return this.each(function() {
        var $i = $(this);
        var fieldName = "";
      
        if (this.nodeName == "INPUT" && this.type=="checkbox") {
          checkable.call(this,this);
          return;
        }

        if (this.nodeName = 'A') {
          if (this.className.indexOf('crmf-') == -1) { // it isn't a jeditable field
            var formSettings= $.extend({}, editableSettings.form ,
              {source: $i.attr('href')
              ,success: function (result) {
                if ($i.hasClass('crm-dialog')) {
                  $('.ui-dialog').dialog('close').remove();
                } else 
                  $i.next().slideUp().remove();
                $i.trigger('success',result);
              }
              });
            var id= $i.closest('.crm-entity').attr('id');
            if (id) {
              var e=id.match(/(\S*)-(\S*)/);
               if (!e) 
                 console && console.log && console.log("Couldn't get the entity id. You need to set class='crm-entity' id='{entityName}-{id}'");
              formSettings.entity=e[1];
              formSettings.id=e[2];
            } 
            if ($i.hasClass('crm-dialog')) {
              $i.click (function () {
                var $n=$('<div>Loading</div>').appendTo('body');
                $n.dialog ({modal:true,width:500});
                $n.crmForm (formSettings);
                return false; 
              });
            } else {
              $i.click (function () {
                var $n=$i.next();
                if (!$n.hasClass('crm-target')) {
                  $n=$i.after('<div class="crm-target"></div>').next();
                } else {
                  $n.slideToggle();
                  return false;
                };
                $n.crmForm (formSettings);
                return false; 
              });
            }
            return;
          }
        }


        var settings = {
          tooltip   : 'Click to edit...',
          placeholder  : '<span class="crm-editable-placeholder">Click to edit</span>',
          data: function(value, settings) {
            return value.replace(/<(?:.|\n)*?>/gm, '');
          }
        };
        if ($i.data('placeholder')) {
          settings.placeholder = $i.data('placeholder');
        } else {
          settings.placeholder  = '<span class="crm-editable-placeholder">Click to edit</span>';
        }
        if ($i.data('tooltip')) {
          settings.placeholder = $i.data('tooltip')
        } else {
          settings.tooltip   = 'Click to edit...';
        }
        if ($i.data('type')) {
          settings.type = $i.data('type');
          settings.onblur = 'submit';
        }
        if ($i.data('options')){
          settings.data = $i.data('options');
        }
        $i.addClass ('crm-editable-enabled');
        $i.editable(function(value,settings) {
        //$i.editable(function(value,editableSettings) {
          parent=$i.closest('.crm-entity');
          if (!parent) {
            console && console.log && console.log("crm-editable: you need to define one parent element that has a class .crm-entity");
            return;
          }

          $i.addClass ('crm-editable-saving');
          var params = {};
          var entity = null;
          params['field']=getFieldName(this);
          if (!params['field'])
            return false;
          params['value']=value;
          if  (!getEntityID (this,function (e,id) {
            entity=e;
            params.id = id;
          })) {return;}

          if (params.id == "new") {
            params.id = '';
          }

          if ($i.data('action')) {
            params[params['field']]=value;//format for create at least
            action=$i.data('action');
          } else {
            action="setvalue";
          }
          $().crmAPI.call(this,entity,action,params,{
              error: function (data) {
                editableSettings.error.call(this,entity,fieldName,value,data);
              },
              success: function (data) {
                if ($i.data('options')){
                  value = $i.data('options')[value];
                }
                editableSettings.success.call(this,entity,fieldName,value,data);
              }
            });
           },settings);
    });
  }

  $.fn.crmForm = function (options ) {
    var settings = $.extend( {
      'title':'',
      'entity':'',
      'action':'get',
      'id':0,
      'sequential':1,
      'dialog': false,
      'load' : function (target){},
      'success' : function (result) {
        $(this).html ("Saved");
       }
    }, options);


    return this.each(function() {
      var formLoaded = function (target) {
        var $this =$(target);
        var destination="<input type='hidden' name='civicrmDestination' value='"+$.crmURL('civicrm/ajax/rest',{
          'sequential':settings.sequential,
          'json':'html',
          'entity':settings.entity,
          'action':settings.action,
          'id':settings.id
          })+"' />";
        $this.find('form').ajaxForm({
          beforeSubmit :function () {
            $this.html("<div class='crm-editable-saving'>Saving...</div>");
            return true;
          },
          success:function(response) { 
            if (response.indexOf('crm-error') >= 0) { // we got an error, re-display the page
              $this.html(response);
              formLoaded(target);
            } else {
              if (response[0] == '{')
                settings.success($.parseJSON (response));
              else
                settings.success(response);
            }
          }
        }).append('<input type="hidden" name="snippet" value="1"/>'+destination).trigger('load'); 

        settings.load(target);
      };

      var $this = $(this);
       if (settings.source && settings.source.indexOf('snippet') == -1) {
         if (settings.source.indexOf('?') >= 0)
           settings.source = settings.source + "&snippet=1";
         else
           settings.source = settings.source + "?snippet=1";
       }


       $this.html ("Loading...");
       if (settings.dialog)
         $this.dialog({width:'auto',minWidth:600});
       $this.load (settings.source ,function (){formLoaded(this)});

    });
  };

  $.fn.crmFormInline = function() {
    var o = $(this[0]);
    var data = o.data('edit-params');
    if (data) {
      o.animate({height: '+=50px'}, 200);
      data.snippet = 5;
      data.reset = 1;
      o.addClass('form');
      addCiviOverlay(o);
      $.ajax({
        url: $.crmURL('civicrm/ajax/inline', data),
      }).done( function(response) {
        o.css('overflow', 'hidden').wrapInner('<div class="inline-edit-hidden-content" style="display:none" />').append( response);
        // Smooth resizing
        var newHeight = $('.crm-container-snippet', o).height();
        var diff = newHeight - parseInt(o.css('height'));
        if (diff < 0) {
          diff = 0 - diff;
        }
        o.animate({height: '' + newHeight + 'px'}, diff * 2, function(){o.removeAttr('style');});
        removeCiviOverlay(o);
        $('form', o).ajaxForm( {beforeSubmit: requestHandler} );
        $(':submit[name$=cancel]', o).click(function() {
          var container = $(this).closest('.crm-inline-edit.form');
          $('.inline-edit-hidden-content', container).nextAll().remove();
          $('.inline-edit-hidden-content > *:first-child', container).unwrap();
          container.removeClass('form');
          return false;
        });
        o.trigger('crmFormLoad');
      });
    }
  };

  function requestHandler(formData, jqForm, options) {
    var o = jqForm.closest('div.crm-inline-edit.form');
    var data = o.data('edit-params');
    data.snippet = 5;
    data.reset = 1;
    o.trigger('crmFormBeforeSave', [formData]);
    var queryString = $.param(formData) + '&' + $.param(data); 
    var postUrl = $.crmURL('civicrm/ajax/inline'); 
    $.ajax({
      type: "POST",
      url: postUrl,
      data: queryString,
      dataType: "json",
      success: function( response ) {
        o.trigger('crmFormSuccess', [response]);
        if (status = response.status) {
          var data = o.data('edit-params');
          var dependent = o.data('dependent-fields') || [];
          // Clone the add-new link if replacing it, and queue the clone to be refreshed as a dependant block
          if (o.hasClass('add-new')) {
            if (response.addressId) {
              data.aid = response.addressId;
            }
            var clone = o.parent().clone();
            o.data('edit-params', data);
            $('.crm-container-snippet', clone).remove();
            if (clone.hasClass('contactCardLeft')) {
              clone.removeClass('contactCardLeft').addClass('contactCardRight');
            }
            else if (clone.hasClass('contactCardRight')) {
              clone.removeClass('contactCardRight').addClass('contactCardLeft');
            }
            var cl = $('.crm-inline-edit', clone);
            var clData = cl.data('edit-params');
            var locNo = clData.locno++;
            cl.attr('id', cl.attr('id').replace(locNo, clData.locno)).removeClass('form');
            o.parent().after(clone);
            $.merge(dependent, $('.crm-inline-edit', clone));
          }
          // Reload this block plus all dependent blocks
          var update = $.merge([o], dependent);
          for (var i in update) {
            $(update[i]).each(function() {
              var data = $(this).data('edit-params');
              data.snippet = 1;
              data.reset = 1;
              data.class_name = data.class_name.replace('Form', 'Page');
              data.type = 'page';
              $(this).parent().load(postUrl, data);
            });
          }
          cj().crmAlert('', ts('Saved'), 'success');
        }
      },
      error: function (obj, status) {
        $('.crm-container-snippet', o).replaceWith(obj.responseText);
        $('form', o).ajaxForm( {beforeSubmit: requestHandler} );
        o.trigger('crmFormError', [obj, status]);
      }
    });
    // disable ajaxForm submit
    return false; 
  };

  /**
   * Configure optimistic locking mechanism for inplace editing
   *
   * options.ignoreLabel: string, text for a button
   * options.reloadLabel: string, text for a button
   */
  $.fn.crmFormContactLock = function(options) {
    var oplock_ts = false;

    // AFTER LOAD: For first edit form, extract oplock_ts
    this.on('crmFormLoad', function(event) {
      var o = $(event.target);
      if (oplock_ts == false) { // first block
        oplock_ts = o.find('input[name="oplock_ts"]').val();
      }
    });
    // BEFORE SAVE: Replace input[oplock_ts] with oplock_ts
    this.on('crmFormBeforeSave', function(event, formData) {
      $.each(formData, function(key, formItem) {
        if (formItem.name == 'oplock_ts') {
          formItem.value = oplock_ts;
        }
      });
    });
    // AFTER SUCCESS: Update oplock_ts
    this.on('crmFormSuccess', function(event, response) {
      oplock_ts = response.oplock_ts;
    });
    // AFTER ERROR: Render any "Ignore" and "Restart" buttons
    return this.on('crmFormError', function(event, obj, status) {
      var o = $(event.target);
      var data = o.data('edit-params');
      var errorTag = o.find('.update_oplock_ts');
      if (errorTag.length > 0) {
        $('<button>')
          .addClass('crm-button')
          .text(options.ignoreLabel)
          .click(function() {
            oplock_ts = errorTag.attr('data:update_oplock_ts');
            errorTag.parent().hide();
            var containerTag = errorTag.closest('.crm-error');
            if (containerTag.find('li').length == 1) {
              containerTag.hide();
            }
            return false;
          })
          .appendTo(errorTag)
          ;
        $('<button>')
          .addClass('crm-button')
          .text(options.reloadLabel)
          .click(function() {
            window.location.reload();
            return false;
          })
          .appendTo(errorTag)
          ;
      }
    });
  };

  $('document').ready(function() {
    // Respond to a click (not drag, not right-click) of crm-inline-edit blocks
    var clicking;
    $('.crm-inline-edit-container').on('mousedown', '.crm-inline-edit:not(.form)', function(button) {
      if (button.which == 1) {
        clicking = this;
        setTimeout(function() {clicking = null;}, 500);
      }
    });
    $('.crm-inline-edit-container').on('mouseup', '.crm-inline-edit:not(.form)', function(button) {
      if (clicking === this && button.which == 1) {
        $(this).crmFormInline();
      }
    });
    // Trigger cancel button on esc keypress
    $(document).keydown(function(key) {
      if (key.which == 27) {
        $('.crm-inline-edit.form :submit[name$=cancel]').click();
      }
    });
  });

})(jQuery);
