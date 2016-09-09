<?php

class CsMegaMenuOptionClass extends ObjectModel
{
	public $id_menu;
	public $id_option;
	public $type_option;
	public $position_option;
	public $content_option;
	public $temp_url = "{megamenu_url}";
	public static $definition = array(
		'table' => 'csmegamenu_option',
		'primary' => 'id_option',
		//'multilang' => true,
		'multishop' => true,
		'fields' => array(
			//Shop fields
			'id_menu' =>		array('type' => self::TYPE_INT,'shop' => true, 'validate' => 'isunsignedInt'),
			'type_option' =>		array('type' => self::TYPE_INT,'shop' => true, 'validate' => 'isunsignedInt'),
			'position_option' =>	array('type' => self::TYPE_INT,'shop' => true, 'validate' => 'isunsignedInt'),
			'content_option' => 		array('type' => self::TYPE_HTML, 'shop' => true, 'validate' => 'isString', 'required' => true),
		)
	);
	public	function __construct($id_option = null, $id_lang = null, $id_shop = null, Context $context = null)
	{
		parent::__construct($id_option, $id_lang, $id_shop);
		if ($this->id)
		{
			$this->type_option = $this->getFieldShop('type_option');
			$this->position_option = $this->getFieldShop('position_option');
			$this->content_option = $this->getFieldShop('content_option');
		}
		Shop::addTableAssociation('csmegamenu_option', array('type' => 'shop'));
	}
	
	public function getFieldShop($field)
	{
		$id_shop = (int)Context::getContext()->shop->id;
		$sql = 'SELECT mos.'.$field.' FROM '._DB_PREFIX_.'csmegamenu_option mo
		LEFT JOIN '._DB_PREFIX_.'csmegamenu_option_shop mos ON (mo.id_option = mos.id_option)
		WHERE mo.id_option = '.$this->id.' AND mos.id_shop = '.$id_shop.'';
		$result = Db::getInstance()->getValue($sql);
		return $result;
	}
	public function validateController($htmlentities = true, $copy_post = false)
	{
		$errors = '';
		/*check validate*/
		if ($_POST['type_option'] == 0) //category
		{
			if(!isset($_POST['content_option'][$_POST['type_option']]['opt_id_parent_cat']))
				$errors = 'Choose Parent Category empty';
		}
		if ($_POST['type_option'] == 1) //product
		{
			if(!isset($_POST['content_option'][$_POST['type_option']]['input_hidden_id']) || $_POST['content_option'][$_POST['type_option']]['input_hidden_id'] == '')
				$errors = 'Choose product empty';
		}
		if ($_POST['type_option'] == 2) //static
		{
			$defaultLanguage = (int)(Configuration::get('PS_LANG_DEFAULT'));
			if(!isset($_POST['content_option'][$_POST['type_option']]['opt_content_static'][$defaultLanguage]) || $_POST['content_option'][$_POST['type_option']]['opt_content_static'][$defaultLanguage] == '')
				$errors = 'Content static default languages empty';
		}
		if ($_POST['type_option'] == 3) //manufacture
		{
			if(!isset($_POST['content_option'][$_POST['type_option']]['opt_list_manu']))
				$errors = 'Manufacture empty';
		}
		if ($_POST['type_option'] == 4) //cms
		{
			if(!isset($_POST['content_option'][$_POST['type_option']]['footerBox']))
				$errors = 'CMS information empty';
		}
		if(isset($errors) && $errors)
			return $errors;
		if($_POST['content_option'][0]['opt_fill_column'] == '')
			$_POST['content_option'][0]['opt_fill_column'] = 1;
	}
	
