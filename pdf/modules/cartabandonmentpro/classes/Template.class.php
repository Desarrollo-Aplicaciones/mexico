<?php
class Template extends ObjectModel{

	public  $id_template 	= null;
	public  $model	 		= null;
	private $fields		 	= array();
	private $wich_template	= 0;
	private $name			= '';
	public function __construct($id_template = null, $model = null, $wich_template = 0){
		$this->id_template 		= $id_template;
		$this->model	   		= $model;
		$this->wich_template	= $wich_template;
	}

	private function insertTemplate($id_template, $active){
		
	}
	
	public function save($null_values = false, $autodate = true){
		if(!is_null($this->id_template) && $this->id_template > 0){
			$active = TemplateController::isActive($this->id_template);
			$id_template = $this->id_template;
		}
		else{
			$active = 1;
			$id_template = 'NULL';
		}
		$query = "REPLACE INTO "._DB_PREFIX_."cartabandonment_template VALUES (" . pSQL($id_template) . ", " . (int)$this->model->getId() . ", '" . pSQL($this->name) . "', " . Tools::getValue('language') . ", ".pSQL(Tools::getValue('id_shop')).", " . $active . ", 1)";

		if(!Db::getInstance()->Execute($query))
			return false;

		$this->id_template = Db::getInstance()->Insert_ID();
		
		$content = $this->model->getContent();
		
		$this->editContent($content);
		
		$iso = Language::getIsoById(Tools::getValue('language'));
		if(!is_dir('../modules/cartabandonmentpro/mails/' . $iso))
			mkdir('../modules/cartabandonmentpro/mails/' . $iso, 0777);
		$fp = fopen('../modules/cartabandonmentpro/mails/'.$iso.'/'.$this->id_template.'.html', 'w+');
		fwrite($fp, $content);
		fclose($fp);
		
		$content = $this->model->getContentEdit($this->wich_template);
		$this->editContent($content, false);
		
		$fp = fopen('../modules/cartabandonmentpro/tpls/'.$this->id_template.'.html', 'w+');
		fwrite($fp, $content);
		fclose($fp);

		return $this->id_template;
	}
	
	// This function edits the newsletter
	// left column, right column, center column and the colors
	private function editContent(&$content, $save = true){
		$this->editLeftColumn($content, $save);
		$this->editRightColumn($content, $save);
		$this->editCenter($content, $save);
		$this->editColors($content, $save);
		$context = Context::getContext();
		$logo = $context->shop->getBaseUrl().'img/'.Configuration::get('PS_LOGO');
		$content = str_replace('%logo%', $logo, $content);
	}
	
	// Replace all content in left column
	private function editLeftColumn(&$content, $save = true){
		if(!$this->model->getLeftColumn())
			return false;
		for($nb = 1 ; $nb <= $this->model->getTxtsLeft() ; $nb++){
			$content = str_replace('%left_'.$nb.'%', Tools::getValue('left_'.$this->model->getId().'_'.$nb.'_'.$this->wich_template), $content);
			if($save)
				$this->saveColumn('left', $nb, Tools::getValue('left_'.$this->model->getId().'_'.$nb.'_'.$this->wich_template));
		}
	}
	
	// Replace all content in right column
	private function editRightColumn(&$content, $save = true){
		if(!$this->model->getRightColumn())
			return false;
		for($nb = 1 ; $nb <= $this->model->getTxtsRight() ; $nb++){
			$content = str_replace('%right_'.$nb.'%', Tools::getValue('right_'.$this->model->getId().'_'.$nb.'_'.$this->wich_template), $content);
			if($save)
				$this->saveColumn('right', $nb, Tools::getValue('right_'.$this->model->getId().'_'.$nb.'_'.$this->wich_template));
		}
	}
	
	// Replace all content in center column
	private function editCenter(&$content, $save = true){
		for($nb = 1 ; $nb <= $this->model->getTxtsCenter() ; $nb++){
			$content = str_replace('%center_'.$nb.'%', Tools::getValue('center_'.$this->model->getId().'_'.$nb.'_'.$this->wich_template), $content);
			if($save)
				$this->saveColumn('center', $nb, Tools::getValue('center_'.$nb.'_'.$this->wich_template));
		}
	}
	
