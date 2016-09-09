<?php

class Search extends SearchCore
{

public static function find($id_lang, $expr, $page_number = 1, $page_size = 1, $order_by = 'position2',
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

		if (!Validate::isOrderBy($order_by) || !Validate::isOrderWay($order_way))
			return false;

		$intersect_array = array();
		$score_array = array();
		$words = explode(' ', Search::sanitize($expr, $id_lang));

		foreach ($words as $key => $word)
			if (!empty($word) && strlen($word) >= (int)Configuration::get('PS_SEARCH_MINWORDLEN'))
			{
				$word = str_replace('%', '\\%', $word);
				$word = str_replace('_', '\\_', $word);

				if ($word[0] != '-')
					$score_array[] = " ( 100 - ( ( levenshtein(sw.word, '".pSQL(Tools::substr($word, 0, PS_SEARCH_MAX_WORD_LENGTH))."') ) * 100)/ length(sw.word) ) >= 70 ";
				
				
			}
			else
				unset($words[$key]);

		if (!count($words))
			return ($ajax ? array() : array('total' => 0, 'result' => array()));

		$product_pool = '';
		$product_pool = ((strpos($product_pool, ',') === false) ? (' = '.(int)$product_pool.' ') : (' IN ('.rtrim($product_pool, ',').') '));

		$conn_open = 0;

		if ($ajax)
		{

			$conn_string = "host=10.0.1.111 port=5432 dbname=farmalisto_mexico user=search password=search123";
			$dbconn4 = pg_pconnect($conn_string);
			$conn_open = 1;

			$query_psql = " SELECT si.id_product, SUM(si.weight) AS peso FROM
				ps_search_index si
				INNER JOIN ps_search_word sw ON ( sw.id_word = si.id_word )
				WHERE 
					( ".implode(' OR ', $score_array)."
					)
				GROUP BY si.id_product
				ORDER BY peso DESC";
			$alenum = 0;
			if ( $resultado = pg_exec($query_psql)) {
				
					$alenum = rand(10,50);
					$sql_create = 'CREATE TEMPORARY TABLE tmp_search_'.$alenum.' (
						id_asc  int NOT NULL AUTO_INCREMENT ,
						id_product  int NULL ,
						peso  int NULL ,
						PRIMARY KEY (`id_asc`),
						INDEX indx_pr_search_'.$alenum.' (id_product) USING BTREE
						)';
					
					$result_crear = $db->execute($sql_create);

					$ins_prods_buscar = " INSERT INTO tmp_search_".$alenum." (id_product, peso) VALUES  ";

					while ( $fetch = pg_fetch_row($resultado)) {
						$eligible_products[$fetch['0']] = $fetch['0'];
						$ins_prods_buscar .= " (".$fetch['0'].", ".$fetch['1']."),";
			        }

			        if ($conn_open == 1) {
				    	pg_close($dbconn4);
				    	$conn_open = 0;
					}
			        
			        $ins_prods_buscar = rtrim($ins_prods_buscar, ",");

			        if ( !$ret_insert = $db->execute($ins_prods_buscar) ) {
			        	//echo "<br> nooooooooooooo";
			        	return ($ajax ? array() : array('total' => 0, 'result' => array()));
			        } else {
			        	//echo "<br> siiiiiiiiiiii";
			        }
		        
		    }

		    if ($conn_open == 1) {
		    	pg_close($dbconn4);
		    	$conn_open = 0;
			}

			$sql = "SELECT DISTINCT p.id_product, pl.name pname, cl.name cname, cl.link_rewrite crewrite, pl.link_rewrite prewrite, position2.peso
				FROM ps_product p
				INNER JOIN ps_product_lang pl ON (p.active = 1 AND p.id_product = pl.id_product AND pl.id_lang = 1 AND pl.id_shop = 1  )
				INNER JOIN ps_product_shop product_shop ON ( product_shop.active = 1 AND product_shop.visibility IN ('both', 'search')
									AND product_shop.indexed = 1 AND product_shop.id_product = p.id_product AND product_shop.id_shop = 1 )
				INNER JOIN ps_category_lang cl ON ( product_shop.id_category_default = cl.id_category AND cl.id_lang = 1 AND cl.id_shop = 1  )
				INNER JOIN tmp_search_".$alenum." position2 ON ( position2.id_product = p.id_product)
				ORDER BY position2.peso DESC
				LIMIT 10";

				if ($alenum != 0 && $result = $db->executeS($sql)) {
					//echo "<br> 222222 siiiiiiii";
					return $result;
				} else {
					//echo "<br> 222222 Noooooooo";
					return ($ajax ? array() : array('total' => 0, 'result' => array()));
				}			
		}

		if (strpos($order_by, '.') > 0)
		{
			$order_by = explode('.', $order_by);
			$order_by = pSQL($order_by[0]).'.`'.pSQL($order_by[1]).'`';
		}
		$alias = '';
		if ($order_by == 'price')
			$alias = 'product_shop.';
		else if ($order_by == 'date_upd')
			$alias = 'p.';

