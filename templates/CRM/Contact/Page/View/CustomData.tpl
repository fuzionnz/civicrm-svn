<div id="name" class="data-group form-item">
    <p>
	<label>{$displayName}</label>
    </p>
</div>

{if $action eq 4}
{if $cd}
    <p>
    <fieldset>
      <legend>View Custom Data</legend>
    </fieldset>
    </p>
{/if}
{elseif $action eq 1 or $action eq 2}
    <form {$form.attributes}>
    <p>
    <fieldset><legend>{if $action eq 1}New{else}Edit{/if} Note</legend>
    <div class="form-item">
        {$form.note.html}
        <br/>
        {$form.buttons.html}
    </div>
    </fieldset>
    </p>
    </form>
{/if}

{if $cd}
<div id="notes">
 <p>
    <div class="form-item">
       {strip}
       <table>
       <tr class="columnheader">
	<th>Note Listings</th>
	<th>Date</th>
	<th></th>
       </tr>
       {foreach from=$cd item=note}
         <tr class="{cycle values="odd-row,even-row"}">
            <td>
                {$note.note|truncate:80:"...":true}
                {* Include '(more)' link to view entire note if it has been truncated *}
                {assign var="cdize" value=$note.note|count_characters:true}
                {if $cdize GT 80}
		  <a href="{crmURL p='civicrm/contact/view/note' q="nid=`$note.id`&action=view"}">(more)</a>
                {/if}
            </td>
            <td>{$note.modified_date|date_format:"%B %e, %Y"}</td>
            <td><a href="{crmURL p='civicrm/contact/view/note' q="nid=`$note.id`&action=view"}">View</a> | 
		<a href="{crmURL p='civicrm/contact/view/note' q="nid=`$note.id`&action=update"}">Edit</a>
            </td>	
         </tr>
       {/foreach}
       </table>
       {/strip}

       {if $action eq 16 or $action eq 4}
	<br/>
       <div class="action-link">
    	 <a href="{crmURL p='civicrm/contact/view/note' q="cid=`$contactId`&action=add"}">New Note</a>
       </div>
       {/if}
    </div>
 </p>
</div>

{else}
   <div class="message status">
   <img src="{$config->resourceBase}i/Inform.gif" alt="status"> &nbsp;
   There are no cd entered for this contact. You can <a href="{crmURL p='civicrm/contact/view/note' q='action=add'}">add one</a>.
   </div>
{/if}
