<html>
 <Title>PHP info</title>
 <?php

      // Lista información de PHP
      //phpinfo();

      // Muestra información de los módulos
     // phpinfo(INFO_MODULES);

  

include(dirname(__FILE__).'/../config/config.inc.php');

//include(dirname(__FILE__).'/../init.php');
$ini=rand(0,500);
$fin=rand(500,1000);

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
            
            echo "<h3>inicio: ".$ini." - Fin: ".$fin."</h3><br>";
        if ($results = Db::getInstance()->ExecuteS($query)){
            foreach ($results as $value) {
                
                echo $value['name']."<br>";
            }
        }

        echo "<br><h1>Resultados</h1><br>";
        for ($i=rand(0,100); $i < rand(100,200); $i++) { 
          $a=rand(0,100);
          $b=rand(0,100);
          $c=rand(0,100);
          $d=rand(0,100);
          $resultado = $a * $b + ($b * $c) + ($a * $d) - ($a + $b + $c) * $d;
          echo "<br> val[".$i."] = ".$resultado." exp: ".exp($resultado)." atanh: ".atanh($resultado)." acosh: ".acosh($resultado)." pow: ".pow($resultado,$resultado);
        }



$fr=rand(0,100);
$to=$fr+10;

$query_imagenes =  "SELECT i.id_image, p.id_product, p.reference FROM ps_product p
INNER JOIN ps_image i ON ( i.id_product = p.id_product )
WHERE p.active = 1 
AND p.id_product >= ".$fr." 
AND p.id_product <= ".$to." 
ORDER BY p.id_product ASC ";


$tiposimg[] = '';
$tiposimg[] = 'home_default';
$tiposimg[] = 'large_default';
$tiposimg[] = 'medium_default';
$tiposimg[] = 'small_default';
$tiposimg[] = 'thickbox_default';


         //echo("<br>".$iteracion);

                if ($results = Db::getInstance()->ExecuteS($query_imagenes)) {



                    
                    $sheet_name = '';
                    $sheet_reg = 0;
                    $regshow = 0;

                    
                    


                    foreach ($results as $dat_print) {                        

                        foreach ($tiposimg as $key_t => $value_t) {
                            
                            $imagen_encontrada = 0;                            
                            

                            $dat_print['id_image'];
                            
                            $caracteres = preg_split('//', $dat_print['id_image'], -1, PREG_SPLIT_NO_EMPTY);
                            //print_r($caracteres);
                            $compemento_ruta = implode("/", $caracteres);

                            if ( $value_t != '' ) {
                                $ruta_imagen_existe = $dir_server."img/p/".$compemento_ruta.'/'.$dat_print['id_image'].'-'.$value_t.".jpg";
                            } else {
                                $ruta_imagen_existe = $dir_server."img/p/".$compemento_ruta.'/'.$dat_print['id_image'].$value_t.".jpg";
                            }
                            echo "<br> <img src='../".$ruta_imagen_existe."'>";

                            if ( file_exists($ruta_imagen_existe) ) {

                            
                            }

                            
                        }
                    }



                    // COMENTARIO PARA LA ULTIMA HOJA
                    //$row = $objPHPExcel->getActiveSheet()->getHighestRow()+2;
                    //$objPHPExcel->getActiveSheet()->getCell('A'.$row)->setValue('Convenciones AP Aprobado, RE Rechazado');
                    //$objPHPExcel->getActiveSheet()->getStyle('A'.$row)->getFont()->setSize(8);

                    // Redirect output to a client’s web browser (Excel5)
                    

                } 

            
?>


  </html>