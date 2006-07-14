{* Quest Pre-application: Personal Information section *}
{include file="CRM/Quest/Form/MatchApp/AppContainer.tpl" context="begin"}
<table cellpadding=0 cellspacing=1 border=1 width="90%" class="app">
<tr>
    <td colspan=2 id="category">{$wizard.currentStepRootTitle}{$wizard.currentStepTitle}</td>
</tr>
<tr>
    <td rowspan=4 valign=top class="grouplabel" width="30%">
        <label for="first_name">{ts}Legal Name{/ts}</label> <span class="marker">*</span></td>
    <td class="fieldlabel" width="70%">
        {$form.first_name.html}<br />
        {edit}{$form.first_name.label}{/edit}</td>
</tr>
<tr>
    <td class="fieldlabel">
        {$form.middle_name.html}<br />
        {edit}{$form.middle_name.label}{/edit}</td>
</tr> 
<tr>
    <td class="fieldlabel">
        {$form.last_name.html}<br />
	{edit}{$form.last_name.label}{/edit}</td>
</tr> 
<tr>
    <td class="fieldlabel">
        {$form.suffix_id.html}<br />
        {edit}{$form.suffix_id.label}{/edit}</td>
</tr> 
<tr>
    <td class="grouplabel">
        {$form.nick_name.label}</td>
    <td class="fieldlabel">
        {$form.nick_name.html}</td>
</tr>
<tr>
    <td class="grouplabel">
        {$form.upload_pics.label}</td>
    <td class="fieldlabel">
        {$form.upload_pics.html}</td>
</tr>
<tr>
    <td class="grouplabel">
        {$form.gender_id.label}</td>
    <td class="fieldlabel">
        {$form.gender_id.html}</td>
</tr>
<tr>
    <td class="grouplabel">
        {$form.location.1.email.1.email.label}</td>
    <td class="fieldlabel">
        {$form.location.1.email.1.email.html}</td>
</tr>
<tr>
    <td class="grouplabel" rowspan="6">
        <label>{ts}Permanent Address{/ts} <span class="marker">*</span></td>
    <td class="fieldlabel">
        {$form.location.1.address.street_address.html}<br />
        {edit}{ts}Number and Street (including apartment number){/ts}{/edit}
    </td>
</tr>
<tr>
    <td class="fieldlabel">
        {$form.location.1.address.supplemental_address_1.html}<br />
        {edit}{$form.location.1.address.supplemental_address_1.label}{/edit}
        </td>
</tr>
<tr>
    <td class="fieldlabel">
        {$form.location.1.address.city.html}<br />
        {edit}{$form.location.1.address.city.label}{/edit}
        </td>
</tr>
<tr>
    <td class="fieldlabel">
        {$form.location.1.address.state_province_id.html}<br />
        {edit}{ts}State (required only for USA, Canada, and Mexico) {/ts}<span class="marker">*</span>{/edit}
    </td>
</tr>
<tr>
    <td class="fieldlabel">
        {$form.location.1.address.postal_code.html} - {$form.location.1.address.postal_code_suffix.html}<br />
        {edit}{ts}USA Zip Code (Zip Plus 4 if available) OR International Postal Code{/ts} <span class="marker">*</span>{/edit}
    </td>
</tr>
<tr>
    <td class="fieldlabel">
        {$form.location.1.address.country_id.html}<br />
        {edit}{$form.location.1.address.country_id.label}{/edit}
        </td>
</tr>
<tr>
    <td class="grouplabel">
        <label>{ts}Permanent Telephone{/ts} <span class="marker">*</span></td>
    <td class="fieldlabel">
        {$form.location.1.phone.1.phone.html}<br />
        {ts}{edit}Area Code and Number. Include extension, if applicable. Include country code, if not US or Canada.{/edit}{/ts}
    </td>
</tr>
<tr>
    <td class="grouplabel" rowspan="7">
        <label>{ts}Mailing Address{/ts}</label> <span class="marker">*</span></td>
    <td class="fieldlabel">
{edit}
        <input type="checkbox" name="copy_address" value="1" onClick="copyAddress()"/> {ts}Check if same as Permanent Address{/ts}
{/edit}
    </td>
</tr>
<tr>
    <td class="fieldlabel">
        {$form.location.2.address.street_address.html}<br />
        {ts}{edit}Number and Street (including apartment number){/edit}{/ts}
    </td>
</tr>
<tr>
    <td class="fieldlabel">
        {$form.location.2.address.supplemental_address_1.html}<br />
        {edit}{$form.location.2.address.supplemental_address_1.label}{/edit}
        </td>
</tr>
<tr>
    <td class="fieldlabel">
        {$form.location.2.address.city.html}<br />
        {edit}{$form.location.2.address.city.label}{/edit}
        </td>
</tr>
<tr>
    <td class="fieldlabel">
        {$form.location.2.address.state_province_id.html}<br />
        {edit}{ts}State (required only for USA, Canada, and Mexico) <span class="marker">*</span>{/ts}{/edit}
        </td>
