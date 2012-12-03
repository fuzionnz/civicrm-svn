{*
 +--------------------------------------------------------------------+
 | CiviCRM version
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
{include file="CRM/common/accesskeys.tpl"}
{if !empty($contactId)} {* Display contact-related footer. *}
  <div class="footer" id="record-log">
    <span class="col1">
      {if !empty($external_identifier)}{ts}External ID{/ts}:&nbsp;{$external_identifier}{/if}
      {if $action NEQ 2}&nbsp; &nbsp;{ts}CiviCRM ID{/ts}:&nbsp;{$contactId}{/if}
    </span>
    {if !empty($lastModified)}
      {ts}Last Change by{/ts} <a href="{crmURL p='civicrm/contact/view' q="action=view&reset=1&cid=`$lastModified.id`"}">{$lastModified.name}</a> ({$lastModified.date|crmDate}) &nbsp;
      {if !empty($changeLog)}
        <a href="{crmURL p='civicrm/contact/view' q="reset=1&action=browse&selectedChild=log&cid=`$contactId`"}">&raquo; {ts}View Change Log{/ts}</a>
      {/if}
    {/if}
  </div>
{/if}

<div class="footer" id="civicrm-footer">
  {include file="CRM/common/version.tpl" assign=version}
  {ts 1=$version}Powered by CiviCRM %1.{/ts}
  {if !empty($newer_civicrm_version)}
    <span class="status">{ts 1=$newer_civicrm_version}A newer version (%1){/ts}
    <a href="http://civicrm.org/download">{ts}is available for download{/ts}</a>.</span>
  {/if}
  {ts 1='http://www.gnu.org/licenses/agpl-3.0.html'}CiviCRM is openly available under the <a href='%1'>GNU AGPL License</a>.{/ts}<br/>
  <a href="http://civicrm.org/download">{ts}Download CiviCRM.{/ts}</a> &nbsp; &nbsp;
  <a href="http://issues.civicrm.org/jira/browse/CRM?report=com.atlassian.jira.plugin.system.project:roadmap-panel">{ts}View issues and report bugs.{/ts}</a> &nbsp; &nbsp;
  {docURL page="" text="Online documentation."}
</div>
{include file="CRM/common/notifications.tpl"}
