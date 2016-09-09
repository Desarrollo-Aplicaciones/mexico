<?php 
/*include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');
include(dirname(__FILE__).'/Caption11.php');

$caption = new Caption();
$caption->copyFromPost();
$caption->add();*/


// Located in /modules/mymodule/ajax.php
require_once(dirname(__FILE__).'/../../config/config.inc.php');
require_once(dirname(__FILE__).'/../../init.php');
include(dirname(__FILE__).'/csslider.php');
$csslider = new CsSlider();
echo $csslider->ajaxCallLoadCaption(Tools::getValue('id_caption'));
