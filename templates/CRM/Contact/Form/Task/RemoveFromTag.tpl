{* template to remove tags from contact  *}
<div class="form-item">
<fieldset>
<legend>
{ts}Tag Contact(s) (Remove){/ts}
</legend>
<dl>
<dt>{$form.tag_id.label}</dt><dd>{$form.tag_id.html}</dd>
<dt></dt><dd>{include file="CRM/Contact/Form/Task.tpl"}</dd>
<dt></dt><dd>{$form.buttons.html}</dd>
</dl>
</fieldset>
</div>
