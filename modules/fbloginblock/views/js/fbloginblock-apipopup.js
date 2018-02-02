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


        if(fbloginblockapipopup == 1){
            display_htmlapipopup(fbloginblock_htmlapipopup);
        }

    });

});






function display_htmlapipopup(data_fbloginblock_htmlapipopup){


    if ($('div#fb-con-wrapper').length == 0)
    {
        conwrapper = '<div id="fb-con-wrapper"><\/div>';
        $('body').append(conwrapper);
    }

    if ($('div#fb-con').length == 0)
    {
        condom = '<div id="fb-con"><\/div>';
        $('body').append(condom);
    }

    $('div#fb-con').fadeIn(function(){

        $(this).css('filter', 'alpha(opacity=70)');
        $(this).bind('click dblclick', function(){
            $('div#fb-con-wrapper').hide();
            $(this).fadeOut();
            window.location.reload(true);
        });
    });


    $('div#fb-con-wrapper').html('<a id="button-close-apipopup" style="display: inline;"><\/a>'+data_fbloginblock_htmlapipopup).fadeIn();

    $("a#button-close-apipopup").click(function() {
        $('div#fb-con-wrapper').hide();
        $('div#fb-con').fadeOut();
        window.location.reload(true);
    });

}


function setCookiePopupBlock_fbloginblock (name, value, expires, path, domain, secure) {
    document.cookie = name + "=" + escape(value) +
        ((expires) ? "; expires=" + expires : "") +
        ((path) ? "; path=" + path : "") +
        ((domain) ? "; domain=" + domain : "") +
        ((secure) ? "; secure" : "");
        window.location.reload(true);
}


function update_social_api_email(){
    $('#fb-con-wrapper').css('opacity',0.8);

    var apiemail = $('#api-email').val();
    $.post(''+fbloginblockupdate_email+'',
        {cid:fbloginblockcid,
            email:apiemail
        },
        function (data) {
            if (data.status == 'success') {

                $('#fb-con-wrapper').html('');
                $('#fb-con-wrapper').html('<br/><p>'+data.params.content+'</p><br/>');
                $('#fb-con-wrapper').css('opacity',1);


            } else {
                $('#fb-con-wrapper').css('opacity',1);
                alert(data.message);


            }
        }, 'json');

}