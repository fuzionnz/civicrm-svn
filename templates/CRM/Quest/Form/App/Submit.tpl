{* Quest Pre-application: Scholarship Information section *}

{include file="CRM/Quest/Form/App/AppContainer.tpl" context="begin"}

<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
<tr>
    <td colspan=2 id="category">{$wizard.currentStepRootTitle}{$wizard.currentStepTitle}</td>
</tr>
<tr>
<td colspan=2 class="grouplabel">
<p class="preapp-instruction">
If you have completed the application, please click the "Submit
Application" button below. Once you click "Submit", we will check the
application for any errors or missing pieces of information that are
required. Please be patient as the checking process may take a minute
or so.
<br><br>
Please note: if you need to make changes to the application
after you have submitted it, please "Submit" the application again so
it can be checked again. Thank you. 
</p>
<p>
{$form.approve.html}&nbsp;I understand that my application will be
shared with QuestBridge's partner colleges. I have filled out the
application to the best of my knowledge and understand any deliberate
misrepresentation of information will result in forfeiture of any
scholarships received. 
</td>
</table>
{include file="CRM/Quest/Form/App/AppContainer.tpl" context="end"}

