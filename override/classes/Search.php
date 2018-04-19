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

		if ( strlen($expr) > 80 ) {
			$expr = substr($expr, 0, 80);
		}

		$words = explode(' ', Search::sanitize($expr, $id_lang));
		$listword = '';
		$reference_load = 0;	// si la palabra cargada puede ser una referencia
		$cant_words = 0;		// cantidad de palabras colocadas en la búsqueda
		$sum_words_fibo = 0; 	// sumatoria fibonnaci de las palabras para resultados de búsqueda

		foreach ($words as $key => $word)
			if (!empty($word) && strlen($word) >= (int)Configuration::get('PS_SEARCH_MINWORDLEN'))
			{
				$word = str_replace('%', '\\%', $word);
				$word = str_replace('_', '\\_', $word);

				if ($word[0] != '-') {
					$cant_words++;
					$sum_words_fibo += $cant_words;
					$score_array[] = " ( 100 - ( ( search.levenshtein(sw.word, '".pSQL(Tools::substr($word, 0, PS_SEARCH_MAX_WORD_LENGTH))."') ) * 100)/ length(sw.word) ) >= 70 ";
					$listword .= $word.';';				
					
					if ($cant_words == 1 && is_numeric($word) && strlen($word) >= 5 ) { // si la palabra que busco es la primera palabra y es numérica
						$reference_load = 1;
						//echo "tama:".strlen($word);
						//echo "numerico:".is_numeric($word);
					}
				}
			}
			else {
				unset($words[$key]);
			}
			$listword = trim($listword,";");

		if (!count($words))
			return ($ajax ? array() : array('total' => 0, 'result' => array()));

		$product_pool = '';
		$product_pool = ((strpos($product_pool, ',') === false) ? (' = '.(int)$product_pool.' ') : (' IN ('.rtrim($product_pool, ',').') '));

		$conn_open = 0;

		if ($ajax)
		{

			$conn_string = "host=127.0.0.1 port=15432 dbname=farmalisto_colombia user=search password=search123";
			//$conn_string = "host=search.cuznbgafgkfl.us-east-1.rds.amazonaws.com port=15432 dbname=farmalisto_colombia user=bus_col password='nyT.19*xS'";
			$dbconn4 = pg_pconnect($conn_string);
			$conn_open = 1;

			/*$query_psql = "SELECT si.id_product, SUM(si.weight) AS peso, ( 100 - ( ( levenshtein(sw.word, tw.palabra) ) * 100)/ length(sw.word) ) AS porce FROM ps_search_index si
				INNER JOIN ps_search_word sw ON ( sw.id_word = si.id_word )
				INNER JOIN (SELECT row_number() OVER() AS orden, UNNEST(STRING_TO_ARRAY('".$listword."', ';')) palabra ) tw 
					ON ( ( 100 - ( ( levenshtein(sw.word, tw.palabra) ) * 100)/ length(sw.word) ) >= 70 )				
				GROUP BY ( 100 - ( ( levenshtein(sw.word, tw.palabra) ) * 100)/ length(sw.word) ), si.id_product, tw.orden
				ORDER BY ( 100 - ( ( levenshtein(sw.word, tw.palabra) ) * 100)/ length(sw.word) ) DESC, tw.orden, peso DESC";
				*/


			$query_psql = "SELECT si.id_product, SUM(si.weight) AS peso, sum( 100 - ( ( search.levenshtein(sw.word, tw.palabra) ) * 100)/ length(sw.word) ) AS porce
			FROM mex.ps_search_index si
				INNER JOIN mex.ps_search_word sw ON ( sw.id_word = si.id_word )
				INNER JOIN (SELECT *, 100 - ( row_number() OVER (PARTITION by orden1) ) AS orden
				FROM  (
					 SELECT 	row_number() OVER() AS orden1, 
													UNNEST(STRING_TO_ARRAY('".$listword."', ';')) palabra
					 		) pepe
 						) tw 
					ON ( sw.word LIKE tw.palabra || '%' OR ( 100 - ( ( search.levenshtein(sw.word, tw.palabra) ) * 100)/ length(sw.word) ) >= 70 )
				GROUP BY si.id_product 
				HAVING sum(tw.orden) > 0
				ORDER BY SUM(si.weight) DESC, sum( 100 - ( ( search.levenshtein(sw.word, tw.palabra) ) * 100)/ length(sw.word) ) DESC";
				

				//$loggin = new Registrolog();
            	//$loggin->lwrite("Search_ajax", "log_busquedas.txt", $query_psql_1);

				//$loggin = new Registrolog();
            	//$loggin->lwrite("Search_ajax", "log_busquedas.txt", $query_psql);
				/*"SELECT si.id_product, SUM(si.weight) AS peso FROM
				ps_search_index si
				INNER JOIN ps_search_word sw ON ( sw.id_word = si.id_word )
				WHERE 
					( ".implode(' OR ', $score_array)."
					)
				GROUP BY si.id_product
				ORDER BY peso DESC";*/
			$alenum = 0;
			if ( $resultado = pg_exec($query_psql)) {
				
					$alenum = rand(10,50);
					$sql_create = 'CREATE TEMPORARY TABLE tmp_search_'.$alenum.' (
						id_asc  int NOT NULL AUTO_INCREMENT ,
						id_product  int NULL ,
						peso  int NULL ,
						porc int NULL,
						PRIMARY KEY (`id_asc`),
						INDEX indx_pr_search_'.$alenum.' (id_product) USING BTREE
						)';

					//$loggin->lwrite("Search_ajax", "log_busquedas.txt", $sql_create);
					
					$result_crear = $db->execute($sql_create);

					$ins_prods_buscar = " INSERT INTO tmp_search_".$alenum." (id_product, peso, porc) VALUES  ";

					while ( $fetch = pg_fetch_row($resultado)) {
						$eligible_products[$fetch['0']] = $fetch['0'];
						$ins_prods_buscar .= " (".$fetch['0'].", ".$fetch['1'].", ".$fetch['2']."),";
			        }

			        if ($conn_open == 1) {
				    	pg_close($dbconn4);
				    	$conn_open = 0;
					}

			        $ins_prods_buscar = rtrim($ins_prods_buscar, ",");

			        //$loggin->lwrite("Search_ajax", "log_busquedas.txt", $ins_prods_buscar);

			        if ( !$ret_insert = $db->execute($ins_prods_buscar) ) {
			        	//echo "<br> nooooooooooooo";
			        	//$loggin->lwrite("Search_ajax", "log_busquedas.txt", "NOOOO inserto ");
			        	return ($ajax ? array() : array('total' => 0, 'result' => array()));
			        } else {
			        	//echo "<br> siiiiiiiiiiii";
			        	//$loggin->lwrite("Search_ajax", "log_busquedas.txt", "Siiiii inserto");
			        }
		        
		    }

			if ($conn_open == 1) {
		    	pg_close($dbconn4);
		    	$conn_open = 0;
			}

			$productBlackList = Configuration::get('PRODUCT_BLACK_LIST_SHOW');
			$sql = "SELECT DISTINCT p.id_product, pl.name pname, cl.name cname, cl.link_rewrite crewrite, pl.link_rewrite prewrite, position2.peso, IF(position2.porc = 100 ,".$reference_load.",0) AS ref_fnd, GROUP_CONCAT(DISTINCT im.id_image SEPARATOR ',') AS imgs, p.id_tax_rules_group AS taxgr, t.rate,
				CASE p.id_tax_rules_group
					WHEN 0 THEN ROUND(p.price,2)
					ELSE IF(t.rate IS NOT NULL, ROUND(p.price + (p.price * t.rate/100),2), ROUND(p.price,2) )
					END
					AS price
				FROM ps_product p
				LEFT JOIN ps_product_black_list product_black ON (product_black.id_product = p.id_product)
 				INNER JOIN ps_product_lang pl ON (p.active = IF(product_black.motivo IN (".$productBlackList."),  0,  1) AND p.id_product = pl.id_product AND pl.id_lang = 1 AND pl.id_shop = 1  )
                INNER JOIN ps_product_shop product_shop ON ( product_shop.active = IF(product_black.motivo IN (".$productBlackList."),  0,  1) AND product_shop.visibility IN ('both', 'search')					                                        
 				AND product_shop.indexed = 1 AND product_shop.id_product = p.id_product AND product_shop.id_shop = 1 )
  				/*AND product_shop.id_product = p.id_product AND product_shop.id_shop = 1 )*/
                INNER JOIN ps_category_lang cl ON ( product_shop.id_category_default = cl.id_category AND cl.id_lang = 1 AND cl.id_shop = 1  )
				INNER JOIN tmp_search_".$alenum." position2 ON ( position2.id_product = p.id_product)
				LEFT JOIN `ps_tax_rule` tr ON (product_shop.id_tax_rules_group = tr.id_tax_rules_group AND tr.id_tax != 0)
				LEFT JOIN `ps_tax` t ON (t.id_tax = tr.id_tax AND t.active = 1 AND t.deleted = 0 )
				LEFT JOIN ps_image im ON ( p.id_product = im.id_product AND im.cover = 1 )
				GROUP BY p.id_product
				ORDER BY position2.id_asc ASC				
				LIMIT 7";

				//$loggin->lwrite("Search_ajax", "log_busquedas.txt", "sql 138: ".$sql);
				
				if ($alenum != 0 && $result = $db->executeS($sql)) {
					//echo "<br> 222222 siiiiiiii";
					//$loggin->lwrite("Search_ajax", "log_busquedas.txt", "SI ENCONTROOOOOO");
					$array_full = array();
					$cantnum = 0;

					foreach ($result as $value) {						

						$array_img = array();

						$array_full[$cantnum] = $value;

						if ( $value['imgs'] != NULL ) {				
							

								$array_full[$cantnum]['imgs'] = _PS_BASE_URL_SSL_
								. __PS_BASE_URI__
								. 'img/p/'
								. Image::getImgFolderStatic($value['imgs'])
								. $value['imgs']
								. '-small_default.jpg';
							

						} else {

							$array_full[$cantnum]['imgs'] = _PS_BASE_URL_SSL_
							. __PS_BASE_URI__
							. 'img/p/'
							. 'es-default-small_default.jpg';

						}

						$cantnum++;
					}

					$result = $array_full;

					return $result;
				} else {
					//echo "<br> 222222 Noooooooo";
					//$loggin->lwrite("Search_ajax", "log_busquedas.txt", "NOOO ENCONTROOOOOO");
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

		$conn_string = "host=127.0.0.1 port=15432 dbname=farmalisto_colombia user=search password=search123";
		//$conn_string = "host=search.cuznbgafgkfl.us-east-1.rds.amazonaws.com port=5432 dbname=farmalisto_colombia user=bus_col password='nyT.19*xS'";
		$dbconn4 = pg_pconnect($conn_string);
		$conn_open = 1;

		/*$sql = "SELECT si.id_product, SUM(si.weight) AS peso, ( 100 - ( ( levenshtein(sw.word, tw.palabra) ) * 100)/ length(sw.word) ) AS porce FROM ps_search_index si
				INNER JOIN ps_search_word sw ON ( sw.id_word = si.id_word )
				INNER JOIN (SELECT row_number() OVER() AS orden, UNNEST(STRING_TO_ARRAY('".$listword."', ';')) palabra ) tw 
					ON ( ( 100 - ( ( levenshtein(sw.word, tw.palabra) ) * 100)/ length(sw.word) ) >= 70 )				
				GROUP BY ( 100 - ( ( levenshtein(sw.word, tw.palabra) ) * 100)/ length(sw.word) ), si.id_product, tw.orden
				ORDER BY ( 100 - ( ( levenshtein(sw.word, tw.palabra) ) * 100)/ length(sw.word) ) DESC, tw.orden, peso DESC";
				*/

		$sql = "SELECT si.id_product, SUM(si.weight) AS peso, sum( 100 - ( ( search.levenshtein(sw.word, tw.palabra) ) * 100)/ length(sw.word) ) AS porce
			FROM mex.ps_search_index si
				INNER JOIN mex.ps_search_word sw ON ( sw.id_word = si.id_word )
				INNER JOIN (SELECT *, 100 - ( row_number() OVER (PARTITION by orden1) ) AS orden
				FROM  (
					 SELECT 	row_number() OVER() AS orden1, 
													UNNEST(STRING_TO_ARRAY('".$listword."', ';')) palabra
					 		) pepe
 						) tw 
					ON ( sw.word LIKE tw.palabra || '%' OR ( 100 - ( ( search.levenshtein(sw.word, tw.palabra) ) * 100)/ length(sw.word) ) >= 70 )
				GROUP BY si.id_product 
				HAVING sum(tw.orden) > 0
				ORDER BY SUM(si.weight) DESC, sum( 100 - ( ( search.levenshtein(sw.word, tw.palabra) ) * 100)/ length(sw.word) ) DESC";

		

		//error_log($sql);
				/*"SELECT si.id_product, SUM(si.weight) AS peso FROM
				ps_search_index si 
				INNER JOIN ps_search_word sw ON ( sw.id_word = si.id_word )
				WHERE 
					( ".implode(' OR ', $score_array)."
					)
				GROUP BY si.id_product
				ORDER BY peso DESC";*/
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
				AND p.indexed = 1 AND p.visibility = "both"
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
					$score_array[] = " ( 100 - ( ( search.levenshtein(sw.word, '".pSQL(Tools::substr($word, 0, PS_SEARCH_MAX_WORD_LENGTH))."') ) * 100)/ length(sw.word) ) >= 70 ";
				
				
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


		$conn_string = "host=127.0.0.1 port=15432 dbname=farmalisto_colombia user=search password=search123";
		//$conn_string = "host=search.cuznbgafgkfl.us-east-1.rds.amazonaws.com port=15432 dbname=farmalisto_colombia user=bus_col password='nyT.19*xS'";
		$dbconn4 = pg_pconnect($conn_string);
		$conn_open = 1;

		$sql = "SELECT si.id_product, SUM(si.weight) AS peso FROM
				mex.ps_search_index si 
				INNER JOIN mex.ps_search_word sw ON ( sw.id_word = si.id_word )
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


		$sql = 'SELECT p.id_product, p.reference,pl.`name`, pl.description, pl.description_short,
                         CONCAT(GROUP_CONCAT(cat_prodl.meta_title SEPARATOR "|")) AS categorias,
                            CONCAT(GROUP_CONCAT(cat_prodl.id_category SEPARATOR "|")) AS ids_categorias,
                            	CASE p.id_tax_rules_group
								WHEN  0 THEN p.price
								ELSE IF(taxr.id_tax != 0,p.price + (p.price * tax.rate/100),p.price)
								END
								AS `price`, p.price AS `price_base` 
				FROM '._DB_PREFIX_.'product p
				INNER JOIN ps_category_product cat_prod ON (cat_prod.id_product=p.id_product)
				LEFT JOIN ps_tax_rule taxr ON(p.id_tax_rules_group = taxr.id_tax_rules_group)
				LEFT JOIN ps_tax tax ON(taxr.id_tax = tax.id_tax) 
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
				WHERE p.active = 1 AND product_shop.active = 1 AND p.`id_product` '.$product_pool. ' AND (taxr.id_tax != 0 OR ISNULL(taxr.id_tax))
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

	public static function findApp($id_lang, $expr, $page_number = 1, $page_size = 1, $order_by = 'position',
	$order_way = 'desc',$active = 1, $ajax = false, $use_cookie = true, Context $context = null)
	{

		// error_log("expr: ".$expr);
		// error_log("page_number: ".$page_number);
		// error_log("page_size: ".$page_size);

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

		if ( strlen($expr) > 80 ) {
			$expr = substr($expr, 0, 80);
		}

		$words = explode(' ', Search::sanitize($expr, $id_lang));
		$listword = '';
		$reference_load = 0;	// si la palabra cargada puede ser una referencia
		$cant_words = 0;		// cantidad de palabras colocadas en la búsqueda
		$sum_words_fibo = 0; 	// sumatoria fibonnaci de las palabras para resultados de búsqueda

		foreach ($words as $key => $word)
			if (!empty($word) && strlen($word) >= (int)Configuration::get('PS_SEARCH_MINWORDLEN'))
			{
				$word = str_replace('%', '\\%', $word);
				$word = str_replace('_', '\\_', $word);

				if ($word[0] != '-') {
					$cant_words++;
					$sum_words_fibo += $cant_words;
					$score_array[] = " ( 100 - ( ( search.levenshtein(sw.word, '".pSQL(Tools::substr($word, 0, PS_SEARCH_MAX_WORD_LENGTH))."') ) * 100)/ length(sw.word) ) >= 70 ";
					$listword .= $word.';';				
					
					if ($cant_words == 1 && is_numeric($word) && strlen($word) >= 5 ) { // si la palabra que busco es la primera palabra y es numérica
						$reference_load = 1;
						//echo "tama:".strlen($word);
						//echo "numerico:".is_numeric($word);
					}
				}
			}
			else {
				unset($words[$key]);
			}
			$listword = trim($listword,";");

		if (!count($words))
			return ($ajax ? array() : array('total' => 0, 'result' => array()));

		$product_pool = '';
		$product_pool = ((strpos($product_pool, ',') === false) ? (' = '.(int)$product_pool.' ') : (' IN ('.rtrim($product_pool, ',').') '));

		$conn_open = 0;

		if ($ajax)
		{

			$conn_string = "host=127.0.0.1 port=15432 dbname=farmalisto_colombia user=search password=search123";
			//$conn_string = "host=search.cuznbgafgkfl.us-east-1.rds.amazonaws.com port=15432 dbname=farmalisto_colombia user=bus_col password='nyT.19*xS'";
			$dbconn4 = pg_pconnect($conn_string);
			$conn_open = 1;

			/*$query_psql = "SELECT si.id_product, SUM(si.weight) AS peso, ( 100 - ( ( levenshtein(sw.word, tw.palabra) ) * 100)/ length(sw.word) ) AS porce FROM ps_search_index si
				INNER JOIN ps_search_word sw ON ( sw.id_word = si.id_word )
				INNER JOIN (SELECT row_number() OVER() AS orden, UNNEST(STRING_TO_ARRAY('".$listword."', ';')) palabra ) tw 
					ON ( ( 100 - ( ( levenshtein(sw.word, tw.palabra) ) * 100)/ length(sw.word) ) >= 70 )				
				GROUP BY ( 100 - ( ( levenshtein(sw.word, tw.palabra) ) * 100)/ length(sw.word) ), si.id_product, tw.orden
				ORDER BY ( 100 - ( ( levenshtein(sw.word, tw.palabra) ) * 100)/ length(sw.word) ) DESC, tw.orden, peso DESC";
				*/


			$query_psql = "SELECT si.id_product, SUM(si.weight) AS peso, sum( 100 - ( ( search.levenshtein(sw.word, tw.palabra) ) * 100)/ length(sw.word) ) AS porce
			FROM mex.ps_search_index si
				INNER JOIN mex.ps_search_word sw ON ( sw.id_word = si.id_word )
				INNER JOIN (SELECT *, 100 - ( row_number() OVER (PARTITION by orden1) ) AS orden
				FROM  (
					 SELECT 	row_number() OVER() AS orden1, 
													UNNEST(STRING_TO_ARRAY('".$listword."', ';')) palabra
					 		) pepe
 						) tw 
					ON ( sw.word LIKE tw.palabra || '%' OR ( 100 - ( ( search.levenshtein(sw.word, tw.palabra) ) * 100)/ length(sw.word) ) >= 70 )
				GROUP BY si.id_product 
				HAVING sum(tw.orden) > 0
				ORDER BY SUM(si.weight) DESC, sum( 100 - ( ( search.levenshtein(sw.word, tw.palabra) ) * 100)/ length(sw.word) ) DESC";

				//$loggin = new Registrolog();
            	//$loggin->lwrite("Search_ajax", "log_busquedas.txt", $query_psql_1);

				//$loggin = new Registrolog();
            	//$loggin->lwrite("Search_ajax", "log_busquedas.txt", $query_psql);
				/*"SELECT si.id_product, SUM(si.weight) AS peso FROM
				ps_search_index si
				INNER JOIN ps_search_word sw ON ( sw.id_word = si.id_word )
				WHERE 
					( ".implode(' OR ', $score_array)."
					)
				GROUP BY si.id_product
				ORDER BY peso DESC";*/
			$alenum = 0;
			if ( $resultado = pg_exec($query_psql)) {
				
					$alenum = rand(10,50);
					$sql_create = 'CREATE TEMPORARY TABLE tmp_search_'.$alenum.' (
						id_asc  int NOT NULL AUTO_INCREMENT ,
						id_product  int NULL ,
						peso  int NULL ,
						porc int NULL,
						PRIMARY KEY (`id_asc`),
						INDEX indx_pr_search_'.$alenum.' (id_product) USING BTREE
						)';

					//$loggin->lwrite("Search_ajax", "log_busquedas.txt", $sql_create);
					
					$result_crear = $db->execute($sql_create);

					$ins_prods_buscar = " INSERT INTO tmp_search_".$alenum." (id_product, peso, porc) VALUES  ";

					while ( $fetch = pg_fetch_row($resultado)) {
						$eligible_products[$fetch['0']] = $fetch['0'];
						$ins_prods_buscar .= " (".$fetch['0'].", ".$fetch['1'].", ".$fetch['2']."),";
			        }

			        if ($conn_open == 1) {
				    	pg_close($dbconn4);
				    	$conn_open = 0;
					}

			        $ins_prods_buscar = rtrim($ins_prods_buscar, ",");

			        //$loggin->lwrite("Search_ajax", "log_busquedas.txt", $ins_prods_buscar);

			        if ( !$ret_insert = $db->execute($ins_prods_buscar) ) {
			        	//echo "<br> nooooooooooooo";
			        	//$loggin->lwrite("Search_ajax", "log_busquedas.txt", "NOOOO inserto ");
			        	return ($ajax ? array() : array('total' => 0, 'result' => array()));
			        } else {
			        	//echo "<br> siiiiiiiiiiii";
			        	//$loggin->lwrite("Search_ajax", "log_busquedas.txt", "Siiiii inserto");
			        }
		        
		    }

			if ($conn_open == 1) {
		    	pg_close($dbconn4);
		    	$conn_open = 0;
			}

			$sql = "SELECT DISTINCT p.id_product, pl.name pname, cl.name cname, cl.link_rewrite crewrite, pl.link_rewrite prewrite, position2.peso, IF(position2.porc = 100 ,".$reference_load.",0) AS ref_fnd, GROUP_CONCAT(DISTINCT im.id_image SEPARATOR ',') AS imgs, p.id_tax_rules_group AS taxgr, t.rate,
				CASE p.id_tax_rules_group
					WHEN 0 THEN ROUND(p.price,2)
					ELSE IF(t.rate IS NOT NULL, ROUND(p.price + (p.price * t.rate/100),2), ROUND(p.price,2) )
					END
					AS price
				FROM ps_product p
				INNER JOIN ps_product_lang pl ON (p.active = 1 AND p.id_product = pl.id_product AND pl.id_lang = 1 AND pl.id_shop = 1  )
				INNER JOIN ps_product_shop product_shop ON ( product_shop.active = 1 AND product_shop.visibility IN ('both', 'search')
									AND product_shop.indexed = 1 AND product_shop.id_product = p.id_product AND product_shop.id_shop = 1 )
				INNER JOIN ps_category_lang cl ON ( product_shop.id_category_default = cl.id_category AND cl.id_lang = 1 AND cl.id_shop = 1  )
				INNER JOIN tmp_search_".$alenum." position2 ON ( position2.id_product = p.id_product)
				LEFT JOIN `ps_tax_rule` tr ON (product_shop.id_tax_rules_group = tr.id_tax_rules_group AND tr.id_tax != 0)
				LEFT JOIN `ps_tax` t ON (t.id_tax = tr.id_tax AND t.active = 1 AND t.deleted = 0 )
				LEFT JOIN ps_image im ON ( p.id_product = im.id_product AND im.cover = 1 )
				GROUP BY p.id_product
				ORDER BY position2.id_asc ASC				
				LIMIT 7";

				//$loggin->lwrite("Search_ajax", "log_busquedas.txt", "sql 138: ".$sql);
				
				if ($alenum != 0 && $result = $db->executeS($sql)) {
					//echo "<br> 222222 siiiiiiii";
					//$loggin->lwrite("Search_ajax", "log_busquedas.txt", "SI ENCONTROOOOOO");
					$array_full = array();
					$cantnum = 0;

					foreach ($result as $value) {						

						$array_img = array();

						$array_full[$cantnum] = $value;

						if ( $value['imgs'] != NULL ) {				
							

								$array_full[$cantnum]['imgs'] = _PS_BASE_URL_SSL_
								. __PS_BASE_URI__
								. 'img/p/'
								. Image::getImgFolderStatic($value['imgs'])
								. $value['imgs']
								. '-small_default.jpg';
							

						} else {

							$array_full[$cantnum]['imgs'] = _PS_BASE_URL_SSL_
							. __PS_BASE_URI__
							. 'img/p/'
							. 'es-default-small_default.jpg';

						}

						$cantnum++;
					}

					$result = $array_full;

					return $result;
				} else {
					//echo "<br> 222222 Noooooooo";
					//$loggin->lwrite("Search_ajax", "log_busquedas.txt", "NOOO ENCONTROOOOOO");
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

		$conn_string = "host=127.0.0.1 port=15432 dbname=farmalisto_colombia user=search password=search123";
		//$conn_string = "host=search.cuznbgafgkfl.us-east-1.rds.amazonaws.com port=15432 dbname=farmalisto_colombia user=bus_col password='nyT.19*xS'";
		$dbconn4 = pg_pconnect($conn_string);
		$conn_open = 1;

		/*$sql = "SELECT si.id_product, SUM(si.weight) AS peso, ( 100 - ( ( levenshtein(sw.word, tw.palabra) ) * 100)/ length(sw.word) ) AS porce FROM ps_search_index si
				INNER JOIN ps_search_word sw ON ( sw.id_word = si.id_word )
				INNER JOIN (SELECT row_number() OVER() AS orden, UNNEST(STRING_TO_ARRAY('".$listword."', ';')) palabra ) tw 
					ON ( ( 100 - ( ( levenshtein(sw.word, tw.palabra) ) * 100)/ length(sw.word) ) >= 70 )				
				GROUP BY ( 100 - ( ( levenshtein(sw.word, tw.palabra) ) * 100)/ length(sw.word) ), si.id_product, tw.orden
				ORDER BY ( 100 - ( ( levenshtein(sw.word, tw.palabra) ) * 100)/ length(sw.word) ) DESC, tw.orden, peso DESC";
				*/

		$sql = "SELECT si.id_product, SUM(si.weight) AS peso, sum( 100 - ( ( search.levenshtein(sw.word, tw.palabra) ) * 100)/ length(sw.word) ) AS porce
			FROM mex.ps_search_index si
				INNER JOIN mex.ps_search_word sw ON ( sw.id_word = si.id_word )
				INNER JOIN (SELECT *, 100 - ( row_number() OVER (PARTITION by orden1) ) AS orden
				FROM  (
					 SELECT 	row_number() OVER() AS orden1, 
													UNNEST(STRING_TO_ARRAY('".$listword."', ';')) palabra
					 		) pepe
 						) tw 
					ON ( sw.word LIKE tw.palabra || '%' OR ( 100 - ( ( search.levenshtein(sw.word, tw.palabra) ) * 100)/ length(sw.word) ) >= 70 )
				GROUP BY si.id_product 
				HAVING sum(tw.orden) > 0
				ORDER BY SUM(si.weight) DESC, sum( 100 - ( ( search.levenshtein(sw.word, tw.palabra) ) * 100)/ length(sw.word) ) DESC";

		

		//error_log($sql);
				/*"SELECT si.id_product, SUM(si.weight) AS peso FROM
				ps_search_index si 
				INNER JOIN ps_search_word sw ON ( sw.id_word = si.id_word )
				WHERE 
					( ".implode(' OR ', $score_array)."
					)
				GROUP BY si.id_product
				ORDER BY peso DESC";*/
			$eligible_products = array();
			$ret_insert = 0;
			$orden_bus = '';
			if ( $resultado = pg_exec($sql)) {
				$alenum = rand(10,50);
				$sql_create = 'CREATE TEMPORARY TABLE tmp_search_'.$alenum.' (
					id_asc  int NOT NULL AUTO_INCREMENT ,
					id_product  int NULL ,
					id_peso  int NULL ,
					id_porcent  int NULL ,
					PRIMARY KEY (`id_asc`),
					INDEX indx_pr_search_'.$alenum.' (id_product) USING BTREE
					)';
				
				$result_crear = $db->execute($sql_create);

				$ins_prods_buscar = " INSERT INTO tmp_search_".$alenum." (id_product, id_peso, id_porcent) VALUES  ";

				while ( $fetch = pg_fetch_row($resultado)) {
					$eligible_products[$fetch['0']] = $fetch['0'];
					$ins_prods_buscar .= " (".$fetch['0'].", ".$fetch['1'].", ".$fetch['2']."),";
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


		$sql = 'SELECT p.id_product,p.active, p.reference, stock.out_of_stock, IFNULL(stock.quantity, 0) as quantity, 
				/*pl.`description_short`, pl.`available_now`, pl.`available_later`,*/ pl.`link_rewrite`, lower(pl.`name`) AS name,
			 MAX(image_shop.`id_image`) id_image, il.`legend`, m.id_manufacturer AS id_lab, lower(m.`name`) AS lab, cat.name AS cat, cat.id_category AS id_cat,
			  p.id_tax_rules_group AS taxgr, t.rate,
				CASE p.id_tax_rules_group
					WHEN 0 THEN ROUND(p.price,2)
					ELSE IF(t.rate IS NOT NULL, ROUND(p.price + (p.price * t.rate/100),2), ROUND(p.price,2) )
					END
					AS price,
					bt.*
				FROM '._DB_PREFIX_.'product p
				'.Shop::addSqlAssociation('product', 'p').'
				INNER JOIN `'._DB_PREFIX_.'product_lang` pl ON (
					p.`id_product` = pl.`id_product`
					AND pl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('pl').'
				)				
				'.Product::sqlStock('p', '', false, $context->shop).'
				LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (product_shop.id_tax_rules_group = tr.id_tax_rules_group AND tr.id_tax != 0)
				LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.id_tax = tr.id_tax AND t.active = 1 AND t.deleted = 0 )
				LEFT JOIN `'._DB_PREFIX_.'manufacturer` m ON m.`id_manufacturer` = p.`id_manufacturer`
				LEFT JOIN `'._DB_PREFIX_.'image` i ON (i.`id_product` = p.`id_product`)
				LEFT JOIN `'._DB_PREFIX_.'category_lang` cat ON (p.`id_category_default` = cat.`id_category`)'.
				Shop::addSqlAssociation('image', 'i', false, 'image_shop.cover=1').'
				LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (i.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)$id_lang.')'.
				($ret_insert ? ' INNER JOIN tmp_search_'.$alenum.' bt ON ( bt.id_product = p.id_product )' : '').'
				WHERE p.active = '. (int)$active .' AND product_shop.active = '. (int)$active .' AND p.`id_product` '.$product_pool.'
				GROUP BY product_shop.id_product
				'.$orden_bus.'			
				LIMIT '.(int)(($page_number - 1) * $page_size).','.(int)$page_size;

//error_log($sql);
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
			//$result_properties = Product::getProductsProperties((int)$id_lang, $result);
                

		return array('total' => $total,'result' => $result);
	}	      
	     
    
	protected static function getProductsToIndex($total_languages, $id_product = false, $limit = 50)
	{
		// Adjust the limit to get only "whole" products, in every languages (and at least one)
		$max_possibilities = $total_languages * count(Shop::getShops(true));
		$limit = max($max_possibilities, floor($limit / $max_possibilities) * $max_possibilities);
		$productBlackList = Configuration::get('PRODUCT_BLACK_LIST_SHOW');
		$query = '
			SELECT p.id_product, pl.id_lang, pl.id_shop, pl.name pname, p.reference, p.ean13, p.upc,
				pl.description_short, pl.description, cl.name cname, m.name mname, l.iso_code
			FROM '._DB_PREFIX_.'product p
			LEFT JOIN '._DB_PREFIX_.'product_lang pl
				ON p.id_product = pl.id_product
			'.Shop::addSqlAssociation('product', 'p').'
			LEFT JOIN '._DB_PREFIX_.'category_lang cl
				ON (cl.id_category = product_shop.id_category_default AND pl.id_lang = cl.id_lang AND cl.id_shop = product_shop.id_shop)
			LEFT JOIN '._DB_PREFIX_.'manufacturer m
				ON m.id_manufacturer = p.id_manufacturer
			LEFT JOIN '._DB_PREFIX_.'lang l
				ON l.id_lang = pl.id_lang
			LEFT JOIN '._DB_PREFIX_.'product_black_list product_black ON (product_black.id_product = p.id_product)
			WHERE product_shop.indexed = 0
			AND product_shop.visibility IN ("both", "search")
			'.($id_product ? 'AND p.id_product = '.(int)$id_product : '');
			if(Configuration::get('SEARCH_INDEX_ACTIVE') && Configuration::get('SEARCH_INDEX_ACTIVE') == 1){
				$query .= ' AND p.active = IF(product_black.motivo IN ('.$productBlackList.'),  0, '.Configuration::get('SEARCH_INDEX_ACTIVE').')';
			}
		return Db::getInstance()->executeS($query);
	}
}
