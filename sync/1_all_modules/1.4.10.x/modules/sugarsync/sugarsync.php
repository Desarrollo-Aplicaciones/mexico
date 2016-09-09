<?php

/*
  @copyright  2007-2011 PrestaShop SA
  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*/

/***************************************************************************************************
* Copyright(c) @2011 ANTERP SOLUTIONS. All rights reserved.
* Website				http://www.cartbooks.com
* Authors		    	tclim
* Date Created     		May 26, 2012 4:38:48 PM
* 
* Additional License	This software require you to buy from ANTERP SOLUTIONS. 
* 						You have no right to redistribute this program.
* 
* Description			Data Sync Suites developed and distributed by ANTERP SOLUTIONS.
*  
 **************************************************************************************************/

if (!defined('_PS_VERSION_'))
	exit;

class SugarSync extends Module
{
	private $_html = '';
	private $_postErrors = array();
	private $_cookie;

	public function __construct()
	{
		global $cookie;
		$this->_cookie = $cookie;
		$this->name = 'sugarsync';
        $this->tab = "pricing_promotion";
		$this->displayName = $this->l('CartBooks Accounting Connector');
		$this->version = '1.0';
		$this->author = 'cartbooks.com';

		parent::__construct();
		
		$this->displayName = $this->l('SugarCRM Data Sync Suites');
		$this->description = $this->l('Data Sync Suites for PrestaShop SugarCRM Integration. Automatically synchronize Customers, Categories, Products, SalesOrder/Invoices.');
		$this->password = $this->l('Data Sync Suite Powered by CartBooks.com');
	}

	public function install()
	{	
		if (!parent::install())
			return false;
		
	// Install SQL
		include(dirname(__FILE__).'/sql-install.php');
		foreach ($sql as $s)
			if (!Db::getInstance()->Execute($s))
				return false;
		
		return true;
	}
	
	public function uninstall()
	{
		// Uninstall SQL
		include(dirname(__FILE__).'/sql-uninstall.php');
		foreach ($sql as $s)
			if (!Db::getInstance()->Execute($s))
				return false;

		Configuration::deleteByName('sugar_sync_status');
		Configuration::deleteByName('sugar_sync_cartversion');
		Configuration::deleteByName('sugar_sync_debug_mode');
		Configuration::deleteByName('sugar_sync_server');
		Configuration::deleteByName('sugar_sync_memory_limit');
		Configuration::deleteByName('sugar_sync_data_sync_url');
		Configuration::deleteByName('sugar_sync_url');
		Configuration::deleteByName('sugar_sync_user_id');
		Configuration::deleteByName('sugar_sync_password');
		Configuration::deleteByName('sugar_sync_password_auth');
        Configuration::deleteByName('sugar_sync_default_item_code');
        Configuration::deleteByName('sugar_sync_default_part_number');
		Configuration::deleteByName('sugar_sync_delivered_status');
		Configuration::deleteByName('sugar_sync_invoice_void');
		Configuration::deleteByName('sugar_sync_invoice_status');
		Configuration::deleteByName('sugar_sync_def_void_sts');
		Configuration::deleteByName('sugar_sync_def_inv_sts');
		Configuration::deleteByName('sugar_sync_def_inv_tpl');
		Configuration::deleteByName('sugar_sync_disc_type');		

		parent::uninstall();
	}