		$conn_string = "host=10.0.1.111 port=5432 dbname=farmalisto_mexico user=search password=search123";
		$dbconn4 = pg_pconnect($conn_string);
		$conn_open = 1;

		$sql = "SELECT si.id_product, SUM(si.weight) AS peso FROM
				ps_search_index si 
				INNER JOIN ps_search_word sw ON ( sw.id_word = si.id_word )
				WHERE 
					( ".implode(' OR ', $score_array)."
					)
				GROUP BY si.id_product
				ORDER BY peso DESC";
			$eligible_products = array();
			$ret_insert = 0;
			$orden_bus = '';
			if ( $resultado = pg_exec($sql)) {
				$alenum = rand(10,50);
				$sql_create = 'CREATE TEMPORARY TABLE tmp_search_'.$alenum.' (
					id_asc  int NOT NULL AUTO_INCREMENT ,
					id_product  int NULL ,
					id_peso  int NULL ,
					PRIMARY KEY (`id_asc`),
					INDEX indx_pr_search_'.$alenum.' (id_product) USING BTREE
					)';
				
				$result_crear = $db->execute($sql_create);

				$ins_prods_buscar = " INSERT INTO tmp_search_".$alenum." (id_product, id_peso) VALUES  ";

				while ( $fetch = pg_fetch_row($resultado)) {
					$eligible_products[$fetch['0']] = $fetch['0'];
					$ins_prods_buscar .= " (".$fetch['0'].", ".$fetch['1']."),";
				}	
				
				if ($conn_open == 1) {
			    	pg_close($dbconn4);
			    	$conn_open = 0;
				}

				$ins_prods_buscar = rtrim($ins_prods_buscar, ",");
				
