<?php
	// ربات پاسخگو
	//?phone=+989357973301
	require_once('UserLogin.php'); // خواندن سشن
	$SentMSGs=explode("\n",file_get_contents('SentMSGs'));
	while(true){
		if(file_exists('_stop_bot')){
			echo "ربات متوقف شد.<br>";
			exit();
		}
		$updates = $MadelineProto->get_updates(['offset' => -1]);
		foreach($updates as $update){			
			$out=0;
			if(isset($update['update']['message']['out'])){
				$out = $update['update']['message']['out'];
			}
			$message='';
			if(isset($update['update']['message']['message'])){
				$message = $update['update']['message']['message'];
			}
			$sent=0;
			if($out != 1){
				if($message !=""){
					$mid = $update['update']['message']['id'];
					$peer='';
					if(isset($update['update']['message']['from_id'])){
						$from_id = $update['update']['message']['from_id'];
						$peer = $from_id;
					}
					$channel_id = "";
					if(isset($update['update']['message']['to_id']['channel_id'])){
						$channel_id = $update['update']['message']['to_id']['channel_id'];
						$peer = "-100".$channel_id;
					}
					
					$date = $update['update']['message']['date'];
					$uniq = $from_id."_".$mid."_".$date;
					$text='';
					if(!in_array($uniq,$SentMSGs) && $peer !=''){
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
										
										default:
											$text= '💥 با استفاده از این دستور شما میتوانید متدهای میدلاین را تست کنید!

🖥 ساختار ارسال دستور:
/madeline پارمترهابصورت جی سون % نام متد
📌 مانند:
/madeline messages.getPeerDialogs % {"peers": ["@wecanco"] }
';
										break;
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
						
						if($text !=""){
							$SentMSGs[]=$uniq;
							$m = $MadelineProto->messages->sendMessage(['peer' => $peer, 'reply_to_msg_id' => $mid , 'message' => $text, 'parse_mode' => 'HTML' ]);							
							$sent=1;
						}
						
					}
				}
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
		sleep(1);
		
	}

