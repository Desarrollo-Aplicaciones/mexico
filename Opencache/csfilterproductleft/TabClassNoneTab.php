<?php

class TabClassNoneTab extends ObjectModel
{
	public $id_tab;
	public $product_type;
	public $position;
	public $hook;
	public $display;
	public $title;
	public $product_type_menu;
	public $product_list = false;
	
	public static $definition = array(
		'table' => 'csfilterproductleft',
		'primary' => 'id_tab',
		'multilang' => true,
		'multilang_shop' => true,
		'fields' => array(
			'hook' =>		array('type' => self::TYPE_INT,'validate' => 'isunsignedInt'),
			// Lang fields
			'title' =>			array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml', 'required' => true, 'size' => 255),
			//Shop fields
			'product_type' => 	array('type' => self::TYPE_STRING,'shop' => true, 'validate' => 'isString', 'size' => 100),
			'position' =>		array('type' => self::TYPE_INT,'shop' => true, 'validate' => 'isunsignedInt'),
			'display'  => 		array('type' => self::TYPE_BOOL,'shop' => true),
		)
	);
	
	public	function __construct($id_tab = null, $id_lang = null, $id_shop = null, Context $context = null)
	{
		parent::__construct($id_tab, $id_lang, $id_shop);
		$this->product_type_menu = (int)(preg_replace("/[^0-9]/", '', $this->product_type));
		Shop::addTableAssociation('csfilterproductleft', array('type' => 'shop'));
		Shop::addTableAssociation('csfilterproductleft_lang', array('type' => 'fk_shop'));
	}
	public function copyFromPost()
	{
		foreach ($_POST AS $key => $value)
			if (key_exists($key, $this) AND $key != 'id_'.$this->table)
				$this->{$key} = $value;
		if($this->product_type == "choose_the_category")
			$this->product_type .= '_'.$this->product_type_menu;
		
		if (sizeof($this->fieldsValidateLang))
		{
			$languages = Language::getLanguages(false);
			foreach ($languages AS $language)
				foreach ($this->fieldsValidateLang AS $field => $validation)
					if (isset($_POST[$field.'_'.(int)($language['id_lang'])]))
						$this->{$field}[(int)($language['id_lang'])] = $_POST[$field.'_'.(int)($language['id_lang'])];
		}
		
	}
	public function add($autodate = true, $null_values = false)
	{
		parent::add($autodate, true);
		if (Shop::getContext() != Shop::CONTEXT_SHOP)
		{
			foreach (Shop::getContextListShopID() as $id_shop)
			{
				$this->updatePositionMax($id_shop);
			}
			
		}
		else
		{
			$id_shop = (int)Context::getContext()->shop->id;
			$this->updatePositionMax($id_shop);
		}
	}
	
