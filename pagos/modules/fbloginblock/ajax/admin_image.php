<?php
/**
 * StorePrestaModules SPM LLC.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 *
 /*
 * 
 * @author    StorePrestaModules SPM
 * @category social_networks
 * @package fbloginblock
 * @copyright Copyright StorePrestaModules SPM
 * @license   StorePrestaModules SPM
 */

include(dirname(__FILE__).'/../../../config/config.inc.php');
include(dirname(__FILE__).'/../../../init.php');
ob_start(); 
$status = 'success';
$message = '';

include_once(dirname(__FILE__).'/../classes/facebookhelp.class.php');
$obj_facebookhelp = new facebookhelp();

$action = $_REQUEST['action'];

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

echo Tools::jsonEncode($response);

?>