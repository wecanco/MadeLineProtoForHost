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
	
	while(true){
		if(file_exists('_stop_bot')){
			echo "ربات متوقف شد.<br>";
			exit();
		}
		$updates = $MadelineProto->get_updates(['offset' => -1]);
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
				if(isset($update['update']['message']['media']['document'])){
					$media = $update['update']['message']['media']['document'];
					$thumb = $media['thumb'];
					$message='!!!!!!thumb';
				}
				
				
				
				$sent=0;
				if($out != 1){
					if($message !=""){
						$mid = $update['update']['message']['id'];
						
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
							
							if($media !=""){
								$name = 'thumb_'.time().'.jpg';
								$file = '../temp/'.$name;
								$res = $MadelineProto->download_to_file($thumb, $file);
								$text =  "http://tlbots.cf/temp/".$name;
								$m = $MadelineProto->messages->sendMessage(['peer' => $peer, 'message' => $text, 'parse_mode' => 'HTML' ]);	
								break;
								
							}
				
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
									
									
								}else if(strpos($message,"/link2file ") !== false){
									
									// ;)
									
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
										
										$caption = 'پیام های کانال '.$parms['channel'].' ('.implode(",",$parname).') |  گروه وی کن @WeCanGP';
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
										$text='سلام من ربات میدلاین هستم! منو @WeCanCo ساخته! 🙃
										دستورات من:
										/start2  -> شروع
										/wecan  -> سازنده
										/mymention  -> منشن شما
										/madeline help -> تست متدهای میدلاین
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
				}
			
			if($text !="" && $peer !=""){
				$SentMSGs[]=$uniq;
				$m = $MadelineProto->messages->sendMessage(['peer' => $peer, 'reply_to_msg_id' => $mid , 'message' => $text, 'parse_mode' => 'HTML' ]);							
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
	