</tr>
<tr>
    <td class="fieldlabel">
        {$form.location.2.address.postal_code.html} - {$form.location.2.address.postal_code_suffix.html}<br />
        {edit}{ts}USA Zip Code (Zip Plus 4 if available) OR International Postal Code{/ts} <span class="marker">*</span>{/edit}
        </td>
</tr>
<tr>
    <td class="fieldlabel">
        {$form.location.2.address.country_id.html}<br />
        {edit}{$form.location.2.address.country_id.label}{/edit}
        </td>
</tr>
<tr>
    <td class="grouplabel">
        <label>{ts}Mailing Telephone{/ts}</label></td>
    <td class="fieldlabel">
        {$form.location.2.phone.1.phone.html}<br />
        {ts}{edit}Area Code and Number. Include extension, if applicable. Include country code, if not US or Canada.{/edit}{/ts}
    </td>
</tr>
<tr>
    <td class="grouplabel">
        <label>{ts}Alternate Telephone{/ts}</td>
    <td class="fieldlabel">
        {$form.location.2.phone.2.phone.html}<br />
        {ts}{edit}Area Code and Number. Include extension, if applicable. Include country code, if not US or Canada.{/edit}{/ts}
    </td>
</tr>
<tr>
    <td class="grouplabel">
        {$form.citizenship_status_id.label}</td>
    <td class="fieldlabel">
        {$form.citizenship_status_id.html}<br />
{edit}
        {ts}<strong>If you were born in the U.S., you are most likely a U.S. citizen, not a permanent resident.</strong>{/ts}<br />
        {ts}The information collected by Quest in response to this question will not be forwarded to any government agency and will be used solely for the purpose of your application to Quest. Please note that Quest does not require students to be citizens of the United States.{/ts}<br />
        <a href="javascript:popUp('http://questscholars.stanford.edu/appFAQans.htm#q6')">{ts}Please click here for more information{/ts}</a>.
{/edit}
    </td>
</tr>
<tr>
    <td class="grouplabel">
        {$form.citizenship_country_id.label}</td>
    <td class="fieldlabel">
        {$form.citizenship_country_id.html}</td>
</tr>
<tr>
    <td class="grouplabel">
        {$form.ethnicity_id_1.label}
    </td>
    <td class="fieldlabel">
        {$form.ethnicity_id_1.html}<br/>
	{ts}{edit}Quest Scholars seeks to enroll a diverse student body. Please select a response from the following list. Completion of this information is appreciated, but not required.{/edit}{/ts}
    </td>
</tr>
<tr>
    <td class="grouplabel">
        {$form.birth_date.label}
    <td class="fieldlabel">
        {$form.birth_date.html}
{edit}
        <div class="description"> 
            {include file="CRM/common/calendar/desc.tpl"}
        </div>
        {include file="CRM/common/calendar/body.tpl" dateVar=birth_date startDate=1986 endDate=currentYear}
{/edit}
    </td>
</tr>
<tr>
    <td class="grouplabel">
        {$form.home_area_id.label}</td>
    <td class="fieldlabel">
        {$form.home_area_id.html}</td>
</tr>
<tr>
    <td class="grouplabel">
        {$form.growup_country_id.label}</td>
    <td class="fieldlabel">
        {$form.growup_country_id.html}</td>
</tr>
<tr>
    <td class="grouplabel">
        {$form.years_in_us.label}</td>
    <td class="fieldlabel">
        {$form.years_in_us.html}</td>
</tr>
<tr>
    <td class="grouplabel">
        {$form.number_siblings.label}</td>
    <td class="fieldlabel">
        {$form.number_siblings.html}</td>
</tr>
<tr>
    <td class="grouplabel">
        {$form.nationality_country_id.label}</td>
    <td class="fieldlabel">
        {$form.nationality_country_id.html}</td>
</tr>
<tr>
    <td class="grouplabel">
        {$form.first_language.label}</td>
    <td class="fieldlabel">
        {$form.first_language.html}</td>
</tr>
<tr>
    <td class="grouplabel">
        {$form.primary_language.label}</td>
    <td class="fieldlabel">
        {$form.primary_language.html}</td>
</tr>
<tr>
    <td class="grouplabel">
        {$form.high_school_grad_year.label}</td>
    <td class="fieldlabel">
        {$form.high_school_grad_year.html}</td>
</tr>

</table>
{include file="CRM/Quest/Form/MatchApp/AppContainer.tpl" context="end"}

{literal}
    <script type="text/javascript">
	var field = new Array(7);

	field[0] = "[street_address]";
	field[1] = "[supplemental_address_1]";
	field[2] = "[city]";
	field[3] = "[postal_code]";
	field[4] = "[postal_code_suffix]";
	field[5] = "[state_province_id]";
	field[6] = "[country_id]";

   	function copyAddress() {
	    if (document.getElementsByName("copy_address")[0].checked) {
	  	 for (i = 0; i < field.length; i++) {
 		   	document.getElementById("location[2][address]"+field[i]).value = 
				document.getElementById("location[1][address]"+field[i]).value;
	         }
	    } else {
	  	 for (i = 0; i < field.length; i++) {
 		    document.getElementById("location[2][address]"+field[i]).value = null;
	   	 }
	    }
	}
    </script>  
{/literal}
