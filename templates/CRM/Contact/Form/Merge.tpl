<div class='spacer'></div>
<table>
  <tr><th></th><th>{$main_name}</th><th>{$other_name}</th></tr>
  {foreach from=$diffs.contact item=field}
    <tr><th>{$form.$field.label}</th><td>{$form.$field.main.html}</td><td>{$form.$field.other.html}</td></tr>
  {/foreach}
</table>
<div class='form-item'>
  <p>{$form.buttons.html}</p>
</div>
