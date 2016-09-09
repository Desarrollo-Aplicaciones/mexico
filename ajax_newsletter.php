<?php

require(dirname(__FILE__) . '/config/config.inc.php');

try {

//    echo '<pre>';
//    print_r($_POST);
//    exit();

    if (isset($_POST['mail']) && !empty($_POST['mail'])) {

        $email = $_POST['mail'];
        $id_shop = 1;
        $id_shop_group = 1;
        $newsletter_date_add = date('Y-m-d H:i:s');
        $ip_registration_newsletter = Tools::getRemoteAddr();
        $active = 1;

        if (isset($_POST['sex']) && $_POST['sex'] === 'M') {
            $id_gender = 1;
        }elseif (isset($_POST['sex']) && $_POST['sex'] === 'F') {
            $id_gender = 2;
        }

        if (isset($_POST['nombre']) && !empty($_POST['nombre'])) {
            $newsletter_name = $_POST['nombre'];
        } else {
            $newsletter_name = NULL;
        }
        if (isset($_POST['mx']) && !empty($_POST['mx'])) {
            $mx = $_POST['mx'];
        } else {
            $mx = 0;
        }

        $option = array();

        for ($i = 1; $i <= 6; $i++) {
            if (isset($_POST['option' . $i]) && !empty($_POST['option' . $i]) && $_POST['option' . $i]==='true') {
                $option[$i] = 'si';
            } else {
                $option[$i] = 'no';
            }
        }

        $expr = "/[\w-\.]{3,}@([\w-]{2,}\.)*([\w-]{2,}\.)[\w-]{2,4}/";

        if (!preg_match($expr, $email)) {
            // no es un correo valido
            echo 'error2';
            exit();
        } else {

            try {
                
             $sql = "select id_campaign_newsletter as id_campaign from ps_campaign_newsletter
                     WHERE ` active` ='si';";  
             $results = Db::getInstance()->ExecuteS($sql);
             $id_campaign=$results['0']['id_campaign'];

                $sql = "SELECT email FROM " . _DB_PREFIX_ . "newsletter WHERE email='$email'";

$results = Db::getInstance()->ExecuteS($sql); 

                if (isset($results)) {
                    // si el correo existe actualizamos las opciones    
                    if (isset($results[0]['email']) && strtolower($results[0]['email']) === strtolower($email)) {
                        Db::getInstance()->update('newsletter', array(
                            'option1' => pSQL($option[1]),
                            'option2' => pSQL($option[2]),
                            'option3' => pSQL($option[3]),
                            'option4' => pSQL($option[4]),
                            'option5' => pSQL($option[5]),
                            'option6' => pSQL($option[6]),
                            'id_campaign_newsletter' => (int)$id_campaign, ), $where = "email = '".$email."'", $limit = 1, $null_values = false, $use_cache = FALSE, $add_prefix = true);
                    } // si el correo no existe se crea la suscripción 
                    else {
                        
                        Db::getInstance()->insert('newsletter', array(
                            'id_shop' => (int) $id_shop,
                            'id_shop_group' => (int) $id_shop_group,
                            'email' => pSQL($email),
                            'newsletter_date_add' => $newsletter_date_add,
                            'ip_registration_newsletter' => pSQL($ip_registration_newsletter),
                            'active' => (int) $active,
                            'newsletter_name' => pSQL($newsletter_name ? $newsletter_name : ''),
                            'id_gender' => (int) $id_gender,
                            'mx' => (int) $mx,
                            'option1' => pSQL($option[1]),
                            'option2' => pSQL($option[2]),
                            'option3' => pSQL($option[3]),
                            'option4' => pSQL($option[4]),
                            'option5' => pSQL($option[5]),
                            'option6' => pSQL($option[6]),
                            'id_campaign_newsletter' => (int)$id_campaign,));

                        if ($mx != "1") {
                            // solo a los usuarios nuevos que no sean de mexico se les envia correo   
                            //Mail::Send(1, 'newsletter_welcome', 'Bienvenido al boletin de Farmalisto', null, $email, null, null, null, null, null, _PS_ROOT_DIR_ . '/mails/', false, 1);
                        }
                    }

                    // cookie por una año
                    setcookie("newsletter", 'newsletter', time() + 3600 * 24 * 30 * 365);
                echo 'ok';
                exit();
                }
            } catch (Exception $exc) {
                // no fue posible crear la suscripción en el sistema 
                echo 'error3';
                exit();
            }
            // se creo la suscripción o se actualizaron los datos correctamente   
            echo 'no';
            exit();
        }
        // no inserto un cerreo 
        echo 'error1';
    }
} catch (Exception $exc) {
// error indeterminado
    echo 'error4';
    exit();
}
// error indeterminado 2
echo 'error5';
