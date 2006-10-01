<div class="form-item">
<fieldset>
    <legend>{$profileTitle}</legend>
        {if $fields}
         <table>
            <tr class="columnheader">
             <th>Name</th>
             {foreach from=$fields item=field key=name}
                <th>{$field.title}</th>
             {/foreach}
            </tr>
            {foreach from=$contactIds item=cid}
             <tr class="{cycle values="odd-row,even-row"} {$row.class}">
              <td>{$sortName.$cid}</td> 
              {foreach from=$fields item=field key=name}
                {assign var=n value=$field.name}
                <td class="compressed">{$form.field.$cid.$n.html}</td> 
              {/foreach}
             </tr>
            {/foreach}
           </tr>
         </table>
        {else}
        <dl>
          <dt></dt><dd>{$form._qf_BatchUpdateProfile_refresh.html} &nbsp; {$form._qf_BatchUpdateProfile_cancel.html}</dd>
        </dl>
        {/if}
        <dl>
            <dt></dt><dd>{if $fields}{$form._qf_BatchUpdateProfile_refresh.html}{/if} &nbsp; {$form.buttons.html}</dd>
        </dl>
</fieldset>
</div>

