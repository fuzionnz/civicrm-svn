{*
 +--------------------------------------------------------------------+
 | CiviCRM version 3.1                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2010                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007 and the CiviCRM Licensing Exception.   |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License and the CiviCRM Licensing Exception along                  |
 | with this program; if not, contact CiviCRM LLC                     |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*}
<script type="text/javascript">var showTab = Array( );</script>
{foreach from=$groupTree item=cd_edit key=group_id}    
<h3 class="head"> 
    <span id="custom{$group_id}" class="ui-icon ui-icon-triangle-1-e"></span><a href="#">{$cd_edit.title}</a>
</h3>
<div id="customData{$group_id}" class="ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom">
    <fieldset>{include file="CRM/Custom/Form/CustomData.tpl" formEdit=true}</fieldset>
</div>
<script type="text/javascript">
{if $cd_edit.collapse_display eq 0 }
    var eleSpan          = "span#custom{$group_id}";
    var eleDiv           = "div#customData{$group_id}";
    showTab[{$group_id}] = {literal}{"spanShow":eleSpan,"divShow":eleDiv}{/literal};
{else}
    showTab[{$group_id}] = {literal}{"spanShow":""}{/literal};
{/if}
</script>
{/foreach}
