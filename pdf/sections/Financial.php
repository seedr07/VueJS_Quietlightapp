<?php
require_once("Section.php");

class Financial extends Section {
	

	public function newPage(){
		$financial = $this->get();

		$this->pdf->SetPrintHeader(true);
		$this->pdf->AddPage('P','LETTER');
		$this->pdf->SetPrintFooter(true);		


		//E.S. Header
		$this->pdf->SetTextColorArray(qlpdf::$BLACK);
		$this->pdf->SetFont('LatoLight', '', 40);
		$this->pdf->SetXY(83, 45);
		$this->pdf->Cell(529, 40, $this->section['name'], '', 0, 'L');

		if(isset($this->configs['financial_disclosure'])){
			$disclosure = $this->configs['financial_disclosure'];
			$this->pdf->SetFont('LatoLight','', 10);
			$this->pdf->SetXY(83, 110);
			$this->pdf->MultiCell(446, 40, $disclosure, '', 'L');
		}	

		if($financial['pl_link']){
			$this->pdf->Image(__DIR__."/../images/graybox.png",83, 145, 453, 74.77,'','','', false, 300);

			$pl_desc = (isset($this->configs['financial_profit_loss_description'])) ? $this->configs['financial_profit_loss_description'] : "";
			$pl_link = (isset($this->configs['financial_profit_loss_link'])) ? $this->configs['financial_profit_loss_link'] : "";

			$this->pdf->SetTextColorArray(qlpdf::$BLACK);
			$this->pdf->SetFont('latobi','',9);
			$this->pdf->SetXY(0, 160);
			$this->pdf->Cell(619, 9, $pl_desc, '', 0, 'C');

			$this->pdf->SetTextColorArray(qlpdf::$LINK_BLUE);
			$this->pdf->SetFont('latob','u',14);
			$this->pdf->SetXY(0, 184);
			$this->pdf->Cell(619, 14, $pl_link, '', 2, 'C', false, $financial['pl_link']);
			$this->pdf->SetY($this->pdf->GetY()+25);
			$this->pdf->SetFont('latob','',14);
		}
		
		$this->pdf->SetFont('LatoLight','',10);
        $this->pdf->SetTextColorArray(qlpdf::$BLACK);
		$this->pdf->qlHTMLCell("financial", 446, 0, 83, $this->pdf->GetY(), $financial['body'],0,2,false, true,'J');


		$this->pdf->qlBottom();
	}

}