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
{* this template is used for adding/editing/viewing relationships  *}
<div class="crm-block crm-form-block">
{if $cdType }
  {include file="CRM/Custom/Form/CustomData.tpl"}
{else}
  {if $action eq 4 } {* action = view *}
    <div class="form-item">
      <fieldset><legend>{ts}View Relationship{/ts}</legend>

        <table class="view-layout">
	    {foreach from=$viewRelationship item="row"}
            <tr>
                <td class="label">{$row.relation}</td> 
                <td><a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=`$row.cid`"}">{$row.name}</a></td>
            </tr>
            {if $row.start_date}
                <tr><td class="label">{ts}Start Date:{/ts}</td><td>{$row.start_date|crmDate}</td></tr>
            {/if}
            {if $row.end_date}
                <tr><td class="label">{ts}End Date:{/ts}</td><td>{$row.end_date|crmDate}</td></tr>
            {/if}
            {if $row.description}
                <tr><td class="label">{ts}Description:{/ts}</td><td>{$row.description}</td></tr>
            {/if}
	        {foreach from=$viewNote item="rec"}
		    {if $rec }
			    <tr><td class="label">{ts}Note:{/ts}</td><td>{$rec}</td></tr>	
	   	    {/if}
            {/foreach}
            {if $row.is_permission_a_b}
                {if $row.rtype EQ 'a_b' AND $is_contact_id_a}
                     <tr><td class="label">&nbsp;</td><td><strong>'{$displayName}'</strong> can view and update information for <strong>'{$row.display_name}'</strong></td></tr>
                {else}
                     <tr><td class="label">&nbsp;</td><td><strong>'{$row.display_name}'</strong> can view and update information for <strong>'{$displayName}'</strong></td></tr>
                {/if}
            {/if}
            {if $row.is_permission_b_a}
                 {if $row.rtype EQ 'a_b' AND $is_contact_id_a}   
                     <tr><td class="label">&nbsp;</td><td><strong>'{$row.display_name}'</strong> can view and update information for <strong>'{$displayName}'</strong></td></tr>
                 {else}
                     <tr><td class="label">&nbsp;</td><td><strong>'{$displayName}'</strong> can view and update information for <strong>'{$row.display_name}'</strong></td></tr>
                 {/if}   
            {/if}
           
            <tr><td class="label">{ts}Status{/ts}</td><td>{if $row.is_active}{ts}Enabled{/ts} {else} {ts}Disabled{/ts}{/if}</td></tr>

            {include file="CRM/Custom/Page/CustomDataView.tpl"}
            <tr>
            <td></td>
            <td><input type="button" name='cancel' value="{ts}Done{/ts}" onclick="location.href='{crmURL p='civicrm/contact/view' q='action=browse&selectedChild=rel'}';"/></td>
            </tr>
            {/foreach}
		
        </table>
        </fieldset>
     </div>    
   {/if}
   {if $action eq 2 | $action eq 1} {* add and update actions *}
    <fieldset><legend>{if $action eq 1}{ts}New Relationship{/ts}{else}{ts}Edit Relationship{/ts}{/if}</legend>
        <div class="form-item">
            {if $action eq 1}
                <div class="description">
                {ts}Select the relationship type. Then locate target contact(s) for this relationship by entering a complete or partial name and clicking 'Search'.{/ts}
                </div>
            {/if}
            <dl>
            <dt>{$form.relationship_type_id.label}</dt><dd>{$form.relationship_type_id.html}
            {if $action EQ 2} {* action = update *}
                <label>{$sort_name_b}</label></dd>
                </dl>
        	    <div>
                    <dt id="employee">{ts}Current Employee?{/ts}</dt>
                    <dt id="employer">{ts}Current Employer?{/ts}</dt>
                    <dd id="current_employer">{$form.is_current_employer.html}</dd>
        	    </div>
            {else} {* action = add *}
                </dd>
		    <dt>{$form.rel_contact.label}</dt>
                {literal}
                  <script type="text/javascript">
                    var relType = 0;
                    cj( function( ) {
                        createRelation( ); 
                    	cj('#relationship_type_id').change( function() { 
                            cj('#quick-save').hide();
                            cj('#rel_contact').val('');
                            cj("input[name=rel_contact_id]").val('');
                            createRelation( ); 
                        });
                    });
                    
                    function createRelation(  ) {
                        var relType = cj('#relationship_type_id').val( );
                        if ( relType ) {
                             cj('#rel_contact').unbind( 'click' );
                             cj("input[name=rel_contact_id]").val('');
                             var dataUrl = {/literal}'{crmURL p="civicrm/ajax/contactlist" h=0 q="rel="}'{literal} + relType;
                             cj('#rel_contact').autocomplete( dataUrl, { width : 180, selectFirst : false, matchContains: true });
                             cj('#rel_contact').result(function( event, data ) {
                               	cj("input[name=rel_contact_id]").val(data[1]);
                                cj('#quick-save').show();
                             });
                        } else { 
                            cj('#rel_contact').unautocomplete( );
                            cj("input[name=rel_contact_id]").val('');
                            cj('#rel_contact').click( function() { alert( '{/literal}{ts}Please select a relationship type first.{/ts}{literal} ...' );});
                        }
                    }       
				  </script>
                {/literal}
                <dd>{$form.rel_contact.html}</dd>
                <dt> </dt>
                  <dd>
                    <span class="crm-button crm-button-type-refresh crm-button_qf_Relationship_refresh.html">{$form._qf_Relationship_refresh.html}</span>
                    <span class="crm-button crm-button-type-save crm-button_qf_Relationship_refresh_save">{$form._qf_Relationship_refresh_save.html}</span>
                    <span class="crm-button crm-button-type-cancel crm-button_qf_Relationship_cancel">{$form._qf_Relationship_cancel.html}</span>
                  </dd>
                </dl>
                <div class="clear"></div>

              {if $searchDone } {* Search button clicked *} 
                {if $searchCount || $callAjax}
                    {if $searchRows || $callAjax} {* we've got rows to display *}
                        <fieldset><legend>{ts}Mark Target Contact(s) for this Relationship{/ts}</legend>
                        <div class="description">
                            {ts}Mark the target contact(s) for this relationship if it appears below. Otherwise you may modify the search name above and click Search again.{/ts}
                        </div>
                        {strip}
				
			{if $callAjax}
			<div id="count_selected"> </div><br />
			{$form.store_contacts.html}
		
			{if $isEmployeeOf || $isEmployerOf}
			     {$form.store_employer.html}     	
			{/if}
			{include file="CRM/common/jsortable.tpl"  sourceUrl=$sourceUrl useAjax=1 callBack=1 }
			{/if}
                        <table id="rel-contacts" class="pagerDisplay">
			<thead>
                        <tr class="columnheader">
                        <th id="nosort" class="contact_select">&nbsp;</th>
                        <th>{ts}Name{/ts}</th>
                        {if $isEmployeeOf}<th id="nosort" class="current_employer">{ts}Current Employer?{/ts}</th> 
                        {elseif $isEmployerOf}<th id="nosort" class="current_employer">{ts}Current Employee?{/ts}</th>{/if}
                        <th>{ts}City{/ts}</th>
                        <th>{ts}State{/ts}</th>
                        <th>{ts}Email{/ts}</th>
                        <th>{ts}Phone{/ts}</th>
                        </tr>
			</thead>
 			<tbody>

			{if !$callAjax}
                        {foreach from=$searchRows item=row}
                        <tr class="{cycle values="odd-row,even-row"}">
                            <td class="contact_select">{$form.contact_check[$row.id].html}</td>
                            <td>{$row.type} {$row.name}</td>
                            {if $isEmployeeOf}<td>{$form.employee_of[$row.id].html}</td>
                            {elseif $isEmployerOf}<td>{$form.employer_of[$row.id].html}</td>{/if}
                            <td>{$row.city}</td>
                            <td>{$row.state}</td>
                            <td>{$row.email}</td>
                            <td>{$row.phone}</td>
                        </tr>
                        {/foreach}
			{else}
			<tr><td colspan="5" class="dataTables_empty">Loading data from server</td></tr>
			{/if}

			</tbody>
                        </table>
                        {/strip}
                        </fieldset>
                    {else} {* too many results - we're only displaying 50 *}
                        </div></fieldset>
                        {if $duplicateRelationship}  
                          {capture assign=infoMessage}{ts}Duplicate relationship.{/ts}{/capture}
                        {else}   
                          {capture assign=infoMessage}{ts}Too many matching results. Please narrow your search by entering a more complete target contact name.{/ts}{/capture}
                        {/if}  
                        {include file="CRM/common/info.tpl"}
                    {/if}
                {else} {* no valid matches for name + contact_type *}
                        </div></fieldset>
                        {capture assign=infoMessage}{ts}No matching results for{/ts} <ul><li>{ts 1=$form.rel_contact.value}Name like: %1{/ts}</li><li>{ts}Contact Type{/ts}: {$contact_type_display}</li></ul>{ts}Check your spelling, or try fewer letters for the target contact name.{/ts}{/capture}
                        {include file="CRM/common/info.tpl"}                
                {/if} {* end if searchCount *}
              {else}
                </div></fieldset>
              {/if} {* end if searchDone *}
        {/if} {* end action = add *}

        {* Only show start/end date and buttons if action=update, OR if we have $contacts (results)*}
        {if ( $searchRows OR $callAjax ) OR $action EQ 2}
            <div class="form-item">
                <dl class="html-adjust">
                    <dt>{$form.start_date.label}</dt>
                    <dd>{include file="CRM/common/jcalendar.tpl" elementName=start_date}</dd>
                    <dt>{$form.end_date.label}</dt>
                    <dd>{include file="CRM/common/jcalendar.tpl" elementName=end_date}</dd>
                    <dt>&nbsp;</dt>
                    <dd class="description">
                        {ts}If this relationship has start and/or end dates, specify them here.{/ts}
                    </dd>
                    <dt>{$form.description.label}</dt>
                    <dd>{$form.description.html}</dd>
                    <dt>{$form.note.label}</dt><dd>{$form.note.html}</dd>
                    <dt>&nbsp;</dt>
                    <dd>{$form.is_permission_a_b.html}&nbsp;<strong>{if $rtype eq 'a_b'}'{$sort_name_a}'{else}{if $sort_name_b}'{$sort_name_b}'{else}{ts}Selected contact(s){/ts}{/if}{/if}</strong> {ts}can view and update information for{/ts} <strong>{if $rtype eq 'a_b'}{if $sort_name_b}'{$sort_name_b}'{else}{ts}selected contact(s){/ts}{/if}{else}'{$sort_name_a}'{/if}</strong></dd>

                    <dt>&nbsp;</dt><dd>{$form.is_permission_b_a.html}&nbsp;<strong>{if $rtype eq 'b_a'}'{$sort_name_a}'{else}{if $sort_name_b}'{$sort_name_b}'{else}{ts}Selected contact(s){/ts}{/if}{/if}</strong> {ts}can view and update information for{/ts} <strong>{if $rtype eq 'b_a'}{if $sort_name_b}'{$sort_name_b}'{else}{ts}selected contact(s){/ts}{/if}{else}'{$sort_name_a}'{/if}</strong></dd>

                    <dt>{$form.is_active.label}</dt><dd>{$form.is_active.html}</dd>
                </dl>
            </div>
        <div id="customData"></div>
        <div class="spacer"></div>
        <dl>
      	  <dt></dt><dd>{include file="CRM/common/formButtons.tpl"}</dd>
        </dl>
        </fieldset>
        {/if}
  {/if}
 
  {if $action eq 8}
     <fieldset><legend>{ts}Delete Relationship{/ts}</legend>
       <dl>
        <div class="status">
        {capture assign=relationshipsString}{$currentRelationships.$id.relation}{ $disableRelationships.$id.relation} {$currentRelationships.$id.name}{ $disableRelationships.$id.name }{/capture}
        {ts 1=$relationshipsString}Are you sure you want to delete the Relationship '%1'?{/ts}
        </div>
        <dt></dt>
        <dd>{include file="CRM/common/formButtons.tpl"}</dd>
      </dl>
    </fieldset>	
  {/if}
{/if} {* close of custom data else*}

{if $callAjax}
{literal}
<script type="text/javascript">
	var contact_checked  = new Array();
	var employer_checked = new Array();
	var employer_holdelement = new Array();
	var countSelected = useEmployer = isRadio = 0;
	
	{/literal} {if $isEmployeeOf || $isEmployerOf} {literal}
		   var storeElement  = 'store_employers';
		   var employerClass = 'current_employer';
		   useEmployer = 1;
	{/literal} {/if} {if $isEmployeeOf} {literal}
	           isRadio = 1;
	{/literal} {/if} {literal}

	cj(document).ready(function() {
	
	// clear old data if any
	cj('#store_contacts').val('');
	if ( useEmployer ) {
	cj('#store_employers').val('');
	} 

	cj('.pagerDisplay tbody tr .contact_select input').live('click', function () {
	    
		var valueSelected = cj(this).val();	  
		if ( cj(this).attr('checked') == true ) {   
		  contact_checked[valueSelected] =  valueSelected;
		  countSelected++;
		} else if( contact_checked[valueSelected] ) {
		  delete contact_checked[valueSelected];
		  countSelected--;
		  if ( useEmployer && employer_holdelement[valueSelected] ) {
		       cj( employer_holdelement[valueSelected] ).attr('checked',false);
		       delete employer_checked[valueSelected];
		       delete employer_holdelement[valueSelected];
		  } 
	       }
	     cj('#count_selected').html(countSelected +' Contacts selected.')  
	} );
	
	if ( useEmployer ) {
	   cj('.pagerDisplay tbody tr .'+ employerClass +' input').live('click', function () {
	   	 var valueSelected = cj(this).val();	
		 if ( isRadio ) {
		       employer_checked = new Array();
		 }
	         if ( cj(this).attr('checked') == true ) {
		      
		      // add validation to match with selected contacts
		      if( !contact_checked[valueSelected] ) {
		          alert('Current employer / Current employee should be among the selected contacts.');
			  cj(this).attr('checked',false); 
		      } else {
		          employer_checked[valueSelected] = valueSelected;
			  employer_holdelement[valueSelected] = this;
		      }

		} else if ( employer_checked[valueSelected] ) {
		   delete employer_checked[valueSelected]; 
     		   delete employer_holdelement[valueSelected];
		}
	   } );
	}

	});

	function checkSelected( ) {
		 cj('.pagerDisplay tbody tr .contact_select input').each(
		 function( ) {
			   	if ( contact_checked[cj(this).val()] ) { 
		  	  	cj(this).attr('checked',true);
        		   	}
			  });
	
		if ( useEmployer ) {
		  // register new elements
		  employer_holdelement = new Array();
		  cj('.pagerDisplay tbody tr .'+ employerClass +' input').each(
		  function( ) {
			   	if ( employer_checked[cj(this).val()] ) { 
				   cj(this).attr('checked',true);
				   employer_holdelement[cj(this).val()] = this;
        		   	}
			  });  
		}	  	  
	}

	function submitAjaxData() {
		 cj('#store_contacts').val( contact_checked.toString() );
		 if ( useEmployer )  {
		    cj('#store_employers').val( employer_checked.toString() ); 
		 }
		 return true;	 
	}

</script>
{/literal}
{/if}

{if $searchRows OR $action EQ 2}
{*include custom data js file*}
{include file="CRM/common/customData.tpl"}
{literal}
<script type="text/javascript">
	
	{/literal} {if $searchRows} {literal}
	cj(".contact_select .form-checkbox").each(
		function( ) {
			  if (this) { 
		  	  cj(this).attr('checked',true);
        		  } }	  
	);
	{/literal} {/if} {literal}
	
	cj(document).ready(function() {
		{/literal}
		buildCustomData( '{$customDataType}' );
		{if $customDataSubType}
			buildCustomData( '{$customDataType}', {$customDataSubType} );
		{/if}
		{literal}
	});
</script>
{/literal}
{/if}
{if $action EQ 2}
{literal}
<script type="text/javascript">

   currentEmployer( );
   function currentEmployer( ) 
   {
      var relType = document.getElementById('relationship_type_id').value;
      if ( relType == '4_a_b' ) {
           show('current_employer', 'block');
           show('employee', 'block');
           hide('employer', 'block');
      } else if ( relType == '4_b_a' ) {
	   show('current_employer', 'block');
           show('employer', 'block');
           hide('employee', 'block');
      } else {
           hide('employer', 'block');
           hide('employee', 'block');
	   hide('current_employer', 'block');
      }
   }
</script>
{/literal}
{/if}
</div>