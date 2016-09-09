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
$items_setting = simplexml_load_file(dirname(__FILE__).'/items.setting.xml');;
$css_content = '';
header("Content-type: text/css");
if(isset($_COOKIE['mode_css']) && $_COOKIE['mode_css'] !='')
{
	$css_content .='#page{width:'.$_COOKIE['mode_css'].';}';
}
else if($arr_setting_value['bg_mode'] == "box_mode")
	$css_content .=  '#page{	width: 1200px;
    box-shadow: 0 0 8px #666666;
    margin:0 auto;}

@media only screen and (min-width: 1024px) and (max-width: 1279px) {
	#page{	width: 960px;}
}
@media only screen and (max-width: 1023px)
	{
		#page{margin:0 auto;border-radius:0;
		box-shadow: 0 0 0 #fff;}
	}
@media only screen and (min-width: 768px) and (max-width: 1023px) {
	#page {width: 768px;}
}
@media only screen and (max-width: 767px)
{
	#page{width:100%;}
}';
foreach($items_setting->children() as $child)
{
	foreach($child->children() as $item)
	{
		$style_css = explode(",", $item->style_css);
		$foreach = true;
			foreach($style_css as $style)
			{
				if($foreach == true)
				{
				switch ($style)
				{
					case 'background':
						if(in_array($item->name.'_img',$arr_setting_key) || (in_array($item->name.'_pattern',$arr_setting_key) && $arr_setting_value[(string)$item->name.'_pattern'] != 'no_img.jpg') || in_array($item->name.'_repeat',$arr_setting_key) || in_array($item->name.'_color',$arr_setting_key))
						{
							$css_content .= $item->class.'{';
							$foreach = false;
						}
						break;
					case 'color_bg':
						if(in_array($item->name.'_color_bg',$arr_setting_key))
						{
							$css_content .= $item->class.'{';
							$foreach = false;
						}
						break;
					case 'color_text':
						if(in_array($item->name.'_color_text',$arr_setting_key))
						{
							$css_content .= $item->class.'{';
							$foreach = false;
						}
						break;
					case 'font':
						if(in_array($item->name.'_fstyle',$arr_setting_key) || in_array($item->name.'_fsize',$arr_setting_key) || in_array($item->name.'_fweight',$arr_setting_key) || in_array($item->name.'_fweight2',$arr_setting_key))
						{
							$css_content .= $item->class.'{';
							$foreach = false;
						}
						break;
					case 'border_color':
						if(in_array($item->name.'_border_color',$arr_setting_key) || in_array($item->name.'_border_weight',$arr_setting_key) || in_array($item->name.'_border_style',$arr_setting_key))
						{
							$css_content .= $item->class.'{';
							$foreach = false;
						}
						break;
				}
			}
			}
			foreach($style_css as $style)
			{
				switch ($style)
				{
					case 'background':
						if(in_array($item->name.'_img',$arr_setting_key))
						{
							$css_content .= 'background-image : url("images/backgrounds/'.$arr_setting_value[(string)$item->name.'_img'].'");';
						}
						else if(in_array($item->name.'_pattern',$arr_setting_key) && $arr_setting_value[(string)$item->name.'_pattern'] != 'no_img.jpg')
						{
							$css_content .= 'background-image : url("images/patterns/'.$arr_setting_value[(string)$item->name.'_pattern'].'");';
						}
						if(in_array($item->name.'_repeat',$arr_setting_key))
						{
							$css_content .= 'background-repeat : '.$arr_setting_value[(string)$item->name.'_repeat'].';';
						}
						if(in_array($item->name.'_color',$arr_setting_key))
						{
							if(in_array($item->name.'_img',$arr_setting_key) || in_array($item->name.'_pattern',$arr_setting_key))
								$css_content .= 'background-color : '.$arr_setting_value[(string)$item->name.'_color'].';';
							else
								$css_content .= 'background : '.$arr_setting_value[(string)$item->name.'_color'].';';
						}
						break;
					case 'color_bg':
						if(in_array($item->name.'_color_bg',$arr_setting_key))
						{
							$css_content .= 'background : '.$arr_setting_value[(string)$item->name].';';
						}
						break;
					case 'color_text':
							if(in_array($item->name.'_color_text',$arr_setting_key))
							{
								$css_content .= 'color : '.$arr_setting_value[(string)$item->name.'_color_text'].';';
							}
						break;
					case 'border_color':
							if(in_array($item->name.'_border_color',$arr_setting_key))
							{
								$css_content .= 'border-color : '.$arr_setting_value[(string)$item->name.'_border_color'].';';
							}
							if(in_array($item->name.'_border_weight',$arr_setting_key))
							{
								$css_content .= 'border-width : '.$arr_setting_value[(string)$item->name.'_border_weight'].'px;';
							}
							if(in_array($item->name.'_border_style',$arr_setting_key))
							{
								$css_content .= 'border-style : '.$arr_setting_value[(string)$item->name.'_border_style'].';';
							}
						break;
					case 'font':
						if(in_array($item->name.'_fstyle',$arr_setting_key))
							$css_content .= 'font-family : "'.$arr_setting_value[(string)$item->name.'_fstyle'].'";';
						if(in_array($item->name.'_fsize',$arr_setting_key))
							$css_content .= 'font-size : '.$arr_setting_value[(string)$item->name.'_fsize'].'px;';
						if(in_array($item->name.'_fweight',$arr_setting_key))
							$css_content .= 'font-style : '.$arr_setting_value[(string)$item->name.'_fweight'].';';
						if(in_array($item->name.'_fweight2',$arr_setting_key))
							$css_content .= 'font-weight : '.$arr_setting_value[(string)$item->name.'_fweight2'].';';
				break;
				}
			}
		$foreach2 = true;
		foreach($style_css as $style)
			{
				if($foreach2 == true)
				{
				switch ($style)
				{
					case 'background':
						if(in_array($item->name.'_img',$arr_setting_key) || (in_array($item->name.'_pattern',$arr_setting_key) && $arr_setting_value[(string)$item->name.'_pattern'] != 'no_img.jpg') || in_array($item->name.'_repeat',$arr_setting_key) || in_array($item->name.'_color',$arr_setting_key))
						{
							$css_content .= '}';
							$foreach2 = false;
						}
						break;
					case 'color_bg':
						if(in_array($item->name.'_color_bg',$arr_setting_key))
						{
							$css_content .= '}';
							$foreach2 = false;
						}
						break;
					case 'color_text':
						if(in_array($item->name.'_color_text',$arr_setting_key))
						{
							$css_content .= '}';
							$foreach2 = false;
						}
						break;
					case 'font':
						if(in_array($item->name.'_fstyle',$arr_setting_key) || in_array($item->name.'_fsize',$arr_setting_key) || in_array($item->name.'_fweight',$arr_setting_key) || in_array($item->name.'_fweight2',$arr_setting_key))
						{
							$css_content .= '}';
							$foreach2 = false;
						}
						break;
					case 'border_color':
						if(in_array($item->name.'_border_color',$arr_setting_key) || in_array($item->name.'_border_weight',$arr_setting_key) || in_array($item->name.'_border_style',$arr_setting_key))
						{
							$css_content .= '}';
							$foreach2 = false;
						}
					break;
				}
			}
		}
	}
}
echo $css_content;
?>





