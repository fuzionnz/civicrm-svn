{*
 +--------------------------------------------------------------------+
 | CiviCRM version 3.2                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2010                                |
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
*}
{* template for assigning the current case to another client*}
<div class="form-item">
<fieldset><legend>{ts}Change Client{/ts}</legend> 
<table>
   <tr>
	<td id='client'>{$form.change_client_id.html|crmReplace:class:big}
	&nbsp;{$form._qf_EditClient_next_edit_client.html}</td>
   </tr>
</table>
</fieldset>
</div>

{literal}
<script type="text/javascript"> 
var contactUrl = {/literal}"{crmURL p='civicrm/ajax/contactlist' q='context=newcontact' h=0 }"{literal};
var selectedContact = '';

cj( "#change_client_id").autocomplete( contactUrl, { width : 250, selectFirst : false, matchContains:true
                            }).result( function(event, data, formatted) { cj( "#contact_id" ).val( data[1] ); selectedContact = data[0];
                            }).bind( 'click', function( ) { cj( "#contact_id" ).val(''); });

function checkSelection( field ) {
    var validationMessage = '';
    var selectedContactName = cj('#change_client_id').val( );
    var clientName = new Array( );
    clientName = selectedContact.split('::');
    
    if ( selectedContactName == '' ) {
        validationMessage = '{/literal}{ts}Please select a client for this case.{/ts}{literal}';
	alert( validationMessage );
        return false;
    } else {
        validationMessage = '{/literal}{ts}Are you sure you want to reassign this case and all related activities and relationships to '+clientName[0]+'?{/ts}{literal}';
        if ( confirm( validationMessage ) ) {
	    this.href+='&amp;confirmed=1'; 
        } else {
	    return false;
	}	
    }       
    
}
</script>
{/literal}