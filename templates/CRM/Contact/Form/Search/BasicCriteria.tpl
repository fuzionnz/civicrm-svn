{* Search criteria form elements *}
{capture assign=advSearchURL}{crmURL p='civicrm/contact/search/advanced' q="reset=1"}{/capture}
<fieldset>
    <legend><span id="searchForm_hide"><a href="#" onclick="hide('searchForm','searchForm_hide'); show('searchForm_show'); return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="{ts}close section{/ts}" /></a></span>
        {if $context EQ 'smog'}{ts}Find Members within this Group{/ts}
        {elseif $context EQ 'amtg'}{ts}Find Contacts to Add to this Group{/ts}
        {else}{ts}Search Criteria{/ts}{/if}
    </legend>
 <div class="form-item">
    {strip}
	<table class="form-layout">
		<tr>
            <td class="font-size12pt">{$form.contact_type.label}</td><td>{$form.contact_type.html}</td>
            {*FIXME : uncomment following code once we will be complete with the subgroup functionality
            {if $context EQ 'smog'}
               <td>  
                 {$form.subgroups.html}
                 {$form.subgroups_dummy.html}
              </td>
            {/if}
            *}
            <td class="label">
                {if $context EQ 'smog'}
                    {$form.group_contact_status.label}<br/>{ts 1=$form.group.html}(for %1){/ts}
                {else}
                    {$form.group.label}
                {/if}
            </td>

            <td> {if $context EQ 'smog'}
                    {$form.group_contact_status.html}
                 {else}
                    {$form.group.html}
                    {*FIXME : uncomment following code once we will be complete with the subgroup functionality
                    {$form.subgroups.html}	
                    {$form.subgroups_dummy.html}
                    *}
	             {/if}
            </td>
            <td class="label">{$form.tag.label}</td><td>{$form.tag.html}</td>
        </tr>
        <tr>
            <td class="label">{$form.sort_name.label}</td>
            <td colspan={if $context EQ 'smog'}"7"{else}"5"{/if}>{$form.sort_name.html}</td>
        </tr>
        <tr><td>&nbsp;</td>
            <td colspan={if $context EQ 'smog'}"6"{else}"4" class="report"{/if}>
                <div class="description font-italic">
                {ts 1=$advSearchURL}To search by first AND last name, enter 'lastname, firstname'. Example: 'Doe, Jane'. For partial name search, use '%partialname' ('%' equals 'begins with any combination of letters'). To search by email address, use <a href='%1'>Advanced Search</a>.{/ts}
                </div></td>
            <td class="label">{$form.buttons.html}</td>
        </tr>
        <tr>
            <td class="label" colspan={if $context EQ 'smog'}"8"{else}"6"{/if}>
                {if $context EQ 'smog'}
                     <a href="{crmURL p='civicrm/group/search/advanced' q="gid=`$group.id`&reset=1&force=1"}">&raquo; {ts}Advanced Search{/ts}</a>
                {elseif $context EQ 'amtg'}
                     <a href="{crmURL p='civicrm/contact/search/advanced' q="context=amtg&amtgID=`$group.id`&reset=1&force=1"}">&raquo; {ts}Advanced Search{/ts}</a>
                {else}
                     <a href="{$advSearchURL}">&raquo; {ts}Advanced Search{/ts}</a>
                {/if}
            </td>
        </tr>
    </table>
    {/strip}

 </div>
</fieldset>
