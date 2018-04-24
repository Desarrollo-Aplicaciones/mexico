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

    #resendSMS{
        font-weight: bold;
        color: #39BC93;
    }
    #email-addr{
        display: block;
        width: 200px;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
    }
    @media (max-width: 480px) {
        #email-addr{
            font-size: 12px;
        }
    }

</style>


<div style="text-align: center; margin: 85px 0 60px 0">
    <img src="/themes/gomarket/img/Logo_Login_MX.png"/>
</div>

<div class="box-account">
    <h1 style="color: #000"><strong>{l s='Recupera'}</strong> {l s='tu contraseña'}</h1>


    <div id="divStep1" style="padding: 15px 30px; display: block">
        <div style="line-height: 20px;">{l s='Por favor ingresa la dirección de e-mail que registraste al momento de crear tu cuenta.'}

        </div>

        <form method="post" class="std" id="form_forgotpassword">
            <fieldset>
                <p class="text">
                    <label for="email"
                           style="line-height: 10px; margin-top: 20px; font-size: 14px;">{l s='Email'}</label>
                    <input type="text" id="email" name="email" class="input-custom" placeholder="ejemplo@mail.com"
                           value="{if isset($smarty.post.email)}{$smarty.post.email|escape:'htmlall':'UTF-8'|stripslashes}{/if}"/>
                </p>
                <p id="error-text" class="class-error"
                   style="display: none">{l s='Ups, no hemos podido encontrar tu cuenta en farmalisto.'}
                    ​​​​​​​</p>
                <p class="submit">
                    <input type="submit" class="button" id="btn-restore" value="{l s='Recuperar contraseña'}"/>
                </p>
            </fieldset>
        </form>

        {l s='¿No tienes cuenta?'} <a href="{$link->getPageLink('authentication', true)}?reg=5"
                                      style="color: #3db990; font-weight: 700;">{l s='Créala aquí'}</a>

    </div>

    <div id="divStep2" style="padding: 15px 15px; display: none">
        <div style="line-height: 20px;">{l s='¿Cómo deseas generar tu nueva contraseña?'}</div>

        <div class="option-regen" data-via="mail">
            <div class="image-regen">
                <img src="/themes/gomarket/img/icono-mail.png"/>
            </div>
            <div class="text-regen">
                <b>{l s='Vía correo electrónico'}</b> <br/>
                <span id="email-addr">ejemplo@mail.com</span>
            </div>
        </div>

        <div class="option-regen" data-via="tel" data-id="">
            <div class="image-regen">
                <img src="/themes/gomarket/img/icono-tel.png"/>
            </div>
            <div class="text-regen">
                <b>{l s='Vía SMS'}</b> <br/>
                <span id="sms-addr"></span>
            </div>
        </div>

    </div>

    <div id="divStep3" style="padding: 15px 30px; display: none">

        <div class="div-success">
            <div class="suc-button">{l s='Correo enviado'}</div>
            <img src="/themes/gomarket/img/Icono_Check.png"/>
        </div>

        <div style="line-height: 20px;">{l s='Ingresa a tu correo electrónico y sigue las instrucciones.'}</div>

    </div>


    <div id="divStep4" style="padding: 15px 30px; display: none">
        <div class="div-success">
            <div class="suc-button">{l s='SMS enviado'}</div>
            <img src="/themes/gomarket/img/Icono_Check.png"/>
        </div>

        <form method="post" class="std" id="form_code">
            <fieldset>
                <input type="hidden" id="code-id_customer" />
                <p class="text">
                    <input required type="number" id="code" name="code" class="input-custom" placeholder="{l s='Introduce aquí el código que te enviamos'}"
                           value="" style="    margin-top: 10px;"/>
                </p>
                <p id="error-text-code" class="class-error"
                   style="display: none">{l s='Ups, no hemos podido encontrar el código de recuperación.'}​​​​​​​</p>

                <p class="submit">
                    <input type="submit" class="button" id="btn-code" value="{l s='Continuar'}" style="    margin-top: 10px; color: #fde1c0; border: #fde1c0 3px solid;"/>
                </p>
            </fieldset>
        </form>

        <div style="line-height: 20px;">{l s='Si no te ha llegado el código da clic'} <a href="#" id="resendSMS"> {l s='aquí'} </a> {l s='para enviarlo de nuevo.'}</div>

    </div>

