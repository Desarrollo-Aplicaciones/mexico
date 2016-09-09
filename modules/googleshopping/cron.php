<?php
set_time_limit ( 3600 );
include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');
include(dirname(__FILE__).'/googleshopping.php');

$module=new GoogleShopping();
$module->generateFileList();
die ('OK');

?>
