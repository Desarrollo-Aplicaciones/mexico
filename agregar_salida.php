<?php require_once('classes/OrdenSuministroDetail.php');
//if(isset($_GET)){
//    echo '<pre>';
//    print_r($_GET);
//}

$host=$_SERVER['HTTP_HOST'];
if(isset($_GET)){
$id_emp=trim(addslashes(filtro($_GET['id_emp'])));
$id_order=trim(addslashes(filtro($_GET['id_order'])));
$id_customer=trim(addslashes(filtro($_GET['id_customer'])));
$id_cart=trim(addslashes(filtro($_GET['id_cart'])));
//$invoice_number=trim(addslashes(filtro($_GET['invoice_numer'])));
$delivery_number=trim(addslashes(filtro($_GET['delivery_number'])));
}

if(isset($id_order) && $id_order && $id_order != '' && isset($id_cart) && $id_cart && $id_cart != '' && isset($id_emp) && $id_emp && $id_emp != '')
{
$ordsum =  new OrdenSuministroDetail();
header('Content-Type: text/html; charset=UTF-8');  
/* validaciones de cantidades diponibles de productos, cantidad de productos requeridos en la orden de salida y cantidades seleccionadas en la orden.
 * 
 */
$cantidades_disponibles=$ordsum->cantidadDisponibleProductos($id_order);
//echo '<pre> Cantidades requeridas <br>'.
$cantidades_requeridas=$ordsum->numProducts($id_order);
//echo '<hr> Cantidades seleccionadas <br>'.
$cantidades_en_la_orden=$ordsum->contarProductosOrdenSalida($id_order);
//echo "<br>--|".$cantidades_en_la_orden."|--";
//echo "<br>cant: ".count($cantidades_en_la_orden) ;

$cantidades_faltantes=NULL;

$i=0;
foreach ($cantidades_requeridas as $row)
{
$cantidades_faltantes[]= array('product_id'=>$row['product_id'],'product_quantity'=>0);
  
  if ($cantidades_en_la_orden != '') {
    foreach ($cantidades_en_la_orden as $value) {

        if($row['product_id']==$value['id_product'])
        {
         $cantidades_faltantes[$i]= array('product_id'=>$row['product_id'],'product_quantity'=>($value['cantidad']-$row['product_quantity']));
        } else {
         $cantidades_faltantes[$i]= array('product_id'=>$row['product_id'],'product_quantity'=>($row['product_quantity']));  
        }
    }
  }

 $i++;   
}

?>



<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
</head>

<style>
.bubble {
    margin: 5px 0px 15px 35px;
    padding: 8px;
    position: relative;
    border-radius: 8px 8px 8px 8px;
    width: 200px;
}
.bubble:after {
    content: "";
    position: absolute;
   top: 100%;
   left: 32px;
   border-top: 15px solid blue;
   border-top-color: inherit;
   border-left: 15px solid transparent;
   border-right: 20px solid transparent;
}

.input_fous:focus{
 
background-color:yellow;   
}


</style>



        <style type="text/css">
    #tablaprod { background-color:#FFFFE0; border-collapse:collapse; }
    #tablaprod th { background-color:gainsboro; color:darkgreen; }
    #tablaprod td, #tablaprod th { padding:5px;border:1px solid #BDB76B; }


    #icrstable { background-color:#FFFFE0; border-collapse:collapse; }
    #icrstable th { background-color:gainsboro; color:darkgreen; }
    #icrstable td, #icrstable th { padding:5px;border:1px solid #BDB76B; }


    h3{
        font-family: verdana;
        font-size: 12px;
        line-height:0px;
        color: darkgreen;
        background:aliceblue;
        
    }
    </style>



<form action="icrassoc.php" method="POST" id="form_target">
    <div  style=" width: 100%; height: 95%;">
        <div style="width: 100%; margin: -21px 0px 0px 0px; font-family: verdana; font-size: 12px; color: dimgray;"><h1>Orden De Salida # <?php printf("%04d",$id_order);  ?> | <?php if(isset($_GET['opcion'])){ if($_GET['opcion']=='update'){ echo '<b>Desasociar</b>';} if($_GET['opcion']=='save'){ echo '<b>Asociar</b>';}} ?></h1></div> 
    
    <div style="width: 100%;  margin: -17px 0 0 0; ">
        <div style=" width: 100%;"> <div class="bubble" style="background: #00BB35;  border-color: #00BB35; float: left;">C&oacute;digo ICR a adicionar al producto.</div></div>
        <div style="float: left; width: 100%; "> <div style="float: left; margin: 0px 0px 0px 35px"><input class="input_fous" type="text" id="txtSearch" name="txtSearch"/> </div></div>
        
    </div>
    
 <div style="float: left; width: 80%; height: 69%;; 
      border: solid gainsboro;
-webkit-border-radius: 15px;
-moz-border-radius: 15px;
border-radius: 15px; 
padding: 3px;
margin: 3px;
">  

     <div style="float: left; width: 50%" id="dato_prod"></div>

     <div style="float: right; width: 99%; margin: -3px 2px 0px 18px; overflow: scroll; height: 98%;" id="list_cb">
         <div style="width: 350px;"> <div style="float: left; "><h3>C&oacute;digo ICR</h3></div>
                  <div style="float: right;" id="order_estado">
         <?php
         if($ordsum->ordenCompleta($id_order))
         {
          ?>
                         <h3 id="text_orden_estado" style="color: #00BB35;">Su orden esta completa</h3>
          <?php
         }  else {
              ?>
                         <h3 id="text_orden_estado" style="color: #CC0000;">Orden incompleta</h3>
          <?php   
         }
         ?>          </div>
          </div> 
         
     
               

         <table  id="icrstable">
                 <thead>
                     <tr>
                         <th>Producto</th>
                         <th>Referencia</th>
                         <th>IRC</th>
                     </tr>
                  
                     <?php
                     $prod_array=$ordsum->cargarProductosEnOrden($id_order);
                  
                  if ($prod_array != '') {
                     foreach ($prod_array as $value) { 
                     echo '<tr id="'.$value['cod_icr'].'_tr" >';
                     echo '<td>'.$value['product_name'].'</td>';
                     echo '<td>'.$value['reference'].'</td>';
                     echo '<td>'.$value['cod_icr'].'<a id="'.$value['cod_icr'].'" id_prod="'.$value['id_product'].'" class="removeicr" href="#"> X </a></td>';
                     echo "</tr>";
                     }
                  }
                     
                     ?>
                 </thead>
                 <tbody></tbody>
             </table>
         </div>
  
<input type="hidden" value="1" name="add_products_out_order" >

<input type="hidden" value="<?=$id_emp?>" name="id_emp">
<input type="hidden" value="<?=$id_cart?>" name="id_cart">
<input type="hidden" value="<?=$id_order?>" name="id_order">
<input type="hidden" value="add_products_out_order" name="accion">
  </div>      
        <div style="width: 100%; margin: 46% 0 0 0; text-align: center;"><input id="save" type="button" value="Guardar" > <input style="display: none;" id="update" type="button" value="Actualizar y permancer"></div>    
</div>

    
</form> 
    
<audio id="alert_producto" src="/sound/beam.wav" type="audio/wav"></audio>  
<audio id="alert_irc" src="/sound/beam.wav" type="audio/wav"></audio> 
<audio id="alert_irc_list" src="/sound/beam.wav" type="audio/wav"></audio> 
<audio id="product_not_in_order" src="/sound/product_not_in_order.mp3" type="audio/mp3"></audio>

</html>

 <script src="//ajax.googleapis.com/ajax/libs/jquery/1.8/jquery.min.js" type="text/javascript" charset="utf-8"></script>

<script type="text/javascript">


       
 var product_infos = null;
 var debug = null;
 var ruta = "icrassoc.php";
 var codigos_icr = [];
 var ref_product_icr = {};
 var reference_prod=null;
 var count_icr=0;
 var count_products=0;
 var product_ids = [];
 var product_ids_to_delete = [];
 var bandera_prod = 0;
 var productos = new Array();
 var ids_productos = new Array();
 var orden_completa=false;

 // evitar submit al pulsar enter   
        $(document).ready(function() {
    $(window).keydown(function(event){
        
    if(event.keyCode == 13) {
    event.preventDefault();
    return false;
    }
    
    });
  
      // Ejecutar submit con el bot�n save    
      $( "#save" ).click(function() {
    $( "#form_target" ).submit();
    });
    
    
    
});
    
            // add click event on just created delete item link
            $('a.quitaicr').live('click', function() {
                
                var id = $(this).attr('id');
                var del_cod_icr = id.split('|')[0];
               var position = jQuery.inArray(del_cod_icr, codigos_icr );
               
               console.log("codígos: "+codigos_icr.toString());
               console.log("id: "+del_cod_icr);
               console.log("position: "+position);
               
        if (position !== -1)
        {
       
            //remove the id from the array
            codigos_icr.splice(position, 1);
            
          
            console.log("Rm Array "+codigos_icr.toString());
        }
                
                var product_id = id.split('|')[1];
                productos["cont_"+product_id]--;
                ordenCompleta();

                //find the position of the product id in product_id array
                var position = jQuery.inArray(product_id, product_ids);
                if (position != -1)
                {
                 alert("encontrado");   
                    //remove the id from the array
                    product_ids.splice(position, 1);

                    var input_id = $('input[name~="input_id_'+product_id+'"]');
                    if (input_id != 'undefined')
                        if (input_id.length > 0)
                            product_ids_to_delete.push(product_id);

                    // update the product_ids hidden field
                    $('#product_ids').val(product_ids.join('|'));
                    $('#product_ids_to_delete').val(product_ids_to_delete.join('|'));

                    //remove the table row
                    $(this).parents('tr:eq(0)').remove();
                    // Decrementar del contador de productos
                  
                   
   
                }
$(this).parents('tr:eq(0)').remove();
 
                return false;
            });
            
       // remover producto relacionado a la orden     
            $('a.removeicr').live('click', function() {
                var id = $(this).attr('id'); 
                var id_prod = $(this).attr('id_prod');
                var id_tr="#"+ id +"_tr";
   // enviar solicitutd              
   $.ajax(ruta, {
   "type": "post",   // usualmente post o get
   "success": function(result) {
      
      if(result=='1')
      {
       $(id_tr).remove();
       
        productos["cont_"+id_prod]--;
        ordenCompleta();
      }else{
       alert("¡Ups!, ocurrio un error inesperado, el producto no se elimino de la lista.");   
      }
   },
   "error": function(result) {
    alert("¡Ups!, ocurrio un error inesperado, el producto no se elimino de la lista. "+result);
   },
   "data": {opcion: "removeIcr", accion: "ajaxIcrRemove", cod_icr: id},
   "async": true
});         
            });



<?php

$i=0;
$array=$cantidades_requeridas;
foreach ($array as $row) {
    echo "productos['cont_".$row['product_id']."']=0;\n";
    echo "productos['total_".$row['product_id']."']=".$row['product_quantity'].";\n";
    
    echo "ids_productos[".$i."] = ".$row['product_id'].";\n";
    $i++;
}
?>
    
    <?php

$array=$cantidades_en_la_orden;
  if ( $array != '') {
    foreach ($array as $row) {
     echo "productos['cont_".$row['id_product']."']=".$row['cantidad'].";\n";
    }
  }
?>
   

$("#txtSearch").keyup(function(event){
            if(event.keyCode == 13){
              
           if(!ordenCompleta())
           {
                
                var icr=$("#txtSearch").val();
                $("#txtSearch").val("");
        // valida si se ha seleccionado en producto
        if(icr!='')  // fin campo vacio
        {
          // ajax validacion confirmacion ICR
                   $.ajax({
                    type:"post",
                    url:ruta,
                    data:{
                         "opcion": "addIcr",
                        "accion": "ajaxIcrAddOutOrder",
                        "cod_icr":icr,
                        "id_orden":<?=$id_order?>
                    },
                    success:function(response){
                                               
                       if(response != 'NO') {

                          if(response != 'NOFEC') {

                            // if()   
                            // valida si el codigo ICR esta en la lista       
                            var position = jQuery.inArray(icr, codigos_icr );
                                if (position == -1)
                                {
                                
                                    var retornado = response.split('|');
                                    
                                  // si la cantidad de un producto es menor o igual al total requerido
                                    if(productos["cont_"+retornado[4]]<=productos["total_"+retornado[4]])
                                    {
                                 
                                    $("#icrstable").append("<tr><td>"+ retornado[2] +"</td><td>"+ retornado[3] +"</td><td>"+ count_icr +" <input readonly type='text' name='pri"+ "[]' value='" + retornado[1] + "'> <a class=\"quitaicr\" id=\""+ retornado[1] +"|"+retornado[4]+ "\"  href='#'> X </a> </td></tr>");
                                       
                                         //agrgar al array de icr  
                                         codigos_icr[count_icr]=retornado[1];
                                         // incrementar el contador de prodcutos
                                         count_icr++;
                                         // incrementar la cantidad de  un producto
                                        
                                         productos["cont_"+retornado[4]]++;

                                         console.log(retornado.toString());
                                         ordenCompletaGuargar();
                                       } else{
                                           alert("Tu orden contiene todos los elementos de este producto: ("+retornado[2]+"), o este producto no esta en la orden." );
                                       }
                                
                              } else { 
                                   $('#alert_irc_list').get(0).play();
                                  alert("Este Codigo ya esta en la lista "+ codigos_icr[count_icr]+"");
                                   $("#txtSearch").val("");
                                }
                          } else {

                            $('#alert_irc').get(0).play();
                            alert("Este Codigo <"+icr+"> Ya ha vencido.");
                            $("#txtSearch").val("");

                          }
                                                      
                        } else {
                            $('#alert_irc').get(0).play();
                            alert("Este Codigo <"+icr+"> no esta autorizado.");
                            $("#txtSearch").val("");
                        }
                        //alert("fin success")
                        //$("#txtSearch").prop("readonly",false);
                        // $("#txtSearch").removeAttr("readonly");
                    },
                    error: function(err){
                        alert(err.responseText);
                        // $("#txtSearch").prop("readonly",false);
                          //$("#txtSearch").removeAttr("readonly");
                    }
                });
              
              } //fin vacio      
            
                else{
                       
                       
                    $('#alert_producto').get(0).play();
        
               alert("Selecciona un ICR");
            
            } 
         }
          
            }
            
            
            
        });
        
  // tamaño de un array      
  Object.size = function(obj) {
    var size = 0, key;
    for (key in obj) {
        if (obj.hasOwnProperty(key)) size++;
    }
    return size;
};


function ordenCompleta()
{   
// valida si la orden esta completa
     var flag=false; 
     $.each(ids_productos, function( index, value ) {
     if(productos["cont_"+value]==productos["total_"+value])
             {
               //alert("Producto completado: "+value);
             }else{
               flag=true;  
             }
          });
          if(!flag)
          {
              $("#text_orden_estado").html("Su orden esta completa");
             $("#text_orden_estado").css({ color: "#00BB35", background: "#FF0000" });
           alert("Orden Completa ");
           return true;
          }else{
              $("#text_orden_estado").html("Orden incompleta");
              $("#text_orden_estado").css({ color: "#CC0000", background: "#FF0000" });
              return false;
          }
     /// end orden completa      
}

// Guardar si la orden esta completa
function ordenCompletaGuargar(){
    
               if(ordenCompleta())
       {
       var r=confirm("Su orden esta completa, ¿desea guardarla?");
    if (r==true)
      {
     $( "#form_target" ).submit();
      }
    else
      {
   alert("Recuerda guardar en el futuro.");
      }
  
   }
}

</script>

<?php
}

   // filtrar etiquetas  html 
    function filtro($texto) {
        $html = array("<", ">");
        $filtrado = array("&lt;", "&gt;");
        $final = str_replace($html, $filtrado, $texto);

        return $final;
    }
?>