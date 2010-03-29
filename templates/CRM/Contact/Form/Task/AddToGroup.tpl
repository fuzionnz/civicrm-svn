{*
 +--------------------------------------------------------------------+
 | CiviCRM version 3.1                                                |
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
<div class="crm-block crm-form-block">

<div class="form-item">
<table class="form-layout">
    {if $group.id}
       <tr><td class="label">{ts}Group{/ts}</td><td>{$form.group_id.html}</td></tr>
    {else}
        <tr><td>{$form.group_option.html}</td></tr>
        <tr id="id_existing_group">
            <td>
                <table class="form-layout">
                <tr><td class="label">{$form.group_id.label}<span class="marker">*</span></td><td>{$form.group_id.html}</td></tr>
                </table>
            </td>
        </tr>
        <tr id="id_new_group" class="html-adjust">
            <td>
                <table class="form-layout">
                <tr><td class="label">{$form.title.label}<span class="marker">*</span></td><td>{$form.title.html}</td><tr>
                <tr><td class="label">{$form.description.label}</td><td>{$form.description.html}</td></tr>
                {if $form.group_type}
                    <tr><td class="label">{$form.group_type.label}</td><td>{$form.group_type.html}</td></tr>
                {/if}
                </table>
            </td>
        </tr>
    {/if}
</table>
<table class="form-layout">
        <tr><td>{include file="CRM/Contact/Form/Task.tpl"}</td></tr>
        <tr><td>{$form.buttons.html}</td></tr>       
</table>
</div>
</div>
{include file="CRM/common/showHide.tpl"}

{if !$group.id}
{literal}
<script type="text/javascript">
showElements();
function showElements() {
    if ( document.getElementsByName('group_option')[0].checked ) {
      cj('#id_existing_group').show();
      cj('#id_new_group').hide();
    } else {
      cj('#id_new_group').show();
      cj('#id_existing_group').hide();  
    }
}
</script>
{/literal} 
{/if}