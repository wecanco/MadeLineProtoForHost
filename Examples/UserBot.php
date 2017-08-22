<?php
	// ربات پاسخگو
	
	require_once('UserLogin.php'); // خواندن سشن
	$SentMSGs=explode("\n",file_get_contents('SentMSGs'));
	while(true){
		if(file_exists('_stop_bot')){
			echo "ربات متوقف شد.<br>";
			exit();
		}
		$updates = $MadelineProto->get_updates();
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
							case "/start":
								$text='سلام من ربات میدلاین هستم! منو @WeCanCo ساخته! 🙃';
							break;
							
							case "/wecan":
								$text='به افتحارش!!! 👏👏👏';
							break;
						}
						
						if($text !=""){
							$m = $MadelineProto->messages->sendMessage(['peer' => $peer, 'reply_to_msg_id' => $mid , 'message' => $text, 'parse_mode' => 'HTML' ]);
							$SentMSGs[]=$uniq;
							$sent=1;
						}
						
					}
				}
			}
			
			if($sent==1){
				echo "پیام ارسال شد!<br>";
			}else{
				echo ".";
			}
			
		}
		//print_r($up);
		\danog\MadelineProto\Serialization::serialize($sessionFile, $MadelineProto);
		file_put_contents('SentMSGs',implode("\n",$SentMSGs));
		
		sleep(1);
		
	}

