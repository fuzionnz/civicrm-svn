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
{if $context EQ 'Search'}
    {include file="CRM/common/pager.tpl" location="top"}
{/if}

{strip}
<table class="selector">
<thead class="sticky">
    <tr>
    {if ! $single and $context eq 'Search' }
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
 </thead>

  {counter start=0 skip=1 print=false}
  {foreach from=$rows item=row}
  <tr id='rowid{$row.participant_id}' class="{cycle values="odd-row,even-row"}">
     {if ! $single }
        {if $context eq 'Search' }       
            {assign var=cbName value=$row.checkbox}
            <td>{$form.$cbName.html}</td> 
        {/if}	
	<td>{$row.contact_type}</td>
    	<td><a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=`$row.contact_id`"}" title="{ts}View contact record{/ts}">{$row.sort_name}</a></td>
    {/if}

    <td><a href="{crmURL p='civicrm/event/info' q="id=`$row.event_id`&reset=1"}" title="{ts}View event info page{/ts}">{$row.event_title}</a>
        {if $contactId}<br /><a href="{crmURL p='civicrm/event/search' q="reset=1&force=1&event=`$row.event_id`"}" title="{ts}List participants for this event (all statuses){/ts}">({ts}participants{/ts})</a>{/if}
    </td> 
    {assign var="participant_id" value=$row.participant_id}
    {if $lineItems.$participant_id}
        <td>
        {foreach from=$lineItems.$participant_id item=line name=lineItemsIter}
            {$line.description}: {$line.qty}
            {if ! $smarty.foreach.lineItemsIter.last}<br />{/if}
        {/foreach}
        </td>
    {else}
        <td>{if !$row.paid && !$row.participant_fee_level} {ts}(no fee){/ts}{else} {$row.participant_fee_level}{/if}</td>
    {/if}
    <td class="right nowrap">{$row.participant_fee_amount|crmMoney:$row.participant_fee_currency}</td>
    <td>{$row.event_start_date|truncate:10:''|crmDate}
        {if $row.event_end_date && $row.event_end_date|date_format:"%Y%m%d" NEQ $row.event_start_date|date_format:"%Y%m%d"}
            <br/>- {$row.event_end_date|truncate:10:''|crmDate}
        {/if}
   </td>
    <td>{$row.participant_register_date|truncate:10:''|crmDate}</td>	
    <td>{$row.participant_status_id}</td>
    <td>{$row.participant_role_id}</td>
    <td>{$row.action|replace:'xx':$participant_id}</td>
   </tr>
  {/foreach}
{* Link to "View all participants" for Dashboard and Contact Summary *}
{if $limit and $pager->_totalItems GT $limit }
  {if $context EQ 'event_dashboard' }
    <tr class="even-row">
    <td colspan="10"><a href="{crmURL p='civicrm/event/search' q='reset=1'}">&raquo; {ts}Find more event participants{/ts}...</a></td></tr>
    </tr>
  {elseif $context eq 'participant' }  
    <tr class="even-row">
    <td colspan="7"><a href="{crmURL p='civicrm/contact/view' q="reset=1&force=1&selectedChild=participant&cid=$contactId"}">&raquo; {ts}View all events for this contact{/ts}...</a></td></tr>
    </tr>
  {/if}
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
