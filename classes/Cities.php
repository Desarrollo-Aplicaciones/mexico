<?php
/*
* 2007-2013 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class CitiesCore extends ObjectModel
{
	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'cities_col',
		'primary' => 'id_city',
		'fields' => array(
			'id_country' => array('type' => self::TYPE_INT,  'required' => true, 'size' => 12),
			'id_state' => 	array('type' => self::TYPE_INT,  'required' => true, 'size' => 12),
			'city_name' => array('type' => self::TYPE_STRING),
		),
	);

	public function __construct($id = null, $alias = null, $search = null, $id_lang = null)
	{
		
	}


	public function getCities($contexto)
	{
/*

echo "<pre>";
print_r(Context::getContext()->controller);
echo "</pre>";
*/

		$ciudades = array();
		$cities = Db::getInstance()->executeS('
		SELECT id_city, city_name
		FROM `'._DB_PREFIX_.'cities_col` a
		WHERE `id_country` = \''.Configuration::get('PS_COUNTRY_DEFAULT').'\'');

		foreach ($cities as $row)
			$ciudades[$row['id_city']] = $row;

		return $ciudades;
	}

	/**
	 * This method is allow to know if a feature is used or active
	 * @since 1.5.0.1
	 * @return bool
	 */
	public static function isFeatureActive()
	{
		return Configuration::get('PS_ALIAS_FEATURE_ACTIVE');
	}
}

