<?php

class Search extends SearchCore
{
	public static function find($id_lang, $expr, $page_number = 1, $page_size = 1, $order_by = 'position',
	$order_way = 'desc', $ajax = false, $use_cookie = FALSE, Context $context = null)
	{
      
		if (!$context)
			$context = Context::getContext();
		$db = Db::getInstance(_PS_USE_SQL_SLAVE_);

		// Only use cookie if id_customer is not present
		if ($use_cookie)
			$id_customer = $context->customer->id;
		else
			$id_customer = 0;

		// TODO : smart page management
		if ($page_number < 1) $page_number = 1;
		if ($page_size < 1) $page_size = 1;

		if (!Validate::isOrderBy($order_by) || !Validate::isOrderWay($order_way))
			return false;

		$intersect_array = array();
		$score_array = array();
		$words = explode(' ', Search::sanitize($expr, $id_lang));

		foreach ($words as $key => $word)
			if (!empty($word) && strlen($word) >= (int)Configuration::get('PS_SEARCH_MINWORDLEN'))
			{
				if ($word[0] != '-')
					$score_array[] = 'sw.word LIKE \''.pSQL(Tools::substr($word, 0, PS_SEARCH_MAX_WORD_LENGTH)).'%\'';
			}
			else
				 unset($words[$key]);

		if (!count($words))
			return ($ajax ? array() : array('total' => 0, 'result' => array()));

		$score = '';
		if (count($score_array))
			$score = ',(
				SELECT SUM(weight)
				FROM '._DB_PREFIX_.'search_word sw
				LEFT JOIN '._DB_PREFIX_.'search_index si ON sw.id_word = si.id_word
				WHERE sw.id_lang = '.(int)$id_lang.'
					AND sw.id_shop = '.$context->shop->id.'
					AND si.id_product = p.id_product
					AND ('.implode(' OR ', $score_array).')
			) position';
                
                
		if ($ajax)
		{
			$sql = 'SELECT DISTINCT p.id_product, CONCAT(pl.name,\' $\', ROUND(product_shop.price * (1+ (t.rate / 100)))) pname, cl.name cname,
						cl.link_rewrite crewrite, pl.link_rewrite prewrite '.$score.'
					FROM '._DB_PREFIX_.'product p
					INNER JOIN `'._DB_PREFIX_.'product_lang` pl ON (
						p.`id_product` = pl.`id_product`
						AND pl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('pl').'
					)
					'.Shop::addSqlAssociation('product', 'p').'
					INNER JOIN `'._DB_PREFIX_.'category_lang` cl ON (
						product_shop.`id_category_default` = cl.`id_category`
						AND cl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('cl').'
					)
					INNER JOIN `'._DB_PREFIX_.'tax_rule` tr ON (product_shop.id_tax_rules_group = tr.id_tax_rules_group)
					INNER JOIN `'._DB_PREFIX_.'tax` t ON (t.id_tax = tr.id_tax)
					WHERE p.`id_product` '.$product_pool.'
					ORDER BY position DESC LIMIT 10';
			return $db->executeS($sql);
		}   
                

		if (strpos($order_by, '.') > 0)
		{
			$order_by = explode('.', $order_by);
			$order_by = pSQL($order_by[0]).'.`'.pSQL($order_by[1]).'`';
		}
		$alias = '';
		if ($order_by == 'price')
			$alias = 'product_shop.';

		/*if( $order_by == 'position') { // mejorar busqueda rapida no toma en cuenta el peso de la palabra a buscar en el producto
			$score = '';
			$order_by = "pl.`name`";
			$order_way = "ASC";
		}*/
                

