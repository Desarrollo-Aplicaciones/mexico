<?php
/**
 * 2011 - 2017 StorePrestaModules SPM LLC.
 *
 * MODULE fbloginblock
 *
 * @author    SPM <kykyryzopresto@gmail.com>
 * @copyright Copyright (c) permanent, SPM
 * @license   Addons PrestaShop license limitation
 * @version   1.7.9
 * @link      http://addons.prestashop.com/en/2_community-developer?contributor=61669
 *
 * NOTICE OF LICENSE
 *
 * Don't use this module on several shops. The license provided by PrestaShop Addons
 * for all its modules is valid only once for a single shop.
 */

class facebookSdkCustomhelper{

    private $_name_module = "fbloginblock";

    public function loadSDKLibrary($data){

        $redirect_uri = isset($data['redirect_uri'])?$data['redirect_uri']:'';


        spl_autoload_register('includeFacebookAPiClasses');

        $appid = Configuration::get($this->_name_module.'appid');
        $appid = trim($appid);
        $secret = Configuration::get($this->_name_module.'secret');
        $secret = trim($secret);


        if(Tools::strlen($appid)>0 && Tools::strlen($secret)>0) {

            $fb = new \Facebook\Facebook(array(
                'app_id' => $appid,
                'app_secret' => $secret,
                'default_graph_version' => 'v2.10',
            ));


            $helper = $fb->getRedirectLoginHelper();

            if (Tools::getValue('state')) {
                $helper->getPersistentDataHandler()->set('state', Tools::getValue('state'));
            }

            $permissions = array('email'); // Optional permissions
            $redirect_uri = $helper->getLoginUrl($redirect_uri, $permissions);
        }

        return $redirect_uri;
    }


    public function connectionSDKCustom($data){
        $appid = isset($data['appid'])?$data['appid']:'';
        $secret = isset($data['secret'])?$data['secret']:'';


        ### fix for oauth 18.10.2017 ###
        require_once(_PS_MODULE_DIR_.$this->_name_module. '/backward_compatibility/backward_session_start.php');
        session_start_fbloginblock_fix();


        spl_autoload_register('includeFacebookAPiClasses');


        $fb = new \Facebook\Facebook(array(
            'app_id' => $appid,
            'app_secret' => $secret,
            'default_graph_version' => 'v2.10',
            //'default_access_token' => '{access-token}', // optional
        ));



        try {
            // Get the \Facebook\GraphNodes\GraphUser object for the current user.
            // If you provided a 'default_access_token', the '{access-token}' is optional.


            $helper = $fb->getRedirectLoginHelper();

            if (Tools::getValue('state')) {
                $helper->getPersistentDataHandler()->set('state', Tools::getValue('state'));
            }

            $accessToken = $helper->getAccessToken();
            $access_token = $accessToken->getValue();

            $response = $fb->get('/me', $access_token);
        } catch(\Facebook\Exceptions\FacebookResponseException $e) {
            // When Graph returns an error
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch(\Facebook\Exceptions\FacebookSDKException $e) {
            // When validation fails or other local issues
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }


        $me = $response->getGraphUser();

        $id = $me->getId();

        /*$email = $me->getEmail();
        $first_name = $me->getFirstName();
        $last_name = $me->getLastName();
        $gender = $me->getGender();
        $birthday = $me->getBirthday();

        $location = $me->getLocation();

        //echo 'Logged in as ' . $me->getName();

        $me = array();
        $me['id'] = $id;
        $me['first_name'] = $first_name;
        $me['last_name'] = $last_name;
        $me['email'] = $email;
        $me['birthday'] = $birthday;
        $me['gender'] = $gender;*/

        ### get user info ###
        $url_fix = 'https://graph.facebook.com/v2.10/'.$id.'?fields=email,id,first_name,last_name,name,birthday,gender&access_token='.$access_token;


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url_fix);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($ch);

        $me = json_decode($data);
        $me = (array)$me;
        ### get user info ###



        if(!empty($access_token))
            $_SESSION['fb_access_token'] = (string) $access_token;

        ### fix for oauth 18.10.2017 ###


        return array('me'=>$me,'access_token'=>$access_token);
    }
}


function includeFacebookAPiClasses($classname){
    $classname = str_replace("\\","/",$classname);

    $path = dirname(__FILE__).'/../lib/'.$classname.'.php';

    if(file_exists($path)){
        include_once($path);
    }

}