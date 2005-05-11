<div id="name" class="data-group">
   <div class="float-right">
        <a href="{crmURL p='civicrm/contact/edit' q="reset=1&cid=$contact_id"}">Edit Basic Contact and Location Info</a>&nbsp;&nbsp;&nbsp;
   </div>
   <div>
    {if $contact_type eq 'Individual'}
        <label>{$prefix} {$display_name} {$suffix}</label> &nbsp; &nbsp; {$job_title}
    {else}
        <label>{$sort_name}</label>
    {/if}
    {if $contactCategory}<br />{$contactCategory}{/if}
   </div>
</div>


{* Display populated Locations. Primary location expanded by default. *}
{foreach from=$location item=loc key=locationIndex}

 <div id="location[{$locationIndex}][show]" class="data-group">
  <a href="#" onClick="hide('location[{$locationIndex}][show]'); show('location[{$locationIndex}]'); return false;"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="open block"></a><label>{$loc.location_type}</label><br />
 </div>

 <div id="location[{$locationIndex}]">
  <fieldset>
   <legend{if $locationIndex eq 1} class="label"{/if}><a href="#" onClick="hide('location[{$locationIndex}]'); show('location[{$locationIndex}][show]'); return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="close block"></a>{$loc.location_type}{if $locationIndex eq 1} (primary contact location){/if}<legend/>

  <div class="col1">
    {$loc.address.street_address}<br />
    {if $loc.address.supplemental_address_1}{$loc.address.supplemental_address_1}<br />{/if}
    {$loc.address.city}, {$loc.address.state_province} {$loc.address.postal_code}<br />
    {$loc.address.country}
  </div>
  
  <div class="col2">
   {foreach from=$loc.phone item=phone}
     {if $phone.is_primary eq 1}<strong>{/if}
     {$phone.phone_type}: {$phone.phone} 
     {if $phone.is_primary eq 1}</strong>{/if}
     <br />
   {/foreach}

   {foreach from=$loc.email item=email}
     {if $email.is_primary eq 1}<strong>{/if}
     Email: {$email.email}
     {if $email.is_primary eq 1}</strong>{/if}
     <br />
   {/foreach}

   {foreach from=$loc.im item=im key=imKey}
     {if $im.is_primary eq 1}<strong>{/if}
     Instant Messenger: {$im.name} ( {$im.provider} )
     {if $im.is_primary eq 1}</strong>{/if}
     <br />
   {/foreach}
  </fieldset>
 </div>
{/foreach}

 <div id="commPrefs[show]" class="data-group">
  <a href="#" onClick="hide('commPrefs[show]'); show('commPrefs'); return false;"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="open block"></a><label>Communications Preferences</label><br />
 </div>

<div id="commPrefs">
 <fieldset>
  <legend><a href="#" onClick="hide('commPrefs'); show('commPrefs[show]'); return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="close block"></a>Communications Preferences</legend>
  <div class="col1">
    <label>Privacy:</label>
    <span class="font-red">
    {foreach from=$privacy item=privacy_val key=privacy_label}
      {if $privacy_val eq 1}{$privacy_label|replace:"_":" "|upper} &nbsp; {/if}
    {/foreach}
    </span>
  </div>
  <div class="col2">
    <label>Prefers:</label> {$preferred_communication_method}
  </div>
 </fieldset>
</div>

 {if $contact_type eq 'Individual'}
 <div id="demographics[show]" class="data-group">
  <a href="#" onClick="hide('demographics[show]'); show('demographics'); return false;"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="open block"></a><label>Demographics</label><br />
 </div>

 <div id="demographics">
  <fieldset>
   <legend><a href="#" onClick="hide('demographics'); show('demographics[show]'); return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="close block"></a>Demographics</legend>
   <div class="col1">
    <label>Gender:</label> {$gender.gender}<br />
    {if $is_deceased eq 1}
        <label>Contact is Deceased</label>
    {/if}
   </div>
   <div class="col2">
    <label>Date of Birth:</label> {$birth_date|date_format:"%B %e, %Y"}
   </div>
  </fieldset>
 </div>
 {/if}

<div id="relationships[show]" class="data-group">
  {if $relationshipsCount}
    <a href="#" onClick="hide('relationships[show]'); show('relationships'); return false;"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="open block"></a><label>Relationships</label> ({$relationshipsCount})<br />
  {else}
    <label>Relationships</label><span class="horizontal-position">None entered - use the <a href="{crmURL p='civicrm/contact/view/rel' q='action=add'}">Relationships tab</a> to add them.</span>
  {/if}
</div>

