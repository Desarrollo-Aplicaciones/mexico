<?php

class SliderClass extends ObjectModel
{
	public $id_slider;
	public $image;
	//shop
	public $animation;
	public $url;
	public $position;
	public $display;
	
	public static $definition = array(
		'table' => 'csslider',
		'primary' => 'id_slider',
		'multishop' => true,
		'fields' => array(
			// basic fields
			'image' =>	 array('type' => self::TYPE_STRING,'shop' => true, 'validate' => 'isCleanHtml', /*'required' => true,*/ 'size' => 255),
			//Shop fields
			'animation' => array('type' => self::TYPE_HTML, 'shop' => true, 'validate' => 'isString'/*, 'required' => true*/),
			'url' => 	array('type' => self::TYPE_STRING,'shop' => true, 'validate' => 'isString', 'size' => 255),
			'position' =>		array('type' => self::TYPE_INT,'shop' => true, 'validate' => 'isunsignedInt'),
			'display'  => 		array('type' => self::TYPE_BOOL,'shop' => true),
		)
	);
	
	public	function __construct($id_tab = null, $id_lang = null, $id_shop = null)
	{
		parent::__construct($id_tab, $id_lang, $id_shop);
		Shop::addTableAssociation('csslider', array('type' => 'shop'));
		Shop::addTableAssociation('csslider_lang', array('type' => 'fk_shop'));
		if ($this->id)
		{
			$this->animation = $this->getFieldShop('animation');
			$this->url = $this->getFieldShop('url');
			$this->image = $this->getFieldShop('image');
			$this->position = $this->getFieldShop('position');
			$this->display = $this->getFieldShop('display');
		}
		$this->image = json_decode($this->image);
		$this->animation = json_decode($this->animation);
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
			UPDATE `'._DB_PREFIX_.'csslider_shop`
			SET `display`='.$status.'
			WHERE `id_shop` = '.$id_shop.' AND `id_slider`='.(int)($this->id_slider)));
		return $result;
	}
	
	public function getFieldShop($field)
	{
		$id_shop = (int)Context::getContext()->shop->id;
		$sql = 'SELECT ss.'.$field.' FROM '._DB_PREFIX_.'csslider s
		LEFT JOIN '._DB_PREFIX_.'csslider_shop ss ON (s.id_slider = ss.id_slider)
		WHERE s.id_slider = '.$this->id.' AND ss.id_shop = '.$id_shop.'';
		$position = Db::getInstance()->getValue($sql);
		return $position;
	}
	
	public function add($autodate = true, $null_values = false)
	{
		parent::add($autodate, true);
		if (Shop::getContext() != Shop::CONTEXT_SHOP)
		{
			foreach (Shop::getContextListShopID() as $id_shop)
			{
				$this->updatePositionMax($id_shop);
				$this->updateInforImage($id_shop);
			}
		}
		else
		{
			$id_shop = (int)Context::getContext()->shop->id;
			$this->updatePositionMax($id_shop);
			$this->updateInforImage($id_shop);
		}
		$image_del = json_decode($this->image);
		if(file_exists(_PS_MODULE_DIR_.'csslider/images/'.$image_del->value))
				unlink(_PS_MODULE_DIR_.'csslider/images/'.$image_del->value);
		if(file_exists(_PS_MODULE_DIR_.'csslider/images/thumbs/'.$image_del->value))
				unlink(_PS_MODULE_DIR_.'csslider/images/thumbs/'.$image_del->value);
		
	}
	public function delete($autodate = true, $null_values = false)
	{
		parent::delete($autodate, true);
		$image = $this->image;
		if (Shop::getContext() != Shop::CONTEXT_SHOP)
		{
			foreach (Shop::getContextListShopID() as $id_shop)
			{
				if(file_exists(_PS_MODULE_DIR_.'csslider/images/'.(int)$id_shop.'_'.$image->value))
					unlink(_PS_MODULE_DIR_.'csslider/images/'.(int)$id_shop.'_'.$image->value);
				if(file_exists(_PS_MODULE_DIR_.'csslider/images/thumbs/'.(int)$id_shop.'_'.$image->value))
					unlink(_PS_MODULE_DIR_.'csslider/images/thumbs/'.(int)$id_shop.'_'.$image->value);
			}
		}
		else
		{
			$id_shop = (int)Context::getContext()->shop->id;
			if(file_exists(_PS_MODULE_DIR_.'csslider/images/'.(int)$id_shop.'_'.$image->value))
				unlink(_PS_MODULE_DIR_.'csslider/images/'.(int)$id_shop.'_'.$image->value);
			if(file_exists(_PS_MODULE_DIR_.'csslider/images/thumbs/'.(int)$id_shop.'_'.$image->value))
					unlink(_PS_MODULE_DIR_.'csslider/images/thumbs/'.(int)$id_shop.'_'.$image->value);
		}
		
	}
	
