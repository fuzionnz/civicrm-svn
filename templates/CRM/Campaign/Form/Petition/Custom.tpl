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
<div id="help">
    <p>{ts}You may want to collect information from supporters beyond what is required to sign a petition. Add any number of fields to the petition signature form by selecting CiviCRM Profiles (collections of fields) to include at the beginning of the page, and/or at the bottom.{/ts}</p>

    {if $noProfile} 
        {capture assign=crmURL}{crmURL p='civicrm/admin/uf/group' q="reset=1&action=browse"}{/capture}
        <div class="status message"> 
            {ts 1=$crmURL 2=Profile}No Profile(s) have been configured / enabled for your site. You need to first configure <a href="%1"><strong>&raquo; %2</a>(s).{/ts} {docURL page="Profiles Admin"}
        </div>
    {else}
        <p>{ts 1=$crmURL}You can use existing CiviCRM Profiles on your page - OR create profile(s) specifically for use in Online Contribution pages. Go to <a href='%1'>Administer CiviCRM Profiles</a> if you need to review, modify or create profiles (you can come back at any time to select or update the Profile(s) used for this page).{/ts}</p>
    {/if}
</div>
{if ! $noProfile} 
<div class="crm-submit-buttons">{include file="CRM/common/formButtons.tpl" location="top"}</div>
    <table class="form-layout-compressed">
    <tr class="crm-contribution-contributionpage-custom-form-block-custom_pre_id">
       <td class="label">{$form.custom_pre_id.label}
       </td>
       <td class="html-adjust">{$form.custom_pre_id.html}<br />
          <span class="description">{ts}Include additional fields in the online petition signature page by configuring and selecting a CiviCRM Profile to be included above the email information.{/ts}</span>
       </td>
    </tr>
    <tr class="crm-contribution-contributionpage-custom-form-block-custom_post_id">
       <td class="label">{$form.custom_post_id.label}
       </td>
       <td class="html-adjust">{$form.custom_post_id.html}<br />
          <span class="description">{ts}Include additional fields in the online petition signature page by configuring and selecting a CiviCRM Profile to be included at the bottom of the page.{/ts}</span>
       </td>
    </tr>
    </table>
{/if}
<div class="crm-submit-buttons">{include file="CRM/common/formButtons.tpl" location="bottom"}</div>
</div>

{* include jscript to warn if unsaved form field changes *}
{include file="CRM/common/formNavigate.tpl"}
