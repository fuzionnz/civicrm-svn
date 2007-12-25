{* this template is used for adding/editing other (custom) activities. *}


{* added onload javascript for source contact*}
{if $source_contact_value and $admin }
   <script type="text/javascript">
       dojo.addOnLoad( function( ) {ldelim}
       dijit.byId( 'source_contact' ).setValue( "{$source_contact_value}", "{$source_contact_value}" )
       {rdelim} );
   </script>
{/if}

{* added onload javascript for target contact*}
{if $target_contact_value and $context eq 'standalone' }
   <script type="text/javascript">
       dojo.addOnLoad( function( ) {ldelim}
       dijit.byId( 'target_contact' ).setValue( "{$target_contact_value}", "{$target_contact_value}" )
       {rdelim} );
   </script>
{/if}

{* added onload javascript for assignee contact*}
{if $assignee_contact_value}
   <script type="text/javascript">
       dojo.addOnLoad( function( ) {ldelim}
       dijit.byId( 'assignee_contact' ).setValue( "{$assignee_contact_value}", "{$assignee_contact_value}" )
       {rdelim} );
   </script>
{/if}

{* added onload javascript for case subject*}
{if $subject_value and $context neq 'standalone'}
   <script type="text/javascript">
       dojo.addOnLoad( function( ) {ldelim}
       dijit.byId( 'case_subject' ).setValue( "{$subject_value}", "{$subject_value}" )
       {rdelim} );
   </script>
{/if}

       <fieldset>
          <legend>
           {if $action eq 1}
              {ts}New{/ts} 
           {elseif $action eq 2}
              {ts}Edit{/ts} 
           {elseif $action eq 8}
              {ts}Delete{/ts}
           {elseif $action eq 4}
              {ts}View{/ts}
           {elseif $action eq 32768}
              {ts}Detach{/ts}
           {/if}
           {$activityTypeName}
          </legend>
          { if $activityTypeDescription }  
              <div id="help">{$activityTypeDescription}</div>
          {/if}
         <table class="form-layout">
           {if $action eq 1 or $action eq 2  or $action eq 4 }
             {if $context eq ('standalone' or 'case') }
                <tr>
                   <td class="label">{$form.activity_type_id.label}</td><td class="view-value">{$form.activity_type_id.html}</td>
                </tr>
             {/if}
             <tr>
                <td class="label">{$form.source_contact.label}</td>
                <td class="view-value">
                   <div dojoType="dojox.data.QueryReadStore" jsId="contactStore" url="{$dataUrl}" class="tundra">
                       {if $admin }{$form.source_contact.html} {else} {$source_contact_value} {/if}
                   </div>
                </td>
             </tr>
             <tr>
                <td class="label">{$form.target_contact.label}</td>
                <td class="view-value">
                   <div dojoType="dojox.data.QueryReadStore" jsId="contactStore" url="{$dataUrl}" class="tundra">
                       {if $context eq 'standalone' } {$form.target_contact.html} {else} {$target_contact_value} {/if}
                   </div>
                </td>
             </tr>
             <tr>
                <td class="label">{$form.assignee_contact.label}</td>
                <td class="view-value">
                   <div dojoType="dojox.data.QueryReadStore" jsId="contactStore" url="{$dataUrl}" class="tundra">
                       {$form.assignee_contact.html}
                   </div>
                   {edit}<span class="description">{ts}You can optionally assign this activity to someone. Assigned activities will appear in their Contact Dashboard.{/ts}</span>{/edit}
                </td>
             </tr>
             {if $context neq 'standalone' }
               <tr>
                  <td class="label">{$form.case_subject.label}</td>
                  <td class="view-value">
                     <div dojoType="dojox.data.QueryReadStore" jsId="caseStore" url="{$caseUrl}" class="tundra">
                         {$form.case_subject.html}
                     </div>
                  </td>
               </tr>
             {/if}
             <tr>
                <td class="label">{$form.subject.label}</td><td class="view-value">{$form.subject.html}</td>
             </tr> 
             <tr>
                <td class="label">{$form.location.label}</td><td class="view-value">{$form.location.html}</td>
             </tr> 
             <tr>
                <td class="label">{$form.activity_date_time.label}</td>
                <td class="view-value">{$form.activity_date_time.html | crmDate }</br>
                    {if $action neq 4}
                      <span class="description">
                      {include file="CRM/common/calendar/desc.tpl" trigger=trigger_activity}
                      {include file="CRM/common/calendar/body.tpl" dateVar=activity_date_time startDate=currentYear 
                                      endDate=endYear offset=10 doTime=1 trigger=trigger_activity}
                      </span>
                   {/if}  
                </td>
             </tr> 
             <tr>
                <td class="label">{$form.duration_hours.label}</td>
                <td class="view-value">
                    {if $action eq 4}
                        {if $form.duration_hours.value}{$form.duration_hours.html} {ts}Hrs{/ts}&nbsp;&nbsp;{/if}
                        {if $form.duration_minutes.value}{$form.duration_minutes.html} {ts}Mins{/ts}{/if}
                    {else}
                        {$form.duration_hours.html} {ts}Hrs{/ts}&nbsp;&nbsp;{$form.duration_minutes.html} {ts}Mins{/ts}
                    {/if}
                </td>
             </tr> 
             <tr>
                <td class="label">{$form.status_id.label}</td><td class="view-value">{$form.status_id.html}</td>
             </tr> 
             <tr>
                <td class="label">{$form.details.label}</td><td class="view-value">{$form.details.html|crmReplace:class:huge}</td>
             </tr> 
             <tr>
                <td colspan="2">
	           {if $action eq 4} 
                       {include file="CRM/Contact/Page/View/InlineCustomData.tpl"}
                   {else}
                       {include file="CRM/Contact/Page/View/CustomData.tpl" mainEditForm=1}
                   {/if} 
                </td>
             </tr> 
             <tr>
                <td colspan="2">&nbsp;</td>
             </tr> 
           {elseif $action eq 8}
             <tr>
                <td colspan="2">
                    <div class="status">{ts 1=$delName}Are you sure you want to delete "%1"?{/ts}</div>
                </td>
             </tr>  
           {elseif $action eq 32768}
             <tr>
                <td colspan="2">
                    <div class="status">{ts 1=$delName}Are you sure you want to detach "%1" from this case?{/ts}</div>
                </td>
             </tr>  
           {/if}
             <tr>
                <td>&nbsp;</td><td>{$form.buttons.html}</td>
             </tr> 
         </table>   
      </fieldset> 

