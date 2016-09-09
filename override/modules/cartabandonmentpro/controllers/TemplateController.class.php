<?php
class TemplateController extends FrontController{

	public function __construct (){

	}
	
	public function edit(){
		if(!Tools::getValue('tpl')) return false;
		
		$id_tpl = (int)Tools::getValue('edittpl');
		if(!isset($id_tpl) || is_null($id_tpl) || $id_tpl == 0)
			$id_tpl = null;
			
		$template = new Template($id_tpl, new Model(Tools::getValue('tpl')));
		$template->save();

		header('Location: ' . Tools::getValue('uri'));die;
	}
	
	public static function getAllTemplates($id_shop, $id_lang = 1){
		$query = "SELECT ct.name as template_name, ct.id_template, ct.id_lang, ct.id_shop, ct.active, crl.tpl_same, crl.tpl_same, crl.wich_remind, l.name as lang_name
				  FROM `"._DB_PREFIX_."cartabandonment_template` ct
				  JOIN "._DB_PREFIX_."lang l ON ct.id_lang = l.id_lang
				  JOIN "._DB_PREFIX_."cartabandonment_remind_lang crl ON ct.id_template = crl.id_template
				  JOIN "._DB_PREFIX_."cartabandonment_remind_config crc ON crl.wich_remind = crc.wich_remind
				  WHERE crc.active = 1
				  AND crl.id_shop = " . (int)$id_shop . " AND ct.id_lang = " . (int)$id_lang;
		$results = Db::getInstance()->ExecuteS($query);
		return $results;
	}
	/** Return all templates ready to be send **/
	public static function getActiveTemplate($id_shop){
		$query = "SELECT ct.id_template, ct.id_lang, ct.id_shop, ct.name, crl.wich_remind, crl.tpl_same
				  FROM `"._DB_PREFIX_."cartabandonment_template` ct
				  INNER JOIN "._DB_PREFIX_."cartabandonment_remind_lang crl ON ct.id_template = crl.id_template
				  WHERE ct.id_shop = " . (int)$id_shop . " AND ct.active = 1 ORDER BY id_lang, wich_remind";

		$results = Db::getInstance()->ExecuteS($query);
		if(empty($results))
			return false;
		$return = array();

		$id_lang = $results[0]['id_lang'];
		$tpl_same = $results[0]['tpl_same'];
		$id_tpl   = $results[0]['id_template'];
		$name   = $results[0]['name'];

		foreach($results as $result){
			if($id_lang != $result['id_lang']){
				$tpl_same = $result['tpl_same'];
				$id_tpl   = $result['id_template'];
				$name   = $result['name'];
			}
			if($tpl_same == 0){
				$id_tpl   = $result['id_template'];
				$name   = $result['name'];
			}

			$id_lang = $result['id_lang'];

			$return[$result['id_shop']][$result['id_lang']][$result['wich_remind']]['id'] = $id_tpl;
			$return[$result['id_shop']][$result['id_lang']][$result['wich_remind']]['name'] = $name;
		}
		return $return;
	}
	/**  **/
	public static function getEditor(){
		$query = "SELECT ct.id_model, ct.name, ct.id_lang
				  FROM `"._DB_PREFIX_."cartabandonment_template` ct";
		return Db::getInstance()->ExecuteS($query);
	}
	
	public static function getEditorColors($id_template){
		$query = "SELECT ctc.id_color, ctc.value
				  FROM `"._DB_PREFIX_."cartabandonment_template_color` ctc
				  WHERE ctc.id_template = " . (int)$id_template;
		return Db::getInstance()->ExecuteS($query);
	}
	
	public static function getEditorFields($id_template){
		$query = "SELECT ctf.id_field, ctf.value, ctf.column
				  FROM `"._DB_PREFIX_."cartabandonment_template_field` ctf
				  WHERE ctf.id_template = " . (int)$id_template;
		return Db::getInstance()->ExecuteS($query);
	}
	
	public static function getTemplates(){
		$query = "SELECT ct.*, l.name as language_name, l.id_lang, ct.active
				  FROM `"._DB_PREFIX_."cartabandonment_template` ct
				  INNER JOIN ps_lang l ON ct.id_lang = l.id_lang";
		return Db::getInstance()->ExecuteS($query);
	}
	
	public static function getModelByTemplate($id_template){
		$query = "SELECT ct.id_model
				  FROM `"._DB_PREFIX_."cartabandonment_template` ct
				  WHERE ct.id_template = " . pSql($id_template);
		return Db::getInstance()->getValue($query);
	}
	
	public static function isActive($id_template){
		$query = "SELECT ct.active
				  FROM `"._DB_PREFIX_."cartabandonment_template` ct
				  WHERE ct.id_template = " . (int)$id_template;
		return Db::getInstance()->getValue($query);
	}
	
	public static function activate($id_template, $active){
		if($active == 1) 	$active = 0;
		else				$active = 1;
		$query = "UPDATE `"._DB_PREFIX_."cartabandonment_template`
				  SET active = " . $active . "
				  WHERE id_template = " . (int)$id_template;
		return Db::getInstance()->Execute($query);
	}
	
	public static function deleteTemplate($id_template, $lang){
		$query = "DELETE
					FROM `"._DB_PREFIX_."cartabandonment_template`
					WHERE id_template = " . (int)$id_template;
		$query2 = "DELETE
					FROM `"._DB_PREFIX_."cartabandonment_template_color`
					WHERE id_template = " . (int)$id_template;
		$query3 = "DELETE
					FROM `"._DB_PREFIX_."cartabandonment_template_field`
					WHERE id_template = " . (int)$id_template;

		unlink('../modules/cartabandonmentpro/tpls/' . (int)$id_template . '.html');
		unlink('../modules/cartabandonmentpro/mails/' . $lang . '/' . (int)$id_template . '.html');
		return Db::getInstance()->ExecuteS($query) && Db::getInstance()->ExecuteS($query2) && Db::getInstance()->ExecuteS($query3);
	}
}