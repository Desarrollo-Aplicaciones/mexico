<?php
if (!defined('_CAN_LOAD_FILES_'))
	exit;
include_once(dirname(__FILE__).'/classes/CsMegaMenuClass.php');
include_once(dirname(__FILE__).'/classes/CsMegaMenuOptionClass.php');
class CsMegaMenu extends Module
{
	private $_html;
	private $temp_url = "{megamenu_url}";
	private $optionsMenu = array('Category','Product','Static Block','Manufacture','Information');
	private $user_groups;
	function __construct()
	{
		$this->name = 'csmegamenu';
	 	$this->tab = 'MyBlocks';
	 	$this->version = '1.0';
		$this->author = 'CodeSpot';
                $this->_respMenu='';

		parent::__construct();

		$this->displayName = $this->l('CS Mega Menu block');
		$this->description = $this->l('Adds a mega menu block.');
	}
	
	public function init_data()
	{
	
		$id_en = Language::getIdByIso('en');
		$id_fr = Language::getIdByIso('fr');
		$id_shop = Configuration::get('PS_SHOP_DEFAULT');
		$option_1 = '{"opt_fill_column":"1","opt_show_image_cat":"0","opt_image_size_cate":"category_default","opt_show_sub_cat":"0","opt_show_parent_cat":"1","opt_id_parent_cat":"3"}';
		$option_2 = '{"opt_fill_column":"1","opt_show_image_cat":"0","opt_image_size_cate":"category_default","opt_show_sub_cat":"0","opt_show_parent_cat":"1","opt_id_parent_cat":"4"}';
		$option_3 = '{"opt_fill_column":"1","opt_show_image_cat":"0","opt_image_size_cate":"category_default","opt_show_sub_cat":"0","opt_show_parent_cat":"1","opt_id_parent_cat":"4"}';
		$option_4 = '{"opt_fill_column":"1","opt_show_image_cat":"1","opt_image_size_cate":"medium_default","opt_show_sub_cat":"1","opt_show_parent_cat":"1","opt_id_parent_cat":"3"}';
		$option_5 = '{"opt_show_image_product":"1","opt_image_size_product":"medium_default","input_hidden_id":"1-2-3-4-","input_hidden_name":"iPod Nano\iPod Nano\iPod Nano\iPod Nano\u00a4","opt_fill_column":"2"}';
		$option_6 = '{"opt_show_image_manu":"1","opt_image_size_manu":"medium_default","opt_show_name_manu":"0","opt_list_manu":["1","6","3","4","8","7","9","2"],"opt_fill_column":"1"}';
		$option_7 = '{&quot;opt_content_static&quot;:{&quot;1&quot;:&quot;&lt;p&gt;Presthemes GoMarket has a simple-looking, neat, clean layout with blocks of featured products, featured categories and promotions are well-organized on a white background. But this design is highlighted with many new and useful features, becomes one of the most powerful Prestashop templates.&lt;\/p&gt;&quot;,&quot;2&quot;:&quot;&lt;p&gt;Presthemes GoMarket a soign\u00e9e, mise en page simple daspect, propre avec des blocs de produits en vedette, vedette cat\u00e9gories et promotions sont bien organis\u00e9s sur un fond blanc. Mais cette conception est mise en \u00e9vidence avec de nombreuses fonctionnalit\u00e9s nouvelles et utiles, devient lun des plus puissants mod\u00e8les les Prestashop.&lt;\/p&gt;&quot;},&quot;opt_fill_column&quot;:&quot;1&quot;}';
		$option_8 = '{"opt_show_image_product":"1","opt_image_size_product":"medium_default","input_hidden_id":"1-2-3-4-","input_hidden_name":"iPod Nano\iPod Nano\iPod Nano\iPod Nano\u00a4","opt_fill_column":"2"}';
		$option_9 = '{"opt_fill_column":"1","opt_show_image_cat":"0","opt_image_size_cate":"category_default","opt_show_sub_cat":"0","opt_show_parent_cat":"1","opt_id_parent_cat":"3"}';
		$option_10 = '{"opt_fill_column":"1","opt_show_image_cat":"0","opt_image_size_cate":"category_default","opt_show_sub_cat":"0","opt_show_parent_cat":"1","opt_id_parent_cat":"3"}';
		$option_11 = '{"opt_fill_column":"1","opt_show_image_cat":"0","opt_image_size_cate":"category_default","opt_show_sub_cat":"0","opt_show_parent_cat":"1","opt_id_parent_cat":"4"}';
		
		$option_12 = '{"opt_fill_column":"1","opt_show_image_cat":"0","opt_image_size_cate":"small_default","opt_show_sub_cat":"0","opt_show_parent_cat":"1","opt_id_parent_cat":"4"}';
		
		//insert db table csmegamenu
		if(!Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'csmegamenu` (`id_menu`, `number_column`, `width`, `link_of_title`,`icon`,`display`,`display_icon`,`classes`,`position`) VALUES 
		(1, 2, 322,"{megamenu_url}index.php?id_category=3&controller=category","icon_1.jpg", 1, 1,"class_menu1", 0),
		(2, 1, 200, "{megamenu_url}index.php?id_category=4&controller=category","icon_2.jpg", 1, 1,"",1),
		(3, 2, 620, "{megamenu_url}index.php?id_category=3&controller=category","icon_3.png", 1, 1,"block_product",2),
		(4, 1, 905, "{megamenu_url}index.php?id_category=4&controller=category","icon_4.png", 1, 1,"menu_custom",3),
		(5, 6, 950, "{megamenu_url}index.php?id_category=3&controller=category","icon_5.png", 1, 1,"",4),
		(6, 1, 500, "{megamenu_url}index.php?id_category=3&controller=category","icon_6.png", 1, 1,"",5),
		(7, 0, 0, "{megamenu_url}index.php?id_category=3&controller=category","icon_7.jpg", 1, 1,"",0),
		(8, 0, 0, "{megamenu_url}index.php?id_category=3&controller=category", "icon_8.jpg", 1, 1,"",0),
		(9, 0, 0, "{megamenu_url}index.php?id_category=3&controller=category", "icon_9.jpg", 1, 1,"",0);'	
		) OR
		//insert db table csmegamenu_shop
		!Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'csmegamenu_shop` (`id_menu`, `id_shop`, `number_column`, `width`, `link_of_title`, `display`, `position`) VALUES
		(1, "'.$id_shop.'",2, 322,"{megamenu_url}index.php?id_category=3&controller=category",1, 0),
		(2, "'.$id_shop.'", 1, 200, "{megamenu_url}index.php?id_category=4&controller=category",1, 1),
		(3, "'.$id_shop.'",2, 620, "{megamenu_url}index.php?id_category=3&controller=category",1, 2),
		(4,"'.$id_shop.'",1, 905, "{megamenu_url}index.php?id_category=4&controller=category",1, 3),
		(5, "'.$id_shop.'",6, 950, "{megamenu_url}index.php?id_category=3&controller=category",1, 4),
		(6, "'.$id_shop.'",1, 500, "{megamenu_url}index.php?controller=best-sales",1,5),
		(7, "'.$id_shop.'",0, 0, "{megamenu_url}index.php?controller=prices-drop",1, 6),
		(8, "'.$id_shop.'",0, 0, "{megamenu_url}index.php?controller=contact",1, 7),
		(9, "'.$id_shop.'",0, 0, "{megamenu_url}index.php?id_category=3&controller=category", 1, 8);'
		) OR
		//insert db table csmegamenu_lang
		!Db::getInstance()->Execute("INSERT INTO `"._DB_PREFIX_."csmegamenu_lang` (`id_menu`, `id_lang`, `id_shop`, `title`,`description`) VALUES 
			(1, '".$id_en."', '".$id_shop."', 'Fashion for women', ''),
			(1, '".$id_fr."', '".$id_shop."', 'Fashion for women', ''),
			(2, '".$id_en."', '".$id_shop."', 'Fashion for men', ''),
			(2, '".$id_fr."', '".$id_shop."', 'Fashion for men', ''),
			(3, '".$id_en."', '".$id_shop."', 'Gift Toys', ''),
			(3, '".$id_fr."', '".$id_shop."', 'Gift Toys', ''),
			(4, '".$id_en."', '".$id_shop."', 'Laptop Computer', ''),
			(4, '".$id_fr."', '".$id_shop."', 'Laptop Computer', ''),
			(5, '".$id_en."', '".$id_shop."', 'Camera Camcorde', ''),
			(5, '".$id_fr."', '".$id_shop."', 'Camera Camcorde', ''),
			(6, '".$id_en."', '".$id_shop."', 'Iphone / Ipod / Ipad', ''),
			(6, '".$id_fr."', '".$id_shop."', 'Iphone / Ipod / Ipad', ''),
			(7, '".$id_en."', '".$id_shop."', 'Mother & Baby', ''),
			(7, '".$id_fr."', '".$id_shop."', 'Mother & Baby', ''),
			(8, '".$id_en."', '".$id_shop."', 'Watch', ''),
			(8, '".$id_fr."', '".$id_shop."', 'Watch', ''),
			(9, '".$id_en."', '".$id_shop."', 'Cosmetics', ''),
			(9, '".$id_fr."', '".$id_shop."', 'Cosmetics', '');"		
		) OR
		//insert db table csmegamenu_option
		!Db::getInstance()->Execute("INSERT INTO `"._DB_PREFIX_."csmegamenu_option` (`id_option`, `id_menu`, `type_option`, `position_option`, `content_option`) 
		VALUES 
			(1, 1, 0, 0,'".$option_1."'),
			(2, 1, 0, 0,'".$option_2."'),
			(3, 1, 0, 0, '".$option_3."'),
			(4, 2, 0, 2, '".$option_4."'),
			(5, 3, 1, 3,'".$option_5."'),
			(6, 4, 3, 0, '".$option_6."'),
			(7, 5, 2, 0,'".pSQL($option_7)."'),
			(8, 5, 1, 0,'".$option_8."'),
			(9, 5, 0, 0,'".$option_9."'),
			(10,5, 0, 0,'".$option_10."'),
			(11,5, 0, 0,'".$option_11."'),
			(12,1, 0, 0,'".$option_10."');") OR
		//insert db table csmegamenu_option_shop
		!Db::getInstance()->Execute("INSERT INTO `"._DB_PREFIX_."csmegamenu_option_shop` (`id_option`, `id_menu`, `id_shop`, `type_option`, `position_option`, `content_option`)
			VALUES 
			(1, 1, '".$id_shop."', 0, 0,'".$option_1."'),
			(2, 1, '".$id_shop."', 0, 7, '".$option_2."'),
			(3, 1, '".$id_shop."', 0, 9, '".$option_3."'),
			(4, 2, '".$id_shop."', 0, 5, '".$option_4."'),
			(5, 3, '".$id_shop."', 1, 8, '".$option_5."'),
			(6, 4, '".$id_shop."', 3, 2, '".$option_6."'),
			(7, 5, '".$id_shop."', 2, 0, '".pSQL($option_7)."'),
			(8, 5, '".$id_shop."', 1, 1, '".$option_8."'),
			(9, 5, '".$id_shop."', 0, 4, '".$option_9."'),
			(10, 5, '".$id_shop."', 0, 3, '".$option_10."'),
			(11, 5, '".$id_shop."', 0, 2, '".$option_11."'),
			(12, 1, '".$id_shop."', 0, 4, '".$option_12."');"))
			return false;
		return true;
	}
	
	function install()
	{
		if (!parent::install() || !$this->registerHook('header')|| !$this->registerHook('csmegamenu') ||
			!$this->registerHook('actionObjectCategoryUpdateAfter') ||
			!$this->registerHook('actionObjectCategoryDeleteAfter') ||
			!$this->registerHook('actionObjectCmsUpdateAfter') ||
			!$this->registerHook('actionObjectCmsDeleteAfter') ||
			!$this->registerHook('actionObjectManufacturerUpdateAfter') ||
			!$this->registerHook('actionObjectManufacturerDeleteAfter') ||
			!$this->registerHook('actionObjectProductUpdateAfter') ||
			!$this->registerHook('actionObjectProductDeleteAfter') ||
			!$this->registerHook('categoryUpdate') ||
			!$this->registerHook('actionUpdateQuantity')
			)
			return false;
		if (!Db::getInstance()->Execute('CREATE TABLE '._DB_PREFIX_.'csmegamenu (`id_menu` int(10) unsigned NOT NULL AUTO_INCREMENT,`number_column` int(10) unsigned default \'1\', `width` int(10) unsigned default \'120\',  `link_of_title` varchar(300),`icon` varchar(255) default \'\', `display` tinyint(1) NOT NULL default \'1\',`display_icon` tinyint(1) NOT NULL default \'1\',`classes` varchar(150),`position` int(10) unsigned default \'0\',PRIMARY KEY (`id_menu`)) ENGINE=InnoDB default CHARSET=utf8'))
	 		return false;
		if (!Db::getInstance()->Execute('CREATE TABLE '._DB_PREFIX_.'csmegamenu_shop (`id_menu` int(10) unsigned NOT NULL ,`id_shop` int(10) unsigned NOT NULL,`number_column` int(10) unsigned default \'1\', `width` int(10) unsigned default \'120\',  `link_of_title` varchar(300), `display` tinyint(1) NOT NULL default \'1\',`position` int(10) unsigned default \'0\',PRIMARY KEY (`id_menu`,`id_shop`)) ENGINE=InnoDB default CHARSET=utf8'))
	 		return false;
		if (!Db::getInstance()->Execute('CREATE TABLE '._DB_PREFIX_.'csmegamenu_option (`id_option` int(10) unsigned NOT NULL AUTO_INCREMENT,`id_menu` int(10) unsigned NOT NULL,`type_option` int(10) unsigned NOT NULL, `position_option` int(10) unsigned default \'0\', `content_option` text ,PRIMARY KEY (`id_option`)) ENGINE=InnoDB default CHARSET=utf8'))
			return false;
		if (!Db::getInstance()->Execute('CREATE TABLE '._DB_PREFIX_.'csmegamenu_option_shop (`id_option` int(10) unsigned NOT NULL,`id_menu` int(10) unsigned NOT NULL,`id_shop` int(10) unsigned NOT NULL,`type_option` int(10) unsigned NOT NULL, `position_option` int(10) unsigned default \'0\', `content_option` text ,PRIMARY KEY (`id_option`,`id_shop`)) ENGINE=InnoDB default CHARSET=utf8'))
			return false;
		if (!Db::getInstance()->Execute('CREATE TABLE '._DB_PREFIX_.'csmegamenu_lang (`id_menu` int(10) unsigned NOT NULL, `id_lang` int(10) unsigned NOT NULL,`id_shop` int(10) unsigned NOT NULL, `title` varchar(255) NOT NULL default \'\', `description` varchar(255) default \'\', PRIMARY KEY (`id_menu`,`id_lang`,`id_shop`)) ENGINE=InnoDB default CHARSET=utf8'))
	 		return false;
		$this->init_data();
		return true;
	}
	
	public function uninstall()
	{
	 	if (parent::uninstall() == false)
	 		return false; 	
		if (!Db::getInstance()->Execute('DROP TABLE '._DB_PREFIX_.'csmegamenu') OR !Db::getInstance()->Execute('DROP TABLE '._DB_PREFIX_.'csmegamenu_shop') OR !Db::getInstance()->Execute('DROP TABLE '._DB_PREFIX_.'csmegamenu_option') OR !Db::getInstance()->Execute('DROP TABLE '._DB_PREFIX_.'csmegamenu_option_shop') OR !Db::getInstance()->Execute('DROP TABLE '._DB_PREFIX_.'csmegamenu_lang'))
	 		return false;
		$this->_clearCache('csmegamenu.tpl');
	 	return true;
	}
	
	private function _displayHelp() //not write
	{
	}
	//cms
	private function displayRecurseCheckboxes($categories, $selected, $has_suite = array())
	{
		static $irow = 0;	
		$this->_html .= '
			<tr '.($irow++ % 2 ? 'class="alt_row"' : '').'>
				<td width="3%"><input type="checkbox" name="content_option[4][footerBox][]" class="cmsBox" id="1_'.$categories['id_cms_category'].'" value="1_'.$categories['id_cms_category'].'" '.
				(in_array('1_'.$categories['id_cms_category'], $selected) ? ' checked="checked"' : '').' /></td>
				<td width="3%">'.$categories['id_cms_category'].'</td>
				<td width="94%">';
		for ($i = 1; $i < $categories['level_depth']; $i++)
		if(isset($has_suite[$i - 1]))
			$this->_html .=	'<img style="vertical-align:middle;" src="../img/admin/lvl_'.$has_suite[$i - 1].'.gif" alt="" />';
		$this->_html .= '<img style="vertical-align:middle;" src="../img/admin/'.($categories['level_depth'] == 0 ? 'lv1' : 'lv2_');
		if(isset($has_suite[$categories['level_depth'] - 1]))
			$this->_html .= 'b.gif" alt="" /> &nbsp;';
		else
			$this->_html .= 'f.gif" alt="" /> &nbsp;';
			$this->_html .= '<label for="1_'.$categories['id_cms_category'].'" class="t"><b>'.$categories['name'].'</b></label></td>
			</tr>';
		if (isset($categories['children']))
			foreach ($categories['children'] as $key => $category)
			{
				$has_suite[$categories['level_depth']] = 1;
				if (sizeof($categories['children']) == $key + 1 AND !sizeof($categories['cms']))
					$has_suite[$categories['level_depth']] = 0;
				$this->displayRecurseCheckboxes($category, $selected, $has_suite, 0);
			}
		
		$cpt = 0;
		foreach ($categories['cms'] as $cms)
		{
			$this->_html .= '
				<tr '.($irow++ % 2 ? 'class="alt_row"' : '').'>
					<td width="3%"><input type="checkbox" name="content_option[4][footerBox][]" class="cmsBox" id="0_'.$cms['id_cms'].'" value="0_'.$cms['id_cms'].'" '.
					(in_array('0_'.$cms['id_cms'], $selected) ? ' checked="checked"' : '').' /></td>
					<td width="3%">'.$cms['id_cms'].'</td>
					<td width="94%">';
			for ($i = 0; $i < $categories['level_depth']; $i++)
			if(isset($has_suite[$i]))
				$this->_html .=	'<img style="vertical-align:middle;" src="../img/admin/lvl_'.$has_suite[$i].'.gif" alt="" />';
			$this->_html .= '<img style="vertical-align:middle;" src="../img/admin/lv2_'.(++$cpt == sizeof($categories['cms']) ? 'f' : 'b').'.gif" alt="" /> &nbsp;
			<label for="0_'.$cms['id_cms'].'" class="t" style="margin-top:6px;">'.$cms['meta_title'].'</label></td>
				</tr>';
		}
	}
	private function _displayFormCMS($array_check)
	{
		global $currentIndex, $cookie;
		$this->_html .='<div class="margin-form"><table cellspacing="0" cellpadding="0" class="table" width="100%">
				<tr>
					<th width="3%"><input type="checkbox" name="content_option[4][checkme_cms]" class="noborder" onclick="checkallCMSBoxes($(this).attr(\'checked\'))"/></th>
					<th width="3%">'.$this->l('ID').'</th>
					<th width="94%">'.$this->l('Name').'</th>
				</tr>';
				$this->displayRecurseCheckboxes(CMSCategory::getRecurseCategory($cookie->id_lang),$array_check);
		$this->_html .= '</table></div>';
	}
	//cms
	private function _displayAddOptionForm()
	{
		global $currentIndex, $cookie;
		$menus = $this->getMenus();
		$menu_item = new CsMegaMenuClass(Tools::getValue('id_menu'));
		$this->_html .= '
		<script type="text/javascript">
		$(window).ready(function(){
			'.(isset($_POST['type_option']) ? '
			for (var i=0;i<5;i++)
			{ 
				$(".option_type_" + i + "").hide();
			}
			$(".option_type_'.$_POST['type_option'].'").show();' : '' ).'
			});
		</script>';
		$this->_html .= '<div style="overflow:hidden"><div class="sidebar" style="width:35%">';
		$this->_html .=$this->_displayMenuCategories().'</div><form method="post" action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" enctype="multipart/form-data"><div id="moduleContainer"><fieldset>
			<legend><img src="'.$this->_path.'logo.gif" alt="" title="'.$this->l('Add New Option').$menu_item->title[(int)($cookie->id_lang)].'" /> '.$this->l('Add New Option').$menu_item->title[(int)($cookie->id_lang)].'</legend>
		
		<input type="hidden" name="id_menu" value="'.Tools::getValue('id_menu').'"/>
		<div class="div_select_option">
					<div id="div_select_option">
					<p>
					<label>'.$this->l('Add for menu').'</label>
						<select name="id_menu" style="width:250px;" >';
					foreach ($menus AS $menu){
						$this->_html .= '<option value="'.$menu['id_menu'].'">'.$menu['id_menu'].' - '.$menu['title'].'</option>';
					}
					$this->_html .= '
						</select>
					</p>
					<p>
					<label>'.$this->l('Option:').'</label>
						<select name="type_option" onchange="displayFormOption(this.value);" style="width:250px;" >';
					foreach ($this->optionsMenu AS $key=>$optionValue){
						$this->_html .= '<option value="'.$key.'" '.(isset($_POST['type_option']) && $_POST['type_option'] == $key ? 'selected' : '' ).' >'.$optionValue.'</option>';
					}
					$this->_html .= '
						</select>
					</p>
					<p>
					<label>'.$this->l('Fill the column:').'</label>
					<input type="text" class="opt_fill_column" size="20" value="'.(isset($_POST['content_option'][''.Tools::getValue('type_option').'']['opt_fill_column']) ? $_POST['content_option'][''.Tools::getValue('type_option').'']['opt_fill_column'] : '').'"  name="content_option[0][opt_fill_column]"/>
					</p>
					</div>
			<div class="div_content_option">
				<div class="option_type_0">
					<p>
					<label>'.$this->l('Show Image:').'</label> 
					<input class="opt_show_image_cat" type="radio" name="content_option[0][opt_show_image_cat]" value="1"'.(Tools::getValue('display',1) ? 'checked="checked"' : '').' />
					<label class="t"><img src="'._PS_ADMIN_IMG_.'enabled.gif" alt="Enabled" title="Enabled" /></label>
					<input class="opt_show_image_cat" type="radio" name="content_option[0][opt_show_image_cat]" value="0"'.(Tools::getValue('display',1) ? '' : 'checked="checked"').' />
					<label class="t"><img src="'._PS_ADMIN_IMG_.'disabled.gif" alt="Disabled" title="Disabled" /></label></p>';
					
					$imageTypes = ImageType ::getImagesTypes();
				$this->_html .= '
					<p>
					<label>'.$this->l('Size Image (W x H):').'</label>
					<select class="opt_img_size"  name="content_option[0][opt_image_size_cate]">';
				foreach ($imageTypes as $imageType)
				{
					$this->_html .= '<option  value="'.$imageType['name'].'">'.$imageType['name'].' - '.$imageType['width'].'x'.$imageType['height'].'</option>';
				}
				$this->_html .= '</select></p>
					<p>
					<label>'.$this->l('Show Sub Category:').'</label> 
					<input class="opt_show_sub_cat" name="content_option[0][opt_show_sub_cat]" type="radio" value="1"'.(Tools::getValue('display',1) ? 'checked="checked"' : '').' />
					<label class="t"><img src="'._PS_ADMIN_IMG_.'enabled.gif" alt="Enabled" title="Enabled" /></label>
					<input class="opt_show_sub_cat" name="content_option[0][opt_show_sub_cat]" type="radio" value="0"'.(Tools::getValue('display',1) ? '' : 'checked="checked"').' />
					<label class="t"><img src="'._PS_ADMIN_IMG_.'disabled.gif" alt="Disabled" title="Disabled" /></label></p>
					<p>
					<label>'.$this->l('Show Parent Category:').'</label> 
					<input class="opt_show_parent_cat" type="radio" name="content_option[0][opt_show_parent_cat]" value="1"'.(Tools::getValue('display',1) ? 'checked="checked"' : '').' />
					<label class="t"><img src="'._PS_ADMIN_IMG_.'enabled.gif" alt="Enabled" title="Enabled" /></label>
					<input class="opt_show_parent_cat" type="radio" name="content_option[0][opt_show_parent_cat]" value="0"'.(Tools::getValue('display',1) ? '' : 'checked="checked"').' />
					<label class="t"><img src="'._PS_ADMIN_IMG_.'disabled.gif" alt="Disabled" title="Disabled" /></label></p>';
					$cate_selected = array();
					$helper = new Helper();
					$this->_html .= '<div class="cs_out_content"><label>Choose Parent Category</label><div class="margin-form">'.
					$helper->renderCategoryTree(null, $cate_selected, 'content_option[0][opt_id_parent_cat]', true, false, array(), false, true);
				$this->_html .= '</div></div>
				</div>
				<div class="option_type_1" style="display:none">
				<p>
				<label>'.$this->l('Show Image:').'</label> 
					<input class="opt_show_image_product" type="radio" name="content_option[1][opt_show_image_product]" value="1"'.(Tools::getValue('display',1) ? 'checked="checked"' : '').' />
					<label class="t"><img src="'._PS_ADMIN_IMG_.'enabled.gif" alt="Enabled" title="Enabled" /></label>
					<input class="opt_show_image_product" type="radio" name="content_option[1][opt_show_image_product]" value="0"'.(Tools::getValue('display',1) ? '' : 'checked="checked"').' />
					<label class="t"><img src="'._PS_ADMIN_IMG_.'disabled.gif" alt="Disabled" title="Disabled" /></label></p>
				<p>
					<label>'.$this->l('Size Image (W x H):').'</label>
					<select class="opt_img_size" name="content_option[1][opt_image_size_product]">';
				foreach ($imageTypes as $imageType)
				{
					$this->_html .= '<option  value="'.$imageType['name'].'">'.$imageType['name'].' - '.$imageType['width'].'x'.$imageType['height'].'</option>';
				}
				$this->_html .= '</select></p>
				<p>
				<label>'.$this->l('Choose product').'</label>
				<input type="text" value="" id="opt_product_autocomplete_input" /></p>
				<input type="hidden" value="" id="input_hidden_id" name="content_option[1][input_hidden_id]"/>
				<input type="hidden" value="" id="input_hidden_name" name="content_option[1][input_hidden_name]"/>
				<div id="opt_result_product_autocomplete" class="margin-form"></div>
				</div>
				<div class="option_type_2" style="display:none">';
				$defaultLanguage = (int)(Configuration::get('PS_LANG_DEFAULT'));
				$languages = Language::getLanguages(false);
				$divLangName = 'opt_content_static';
				$iso = Language::getIsoById((int)($cookie->id_lang));
				$isoTinyMCE = (file_exists(_PS_ROOT_DIR_.'/js/tiny_mce/langs/'.$iso.'.js') ? $iso : 'en');
				$ad = dirname($_SERVER["PHP_SELF"]);
				$this->_html .=  '
				<script type="text/javascript">	
				var iso = \''.$isoTinyMCE.'\' ;
				var pathCSS = \''._THEME_CSS_DIR_.'\' ;
				var ad = \''.$ad.'\' ;
				</script>
				<script type="text/javascript" src="'.__PS_BASE_URI__.'js/tiny_mce/tiny_mce.js"></script>
				<script type="text/javascript" src="'.__PS_BASE_URI__.'js/tinymce.inc.js"></script>
				<script type="text/javascript">id_language = Number('.$defaultLanguage.');</script>	
				<script type="text/javascript">
				$(document).ready(function(){		
					tinySetup({});});
				</script>
				<div class="cs_out_content">
				<label>'.$this->l('Content:').'</label>
				<div class="margin-form">';									
					foreach ($languages as $language)
					{
						$this->_html .= '
					<div id="opt_content_static_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').'; float: left;">
						<textarea class="rte" name="content_option[2][opt_content_static]['.$language['id_lang'].']" id="opt_content_static'.$language['id_lang'].'" cols="100" rows="20">'.Tools::getValue('opt_content_static_'.$language['id_lang']).'</textarea>
					</div>';
					}
					$this->_html .= $this->displayFlags($languages, $defaultLanguage, $divLangName, 'opt_content_static', true);
					$this->_html .= '</div></div>';
				$this->_html .= '</div>
				<div class="option_type_3" style="display:none">
				<label>'.$this->l('Show Image:').'</label> 
				<input class="opt_show_image_manu" type="radio" name="content_option[3][opt_show_image_manu]" value="1"'.(Tools::getValue('display',1) ? 'checked="checked"' : '').' />
				<label class="t"><img src="'._PS_ADMIN_IMG_.'enabled.gif" alt="Enabled" title="Enabled" /></label>
				<input class="opt_show_image_manu" type="radio" name="content_option[3][opt_show_image_manu]" value="0"'.(Tools::getValue('display',1) ? '' : 'checked="checked"').' />
				<label class="t"><img src="'._PS_ADMIN_IMG_.'disabled.gif" alt="Disabled" title="Disabled" /></label></p>';
				$this->_html .= '
					<p>
					<label>'.$this->l('Size Image (W x H):').'</label>
					<select class="opt_img_size" name="content_option[3][opt_image_size_manu]">';
				foreach ($imageTypes as $imageType)
				{
					$this->_html .= '<option  value="'.$imageType['name'].'">'.$imageType['name'].' - '.$imageType['width'].'x'.$imageType['height'].'</option>';
				}
				$this->_html .= '</select></p>
				<p>
				<label>'.$this->l('Show Name:').'</label> 
				<input class="opt_show_name_manu" type="radio" name="content_option[3][opt_show_name_manu]" value="1"'.(Tools::getValue('display',1) ? 'checked="checked"' : '').' />
				<label class="t"><img src="'._PS_ADMIN_IMG_.'enabled.gif" alt="Enabled" title="Enabled" /></label>
				<input class="opt_show_name_manu" type="radio" name="content_option[3][opt_show_name_manu]" value="0"'.(Tools::getValue('display',1) ? '' : 'checked="checked"').' />
				<label class="t"><img src="'._PS_ADMIN_IMG_.'disabled.gif" alt="Disabled" title="Disabled" /></label></p>
				<div class="cs_out_content">
				<label>'.$this->l('Choose Manufacturers:').'</label>
				<div class="margin-form">
				<table cellspacing="0" cellpadding="0" class="table" width="100%">
				<tbody>
				<tr class="nodrag nodrop">
				<th><input type="checkbox" name="content_option[3][opt_check-manu]" class="noborder" /></th>
				<th>'.$this->l('ID').'</th>
				<th>'.$this->l('Name').'</th>
				</tr>
				';
				$manu_list = Manufacturer ::getManufacturers();
				static $irow_manu = 0;
				foreach($manu_list as $manu)
				{
					$this->_html .= '
					<tr '.($irow_manu++ % 2 ? 'class="alt_row"' : '').'>
					<td><input type="checkbox" name="content_option[3][opt_list_manu][]" value="'.$manu['id_manufacturer'].'"/>'.'
					</td>
					<td>'.$manu['id_manufacturer'].'</td>
					<td>'.$manu['name'].'</td>
					</tr>
					';
				}
				$this->_html .= '
				</tbody>
				</table>
				</div>
				</div>
				</div>
				<div class="option_type_4" style="display:none">
				';
				$array_check_cms =  array();
				$this->_displayFormCMS($array_check_cms);
				$this->_html .='
				</div>
			</div>
			
			<div class="margin-form"><input type="submit" class="button pointer" name="saveSubmitOptionMenu" value="'.$this->l('Save').'" id="saveSubmitOptionMenu" />
		</div>
		</fieldset>
		</div>
		</form>
		';
	}
	
	public function _displayMenuCategories()
	{
		global $currentIndex, $cookie;
		$this->context->controller->addJs($this->_path.'js/csmegamenu_dnd.js');
		$stringConfirmOption='if (!confirm(\'Are you sure that you want to delete this item option?\')) return false;';
		$html = '<form method="post" name="formMainMenu" action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" enctype="multipart/form-data">
		<div class="categorieTitle">
		<fieldset>
		<legend>'.$this->l('Menu Tree').'</legend>
		
		<div class="subHeadline">&nbsp;</div>
		<table width="100%" id="menu" class="table csmegamenu tableDnD feature table_grid" cellspacing="0" cellpadding="0">
		<tbody>
		<tr style="height: 40px" class="nodrag nodrop">
			<th class="center"><input type="checkbox" name="checkme" id="idCheckDelBoxesMenu" class="noborder" onclick="checkDelBoxesMenu(this.form, \'checkMenuItem[]\',this.checked)"></th>
			<th colspan="4"><input style="margin-left:9px" type="submit" class="button" name="submitDeleteMenus" value="Delete selected" onclick="return confirm(\'Delete selected items?\');"></th>
		</tr>
		';
		$menus = $this->getMenus();
		if (is_array($menus))
		{
			foreach ($menus as $menu)
			{
				$menu_item = new CsMegaMenuClass($menu['id_menu']);
				$options_list = $menu_item->getOptionForMenu();
				$number_option = $menu_item->getNumberOptionForMenu();
				$html .= '
				<tr class="row_hover" id="'.$menu['id_menu'].'">
				<td class="center"><input type="checkbox" name="checkMenuItem[]" class="noborder" value="'.$menu['id_menu'].'" onclick="checkCheckItemBox(this.form,\'checkme\',\'checkMenuItem[]\',this.checked)"/></td>
				<td class="dragHandle center">'.($menu !== end($menus) ? '<a href="'.$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&orderMenu&id_menu='.$menu['id_menu'].'&way=1&position='.($menu['position']+1).'"><img src="'._PS_ADMIN_IMG_.'down.gif" alt="'.$this->l('Down').'" /></a>' : '').($menu !== reset($menus) ? '<a href="'.$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&orderMenu&id_menu='.$menu['id_menu'].'&way=0&position='.($menu['position']-1).'"><img src="'._PS_ADMIN_IMG_.'up.gif" alt="'.$this->l('Up').'" /></a>' : '').'</td>
				<td><div class="categorieWidth" id="categorieWidth'.$menu['id_menu'].'" onclick="$(\'.categorieWidth\').css(\'font-weight\',\'normal\');$(this).css(\'font-weight\',\'bold\');return ajaxLoadMenu('.$menu['id_menu'].');"><a class="upper" href="#">'.$menu['title'].'</a></div></td>
				<td><b>'.$number_option.'</b></td>
				<td><b><img style="cursor:pointer" title="'.$this->l('Show option').'" id="image_more_options'.$menu['id_menu'].'" src="../img/admin/more.png" alt="'.$this->l('show Options').'" 
					onclick="if ($(\'#option_menu_'.$menu['id_menu'].'\').is(\':visible\'))$(this).attr(\'src\', \'../img/admin/more.png\'); else $(this).attr(\'src\', \'../img/admin/less.png\');$(\'#option_menu_'.$menu['id_menu'].'\').slideToggle(\'fast\');" /></b></td>
				</tr>';
				$menu_item = new CsMegaMenuClass($menu['id_menu']);
				$options_list = $menu_item->getOptionForMenu();
				$html .= '
					<script type="text/javascript">
						$(document).ready(function() {
							initTableDnD("table.tableDnDOption'.$menu['id_menu'].'");
						});
					</script>
					<tr id="option_menu_'.$menu['id_menu'].'" style="display:none">
					<td>&nbsp;</td>
					<td colspan="4"><table id="tableDnDOption'.$menu['id_menu'].'" class="tableOption tableDnDOption'.$menu['id_menu'].' table">';
					if(isset($options_list) && !empty($options_list))
					{
						$number = 1;
						foreach ($options_list as $option)
						{
							$html .= '<tr id="option'.$menu['id_menu'].'_'.$option['id_option'].'" class="row_hover_opt alt_row" >';
							foreach ($this->optionsMenu as $k=>$ot)
							{
								if($k == $option['type_option'])
								{
									$html .='<td class="pointer dragHandle center">'.($option !== end($options_list) ? '
										<a href="'.$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&orderOptionMenu&id_option='.$option['id_option'].'&way=1&position_option='.($option['position_option']+1).'&id_menu='.$option['id_menu'].'">
										<img src="'._PS_ADMIN_IMG_.'down.gif" alt="'.$this->l('Down').'" />
										</a>' : '').($option !== reset($options_list) ? '
										<a href="'.$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&orderOptionMenu&id_option='.$option['id_option'].'&way=0&position_option='.($option['position_option']-1).'&id_menu='.$option['id_menu'].'">
										<img src="'._PS_ADMIN_IMG_.'up.gif" alt="'.$this->l('Up').'" /></a>' : '').'</td>
										<td><a href="'.$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&editOptionMenu&id_option='.$option['id_option'].'&id_menu='.$menu['id_menu'].'&type_option='.$option['type_option'].'"><span>'.$number++.' - '.$this->l('Optiton ').''.$ot.' (#'.$option['id_option'].')</span></a></td>';
									break; 
								}
							}
							$html .= '<td><b onclick="'.$stringConfirmOption.'document.location =\''.$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&deleteOptionMenu&id_option='.$option['id_option'].'\'"><img src="'._PS_ADMIN_IMG_.'delete.gif" alt="'.$this->l('Delete Option').'" title="'.$this->l('Delete Option').'" /></b></td>
							</tr>';
						}
					}
					else
					{
						$html .= '<tr><td><a href="#"><span style="margin-left:97px;color:#585a69">'.$this->l('No option').'</span></a></td></tr>';
					}
					$html .= '</table></td>
					</tr>
					';
			}
		}
		
		$html .= '
		<tr style="height: 40px" class="nodrag nodrop">
			<th class="center"><input type="checkbox" name="checkme" id="idCheckDelBoxesMenu" class="noborder" onclick="checkDelBoxesMenu(this.form, \'checkMenuItem[]\',this.checked)"></th>
			<th colspan="4"><input style="margin-left:9px" type="submit" class="button" name="submitDeleteMenus" value="Delete selected" onclick="return confirm(\'Delete selected items?\');"></th>
		</tr>
		</tbody>
		</table>
		</fieldset>
		</div>
		</form>
		';
		return $html;
	}
	
	private function _displayUpdateOptionForm()
	{
		global $currentIndex, $cookie;
		if (!Tools::getValue('id_option'))
		{
			$this->_html .= '<a href="'.$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'"><img src="'._PS_ADMIN_IMG_.'arrow2.gif" alt="" />'.$this->l('Back to list').'</a>';
			return;
		}
		$menu_item = new CsMegaMenuClass(Tools::getValue('id_menu'));
		$option = new CsMegaMenuOptionClass((int)Tools::getValue('id_option'));
		if($option->type_option == 2) //check option static block
			$option->content_option =json_decode(htmlspecialchars_decode(($option->content_option)));
		else
			$option->content_option =json_decode($option->content_option);
		$this->_html .= '<div style="overflow:hidden"><div class="sidebar" style="width:35%">';
		$this->_html .=$this->_displayMenuCategories().'</div><form method="post" action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" enctype="multipart/form-data"><div id="moduleContainer"><fieldset>
			<legend><img src="'.$this->_path.'logo.gif" alt="'.$this->l('Edit Option').'" title="'.$this->l('Edit option for menu ').$menu_item->title[(int)($cookie->id_lang)].'" /> '.$this->l('Edit option (#'.$option->id_option.') for menu ').$menu_item->title[(int)($cookie->id_lang)].'</legend>
		<div class="div_select_option">
					<div id="div_select_option">
					<p>
					<label>'.$this->l('Option:').'</label>
						<select name="type_option" onchange="displayFormOption(this.value);" style="width:150px;" >';
					foreach ($this->optionsMenu AS $key=>$optionValue){
						$this->_html .= '<option value="'.$key.'"'; 
						if($key == $option->type_option)  
							$this->_html .= 'selected';
						$this->_html .= '>'.$optionValue.'</option>';
					}
					$this->_html .= '
						</select>
					</p>
					<p>
					<label>'.$this->l('Fill the column:').'</label>
					<input type="text" class="opt_fill_column" size="20" name="content_option[0][opt_fill_column]" value="'.(isset($option->content_option->opt_fill_column) ? $option->content_option->opt_fill_column : '').'"/></p>
					</div>
			<div class="div_content_option">
				<div class="option_type_0" style="display:none">
					<p>
					<label>'.$this->l('Show Image:').'</label> 
					<input class="opt_show_image_cat" type="radio" name="content_option[0][opt_show_image_cat]" value="1"';
					if(isset($option->content_option->opt_show_image_cat))
						if($option->content_option->opt_show_image_cat == 1)
							$this->_html .= 'checked="checked"';
					$this->_html .= '/>
					<label class="t"><img src="'._PS_ADMIN_IMG_.'enabled.gif" alt="Enabled" title="Enabled" /></label>
					<input class="opt_show_image_cat" type="radio" name="content_option[0][opt_show_image_cat]" value="0"';
					if(isset($option->content_option->opt_show_image_cat))
						if($option->content_option->opt_show_image_cat == 0)
							$this->_html .= 'checked="checked"';
					$this->_html .= '/>
					<label class="t"><img src="'._PS_ADMIN_IMG_.'disabled.gif" alt="Disabled" title="Disabled" /></label></p>';
					$imageTypes = ImageType ::getImagesTypes('categories');
				$this->_html .= '
				<p>
					<label>'.$this->l('Size Image (W x H):').'</label>
					<select class="opt_img_size"  name="content_option[0][opt_image_size_cate]">';
				
				foreach ($imageTypes as $key_image=>$imageType)
				{
					$this->_html .= '<option';
					if(isset($option->content_option->opt_image_size_cate))
					{
						if(strcmp($imageType['name'],$option->content_option->opt_image_size_cate)==0)
							$this->_html .= ' selected';
					}
					$this->_html .= ' value="'.$imageType['name'].'" >'.$imageType['name'].' - '.$imageType['width'].'x'.$imageType['height'].'</option>';
				}
				$this->_html .= '</select></p>
				<p>
					<label>'.$this->l('Show Sub Category:').'</label> 
					<input class="opt_show_sub_cat" name="content_option[0][opt_show_sub_cat]" type="radio" value="1"';
					if(isset($option->content_option->opt_show_sub_cat))
						if($option->content_option->opt_show_sub_cat == 1)
							$this->_html .= 'checked="checked"';
					$this->_html .= ' />
					<label class="t"><img src="'._PS_ADMIN_IMG_.'enabled.gif" alt="Enabled" title="Enabled" /></label>
					<input class="opt_show_sub_cat" name="content_option[0][opt_show_sub_cat]" type="radio" value="0"';
					if(isset($option->content_option->opt_show_sub_cat))
						if($option->content_option->opt_show_sub_cat == 0)
							$this->_html .= 'checked="checked"';
					$this->_html .= '/>
					<label class="t"><img src="'._PS_ADMIN_IMG_.'disabled.gif" alt="Disabled" title="Disabled" /></label></p>
					<p>
					<label>'.$this->l('Show Parent Category:').'</label> 
					<input class="opt_show_parent_cat" type="radio" name="content_option[0][opt_show_parent_cat]" value="1"';
					if(isset($option->content_option->opt_show_parent_cat))
						if($option->content_option->opt_show_parent_cat == 1)
							$this->_html .= 'checked="checked"';
					$this->_html .= '/>
					<label class="t"><img src="'._PS_ADMIN_IMG_.'enabled.gif" alt="Enabled" title="Enabled" /></label>
					<input class="opt_show_parent_cat" type="radio" name="content_option[0][opt_show_parent_cat]" value="0"';
					if(isset($option->content_option->opt_show_parent_cat))
						if($option->content_option->opt_show_parent_cat == 0)
							$this->_html .= 'checked="checked"';
					$this->_html .= '/>
					<label class="t"><img src="'._PS_ADMIN_IMG_.'disabled.gif" alt="Disabled" title="Disabled" /></label></p>';
					if(isset($option->content_option->opt_id_parent_cat))
						$cate_selected = array($option->content_option->opt_id_parent_cat);
					else
						$cate_selected = array();
					$helper = new Helper();
					$this->_html .= '<div class="cs_out_content"><label>Choose Parent Category</label>
					<div class="margin-form">
					'.
					$helper->renderCategoryTree(null, $cate_selected, 'content_option[0][opt_id_parent_cat]', true, false, array(), false, true);
				$this->_html .= '</div></div>
				</div>
				<div class="option_type_1" style="display:none">
				<p>
				<label>'.$this->l('Show Image:').'</label> 
					<input class="opt_show_image_product" type="radio" name="content_option[1][opt_show_image_product]" value="1"';
					if(isset($option->content_option->opt_show_image_product))
						if($option->content_option->opt_show_image_product == 1)
							$this->_html .= 'checked="checked"';
					$this->_html .= '/>
					<label class="t"><img src="'._PS_ADMIN_IMG_.'enabled.gif" alt="Enabled" title="Enabled" /></label>
					<input class="opt_show_image_product" type="radio" name="content_option[1][opt_show_image_product]" value="0"';
					if(isset($option->content_option->opt_show_image_product))
						if($option->content_option->opt_show_image_product == 0)
							$this->_html .= 'checked="checked"';
					$this->_html .= '/>
					<label class="t"><img src="'._PS_ADMIN_IMG_.'disabled.gif" alt="Disabled" title="Disabled" /></label></p>
					<p>
					<label>'.$this->l('Size Image (W x H):').'</label>
					<select class="opt_img_size" name="content_option[1][opt_image_size_product]">';
					$imageTypes = ImageType ::getImagesTypes('products');
				foreach ($imageTypes as $key_image=>$imageType)
				{
					$this->_html .= '<option';
					if(isset($option->content_option->opt_image_size_product))
					{
						if(strcmp($imageType['name'],$option->content_option->opt_image_size_product)==0)
							$this->_html .= ' selected';
					}
					$this->_html .= ' value="'.$imageType['name'].'" >'.$imageType['name'].' - '.$imageType['width'].'x'.$imageType['height'].'</option>';
				}
				$this->_html .= '</select></p>
				<p>
				<label>'.$this->l('Choose product').'</label>
				<input type="text" value="" id="opt_product_autocomplete_input" />
				</p>
				<input type="hidden" id="input_hidden_id" name="content_option[1][input_hidden_id]" value="'.(isset($option->content_option->input_hidden_id) ? $option->content_option->input_hidden_id : '').'"/>
				<input type="hidden" id="input_hidden_name" name="content_option[1][input_hidden_name]" value="'.(isset($option->content_option->input_hidden_id) ? $option->content_option->input_hidden_name : '').'"/>
				
				<div id="opt_result_product_autocomplete" class="margin-form">';
				if(isset($option->content_option->input_hidden_id))
				{
					$stringIdProducts = $option->content_option->input_hidden_id;
					$arrayIdProducts = explode('-',$stringIdProducts);
					$stringNameProducts = $option->content_option->input_hidden_name;
					$arrayNameProducts = explode('¤',$stringNameProducts);
					$products = array();
					foreach ($arrayIdProducts as $k=>$id_product)
					{
						
							if($id_product !== end($arrayIdProducts))
							{
								$proObj = new Product((int)$id_product,(int)($cookie->id_lang));
								if (Validate::isLoadedObject($proObj))
								{
									$this->_html .= $arrayNameProducts[$k].'
									<span class="delProducts" name="'.$id_product.'" style="cursor: pointer;">
									<img src="../img/admin/delete.gif" alt="" />
									</span><br />';
								}
							}
					}
				}
				$this->_html .= '</div></div>
				<div class="option_type_2" style="display:none">';
				$defaultLanguage = (int)(Configuration::get('PS_LANG_DEFAULT'));
				$languages = Language::getLanguages(false);
				$divLangName = 'opt_content_static';
				$iso = Language::getIsoById((int)($cookie->id_lang));
				$isoTinyMCE = (file_exists(_PS_ROOT_DIR_.'/js/tiny_mce/langs/'.$iso.'.js') ? $iso : 'en');
				$ad = dirname($_SERVER["PHP_SELF"]);
				$this->_html .=  '
				<script type="text/javascript">	
				var iso = \''.$isoTinyMCE.'\' ;
				var pathCSS = \''._THEME_CSS_DIR_.'\' ;
				var ad = \''.$ad.'\' ;
				</script>
				<script type="text/javascript" src="'.__PS_BASE_URI__.'js/tiny_mce/tiny_mce.js"></script>
				<script type="text/javascript" src="'.__PS_BASE_URI__.'js/tinymce.inc.js"></script>
				<script type="text/javascript">id_language = Number('.$defaultLanguage.');</script>	
				<script type="text/javascript">
				$(document).ready(function(){		
					tinySetup({});});
				</script>
				<div class="cs_out_content">
				<label>'.$this->l('Content:').'</label>
				<div class="margin-form">';
					foreach ($languages as $language)
					{
						if(isset($option->content_option->opt_content_static->$language['id_lang']))
							$option->content_option->opt_content_static->$language['id_lang'] = str_replace($this->temp_url, _PS_BASE_URL_.__PS_BASE_URI__, $option->content_option->opt_content_static->$language['id_lang']);
						$this->_html .= '
					<div id="opt_content_static_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').'; float: left;">
						<textarea class="rte" name="content_option[2][opt_content_static]['.$language['id_lang'].']" id="opt_content_static'.$language['id_lang'].'" cols="100" rows="20">'.(isset($option->content_option->opt_content_static->$language['id_lang']) ? $option->content_option->opt_content_static->$language['id_lang'] : '').'</textarea>
					</div>';
					}
					$this->_html .= $this->displayFlags($languages, $defaultLanguage, $divLangName, 'opt_content_static', true);
					$this->_html .= '</div></div>';
				$this->_html .= '</div>
				<div class="option_type_3" style="display:none">
				<p>
				<label>'.$this->l('Show Image:').'</label> 
				<input class="opt_show_image_manu" type="radio" name="content_option[3][opt_show_image_manu]" value="1"';
				if(isset($option->content_option->opt_show_image_manu))
						if($option->content_option->opt_show_image_manu == 1)
							$this->_html .= 'checked="checked"';
				$this->_html .= '/>
				<label class="t"><img src="'._PS_ADMIN_IMG_.'enabled.gif" alt="Enabled" title="Enabled" /></label>
				<input class="opt_show_image_manu" type="radio" name="content_option[3][opt_show_image_manu]" value="0"';
				if(isset($option->content_option->opt_show_image_manu))
						if($option->content_option->opt_show_image_manu == 0)
							$this->_html .= 'checked="checked"';
				$this->_html .= '/>
				<label class="t"><img src="'._PS_ADMIN_IMG_.'disabled.gif" alt="Disabled" title="Disabled" /></label></p>';
				$this->_html .= '
				<p>
					<label>'.$this->l('Size Image (W x H):').'</label>
					<select class="opt_img_size" name="content_option[3][opt_image_size_manu]">';
				$imageTypes = ImageType ::getImagesTypes('manufacturers');
				foreach ($imageTypes as $key_image=>$imageType)
				{
					$this->_html .= '<option';
					if(isset($option->content_option->opt_image_size_manu))
					{
						if(strcmp($imageType['name'],$option->content_option->opt_image_size_manu)==0)
							$this->_html .= ' selected';
					}
					$this->_html .= ' value="'.$imageType['name'].'" >'.$imageType['name'].' - '.$imageType['width'].'x'.$imageType['height'].'</option>';
				}
				$this->_html .= '</select></p>
				<p>
				<label>'.$this->l('Show Name:').'</label> 
				<input class="opt_show_name_manu" type="radio" name="content_option[3][opt_show_name_manu]" value="1"';
				if(isset($option->content_option->opt_show_name_manu))
						if($option->content_option->opt_show_name_manu == 1)
							$this->_html .= 'checked="checked"';
				$this->_html .= '/>
				<label class="t"><img src="'._PS_ADMIN_IMG_.'enabled.gif" alt="Enabled" title="Enabled" /></label>
				<input class="opt_show_name_manu" type="radio" name="content_option[3][opt_show_name_manu]" value="0"';
				if(isset($option->content_option->opt_show_name_manu))
						if($option->content_option->opt_show_name_manu == 0)
							$this->_html .= 'checked="checked"';
				$this->_html .= '/>
				<label class="t"><img src="'._PS_ADMIN_IMG_.'disabled.gif" alt="Disabled" title="Disabled" /></label></p>
				<div class="cs_out_content">
				<label>'.$this->l('Choose Manufacturers:').'</label>
				<div class="margin-form">
				<table cellspacing="0" cellpadding="0" class="table" width="100%">
				<tbody>
				<tr class="nodrag nodrop">
				<th><input type="checkbox" name="content_option[3][opt_check-manu]" class="noborder" value=""/></th>
				<th>'.$this->l('ID').'</th>
				<th>'.$this->l('Name').'</th>
				</tr>
				';
				$manu_list = Manufacturer ::getManufacturers(false,0,false);
				static $irow_manu = 0;
				foreach($manu_list as $manu)
				{
					$this->_html .= '
					<tr '.($irow_manu++ % 2 ? 'class="alt_row"' : '').'>
					<td><input type="checkbox"';
					if(isset($option->content_option->opt_list_manu))
					{
						if(in_array($manu['id_manufacturer'], $option->content_option->opt_list_manu))
						$this->_html .= ' checked=checked';
					}
					$this->_html .= ' name="content_option[3][opt_list_manu][]" value="'.$manu['id_manufacturer'].'"/>'.'
					</td>
					<td>'.$manu['id_manufacturer'].'</td>
					<td>'.$manu['name'].'</td>
					</tr>
					';
				}
				$this->_html .= '
				</tbody>
				</table>
				</div>
				</div>
				</div>
				<div class="option_type_4" style="display:none">';
				if(isset($option->content_option->footerBox))
					$arr_checked_cms_update = $option->content_option->footerBox;
				else
					$arr_checked_cms_update = array();
				$this->_displayFormCMS($arr_checked_cms_update);
				$this->_html .='
				</div>
			</div>
			<div class="margin-form"><input type="submit" class="button pointer" name="saveSubmitOptionMenu" value="'.$this->l('Save').'" id="saveSubmitOptionMenu" />
		</div>
		</fieldset></div></form></div>';
	}
	
	
	private function _displayForm()
	{
		global $currentIndex, $cookie;
		// Language 
	 	$defaultLanguage = (int)(Configuration::get('PS_LANG_DEFAULT'));
		$languages = Language::getLanguages(false);
		$divLangName = 'titlediv¤descriptiondiv';
	 	$this->_html .= '<div style="overflow:hidden"><div class="sidebar" style="width:35%">';
		$this->_html .= $this->_displayMenuCategories().'</div><form method="post" action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" enctype="multipart/form-data">
		<div id="moduleContainer">
			<fieldset>
			<legend>'.$this->l('Creat New Menu').'</legend>
				<label>'.$this->l('Title:').'</label>
				<div class="margin-form">';
					foreach ($languages as $language)
					{
						$this->_html .= '
					<div id="titlediv_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').'; float: left;">
						<input type="text" name="title_'.$language['id_lang'].'" value="'.Tools::getValue('title_'.$language['id_lang']).'" size="55" /><sup> *</sup>
					</div>';
					}
					$this->_html .= $this->displayFlags($languages, $defaultLanguage, $divLangName, 'titlediv', true);	
					$this->_html .= '
					<div class="clear"></div>
				</div>
				<label>'.$this->l('Link of title:').'</label>
				<div class="margin-form">
					<input type="text" name="link_of_title" value="'.Tools::getValue('link_of_title').'" size="55" />
				</div>
				<label style="display:none">'.$this->l('Description:').'</label>
				<div class="margin-form" style="display:none">';
					foreach ($languages as $language)
					{
						$this->_html .= '
					<div id="descriptiondiv_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').'; float: left;">
						<input type="text" name="description_'.$language['id_lang'].'" value="'.Tools::getValue('description_'.$language['id_lang']).'" size="55" />
					</div>';
					}
					$this->_html .= $this->displayFlags($languages, $defaultLanguage, $divLangName, 'descriptiondiv', true);	
					$this->_html .= '
					<div class="clear"></div>
				</div>
				<label>'.$this->l('Width:').'</label>
				<div class="margin-form">
					<input type="text" name="width" onkeypress="return isNumberKey(event)" value="'.Tools::getValue('width').'" size="55" />
				</div>
				<label>'.$this->l('Number of column:').'</label>
				<div class="margin-form">
					<input type="text" name="number_column" onkeypress="return isNumberKey(event)" value="'.Tools::getValue('number_column').'" size="55" />
				</div>
				<label>'.$this->l('Class:').'</label>
				<div class="margin-form">
					<input type="text" name="classes" value="'.Tools::getValue('classes').'" size="55" />
				</div>
				<label>'.$this->l('Displayed:').'</label>
				<div class="margin-form">
					<div id="activediv" style="float: left;">
						<input type="radio" class="activediv" name="display" value="1"'.(Tools::getValue('display',1) ? 'checked="checked"' : '').' />
						<label class="t"><img src="'._PS_ADMIN_IMG_.'enabled.gif" alt="Enabled" title="Enabled" /></label>
						<input type="radio" class="activediv" name="display" value="0"'.(Tools::getValue('display',1) ? '' : 'checked="checked"').' />
						<label class="t"><img src="'._PS_ADMIN_IMG_.'disabled.gif" alt="Disabled" title="Disabled" /></label>
					</div>
					<div class="clear"></div>
				</div>
				<label>'.$this->l('Icon:').'</label>
				<div class="margin-form">
					<input type="file" name="icon" value="'.Tools::getValue('icon').'" size="55" />
				</div>
				<label>'.$this->l('Displayed Icon:').'</label>
				<div class="margin-form">
					<div id="activedivicon" style="float: left;">
						<input type="radio" class="activedivicon" name="display_icon" value="1"'.(Tools::getValue('display_icon',1) ? 'checked="checked"' : '').' />
						<label class="t"><img src="'._PS_ADMIN_IMG_.'enabled.gif" alt="Enabled" title="Enabled" /></label>
						<input type="radio" class="activedivicon" name="display_icon" value="0"'.(Tools::getValue('display_icon',1) ? '' : 'checked="checked"').' />
						<label class="t"><img src="'._PS_ADMIN_IMG_.'disabled.gif" alt="Disabled" title="Disabled" /></label>
					</div>
					<div class="clear"></div>
				</div>
				';
					$this->_html .='
				<div class="margin-form"><input type="submit"  onclick="return ajaxAddMenu();" class="button pointer" name="saveMenu" value="'.$this->l('Save Menu').'" id="saveMenu" />';
					$this->_html .= '					
				</div>
			</fieldset>
		</div>
		</form>
		</div>';
	}
	
	
	public function _displayUpdateForm($id_menu) //used
	{
		global $currentIndex, $cookie;
		if (!$id_menu)
		{
			$this->_html = '<a href="'.$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'"><img src="'._PS_ADMIN_IMG_.'arrow2.gif" alt="" />'.$this->l('Back to list').'</a>';
			return;
		}
	 	// Language 
	 	$defaultLanguage = (int)(Configuration::get('PS_LANG_DEFAULT'));
		$languages = Language::getLanguages(false);
		$divLangName = 'titlediv¤descriptiondiv';
		$menu = new CsMegaMenuClass((int)$id_menu);
		// Form
		$string_image = _PS_BASE_URL_._MODULE_DIR_.'csmegamenu/img/icon/no_image.jpg';
		$this->_html .= '
		<script type="text/javascript">
			$(document).ready(function(){
				$("#img_delete_icon").click(function(){
					$("input[name=\'icon_hidden\']").val(\'\');
					$("#show_image_icon").attr("src","'.$string_image.'");
				});
			})
		</script>
		<fieldset>
			<legend><img src="'.$this->_path.'logo.gif" alt="" title="" /> '.$this->l('Update menu ').$menu->title[$defaultLanguage].'</legend>
				<input type="hidden" value="'.$menu->id_menu.'" name="id_menu"/>
				<label>'.$this->l('Title:').'</label>
				<div class="margin-form">';
					foreach ($languages as $language)
					{
						$this->_html .= '
					<div id="titlediv_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').'; float: left;">
						<input type="text" name="title_'.$language['id_lang'].'" value="'.(isset($menu->title[$language['id_lang']]) ? $menu->title[$language['id_lang']] : '').'" size="55" /><sup> *</sup>
					</div>';
					}
					$this->_html .= $this->displayFlags($languages, $defaultLanguage, $divLangName, 'titlediv', true);	
					$this->_html .= '
					<div class="clear"></div>
				</div>
				<label>'.$this->l('Link of title:').'</label>
				<div class="margin-form">
					<input type="text" name="link_of_title" value="'.$menu->link_of_title.'" size="55" />
				</div>
				<label style="display:none">'.$this->l('Description:').'</label>
				<div class="margin-form" style="display:none">';
					foreach ($languages as $language)
					{
						$this->_html .= '
					<div id="descriptiondiv_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').'; float: left;">
						<input type="text" name="description_'.$language['id_lang'].'" value="'.(isset($menu->description[$language['id_lang']]) ? $menu->description[$language['id_lang']] : '').'" size="55" />
					</div>';
					}
					$this->_html .= $this->displayFlags($languages, $defaultLanguage, $divLangName, 'descriptiondiv', true);	
					$this->_html .= '
					<div class="clear"></div>
				</div>
				<label>'.$this->l('Number of column:').'</label>
				<div class="margin-form">
					<input type="text" onkeypress="return isNumberKey(event)" name="number_column" value="'.$menu->number_column.'" size="55" />
				</div>
				<label>'.$this->l('Width:').'</label>
				<div class="margin-form">
					<input type="text" onkeypress="return isNumberKey(event)" name="width" value="'.$menu->width.'" size="55" />
				</div>
				<label>'.$this->l('Class:').'</label>
				<div class="margin-form">
					<input type="text" name="classes" value="'.$menu->classes.'" size="55" />
				</div>
				<label>'.$this->l('Displayed:').'</label>
				<div class="margin-form">
					<div id="activediv" style="float: left;">
						<input type="radio" class="activediv" name="display" value="1"'.($menu->display ? 'checked="checked"' : '').' />
						<label class="t"><img src="'._PS_ADMIN_IMG_.'enabled.gif" alt="Enabled" title="Enabled" /></label>
						<input type="radio" class="activediv" name="display" value="0"'.($menu->display ? '' : 'checked="checked"').'/>
						<label class="t"><img src="'._PS_ADMIN_IMG_.'disabled.gif" alt="Disabled" title="Disabled" /></label>
					</div>
					<div class="clear"></div>
				</div>
				<label>'.$this->l('Icon:').'</label>
				<div class="margin-form">
					<input type="hidden" name="icon_hidden" value="'.$menu->icon.'" size="55" />
					<input type="file" name="icon" value="'.$menu->icon.'" size="55" />
				</div><span style="float:left;margin-left:540px;margin-top:-41px">
				'.($menu->icon ? '<img id="show_image_icon" style="width:30px;height:30px" src="'._PS_BASE_URL_._MODULE_DIR_.'csmegamenu/img/icon/'.$menu->icon.'"/><img style="cursor:pointer;" id="img_delete_icon" src="'._PS_ADMIN_IMG_.'delete.gif" alt="'.$this->l('Delete icon').'" title="'.$this->l('Delete icon').'" />' : '' ).'</span>
				<label>'.$this->l('Displayed Icon:').'</label>
				<div class="margin-form">
					<div id="activedivicon" style="float: left;">
						<input type="radio" class="activedivicon" name="display_icon" value="1"'.($menu->display_icon ? 'checked="checked"' : '').' />
						<label class="t"><img src="'._PS_ADMIN_IMG_.'enabled.gif" alt="Enabled" title="Enabled" /></label>
						<input type="radio" class="activedivicon" name="display_icon" value="0"'.($menu->display_icon ? '' : 'checked="checked"').'/>
						<label class="t"><img src="'._PS_ADMIN_IMG_.'disabled.gif" alt="Disabled" title="Disabled" /></label>
					</div>
					<div class="clear"></div>
				</div>
				';
					$this->_html .='
				<div class="margin-form"><input type="submit" class="button pointer" name="saveMenu" value="'.$this->l('Save Menu').'" id="saveMenu" />';
					$this->_html .= '					
				</div>
		</fieldset>';
		return $this->_html;
	}
	
	private function _postProcess()
	{
		global $currentIndex;
		$errors = array();
		if (Tools::isSubmit('sortpostition'))
		{
			$positions = Tools::getValue('menu');
			if (is_array($positions))
			{
				$menus = array();
				$i = 0;
				foreach ($positions as $pos)
				{
					if(is_numeric($pos))
					{
						$menus[$i] = $pos;
						$i++;
					}
				}
				$CsMegaMenuClass = new CsMegaMenuClass();
				$CsMegaMenuClass->cleanSortPositions($menus);
				$this->_clearCache('csmegamenu.tpl');
				echo $this->_displayMenuCategories();die;
			}
		}
		else if (Tools::isSubmit('sortpostitionoption'))
		{
			
			$optForMenu = Tools::getValue('table_name');
			$positions = Tools::getValue(''.$optForMenu.'');
			$options = array();
			$i = 0;
			foreach($positions as $pos)
			{
				$temp = explode("_", $pos);
				$options[$i] = $temp[1];
				$i++;
			}
			$CsMegaMenuOptionClass = new CsMegaMenuOptionClass();
			$CsMegaMenuOptionClass->cleanSortPositions($options);
			$this->_clearCache('csmegamenu.tpl');
			echo $this->_displayMenuCategories();
			die;
		}
		elseif (Tools::isSubmit('saveMenu'))
		{
			$mg_menu = new CsMegaMenuClass(Tools::getValue('id_menu'));
			$mg_menu->copyFromPost();
			$errors = $mg_menu->validateController();
			if (sizeof($errors))
			{
				$this->_html .= $this->displayError(implode('<br />', $errors));
			}
			else
			{
				Tools::getValue('id_menu') ? $mg_menu->update() : $mg_menu->add();
				$this->_clearCache('csmegamenu.tpl');
				Tools::redirectAdmin($currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&saveMenuConfirmation');
			}
		}
		elseif (Tools::isSubmit('saveSubmitOptionMenu'))
		{
			$mg_menu_option = new CsMegaMenuOptionClass(Tools::getValue('id_option'));
			$errors =  $mg_menu_option->validateController();
			if (isset($errors) && $errors != '')
			{
				$this->_html .= $this->displayError($errors);
			}
			else
			{
				$mg_menu_option->copyFromPostOption();
				Tools::getValue('id_option') ? $mg_menu_option->update() : $mg_menu_option->add();
				$this->_clearCache('csmegamenu.tpl');
				Tools::redirectAdmin($currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&saveMenuOptionConfirmation&id_menu='.$mg_menu_option->id_menu);
			}
		}
		elseif (Tools::isSubmit('applyOptions'))
		{
			if ($error = $this->saveXmlOption())
				$this->_html .= $error;
			else
			{
				$this->_clearCache('csmegamenu.tpl');
				Tools::redirectAdmin($currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules'));
			}
		}
		elseif (Tools::isSubmit('resetOptions'))
		{
			if ($error = $this->saveXmlOption(true))
				$this->_html .= $error;
			else
			{
				$this->_clearCache('csmegamenu.tpl');
				Tools::redirectAdmin($currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules'));
			}
		}
		elseif (Tools::isSubmit('changeStatusMenu') AND Tools::getValue('id_menu'))
		{
			$mg_menu = new CsMegaMenuClass(Tools::getValue('id_menu'));
			$mg_menu->updateStatus(Tools::getValue('status'));
			$this->_clearCache('csmegamenu.tpl');
			Tools::redirectAdmin($currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules'));
		}
		elseif (Tools::isSubmit('deleteMenu') AND Tools::getValue('id_menu'))
		{
			$mg_menu = new CsMegaMenuClass(Tools::getValue('id_menu'));
			$options = $mg_menu->getOptionForMenu();
			if(!empty($options))
			{
				foreach ($options as $option)
				{
					$mg_menu_option = new CsMegaMenuOptionClass($option['id_option']);
					$mg_menu_option->delete();
					$mg_menu_option->cleanPositionsOption();
				}
			}
			$mg_menu->delete();
			$mg_menu->cleanPositions();
			$this->_clearCache('csmegamenu.tpl');
			Tools::redirectAdmin($currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&deleteMenuConfirmation');
		}
		elseif (Tools::isSubmit('submitDeleteMenus'))
		{
			if(empty($_POST['checkMenuItem']))
			{
				$this->_html = $this->displayError($this->l('You must select at least one element to delete.'));
				return;
			}
			foreach($_POST['checkMenuItem'] as $IDMenu)
			{
				$mg_menu = new CsMegaMenuClass($IDMenu);
				$options = $mg_menu->getOptionForMenu();
				if(!empty($options))
				{
					foreach ($options as $option)
					{
						$mg_menu_option = new CsMegaMenuOptionClass($option['id_option']);
						$mg_menu_option->delete();
						$mg_menu_option->cleanPositionsOption();
					}
				}
				$mg_menu->delete();
				$mg_menu->cleanPositions();
				$this->_clearCache('csmegamenu.tpl');
			}
			Tools::redirectAdmin($currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&deleteMenuConfirmation');
		}
		elseif (Tools::isSubmit('orderMenu') AND Validate::isInt(Tools::getValue('id_menu')) AND Validate::isInt(Tools::getValue('position')))
		{
			$mg_menu = new CsMegaMenuClass(Tools::getValue('id_menu'));
			$mg_menu->updatePosition(Tools::getValue('way'),Tools::getValue('position'));
			$this->_clearCache('csmegamenu.tpl');
			Tools::redirectAdmin($currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&changePositionConfirmation');
		}
		elseif (Tools::isSubmit('orderOptionMenu') AND Validate::isInt(Tools::getValue('id_option')) AND Validate::isInt(Tools::getValue('position_option')))
		{
			$mg_menu_option = new CsMegaMenuOptionClass(Tools::getValue('id_option'));
			$mg_menu_option->updatePositionOption(Tools::getValue('way'),Tools::getValue('position_option'));
			$this->_clearCache('csmegamenu.tpl');
			Tools::redirectAdmin($currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&changPositionOptionMenuConfirmation&id_menu='.Tools::getValue('id_menu').'');
		}
		elseif (Tools::isSubmit('deleteOptionMenu') AND Tools::getValue('id_option'))
		{
			$mg_menu_option = new CsMegaMenuOptionClass(Tools::getValue('id_option'));
			$mg_menu_option->delete();
			$mg_menu_option->cleanPositionsOption();
			$this->_clearCache('csmegamenu.tpl');
			Tools::redirectAdmin($currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&deleteOptionMenuConfirmation&id_menu='.$mg_menu_option->id_menu.'');
		}
		elseif (Tools::isSubmit('saveMenuConfirmation'))
			$this->_html .= $this->displayConfirmation($this->l('Menu has been added successfully'));
		elseif (Tools::isSubmit('deleteOptionMenuConfirmation'))
		{
			$this->_html .= '
			<script type="text/javascript">
			$(document).ready(function() {
				$("#option_menu_'.Tools::getValue('id_menu').'").show();
				$("#image_more_options'.Tools::getValue('id_menu').'").attr(\'src\', \'../img/admin/less.png\');
				$("#categorieWidth'.Tools::getValue('id_menu').'").css("font-weight","bold");
			});</script>';
			$this->_html .= $this->displayConfirmation($this->l('Option has been deleted successfully'));
		}
		elseif (Tools::isSubmit('changPositionOptionMenuConfirmation'))
		{
			$this->_html .= '
			<script type="text/javascript">
			$(document).ready(function() {
				$("#option_menu_'.Tools::getValue('id_menu').'").show();
				$("#image_more_options'.Tools::getValue('id_menu').'").attr(\'src\', \'../img/admin/less.png\');
			});</script>';
			$this->_html .= $this->displayConfirmation($this->l('Change position option has been deleted successfully'));
		}
		elseif (Tools::isSubmit('saveMenuOptionConfirmation'))
		{
			$this->_html .= '
				<script type="text/javascript">
				$(document).ready(function() {
					$("#option_menu_'.Tools::getValue('id_menu').'").show();
					$("#image_more_options'.Tools::getValue('id_menu').'").attr(\'src\', \'../img/admin/less.png\');
					$("#image_more_options'.Tools::getValue('id_menu').'").attr(\'src\', \'../img/admin/less.png\');
					$("#categorieWidth'.Tools::getValue('id_menu').'").css("font-weight","bold");
				});</script>';
			$this->_html .= $this->displayConfirmation($this->l('Option of menu has been saved successfully'));
		}
		elseif (Tools::isSubmit('changePositionConfirmation'))
		{
		$this->_html .= $this->displayConfirmation($this->l('Change position successfully'));
		}
	}
	
	private function _displayOptions()
	{
		$option = simplexml_load_file(dirname(__FILE__).'/'.'option.xml');
		$this->_html .= '
		<br/>
	 	<fieldset>
			<legend><img src="'.$this->_path.'logo.gif" alt="" title="" /> '.$this->l('Megamenu Options').'</legend>
			<form method="post" action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" enctype="multipart/form-data">
			
			<label>'.$this->l('Add More Item for Ipad horizontal:').'</label>
			<div class="margin-form">
				<input type="text" name="moreipadh" value="'.($option->moreipadh ? $option->moreipadh : 0).'"/>
				<p class="clear">'.$this->l('Add More-item after the xth menu item. Ex: 6,7,8... If =0,will not add More-item').'</p>
				<div class="clear"></div>
			</div>
			<label>'.$this->l('Add More Item for Ipad vertical:').'</label>
			<div class="margin-form">
				<input type="text" name="moreipadv" value="'.($option->moreipadv ? $option->moreipadv : 0).'"/>
				<p class="clear">'.$this->l('Add More-item after the xth menu item. Ex: 5,6,7... If =0,will not add More-item').'</p>
				<div class="clear"></div>
			</div>
			<div class="margin-form">';
				$this->_html .= '
				<input type="submit" class="button" name="applyOptions" value="'.$this->l('Apply').'" id="applyOptions" />
				<input type="submit" class="button" name="resetOptions" value="'.$this->l('Reset').'" id="resetOptions" />';
				$this->_html .= '					
			</div>';
		$this->_html .= '
			</form>
		</fieldset>';
	}
	
	private function saveXmlOption($reset = false)
	{
		$error = false;
		$newXml = '<?xml version=\'1.0\' encoding=\'utf-8\' ?>'."\n".'<options>'."\n";
		
		$newXml .= '<moreipadh>';
		$newXml .= ($reset ? 0 : Tools::getValue('moreipadh'));
		$newXml .= '</moreipadh>'."\n";
		
		$newXml .= '<moreipadv>';
		$newXml .= ($reset ? 0 : Tools::getValue('moreipadv'));
		$newXml .= '</moreipadv>'."\n";
		
		$newXml .= '</options>'."\n";
		if ($fd = @fopen(dirname(__FILE__).'/'.'option.xml', 'w'))
		{
			if (!@fwrite($fd, $newXml))
				$error = $this->displayError($this->l('Unable to write to the editor file.'));
			if (!@fclose($fd))
				$error = $this->displayError($this->l('Can\'t close the editor file.'));
		}
		else
			$error = $this->displayError($this->l('Unable to update the editor file. Please check the editor file\'s writing permissions.'));
		return $error;
	}
	
	public function getContent()
   	{
		global $currentIndex;
		$this->context->controller->addCss($this->_path.'css/csmegamenu_admin.css', 'all');//css for admin form
		$this->_html .= '
		<link href="'._PS_JS_DIR_.'jquery/plugins/autocomplete/jquery.autocomplete.css" rel="stylesheet" type="text/css" media="all" />
		<script type="text/javascript" src="'._PS_JS_DIR_.'jquery/plugins/autocomplete/jquery.autocomplete.js"></script>
		<script type="text/javascript" src="'.$this->_path.'js/csmegamenu.js"></script>
		<script type="text/javascript" src="'._PS_JS_DIR_.'jquery/plugins/jquery.tablednd.js"></script>	
		
		<script type="text/javascript">
            var come_from = "menu";
            var token = "'.Tools::getAdminTokenLite('AdminModules').'";
			var __PS_BASE_URI__ = "'.__PS_BASE_URI__.'";
            var alternate = 1;
			var currentIndex = "'.$currentIndex.'";
			var name = "'.$this->name.'";
        </script>
		<script type="text/javascript">
					function ajaxLoadMenu(id_menu)
					{
						$.ajax({
						type: \'post\',
						url: \''.__PS_BASE_URI__.'\' + \'modules/csmegamenu/csmegamenu_ajax.php\',
						data: \'&id_menu=\' + id_menu,
						success: function(result) {
							$("#moduleContainer").html(result);
						}
						});
					return false;
					};
		</script>
		<div class="toolbar-placeholder">
			<div class="toolbarBox toolbarHead">
				<ul style="float:right;width:180px;">
				<li id="btnAddMenu">
					<a class="toolbar_btn" href="'.$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'"><span class="process-icon-new "></span>
					<div>'.$this->l('Add new Menu').'</div></a>
				</li>
				<li>
					<a class="toolbar_btn" href="'.$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&addOptionMenu'.'"><span class="process-icon-new "></span>
					<div>'.$this->l('Add new Option').'</div></a>
				</li>
				</ul>
				<div class="pageTitle">
					<h3><span id="current_obj" style="font-weight: normal;"><span class="breadcrumb item-0">'.$this->displayName.'</span></h3>
				</div>
			</div>
			
		</div>';
		$this->_postProcess();
		if (Tools::isSubmit('addOptionMenu'))
		$this->_displayAddOptionForm();
		elseif (Tools::isSubmit('editOptionMenu'))
		{
			$type_option = Tools::getValue('type_option');
			$this->_html.='
				<script type="text/javascript">
			$(document).ready(function() {
				$(".option_type_'.$type_option.'").show();
			});</script>
			<script type="text/javascript">
				$(document).ready(function() {
					$("#option_menu_'.Tools::getValue('id_menu').'").show();
					$("#image_more_options'.Tools::getValue('id_menu').'").attr(\'src\', \'../img/admin/less.png\');
					$("#option_menu_'.Tools::getValue('id_menu').' #option'.Tools::getValue('id_menu').'_'.Tools::getValue('id_option').'").css("font-weight","bold");
					$("#categorieWidth'.Tools::getValue('id_menu').'").css("font-weight","bold");
				});</script>
			';
			$this->_displayUpdateOptionForm();
		}
		else
			$this->_displayForm();
		
		$this->_displayOptions();
		return $this->_html;
	}
	
	private function getMenus($active = null) //case in : allshop show shop default
	{
		$this->context = Context::getContext();
		$id_lang = $this->context->language->id;
		$id_shop = $this->context->shop->id;
	 	if (!$result = Db::getInstance()->ExecuteS(
			'SELECT ms.*, ml.`title`,ml.`description`,m.`icon`
			FROM `'._DB_PREFIX_.'csmegamenu` m
			LEFT JOIN `'._DB_PREFIX_.'csmegamenu_shop` ms ON (m.`id_menu` = ms.`id_menu` )
			LEFT JOIN `'._DB_PREFIX_.'csmegamenu_lang` ml ON (m.`id_menu` = ml.`id_menu` '.( $id_shop ? 'AND ml.`id_shop` = '.$id_shop : ' ' ).') 
			WHERE ml.id_lang = '.(int)$id_lang.
			($active ? ' AND ms.`display` = 1' : ' ').
			( $id_shop ? 'AND ms.`id_shop` = '.$id_shop : ' ' ).'
			ORDER BY ms.`position` ASC'))
	 		return false;
	 	return $result;
	}
	
	
	/*For front end*/
	public function getTree($resultParents, $resultIds, $maxDepth, $id_category = null, $currentDepth = 0)
	{
		if (is_null($id_category))
			$id_category = $this->context->shop->getCategory();

		$children = array();
		if (isset($resultParents[$id_category]) && count($resultParents[$id_category]) && ($maxDepth == 0 || $currentDepth < $maxDepth))
			foreach ($resultParents[$id_category] as $subcat)
				$children[] = $this->getTree($resultParents, $resultIds, $maxDepth, $subcat['id_category'], $currentDepth + 1);
		if (!isset($resultIds[$id_category]))
			return false;
		$return = array('id' => $id_category, 'link' => $this->context->link->getCategoryLink($id_category, $resultIds[$id_category]['link_rewrite']),
					 'name' => $resultIds[$id_category]['name'], 'desc'=> $resultIds[$id_category]['description'],
					 'children' => $children);
		return $return;
	}
	public function getAllSubCategory ($id_parent)
	{
		global $cookie;
		$this->context = Context::getContext();
		$id_shop = $this->context->shop->id;
		$id_customer = intval($cookie->id_customer);
		$maxdepth = 5;
		$row = Db::getInstance()->getRow('
		SELECT `level_depth`
		FROM '._DB_PREFIX_.'category c
		WHERE c.`id_category` = '.intval($id_parent));
		$maxdepth = $maxdepth + $row['level_depth'];
		if (!$result = Db::getInstance()->ExecuteS('
		SELECT DISTINCT c.*, cl.*
		FROM `'._DB_PREFIX_.'category` c 
		LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (c.`id_category` = cl.`id_category` AND `id_lang` = '.(int)Context::getContext()->language->id.')
		LEFT JOIN `'._DB_PREFIX_.'category_group` cg ON (cg.`id_category` = c.`id_category`)
		WHERE c.`active` = 1 AND cl.id_shop = '.$id_shop
		.(intval($maxdepth) != 0 ? ' AND `level_depth` <= 2'./*intval($maxdepth)*/'' : '').'
		AND cg.`id_group` '.(!$cookie->id_customer ?  '= 1' : 'IN (SELECT id_group FROM '._DB_PREFIX_.'customer_group WHERE id_customer = '.intval($cookie->id_customer).')').'
		ORDER BY `level_depth` ASC'))
			return;
		$resultParents = array();
		$resultIds = array();

		foreach ($result as $row)
		{
			$resultParents[$row['id_parent']][] = $row;
			$resultIds[$row['id_category']] = $row;
		}
		
		$blockCategTree = $this->getTree($resultParents, $resultIds, $maxdepth,$id_parent);
		return $blockCategTree;
	}
	public static function getCMStitles($cmsCategories)
	{
		global $cookie;
		$content = array();
		$link = new Link();
		$id_cms_temp = 0;
		foreach ($cmsCategories AS $key=>$cmsCategory)
		{
			$ids = explode('_', $cmsCategory);
			$cms = new CMS((int)$ids[1], (int)$cookie->id_lang);
			if (Validate::isLoadedObject($cms) && $cms->active == 1)
			{
				if ($ids[0] == 1)
				{
					$query = Db::getInstance()->getRow('
					SELECT cl.`name`, cl.`link_rewrite`
					FROM `'._DB_PREFIX_.'cms_category_lang` cl
					INNER JOIN `'._DB_PREFIX_.'cms_category` c ON (cl.`id_cms_category` = c.`id_cms_category`)
					WHERE cl.`id_cms_category` = '.(int)$ids[1].' AND (c.`active` = 1 OR c.`id_cms_category` = 1)
					AND cl.`id_lang` = '.(int)$cookie->id_lang);
					$content[$id_cms_temp]['link'] = $link->getCMSCategoryLink((int)$ids[1], $query['link_rewrite']);
					$content[$id_cms_temp]['meta_title'] = $query['name'];
				}
				elseif (!$ids[0])
				{
					$query = Db::getInstance()->getRow('
					SELECT cl.`meta_title`, cl.`link_rewrite` 
					FROM `'._DB_PREFIX_.'cms_lang` cl
					INNER JOIN `'._DB_PREFIX_.'cms` c ON (cl.`id_cms` = c.`id_cms`)
					WHERE cl.`id_cms` = '.(int)$ids[1].' AND c.`active` = 1
					AND cl.`id_lang` = '.(int)$cookie->id_lang);
					
					$content[$id_cms_temp]['link'] = $link->getCMSLink((int)$ids[1], $query['link_rewrite']);
					$content[$id_cms_temp]['meta_title'] = $query['meta_title'];
				}
				$id_cms_temp++;
			}
		}
		return $content;
	}
	
	private function getMenuDisplay()
	{
		global $cookie;
		$menus = array();
		$this->context = Context::getContext();
		$id_shop = $this->context->shop->id;
		$results = Db::getInstance()->ExecuteS(
					'SELECT ms.`id_menu` FROM `'._DB_PREFIX_.'csmegamenu_shop` ms
					LEFT JOIN `'._DB_PREFIX_.'csmegamenu` m ON (ms.id_menu = m.id_menu)
					WHERE (ms.id_shop = '.(int)$id_shop.')
					AND ms.`display` = 1 ORDER BY ms.`position` ASC
				');
		foreach ($results as $row)
		{
			$menus[$row['id_menu']] = new CsMegaMenuClass($row['id_menu']);
			if($menus[$row['id_menu']]->number_column !=0)
				$menus[$row['id_menu']]->width_item = $menus[$row['id_menu']]->width/($menus[$row['id_menu']]->number_column!= 0 ? $menus[$row['id_menu']]->number_column : 1);
			else
				$menus[$row['id_menu']]->width_item = $menus[$row['id_menu']]->width;
			$menus[$row['id_menu']]->options = $menus[$row['id_menu']]->getOptionForMenu();
			if($menus[$row['id_menu']]->options)
			{
				foreach ($menus[$row['id_menu']]->options as $key=>$option)
				{
					
					if($option['type_option'] == 2) //check option static block
					{
						$option['content_option'] = json_decode(htmlspecialchars_decode(($option['content_option'])));
						$languages = Language::getLanguages(false);
						foreach ($languages AS $language)
						{
							if(isset($option['content_option']->opt_content_static->$language['id_lang']))
							$option['content_option']->opt_content_static->$language['id_lang'] = str_replace($this->temp_url, _PS_BASE_URL_.__PS_BASE_URI__, $option['content_option']->opt_content_static->$language['id_lang']);
						}
					}
					else
						$option['content_option'] = json_decode($option['content_option']);
					$option['width'] = $menus[$row['id_menu']]->width_item * $option['content_option']->opt_fill_column;
					//get content for type option
					if($option['type_option'] == 0)//option 0 : type category
					{
						//if($option['content_option']->opt_show_parent_cat == 1)
						//{
							$option['category_parent']= new Category($option['content_option']->opt_id_parent_cat,(int)Context::getContext()->language->id);
						//}
						$option['sub_category'] = $this->getAllSubCategory($option['content_option']->opt_id_parent_cat);
					}
					if($option['type_option'] == 1) //option 1 : type product
					{
						$stringIdProducts = $option['content_option']->input_hidden_id;
						$arrayIdProducts = explode('-',$stringIdProducts);
						
						$productIds = substr(implode(',',$arrayIdProducts),0,strlen(implode(',',$arrayIdProducts)) - 1);
						$productsImages = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
					SELECT image_shop.id_image, p.id_product, il.legend, product_shop.active, pl.name, pl.description_short, pl.link_rewrite, cl.link_rewrite AS category_rewrite, p.show_price,p.available_for_order,p.out_of_stock,p.id_category_default, p.ean13 
					FROM '._DB_PREFIX_.'product p
					LEFT JOIN '._DB_PREFIX_.'product_lang pl ON (pl.id_product = p.id_product'.Shop::addSqlRestrictionOnLang('pl').')
					LEFT JOIN '._DB_PREFIX_.'image i ON (i.id_product = p.id_product AND i.cover = 1)'.
						Shop::addSqlAssociation('image', 'i', false, 'image_shop.cover=1').'
					LEFT JOIN '._DB_PREFIX_.'image_lang il ON (il.id_image = i.id_image)
					'.Shop::addSqlAssociation('product', 'p').'
					LEFT JOIN '._DB_PREFIX_.'category_lang cl ON (cl.id_category = product_shop.id_category_default'.Shop::addSqlRestrictionOnLang('cl').')
					WHERE p.id_product IN ('.$productIds.')
					AND (i.id_image IS NULL OR image_shop.id_shop='.(int)$this->context->shop->id.')
					AND pl.id_lang = '.(int)$this->context->language->id.'
					AND cl.id_lang = '.(int)$this->context->language->id
					);

					$productsImagesArray = array();
					foreach ($productsImages as $pi)
						$productsImagesArray[$pi['id_product']] = $pi;

					$productsViewedObj = Product::getProductsProperties((int)$this->context->language->id, $productsImagesArray);
						$option['product_list'] = $productsViewedObj;
					}
					//option 2: static block available
					if($option['type_option'] == 3) //option 3 : manufacturer
					{
						if(isset($option['content_option']->opt_list_manu))
						{
							$manu_temp = 0;
							foreach($option['content_option']->opt_list_manu as $k_manu=>$id_manu)
							{
								$manuObj = new Manufacturer((int)$id_manu,$this->context->language->id);
								if(Validate::isLoadedObject($manuObj))
								{
									$option['opt_list_manu_info'][$manu_temp] = $manuObj;
									$manu_temp++;
								}
							}
						}
					}
					if($option['type_option'] == 4) //option 4: cms (information)
					{
						if(isset($option['content_option']->footerBox) && $option['content_option']->footerBox)
						$option['cms'] = $this->getCMStitles($option['content_option']->footerBox);
					}
					$menus[$row['id_menu']]->options[$key] = $option;
					
				}
			}
			
		}

		return $menus;
	}
	
	/*-------------------------------------------------------------*/    
           
        public function _getRespCategories($id_category = 1, $id_lang = false, $id_shop = false){
        
            $id_lang = $id_lang ? (int)$id_lang : (int)Context::getContext()->language->id;
            $category = new Category((int)$id_category, (int)$id_lang, (int)$id_shop);

            if (is_null($category->id)){
                return;
            }

            $children = Category::getChildren((int)$id_category, (int)$id_lang, true, (int)$id_shop);
            
            
            $class = "";
            if (isset($children) && count($children) && $category->level_depth > 1){
                $class .= "parent ";
            }

                        
            if ($category->level_depth > 1){
                $cat_link = $category->getLink();
            }else{
                $cat_link = $this->context->link->getPageLink('index');
            }
            
            $is_intersected = array_intersect($category->getGroups(), $this->user_groups);
                                    
            if (!empty($is_intersected)){
                $this->_respMenu .= '<li class="'.$class.'">';
                $this->_respMenu .= '<a href="'.$cat_link.'"><span>'.$category->name.'</span></a>';
            }
            
            if (isset($children) && count($children)){
                
                $this->_respMenu .= '<ul>';
                
                foreach ($children as $child){
                        $this->_getRespCategories((int)$child['id_category'], (int)$id_lang, (int)$child['id_shop']);
                }

                $this->_respMenu .= '</ul>';
                
            }
			 if (!empty($is_intersected)){
				$this->_respMenu .= '</li>';
			}
           
            return $this->_respMenu;
     }
    
    
    
/*-------------------------------------------------------------*/
	
    public function _buildResponsiveMenu(){
 
		
        return $this->_getRespCategories(1, (int)Context::getContext()->language->id, $id_shop = false);
        
    }
  	
	public function hookDisplayHeader()
	{
		global $smarty;
		$smarty->assign(array(
			'CS_MEGA_MENU' => Hook::Exec('csmegamenu')
		));
		$this->context->controller->addCSS(($this->_path).'css/csmegamenu_front.css', 'all');
		$this->context->controller->addJS(($this->_path).'js/csmegamenu_front.js');
		$this->context->controller->addJS(($this->_path).'js/csmegamenu_addmore.js');
	}
	
	function hookCsMegaMenu($params)
	{
		global $smarty, $cookie;

			$option_megamenu = simplexml_load_file(dirname(__FILE__).'/'.'option.xml');
			$menus = '';//$this->getMenuDisplay();
			$responsive_menu = '';//$this->_buildResponsiveMenu();

			$smarty->assign(array(
				'typeOS' => Mobile_Detect::getBrowserOS()['os_platform'],
				'menus' => $menus,
				'ps_manu_img_dir' => _PS_MANU_IMG_DIR_,
				'ps_cat_img_dir' => _PS_CAT_IMG_DIR_,
				'path_icon' => _PS_BASE_URL_._MODULE_DIR_.'csmegamenu/img/icon/',
				'responsive_menu' => 'true',
				'option_megamenu' => $option_megamenu
			));
		//}
		if (version_compare(_PS_VERSION_,'1.5.4','<'))
			Tools::restoreCacheSettings();

		//return $this->display(__FILE__, 'csmegamenu.tpl', $smarty_cache_id);
		return $this->display(__FILE__, 'csmegamenu.tpl');
	}
	public function hookActionObjectCategoryUpdateAfter($params)
	{
		$this->_clearCache('csmegamenu.tpl');
	}
	
	public function hookActionObjectCategoryDeleteAfter($params)
	{
		$this->_clearCache('csmegamenu.tpl');
	}
	
	public function hookActionObjectCmsUpdateAfter($params)
	{
		$this->_clearCache('csmegamenu.tpl');
	}
	
	public function hookActionObjectCmsDeleteAfter($params)
	{
		$this->_clearCache('csmegamenu.tpl');
	}

	public function hookActionObjectManufacturerUpdateAfter($params)
	{
		$this->_clearCache('csmegamenu.tpl');
	}
	
	public function hookActionObjectManufacturerDeleteAfter($params)
	{
		$this->_clearCache('csmegamenu.tpl');
	}
	
	public function hookActionObjectProductUpdateAfter($params)
	{
		$this->_clearCache('csmegamenu.tpl');
	}
	
	public function hookActionObjectProductDeleteAfter($params)
	{
		$this->_clearCache('csmegamenu.tpl');
	}
	
	public function hookCategoryUpdate($params)
	{
		$this->_clearCache('csmegamenu.tpl');
	}
	public function hookActionUpdateQuantity($params)
	{
		$this->_clearCache('csmegamenu.tpl');
	}
	
}

?>
