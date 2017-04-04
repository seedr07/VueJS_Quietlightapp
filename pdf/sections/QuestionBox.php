<?php
require_once("Section.php");

class QuestionBox extends Section {

	public function newPage(){
		$questionbox = $this->get();

		$this->pdf->SetPrintHeader(true);
		$this->pdf->AddPage('P','LETTER');
		$this->pdf->SetPrintFooter(true);

		//Questions Header
		$this->pdf->SetTextColorArray(qlpdf::$BLACK);
		$this->pdf->SetFont('LatoLight', '', 40);
		$this->pdf->SetXY(83, 45);
		$title = $this->section['name'];
		$this->pdf->Cell(529, 40, $title, '', 0, 'L');

		$this->pdf->SetFont('LatoLight','',10);
		$this->pdf->SetTextColorArray(qlpdf::$BLACK);
		$this->pdf->qlHTMLCell("questionbox", 446, 0, 83, $this->pdf->GetY()+32, $questionbox['body'],0,2,false,true,'J');
	}

}