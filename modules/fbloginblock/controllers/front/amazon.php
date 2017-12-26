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

class FbloginblockAmazonModuleFrontController extends ModuleFrontController
{

    public function initContent()
    {

        parent::initContent();

        $name_module = 'fbloginblock';

        if(version_compare(_PS_VERSION_, '1.7', '>')) {
            require_once(_PS_MODULE_DIR_.$name_module . '/backward_compatibility/backward_functions.php');
        }


        $http_referer = isset($_REQUEST['http_referer'])?$_REQUEST['http_referer']:'';




        if (version_compare(_PS_VERSION_, '1.5', '>')) {
            $cookie = new Cookie('ref');
            $cookie->http_referer_custom = $http_referer;
        }

        $cookie_clsa = new Cookie('clsa');
        $linksocialaccount = $cookie_clsa->linksocialaccount;
        if($linksocialaccount == 1)
            unset($cookie_clsa);



        include_once(_PS_MODULE_DIR_.$name_module.'/classes/amazonhelp.class.php');



        $aci = Configuration::get($name_module.'aci');
        $aci = trim($aci);


        $aru = Configuration::get($name_module.'aru');
        $aru = trim($aru);

        if(Tools::strlen($aci)==0 || Tools::strlen($aru)==0){
            echo "Error: Please fill Amazon Client ID and Amazon Allowed Return URL in the module settings!";exit;
        }

        if (Configuration::get('PS_SSL_ENABLED') == 0)
        {
            echo 'Note: To enable Amazon Connect, Please make sure that "SSL" has enabled on your server';exit;
        }


        //$_REQUEST['access_token'] = "Atza|IwEBIFDbeQFat1zK5ZKBUjJGsJzzexMab51TfrKb7iN77Wv_ZebndxTOJBo-eNSnMVPXae0wXyTNdY5NaxqCIGxSaqLqVfO5RgRQ3uL4YKv48bmzFljult0i4CzhLmGqNS6lIcx60MXPrsYQOVTF26FknkFWbq7-YOE3vDIY-p_zSjzORk5bD2Malwah1DLQ3Zm0oaei6KRdkP_SvCuCWpmEpn1MPJhe1oDZ7Omula7XtHKc3DdKGOQ26cvqPSXdSa06j5K_H6PncfmSFFO6gG6I9v3ts2mUaSbIvivQUKIYzXEy-LUP98PXZh4w9eLmWiRfccml4eh-OkeHYRlV2R30ZjHSGiBdrzSPLiyLQA_aHC7AtnB4GvpKhp33zF0yr7TfO0WQPl8XM5gAtKbCwzmdQgTcDLhWrMTl05_EmBbjEbprYnwwGYZ77hRiMLaJq-oqOd7TyjDxrvqTvWdS9xdZ8BEliCXx7yIVZrsGvrCRujwH_hpFxf2rgYS3yHZKKxzoMIRYi26_UEEiOoqsp3cK4X7DxWoTomeDpqioBTtQ-QP-tYEXB0rNps31CHC2obFKrXA";
        //$_REQUEST['access_token'] = "Atza|IwEBIEc-db9PNWIfvjg26Uc2bqPU6q1OfuF1yq-Un5ux3QQZuPCsLLWQuGNrqcrfNQkVmIrhMFqtpNWtv8V81UgTF4cr7zMN03kH-U1PKzYjINHlXFdwUzZL0GYsOoBDqVTkuni5NhELLZPXcnlDFIR4RWin9owHhy4zDokQzgb3YLOngOmGnRfl0G5HQJeCCgfQUOlJoYjeXvlv3msGKpBo4jXjNmf4iOSxz7ScbrRhWKoj6QlqQTTNSazeT0bXEtEqN6xOh3Tp_EIgT22rxnqGlUj90NVUIV4C_ue8tGELwuBFCZSbVkscFtxnEY_uuURovEBfGQnYNvzEGHTvM-_e3hrh2jIzkGuBVDTfm0220-qhZL3Ah3j1dQQqp1BKyFp6PxCsD28jJAzX1zS1PMbRA6hycVocWZWvHo_1xB20y_wLw9AUSI-V3q4zQEePRI617brsdNxdcJnCqK-Mypzbh451fUR8vwLSJi47N81NGQCuBbGHakLlQT-IlWEJm3MLh008OGThgKGbxeheZGBj9fE0LUhASISbRxMnAMt7XzdcmTPlBtwnMs8GVSC8u3QK0sysXhhQvie4FB_NjuzxT9Ew";

        // verify that the access token belongs to us
        $c = curl_init('https://api.amazon.com/auth/o2/tokeninfo?access_token=' . urlencode($_REQUEST['access_token']));
        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);

        $r = curl_exec($c);
        curl_close($c);
        $d = json_decode($r);



        if ($d->aud != $aci) {
            //echo 'Page not found'; exit;
        }

        // exchange the access token for user profile
        $c = curl_init('https://api.amazon.com/user/profile');
        curl_setopt($c, CURLOPT_HTTPHEADER, array('Authorization: bearer ' . $_REQUEST['access_token']));
        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);

        $r = curl_exec($c);
        curl_close($c);
        $d = json_decode($r);

        //echo sprintf('%s %s %s', $d->name, $d->email, $d->user_id);


        $first_name = $d->name;
        $last_name = $d->name;
        $email_address = $d->email;


        $data_first_and_last_names = explode(" ",$first_name);
        if(sizeof($data_first_and_last_names)>1){
            $first_name = $data_first_and_last_names[0];
            $last_name = $data_first_and_last_names[1];
        }

        $data_profile = array(
            'first_name'=>$first_name,
            'last_name'=>$last_name,
            'email'=>$email_address,

        );



        $amazonhelp = new amazonhelp();
        $red_url =$amazonhelp->userLog(
            array(
                'data'=>$data_profile,
                'http_referer_custom'=>$http_referer
            )
        );



        redirect_custom_fbloginblock($red_url);

        exit;

    }


}

?>