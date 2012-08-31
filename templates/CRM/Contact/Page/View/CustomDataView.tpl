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
{* Custom Data view mode*}
{assign var="customGroupCount" value = 1}
{foreach from=$viewCustomData item=customValues key=customGroupId}
  {assign var="count" value=$customGroupCount%2}
  {if ($count eq $side) or $skipTitle }
    {foreach from=$customValues item=cd_edit key=cvID}
      <div class="customFieldGroup collapsible ui-corner-all {$cd_edit.name} crm-custom-set-block-{$customGroupId}">
        <div class="collapsible-title{if $cd_edit.collapse_display} collapsed{/if}">
          {$cd_edit.title}
        </div>
        <div class="crm-summary-block" id="custom-set-block-{$customGroupId}">
          {include file="CRM/Contact/Page/View/CustomDataFieldView.tpl" customGroupId=$customGroupId}
        </div>
      </div>
    {/foreach}
  {/if}
  {assign var="customGroupCount" value = $customGroupCount+1}
{/foreach}

