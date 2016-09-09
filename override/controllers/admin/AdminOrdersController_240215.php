<?php
/*
* 2007-2013 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
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
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AdminOrdersController extends AdminOrdersControllerCore
{
	public function printPDFIcons($id_order, $tr)
	{
		$order = new Order($id_order);
		$order_state = $order->getCurrentOrderState();
		if (!Validate::isLoadedObject($order_state) || !Validate::isLoadedObject($order))
		{
			return '';
		}

		$sqlPayu = "SELECT * FROM ps_pagos_payu WHERE id_order = " . $order->id;
		$results = Db::getInstance()->ExecuteS($sqlPayu);
		if (empty($results)){
			$validacionPagoPayu = "empty";
		} else {
			$validacionPagoPayu = "full";
		}

		$this->context->smarty->assign(array(
			'order' => $order,
			'order_state' => $order_state,
			'tr' => $tr,
            'complete_order'=>$this->ordenCompleta($id_order),
            'order_payu' => $validacionPagoPayu
		));

		return $this->createTemplate('_print_pdf_icon.tpl')->fetch();
	}

	
	
	/**
     * [removeProductsOrder description]
     * @param  [type] $id_order [description]
     * @return [type]           [description]
     */
    public function removeProductsOrder($id_order) {

    	$loggin = new Registrolog();
        $loggin->lwrite("AdminOrdersController", "remover_productos.txt", "Ingreso Eliminar");

        if ($this->addProductStock($id_order)) {
        	$loggin->lwrite("AdminOrdersController", "remover_productos.txt", "Si se actualizo stock ");

            $query = "SELECT  picking.id_order_picking, id_icr 
			from 
			ps_icr icr 
			INNER JOIN ps_order_picking picking ON(icr.id_icr=picking.id_order_icr) 
			WHERE  icr.cod_icr IN(SELECT icr.cod_icr
			from ps_orders orders 
			INNER JOIN ps_order_detail orders_d ON( orders.id_order= orders_d.id_order)
			INNER JOIN ps_supply_order_detail s_order_d ON(orders_d.product_id=s_order_d.id_product)
			INNER JOIN ps_supply_order_icr s_order_i ON (s_order_d.id_supply_order_detail=s_order_i.id_supply_order_detail)
			INNER JOIN ps_icr icr ON (s_order_i.id_icr=icr.id_icr)
			INNER JOIN ps_order_picking o_picking ON (orders_d.id_order_detail= o_picking.id_order_detail AND s_order_i.id_supply_order_icr =o_picking.id_order_supply_icr)
			INNER JOIN ps_product product ON (s_order_d.id_product=product.id_product)
			WHERE icr.id_estado_icr=3 and orders.id_order=".$id_order." 
			GROUP BY icr.cod_icr );";

            $array_icr = null;
            $array_order_picking = null;

            if ($results = Db::getInstance()->ExecuteS($query)) {

                foreach ($results as $row) {
                    $array_order_picking[] = $row['id_order_picking'];
                    $array_icr[] = $row['id_icr'];
                }
                if ($array_icr != NULL && $array_order_picking != NULL) {

                    $query_2 = "DELETE from ps_order_picking
                        WHERE id_order_picking IN ('" . implode("','", $array_order_picking) . "')";

                    $query_3 = "update ps_icr SET id_estado_icr=2
                        WHERE id_icr IN ('" . implode("','", $array_icr) . "')";
                    
                    if ($results = Db::getInstance()->ExecuteS($query_2) && $results3 = Db::getInstance()->ExecuteS($query_3)) {
                    	$loggin->lwrite("AdminOrdersController", "remover_productos.txt", "ICR actualizados ");
                        return true;
                    }
                }
            }        
        } else {
        	$loggin->lwrite("AdminOrdersController", "remover_productos.txt", "No se actualizo stock ");
        }
        return false;
    }

    public function initToolbar()
	{
		if ($this->display == 'view')
		{
			$order = new Order((int)Tools::getValue('id_order'));
			if ($order->hasBeenShipped())
				$type = $this->l('Return products');
			elseif ($order->hasBeenPaid())
				$type = $this->l('Standard refund');
			else
				$type = $this->l('Cancel products');

			if (Configuration::get('PS_ORDER_EDIT') == '1') {
							
				if (!$order->hasBeenShipped() && !$this->lite_display) {
					$this->toolbar_btn['new'] = array(
						'short' => 'Create',
						'href' => '#',
						'desc' => $this->l('Add a product'),
						'class' => 'add_product'
					);
				}

				if (Configuration::get('PS_ORDER_RETURN') && !$this->lite_display) {
					$this->toolbar_btn['standard_refund'] = array(
						'short' => 'Create',
						'href' => '',
						'desc' => $type,
						'class' => 'process-icon-standardRefund'
					);
				}
				
				if ($order->hasInvoice() && !$this->lite_display) {
					$this->toolbar_btn['partial_refund'] = array(
						'short' => 'Create',
						'href' => '',
						'desc' => $this->l('Partial refund'),
						'class' => 'process-icon-partialRefund'
					);
				}			
			}
		}

		$res = AdminController::initToolbar();
		if (Context::getContext()->shop->getContext() != Shop::CONTEXT_SHOP && isset($this->toolbar_btn['new']) && Shop::isFeatureActive())
			unset($this->toolbar_btn['new']);
		return $res;
	}
}

