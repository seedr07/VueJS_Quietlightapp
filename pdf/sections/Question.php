<?php
require_once("Section.php");

class Question extends Section {
	

	public function newPage(){
		$questions = $this->section;

		$this->pdf->SetPrintHeader(true);
		$this->pdf->AddPage('P','LETTER');
		$this->pdf->SetPrintFooter(true);		


		//Questions Header
		$this->pdf->SetTextColorArray(qlpdf::$BLACK);
		$this->pdf->SetFont('LatoLight', '', 40);
		$this->pdf->SetXY(83, 45);
		$title = $this->section['name'];
		
		$this->pdf->Cell(529, 40, $title, '', 2, 'L');
		$this->pdf->SetY($this->pdf->GetY()+10);

		$cur_header = '';
		$question_number = 1;
		$cur_page_number = $this->pdf->cur_page;
		foreach($questions as $question){
			$before_y = $this->pdf->GetY();

			$this->section = $question;
			$q_array = $this->get();

			//Add New Header
			if($cur_header != $q_array['header']){
				$this->pdf->SetTextColorArray(qlpdf::$BLACK);
				$this->pdf->SetFont('LatoBlack', '', 14);
				$this->pdf->SetX(83);
				$this->pdf->Cell(529, 40, strtoupper($q_array['header']), '', 2, 'L');
				$cur_header = $q_array['header'];
			}

			$this->pdf->SetTextColorArray(qlpdf::$BLACK);
			$this->pdf->SetFont('LatoB', '', 10);
			$this->pdf->SetX(83);
			$this->pdf->Cell(529, 14, $question_number.". ".$q_array['question'], '', 2, 'L');

			$this->pdf->qlHTMLCell("question", 446, 0, 83, $this->pdf->GetY(), $q_array['answer'],0,2,false, true,'J');
			

			$question_number++;
		}



	}

}