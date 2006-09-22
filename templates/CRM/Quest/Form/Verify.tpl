{* Quest College Match New Recommender Verification Form *}
<div id="app-content">
<table cellpadding=0 cellspacing=1 border=1 class="app">
<tr>
    <td colspan=2 id="category">{ts}Registration Verification{/ts}</td>
</tr>
<tr>
    <td colspan=2 class="grouplabel"><p>{ts}Complete this form to select a password and verify your initial login to the QuestBridge Recommendations site. You will be automatically logged in to your personal <strong>Locker</strong> once you submit this form.{/ts}</p></td>
</tr>
<tr>
    <td class="grouplabel">{$form.hash.label}</td>
    <td class="fieldlabel">{$form.hash.html}<br />{ts}Please enter the exact Confirmation Code value as specified in the Recommendation Request. If the above field is already filled out, you do not need to enter any additional information to the above field.{/ts}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.email.label}</td>
    <td class="fieldlabel">{$form.email.html}<br />{ts}Please enter the exact email address as specified in the Recommendation Request.{/ts}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.password_1.label}</td>
    <td class="fieldlabel">{$form.password_1.html}<br />{ts}Please select a secure password for your ongoing login to QuestBridge.{/ts}</td>
</tr>
<tr>
    <td class="grouplabel">{$form.password_2.label}</td>
    <td class="fieldlabel">{$form.password_2.html}<br />{ts}Please re-enter the password you entered above.{/ts}</td>
</tr>
<tr><td class="grouplabel" colspan=2>{$form._qf_Verify_refresh.html}</td>
</table>
</div>
