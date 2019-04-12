<?php
	//?phone=+989357973301
	require_once('UserLogin.php'); // خواندن سشن
	require_once($currentDIR.'/inc/GTranslator.php'); // کلاس ترجمه گوگل
	
	try{
		//mkdir('temp');
	} catch (Exception $e) { 
		//$text = "❌ ".$e->getMessage(); 
	}
	
	
	$sessionF="";
	$ACsListF = ".CurrentAC";
	$GPListF = ".GroupsList";
	$FaqF = ".faqs";
	$RemindsF = ".reminds";
	$CheckOnlineSitesFile = ".checkOnlineSites";
	$InlineMode = false;
	$Serialize = false;
	$SentMSGs=[];
	$Splitor = "|";
	
	
	if(!file_exists($ACsListF)){
		file_put_contents($ACsListF,"[]");
	}
	
	
	if(!file_exists($FaqF)){
		file_put_contents($FaqF,"[]");
	}
	
	if(!file_exists($GPListF)){
		file_put_contents($GPListF,"");
	}
	if(!file_exists($RemindsF)){
		file_put_contents($RemindsF,"[]");
	}
	if(!file_exists($CheckOnlineSitesFile)){
		file_put_contents($CheckOnlineSitesFile,"[]");
	}
	
	if(sizeof($phones) > 0){
		if(file_exists($ACsListF)){
			$phoneF = file_get_contents($ACsListF);
			$phonesF = json_decode($phoneF,true);
			foreach($phonesF as $phone){
				$phone = trim($phone['number']);
				if($phone != "" && trim($phones[0]['number']) != $phone){
					$j = sizeof($phones);
					$phones[$j]['number']= $phone;
					$phones[$j]['active']= false;
					$phones[$j]['current']= false;
				}
			}
		}
	}else{
	
	}
	
	echo "$BreakLine get bots... $BreakLine";
	$MadelineProtoBot = [];
	foreach($Bots as $bkey => $bval){
		if($Bots[$bkey]['active']){
			try{
				$MadelineProtoBot[$bkey] = new \danog\MadelineProto\API($settings);
				$Bres = $MadelineProtoBot[$bkey]->bot_login(trim($Bots[$bkey]['token']));
			}catch(Exception $e){
			
			}
		}
	}
	
	
	while(true){
		foreach($phones as $pk => $phone){
			if(!$phone['active']){
				 continue;
			}
			
			$offset= 0;
			if(isset($phone['last_update_id'])){
				$offset = ($phone['last_update_id']);
			}else{
				$phone['last_update_id'] = $offset;
			}

			//$tracee = "$BreakLine اکانات: ".$phone['number']."$BreakLine وضعیت: ".$phone['active']."$BreakLine offset:".$offset." $BreakLine ".$phone['last_update_id']." $BreakLine ---------- $BreakLine";

			//echo $tracee;

			$ClearedPhone = str_replace(array("+","-","(",")"),"",$phone['number']);
			$stopBotFile = "_stop_bot_".$ClearedPhone;
			if(file_exists('_stop_bots')){
				echo "ربات متوقف شد. $BreakLine";
				exit();
			}else if(file_exists($stopBotFile)){
				$phone['active'] = false;
				$phone['current'] = false;
			}
			
			if(file_exists(".reset")){
				$tmp_to = file_get_contents(".reset");
				unlink(".reset");
				$txt="✅ ربات مجدد راه اندازی شد.";
				$MadelineProto[$phone['number']]->messages->sendMessage(['peer' => $tmp_to, 'message' => $txt, 'parse_mode' => 'HTML' ]);
			}
			
			$SentMSGsF = '.SentMSGs_'.$ClearedPhone;
			if(!file_exists($SentMSGsF)){
				file_put_contents($SentMSGsF,"0");
			}

			$SentMSGs[$phone['number']]=explode("\n",file_get_contents($SentMSGsF));
	
			$sessionFile = $sessionsDir."/.session_".$ClearedPhone.""; // مسیر سشن
			echo "$BreakLine set settings... $BreakLine";
			$MadelineProto[$phone['number']]->settings['updates']['handle_updates'] = true;
			echo "$BreakLine get updates...$BreakLine";
			$updates = $MadelineProto[$phone['number']]->get_updates(['offset' => $offset, 'limit' => 50, 'timeout' => 0]);

			if(sizeof($updates) > 0){
				foreach($updates as $key => $val){
					$update = $updates[$key];
					$phone['last_update_id'] = ($update['update_id']) + 1;
					$phones[$pk]['last_update_id'] = $update['update_id'] + 1;
					$UpType = $update['update']['_'];
					if(($UpType != 'updateNewMessage' && 
					$UpType != 'updateNewChannelMessage')){
						unset($updates[$key]);
					}
				}
			}
			
			
			$Reminds = json_decode(file_get_contents($RemindsF),true);
			if(sizeof($Reminds) > 0){
				foreach($Reminds as $key => $remind){
					if(isset($remind['time']) && $remind['status']=='active'){
						if(time() >= $remind['time']){
							$Reminds[$key]['status']='done';
							$remindeText = $remind['note'];
							$remindeTo = trim($remind['to']);
							unset($Reminds[$key]);
							try{
								$MadelineProto[$phone['number']]->messages->sendMessage(['peer' => $remindeTo, 'message' => $remindeText, 'parse_mode' => 'HTML' ]);
							}catch(Exception $e){}
						}
					}
				}
			}
			
			
			$CheckOnlineSites = json_decode(file_get_contents($CheckOnlineSitesFile),true);
			if(sizeof($CheckOnlineSites) > 0){
				foreach($CheckOnlineSites as $key => $site){
					
					if(checkOnline($site['url'])){ 
						
					}else{
					
						if( !isset($site['lastcheck']) ||
							( (intval($site['lastcheck'] + (60 * 3))) < time() ) 
							){
							try{
								$checkonlineText = "🚨 وبسایت ".$site['url']." دان شد! ";
								$CheckOnlineSites[$key]['lastcheck'] = time();
								file_put_contents($CheckOnlineSitesFile,json_encode($CheckOnlineSites));
								
								$MadelineProto[$phone['number']]->messages->sendMessage(['peer' => $site['chat_id'], 'message' => $checkonlineText, 'parse_mode' => 'HTML' ]);
							}catch(Exception $e){
							}
						}
						
					}
					
				}
			}
			
			
			
			if(sizeof($updates) > 0){
			foreach($updates as $update){
				$ExistCase = false;
				$phone['last_update_id'] = $update['update_id'] + 1;
				$phones[$pk]['last_update_id'] = $update['update_id'] + 1;
				echo $phone['last_update_id'].", ";
				
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
				$uniq = $update['update_id']."_".$from_id."_".$mid."_".$date;
				
				//seen
				if(intval($peer) < 0){
					$MadelineProto[$phone['number']]->channels->readHistory(['channel' => $peer, 'max_id' => $mid ]);
					$MadelineProto[$phone['number']]->channels->readMessageContents(['channel' => $peer, 'id' => [$mid] ]);
				}else{
					$MadelineProto[$phone['number']]->messages->readHistory(['peer' => $peer , 'max_id' => $mid ]);
				}
				if(($update['update']['_'] == 'updateNewMessage' || $update['update']['_'] == 'updateNewChannelMessage') ){
				try {
					if($out != 1){
						if($message !=""){
							if(!isset($phone['current']) || !$phone['current']){
								continue;
							}	
							
							if((!in_array($uniq,$SentMSGs[$phone['number']])) && ($peer !='') ){
								$SentMSGs[$phone['number']][]=$uniq;
								file_put_contents($SentMSGsF,implode("\n",$SentMSGs[$phone['number']]));
								
								$message_array = explode(" ",$message);
								$Commond = $message_array[0];
								unset($message_array[0]);
								if(sizeof($message_array) <= 0){
									$message_array = [];
								}
								$messageTXT = trim(implode(" ",$message_array));
								
								if(file_exists('UserBot.php')){
									include('UserBot.php');
								}
								
								if(!$ExistCase){
									// امکانات پیشرفته
									if(file_exists('UserBotPro.php')){
										include('UserBotPro.php');
									}
								}
								
								if(!$ExistCase){
									switch($Commond){
										default:
										$dmid = str_replace("/delmsg","",$Commond);
										if(is_numeric($dmid)){
											$ExistCase = true;
											if(intval($peer) < 0){
												$res = $MadelineProto[$phone['number']]->channels->deleteMessages(['channel' => $peer, 'id' => [$dmid] ]);
											}else{
												$res = $MadelineProto[$phone['number']]->messages->deleteMessages(['id' => [$dmid] ]);
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
											}
								
										break;
									}
								}
								
								
								
								
							}
						}
					}
					
					
				}catch(Exception $e) { 
					$text = "❌ ".$e->getMessage();  
					$err = $e->getMessage();
					$err = substr($err,0,70);
					if($err == 'The authorization key has expired'){
						if(file_exists($sessionF)){
							unlink($sessionF);
						}
					}
					$MadelineProto[$phone['number']]->account->updateProfile(['about' => $err ]);
				}
				
				try{
					if($text !="" && $peer !=""){
						if(sizeof($MadelineProtoBot) > 0 && $InlineMode){
							$FirstBotKey = array_keys($MadelineProtoBot)[0];
							$TLFile = "temp/".time().".tl";
							file_put_contents($TLFile,$text);
							$query = [];
							$query['text'] = $TLFile;
							$query['keyboard'] = [
								"inline_keyboard" => [
										[
											[
												"text" => '❌ حذف',
												"callback_data" => '/del'
											]
										],
										[
											[
												"text" => '🇬🇧',
												"callback_data" => '/tl#@@#en#@@#'.$TLFile
											]
										]
										
										
									]

							];
							
							
							$BotResults = $MadelineProto[$phone['number']]->messages->getInlineBotResults(['bot' => "@".$FirstBotKey, 'peer' => $peer, 'query' => (json_encode($query)), 'offset' => '0' ]);
							
							$query_id = $BotResults['query_id'];
							$query_res_id = $BotResults['results'][0]['id'];
							
							$up = $MadelineProto[$phone['number']]->messages->sendInlineBotResult(['silent' => true, 'background' => false, 'clear_draft' => true, 'peer' => $peer, 'query_id' => $query_id, 'id' => ''.$query_res_id ]);
							
							
						}else{
							$m = $MadelineProto[$phone['number']]->messages->sendMessage(['peer' => $peer, 'reply_to_msg_id' => $mid , 'message' => $text, 'parse_mode' => 'HTML' ]);	
							$gid="";
							if(isset($m['updates'][0]['id'])){
								$mid = $m['updates'][0]['id'];
								$gid = $peer;
							}else{
								$mid = $m['id'];
							}
							
							$delmsgID = "🗑 /delmsg".$mid;
							$text .= "\n\n".$delmsgID."";
							if(intval($peer) <0){
								$ed = $MadelineProto[$phone['number']]->messages->editMessage(['peer' => $peer, 'id' => $mid, 'message' => $text, 'parse_mode' => 'html' ]);
							}
						}
						
						
						
						$sent=1;
						//$MadelineProto[$phone['number']]->account->updateProfile(['about' => 'آخرین عملیات: '.date("Y-m-d H:i:s", time()) ]);
					}
					} catch (Exception $e) { 
					//var_dump($e);
					$err = $e->getMessage();
					file_put_contents('e_'.time().'.txt',json_encode($e));
					$err = substr($err,0,70);
					if(isset($MadelineProto[$phone['number']])){
						$MadelineProto[$phone['number']]->account->updateProfile(['about' => $err ]);
					}
				}
				if($sent==1){
					echo "پیام ارسال شد! $BreakLine";
				}else{
					echo ".";
				}
				
			}
			
			
			
			
		}
		
		}
		
		if($Serialize){
			\danog\MadelineProto\Serialization::serialize($sessionFile, $MadelineProto[$phone['number']]);
		}
		file_put_contents($RemindsF,json_encode($Reminds));
		file_put_contents($ACsListF,json_encode($phones));
	}
	
}

