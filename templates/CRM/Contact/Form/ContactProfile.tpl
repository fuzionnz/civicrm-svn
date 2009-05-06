{* added onload javascript for contact profile*}
<form id="profileContact" action="http://civicrm/civicrm/contact/profilecreate?reset=1&gid={$profileID}" method="POST">
    {include file="CRM/UF/Form/Block.tpl"}
    {$form.buttons.html}
</form>
<script type="text/javascript">
{literal}
    // prepare the form when the DOM is ready 
    cj( function() { 
        var options = { 
            target:        '#output2',   // target element(s) to be updated with server response 
            beforeSubmit:  showRequest,  // pre-submit callback 
            success:       showResponse  // post-submit callback 

            // other available options: 
            //url:       url         // override for form's 'action' attribute 
            //type:      type        // 'get' or 'post', override for form's 'method' attribute 
            //dataType:  null        // 'xml', 'script', or 'json' (expected server response type) 
            //clearForm: true        // clear all form fields after successful submit 
            //resetForm: true        // reset the form after successful submit 

            // $.ajax options can be used here too, for example: 
            //timeout:   3000 
        }; 

        // bind to the form's submit event 
        cj('#profileContact').submit(function() { 
            // inside event callbacks 'this' is the DOM element so we first 
            // wrap it in a jQuery object and then invoke ajaxSubmit 
            cj(this).ajaxSubmit(options); 

            // !!! Important !!! 
            // always return false to prevent standard browser submit and page navigation 
            return false; 
        }); 
    });
    
    // post-submit callback 
    function showResponse(responseText, statusText)  { 
        // for normal html responses, the first argument to the success callback 
        // is the XMLHttpRequest object's responseText property 

        // if the ajaxSubmit method was passed an Options Object with the dataType 
        // property set to 'xml' then the first argument to the success callback 
        // is the XMLHttpRequest object's responseXML property 

        // if the ajaxSubmit method was passed an Options Object with the dataType 
        // property set to 'json' then the first argument to the success callback 
        // is the json data object returned by the server 
        
        alert('status: ' + statusText + '\n\nresponseText: \n' + responseText + 
            '\n\nThe output div should have already been updated with the responseText.'); 
    }
    
    // pre-submit callback 
    function showRequest(formData, jqForm, options) { 
        // formData is an array; here we use $.param to convert it to a string to display it 
        // but the form plugin does this for you automatically when it submits the data 
        var queryString = cj.param(formData); 

        // jqForm is a jQuery object encapsulating the form element.  To access the 
        // DOM element for the form do this: 
        // var formElement = jqForm[0]; 

        //alert('About to submit: \n\n' + queryString); 

        var dataUrl = {/literal}"{crmURL p='civicrm/ajax/profilecontact' h=0 }"{literal};
        cj.post( dataUrl, { formValues: queryString }, function(data) {
           cj("#contact").val( data.sortName ).focus();
           cj("input[name=contact_id]").val( data.contactID );
        }, 'json');

        cj("#contact-dialog").dialog("close");

        // here we could return false to prevent the form from being submitted; 
        // returning anything other than false will allow the form submit to continue 
        return false; 
    }
{/literal}
</script>