{* smarty *}
{literal}
<script type="text/javascript" src="/js/HOUSE.js"></script>
{/literal}

{$form.javascript}

<form {$form.attributes}>

	{$form.mdyx.html}
	
	{if $form.hidden}
	{$form.hidden}{/if}


	{if count($form.errors) gt 0}
	<table width="100%" cellpadding="1" cellspacing="0" border="0" bgcolor="#ff9900"><tr><td>
	<table width="100%" cellpadding="10" cellspacing="0" border="0" bgcolor="#FFFFCC"><tr><td align="center">
	<span class="error" style="font-size: 13px;">Please correct the errors below.</span>
	</td></tr></table>
	</td></tr></table>
	</p>
	{/if}
	
<br/>
<div id="core">
<label><i><h1>Household</h1></i></label>
<table border = "1" cellpadding="2" cellspacing="2">
	<tr>
		<td class="form-item"><label>{$form.household_name.label} </label></td>
		<td>{$form.household_name.html}</td>
	</tr>
	<tr>
		<td class="form-item"><label>{$form.nick_name.label}</label></td>
		<td>{$form.nick_name.html}</td>
	</tr>
	<tr>
		<td class="form-item"><label>{$form.primary_contact_id.label}</label></td>
		<td>{$form.primary_contact_id.html}</td>
	</tr>
	<tr>
		<td class="form-item"><label>{$form.annual_income.label}</label></td>
		<td>{$form.annual_income.html}</td>
	</tr>

</table>

<br/>
<table cellpadding="2" cellspacing="2">		
	<tr>
		<td><label><i><h1>Communication Preferences</h1></i></label></td>
		<td></td>
	</tr>
	<tr>	
		<td>
		<table border="1" cellpadding="2" cellspacing="2" width="90%">
			<tr>
				<td class="form-item"><label>{$form.do_not_phone.label}</label></td>
				<td class="form-item">{$form.do_not_phone.html} 
	                               		      {$form.do_not_email.html} 
                                       		      {$form.do_not_mail.html}</td>
			</tr>
			<tr>
				<td class="form-item"><label>{$form.preferred_communication_method.label}</label></td>
				<td class="form-item">{$form.preferred_communication_method.html}
				<div class="description">Preferred method of communicating with this individual</div></td>
			</tr>
			</table>
			
		</td>

	</tr> 
</table>
<br/>

<label><i><h1>Location</h1></i></label>

{* STARTING UNIT gx3 LOCATION ENGINE *}


{assign var = "lid" value = "location"}

<br />

<table id = "location" border="1" cellpadding="2" cellspacing="2" width="90%">
	<tr>
		<td colspan = "2" class = "form-item">Location :</td>
	</tr>
	<tr>	
		<td class="form-item">
		{$form.$lid.context_id.html}</td>
		<td colspan=2 class="form-item">	
		{$form.$lid.is_primary.html}<label>{$form.$lid.is_primary.label}</label></td>
	</tr>

<!-- LOADING PHONE BLOCK -->
	<tr>
		
		<td class="form-item">
		<label>{$form.$lid.phone_1.label}</label></td>
		<td class="form-item">
		{$form.$lid.phone_type_1.html}{$form.$lid.phone_1.html}
		</td>
	</tr>

	<tr><!-- Second phone block.-->
		
		<td colspan="2">
		<table id="expand_phone0_2_1">
		<tr>
			<td>
			{$form.exph02_1.html}
			</td>
		</tr>
		</table></td>
	</tr>

	<tr>
	
		<td colspan="2">	

		<table id="phone0_2_1">
		<tr>
			<td class="form-item">
			<label>{$form.$lid.phone_2.label}</label>
			</td>
			<td class="form-item">
			{$form.$lid.phone_type_2.html}{$form.$lid.phone_2.html}
			</td>
		</tr>	

		<tr>
			<td colspan="2">
			{$form.hideph02_1.html}
			</td>
		</tr>
		</table></td>
	</tr>

	<tr><!-- Third phone block.-->
	
		<td colspan=2>
		<table id="expand_phone0_3_1">
			<tr>	<td>
				{$form.exph03_1.html}
				</td>
			</tr>
		</table>
	       	</td>
	</tr>
	<tr>

		<td colspan="2">
		<table id="phone0_3_1">
		<tr>
			<td class="form-item">
			<label>{$form.$lid.phone_3.label}</label></td>
			<td class="form-item">
			{$form.$lid.phone_type_3.html}{$form.$lid.phone_3.html}
			</td>
		</tr>

		<tr>
			<td colspan="2">
			{$form.hideph03_1.html}
			</td>
		</tr>
		</table></td>
	</tr>



