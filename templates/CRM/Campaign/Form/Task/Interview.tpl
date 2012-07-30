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
{if $votingTab and $errorMessages}
  <div class='messages status'>
     <div class="icon inform-icon"></div>
        <ul>
	   {foreach from=$errorMessages item=errorMsg}	
             <li>{ts}{$errorMsg}{/ts}</li>
           {/foreach}
       </ul>
     </div>
  </div>

{elseif $voterDetails}
<div class="form-item">
<fieldset>

{if $surveyValues.instructions}
   <div id='survey_instructions' class='help'>{ts 1=$surveyValues.instructions}%1{/ts}</div>
{/if}

<div id='responseErrors' class = "hiddenElement messages crm-error"></div>

<div id='help'>
    {if $votingTab}
    {ts}Click <strong>record response</strong> button to update values for each respondent as needed.{/ts}
    {else}
    {ts}Click <strong>record response</strong> button to update values for each respondent as needed. <br />Click <strong>Release Respondents >></strong> button below to release any respondents for whom you haven't recorded a response. <br />Click <strong>Reserve More Respondents >></strong> button if you need to get more respondents to interview.{/ts}
    {/if}
</div>
 {if $instanceId}
 {capture assign=instanceURL}{crmURL p="civicrm/report/instance/$instanceId" q="reset=1"}{/capture} 
  <div class="float-right"><a href='{$instanceURL}' class="button">{ts}Survey Report{/ts}</a></div>
 {/if} 
<div id="order-by-elements" class="civireport-criteria">
   <table id="optionField" class="form-layout-compressed">
        <tr>
        <th></th>
        <th> Column</th>
        <th> Order</th>
        </tr>

	{section name=rowLoop start=1 loop=5}
	{assign var=index value=$smarty.section.rowLoop.index}
	<tr id="optionField_{$index}" class="form-item {cycle values="odd-row,even-row"}">
        <td>
        {if $index GT 1}
            <a onclick="hideRow({$index});" name="orderBy_{$index}" href="javascript:void(0)" class="form-link"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="{ts}hide field or section{/ts}"/></a>
        {else}
            {$form.buttons._qf_Interview_submit_orderBy.html}
        {/if}
        </td>
        <td> {$form.order_bys.$index.column.html}</td>
        <td> {$form.order_bys.$index.order.html}</td>
	</tr>
        {/section}
  </table>
  <div id="optionFieldLink" class="add-remove-link">
    <a onclick="showHideRow();" name="optionFieldLink" href="javascript:void(0)" class="form-link"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="{ts}show field or section{/ts}"/>{ts}another column{/ts}</a>
  </div>

  <script type="text/javascript">
            var showRows   = new Array('optionField_1');
            var hideBlocks = new Array('optionField_2','optionField_3','optionField_4');
            var rowcounter = 0;
            {literal}
            if (navigator.appName == "Microsoft Internet Explorer") {
                for ( var count = 0; count < hideBlocks.length; count++ ) {
                    var r = document.getElementById(hideBlocks[count]);
                    r.style.display = 'none';
                }
            }

            // hide and display the appropriate blocks as directed by the php code
            on_load_init_blocks( showRows, hideBlocks, '' );
	    
	    if(cj("#order_bys_2_column").val()){
	    	   cj("#optionField_2").show();
	    }
	    if(cj("#order_bys_3_column").val()){
	    	  cj("#optionField_3").show();
	    }
	    if(cj("#order_bys_4_column").val()){
	    	  cj("#optionField_4").show();
	    }

            function hideRow(i) {
                showHideRow(i);
                // clear values on hidden field, so they're not saved
                cj('select#order_by_column_'+ i).val('');
                cj('select#order_by_order_'+ i).val('ASC');
            }
            {/literal}
  </script>
</div>

