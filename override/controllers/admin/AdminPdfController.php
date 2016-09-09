<?php
class AdminPdfController extends AdminPdfControllerCore
{
	public function generatePDF($object, $template)
	{
		try {
			$pdf = new PDF($object, $template, Context::getContext()->smarty);
			
		} catch (Exception $e) {

		}

		
		if ($template=="SupplyOrderForm") {
			$pdf->renderSupplyOrderForm();
		}elseif ($template=='SupplyOrderReceipt') {
			$pdf->renderSupplyOrderReceipt();
		} elseif ($template == "Traslado") { 
			$pdf->renderTrasladoForm();
		} else {			
			$pdf->render();
		}
	}

	public function processGenerateSupplyOrderReceiptPDF()
	{
		if (!Tools::isSubmit('id_supply_order'))
			die (Tools::displayError('The supply order ID is missing.'));

		$id_supply_order = (int)Tools::getValue('id_supply_order');
		$supply_order = new SupplyOrder($id_supply_order);

		if (!Validate::isLoadedObject($supply_order))
			die(Tools::displayError('The supply order cannot be found within your database.'));
		$this->generatePDF($supply_order, PDF::TEMPLATE_SUPPLY_ORDER_RECEIPT);
	}

	public function processgenerateTrasladoPDF()
	{
		if (Tools::getValue('date')) {
			//echo "<br><pre>Date: ";//.Tools::getValue('date')." - ".PDF::TEMPLATE_TRASLADO;
			$supply_order = new SupplyOrder(1);
			
			//echo "<br> sql:".
			$head_tras = "SELECT soih.date, CONCAT( emp.firstname, ' ', emp.lastname) AS employee,  
				ww1.`name` AS bodegaorigen, ww2.`name` AS bodegadestino,
				DATE_FORMAT(soih.date,'%y%m%d%H%i') AS folio
				FROM ps_supply_order_icr_history soih 
				INNER JOIN ps_warehouse ww1 ON ( soih.id_origin_warehouse = ww1.id_warehouse)
				INNER JOIN ps_warehouse ww2 ON ( soih.id_destination_warehouse = ww2.id_warehouse)
				LEFT JOIN ps_employee emp ON ( soih.id_employee = emp.id_employee )
				WHERE soih.date = '".Tools::getValue('date')."'
				GROUP BY soih.date; ";

			$result = Db::getInstance()->executeS($head_tras);
			//print_r( $result[0] );
			
			$this->generatePDF( $result, PDF::TEMPLATE_TRASLADO );

		} else {
			die (Tools::displayError('The order ID -- or the invoice order ID -- is missing.'));
		}
	}
}