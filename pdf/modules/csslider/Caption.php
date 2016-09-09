<?php

class Caption extends ObjectModel
{
	public $id_caption;
	public $id_slider;
	public $layer;
	public $content;
	
	public static $definition = array(
		'table' => 'csslider_caption',
		'primary' => 'id_caption',
		'fields' => array(
			// basic fields
			'id_slider' =>	array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
			'layer' =>	array('type' => self::TYPE_INT, 'validate' => 'isunsignedInt'),
			'content' => 	array('type' => self::TYPE_HTML, 'validate' => 'isString', 'required' => true),
		)
	);

	
	
	public function copyFromPost()
	{
		$languages = Language::getLanguages(false);
		$arrayTypeCaption = array();
		foreach ($_POST AS $key => $value)
		{
			if (key_exists($key, $this) AND $key != 'id_'.$this->table)
			{
				if($key == 'content')
				{
					if($_POST['addCaption'] == 'imagecaption')
					{
						foreach ($languages AS $language)
						{
							//

							//
							
							if($_FILES['imagecaption_'.$language['id_lang']]['name'] == '')
							{
								$id_lang = (int)(Configuration::get('PS_LANG_DEFAULT'));
							}
							else
							{
								$id_lang = $language['id_lang'];
							}
							if(isset($_POST['imagehidden_'.$id_lang]) && $_POST['imagehidden_'.$id_lang]!= '')
							{
								$image =  $_POST['imagehidden_'.$id_lang];
							}
							else
							{
								$dot_pos = strrpos($_FILES['imagecaption_'.$id_lang]['name'],'.');
								$ext = substr($_FILES['imagecaption_'.$id_lang]['name'], $dot_pos+1);
								$image = ($this->id ? $this->id : $this->getNextId()).'_'.$id_lang.'.'.$ext;
							 }		
							$arrayTypeCaption = array_merge($arrayTypeCaption,array('image_'.$language['id_lang']=>($image)));
						}
					}
					else if($_POST['addCaption'] == 'videocaption')
						$arrayTypeCaption = array("type_video"=>$_POST[$_POST['addCaption'].'_type'],"type_id"=>$_POST[$_POST['addCaption'].'_text']);
					else
						$arrayTypeCaption = array("text"=>$_POST[$_POST['addCaption']]);
					
					$value = array_merge(array("type_caption"=> $_POST['addCaption']),(array)$value, $arrayTypeCaption);
					$value = json_encode($value);
				}
				$this->{$key} = $value;
			}
		}
	}

