<?php

require(dirname(__FILE__).'/../../config/config.inc.php');

header("Content-type: text/csv");
header("Content-Disposition: attachment; filename=Listado_ciudades_codigo_postal.csv");
header("Pragma: no-cache");
header("Expires: 0");


$query_list = "
SELECT cp.codigo_postal, cp.nombre, cc.id_city, cc.city_name, s.id_state, s.`name` 
FROM "._DB_PREFIX_."state s 
LEFT JOIN "._DB_PREFIX_."cities_col cc ON ( s.id_state = cc.id_state AND s.id_country = ".(int)Configuration::get('PS_COUNTRY_DEFAULT').")
LEFT JOIN "._DB_PREFIX_."cod_postal cp ON ( cc.id_city = cp.id_ciudad )
WHERE cc.city_name IS NOT NULL
ORDER BY s.`name`, cc.city_name, cp.nombre ASC";


if ( $res = Db::getInstance()->executeS($query_list) ) {
    echo "codigo_postal; colonia; id_ciudad; ciudad; id_estado; estado
";
    foreach ($res as $row) {
        echo utf8_decode($row['codigo_postal']).";".utf8_decode($row['nombre']).";".utf8_decode($row['id_city']).";".utf8_decode($row['city_name']).";".utf8_decode($row['id_state']).";".utf8_decode($row['name'])."
";
	}
}



?>