	public function copyFromPostOption()
	{
		foreach ($_POST AS $key => $value)
		if (key_exists($key, $this) AND $key != 'id_'.$this->table)
		{	
			if($key == 'content_option')
			{
				foreach ($_POST[$key] as $keyOption=>$opArr)
				{
					if(isset($opArr['opt_fill_column']))
					{
						$_POST[$key][$_POST['type_option']]['opt_fill_column'] = $opArr['opt_fill_column'];
					}
				}
				if ($_POST['type_option'] == 2) //check option static block
				{
					$languages = Language::getLanguages(false);
					foreach ($languages AS $language)
					{
							$temp = str_replace(_PS_BASE_URL_.__PS_BASE_URI__, $this->temp_url,$_POST['content_option'][$_POST['type_option']]['opt_content_static'][(int)($language['id_lang'])]);
							$_POST['content_option'][$_POST['type_option']]['opt_content_static'][(int)($language['id_lang'])] = $temp;
					}
					
					$value = htmlspecialchars(json_encode($_POST['content_option'][$_POST['type_option']]));
					$turned = '\r\n';
					$turn_back = '';
					$value = str_replace( $turned, $turn_back, $value );
				}
				else
				{
					$value = json_encode($_POST['content_option'][$_POST['type_option']]);
				}
				
			}
			$this->{$key} = $value;
		}
	}
	public function add($autodate = true, $null_values = false)
	{
		parent::add($autodate, true);
		if (Shop::getContext() != Shop::CONTEXT_SHOP)
		{
			foreach (Shop::getContextListShopID() as $id_shop)
			{
				$this->updatePositionOptionMax($id_shop);
			}
			
		}
		else
		{
			$id_shop = (int)Context::getContext()->shop->id;
			$this->updatePositionOptionMax($id_shop);
		}
	}
	
	
	public function updatePositionOptionMax($id_shop)
	{
		$result = (Db::getInstance()->Execute('
					UPDATE `'._DB_PREFIX_.'csmegamenu_option_shop`
					SET `position_option`='.$this->getNextPositionOption($id_shop,$this->id_menu).'
					WHERE `id_shop` = '.$id_shop.' AND `id_option`='.(int)($this->id)));
		return $result;
	}
	
	public static function getNextPositionOption($id_shop,$id_menu)
	{
		$max = Db::getInstance()->getValue('SELECT MAX(position_option)+1 FROM `'._DB_PREFIX_.'csmegamenu_option_shop` WHERE id_menu = '.$id_menu.($id_shop ? ' AND `id_shop` = '.$id_shop : '1').' ' );
		return ($max ? $max : 0);
	}
	
	
	public function updatePositionOption($way, $position)
	{
		if (!isset($position))
			return false;
			$id = (int)Context::getContext()->shop->id;
			$id_shop = $id ? $id: Configuration::get('PS_SHOP_DEFAULT');
			$option_move = Db::getInstance()->getRow(
					'SELECT mos.* FROM `'._DB_PREFIX_.'csmegamenu_option_shop` mos
					WHERE (mos.id_shop = '.(int)$id_shop.')
					AND mos.`id_option` = '.(int)($this->id_option));
			$result = (Db::getInstance()->Execute('
				UPDATE `'._DB_PREFIX_.'csmegamenu_option_shop`
				SET `position_option`= `position_option` '.($way ? '- 1' : '+ 1').'
				WHERE `id_shop` = '.$id_shop.' AND `position_option`
				'.($way
					? '> '.(int)($option_move['position_option']).' AND `position_option` <= '.(int)($position)
					: '< '.(int)($option_move['position_option']).' AND `position_option` >= '.(int)($position)).'
					AND `id_menu` = '.$this->id_menu.'
					')
			AND Db::getInstance()->Execute('
				UPDATE `'._DB_PREFIX_.'csmegamenu_option_shop`
				SET `position_option` = '.(int)($position).'
				WHERE `id_shop` = '.$id_shop.' AND `id_option`='.(int)($this->id_option)).'
				AND `id_menu` = '.$this->id_menu.'
				');
		return $result;
	}
	
	public static function cleanPositionsOption()
	{
		$return = true;
		$id = (int)Context::getContext()->shop->id;
		$id_shop = $id ? $id: Configuration::get('PS_SHOP_DEFAULT');
		$result = Db::getInstance()->ExecuteS('
		SELECT `id_option`
		FROM `'._DB_PREFIX_.'csmegamenu_option_shop`
		WHERE `id_shop` = '.$id_shop.'
		ORDER BY `position_option`');
		$sizeof = sizeof($result);
		for ($i = 0; $i < $sizeof; $i++){
				$sql = '
				UPDATE `'._DB_PREFIX_.'csmegamenu_option_shop`
				SET `position_option` = '.(int)($i).'
				WHERE `id_shop` = '.$id_shop.' AND `id_option` = '.(int)($result[$i]['id_option']);
				$return &= Db::getInstance()->Execute($sql);
			}
		return $return;
	}
	public function cleanSortPositions($optionSort)
	{
		
		$id = (int)Context::getContext()->shop->id;
		$id_shop = $id ? $id: Configuration::get('PS_SHOP_DEFAULT');
		$return = true;
		$sizeof = sizeof($optionSort);
		//$sql = '';
		for ($i = 0; $i < $sizeof; $i++){
				$sql = '
				UPDATE `'._DB_PREFIX_.'csmegamenu_option_shop`
				SET `position_option` = '.(int)($i).'
				WHERE `id_shop` = '.$id_shop.' AND `id_option` = '.(int)($optionSort[$i]);
				
				$return &= Db::getInstance()->Execute($sql);
			}
			//die($sql);
		return $return;
	}
}

?>