	public static function getNextId()
	{
		$max = Db::getInstance()->getValue('SELECT MAX(id_caption) FROM `'._DB_PREFIX_.'csslider_caption`');
		return ($max ? $max + 1 : 1);
	}
	public function uploadImageCaption($id_caption = null)
	{
		$errors = "";
		$languages = Language::getLanguages(false);
		foreach ($languages AS $language)
		{
			if (isset($_FILES['imagecaption_'.$language['id_lang']]) AND isset($_FILES['imagecaption_'.$language['id_lang']]['tmp_name']) AND !empty($_FILES['imagecaption_'.$language['id_lang']]['tmp_name']))
			{
				if ($er = ImageManager::validateUpload($_FILES['imagecaption_'.$language['id_lang']], Tools::convertBytes(ini_get('upload_max_filesize'))))
				{
					$errors .= $er;
				}
				elseif ($dot_pos = strrpos($_FILES['imagecaption_'.$language['id_lang']]['name'],'.'))
				{
					
					$ext = substr($_FILES['imagecaption_'.$language['id_lang']]['name'], $dot_pos+1);
					$newname = ($id_caption ? $id_caption : $this->getNextId()).'_'.$language['id_lang'];
					if (!move_uploaded_file($_FILES['imagecaption_'.$language['id_lang']]['tmp_name'],_PS_MODULE_DIR_.'csslider/images/captions/'.$newname.'.'.$ext))
					{
						//die("loi upload");
						$errors .= Tools::displayError('Error move uploaded file');
					}
					else
					{
						//die("fasfsdf");
						
					}
				}
			}
		}
		return ($errors != "" ? $errors : false);
	}
	public function updateLayer($way, $layer)
	{
		if (!isset($layer))
			return false;
			$caption_move = Db::getInstance()->getRow(
					'SELECT csc.* FROM `'._DB_PREFIX_.'csslider_caption` csc
					WHERE csc.`id_caption` = '.(int)($this->id_caption));
			$result = (Db::getInstance()->Execute('
				UPDATE `'._DB_PREFIX_.'csslider_caption`
				SET `layer`= `layer` '.($way ? '- 1' : '+ 1').'
				WHERE `layer`
				'.($way
					? '> '.(int)($caption_move['layer']).' AND `layer` <= '.(int)($layer)
					: '< '.(int)($caption_move['layer']).' AND `layer` >= '.(int)($layer)))
			AND Db::getInstance()->Execute('
				UPDATE `'._DB_PREFIX_.'csslider_caption`
				SET `layer` = '.(int)($layer).'
				WHERE `id_caption`='.(int)($this->id_caption)));
		return $result;
	}
	
	public function validateController($htmlentities = true, $copy_post = false)
	{
		$errors = array();
		if($_POST['addCaption'] == "imagecaption")
		{
			if( $error = $this->uploadImageCaption($this->id_caption))
			{
				$errors[] = $error;
				return $errors;
			}
		}
		$defaultLanguage = (int)(Configuration::get('PS_LANG_DEFAULT'));
		switch($_POST['addCaption'])
		{
			case 'textcaption':
				if(!$_POST[$_POST['addCaption']] OR !sizeof($_POST[$_POST['addCaption']]) OR ($_POST[$_POST['addCaption']][$defaultLanguage] !== '0' AND empty($_POST[$_POST['addCaption']][$defaultLanguage])))
					$errors[] = '<b>Text caption</b> '.Tools::displayError('is empty for default language.');
				break;
			case 'imagecaption':
				if(isset($_POST['imagehidden_'.$defaultLanguage]) && $_POST['imagehidden_'.$defaultLanguage] != '')
					break;
				if(!isset($_FILES['imagecaption_'.$defaultLanguage]) OR $_FILES['imagecaption_'.$defaultLanguage]['tmp_name'] == '')
				{
					$errors[] = '<b>Image caption</b> '.Tools::displayError('is empty for default language.');
					break;
				}
				break;
			case 'videocaption':
				if(!$_POST[$_POST['addCaption'].'_text'] OR !sizeof($_POST[$_POST['addCaption'].'_text']) OR ($_POST[$_POST['addCaption'].'_text'][$defaultLanguage] !== '0' AND empty($_POST[$_POST['addCaption'].'_text'][$defaultLanguage])))
					$errors[] = '<b>Video id</b> '.Tools::displayError('is empty for default language.');
				break;
		}
		
		return $errors;
	}
	
	public static function getNextLayer()
	{
		$max = Db::getInstance()->getValue('SELECT MAX(layer)+1 FROM `'._DB_PREFIX_.'csslider_caption`');
		return ($max ? $max : 0);
	}
	
	public static function cleanLayers()
	{
		$return = true;
		$result = Db::getInstance()->ExecuteS('
		SELECT `id_caption`
		FROM `'._DB_PREFIX_.'csslider_caption`
		ORDER BY `layer`');
		$sizeof = sizeof($result);
		for ($i = 0; $i < $sizeof; $i++){
				$sql = '
				UPDATE `'._DB_PREFIX_.'csslider_caption`
				SET `layer` = '.(int)($i).'
				WHERE `id_caption` = '.(int)($result[$i]['id_caption']);
				$return &= Db::getInstance()->Execute($sql);
			}
		return $return;
	}
	
	

}

?>