</div>


<div style="height: 10px"></div>

<div style="text-align: center; width: 300px;">
    <a href="{$base_dir}" class="button" id="goBack" value="">{l s='Regresar al inicio'} </a>
    <input type="button" class="button" id="goStep1" style="display: none" value="{l s='Regresar'}"/>
</div>

<script>

    doingAjax = false;

    {literal}
    $('#goStep1').click(function (e) {

        $('#divStep2').hide();
        $('#divStep3').hide();
        $('#divStep4').hide();
        $('#goStep1').hide();
        $('#goBack').show();
        $('#divStep1').show();
    });

    $('#btn-restore').click(function (e) {
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
                    checkEmail: 1,
                    ajax: true,
                    email: $('#email').val(),
                    token: token
                },
                success: function (jsonData) {
                    console.log(jsonData);
                    if (jsonData.email) {
                        $('#error-text').hide();
                        $('#divStep1 input').removeClass('class-error');

                        $('#goBack').hide();
                        $('#divStep1').hide();
                        $('#divStep2').show();
                        $('#goStep1').show();

                        $('#email-addr').text(jsonData.email);

                        $(".option-regen[data-via='mail']").attr('data-id', jsonData.id_customer);
                        console.log(jsonData.phones[0]);
                        if(jsonData.phones[0]){
                            $(".option-regen[data-via='tel']").show();
                            $('#sms-addr').text(jsonData.phones[0].phone);
                            $(".option-regen[data-via='tel']").attr('data-id', jsonData.phones[0].id_address_delivery);

                        }
                        else{
                            $(".option-regen[data-via='tel']").hide();
                        }

                    }
                    else {
                        $('#error-text').show();
                        $('#divStep1 input').addClass('class-error');
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

    $('.option-regen').click(function (e) {
        e.preventDefault();
        var via = $(this).attr('data-via');
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
                    via:via,
                    id: $(this).attr('data-id'),
                    token: token
                },
                success: function (jsonData) {
                    console.log(jsonData);
                    if (!jsonData.errors) {

                        $('#divStep2').hide();

                        if(via=='mail')
                            $('#divStep3').show();
                        if(via=='tel') {
                            $('#divStep4').show();
                            $('#code-id_customer').val(jsonData.id_customer);
                        }
                    }
                    else {

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

    $('#btn-code').click(function (e) {
        if ($('#form_code')[0].checkValidity() && !doingAjax) {
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
                    checkCode: 1,
                    ajax: true,
                    id: $('#code-id_customer').val(),
                    code: $('#code').val(),
                    token: token
                },
                success: function (jsonData) {
                    console.log(jsonData);
                    if (jsonData.url) {

                        window.location.href = jsonData.url;
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

    {/literal}

    ///Nuevo

    document.getElementById('email').addEventListener('input', function() {

        var str = $("#email").val(); 
        var res = str.match(/[.]/);
        var res2 = str.match(/@/);
        if(res == '.' && res2 == '@'){
            $("#btn-restore").addClass('postValidate');
            $("#email").addClass('validCampo');
        }else{
            $("#btn-restore").removeClass('postValidate');
            $("#email").removeClass('validCampo');
        }
    });

    document.getElementById('code').addEventListener('input', function() {

        var codigo = $("#code").val(); 
        if(codigo.length > 5){
            $("#btn-code").addClass('postValidate');
            $("#code").addClass('validCampo');
        }else{
            $("#btn-code").removeClass('postValidate');
            $("#code").removeClass('validCampo');
        }
    });

</script>