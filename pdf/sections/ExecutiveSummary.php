<?php
require_once("Section.php");

class ExecutiveSummary extends Section {
	

	public function newPage(){
		$executivesummary = $this->get();

		$this->pdf->SetPrintHeader(true);
		$this->pdf->AddPage('P','LETTER');
		$this->pdf->SetPrintFooter(true);		


		//E.S. Header
		$this->pdf->SetTextColorArray(qlpdf::$BLACK);
		$this->pdf->SetFont('LatoLight', '', 40);
		$this->pdf->SetXY(83, 45);
		$this->pdf->Cell(529, 40, $this->section['name'], '', 0, 'L');

		//E.S. Table
		$this->pdf->SetFont('LatoHeavy','', 11);
		$this->pdf->SetFillColorArray(qlpdf::$DARK_GRAY);
		$this->pdf->SetDrawColorArray(qlpdf::$DARK_GRAY);
		$this->pdf->SetLineWidth(0.5);
		$this->pdf->SetTextColorArray(qlpdf::$WHITE);
		$this->pdf->SetXY(313.25, 117.5);
		$this->pdf->Cell(117.5, 20.25, " Financial Quickview",0,1,'L',true);
		$this->pdf->Line(313.25, 137.75, 510, 137.75);

		$this->pdf->SetFont('LatoLight','', 10);
		$this->pdf->SetTextColorArray(qlpdf::$BLACK);
		$toggle_bg = true;

		$price = isset($this->pdf->qlstore['Title']) ? $this->pdf->qlstore['Title']['asking_price'] : false;
		
		//Total Revenue
		if($executivesummary['total_revenue']){
			$this->pdf->SetFillColorArray(($toggle_bg) ? qlpdf::$WHITE : qlpdf::$LIGHT_GRAY);
			$this->pdf->SetXY(313.25, $this->pdf->GetY());
			$this->pdf->Cell(108.5, 20.25, "  Total Revenue",0,0,'L',true);
			$this->pdf->Cell(102.25, 20.25, "  ".$this->pdf->formatMoney($executivesummary['total_revenue']),0,1,'C',true);
			$toggle_bg = !$toggle_bg;
		}
		
		//Gross Profit
		if($executivesummary['gross_profit']){		
			$this->pdf->SetFillColorArray(($toggle_bg) ? qlpdf::$WHITE : qlpdf::$LIGHT_GRAY);
			$this->pdf->SetXY(313.25, $this->pdf->GetY());
			$this->pdf->Cell(108.5, 20.25, "  Gross Profit",0,0,'L',true);
			$this->pdf->Cell(102.25, 20.25, "  ".$this->pdf->formatMoney($executivesummary['gross_profit']),0,1,'C',true);
			$toggle_bg = !$toggle_bg;
		}

		//Discretionary Earnings
		if($executivesummary['discretionary_earnings']){		
			$this->pdf->SetFillColorArray(($toggle_bg) ? qlpdf::$WHITE : qlpdf::$LIGHT_GRAY);
			$this->pdf->SetXY(313.25, $this->pdf->GetY());
			$this->pdf->Cell(108.5, 20.25, "  Discretionary Earnings",0,0,'L',true);
			$this->pdf->Cell(102.25, 20.25, "  ".$this->pdf->formatMoney($executivesummary['discretionary_earnings']),0,1,'C',true);
			$toggle_bg = !$toggle_bg;
		}


		//Inventory
		if($executivesummary['inventory']){		
			$this->pdf->SetFillColorArray(($toggle_bg) ? qlpdf::$WHITE : qlpdf::$LIGHT_GRAY);
			$this->pdf->SetXY(313.25, $this->pdf->GetY());
			$this->pdf->Cell(108.5, 20.25, "  Inventory".($executivesummary['inventory_footnote'] ? "†" : ""),0,0,'L',true);
			$this->pdf->Cell(102.25, 20.25, "  ".$this->pdf->formatMoney($executivesummary['inventory']),0,1,'C',true);
			$toggle_bg = !$toggle_bg;
		}

		//Inventory Included
		if($executivesummary['inventory_included']){		
			$this->pdf->SetFillColorArray(($toggle_bg) ? qlpdf::$WHITE : qlpdf::$LIGHT_GRAY);
			$this->pdf->SetXY(313.25, $this->pdf->GetY());
			$this->pdf->Cell(108.5, 20.25, "  Inventory Include",0,0,'L',true);
			$this->pdf->Cell(102.25, 20.25, "  ".($executivesummary['inventory_included']) ? "Yes" : "No",0,1,'C',true);
			$toggle_bg = !$toggle_bg;
		}

		//Asking Multiple
		if($executivesummary['show_multiple']){
			if(!$price){
				$this->pdf->logs[] = "Executive Summary: Multiple not shown. No Asking Price provided.";
			} else if(!$executivesummary['discretionary_earnings']){
				$this->pdf->logs[] = "Executive Summary: Multiple not shown. No Discretionary Earnings provided.";
			} else {
				$this->pdf->SetFillColorArray(($toggle_bg) ? qlpdf::$WHITE : qlpdf::$LIGHT_GRAY);
				$this->pdf->SetXY(313.25, $this->pdf->GetY());
				$this->pdf->Cell(108.5, 20.25, "  Asking Multiple",0,0,'L',true);
				$this->pdf->Cell(102.25, 20.25, "  ".round($price/$executivesummary['discretionary_earnings'],2),0,1,'C',true);
				$toggle_bg = !$toggle_bg;
			}
		}

		if($executivesummary['inventory_footnote']){
			$this->pdf->SetFillColorArray(qlpdf::$WHITE);
			$this->pdf->SetXY(313.25, $this->pdf->GetY()+3);
			$this->pdf->SetFont('LatoLight','I',9);
			$this->pdf->MultiCell(210.75, 20.25, "† ".$executivesummary['inventory_footnote'],0,'L');
		}

		$regions = array(
			array('page' => '', 'xt' => 290, 'yt' =>  117.5, 'xb' => 290, 'yb' =>  $this->pdf->GetY()+10.25, 'side' => 'R')
		);

		// set page regions, check also getPageRegions(), addPageRegion() and removePageRegion()
		$this->pdf->setPageRegions($regions);

		//Summary HTML Cell
		$this->pdf->SetFont('LatoLight','',10);
		$this->pdf->SetTextColorArray(qlpdf::$BLACK);
		$this->pdf->qlHTMLCell("executivesummary", 446, 0, 83, 110, $executivesummary['summary'],0,2,false, true,'J');

		//Key Benefits Header
		$this->pdf->SetTextColorArray(qlpdf::$BLACK);
		$this->pdf->SetFont('LatoLight', '', 30);
		$this->pdf->SetXY(83, $this->pdf->GetY());
		$this->pdf->Cell(529, 30, "Key Benefits", '', 1, 'L');

		//Benefits HTML Cell
		$this->pdf->SetFont('LatoLight','',10);
		$this->pdf->SetTextColorArray(qlpdf::$BLACK);
		$this->pdf->qlHTMLCell("executivesummary", 471, 0, 83, $this->pdf->GetY(), $executivesummary['benefits'] ,0,2,false, true,'J');

		//Check Too Long before the Asking Price... because it's positioned differently.
		$toolong = $this->pdf->tooLong("Executive Summary");

		//Asking Price 
		if(!$toolong){
			$this->pdf->SetFont('Lato', '', 30);
			$html = '<span style="font-family: latob;">Asking Price: </span><span style="font-family: latolight; color: #01A2E5;">'.$this->pdf->formatMoney($price).'</span>';
			$centered = $this->pdf->GetY() + ((748-$this->pdf->GetY())/2) - 40;
			$this->pdf->qlHTMLCell("executivesummary", 612, 0, 0, $centered, $html ,0,2,false, true,'C');

			$this->pdf->qlBottom();
		}


	}

}