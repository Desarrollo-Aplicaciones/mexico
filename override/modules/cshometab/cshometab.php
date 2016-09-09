<?php
include_once(dirname(__FILE__).'/TabClass.php');

class CsHomeTab extends Module
{
	private $_html;
	private $product_types = array("featured_products" => "Featured Products","special_products" => "Special Products","topseller_products" => "Top Seller Products","new_products" => "New Products","choose_the_category" => "Choose the Category...");
	
	public function __construct()
	{
	 	$this->name = 'cshometab';
	 	$this->tab = 'MyBlocks';
	 	$this->version = '1.0';
		$this->author = 'CodeSpot';

	 	parent::__construct();

		$this->displayName = $this->l('CS Home Tab');
		$this->description = $this->l('Add Filter Products on the homepage');
		$this->confirmUninstall = $this->l('Are you sure that you want to delete your CSHomeTab?');

	}
	
	public function install()
	{
	 	if (parent::install() == false || !$this->registerHook('displayHeader') || !$this->registerHook('displayHome'))
	 		return false;
		if (!Db::getInstance()->Execute('CREATE TABLE '._DB_PREFIX_.'cshometab (`id_tab` int(10) unsigned NOT NULL AUTO_INCREMENT, `product_type` varchar(255), `position` int(10) unsigned DEFAULT \'0\', `display` tinyint(1) NOT NULL DEFAULT \'1\', PRIMARY KEY (`id_tab`)) ENGINE=InnoDB default CHARSET=utf8'))
	 		return false;
		if (!Db::getInstance()->Execute('CREATE TABLE '._DB_PREFIX_.'cshometab_shop (`id_tab` int(10) unsigned NOT NULL ,`id_shop` int(10) unsigned NOT NULL,`product_type` varchar(255), `position` int(10) unsigned DEFAULT \'0\', `display` tinyint(1) NOT NULL DEFAULT \'1\', PRIMARY KEY (`id_tab`,`id_shop`)) ENGINE=InnoDB default CHARSET=utf8'))
	 		return false;
		if (!Db::getInstance()->Execute('CREATE TABLE '._DB_PREFIX_.'cshometab_lang (`id_tab` int(10) unsigned NOT NULL, `id_lang` int(10) unsigned NOT NULL,`id_shop` int(10) unsigned NOT NULL, `title` varchar(255) NOT NULL DEFAULT \'\', PRIMARY KEY (`id_tab`,`id_lang`,`id_shop`)) ENGINE=InnoDB default CHARSET=utf8'))
	 		return false;
	 	return true;
	}
	
	public function uninstall()
	{
	 	if (parent::uninstall() == false)
	 		return false;
		if (!Db::getInstance()->Execute('DROP TABLE '._DB_PREFIX_.'cshometab') OR !Db::getInstance()->Execute('DROP TABLE '._DB_PREFIX_.'cshometab_shop') OR  !Db::getInstance()->Execute('DROP TABLE '._DB_PREFIX_.'cshometab_lang'))
	 		return false;
		$this->_clearCache('cshometab.tpl');
	 	return true;
	}
	
	private function _displayHelp()
	{
		$this->_html .= '
		<br/>
	 	<fieldset>
			<legend><img src="'.$this->_path.'logo.gif" alt="" title="" /> '.$this->l('CS HomeTab Helper').'</legend>
			<div>
			<h4>Add a new tab</h4>
			<div>You can add a new tab which get product by choose the Category.</div>
			</div>
		</fieldset>';
	}

	public function getContent()
   	{
		$this->_html = '<h2>'.$this->displayName.'</h2>';
		
		$this->_postProcess();
		
		if (Tools::isSubmit('addTab'))
			$this->_displayAddForm();
		elseif (Tools::isSubmit('editTab'))
			$this->_displayUpdateForm();
		else
		{
			$this->_displayForm();
		}
		
		$this->_displayHelp();
		
		return $this->_html;
	}
	
	
	
