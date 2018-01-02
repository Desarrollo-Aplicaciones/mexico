<?php
if (!defined('_PS_VERSION_'))
	exit;
require (dirname(__FILE__).'/classes/Editor.php');
class CsThemeEditor extends Module
{
	private $_html;
	private $columns = array('1_column'=>'1 column','2_column_left'=>'2 column left','2_column_right'=>'2 column right','3_column'=>'3 column');
	public $color_list;
	private $column_grids = array(
							'1_column'=> array('grid_24(3)','grid_24(4)','grid_24(6)','grid_24(8)'),
							'2_column_left'=> array('grid_8,grid_16(2)','grid_6,grid_18(3)','grid_4,grid_20(4)','grid_4,grid_20(5)','grid_6,grid_18(6)'),
							'2_column_right'=> array('grid_16(2),grid_8','grid_18(3),grid_6','grid_20(4),grid_4','grid_20(5),grid_4','grid_18(6),grid_6'),
							'3_column'=> array('grid_8,grid_8(1),grid_8','grid_6,grid_12(2),grid_6','grid_4,grid_15(3),grid_5','grid_4,grid_16(4),grid_4','grid_6,grid_12(4),grid_6')
							);
	private $color_dir;
	private $items_settings;
	private $path_pattern;
	private $path_background;
	private $font_list;
	private $pattern_list;
	private $background_items;
	private $color_bg_items;
	private $color_text_items;
	private $font_items;
	private $saveSettingsDefault = false; //variables for developers -- true : editable folder settings/default or false
	function __construct()
	{
		$this->name = 'csthemeeditor';
		$this->tab = 'My Blocks';
		$this->version = 1.0;
		$this->author = 'Codespot';
		parent::__construct();
		$this->displayName = $this->l('Cs Theme Editor');
		$this->description = $this->l('Add block theme editor.');
		$this->color_dir = _PS_MODULE_DIR_.'csthemeeditor/settings/default/';
		$this->path_pattern = _PS_MODULE_DIR_.'csthemeeditor/images/patterns/';
		$this->path_background = _PS_MODULE_DIR_.'csthemeeditor/images/backgrounds/';
		$this->items_settings = simplexml_load_file(dirname(__FILE__).'/items.setting.xml');
		$this->font_list = file_get_contents(dirname(__FILE__).'/fonts/'.'googlefont.html');
		$this->pattern_list = array_merge(glob($this->path_pattern."*.jpg"), glob($this->path_pattern."*.png"),glob($this->path_pattern."*.gif"));
		$color_list = glob($this->color_dir."*.xml");
		foreach($color_list as $k=>$color)
		{
			if(substr(basename($color),0,-4) != "custom")
				$this->color_list[$k]=substr(basename($color),0,-4);
		}
	}

	function install()
	{
		return (parent::install() AND $this->registerHook('top') AND $this->registerHook('header') AND Configuration::updateValue('CS_COLOR_TEMPLATE','default',false,Shop::getGroupFromShop(Configuration::get('PS_SHOP_DEFAULT')),Configuration::get('PS_SHOP_DEFAULT')));
	}
	
	public function uninstall()
	{
		return (Configuration::deleteByName('CS_COLOR_TEMPLATE') AND parent::uninstall());
	}
	public function UploadBackground($field_name)
	{
		if (isset($_FILES[$field_name]['tmp_name']) && $_FILES[$field_name]['tmp_name'])
		{
			if (ImageManager::validateUpload($_FILES[$field_name], 900000))
			{
				$er = ImageManager::validateUpload($_FILES[$field_name], 900000);
				return $er;
			}
			$tmp_name = $this->path_background.time().$_FILES[$field_name]['name'];
			if (!move_uploaded_file($_FILES[$field_name]['tmp_name'],$tmp_name))
			{
				return $this->displayError('Error upload image');
			}
			return true;
		}
		return false;
		
	}

