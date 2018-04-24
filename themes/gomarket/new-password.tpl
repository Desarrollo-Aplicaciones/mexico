{*
* 2007-2012 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2012 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<style >
    
    #fondoLog{
        background-image: linear-gradient(134deg, rgb(98, 195, 95) 3%, rgb(58, 155, 55) 53%, rgb(57, 188, 147) 103%);
        background-size: auto;
        background-repeat: repeat;
        background-position: center center;
        width: 3382px;
        height: 334px;
        position: absolute;
    }

    .postValidate{
        background-color: #FE922E !important;
        border: none !important;
        color: white !important;
    }

    .validCampo{
        border-bottom: 2px solid rgb(58, 155, 55) !important;
    }

</style>


<div style="text-align: center; margin: 85px 0 60px 0">
    <img src="/themes/gomarket/img/logo-farmalisto.png"/>
</div>

<div class="box-account" id="box-1">
    <h1 style="color: #000"><strong>{l s='Asignación'}</strong> {l s='de nueva contraseña'}</h1>


    <div id="divStep1" style="padding: 15px 30px; display: block">
        <div style="line-height: 20px;">{l s='Ingresa una nueva contraseña para tu cuenta y'} <strong> {l s='¡Listo!'}</strong>

        </div>

        <form method="post" class="std" id="form_forgotpassword">
            <input type="hidden" id="token" required name="token" class="input-custom"
                   value="{{$token}}" style="margin-top: 15px;"/>
            <input type="hidden" id="id_customer" required name="id_customer" class="input-custom"
                   value="{{$id_customer}}" style="margin-top: 15px;"/>
            <fieldset>
                <p class="text">

                    <input type="password" id="inpassword" required name="password" class="input-custom rePass" placeholder="{l s='Nueva contraseña'}"
                           value="" maxlength="19" style="margin-top: 15px;"/>
                </p>

                <p class="text">

                    <input type="password" id="repassword" required name="repassword" class="input-custom rePass" placeholder="{l s='Confirma la contraseña'}"
                           value="" maxlength="19"/>
                </p>

                <p class="submit">
                    <input type="submit" class="button" id="btn-restore" value="{l s='Guardar contraseña'}"/>
                </p>
            </fieldset>
        </form>



    </div>

    <p style="    color: #4dae49;
    border: 7px solid #4dae49;
    padding: 10px;
    border-width: 0px 5px;">{l s='La nueva contraseña debe tener mínimo 5 caracteres y máximo 19'}</p>

</div>

<div style="
    font-size: 40px;
    color: #FFF;
    text-align: center;
    margin-bottom: 24px;
    display: none;
" id="welcome"> Bienvenido <span id="customerName" style="font-weight: bold"></span> </div>

<div class="box-account" id="box-2" style="display: none">
    <h1 style="color: #000">{l s='¡El cambio de tu contraseña fue'}  <strong>{l s='exitoso'}!</strong> </h1>

</div>

<div style="height: 10px"></div>

<script language='javascript' type='text/javascript'>
    var doingAjax = false;


    var password = document.getElementById("inpassword")
        , confirm_password = document.getElementById("repassword");

    function validatePassword(){

        if (password.value.length < 5){
            password.setCustomValidity("Contraseña inválida");
            
        }
        else{
            password.setCustomValidity("");
            
        }
        if(password.value != confirm_password.value) {
            confirm_password.setCustomValidity("Las contraseñas deben coincidir");
        } else {
            confirm_password.setCustomValidity('');
        }
    }

    password.onkeyup = validatePassword;
    confirm_password.onkeyup = validatePassword;



</script>



<script>
    jQuery(function($) { $.extend({
        form: function(url, data, method) {
            if (method == null) method = 'POST';
            if (data == null) data = {};

            var form = $('<form>').attr({
                method: method,
                action: url
            }).css({
                display: 'none'
            });

            var addData = function(name, data) {
                if ($.isArray(data)) {
                    for (var i = 0; i < data.length; i++) {
                        var value = data[i];
                        addData(name + '[]', value);
                    }
                } else if (typeof data === 'object') {
                    for (var key in data) {
                        if (data.hasOwnProperty(key)) {
                            addData(name + '[' + key + ']', data[key]);
                        }
                    }
                } else if (data != null) {
                    form.append($('<input>').attr({
                        type: 'hidden',
                        name: String(name),
                        value: String(data)
                    }));
                }
            };

            for (var key in data) {
                if (data.hasOwnProperty(key)) {
                    addData(key, data[key]);
                }
            }

            return form.appendTo('body');
        }
    }); });


    $('#btn-restore').click(function(e){
        if ($('#form_forgotpassword')[0].checkValidity() && !doingAjax) {
            e.preventDefault();
            doingAjax=true;

            $.ajax({
                type: 'POST',
                url: baseUri,
                async: true,
                cache: false,
                dataType: "json",
                data: {
                    controller: 'password',
                    changePassword: 1,
                    ajax: true,
                    id_customer: $('#id_customer').val(),
                    c_token: $('#token').val(),
                    password: $('#inpassword').val(),
                    token: token
                },
                success: function (jsonData) {
                    if (jsonData.email) {
                        $('#box-1').hide();
                        $('#box-2').show();
                        $('#welcome').show();
                        $('#customerName').text(jsonData.name);
                        $.form(baseUri, { controller: 'authentication', SubmitLogin: '1', email: jsonData.email, passwd: $('#inpassword').val() }, 'POST').submit();
                        //window.location.href = jsonData.url;
                    }
                    else {
                        $('#error-text-code').show();
                    }

                    doingAjax = false;
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    alert("TECHNICAL ERROR: unable to load form.\n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
                    doingAjax = false;
                }
            });


        }
    });

    $('.rePass').on('input', function(){
        var pass = $("#repassword").val();
        var pass2 = $("#inpassword").val(); 

        if(pass.length > 5){
            $("#repassword").addClass('validCampo');
        }else{
            $("#repassword").removeClass('validCampo');
        }

        if(pass2.length > 5){
            $("#inpassword").addClass('validCampo');
        }else{
            $("#inpassword").removeClass('validCampo');
        }

        if(pass.length > 5 && pass2.length > 5){
            $("#btn-restore").addClass('postValidate');
        }else{
            $("#btn-restore").removeClass('postValidate');
        }
    });

</script>