<table id="voterRecords" class="display crm-copy-fields">
    <thead>
       <tr class="columnheader">
             {foreach from=$readOnlyFields item=fTitle key=fName}
	        <th {if $fName neq 'contact_type'} class="contact_details"{/if}>{$fTitle}</th>
	     {/foreach}
	    
	     {* display headers for profile survey fields *}
	     {if $surveyFields}
	        {foreach from=$surveyFields item=field key=fieldName}
                <th><img  src="{$config->resourceBase}i/copy.png" alt="{ts 1=$field.title}Click to copy %1 from row one to all rows.{/ts}" fname="{$field.name}" class="action-icon" title="{ts}Click here to copy the value in row one to ALL rows.{/ts}" />{$field.title}</th>
            {/foreach}
	     {/if}

	     <th><img  src="{$config->resourceBase}i/copy.png" alt="{ts 1=note}Click to copy %1 from row one to all rows.{/ts}" fname="note" class="action-icon" title="{ts}Click here to copy the value in row one to ALL rows.{/ts}" />{ts}Note{/ts}</th>
	     <th><img  src="{$config->resourceBase}i/copy.png" alt="{ts 1=result}Click to copy %1 from row one to all rows.{/ts}" fname="result" class="action-icon" title="{ts}Click here to copy the value in row one to ALL rows.{/ts}" />{ts}Result{/ts}</th>
	     <th><a id = "interview_voter_button" class='button' style="float:left;" href="#" title={ts}Vote{/ts} onClick="registerInterviewforall( );return false;">{ts}Record Responses for All{/ts}</a></th> 
       </tr>
    </thead>

    <tbody>
	{foreach from=$componentIds item=voterId}
	<tr id="row_{$voterId}" class="{cycle values="odd-row,even-row"}" entity_id="{$voterId}">
	    {foreach from=$readOnlyFields item=fTitle key=fName}
	       <td {if $fName neq 'contact_type'} class="name"{/if}>{$voterDetails.$voterId.$fName}</td>
	    {/foreach}

	    {* here build the survey profile fields *}
	    {if $surveyFields}
	    {foreach from=$surveyFields item=field key=fieldName}
        <td class="compressed {$field.data_type} {$fieldName}">
                {if ( $field.data_type eq 'Date') or 
            ( $fieldName eq 'thankyou_date' ) or ( $fieldName eq 'cancel_date' ) or ( $fieldName eq 'receipt_date' ) or (  $fieldName eq 'activity_date_time') }
                    {include file="CRM/common/jcalendar.tpl" elementName=$fieldName elementIndex=$voterId batchUpdate=1}
                {else}
                   {$form.field.$voterId.$fieldName.html}
                {/if}
		{if $field.html_type eq 'Autocomplete-Select'}
		  {include file="CRM/Custom/Form/AutoComplete.tpl" element_name = field[`$voterId`][`$fieldName`]}
                {/if}
		</td> 
            {/foreach}
	    {/if}
	    
	    <td class='note'>{$form.field.$voterId.note.html}</td>
	    <td class='result'>{$form.field.$voterId.result.html}</td>

	    <td>
		<a id = "interview_voter_button_{$voterId}" class='button' style="float:left;" href="#" title={ts}Vote{/ts} onClick="registerInterview( {$voterId} );return false;">
		{ts}record response{/ts}
		</a>
		{if $allowAjaxReleaseButton}
		   <a id="release_voter_button_{$voterId}" class='button'  href="#" title={ts}Release{/ts} onClick="releaseOrReserveVoter( {$voterId} );return false;">
		   {ts}release{/ts}
		   </a>
		{/if}
		<span id='restmsg_vote_{$voterId}' class="ok" style="display:none;float:right;">
		     {ts}Response Saved.{/ts}
		</span>
		
		<span id='restmsg_release_or_reserve_{$voterId}' class="ok" style="display:none;float:right;">
		  {ts}Released.{/ts}
		</span>	
	    </td>

	</tr>
	{/foreach}
    </tbody>
</table>

 {if !$votingTab}
 <div class="spacer"></div>
 <div class="crm-submit-buttons">{$form.buttons._qf_Interview_cancel_interview.html}&nbsp;{$form.buttons._qf_Interview_next_interviewToRelease.html}&nbsp;{$form.buttons._qf_Interview_done_interviewToReserve.html}</div>
 {/if}

</fieldset>
</div>


