{* this template is used for adding/editing tags  *}

<form {$form.attributes}>
<fieldset><legend>Tags</legend>
    <p>Current tag assignments are checked. Mark or unmark the checkboxes, and click '{if $action eq 4} Edit {else}Update Tags{/if}' to change the tags for this contact.</p>
    <div class="form-item">
      {foreach from=$category item="row" key = "id"}
         {$form.categoryList[$id].html} &nbsp;{$row} <br>
      {/foreach}
    </div>
    {if $action eq 4}	
       <div class="form-item">{$form.edit.html}</div>
    {else}
       <div class="form-item">{$form.buttons.html}</div>
    {/if}
</fieldset>
	
</form>

