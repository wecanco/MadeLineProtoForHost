#!/usr/bin/env php
<?php
	//print_r($_SERVER);
	require_once 'config.php'; // فایل کانفیگ
	require_once $libPath.'vendor/autoload.php'; // فراخوانی لودر کتابخانه میدلاین
	//require_once $libPath.'src/danog/MadelineProto/VoIP/php-libtgvoip.php';
	
	if(file_exists('inc/WeCanFunctions.php')){
		require_once('inc/WeCanFunctions.php'); // توابع کاربردی
	}
	if(file_exists('inc/SocksProxy.php')){
		require_once('inc/SocksProxy.php'); // پروکسی
	}
	
	if(!file_exists($sessionsDir)){
		mkdir($sessionsDir);
	}
	
	$BreakLine = "<br>";
	if(isset($_SERVER['SESSIONNAME']) && strpos(strtolower($_SERVER['SESSIONNAME']), 'console') !== false ){
		$RunInTerminal = true;
	}
	
	if($RunInTerminal){
		if(isset($argv[1])){
			if(trim($argv[1]) !=""){
				$_GET['phone'] = $argv[1];
			}
			if(isset($argv[2])){
				if(trim($argv[2]) !=""){
					$_GET['code'] = $argv[2];
				}
			}
			if(isset($argv[3])){
				if(trim($argv[3]) !=""){
					$_GET['pass'] = $argv[3];
				}
			}
		}else{
			$_GET['phone'] = readline('Shomare Hamrahe Khod Ra Vared Namaed: ');
		}
		$BreakLine = "";
	}else{
		echo '
		<html dir="rtl">
			<style>
				input[type=text]{
					width: 250px;
				}
			</style>
			<body style="direction: rtl;font-family:tahoma;font-size: 12px;">
		';
	}
	
	if(isset($_GET['phone'])){
		$phone = $_GET['phone'];
	}else{
		echo '
			<form action="" method="">
				<input type="text" name="phone" style="direction:ltr;"  placeholder="شماره همراه خود را وارد نمایید..." />
				<input type="submit" value="اتصال">
			</form>
		
		';
		exit();
	}
	
	//$MySettings = $settings_proxy;

	$phone = str_replace(array(" ","(",")"),"",$phone); // شماره موبایلی که با آن لاگین میشوید
	$sessionFile = $sessionsDir."/session_".str_replace(array("+","-","(",")"),"",$phone).""; // مسیر سشن

	if (file_exists($libPath.'web_data.php')) {
		require_once $libPath.'web_data.php';
	}

	$MadelineProto = false;
	echo "درحال آماده سازی...". PHP_EOL .$BreakLine;

	if(file_exists($sessionFile)){
		try {
			echo 'درحال خواندن سشن: ('.$sessionFile.')...'. PHP_EOL .$BreakLine;
			
			//RemoveProxies($sessionFile);
			
			$MadelineProto = \danog\MadelineProto\Serialization::deserialize($sessionFile);
			echo 'سشن خوانده شد.'. PHP_EOL .$BreakLine;
			if(!$RunInTerminal){
				echo '<a href="./UserBot.php">توقف اکانت</a>'. PHP_EOL .$BreakLine;
			}
			// set proxy
			//$MadelineProto->settings['logger']['logger'] = $MySettings['logger']['logger'];
			//$MadelineProto->settings['connection_settings'] = $MySettings['connection_settings'];
			//$MadelineProto->settings['app_info'] = $MySettings['app_info'];
			
			// remove proxy
			//unset($MadelineProto->settings['connection_settings']['all']['proxy']);
			//unset($MadelineProto->settings['connection_settings']['all']['proxy_extra']);
			//$MadelineProto->updates->API->chats = null;
			//$MadelineProto->updates->API->full_chats = null;
			//$MadelineProto->updates->API->updates = null;
			//$MadelineProto->updates->API->constructors = null;
			//$MadelineProto->updates->API->methods = null;
			//for($i=0; $i<sizeof($MadelineProto->updates->API->datacenter->sockets); $i++){
				//$MadelineProto->updates->API->datacenter->sockets[$i]->extra = [];
				//$MadelineProto->updates->API->datacenter->sockets[$i]->proxy = '\Socket';
			//}
			//foreach ($MadelineProto->updates->API->datacenter->sockets as $key => $socket) {
				//print_r($MadelineProto->updates->API->datacenter->sockets[$key]);
				//exit();
			//}
			//$MadelineProto->updates->API->phone->API = null;
			//$MadelineProto->updates->API->stickers->API = null;
			
			
		} catch (\danog\MadelineProto\Exception $e) {
			echo 'خطا: '. PHP_EOL .$BreakLine;
			var_dump($e->getMessage());
			exit();
		}
	}

	if ($MadelineProto === false) {
		sleep(0.5);
		echo 'درحال اتصال به سرور تلگرام...'.PHP_EOL;
		$MadelineProto = new \danog\MadelineProto\API($settings);
		echo 'به سرور تلگرام متصل شد.'. PHP_EOL .$BreakLine;

		echo 'درحال چک کردن شماره موبایل...'. PHP_EOL .$BreakLine;
		$checkedPhone = $MadelineProto->auth->checkPhone(['phone_number' => $phone,]);
		echo 'موبایل چک شد.'. PHP_EOL .$BreakLine;

		echo 'درحال ارسال کد جهت ورود به اکانت...'. PHP_EOL .$BreakLine;
		$sentCode = $MadelineProto->phone_login($phone);
		$phone_code_hash = $sentCode['phone_code_hash'];
		\danog\MadelineProto\Serialization::serialize($sessionFile, $MadelineProto);
		if($phone_code_hash !==""){
			if($RunInTerminal){
				$_GET['code'] = readline('Code Taeed Ra Vared Namaed: ');
			}else{
				echo 'کد به تلگرام شما ارسال شد.'. PHP_EOL .$BreakLine;
				echo '
				<form action="" method="">
					<input type="hidden" name="phone" value="'.$_GET['phone'].'" />
					<input type="text" name="code" style="direction:ltr;"  placeholder="کد تایید را وارد نمایید..." />
					<input type="submit" value="تایید کد">
				</form>
				';
				exit();
			}
		}else{
			echo 'خطا در ارسال کد.'. PHP_EOL .$BreakLine;
			exit();
		}
	}
	
	if($MadelineProto != false && isset($_GET['code'])){
		$code = $_GET['code'];
		echo 'درحال تایید کد...'. PHP_EOL .$BreakLine;
		$authorization = $MadelineProto->complete_phone_login($code);

		if ($authorization['_'] === 'account.noPassword') {
			echo 'ورود دو مرحله ای شما فعال است و پسورد خود را وارد نکردید!'. PHP_EOL .$BreakLine;
			exit();
		}
		if ($authorization['_'] === 'account.password') {
			$help = $authorization['hint']; // راهنمای پسورد
			if($RunInTerminal){
				$_GET['pass'] = readline('Password: ('.$help.')');
			}else{
				if(!isset($_GET['pass'])){
					echo "راهنمای پسورد اکانت: ".$help. PHP_EOL .$BreakLine;
					echo '
					<form action="" method="">
						<input type="hidden" name="phone" value="'.$_GET['phone'].'" />
						<input type="hidden" name="code" value="'.$_GET['code'].'" />
						<input type="password" name="pass" placeholder="رمز اکانت خود را وارد نمایید..." />
						<input type="submit" value="تایید رمز">
					</form>
					';
					exit();
				}
			}
			// ورود دو مرحله ای
			$authorization = $MadelineProto->complete_2fa_login($_GET['pass']);
		}
		if ($authorization['_'] === 'account.needSignup') {
			// اگر برای اولین بار است که اکانت تلگرام روی این شماره فعال می شود، نام و نام خانوادگی را دریافت کن
			if($RunInTerminal){
				$_GET['first_name'] = readline('Name Shoma: ');
				$_GET['last_name'] = readline('Name Khanevadegi Shoma: ');
			}else{
				if(!isset($_GET['first_name'])){
					echo "درحال ثبت نام اکانت...". PHP_EOL .$BreakLine;
					echo '
					<form action="" method="">
						<input type="hidden" name="phone" value="'.$_GET['phone'].'" />
						<input type="hidden" name="code" value="'.$_GET['code'].'" />
						<input type="hidden" name="pass" value="'.$_GET['pass'].'" />
						<input type="text" name="first_name" placeholder="نام کوچک شما..." />
						<input type="text" name="last_name" placeholder="نام خانوادگی شما..." />
						<input type="submit" value="ثبت نام">
					</form>
					';
					exit();
				}
			}
			$authorization = $MadelineProto->complete_signup($_GET['first_name'],$_GET['last_name']);
		}
		echo 'کد تایید شد.'. PHP_EOL .$BreakLine;
		$updates = $MadelineProto->get_updates();
		$MadelineProto->API->get_updates_difference();
		$MadelineProto->API->store_db([], true);
		$MadelineProto->API->reset_session();
		\danog\MadelineProto\Serialization::serialize($sessionFile, $MadelineProto);
		echo 'حالا میتوانید با از سشن زیر جهت استفاده از اکانت خود استفاده نمایید:'. PHP_EOL .$BreakLine;
		echo $sessionFile. PHP_EOL .$BreakLine;
	}