{literal}
<script type="text/javascript">
    var updateVote = "{/literal}{ts}Update Response{/ts}{literal}";
    var updateVoteforall = "{/literal}{ts}Update Responses for All{/ts}{literal}";
    cj( function( ) {
        var count = 0; var columns='';
	
        cj('#voterRecords th').each( function( ) {
          if ( cj(this).attr('class') == 'contact_details' ) {
	    columns += '{"sClass": "contact_details"},';
	  } else {
	    columns += '{ "bSortable": false },';
	  }
	  count++; 
	});

	columns    = columns.substring(0, columns.length - 1 );
	eval('columns =[' + columns + ']');

	//load jQuery data table.
        cj('#voterRecords').dataTable( {
		"sPaginationType": "full_numbers",
		"bJQueryUI"  : true,
		"aoColumns"  : columns
        });        

    });

    function registerInterview( voterId )
    {
        //reset all errors.   
        cj( '#responseErrors' ).hide( ).html( '' );
    	
	//collect all submitted data.
	var data = new Object;
	
	//get the values for common elements.
	var fieldName = 'field_' + voterId + '_custom_';
	var specialFieldType = new Array( 'radio', 'checkbox', 'select' );
	cj( '[id^="'+ fieldName +'"]' ).each( function( ) {
	    fieldType = cj( this ).attr( 'type' );
	    if ( specialFieldType.indexOf( fieldType ) == -1 ) {
	       data[cj(this).attr( 'id' )] = cj( this ).val( );
	    }
        });

	//get the values for select.
	cj( 'select[id^="'+ fieldName +'"]' ).each( function( ) {
	    value = cj(this).val( );
	    if ( cj(this).attr( 'multiple' ) ) {
	       values = value;
	       value = '';
	       if ( values ) {
	       	  submittedValues = values.toString().split(",");
		  value = new Object;
	       	  for ( val in submittedValues ) {
		      currentVal = submittedValues[val];
		      value[currentVal] = currentVal;
	       	  }
	       }
	    }
	    data[cj(this).attr( 'id' )] = value;
        });
				
	var checkBoxField = 'field['+ voterId +'][custom_';		
	cj( 'input:checkbox[name^="'+ checkBoxField +'"]' ).each( function( ) {
	     value = '';
	     if ( cj(this).is(':checked') == true ) value = 1;
	     data[cj(this).attr( 'name' )] = value;
        });
	
	var allRadios   = new Object;
	var radioField = 'field['+ voterId +'][custom_';		
	cj( 'input:radio[name^="'+ radioField +'"]' ).each( function( ) {
	    radioName = cj(this).attr( 'name' );
	    if ( cj(this).is(':checked') == true ) {
	       data[radioName] = cj(this).val();
	    }
	    allRadios[radioName] = radioName;
        });
	for ( radioName in allRadios ) {
	   if ( !data.hasOwnProperty( radioName ) ) data[radioName] = '';  
	}
	
	//carry contact related profile field data.
	fieldName = 'field_' + voterId;
	cj( '[id^="'+ fieldName +'"]' ).each( function( ) {
	    fldId = cj(this).attr( 'id' );
	    if ( fldId.indexOf( '_custom_' ) == -1 &&
	         fldId.indexOf( '_result' ) == -1  && 
		 fldId.indexOf( '_note' ) == -1  ) {
	       data[fldId] = cj( this ).val( );
	    }
        });
	
	var surveyActivityIds = {/literal}{$surveyActivityIds}{literal};
	activityId =  eval( "surveyActivityIds.activity_id_" + voterId );
	if ( !activityId ) return; 	

	data['voter_id']         = voterId;
	data['interviewer_id']   = {/literal}{$interviewerId}{literal};
	data['activity_type_id'] = {/literal}{$surveyTypeId}{literal};
	data['activity_id']      = activityId;
	data['result']           = cj( '#field_' + voterId + '_result' ).val( ); 
	data['note']             = cj( '#field_' + voterId + '_note' ).val( );
	data['surveyTitle']      = {/literal}'{$surveyValues.title|escape:javascript}'{literal};
	data['survey_id']        = {/literal}'{$surveyValues.id}'{literal};
	
	var dataUrl = {/literal}"{crmURL p='civicrm/campaign/registerInterview' h=0}"{literal}	          
	
	//post data to create interview.
	cj.post( dataUrl, data, function( interview ) {
	       if ( interview.status == 'success' ) {
	       	 cj("#row_"+voterId+' td.name').attr('class', 'name disabled' );
		 cj( '#restmsg_vote_' + voterId ).fadeIn("slow").fadeOut("slow");
		 cj( '#interview_voter_button_' + voterId ).html(updateVote);
		 cj( '#release_voter_button_' + voterId ).hide( );
	       } else if ( interview.status == 'fail' && interview.errors ) {
		 var errorList = '';
		 for ( error in interview.errors ) {
		    if ( interview.errors[error] ) errorList =  errorList + '<li>' + interview.errors[error] + '</li>';
	         }
		 if ( errorList ) {
		      var allErrors = '<div class = "icon red-icon alert-icon"></div>Please correct the following errors in the survey fields below:' + '<ul>' + errorList + '</ul>';
		    cj( '#responseErrors' ).show( ).html( allErrors );   
		 }
	       }		 
	}, 'json' );
    }
    
    function releaseOrReserveVoter( voterId ) 
    {
	if ( !voterId ) return; 

	var surveyActivityIds = {/literal}{$surveyActivityIds}{literal};
	activityId =  eval( "surveyActivityIds.activity_id_" + voterId );
	if ( !activityId ) return;
	
	var operation  = 'release';	
	var isReleaseOrReserve = cj( '#field_' + voterId + '_is_release_or_reserve' ).val( );
	if ( isReleaseOrReserve == 1 ) {
	     operation = 'reserve';
	     isReleaseOrReserve = 0;
	} else {
	     isReleaseOrReserve = 1;
	}

	var data = new Object;
	data['operation']   = operation;
	data['isDelete']    = ( operation == 'release' ) ? 1 : 0;
	data['activity_id'] = activityId; 

	var actUrl = {/literal}
	             "{crmURL p='civicrm/ajax/rest' h=0 q='className=CRM_Campaign_Page_AJAX&fnName=processVoterData'}"
	             {literal};

        //post data to release / reserve voter.
        cj.post( actUrl, 
  	         data, 
	         function( response ) {
	    	      if ( response.status == 'success' ) {
			 if ( operation == 'release' ) {
			      cj( '#interview_voter_button_' + voterId ).hide( );
			      cj( '#restmsg_release_or_reserve' + voterId ).fadeIn( 'slow' ).fadeOut( 'slow' );
			      cj( '#row_' + voterId + ' td.name' ).addClass( 'disabled' );
			      cj( '#release_voter_button_'+ voterId ).html( "{/literal}{ts}reserve{/ts}{literal}"  );
			      cj( '#release_voter_button_' + voterId ).attr('title',"{/literal}{ts}Reserve{/ts}{literal}");
			  } else {
			      cj( '#interview_voter_button_' + voterId ).show( );
			      cj( '#restmsg_release_or_reserve' + voterId ).fadeIn( 'slow' ).fadeOut( 'slow' );
			      cj( '#row_' + voterId + ' td.name' ).removeClass( 'disabled' ); 
			      cj( '#release_voter_button_'+ voterId ).html( "{/literal}{ts}release{/ts}{literal}"  );
			      cj( '#release_voter_button_' + voterId ).attr('title',"{/literal}{ts}Release{/ts}{literal}");
			  }
		      	  cj( '#field_' + voterId + '_is_release_or_reserve' ).val( isReleaseOrReserve );  
		      }	     
	         }, 
		 'json' );		     
    }

    function registerInterviewforall( )
    {
	var Ids = {/literal}{$componentIdsJson}{literal};
	  for ( var contactid in Ids ) {
		if (cj('#field_'+ Ids[contactid] +'_result').val()) {
	         	registerInterview(Ids[contactid]);
		 	cj('#interview_voter_button').html(updateVoteforall);
		}
	}

    }
    	
</script>
{/literal}
{*include batch copy js js file*}
{include file="CRM/common/batchCopy.tpl"}
{/if}

