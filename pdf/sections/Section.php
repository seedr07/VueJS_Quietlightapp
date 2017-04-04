<?php

class Section {
	public $section;
	public $api;
	public $configs;



	public function __construct($section, $pdf, $api, $configs = array()){
		$this->api = $api;
		$this->section = $section;
		$this->pdf = $pdf;
		$this->configs = $configs;

	}

	public function get(){
		$id = (isset($this->section['associated_id'])) ? $this->section['associated_id'] : false;

		if($id === false) throw new Exception("ID not set.");

		$url = $this->getUrl($id);

		$content = $this->api->get($url);
		$this->pdf->qlstore[$this->section['table']] = $content;
		$this->pdf->qlstore['cur_class'] = $this->section['table'];

		return $content;
	}

	public function getUrl($id){
		$table = (isset($this->section['table'])) ? $this->section['table'] : false;
		if($table == false) throw new Exception("Table not set");

		return "/".strtolower($table)."/".$id;
	}

	public function newPage(){
		//to be overwritten
	}

	public static function addPage($section, $pdf, $api, $configs){
		$section = new static($section, $pdf, $api, $configs);
		$section->newPage();
	}



	/**** COLOR ARRAYS ****/
	public static $DARK_BLUE = [19, 40, 75];


	
}

?>