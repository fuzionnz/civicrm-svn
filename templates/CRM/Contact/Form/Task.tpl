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
{ts 1=$totalSelectedContacts}Number of selected contacts: %1{/ts}

{if $searchtype eq 'ts_sel'}
<div id="popupContainer" class="hidden">
{ts 1=$totalSelectedContacts}Number of selected contacts: %1{/ts}    
     {include file="CRM/common/pager.tpl" location="top" noForm=1}
<table>
<tr class="columnheader">
   <th>{ts}Name{/ts}</th>
</tr>
{foreach from=$value item="row"}
<tr class="{cycle values="odd-row,even-row"}">
    <td>{$row}<br/></td>
</tr>
{/foreach}
</table>
 {include file="CRM/common/pager.tpl" location="bottom" noForm=1}
</div>
<br /><a href="#"id="button">{ts}View Selected Contacts{/ts}</a>
{/if}
{if $searchtype eq 'ts_sel'}
{literal}
<script type="text/javascript">
cj("#popupContainer").css({
		"background-color":"#E0E0E0"
		});

cj("#button").click(function(){
cj("#popupContainer").dialog({
	title: "Selected Contacts",
	width:600,
	height:400,
	modal: true,
	overlay: {
            		opacity: 0.5,
             		background: "black"
             	}
});
});
var url=location.href.split('&');
    if(url[3])
{   
	cj("#popupContainer").dialog({
			title: "Selected Contacts",
			width:600,
			height:400,
			modal: true,
			overlay: {
					opacity: 0.5,
             			 	background: "black"
             			 }
		});
}
else
{
cj(document).ready(function(){
cj("#popupContainer").hide();
cj("#button").click(function(){
		cj("#popupContainer").dialog({
			title: "Selected Contacts",
			width:600,
			height:400,
			modal: true,
			overlay: {
					opacity: 0.5,
             				background: "black"
             			}
			});
		});
});
}
</script>
{/literal}
{/if}
{if $rows } 
<div class="form-item">
<table width="30%">
  <tr class="columnheader">
    <th>{ts}Name{/ts}</th>
  </tr>
{foreach from=$rows item=row}
<tr class="{cycle values="odd-row,even-row"}">
<td>{$row.displayName}</td>
</tr>
{/foreach}
</table>
</div>
{/if}
