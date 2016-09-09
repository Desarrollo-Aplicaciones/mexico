
<script type="text/javascript">
if(!isMobile())
{
	jQuery(document).ready(function($) { //background
		{foreach from=$patterns item=pattern name=patterns}
			{assign var=id_pattern value="."|explode:$pattern}
			$("#pattern_{$id_pattern[0]}").click(function() {
				$(".pattern_item").removeClass("active");
				$(this).addClass("active");
				var url_pattern = "{$path}/images/patterns/{$pattern}";
				$.cookie('cookie_bg_pattern',url_pattern);
				$.cookie('cookie_bg_pattern_class','pattern_{$id_pattern[0]}');
				$('body').css('background-image', 'url("' + $.cookie('cookie_bg_pattern') + '")');
			});
			if($.cookie('cookie_bg_pattern_class'))
			{
				$("#" + $.cookie('cookie_bg_pattern_class') + "").addClass("active");
			}
		{/foreach}
	});
}
</script>
<div class="cpanelContainer">
	<div class="cpanel_content_container cpanel_closed">
		<span class="theme_sett_heading">{l s='Theme settings' mod='csthemeeditor'}</span>
		<div class="cpanel_icon">
			<span>icon</span>
		</div>
		<div class="out_improved">
		<ul id="improved">
			<li>
				<a href="#" class="head">{l s='Template color' mod='csthemeeditor'}</a>
				<div class="content" style="display: none;">
					<div class="row">
					{foreach from=$color_templates item=color_template name=color_templates}
						<p><input type="radio" name="color_template" value="{$color_template}"/>{$color_template}</p>
					{/foreach}
					</div>
				</div>
			</li>
			<li>
				<a href="#" class="head">{l s='Product grid view' mod='csthemeeditor'}</a>
				<div class="content" style="display: none;">
					<select name="setting_column" onchange="changeOptionColumn($(this).val())">
						{foreach from=$column_lists item=value_column key=name_column name=columns}
							<option value="{$name_column}">{$name_column}</option>
						{/foreach}
					</select>
					<div class="option_columns">
						{foreach from=$column_lists item=value_column key=name_column name=columns}
						<div class="{$name_column} row" style="display:none">
							{foreach from=$value_column item=class_column name=class_columns }
							{assign var=temp_class value="("|explode:$class_column} 
							{assign var="pot_cut" value=$temp_class[1]|strpos:")"}
							{assign var="number" value=$temp_class[1]|substr:0:$pot_cut}
							
								<p><input type="radio" name="radio_setting_column" value="{$class_column}"/> {$class_column}</p>
								<span class="explain" style="margin-left:19px">{$number}{l s=' products in a row' mod='csthemeeditor'}</span>
							{/foreach}
						</div>
						{/foreach}
					</div>
				</div>
			</li>
			<li><a href="#" class="head">{l s='Mode CSS' mod='csthemeeditor'}</a>
			<div class="content" style="display: none;">
				<input type="radio" name="mode_css" value="box"/> {l s='Box' mod='csthemeeditor'}
				<input type="radio" name="mode_css" value="wide"/>{l s='Wide' mod='csthemeeditor'} 
			</div>
			</li>
			<li>
				<a href="#" class="head">{l s='Background image' mod='csthemeeditor'}</a>
				<div class="content" style="display: none;">
					{foreach from=$patterns item=pattern name=patterns}
						{assign var=id_pattern value="."|explode:$pattern}
						<div class="pattern_item" id="pattern_{$id_pattern[0]}" style="background:url({$path}/images/patterns/{$pattern})"></div>
					{/foreach}
				</div>
			</li>
			<li>
				<a href="#" class="head">{l s='Color' mod='csthemeeditor'}</a>
				<div class="content">
					{foreach from=$color_bgs key=elem_color item=color name=color_bgs}
					<div class="color_item">
					<h4>{$color[0]}</h4>
					<div id="{$elem_color}" class="bg_color_setting" style="cursor:pointer">text</div>
					</div>
					{/foreach}
					{foreach from=$color_texts key=elem_color_text item=color_text name=color_text}
					<div class="color_item">
					<h4>{$color_text[0]}</h4>
					<div id="{$elem_color_text}" class="bg_color_setting" style="cursor:pointer">text</div>
					</div>
					{/foreach}
					
				</div>
			</li>
			<li>
				<a href="#" class="head">{l s='Font' mod='csthemeeditor'}</a>
				<div class="content">
					{foreach from=$fonts key=elem_font item=font name=fonts}
					<div class="font_item">
					<h4>{$font[0]}</h4>
					<select name="{$elem_font}" id="{$elem_font}" onchange="showResultChooseFont('{$elem_font}','{$font[1]}')">
					{$font_list}
					</select>
					<span id="result_{$elem_font}"></span>
					</div>
				{/foreach}
				</div>
			</li>
		</ul>
		<input type="button" id="cs_reset_setting" value="{l s='Reset' mod='csthemeeditor'}" />
		</div>
	</div>
</div>