	public function showBackground($path_background,$prefix_text,$settings,$color_template,$text,$showPattern)
	{
			global $currentIndex;
			$stringConfirm='onclick="if (!confirm(\'Are you sure that you want to delete background image ?\')) return false "';
			$name_pattern = $prefix_text.'_pattern';
			$name_image = $prefix_text.'_img';
			$name_repeat = $prefix_text.'_repeat';
			$name_color = $prefix_text.'_color';
			$html = '<div style="clear:both;">';
			if($showPattern == "false")
				$html .='';
			else
			{
				$html .='<label>Background image</label><p style="overflow:hidden">';
				foreach($this->pattern_list as $pattern)
				{
					$relative_path = _MODULE_DIR_.'csthemeeditor/images/patterns/'.basename($pattern);
					$html .= '<span class="bkg_pattern" style="background:url('.$relative_path.') repeat;float:left;margin:0 10px 10px 0">
					<input type="radio" name="'.$name_pattern.'" value="'.basename($pattern).'" '.($settings->$name_pattern && $settings->$name_pattern == basename($pattern) ? 'checked=checked' : '').' /></span>';
				}
			}
			$html .= '</p><div style="clear:both;overflow:hidden;"><label class="image_upload"><input type="radio" name="'.$name_pattern.'" value="up_load_image_'.$prefix_text.'" '.(isset($settings->$name_image) ? 'checked=checked' : '' ).' id="up_load_image_'.$prefix_text.'" />Image upload</label>
			<div id="show_up_load_image_'.$prefix_text.'" style="display:none">
			<input type="hidden" name="hid_'.$name_image.'" value="'.($settings->$name_image ? $settings->$name_image : '').'"/>
			<input style="float:left" type="file" name="'.$name_image.'" value="'.($settings->$name_image ? $settings->$name_image : '').'"/>';
				$backgrounds_old = array_merge(glob($path_background."*.jpg"), glob($path_background."*.png"),glob($path_background."*.gif"));
				if(isset($settings->$name_image))
				{
					$path_bg = _MODULE_DIR_.$this->name.'/images/backgrounds/'.$settings->$name_image;
					$html .= '<div class="thumb_bg" style="background:url('.$path_bg.');">result</div>
					<a href="'.$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&delete_'.$name_image.'&color_template_change='.$color_template.'" '.$stringConfirm.'><img src="'._PS_ADMIN_IMG_.'delete.gif" alt="'.$this->l('Delete image').'" title="'.$this->l('Delete').'" /></a>
					';
				}
				$html .= '</div></div></br>
				<div class="clear:both;overflow:hidden;"><label>Repeat image</label>
				<div class="repeat_image">
				 <span><input type="radio" name="'.$name_repeat.'" value="no-repeat" '.($settings->$name_repeat && $settings->$name_repeat == 'no-repeat' ? 'checked=checked' : '' ).' />No repeat</span>
				 <span><input type="radio" name="'.$name_repeat.'" value="repeat-x" '.($settings->$name_repeat && $settings->$name_repeat == 'repeat-x' ? 'checked=checked' : '' ).' />Repeat-x</span>
				 <span><input type="radio" name="'.$name_repeat.'" value="repeat-y" '.($settings->$name_repeat && $settings->$name_repeat == 'repeat-y' ? 'checked=checked' : '' ).' />Repeat-y</span>
				 <span><input type="radio" name="'.$name_repeat.'" value="repeat" '.($settings->$name_repeat && $settings->$name_repeat == 'repeat' ? 'checked=checked' : '' ).' />Repeat-xy</span>
				</div></div>';
			$html .= Editor::showColor('Background color',$settings->$name_color,$name_color);
			$html .= '<script type="text/javascript">
						(function($){var initLayout = function() {
								colorEvent("'.$name_color.'","'.$this->saveSettingsDefault.'");};
								EYE.register(initLayout, \'init\');
						})(jQuery)
						$(window).ready(function() {
							ShowUploadImage("'.$name_pattern.'","up_load_image_'.$prefix_text.'");
						});
					</script>';
		return $html;
	}
	private function saveXmlSetting($file,$items_settings)
	{
		$newXml = '<?xml version=\'1.0\' encoding=\'utf-8\' ?>'."\n".'<setting>'."\n";
		$arrColumn = array('bg_mode','column','column_class');
		foreach($arrColumn as $item)
		{
			if (Tools::getValue(''.$item.'') != '')
			{
				$newXml .= '<'.$item.'>';
				$newXml .= Tools::getValue(''.$item.'');
				$newXml .= '</'.$item.'>'."\n";
			}
		}
		foreach($items_settings->children() as $child)
		{
			foreach($child->children() as $item)
			{
				$style_css = explode(",", $item->style_css);
				foreach($style_css as $style)
				{
					switch ($style)
					{
						case 'background':
							$arBg = array('_color','_repeat');
								foreach ($arBg as $b)
								{
									if (Tools::getValue(''.$item->name.$b.'') != '')
									{
										$newXml .= '<'.$item->name.$b.'>';
										$newXml .= Tools::getValue(''.$item->name.$b.'');
										$newXml .= '</'.$item->name.$b.'>'."\n";
									}
								}
								if (Tools::getValue(''.$item->name.'_pattern') != '' && Tools::getValue(''.$item->name.'_pattern') != 'up_load_image_'.$item->name)
								{
									$newXml .= '<'.$item->name.'_pattern>';
									$newXml .= Tools::getValue(''.$item->name.'_pattern');
									$newXml .= '</'.$item->name.'_pattern>'."\n";
								}
								else if($_FILES[''.$item->name.'_img']['name'] != "")
								{
									$er = $this->UploadBackground($item->name.'_img');
									$newXml .= '<'.$item->name.'_img>';
									$newXml .= time().$_FILES[''.$item->name.'_img']['name'];
									$newXml .= '</'.$item->name.'_img>'."\n";
									if($er != null)
									$error = $this->displayError($this->l($er));
								}
								else if($_POST['hid_'.$item->name.'_img'] != '' && $_FILES[''.$item->name.'_img']['name'] == "" && Tools::getValue(''.$item->name.'_pattern') != 'no_img.jpg')
								{
									$newXml .= '<'.$item->name.'_img>';
									$newXml .= $_POST['hid_'.$item->name.'_img'];
									$newXml .= '</'.$item->name.'_img>'."\n";
								}
							break;
						case 'border_color':
							$arboder = array('_border_color','_border_weight','_border_style');
								foreach($arboder as $b)
								{
									if (Tools::getValue(''.$item->name.$b.'') != '')
									{
										$newXml .= '<'.$item->name.$b.'>';
										$newXml .= Tools::getValue(''.$item->name.$b.'');
										$newXml .= '</'.$item->name.$b.'>'."\n";
									}
								}
							break;
						case 'font':
							$arFont = array('_fstyle','_fsize','_fweight','_fweight2');
								foreach($arFont as $f)
								{
									if (Tools::getValue(''.$item->name.$f.'') != '')
									{
										$newXml .= '<'.$item->name.$f.'>';
										$newXml .= Tools::getValue(''.$item->name.$f.'');
										$newXml .= '</'.$item->name.$f.'>'."\n";
									}
								}
							break;
						case 'color_text':
								if (Tools::getValue(''.$item->name.'_color_text') != '')
								{
									$newXml .= '<'.$item->name.'_color_text>';
									$newXml .= Tools::getValue(''.$item->name.'_color_text');
									$newXml .= '</'.$item->name.'_color_text>'."\n";
								}
							break;
						case 'color_bg':
								if (Tools::getValue(''.$item->name.'_color_bg') != '')
								{
									$newXml .= '<'.$item->name.'_color_bg>';
									$newXml .= Tools::getValue(''.$item->name.'_color_bg');
									$newXml .= '</'.$item->name.'_color_bg>'."\n";
								}
							break;
					}
				}
			}
		}
		$newXml .= '</setting>'."\n";
		if ($fd = @fopen(dirname(__FILE__).'/settings/'.$file.'.xml', 'w'))
		{
			if (!@fwrite($fd, $newXml))
				$error = $this->displayError($this->l('Unable to write to the editor file.'));
			if (!@fclose($fd))
				$error = $this->displayError($this->l('Can\'t close the editor file.'));
		}
		else
			$error = $this->displayError($this->l('Unable to update the editor file. Please check the editor file\'s writing permissions.'));
	}
	public function _postProcess()
	{
		global $currentIndex;
		$id = (int)Context::getContext()->shop->id;
		$id_shop = $id ? $id: Configuration::get('PS_SHOP_DEFAULT');
		if(Tools::getValue('color_template'))
			$color_template = Tools::getValue('color_template');
		else if(Tools::getValue('color_template_change'))
			$color_template = Tools::getValue('color_template_change');
		else
			$color_template = Configuration::get('CS_COLOR_TEMPLATE',null,Shop::getGroupFromShop($id_shop),$id_shop);
		foreach($this->items_settings->children() as $child)
		{
			foreach($child->children() as $item)	
			{
				$style_css = explode(",", $item->style_css);
				foreach($style_css as $style)
				{
					switch ($style)
					 {
						case 'background':
							if (Tools::isSubmit('delete_'.$item->name.'_img'))
							{
								if($this->saveSettingsDefault) //developer
								{
									$c_t = $color_template;
									$path_c = dirname(__FILE__).'/settings/default/';
								}
								else //user normal
								{
									$c_t = 'custom'.$id_shop;
									$path_c = dirname(__FILE__).'/settings/';
									if (in_array($color_template,$this->color_list))
									{
										copy(dirname(__FILE__).'/settings/default/'.$color_template.'.xml',dirname(__FILE__).'/settings/'.$c_t.'.xml');
									}
								}
								$data = simplexml_load_file($path_c.$c_t.'.xml');
								$item_delete = $item->name.'_img';
								unset($data->$item_delete);
								Configuration::updateValue('CS_COLOR_TEMPLATE',$c_t,false,Shop::getGroupFromShop($id_shop),$id_shop);
								file_put_contents($path_c.$c_t.'.xml', $data->saveXML());
								Tools::redirectAdmin($currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules'));
							}
						break;
					}
				}
			}
		}
		if (Tools::isSubmit('saveMainSetting'))
		{
			if (Shop::getContext() != Shop::CONTEXT_SHOP)
			{
				foreach (Shop::getContextListShopID() as $id_shop)
				{
					if ($color_template == "custom")
						$color_template = $color_template.''.$id_shop;
					Configuration::updateValue('CS_COLOR_TEMPLATE',$color_template,false,Shop::getGroupFromShop($id_shop),$id_shop);
					Configuration::updateValue('SHOW_PANEL_FRONT_END',Tools::getValue('show_panel_front_end'),false,Shop::getGroupFromShop($id_shop),$id_shop);
				}
			}
			else
			{
				if ($color_template == "custom")
					$color_template = $color_template.''.$id_shop;
				Configuration::updateValue('CS_COLOR_TEMPLATE',$color_template,false,Shop::getGroupFromShop($id_shop),$id_shop);
				Configuration::updateValue('SHOW_PANEL_FRONT_END',Tools::getValue('show_panel_front_end'),false,Shop::getGroupFromShop($id_shop),$id_shop);
			}
			if($this->saveSettingsDefault) //developer
			{
				$this->saveXmlSetting('default/'.$color_template,$this->items_settings);
			}
			else //user normal
			{
				if(!in_array($color_template,$this->color_list))
					$this->saveXmlSetting($color_template,$this->items_settings);
			}
			Tools::redirectAdmin($currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&saveMainSettingConfirmation');
		}
		else if (Tools::isSubmit('resetSetting'))
		{
			$path = dirname(__FILE__).'/settings/default/default.xml';
			$data = simplexml_load_file($path);
			$item_bg_mode = $data->bg_mode;
			$item_column = $data->column;
			$item_column_class = $data->column_class;
			
			$newXml = '<?xml version=\'1.0\' encoding=\'utf-8\' ?>'."\n".'<setting>'."\n";
			$newXml .= '<bg_mode>';
			$newXml .= $item_bg_mode;
			$newXml .= '</bg_mode>'."\n";
			$newXml .= '<column>';
			$newXml .= $item_column;
			$newXml .= '</column>'."\n";
			$newXml .= '<column_class>';
			$newXml .= $item_column_class;
			$newXml .= '</column_class>'."\n";
			$newXml .= '</setting>'."\n";
			
			//echo $item_column.$item_column_class;die();
			
			if (Shop::getContext() != Shop::CONTEXT_SHOP)
			{
				foreach (Shop::getContextListShopID() as $id_shop)
				{
					Configuration::updateValue('CS_COLOR_TEMPLATE','custom'.$id_shop,false,Shop::getGroupFromShop($id_shop),$id_shop);
					if ($fd = @fopen(dirname(__FILE__).'/settings/custom'.$id_shop.'.xml', 'w'))
					{
						if (!@fwrite($fd, $newXml))
							$error = $this->displayError($this->l('Unable to write to the editor file.'));
						if (!@fclose($fd))
							$error = $this->displayError($this->l('Can\'t close the editor file.'));
					}
					else
						$error = $this->displayError($this->l('Unable to update the editor file. Please check the editor file\'s writing permissions.'));
				}
			}
			else
			{
				Configuration::updateValue('CS_COLOR_TEMPLATE','custom'.$id_shop,false,Shop::getGroupFromShop($id_shop),$id_shop);
				if ($fd = @fopen(dirname(__FILE__).'/settings/custom'.$id_shop.'.xml', 'w'))
				{
					if (!@fwrite($fd, $newXml))
						$error = $this->displayError($this->l('Unable to write to the editor file.'));
					if (!@fclose($fd))
						$error = $this->displayError($this->l('Can\'t close the editor file.'));
				}
				else
					$error = $this->displayError($this->l('Unable to update the editor file. Please check the editor file\'s writing permissions.'));
			}
		}
		else if (Tools::isSubmit('cancelSetting'))
		{
			Tools::redirectAdmin($currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules'));
		}
		else if (Tools::isSubmit('color_template'))
		{
			$color = Tools::getValue('color_template');
			Tools::redirectAdmin($currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&color_template_change='.$color);
		}
		elseif (Tools::isSubmit('saveMainSettingConfirmation'))
			$this->_html = $this->displayConfirmation($this->l('Save main setting successfully'));
		
	}
	public function getContent()
	{
		$this->_html = '<h2>'.$this->displayName.'</h2>';
		$this->_postProcess();
		$this->_configForm();
		return $this->_html;
	}
	public function _configForm ()
	{
		global $currentIndex;
		$id = (int)Context::getContext()->shop->id;
		$id_shop = $id ? $id: Configuration::get('PS_SHOP_DEFAULT');
		$this->context->controller->addCss($this->_path.'css/colorpicker.css', 'all');
		$this->context->controller->addCss($this->_path.'css/admin/style.css', 'all');
		$this->context->controller->addCss($this->_path.'css/admin/tab.css', 'all');
		$this->context->controller->addJs($this->_path.'js/colorpicker.js',true);
		$this->context->controller->addJs($this->_path.'js/eye.min.js',true);
		$this->context->controller->addJs($this->_path.'js/utils.js',true);
		$this->context->controller->addJs($this->_path.'js/admin/csthemeeditor.js',true);
		$this->context->controller->addJS($this->_path.'js/admin/tab.js',true);
		if(!Configuration::get('CS_COLOR_TEMPLATE',null,Shop::getGroupFromShop($id_shop),$id_shop))
			Configuration::updateValue('CS_COLOR_TEMPLATE','default',false,Shop::getGroupFromShop($id_shop),$id_shop);
		$color_config = Configuration::get('CS_COLOR_TEMPLATE',null,Shop::getGroupFromShop($id_shop),$id_shop);
		$color_template = (Tools::getValue('color_template_change') ? Tools::getValue('color_template_change') : $color_config);
		$boderStyle = array("none","solid","dotted","dashed","double","groove","ridge","inset","outset");
		if($color_template == "custom")
			$color_template = $color_template.$id_shop;
		if(!in_array($color_template,$this->color_list))
			$path = _PS_MODULE_DIR_.'csthemeeditor/settings/';
		else
			$path = $this->color_dir;
		$settings = simplexml_load_file($path.$color_template.'.xml');
		$class_column_home = explode(",",$settings->home_class);
		$this->_html .= '
		<div class="productTabs" style="margin-right: -1px;width: 170px;">
				<ul class="tab">
				<li class="tab-row">
						<a class="nav-manager selected" id="manager-column" href="javascript:void(0);">'.$this->l('Setting column').'</a></li>
				';
				foreach($this->items_settings->children() as $child)
				{
					$this->_html .='<li class="tab-row">
						<a class="nav-manager selected" id="manager-'.$child->getName().'" href="javascript:void(0);">'.$this->l('Setting '.$child->getName().'').'</a></li>';
				}
		$this->_html .=	'</ul></div>
		<form class="csthemeeditor" method="post" action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" enctype="multipart/form-data">
				<fieldset class="manager-column tab-manager plblogtabs">';
				$this->_html .= Editor::showConfigPanelAndColorTemplate(Configuration::get('SHOW_PANEL_FRONT_END',null,Shop::getGroupFromShop($id_shop),$id_shop),$this->color_list,$color_template,$settings,$this->saveSettingsDefault);
					$this->_html .= '<div class="setting_column">
						<h4>'.$this->l('Sub page (no home)').'</h4>
						
						<p><label>'.$this->l('Column:').'</label>
						</p>
						<div class="show_class_no_home">
						<select name="column" onchange="changeOptionColumn($(this).val(),\''.$this->saveSettingsDefault.'\')">';
						foreach($this->column_grids as $name_column=>$value_column)
						{
							$this->_html .= '<option value="'.$name_column.'" '.($settings->column && $settings->column == $name_column ? 'selected' : '').'>'.$name_column.'</option>';
						}
						
					$this->_html .= '</select>
					<div class="option_columns margin-form">';
					foreach($this->column_grids as $name_column=>$value_column)
					{
						$this->_html .= '<div class="'.$name_column.'" style="'.($settings->column && $settings->column == $name_column ? 'display:block' : 'display:none').'">';
						foreach ($value_column as $class_column)
						{
							$temp_class = explode("(",$class_column);
							$number_product = substr($temp_class[1], 0, strpos($temp_class[1], ')'));
							$this->_html .= '<p><input type="radio" '.($settings->column_class && $settings->column_class == $class_column ? 'checked="checked"' : '').' name="column_class" value="'.$class_column.'"/> '.$class_column.'</p><p style="margin-left:15px"> '.$number_product.$this->l(' products in a row.').'</p>';
						}
						$this->_html .= '</div>';
					}
						$this->_html .= '</div></div>
						<p class="preference_description clear">'.$this->l('Select number column and classes of column for sub page (no home).').'</p>
					</div>
				</fieldset>';
				foreach($this->items_settings->children() as $child)
				{
					$this->_html .='<fieldset class="manager-'.$child->getName().' tab-manager plblogtabs" style="display:none;">';
					foreach($child->children() as $item)
					{
						$this->_html .= '<h4>'.$item->text.'</h4>';
						$style_css = explode(",", $item->style_css);
						foreach($style_css as $style)
						{
							switch ($style)
							{
								case 'background':
										$this->_html .= $this->showBackground($this->path_background,$item->name,$settings,$color_template,$item->text,$item->pattern);
									break;
								case 'color_bg':
									$style = $item->name.'_color_bg';
									$this->_html .= Editor::showColor($item->text,(string)$settings->$style,$item->name.'_color_bg');
									$this->_html .= '<script type="text/javascript">
									(function($){var initLayout = function() {
											colorEvent("'.$item->name.'_color_bg","'.$this->saveSettingsDefault.'");};
											EYE.register(initLayout, \'init\');
									})(jQuery)
									</script>';
									break;
								case 'color_text':
									$name_input = $item->name.'_color_text';
									$label = 'Color text';
									$this->_html .= Editor::showColor($label,(string)$settings->$name_input,$name_input);
									$this->_html .= '<script type="text/javascript">
									(function($){var initLayout = function() {
											colorEvent("'.$name_input.'","'.$this->saveSettingsDefault.'");};
											EYE.register(initLayout, \'init\');
									})(jQuery)
									</script>';
									break;
								case 'border_color': 
								$this->_html .='<div class="separation"></div>';
									$color = $item->name.'_border_color';
									$weight = $item->name.'_border_weight';
									$style = $item->name.'_border_style';
									$label = 'Border color';
									$this->_html .= Editor::showColor($label,(string)$settings->$color,$color);
									$this->_html .= '<p><label>'.$this->l('Boder weight').'</label>
									<input type="text" name="'.$weight.'" value="'.($settings->$weight ? $settings->$weight : '').'" /><span>px</span></p>
									<p><label>'.$this->l('Boder style').'</label>
									<select name="'.$style.'">
									<option value="">'.$this->l('No choose').'</option>
									';
									foreach($boderStyle as $sty)
									{
										$this->_html .= '<option '.($settings->$style == $sty ? "selected=selected" : "").' value="'.$sty.'">'.$sty.'</option>';
									}
									$this->_html .= '</select></p>
									<script type="text/javascript">
									(function($){var initLayout = function() {
											colorEvent("'.$color.'","'.$this->saveSettingsDefault.'");};
											EYE.register(initLayout, \'init\');
									})(jQuery)
									</script>';
									break;
								case 'font':
									$ffamily = $item->name.'_fstyle';
									$fsize = $item->name.'_fsize';
									$fweight = $item->name.'_fweight';
									$fweight2 = $item->name.'_fweight2';
									$this->_html .= Editor::showFont($this->font_list,$item->name,$item->text,$settings,$this->saveSettingsDefault);	
									$this->_html .= '<script type="text/javascript">$(document).ready(function() {loadGoogleFontFromConfig("'.$item->name.'_fstyle'.'","'.$settings->$ffamily.'","'.$settings->$fsize.'","'.$settings->$fweight.'","'.$settings->$fweight2.'");});</script>';
									break;
							}
						}
						$this->_html .='<div class="separation"></div>';
					}
					$this->_html .= '</fieldset>';
				}
				$this->_html .= '<br/>';
				
				$this->_html .= '<center>
				<input type="submit" class="button pointer" name="saveMainSetting" value="'.$this->l('Save').'" id="saveMainSetting" />';
				if($this->saveSettingsDefault)
					$this->_html .= ' <input type="submit" class="button pointer" name="cancelSetting" value="'.$this->l('Cancel').'" id="resetSetting" />';
				else
				$this->_html .= ' <input type="submit" class="button pointer" name="resetSetting" value="'.$this->l('Reset').'" id="resetSetting" />';
			$this->_html .= '</center>
			</form>
		';
	}
	function hookTop($params)
	{
		$id_shop = (int)Context::getContext()->shop->id;
		if(Configuration::get('SHOW_PANEL_FRONT_END',null,Shop::getGroupFromShop($id_shop),$id_shop) == 0)
			return;
		global $smarty;
		foreach($this->items_settings->children() as $child)
		{
			foreach($child->children() as $item)
			{
				$frontend = true;
				$style_css = explode(",", $item->style_css);
				foreach($style_css as $style)
				{
					if($frontend)
					{
						switch ($style) 
						{
							case 'background':
									if($item->frontend == "true")
									{
										$color_bgs[''.$item->name.'_color'] = array($item->text,$item->class);
										$frontend = false;
									}
								break;
							case 'color_bg':
									if($item->frontend == "true")
										$color_bgs[''.$item->name.'_color_bg'] = array($item->text,$item->class);
								break;
							case 'color_text':
									if($item->frontend == "true")
										$color_texts[''.$item->name.'_color_text'] = array($item->text,$item->class);
								break;
							case 'font':
									if($item->frontend == "true")
										$fonts[''.$item->name.'_fstyle'] = array($item->text,$item->class);
								break;
						}
					}
				}
			}
		}
		foreach ( $this->pattern_list as $key=>$pattern_temp)
		{
			if(basename($pattern_temp) != "no_img.jpg")
				$patterns[$key] = basename($pattern_temp);
		}
		$smarty->assign(array(
			'patterns' => $patterns,
			'color_bgs' => $color_bgs,
			'color_texts' => $color_texts,
			'fonts' => $fonts,
			'font_list' => $this->font_list,
			'column_lists' => $this->column_grids,
			'path' => _MODULE_DIR_.$this->name,
			'path_admin' =>_PS_ADMIN_IMG_,
			'color_templates' =>$this->color_list
		));
		return $this->display(__FILE__, 'csthemeeditor.tpl');
	}
	
	
	function hookHeader($params)
	{
		global $smarty;
		$id_shop = (int)Context::getContext()->shop->id;
		if(!Configuration::get('CS_COLOR_TEMPLATE',null,Shop::getGroupFromShop($id_shop),$id_shop))
			Configuration::updateValue('CS_COLOR_TEMPLATE','default',false,Shop::getGroupFromShop($id_shop),$id_shop);
		$color_tp = Configuration::get('CS_COLOR_TEMPLATE',null,Shop::getGroupFromShop($id_shop),$id_shop);		
		
		$this->_html .= '<link href="'.$this->_path.'config.css.php" rel="stylesheet" type="text/css" media="all" />';
		$this->_html .= '<script type="text/javascript" src="'.$this->_path.'config.js.php"></script>';
		//$this->context->controller->addJS($this->_path.'js/frontend/jquery.cookie.js');
		$this->context->controller->addCss($this->_path.'css/colorpicker.css', 'all');
		$this->context->controller->addJs($this->_path.'js/colorpicker.js',true);
		$this->context->controller->addJs($this->_path.'js/eye.min.js',true);
		$this->context->controller->addJs($this->_path.'js/utils.js',true);
		$this->context->controller->addJs($this->_path.'js/frontend/setconfig.js',true);
		if(in_array($color_tp,$this->color_list))
			$path = dirname(__FILE__).'/settings/default/';
		else
			$path = dirname(__FILE__).'/settings/';
		if (!file_exists($path.$color_tp.'.xml'))
			$color_tp = Configuration::get('CS_COLOR_TEMPLATE',null,Shop::getGroupFromShop(Configuration::get('PS_SHOP_DEFAULT')),Configuration::get('PS_SHOP_DEFAULT'));
		$settings = simplexml_load_file($path.$color_tp.'.xml');	
		if(isset($_COOKIE['setting_column']) && $_COOKIE['setting_column']!=null) 
		{
			$cookie_column = explode("&",$_COOKIE['setting_column']);
			$settings->column = $cookie_column[0];
			$settings->column_class = $cookie_column[1];
		}
		$class_list = explode(",",$settings->column_class);
		$grid_product = 'grid_';
		$number_p = 1;
		switch ($settings->column)
		{
		case '1_column':
			$settings->left_class = ' ';
			$settings->center_class = '';
			$settings->right_class = ' ';
			$grid_column = 'one_column';
			$temp = substr($class_list[0],strpos($class_list[0], '(') + 1);
			$number_p = substr($temp,0,strpos($temp, ')'));
			$grid_product .= (int)substr($class_list[0],5)/(int)$number_p;
		  break;
		case '2_column_left':
			$grid_column = 'two_column';
			$settings->left_class =$class_list[0];
			$settings->center_class =substr($class_list[1],0,strpos($class_list[1], '(')) ;
			$settings->right_class = '';
			$temp = substr($class_list[1],strpos($class_list[1], '(') + 1);
			$number_p = substr($temp,0,strpos($temp, ')'));
			$grid_product .= (int)substr($settings->center_class,5)/(int)$number_p;
			$this->_html .= '
			<script type="text/javascript">
			$(window).ready(function(){
			if ($("body").attr("id") != "index")
			{
				$("#center_column").addClass("omega");
			}});</script> ';
			break;
		case '2_column_right':
			$grid_column = 'two_column';
			$settings->left_class = '';
			$settings->center_class = substr($class_list[0],0,strpos($class_list[0], '('));
			$settings->right_class = $class_list[1];
			$temp = substr($class_list[0],strpos($class_list[0], '(') + 1);
			$number_p = substr($temp,0,strpos($temp, ')'));
			$grid_product .= (int)substr($settings->center_class,5)/(int)$number_p;
			$this->_html .= '
			<script type="text/javascript">
			$(window).ready(function(){
			if ($("body").attr("id") != "index")
			{
				$("#center_column").addClass("alpha");
			}
			}); </script> ';
			break;
		case '3_column':
			$grid_column = 'three_column';
			$settings->left_class = $class_list[0];
			$settings->center_class =substr($class_list[1],0,strpos($class_list[1], '(')) ;
			$settings->right_class = $class_list[2];
			$temp = substr($class_list[1],strpos($class_list[1], '(') + 1);
			$number_p = substr($temp,0,strpos($temp, ')'));
			$grid_product .= (int)substr($settings->center_class,5)/(int)$number_p;
			$this->_html .= '
			<script type="text/javascript">
			$(window).ready(function(){
			if ($("body").attr("id") != "index")
				{
					$("#center_column").removeClass("alpha");
					$("#center_column").removeClass("omega");
			}});</script>'; 
			break;
		default:
			$settings->left_class = 'grid_6';
			$settings->center_class = 'grid_18';
			$settings->right_class = '';
			$grid_product = 'grid_6';
		}
		$this->_html .= '
		<script type="text/javascript">
		$.cookie("grid_product", "'.$grid_product.'"); 
		</script>';
		$smarty->assign(array(
			'settings' => $settings,
			'grid_product' =>$grid_product,
			'grid_column' => $grid_column
		));
		return $this->_html;
	}
}


