<?php  
  
class Product extends ProductCore {  

  
	/**
	* Admin panel product search
	*
	* @param integer $id_lang Language id
	* @param string $query Search query
	* @return array Matching products
	*/
	public static function searchByName($id_lang, $query, Context $context = null)
	{
		if (!$context)
			$context = Context::getContext();

		$sql = new DbQuery();
		$sql->select('p.`id_product`, pl.`name`, p.`active`, p.`reference`, m.`name` AS manufacturer_name, stock.`quantity`, product_shop.advanced_stock_management, p.`customizable`,  ROUND(sod.unit_price_te) AS unit_price_te, ROUND(ps.wholesale_price) AS wholesale_price');
		$sql->from('category_product', 'cp');
		$sql->leftJoin('product', 'p', 'p.`id_product` = cp.`id_product`');
		$sql->join(Shop::addSqlAssociation('product', 'p'));
                
                $sql->innerJoin('product_shop', 'ps', 'ps.`id_product`  =  p.`id_product`');
                $sql->innerJoin('supply_order_detail', 'sod', 'sod.`id_product` = ps.`id_product`');
                $sql->leftJoin('product_black_list', 'prod_black', 'prod_black.`reference`  =  p.`reference`');
		$sql->leftJoin('product_lang', 'pl', '
			p.`id_product` = pl.`id_product`
			AND pl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('pl')
		);
		$sql->leftJoin('manufacturer', 'm', 'm.`id_manufacturer` = p.`id_manufacturer`');

		$where = ' p.active=1 AND ps.active=1 AND  prod_black.`reference` IS NULL AND  (pl.`name` LIKE \'%'.pSQL($query).'%\'
		OR p.`reference` LIKE \'%'.pSQL($query).'%\'
		OR p.`supplier_reference` LIKE \'%'.pSQL($query).'%\'
		OR  p.`id_product` IN (SELECT id_product FROM '._DB_PREFIX_.'product_supplier sp WHERE `product_supplier_reference` LIKE \'%'.pSQL($query).'%\') )';
		$sql->groupBy('`id_product`');
		$sql->orderBy('pl.`name` ASC');

		if (Combination::isFeatureActive())
		{
			$sql->leftJoin('product_attribute', 'pa', 'pa.`id_product` = p.`id_product`');
			$sql->join(Shop::addSqlAssociation('product_attribute', 'pa', false));
			$where .= ' OR pa.`reference` LIKE \'%'.pSQL($query).'%\'';
		}
		$sql->where($where);
		$sql->join(Product::sqlStock('p', 'pa', false, $context->shop));

		$result = Db::getInstance()->executeS($sql);

		if (!$result)
			return false;

		$results_array = array();
		foreach ($result as $row)
		{
			$row['price_tax_incl'] = Product::getPriceStatic($row['id_product'], true, null, 2);
			$row['price_tax_excl'] = Product::getPriceStatic($row['id_product'], false, null, 2);
			$results_array[] = $row;
		}
		return $results_array;
	}


	/**
	* Get new products
	*
	* @param integer $id_lang Language id
	* @param integer $pageNumber Start from (optional)
	* @param integer $nbProducts Number of products to return (optional)
	* @return array New products
	*/
	public static function getNewProducts($id_lang, $page_number = 0, $nb_products = 10,
		$count = false, $order_by = null, $order_way = null, Context $context = null)
	{
		if (!$context)
			$context = Context::getContext();

		$front = true;
		if (!in_array($context->controller->controller_type, array('front', 'modulefront')))
			$front = false;

		if ($page_number < 0) $page_number = 0;
		if ($nb_products < 1) $nb_products = 10;
		if (empty($order_by) || $order_by == 'position') $order_by = 'date_add';
		if (empty($order_way)) $order_way = 'DESC';
		if ($order_by == 'id_product' || $order_by == 'price' || $order_by == 'date_add'  || $order_by == 'date_upd')
			$order_by_prefix = 'p';
		else if ($order_by == 'name')
			$order_by_prefix = 'pl';
		if (!Validate::isOrderBy($order_by) || !Validate::isOrderWay($order_way))
			die(Tools::displayError());

		$groups = FrontController::getCurrentCustomerGroups();
		$sql_groups = (count($groups) ? 'IN ('.implode(',', $groups).')' : '= 1');
		if (strpos($order_by, '.') > 0)
		{
			$order_by = explode('.', $order_by);
			$order_by_prefix = $order_by[0];
			$order_by = $order_by[1];
		}
		if ($count)
		{
			$sql = 'SELECT COUNT(p.`id_product`) AS nb
					FROM `'._DB_PREFIX_.'product` p
					'.Shop::addSqlAssociation('product', 'p').'
					WHERE product_shop.`active` = 1
					AND product_shop.`date_add` > "'.date('Y-m-d', strtotime('-'.(Configuration::get('PS_NB_DAYS_NEW_PRODUCT') ? (int)Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20).' DAY')).'"
					'.($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '').'
					AND p.`id_product` IN (
						SELECT cp.`id_product`
						FROM `'._DB_PREFIX_.'category_group` cg
						LEFT JOIN `'._DB_PREFIX_.'category_product` cp ON (cp.`id_category` = cg.`id_category`)
						WHERE cg.`id_group` '.$sql_groups.'
					)';
			return (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
		}

		$sql = new DbQuery();
		$sql->select(
			'p.*, product_shop.*, stock.out_of_stock, IFNULL(stock.quantity, 0) as quantity, pl.`description`, pl.`description_short`, pl.`link_rewrite`, pl.`meta_description`,
			pl.`meta_keywords`, pl.`meta_title`, pl.`name`, MAX(image_shop.`id_image`) id_image, il.`legend`, m.`name` AS manufacturer_name,
			product_shop.`date_add` > "'.date('Y-m-d', strtotime('-'.(Configuration::get('PS_NB_DAYS_NEW_PRODUCT') ? (int)Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20).' DAY')).'" as new'
		);

		$sql->from('product', 'p');
		$sql->join(Shop::addSqlAssociation('product', 'p'));
		$sql->leftJoin('product_lang', 'pl', '
			p.`id_product` = pl.`id_product`
			AND pl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('pl')
		);
		$sql->leftJoin('image', 'i', 'i.`id_product` = p.`id_product`');
		$sql->join(Shop::addSqlAssociation('image', 'i', false, 'image_shop.cover=1'));
		$sql->leftJoin('image_lang', 'il', 'i.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)$id_lang);
		$sql->leftJoin('manufacturer', 'm', 'm.`id_manufacturer` = p.`id_manufacturer`');

		$sql->where('product_shop.`active` = 1');
		//$sql->where('product_shop.`id_product` >= 33532');
		
		if ($front)
			$sql->where('product_shop.`visibility` IN ("both", "catalog")');
		$sql->where('product_shop.`date_add` > "'.date('Y-m-d', strtotime('-'.(Configuration::get('PS_NB_DAYS_NEW_PRODUCT') ? (int)Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20).' DAY')).'"');
		/*$sql->where('p.`id_product` IN (
			SELECT cp.`id_product`
			FROM `'._DB_PREFIX_.'category_group` cg
			LEFT JOIN `'._DB_PREFIX_.'category_product` cp ON (cp.`id_category` = cg.`id_category`)
			WHERE cg.`id_group` '.$sql_groups.'
		)');*/
		$sql->groupBy('product_shop.id_product');

		$sql->orderBy((isset($order_by_prefix) ? pSQL($order_by_prefix).'.' : '').'`'.pSQL($order_by).'` '.pSQL($order_way));
		$sql->limit($nb_products, $page_number * $nb_products);

		/*if (Combination::isFeatureActive())
		{
			$sql->select('MAX(product_attribute_shop.id_product_attribute) id_product_attribute');
			$sql->leftOuterJoin('product_attribute', 'pa', 'p.`id_product` = pa.`id_product`');
			$sql->join(Shop::addSqlAssociation('product_attribute', 'pa', false, 'product_attribute_shop.default_on = 1'));
		}*/
		
		//$sql->join(Product::sqlStock('p', Combination::isFeatureActive() ? 'product_attribute_shop' : 0));
		$sql->join(Product::sqlStock('p', Combination::isFeatureActive() ? 0 : 0));

		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

		if ($order_by == 'price')
			Tools::orderbyPrice($result, $order_way);
		if (!$result)
			return false;

		$products_ids = array();
		foreach ($result as $row)
			$products_ids[] = $row['id_product'];
		// Thus you can avoid one query per product, because there will be only one query for all the products of the cart
		Product::cacheFrontFeatures($products_ids, $id_lang);

		return Product::getProductsProperties((int)$id_lang, $result);
	}
	
	/**
	 * Create JOIN query with 'stock_available' table
	 *
	 * @param string $productAlias Alias of product table
	 * @param string|int $productAttribute If string : alias of PA table ; if int : value of PA ; if null : nothing about PA
	 * @param bool $innerJoin LEFT JOIN or INNER JOIN
	 * @param Shop $shop
	 * @return string
	 */
	public static function sqlStock($product_alias, $product_attribute = null, $inner_join = false, Shop $shop = null)
	{
		$id_shop = ($shop !== null ? (int)$shop->id : null);
		$sql = (($inner_join) ? ' INNER ' : ' LEFT ').'
			JOIN '._DB_PREFIX_.'stock_available_mv stock
			ON (stock.id_product = '.pSQL($product_alias).'.id_product';

		if (!is_null($product_attribute))
		{
			if (!Combination::isFeatureActive())
				$sql .= ' AND stock.id_product_attribute = 0';
			elseif (is_numeric($product_attribute))
				$sql .= ' AND stock.id_product_attribute = '.$product_attribute;
			elseif (is_string($product_attribute))
				$sql .= ' AND stock.id_product_attribute = IFNULL(`'.bqSQL($product_attribute).'`.id_product_attribute, 0)';
		}

		$sql .= StockAvailable::addSqlShopRestriction(null, $id_shop, 'stock').' )';

		return $sql;
	}
        
        /**
	* Buscar producto por referencia
	*
	* @param integer $id_lang Language id
	* @param string $query Search query
	* @return array Matching products
	*/
	public static function searchByReference( $reference, $proveedor = null, $active = 1)
	{

		$sql = new DbQuery();
		$sql->select('p.`id_product`, p.`active`, p.`reference`, ps.advanced_stock_management');
		$sql->from('product', 'p');
		$sql->innerJoin('product_shop', 'ps', 'ps.`id_product` = p.`id_product`');

		if ( $proveedor != null) {

			$sql->innerJoin('product_supplier', 'pss', 'pss.`id_product`  =  p.`id_product`');
			$sql->innerJoin('supplier', 'sup', 'sup.`id_supplier` = pss.`id_supplier`');
			$compl_supplier = ' AND sup.name LIKE \''.pSQL($proveedor).'\' ';

		} else {
			$compl_supplier = '';
		}

        	$sql->leftJoin('product_black_list', 'prod_black', 'prod_black.`reference`  =  p.`reference`');
		
		if($active == 1) {
			$compl_active = ' p.active = 1 AND ps.active = 1 AND ';
		} else {
			$compl_active = '';
		}

		$where = $compl_active.' prod_black.`reference` IS NULL AND ( p.`reference` LIKE \''.pSQL($reference).'\' ) '.$compl_supplier;
		$sql->groupBy('`id_product`');
		$sql->orderBy('p.`id_product` ASC');

		$sql->where($where);
		
		$result = Db::getInstance()->executeS($sql);

		if (!$result)
			return false;

		return $result[0];
	}
}