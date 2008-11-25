{* Custom Data form*}
{strip}
{foreach from=$groupTree item=cd_edit key=group_id}
<div id="{$cd_edit.name}_show_{$cgCount}" class="section-hidden section-hidden-border">
	<a href="#" onclick="hide('{$cd_edit.name}_show_{$cgCount}'); show('{$cd_edit.name}_{$cgCount}'); return false;"><img src="{$config->resourceBase}i/TreePlus.gif" class="action-icon" alt="{ts}open section{/ts}"/></a><label>{ts}{$cd_edit.title}{/ts}</label><br />
</div>

<div id="{$cd_edit.name}_{$cgCount}" class="form-item">
	<fieldset><legend><a href="#" onclick="hide('{$cd_edit.name}_{$cgCount}'); show('{$cd_edit.name}_show_{$cgCount}'); return false;"><img src="{$config->resourceBase}i/TreeMinus.gif" class="action-icon" alt="{ts}close section{/ts}"/></a>{ts}{$cd_edit.title}{/ts}</legend>

		{if $cd_edit.help_pre}<div class="messages help">{$cd_edit.help_pre}</div>{/if}
		<dl>
			{foreach from=$cd_edit.fields item=element key=field_id}
			{assign var="element_name" value=$element.element_name}
			{if $element.is_view eq 0}{* fix for CRM-3510 *}
			{if $element.options_per_line != 0 }

			<dt>{$form.$element_name.label}</dt>
			<dd class="html-adjust">
				{assign var="count" value="1"}
				{strip}
				<table class="form-layout-compressed">
					<tr>
						{* sort by fails for option per line. Added a variable to iterate through the element array*}
						{assign var="index" value="1"}
						{foreach name=outer key=key item=item from=$form.$element_name}
						{if $index < 10}
						{assign var="index" value=`$index+1`}
						{else}
						<td class="labels font-light">{$form.$element_name.$key.html}</td>
						{if $count == $element.options_per_line}
					</tr>
					<tr>
						{assign var="count" value="1"}
						{else}
						{assign var="count" value=`$count+1`}
						{/if}
						{/if}
						{/foreach}
					</tr>
				</table>
				{/strip}
			</dd>
			{if $element.help_post}
			<dt></dt><dd class="html-adjust description">{$element.help_post}</dd>
			{/if}
			{else}
			<dt>{$form.$element_name.label}</dt>
			<dd class="html-adjust">{$form.$element_name.html}
				{if $element.html_type eq 'Radio'}
				&nbsp;&nbsp;(&nbsp;<a href="#" title="unselect" onclick="unselectRadio('{$element_name}', '{$form.formName}'); return false;" >{ts}unselect{/ts}</a>&nbsp;) 
				{/if}
				{if $element.data_type eq 'File'}
				{if $element.customValue.data}
				<span class="html-adjust"><br />
					&nbsp;{ts}Attached File{/ts}: &nbsp;
					{if $groupTree.$group_id.fields.$field_id.customValue.displayURL }
					<a href="javascript:popUp('{$groupTree.$group_id.fields.$field_id.customValue.displayURL}')" ><img src="{$groupTree.$group_id.fields.$field_id.customValue.displayURL}" height = "100" width="100"></a>
					{else}
					<a href="{$groupTree.$group_id.fields.$field_id.customValue.fileURL}">{$groupTree.$group_id.fields.$field_id.customValue.fileName}</a>
					{/if}
					{if $groupTree.$group_id.fields.$field_id.customValue.deleteURL }
					<br />
					{$groupTree.$group_id.fields.$field_id.customValue.deleteURL}
					{/if}	
				</span>  
				{/if} 
				{/if}
				{if $element.data_type eq 'Date' && $element.skip_calendar NEQ true } 
				<br />
				{if $element.skip_ampm NEQ true }
				{include file="CRM/common/calendar/desc.tpl" trigger=trigger_customdata_$field_id doTime=1}
				{include file="CRM/common/calendar/body.tpl" dateVar=$element_name startDate=$currentYear-$element.start_date_years endDate=$currentYear+$element.end_date_years doTime=1 trigger=trigger_customdata_$field_id ampm=1}
				{else}
				{include file="CRM/common/calendar/desc.tpl" trigger=trigger_customdata_$field_id}
				{include file="CRM/common/calendar/body.tpl" dateVar=$element_name startDate=$currentYear-$element.start_date_years endDate=$currentYear+$element.end_date_years doTime=1 trigger=trigger_customdata_$field_id}
				{/if} 
				{/if}
			</dd>                
			{if $element.help_post}
			<dt>&nbsp;</dt><dd class="html-adjust description">{$element.help_post}</dd>
			{/if}
			{/if}
			{/if}
			{/foreach}
		</dl>
		<div class="spacer"></div>
		{if $cd_edit.help_post}<div class="messages help">{$cd_edit.help_post}</div>{/if}
		{if $cd_edit.is_multiple}
		<div id="add-more-link-{$cgCount}"><a href="javascript:buildCustomData('{$cd_edit.extends}','{$cd_edit.extends_entity_column_id}', '{$cd_edit.extends_entity_column_value}', {$cgCount}, {$group_id}, true );">{ts}Add More{/ts}</a></div>	
		{/if}

	</fieldset>
</div>
<div id="custom_group_{$group_id}_{$cgCount}"></div>

<script type="text/javascript">
{if $cd_edit.collapse_display eq 0 }
	hide("{$cd_edit.name}_show_{$cgCount}"); show("{$cd_edit.name}_{$cgCount}");
{else}
	show("{$cd_edit.name}_show_{$cgCount}"); hide("{$cd_edit.name}_{$cgCount}");
{/if}
</script>
{/foreach}
{/strip}


