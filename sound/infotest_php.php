<html>
 <Title>PHP info</title>
 <?php

      // Lista información de PHP
      //phpinfo();

      // Muestra información de los módulos
     // phpinfo(INFO_MODULES);

  

  include(dirname(__FILE__).'/../config/config.inc.php');
include(dirname(__FILE__).'/../init.php');
$ini=10;
$fin=1000;

            $query="SELECT  stock.out_of_stock, IFNULL(stock.quantity, 0) as quantity, 
        pl.`description_short`, pl.`available_now`, pl.`available_later`, pl.`link_rewrite`, pl.`name`,
       MAX(image_shop.`id_image`) id_image, il.`legend`, m.`name` manufacturer_name ,(
        SELECT SUM(weight)
        FROM ps_search_word sw
        LEFT JOIN ps_search_index si ON sw.id_word = si.id_word
        WHERE sw.id_lang = 1
          AND sw.id_shop = 1
          AND si.id_product = p.id_product
          AND (sw.word LIKE 'alcohol%')
      ) position, MAX(product_attribute_shop.`id_product_attribute`) id_product_attribute,
        DATEDIFF(
          p.`date_add`,
          DATE_SUB(
            NOW(),
            INTERVAL 20 DAY
          )
        ) > 0 new
        FROM ps_product p
         INNER JOIN ps_product_shop product_shop
    ON (product_shop.id_product = p.id_product AND product_shop.id_shop = 1)
        INNER JOIN `ps_product_lang` pl ON (
          p.`id_product` = pl.`id_product`
          AND pl.`id_lang` = 1 AND pl.id_shop = 1 
        )
        LEFT JOIN `ps_product_attribute` pa ON (p.`id_product` = pa.`id_product`)
         LEFT JOIN ps_product_attribute_shop product_attribute_shop
    ON (product_attribute_shop.id_product_attribute = pa.id_product_attribute AND product_attribute_shop.id_shop = 1 AND product_attribute_shop.`default_on` = 1)
         LEFT 
      JOIN ps_stock_available stock
      ON (stock.id_product = p.id_product AND stock.id_product_attribute = IFNULL(`product_attribute_shop`.id_product_attribute, 0) AND stock.id_shop = 1  )
        LEFT JOIN `ps_manufacturer` m ON m.`id_manufacturer` = p.`id_manufacturer`
        LEFT JOIN `ps_image` i ON (i.`id_product` = p.`id_product`) LEFT JOIN ps_image_shop image_shop
    ON (image_shop.id_image = i.id_image AND image_shop.id_shop = 1 AND image_shop.cover=1)
        LEFT JOIN `ps_image_lang` il ON (i.`id_image` = il.`id_image` AND il.`id_lang` = 1)
        WHERE p.`id_product` > ".$ini."  AND p.`id_product` < ".$fin."
        GROUP BY product_shop.id_product
        ORDER BY  position desc
        LIMIT 0,10";
            
           // echo "<h3>inicio: ".$ini." - Fin: ".$fin."</h3><br>";
       /* if ($results = Db::getInstance()->ExecuteS($query)){
            foreach ($results as $value) {
                
                echo $value['name']."<br>";
            }
        }*/

        echo "<br><h1>Resultados</h1><br>";
        for ($i=0; $i < 1000; $i++) { 
          $a=105;
          $b=265;
          $c=987;
          $d=154;
          $resultado = $a * $b + ($b * $c) + ($a * $d) - ($a + $b + $c) * $d;
          echo "<br> val[".$i."] = ".$resultado." exp: ".exp($resultado)." atanh: ".atanh($resultado)." acosh: ".acosh($resultado)." pow: ".pow($resultado,$resultado);
        }
?>
  </html>