{* smarty *}

 {literal}
  <script type="text/javascript" src="/js/Individual.js"></script>
 {/literal}

 {$form.javascript}

 <form {$form.attributes}>

	 {$form.mdyx.html}

 <table border="0" width="100%" cellpadding="2" cellspacing="2">
 <tr><td>
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
</td></tr>


 <div id="core">
<tr><td>
 <!--label><i><h1>Name and Greeting</h1></i></label-->
 <fieldset><legend>Name and Greeting</legend>
 <table border = "0" cellpadding="2" cellspacing="2" width="100%">
	 <tr>
		 <td class="form-item" width="130"><label>{$form.first_name.label}</label></td>
		 <td>
		 {$form.prefix.html}
		 {$form.first_name.html}
		 {$form.last_name.html}
		 {$form.suffix.html}
		 </td>
	 </tr>
	 <tr>
		 <td class="form-item"><label>{$form.greeting_type.label}</label></td>
		 <td class="form-item">{$form.greeting_type.html}</td>
	 </tr>
	 <tr>
		 <td class="form-item"><label>{$form.job_title.label}</label></td>
		 <td class="form-item">{$form.job_title.html}</td>
	 </tr>
 </table>
 </fieldset>
 </td></tr>
 
 <tr><td>
 {include file="CRM/Contact/Form/Contact.tpl"}  
 </td></tr>
 
 {* STARTING UNIT gx3 LOCATION ENGINE *}

 {include file="CRM/Contact/Form/Location.tpl" locloop = 4 phoneloop = 4 emailloop = 4 imloop = 4} 

 {* ENDING UNIT gx3 LOCATION ENGINE *} 

 {******************************** ENDIND THE DIV SECTION **************************************}
 {******************************** ENDIND THE DIV SECTION **************************************}

 </div> <!--end 'core' section of contact form -->



<tr><td>
 <div id = "expand_demographics">
 <table>
	 <tr>
		 <td>
		 {$form.exdemo.html}
		 </td>
	 </tr>
 </table>
</div>
</td></tr>


 <tr><td>
 <div id="demographics">
 <fieldset><legend>Demographics</legend>
 <table border="0" cellpadding="2" cellspacing="2" width="100%">
	  <!--label><i><h1>Demographics</h1></i></label-->
	 <tr>
		 <td class="form-item"><label>{$form.gender.female.label}</label></td>
		 <td class="form-item">{$form.gender.female.html}
		 {$form.gender.male.html}
		 {$form.gender.transgender.html}</td>
	 {*{html_radios options=$form.gender.values selected=$form.gender.selected separator="<br />"*}
	 </tr>
	 <tr>
		 <td class="form-item"><label>{$form.birth_date.label}</label></td>
		 <td class="form-item">{$form.birth_date.html}</td>
	 </tr>
	 <tr>
		 <td class="form-item" colspan=2>{$form.is_deceased.html}<label>{$form.is_deceased.label} </label></td>
	 </tr>
	 <tr>
		 <td class="form-item"><label> Custom demographics flds </label></td>
		 <td class="form-item">... go here ...</td>
	 </tr>
	 <tr>
		 <td colspan=2>
		 {$form.hidedemo.html}
		 </td>
	 </tr>

 </table>
 </fieldset>
 </div>
 </td></tr>
  

 {******************************** ENDIND THE DEMOGRAPHICS SECTION **************************************}
 {******************************** ENDIND THE DEMOGRAPHICS SECTION **************************************}

 
<tr><td> 
<div id = "expand_notes">
<table border="0" cellpadding="2" cellspacing="2">
	 <tr>
		 <td>
		 {$form.exnotes.html}
		 </td>
	 <tr>
 </table>
</div>
</td></tr>



 <tr><td>
 <div id = "notes">
 <fieldset><legend>Notes</legend>
 <table border="0" cellpadding="2" cellspacing="2">
	 <tr>
		 <td class="form-item">{$form.address_note.label}</td>
		 <td class="form-item">{$form.address_note.html}
		 <div class = "description">
		  Record any descriptive comments about this contact. You may add an unlimited number of notes, and view or 
		 <br/>search on them at any time.</div>
		 </td>
	 </tr>
	 <tr>	
		 <td colspan=2>{$form.hidenotes.html}</td>
	 </tr>
 </table>
 </fieldset>
</div>
 </td></tr>
</table>

 <div id = "buttons">
 <table cellpadding="2" cellspacing="2" width="100%">
 <tr>
	 <td class="form-item">
	 {$form.buttons.html}
	 </td>
 </tr>
 </table>
 </div>

 {$form.my_script.label}
 </form>


 
 {literal}
 <script type="text/javascript">
 on_load_execute(frm.name);
 </script>
 {/literal}

 {if count($form.errors) gt 0}
 {literal}
 <script type="text/javascript">
 on_error_execute(frm.name);
 </script>
 {/literal}
 {/if}



 {*{if count($form.errors) gt 0}
 {literal}
 <script type="text/javascript">
 document.forms[frm.name].elements['display_set_fields'].label = "true";
 </script>
 {/literal}
 {/if}
 
 {literal}
 <script type="text/javascript">
 on_load_execute(frm.name);
 if (document.forms[frm.name].elements['display_set_fields'].label == "true") {
 on_error_execute(frm.name);
 }
 </script>
 {/literal}
*}