<!-- LOADING EMAIL BLOCK -->

	<tr>
		<td class="form-item">
		<label>{$form.$lid.email.label}</label></td>
		<td class = "form-item">
		{$form.$lid.email.html}</td>
	</tr>
	<tr><!-- email 2.-->
		<td colspan="2">
		<table id="expand_email0_2_1" >
		<tr>
			<td>
			{$form.exem02_1.html}
			</td>
		</tr>
		</table>
		</td>
	</tr>

	<tr>
		<td colspan="2">
		<table id="email0_2_1">
		<tr>
			<td class="form-item">
			<label>{$form.$lid.email_secondary.label}</label>
			</td>
			<td class = "form-item">
			{$form.$lid.email_secondary.html}
			</td>
		</tr>
		<tr>
			<td colspan="2">
			{$form.hideem02_1.html}
			</td>
		</tr>
		</table>
		</td>
	</tr>
	<tr><!-- email 3.-->

		<td colspan="2">
		<table id="expand_email0_3_1" >
		<tr>
			<td>
			{$form.exem03_1.html}
			</td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td colspan="2">
		<table id="email0_3_1">
		<tr>
			<td class="form-item">
			<label>{$form.$lid.email_tertiary.label}</label></td>
			<td class = "form-item">{$form.$lid.email_tertiary.html}
			</td>
		</tr>

		<tr>
			<td colspan="2">
			{$form.hideem03_1.html}
			</td>
		</tr>
		</table></td>
	</tr>
	<tr><!-- LOADING IM BLOCK -->
		
		<td class="form-item">
		<label>{$form.$lid.im_service_id_1.label}</label>
		</td>
		<td class="form-item">
		{$form.$lid.im_service_id_1.html}{$form.$lid.im_screenname_1.html}
		<div class="description">Select IM service and enter screen-name / user id.</div>
		</td>
	</tr>
	<tr><!-- IM 2.-->
		
		<td colspan="2">
		<table id="expand_IM0_2_1" >
		<tr>
			<td>
			{$form.exim02_1.html}
			</td>
		</tr>
		</table	></td>
	</tr>
	<tr>
		<td colspan="2">
		<table id="IM0_2_1">
		<tr>
			<td class="form-item">
			<label>{$form.$lid.im_service_id_2.label}</label></td>
			<td class="form-item">
			{$form.$lid.im_service_id_2.html}{$form.$lid.im_screenname_2.html}
			<div class="description">Select IM service and enter screen-name / user id.</div></td>
		</tr>
		<tr>
			<td colspan="2">
			{$form.hideim02_1.html}
			</td>
		</tr>
		</table></td>
	</tr>
	<tr><!-- IM 3.-->
		<td colspan="2">
		<table id="expand_IM0_3_1" >
		<tr>	<td>
			{$form.exim03_1.html}
			</td>
		</tr>
		</table></td>
	</tr>
	<tr>
		<td colspan="2">	
		<table id="IM0_3_1">
		<tr>
			<td class="form-item">
			<label>{$form.$lid.im_service_id_3.label}</label></td>
			<td class="form-item">
			{$form.$lid.im_service_id_3.html}{$form.$lid.im_screenname_3.html}
			<div class="description">Select IM service and enter screen-name / user id.</div>
			</td>
		</tr>
		<tr>
			<td colspan="2">
			{$form.hideim03_1.html}
			</td>
		</tr>
		</table></td>
	</tr>
	<tr>
		
		<td class="form-item">
		<label>{$form.$lid.street.label}</label></td>
		<td class="form-item">
		{$form.$lid.street.html}<br/>
		<div class="description">Street number, street name, apartment/unit/suite - OR P.O. box</div>
		</td>
	</tr>
	<tr>
		
		<td class="form-item">
		<label>{$form.$lid.supplemental_address.label}</label></td>
		<td class="form-item">
		{$form.$lid.supplemental_address.html}<br/>
		<div class="description">Supplemental address info, e.g. c/o, department name, building name, etc.</div>
		</td>
	</tr>
	<tr>
		<td class="form-item">
		<label>{$form.$lid.city.label}</label>
		</td><td class="form-item">
		{$form.$lid.city.html}<br/>
		</td>
	</tr>
	<tr>
		<td class="form-item">
		<label>{$form.$lid.state_province_id.label}</label></td>
		<td class="form-item">
		{$form.$lid.state_province_id.html}
		</td>
	</tr>
	<tr>
		<td class="form-item">
		<label>{$form.$lid.postal_code.label}</label></td>
		<td class="form-item">
		{$form.$lid.postal_code.html}<br/>
		</td>
	</tr>
	<tr>
		<td class="form-item">
		<label>{$form.$lid.country_id.label}</label>
		</td><td class="form-item">
		{$form.$lid.country_id.html}
		</td>
	</tr>

</table>




{* ENDING UNIT gx3 LOCATION ENGINE } */

{******************************** ENDIND THE DIV SECTION **************************************}
{******************************** ENDIND THE DIV SECTION **************************************}

</div> <!-- end 'core' section of contact form -->
<br>

<div id = "buttons">
<table cellpadding="2" cellspacing="2">
<tr>
	<td class="form-item">
	{$form.buttons.html}</td>
	
</tr>
</table>
</div>

</form>
	
{literal}
<script type="text/javascript">
on_load_execute();
</script>
{/literal}

{if count($form.errors) gt 0}
{literal}
<script type="text/javascript">
on_error_execute();
</script>
{/literal}
{/if}

