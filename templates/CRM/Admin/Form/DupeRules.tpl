<div class="form-item">
  <h2>Matching Rules for {$contact_type}</h2>
  <table>
    <tr class="columnheader"><th>{ts}Field{/ts}</th><th>{ts}Length{/ts}</th><th>{ts}Weight{/ts}</th></tr>
    {section name=count loop=5}
      {capture assign=where}where_{$smarty.section.count.index}{/capture}
      {capture assign=length}length_{$smarty.section.count.index}{/capture}
      {capture assign=weight}weight_{$smarty.section.count.index}{/capture}
      <tr><td>{$form.$where.html}</td><td>{$form.$length.html}</td><td>{$form.$weight.html}</td></tr>
    {/section}
    <tr><td></td><td></td><td><hr /></td></tr>
    <tr><th colspan="2" style="text-align: right;">{$form.threshold.label}</th><td>{$form.threshold.html}</td></tr>
  </table>
  <p>{$form.buttons.html}</p>
</div>