		$sql = 'SELECT p.*, product_shop.*, stock.out_of_stock, IFNULL(stock.quantity, 0) as quantity, 
				pl.`description_short`, pl.`available_now`, pl.`available_later`, pl.`link_rewrite`, pl.`name`,
			 MAX(image_shop.`id_image`) id_image, il.`legend`, m.`name` manufacturer_name '.$score.', 
			 MAX(product_attribute_shop.`id_product_attribute`) id_product_attribute,
				DATEDIFF(
					p.`date_add`,
					DATE_SUB(
						NOW(),
						INTERVAL '.(Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ? Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20).' DAY
					)
				) > 0 new
				FROM '._DB_PREFIX_.'product p
				'.Shop::addSqlAssociation('product', 'p').'
				INNER JOIN `'._DB_PREFIX_.'product_lang` pl ON (
					p.`id_product` = pl.`id_product`
					AND pl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('pl').'
				)
				LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa	ON (p.`id_product` = pa.`id_product`)
				'.Shop::addSqlAssociation('product_attribute', 'pa', false, 'product_attribute_shop.`default_on` = 1').'
				'.Product::sqlStock('p', 'product_attribute_shop', false, $context->shop).'
				LEFT JOIN `'._DB_PREFIX_.'manufacturer` m ON m.`id_manufacturer` = p.`id_manufacturer`
				LEFT JOIN `'._DB_PREFIX_.'image` i ON (i.`id_product` = p.`id_product`)'.
				Shop::addSqlAssociation('image', 'i', false, 'image_shop.cover=1').'
				LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (i.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)$id_lang.')
				WHERE p.`id_product` IN 
                                
(select t1.id_product from
(
       SELECT si.id_product
					FROM ps_search_word sw
					LEFT JOIN ps_search_index si ON sw.id_word = si.id_word
					WHERE sw.id_lang = 1
						AND sw.id_shop = 1';

        for ($i=0; $i < count($words); $i++) {

            if (strlen($words[$i])>=3) {
            $sql.=" AND sw.word LIKE '%" . str_replace('%', '\\%', $words[$i]) . "%' "; 
            }
        }

$sql .='
    
GROUP BY si.id_product
			
) as t1

INNER JOIN
(

SELECT cp.`id_product`
				FROM `'._DB_PREFIX_.'category_group` cg
				INNER JOIN `'._DB_PREFIX_.'category_product` cp ON cp.`id_category` = cg.`id_category`
				INNER JOIN `'._DB_PREFIX_.'category` c ON cp.`id_category` = c.`id_category`
				INNER JOIN `'._DB_PREFIX_.'product` p ON cp.`id_product` = p.`id_product`
				'.Shop::addSqlAssociation('product', 'p', false).'
				WHERE c.`active` = 1
					AND product_shop.`active` = 1
					AND product_shop.`visibility` IN ("both", "search")
					AND product_shop.indexed = 1
					AND cg.`id_group` '.(!$id_customer ?  '= 1' : 'IN (
						SELECT id_group FROM '._DB_PREFIX_.'customer_group
						WHERE id_customer = '.(int)$id_customer.'
					 ) ').'
                  GROUP BY cp.`id_product` 

) as t2
ON(t1.id_product=t2.id_product))

				GROUP BY product_shop.id_product
				'.($order_by ? 'ORDER BY  '.$alias.$order_by : '').($order_way ? ' '.$order_way : '').'
				LIMIT '.(int)(($page_number - 1) * $page_size).','.(int)$page_size;
//echo '<pre>';
//print_r($sql);

		$result = $db->executeS($sql);

		$sql = 'SELECT COUNT(*)
				FROM '._DB_PREFIX_.'product p
				'.Shop::addSqlAssociation('product', 'p').'
				INNER JOIN `'._DB_PREFIX_.'product_lang` pl ON (
					p.`id_product` = pl.`id_product`
					AND pl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('pl').'
				)
				LEFT JOIN `'._DB_PREFIX_.'manufacturer` m ON m.`id_manufacturer` = p.`id_manufacturer`
				WHERE p.`id_product` IN 
                                
(select t1.id_product from
(
       SELECT si.id_product
					FROM ps_search_word sw
					LEFT JOIN ps_search_index si ON sw.id_word = si.id_word
					WHERE sw.id_lang = 1
						AND sw.id_shop = 1';

        for ($i=0; $i < count($words); $i++) {

            if ( strlen($words[$i])>=3 ) {
                
            $sql.=" AND sw.word LIKE '%" . str_replace('%', '\\%', $words[$i]) . "%' ";
            
            }
        }

$sql .='
 GROUP BY si.id_product
			
) as t1

INNER JOIN
(

SELECT cp.`id_product`
				FROM `'._DB_PREFIX_.'category_group` cg
				INNER JOIN `'._DB_PREFIX_.'category_product` cp ON cp.`id_category` = cg.`id_category`
				INNER JOIN `'._DB_PREFIX_.'category` c ON cp.`id_category` = c.`id_category`
				INNER JOIN `'._DB_PREFIX_.'product` p ON cp.`id_product` = p.`id_product`
				'.Shop::addSqlAssociation('product', 'p', false).'
				WHERE c.`active` = 1
					AND product_shop.`active` = 1
					AND product_shop.`visibility` IN ("both", "search")
					AND product_shop.indexed = 1
					AND cg.`id_group` '.(!$id_customer ?  '= 1' : 'IN (
						SELECT id_group FROM '._DB_PREFIX_.'customer_group
						WHERE id_customer = '.(int)$id_customer.'
					 ) ').'
             GROUP BY cp.`id_product`                                

) as t2
ON(t1.id_product=t2.id_product))
                                

';

