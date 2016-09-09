<?php

/**
 * @since 1.5
 */
class PDFGenerator extends PDFGeneratorCore
{
	
	public function writePage()
	{
		$this->SetHeaderMargin(5);
		$this->SetFooterMargin(7);
		$this->setMargins(10, 43, 10);
		$this->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);

		if ( in_array("generateDeliverySlipPDF", $_REQUEST) ) {

			$this->SetHeaderMargin(5);
			$this->SetFooterMargin(5);
			$this->setMargins(10, 40, 10);

		}


		$this->AddPage();

		$this->writeHTML($this->content, true, false, true, false, '');
	}

	public function Footer()
    {
        //$this->writeHTML($this->footer);
        $add_footer = '	- Página '.$this->getAliasNumPage().'/'.$this->getAliasNbPages().'
		</td>
	</tr>
</table>';
        $this->writeHTML($this->footer.$add_footer);
    }

}
