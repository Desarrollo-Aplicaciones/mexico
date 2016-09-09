<?php

class CsMegaMenuClass extends ObjectModel
{
	public $id_menu;
	public $number_column;
	public $width;
	public $link_of_title;
	public $description;
	public $classes;
	public $icon;
	public $display;
	public $display_icon;
	public $position;
	public $title;
	public $temp_url = "{megamenu_url}";
	public static $definition = array(
		'table' => 'csmegamenu',
		'primary' => 'id_menu',
		'multilang' => true,
		'multilang_shop' => true,
		'fields' => array(
			//basic fields 
			'icon' => 	array('type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 300),
			'display_icon' => 	array('type' => self::TYPE_BOOL,'validate' => 'isunsignedInt'),
			'classes' => 	array('type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 300),
			// Lang fields
			'title' =>			array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml', 'required' => true, 'size' => 255),
			'description' =>			array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml', 'size' => 255),
			//Shop fields
			'number_column' =>		array('type' => self::TYPE_INT,'shop' => true, 'validate' => 'isunsignedInt'),
			'width' =>		array('type' => self::TYPE_INT,'shop' => true, 'validate' => 'isunsignedInt'),
			'link_of_title' => 	array('type' => self::TYPE_STRING,'shop' => true, 'validate' => 'isString', 'size' => 300),
			'display'  => 		array('type' => self::TYPE_BOOL,'shop' => true,'validate' => 'isunsignedInt'),
			'position' =>		array('type' => self::TYPE_INT,'shop' => true, 'validate' => 'isunsignedInt'),
		)
	);
	public	function __construct($id_menu = null, $id_lang = null, $id_shop = null, Context $context = null)
	{
		parent::__construct($id_menu, $id_lang, $id_shop);
		if ($this->id)
		{
			$this->number_column = $this->getFieldShop('number_column');
			$this->width = $this->getFieldShop('width');
			$this->display = $this->getFieldShop('display');
			$this->position = $this->getFieldShop('position');
			$languages = Language::getLanguages(false);
			foreach ($languages as $language)
			{
				if (isset($this->link_of_title))
				{
					$temp = str_replace($this->temp_url, _PS_BASE_URL_.__PS_BASE_URI__, $this->link_of_title);
					$this->link_of_title = $temp;
				}
			}
		}
		Shop::addTableAssociation('csmegamenu', array('type' => 'shop'));
		Shop::addTableAssociation('csmegamenu_lang', array('type' => 'fk_shop'));
		
	}
	
	public function getFieldShop($field)
	{
		$id_shop = (int)Context::getContext()->shop->id;
		$sql = 'SELECT ms.'.$field.' FROM '._DB_PREFIX_.'csmegamenu m
		LEFT JOIN '._DB_PREFIX_.'csmegamenu_shop ms ON (m.id_menu = ms.id_menu)
		WHERE m.id_menu = '.$this->id.' AND ms.id_shop = '.$id_shop.'';
		$position = Db::getInstance()->getValue($sql);
		return $position;
	}

	public function copyFromPost()
	{	
		foreach ($_POST AS $key => $value)
			if (key_exists($key, $this) AND $key != 'id_'.$this->table)
			{	
				$temp = str_replace(_PS_BASE_URL_.__PS_BASE_URI__, $this->temp_url, $value);
				$this->{$key} = $temp;
			}

		if (sizeof($this->fieldsValidateLang))
		{
			$languages = Language::getLanguages(false);
			foreach ($languages AS $language)
				foreach ($this->fieldsValidateLang AS $field => $validation)
					if (isset($_POST[$field.'_'.(int)($language['id_lang'])]))
					{
						$this->{$field}[(int)($language['id_lang'])] = $_POST[$field.'_'.(int)($language['id_lang'])];
					}
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
		$result = (Db::getInstance()->Execute('
					UPDATE `'._DB_PREFIX_.'csmegamenu_shop`
					SET `position`='.$this->getNextPosition($id_shop).'
					WHERE `id_shop` = '.$id_shop.' AND `id_menu`='.(int)($this->id)));
		return $result;
	}
	
	public static function getNextPosition($id_shop)
	{
		$max = Db::getInstance()->getValue('SELECT MAX(position)+1 FROM `'._DB_PREFIX_.'csmegamenu_shop` WHERE '.($id_shop ? ' `id_shop` = '.$id_shop : '1').' ');
		return ($max ? $max : 0);
	}
	
	public function getOptionForMenu()
	{
		$id_shop = (int)Context::getContext()->shop->id;
		if (!$result = Db::getInstance()->ExecuteS(
			'SELECT m.id_menu, mos.* 
			FROM `'._DB_PREFIX_.'csmegamenu` m 
			RIGHT JOIN `'._DB_PREFIX_.'csmegamenu_option_shop` mos ON (m.`id_menu` = mos.`id_menu` AND mos.`id_shop` = '.$id_shop.') 
			WHERE m.`id_menu` = '.(int)($this->id_menu).'
			ORDER BY mos.`position_option` ASC'))
			return false;
	 	return $result;
		
	}
	public function getNumberOptionForMenu()
	{
		$id_shop = (int)Context::getContext()->shop->id;
		$sql = 'SELECT COUNT(*) FROM `'._DB_PREFIX_.'csmegamenu` m 
			RIGHT JOIN `'._DB_PREFIX_.'csmegamenu_option_shop` mos ON (m.`id_menu` = mos.`id_menu` AND mos.`id_shop` = '.$id_shop.') 
			WHERE m.`id_menu` = '.(int)($this->id_menu);
		$total = Db::getInstance()->getValue($sql);
	 	return $total;
		
	}
	public function UploadIcon($id_menu = null)
	{
		if (isset($_FILES['icon']['tmp_name']) && $_FILES['icon']['tmp_name'])
		{
			if ($er = ImageManager::validateUpload($_FILES['icon'], 900000))
			{
				return $er;
			}
			else
			{
				$dot_pos = strrpos($_FILES['icon']['name'],'.');
				$ext = substr($_FILES['icon']['name'], $dot_pos+1);
				$newname = ($id_menu ? $id_menu : $this->getNextId());
				$tmp_name = _PS_MODULE_DIR_.'csmegamenu/img/icon/'.'icon_'.$newname.'.'.$ext;
				if (!move_uploaded_file($_FILES['icon']['tmp_name'],$tmp_name))
				{
					return $this->displayError('Error upload image');
				}
				$this->icon = 'icon_'.$newname.'.'.$ext;
			}
		}
		else
		{
			if(isset($_POST['icon_hidden']) && $_POST['icon_hidden']!='')
				$this->icon = $_POST['icon_hidden'];
			else
				$this->icon = '';
			return false;
		}
		
	}
	public static function getNextId()
	{
		$max = Db::getInstance()->getValue('SELECT MAX(id_menu) FROM `'._DB_PREFIX_.'csmegamenu`');
		return ($max ? $max + 1 : 1);
	}
	public function validateController($htmlentities = true, $copy_post = false)
	{
		$errors = array();
		if( $error = $this->uploadIcon($this->id_menu))
		{
			$errors[] = $error;
			return $errors;
		}
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
			UPDATE `'._DB_PREFIX_.'csmegamenu_shop`
			SET `display`='.$status.'
			WHERE `id_shop` = '.$id_shop.' AND `id_menu`='.(int)($this->id_menu)));
		return $result;
	}
	public function updatePosition($way, $position)
	{
		
		if (!isset($position))
			return false;
			$id = (int)Context::getContext()->shop->id;
			$id_shop = $id ? $id: Configuration::get('PS_SHOP_DEFAULT');
			$menu_move = Db::getInstance()->getRow(
					'SELECT ms.* FROM `'._DB_PREFIX_.'csmegamenu_shop` ms
					WHERE (ms.id_shop = '.(int)$id_shop.')
					AND ms.`id_menu` = '.(int)($this->id_menu));
			$result = (Db::getInstance()->Execute('
				UPDATE `'._DB_PREFIX_.'csmegamenu_shop`
				SET `position`= `position` '.($way ? '- 1' : '+ 1').'
				WHERE `id_shop` = '.$id_shop.' AND `position`
				'.($way
					? '> '.(int)($menu_move['position']).' AND `position` <= '.(int)($position)
					: '< '.(int)($menu_move['position']).' AND `position` >= '.(int)($position)))
			AND Db::getInstance()->Execute('
				UPDATE `'._DB_PREFIX_.'csmegamenu_shop`
				SET `position` = '.(int)($position).'
				WHERE `id_shop` = '.$id_shop.' AND `id_menu`='.(int)($this->id_menu)));
		return $result;
	}
	public static function cleanPositions()
	{
		$return = true;
		$id = (int)Context::getContext()->shop->id;
		$id_shop = $id ? $id: Configuration::get('PS_SHOP_DEFAULT');
		$result = Db::getInstance()->ExecuteS('
		SELECT `id_menu`
		FROM `'._DB_PREFIX_.'csmegamenu_shop`
		WHERE `id_shop` = '.$id_shop.'
		ORDER BY `position`');
		$sizeof = sizeof($result);
		for ($i = 0; $i < $sizeof; $i++){
				$sql = '
				UPDATE `'._DB_PREFIX_.'csmegamenu_shop`
				SET `position` = '.(int)($i).'
				WHERE `id_shop` = '.$id_shop.' AND `id_menu` = '.(int)($result[$i]['id_menu']);
				$return &= Db::getInstance()->Execute($sql);
			}
		return $return;
	}
	public function cleanSortPositions($menuSort)
	{
		$id = (int)Context::getContext()->shop->id;
		$id_shop = $id ? $id: Configuration::get('PS_SHOP_DEFAULT');
		$return = true;
		$sizeof = sizeof($menuSort);
		for ($i = 0; $i < $sizeof; $i++){
				$sql = '
				UPDATE `'._DB_PREFIX_.'csmegamenu_shop`
				SET `position` = '.(int)($i).'
				WHERE `id_shop` = '.$id_shop.' AND `id_menu` = '.(int)($menuSort[$i]);
				$return &= Db::getInstance()->Execute($sql);
			}
		return $return;
	}
}

?>
