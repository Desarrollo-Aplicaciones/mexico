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

<style>


    #page .button.btn-color {
        opacity: 0.3;
        box-shadow: rgba(0, 0, 0, 0.16) 0px 3px 6px, rgba(0, 0, 0, 0.23) 0px 3px 6px;
        border-color: rgb(221, 221, 221);
        background-color: transparent;
        background-image: linear-gradient(135deg, rgb(88, 185, 85) 1%, rgb(58, 155, 55) 101%);
        color: rgb(255, 255, 255);
        border-width: 0px;
        border-radius: 5px !important;
        font-size: 16px;
        margin-top: 12px;
    }

    #btn-register {
        background-image: linear-gradient(135deg, rgb(87, 218, 177) 1%, rgb(57, 188, 147) 103%) !important;
    }

    body #page .button.btn-color:hover {
        background-image: linear-gradient(135deg, rgb(88, 185, 85) 1%, rgb(58, 155, 55) 101%) !important;
        color: #FFF;
    }

    .box-title {
        display: flex;
        border-bottom: 1px solid #e5e5e5;
        height: 65px;
    }

    .box-title h1 {
        border: 0;
        font-family: 'Montserrat', sans-serif;
        margin: auto;
        padding: 0;
        font-weight: 400;
        font-size: 18px;
        color: rgb(100, 100, 100);
    }

    input[type="text"].input-custom, input[type="email"].input-custom, input[type="password"].input-custom {

        margin: 10px 0 0px;
    }

    input[type="checkbox"]:focus {
        outline: 0;
    }

    #fondoLog{
        background-image: linear-gradient(134deg, rgb(98, 195, 95) 3%, rgb(58, 155, 55) 53%, rgb(57, 188, 147) 103%);
        background-size: auto;
        background-repeat: repeat;
        background-position: center center;
        width: 9382px;
        height: 334px;
        position: absolute;
    }

    #fondoLogSuperpuesto{
        background-image: linear-gradient(134deg, rgb(98, 195, 95) 3%, rgb(58, 155, 55) 53%, rgb(57, 188, 147) 103%);
        background-size: auto;
        background-repeat: repeat;
        background-position: center center;
        width: 3382px;
        height: 334px;
        position: absolute;
    }
    @media only screen and (max-width: 500px) and (min-width: 120px) {
        .degradado{
            transform: rotate(0deg) translate(-312px, 0px) rotate(0deg) !important;
        }  
    }
    @media only screen and (max-width: 800px) and (min-width: 501px) {
        .degradado{
            transform: rotate(0deg) translate(-500px, 0px) rotate(0deg) !important;
        }  
    }
    @media only screen and (max-width: 1200px) and (min-width: 801px) {
        .degradado{
            transform: rotate(0deg) translate(-800px, 0px) rotate(0deg) !important;
        }  
    }
    @media (min-width: 1200px) {
        .degradado{
            transform: rotate(0deg) translate(-1200px, 0px) rotate(0deg) !important;
        }  
    }

    .degradado{
        background: linear-gradient(134deg, rgb(98, 195, 95) 3%, rgb(58, 155, 55) 53%, rgb(57, 188, 147) 103%) !important;
        opacity: 0;
        animation-name: degr;
        animation-duration: 0.2s;
        animation-timing-function: linear;
        animation-fill-mode: both;
    }  
  
    @keyframes degr {
        from   { opacity: 0; transform: rotate(0deg) translate(0px, 0px) rotate(0deg) !important;} 
        to  { opacity: 1; transform: rotate(0deg) translate(-1460px, 0px) rotate(0deg) !important;}
    }

    .degradadoFuera{
        background: linear-gradient(134deg, rgb(98, 195, 95) 3%, rgb(58, 155, 55) 53%, rgb(57, 188, 147) 103%) !important;
        opacity: 1;
        animation-name: degrOut;
        animation-duration: 0.2s;
        animation-timing-function: linear;
        animation-fill-mode: both;
        transform: rotate(0deg) translate(0px, 0px) rotate(0deg) !important;
    } 

    @keyframes degrOut {
        from   { opacity: 1; transform: rotate(0deg) translate(-1460px, 0px) rotate(0deg) !important;} 
        to  { opacity: 0; transform: rotate(0deg) translate(0px, 0px) rotate(0deg) !important;}
    }

    .TOSreg label{
        width: 15px;
    }

    #btn-register{
        opacity: 0.3;
    }
    .postValidate{
        opacity: 1 !important;
    }

    .stepOne {
      animation: one 1s ;
      animation-direction: alternate;
    }

    @keyframes one {
      0% {
        transform: rotateY(0deg);
      }
      100% {
        transform: rotateY(-90deg);
      }
    }

    .stepTwo {
      animation: two 1s ;
      animation-direction: alternate;
    }

    @keyframes two {
      0% {
        transform: rotateY(-90deg);
      }
      100% {
        transform: rotateY(0deg);
      }
    }
    .box-account{
        overflow: hidden;
    }
    .divStep1out{
        padding: 15px 30px;
        display: inline;
        position: fixed;
    }
    #btn-login2{
        right: -329px;
        opacity: 0;
    }
    #password{
        position: relative;
        right: -329px;
        opacity: 0;
    }
    #olvidCont{
        position: relative;
        bottom: -50px;
        opacity: 0;
    }
    .validCampo{
        border-bottom: 2px solid rgb(58, 155, 55) !important;
    }
    .validCampoReg{
        border-bottom: 2px solid rgb(57, 188, 147) !important;
    }
    .auth-page-txt-before-logins{
        font-size: 14px;
        font-weight: bold;
        text-align: right;
        margin-top: 30px;
        margin-right: 30px;
        margin-left: auto;
        width: 164px;
        color: gray;
    }
    .google.custom-social-button-all.custom-social-button-2,.facebook.custom-social-button-all.custom-social-button-2{
        border-radius: 99px;
        margin-top: 30px;
    }
    .google.custom-social-button-all.custom-social-button-2{
        margin-right: auto;
        margin-left: 30px;
    }
    #fbloginblock-authpage{
        display: flex;
    }

