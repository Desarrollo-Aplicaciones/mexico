/**
 * StorePrestaModules SPM LLC.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 *
 /*
 * 
 * @author    StorePrestaModules SPM
 * @category social_networks
 * @package fbloginblock
 * @copyright Copyright StorePrestaModules SPM
 * @license   StorePrestaModules SPM
 */

function tabs_custom(id){
	
	for(i=0;i<100;i++){
		$('#tab-menu-'+i).removeClass('selected');
	}
	$('#tab-menu-'+id).addClass('selected');
	for(i=0;i<100;i++){
		$('#tabs-'+i).hide();
	}
	$('#tabs-'+id).show();
}

function init_tabs(id){
	$('document').ready( function() {
		for(i=0;i<100;i++){
			$('#tabs-'+i).hide();
		}
		$('#tabs-'+id).show();
		tabs_custom(id);
	});
}

init_tabs(1);