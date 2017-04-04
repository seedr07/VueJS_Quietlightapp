<?php
require_once("Section.php");

class Disclosure extends Section {
	

	public function newPage(){
		$disclosure = $this->get();

		$this->pdf->SetPrintHeader(true);
		$this->pdf->AddPage('P','LETTER');
		$this->pdf->SetPrintFooter(true);		

		//Header
		$this->pdf->SetTextColorArray(qlpdf::$BLACK);
		$this->pdf->SetFont('LatoLight', '', 40);
		$this->pdf->SetXY(83, 45);
		$this->pdf->Cell(529, 40, $this->section['name'], '', 0, 'L');

		//Asking Price Header
		$this->pdf->SetFont('LatoLight','',10);
		$this->pdf->SetTextColorArray(qlpdf::$BLACK);
		$this->pdf->qlHTMLCell("disclosure", 446, 0, 83, 110, $disclosure['body']);

		$toolong = $this->pdf->tooLong("Disclosures");

		$this->pdf->qlBottom();
	}

}