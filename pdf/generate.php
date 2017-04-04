<?php

	require(__DIR__."/vendor/autoload.php");
	require(__DIR__."/qlpdf.php");

	require(__DIR__."/sections/Title.php");
	require(__DIR__."/sections/Disclosure.php");
	require(__DIR__."/sections/ExecutiveSummary.php");
	require(__DIR__."/sections/Financial.php");
	require(__DIR__."/sections/FinancialV2.php");
	require(__DIR__."/sections/Flex.php");
	require(__DIR__."/sections/FMI.php");
	require(__DIR__."/sections/Addendum.php");
	require(__DIR__."/sections/Question.php");
	require(__DIR__."/sections/QuestionBox.php");
	require(__DIR__."/sections/WebTraffic.php");


	$cli = !isset($_GET['summary_id']);

	$dotenv = new Dotenv\Dotenv(__DIR__);
	$dotenv->load();

	require(__DIR__."/api.php");
	$api = new API();

	$login = $api->post("/session/login", ['email' => getenv("API_USER"), "password"=> getenv("API_PASS")]);

	//Set Login
	if(isset($login['session'])){
		$api->session = $login['session'];
	}
	//@TODO get from $_GET
	if(isset($_GET['summary_id'])){
		$summary_id = $_GET['summary_id'];
		$generated_by = $_GET['generated_by'];
	} else {
		$summary_id = 16; 
		$generated_by = 4;
		//die("No Summary Specified.");
	}

	//Get Summary
	$summary = $api->get("/summary/".$summary_id);
	
	//Get and Hash Configs
	$configs = $api->get("/config?limit=1000");
	$config_hash = [];
	foreach($configs as $c){
		$config_hash[$c['name']] = $c['value'];
	}

	$pdf = new qlpdf(PDF_PAGE_ORIENTATION, 'pt', array(612, 792), true, 'UTF-8', false);

	// set document information
	$pdf->SetCreator(PDF_CREATOR);
	$pdf->SetAuthor('QuietLight Brokerage');
	$pdf->SetTitle($summary['name']);

	$pdf->SetMargins(18, 36, 18);
	$pdf->SetHeaderMargin(0);
	$pdf->SetFooterMargin(0);
	
	// set auto page breaks
	$pdf->SetAutoPageBreak(TRUE, 60);

	$pdf->setListIndentWidth(12);
	
	$pdf->setImageScale(1.53); 

	$pdf->AddFont('lato', '', 'lato', false);
	$pdf->AddFont('latolight', '', 'latolight', false);
	//$pdf->AddFont('latolighti', '', 'latolighti');
	$pdf->AddFont('latob', '', 'latob', false);

	//Vertical Space
	$tagvs = array(
                'h1'  => array(0 => array('h' => 1, 'n' => 1), 1 => array('h' => 1, 'n' => 1)),
                'h2'  => array(0 => array('h' => 1, 'n' => 1), 1 => array('h' => 1, 'n' => 1)),
                'h3'  => array(0 => array('h' => 1, 'n' => 1), 1 => array('h' => 1, 'n' => 1)),
                'h4'  => array(0 => array('h' => 1, 'n' => 1), 1 => array('h' => 1, 'n' => 1)),
                'h5'  => array(0 => array('h' => 1, 'n' => 1), 1 => array('h' => 1, 'n' => 1)),
                'p'   => array(0 => array('h' => 1, 'n' => 1), 1 => array('h' => 1, 'n' => 8)),
                'ul'  => array(0 => array('h' => 1, 'n' => 1), 1 => array('h' => 1, 'n' => 8)),                
                'ol'  => array(0 => array('h' => 1, 'n' => 1), 1 => array('h' => 1, 'n' => 8)),                
                'img' => array(0 => array('h' => 1, 'n' => 0), 1 => array('h' => 1, 'n' => 0)),
               );
    $pdf->setHtmlVSpace($tagvs);

    $pdf->qlstore['summary'] = $summary;

	$pdf->SetFont('lato', '', 11);

	//Get Sections
	$results = $api->get("/summarysection", ['summary_id'=>$summary_id,'limit'=>10000,'offset'=>0]);
	$sections = $results['sections'];

	$questions = [];
	foreach($sections as $section){
		
		//If no longer in a Question section, add them
		if(count($questions) && $section['table'] != "Question"){
			Question::addPage($questions, $pdf, $api, $config_hash);
			$questions = [];
		}

		switch($section['table']){
			case "Addendum":
				Addendum::addPage($section, $pdf, $api, $config_hash);
				break;
			case "Title":
				Title::addPage($section, $pdf, $api, $config_hash);
				break;
			case "Disclosure":
				Disclosure::addPage($section, $pdf, $api, $config_hash);
				break;
			case "ExecutiveSummary":
			case "Executivesummary":
			    ExecutiveSummary::addPage($section, $pdf, $api, $config_hash);
				break;
			case "Financial":
				Financial::addPage($section, $pdf, $api, $config_hash);
				break;
			case "FinancialV2":
			case "Financialv2":
				FinancialV2::addPage($section, $pdf, $api, $config_hash);
				break;
			case "Flex":
				Flex::addPage($section, $pdf, $api, $config_hash);
				break;
			//A question box is a big text box with all the questions in them.
			case "Questionbox":
				QuestionBox::addPage($section, $pdf, $api, $config_hash);
				break;
			case "WebTraffic":
			case "Webtraffic":
				WebTraffic::addPage($section, $pdf, $api, $config_hash);
				break;
			case "Question":
				$questions[] = $section;
				break;
		}
	}
	//If we ended with questions in store, write theme
	if(count($questions)) Question::addPage($questions, $pdf, $api, $config_hash);

	//Add "For More Info" page
	FMI::addPage([], $pdf, $api, $config_hash);

	$logs = $pdf->logs;

	$directory = __DIR__."/../generations/".str_replace(" ", "_", strtolower($summary['name']))."/";
	$web_dir = "/generations/".str_replace(" ", "_", strtolower($summary['name']))."/";
	$filename = str_replace(" ", "_", strtolower($summary['name']))."-".date("m-d-y-h-i").".pdf";
	$img_filename = str_replace(" ", "_", strtolower($summary['name']))."-".date("m-d-y-h-i").".png";
	
	@mkdir($directory,0777,true);
	$pdf->Output($directory.$filename, "F");
	
	// instantiate Imagick 
	$im = new Imagick();

	$im->setResolution(300,300);
	$im->readimage($directory.$filename.'[0]'); 
	$im->setImageFormat('jpeg');    
	$im->resizeImage(153,198,Imagick::FILTER_LANCZOS,1);
	$im->writeImage($directory.$img_filename); 
	$im->clear(); 
	$im->destroy();

	$api->post("/summary/".$summary_id."/generated",[
		'file' => $web_dir.$filename,
		'thumb' => $web_dir.$img_filename,
		'generator' => $generated_by
	]);

	echo json_encode([
		'logs' => $logs,
		'file' => $web_dir.$filename
	]);

?>
