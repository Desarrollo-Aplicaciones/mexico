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

class CityCore extends ObjectModel
{
	/** @var integer Country id which state belongs */
	public $id_country;

	/** @var integer State id which state belongs */
	public $id_state;

	/** @var string Name */
	public $city_name;


	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'cities_col',
		'primary' => 'id_city',
		'fields' => array(
			'id_country' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
			'id_state' => 	array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
			'city_name' => 	array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'size' => 32)
		),
	);

	protected $webserviceParameters = array(
		'fields' => array(
			'id_state' => array('xlink_resource'=> 'states'),
			'id_country' => array('xlink_resource'=> 'countries')
		),
	);

	public static function getCities()
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT *
		FROM `'._DB_PREFIX_.'cities_col`
		ORDER BY `city_name` ASC');
	}

	public static function getCitiesByState($id_state)
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT *
		FROM `'._DB_PREFIX_.'cities_col`
		WHERE id_state = '. $id_state .'
		ORDER BY `city_name` ASC');
	}

	/**
	 * Get a city name with its ID
	 *
	 * @param integer $id_city
	 * @return string City name
	 */
	public static function getNameById($id_city)
	{
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
			SELECT `city_name`
			FROM `'._DB_PREFIX_.'cities_col`
			WHERE `id_city` = '.(int)$id_city
		);

		return $result['city_name'];
	}

	/**
	* Delete a state only if is not in use
	*
	* @return boolean
	*/
	public function delete()
	{
		// Database deletion
		$result = Db::getInstance()->delete($this->def['table'], '`'.$this->def['primary'].'` = '.(int)$this->id);
		if (!$result)
			return false;
	}

    public static function getStatesByIdCountry($id_country)
    {
        if (empty($id_country))
            die(Tools::displayError());

        return Db::getInstance()->executeS('
        SELECT *
        FROM `'._DB_PREFIX_.'state` s
        WHERE s.`id_country` = '.(int)$id_country
        );
    }
}

