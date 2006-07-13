{* Quest Pre-application:  essay section *}


{include file="CRM/Quest/Form/App/AppContainer.tpl" context="begin"}

<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
<tr>
    <td colspan=2 id="category">{$wizard.currentStepRootTitle}{$wizard.currentStepTitle}</td>
</tr>
<tr>
  <td class="grouplabel">
    {edit}
    <p class="preapp-instruction">     
    {ts}To minimize the risk of losing your work, you may wish to write your essay in another program and then paste it in this box when you are ready.{/ts}
    </p> 
    {/edit}

    <label>{ts}Please answer the following questions in no more than three lines. (You may use lists instead of sentences when appropriate.) (30 words per essay).{/ts}<span class="marker">*</span></label>
  </td>
</tr>

{include file="CRM/Quest/Form/MatchApp/Essay.tpl"}

