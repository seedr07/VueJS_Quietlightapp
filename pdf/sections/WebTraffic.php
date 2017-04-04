<?php
require_once("Section.php");

class WebTraffic extends Section {
	

	public function newPage(){
		$webtraffic = $this->get();

		$this->pdf->SetPrintHeader(true);
		$this->pdf->AddPage('P','LETTER');
		$this->pdf->SetPrintFooter(true);		


		//E.S. Header
		$this->pdf->SetTextColorArray(qlpdf::$BLACK);
		$this->pdf->SetFont('LatoLight', '', 40);
		$this->pdf->SetXY(83, 45);
		$title = $this->section['name'];
		$this->pdf->Cell(529, 40, $title, '', 0, 'L');

		if(isset($this->configs['webtraffic_disclosure'])){
			$disclosure = $this->configs['webtraffic_disclosure'];
			$disclosure = str_replace("\$email\$", $this->pdf->qlstore['Title']['advisor']['email'], $disclosure);
			$this->pdf->SetFont('LatoLight','', 10);
			$this->pdf->SetXY(83, 110);
			$this->pdf->MultiCell(446, 40, $disclosure, '', 'L');
		}	

		$this->pdf->SetFont('LatoLight','',10);
		$this->pdf->SetTextColorArray(qlpdf::$BLACK);
		$this->pdf->qlHTMLCell("webtraffic", 446, 0, 83, $this->pdf->GetY(), $webtraffic['body'],0,2,false, true,'J');


		$this->pdf->qlBottom();
	}

}