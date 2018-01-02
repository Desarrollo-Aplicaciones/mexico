/**
 * 2011 - 2017 StorePrestaModules SPM LLC.
 *
 * MODULE fbloginblock
 *
 * @author    SPM <kykyryzopresto@gmail.com>
 * @copyright Copyright (c) permanent, SPM
 * @license   Addons PrestaShop license limitation
 * @version   1.7.7
 * @link      http://addons.prestashop.com/en/2_community-developer?contributor=61669
 *
 * NOTICE OF LICENSE
 *
 * Don't use this module on several shops. The license provided by PrestaShop Addons
 * for all its modules is valid only once for a single shop.
 */

document.addEventListener("DOMContentLoaded", function(event) {

    $(document).ready(function() {



        if(fbloginblockislogged) {
            /* fixed bug for icons CSS styles only for Prestashop 1.7.x.x and Safari */
            fbloginblock_fixed_safari_css_bug();


            /* footer social logins buttons. var fbloginblock_login_buttons_footer - located in the head.tpl */
            display_fbloginblock_login_buttons_footer(fbloginblock_login_buttons_footer);


            /* top social logins buttons. var fbloginblock_login_buttons_top - located in the head.tpl */
            display_fbloginblock_login_buttons_top(fbloginblock_login_buttons_top);

            /* authpage social logins buttons. var fbloginblock_login_buttons_authpage - located in the head.tpl */
            display_fbloginblock_login_buttons_authpage(fbloginblock_login_buttons_authpage);

            /* beforeauthpage social logins buttons. var fbloginblock_login_buttons_beforeauthpage - located in the head.tpl */
            display_fbloginblock_login_buttons_beforeauthpage(fbloginblock_login_buttons_beforeauthpage);

            /* welcome social logins buttons. var fbloginblock_login_buttons_welcome - located in the head.tpl */
            display_fbloginblock_login_buttons_welcome(fbloginblock_login_buttons_welcome);
        }


        if(fbloginblockamazonci != '' && fbloginblockis_ssl == 1) {
            init_amazon_fbloginblock();
        }


    });

});




function display_fbloginblock_login_buttons_welcome(data_fbloginblock_login_buttons_welcome){


    if($('#header_user_info a') && data_fbloginblock_login_buttons_welcome.length > 0)
        $('#header_user_info a:last').after(data_fbloginblock_login_buttons_welcome);

    // for PS 1.6 >
    if($('.header_user_info') && data_fbloginblock_login_buttons_welcome.length > 0)
        $('.header_user_info:last').after('<div class="header_user_info_ps16">'+data_fbloginblock_login_buttons_welcome+'<\/div>');

    // for ps 1.7
    if($('.user-info') && data_fbloginblock_login_buttons_welcome.length > 0)
        $('.user-info:last').prepend(data_fbloginblock_login_buttons_welcome);


}


function display_fbloginblock_login_buttons_beforeauthpage(data_fbloginblock_login_buttons_beforeauthpage){

    if(fbloginblock_is17==1) {

        /*setTimeout(function(){

         if($('#checkout-login-form').length>0){
         $('#checkout-login-form').append(ph);
         } else {
         $('#login-form').parent().after(ph);
         }

         }, 1000);*/

        $('#login-form').parent().before(data_fbloginblock_login_buttons_beforeauthpage);
    }else if(fbloginblock_is16 == 1) {
        $('#login_form').parent('div').parent().prepend(data_fbloginblock_login_buttons_beforeauthpage);
    }else {
        $('#create-account_form').before(data_fbloginblock_login_buttons_beforeauthpage);
    }


}


function display_fbloginblock_login_buttons_authpage(data_fbloginblock_login_buttons_authpage){

    if(fbloginblock_is17==1) {

        /*setTimeout(function(){

         if($('#checkout-login-form').length>0){
         $('#checkout-login-form').append(ph);
         } else {
         $('#login-form').parent().after(ph);
         }

         }, 1000);*/

        $('#login-form').parent().after(data_fbloginblock_login_buttons_authpage);
    }else if(fbloginblock_is16 == 1) {
        $('#login_form').parent('div').after(data_fbloginblock_login_buttons_authpage);
    }else {
        $('#login_form').after(data_fbloginblock_login_buttons_authpage);
    }


}

function display_fbloginblock_login_buttons_top(data_fbloginblock_login_buttons_top){
    $('body').prepend(data_fbloginblock_login_buttons_top);
}


function display_fbloginblock_login_buttons_footer(data_fbloginblock_login_buttons_footer){
    $('body').append(data_fbloginblock_login_buttons_footer);
}


function fbloginblock_fixed_safari_css_bug(){
    if(fbloginblock_is17) {
        /* fixed bug for icons CSS styles only for Prestashop 1.7.x.x and Safari */
        var inDesktopSafari = (navigator.userAgent.toLowerCase().indexOf('safari') !== -1 && navigator.userAgent.toLowerCase().indexOf('chrome') === -1) && typeof window.ontouchstart === 'undefined';

        var inMobileSafari = (navigator.userAgent.toLowerCase().indexOf('safari') !== -1 && navigator.userAgent.toLowerCase().indexOf('chrome') === -1) && typeof window.ontouchstart !== 'undefined';

        if (inDesktopSafari || inMobileSafari) {

            document.body.className += ' safari';
        }
        /* fixed bug for icons CSS styles only for Prestashop 1.7.x.x and Safari */
    }
}




function amazonlogin(){

    if(fbloginblockis_ssl==0) {
        alert(fbloginblockssltxt);
        return;
    } else if(fbloginblockamazonci == ''){
        alert(fbloginblock_aerror);
        return;
    } else {
        options = { scope : 'profile' };
        amazon.Login.authorize(options, fbloginblockamazon_url);
        return false;
    }

}

function init_amazon_fbloginblock(){

    //add div amazon-root
    if ($('div#amazon-root').length == 0)
    {
        FBRootDomAmazon = $('<div>', {'id':'amazon-root'});
        $('body').prepend(FBRootDomAmazon);
    }

    window.onAmazonLoginReady = function() {
        amazon.Login.setClientId(fbloginblockamazonci);
    };
    (function(d) {
        var a = d.createElement('script'); a.type = 'text/javascript';
        a.async = true; a.id = 'amazon-login-sdk';
        a.src = 'https://api-cdn.amazon.com/sdk/login1.js';
        d.getElementById('amazon-root').appendChild(a);
    })(document);

}