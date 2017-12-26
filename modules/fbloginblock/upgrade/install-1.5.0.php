<?php
/**
 * 2011 - 2017 StorePrestaModules SPM LLC.
 *
 * MODULE fbloginblock
 *
 * @author    SPM <kykyryzopresto@gmail.com>
 * @copyright Copyright (c) permanent, SPM
 * @license   Addons PrestaShop license limitation
 * @version   1.7.7
 * @link      http://addons.prestashop.com/en/2_community-developer?contributor=61669
 *
 * NOTICE OF LICENSE
 *
 * Don't use this module on several shops. The license provided by PrestaShop Addons
 * for all its modules is valid only once for a single shop.
 */

function upgrade_module_1_5_0($module)
{
	
	if(version_compare(_PS_VERSION_, '1.5', '>')){
		
		$module->createOklassTable();
		
		$module->createPinterestTable();
		
		$module->createTumblrTable();
		
		$module->registerHook('socialConnectSpm');
		
		Configuration::updateValue('redirpage', 2);
		
		### update for compatibility for ps_customer table ####
		
		$list_fields = Db::getInstance()->executeS('SHOW FIELDS FROM `'._DB_PREFIX_.'customers_statistics_spm`');
		if (is_array($list_fields))
		{
			foreach ($list_fields as $k => $field)
				$list_fields[$k] = $field['Field'];
			if (!in_array('email_stat', $list_fields))
				if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute('
						ALTER TABLE `'._DB_PREFIX_.'customers_statistics_spm` CHANGE `email` `email_stat` TEXT NULL DEFAULT NULL;'))
				return false;
		}
		### update for compatibility for ps_customer table ####
		
		
		#### update for override AdminCustomersController #####
		try {
			$module->installOverrides();
		} catch (Exception $e) {
			$module->uninstallOverrides();
			return false;
		}
		
		$path_to_delete_class_index = dirname(__FILE__).DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."cache".DIRECTORY_SEPARATOR."class_index.php";
		if(file_exists($path_to_delete_class_index)){
			unlink($path_to_delete_class_index);
		}
		#### update for override AdminCustomersController #####
		
		
		### add hook displayBackOfficeHeader ###
		if(!$module->registerHook('displayBackOfficeHeader'))
			return false;
		### add hook displayBackOfficeHeader ###
		
	
		
		#### add tab Statistics in admin panel ####
		$id_tab = Tab::getIdFromClassName('AdminStat');
		if (empty ($id_tab))
			$module->createAdminTabs();
		
		Tools::clearCache();
		#### add tab Statistics in admin panel ####
		
	}
	
	return true;
}
?>