				if ( $ret_insert = $db->execute($ins_prods_buscar) ) {
					$orden_bus = " ORDER BY bt.id_asc ASC ";
				} else {
					$ret_insert = 0;
					$orden_bus = ($order_by ? 'ORDER BY  '.$alias.$order_by : '').($order_way ? ' '.$order_way : '');
				}
			}	

		if ($conn_open == 1) {
	    	pg_close($dbconn4);
	    	$conn_open = 0;
		}


		$product_pool = '';
		foreach ($eligible_products as $id_product) {
			if ($id_product) {
				$product_pool .= (int)$id_product.',';
			}
		}

		$product_pool = ((strpos($product_pool, ',') === false) ? (' = '.(int)$product_pool.' ') : (' IN ('.rtrim($product_pool, ',').') '));


		$sql = 'SELECT p.*, product_shop.*, stock.out_of_stock, IFNULL(stock.quantity, 0) as quantity, 
				pl.`description_short`, pl.`available_now`, pl.`available_later`, pl.`link_rewrite`, pl.`name`,
			 MAX(image_shop.`id_image`) id_image, il.`legend`, m.`name` manufacturer_name
				FROM '._DB_PREFIX_.'product p
				'.Shop::addSqlAssociation('product', 'p').'
				INNER JOIN `'._DB_PREFIX_.'product_lang` pl ON (
					p.`id_product` = pl.`id_product`
					AND pl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('pl').'
				)				
				'.Product::sqlStock('p', '', false, $context->shop).'
				LEFT JOIN `'._DB_PREFIX_.'manufacturer` m ON m.`id_manufacturer` = p.`id_manufacturer`
				LEFT JOIN `'._DB_PREFIX_.'image` i ON (i.`id_product` = p.`id_product`)'.
				Shop::addSqlAssociation('image', 'i', false, 'image_shop.cover=1').'
				LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (i.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)$id_lang.')'.
				($ret_insert ? ' INNER JOIN tmp_search_'.$alenum.' bt ON ( bt.id_product = p.id_product )' : '').'
				WHERE p.active = 1 AND product_shop.active = 1 AND p.`id_product` '.$product_pool.'
				GROUP BY product_shop.id_product
				'.$orden_bus.'			
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
				WHERE p.`id_product` '.$product_pool;
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
		$conn_open = 0;
		$intersect_array = array();
		$score_array = array();
		$words = explode(' ', Search::sanitize($expr, $id_lang));

		foreach ($words as $key => $word)
			if (!empty($word) && strlen($word) >= (int)Configuration::get('PS_SEARCH_MINWORDLEN'))
			{
				if ($word[0] != '-')
					$score_array[] = " ( 100 - ( ( levenshtein(sw.word, '".pSQL(Tools::substr($word, 0, PS_SEARCH_MAX_WORD_LENGTH))."') ) * 100)/ length(sw.word) ) >= 70 ";
				
				
			}
			else
				unset($words[$key]);

		if (!count($words))
			return ($ajax ? array() : array('total' => 0, 'result' => array()));


		if (strpos($order_by, '.') > 0)
		{
			$order_by = explode('.', $order_by);
			$order_by = pSQL($order_by[0]).'.`'.pSQL($order_by[1]).'`';
		}
		$alias = '';
		if ($order_by == 'price')
			$alias = 'product_shop.';


		$conn_string = "host=10.0.1.111 port=5432 dbname=farmalisto_mexico user=search password=search123";
		$dbconn4 = pg_pconnect($conn_string);
		$conn_open = 1;

		$sql = "SELECT si.id_product, SUM(si.weight) AS peso FROM
				ps_search_index si 
				INNER JOIN ps_search_word sw ON ( sw.id_word = si.id_word )
				WHERE 
					( ".implode(' OR ', $score_array)."
					)
				GROUP BY si.id_product
				ORDER BY peso DESC";
			$eligible_products = array();
			$ret_insert = 0;
			$orden_bus = '';
			if ( $resultado = pg_exec($sql)) {

				$alenum = rand(10,50);
				$sql_create = 'CREATE TEMPORARY TABLE tmp_search_'.$alenum.' (
					id_asc  int NOT NULL AUTO_INCREMENT ,
					id_product  int NULL ,
					id_peso  int NULL ,
					PRIMARY KEY (`id_asc`),
					INDEX indx_pr_search_'.$alenum.' (id_product) USING BTREE
					)';
				
				$result_crear = $db->execute($sql_create);

				$ins_prods_buscar = " INSERT INTO tmp_search_".$alenum." (id_product, id_peso) VALUES  ";

				while ( $fetch = pg_fetch_row($resultado)) {
					$eligible_products[$fetch['0']] = $fetch['0'];
					$ins_prods_buscar .= " (".$fetch['0'].", ".$fetch['1']."),";
				}	

				if ($conn_open == 1) {
			    	pg_close($dbconn4);
			    	$conn_open = 0;
				}

				$ins_prods_buscar = rtrim($ins_prods_buscar, ",");
				
				if ( $ret_insert = $db->execute($ins_prods_buscar) ) {
					$alias = '';
					if( $order_by == 'id_product' || $order_by == 'position' ) {
						$alias = 'position.';
						$order_by = 'id_asc';
						$order_way = ' ASC ';
					}
										
					$orden_bus = ($order_by ? 'ORDER BY  '.$alias.$order_by : '').($order_way ? ' '.$order_way : '');
				} else {
					
					$ret_insert = 0;					
				}
			}
		
		if ($conn_open == 1) {
	    	pg_close($dbconn4);
	    	$conn_open = 0;
		}

		$product_pool = '';
		foreach ($eligible_products as $id_product) {
			if ($id_product) {
				$product_pool .= (int)$id_product.',';
			}
		}

		$product_pool = ((strpos($product_pool, ',') === false) ? (' = '.(int)$product_pool.' ') : (' IN ('.rtrim($product_pool, ',').') '));


		$sql = 'SELECT p.id_product,p.price, p.reference,pl.`name`, pl.description, pl.description_short,
                         CONCAT(GROUP_CONCAT(cat_prodl.meta_title SEPARATOR "|")) AS categorias,
                            CONCAT(GROUP_CONCAT(cat_prodl.id_category SEPARATOR "|")) AS ids_categorias
				FROM '._DB_PREFIX_.'product p
				INNER JOIN ps_category_product cat_prod ON (cat_prod.id_product=p.id_product)
                INNER JOIN ps_category_lang cat_prodl ON (cat_prod.id_category=cat_prodl.id_category)
				'.Shop::addSqlAssociation('product', 'p').'
				INNER JOIN `'._DB_PREFIX_.'product_lang` pl ON (
					p.`id_product` = pl.`id_product`
					AND pl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('pl').'
				)				
				'.Product::sqlStock('p', '', false, $context->shop).'
				LEFT JOIN `'._DB_PREFIX_.'manufacturer` m ON m.`id_manufacturer` = p.`id_manufacturer`
				LEFT JOIN `'._DB_PREFIX_.'image` i ON (i.`id_product` = p.`id_product`)'.
				Shop::addSqlAssociation('image', 'i', false, 'image_shop.cover=1').'
				LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (i.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)$id_lang.')'.
				($ret_insert ? ' INNER JOIN tmp_search_'.$alenum.' position ON ( position.id_product = p.id_product )' : '').'
				WHERE p.active = 1 AND product_shop.active = 1 AND p.`id_product` '.$product_pool.'
				GROUP BY product_shop.id_product
				'.$orden_bus.'			
				LIMIT '.(int)(($page_number - 1) * $page_size).','.(int)$page_size;
// echo json_encode($sql);
// exit;

		$result = $db->executeS($sql);

		$sql = 'SELECT COUNT(*)
				FROM '._DB_PREFIX_.'product p
				'.Shop::addSqlAssociation('product', 'p').'
				INNER JOIN `'._DB_PREFIX_.'product_lang` pl ON (
					p.`id_product` = pl.`id_product`
					AND pl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('pl').'
				)
				LEFT JOIN `'._DB_PREFIX_.'manufacturer` m ON m.`id_manufacturer` = p.`id_manufacturer`
				WHERE p.`id_product` '.$product_pool;
		$total = $db->getValue($sql);

		$total = $db->getValue($sql);

		if (!$result)
			$result_properties = false;
		else
	
		return array('total' => $total,'result' => $result);
	}      
        
}