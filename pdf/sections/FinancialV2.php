<?php
require_once("Section.php");

class FinancialV2 extends Section {
	

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
			$this->pdf->SetXY(83, 96);
			$this->pdf->MultiCell(446, 40, $disclosure, '', 'L');
		}	

		if(0 && $we_generated_the_pl_somewhere){
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
		
		//Create image from graph1_data
		if(isset($financial['graph1_data']) && $financial['graph1_data']){
			$imageDataEncoded = str_replace("data:image/png;base64,","",$financial['graph1_data']);
			$imageData = base64_decode($imageDataEncoded);
			$source = imagecreatefromstring($imageData);
			$graph1_name = tempnam(sys_get_temp_dir(), 'qlfv2');
			$imageSave = imagejpeg($source,$graph1_name,100);
			imagedestroy($source);
		}
		//Create image from graph2_data
		if(isset($financial['graph2_data']) && $financial['graph2_data']){
			$imageDataEncoded = str_replace("data:image/png;base64,","",$financial['graph2_data']);
			$imageData = base64_decode($imageDataEncoded);
			$source = imagecreatefromstring($imageData);
			$graph2_name = tempnam(sys_get_temp_dir(), 'qlfv2');
			$imageSave = imagejpeg($source,$graph2_name,100);
			imagedestroy($source);
		}

		$y = $this->pdf->GetY();
		$this->pdf->Image($graph1_name, 117, $y);
		$this->pdf->Image($graph2_name, 117, $y+150);

		$this->pdf->qlBottom();
	}

}