	private function _postProcess()
	{
		global $currentIndex;
		$errors = array();
		if (Tools::isSubmit('saveTab'))
		{
			$tab = new TabClass(Tools::getValue('id_tab'));
			$tab->copyFromPost();
			
			$errors = $tab->validateController();
						
			if (sizeof($errors))
			{
				$this->_html .= $this->displayError(implode('<br />', $errors));
			}
			else
			{
				Tools::getValue('id_tab') ? $tab->update() : $tab->add();
				$this->_clearCache('cshometab.tpl');
				Tools::redirectAdmin($currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&saveTabConfirmation');
			}
		}
		elseif (Tools::isSubmit('deleteTab') AND Tools::getValue('id_tab'))
		{
			$tab = new TabClass(Tools::getValue('id_tab'));
			$tab->delete();
			$this->_clearCache('cshometab.tpl');
			Tools::redirectAdmin($currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&deleteTabConfirmation');
		}
		elseif (Tools::isSubmit('orderTab') AND Validate::isInt(Tools::getValue('id_tab')) AND Validate::isInt(Tools::getValue('position')))
		{
			$tab = new TabClass(Tools::getValue('id_tab'));
			$tab->updatePosition(Tools::getValue('way'),Tools::getValue('position'));
			$this->_clearCache('cshometab.tpl');
			Tools::redirectAdmin($currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules'));
		}
		elseif (Tools::isSubmit('changeStatusTab') AND Tools::getValue('id_tab'))
		{
			$tab = new TabClass(Tools::getValue('id_tab'));
			$tab->updateStatus(Tools::getValue('status'));
			$this->_clearCache('cshometab.tpl');
			Tools::redirectAdmin($currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules'));
		}
		elseif (Tools::isSubmit('saveTabConfirmation'))
			$this->_html = $this->displayConfirmation($this->l('Tab has been saved successfully'));
		elseif (Tools::isSubmit('deleteTabConfirmation'))
			$this->_html = $this->displayConfirmation($this->l('Tab deleted successfully'));
		elseif (Tools::isSubmit('statusConfirmation'))
			$this->_html = $this->displayConfirmation($this->l('Options updated successfully'));
	}
	
	private function getTabs($active = null) //case in : allshop show shop default
	{
		$this->context = Context::getContext();
		$id_lang = $this->context->language->id;
		$id_shop = $this->context->shop->id;
	 	if (!$result = Db::getInstance()->ExecuteS(
			'SELECT ts.*, tl.`title`
			FROM `'._DB_PREFIX_.'cshometab` t
			LEFT JOIN `'._DB_PREFIX_.'cshometab_shop` ts ON (ts.`id_tab` = t.`id_tab` )
			LEFT JOIN `'._DB_PREFIX_.'cshometab_lang` tl ON (t.`id_tab` = tl.`id_tab` '.( $id_shop ? 'AND tl.`id_shop` = '.$id_shop : ' ' ).') 
			WHERE tl.id_lang = '.(int)$id_lang.
			($active ? ' AND ts.`display` = 1' : ' ').
			( $id_shop ? 'AND ts.`id_shop` = '.$id_shop : ' ' ).'
			ORDER BY ts.`position` ASC'))
	 		return false;
	 	return $result;
	}
	
	private function _displayForm()
	{
		global $currentIndex, $cookie;
		
	 	$this->_html .= '
	 	<fieldset>
			<legend><img src="'.$this->_path.'logo.gif" alt="" title="" /> '.$this->l('Tabs').'</legend>
			<p><a href="'.$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&addTab"><img src="'._PS_ADMIN_IMG_.'add.gif" alt="" /> '.$this->l('Add a new Tab').'</a></p><br/>
			<table width="100%" class="table" cellspacing="0" cellpadding="0">
			<thead>
			<tr class="nodrag nodrop">
				<th>'.$this->l('Id').'</th>
				<th class="center">'.$this->l('Title').'</th>
				<th class="center">'.$this->l('Get Product from').'</th>
				<th class="center">'.$this->l('Displayed').'</th>
				<th class="center">'.$this->l('Position').'</th>
				<th class="right">'.$this->l('Delete').'</th>
			</tr>
			</thead>
			<tbody>';
		$tabs = $this->getTabs(false);
		if (is_array($tabs))
		{
			static $irow;
			$number = 1;
			
			foreach ($tabs as $tab)
			{
				$stringConfirm='onclick="if (!confirm(\'Are you sure that you want to delete item tab id = '.$tab['id_tab'].' ?\')) return false "';
				$this->_html .= '
				<tr class="'.($irow++ % 2 ? 'alt_row' : '').'">
					<td class="pointer" onclick="document.location = \''.$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&editTab&id_tab='.$tab['id_tab'].'\'">'.$tab['id_tab'].'</td>
					<td class="pointer center" onclick="document.location = \''.$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&editTab&id_tab='.$tab['id_tab'].'\'">'.$tab['title'].'</td>
					<td class="pointer center" onclick="document.location = \''.$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&editTab&id_tab='.$tab['id_tab'].'\'">'.$tab['product_type'].'</td>
					<td class="pointer center"> <a href="'.$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&changeStatusTab&id_tab='.$tab['id_tab'].'&status='.$tab['display'].'">'.($tab['display'] ? '<img src="'._PS_ADMIN_IMG_.'enabled.gif" alt="Enabled" title="Enabled" />' : '<img src="'._PS_ADMIN_IMG_.'disabled.gif" alt="Disabled" title="Disabled" />').'</a> </td><td class="pointer center">'.($tab !== end($tabs) ? '<a href="'.$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&orderTab&id_tab='.$tab['id_tab'].'&way=1&position='.($tab['position']+1).'"><img src="'._PS_ADMIN_IMG_.'down.gif" alt="'.$this->l('Down').'" /></a>' : '').($tab !== reset($tabs) ? '<a href="'.$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&orderTab&id_tab='.$tab['id_tab'].'&way=0&position='.($tab['position']-1).'"><img src="'._PS_ADMIN_IMG_.'up.gif" alt="'.$this->l('Up').'" /></a>' : '').'</td><td class="pointer center">
					<a href="'.$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&deleteTab&id_tab='.$tab['id_tab'].'\'" '.$stringConfirm.'><img src="'._PS_ADMIN_IMG_.'delete.gif" alt="Delete" title="Delete" /></a>
					</td>
				</tr>';
				$number++;
			}
		}
		$this->_html .= '
			</tbody>
			</table>
		</fieldset>';
			
		
	}
	
	private function _displayAddForm()
	{
		global $currentIndex, $cookie;
	 	// Language 
	 	$defaultLanguage = (int)(Configuration::get('PS_LANG_DEFAULT'));
		$languages = Language::getLanguages(false);
		$divLangName = 'titlediv';

		// Form
		$this->_html .= '
		<fieldset>
			<legend><img src="'.$this->_path.'logo.gif" alt="" title="" /> '.$this->l('New Tab').'</legend>
			<form method="post" action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" enctype="multipart/form-data">
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
				<div style="display:none">
				<label>'.$this->l('Get Product From:').'</label>
				<div class="margin-form">
					<div id="product_typediv" style="float: left;">
						<select name="product_type" id="product_type">
						<option value="choose_the_category"></option>
						</select>
					</div>
					<div class="clear"></div>
					<script type="text/javascript">
						$("#product_type").bind("change", function() {
							if($(this).attr("value") == "choose_the_category")
								$("#product_type_menu").show("fast"); 
							else
								$("#product_type_menu").hide("slow"); 
						});
					</script>
				</div>
				</div>
				<div class="margin-form" id="product_type_menu" '.'>';
				$helper = new Helper();
				$this->_html .= $helper->renderCategoryTree(null, array(Tools::getValue('product_type_menu')),'product_type_menu', true);
				$this->_html .= '
				</div>
				<label>'.$this->l('Displayed:').'</label>
				<div class="margin-form">
					<div id="activediv" style="float: left;">
						<input type="radio" name="display" value="1"'.(Tools::getValue('display',1) ? 'checked="checked"' : '').' />
						<label class="t"><img src="'._PS_ADMIN_IMG_.'enabled.gif" alt="Enabled" title="Enabled" /></label>
						<input type="radio" name="display" value="0"'.(Tools::getValue('display',1) ? '' : 'checked="checked"').' />
						<label class="t"><img src="'._PS_ADMIN_IMG_.'disabled.gif" alt="Disabled" title="Disabled" /></label>
					</div>
					<div class="clear"></div>
				</div>
				<div class="margin-form">';
					$this->_html .= '<input type="submit" class="button" name="saveTab" value="'.$this->l('Save Tab').'" id="saveTab" />';
					$this->_html .= '					
				</div>
				
			</form>
			<a href="'.$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'"><img src="'._PS_ADMIN_IMG_.'arrow2.gif" alt="" />'.$this->l('Back to list').'</a>
		</fieldset>';
	}
	
	private function _displayUpdateForm()
	{
		global $currentIndex, $cookie;
		$id = (int)Context::getContext()->shop->id;
		$id_shop = $id ? $id: Configuration::get('PS_SHOP_DEFAULT');
		if (!Tools::getValue('id_tab'))
		{
			$this->_html .= '<a href="'.$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'"><img src="'._PS_ADMIN_IMG_.'arrow2.gif" alt="" />'.$this->l('Back to list').'</a>';
			return;
		}
		$tab = new TabClass((int)Tools::getValue('id_tab'));
	 	// Language 
	 	$defaultLanguage = (int)(Configuration::get('PS_LANG_DEFAULT'));
		$languages = Language::getLanguages(false);
		$divLangName = 'titlediv';

		// Form
		$this->_html .= '
		<fieldset>
			<legend><img src="'.$this->_path.'logo.gif" alt="" title="" /> '.$this->l('Edit Tab').'</legend>
			<form method="post" action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" enctype="multipart/form-data">
				<input type="hidden" name="id_tab" value="'.(int)$tab->id_tab.'" id="id_tab" />
				<div class="margin-form">
					<input type="submit" class="button " name="deleteTab" value="'.$this->l('Delete Tab').'" id="deleteTab" onclick="if (!confirm(\'Are you sure that you want to delete this tab?\')) return false "/>
				</div>
				<label>'.$this->l('Title:').'</label>
				<div class="margin-form">';
					foreach ($languages as $language)
					{
						$this->_html .= '
					<div id="titlediv_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').'; float: left;">
						<input type="text" name="title_'.$language['id_lang'].'" value="'.(isset($tab->title[$language['id_lang']]) ? $tab->title[$language['id_lang']] : '').'" size="55" /><sup> *</sup>
					</div>';
					}
					$this->_html .= $this->displayFlags($languages, $defaultLanguage, $divLangName, 'titlediv', true);	
					$this->_html .= '
					<div class="clear"></div>
				</div>
				<div style="display:none">
				<label>'.$this->l('Get Product From:').'</label>
				<div class="margin-form">
					<div id="product_typediv" style="float: left;">
						<select name="product_type" id="product_type">';
					foreach ($this->product_types AS $key => $value){
						$this->_html .= '<option value="'.$key.'"'.(strpos($tab->product_type, $key) !== false ? 'selected="selected"' : '').'>'.$value.'</option>';
					}	
					$this->_html .= '
						</select>
					</div>
					<div class="clear"></div>
				</div>
				</div>
				<div class="margin-form" id="product_type_menu" '.'>';
				$helper = new Helper();
				$this->_html .= $helper->renderCategoryTree(null, array($tab->product_type_menu), 'product_type_menu', true,false, array(), false, true);
				$this->_html .= '
				</div>
				<label>'.$this->l('Displayed:').'</label>
				<div class="margin-form">
					<div id="activediv" style="float: left;">
						<input type="radio" name="display" value="1"'.($tab->display ? 'checked="checked"' : '').' />
						<label class="t"><img src="'._PS_ADMIN_IMG_.'enabled.gif" alt="Enabled" title="Enabled" /></label>
						<input type="radio" name="display" value="0"'.($tab->display ? '' : 'checked="checked"').' />
						<label class="t"><img src="'._PS_ADMIN_IMG_.'disabled.gif" alt="Disabled" title="Disabled" /></label>
					</div>
					<div class="clear"></div>
				</div>
				
				<div class="margin-form">';
					$this->_html .= '<input type="submit" class="button" name="saveTab" value="'.$this->l('Save Tab').'" id="saveTab" />';
					$this->_html .= '					
				</div>
				
			</form>
			<a href="'.$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'"><img src="'._PS_ADMIN_IMG_.'arrow2.gif" alt="" />'.$this->l('Back to list').'</a>
		</fieldset>';
	}
	
	private function getTabsDisplay($nb = 10,$id_shop)
	{
		$tabs = array();
	 	$results = Db::getInstance()->ExecuteS(
					'SELECT ts.`id_tab` FROM `'._DB_PREFIX_.'cshometab_shop` ts
					LEFT JOIN `'._DB_PREFIX_.'cshometab` t ON (ts.id_tab = t.id_tab)
					WHERE (ts.id_shop = '.(int)$id_shop.')
					AND ts.`display` = 1 ORDER BY ts.`position` ASC
				');
		foreach ($results as $key=>$row)
		{
			$temp = new TabClass($row['id_tab']);
			$temp->getProductList($nb);
			$tabs[$key]['category'] = new Category((int)$temp->product_type_menu);
			$tabs[$key]['info'] = $temp;
			
		}
		return $tabs;
	}
	
	public function hookDisplayHeader($params)
	{	
		global $smarty;
		if ($smarty->tpl_vars['page_name']->value == 'index')
		{
			$this->context->controller->addCss($this->_path.'css/cshometab.css');
			$this->context->controller->addJs($this->_path.'jquery-ui-tabs.min.js');
		}
	}
	
	public function hookDisplayHome()
	{
		global $smarty, $cookie;
		$this->context = Context::getContext();
		$id_shop = $this->context->shop->id;
		$tabs = $this->getTabsDisplay(3,$id_shop);
		$smarty->assign(array(
			'tabs' => $tabs
		));
		return $this->display(__FILE__, 'cshometab.tpl');
	}
	
}
?>