</style>

<div style="text-align: center; margin: 85px 0 60px 0">
    <img src="/themes/gomarket/img/Logo_Login_MX.png"/>
</div>

<div id="login_form" class="box-account">
    
    <div class="box-title">
        <div style="margin: auto;">
            <h1 style="color: #000 ; border: none" id="title-step1">{l s='Ingresa tu'}
                <strong>{l s='correo electrónico'}</strong></h1>

            <div id="title-step2" style="display: none">
                <h1 style="color: #000 ; border: none">{l s='Te damos la bienvenida'}</h1>
                <span id="welcome-mail" style="font-size: 14px; display: block; color: rgb(30, 129, 122);"></span>
            </div>

            <h1 style="color: #000 ; border: none; display: none" id="title-step3">{l s='Regístrate'}</h1>
        </div>
    </div>


        
    
    <div id="divStep1" style="padding: 15px 30px; {if $smarty.get.reg != 5} display: block {else} display: none; {/if}">
        <form method="post" class="std" id="form_loginemail">
            <fieldset>
                <p class="text">
                    <label for="email"
                           style="line-height: 10px; margin-top: 20px; font-size: 14px; font-weight: 700 ">{l s='Correo electrónico'}</label>
                    <input type="email" required id="email" name="email" class="input-custom"
                           placeholder="ejemplo@mail.com"
                           value="{if isset($smarty.post.email)}{$smarty.post.email|escape:'htmlall':'UTF-8'|stripslashes}{/if}"/>
                </p>
                <p id="error-text" class="class-error"
                   style="display: none">{l s='Ups, no hemos podido encontrar tu cuenta en farmalisto.'}
                    ​​​​​​​</p>
                <p class="submit">
                    <input type="submit" class="button btn-color disable" id="btn-login1" value="{l s='Siguiente'}"/>
                </p>
            </fieldset>
        </form>

        {l s='¿No tienes cuenta?'} <a href="#" class="goRegister"
                                      style="color: #3db990; font-weight: 700;">{l s='Créala aquí'}</a>

    </div>
    <div id="divStep2" style="padding: 15px 30px; display: none">
        <form method="post" class="std" id="form_loginpassword">
            <fieldset>
                <p class="text">
                    <label for="email"
                           style="line-height: 10px; margin-top: 20px; font-size: 14px; font-weight: 700 ">{l s='Ingresa tu contraseña'}</label>
                    <input type="password" required id="password" name="password" class="input-custom"
                           placeholder="{l s='contraseña'}"
                           value=""/>
                </p>

                <p style="margin-top: 9px;"><a href="{$link->getPageLink('password')}" id="olvidCont"
                                               style="color: #7174ee">{l s='¿Olvidaste tu contraseña?'}
                        ​​​​​​​</a></p>

                <p id="error-text" class="class-error"
                   style="display: none">{l s='Ups, no hemos podido encontrar tu cuenta en farmalisto.'}
                    ​​​​</p>
                <input type="hidden" name="backUrl" id="backUrl" value="{{$smarty.get.back}}">
                <p class="submit">
                    <input type="submit" class="button btn-color disable" id="btn-login2" value="{l s='Siguiente'}"/>
                </p>
            </fieldset>
        </form>

        {l s='¿No tienes cuenta?'} <a href="#" class="goRegister"
                                      style="color: #3db990; font-weight: 700;">{l s='Créala aquí'}</a>
    </div>

    <div id="divStep3" style="padding: 15px 30px; {if $smarty.get.reg == 5} display: block; {else} display: none {/if}">

        <form method="post" class="std" id="form_register">
            <fieldset>
                <p class="text">
                    <label for="email"
                           style="line-height: 10px; margin-top: 20px; font-size: 14px; font-weight: 700 ">{l s='Ingresa tu correo electrónico'}</label>
                    <input type="email" required id="email-register" name="email" class="input-custom registro"
                           placeholder="{l s='nombre@ejemplo.com'}"
                           value=""/>
                </p>

                <p class="text">
                    <label for="name"
                           style="line-height: 10px; margin-top: 20px; font-size: 14px; font-weight: 700 ">{l s='Ingresa tu nombre'}</label>
                    <input type="text" required id="name-register" name="firstname" class="input-custom registro"
                           placeholder="{l s='Nombre'}"
                           value=""/>
                </p>

                <p class="text">
                    <label for="lastname"
                           style="line-height: 10px; margin-top: 20px; font-size: 14px; font-weight: 700 ">{l s='Ingresa tu apellido'}</label>
                    <input type="text" required id="lastname-register" name="lastname" class="input-custom registro"
                           placeholder="{l s='Apellido'}"
                           value=""/>
                </p>


                <p class="text">
                    <label for="email"
                           style="line-height: 10px; margin-top: 20px; font-size: 14px; font-weight: 700 ">{l s='Crea una contraseña'}</label>
                    <input type="password" required id="password-register" name="passwd" class="input-custom registro"
                           placeholder="{l s='contraseña'}"
                           value=""/>
                </p>

                <p class="text">
                    <label for="email"
                           style="line-height: 10px; margin-top: 20px; font-size: 14px; font-weight: 700 ">{l s='Confirma la contraseña'}</label>
                    <input type="password" required id="repassword" name="repassword" class="input-custom registro"
                           placeholder="{l s='contraseña'}"
                           value=""/>
                </p>


                <div class="text TOS" style="display: flex;">
                    <div class="TOSreg" style="    display: inline-block;width: 48px;    margin: 8px 11px 0 0;">

                        <input type="checkbox" value="None" id="TOSreg" name="check" style="display: block" required>
                        <label
                                for="TOSreg"></label></div>
                    <div class="TOSlegend"
                         style=" width: auto;margin-top: 1px;color: #777777;line-height: 23px;  width: auto !important;">
                        Confirmo que soy mayor de edad y acepto
                        <a href="{$base_uri}?id_cms=3&controller=cms" target="blank" target="blank"
                           style="color: #39bc93; text-decoration: none;">
                            los términos y las condiciones</a>
                        legales y el 
                        <a href="{$base_uri}?id_cms=51&controller=cms" target="blank" target="blank"
                           style="color: #39bc93; text-decoration: none;">aviso de privacidad</a> del sitio.</div>
                    <div class="rterror" id="errorTOSreg"></div>
                </div>

                <p id="error-text" class="class-error" style="display: none">​​​​​​​</p>
                <p class="submit">
                    <input type="submit" class="button btn-color disable" id="btn-register"
                           value="{l s='Completa tus datos'}"/>
                </p>
            </fieldset>
        </form>

        {l s='¿Ya tienes cuenta?'} <a href="#" class="goLogin"
                                      style="color: #3db990; font-weight: 700;">{l s='Inicia sesión aquí'}</a>

    </div>

