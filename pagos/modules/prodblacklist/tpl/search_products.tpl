{include file=$pathModule|cat:"/tpl/formulario.tpl"}

<script src="//ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.min.js"></script>

<script type="text/javascript">
    $(function() {
        $('#formularito').validate({    {literal}
                    wrapper: 'span',
                    errorPlacement: function(error, element) {
                        error.css({'clear': 'both', 'margin': '0px 5px 0 5px', 'padding-bottom': '2px', 'float': 'left'});
                        error.addClass("arrow");
                        error.insertAfter(element);
                    },    {/literal}
                                rules: {
                                    product_name: {
                                        required: true, //para validar campo vacio
                                    },
                                    referencia: {
                                        required: true,
                                        minlength: 5, //para validar campo con minimo 3 caracteres
                                        maxlength: 50  //para validar campo con maximo 9 caracteres
                                    },
                                    descripcion: {
                                        required: true,
                                        rangelength: [30, 512]
                                        //minlength: 30, //para validar campo con minimo 3 caracteres
                                        //maxlength: 512  //para validar campo con maximo 9 caracteres
                                    }

                                },
                                messages: {
                                    descripcion: {
                                    required: "Se requiere una descripción",
                                    rangelength: "Por favor, escribe una descripción entre 30 y 512 caracteres..",
                                    }
                                }
                            });
                        });



                        jQuery.extend(jQuery.validator.messages, {
                            required: "Este campo es obligatorio.",
                            remote: "Por favor, rellena este campo.",
                            email: "Por favor, escribe una dirección de correo válida",
                            url: "Por favor, escribe una URL válida.",
                            date: "Por favor, escribe una fecha válida.",
                            dateISO: "Por favor, escribe una fecha (ISO) válida.",
                            number: "Por favor, escribe un número entero válido.",
                            digits: "Por favor, escribe sólo dígitos.",
                            creditcard: "Por favor, escribe un número de tarjeta válido.",
                            equalTo: "Por favor, escribe el mismo valor de nuevo.",
                            accept: "Por favor, escribe un valor con una extensión aceptada.",
                            maxlength: jQuery.validator.format("Por favor, no escribas más de {0} caracteres."),
                            minlength: jQuery.validator.format("Por favor, no escribas menos de {0} caracteres."),
                            rangelength: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1} caracteres."),
                            range: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1}."),
                            max: jQuery.validator.format("Por favor, escribe un valor menor o igual a {0}."),
                            min: jQuery.validator.format("Por favor, escribe un valor mayor o igual a {0}.")
                        });

</script>	

<script type="text/javascript">

    $(document).ready(function() {

    });


    $(function() {

        $('.row_prod').click(function() {
            var i = 0;
            $(this).find('td').each(function() {


                if (i == 0)
                {
                    $('#id_product').val($(this).html());
                }
                else if (i == 1) {

                    $('#ref_product').val($(this).html());
                }
                else if (i == 2) {
                    $('#product_name').val($(this).html());
                }
                else if (i >= 3)
                {
                    return false;

                }

                i++;
            });

            $(".fancybox").fancybox();

        });
    });


</script>

<style type="text/css">
    .fila{        
        margin: 5px 0 0 0;
        width: 100%;
        height: 21px;
    }
    .labelst{
        text-align: left;
        width: 76px;
    }
    .celdadiv{
        float: left;
    }  
    #formularito label.invalid
    {
        color: Red;
        font-style: italic;
        padding: 1px;
        margin: 0px 0px 0px 5px;
    }
    #formularito label.error {
        color:red;
    }
    #formularito input.error {
        border:1px solid red;
        float: left;
    }
    .error{
        float: right;
    }
</style>

<p> <h3>Resultado de la búsqueda, productos para agregar a la lista negra: <b>{$totalrows}</B></h3> <b>pagina: {$pageprod} de {$totalpages} </b> </p>



<table border="0">
    <thead>
        <tr>

            <th class="celdahead">Id Producto</th>
            <th class="celdahead">Referencia</th>
            <th class="celdahead">Nombre</th>
            <th class="celdahead">Enviar a la lista negra</th>
        </tr>
    </thead>
    <tbody>


        {foreach name=outer item=product from=$productsblock}
            <tr class="row_prod">
                <td class="celda">{$product.id_product}</td>
                <td class="celda">{$product.reference}</td> 
                <td class="celda">{$product.name}</td>
                <td class="celda"><a href="#midivok" class="fancybox">Enviar a la lista negra</a> </td>
            </tr>
        {/foreach}
    </tbody>
</table>

<div id="midivok" style="display: none; width: auto;" >

    <div id="dialog-modal" title="Enviar este producto a la lsita negra. ">

        <p class="validateTips">Los campos con (*) son obligatorios</p>
        <p>
        <form method="POST" action="{$current}&token={$token}&configure=prodblacklist" id="formularito">
            <fieldset>
                <div class="fila" style="clear:both;">
                    <div class="celdadiv"> <label for="name" class="labelst">Nombre*</label></div>
                    <div class="celdadiv"> <input type="text" name="product_name" id="product_name" readonly="readonly" value="" class="required" style="width: 316px;"></div>
                </div>
                <div class="fila" style="clear:both;">
                    <div class="celdadiv"><label class="labelst" for="referencia">Referencia*</label></div>
                    <div class="celdadiv"><input type="text" name="ref_product" id="ref_product" readonly="readonly" value="" class="required" style="width: 316px;"></div>
                </div>
                <div class="fila" style="clear:both;">
                    <div class="celdadiv"><label class="labelst" for="descripcion">Descripción*</label></div>
                    <div class="celdadiv"><textarea style="float: left;" id="descripcion" class="required" name="descripcion" cols="54" rows="7" placeholder="Escriba aquí una descripción detallada, indicando claramente porque desea enviar este producto a la lista negra."></textarea> </div>
                </div>

                <div class="fila" style="margin: 10px 0 0 0; clear:both;">
                    <div class="celdadiv"   <label for="motivo" class="labelst" >Motivo*</label></div>
                    <div class="celdadiv">

                        <select name="motivo" id="motivo">
                            {foreach name=outer item=item from=$select_motivo}
                                <option value="{$item.id_black_motivo}">{$item.name}</option>

                            {/foreach}

                        </select>

                    </div>
                </div>
                <input type="hidden" value="{$employee->id}" id="id_emp" name="id_emp">
                <input type="hidden" value="{$employee->lastname}" id="lastname" name="lastname">
                <input type="hidden" value="{$employee->firstname}" id="firstname" name="firstname">
                <input type="hidden" value="1" id="status" name="status">
                <input type="hidden"  id="id_product" name="id_product">

                <br/> <br/>

                <input type="submit" style=" margin: 10px 0 0 0; width: 100%;" value="Enviar producto a la lista negra"  id="submit" name="submit_black_list">

            </fieldset>
        </form>
        </p>
    </div>
</div>





