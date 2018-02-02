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

class FbloginblockLinkedinauthModuleFrontController extends ModuleFrontController
{

    public function initContent()
    {

        parent::initContent();
        $name_module = 'fbloginblock';

        if (version_compare(_PS_VERSION_, '1.7', '<')){
            require_once(_PS_MODULE_DIR_.$name_module.'/backward_compatibility/backward.php');
        }


        if(version_compare(_PS_VERSION_, '1.7', '>')) {
            require_once(_PS_MODULE_DIR_.$name_module. '/backward_compatibility/backward_functions.php');
            session_start_fbloginblock();
        }

        require_once(_PS_MODULE_DIR_.$name_module.'/lib/oAuth/linkedinoAuth.php');
        require_once(_PS_MODULE_DIR_.$name_module.'/lib/oAuth/class.linkedClass.php');
        include_once(_PS_MODULE_DIR_.$name_module.'/classes/linkedinhelp.class.php');
        $linkedinhelp = new linkedinhelp();


        $http_referer = isset($_REQUEST['http_referer'])?$_REQUEST['http_referer']:'';

        $lapikey = Configuration::get($name_module.'lapikey');
        $lapikey = trim($lapikey);
        $lsecret = Configuration::get($name_module.'lsecret');
        $lsecret = trim($lsecret);

        $data = array(
            'access' => $lapikey,
            'secret' => $lsecret,
        );



        $linkedClass   =   new linkedClass($data);
        # First step is to initialize with your consumer key and secret. We'll use an out-of-band oauth_callback
        $linkedin = new LinkedIn($data['access'], $data['secret']);
        //$linkedin->debug = true;

        if (isset($_REQUEST['oauth_verifier'])){
            $_SESSION['oauth_verifier']     = $_REQUEST['oauth_verifier'];

            $linkedin->request_token    =   unserialize($_SESSION['requestToken']);
            $linkedin->oauth_verifier   =   $_SESSION['oauth_verifier'];
            $linkedin->getAccessToken($_REQUEST['oauth_verifier']);
            $_SESSION['oauth_access_token'] = serialize($linkedin->access_token);
        }
        else{
            $linkedin->request_token    =   unserialize($_SESSION['requestToken']);
            $linkedin->oauth_verifier   =   $_SESSION['oauth_verifier'];
            $linkedin->access_token     =   unserialize($_SESSION['oauth_access_token']);
        }
        $content1 = $linkedClass->linkedinGetUserInfo($_SESSION['requestToken'], $_SESSION['oauth_verifier'], $_SESSION['oauth_access_token']);


        $xml   = simplexml_load_string($content1);



        $first_name = '';
        $last_name = '';
        $email_address = '';

        foreach ($xml as $name => $element) {
            switch($name){
                case 'first-name':
                    $first_name = (string) $element;
                    break;
                case 'last-name':
                    $last_name = (string) $element;
                    break;
                case 'email-address':
                    $email_address = (string) $element;
                    break;
            }
        }

        $data_profile = array('first_name'=>$first_name,
            'last_name'=>$last_name,
            'email'=>$email_address
        );


        $linkedinhelp->userLog(
            array('data'=>$data_profile,
                'http_referer_custom'=>$http_referer
            )
        );

        exit;


    }

}

?>