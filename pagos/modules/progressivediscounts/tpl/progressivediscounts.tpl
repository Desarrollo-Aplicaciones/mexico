<script type="text/javascript" src="{$module_template_dir}js/events.js"></script>
<link rel="stylesheet" type="text/css" href="{$module_template_dir}/css/styles.css" media="screen" />
<link rel="stylesheet" type="text/css" href="../js/jquery/plugins/fancybox/jquery.fancybox.css" media="screen" />
<script type="text/javascript" src="../js/jquery/plugins/fancybox/jquery.fancybox.js"></script>

<div>
	<form id="form_progressive_discounts" enctype="multipart/form-data" method="post" >
		
		<input id="module_template_dir" name="module_template_dir" type="hidden" value="{$module_template_dir}">
		<input id="pathModule" name="pathModule" type="hidden" value="{$pathModule}">
		<input id="txt_home_page_module" name="txt_home_page_module" type="hidden" value="{$current}&token={$token}&configure=progressivediscounts">

		<fieldset id="block_header_progressive_discounts">
				<div id="title_progressive_discounts">
					Descuentos Progresivos
				</div>
				<div id="buttons_progressive_discounts">
					{include file="$pathModule/tpl/{$buttons}.tpl"}
				</div>
		</fieldset>

		<fieldset id="block_body_progressive_discounts">
			<legend>
				<img width="18px" src="{$module_template_dir}logo.png" />{$legend_body}
			</legend>

			<div id="grid_progressive_discounts">
				{include file="$pathModule/tpl/{$form}.tpl"}
			</div>
		</fieldset>
	</form>
</div>