		$total = $db->getValue($sql);

		if (!$result)
			$result_properties = false;
		else
			$result_properties = Product::getProductsProperties((int)$id_lang, $result);
                

		return array('total' => $total,'result' => $result_properties);
	}
        

	public static function findWsMobile($id_lang, $expr, $page_number = 1, $page_size = 1, $order_by = 'position',
	$order_way = 'desc', $ajax = false, $use_cookie = true, Context $context = null)
	{
		if (!$context)
			$context = Context::getContext();
		$db = Db::getInstance(_PS_USE_SQL_SLAVE_);

		// Only use cookie if id_customer is not present
		if ($use_cookie)
			$id_customer = $context->customer->id;
		else
			$id_customer = 0;

		// TODO : smart page management
		if ($page_number < 1) $page_number = 1;
		if ($page_size < 1) $page_size = 1;

//		if (!Validate::isOrderBy($order_by) || !Validate::isOrderWay($order_way)){
//                    return 'xd';
//			return false;
//                }

		$intersect_array = array();
		$score_array = array();
		$words = explode(' ', Search::sanitize($expr, $id_lang));

		foreach ($words as $key => $word)
			if (!empty($word) && strlen($word) >= (int)Configuration::get('PS_SEARCH_MINWORDLEN'))
			{
				if ($word[0] != '-')
					$score_array[] = 'sw.word LIKE \''.pSQL(Tools::substr($word, 0, PS_SEARCH_MAX_WORD_LENGTH)).'%\'';
			}
			else
				 unset($words[$key]);

		if (!count($words))
			return ($ajax ? array() : array('total' => 0, 'result' => array()));

		$score = '';
		if (count($score_array))
			$score = ',(
				SELECT SUM(weight)
				FROM '._DB_PREFIX_.'search_word sw
				LEFT JOIN '._DB_PREFIX_.'search_index si ON sw.id_word = si.id_word
				WHERE sw.id_lang = '.(int)$id_lang.'
					AND sw.id_shop = '.$context->shop->id.'
					AND si.id_product = p.id_product
					AND ('.implode(' OR ', $score_array).')
			) position';
                
                
	        

		if (strpos($order_by, '.') > 0)
		{
			$order_by = explode('.', $order_by);
			$order_by = pSQL($order_by[0]).'.`'.pSQL($order_by[1]).'`';
		}
		$alias = '';
		if ($order_by == 'price')
			$alias = 'product_shop.';

		/*if( $order_by == 'position') { // mejorar busqueda rapida no toma en cuenta el peso de la palabra a buscar en el producto
			$score = '';
			$order_by = "pl.`name`";
			$order_way = "ASC";
		}*/
                /*
                 * select prod.id_product , prod.price, prod.reference, prodl.`name`,prodl.description , prodl.description_short , CONCAT(GROUP_CONCAT(cat_prodl.meta_title SEPARATOR \"|\")) AS categorias,
CONCAT(GROUP_CONCAT(cat_prodl.id_category SEPARATOR \"|\")) AS ids_categorias 
                 */

		$sql = 'SELECT p.id_product,p.price, p.reference,pl.`name`, pl.description, pl.description_short,
                         CONCAT(GROUP_CONCAT(cat_prodl.meta_title SEPARATOR "|")) AS categorias,
                            CONCAT(GROUP_CONCAT(cat_prodl.id_category SEPARATOR "|")) AS ids_categorias '.$score.' 
                    

		
				FROM '._DB_PREFIX_.'product p
				'.Shop::addSqlAssociation('product', 'p').'
                                INNER JOIN ps_category_product cat_prod ON (cat_prod.id_product=p.id_product)
                                INNER JOIN ps_category_lang cat_prodl ON (cat_prod.id_category=cat_prodl.id_category) 
				INNER JOIN `'._DB_PREFIX_.'product_lang` pl ON (
					p.`id_product` = pl.`id_product`
					AND pl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('pl').'
				)
				LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa	ON (p.`id_product` = pa.`id_product`)
				'.Shop::addSqlAssociation('product_attribute', 'pa', false, 'product_attribute_shop.`default_on` = 1').'
				'.Product::sqlStock('p', 'product_attribute_shop', false, $context->shop).'
				LEFT JOIN `'._DB_PREFIX_.'manufacturer` m ON m.`id_manufacturer` = p.`id_manufacturer`
				LEFT JOIN `'._DB_PREFIX_.'image` i ON (i.`id_product` = p.`id_product`)'.
				Shop::addSqlAssociation('image', 'i', false, 'image_shop.cover=1').'
				LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (i.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)$id_lang.')
				WHERE p.`id_product` IN 
                                
