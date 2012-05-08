{*
 +--------------------------------------------------------------------+
 | CiviCRM version 4.1                                                |
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
<h3>{ts}Edit Recurring Contribution{/ts}</h3>
<div class="crm-block crm-form-block crm-recurcontrib-form-block">
  <div class="crm-submit-buttons">{include file="CRM/common/formButtons.tpl" location="top"}</div>
  <table class="form-layout">
    <tr><td class="label">{$form.amount.label}</td><td>{$form.currency.html|crmReplace:class:eight}&nbsp;{$form.amount.html|crmReplace:class:eight}</td></tr>
    <tr><td class="label">{$form.installments.label}</td><td>{$form.installments.html}<br />
          <span class="description">{ts}Total number of payments to be made. Set this to 0 if this is an open-ended commitment i.e. no set end date.{/ts}</span></td></tr>
    <tr><td class="label">{$form.is_notify.label}</td><td>{$form.is_notify.html}</td></tr>
    
    {*<tr><td class="label">{$form.frequency_interval.label}</td><td>{$form.frequency_interval.html}<br />
      <span class="description">{ts}Number of time units for recurrence of payment.{/ts}</span></td></tr>
    <tr><td class="label">{$form.frequency_unit.label}</td><td>{$form.frequency_unit.html}<br />
          <span class="description">{ts}Time unit for recurrence of payment. For example, "month".{/ts}</span></td></tr>
    <tr><td class="label">{$form.cycle_day.label}</td><td>{$form.cycle_day.html}<br />
          <span class="description">{ts}Day in the period when the payment should be charged.{/ts}</span></td></tr>*}

    </table>

  <div class="crm-submit-buttons">{include file="CRM/common/formButtons.tpl" location="bottom"}</div>
</div>
  
{* include jscript to warn if unsaved form field changes *}
{include file="CRM/common/formNavigate.tpl"}