</div>


<div style="height: 10px"></div>



<div style="text-align: center; width: 300px;">
    <a href="{$base_dir}" class="button" id="goBack" value="">{l s='Regresar al inicio'} </a>
    <input type="button" class="button" id="goStep1" style="display: none" value="{l s='Regresar'}"/>
</div>

<script>
    jQuery("form").submit(function (e) {
        e.preventDefault();
    });

    jQuery(function ($) {
        $.extend({
            form: function (url, data, method) {
                if (method == null) method = 'POST';
                if (data == null) data = {};

                var form = $('<form>').attr({
                    method: method,
                    action: url
                }).css({
                    display: 'none'
                });

                var addData = function (name, data) {
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
        });
    });


    doingAjax = false;

    {literal}
    $('#goStep1').click(function (e) {

        $('#divStep2').hide();
        $('#divStep3').hide();
        $('#goStep1').hide();
        $('#goBack').show();
        $('#divStep1').show();

        $('#divStep1').removeClass("divStep1out");
        $('#btn-login1, #email').css('left', '0');

        $('#title-step1').show();
        $('#title-step2').hide();
        $('#title-step3').hide();

    });

    $('#btn-login1').click(function (e) {
        if ($('#form_loginemail')[0].checkValidity() && !doingAjax) {
            e.preventDefault();
            doingAjax = true;
            $.ajax({
                type: 'POST',
                url: baseUri,
                async: true,
                cache: false,
                dataType: "json",
                data: {
                    controller: 'password',
                    checkEmail: 1,
                    ajax: true,
                    email: $('#email').val(),
                    token: token
                },
                success: function (jsonData) {
                    if (jsonData.email) {
                        
                        $('#welcome-mail').text($('#email').val());

                        $('#error-text').hide();
                        $('#divStep1 input').removeClass('class-error');
                        $('#olvidCont').animate({bottom: '0px', opacity: 1}, "slow");
                        $('#goBack').hide();
                        $('#divStep1').addClass("divStep1out").fadeOut("slow");
                        $('#btn-login1, #email').animate({left: '-200px'}, "slow").fadeOut("slow");
                        $('#title-step1').hide();
                        $('#divStep2').fadeIn("slow");
                        $('#password').animate({right: '0px', opacity: 1}, "slow");
                        $('#btn-login2').animate({right: '0px', opacity: 1}, "slow");
                        $('#title-step2').show().addClass('stepTwo');
                        $('#goStep1').show();
                        $('#email-addr').text(jsonData.email);
                        //$('#sms-addr').text(jsonData.phones[0].phone);
                        $(".option-regen[data-via='mail']").attr('data-id', jsonData.id_customer);
                        //$(".option-regen[data-via='tel']").attr('data-id', jsonData.phones[0].id_address_delivery);
                        $("#btn-login1").addClass('postValidate');
                        
                    }
                    else {
                        $('#error-text').show();
                        //$('#divStep1 input').addClass('class-error');
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

    $(".goLogin").on('click',function(){
        $('#divStep1').removeClass("divStep1out");
        $('#btn-login1').css("left","0px");
        
    });

    var passwordLogin = document.getElementById("password");

    $('#password').keyup(function () {
        passwordLogin.setCustomValidity("");
    });
    var backUrl = $("#backUrl").val();
    $('#btn-login2').click(function (e) {
        if ($('#form_loginpassword')[0].checkValidity() && !doingAjax) {
            e.preventDefault();
            doingAjax = true;
            $.ajax({
                type: 'POST',
                url: baseUri,
                async: true,
                cache: false,
                dataType: "json",
                data: {
                    controller: 'authentication',
                    checkPassword: 1,
                    ajax: true,
                    email: $('#email').val(),
                    password: $('#password').val(),
                    token: token
                },
                success: function (jsonData) {
                    console.log(jsonData);
                    if (jsonData == 'OK') {
                        $.form(baseUri, {
                            controller: 'authentication',
                            SubmitLogin: '1',
                            email: $('#email').val(),
                            passwd: $('#password').val(),
                            back: backUrl
                        }, 'POST').submit();
                    }
                    else {
                        passwordLogin.setCustomValidity("Contraseña incorrecta");
                        $('#form_loginpassword').find(':submit').click();

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

    var emailinput = document.getElementById("email-register");

    $('#email-register').keyup(function () {
        emailinput.setCustomValidity("");
    });
    var backUrl = $("#backUrl").val();
    $('#btn-register').click(function (e) {

        if ($('#form_register')[0].checkValidity() && !doingAjax) {
            doingAjax = true;

            $.ajax({
                type: 'POST',
                url: baseUri,
                async: true,
                cache: false,
                dataType: "json",
                data: {
                    controller: 'authentication',
                    checkMailNotExist: 1,
                    ajax: true,
                    email: $('#email-register').val(),
                    token: token
                },
                success: function (jsonData) {
                    if (jsonData == 'OK') {
                        $.form(baseUri, {
                            controller: 'authentication',
                            submitAccount: '1',
                            email: $('#email-register').val(),
                            passwd: $('#password-register').val(),
                            customer_firstname: $('#name-register').val(),
                            customer_lastname: $('#lastname-register').val(),
                            email_create: 1,
                            is_new_customer: 1,
                            back: backUrl
                        }, 'POST').submit();
                    }
                    else {
                        emailinput.setCustomValidity("Ya existe una cuenta creada con este email");
                        $('#form_register').find(':submit').click();
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

    $('.goRegister').click(function (e) {
        e.preventDefault();
        $('#divStep1').hide();
        $('#divStep2').hide();

        $('#title-step1').hide();
        $('#title-step2').hide();

        $('#divStep3').show();
        $('#title-step3').show();

    });

    $('.goLogin').click(function (e) {
        e.preventDefault();
        $('#divStep3').hide();
        $('#divStep2').hide();

        $('#title-step3').hide();
        $('#title-step2').hide();

        $('#divStep1').show();
        $('#title-step1').show();

    });
    
    $('.option-regen').click(function (e) {
        e.preventDefault();
        if (!doingAjax) {
            doingAjax = true;
            $.ajax({
                type: 'POST',
                url: baseUri,
                async: true,
                cache: false,
                dataType: "json",
                data: {
                    controller: 'password',
                    rememberPassword: 1,
                    ajax: true,
                    via: $(this).attr('data-via'),
                    id: $(this).attr('data-id'),
                    token: token
                },
                success: function (jsonData) {
                    if (!jsonData.error) {

                        $('#divStep2').hide();
                        $('#divStep3').show();
                    }
                    else {

                    }

                    doingAjax = false;
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    alert("TECHNICAL ERROR: unable to load form.\n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
                }
            });
        }

    });

    $('#divStep3 form input').on('input', function (e) {
        validateFormRegister();
    });

    $('#divStep3 form input').change(function () {
        validateFormRegister();
    });



    {/literal}
</script>

<script language='javascript' type='text/javascript'>
    var password = document.getElementById("password-register")
        , confirm_password = document.getElementById("repassword");

    var loginPassword = document.getElementById("password")

    function validatePassword() {

        if (password.value.length < 5)
            password.setCustomValidity("Contraseña inválida, utiliza al menos 6 caracteres.");
        else
            password.setCustomValidity("");

        if (password.value != confirm_password.value) {
            confirm_password.setCustomValidity("Las contraseñas deben coincidir");
        } else {
            confirm_password.setCustomValidity('');
        }

        validateFormRegister();
    }

    function validateLoginPassword() {
        if (loginPassword.value.length < 5)
            loginPassword.setCustomValidity("Contraseña inválida, utiliza al menos 6 caracteres.");
        else
            loginPassword.setCustomValidity("");
    }

    loginPassword.onkeyup = validateLoginPassword;

    password.onkeyup = validatePassword;
    confirm_password.onkeyup = validatePassword;

    function validateFormRegister() {
        if ($('#form_register')[0].checkValidity()) {
            $('#form_register #btn-register').val("Crear cuenta");
        }
        else {
            $('#form_register #btn-register').val("Completa tus datos");
        }
    }

    ////////////Nuevo

    document.getElementById('email').addEventListener('input', function() {

        var str = $("#email").val(); 
        var res = str.match(/[.]/);
        var res2 = str.match(/@/);
        if(res == '.' && res2 == '@'){
            $("#btn-login1").addClass('postValidate');
            $("#email").addClass('validCampo');
        }else{
            $("#btn-login1").removeClass('postValidate');
            $("#email").removeClass('validCampo');
        }
    });

    document.getElementById('password').addEventListener('input', function() {

        var pass = $("#password").val(); 
        if(pass.length > 5){
            $("#btn-login2").addClass('postValidate');
            $("#password").addClass('validCampo');
        }else{
            $("#btn-login2").removeClass('postValidate');
            $("#password").removeClass('validCampo');
        }
    });

    $('.registro').on('input', function(){
       
        var email = $("#email-register").val(); 
        var nombre = $("#name-register").val(); 
        var apellido = $("#lastname-register").val(); 
        var contrasena = $("#password-register").val(); 
        var contrasena2 = $("#repassword").val(); 
        var checkbox = $("#TOSreg").val(); 
        var res = email.match(/[.]/);
        var res2 = email.match(/@/);
        if(res == '.' && res2 == '@'){
            $("#email-register").addClass('validCampoReg');
        }else{
            $("#email-register").removeClass('validCampoReg');
        }

        if(nombre.length > 1){
            $("#name-register").addClass('validCampoReg');
        }else{
            $("#name-register").removeClass('validCampoReg');
        }

        if(apellido.length > 1){
            $("#lastname-register").addClass('validCampoReg');
        }else{
            $("#lastname-register").removeClass('validCampoReg');
        }

        if(contrasena.length > 5){
            $("#password-register").addClass('validCampoReg');
        }else{
            $("#password-register").removeClass('validCampoReg');
        }

        if(contrasena2.length > 5){
            $("#repassword").addClass('validCampoReg');
        }else{
            $("#repassword").removeClass('validCampoReg');
        }


        if(res == '.' && res2 == '@' && nombre.length > 1 && apellido.length > 1 && contrasena.length > 5 && contrasena2.length > 5 && $('#TOSreg').is(':checked')){
            $("#btn-register").addClass('postValidate');
        }else{
            $("#btn-register").removeClass('postValidate');
        }
        
    });

    $('#TOSreg').on('change', function(){
        if( $('#TOSreg').is(':checked') ){
            var email = $("#email-register").val(); 
            var nombre = $("#name-register").val(); 
            var apellido = $("#lastname-register").val(); 
            var contrasena = $("#password-register").val(); 
            var contrasena2 = $("#repassword").val(); 
            var checkbox = $("#TOSreg").val(); 
            var res = email.match(/[.]/);
            var res2 = email.match(/@/);
            if(res == '.' && res2 == '@' && nombre.length > 1 && apellido.length > 1 && contrasena.length > 5 && contrasena2.length > 5 ){
                $("#btn-register").addClass('postValidate');
            }else{
                $("#btn-register").removeClass('postValidate');
            }
        }else{
            $("#btn-register").removeClass('postValidate');
        }
    });

    $( document ).ready(function() {
        $('.goRegister').on('click', function(){
            $('#fondoLogSuperpuesto').addClass('degradado');
            $('#fondoLogSuperpuesto').removeClass('degradadoFuera');
        });
        $('.goLogin').on('click', function(){
            $('#fondoLogSuperpuesto').removeClass('degradado');
            $('#fondoLogSuperpuesto').addClass('degradadoFuera');
        });
    });

</script>

<!-- Start of LiveChat (www.livechatinc.com) code -->
<script type="text/javascript">
    window.__lc = window.__lc || {};
    window.__lc.license = 6077601;
    window.__lc.chat_between_groups = false;
    (function () {
        var lc = document.createElement('script');
        lc.type = 'text/javascript';
        lc.async = true;
        lc.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'cdn.livechatinc.com/tracking.js';
        var s = document.getElementsByTagName('script')[0];
        s.parentNode.insertBefore(lc, s);
    })();
</script>
<!-- End of LiveChat code -->