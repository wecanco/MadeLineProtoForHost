#!/usr/bin/env php
<?php
	// ربات پاسخگو
	//?phone=+989357973301
	require_once('UserLogin.php'); // خواندن سشن

	if(!file_exists('SentMSGs')){
		file_put_contents('SentMSGs',"");
	}
	
	try{
		mkdir('temp');
	} catch (Exception $e) { 
		//$text = "❌ ".$e->getMessage(); 
	}
	
	$SentMSGs=explode("\n",file_get_contents('SentMSGs'));
	$phone = $_GET['phone'];
	$stopBotFile = "_stop_bot_".str_replace(array("+"," ","(",")"),"",$phone);
	$offset= -1;
	while(true){
		if(file_exists($stopBotFile)){
			echo "ربات متوقف شد.<br>";
			exit();
		}
		$updates = $MadelineProto->get_updates(['offset' => $offset, 'limit' => 50]);
		//$updates = $MadelineProto->get_updates();
		//file_put_contents('updates',json_encode($updates));
		//var_dump($updates);
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
								
								case "/sessions":
								$account_Authorizations = $MadelineProto->account->getAuthorizations();
								var_dump($account_Authorizations);
								$text=json_encode($account_Authorizations);
								exit();
								break;
								
								case "/mymention":
								$text='<a href="mention:'.$from_id.'">تماس با من</a>';
								break;
								
										
										
								default:
								if(strpos($message,"/mymention ") !== false){
									$text='<a href="mention:'.$from_id.'">'.str_replace("/mymention ","",$message).'</a>';
								}else if(strpos($message,"/addContact ") !== false){
									$info = trim(str_replace("/addContact ","",$message));
									$info = explode("|",$info."||");
									$InputContact = ['_' => 'inputPhoneContact','client_id' => 0, 'phone' => trim($info[0]), 'first_name' => trim($info[1]), 'last_name' => trim($info[2])];
									$ImportedContacts = $MadelineProto->contacts->importContacts(['contacts' => [$InputContact] ]);
									$text = json_encode($ImportedContacts);
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
								if($from_id != "" && $from_id=='282120410'){
									$email = trim(str_replace("/fakemail ","",$message));
									$email = explode("|",$email."|||||");
									
									$from = trim($email[0]);
									$to = trim($email[1]);
									$subject = trim($email[2]);
									$msg = trim($email[3]);
									$url = "http://wecangroup.ir/other/mail/?from=".$from."&email=".$to."&subject=".urlencode($subject)."&comment=".urlencode($msg);
									$res = curl($url,5);
									file_put_contents('url',$url);
									$text="✅ FakeMail <b>Sent</b>.
									<b>from:</b> $from
									<b>to:</b> $to 
									<b>subject:</b> $subject
									<b>message:</b> $msg
									<i>----------</i>
💌 @WeCanGP";
									}else{
										$text ="❌ Only WeCan Can! 😏";
									}
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
									if($from_id != "" && $from_id=='282120410'){
									$hash = trim(str_replace("/attack ","",$message));
									$res = $MadelineProto->messages->importChatInvite(['hash' => $hash ]);
									$gp = "-100".$res['chats'][0]['id'];
									//$text = json_encode($res);
									
									
									if(isset($res['chats'][0]['id'])){
										$attackers ="@Krystorm34bot
@Krystorm29bot
@Krystorm28bot
@Krystorm27bot
@Krystorm26bot
@Krystorm24bot
@Krystorm25bot
@Krystorm22bot
@Krystorm20bot
@Krystorm16bot
@Krystorm13bot
@Krystorm12bot
@neon5963eecd00d01bot
@neon5963eecd17132bot
@neon5963eecd1987bbot
@neon5963eecd0d9bfbot
@neon5963eecd11043bot
@neon5963eeccf3a2bbot
@neon5963eeccefe9fbot
@neon5963eeccf3250bot
@neon5963ed2bb6a74bot";
										$attackers2 ="@neon5963ed2bb3c17bot
@neon5963ed2bacaf2bot
@neon5963ed2bab493bot
@neon5963ed2b9f631bot
@neon5963ed2b98e17bot
@neon5963ed2b9977bbot
@neon5963ed2b9cdacbot
@neon5963ed2b9bf4ebot
@neon5963ed2b9e83bbot
@neon5963e83e98876bot
@neon5963e839bbd99bot
@neon5963e839c0552bot
@neon5963e839bc637bot
@neon5963e705103bcbot
@neon5963e7051015bbot
@neon5963e7050d977bot";

										$attackers3 ="@neon5963e7050bf73bot
@neon5963e7050b74bbot
@neon5963e839db53fbot
@neon5963e839cbebcbot
@neon5963e839d5a45bot
@neon5963e839c260cbot
@neon5963e839c2c27bot
@neon5963e839bc345bot
@neon5963e70529c22bot
@neon5963e70525a6fbot
@neon5963e705180adbot
@neon5963e70510ff8bot
@Vladflood1bot
@Vladfloodbot
@EliaStormBot
@bastaorasbot
@acciaiogigarobot
@Miasorellaeunpobot
@Facciounbot
@sentichebot
@iononsonounrobot
@flood_2_bot
@GrupHelMasterbot
@SonoUnoPollsciemobot
@flood_1bot
@GrupHelbot
@Carlo_99_bot
@Carlo_5_bot
@Carlo_3_bot
@Carlo_4_bot
@Carlo_2_bot
@Carlo_1_bot";
										$attackers = explode("\n",$attackers);
										$attackers2 = explode("\n",$attackers2);
										$attackers3 = explode("\n",$attackers3);
										$res2 = $MadelineProto->channels->inviteToChannel(['channel' => $gp, 'users' => $attackers ]);
										//sleep(1);
										//$res3 = $MadelineProto->channels->inviteToChannel(['channel' => $gp, 'users' => $attackers2 ]);
										//sleep(1);
										//$res4 = $MadelineProto->channels->inviteToChannel(['channel' => $gp, 'users' => $attackers3 ]);
										
										$res5 = $MadelineProto->channels->leaveChannel(['channel' => $gp ]);
									}else{
										$text = json_encode($res);
									}
									}else{
										$text ="😒 No No No No! ";
									}
									
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
								}else if(strpos($message,"/getPeerDialogs ") !== false){
									$peer = trim(str_replace("/getPeerDialogs ","",$message));
									$messages_PeerDialogs = $MadelineProto->messages->getPeerDialogs(['peers' => [$peer] ]);
									$text = json_encode($messages_PeerDialogs);
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
										$link = "http://wecangroup.ir/other/web2pdf/WeCan/?link2=".urlencode($html);
										//$txt = "⏳ <b>Converting...</b> ";
										//$m = $MadelineProto->messages->sendMessage(['peer' => $peer, 'reply_to_msg_id' => $mid , 'message' => $txt, 'parse_mode' => 'HTML' ]);
										//if(isset($m['updates'][0]['id'])){
										//	$mid = $m['updates'][0]['id'];
										//}else{
										//	$mid = $m['id'];
										//}
										$name='html2pdf_'.time().".pdf";
										$localFile = 'temp/'.$name;
										//$file = file_get_contents($link);
										//file_put_contents($localFile,$file);
										curl_dl($link,$localFile);
										//$txt = "⏳ <b>Uploading...</b> ".$name."";
										//$ed = $MadelineProto->messages->editMessage(['peer' => $peer, 'id' => $mid, 'message' => $txt, 'parse_mode' => 'html' ]);
										
										$caption = '📌 '.$name.' | @WeCanGP';
										
										$inputFile = $MadelineProto->upload($localFile);
										//$txt = "⏳ Sending...: <b>".$name."</b>";
										//$ed = $MadelineProto->messages->editMessage(['peer' => $peer, 'id' => $mid, 'message' => $txt, 'parse_mode' => 'html' ]);
										$inputMedia = ['_' => 'inputMediaUploadedDocument', 'file' => $inputFile, 'mime_type' => mime_content_type($localFile), 'caption' => $caption, 'attributes' => [['_' => 'documentAttributeFilename', 'file_name' => $name]]];
										
										$p = ['peer' => $peer, 'media' => $inputMedia];
										$res = $MadelineProto->messages->sendMedia($p);
										unlink($localFile);
										
										//$txt = "✅ <b>Sent!</b> @WeCanCo 😎";
										//$ed = $MadelineProto->messages->editMessage(['peer' => $peer, 'id' => $mid, 'message' => $txt, 'parse_mode' => 'html' ]);
										//$res = $MadelineProto->channels->deleteMessages(['channel' => $peer, 'id' => [$mid] ]);
									}
									//
								}else if(strpos($message,"/web2pdf ") !== false){
									$web = trim(str_replace("/web2pdf ","",$message));
									$web = explode("|",$web."|");
									$name = trim($web[1]);
									$web= trim($web[0]);
									if($web !=""){
										$link = "http://wecangroup.ir/other/web2pdf/WeCan/?link=".($web);
										//$txt = "⏳ <b>Converting...</b> ";
										//$m = $MadelineProto->messages->sendMessage(['peer' => $peer, 'reply_to_msg_id' => $mid , 'message' => $txt, 'parse_mode' => 'HTML' ]);
										//if(isset($m['updates'][0]['id'])){
										//	$mid = $m['updates'][0]['id'];
										//}else{
										//	$mid = $m['id'];
										//}
										$web = explode("/",$web);
										if($name==""){
											$name=str_replace(array("http:","https:","/",":"),"",$web[2]).".pdf";
										}
										$localFile = 'temp/'.$name;
										//$file = file_get_contents($link);
										//file_put_contents($localFile,$file);
										curl_dl($link,$localFile);
										//$txt = "⏳ <b>Uploading...</b> ".$name."";
										//$ed = $MadelineProto->messages->editMessage(['peer' => $peer, 'id' => $mid, 'message' => $txt, 'parse_mode' => 'html' ]);
										
										$caption = '📌 '.$name.' | @WeCanGP';
										
										$inputFile = $MadelineProto->upload($localFile);
										//$txt = "⏳ Sending...: <b>".$name."</b>";
										//$ed = $MadelineProto->messages->editMessage(['peer' => $peer, 'id' => $mid, 'message' => $txt, 'parse_mode' => 'html' ]);
										$inputMedia = ['_' => 'inputMediaUploadedDocument', 'file' => $inputFile, 'mime_type' => mime_content_type($localFile), 'caption' => $caption, 'attributes' => [['_' => 'documentAttributeFilename', 'file_name' => $name]]];
										
										$p = ['peer' => $peer, 'media' => $inputMedia];
										$res = $MadelineProto->messages->sendMedia($p);
										unlink($localFile);
										
										//$txt = "✅ <b>Sent!</b> @WeCanCo 😎";
										//$ed = $MadelineProto->messages->editMessage(['peer' => $peer, 'id' => $mid, 'message' => $txt, 'parse_mode' => 'html' ]);
										//$res = $MadelineProto->channels->deleteMessages(['channel' => $peer, 'id' => [$mid] ]);
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
								
										
									}else if($channel_id=="" && 1==2){
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
		//\danog\MadelineProto\Serialization::serialize($sessionFile, $MadelineProto);
		file_put_contents('SentMSGs',implode("\n",$SentMSGs));
		//$MadelineProto = \danog\MadelineProto\Serialization::deserialize($sessionFile);
		//sleep(1);
		
	}
	
