{* WizardHeader.tpl provides visual display of steps thru the wizard as well as title for current step *}
{include file="CRM/WizardHeader.tpl}
<div id="help">
    {ts}<p>You may want to collect information from contributors in addition to the name and address required
    for billing purposes. Add these custom elements to your page by selecting CiviCRM Profiles (collections of fields)
    to include at the beginning of the page, and/or below the billing section.</p>
    <p>You can use existing CiviCRM Profiles on your page - OR create profile(s) specifically for
    use in Online Contribution pages. Click <a href="{crmURL p='civicrm/admin/uf/group' q="reset=1&action=browse"}">here</a>
    if you need to review, modify or create profiles.</p>{/ts}
</div>
 
<div class="form-item">
    <fieldset><legend>{ts}Custom Page Elements{/ts}</legend>
    <dl>
    <dt>{$form.custom_pre_id.label}</dt><dd>{$form.custom_pre_id.html}</dd>
    <dt>&nbsp;</dt><dd class="description">{ts}Select a CiviCRM Profile to be included at the top of the
    page (below the introductory message, and above the billing information fields).{/ts}</dd>
    <dt>{$form.custom_post_id.label}</dt><dd>{$form.custom_post_id.html}</dd>
    <dt>&nbsp;</dt><dd class="description">{ts}Select a CiviCRM Profile to be included at the bottom of the
    page (below the billing information fields).{/ts}</dd>
    </dl>
    </fieldset>
</div>
<div id="crm-submit-buttons">
    {$form.buttons.html}
</div>
