<?php

require_once("Section.php");

class Addendum extends Section {
	

	public function newPage(){
		$addendum = $this->get();

		if(!is_file(__DIR__."/../../".$addendum['file'])) return;

		//Turn off Header and Footer
		$this->pdf->SetPrintHeader(false);

		$pageCount = $this->pdf->setSourceFile(__DIR__."/../../".$addendum['file']);
		for($i = 1; $i <= $pageCount; $i++){
			$tplIdx = $this->pdf->importPage($i, '/MediaBox');
			$this->pdf->addPage('P','LETTER');
	
			$this->pdf->SetPrintFooter(false);

			$this->pdf->useTemplate($tplIdx);
		}


	}

}