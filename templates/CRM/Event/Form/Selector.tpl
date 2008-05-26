{if $context EQ 'Search'}
    {include file="CRM/common/pager.tpl" location="top"}
{/if}

{if $context EQ 'Contact Summary'}
    {assign var='columnHeaders' value=$event_columnHeaders}
    {assign var='rows' value=$event_rows}
    {assign var='single' value=$event_single}
    {assign var='limit' value=$event_limit}
{/if}
{strip}
<table class="selector">
  <tr class="columnheader">
{if ! $event_single and $context neq 'dashboard' }
  <th scope="col" title="Select Rows">{$form.toggleSelect.html}</th> 
{/if}
  {foreach from=$columnHeaders item=header}
    <th scope="col">
    {if $header.sort}
      {assign var='key' value=$header.sort}
      {$sort->_response.$key.link}
    {else}
      {$header.name}
    {/if}
    </th>
  {/foreach}
  </tr>

  {counter start=0 skip=1 print=false}
  {foreach from=$rows item=row}
  <tr id='rowid{$row.participant_id}' class="{cycle values="odd-row,even-row"}">
     {if ! $event_single }
        {if $context neq 'dashboard' }       
            {assign var=cbName value=$row.checkbox}
            <td>{$form.$cbName.html}</td> 
 	    <td>{$row.contact_type}</td>
        {/if}	
    	<td><a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=`$row.contact_id`"}">{$row.sort_name}</a></td>
    {/if}

    <td>{$row.event_title}</td>
    {assign var="participant_id" value=$row.participant_id}
    {if $lineItems.$participant_id}
    <td>
        {foreach from=$lineItems.$participant_id item=line name=lineItemsIter}
        {$line.label}: {$line.qty}
        {if ! $smarty.foreach.lineItemsIter.last}<br>{/if}
        {/foreach}
    </td>
    {else}
    <td>{if !$row.paid && !$row.fee_level} {ts}(no fee){/ts}{else} {$row.fee_level}{/if}</td>
    {/if}
    <td>{$row.event_start_date|truncate:10:''|crmDate}
        {if $row.event_end_date && $row.event_end_date|date_format:"%Y%m%d" NEQ $row.event_start_date|date_format:"%Y%m%d"}
            <br/>- {$row.event_end_date|truncate:10:''|crmDate}
        {/if}
   </td>
    <td>{$row.participant_status_id}</td>
    <td>{$row.participant_role_id}</td>
    <td>{$row.action}</td>
   </tr>
  {/foreach}
{* Link to "View all participations" for Contact Summary selector display *}
{if ($context EQ 'Contact Summary') AND $event_pager->_totalItems GT $limit}
  <tr class="even-row">
    <td colspan="7"><a href="{crmURL p='civicrm/contact/view' q="reset=1&force=1&selectedChild=participant&cid=$contactId"}">&raquo; {ts}View all events for this contact{/ts}...</a></td></tr>
  </tr>
{/if}
{if ($context EQ 'dashboard') AND $pager->_totalItems GT $limit}
  <tr class="even-row">
    <td colspan="9"><a href="{crmURL p='civicrm/event/search' q='reset=1&force=1'}">&raquo; {ts}List more Event Participants{/ts}...</a></td></tr>
  </tr>
{/if}
</table>
{/strip}

{if $context EQ 'Search'}
 <script type="text/javascript">
 {* this function is called to change the color of selected row(s) *}
    var fname = "{$form.formName}";	
    on_load_init_checkboxes(fname);
 </script>
{/if}

{if $context EQ 'Search'}
    {include file="CRM/common/pager.tpl" location="bottom"}
{/if}
