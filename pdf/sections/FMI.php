<?php
require_once("Section.php");

class FMI extends Section {
	

	public function newPage(){

		$this->pdf->SetPrintHeader(true);
		$this->pdf->AddPage('P','LETTER');
		$this->pdf->SetPrintFooter(true);

		if(isset($this->configs['fmi_title'])){
			$title = $this->configs['fmi_title'];
		} else {
			$title = "For More Information...";
		}

		//Header
		$this->pdf->SetTextColorArray(qlpdf::$BLACK);
		$this->pdf->SetFont('LatoLight', '', 40);
		$this->pdf->SetXY(83, 45);
		$this->pdf->Cell(529, 40, $title, '', 0, 'L');
		
		$this->pdf->SetXY(83, 96);
		$this->pdf->SetFont('LatoLight','', 10);

		$name = $this->pdf->qlstore['Title']['advisor']['first_name']." ".$this->pdf->qlstore['Title']['advisor']['last_name'];

		if(isset($this->configs['fmi_description'])){
			$description = $this->configs['fmi_description'];

			$description = str_replace("\$agent\$", $name, $description);

			$this->pdf->MultiCell(446, 40, $description, '', 'L', false, 2);
			$this->pdf->SetY($this->pdf->GetY()+15);
		}	

		$contact = $name."\n".
				   $this->pdf->qlstore['Title']['advisor']['phone']."\n".
				   $this->pdf->qlstore['Title']['advisor']['email'];

		$this->pdf->SetX(83);	
		$this->pdf->SetFont('LatoLight','', 12);	   
		$this->pdf->MultiCell(446, 40, $contact, '', 'L');


		$this->pdf->SetXY(83, $this->pdf->GetY()+36);
		$this->pdf->SetFont('LatoLight','', 10);	   
		$updated = $this->pdf->qlstore['summary']['updated_at'];
		$updated = date("F j, Y, g:i a", $updated);
		$this->pdf->MultiCell(446, 40, "Last Updated: ".$updated, '', 'L');
	}

}