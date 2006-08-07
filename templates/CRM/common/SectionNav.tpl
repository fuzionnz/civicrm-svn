{* Navigation template for multi-section Wizards *}
{if count( $category.steps ) > 0}
<div id="wizard-steps">
   <ul class="section-list">
    {foreach from=$category.steps item=step}
            {assign var=i value=$smarty.section.step.iteration}
            {if $step.current}
                {assign var="stepClass" value="current-section"}
            {else}
                {assign var="stepClass" value="future-section"}
            {/if}
            {if !$step.valid}
                {assign var="stepClass" value="$stepClass not-valid"}
            {/if}
            {* Skip "Submit Application" category - it is shown separately *}
            {if $step.title EQ 'Submit Application' || ($step.title EQ 'Partner Supplements' && !$step.link)}
            {else}
                {* step.link value is passed for section usages which allow clickable navigation AND when section state is clickable *} 
                <li class="{$stepClass}">{if $step.link && !$step.current}<a href="{$step.link}">{/if}{$step.title}{if $step.link && !$step.current}</a>{/if}</li>
                {if $step.current}
                    {include file="CRM/common/WizardHeader.tpl"}
                {/if}
            {/if}
    {/foreach}
   </ul>
</div>
{/if}