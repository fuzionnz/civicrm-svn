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
<p>

{if $rows } 
<div class="form-item">
     <span class="element-right">{$form.buttons.html}</span>
</div>
<div class="spacer"></div>
<br />
<p>
<table>
  <tr class="columnheader">
    <th>{ts}Type{/ts}</th>
    <th>{ts}Subject{/ts}</th>
    <th>{ts}Added By{/ts}</th>
    <th>{ts}With{/ts}</th>
    <th>{ts}Assigned To{/ts}</th>
    <th>{ts}Date{/ts}</th>
    <th>{ts}Status{/ts}</th>
  </tr>



<td>{$row.sort_name}</td>
	<td>{$row.membership_type_id}</td>
        <td>{$row.join_date|truncate:10:''|crmDate}</td>
        <td>{$row.membership_start_date|truncate:10:''|crmDate}</td>
        <td>{$row.membership_end_date|truncate:10:''|crmDate}</td>
        <td>{$row.membership_source}</td>
        <td>{$row.status_id}</td>



{foreach from=$rows item=row}
    <tr class="{cycle values="odd-row,even-row"}">
        <td>{$row.sort_name}</td>
        <td>{$row.activity_type}</td>
        <td>{$row.subject}</td>  
        <td>{$row.source_contact_name}</td> 
        <td>{$row.target_contact_name}</td>
        <td>{$row.assignee_contact_name}</td>
        <td>{$row.activity_date_time}</td>
        <td>{$row.status}</td>
    </tr>
{/foreach}
</table>

<div class="form-item">
     <span class="element-right">{$form.buttons.html}</span>
</div>

{else}
   <div class="messages status">
    <dl>
    <dt><div class="icon inform-icon"></div></dt>
    <dd>
        {ts}There are no records selected for Print.{/ts}
    </dd>
    </dl>
   </div>
{/if}