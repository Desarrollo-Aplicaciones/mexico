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

include_once(dirname(__FILE__).'/../../config/config.inc.php');
include_once(_PS_ROOT_DIR_.'/init.php');

$action = isset($_REQUEST['action'])?$_REQUEST['action']:'';
$http_referer = isset($_REQUEST['http_referer'])?urldecode($_REQUEST['http_referer']):'';

if (version_compare(_PS_VERSION_, '1.5', '>')){
$cookie = new Cookie('ref');
$cookie->http_referer_custom = $http_referer;
}

$name_module = "fbloginblock";

include_once(_PS_MODULE_DIR_.$name_module.'/fbloginblock.php');
$obj_fbloginblock_ps14_13 = new fbloginblock();
$obj_fbloginblock_ps14_13->setCookieForPrestashop14_13();




include_once(_PS_MODULE_DIR_.$name_module.'/classes/twitter.class.php');


$consumer_key = Configuration::get($name_module.'twitterconskey');
$consumer_key = trim($consumer_key);
$consumer_secret = Configuration::get($name_module.'twitterconssecret');
$consumer_secret = trim($consumer_secret);
$callback = "";


if(Tools::strlen($consumer_key)==0 || Tools::strlen($consumer_secret)==0){
    echo "Error: Please fill Twitter Consumer key, Twitter Consumer secret in the module settings!";
    exit;
}

$data = array('key'=>$consumer_key,
		'secret' =>$consumer_secret,
		'callback' => $callback,
		'http_referer'=>$http_referer
);

$obj = new twitter($data);
$obj->twitterLogin(array('action'=>$action));


        
?>