	// Replace all colors
	private function editColors(&$content, $save = true){
		for($nb = 1 ; $nb <= $this->model->getColors() ; $nb++){
			$content = str_replace('%color_'.$nb.'%', Tools::getValue('color_picker_'.$this->model->getId().'_'.$nb.'_'.$this->wich_template), $content);
			if($save){
				Db::getInstance()->Execute("DELETE FROM "._DB_PREFIX_."cartabandonment_template_color WHERE id_template = ".(int)$this->id_template);
				Db::getInstance()->Execute("INSERT INTO "._DB_PREFIX_."cartabandonment_template_color VALUES (NULL, ".(int)$this->id_template.", ".$nb.", '".Tools::getValue('color_picker_'.$this->model->getId().'_'.$nb.'_'.$this->wich_template)."')");
			}
		}
	}
	
	// Save One column in database
	private function saveColumn($column, $id_field, $value){
		if(!isset($column) || !isset($id_field) || !isset($value))
			return false;
		return Db::getInstance()->Execute("INSERT INTO "._DB_PREFIX_."cartabandonment_template_field VALUES (NULL, ".(int)$this->id_template.", ".$id_field.", '".$value."', '".$column."')");
	}
	
	// This function replace all %TAGS% before sending the newsletter
	// if id_cart is NULL, it means that this is a test (test send or preview)
	public function editTemplate($content, $id_cart = NULL, $id_lang = 1){
		if(is_null($id_cart))
			$id_cart = 1;

		$products = Db::getInstance()->ExecuteS('
			SELECT ca.id_cart, pl.id_product, pl.name, c.firstname, c.lastname, c.id_lang, gl.name as gender_name
			FROM `'._DB_PREFIX_.'cart` ca 
			JOIN '._DB_PREFIX_.'cart_product cp ON ca.id_cart = cp.id_cart
			JOIN '._DB_PREFIX_.'customer c ON ca.id_customer = c.id_customer
			JOIN '._DB_PREFIX_.'product_lang pl ON cp.id_product = pl.id_product
			JOIN '._DB_PREFIX_.'gender_lang gl ON c.id_gender = gl.id_gender
			WHERE 
			ca.id_cart = ' . (int)$id_cart . '
			AND pl.id_lang = c.id_lang AND pl.id_lang = ' . (int)$id_lang);

		$html = '<table>';
		// $cartProducts = $this->model->getCartProducts();

		$link = new Link();
		foreach($products as $product){
			$img = Template::getImage($product['id_product'], $id_lang);
			// $img = Product::getCover($product['id_product']);
			
			$html .= '<tr><td colspan="3" height="10px">&nbsp;</td></tr>';
			$html .= '<tr>';
			$html .= '<td>';
			$html .= '<a target="_blank" style="text-decoration: none;" href="' . $link->getProductLink($product['id_product']) . '"><img width="170px;" valign="bottom" src="http://' . $img . '"></a>';
			$html .= '</td>';
			$html .= '<td align="left" valign="bottom">&nbsp;&nbsp;&nbsp;&nbsp;<a style="text-decoration: none;" target="_blank" href="' . $link->getProductLink($product['id_product']) . '">' . $product['name'] . '</a></td>';
			$html .= '</tr>';
			// $product .= str_replace('%IMG%', '<img src="http://' . $img . '">', $cartProducts);
			// $product .= str_replace('%NAME%', Product::getProductName($product['id_product'], null, $product['id_lang']), $product);
			// $html .= $product;
		}
		$html .= '</table>';
								
		if(empty($products)){
			$products = Db::getInstance()->ExecuteS('
			SELECT c.firstname, c.lastname, c.id_lang, gl.name as gender_name
			FROM `'._DB_PREFIX_.'cart` ca 
			JOIN '._DB_PREFIX_.'customer c ON ca.id_customer = c.id_customer
			JOIN '._DB_PREFIX_.'gender_lang gl ON c.id_gender = gl.id_gender
			WHERE 
			ca.id_cart = ' . (int)$id_cart);
		}					
		$content = str_replace('%CART_PRODUCTS%', $html, $content);
		$content = str_replace('%SHOP_OPEN_LINK%', '<a href="'.Tools::getShopDomain(true).__PS_BASE_URI__.'" target="_blank">', $content);
		$content = str_replace('%SHOP_CLOSE_LINK%', '</a>', $content);
		$content = str_replace('%FIRSTNAME%', $products[0]['firstname'], $content);
		$content = str_replace('%LASTNAME%', $products[0]['lastname'], $content);
		$content = str_replace('%GENDER%', $products[0]['gender_name'], $content);
		$content = str_replace('%SHOP_LINK_OPEN%', '<a href="'.Tools::getShopDomain(true).__PS_BASE_URI__.'" target="_blank">', $content);
		$content = str_replace('%SHOP_LINK_CLOSE%', '</a>', $content);
		$content = str_replace('%UNUBSCRIBE_OPEN%', '<a href="'.Tools::getShopDomain(true).__PS_BASE_URI__.'" target="_blank">', $content);
		$content = str_replace('%UNUBSCRIBE_CLOSE%', '</a>', $content);
		
		return $content;
	}
	
	private static function editCart($content, $id_cart = NULL, $id_lang = 1){
		
	}
	
	public static function editTitleBeforeSending($title, $id_cart = NULL, $id_lang = 1){
		if(is_null($id_cart))
			$id_cart = 1;
		$products = Db::getInstance()->ExecuteS('
				SELECT c.firstname, c.lastname, gl.name as gender_name
				FROM `'._DB_PREFIX_.'cart` ca 
				JOIN '._DB_PREFIX_.'customer c ON ca.id_customer = c.id_customer
				JOIN '._DB_PREFIX_.'gender_lang gl ON c.id_gender = gl.id_gender
				WHERE 
				ca.id_cart = ' . (int)$id_cart);
		$title = str_replace('%FIRSTNAME%', $products[0]['firstname'], $title);
		$title = str_replace('%LASTNAME%', $products[0]['lastname'], $title);
		$title = str_replace('%GENDER%', $products[0]['gender_name'], $title);
		return $title;
	}
	
	public static function editBeforeSending($content, $id_cart = NULL, $id_lang = 1, $wichRemind){
		if(is_null($id_cart))
			$id_cart = 1;
		
		$products = Db::getInstance()->ExecuteS('
				SELECT ca.id_cart, pl.id_product, pl.name, c.firstname, c.lastname, c.id_lang, ca.secure_key, ca.id_customer, gl.name as gender_name
				FROM `'._DB_PREFIX_.'cart` ca 
				JOIN '._DB_PREFIX_.'cart_product cp ON ca.id_cart = cp.id_cart
				JOIN '._DB_PREFIX_.'customer c ON ca.id_customer = c.id_customer
				JOIN '._DB_PREFIX_.'product_lang pl ON cp.id_product = pl.id_product
				JOIN '._DB_PREFIX_.'gender_lang gl ON c.id_gender = gl.id_gender
				WHERE 
				ca.id_cart = ' . (int)$id_cart . '
				AND pl.id_lang = c.id_lang AND pl.id_lang = ' . (int)$id_lang);
		
		if(empty($products)) return false;
		
		if(strpos($content,'%CART_PRODUCTS%')){
			$html = '<table width="100%">';
			// $cartProducts = $this->model->getCartProducts();

			$link = new Link();
			foreach($products as $product){
				$img = Template::getImage($product['id_product'], $id_lang);
				// $img = Product::getCover($product['id_product']);
				
				$html .= '<tr><td height="10px">&nbsp;</td></tr>';
				$html .= '<tr>';
				$html .= '<td>';
				$html .= '<a target="_blank" style="text-decoration: none;" href="' . $link->getProductLink($product['id_product']) . '"><img width="170px;" valign="bottom" src="http://' . $img . '"></a>';
				$html .= '</td>';
				$html .= '<td align="left" valign="bottom" width="1px"><a style="text-decoration: none;" target="_blank" href="' . $link->getProductLink($product['id_product']) . '">' . $product['name'] . '</a></td>';
				$html .= '</tr>';
				// $product .= str_replace('%IMG%', '<img src="http://' . $img . '">', $cartProducts);
				// $product .= str_replace('%NAME%', Product::getProductName($product['id_product'], null, $product['id_lang']), $product);
				// $html .= $product;
			}
			$html .= '</table>';
									
			$content = str_replace('%CART_PRODUCTS%', $html, $content);
		}
		$token_cart = md5(_COOKIE_KEY_.'recover_cart_'.$id_cart);
		$token = Configuration::get('CARTABAND_TOKEN');
		$content = str_replace('%SHOP_LINK_OPEN%', '
		<a href="'.Tools::getShopDomain(true).__PS_BASE_URI__.'modules/cartabandonmentpro/redirectShop.php?token='.$token.'&wichRemind='.$wichRemind.'&id_cart='.$id_cart.'&link=shop&id_customer='.$products[0]['id_customer'].'" target="_blank">', $content);
		$content = str_replace('%SHOP_LINK_CLOSE%', '</a>', $content);
		$content = str_replace('%CART_LINK_OPEN%', '<a href="'.Tools::getShopDomain(true).__PS_BASE_URI__.'modules/cartabandonmentpro/redirectShop.php?token='.$token.'&token_cart='.$token_cart.'&wichRemind='.$wichRemind.'&id_cart='.$id_cart.'&link=cart&id_customer='.$products[0]['id_customer'].'&recover_cart='.$id_cart.'&secure_key='.$products[0]['secure_key'].'" target="_blank">', $content);
		$content = str_replace('%CART_LINK_CLOSE%', '</a>', $content);

		$content = str_replace('%FIRSTNAME%', $products[0]['firstname'], $content);
		$content = str_replace('%LASTNAME%', $products[0]['lastname'], $content);
		$content = str_replace('%GENDER%', $products[0]['gender_name'], $content);
		
		$content = str_replace('%UNUBSCRIBE_OPEN%', '<a href="'.Tools::getShopDomain(true).__PS_BASE_URI__.'modules/cartabandonmentpro/redirectShop.php?token='.$token.'&token_cart='.$token_cart.'&wichRemind='.$wichRemind.'&id_cart='.$id_cart.'&link=unsubscribe&id_customer='.$products[0]['id_customer'].'&recover_cart='.$id_cart.'&secure_key='.$products[0]['secure_key'].'" target="_blank">', $content);
		$content = str_replace('%UNUBSCRIBE_CLOSE%', '</a>', $content);
		
		
		return '<img width="1px" height="1px" src="'.Tools::getShopDomain(true).__PS_BASE_URI__.'modules/cartabandonmentpro/visualize.php?token='.$token.'&wichRemind='.$wichRemind.'&id_cart='.$id_cart.'"> '.$content;
	}
	
	private static function getImage($p_id, $id_lang)
	{
		$images = Db::getInstance()->ExecuteS('
			SELECT id_image
			FROM '._DB_PREFIX_.'image
			WHERE id_product = ' . (int)$p_id . '
			ORDER BY cover DESC');
		
		$query = 'SELECT link_rewrite FROM '._DB_PREFIX_.'product_lang
				  WHERE id_product = ' . (int)$p_id . ' AND id_lang = ' . (int)$id_lang;
		
		$link_rewrite = Db::getInstance('PS_USE_SQL_SLAVE')->ExecuteS($query);
		$link = new Link();
		
		if (version_compare('PS_VERSION', '1.5.0.17') >= 0) {
			if (Configuration::get('PS_LEGACY_IMAGES')){
				$imageLink = Tools::getShopDomain(true).'/img/p/'.$p_id.'-'.$images[0]['id_image'].'-home_default.jpg';
			}
			else{
				$imageLink = $link->getImageLink($link_rewrite[0]['link_rewrite'], $images[0]['id_image']);
			}
		}
		else{
			$imageLink = $link->getImageLink($link_rewrite[0]['link_rewrite'], (int)$p_id.'-'.(int)$images[0]['id_image']);
			$imageLink = str_replace('.jpg', '-medium_default.jpg', $imageLink);
			$imageLink = str_replace('.png', '-medium_default.png', $imageLink);
		}
		return $imageLink;
	}
	public function setWichTemplate($val){
		$this->wich_template = $val;
	}
	public function getName(){ return $this->name; }
	public function setName($name) { $this->name = $name; }
}