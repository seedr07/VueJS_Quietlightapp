<?php
require_once("Section.php");

class Title extends Section {
	

	public function newPage(){
		$title = $this->get();

		$this->pdf->SetPrintHeader(false);
		$this->pdf->AddPage('P','LETTER');
		$this->pdf->SetPrintFooter(false);		

		$this->pdf->SetFillColorArray(qlpdf::$BLUE);
		$this->pdf->Rect(0,0,612,792,'F');
		
		$this->pdf->Image(__DIR__."/../images/white_q.png", 220.5, 204, 149.04, 167.28);
		
		//Presents
		$this->pdf->SetTextColorArray(qlpdf::$WHITE);
		$this->pdf->setTextShadow(array('enabled'=>true, 'depth_w'=>0.8, 'depth_h'=>0.8, 'color'=>0, 'opacity'=>0.8, 'blend_mode'=>'Normal'));
		$this->pdf->SetFont('Lato', '', 28);
		$this->pdf->SetXY(0, 376);
		$this->pdf->Cell(612, 28, "Presents", '', 0, 'C');
		$this->pdf->setTextShadow(array('enabled'=>false));


		//Big Centered Domain
		$this->pdf->SetTextColorArray(qlpdf::$WHITE);
		$this->pdf->SetFont('LatoLight', '', 60);
		$this->pdf->SetXY(0, 422);
		$this->pdf->MultiCell(612, 60, $title['name'], '', 'C');


		//Listed By
		$this->pdf->SetXY(0, 622);
		$this->pdf->SetTextColorArray(qlpdf::$WHITE);
		$this->pdf->SetFont('LatoLight', '', 10);
		$listed_by = "Listed By\n".
					 $title['advisor']['first_name']." ".$title['advisor']['last_name']."\n".
					 $title['advisor']['email']."\n".	
					 "Direct Line: ".$title['advisor']['phone'];
		$this->pdf->MultiCell(612, 32, $listed_by, '', 'C');

	}

}