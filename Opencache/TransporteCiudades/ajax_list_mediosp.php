<?php
require(dirname(__FILE__) . '/../../config/config.inc.php');

if (isset($_POST['get_mediosp']) && !empty($_POST['get_mediosp']) && $_POST['get_mediosp'] === 'mediosp') {


    $query = "select id_medio_de_pago,nombre,Activo from ps_medios_de_pago
LIMIT 10;";

    $mediosp = Db::getInstance()->executeS($query);
    ?>
    <table border="1">
        <tr> <th><b>Id</b></th> <th><b>Nombre</b></th> <th><b>Activar/Inactivar</b></th> </tr>
        <?php
        foreach ($mediosp as $value) {
            ?>

            <tr>
                <td><?php echo $value['id_medio_de_pago']; ?></td>
                <td><?php echo $value['nombre']; ?></td> 
                <td> <button value="<?php
                    if ($value['Activo']) {
                        echo 'ON_'.$value['id_medio_de_pago'];
                    } else {
                        echo 'OFF_'.$value['id_medio_de_pago'];
                    }
                    ?>"  id="medio_<?= $value['id_medio_de_pago'] ?>"  onclick="changemediop(this);"> <?php
                        if ($value['Activo']) {
                            echo 'Desactivar';
                        } else {
                            echo 'Activar';
                        }
                        ?></button> 

                </td> </tr>
            <?php
        }
        ?>
    </table>    
    <?php
} elseif(isset ($_POST['mediosp']) && $_POST['mediosp'] && isset ($_POST['status']) && !empty ($_POST['status']) && isset ($_POST['id_mediop']) && !empty ($_POST['id_mediop']) ){
   
    $status = -1;
    $msg = "";
    
    if($_POST['status']=='ON'){
        $status=0;
           $msg= "Activar_OFF";
    } elseif ($_POST['status']=='OFF') {
    $status=1;
       $msg= "Desactivar_ON";
}

 
if($status !=  -1) {
    $query="update ps_medios_de_pago
            SET Activo = ".(int)$status.""
        . " WHERE id_medio_de_pago = ".(int)$_POST['id_mediop'].";";
    if(Db::getInstance()->execute($query)) 
    {
        echo $msg;
        exit();
    }
    echo '0';
}
echo '0';exit();
}else {
    echo '0';
}
?>