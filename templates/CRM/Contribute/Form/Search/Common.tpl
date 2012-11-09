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
<tr>
  <tr><td><label>{ts}Contribution Dates{/ts}</label></td></tr>
  {include file="CRM/Core/DateRange.tpl" fieldName="contribution_date" from='_low' to='_high'}
</tr>
<tr>
  <td><label>{ts}Contribution Amounts{/ts}</label> <br />
  {$form.contribution_amount_low.label}
  {$form.contribution_amount_low.html} &nbsp;&nbsp;
  {$form.contribution_amount_high.label}
  {$form.contribution_amount_high.html} </td>
  <td><label>{ts}Contribution Status{/ts}</label> <br />
  {$form.contribution_status_id.html} </td>
</tr>
<tr>
  <td>
    <div class="float-left">
      <label>{ts}Payment Method{/ts}</label> <br />
      {$form.contribution_payment_instrument_id.html}
    </div>
    <div class="float-left" id="contribution_check_number_wrapper">
      {$form.contribution_check_number.label} <br />
      {$form.contribution_check_number.html}
    </div>
  </td>
  <td>{$form.contribution_transaction_id.label} <br />
  {$form.contribution_transaction_id.html}</td>
</tr>
<tr>
  <td>
    <table style="width:auto">
      <tbody>
        <tr>
          <td>{$form.contribution_thankyou_date_is_not_null.label}</td>
          <td>{$form.contribution_thankyou_date_is_not_null.html}<span class="crm-clear-link">(<a href="javascript:unselectRadio('contribution_thankyou_date_is_not_null','{$form.formName}')">{ts}clear{/ts}</a>)</span></td>
        </tr>
        <tr>
          <td>{$form.contribution_receipt_date_is_not_null.label}</td>
          <td>{$form.contribution_receipt_date_is_not_null.html}<span class="crm-clear-link">(<a href="javascript:unselectRadio('contribution_receipt_date_is_not_null','{$form.formName}')">{ts}clear{/ts}</a>)</span></td>
        </tr>
        <tr>
          <td>{$form.contribution_test.label} {help id="is-test" file="CRM/Contact/Form/Search/Advanced"}</td>
          <td>{$form.contribution_test.html}<span class="crm-clear-link">(<a href="javascript:unselectRadio('contribution_test','{$form.formName}')">{ts}clear{/ts}</a>)</span></td>
        </tr>
      </tbody>
    </table>
  </td>
  <td>
    <table style="width:auto">
      <tbody>
        <tr>
          <td>{$form.contribution_pay_later.label}</td>
          <td>{$form.contribution_pay_later.html}<span class="crm-clear-link">(<a href="javascript:unselectRadio('contribution_pay_later','{$form.formName}')">{ts}clear{/ts}</a>)</span></td>
        </tr>
        <tr>
          <td>{$form.contribution_recurring.label}</td>
          <td>{$form.contribution_recurring.html}<span class="crm-clear-link">(<a href="javascript:unselectRadio('contribution_recurring','{$form.formName}')">{ts}clear{/ts}</a>)</span></td>
        </tr>
      </tbody>
    </table>
  </td>
</tr>
<tr>
  <td><label>{ts}Financial Type{/ts}</label> <br />
  {$form.financial_type_id.html|crmAddClass:twenty}</td>
  <td><label>{ts}Contribution Page{/ts}</label> <br />
  {$form.contribution_page_id.html|crmAddClass:twenty}</td>
</tr>
<tr>
  <td>{$form.contribution_in_honor_of.label} <br />
  {$form.contribution_in_honor_of.html|crmAddClass:twenty}</td>
  <td>{$form.contribution_source.label} <br />
  {$form.contribution_source.html|crmAddClass:twenty}</td>
</tr>
<tr>
  <td>{$form.contribution_pcp_made_through_id.label} <br />
  {$form.contribution_pcp_made_through_id.html|crmAddClass:twenty}</td>

  <td>{$form.contribution_pcp_display_in_roll.label}
  {$form.contribution_pcp_display_in_roll.html}<span class="crm-clear-link">(<a href="javascript:unselectRadio('contribution_pcp_display_in_roll','{$form.formName}')">{ts}clear{/ts}</a>)</span></td>
</tr>

<tr>
  <td><label>{ts}Currency{/ts}</label> <br />
  {$form.contribution_currency_type.html}</td>
  {if $form.contribution_batch_id.html }
    <td>{$form.contribution_batch_id.label} <br />
    {$form.contribution_batch_id.html}</td>
  {/if}
</tr>

{* campaign in contribution search *}
{include file="CRM/Campaign/Form/addCampaignToComponent.tpl" campaignContext="componentSearch"
   campaignTrClass='' campaignTdClass=''}

{if $contributeGroupTree}
<tr>
  <td colspan="2">
  {include file="CRM/Custom/Form/Search.tpl" groupTree=$contributeGroupTree showHideLinks=false}</td>
</tr>
{/if}
{literal}
  <script type="text/javascript">
    cj('#contribution_payment_instrument_id').change(function() {
      if (cj(this).val() == '4') {
        cj('#contribution_check_number_wrapper').show();
      }
      else {
        cj('#contribution_check_number_wrapper').hide();
        cj('#contribution_check_number').val('');
      }
    }).change();
  </script>
{/literal}
