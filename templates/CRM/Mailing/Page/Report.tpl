<fieldset>
<legend>
    {ts}Mailing Settings{/ts}
</legend>
<table class="form-layout">
<tr><td class="label">{ts}Mailing Name{/ts}</td><td>{$report.mailing.name}</td></tr>
<tr><td class="label">{ts}Subject{/ts}</td><td>{$report.mailing.subject}</td></tr>
<tr><td class="label">{ts}From{/ts}</td><td>{$report.mailing.from_name} &lt;{$report.mailing.from_email}&gt;</td></tr>
<tr><td class="label">{ts}Reply-to email{/ts}</td><td>&lt;{$report.mailing.replyto_email}&gt;</td></tr>

<tr><td class="label">{ts}URL Clickthrough tracking{/ts}</td><td>{if $report.mailing.url_tracking}{ts}On{/ts}{else}{ts}Off{/ts}{/if}</td></tr>
<tr><td class="label">{ts}Forward replies{/ts}</td><td>{if $report.mailing.forward_replies}{ts}On{/ts}{else}{ts}Off{/ts}{/if}</td></tr>
<tr><td class="label">{ts}Auto-respond to replies{/ts}</td><td>{if $report.mailing.auto_responder}{ts}On{/ts}{else}{ts}Off{/ts}{/if}</td></tr>
<tr><td class="label">{ts}Open tracking{/ts}</td><td>{if $report.mailing.open_tracking}{ts}On{/ts}{else}{ts}Off{/ts}{/if}</td></tr>
</table>
</fieldset>

<fieldset>
<legend>{ts}Recipients{/ts}</legend>
{if $report.group.include|@count}
<span class="label">{ts}Included{/ts}</span>
<table>
{foreach from=$report.group.include item=group}
<tr class="{cycle values="odd-row,even-row"}">
<td>
{if $group.mailing}
{ts}Recipients of <a href="{$group.link}">{$group.name}</a>{/ts}
{else}
{ts}Members of <a href="{$group.link}">{$group.name}</a>{/ts}
{/if}
</td>
</tr>
{/foreach}
</table>
{/if}

{if $report.group.exclude|@count}
<span class="label">{ts}Excluded{/ts}</span>
<table>
{foreach from=$report.group.exclude item=group}
<tr class="{cycle values="odd-row,even-row"}">
<td>
{if $group.mailing}
{ts}Recipients of <a href="{$group.link}">{$group.name}</a>{/ts}
{else}
{ts}Members of <a href="{$group.link}">{$group.name}</a>{/ts}
{/if}
</td>
</tr>
{/foreach}
</table>
{/if}
</fieldset>

<fieldset>
<legend>{ts}Delivery Statistics{/ts}</legend>

{if $report.jobs|@count > 1}
<table>
<tr>
<th>{ts}Status{/ts}</th>
<th>{ts}Scheduled Date{/ts}</th>
<th>{ts}Start Date{/ts}</th>
<th>{ts}End Date{/ts}</th>
<th>{ts}Queued{/ts}</th>
<th>{ts}Delivered{/ts}</th>
{if $report.mailing.open_tracking}
<th>{ts}Opened{/ts}</th>
{/if}
<th>{ts}Bounces{/ts}</th>
<th>{ts}Replies{/ts}</th>
<th>{ts}Unsubscriptions{/ts}</th>
{if $report.mailing.url_tracking}
<th>{ts}Click-throughs{/ts}</th>
{/if}
</tr>
{foreach from=$report.jobs item=job}
<tr class="{cycle value="odd-row,even-row"}">
<td>{$job.status}</td>
<td>{$job.scheduled_date|date_format}</td>
<td>{$job.start_date|date_format}</td>
<td>{$job.end_date|date_format}</td>
<td>{$job.queue}</td>
<td>{$job.delivered}</td>
{if $report.mailing.open_tracking}
<td>{$job.opened}</td>
{/if}
<td>{$job.bounce}</td>
<td>{$job.reply}</td>
<td>{$job.unsubscribe}</td>
{if $report.mailing.url_tracking}
<td>{$job.url}</td>
{/if}
</tr>
{/foreach}
<tr>
<th class="label" colspan=4>{ts}Totals{/ts}</th>
<th>{$report.event_totals.queue}</th>
<th>{$report.event_totals.delivered}</th>
{if $report.mailing.open_tracking}
<th>{$report.event_totals.opened}</th>
{/if}
<th>{$report.event_totals.bounce}</th>
<th>{$report.event_totals.reply}</th>
<th>{$report.event_totals.unsubscribe}</th>
{if $report.mailing.url_tracking}
<th>{$report.event_totals.url}</th>
{/if}
</tr>
</table>
{else}
<table class="form-layout">
<tr><td class="label">{ts}Scheduled Date{/ts}</td><td>{$report.jobs.0.scheduled_date}</td></tr>
<tr><td class="label">{ts}Start Date{/ts}</td><td>{$report.jobs.0.start_date}</td></tr>
<tr><td class="label">{ts}End Date{/ts}</td><td>{$report.jobs.0.end_date}</td></tr>
<tr><td class="label">{ts}Intended Recipients{/ts}</td><td>{$report.jobs.0.queue}</td></tr>
<tr><td class="label">{ts}Succesful Deliveries{/ts}</td><td>{$report.jobs.0.delivered}</td></tr>
{if $report.mailing.open_tracking}
<tr><td class="label">{ts}Tracked Opens{/ts}</td><td>{$report.jobs.0.opened}</td></tr>
{/if}
<tr><td class="label">{ts}Bounces{/ts}</td><td>{$report.jobs.0.bounce}</td></tr>
<tr><td class="label">{ts}Replies{/ts}</td><td>{$report.jobs.0.reply}</td></tr>
<tr><td class="label">{ts}Unsubscriptions{/ts}</td><td>{$report.jobs.0.unsubscribe}</td></tr>
{if $report.mailing.url_tracking}
<tr><td class="label">{ts}Click-throughs{/ts}</td><td>{$report.jobs.0.url}</td></tr>
{/if}
</table>
{/if}
</fieldset>

{if $report.mailing.url_tracking}
<fieldset>
<legend>{ts}Click-through Statistics{/ts}</legend>
<table>
<tr><th>Clicks</th><th>{ts}URL{/ts}</th></tr>
{foreach from=$report.click_through item=row}
<tr class="{cycle values="odd-row,even-row"}">
<td>{$row.clicks}</td>
<td><a href="{$row.url}">{$row.url}</a></td>
</tr>
{/foreach}
</table>
</fieldset>
{/if}
