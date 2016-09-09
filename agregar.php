<?php
$host=$_SERVER['HTTP_HOST'];

if(isset($_GET['id_supply_order']))
{
header('Content-Type: text/html; charset=UTF-8');    
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
        <div style="width: 100%; margin: -21px 0px 0px 0px; font-family: verdana; font-size: 12px; color: dimgray;"><h1>Orden de suministros # <?php printf("%04d",$_GET['id_supply_order']);  ?> | <?php if(isset($_GET['opcion'])){ if($_GET['opcion']=='update'){ echo '<b>Desasociar</b>';} if($_GET['opcion']=='save'){ echo '<b>Asociar</b>';}} ?></h1></div> 
    
    <div style="width: 100%;  margin: -17px 0 0 0; ">
        <div style=" width: 100%;"><div class="bubble" style="background: #00BB35; border-color: #00BB35; float: left; margin:5px 0px 0px 0px;">Referencia del producto a adicionar el ICR.</div> <div class="bubble" style="background: #00BB35;  border-color: #00BB35; float: left;">C&oacute;digo ICR a adicionar al producto.</div></div>
        <div style="float: left; width: 100%; "><div style="float: left;"><input class="input_fous" size="35"  type="text" id="producto" name="producto"/>  </div> <div style="float: left; margin: 0px 0px 0px 35px"><input class="input_fous" type="text" id="txtSearch" name="txtSearch"/> </div></div>
        
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

     <div style="float: right; width: 25%; margin: -3px 2px 0px 18px; overflow: scroll; height: 98%;" id="list_cb">
         <h3>C&oacute;digo ICR</h3>
         <table  id="icrstable">
                 <thead>
                     <tr>
                         <th>IRC</th>
                     </tr>
                 </thead>
                 <tbody></tbody>
             </table>
         </div>
     

     
     
 <input type="hidden" value="<?php if(isset($_GET['id_supply_order'])) { echo $_GET['id_supply_order'];} ?>" name="id_supply_order" >
<input type="hidden" value="1" name="add_products_order" >

<input id="opcion" type="hidden" name="opcion" value="<?php if(isset($_GET['opcion'])){    echo $_GET['opcion']; } ?>">
<input type="hidden" value="<?php if(isset($_GET['id_emp'])) { echo $_GET['id_emp'];} ?>" name="id_emp">
<input type="hidden" value="<?php if(isset($_GET['lastname'])) { echo $_GET['lastname'];} ?>" name="lastname">
<input type="hidden" value="<?php if(isset($_GET['firstname'])) { echo $_GET['firstname'];} ?>" name="firstname">

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

 // evitar submit al pulsar enter   
        $(document).ready(function() {
    $(window).keydown(function(event){
    if(event.keyCode == 13) {
    event.preventDefault();
    return false;
    }
    });
  
      // Ejecutar submit con el botón save    
      $( "#save" ).click(function() {
    $( "#form_target" ).submit();
    });
 // Ejecutar submit con el botón update    
      $( "#save" ).click(function() {
      $( "#update" ).click(function() {
     $( "#form_target" ).submit();
    });
  
});
    
    });
    
    
            // add click event on just created delete item link
            $('a.quitaicr').live('click', function() {
                var id = $(this).attr('id');
              
               var position = jQuery.inArray(id, codigos_icr );
               console.log("codígos: "+codigos_icr.toString());
               console.log("id: "+id);
               console.log("position: "+position);
               
        if (position !== -1)
        {
         
            //remove the id from the array
            codigos_icr.splice(position, 1);
            console.log("Rm Array "+codigos_icr.toString());
        }
                
                
                var product_id = id.split('|')[1];


                //find the position of the product id in product_id array
                var position = jQuery.inArray(product_id, product_ids);
                if (position != -1)
                {
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
                }
$(this).parents('tr:eq(0)').remove();
                return false;
            });




// add click event on just created delete item link
            $('a.quitaprodicr').live('click', function() {
                var id = $(this).attr('id');
              alert(id);

                for (var key in ref_product_icr[id]) {
                    console.log("prod: "+key);
                    var obj = ref_product_icr[id][key];
                    console.log("obj: "+obj);











                    var position = jQuery.inArray(obj, codigos_icr );
                   console.log("codígos: "+codigos_icr.toString());
                   console.log("id: "+id);
                   console.log("position: "+position);
                   
                    if (position !== -1)
                    {
                     
                        //remove the id from the array
                        codigos_icr.splice(position, 1);
                        console.log("Rm Array "+codigos_icr.toString());
                    }
                    
                    
                    var product_id = id.split('|')[1];


                    //find the position of the product id in product_id array
                    var position = jQuery.inArray(product_id, product_ids);
                    if (position != -1)
                    {
                        //remove the id from the array
                        product_ids.splice(position, 1);

                        var input_id = $('input[name~="input_id_'+product_id+'"]');
                        if (input_id != 'undefined')
                            if (input_id.length > 0)
                                product_ids_to_delete.push(product_id);

                        // update the product_ids hidden field
                        $('#product_ids').val(product_ids.join('|'));
                        $('#product_ids_to_delete').val(product_ids_to_delete.join('|'));
                        
                    }  
                    $("#"+obj).parents('tr:eq(0)').remove();                 
                }
                delete ref_product_icr[id];
                $(this).parents('tr:eq(0)').remove();

/*
               
                return false;
                */
            });



$("#txtSearch").keyup(function(event){
            if(event.keyCode == 13){
                
                
                var icr=$("#txtSearch").val();
                $("#txtSearch").val("");
               // $('#txtSearch').attr('readonly', true);
               //$("#txtSearch").prop("readonly",true);

        
        // valida si se ha seleccionado en producto
       if ($('#prodsel').length){
           
        if(icr!='')  // fin campo vacio
        {
         var product = parseInt($("#prodsel").val());
         var orden = <?php echo $_GET['id_supply_order']; ?>;
          // ajax validacion confirmacion ICR
                   $.ajax({
                    type:"post",
                    url:ruta,
                    data:{
                         "opcion": "<?php if(isset($_GET['opcion'])){    echo $_GET['opcion']; } ?>",
                        "accion": "ajaxIcrAdd",
                        "cod_icr":icr,
                        "id_orden":orden,
                        "referencia":product
                    },
                    success:function(response){
                        
                                               
                       if(response != 'No')
                        {
                                     // valida si el codigo ICR esta en la lista       
                        var position = jQuery.inArray(icr, codigos_icr );
                            if (position == -1)
                            {  
                            var retornado = response.split('|');
                            codigos_icr[count_icr]=retornado[1];

                            
                            if (typeof(ref_product_icr[product]) == 'undefined') { // si el producto no tiene ningun icr asociado
                                ref_product_icr[product] = {};
                            }
                            ref_product_icr[product][count_icr] = retornado[1];

                            if (bandera_prod == 0) {
                                $("#icrstable").append("<tr><td> " +$("#referencepr").val() + " <a class=\"quitaprodicr\" id=\""+ $("#prodsel").val() +"\"  href='#'> X  </a> </td></tr>");
                                bandera_prod=1;
                            }
                                                       
                            count_icr++;
                            
                            console.log("retornado: " + product + " | arr: "+ JSON.stringify(retornado).toString() );
                            $("#icrstable").append("<tr><td>"+ count_icr +" <input readonly type='text' name='pr_" + $("#prodsel").val() + "_pri_" + retornado[0] + "[]' value='" + retornado[1] + "'> <a class=\"quitaicr\" id=\""+ retornado[1] +"\"  href='#'> X </a> </td></tr>");
                            //$('#txtSearch').attr('readonly', false); +1.9
                           //$("#txtSearch").prop("readonly",false); jquery -1.9
                            //console.log("json:::: - prod: " + product + " | arr: "+ JSON.stringify(ref_product_icr).toString() );

                            count_products++;
                            console.log("In Array: "+codigos_icr.toString());
                            
                            } else { 
                             $('#alert_irc_list').get(0).play();
                            alert("Este Codigo ya esta en la lista "+ codigos_icr[count_icr]+"");
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
            }// seleccion de producto
            
                       else{
                       
                       
                           $('#alert_producto').get(0).play();
           // $("#txtSearch").prop("readonly",true);
            alert("Selecciona un producto antes de ingresar los Código ICR");
            
            } // seleccion No producto
            
            }
         
        });

		$("#producto").keyup(function(event){
		if(event.keyCode == 13){		        
                var product = $(this).val();

		$("#producto").val("");
                  
               if(product!="")
               {                    
                var orden = <?php echo $_GET['id_supply_order']; ?>;
                
                // $('#city').empty();
                $.ajax({
                    type:"post",
                    url:ruta,
                    data:{
                        "opcion": "<?php if(isset($_GET['opcion'])){    echo $_GET['opcion']; } ?>",
                        "accion": "ajaxProductoOrden",
                        "id_orden":orden,
                        "referencia":product
                    },
                    success:function(response){
                        if(response==0)
                        {
                           
                            $('#product_not_in_order').get(0).play();
                         alert("Referencia de producto no encontrada en la Orden Actual.");  
                        }
                        else
                        {
                        /* var json = $.parseJSON(response);
                        alert(json);*/
                 
                        $('#dato_prod').html(response);
                        reference_prod=$("#prodsel").val();
                        // $("#txtSearch").removeAttr("readonly");
                        
                        }
                        bandera_prod = 0;
                    },
                    error: function(err){
                        alert(err.responseText);
                    }
                });
                
        }

		    }

		});
             
</script>

<?php
}
?>


