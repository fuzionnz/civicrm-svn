{foreach from=$essays item=essay}
{assign var=name value=$essay.name}
<tr>
    <td class="grouplabel">
        {$form.essay.$name.label}<br />
	{if $name eq 'personal'}
        <br/>{$form.personalStat_quests.label}{$form.personalStat_quests.html}<br/><br/>
        <div id="id_upload_photo">
	    {$form.upload_photo.label}&nbsp;{$form.upload_photo.html}<br/>
	    {ts}(The file should be of type GIF or JPEG. The file size should be at most 2MB.){/ts}<br/><br/>
        </div>
	{/if }
        {$form.essay.$name.html} &nbsp;<br /><br />
        {$form.word_count.$name.label} &nbsp;&nbsp;{$form.word_count.$name.html}
    </td> 
</tr>
{/foreach}
</table>

{include file="CRM/Quest/Form/MatchApp/AppContainer.tpl" context="end"}
