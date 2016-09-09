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

class Manufacturer extends ManufacturerCore
{
	/**
	  * Return manufacturers
	  *
	  * @param boolean $get_nb_products [optional] return products numbers for each
	  * @return array Manufacturers
	  */
		public static function getManufacturers($get_nb_products = false, $id_lang = 0, $active = true, $p = false,
		$n = false, $all_group = false, $manufacturers_sel = array())
	{	

		if (!$id_lang)
			$id_lang = (int)Configuration::get('PS_LANG_DEFAULT');

		$sql = 'SELECT m.*, ml.`description`, ml.`short_description`
			FROM `'._DB_PREFIX_.'manufacturer` m
			LEFT JOIN `'._DB_PREFIX_.'manufacturer_lang` ml ON (
				m.`id_manufacturer` = ml.`id_manufacturer`
				AND ml.`id_lang` = '.(int)$id_lang.'
			)
			'.Shop::addSqlAssociation('manufacturer', 'm');
			if ($active)
				$sql .= '
			WHERE m.`active` = 1';

			if (count($manufacturers_sel) > 0) {  // para mostrar solo manufacturers seleccionados
				$sql .= ' AND m.id_manufacturer in ('.implode(',', $manufacturers_sel).')';	
			}

			$sql .= '
			GROUP BY m.id_manufacturer
			ORDER BY m.`name` ASC'.
			($p ? ' LIMIT '.(((int)$p - 1) * (int)$n).','.(int)$n : '');

		$manufacturers = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
		if ($manufacturers === false)
			return false;

		if ($get_nb_products)
		{
			$sql_groups = '';
			if (!$all_group)
			{
				$groups = FrontController::getCurrentCustomerGroups();
				$sql_groups = (count($groups) ? 'IN ('.implode(',', $groups).')' : '= 1');
			}

			foreach ($manufacturers as $key => $manufacturer)
			{
				$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
					'SELECT p.`id_product`
					FROM `'._DB_PREFIX_.'product` p
					'.Shop::addSqlAssociation('product', 'p').'
					LEFT JOIN `'._DB_PREFIX_.'manufacturer` as m ON (m.`id_manufacturer`= p.`id_manufacturer`)
					WHERE m.`id_manufacturer` = '.(int)$manufacturer['id_manufacturer'].
					($active ? ' AND product_shop.`active` = 1 ' : '').
					' AND product_shop.`visibility` NOT IN ("none")'.
					($all_group ? '' : ' AND p.`id_product` IN (
						SELECT cp.`id_product`
						FROM `'._DB_PREFIX_.'category_group` cg
						LEFT JOIN `'._DB_PREFIX_.'category_product` cp ON (cp.`id_category` = cg.`id_category`)
						WHERE cg.`id_group` '.$sql_groups.'
					)')
				);

				$manufacturers[$key]['nb_products'] = count($result);
			}
		}

		$total_manufacturers = count($manufacturers);
		$rewrite_settings = (int)Configuration::get('PS_REWRITING_SETTINGS');

		for ($i = 0; $i < $total_manufacturers; $i++)
			if ($rewrite_settings)
				$manufacturers[$i]['link_rewrite'] = Tools::link_rewrite($manufacturers[$i]['name']);
			else
				$manufacturers[$i]['link_rewrite'] = 0;

		return $manufacturers;
	}
}
