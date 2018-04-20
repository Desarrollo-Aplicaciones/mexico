<?php

require_once('classes/OrdenSuministroDetail.php');

// $pepe =  new OrdenSuministroDetail();
// $pepe -> ajaxProductosOrden(1,'7707232092041');

// echo "<pre>";
// print_r($_POST);

if (isset($_POST)) {
    

	if (isset($_POST['accion']) && $_POST['accion'] != '') {

	$accion = $_POST['accion'];

	$ordsum =  new OrdenSuministroDetail();

		switch ($accion) {
			case 'ajaxProductoOrden':

				if( isset($_POST['id_orden']) && isset($_POST['referencia']) && isset($_POST['opcion']) ) {

					if($_POST['opcion'] == 'save') {
						$ordsum -> ajaxProductoOrden($_POST['id_orden'], $_POST['referencia']);
					}

				if($_POST['opcion'] == 'update') {

						$ordsum -> ajaxProductoOrdenDel($_POST['id_orden'], $_POST['referencia']);
					}
				
				}
							
				break;

			case 'ajaxIcrAdd':

				if( isset($_POST['cod_icr']) && isset($_POST['opcion']) ) {

					if($_POST['opcion'] == 'save') {

						$ordsum -> ajaxIcrAdd($_POST['cod_icr']);
					}

					if($_POST['opcion'] == 'update' && isset($_POST['id_orden']) && isset($_POST['referencia']) ) {

						$ordsum -> ajaxIcrDel($_POST['cod_icr'], $_POST['id_orden'], $_POST['referencia']);
					}
					
				}
							
				break;
            case 'add_products_out_order':
         
               
                  $id_emp=trim(addslashes(filtro($_POST['id_emp'])));
                  $id_cart=trim(addslashes(filtro($_POST['id_cart'])));
                  $id_order=trim(addslashes(filtro($_POST['id_order'])));
                  if( isset($_POST['pri']) && !empty($_POST['pri']) && $ordsum->saveIcrOrderPicking($_POST['pri'], $id_order,$id_emp)) {
	                  echo " <script language=\"javascript\"> parent.document.location = parent.document.location; this.close(); window.close(); 
	                  function closer() { this.close(); } </script>";
	                  echo'La orden <b>'.$_POST['id_order'].'</b> se actualizó correctamente.  <br> Ya puedes cerrar la ventana emergente <a rel="modal:close" href="#" OnClick="closer();">Cerrar la ventana.</a>  <a onclick="javascript:window.close();" href="#">Cerrar la ventana</a>';
	              }else{
	
      				echo "<br><b> 1. Error en la validación de los productos y/o los icr's a asociar, valide la asociación e intente nuevamente.</b>";
      			}
                break;
                        
            case 'ajaxIcrAddOutOrder':

                $icr =  trim(addslashes($_POST['cod_icr']));

                            if ( isset($icr) && $icr != '' ) {
                            	$row = $ordsum->icrDisponible($icr);
                            	$row2 = $ordsum->icrFechaVencida($icr);

                            	if( $row['total'] == 1 ) { //productos.cont_
                         			
                         			if ( $row2['total'] == 0 ) {
                         				$resp=$row['id_icr']."|".$row['cod_icr']."|".$row['name']."|".$row['reference']."|".$row['id_product']."|productos.cont_".$row['id_product']."|productos.total_".$row['id_product'];
                         				die("$resp");
                         			} else {

                         				die('NOFEC');
                         			}                        			

                                    

                            	} else { 

                            		die('NO');
                            		
                            	}

                            } else {
                                echo '-1';
                            }

                break;

            case 'ajaxIcrRemove':
                
    			if(isset($_POST['opcion'])&&$_POST['opcion']) {

                    if($ordsum->eliminarProductoOrdenSalida($_POST['cod_icr'])) {
                        echo '1';
                    } else { 
                    	echo '0'; 
                    }
                }
                
                break;

			default:
				echo "Opción inválida";
				break;
		}
	} elseif (isset($_POST['add_products_order']) && $_POST['add_products_order'] != '') {

		$arrprodsicrs = array();
		$arrprods = array();
		$arricrs = array();

		if (isset($_POST['id_supply_order']) && $_POST['id_supply_order'] != '' && isset($_POST['opcion'])) {

			$ordsum =  new OrdenSuministroDetail();

			foreach ($_POST as $key => $value) {

				if (substr($key,0,3) == 'pr_') {

					$arr_prod_icr = explode("_", $key);

					if ( !isset( $arrprods[ $arr_prod_icr[1] ] ) ) {

						$arrprods[ $arr_prod_icr[1]  ] = $arr_prod_icr[1] ;
					}

					foreach ($value as $id_icr => $value_icr) {

						$arrprodsicrs[ $arr_prod_icr[1] ][$arr_prod_icr[3]] = $value_icr;

						$arricrs[ $arr_prod_icr[3] ] = $arr_prod_icr[3];
						//echo "<br>val: ".$value_icr;						
					}

					
				}
			}

			$ordsum->supply_order = $_REQUEST['id_supply_order'];
            $ordsum->setId_employee($_REQUEST['id_emp']);
            $ordsum->setNomemployee($_REQUEST['firstname']);
            $ordsum->setApeemployee($_REQUEST['lastname']);
			$ordsum->setProductos($arrprods);
			$ordsum->setIcr($arricrs);
			$ordsum->setProductosIcr($arrprodsicrs);

			if($_POST['opcion'] == "save") {

				//echo "<br>cant_pro:".count($arrprods);
				//echo "<br>prods validos:".				

				if ( isset($_GET) && isset($_GET['modo_debug']) && $_GET['modo_debug'] == 'md_col_09374' ) {

				echo "<br>cant_pro:".count($arrprods);
				echo "<br>prods validos:".
				$prodsValidos = $ordsum -> validarProductosOrden("add");

				echo "<pre><br>prods_icr:";
				print_r($ordsum->productoicr);

				echo "<br>icr validos:".
				$icrValidos = $ordsum -> validarIcrOrden();
				exit();
			 	} else { 
			 		$prodsValidos = $ordsum -> validarProductosOrden("add");
					$icrValidos = $ordsum -> validarIcrOrden();
				}

				if( $prodsValidos == 1 && $icrValidos == 1 ) {
					$ordsum -> InsertarProductosIcrOrdenLoad();
					$ordsum -> InsertarProductosIcrOrden();
					$ordsum -> updateIcrProductoOrder();
					$ordsum -> updateIcrStatus();

					echo " <script language=\"javascript\"> parent.document.location = parent.document.location; this.close(); window.close(); 
	                  function closer() { this.close(); }</script>";
				} else {
					echo "<br>cant_pros:".count($arrprods);					
					echo "<br><b> 2. Error en la validación de los productos y/o los icr's a asociar, ";
					if ( $prodsValidos == 0) { echo "<u> valide la asociación al proveedor, </u>";} 
					if ( $icrValidos == 0) { echo "<u> valide que los ICR estén en estado 1 ( creado ), </u>";}
					echo "e intente nuevamente.</b> <br>prods validos:".$prodsValidos;
					echo "<br>icr validos:".$icrValidos;
				}
				
			} elseif ($_POST['opcion'] == "update") {

				//echo "<br>cant_pro:".count($arrprods);
				//echo "<br>prods validos:".
				$prodsValidos = $ordsum -> validarProductosOrden("del");

				//echo "<pre><br>prods_icr:";
				//print_r($ordsum->productoicr);

				//echo "<br>icr validos:".
				$icrValidos = $ordsum -> validarIcrOrden();	

				if( $prodsValidos == 1 && $icrValidos == 1 ) {
					$ordsum -> InsertarProductosIcrOrdenLoad();
					$ordsum -> updateIcrProductoOrder();
					$ordsum -> updateIcrStatus();
					$ordsum -> eliminarIcrProducto();

					echo " <script language=\"javascript\"> parent.document.location = parent.document.location; this.close(); window.close(); 
	                  function closer() { this.close(); }</script>";
				} else {
					echo "<br><b> 3. Error en la validación de los productos y/o los icr's a desasociar, .</b>";
					echo "<br>cant_pro:".count($arrprods);					
					if ( $prodsValidos == 0) { echo "<u> valide la asociación del producto con el proveedor, </u>";} 
					if ( $icrValidos == 0) { echo "<u> valide que los ICR estén en estado 2 ( Asignado ), </u>";}
					echo "valide la des-asociación e intente nuevamente.</b> <br>prods validos?:".$prodsValidos;
					echo "<br>icr's validos?:".$icrValidos;
				}
								
			}
			/*
			echo "<br>cant_pro:".count($arrprods);
			echo "<br>prods validos:".$prodsValidos = $ordsum -> validarProductosOrden("add");

			echo "<pre><br>prods_icr:";
			print_r($ordsum->productoicr);

			echo "<br>icr validos:".$icrValidos = $ordsum -> validarIcrOrden();

			if( $prodsValidos == 1 && $icrValidos == 1 ) {
				$ordsum -> InsertarProductosIcrOrdenLoad();
				$ordsum -> InsertarProductosIcrOrden();
				$ordsum -> updateIcrProductoOrder();
				$ordsum -> updateIcrStatus();
				//$ordsum -> eliminarIcrProducto();
			}
			//$prodsIcrValidos = $ordsum -> validarProductosIcrOrden();
			*/
		//
		}


	}
        
           // filtrar etiquetas  html 
    
}

function filtro($texto) {
        $html = array("<", ">");
        $filtrado = array("&lt;", "&gt;");
        $final = str_replace($html, $filtrado, $texto);
        return $final;
    }





 