(select t1.id_product from
(
       SELECT si.id_product
					FROM ps_search_word sw
					LEFT JOIN ps_search_index si ON sw.id_word = si.id_word
					WHERE sw.id_lang = 1
						AND sw.id_shop = 1';

        for ($i=0; $i < count($words); $i++) {

            if (strlen($words[$i])>=3) {
            $sql.=" AND sw.word LIKE '%" . str_replace('%', '\\%', $words[$i]) . "%' "; 
            }
        }

$sql .='
    
GROUP BY si.id_product
			
) as t1

INNER JOIN
(

SELECT cp.`id_product`
				FROM `'._DB_PREFIX_.'category_group` cg
				INNER JOIN `'._DB_PREFIX_.'category_product` cp ON cp.`id_category` = cg.`id_category`
				INNER JOIN `'._DB_PREFIX_.'category` c ON cp.`id_category` = c.`id_category`
				INNER JOIN `'._DB_PREFIX_.'product` p ON cp.`id_product` = p.`id_product`
				'.Shop::addSqlAssociation('product', 'p', false).'
				WHERE c.`active` = 1
					AND product_shop.`active` = 1
					AND product_shop.`visibility` IN ("both", "search")
					AND product_shop.indexed = 1
					AND cg.`id_group` '.(!$id_customer ?  '= 1' : 'IN (
						SELECT id_group FROM '._DB_PREFIX_.'customer_group
						WHERE id_customer = '.(int)$id_customer.'
					 ) ').'
                  GROUP BY cp.`id_product` 

) as t2
ON(t1.id_product=t2.id_product))

				GROUP BY product_shop.id_product
				'.($order_by ? 'ORDER BY  '.$alias.$order_by : '').($order_way ? ' '.$order_way : '').'
			LIMIT '.(int)(($page_number - 1) * $page_size).','.(int)$page_size;

		$result = $db->executeS($sql);

		$sql = 'SELECT COUNT(*)
				FROM '._DB_PREFIX_.'product p
				'.Shop::addSqlAssociation('product', 'p').'
				INNER JOIN `'._DB_PREFIX_.'product_lang` pl ON (
					p.`id_product` = pl.`id_product`
					AND pl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('pl').'
				)
				LEFT JOIN `'._DB_PREFIX_.'manufacturer` m ON m.`id_manufacturer` = p.`id_manufacturer`
				WHERE p.`id_product` IN 
                                
(select t1.id_product from
(
       SELECT si.id_product
					FROM ps_search_word sw
					LEFT JOIN ps_search_index si ON sw.id_word = si.id_word
					WHERE sw.id_lang = 1
						AND sw.id_shop = 1';

        for ($i=0; $i < count($words); $i++) {

            if ( strlen($words[$i])>=3 ) {
                
            $sql.=" AND sw.word LIKE '%" . str_replace('%', '\\%', $words[$i]) . "%' ";
            
            }
        }

$sql .='
 GROUP BY si.id_product
			
) as t1

INNER JOIN
(

SELECT cp.`id_product`
				FROM `'._DB_PREFIX_.'category_group` cg
				INNER JOIN `'._DB_PREFIX_.'category_product` cp ON cp.`id_category` = cg.`id_category`
				INNER JOIN `'._DB_PREFIX_.'category` c ON cp.`id_category` = c.`id_category`
				INNER JOIN `'._DB_PREFIX_.'product` p ON cp.`id_product` = p.`id_product`
				'.Shop::addSqlAssociation('product', 'p', false).'
				WHERE c.`active` = 1
					AND product_shop.`active` = 1
					AND product_shop.`visibility` IN ("both", "search")
					AND product_shop.indexed = 1
					AND cg.`id_group` '.(!$id_customer ?  '= 1' : 'IN (
						SELECT id_group FROM '._DB_PREFIX_.'customer_group
						WHERE id_customer = '.(int)$id_customer.'
					 ) ').'
             GROUP BY cp.`id_product`                                

) as t2
ON(t1.id_product=t2.id_product))
                                

';

		$total = $db->getValue($sql);

		if (!$result)
			$result_properties = false;
		else
	
		return array('total' => $total,'result' => $result);
	}      
        
}