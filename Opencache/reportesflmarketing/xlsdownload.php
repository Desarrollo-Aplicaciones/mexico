<?php

include_once(dirname(__FILE__)."/../../config/config.inc.php");

if (isset($_GET['opc_sel']) ) {

    switch ($_GET['opc_sel']) {
        case 'infencu':
            $date_format = 'Y-m-d';
            $input1 = $_GET['f_ini'];
            $input2 = $_GET['f_fin'];
            $input1 = trim($input1);
            $input2 = trim($input2);
            $time1 = strtotime($input1);
            $time2 = strtotime($input2);

            if ( date($date_format, $time1) == $input1 && date($date_format, $time2) == $input2 && isset($_GET['f_ini']) && $_GET['f_ini'] != '' && isset($_GET['f_fin']) && $_GET['f_fin'] != '' ) {

                header("Content-type: application/force-download");
                header("Content-Disposition: attachment; filename=".basename("Informe_Encuestas_MEX.csv"));
                header("Content-Transfer-Encoding: binary"); 

                $sql = "SELECT
                        COUNT(oq.id_order) '# Ordenes sin respuesta',
                        NOW() 'Fecha Reporte',
                        o.date_add 'Fecha de la compra',
                        oh.date_add 'Fecha de la entrega',
                        qs.date_qualification 'Fecha de la calificacion',
                        o.id_order 'Id Order',
                        o.id_customer 'Id Customer',
                        o.total_paid_real 'Valor de la factura',
                        qs.qualification 'Calificacion',
                        qs.comments 'Comentario',
                        osl.name state_order
                    FROM ps_quality_score qs
                    INNER JOIN ps_orders o ON ( qs.id_order = o.id_order )
                    INNER JOIN ps_order_state_lang osl ON ( o.current_state = osl.id_order_state )
                    INNER JOIN ps_order_history oh ON ( o.id_order = oh.id_order )
                    LEFT JOIN ps_order_quality oq ON ( 1 = 1 )
                    WHERE ( oh.id_order_state = 5 OR oh.id_order_state = 6 )
                    AND qs.date_qualification BETWEEN '".$input1." 00:00:00' AND '".$input2." 23:59:59'
                    GROUP BY qs.id_order
                    ORDER BY qs.date_qualification ASC";

                if ($results = Db::getInstance_slv()->ExecuteS($sql)) {

                    $output = fopen('php://output', 'w');

                    fputcsv($output, array('# Ordenes sin respuesta', utf8_decode( $results[0]['# Ordenes sin respuesta'] )  ) );
                    fputcsv($output, array('Fecha Reporte', utf8_decode( $results[0]['Fecha Reporte'] )  ) );
                    fputcsv($output, array("Fecha de la compra", "Fecha de la entrega", "Fecha de la calificacion", "Id Order", "Id Customer", "Estado Orden", "Valor de la factura", "Calificacion", "Comentario"  ) );

                            foreach ($results as $dat_print) {

                                fputcsv($output, array( utf8_decode( $dat_print['Fecha de la compra'] ), utf8_decode( $dat_print['Fecha de la entrega'] ), utf8_decode( $dat_print['Fecha de la calificacion'] ), utf8_decode( $dat_print['Id Order'] ), utf8_decode( $dat_print['Id Customer'] ), utf8_decode( $dat_print['state_order'] ), utf8_decode( $dat_print['Valor de la factura'] ), utf8_decode( $dat_print['Calificacion'] ), utf8_decode( $dat_print['Comentario'] ) ) );

                            }

                } else {
                    header("Content-type: application/force-download");
                    header("Content-Disposition: attachment; filename=".basename("No_se_pudo_generar_la_consulta.csv"));
                    header("Content-Transfer-Encoding: binary");
                }
            } else {
                header("Content-type: application/force-download");
                header("Content-Disposition: attachment; filename=".basename("Formato_de_fecha_erroneo_o_fecha_incorrecta.csv"));
                header("Content-Transfer-Encoding: binary"); 
            }
        break;


        case 'infseo':
            header("Content-type: application/force-download");
            header("Content-Disposition: attachment; filename=".basename("Informe_SEO_MEX.csv"));
            header("Content-Transfer-Encoding: binary"); 

            $sql = "SELECT
                        pr.id_product,
                        pr.reference,
                        pl.name AS NombreProducto,
                        pl.description AS Descripcion,
                        pl.description_short AS DescripcionCorta,
                        pl.meta_title,
                        length(pl.meta_title) AS LargoMetaTitulo,
                        IF(length(pl.meta_title)>=60 AND length(pl.meta_title)<=65,'Largo OK', 'Revisión Largo') AS EstadoMetaTitulo,
                        pl.meta_description,
                        length(pl.meta_description) AS LargoMetaDescription,
                        IF(length(pl.meta_description)>=117 AND length(pl.meta_description)<=122,'Largo OK', 'Revisión Largo') AS EstadoMetaDescription,
                        pr.active,
                        REPLACE(pr.price,'.',',') AS price,
                        sum(od.product_quantity) AS UnidadesVendidas,
                        im.FotosCargadas
                    FROM ps_product_lang pl 
                    INNER JOIN ps_product pr ON pl.id_product = pr.id_product
                    LEFT JOIN ps_order_detail od ON pr.id_product=od.product_id
                    LEFT JOIN (
                        SELECT
                            id_product,
                            count(*) AS FotosCargadas
                        FROM  ps_image
                        GROUP BY id_product
                    ) im ON im.id_product=pr.id_product
                    GROUP BY pr.id_product";

            if ($results = Db::getInstance_slv()->ExecuteS($sql)) {
                 $output = fopen('php://output', 'w');

                    fputcsv($output, array( "Producto", "Referencia", "Nombre", "Meta Titulo", "Meta Titulo Largo", "Estado Meta Titulo", "Meta Descripcion", "Meta Descripcion Largo", "Estado Meta Descripcion", "Activo", "Precio", "Unidades Vendidas", "Fotos Cargadas" ) );
/*
                echo "<table border=1>
                        <tr>
                            <td>Producto</td>
                            <td>Referencia</td>
                            <td>Nombre</td>
                            <td>Meta Titulo</td>
                            <td>Meta Titulo Largo</td>
                            <td>Estado Meta Titulo</td>
                            <td>Meta Descripcion</td>
                            <td>Meta Descripcion Largo</td>
                            <td>Estado Meta Descripcion</td>
                            <td>Activo</td>
                            <td>Precio</td>
                            <td>Unidades Vendidas</td>
                            <td>Fotos Cargadas</td>
                        </tr>";*/
                        foreach ($results as $dat_print) {


                    fputcsv($output, array( utf8_decode( $dat_print['id_product'] ), utf8_decode( $dat_print['reference'] ), utf8_decode( $dat_print['NombreProducto'] ), utf8_decode( $dat_print['meta_title'] ), utf8_decode( $dat_print['LargoMetaTitulo'] ), utf8_decode( $dat_print['EstadoMetaTitulo'] ), utf8_decode( $dat_print['meta_description'] ), utf8_decode( $dat_print['LargoMetaDescription'] ), utf8_decode( $dat_print['EstadoMetaDescription'] ), utf8_decode( $dat_print['active'] ), utf8_decode( $dat_print['price'] ), utf8_decode( $dat_print['UnidadesVendidas'] ), utf8_decode( $dat_print['FotosCargadas'] ) ) );
/*
                             echo "<tr>
                                        <td> ". utf8_decode( $dat_print['id_product'] ) ."</td>
                                        <td> ". utf8_decode( $dat_print['reference'] ) ."</td>
                                        <td> ". utf8_decode( $dat_print['NombreProducto'] ) ."</td>
                                        <td> ". utf8_decode( $dat_print['meta_title'] ) ."</td>
                                        <td> ". utf8_decode( $dat_print['LargoMetaTitulo'] ) ."</td>
                                        <td> ". utf8_decode( $dat_print['EstadoMetaTitulo'] ) ."</td>
                                        <td> ". utf8_decode( $dat_print['meta_description'] ) ."</td>
                                        <td> ". utf8_decode( $dat_print['LargoMetaDescription'] ) ."</td>
                                        <td> ". utf8_decode( $dat_print['EstadoMetaDescription'] ) ."</td>
                                        <td> ". utf8_decode( $dat_print['active'] ) ."</td>
                                        <td> ". utf8_decode( $dat_print['price'] ) ."</td>
                                        <td> ". utf8_decode( $dat_print['UnidadesVendidas'] ) ."</td>
                                        <td> ". utf8_decode( $dat_print['FotosCargadas'] ) ."</td>
                                    </tr>";*/
                        }
               /* echo "</table>";*/
            } else {
                header("Content-type: application/force-download");
                header("Content-Disposition: attachment; filename=".basename("No_se_pudo_generar_la_consulta.csv"));
                header("Content-Transfer-Encoding: binary");
            }
        break;

        
        default:
            header("Content-type: application/force-download");
            header("Content-Disposition: attachment; filename=".basename("Ninguna_opcion_seleccionada.csv"));
            header("Content-Transfer-Encoding: binary"); 
        break;
    }
}

?>