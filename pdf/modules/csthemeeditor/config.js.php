<?php
$ts = gmdate("D, d M Y H:i:s") . " GMT";
header("Expires: $ts");
header("Last-Modified: $ts");
header("Pragma: no-cache");
header("Cache-Control: no-cache, must-revalidate");

include(dirname(__FILE__).'/../../config/config.inc.php');
$id_shop = (int)Context::getContext()->shop->id;
if(isset($_COOKIE["color_template"]))
	$color_tp = $_COOKIE["color_template"];
else
	$color_tp = Configuration::get('CS_COLOR_TEMPLATE',null,Shop::getGroupFromShop($id_shop),$id_shop);
$color_list = glob(_PS_MODULE_DIR_.'csthemeeditor/settings/default/'."*.xml");
$color_template = array();
foreach($color_list as $k=>$color)
{
	if(substr(basename($color),0,-4) != "custom")
		$color_template[$k]=substr(basename($color),0,-4);
}
if(in_array($color_tp,$color_template))
	$path = dirname(__FILE__).'/settings/default/';
else
	$path = dirname(__FILE__).'/settings/';
$arr_setting_key = array();
$arr_setting_value = array();
$settings = simplexml_load_file($path.$color_tp.'.xml');
$i = 0;
foreach($settings as $item)
{
	$name = $item->getName();
	$arr_setting_key[$i] = $item->getName();
	$arr_setting_value[$item->getName()] = (string)$settings->$name;
	$i++;
}
$items_setting = simplexml_load_file(dirname(__FILE__).'/items.setting.xml');
if(isset($_COOKIE['setting_column']) && $_COOKIE['setting_column']!=null) 
{
	$cookie_column = explode("&",$_COOKIE['setting_column']);
	$active_select_column = $cookie_column[0];
	$active_radio_class = $cookie_column[1];
}
else
{
	$active_select_column = $settings->column;
	$active_radio_class = $settings->column_class;
}
$js_content = '';
header("Content-type: text/javascript");  
$js_content .= '
function ColorEv (i,j,type)
{
		$("#" + i).ColorPicker({
			color: "#0000ff",
			onShow: function (colpkr) {
				$(colpkr).fadeIn(500);
				return false;
			},
			onHide: function (colpkr) {
				$(colpkr).fadeOut(500);
				return false;
			},
			onChange: function (hsb, hex, rgb) {
				$("#" + i).css("background", "#" + hex); //custom event change
				$.cookie("" + i + "", "#" + hex);
				$("" + j + "").css("" + type + "","" + $.cookie(i) + "");
			}
		});
			if ($.cookie("" + i + "") != null)
			{
				$("#" + i + "").css("background","" + $.cookie(i) + "");
				$("" + j + "").css("" + type + "","" + $.cookie(i) + "");
			}
}
$(document).ready(function() {'; 
if(isset($_COOKIE['mode_css_input']) && $_COOKIE['mode_css_input']!=null)
{
	$js_content .= '$(\'input[value="'.$_COOKIE['mode_css_input'].'"]\').attr(\'checked\', \'checked\');';
}
$js_content .= '
			$(\'select[name=setting_column] option[value='.$active_select_column.']\').attr(\'selected\', \'selected\');
			$(\'div.'.$active_select_column.'\').show();
			$(\'input[value="'.$active_radio_class.'"]\').attr(\'checked\', \'checked\');';
foreach($items_setting->children() as $child)
{
	foreach($child->children() as $item)
	{
		$style_css = explode(",", $item->style_css);
		foreach($style_css as $style)
		{
			switch ($style) 
			{
				case 'background':
						if($item->frontend == "true")
							$js_content .= 'ColorEv("'.$item->name.'_color","'.$item->class.'","background");';
					break;
				case 'color_bg':
						if($item->frontend == "true")
							$js_content .= 'ColorEv("'.$item->name.'_color_bg","'.$item->class.'","background");';
					break;
				case 'color_text':
						if($item->frontend == "true")
							$js_content .= 'ColorEv("'.$item->name.'_color_text","'.$item->class.'","color");';
					break;
				case 'font':
						if($item->frontend == "true")
							$js_content .= 'loadGoogleFont("'.$item->name.'_fstyle'.'","'.$item->class.'");';
						if(in_array($item->name.'_fstyle',$arr_setting_key))
						{
							
							$class = $item->name.'_fstyle';
							$js_content .= 'loadGoogleFontFromConfig("'.$item->name.'_fstyle'.'","'.$settings->$class.'");
							';
						}
					break;
			}
		}
	}
}
$js_content .= '});';
echo $js_content;
?>
