{*
 +--------------------------------------------------------------------+
 | CiviCRM version 4.2                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2012                                |
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
{* template for building email block*}
<div id="crm-email-content" class="crm-table2div-layout{if $permission EQ 'edit'} crm-inline-edit" data-edit-params='{ldelim}"cid": "{$contactId}", "class_name": "CRM_Contact_Form_Inline_Email"{rdelim}' title="{ts}Add or edit email{/ts}{/if}">
  <div class="crm-clear"><!-- start of main -->
  {if $permission EQ 'edit'}
    <div class="crm-edit-help">
      <span class="batch-edit"></span>{if empty($email)}{ts}Add email{/ts}{else}{ts}Add or edit email{/ts}{/if}
    </div>
  {/if}
  {if empty($email)}
    <div class="crm-row clearfix">
      <div class="crm-label">{ts}Email{/ts}</div>
      <div class="crm-content"></div>
    </div>
  {/if}
  {foreach from=$email key="blockId" item=item}
    {if $item.email}
    <div class="crm-row clearfix">
      <div class="crm-label">{$item.location_type}&nbsp;{ts}Email{/ts}</div>
      <div class="crm-content crm-contact_email {if $item.is_primary eq 1}primary{/if}"> <!-- start of content -->
        <span class={if $privacy.do_not_email}"do-not-email" title="{ts}Privacy flag: Do Not Email{/ts}" {elseif $item.on_hold}"email-hold" title="{ts}Email on hold - generally due to bouncing.{/ts}" {elseif $item.is_primary eq 1}"primary"{/if}><a href="mailto:{$item.email}">{$item.email}</a>{if $item.on_hold == 2}&nbsp;({ts}On Hold - Opt Out{/ts}){elseif $item.on_hold}&nbsp;({ts}On Hold{/ts}){/if}{if $item.is_bulkmail}&nbsp;({ts}Bulk{/ts}){/if}</span>
        {if $item.signature_text OR $item.signature_html}
        <span class="signature-link description">
          <a href="#" title="{ts}Signature{/ts}" onClick="showHideSignature( '{$blockId}' ); return false;">{ts}(signature){/ts}</a>
        </span>
        {/if}
        <div id="Email_Block_{$blockId}_signature" class="hiddenElement">
          <strong>{ts}Signature HTML{/ts}</strong><br />{$item.signature_html}<br /><br />
        <strong>{ts}Signature Text{/ts}</strong><br />{$item.signature_text|nl2br}</div>
      </div> <!-- end of content -->
    </div>
    {/if}
  {/foreach}
  </div> <!-- end of main -->
</div>

{literal}
<script type="text/javascript">

function showHideSignature( blockId ) {
  cj("#Email_Block_" + blockId + "_signature").show( );   

  cj("#Email_Block_" + blockId + "_signature").dialog({
      title: "Signature",
      modal: true,
      bgiframe: true,
      width: 900,
      height: 500,
      overlay: { 
          opacity: 0.5, 
          background: "black"
      },

      beforeclose: function(event, ui) {
        cj(this).dialog("destroy");
      },
      open:function() {
      },

      buttons: { 
        "Done": function() { 
                  cj(this).dialog("destroy"); 
                } 
      } 
  });
}
</script>
{/literal}
