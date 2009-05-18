{* this template is used for adding/editing CiviCRM Menu *}
<div class="form-item">
<fieldset><legend>{if $action eq 1}{ts}New Menu{/ts}{elseif $action eq 2}{ts}Edit Menu{/ts}{else}{ts}Delete Menu{/ts}{/if}</legend>
<table class="form-layout-compressed">
{if $action eq 8}
  <div class="messages status">
    <dl>
      <dt><img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}" /></dt>
      <dd>    
        {ts}WARNING: This may mean the loss of a substantial amount of data, and the action cannot be undone.{/ts} {ts}Do you want to continue?{/ts}
      </dd>
    </dl>
  </div>
{else}
    <tr><td class="label">{$form.menu_option.label}</td><td>{$form.menu_option.html}</td></tr>
    <tr id="menu-path" style="display:none;"><td class="label">{$form.path.label}</td><td>{$form.path.html}</td></tr>
    <tr><td class="label">{$form.label.label}</td><td>{$form.label.html}</td></tr>
    <tr id="menu-url"><td class="label">{$form.url.label}</td><td>{$form.url.html}</td></tr>
    <tr><td class="label">{$form.permission.label}</td><td>{$form.permission.html}</td></tr>
    <tr><td class="label">{$form.CiviCRM_OP_OR.label}</td><td>{$form.CiviCRM_OP_OR.html}</td></tr>
    <tr><td class="label">{$form.parent_id.label}</td><td>{$form.parent_id.html}</td></tr>
    <tr><td class="label">{$form.is_active.label}</td><td>{$form.is_active.html}</td></tr>
{/if}
  <tr><td></td></tr>    
  <tr> 
    <td colspan="2">{$form.buttons.html}</td>
  </tr>
</table>   
</fieldset>
</div>

<script type="text/javascript">
{literal}
cj( function( ) {
    cj("#menu_option").change( function( ) {
        if ( cj(this).val( ) == 2 ) {
            cj("#menu-path").show( );
            cj("#menu-url").hide( );
        } else {
            cj("#menu-path").hide( );
            cj("#menu-url").show( );
        }
    });
    cj("#path").change( function( ) {
        if ( cj("#path").val( ) ) {
            cj("#label").val( cj("#path :selected").text() );
        } else {
            cj("#label").val('');
        }
    });
});
{/literal}
</script>
