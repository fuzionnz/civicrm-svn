<div class="form-item">
<fieldset>
    <legend>Add Members to a Group</legend>
    <dl>
        <dt>{if $group.id}Group{else}{$form.group_id.label}{/if}</dt><dd>{$form.group_id.html}</dd>
        <dt></dt><dd>{include file="CRM/Contact/Form/Task.tpl"}</dd>
        <dt></dt><dd>{$form.buttons.html}</dd>
    </dl>
</fieldset>
</div>
