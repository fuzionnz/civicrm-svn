{* Search Builder *}

<div id="help">
<p>{ts}Create your search by selecting the criteria (fields) and entering the values you want to search for. You can define one or many criteria as a set:
  Include contacts where...State IS Washington AND City IS Seattle AND Birth Date is > (greater than) Jan 1, 1985</p>
<p>You can also create additional sets of criteria:</p>
<p>Also include contacts where...State IS California AND City IS Los Angeles AND Birth Date is > (greater than) Jan 1, 1985</p>
<p>Multiple criteria sets are combined (OR'd) to get the final list of contacts...{/ts}</p>

</div>

{* Table for mapping data to CRM fields *}
{include file="CRM/Contact/Form/Search/table.tpl"}


<div id="crm-submit-buttons">
    {$form.buttons.html}
</div>

{if $rowsEmpty}
    {include file="CRM/Contact/Form/Search/EmptyResults.tpl"}
{/if}

{if $rows}
    {* Search request has returned 1 or more matching rows. Display results and collapse the search criteria fieldset. *}
    {assign var="showBlock" value="'searchForm[show]'"}
    {assign var="hideBlock" value="'searchForm'"}
    
    <fieldset>
    
       {* This section handles form elements for action task select and submit *}
       {include file="CRM/Contact/Form/Search/ResultTasks.tpl"}

       {* This section displays the rows along and includes the paging controls *}
       <p>
       {include file="CRM/Contact/Form/Selector.tpl"}
       </p>

    </fieldset>
    {* END Actions/Results section *}

{/if}

{$initHideBoxes}