	private function _postProcess()
	{
		$status = Configuration::get('sugar_sync_status');
		$cartversion = Configuration::get('sugar_sync_cartversion');
		$debug_mode = Configuration::get('sugar_sync_debug_mode');
		$server = Configuration::get('sugar_sync_server');
		$memory_limit = Configuration::get('sugar_sync_memory_limit');
		$data_sync_url = Configuration::get('sugar_sync_data_sync_url');
		$url = Configuration::get('sugar_sync_url');
		$user_id = Configuration::get('sugar_sync_user_id');
		$password = Configuration::get('sugar_sync_password');
		$password_auth = Configuration::get('sugar_sync_password_auth');
	    $default_item_code = Configuration::get('sugar_sync_default_item_code');
	    $default_part_number = Configuration::get('sugar_sync_default_part_number');
		$invoice_start_date = Configuration::get('sugar_sync_invoice_start_date');
		$delivered_status = Configuration::get('sugar_sync_delivered_status');
		$void_invoice_status = Configuration::get('sugar_sync_invoice_void');
		$invoice_status = Configuration::get('sugar_sync_invoice_status');
		$default_void_invoice_status = Configuration::get('sugar_sync_def_void_sts');
		$default_invoice_status = Configuration::get('sugar_sync_def_inv_sts');
		$default_invoice_template = Configuration::get('sugar_sync_def_inv_tpl');
		$discount_type_final = Configuration::get('sugar_sync_disc_type');
		
	}

