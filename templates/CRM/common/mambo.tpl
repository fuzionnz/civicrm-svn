<link rel="stylesheet" href="components/com_civicrm/civicrm/css/drupal.css" type="text/css" />
<link rel="stylesheet" href="components/com_civicrm/civicrm/css/bluemarine.css" type="text/css" />
<link rel="stylesheet" href="components/com_civicrm/civicrm/css/civicrm.css" type="text/css" />

<table border="0" cellpadding="0" cellspacing="0" id="content">
  <tr>
    <td id="sidebar-left" valign="top">
       {$sidebarLeft}
    </td>
    <td valign="top">

{include file="CRM/common/status.tpl"}

<!-- .tpl file invoked: {$tplFile}. Call via form.tpl if we have a form in the page. -->
{if $isForm}
    {include file="CRM/form.tpl"}
{else}
    {include file=$tplFile}
{/if}

<div class="message status" id="feedback-request">
     <p>
     {ts 1='http://objectledge.org/jira/browse/CRM?report=com.atlassian.jira.plugin.system.project:roadmap-panel'}We are now soliciting bug reports. If you find a bug, please review the open issues in our <a href="%1" target="_blank">bug-tracking system</a>, and 'Create a New Issue' if the bug isn't already in the UNRESOLVED list.{/ts}
     </p>
     <p>
     {ts 1='http://objectledge.org/confluence/display/CRM/Demo'}Please add your comments on the look and feel of these pages, along with workflow issues, on the <a href="%1">CiviCRM Comments Page</a>.{/ts}
     </p>
</div>

</td>

</tr>
</table>
