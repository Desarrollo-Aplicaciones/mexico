<?php
class Editor
{
	public static function showColor($text, $style,$id)
	{
		return  '<p>
			<label>'.$text.'</label>
				<input style="background: '.($style ?  $style : '#FFF').'" id="result_'.$id.'" name="'.$id.'" type="text" value="'.($style ? $style : '').'" /> 
				<span id="'.$id.'" style="cursor:pointer">
				<img src="'._PS_ADMIN_IMG_.'color.png"/>
				</span>
			</p>';
	}
	public static function showFont($fontList,$name,$textfont,$settings,$saveSettingsDefault)
	{
		$fstyle = $name.'_fstyle';
		$fsize = $name.'_fsize';
		$fweight = $name.'_fweight';
		$fweight2 = $name.'_fweight2';
		$html = '
				<p>
				<label>Font family</label>
				<select name="'.$fstyle.'" id="'.$fstyle.'" onchange="showResultChooseFont(\''.$fstyle.'\',\'result_'.$fstyle.'\',\''.$saveSettingsDefault.'\')"> '.$fontList.' </select>
						<span id="result_'.$fstyle.'" style="font-family:'.($settings->$fstyle ? $settings->$fstyle : 'arial').'">'.($settings->$fstyle ? $settings->$fstyle : '').'</span>
				</p>
				<p>
				<label>Font size</label>
				<select name="'.$fsize.'" id="'.$fsize.'" onchange="showFontSize(\''.$fsize.'\',\'result_'.$fstyle.'\',\''.$saveSettingsDefault.'\')">
				<option value="">No Choose</option>';
				for($i=12;$i<=80;$i++)
				{
					$html .= '<option value="'.$i.'" '.($settings->$fsize == $i ? "selected" : "").' >'.$i.'px</option>';
				}
				$html .= '</select></p>
				<p>
				<label>Font style</label>';
				$font_weights = array("normal","inherit","italic","oblique");
				$html .= '
				<select name="'.$fweight.'" id="'.$fweight.'" onchange="showFontWeight(\''.$fweight.'\',\'result_'.$fstyle.'\',\''.$saveSettingsDefault.'\')">
				<option value="">No Choose</option>
				';
				foreach($font_weights as $font_weight)
				{
					$html .= '<option value="'.$font_weight.'" '.($settings->$fweight==$font_weight ? "selected" : "").' >'.$font_weight.'</option>';
				}
				$html .= '</select></p>
				<p>
				<label>Font weight</label>';
				//100 -900 , nomal, bold,bolder, lighter
				$font_weight2s = array("100","200","300","400","500","600","700","800","900","normal","bold","bolder","lighter");
				$html .= '
				<select name="'.$fweight2.'" id="'.$fweight2.'" onchange="showFontWeight2(\''.$fweight2.'\',\'result_'.$fstyle.'\',\''.$saveSettingsDefault.'\')"><option value="">No Choose</option>';
				foreach($font_weight2s as $font_fweight2)
				{
					$html .='<option value="'.$font_fweight2.'" '.($settings->$fweight2==$font_fweight2 ? "selected" : "").' >'.$font_fweight2.'</option>';
				}
				$html .= '</select></p>';
		return $html;	
	}
	
	
	public static function showConfigPanelAndColorTemplate($show_panel,$color_templates,$color,$settings,$saveSettingsDefault)
	{
		$html = '<h4>Config panel</h4><p>
				<label>Show panel front-end</label> 
				<input type="radio" name="show_panel_front_end" value="1"'.($show_panel == 1 ? 'checked="checked"' : '').' />
				<label class="t"><img src="'._PS_ADMIN_IMG_.'enabled.gif" alt="Enabled" title="Enabled" /></label>
				<input type="radio" name="show_panel_front_end" value="0"'.($show_panel == 0 ? 'checked="checked"' : '').' />
				<label class="t"><img src="'._PS_ADMIN_IMG_.'disabled.gif" alt="Disabled" title="Disabled" /></label></p>
				<div class="separation"></div>
				<h4>Color Tempate</h4><p>
				<label>Template</label>
				<select name="color_template" id="color_template" onchange="submit()">';
				foreach($color_templates as $template)
				{
					$html .= '<option '.($template == $color ? "selected" : "").'  value='.$template.'>'.$template.'</option>';
				}
				if(!$saveSettingsDefault)
				{
					$html .= '<option value="custom" ';
					//if(!Tools::getValue('color_template_change'))
					//{
						if (!in_array($color, $color_templates)) {
							$html .= 'selected = "selected"';
						}
					//}
					$html .= '>custom</option>';
				}
				$html .= '</select></p><p>
				<label>Background mode:</label>
				<input type="radio" name="bg_mode" value="box_mode" '.($settings->bg_mode && $settings->bg_mode == 'box_mode' ? 'checked=checked' : '').' />Box mode:
				<input type="radio" name="bg_mode" value="wide_mode" '.($settings->bg_mode && $settings->bg_mode == 'wide_mode' ? 'checked=checked' : '').' />Wide mode:</p>
				<div class="separation"></div>';
		return $html;
	}
}
?>