	private function _displayForm()
	{
		global $cookie;
		
		$states = OrderState::getOrderStates((int)($cookie->id_lang));
		$cartversioning = $this->getSyncLookUp('cartversioning');
		$servers = $this->getSyncLookUp('server');
		$memory_limits = $this->getSyncLookUp('memory_limit');
		$item_code_mappings = $this->getSyncLookUp('item_code_mapping');
		$part_number_mappings = $this->getSyncLookUp('part_number_mapping');
		$cartbooks_invoice_status = $this->getSyncResultStatus('PS_SALESINVOICE', 'cartbooks');
		$invoice_statuses = $this->getSyncLookUp('invoicestatus');
		$invoice_templates = $this->getSyncLookUp('invoice_template');
		
		$enabled_array =  array(array('id' => '1', 'text' => 'Enabled'),
                    	        array('id' => '0', 'text' => 'Disabled'));
		
		$true_false_array = array(array('id' => '1', 'text' => 'Yes'),
           		                  array('id' => '0', 'text' => 'No'));

        $default_item_code = Configuration::get('sugar_sync_default_item_code');
        $default_part_number = Configuration::get('sugar_sync_default_part_number');
        $sugar_sync_invoice_start_date = explode('-', Configuration::get('sugar_sync_invoice_start_date') != '' ? Configuration::get('sugar_sync_invoice_start_date') : date('Y-m-d'));
        
        $sl_year = $sugar_sync_invoice_start_date[0];
		$years = $this->currentDateYears();
		$sl_month = $sugar_sync_invoice_start_date[1];
		$months = Tools::dateMonths();
		$sl_day = $sugar_sync_invoice_start_date[2];
		$days = Tools::dateDays();
		$tab_months = array(
			$this->l('January'),
			$this->l('February'),
			$this->l('March'),
			$this->l('April'),
			$this->l('May'),
			$this->l('June'),
			$this->l('July'),
			$this->l('August'),
			$this->l('September'),
			$this->l('October'),
			$this->l('November'),
			$this->l('December'));
							
    	$invoice_start_date = $sl_year . "-" . $sl_month . "-" . $sl_day;

		$auth_array = array(array('id' => '1', 'text' => 'Standard Authentication'),
           		            array('id' => '0', 'text' => 'LDAP Authentication'));

		$discount_type_array = array(array('id' => 'Percentage', 'text' => 'Percentage'),
							   array('id' => 'Amount', 'text' => 'Amount'));
           		                             		                  
		$this->_html .=
			'<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
				<fieldset>
					<legend><img src="../img/admin/cog.gif" alt="" class="middle" />'.$this->l('General Settings').'</legend>
					<br />';
					
					$this->_html .= '<label>'.$this->l('Status: ').'</label>
					<div class="margin-form">';
		
					$this->_html .= '<select name="status">';
		                  
						foreach ($enabled_array AS $status)
							$this->_html .= '<option value="'.$status['id'].'"'.(($status['id'] == Configuration::get('sugar_sync_status')) ? ' selected="selected"' : '').'>'.stripslashes($status['text']) . '<br/>';
						$this->_html .= '</select>';
						
					$this->_html .= '</div>';
					
					$this->_html .= '<label>'.$this->l('Version: ').'</label>
					<div class="margin-form">';
		
					$this->_html .= '<select name="cartversion">';
		
						foreach ($cartversioning AS $cartversion)
							$this->_html .= '<option value="'.$cartversion['ref_key'].'"'.(($cartversion['ref_key'] == Configuration::get('sugar_sync_cartversion')) ? ' selected="selected"' : '').'>'.stripslashes($cartversion['name']).'</option>';
						$this->_html .= '</select>';
						
					$this->_html .= '</div>';
					
					$this->_html .= '<label>'.$this->l('Is Debugging Mode: ').'</label>
					<div class="margin-form">';
					$this->_html .= '<select name="debug_mode">';
		                  
						foreach ($true_false_array AS $debug_mode)
							$this->_html .= '<option value="'.$debug_mode['id'].'"'.(($debug_mode['id'] == Configuration::get('sugar_sync_debug_mode')) ? ' selected="selected"' : '').'>'.stripslashes($debug_mode['text']) . '<br/>';
						$this->_html .= '</select>';
						
					$this->_html .= '</div>';
					
					$this->_html .= '<label>'.$this->l('I am running on: ').'</label>
					<div class="margin-form">';
					$this->_html .= '<select name="server">';
		                  
						foreach ($servers AS $server)
							$this->_html .= '<option value="'.$server['ref_key'].'"'.(($server['ref_key'] == Configuration::get('sugar_sync_server')) ? ' selected="selected"' : '').'>'.stripslashes($server['name']) . '<br/>';
						$this->_html .= '</select>';
						
					$this->_html .= '&nbsp;&nbsp;<select name="memory_limit">';
		                  
						foreach ($memory_limits AS $memory_limit)
							$this->_html .= '<option value="'.$memory_limit['ref_key'].'"'.(($memory_limit['ref_key'] == Configuration::get('sugar_sync_memory_limit')) ? ' selected="selected"' : '').'>'.stripslashes($memory_limit['name']) . '<br/>';
						$this->_html .= '</select>';						
						
					$this->_html .= '</div>';
					
					$this->_html .= '<label>'.$this->l('Data Sync URL: ').'</label>
					<div class="margin-form">
						<input name="data_sync_url" type="text" style="width: 600px;" value="'.Tools::getValue('data_sync_url', Configuration::get('sugar_sync_data_sync_url')).'"/>
						<p class="clear">'.$this->l('Example:').' http://' . $_SERVER['HTTP_HOST'] . '/sugarprestashopsync</p>
					</div>';

					$this->_html .= '</fieldset>';
					
					$this->_html .= '<br/>';
					
					$this->_html .= '<fieldset>
					<legend><img src="../img/admin/cog.gif" alt="" class="middle" />'.$this->l('SugarCRM Settings').'</legend>
					<br />';
					
					$this->_html .= '<label>'.$this->l('SugarCRM URL: ').'</label>
					<div class="margin-form">
						<input name="url" type="text" style="width: 600px;" value="'.Tools::getValue('url', Configuration::get('sugar_sync_url')).'"/>
						<p class="clear">'.$this->l('Example:').' http://' . $_SERVER['HTTP_HOST'] . '/sugarcrm/service/v2/rest.php</p>
					</div>';
					
					$this->_html .= '<label>'.$this->l('User ID: ').'</label>
					<div class="margin-form">
						<input name="user_id" type="text" style="width: 600px;" value="'.Tools::getValue('user_id', Configuration::get('sugar_sync_user_id')).'"/>
					</div>
					<label>'.$this->l('Password: ').'</label>
					<div class="margin-form">
						<input name="password" type="password" style="width: 600px;" value="'.Tools::getValue('password', Configuration::get('sugar_sync_password')).'"/>
					</div>';
					
					$this->_html .= '<label>'.$this->l('Password Authentication: ').'</label>
					<div class="margin-form">';
					$this->_html .= '<select name="password_auth">';
		                  
						foreach ($auth_array AS $password_auth)
							$this->_html .= '<option value="'.$password_auth['id'].'"'.(($password_auth['id'] == Configuration::get('sugar_sync_password_auth')) ? ' selected="selected"' : '').'>'.stripslashes($password_auth['text']) . '<br/>';
						$this->_html .= '</select>';
						
					$this->_html .= '</div>';
					
					$this->_html .= '</fieldset>';
					
					$this->_html .= '<br/>';
					
					$this->_html .= '<fieldset>
					<legend><img src="../img/admin/cog.gif" alt="" class="middle" />'.$this->l('Product Settings').'</legend>
					<br />';
					
					$this->_html .= '<label>'.$this->l('Item Code Mapping: ').'</label>
					<div class="margin-form">';
					
					if ($cartbooks_invoice_status == 1) {						
						$this->_html .= '<b>' . $default_item_code . '<b/>'; 
						$this->_html .= '<input name="default_item_code" type="hidden" value="'.Tools::getValue($default_item_code, Configuration::get('sugar_sync_default_item_code')).'"/>';					
              		} else {
						$this->_html .= '<select name="default_item_code">';
			
							foreach ($item_code_mappings AS $item_code_mapping)
								$this->_html .= '<option value="'.$item_code_mapping['ref_key'].'"'.(($item_code_mapping['ref_key'] == Configuration::get('sugar_sync_default_item_code')) ? ' selected="selected"' : '').'>'.stripslashes($item_code_mapping['name']).'</option>';
							$this->_html .= '</select>';
					}

					$this->_html .= '</div>';
		
					$this->_html .= '<label>'.$this->l('Part Number Mapping: ').'</label>
					<div class="margin-form">';
					$this->_html .= '<select name="default_part_number">';
			
							foreach ($part_number_mappings AS $part_number_mapping)
								$this->_html .= '<option value="'.$part_number_mapping['ref_key'].'"'.(($part_number_mapping['ref_key'] == Configuration::get('sugar_sync_default_part_number')) ? ' selected="selected"' : '').'>'.stripslashes($part_number_mapping['name']).'</option>';
							$this->_html .= '</select>';
							
					$this->_html .= '</div>';
					
					$this->_html .= '</fieldset>';
					
					$this->_html .= '<br/>';
					
					$this->_html .='<script type="text/javascript">
						$(document).ready(function(){
						$("#async").click(function(){
							var params = "posturl=' . Configuration::get('sugar_sync_data_sync_url') . '/prestashop/sugarcrm/sync-prestashop-sugar-load-data.php";
							$.ajax({
								type : "POST",
								data: params,
								url: "../modules/sugarsync/sugar-load-parameters-data.php",
								success: function(msg) {
									alert(\''.$this->l("Please Refresh this page to view the new changes.").'\');
									return false;
								}
							});
						});
					});</script>';
		
					$this->_html .= '<fieldset>
					<legend><img src="../img/admin/cog.gif" alt="" class="middle" />'.$this->l('Load Parameters').'</legend>
					<br />';
					
					$this->_html .= '<label>'.$this->l('Load Parameters Data: ').'</label>';
		
					$this->_html .= '<a id="async" name="async" href="#async" title="Load Parameters Data"><img border="0" src="'.$this->_path.'data-sync.png" width="48" height="48" /></a>';
						
					$this->_html .= '</fieldset>';
					
					$this->_html .= '<br/>';					
					
					
					$this->_html .= '<fieldset>
					<legend><img src="../img/admin/cog.gif" alt="" class="middle" />'.$this->l('Invoice Settings').'</legend>';
					
					$this->_html .= '<label>'.$this->l('Sync Invoice Start Date From:').' </label>
					<div class="margin-form">';
					
					if ($cartbooks_invoice_status == 1) {
						$this->_html .= '<b>' . $sl_year . "-" . $sl_month . "-" . $sl_day. '<b/>';
						$this->_html .= '<input name="days" type="hidden" value="'.$sl_day.'"/>';
						$this->_html .= '<input name="months" type="hidden" value="'.$sl_month.'"/>';
						$this->_html .= '<input name="years" type="hidden" value="'.$sl_year.'"/>';						
					} else {
						$this->_html .=  '
							<select name="days">
								<option value="">-</option>';
								foreach ($days as $v)
									$this->_html .=  '<option value="'.$v.'" '.($sl_day == $v ? 'selected="selected"' : '').'>'.$v.'</option>';
							$this->_html .=  '
							</select>
							<select name="months">
								<option value="">-</option>';
								foreach ($months as $k => $v)
									$this->_html .=  '<option value="'.$k.'" '.($sl_month == $k ? 'selected="selected"' : '').'>'.$this->l($v).'</option>';
							$this->_html .=  '</select>
							<select name="years">
								<option value="">-</option>';
								foreach ($years as $v)
									$this->_html .=  '<option value="'.$v.'" '.($sl_year == $v ? 'selected="selected"' : '').'>'.$v.'</option>';
							$this->_html .=  '</select>';
					}					
					$this->_html .=  '</div>';
					
						$this->_html .= '<input name="sugar_sync_invoice_start_date" type="hidden" value="'.Tools::getValue($invoice_start_date, Configuration::get('sugar_sync_invoice_start_date')).'"/>';


					$this->_html .= '<label>'.$this->l('Void Invoice Status: ').'</label>
					<div class="margin-form">';
		
					$this->_html .= '<select name="sugar_sync_def_void_sts">';
		
						foreach ($invoice_statuses AS $invoice_status)
							$this->_html .= '<option value="'.$invoice_status['ref_key'].'"'.(($invoice_status['ref_key'] == Configuration::get('sugar_sync_def_void_sts')) ? ' selected="selected"' : '').'>'.stripslashes($invoice_status['name']).'</option>';
						$this->_html .= '</select>';

					$this->_html .= '</div>';
					$this->_html .= '<label>'.$this->l('Invoice Status: ').'</label>
					<div class="margin-form">';
		
					$this->_html .= '<select name="sugar_sync_def_inv_sts">';
		
						foreach ($invoice_statuses AS $invoice_status)
							$this->_html .= '<option value="'.$invoice_status['ref_key'].'"'.(($invoice_status['ref_key'] == Configuration::get('sugar_sync_def_inv_sts')) ? ' selected="selected"' : '').'>'.stripslashes($invoice_status['name']).'</option>';
						$this->_html .= '</select>';
						
					$this->_html .= '</div>';
					
					
					$this->_html .= '<label>'.$this->l('Invoice Template: ').'</label>
					<div class="margin-form">';
		
					$this->_html .= '<select name="sugar_sync_def_inv_tpl">';
		
						foreach ($invoice_templates AS $invoice_template)
							$this->_html .= '<option value="'.$invoice_template['ref_key'].'"'.(($invoice_template['ref_key'] == Configuration::get('sugar_sync_def_inv_tpl')) ? ' selected="selected"' : '').'>'.stripslashes($invoice_template['name']).'</option>';
						$this->_html .= '</select>';
						
					$this->_html .= '</div>';
					
					$this->_html .= '<label>'.$this->l('Discount Type: ').'</label>
					<div class="margin-form">';
					$this->_html .= '<select name="sugar_sync_disc_type">';
		                  
						foreach ($discount_type_array AS $discount_type)
							$this->_html .= '<option value="'.$discount_type['id'].'"'.(($discount_type['id'] == Configuration::get('sugar_sync_disc_type')) ? ' selected="selected"' : '').'>'.stripslashes($discount_type['text']) . '<br/>';
						$this->_html .= '</select>';
						
					$this->_html .= '</div>';
						
						
					$this->_html .= '<label>'.$this->l('Sync SalesOrder / Invoice<br/> when status is: ').'</label>
					<div class="margin-form">';
					
					$invoiceList = array_map('trim',explode(",",Configuration::get('sugar_sync_invoice_status')));
					
					 foreach ($states as $k => $state) {					 	                  
		                   if (in_array($state['id_order_state'], $invoiceList)) {		                   	
			                  $this->_html .= '<input type="checkbox" name="invoice_status[]" id="invoice_status[]" value="'. $state['id_order_state'] . '" checked="checked">';
			                  $this->_html .= stripslashes($state['name']) . '<br/>';
			                   } else {
			                  $this->_html .= '<input type="checkbox" name="invoice_status[]" id="invoice_status[]" value="' . $state['id_order_state'] . '">';
			                  $this->_html .= stripslashes($state['name']) . '<br/>';
		                   } 	                  
	                  }
						
					$this->_html .= '</div>';


					$this->_html .= '<label>'.$this->l('Void SalesOrder / Invoice<br/> when status is: ').'</label>
					<div class="margin-form">';
					
					$invoiceList = array_map('trim',explode(",",Configuration::get('sugar_sync_invoice_void')));
					
					 foreach ($states as $k => $state) {					 	                  
		                   if (in_array($state['id_order_state'], $invoiceList)) {		                   	
			                  $this->_html .= '<input type="checkbox" name="void_invoice_status[]" id="void_invoice_status[]" value="'. $state['id_order_state'] . '" checked="checked">';
			                  $this->_html .= stripslashes($state['name']) . '<br/>';
			                   } else {
			                  $this->_html .= '<input type="checkbox" name="void_invoice_status[]" id="void_invoice_status[]" value="' . $state['id_order_state'] . '">';
			                  $this->_html .= stripslashes($state['name']) . '<br/>';
		                   } 	                  
	                  }
						
					$this->_html .= '</div>';

					
					$this->_html .= '<label>'.$this->l('Sync Delivered / Completed<br/>Status: ').'</label>
					<div class="margin-form">';
		
					$this->_html .= '<select name="delivered_status">';
		
						foreach ($states AS $state)
							$this->_html .= '<option value="'.$state['id_order_state'].'"'.(($state['id_order_state'] == Configuration::get('sugar_sync_delivered_status')) ? ' selected="selected"' : '').'>'.stripslashes($state['name']).'</option>';
						$this->_html .= '</select>';
						
					$this->_html .= '</div>';
					
					$this->_html .= '<center><input name="btnSubmit" class="button" value="'.($this->l('Save')).'" type="submit" /></center>';
					
					$this->_html .= '</fieldset>';

		
		$this->_html .= '</form>';
	}

    
	private function _postValidation()
	{
		if (empty($_POST['url']) OR strlen($_POST['url']) < 3)
		{
			$this->_postErrors[] = $this->l('URL is required.');
		}

		if (empty($_POST['user_id']))
		{
			$this->_postErrors[] = $this->l('User ID is required.');
		} 

		if (empty($_POST['password']))
			$this->_postErrors[] = $this->l('Password is invalid');
		
		if (empty($_POST['days']) || empty($_POST['months']) || empty($_POST['years']))
		{
			$this->_postErrors[] = $this->l('Invoice Start Date is required.');
		}	
	}

	function getContent()
	{
		$this->_html .= '<h2>'.$this->l('SugarCRM Data Sync Suites').'</h2>';

		if (Tools::isSubmit('btnSubmit'))
		{
			$this->_postValidation();

			if (!sizeof($this->_postErrors))
			{
				Configuration::updateValue('sugar_sync_status', Tools::getValue('status'));
				Configuration::updateValue('sugar_sync_cartversion', Tools::getValue('cartversion'));
				Configuration::updateValue('sugar_sync_debug_mode', Tools::getValue('debug_mode'));
				Configuration::updateValue('sugar_sync_server', Tools::getValue('server'));
				Configuration::updateValue('sugar_sync_memory_limit', Tools::getValue('memory_limit'));
				Configuration::updateValue('sugar_sync_data_sync_url', Tools::getValue('data_sync_url'));								
				Configuration::updateValue('sugar_sync_url', Tools::getValue('url'));
				Configuration::updateValue('sugar_sync_user_id', Tools::getValue('user_id'));
				Configuration::updateValue('sugar_sync_password', Tools::getValue('password'));
				Configuration::updateValue('sugar_sync_password_auth', Tools::getValue('password_auth'));
				Configuration::updateValue('sugar_sync_default_item_code', Tools::getValue('default_item_code'));
				Configuration::updateValue('sugar_sync_default_part_number', Tools::getValue('default_part_number'));				
				Configuration::updateValue('sugar_sync_invoice_start_date', Tools::getValue('years') . "-" . Tools::getValue('months') . "-" . Tools::getValue('days'));
				Configuration::updateValue('sugar_sync_delivered_status', Tools::getValue('delivered_status'));
				Configuration::updateValue('sugar_sync_invoice_void', $this->getTransactionStatus(Tools::getValue('void_invoice_status')));
				Configuration::updateValue('sugar_sync_invoice_status', $this->getTransactionStatus(Tools::getValue('invoice_status')));
				Configuration::updateValue('sugar_sync_def_void_sts', Tools::getValue('sugar_sync_def_void_sts'));
				Configuration::updateValue('sugar_sync_def_inv_sts', Tools::getValue('sugar_sync_def_inv_sts'));
				Configuration::updateValue('sugar_sync_def_inv_tpl', Tools::getValue('sugar_sync_def_inv_tpl'));
				Configuration::updateValue('sugar_sync_disc_type', Tools::getValue('sugar_sync_disc_type'));
				
				$this->_html .= '<div class="conf confirm"><img src="../img/admin/ok.gif" alt="'.$this->l('Confirmation').'" />'.$this->l('Settings updated').'</div>';
			}
			else
			{
				foreach ($this->_postErrors AS $err)
				{
					$this->_html .= '<div class="alert error">'.$err.'</div>';
				}
			}
		}

		$this->_displayForm();

		return $this->_html;
	}
	
	function getTransactionStatus($obj) {
		$transactionStatus = '';
		
		if(is_null($obj)) {
			return $obj;
		} else {
			for ($i=0; $i < count($obj); $i++){
				$transactionStatus .= $obj[$i] . ","; 
			}
		}
		if (strlen($transactionStatus) > 0) {
			$obj = substr($transactionStatus, 0, -1);
		}
		
		return $obj;
	}
	
	function getSyncLookUp($group_name)
	{
		return Db::getInstance()->ExecuteS("SELECT ref_key,name FROM " . _DB_PREFIX_. "sync_lookup WHERE sync_group_name = '" . $group_name . "' AND is_active = 1");
	}
	
	/**
	 * This function get the id from track Records that use for maintanance table
	 * $module		-	Module Name
	 */
	public function getSyncResultStatus($module, $destination) {
		
		$sql = "SELECT count(tid) as total FROM " . _DB_PREFIX_ . "sync_result WHERE sync_module_cd = '" . $module . "' AND destination = '" . $destination . "'";
		
		return (int)Db::getInstance()->getValue($sql);
		 
	}
	
	function currentDateYears()
	{
		for ($i = date('Y'); $i >= 1900; $i--)
			$tab[] = $i;
		return $tab;
	}
}
?>