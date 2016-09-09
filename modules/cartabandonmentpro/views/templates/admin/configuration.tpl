{*
* 2007-2014 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
{$var_ajax}
<script>
var base_url = base_url;
</script>
<div id="backgroundModal" style="width:100%;height:500%;position:absolute;background-color:black;opacity: 0.9;display:none; z-index: 10;cursor:hand;cursor:pointer;" onClick="closePreview();">&nbsp;</div>
<div id="myModal" style="display: none; position: absolute; width: 1024px; height: auto; z-index: 15; border: 1px solid black; margin :150px 22%;">
	<span class="btn btn-lg glyphicon glyphicon-remove-sign white" style="float: right; margin-right: 25px;" onClick="closePreview();"></span>
	<div id="modalContent">
		&nbsp;
	</div>
</div>
<div class="bootstrap">

{if $ps_version == 0}
	<!-- Beautiful header -->
	{include file="./header.tpl"}
{/if}
	<!-- Module content -->
	<div id="modulecontent" class="clearfix">
		<!-- Nav tabs -->
		<div class="col-lg-2">

			<div class="list-group">
				<a href="#documentation" class="list-group-item {if $conf eq 0}active{/if}" data-toggle="tab"><i class="icon-book"></i> {l s='Documentation' mod='cartabandonmentpro'}</a>
				<a href="#config" class="list-group-item {if $conf eq 1}active{/if}" data-toggle="tab"><i class="icon-briefcase"></i> {l s='Configuration' mod='cartabandonmentpro'}</a>
                <a href="#statistics" class="list-group-item" data-toggle="tab"><i class="icon-bar-chart-o"></i> {l s='Statistics' mod='cartabandonmentpro'}</a>
				<a href="#contacts" class="list-group-item" data-toggle="tab"><i class="icon-envelope"></i> {l s='Contact' mod='cartabandonmentpro'}</a>
			</div>
		</div>
		<!-- Tab panes -->
		<div class="tab-content col-lg-10">

			<div class="tab-pane {if $conf eq 0}active{/if} panel" id="documentation">
				{include file="./tabs/documentation.tpl"}
			</div>
   
			<div class="tab-pane panel {if $conf eq 1}active{/if}" id="config">
				{include file="./tabs/config.tpl"}
			</div>
			
			<div class="tab-pane panel" id="statistics">
				{include file="./tabs/statistics.tpl"}
			</div>
			{include file="./addons.tpl"}
		</div>
	</div>
	{if $ps_version == 0}
	<!-- Manage translations -->
	{include file="./translations.tpl"}
	</div>
	{/if}

</div>
<script>
{literal}
$(".onglet").click(
	function(){
		$('.onglet').removeClass('active');
		$(this).addClass('active');
	}
);
function addTemplate(){
	$("#newTemplate").show();
	$("#edit_template").hide();
}
function selectModel(id_model, id_remind){
	$(".tpl"+id_remind).css('border', '0px');
	$("#model"+id_model+"_tpl"+id_remind).css('border', '1px #585858 solid');
	$(".models").hide();
	$("#model_"+id_model+"_"+id_remind).show();
	$("#model"+id_remind).val(id_model);
	tpl = id_model;
}

<!--
/*<![CDATA[*/
function updateColor(id, color) {
	$("#"+id).css("background-color", "#"+color);
}
function updateColorEdit(id, picker) {	
	$("#"+id).css("background-color", $("#"+picker).css('background-color'));
}
/*]]>*/
-->
{/literal}
</script>