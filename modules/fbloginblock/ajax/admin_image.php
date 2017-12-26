<?php
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

include_once(dirname(__FILE__).'/../../../config/config.inc.php');
include_once(_PS_ROOT_DIR_.'../../../init.php');
ob_start(); 
$status = 'success';
$message = '';

$module_name = 'fbloginblock';

include_once(_PS_MODULE_DIR_.$module_name.'/classes/facebookhelp.class.php');
$obj_facebookhelp = new facebookhelp();

$action = $_REQUEST['action_custom'];

switch ($action){
	case 'returnimage':
		$type = Tools::getValue('type');
		if($type == "facebook"){
			$obj_facebookhelp->deleteImage(array('type'=>1));
		} elseif($type == "facebooksmall"){
			$obj_facebookhelp->deleteImage(array('type'=>4));
		}  elseif($type == "facebooklarge_small"){
			$obj_facebookhelp->deleteImage(array('type'=>17));
		}  elseif($type == "facebookmicro_small"){
			$obj_facebookhelp->deleteImage(array('type'=>18));
			
		}elseif($type == "twitter"){
			$obj_facebookhelp->deleteImage(array('type'=>7));
		} elseif($type == "twittersmall"){
			$obj_facebookhelp->deleteImage(array('type'=>8));
		}  elseif($type == "twitterlarge_small"){
			$obj_facebookhelp->deleteImage(array('type'=>19));
		}  elseif($type == "twittermicro_small"){
			$obj_facebookhelp->deleteImage(array('type'=>20));
			
		}elseif($type == "paypal"){
			$obj_facebookhelp->deleteImage(array('type'=>3));
		} elseif($type == "paypalsmall"){
			$obj_facebookhelp->deleteImage(array('type'=>6));
		} elseif($type == "paypallarge_small"){
			$obj_facebookhelp->deleteImage(array('type'=>21));
		}  elseif($type == "paypalmicro_small"){
			$obj_facebookhelp->deleteImage(array('type'=>22));
			
		} elseif($type == "google"){
			$obj_facebookhelp->deleteImage(array('type'=>2));
		}  elseif($type == "googlesmall"){
			$obj_facebookhelp->deleteImage(array('type'=>5));
		} elseif($type == "googlelarge_small"){
			$obj_facebookhelp->deleteImage(array('type'=>23));
		}  elseif($type == "googlemicro_small"){
			$obj_facebookhelp->deleteImage(array('type'=>24));
			
		} elseif($type == "yahoo"){
			$obj_facebookhelp->deleteImage(array('type'=>9));
		} elseif($type == "yahoosmall"){
			$obj_facebookhelp->deleteImage(array('type'=>10));
		} elseif($type == "yahoolarge_small"){
			$obj_facebookhelp->deleteImage(array('type'=>25));
		}  elseif($type == "yahoomicro_small"){
			$obj_facebookhelp->deleteImage(array('type'=>26));
			
		}elseif($type == "linkedin"){
			$obj_facebookhelp->deleteImage(array('type'=>11));
		} elseif($type == "linkedinsmall"){
			$obj_facebookhelp->deleteImage(array('type'=>12));
		} elseif($type == "linkedinlarge_small"){
			$obj_facebookhelp->deleteImage(array('type'=>27));
		}  elseif($type == "linkedinmicro_small"){
			$obj_facebookhelp->deleteImage(array('type'=>28));
			
		}elseif($type == "microsoft"){
			$obj_facebookhelp->deleteImage(array('type'=>13));
		} elseif($type == "microsoftsmall"){
			$obj_facebookhelp->deleteImage(array('type'=>14));
		} elseif($type == "microsoftlarge_small"){
			$obj_facebookhelp->deleteImage(array('type'=>29));
		}  elseif($type == "microsoftmicro_small"){
			$obj_facebookhelp->deleteImage(array('type'=>30));
			
		}elseif($type == "instagram"){
			$obj_facebookhelp->deleteImage(array('type'=>15));
		} elseif($type == "instagramsmall"){
			$obj_facebookhelp->deleteImage(array('type'=>16));
		} elseif($type == "instagramlarge_small"){
			$obj_facebookhelp->deleteImage(array('type'=>31));
		}  elseif($type == "instagrammicro_small"){
			$obj_facebookhelp->deleteImage(array('type'=>32));
			
		} elseif($type == "foursquare"){
			$obj_facebookhelp->deleteImage(array('type'=>33));
		} elseif($type == "foursquaresmall"){
			$obj_facebookhelp->deleteImage(array('type'=>34));
		} elseif($type == "foursquarelarge_small"){
			$obj_facebookhelp->deleteImage(array('type'=>35));
		}  elseif($type == "foursquaremicro_small"){
			$obj_facebookhelp->deleteImage(array('type'=>36));
			
		}elseif($type == "github"){
			$obj_facebookhelp->deleteImage(array('type'=>37));
		} elseif($type == "githubsmall"){
			$obj_facebookhelp->deleteImage(array('type'=>38));
		} elseif($type == "githublarge_small"){
			$obj_facebookhelp->deleteImage(array('type'=>39));
		}  elseif($type == "githubmicro_small"){
			$obj_facebookhelp->deleteImage(array('type'=>40));
			
		}elseif($type == "disqus"){
			$obj_facebookhelp->deleteImage(array('type'=>41));
		} elseif($type == "disqussmall"){
			$obj_facebookhelp->deleteImage(array('type'=>42));
		} elseif($type == "disquslarge_small"){
			$obj_facebookhelp->deleteImage(array('type'=>43));
		}  elseif($type == "disqusmicro_small"){
			$obj_facebookhelp->deleteImage(array('type'=>44));
			
		} elseif($type == "amazon"){
			$obj_facebookhelp->deleteImage(array('type'=>49));
		} elseif($type == "amazonsmall"){
			$obj_facebookhelp->deleteImage(array('type'=>50));
		} elseif($type == "amazonlarge_small"){
			$obj_facebookhelp->deleteImage(array('type'=>51));
		}  elseif($type == "amazonmicro_small"){
			$obj_facebookhelp->deleteImage(array('type'=>52));
			
		} elseif($type == "dropbox"){
			$obj_facebookhelp->deleteImage(array('type'=>53));
		} elseif($type == "dropboxsmall"){
			$obj_facebookhelp->deleteImage(array('type'=>54));
		} elseif($type == "dropboxlarge_small"){
			$obj_facebookhelp->deleteImage(array('type'=>55));
		}  elseif($type == "dropboxmicro_small"){
			$obj_facebookhelp->deleteImage(array('type'=>56));
			
		} elseif($type == "scoop"){
			$obj_facebookhelp->deleteImage(array('type'=>57));
		} elseif($type == "scoopsmall"){
			$obj_facebookhelp->deleteImage(array('type'=>58));
		} elseif($type == "scooplarge_small"){
			$obj_facebookhelp->deleteImage(array('type'=>59));
		}  elseif($type == "scoopmicro_small"){
			$obj_facebookhelp->deleteImage(array('type'=>60));
			
		} elseif($type == "wordpress"){
			$obj_facebookhelp->deleteImage(array('type'=>61));
		} elseif($type == "wordpresssmall"){
			$obj_facebookhelp->deleteImage(array('type'=>62));
		} elseif($type == "wordpresslarge_small"){
			$obj_facebookhelp->deleteImage(array('type'=>63));
		}  elseif($type == "wordpressmicro_small"){
			$obj_facebookhelp->deleteImage(array('type'=>64));
			
		} elseif($type == "tumblr"){
			$obj_facebookhelp->deleteImage(array('type'=>65));
		} elseif($type == "tumblrsmall"){
			$obj_facebookhelp->deleteImage(array('type'=>66));
		} elseif($type == "tumblrlarge_small"){
			$obj_facebookhelp->deleteImage(array('type'=>67));
		}  elseif($type == "tumblrmicro_small"){
			$obj_facebookhelp->deleteImage(array('type'=>68));
			
		} elseif($type == "pinterest"){
			$obj_facebookhelp->deleteImage(array('type'=>69));
		} elseif($type == "pinterestsmall"){
			$obj_facebookhelp->deleteImage(array('type'=>70));
		} elseif($type == "pinterestlarge_small"){
			$obj_facebookhelp->deleteImage(array('type'=>71));
		}  elseif($type == "pinterestmicro_small"){
			$obj_facebookhelp->deleteImage(array('type'=>72));
			
		} elseif($type == "oklass"){
			$obj_facebookhelp->deleteImage(array('type'=>73));
		} elseif($type == "oklasssmall"){
			$obj_facebookhelp->deleteImage(array('type'=>74));
		} elseif($type == "oklasslarge_small"){
			$obj_facebookhelp->deleteImage(array('type'=>75));
		}  elseif($type == "oklassmicro_small"){
			$obj_facebookhelp->deleteImage(array('type'=>76));
			
		} elseif($type == "mailru"){
			$obj_facebookhelp->deleteImage(array('type'=>77));
		} elseif($type == "mailrusmall"){
			$obj_facebookhelp->deleteImage(array('type'=>78));
		} elseif($type == "mailrularge_small"){
			$obj_facebookhelp->deleteImage(array('type'=>79));
		}  elseif($type == "mailrumicro_small"){
			$obj_facebookhelp->deleteImage(array('type'=>80));
			
		} elseif($type == "yandex"){
			$obj_facebookhelp->deleteImage(array('type'=>81));
		} elseif($type == "yandexsmall"){
			$obj_facebookhelp->deleteImage(array('type'=>82));
		} elseif($type == "yandexlarge_small"){
			$obj_facebookhelp->deleteImage(array('type'=>83));
		}  elseif($type == "yandexmicro_small"){
			$obj_facebookhelp->deleteImage(array('type'=>84));
			
		}  elseif($type == "vkontakte"){
            $obj_facebookhelp->deleteImage(array('type'=>45));
        } elseif($type == "vkontaktesmall"){
            $obj_facebookhelp->deleteImage(array('type'=>46));
        } elseif($type == "vkontaktelarge_small"){
            $obj_facebookhelp->deleteImage(array('type'=>47));
        }  elseif($type == "vkontaktemicro_small"){
            $obj_facebookhelp->deleteImage(array('type'=>48));

        }
	break;
	default:
		$status = 'error';
		$message = 'Unknown parameters!';
	break;
}


$response = new stdClass();
$content = ob_get_clean();
$response->status = $status;
$response->message = $message;	
$response->params = array('content' => $content);

echo json_encode($response);

?>