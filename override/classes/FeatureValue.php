<?php

class FeatureValue extends FeatureValueCore
{
	public static function addFeatureValueImport($id_feature, $value, $id_product = null, $id_lang = null)
	{
		$sqlValidationExistFeature = "SELECT id_feature_value
										FROM "._DB_PREFIX_."feature_value_lang
										WHERE (TRIM(REPLACE(REPLACE(value, ' ', ''), '  ', ''))) = (TRIM(REPLACE(REPLACE('".$value."', ' ', ''), '  ', '')))";
		$results = Db::getInstance()->executeS($sqlValidationExistFeature);
		if (empty($results)) 
		{
			if (!is_null($id_product) && $id_product)
			{
				$id_feature_value = Db::getInstance()->getValue('
					SELECT `id_feature_value`
					FROM '._DB_PREFIX_.'feature_product
					WHERE `id_feature` = '.(int)$id_feature.'
					AND `id_product` = '.(int)$id_product);

				if ($id_feature_value && !is_null($id_lang) && $id_lang)
				{
					Db::getInstance()->execute('
					UPDATE '._DB_PREFIX_.'feature_value_lang 
					SET `value` = \''.pSQL($value).'\' 
					WHERE `id_feature_value` = '.(int)$id_feature_value.' 
					AND `id_lang` = '.(int)$id_lang);
				}
			}
			else
			{
				$id_feature_value = Db::getInstance()->getValue('
					SELECT fv.`id_feature_value`
					FROM '._DB_PREFIX_.'feature_value fv
					LEFT JOIN '._DB_PREFIX_.'feature_value_lang fvl ON (fvl.`id_feature_value` = fv.`id_feature_value`)
					WHERE `value` = \''.pSQL($value).'\'
					AND fv.`id_feature` = '.(int)$id_feature.'
					GROUP BY fv.`id_feature_value`');
			}

			if ($id_feature_value)
			{
				return (int)$id_feature_value;
			}

			// Feature doesn't exist, create it
			$feature_value = new FeatureValue();
			$feature_value->id_feature = (int)$id_feature;
			$feature_value->custom = 0;
			foreach (Language::getLanguages() as $language)
			{
				$feature_value->value[$language['id_lang']] = $value;
			}
			$feature_value->add();

			return (int)$feature_value->id;
		} else {
			return (int)$results[0]['id_feature_value'];
		}
	}
}
