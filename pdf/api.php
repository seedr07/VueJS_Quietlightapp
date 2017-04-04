<?php

class API {

	public $session = false;

	public function __construct() {
	
	}

	protected function special_http_build_query($arrays, &$new = array(), $prefix = null ) {
	    if ( is_object( $arrays ) ) {
	        $arrays = get_object_vars( $arrays );
	    }

	    foreach ( $arrays AS $key => $value ) {
	        $k = isset( $prefix ) ? $prefix . '[' . $key . ']' : $key;
	        if ( is_array( $value ) OR is_object( $value )  ) {
	            $this->special_http_build_query( $value, $new, $k );
	        } else {
	            $new[$k] = $value;
	        }
	    }

	    return $new;
	}

    public function get($url, $data = []) {
        $result = $this->call('GET', $url, $data);
        return $result;
	}
	
	public function post($url, $data = []) {
    	return $this->call('POST', $url, $data);
	}

	public function put($url, $data = []) {
    	return $this->call('PUT', $url, $data);
	}

	public
	 function delete($url, $data = []) {
    	return $this->call('DELETE', $url, $data);
	}

	protected function call($method, $url, $data = []) {
		if(!in_array($method, ['GET','PUT','POST', 'DELETE'])) return FALSE;

		/* Remove any leading slashes */
		$url = preg_replace("|^/+|","",$url);

		$ch = curl_init();

		$skip_ssl = getenv('USE_SSL');

		$headers = ['Content-Type: application/json'];
		if($this->session) {
			$headers[] = 'Session: ' . $this->session;
		}
		$copts = [
			CURLOPT_RETURNTRANSFER => true,			// return web page 
			CURLOPT_HEADER         => false,		// don't return headers 
			CURLOPT_FOLLOWLOCATION => true,			// follow redirects 
			CURLOPT_ENCODING       => "",			// handle all encodings 
			CURLOPT_USERAGENT      => "",			// who am i 
			CURLOPT_AUTOREFERER    => true,			// set referer on redirect 
			CURLOPT_CONNECTTIMEOUT => 120,			// timeout on connect 
			CURLOPT_TIMEOUT        => 120,			// timeout on response 
			CURLOPT_MAXREDIRS      => 10,			// stop after 10 redirects 
			CURLOPT_SSL_VERIFYHOST => 2,			// verify ssl 
			CURLOPT_SSL_VERIFYPEER => !$skip_ssl,			// 
			CURLOPT_VERBOSE        => 0,				// 
			CURLOPT_HTTPHEADER	   => $headers		// set our headers
		];
		
		if($method == "POST") {
			$use_special = false;
			foreach ($data as $value) {
				if(is_array($value)) {
					$use_special = true;
					break;
				}
			}
			if($use_special){
				$data = $this->special_http_build_query($data); //this needs a special method to support file uploads AND multidimensional arrays
			}
			$copts[CURLOPT_POST] = TRUE;
			$copts[CURLOPT_POSTFIELDS] = json_encode($data);
		} elseif($method != "GET") {
	        $copts[CURLOPT_CUSTOMREQUEST] = $method;
			$copts[CURLOPT_POSTFIELDS] = http_build_query($data);
		}

		@curl_setopt_array($ch, $copts);

		$url = getenv("API_HOST")."/".$url;

		if($method == "GET" && !empty($data)) {
			$parsed_url = parse_url($url);
			$qdata = http_build_query($data);
			if(!empty($parsed_url['query'])) {
				$parsed_url['query'] .= "&";
			} else {
				$parsed_url['query'] = '';
			}

			$parsed_url['query'] .= $qdata;

			$url = $parsed_url['scheme']."://".$parsed_url['host'].$parsed_url['path']."?".$parsed_url['query'];
		}

		curl_setopt($ch, CURLOPT_URL, $url);

		$response = curl_exec($ch);
		if($response === FALSE) {
			error_log("Curl error for $url: " . curl_error($ch));
			return FALSE;
		}
        curl_close($ch);
		$result = json_decode($response, true);

		if($result === FALSE) {
			return $response; //return this even though it'll probably cause breakage down the line
		}

		// This is what happens when a session is invalid - if the API is doing this elsewhere... it needs to not do it
		if(isset($result['error']) && isset($result['error']['message']) && $result['error']['message'] == "Unauthorized") {
			return NULL;
		}
		return $result;
	}
}