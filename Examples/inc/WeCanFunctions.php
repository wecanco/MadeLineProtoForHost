<?php
	/***********************************/
	/****************توابع**************/
	/************* WeCan-Co.ir**********/
	/***********************************/
	
	if (!function_exists('readline')) {
		function readline($prompt = null)
		{
			if ($prompt) {
				echo $prompt;
			}
			$fp = fopen('php://stdin', 'r');
			$line = rtrim(fgets($fp, 1024));
			
			return $line;
		}
	}
	
	function curl($url,$timeout=7){		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		//curl_setopt($ch, CURLOPT_SSLVERSION,3);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
		//curl_setopt($ch, CURLOPT_USERAGENT, $_REQUEST['HTTP_USER_AGENT']);
		curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		curl_setopt ($ch, CURLOPT_TIMEOUT, $timeout);
		$data = curl_exec ($ch);
		$error = curl_error($ch); 
		curl_close ($ch);
		return $data;
	}
	
	function curl_dl($url,$LocalFile,$timeout=120){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		//curl_setopt($ch, CURLOPT_POST, count($parms));
		//curl_setopt($ch, CURLOPT_POSTFIELDS, $parms);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		curl_setopt ($ch, CURLOPT_TIMEOUT, $timeout);
		curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
		$data = curl_exec($ch);
		curl_close($ch);
		file_put_contents($LocalFile,$data);
		//$file = fopen($LocalFile, "w+");
		//fputs($file, $data);
		//fclose($file);
	}
	
	function RemoveProxies($sessionFile){
		$sess = file_get_contents($sessionFile);
		
		preg_match_all('/("extra";a:\\d:\\{s:)(.*?)(;})/s',$sess,$m);
		foreach($m[0] as $extra){
			$sess = str_replace($extra,'"extra";a:0:{}',$sess);
		}
		$sess = str_replace('s:11:"\\SocksProxy"','s:7:"\\Socket"',$sess);
		
		file_put_contents($sessionFile,$sess);
	}
	
	function retrieve_remote_file_size($url){
		$ch = curl_init($url);
		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, TRUE);
		curl_setopt($ch, CURLOPT_NOBODY, TRUE);
		
		$data = curl_exec($ch);
		$size = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
		
		curl_close($ch);
		return $size;
	}	
	
		
	function CRequest($url,$fields,$timeout=120){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, count($fields));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
		$result = curl_exec($ch);
		curl_close($ch);
		return $result;
	}
	
	
	function checkOnline($domain) {
	   $curlInit = curl_init($domain);
	   curl_setopt($curlInit,CURLOPT_CONNECTTIMEOUT,7);
	   curl_setopt($curlInit,CURLOPT_TIMEOUT,7);
	   curl_setopt($curlInit,CURLOPT_HEADER,true);
	   curl_setopt($curlInit,CURLOPT_NOBODY,true);
	   curl_setopt($curlInit,CURLOPT_RETURNTRANSFER,true);
	   $response = curl_exec($curlInit);

	   curl_close($curlInit);
	   if ($response) return true;
	   return false;
	}