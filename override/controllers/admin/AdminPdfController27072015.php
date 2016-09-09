<?php
class AdminPdfController extends AdminPdfControllerCore
{
	public function generatePDF($object, $template)
	{
		$pdf = new PDF($object, $template, Context::getContext()->smarty);
		if ($template=="SupplyOrderForm")
		{
			$pdf->renderSupplyOrderForm();
		}
		else{$pdf->render();}
	}
}