/*
 +--------------------------------------------------------------------+
 | CiviCRM version 4.1                                                |
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
*/
/*
* Copyright (C) 2009-2010 Xavier Dutoit
* Licensed to CiviCRM under the Academic Free License version 3.0.
*
*/

/*
TO BE VERIFIED
If you do not use clean urls on drupal, you have to define a variable to set the url of the server to be used for the rest
<script type="text/javascript">
var options {ajaxURL:"{$config->userFrameworkResourceURL}";
</script>


*/

(function($){
      var defaults = {
    	  success: function(result,settings){
    	      var successMsg = 'Saved &nbsp; <a href="#" id="closerestmsg">'+ settings.closetxt +'</a>'; 
    	      $(settings.msgbox).addClass('msgok').html( successMsg ).show();
    	      $("#closerestmsg").click(function(){$(settings.msgbox).fadeOut("slow");return false;});
    	      return true;
    	  },
    	  callBack: function(result,settings){

    	      if (result.is_error == 1) {
    		  $(settings.msgbox).addClass('msgnok').html(result.error_message);
    		  return false;
    	      }
    	      return settings.success.call(this,result,settings);
    	  },
    	  closetxt: "<div class='icon close-icon' title='Close'>[X]</div>",
    	  ajaxURL: "/civicrm/ajax/rest",
    	  msgbox: '#restmsg'
      };

      $.fn.crmAPI = function(entity,action,params,options) {
//    	  params ['fnName'] = "civicrm/"+entity+"/"+action;
    	  params ['entity'] = entity;
    	  params ['action'] = action;
    	  params ['json'] = 1;
    	  var settings = $.extend({}, defaults, options);
    	  $(settings.msgbox).removeClass('msgok').removeClass('msgnok').html("");
        $.ajax({
          url: settings.ajaxURL,
          dataType: 'json',
          data: params,
          context:this,
          success: function(result) {
            settings.callBack.call(this,result,settings);
          }
        });
        /*
    	  $.getJSON.call(this,settings.ajaxURL,params,function(result){
            console.log(this);
            console.log("bbb");
            return settings.callBack.call(self,result,settings);});
            */
      };

    $.fn.crmAutocomplete = function (params,options) {
      if (typeof params == 'undefined') params = {};
      if (typeof options == 'undefined') options = {};
      $().extend(params, {
        rowCount:35,
        json:1,
        entity:'Contact',
        action:'quicksearch',
        sequential:1
      });
        //'return':'sort_name,email'

      options = $().extend({}, {
          result: function(data){
               console.log(data);
          return false;
        },
        parse: function (data){
    			     var acd = new Array();
    			     for(cid in data.values){
                 delete data.values[cid]["data"];// to be removed once quicksearch doesn't return data
    				     acd.push({ data:data.values[cid], value:data.values[cid].sort_name, result:data.values[cid].sort_name });
    			     }
    			     return acd;
        },
    	  delay:100,
        minChars:1
        },options
      );
	    var contactUrl = defaults.ajaxURL + "?"+ $.param(params);
	  
	  //    contactUrl = contactUrl + "fnName=civicrm/contact/search&json=1&";
	  //var contactUrl = "/civicrm/ajax/rest?fnName=civicrm/contact/search&json=1&return[sort_name]=1&return[email]&rowCount=25";
	  
	  return this.each(function() {
		  var selector = this;
		  if (typeof $.fn.autocomplete != 'function') 
		      $.fn.autocomplete = cj.fn.autocomplete;//to work around the fubar cj
		      $(this).autocomplete( contactUrl, {
    			  dataType:"json",
    			      extraParams:{name:function () {
    				  return $(selector).val();}
    			  },
    			  formatItem: function(data,i,max,value,term){
              var tmp = [];
              for (attr in data) {
                if (attr != "id")
                 tmp.push(data[attr]);
              }
              return  tmp.join(' :: '); 
    			  },    			
    			  parse: function(data){ return options.parse(data);},
    			  width: 250,
    			  delay:options.delay,
    			  max:25,
    			  minChars:options.minChars,
    			  selectFirst: true
    		 }).result(function(event, data, formatted) {
              options.result(data);       
          });    
       });
     }

  $.fn.crmError = function (message,item) {
    alert (message);
    console && console.log && console.log (item);
  }

  /* you need to init this function first: cj.crmURL ('init', '{crmURL p="civicrm/example" q="placeholder"}');
  * then you can call it almost like {crmAPI} but on the client side, eg: var url = cj.crmURL ('civicrm/contact/view', {reset:1,cid:42});
  * or $('a.crmURL').crmURL();
  */
  $.extend ({ 'crmURL':
    function (p, params) {
      if (p == "init") {
        $(document).data('civicrm_templateURL',params); // storage and avoid polluting the global namespace
        return;
      }
      var tplURL = $(document).data('civicrm_templateURL');
      if (!tplURL) {
        console && console.log && console.log ("you need to init crmURL first");
        return; // should we alert() or set to drupal clean (/civicrm/bla/bla?param)?
      }
      var t= tplURL.replace("civicrm/example",p);
      if (typeof(params)=='string') {
        if (t[0]="/")
          t= t.substring(1);
        return t.replace("placeholder",params);
      } else
        return t.replace("placeholder",$.param(params));
        
    }});
  
    $.fn.crmURL = function (templateURL) { // you don't need to set templateURL each time, if you have init it with cj.crmURL ('init');
      if (!templateURL && $(document).data('civicrm_templateURL'))
        templateURL = $(document).data('civicrm_templateURL');
      return this.each(function() {
        var $this = $(this);
        if (this.href) {
          var frag = $this.attr('href').split ('?');
          if (frag[1])
            this.href=$.crmURL (frag[0],frag[1]);
          else 
            this.href=$.crmURL (frag[0]);
        }
      });      
    };

})(jQuery);
//})(cj);