	public function updateInforImage($id_shop)
	{
		$slider_update = Db::getInstance()->getRow(
					'SELECT ss.* FROM `'._DB_PREFIX_.'csslider_shop` ss
					WHERE (ss.id_shop = '.(int)$id_shop.')
					AND ss.`id_slider` = '.(int)($this->id));
		$image = json_decode($slider_update['image']);
		$new_name = (int)$id_shop.'_'.$image->value;
		$new_image = json_encode(array("type"=>$image->type,"value"=>$new_name));
		copy(_PS_MODULE_DIR_.'csslider/images/'.$image->value,_PS_MODULE_DIR_.'csslider/images/'.$new_name);
		copy(_PS_MODULE_DIR_.'csslider/images/thumbs/'.$image->value,_PS_MODULE_DIR_.'csslider/images/thumbs/'.$new_name);
		$sql = 'UPDATE `'._DB_PREFIX_.'csslider_shop`
					SET `image` = \''.$new_image.'\'
					WHERE `id_shop` = '.$id_shop.' AND `id_slider` = '.(int)($this->id);
					$return &= Db::getInstance()->Execute($sql);
		return $return;
	}
	
	
	public function copyFromPost()
	{
		foreach ($_POST AS $key => $value)
			if (key_exists($key, $this) AND $key != 'id_'.$this->table)
			{
				if($key == 'animation')
					$value = json_encode($_POST['animation']);
				$arr = array();
				if($key == 'image')
				{
					if($value['type'] == "image")
					{
						if(isset($_POST['imagehidden']) && $_POST['imagehidden']!= '' && $_FILES['image']['name']['value'] == "")
						{
							$arr = array("type"=>$value['type'],"value"=>$_POST['imagehidden']);
						}
						else if ($dot_pos = strrpos($_FILES['image']['name']['value'],'.'))
						{
							$ext = substr($_FILES['image']['name']['value'], $dot_pos+1);
							$newname = ($this->id ? $this->id : $this->getNextId());
							$newname .= '.'.$ext;
							$arr = array("type"=>$value['type'],"value"=>$newname);
						}
						$value = json_encode($arr);
						
					}
					else
						$value = json_encode($_POST['image']);
				}
				$this->{$key} = $value;
			}
	}
	
	public function updatePositionMax($id_shop)
	{
		$result = (Db::getInstance()->Execute('
					UPDATE `'._DB_PREFIX_.'csslider_shop`
					SET `position`='.$this->getNextPosition($id_shop).'
					WHERE `id_shop` = '.$id_shop.' AND `id_slider`='.(int)($this->id)));
		return $result;
	}
	
	
	public static function getNextPosition($id_shop)
	{
		$max = Db::getInstance()->getValue('SELECT MAX(position)+1 FROM `'._DB_PREFIX_.'csslider_shop` WHERE '.($id_shop ? ' `id_shop` = '.$id_shop : '1').' ');
		return ($max ? $max : 0);
	}
	
	public function uploadImage($id_slider = null)
	{
		$errors = "";
		if (isset($_FILES['image']) AND isset($_FILES['image']['tmp_name']) AND !empty($_FILES['image']['tmp_name']))
		{
			if ($dot_pos = strrpos($_FILES['image']['name']['value'],'.'))
			{
				$ext = substr($_FILES['image']['name']['value'], $dot_pos+1);
				$newname = ($id_slider ? $id_slider : $this->getNextId());
				if (!move_uploaded_file($_FILES['image']['tmp_name']['value'],_PS_MODULE_DIR_.'csslider/images/'.$newname.'.'.$ext))
					$errors .= Tools::displayError('Error move uploaded file');
				else
				{
					ImageManager::resize(_PS_MODULE_DIR_.'csslider/images/'.$newname.'.'.$ext, _PS_MODULE_DIR_.'csslider/images/thumbs/'.$newname.'.'.$ext, 45, 45);
				}
			}
		}
		return ($errors != "" ? $errors : false);
	}
	
	
	public function validateController($htmlentities = true, $copy_post = false)
	{
		$errors = array();
		if($_POST['image']['type'] == "image")
		{
			if($_FILES['image']['name']['value'] == '' && !isset($_POST['imagehidden']))
			{
				$errors[] = Tools::displayError('Image is empty.');
				return $errors;
			}
			if($error = $this->uploadImage($this->id_slider))
			{
				$errors[] = $error;
				return $errors;
			}
		}
		else if($_POST['image']['type'] == "color")
		{
			if($_POST['image']['value'] == '')
				$errors[] = Tools::displayError('Color is empty.');
		}
		return $errors;
	}
	public static function getNextId()
	{
		$max = Db::getInstance()->getValue('SELECT MAX(id_slider) FROM `'._DB_PREFIX_.'csslider`');
		return ($max ? $max + 1 : 1);
	}
	
	public function getCaption()
	{
		if (!$result = Db::getInstance()->ExecuteS(
			'SELECT csc.* 
			FROM `'._DB_PREFIX_.'csslider` cs 
			RIGHT JOIN `'._DB_PREFIX_.'csslider_caption` csc ON (cs.`id_slider` = csc.`id_slider`) 
			WHERE cs.`id_slider` = '.(int)($this->id_slider).' ORDER BY csc.`layer` ASC'))
			return false;
	 	return $result;
	}
	
	
	public function updatePosition($way, $position)
	{
		if (!isset($position))
			return false;
			$id = (int)Context::getContext()->shop->id;
			$id_shop = $id ? $id: Configuration::get('PS_SHOP_DEFAULT');
			$slider_move = Db::getInstance()->getRow(
					'SELECT ss.* FROM `'._DB_PREFIX_.'csslider_shop` ss
					WHERE (ss.id_shop = '.(int)$id_shop.')
					AND ss.`id_slider` = '.(int)($this->id_slider));
			$result = (Db::getInstance()->Execute('
				UPDATE `'._DB_PREFIX_.'csslider_shop`
				SET `position`= `position` '.($way ? '- 1' : '+ 1').'
				WHERE `id_shop` = '.$id_shop.' AND `position`
				'.($way
					? '> '.(int)($slider_move['position']).' AND `position` <= '.(int)($position)
					: '< '.(int)($slider_move['position']).' AND `position` >= '.(int)($position)))
			AND Db::getInstance()->Execute('
				UPDATE `'._DB_PREFIX_.'csslider_shop`
				SET `position` = '.(int)($position).'
				WHERE `id_shop` = '.$id_shop.' AND `id_slider`='.(int)($this->id_slider)));
		return $result;
	}
	
	public static function cleanPositions()
	{
		$return = true;
		$id = (int)Context::getContext()->shop->id;
		$id_shop = $id ? $id: Configuration::get('PS_SHOP_DEFAULT');
		$result = Db::getInstance()->ExecuteS('
		SELECT `id_slider`
		FROM `'._DB_PREFIX_.'csslider_shop`
		WHERE `id_shop` = '.$id_shop.'
		ORDER BY `position`');
		$sizeof = sizeof($result);
		for ($i = 0; $i < $sizeof; $i++){
				$sql = '
				UPDATE `'._DB_PREFIX_.'csslider_shop`
				SET `position` = '.(int)($i).'
				WHERE `id_shop` = '.$id_shop.' AND `id_slider` = '.(int)($result[$i]['id_slider']);
				$return &= Db::getInstance()->Execute($sql);
			}
		return $return;
	}
	
}

?>
