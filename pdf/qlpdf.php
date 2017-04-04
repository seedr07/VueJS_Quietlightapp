<?php
	require(__DIR__."/tcpdf/tcpdf.php");
	require(__DIR__.'/fpdi/fpdi.php');
	require(__DIR__."/vendor/autoload.php");

	class qlpdf extends FPDI {
	   
	   	public $qlstore = [];
	   	public $cur_page = 1;
	   	public $logs = [];

	   	public function AcceptPageBreak(){
	   		if(!isset($this->qlstore['cur_class'])) return false;
	   		else {
	   			switch($this->qlstore['cur_class']){
	   				case "Question":
	   				case "Questionbox":
	   				case "Flex":
	   					return true;
	   				default:
	   					return false;
	   			}
	   		}
	   	}

	   	public function tooLong($pageName){
	   		if($this->GetY() == 792){
	   			$this->logs[] = "Content on <b>".$pageName. "</b> is too long.";
	   			return true;
	   		}
	   	}

	   	public function formatMoney($money){
	   		setlocale(LC_MONETARY, 'en_US.UTF-8');
			if(!is_numeric($money)) return $money;
			return money_format('%.2n', $money);
	   	}

	    public function qlHTMLCell($class, $w, $h, $x, $y, $html = '', $border = 0, $ln = 0, $fill = false, $reseth = true, $align = '', $autopadding = true){
	    	$whtml = "<style>".file_get_contents(__DIR__."/styles/style.css")."</style>\n";
	    	if(file_exists(__DIR__."/styles/".$class.".css")) $whtml .= "<style>".file_get_contents(__DIR__."/styles/".$class.".css")."</style>\n";
	    	
	    	//Do a absolute path for images
	    	$html = str_replace("/fm/","/",$html);
	    	$html = str_replace("/userfiles", __DIR__."/../userfiles", $html);

	    	$html = str_replace("[BREAK]",'<div class="break"></div>', $html);

	    	//Add Table Fancyness
	    	$html = ($html) ? $this->formatHTML($html, $class) : $html;


	    	$whtml .= $html;
	    	parent::writeHTMLCell($w, $h, $x, $y, $whtml, $border, $ln, $fill, $reseth, $align, $autopadding);
	    }

	    public function qlTop() { } 

	    public function Header(){
	    	$dim = $this->getPageDimensions();

	    	$width = $dim['MediaBox']['urx'];

			$offset = ($width == 612) ? 0 : (($width == 792) ? 90 : 198);

	    	$this->SetFillColorArray(self::$BLUE);
	    	$this->Rect(83 + $offset,0,446,24.5, 'F');

	    	$this->SetFillColorArray([199,229,248]);
	    	$this->Rect(439 + $offset,12.25,90,12.25, 'F');

	    	$this->SetFillColorArray([144,208,243]);
	    	$this->Rect(349 + $offset,12.25,90,12.25, 'F');

	    	$this->SetFillColorArray([78,189,237]);
	    	$this->Rect(259 + $offset,12.25,90,12.25, 'F');

	    	$this->SetFillColorArray([0,174,233]);
	    	$this->Rect(169 + $offset,12.25,90,12.25, 'F');

	    	$this->SetFont('LatoLight','',8);
	    	$this->SetTextColorArray(self::$GRAY);
	    	$name = isset($this->qlstore['Title']) ? $this->qlstore['Title']['advisor']['first_name']." ".$this->qlstore['Title']['advisor']['last_name'] : "";
	    	$email = isset($this->qlstore['Title']) ? $this->qlstore['Title']['advisor']['email'] : "";
	    	$phone = isset($this->qlstore['Title']) ? $this->qlstore['Title']['advisor']['phone'] : "";
	    	$this->SetXY(36, 300);

	    	$this->StartTransform();
			$this->Rotate(-270);
	    	$this->Cell(0, 0, $name." | ".$email." | ".$phone);
	    	$this->StopTransform();

	    }

	    public function qlBottom(){ }

	    public function Footer() {
	    	$dim = $this->getPageDimensions();

	    	$width = $dim['MediaBox']['urx'];

			$offsetx = ($width == 612) ? 0 : (($width == 792) ? 90 : 198);
			$offsety = ($dim['or'] == 'P') ? 0 : 180;

	   	
	    	$this->SetFont('LatoLight','',8);
	    	$this->SetTextColorArray(self::$GRAY);

	    	//Name
	    	if(isset($this->qlstore['Title']['asking_price'])){
		    	$name = $this->qlstore['Title']['name'];

		    	$this->SetXY(83+$offsetx, 742-$offsety);
		    	$this->Cell(400, 0, $name);
		    }


	    	//Asking Price
	    	if(isset($this->qlstore['Title']['asking_price'])){
		    	$asking = $this->formatMoney($this->qlstore['Title']['asking_price']);

		    	$this->SetXY(83+$offsetx, 755-$offsety);
		    	$this->Cell(400, 0, "Asking Price: ".$asking);
		    }

		    //Logo
		    $this->Image(__DIR__."/images/large_q.png",291+$offsetx, 733-$offsety, 30, 33.7);


		    //Page Number
		   	$this->SetFont('LatoLight','',8);
		    $this->SetXY(473+$offsetx, 748-$offsety);
		    $this->SetFillColorArray([211, 213, 214]);
		    $this->Circle(523+$offsetx, 754-$offsety, 7, 0, 360, 'F');
		    $this->MultiCell(100, 0, $this->cur_page,0,'C');

		    $this->cur_page++;
	    	
	    }


	    public function formatHTML($html, $class = null) {

	    	$dim = $this->getPageDimensions();

			$offsetx = ($dim['or'] == 'P') ? 0 : (($dim['w'] != 1008) ? 266 : 600);

	    	$dom = htmlqp($html);

	    	$tables = $dom->find("table");
	    	foreach($tables as $table){
	    		//Set Table Attributes
	    		$table->attr('cellpadding',10);
	    		$table->attr('border', 0);
	    		$table->css('padding-bottom',10);

	    		//Calc col width
	    		$num_cols = $table->find("tbody > tr:first-child td")->length;
	    		if($num_cols == 2){
	    			$first_width = (679-(221-$offsetx));
	    			$rest_width = 221;
	    		} else {
	    			$first_width = 221;
	    			$rest_width = ($num_cols > 1) ? (679-(221-$offsetx))/($num_cols-1) : (221-$offsetx);
	    		}

	    		//Center columns
	    		$table->find("td")
	    			  ->css("text-align", "center")
	    			  ->css("background-color", "#FFFFFF")
	    			  ->css("border-bottom", "1px solid black")
	    			  ->attr("width", $rest_width)
	    			  ->css("line-height", "14pt");

	    		//First Header Cell Dark
	    		$table->find("th:first-child")
	    			  ->addClass("first-col");

	    		$table->find("th")
	    			  ->css("text-align", "center")
	    			  ->css("background-color", "#FFFFFF")
	    			  ->css("border-bottom", "1px solid black")
	    			  ->css("font-family", "latobi")
	    			  ->attr("width", $rest_width);
	    		
	    		//Add width to first column 
	    		$table->find("td:nth-child(1)")
	    			  ->attr('width',$first_width)
	    			  ->css("text-align", "left");
	    		
	    		//First Header Cell Dark
	    		$table->find("th:first-child")
	    		      ->css("background-color", "rgb(56, 56, 57)")
	    			  ->css("color", "#FFFFFF")
	    			  ->css("text-align", "left")
	    			  ->attr("width",$first_width);

	    		//Last Row Gray
	    		$table->find("tr:last-child > td")
	    			  ->css("background-color", "rgb(248, 248, 248)")
	    			  ->css("border-bottom-color", "rgb(248, 248, 248)")
	    			  ->css("border-left-color", "rgb(248, 248, 248)")
	    			  ->css("border-right-color", "rgb(248, 248, 248)");
	    		$table->find("tr:last-child > td:first-child")
	    		      ->css("text-align", "left");

	    		//Left align first column
	    		$table->find("tbody")
	    		      ->append('<tr><td style="line-height: 8pt"></td></tr>');
	    	}

	    	$uls = $dom->find("ul");
	    	foreach($uls as $ul){
	    		$ul->css("list-style-type","img|png|6|6|images/bullet.png");
	    	}

	    	if($class == "questionbox"){
	    		$answers = $dom->find(".answer");
	    		$c = 1;
	    		foreach($answers as $a){
	    			$a->html('<h4 style="line-height: 12pt; font-weight:bold; font-size: 12pt; margin-bottom: 0pt; padding-bottom: 0pt;">'.$c.". ".htmlentities($a->text())."</h4>");
	    			$c++;
	    		}
	    	}

	    	$breaks = $dom->find(".break");
	    	//foreach($breaks as $b){
	    	//	$b->parent()->css('page-break-before','always');
	    	//	$b->remove();
	    	//}

	    	return $dom->html();
	    }

	    /*** COLORS ***/
	    public static $LIGHT_BLUE = [158, 193, 232];
	    public static $BLUE = [1, 162,229];
	    public static $DARK_BLUE = [23, 54, 93];
	    public static $LINK_BLUE = [33, 94, 158];
	    public static $GRAY = [147, 149, 152];
	    public static $LIGHT_GRAY = [237, 237, 238];
	    public static $DARK_GRAY = [56, 56, 57];
	    public static $BLACK = [0, 0, 0];
	    public static $WHITE = [255, 255, 255];
	}

?>