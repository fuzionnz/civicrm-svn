{* tpl for building phone related fields*}
{if !$addBlock}
<tr>
    <td>{ts}Phone{/ts}
         &nbsp;&nbsp;<a href="#" title={ts}Add{/ts} onClick="buildAdditionalBlocks( 'Phone', '{$className}');return false;">add</a>
    </td>
    {if $className eq 'CRM_Contact_Form_Contact'}
		<td colspan="2"></td>
		<td>{ts}Is Billing?{/ts}</td>
		<td>{ts}Primary?{/ts}</td>
	{/if}
</tr>
{/if}
<!-Add->
<tr id="Phone_Block_{$blockId}">
     <td>{$form.phone.$blockId.phone.html|crmReplace:class:twenty}&nbsp;{$form.phone.$blockId.location_type_id.html}</td>
     <td colspan="2">{$form.phone.$blockId.phone_type_id.html}</td>
	 <td align="center">{$form.phone.$blockId.is_primary.html}</td>
  {if $blockId gt 1}
   <td><a href="#" title={ts}Remove{/ts} onClick="removeBlock('Phone','{$blockId}'); return false;">{ts}remove{/ts}</a></td>
  {/if}
</tr>
<!-Add->