{capture assign=newEventURL}{crmURL p='civicrm/admin/event' q="action=add&reset=1"}{/capture}
{capture assign=pastEventsURL}{crmURL q="action=browse&past=true&reset=1"}{/capture}

{if $action eq 1 or $action eq 2 }
   {include file="CRM/Event/Page/ManageEventEdit.tpl"}
{/if}
<div id="help">
    <p>{ts 1=$pastEventsURL}This page lists current (in-progress) events, and upcoming events. <a href="%1">Click here</a> to browse completed (past) events.{/ts}
</div>
{if $rows}
<div id="ltype">
<p></p>
    <div class="form-item" id=event_status_id>
        {strip}
        <a href="{$newEventURL}" id="newManageEvent">&raquo; {ts}New Event Wizard{/ts}</a><br />
        <table dojoType="SortableTable" widgetId="testTable" headClass="fixedHeader" headerSortUpClass="selectedUp" headerSortDownClass="selectedDown" tbodyClass="scrollContent" enableMultipleSelect="true" enableAlternateRows="true" rowAlternateClass="alternateRow" cellpadding="0" cellspacing="0" border="0">
        <thead>
         <tr class="columnheader">
            <th field="Event" dataType="String">{ts}Event{/ts}</th>
            <th field="City" dataType="String">{ts}City{/ts}</th>
            <th field="State" dataType="String">{ts}State{/ts}</th>
            <th field="Public?" dataType="String">{ts}Public?{/ts}</th>
            <th field="Start Date" dataType="String">{ts}Start Date{/ts}</th>
	        <th field="End Date" dataType="String">{ts}End Date{/ts}</th>
	        <th field="Enabled"  dataType="String" >{ts}Enabled?{/ts}</th>
	        <th datatype="html"></th>
         </tr>
        </thead>
        <tbody> 
        {foreach from=$rows item=row}
          <tr class="{cycle values="odd-row,even-row"} {$row.class}{if NOT $row.is_active} disabled{/if}">
	        <td>{$row.title}</td>
            <td>{$row.city}</td>  
            <td>{$row.state_province}</td>	
            <td>{if $row.is_public eq 1} {ts}Yes{/ts} {else} {ts}No{/ts} {/if}</td>    
	        <td>{$row.start_date|crmDate}</td>
   	        <td>{$row.end_date|crmDate}</td>
	        <td>{if $row.is_active eq 1} {ts}Yes{/ts} {else} {ts}No{/ts} {/if}</td>
	        <td>{$row.action}</td>
          </tr>
        {/foreach}
     
        {if $past and $action ne 1 and $action ne 2} 
        <tr class="odd-row">
           <td colspan=8> <a href="{$pastEventsURL}" id="pastEvents">&raquo; {ts}Show Past Events{/ts}</a></td>
        </tr>
        {/if}
        </tbody>
        </table>
        {/strip}
      
    </div>
</div>
{else}
  {if $action ne 1}
    <div class="messages status">
    <dl>
        <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}"/></dt>
        <dd>{ts 1=$newEventURL}There are no events created yet. You can <a href="%1">add one</a>.{/ts}</dd>
        </dl>
    </div>    
  {/if}
{/if}
