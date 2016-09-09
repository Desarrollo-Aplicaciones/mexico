<?php
/*
* 2007-2013 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2013 PrestaShop SA
*  @version  Release: $Revision: 14011 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

require_once(dirname(__FILE__).'/../../config/config.inc.php');
require_once(_PS_MODULE_DIR_.'payulatam/payulatam.php');
require_once(_PS_MODULE_DIR_.'payulatam/config.php');

if(isset($_POST)  && !empty($_POST))
{

try {
       
$mysqldate = date("Y-m-d H:i:s");    
$logPost= $_POST;

$logPayu = new ConfPayu();
$logPayu->log_response_ws($logPost);
$logPayu->logtxt('Fecha de transacción-WS: '.$mysqldate.'\r\n'.var_export($logPost,TRUE));

} catch (Exception $exc) {
      Logger::AddLog('payulatam [validationws.php] log-response-payu error: ' . $exc->getTraceAsString(), 2, null, null, null, true);
    }

//$payU = new PayULatam();
//$payU->validationws();

}
