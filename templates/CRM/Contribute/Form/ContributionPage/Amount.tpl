<div class="form-item">
    <fieldset><legend>{ts}Contribution Page{/ts}</legend>
    <div id="help">
        <p>
        {ts}Use this form to setup the name, description and more for a customized contribution page.{/ts}
        </p>
    </div>
    <dl>
    <dt></dt><dd>{$form.is_allow_other_amount.html} {$form.is_allow_other_amount.label}</dd>
    <dt>{$form.min_amount.label}</dt><dd>{$form.min_amount.html}</dd> 
    <dt>{$form.max_amount.label}</dt><dd>{$form.max_amount.html}</dd> 
    {if $action ne 4}
        <div id="crm-submit-buttons">
        <dt></dt><dd>{$form.buttons.html}</dd>
        </div>
    {else}
        <div id="crm-done-button">
        <dt></dt><dd>{$form.done.html}</dd>
        </div>
    {/if} {* $action ne view *}
    </dl>
    </fieldset>
</div>
{if $action eq 2 or $action eq 4} {* Update or View*}
    <p>
    <div class="action-link">
    <a href="{crmURL p='civicrm/contribute' q="action=browse&reset=1"}">&raquo;  {ts}Contribution Pages{/ts}</a>
    </div>
    </p>
{/if}
