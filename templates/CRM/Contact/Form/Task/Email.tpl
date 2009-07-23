<div class="form-item">
<dl>{$form.buttons.html}</dl>
<fieldset>
{if $suppressedEmails > 0}
    <div class="status">
        <p>{ts count=$suppressedEmails plural='Email will NOT be sent to %count contacts - (no email address on file, or communication preferences specify DO NOT EMAIL, or contact is deceased).'}Email will NOT be sent to %count contact - (no email address on file, or communication preferences specify DO NOT EMAIL, or contact is deceased).{/ts}</p>
    </div>
{/if}
<table class="form-layout-compressed">
<tr>
    <td class="label">{$form.fromEmailAddress.label}</td><td>{$form.fromEmailAddress.html} {help id ="id-from_email" file="CRM/Contact/Form/Task/Email.hlp"}</td>
</tr>
<tr>
    <td class="label">{if $single eq false}{ts}Recipient(s){/ts}{else}{$form.to.label}{/if}</td>
    <td>{$form.to.html}{if $noEmails eq true}&nbsp;&nbsp;{$form.emailAddress.html}{/if}
    <div class="spacer"></div>
    <span class="bold"><a href="#" id="addcc">{ts}Add CC{/ts}</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="#" id="addbcc">{ts}Add BCC{/ts}</a></span></td>
</tr>
<tr id="cc" {if ! $form.cc_id.value}style="display:none;"{/if}><td class="label">{$form.cc_id.label}</td><td>{$form.cc_id.html}</td></tr>
<tr id="bcc" {if ! $form.bcc_id.value}style="display:none;"{/if}><td class="label">{$form.bcc_id.label}</td><td>{$form.bcc_id.html}</td></tr>
<tr>
    <td class="label">{$form.subject.label}</td><td>{$form.subject.html|crmReplace:class:huge}&nbsp;{help id="id-message-text" file="CRM/Contact/Form/Task/Email.hlp"}
</td>
</tr>
</table>

{include file="CRM/Contact/Form/Task/EmailCommon.tpl"}

<div class="spacer"> </div>

<dl>
{if $single eq false}
    <dt></dt><dd>{include file="CRM/Contact/Form/Task.tpl"}</dd>
{/if}
{if $suppressedEmails > 0}
    <dt></dt><dd>{ts count=$suppressedEmails plural='Email will NOT be sent to %count contacts.'}Email will NOT be sent to %count contact.{/ts}</dd>
{/if}
</dl>
</fieldset>
<dl>{$form.buttons.html}</dl>
</div>
{literal}
<script type="text/javascript">
var toContact  = ccContact = bccContact = '';

{/literal}
{foreach from=$toContact key=id item=name}
     {literal} toContact += '{"name":"'+{/literal}"{$name}"{literal}+'","id":"'+{/literal}"{$id}"{literal}+'"},';{/literal}
{/foreach}

//loop to set the value of cc and bcc if form rule.
{foreach from=","|explode:"cc,bcc" key=key item=element}
	{assign var=currentElement value=`$element`_id}
	{foreach from=","|explode:$form.$currentElement.value key=id item=email}
		{if $email}
			{$element}{literal}Contact += '{"name":"'+{/literal}"{$email|replace:'"':''}"{literal}+'","id":"'+{/literal}"{$email|replace:'"':''}"{literal}+'"},';{/literal}
		{/if}
	{/foreach}
{/foreach}

{literal} 
toContact = '[' + toContact + ']';

cj('#addcc').toggle( function() { cj(this).text('Remove CC');
                                  cj('tr#cc').show().find('ul').find('input').focus();
                   },function() { cj(this).text('Add CC');cj('#cc_id').val('');
                                  cj('tr#cc ul li:not(:last)').remove();cj('#cc').hide();
});
cj('#addbcc').toggle( function() { cj(this).text('Remove BCC');
                                   cj('tr#bcc').show().find('ul').find('input').focus();
                    },function() { cj(this).text('Add BCC');cj('#bcc_id').val('');
                                   cj('tr#bcc ul li:not(:last)').remove();cj('#bcc').hide();
});

eval( 'tokenClass = { tokenList: "token-input-list-facebook", token: "token-input-token-facebook", tokenDelete: "token-input-delete-token-facebook", selectedToken: "token-input-selected-token-facebook", highlightedToken: "token-input-highlighted-token-facebook", dropdown: "token-input-dropdown-facebook", dropdownItem: "token-input-dropdown-item-facebook", dropdownItem2: "token-input-dropdown-item2-facebook", selectedDropdownItem: "token-input-selected-dropdown-item-facebook", inputToken: "token-input-input-token-facebook" } ');

var hintText = "{/literal}{ts}Type in a partial or complete name or email{/ts}{literal}";
var sourceDataUrl = "{/literal}{crmURL p='civicrm/ajax/checkemail' h=0 }{literal}";
var toDataUrl     = "{/literal}{crmURL p='civicrm/ajax/checkemail' q='id=1' h=0 }{literal}";

{/literal}
{if $form.to.value}
{literal}
	toContact = cj.ajax({ url: toDataUrl + "&cid={/literal}{$form.to.value|substr:0:-1}{literal}", async: false }).responseText;
{/literal}
{/if}
{literal}
eval( 'toContact = ' + toContact );
eval( 'ccContact = [' + ccContact + ']');
eval( 'bccContact = [' + bccContact + ']');

cj( "#to"     ).tokenInput( toDataUrl, { prePopulate: toContact, classes: tokenClass, hintText: hintText });
cj( "#cc_id"  ).tokenInput( sourceDataUrl, { prePopulate: ccContact, classes: tokenClass, hintText: hintText });
cj( "#bcc_id" ).tokenInput( sourceDataUrl, { prePopulate: bccContact, classes: tokenClass, hintText: hintText });
cj( 'ul.token-input-list-facebook, div.token-input-dropdown-facebook' ).css( 'width', '450px' );
</script>
{/literal}