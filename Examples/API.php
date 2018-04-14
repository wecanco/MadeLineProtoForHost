<?php
	/* 
		sample:
		/Examples/API.php?phone=+989357973301&method=messages.getPeerDialogs&parms={"peers":["@WeCanGP"]}
	*/
	
		
	// برای یافت خطاها
	
	ini_set('display_errors', 0);
	ini_set('display_startup_errors', 0);
	error_reporting(E_NOTICE);
	header('Content-Type: application/json');
	if(isset($_REQUEST['method']) && isset($_REQUEST['parms']) && isset($_REQUEST['phone'])){
		$ShowLog=false;
		require_once('UserLogin.php'); 
		ini_set('display_errors', 0);
		ini_set('display_startup_errors', 0);
		error_reporting(E_NOTICE);
		$method = $_REQUEST['method'];
		$key = $phones[0]['number'];
		//$MadelineProto[$key]->settings['updates']['handle_updates'] = false;
		//$MadelineProto[$key]->settings['updates']['fullfetch'] = false;
		
		$curdc = $MadelineProto[$key]->API->datacenter->curdc;
		//$curdc = 4;
		$parms = json_decode($_REQUEST['parms'],true);
		try{
			switch(strtolower($method)){
				case "get_updates":
					$MadelineProto[$key]->settings['updates']['handle_updates'] = true;
					$res = $MadelineProto[$key]->API->get_updates($parms);
				break;
				
				case "get_full_info":
					$res = $MadelineProto[$key]->get_full_info($parms['id']);
				break;
				
				case "get_info":
					$res = $MadelineProto[$key]->get_info($parms['id']);
				break;
				
				
				
				case "get_pwr_chat":
					$res = $MadelineProto[$key]->get_pwr_chat($parms['id'],$parms['fullfetch']);
				break;
				
				case "getchannelmembers":
					$res = $MadelineProto[$key]->get_pwr_chat('-1001049295266',true);
				break;
				
				
				
				case "get_dialogs":
					$bool = false;
					if(isset($parms[0]) && $parms[0]){
						$bool = true;
					}
					$res = $MadelineProto[$key]->get_dialogs($bool);
				break;
				
				case "getadminlog":
					$res = $MadelineProto[$key]->channels->getAdminLog($parms);
				break;
				
				case "renamefile":
					if(isset($parms['bot']) && isset($parms['file'])){
						$botToken = $parms ['bot'];
						$file = $parms['file'];
						$fileTypes = ['document','video','audio','photo','voice'];
						$User = $MadelineProto[$key]->get_self();
						$Bot = file_get_contents('https://api.telegram.org/bot'.trim($botToken).'/getme');
						$Bot = json_decode($Bot,true);
						if(!$Bot['ok']){
							echo '{"error":"bot token not valid."}';
							exit();
						}
						$Bot = ['result'];
						$UserID = $User['id'];
						foreach($fileTypes as $fileType){
							$wfile = file_get_contents('https://api.telegram.org/bot'.trim($botToken).'/send'.$fileType.'?chat_id='.$UserID.'&'.$fileType.'='.$file);
							$wfile = json_decode($wfile,true);
							if($wfile['ok']){
								break;
							}
						}
						$mPD = $MadelineProto[$key]->messages->getPeerDialogs(['peers' => [$Bot['id']]]);
						$MMID = $mPD['dialogs'][0]['top_message'];
						$RepMessage = $MadelineProto[$key]->messages->getMessages(['id' => [$MMID] ]);
						$media = trim($RepMessage['messages'][0]['media']);
						$tmpdir = "temp/";
						$preName = $tmpdir."".time()."_";
						if(isset($parms['name']) && trim($parms['name']) != ""){
							$dlres = $MadelineProto[$key]->download_to_file($media, $preName.basename($parms['name']));
						}else{
							$dlres = $MadelineProto[$key]->download_to_dir($media, $tmpdir);
						}
						
						
						
						$res = json_encode($dlres);
						file_put_contents('res',$res);
						
					}else{
						echo '{"error":"need: bot (bot token), file (url or file_id)"}';
						exit();
					}
				break;
				
				
				default:
					$res = $MadelineProto[$key]->method_call($method, $parms, ['datacenter' => $curdc]);
				break;
			}
			\danog\MadelineProto\Serialization::serialize($sessionFile, $MadelineProto[$key]);
		} catch (Exception $e) { 
			$res = ['error' => $e->getMessage()];
		}
		echo json_encode($res,JSON_PRETTY_PRINT);
	}else{
		echo '{"error":"need: method (string), parms (json encoded), phone (string)"}';
	}