<?php

class StaticBlockClass extends ObjectModel
{
	public $id_block;
	public $title;
	public $content;
	public $identifier_block;
	public $is_active;
	public $hook;
	
	public static $definition = array(
		'table' => 'staticblock',
		'primary' => 'id_block',
		'multilang' => true,
		'multilang_shop' => true,
		'fields' => array(
			//basic fields
			'identifier_block' =>			array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'required' => true, 'size' => 255),
			'hook' =>		array('type' => self::TYPE_INT,'validate' => 'isunsignedInt'),
			// Lang fields
			'title' =>			array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCleanHtml', 'required' => true, 'size' => 255),
			'content' => 	array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isString'),
			//Shop fields
			'is_active' =>		array('type' => self::TYPE_INT,'shop' => true, 'validate' => 'isunsignedInt'),
		)
	);
	
	public $temp_url = "{static_block_url}";
	
	public	function __construct($id_tab = null, $id_lang = null, $id_shop = null, Context $context = null)
	{
		parent::__construct($id_tab, $id_lang, $id_shop);
		
		Shop::addTableAssociation('staticblock', array('type' => 'shop'));
		Shop::addTableAssociation('staticblock_lang', array('type' => 'fk_shop'));
		if ($this->id)
		{
			$this->is_active = $this->getFieldShop('is_active');
			$languages = Language::getLanguages(false);
			foreach ($languages as $language)
			{
				if (isset($this->content[(int)($language['id_lang'])]) AND !empty($this->content[(int)($language['id_lang'])]))
				{
					$temp = str_replace($this->temp_url, _PS_BASE_URL_.__PS_BASE_URI__, $this->content[(int)($language['id_lang'])]);
					$this->content[(int)($language['id_lang'])] = $temp;
				}
			}
		}
		
	}
	
	public function getFieldShop($field)
	{
		$id_shop = (int)Context::getContext()->shop->id;
		$sql = 'SELECT bs.'.$field.' FROM '._DB_PREFIX_.'staticblock b
		LEFT JOIN '._DB_PREFIX_.'staticblock_shop bs ON (b.id_block = bs.id_block)
		WHERE b.id_block = '.$this->id.' AND bs.id_shop = '.$id_shop.'';
		$result = Db::getInstance()->getValue($sql);
		return $result;
	}
	
	public function copyFromPost()
	{
		foreach ($_POST AS $key => $value)
			if (key_exists($key, $this) AND $key != 'id_'.$this->table)
				$this->{$key} = $value;

		if (sizeof($this->fieldsValidateLang))
		{
			$languages = Language::getLanguages(false);
			foreach ($languages AS $language)
				foreach ($this->fieldsValidateLang AS $field => $validation)
					if (isset($_POST[$field.'_'.(int)($language['id_lang'])]))
					{
						$temp = str_replace(_PS_BASE_URL_.__PS_BASE_URI__, $this->temp_url, $_POST[$field.'_'.(int)($language['id_lang'])]);
						$this->{$field}[(int)($language['id_lang'])] = $temp;
					}
		}
	}
	
	public function uniqueIndentifier_block($identifier_block, $id_block = null)
	{
		$result = Db::getInstance()->getRow('
			SELECT `identifier_block`
			FROM `'._DB_PREFIX_.'staticblock` 
			WHERE `identifier_block` LIKE \''.$identifier_block.'\''.($id_block ? ' AND `id_block` != '.$id_block : ''));
		$isUnique = ($result['identifier_block']) ? false : true ;
		
		return $isUnique;
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
			UPDATE `'._DB_PREFIX_.'staticblock_shop`
			SET `is_active`='.$status.'
			WHERE `id_shop` = '.$id_shop.' AND `id_block`='.(int)($this->id_block)));
		return $result;
	}
	public function validateController($htmlentities = true, $copy_post = false)
	{
		$errors = array();
		
		$field = "identifier_block";
		if (Tools::getValue($field, $this->{$field}) == false AND (string)$value != '0')
			$errors[] = '<b>Identifier block</b> '.Tools::displayError('is required.');
		
		$field = "identifier_block";
		if ($value = Tools::getValue($field, $this->{$field}))
		{
			if (!Validate::isConfigName($value))
				$errors[] = '<b>Identifier block</b> '.Tools::displayError('is invalid.');
		}
		
		if ($this->identifier_block and !$this->uniqueIndentifier_block($this->identifier_block, (int)($this->id_block)))
		{
			$errors[] = '<b>Identifier </b> '.Tools::displayError('must be unique.');
		}
		
		$defaultLanguage = (int)(Configuration::get('PS_LANG_DEFAULT'));
		$field = "title";

		if (!$this->{$field} OR !sizeof($this->{$field}) OR ($this->{$field}[$defaultLanguage] !== '0' AND empty($this->{$field}[$defaultLanguage])))
		{
			$errors[] = '<b>Title</b> '.Tools::displayError('is empty for default language.');
		}
	

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
	
}

?>
