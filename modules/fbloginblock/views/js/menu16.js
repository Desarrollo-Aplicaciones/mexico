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

function init_tabs(id){
	$('document').ready( function() {
		
		if(id == 99){
			$('#navtabs16 a[href="#basicsettings"]').tab('show'); 
		} else if(id == 6){ // type 6
			$('#navtabs16 a[href="#yahoo"]').tab('show');
		} else if(id == 2){ // type 2
			$('#navtabs16 a[href="#twitter"]').tab('show');
		} else if(id == 1){ // type 1
			$('#navtabs16 a[href="#facebook"]').tab('show');
		} else if(id == 3){ // type 3
			$('#navtabs16 a[href="#google"]').tab('show');
		} else if(id == 4){ // type 4
			$('#navtabs16 a[href="#linkedin"]').tab('show');
		} else if(id == 5){ // type 5
			$('#navtabs16 a[href="#hotmail"]').tab('show');
		} else if(id == 7){ // type 7
			$('#navtabs16 a[href="#instagram"]').tab('show');
		} else if(id == 20){ // type 20
			$('#navtabs16 a[href="#foursquare"]').tab('show');
		} else if(id == 21){ // type 21
			$('#navtabs16 a[href="#github"]').tab('show');
		} else if(id == 22){ // type 22
			$('#navtabs16 a[href="#disqus"]').tab('show');
		} else if(id == 24){ // type 24
			$('#navtabs16 a[href="#amazon"]').tab('show');
		} else if(id == 50){ // type 50
			$('#navtabs16 a[href="#dropbox"]').tab('show');
		} else if(id == 51){ // type 51
			$('#navtabs16 a[href="#scoop"]').tab('show');
		} else if(id == 52){ // type 52
			$('#navtabs16 a[href="#wordpress"]').tab('show');
		} else if(id == 53){ // type 53
			$('#navtabs16 a[href="#tumblr"]').tab('show');
		} else if(id == 54){ // type 54
			$('#navtabs16 a[href="#pinterest"]').tab('show');
		} else if(id == 55){ // type 55
			$('#navtabs16 a[href="#oklass"]').tab('show');
		} else if(id == 56){ // type 56
			$('#navtabs16 a[href="#mailru"]').tab('show');
		} else if(id == 57){ // type 57
			$('#navtabs16 a[href="#yandex"]').tab('show');
		}else if(id == 8){ // type 8
            $('#navtabs16 a[href="#paypal"]').tab('show');
        } else if(id == 58){ // type 58
            $('#navtabs16 a[href="#vkontakte"]').tab('show');
        } else if(id == 98){
            $('#navtabs16 a[href="#enabledisalbe"]').tab('show');
        }


    });
}