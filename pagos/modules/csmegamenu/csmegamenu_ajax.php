<?php 
require_once(dirname(__FILE__).'/../../config/config.inc.php');
require_once(dirname(__FILE__).'/../../init.php');
require_once(dirname(__FILE__).'/csmegamenu.php');
include_once(dirname(__FILE__).'/classes/CsMegaMenuClass.php');
$CsMegaMenu = new CsMegaMenu();

if(Tools::getValue('id_menu'))
{
	echo $CsMegaMenu->_displayUpdateForm(Tools::getValue('id_menu'));
}


