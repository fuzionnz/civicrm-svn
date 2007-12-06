<div class="form-item">
{if $config->smtpAuth and ($config->smtpUsername == '' or $config->smtpPassword == '')}
<div class="status">
<p>{ts}Your setup enforces SMTP authentication, but does not provide SMTP username and/or password. Please fix your civicrm.settings.php file.{/ts}</p>
</div>
{else}
<fieldset>
<legend>{ts}Send an Email{/ts}</legend>
{if $suppressedEmails > 0}
    <div class="status">
        <p>{ts count=$suppressedEmails plural='Email will NOT be sent to %count contacts - communication preferences specify DO NOT EMAIL.'}Email will NOT be sent to %count contact - communication preferences specify DO NOT EMAIL.{/ts}</p>
    </div>
{/if}
<dl>
<dt>{ts}From{/ts}</dt><dd>{$from|escape}</dd>
{if $single eq false}
<dt>{ts}Recipient(s){/ts}</dt><dd>{$to|escape}</dd>
{else}
<dt>{$form.to.label}</dt><dd>{$form.to.html}{if $noEmails eq true}&nbsp;&nbsp;{$form.emailAddress.html}{/if}</dd>
{/if}
  <dt>{$form.template.label}</dt><dd> 
    <div dojoType="dojox.data.QueryReadStore" jsId="tempStore" url="{$dataUrl}" align="left" class="tundra">
        {$form.template.html}
    </div></dd>
  <dt>{$form.subject.label}</dt><dd>{$form.subject.html}</dd>
  <dt>{$form.message.label}</dt><dd>{$form.message.html}</dd>
{if $single eq false}
    <dt></dt><dd>{include file="CRM/Contact/Form/Task.tpl"}</dd>
{/if}
{if $suppressedEmails > 0}
    <dt></dt><dd>{ts count=$suppressedEmails plural='Email will NOT be sent to %count contacts.'}Email will NOT be sent to %count contact.{/ts}</dd>
{/if}
</dl>

<div id="editMessageDetails">
<dl>
    <dt>&nbsp;</dt><dd>{$form.updateTemplate.html}&nbsp;{$form.updateTemplate.label}</dd>
    <dt>&nbsp;</dt><dd>{$form.saveTemplate.html}&nbsp;{$form.saveTemplate.label}</dd>
</dl>
</div>
<div id="saveDetails">
<dl>
    <dt>{$form.saveTemplateName.label}</dt><dd>{$form.saveTemplateName.html}</dd>
</dl>
</div>

<dl>
<dt></dt><dd>{$form.buttons.html}</dd>
</dl>
</fieldset>
{/if}
</div>
 <div>

{*Added For CRM-1393*}
{if $dojoIncludes}
{literal}
<script type="text/javascript" >
     function selectValue(value)
     {
       var tokens = value.split( "^A" );
       dojo.byId('message').value=tokens[1];
       dojo.byId('subject').value=tokens[2];
     }
     function verify( select )
     {
	if ( document.getElementsByName("saveTemplate")[0].checked  == false) {
	    document.getElementById("saveDetails").style.display = "none";
	}

	document.getElementById("editMessageDetails").style.display = "block";
	document.getElementById("saveTemplateName").disabled = false;
     }
   
     function showSaveDetails(chkbox) 
     {
	if (chkbox.checked) {
	    document.getElementById("saveDetails").style.display = "block";
	    document.getElementById("saveTemplateName").disabled = false;
	} else {
	    document.getElementById("saveDetails").style.display = "none";
	    document.getElementById("saveTemplateName").disabled = true;
	}
     }

    document.getElementById("saveDetails").style.display = "none";
    document.getElementById("editMessageDetails").style.display = "none";
</script>
{/literal}
{/if}

