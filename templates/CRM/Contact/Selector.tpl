  {include file="CRM/pager.tpl" location="top"}

<table>
  <tr class="columnheader">
  {foreach from=$columnHeaders item=header}
    <th>
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
  <tr class="{cycle values="odd-row,even-row"}">
    <td width="15" align="center"><input name="chk[{counter}]" type="checkbox" value="{$row.contact_id}"></td>
    <td width="15" valign="top">{$row.c_type}</td>	
    <td valign="top"><a href={$row.view}>{$row.sort_name}</a></td>
    <td valign="top">{$row.street_address}</td>
    <td valign="top">{$row.city}</td>
    <td valign="top">{$row.state}</td>
    <td valign="top">{$row.postal_code}</td>
    <td valign="top">{$row.country}</td>
    <td valign="top">{$row.email}</td>
    <td valign="top">{$row.phone}</td>
    <td valign="top" width="30"><a href="{$row.edit}">Edit</a>&nbsp; <a href="{$row.view}">View</a></td>
  </tr>
  {/foreach}

</table>

  {include file="CRM/pager.tpl" location="bottom"}
