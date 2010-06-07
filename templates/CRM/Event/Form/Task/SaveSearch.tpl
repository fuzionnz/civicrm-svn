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
<div class="crm-block crm-form-block crm-event-savesearch-form-block">
<fieldset>
    <legend>{ts}Smart Group{/ts}</legend>

{if $qill[0]}
<div id="search-status">
    <ul>
        {foreach from=$qill item=criteria}
          <li>{$criteria}</li>
        {/foreach}
    </ul>
    <br />
</div>
{/if}

 <table class="form-layout-compressed">
   <tr class="crm-event-savesearch-form-block-title">
      <td class="label">{$form.title.label}</td>
      <td>{$form.title.html}</td>
   </tr>
   <tr class="crm-event-savesearch-form-block-description">
      <td class="label">{$form.description.label}</td>
      <td>{$form.description.html}</td>
   </tr>
   <tr>
      <td colspan="2" class="label">{include file="CRM/Event/Form/Task.tpl"}</td>
   </tr>
   <tr>
      <td colspan="2">{include file="CRM/common/formButtons.tpl" location="bottom"}</td>
   </tr>
</table>

</fieldset>
</div>

