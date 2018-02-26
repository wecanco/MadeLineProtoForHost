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