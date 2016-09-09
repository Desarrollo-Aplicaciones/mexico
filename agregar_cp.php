<?php



?>

<html>
<head>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.js"></script>
    <script src="http://malsup.github.com/jquery.form.js"></script>

    <script type="text/javascript" src="/prod.farmalisto.com.co/admin8256/../admin8256/../js/jquery/jquery-migrate-1.2.1.js"></script>
            <script type="text/javascript" src="/prod.farmalisto.com.co/admin8256/../admin8256/../js/jquery/plugins/cluetip/jquery.cluetip.js"></script>
            <script type="text/javascript" src="/prod.farmalisto.com.co/admin8256/../admin8256/../js/jquery/plugins/jquery.scrollTo.js"></script>
            <script type="text/javascript" src="/prod.farmalisto.com.co/admin8256/../js/toggle.js"></script>
            <script type="text/javascript" src="/prod.farmalisto.com.co/admin8256/../js/tools.js"></script>
            <script type="text/javascript" src="/prod.farmalisto.com.co/admin8256/../js/ajax.js"></script>
            <script type="text/javascript" src="/prod.farmalisto.com.co/admin8256/../admin8256/../js/jquery/ui/jquery.ui.core.min.js"></script>
            <script type="text/javascript" src="/prod.farmalisto.com.co/admin8256/../js/form.js"></script>



</head>
<br><input type="text" id="orden" name="orden" style="display: none"/>
<br><input type="text" id="producto" name="producto"/> Presione Enter para cargar el producto.
<br><input type="text" id="txtSearch" name="txtSearch"/> Presione Enter para agregar los códigos del producto seleccionado.
<br> |<?php  echo urldecode("%20CODIGO%3A%20MC24339946.%0AMi%"); ?>|
<div id="dato_prod"></div>
<div id="list_cb"><table border="1"><tr><td>listado</td></tr></table>
</div>
</html>





<script type="text/javascript">

product_infos = null;
        debug = null;
        
            product_ids = [];
       
            product_ids_to_delete = [];
      


$(function() {
            // add click event on just created delete item link
            $('a.quitaicr').live('click', function() {


                var id = $(this).attr('id');
                alert("eliminar:"+ id);
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
/**
 	$('#orden').change(function(){
            var ruta = "ajax_form_ordsup.php"; 
            var orden = $('#orden').val();

            $('#producto').empty();
            $('#list_cb').empty();

            $.ajax({
                type:"post",
                url:ruta,
                data:{
                    "id_orden":orden
                },
                success:function(response){
                    var json = $.parseJSON(response);
                    $('#city').html('<option value="" selected="selected">- Ciudad -</option>'+json.results);
                },
                error: function(err){
                    alert(err.responseText)
                }
            });
    })
*/

$("#txtSearch").keyup(function(event){
            if(event.keyCode == 13){
                $("#list_cb").append("<tr><td><input type='text' name='pr_" + $("#prodsel").val() + "[]' value='" + $("#txtSearch").val() + "'> <a class=\"quitaicr\" id=\""+ $("#prodsel").val() +"\"  href='#'> X </a> </td></tr>");
                $("#txtSearch").val("");
            }

        });

		$("#producto").keyup(function(event){
		    if(event.keyCode == 13){		        
                var product = $(this).val();

		        $("#producto").val("");

                var ruta = "icrassoc.php";
                var orden = 1;
                
                // $('#city').empty();
                $.ajax({
                    type:"post",
                    url:ruta,
                    data:{
                        "accion": "ajaxProductoOrden",
                        "id_orden":orden,
                        "referencia":product
                    },
                    success:function(response){
                        /*alert(response);
                        var json = $.parseJSON(response);
                        alert(json);*/
                        $('#dato_prod').html(response);
                    },
                    error: function(err){
                        alert(err.responseText)
                    }
                });

		    }

		});
});

</script>