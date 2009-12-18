{if $form.address.$blockId.street_address}
    <tr id="streetAddress_{$blockId}">
        <td colspan="2">
           {$form.address.$blockId.street_address.label}<br />
           {$form.address.$blockId.street_address.html}
        {if $parseStreetAddress eq 1 && $action eq 2}
           &nbsp;&nbsp;<a href="#" title="{ts}Edit Address Elements{/ts}" onClick="processAddressFields( 'addressElements' , '{$blockId}', 1 );return false;">{ts}Edit Address Elements{/ts}</a>
           {help id="id-edit-street-elements" file="CRM/Contact/Form/Contact.hlp"}
        {/if}
        <br />
        <span class="description font-italic">{ts}Street number, street name, apartment/unit/suite - OR P.O. box{/ts}</span>
        </td>
    </tr>
        
    {if $parseStreetAddress eq 1 && $action eq 2}
           <tr id="addressElements_{$blockId}" class=hiddenElement>
               <td>
                  {$form.address.$blockId.street_number.label}<br />
                  {$form.address.$blockId.street_number.html}
                </td>
           
               <td>
                  {$form.address.$blockId.street_name.label}<br />
                  {$form.address.$blockId.street_name.html}<br />
               </td>
               
               <td colspan="2">
                  {$form.address.$blockId.street_unit.label}<br />       
                  {$form.address.$blockId.street_unit.html}
                  <a href="#" title="{ts}Edit Street Address{/ts}" onClick="processAddressFields( 'streetAddress', '{$blockId}', 1 );return false;">{ts}Edit Complete Street Address{/ts}</a>
                  {help id="id-edit-complete-street" file="CRM/Contact/Form/Contact.hlp"} 
               </td>
           </tr>
    {/if}

{if $parseStreetAddress eq 1}
{literal}
<script type="text/javascript">
function processAddressFields( name, blockId, loadData ) {

	if ( loadData ) { 
	    var allAddressValues = {/literal}{$allAddressFieldValues}{literal};
        
	    var streetName    = eval( "allAddressValues.street_name_"    + blockId );
	    var streetUnit    = eval( "allAddressValues.street_unit_"    + blockId );
	    var streetNumber  = eval( "allAddressValues.street_number_"  + blockId );
	    var streetAddress = eval( "allAddressValues.street_address_" + blockId );
	}

	var showBlockName = '';
	var hideBlockName = '';

        if ( name == 'addressElements' ) {
             if ( loadData ) {
	          streetAddress = '';
	     }
	     
             showBlockName = 'addressElements_' + blockId;		   
	     hideBlockName = 'streetAddress_' + blockId;
	} else {
             if ( loadData ) {
                  streetNumber = streetName = streetUnit = ''; 
             }

             showBlockName = 'streetAddress_' +  blockId;
             hideBlockName = 'addressElements_'+ blockId;
       }

       show( showBlockName );
       hide( hideBlockName );

       // set the values.
       if ( loadData ) {
          cj( '#address_' + blockId +'_street_name'    ).val( streetName    );   
          cj( '#address_' + blockId +'_street_unit'    ).val( streetUnit    );
          cj( '#address_' + blockId +'_street_number'  ).val( streetNumber  );
          cj( '#address_' + blockId +'_street_address' ).val( streetAddress );
       }
}

</script>
{/literal}
{/if}
{/if}

