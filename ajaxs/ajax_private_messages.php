<?php
    require( "../config/config.inc.php" );

    if ( isset($_POST) && $_POST['order_id'] != "" && $_POST['private_message'] != "" ) {

    	$fullmsg_privado = str_replace( "\n", " - ",  str_replace( "\r", " - ", $_POST['prev_private_message'] . ". -|-  " . $_POST['private_message'] ) );

        $result_save_private_message = Db::getInstance()->execute("
            UPDATE `ps_orders`
            SET `private_message` = '".$fullmsg_privado."'
            WHERE `id_order` = '".$_POST['order_id']."';
        ");
    }

    die( true );

?>