{* Relationships block display property is always none if there are 0 relationships for the contact *}
<div id="relationships">
 {if $relationship}
 <fieldset><legend><a href="#" onClick="hide('relationships'); show('relationships[show]'); return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="close block"></a>Relationships</legend>
    {strip}
        <table>
        <tr class="columnheader">
            <th>Relationship</th>
            <th>Contact</th>
            <th>Email</th>
            <th>Phone</th>
            <th>City</th>
            <th>State/Prov</th>
            <th>&nbsp;</th>
        </tr>

        {foreach from=$relationship item=rel}
          {assign var = "rtype" value = "" }
              {if $rel.contact_b > 0 }
            {assign var = "rtype" value = "b_a" }
          {else}	  
            {assign var = "rtype" value = "a_b" }
          {/if}
            <tr class="{cycle values="odd-row,even-row"}">
                    <td>
                    {$rel.relation|truncate:80:"...":true}
                    </td>
                <td><a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=`$rel.cid`"}">{$rel.name}</a></td>
                <td>{$rel.email}</td>
                <td>{$rel.phone}</td>
                <td>{$rel.city}</td>
                <td>{$rel.state}</td>
                    <td><a href="{crmURL p='civicrm/contact/view/rel' q="rid=`$rel.id`&action=update&rtype=$rtype"}">Edit</a></td> 
            </tr>  
        {/foreach}
        </table>
	{/strip}
   <div class="action-link">
       <a href="{crmURL p='civicrm/contact/view/rel' q='action=add'}">New Relationship</a>
        {if $relationshipsCount gt 3 }
         | <a href="{crmURL p='civicrm/contact/view/rel' q='action=browse'}">Browse all Relationships...</a>
        {/if}
   </div>
 </fieldset>
 {/if}
</div>

<div id="groups[show]" class="data-group">
  {if $groupCount}
    <a href="#" onClick="hide('groups[show]'); show('groups'); return false;"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="open block"></a><label>Group Memberships</label> ({$groupCount})<br />
  {else}
    <label>Group Memberships</label><span class="horizontal-position">None active memberships - use the <a href="{crmURL p='civicrm/contact/view/group' q='action=add'}">Groups tab</a> to add them.</span>
  {/if}
</div>

<div id="groups">
 {if $groupCount}
 <fieldset><legend><a href="#" onClick="hide('groups'); show('groups[show]'); return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="close block"></a>Groups</legend>
	{strip}
	<table>
        <tr class="columnheader">
		<th>Group</th>
		<th>Category</th>
		<th>Status</th>
		<th>Date Added</th>
	</tr>
    {foreach from=$groupIn item=row}
        <tr class="{cycle values="odd-row,even-row"}">
        	<td>{$row.title}</td>
	    	<td></td>	
	    	<td>Added (by {$row.in_method})</td> 
            <td>{$row.in_date|date_format:"%B %e, %Y"}</td>
        </tr>
    {/foreach}
    </table>
	{/strip}
    <p>This table currently not populated for group memberships - coming soon!</p>
   <div class="action-link">
       <a href="{crmURL p='civicrm/contact/view/group'}">New Group Membership</a>
        {if $groupCount gt 3 }
         | <a href="{crmURL p='civicrm/contact/view/group'}">Browse all Group Memberships...</a>
        {/if}
   </div>
 </fieldset>
 {/if}
</div>

<div id="notes[show]" class="data-group">
  {if $notesCount}
    <a href="#" onClick="hide('notes[show]'); show('notes'); return false;"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="open block"></a><label>Contact Notes</label> ({$notesCount})<br />
  {else}
    <label>Contact Notes</label> (No notes have been entered for this contact. Use the <a href="{crmURL p='civicrm/contact/view/note' q='action=add'}">Notes tab</a> to add them.)
  {/if}
</div>

<div id="notes">
{if $notesCount}
  <fieldset><legend><a href="#" onClick="hide('notes'); show('notes[show]'); return false;">(-)</a> Contact Notes</legend>
       {strip}
       <table>
       <tr class="columnheader">
    	<th>Note</th>
	    <th>Date</th>
	    <th></th>
       </tr>
       {foreach from=$note item=note}
       <tr class="{cycle values="odd-row,even-row"}">
            <td>
                {$note.note|truncate:80:"...":true}
                {* Include '(more)' link to view entire note if it has been truncated *}
                {assign var="noteSize" value=$note.note|count_characters:true}
                {if $noteSize GT 80}
                    <a href="{crmURL p='civicrm/contact/view/note' q="nid=`$note.id`&action=view"}">(more)</a>
                {/if}
            </td>
            <td>{$note.modified_date|date_format:"%B %e, %Y"}</td>
            <td><a href="{crmURL p='civicrm/contact/view/note' q="nid=`$note.id`&action=update"}">Edit</a></td> 
       </tr>  
       {/foreach}
       </table>
       {/strip}
       
       <div class="action-link">
        <a href="{crmURL p='civicrm/contact/view/note' q='action=add'}">New Note</a>
        {if $notesCount gt 3 }
         | <a href="{crmURL p='civicrm/contact/view/note' q='action=browse'}">Browse all notes...</a>
        {/if}
       </div>
 </fieldset>
{/if}
</div> <!-- End of Notes block -->

 <script type="text/javascript">
    var showBlocks = new Array({$showBlocks});
    var hideBlocks = new Array({$hideBlocks});

{* hide and display the appropriate blocks as directed by the php code *}
    on_load_init_blocks( showBlocks, hideBlocks );
 </script>

