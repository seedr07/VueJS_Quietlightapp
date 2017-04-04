<?php
require_once("Section.php");

class Flex extends Section {

	public function newPage(){
		$flex = $this->get();

		$this->pdf->SetPrintHeader(true);
		switch($flex['orientation']){
			case "P":
				$width = 446;
				$this->pdf->AddPage($flex['orientation'],'LETTER');
				break;
			case "L":
				$width = 626;
				$this->pdf->AddPage($flex['orientation'],'LETTER');
				break;
			case "D":
				$width = 842;
				$this->pdf->AddPage('L','LEGAL');
				break;
		}
		$this->pdf->SetPrintFooter(true);

		$this->pdf->SetTextColorArray(qlpdf::$BLACK);
		$this->pdf->SetFont('LatoLight', '', 40);
		$this->pdf->SetXY(83, 45);
		$this->pdf->Cell(529, 40, $this->section['name'], '', 0, 'L');

		$this->pdf->SetFont('LatoLight','',10);
		$this->pdf->SetTextColorArray(qlpdf::$BLACK);
		$this->pdf->qlHTMLCell("flex", $width, 0, 83, 110, $flex['body'],0,2,false,true,'J');
	}

}