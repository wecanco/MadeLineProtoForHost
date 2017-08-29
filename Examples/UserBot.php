<?php
	// ربات پاسخگو
	//?phone=+989357973301
	require_once('UserLogin.php'); // خواندن سشن
	
	class GoogleTranslate
	{

		public static function translate($source, $target, $text) {
			
			$response 		= self::requestTranslation($source, $target, $text);
			//$translation 	= self::getSentencesFromJSON($response);
			return $response;
		}

		protected static function requestTranslation($source, $target, $text) {
			$url = "https://translate.google.com/translate_a/single?client=at&dt=t&dt=ld&dt=qca&dt=rm&dt=bd&dj=1&hl=es-ES&ie=UTF-8&oe=UTF-8&inputm=2&otf=2&iid=1dd3b944-fa62-4b55-b330-74909a99969e";
			$fields = array(
				'sl' => urlencode($source),
				'tl' => urlencode($target),
				'q' => urlencode($text)
			);

			$fields_string = "";
			foreach($fields as $key=>$value) {
				$fields_string .= $key.'='.$value.'&';
			}
			
			rtrim($fields_string, '&');
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, count($fields));
			curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_ENCODING, 'UTF-8');
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($ch, CURLOPT_USERAGENT, 'AndroidTranslate/5.3.0.RC02.130475354-53000263 5.1 phone TRANSLATE_OPM5_TEST_1');

			$result = curl_exec($ch);

			curl_close($ch);
			return $result;
		}
		/*
		protected static function getSentencesFromJSON($json) {
			$sentencesArray = json_decode($json, true);
			$sentences = "";
			foreach ($sentencesArray["sentences"] as $s) {
				$sentences .= $s["trans"];
			}
			return $sentences;
		}*/
	}
									
	function curl($url,$timeout=7){		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		//curl_setopt($ch, CURLOPT_SSLVERSION,3);
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
		curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		curl_setopt ($ch, CURLOPT_TIMEOUT, $timeout);
		$data = curl_exec($ch);
		curl_close($ch);
		file_put_contents($LocalFile,$data);
		//$file = fopen($LocalFile, "w+");
		//fputs($file, $data);
		//fclose($file);
	}
	
	if(!file_exists('SentMSGs')){
		file_put_contents('SentMSGs',"");
	}
	
	try{
		mkdir('temp');
	} catch (Exception $e) { 
		//$text = "❌ ".$e->getMessage(); 
	}
	
	$SentMSGs=explode("\n",file_get_contents('SentMSGs'));
	$offset= -1;
	while(true){
		if(file_exists('_stop_bot')){
			echo "ربات متوقف شد.<br>";
			exit();
		}
		$updates = $MadelineProto->get_updates(['offset' => $offset, 'limit' => 50]);
		file_put_contents('updates',json_encode($updates));
		//$updates = $MadelineProto->API->get_updates(['offset' => $offset, 'limit' => 50, 'timeout' => 0]);
		foreach($updates as $update){			
			try {
				$out=0;
				$text='';
				$peer='';
				$channel_id = "";
				$uniq="";
				$mid=null;
				
				if(isset($update['update']['message']['out'])){
					$out = $update['update']['message']['out'];
				}
				$message='';
				if(isset($update['update']['message']['message'])){
					$message = $update['update']['message']['message'];
				}
				$media='';
				$document='';
				$photo='';
				$caption='';
				$caption2='';
				$file_type='';
				if(isset($update['update']['message']['media']['caption'])){
					$caption = trim($update['update']['message']['media']['caption']);
					$caption2 = strtolower($caption);
				}
				if(isset($update['update']['message']['media'])){
					$media = $update['update']['message']['media'];
				}
				if(isset($media['document'])){
					$document = $media['document'];
					//$thumb = $document['thumb'];
					
					switch($document['mime_type']){
						case "image/png":
						case "image/jpeg":
							$file_type = explode("/",$document['mime_type'])[1];
							if(in_array($caption2,array('pic2sticker','i love wecanco')) ){
								$photo = $document;
								$message = '/pic2sticker ';
							}
						break;
					}
				}
				if(isset($media['photo'])){
					$photo = $media['photo'];
					if(in_array($caption2,array('pic2sticker','i love wecanco')) ){
						$file_type='jpg';
						$message = '/pic2sticker ';
					}
				}
				
				
				
				$sent=0;
				if($out != 1){
					if($message !=""){
						$mid = $update['update']['message']['id'];
						$from_id="";
						if(isset($update['update']['message']['from_id'])){
							$from_id = $update['update']['message']['from_id'];
							$peer = $from_id;
						}
						
						if(isset($update['update']['message']['to_id']['channel_id'])){
							$channel_id = $update['update']['message']['to_id']['channel_id'];
							$peer = "-100".$channel_id;
						}
						
						$date = $update['update']['message']['date'];
						$uniq = $from_id."_".$mid."_".$date;
						
						if(!in_array($uniq,$SentMSGs) && $peer !=''){
							/*
							if($media !=""){
								$name = 'thumb_'.time().'.jpg';
								$file = '../temp/'.$name;
								$res = $MadelineProto->download_to_file($thumb, $file);
								$text =  "http://tlbots.cf/temp/".$name;
								$m = $MadelineProto->messages->sendMessage(['peer' => $peer, 'message' => $text, 'parse_mode' => 'HTML' ]);	
								break;
								
							}
							*/
				
							switch($message){
								case "/start2":
								$text='سلام من ربات میدلاین هستم! منو @WeCanCo ساخته! 🙃';
								break;
								
								case "/wecan":
								$text='به افتحارش!!! 👏👏👏';
								break;
								
								case "/mymention":
								$text='<a href="mention:'.$from_id.'">تماس با من</a>';
								break;
								
										
										
								default:
								if(strpos($message,"/mymention ") !== false){
									$text='<a href="mention:'.$from_id.'">'.str_replace("/mymention ","",$message).'</a>';
								}else if(strpos($message,"/translate ") !== false){
									$info = trim(str_replace("/translate ","",$message));
									$info = explode("|",$info);
									$lang = trim($info[0]);
									$content = trim($info[1]);
									
									$source 		= 'auto';

									$translation 	= GoogleTranslate::translate($source, $lang, $content);
									$translation = json_decode($translation,true);
									$src = $translation['src'];
									$trans = $translation['sentences'][0]['trans'];
									$orig = $translation['sentences'][0]['orig'];
									$text = "<b>$src:</b> <i>$orig</i>

<b>$lang:</b> $trans								
🌐 @WeCanGP";
									
								}else if(strpos($message,"/fakemail ") !== false){
								
								}else if(strpos($message,"/pic2sticker ") !== false){
									//if($from_id != "" && $from_id=='282120410'){
										$link = trim(str_replace("/pic2sticker ","",$message));
										$file='temp/img_'.time().'.'.$file_type;
										if($photo !=""){
										}else{
										}
										$res = $MadelineProto->download_to_file($media, $file);
										
										if($file_type=='jpg' || $file_type=='jpeg'){
											$image=  imagecreatefromjpeg($file);
											ob_start();
											imagejpeg($image,NULL,100);
										}else{
											$image=  imagecreatefrompng($file);
											ob_start();
											imagepng($image);
										}
										
										$cont=  ob_get_contents();
										ob_end_clean();
										imagedestroy($image);
										$content =  imagecreatefromstring($cont);
										$stick = 'st_'.time().'.webp';
										$fullPath = 'temp/'.$stick;
										imagewebp($content,$fullPath);
										imagedestroy($content);

										$inputFile = $MadelineProto->upload($fullPath);
										$caption='';
										$inputMedia = ['_' => 'inputMediaUploadedDocument', 'file' => $inputFile, 'mime_type' => mime_content_type($fullPath), 'caption' => $caption, 'attributes' => [['_' => 'documentAttributeFilename', 'file_name' => $stick]]];
										
										$p = ['peer' => $peer, 'media' => $inputMedia];
										$res = $MadelineProto->messages->sendMedia($p);
										unlink($file);
										unlink($fullPath);
										
									//}
									
								}else if(strpos($message,"/attack ") !== false){
									
									
									
								}else if(strpos($message,"/optimizeSite ") !== false){
									$site = trim(str_replace("/optimizeSite ","",$message));
									$site = explode("|",$site."|");
									$type=strtolower(trim($site[1]));
									if($type==""){
										$type="desktop";
									}
									$site=trim($site[0]);

									$sitename = parse_url($site);
									$sitename = $sitename['host'];
									$site = urlencode($site);
									$url ="https://www.googleapis.com/pagespeedonline/v3beta1/optimizeContents?key=AIzaSyAwlPiPJIkTejgqqH01v9DmtPoPeOPXDUQ&url=".$site."%2F&strategy=".$type."=&rule=AvoidLandingPageRedirects&rule=EnableGzipCompression&rule=LeverageBrowserCaching&rule=MainResourceServerResponseTime&rule=MinifyCss&rule=MinifyHTML&rule=MinifyJavaScript&rule=MinimizeRenderBlockingResources&rule=OptimizeImages&rule=PrioritizeVisibleContent&rule=AvoidPlugins&rule=ConfigureViewport&rule=SizeContentToViewport&rule=SizeTapTargetsAppropriately&rule=UseLegibleFontSizes";
									
									$dir="temp/";
									$fileName=$sitename."_".$type."_".time().".zip";
									$fullPath = $dir.$fileName;
									curl_dl($url,$fullPath);
									if(filesize($fullPath) > 500){
										$caption = '📌 Read MANIFEST file to replace optimized('.$type.') site contents. | @WeCanGP';
										
										$inputFile = $MadelineProto->upload($fullPath);
										$inputMedia = ['_' => 'inputMediaUploadedDocument', 'file' => $inputFile, 'mime_type' => mime_content_type($fullPath), 'caption' => $caption, 'attributes' => [['_' => 'documentAttributeFilename', 'file_name' => $fileName]]];
										
										$p = ['peer' => $peer, 'media' => $inputMedia];
										$res = $MadelineProto->messages->sendMedia($p);
									}else{
										$text = "👨🏻‍💻 This site (".$sitename.") no need optimization ☺️";
									}
									unlink($fullPath);
									
									
									
									
				
			
								}else if(strpos($message,"/delmsg_") !== false){
									$id = trim(str_replace("/delmsg_","",$message));
									$id = explode("_",base64_decode($id."=")."_");
									$gid = trim($id[0]);
									$mid = trim($id[1]);
									if(is_numeric($mid)){
										if($gid != ""){
											$res = $MadelineProto->channels->deleteMessages(['channel' => $gid, 'id' => [$mid] ]);
										}else{
											$res = $MadelineProto->messages->deleteMessages(['id' => [$mid] ]);
										}
										//$text = json_encode($res);
									}
									
								}else if(strpos($message,"/call ") !== false){
									$to = trim(str_replace("/call ","",$message));
									$controller = $MadelineProto->request_call($to);
									file_put_contents('controller',json_encode($controller));
									
								}else if(strpos($message,"/checkUsername ") !== false){
									$uername = trim(str_replace("/checkUsername ","",$message));
									$Bool = $MadelineProto->account->checkUsername(['username' => trim(str_replace("@","",$uername)) ]);
									if($Bool){
										$text = '✅ Take It! 😜';
									}else{
										$text = '❌ Exist 😏';
									}
								}else if(strpos($message,"/html2text ") !== false){
									$html = trim(str_replace("/html2text ","",$message));
									$text = $html;
								}else if(strpos($message,"/info ") !== false){
									$id = trim(str_replace("/info ","",$message));
									$info = $MadelineProto->get_full_info($id);
									
									$user_id = isset($info['full']['user']['id']) ? $info['full']['user']['id'] : "";
									$user_access_hash =  isset($info['full']['user']['access_hash']) ? $info['full']['user']['access_hash'] : "";
									$first_name =  isset($info['full']['user']['first_name']) ? $info['full']['user']['first_name'] : "";
									$last_name =  isset($info['full']['user']['last_name']) ? $info['full']['user']['last_name'] : "";
									$username =  isset($info['full']['user']['username']) ? $info['full']['user']['username'] : "";
									$phone =  isset($info['full']['user']['phone']) ? $info['full']['user']['phone'] : "";
									$status =  isset($info['full']['user']['status']['_']) ? $info['full']['user']['status']['_'] : "";
									$bot_api_id =  isset($info['bot_api_id']) ? $info['bot_api_id'] : "";
									$last_update =  isset($info['last_update']) ? date("Y-m-d H:i:s",$info['last_update']) : "";
									$about =  isset($info['full']['about']) ? $info['full']['about'] : "";
									$profile_photo_id =  isset($info['full']['profile_photo']['id']) ?  $info['full']['profile_photo']['id'] : "";
									$profile_photo_access_hash =  isset($info['full']['profile_photo']['access_hash']) ? $info['full']['profile_photo']['access_hash'] : "";
									$profile_photo_date =  isset($info['full']['profile_photo']['date']) ? date("Y-m-d H:i:s",$info['full']['profile_photo']['date']) : "";
									
									
									
									$text="
👨🏻‍💻 $id <b>info ‌</b>:

<b>ID: ‌</b> $user_id 
<b>Access Hash: ‌</b> $user_access_hash  ‌
<b>First Name: ‌</b> $first_name ‌
<b>Last Name: ‌</b> $last_name ‌
<b>Username: ‌</b> $username ‌
<b>Phone: ‌</b> $phone ‌
<b>Status: ‌</b> $status  ‌
<b>Bot ID: ‌</b> $bot_api_id ‌
<b>Last Update: ‌</b> $last_update ‌
<b>About: ‌</b> $about  ‌
<b>Profile Photo ID: ‌</b> $profile_photo_id ‌
<b>Profile Photo Access Hash: ‌</b> $profile_photo_access_hash ‌
<b>Profile Photo Date: ‌</b> $profile_photo_date ‌
--------------------------
💝 Tnx for MadelineProto | @WeCanCo | @WeCanGP

									";
									
									
								}else if(strpos($message,"/html2pdf ") !== false){
									$html = trim(str_replace("/html2pdf ","",$message));
									if($html !=""){
										
									}
									//
								}else if(strpos($message,"/web2pdf ") !== false){
									$web = trim(str_replace("/web2pdf ","",$message));
									$web = explode("|",$web."|");
									$name = trim($web[1]);
									$web= trim($web[0]);
									if($web !=""){
										
									}
								}else if(strpos($message,"/link2file ") !== false){
									$req = trim(str_replace("/link2file ","",$message));
									$req = explode("|",$req."|");
									$link = trim($req[0]);
									$name = trim($req[1]);
									$header = get_headers($link,true);
									if(isset($header['Content-Length'])){
										$file_size = $header['Content-Length'];
									}else{
										$file_size = -1;
									}
									$sizeLimit = ( 40 * 1024 * 1024);
									if($name==""){
										$name=explode("/",$link);
										$name = $name[sizeof($name)-1];
									}
									if($file_size > 0 && $file_size <= $sizeLimit ){
										$txt = "⏳ <b>Downloading...</b> ".$name."";
										$m = $MadelineProto->messages->sendMessage(['peer' => $peer, 'reply_to_msg_id' => $mid , 'message' => $txt, 'parse_mode' => 'HTML' ]);
										if(isset($m['updates'][0]['id'])){
											$mid = $m['updates'][0]['id'];
										}else{
											$mid = $m['id'];
										}
										
										$file = file_get_contents($link);
										$localFile = 'temp/'.$name;
										file_put_contents($localFile,$file);
										$txt = "⏳ <b>Uploading...</b> ".$name."";
										$ed = $MadelineProto->messages->editMessage(['peer' => $peer, 'id' => $mid, 'message' => $txt, 'parse_mode' => 'html' ]);
										$caption = '📌 '.$name.' | @WeCanGP';
										
										$inputFile = $MadelineProto->upload($localFile);
										$txt = "⏳ Sending...: <b>".$name."</b>";
										$ed = $MadelineProto->messages->editMessage(['peer' => $peer, 'id' => $mid, 'message' => $txt, 'parse_mode' => 'html' ]);
										$inputMedia = ['_' => 'inputMediaUploadedDocument', 'file' => $inputFile, 'mime_type' => mime_content_type($localFile), 'caption' => $caption, 'attributes' => [['_' => 'documentAttributeFilename', 'file_name' => $name]]];
										
										$p = ['peer' => $peer, 'media' => $inputMedia];
										$res = $MadelineProto->messages->sendMedia($p);
										unlink($localFile);
										
										$txt = "✅ <b>Sent!</b> @WeCanCo 😎";
										$ed = $MadelineProto->messages->editMessage(['peer' => $peer, 'id' => $mid, 'message' => $txt, 'parse_mode' => 'html' ]);
										
										
									}else{
										$text = "❌ Max File Size: <b>".($sizeLimit / 1024 /1024 )."MB</b> but your file is <b>".round(($file_size/1024/1024),2)."MB</b>";
									}
									
								}else if(strpos($message,"/madeline ") !== false){
									$req = str_replace("/madeline ","",$message);
									$req = explode("%",$req);
									
									switch(trim($req[0])){
										case "messages.getPeerDialogs":
										$parms = json_decode(trim($req[1]), TRUE);
										$res = $MadelineProto->messages->getPeerDialogs($parms);
										$text = json_encode($res);
										break;
										
										case "messages.sendMessage":
										$parms = json_decode(trim($req[1]), TRUE);
										$res = $MadelineProto->messages->sendMessage($parms);
										$text = json_encode($res);
										break;
										
										case "photos.getUserPhotos":
										$parms = json_decode(trim($req[1]), TRUE);
										$res = $MadelineProto->photos->getUserPhotos($parms);
										$counter=0;
										foreach($res['photos'] as $photo){
											$id = $photo['id'];
											$access_hash = $photo['access_hash'];
											$counter++;
											
											if(isset($req[2])){
												$peer = trim($req[2]);
											}
											
											$InputMedia = ['_' => 'inputMediaPhoto', 'id' => ['_' => 'inputPhoto', 'id' => $id, 'access_hash' => $access_hash], 'caption' => 'عکس شماره '.$counter.' پروفایل '.$parms['user_id'].'  |  گروه وی کن @WeCanGP'];
											
											$p = ['peer' => $peer, 'media' => $InputMedia];			
											$res = $MadelineProto->messages->sendMedia($p);
										}
										
										//$text = json_encode($res);
										break;
										
										case "channels.getMessages":
										$parms = json_decode(trim($req[1]), TRUE);
										$parname=[];
										if($parms['id'][0]=='all'){
											$parms['id']=null;
											$ids=array();
											for($i=0; $i<2000;$i++){
												$ids[]=$i;
											}
											$parms['id']=$ids;
											$parname[]="all";
										}else{
											$parname = $parms['id'];
										}
										
										$res = $MadelineProto->channels->getMessages($parms);
										$msgs = json_encode($res);
										$filename = 'channel_Messages_'.str_replace("@","",$parms['channel'])."_".implode(",",$parname).".txt";
										$file = 'temp/'.$filename;
										file_put_contents($file,$msgs);
										if(isset($req[2])){
											$peer = trim($req[2]);
										}
										
										$caption = 'Messages of '.$parms['channel'].' ('.implode(",",$parname).') |  @WeCanGP';
										$inputFile = $MadelineProto->upload($file);
										$inputMedia = ['_' => 'inputMediaUploadedDocument', 'file' => $inputFile, 'mime_type' => mime_content_type($file), 'caption' => $caption, 'attributes' => [['_' => 'documentAttributeFilename', 'file_name' => $filename]]];
										

										$p = ['peer' => $peer, 'media' => $inputMedia];
										$res = $MadelineProto->messages->sendMedia($p);
										unlink($file);
										//$text = json_encode($res);

										
										break;
										
										
										
										default:
										
										$text= '💥 با استفاده از این دستور شما میتوانید متدهای میدلاین را تست کنید!
										
										🖥 ساختار ارسال دستور:
										/madeline پارمترهابصورت جی سون % نام متد
										📌 مانند:
										/madeline messages.getPeerDialogs % {"peers": ["@wecanco"] }
										
										/madeline photos.getUserPhotos % {"user_id": "@wecanco", "offset": 0, "max_id": 0, "limit": 1 }
										
										/madeline messages.sendMessage % { "peer": "@wecanco",  "message": "تست",  "parse_mode": "html"}
										
										/madeline channels.getMessages % {"channel": "@wecangp", "id": [78,79,80,81]}
										
										';
										break;
									}
									
									}else if(strpos($message,"/time") !== false){
										$timezone1 = trim(str_replace("/time","",$message));
										$timezone2="";
										
										if($timezone1==""){
											$timezone1 = 'Asia/Tehran';
										}else{
											$timezone = explode(" ",$timezone1);
											$timezone1 = $timezone[0];
											if(isset($timezone[1])){
												$timezone2 = $timezone[1];
											}
										}
										date_default_timezone_set($timezone1);
										$time1 = date("Y-m-d H:i:s ");
										if($timezone2 !=""){
											date_default_timezone_set($timezone2);
											$time2 = date("Y-m-d H:i:s ");
											$T1 = new DateTime($time1);
											$T2 = new DateTime($time2);
											$diff = $T2->diff($T1);
											$diff = $diff->format('%a days %h hours ');
											$txt="
											
⏰ $timezone1: <b>$time1</b>
⏰ $timezone2: <b>$time2</b>
											
🕰 Time Diff: <b>".$diff."</b> 

Powered By <a href='https://github.com/danog/MadelineProto'>MadelineProto</a>";
										}else{
											$txt="⏰ $timezone1: <b>".$time1."</b> Powered By <a href='https://github.com/danog/MadelineProto'>MadelineProto</a>";
										}
										
									
										$m = $MadelineProto->messages->sendMessage(['peer' => $peer, 'reply_to_msg_id' => $mid , 'message' => $txt, 'parse_mode' => 'HTML' ]);
										//file_put_contents('m',json_encode($m));
										if(isset($m['updates'][0]['id'])){
											$mid = $m['updates'][0]['id'];
										}else{
											$mid = $m['id'];
										}
										
										if($timezone2 ==""){
											sleep(2);
											for($i=0; $i<2; $i++){
												if($i%2==0){
													$powT = " Powered By <a href='https://github.com/danog/MadelineProto'>MadelineProto</a>";
													$powT = " @WeCanCo 👨🏻‍💻";
												}else{
													$powT = " Created By <a href='tg://user?id=282120410'>WeCanCo</a>";
													$powT = " @WeCanGP 💝";
												}
												$txt="⏰ $timezone1: <b>".date("Y-m-d H:i:s ")."</b>".$powT;
												$ed = $MadelineProto->messages->editMessage(['peer' => $peer, 'id' => $mid, 'message' => $txt, 'parse_mode' => 'html' ]);
												sleep(1);
											}
										}
								
										
									}else if($channel_id==""){
										$text='سلام من ربات میدلاین هستم! منو @WeCanCo ساخته!
										دستورات من:
										<b>/start2</b>  -> شروع
										<b>/wecan</b>  -> سازنده
										<b>/mymention</b> [TEXT] -> منشن شما
										<b>/madeline</b> help -> تست متدهای میدلاین
										<b>/time</b> Asia/Tehran -> اعلام زمان و تاریخ
										<b>/link2file</b> LINK -> تبدیل لینک به فایل
										<b>/html2text</b> HTML -> تبدیل اچ تی ام ال به تکست
										';
									}else{
									
								}
								break;
							}
							
							
							
						}
					}
				}
				
				
				} catch (Exception $e) { 
					$text = "❌ ".$e->getMessage(); 
					//$text = json_encode($e); 
				}
			
			if($text !="" && $peer !=""){
				$SentMSGs[]=$uniq;
				$m = $MadelineProto->messages->sendMessage(['peer' => $peer, 'reply_to_msg_id' => $mid , 'message' => $text, 'parse_mode' => 'HTML' ]);	
				$gid="";
				if(isset($m['updates'][0]['id'])){
					$mid = $m['updates'][0]['id'];
					$gid = $peer;
				}else{
					$mid = $m['id'];
				}
				$delmsgID = "🗑 /delmsg_".str_replace("=","",base64_encode($gid."_".($mid)));
				$text .= "

".$delmsgID."";
				$ed = $MadelineProto->messages->editMessage(['peer' => $peer, 'id' => $mid, 'message' => $text, 'parse_mode' => 'html' ]);
				$sent=1;
				//$MadelineProto->messages->sendMessage(['peer' => $peer, 'reply_to_msg_id' => $mid , 'message' => json_encode($m), 'parse_mode' => 'HTML' ]);
			}
			
			if($sent==1){
				echo "پیام ارسال شد!<br>";
			}else{
				echo ". ";
			}
			
			
			
		}
		//print_r($up);
		//\danog\MadelineProto\Serialization::serialize($sessionFile, $MadelineProto);
		file_put_contents('SentMSGs',implode("\n",$SentMSGs));
		//$MadelineProto = \danog\MadelineProto\Serialization::deserialize($sessionFile);
		//sleep(1);
		
	}
	