	public function updatePositionMax($id_shop)
	{
		$sql = 'UPDATE `'._DB_PREFIX_.'csfilterproductleft_shop`
					SET `position`='.$this->getNextPosition($id_shop).'
					WHERE `id_shop` = '.$id_shop.' AND `id_tab`='.(int)($this->id);
		$result = (Db::getInstance()->Execute('
					UPDATE `'._DB_PREFIX_.'csfilterproductleft_shop`
					SET `position`='.$this->getNextPosition($id_shop).'
					WHERE `id_shop` = '.$id_shop.' AND `id_tab`='.(int)($this->id)));
		return $result;
	}
	
	public static function getNextPosition($id_shop)
	{
		$max = Db::getInstance()->getValue('SELECT MAX(position)+1 FROM `'._DB_PREFIX_.'csfilterproductleft_shop` WHERE '.($id_shop ? ' `id_shop` = '.$id_shop : '1').' ');
		return ($max ? $max : 0);
	}
	
	public function validateController($htmlentities = true)
	{
		$errors = array();
		
		if($this->product_type == "choose_the_category_" AND (!isset($this->product_type_menu) or $this->product_type_menu == ""))
			$errors[] = '<b>Get Product From</b>: '.Tools::displayError('Choose a category.');
		
		$defaultLanguage = (int)(Configuration::get('PS_LANG_DEFAULT'));
		$field = "title";
		if (!$this->{$field} OR !sizeof($this->{$field}) OR ($this->{$field}[$defaultLanguage] !== '0' AND empty($this->{$field}[$defaultLanguage])))
			$errors[] = '<b>Title</b> '.Tools::displayError('is empty for default language.');

		$validate = new Validate();
		foreach ($this->fieldsValidateLang as $fieldArray => $method)
		{
			if (!is_array($this->{$fieldArray}))
				continue ;
			foreach ($this->{$fieldArray} as $k => $value)
				if (!method_exists($validate, $method))
					die (Tools::displayError('Validation function not found.').' '.Tools::safeOutput($method));
				elseif (!empty($value) AND !call_user_func(array('Validate', $method), $value))
				{
					$errors[] = Tools::displayError('The following field is invalid according to the validate method ').'<b>'.$method.'</b>:<br/> ('.self::displayFieldName($fieldArray, get_class($this), $htmlentities).' = '.$value.' '.Tools::displayError('for language').' '.$k;
				}
		}
		
		return $errors;
	}
	
	public function updatePosition($way, $position)
	{
		if (!isset($position))
			return false;
			$id = (int)Context::getContext()->shop->id;
			$id_shop = $id ? $id: Configuration::get('PS_SHOP_DEFAULT');
			$tab_move = Db::getInstance()->getRow(
					'SELECT ts.* FROM `'._DB_PREFIX_.'csfilterproductleft_shop` ts
					WHERE (ts.id_shop = '.(int)$id_shop.')
					AND ts.`id_tab` = '.(int)($this->id_tab));
			$result = (Db::getInstance()->Execute('
				UPDATE `'._DB_PREFIX_.'csfilterproductleft_shop`
				SET `position`= `position` '.($way ? '- 1' : '+ 1').'
				WHERE `id_shop` = '.$id_shop.' AND `position`
				'.($way
					? '> '.(int)($tab_move['position']).' AND `position` <= '.(int)($position)
					: '< '.(int)($tab_move['position']).' AND `position` >= '.(int)($position)))
			AND Db::getInstance()->Execute('
				UPDATE `'._DB_PREFIX_.'csfilterproductleft_shop`
				SET `position` = '.(int)($position).'
				WHERE `id_shop` = '.$id_shop.' AND `id_tab`='.(int)($this->id_tab)));
		return $result;
	}
	
	public function updateStatus($status)
	{
		$id = (int)Context::getContext()->shop->id;
		$id_shop = $id ? $id: Configuration::get('PS_SHOP_DEFAULT');
		if (!isset($status))
			return false;
		if($status == 0)
			$status = 1;
		else 
			$status = 0;
		$result = (Db::getInstance()->Execute('
			UPDATE `'._DB_PREFIX_.'csfilterproductleft_shop`
			SET `display`='.$status.'
			WHERE `id_shop` = '.$id_shop.' AND `id_tab`='.(int)($this->id_tab)));
		return $result;
	}
	
	
	public static function cleanPositions()
	{
		$return = true;
		$id = (int)Context::getContext()->shop->id;
		$id_shop = $id ? $id: Configuration::get('PS_SHOP_DEFAULT');
		$result = Db::getInstance()->ExecuteS('
		SELECT `id_tab`
		FROM `'._DB_PREFIX_.'csfilterproductleft_shop`
		WHERE `id_shop` = '.$id_shop.'
		ORDER BY `position`');
		$sizeof = sizeof($result);
		for ($i = 0; $i < $sizeof; $i++){
				$sql = '
				UPDATE `'._DB_PREFIX_.'csfilterproductleft_shop`
				SET `position` = '.(int)($i).'
				WHERE `id_shop` = '.$id_shop.' AND `id_tab` = '.(int)($result[$i]['id_tab']);
				$return &= Db::getInstance()->Execute($sql);
			}
		return $return;
	}
	
	public function delete()
	{
		parent::delete();
		$this->cleanPositions();
	}
	
	public function getProductList($nb = 10)
	{
		global $cookie;
		if(strpos($this->product_type, "featured_products") !== false)
		{
			$category = new Category(Context::getContext()->shop->getCategory(), (int)Context::getContext()->language->id);
			$this->product_list = $category->getProducts((int)Context::getContext()->language->id, 1, $nb);
		}
		elseif(strpos($this->product_type, "special_products") !== false)
		{
			$this->product_list = Product::getPricesDrop((int)($cookie->id_lang), 0, $nb);
		}
		elseif(strpos($this->product_type, "topseller_products") !== false)
		{
			$this->product_list = ProductSale::getBestSales((int)($cookie->id_lang), 0, $nb);
		}
		elseif(strpos($this->product_type, "new_products") !== false)
		{
			$this->product_list = Product::getNewProducts((int)($cookie->id_lang), 0, $nb);
		}
		elseif(strpos($this->product_type, "choose_the_category") !== false)
		{
			$category = new Category((int)$this->product_type_menu, (int)Context::getContext()->language->id);
			$this->product_list = $category->getProducts((int)($cookie->id_lang), 1, $nb);
		}
		
	}
	
	public function getLink()
	{
		global $cookie;
		$linkOpj = new Link();
		if(strpos($this->product_type, "featured_products") !== false)
		{
			$cat = new Category(Configuration::get('PS_HOME_CATEGORY'),(int)Context::getContext()->language->id);
			$link = $linkOpj->getCategoryLink($cat->id_category,$cat->link_rewrite);
		}
		elseif(strpos($this->product_type, "special_products") !== false)
		{
			$link = $linkOpj->getPageLink('prices-drop');
		}
		elseif(strpos($this->product_type, "topseller_products") !== false)
		{
			$link = $linkOpj->getPageLink('best-sales');
		}
		elseif(strpos($this->product_type, "new_products") !== false)
		{
			$link = $linkOpj->getPageLink('new-products');
		}
		elseif(strpos($this->product_type, "choose_the_category") !== false)
		{
			$cat = new Category((int)$this->product_type_menu, (int)Context::getContext()->language->id);
			$link = $linkOpj->getCategoryLink($cat->id_category,$cat->link_rewrite);
		}
		return $link;
	}
	
}

?>
