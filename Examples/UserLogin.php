<?php
	require_once 'config.php'; // فایل کانفیگ
	$phpversion = explode('.', phpversion());
	if(intval($phpversion[0]) >= 7){
		require_once ($libPath.'vendor/autoload.php'); // فراخوانی لودر کتابخانه میدلاین
	}else{
		if (!file_exists($libPath.'madeline.php')) {
			copy('https://phar.madelineproto.xyz/madeline.php', $libPath.'madeline.php');
		}
		include $libPath.'madeline.php';
	}
	
	//require_once $libPath.'src/danog/MadelineProto/VoIP/php-libtgvoip.php';
	
	if(file_exists($currentDIR.'/inc/WeCanFunctions.php')){
		require_once($currentDIR.'/inc/WeCanFunctions.php'); // توابع کاربردی
	}
	/*
	if(file_exists('inc/SocksProxy.php')){
		require_once('inc/SocksProxy.php'); // پروکسی
	}
	*/
	
	if(!file_exists($sessionsDir)){
		mkdir($sessionsDir);
	}
	
	file_put_contents('LastRun',date("Y-m-d H:i:s", time()));
	
	try{
		exec("ps aux", $psRes);
		$psResS = implode("\n",$psRes);
	} catch (Exception $e) { 
		$psRes = ['error' => $e->getMessage()];
	}
	


	$BreakLine = "<br>";
	if( (isset($_SERVER['SESSIONNAME']) && strpos(strtolower($_SERVER['SESSIONNAME']), 'console') !== false) || 
		isset($_SERVER['SHELL']) || 
		(isset($_SERVER['_']) &&  strpos(strtolower($_SERVER['_']), 'php') !== false ) || 
		(isset($_SERVER['ComSpec']) && strpos(strtolower($_SERVER['ComSpec']), 'cmd.exe') !== false ) ){
		$RunInTerminal = true;
	}
	
	if(!isset($argv) && isset($_SERVER['argv'])){
		$argv = $_SERVER['argv'];
	}
	
	if($RunInTerminal){
		$tmp_RP = "";
		
		if(isset($_REQUEST) && sizeof($_REQUEST) >= 2){
			$tmp_RP = array_slice($_REQUEST,1,1,true);
			//$tmp_RP = end($tmp_RP);
			//print_r($tmp_RP);
			$tmp_RP = key($tmp_RP);
		}
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
		}else if(is_numeric($tmp_RP) && strlen($tmp_RP."") > 7 ){
			$_GET['phone'] = "+".$tmp_RP;
		}else{
			$_GET['phone'] = readline('Shomare Hamrahe Khod Ra Vared Namaed: (Phone Number) ');
		}
		$BreakLine = "\n";
	}else{
		if( (isset($ShowLog) && $ShowLog) || !isset($ShowLog)){
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
	}

	global $phones;
	$phones=array();
	if(isset($_GET['phone'])){
		$phones[0]['number'] = $_GET['phone'];
		$phones[0]['active'] = true;
		$phones[0]['current'] = true;
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

	$phones[0]['number'] = str_replace(array(" ","(",")"),"",$phones[0]['number']); // شماره موبایلی که با آن لاگین میشوید
	$wclearedPhone = str_replace(array("+","-","(",")"),"",$phones[0]['number']);
	$sessionFile = $sessionsDir."/.session_".$wclearedPhone.""; // مسیر سشن
	
	// dont run multi process from same number
	$UserBotF = 'Start.php '.$phones[0]['number'];
	$UserBotD = rtrim(getcwd(),'/');
	$ProcessCount=0;
	foreach($psRes as $processLine){
		if( (strpos($processLine, $UserBotF) !== false) && 
			//(strpos($processLine, $UserBotD) !== false) &&
			(strpos($processLine, $wclearedPhone) !== false)
				){
			$ProcessCount++;
		}
	}
	
	if($ProcessCount > 2){
		if(file_exists('.ForceRun')){
			unlink('.ForceRun');
		}else{
			echo " [Stoped] ===> Bot is Running: ".$BreakLine;
			print_r($psRes);
			exit();
		}
	}
	
	$MadelineProto[$phones[0]['number']] = false;
	if( (isset($ShowLog) && $ShowLog) || !isset($ShowLog)){
		echo "loading...". PHP_EOL .$BreakLine;
	}

	if(file_exists($sessionFile)){
		try {
			if( (isset($ShowLog) && $ShowLog) || !isset($ShowLog)){
				echo 'reading session file... ['.$sessionFile.']'. PHP_EOL .$BreakLine;
			}
			//$MadelineProto[$phones[0]['number']] = \danog\MadelineProto\Serialization::deserialize($sessionFile,true);
			RemoveUpdates($sessionFile);
			$MadelineProto[$phones[0]['number']] = new \danog\MadelineProto\API($sessionFile, $settings);
			if( (isset($ShowLog) && $ShowLog) || !isset($ShowLog)){
				echo 'session file readed.'. PHP_EOL .$BreakLine;
			}

			if(!$RunInTerminal){
				if( (isset($ShowLog) && $ShowLog) || !isset($ShowLog)){
					echo '<a href="./Start.php">STOP BOT</a>'. PHP_EOL .$BreakLine;
				}
			}

		} catch (\danog\MadelineProto\Exception $e) {
			echo 'Error: '. PHP_EOL .$BreakLine;
			var_dump($e->getMessage());
			exit();
		}
	}

	if ($MadelineProto[$phones[0]['number']] === false) {
		sleep(0.5);
		echo 'Connecting to Telegram Server...'.PHP_EOL;
		$MadelineProto[$phones[0]['number']] = new \danog\MadelineProto\API($settings);
		echo 'Connected to Telegram.'. PHP_EOL .$BreakLine;

		echo 'Checking Phone Number...'. PHP_EOL .$BreakLine;
		$checkedPhone = $MadelineProto[$phones[0]['number']]->auth->checkPhone(['phone_number' => $phones[0]['number'],]);
		echo 'Phone Number Checked.'. PHP_EOL .$BreakLine;

		echo 'Sending Code...'. PHP_EOL .$BreakLine;
		$sentCode = $MadelineProto[$phones[0]['number']]->phone_login($phones[0]['number']);
		$phones_code_hash = $sentCode['phone_code_hash'];
		\danog\MadelineProto\Serialization::serialize($sessionFile, $MadelineProto[$phones[0]['number']]);
		if($phones_code_hash !==""){
			if($RunInTerminal){
				$_GET['code'] = readline('Enter Code: ');
			}else{
				echo 'Code Sent.'. PHP_EOL .$BreakLine;
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
			echo 'Error while sending code!'. PHP_EOL .$BreakLine;
			exit();
		}
	}
	
	if($MadelineProto[$phones[0]['number']] != false && isset($_GET['code'])){
		$code = $_GET['code'];
		echo 'Confrim Code...'. PHP_EOL .$BreakLine;
		$authorization = $MadelineProto[$phones[0]['number']]->complete_phone_login($code);
		\danog\MadelineProto\Serialization::serialize($sessionFile, $MadelineProto[$phones[0]['number']]);
		if ($authorization['_'] === 'account.noPassword') {
			echo 'You Should Enter your Password!'. PHP_EOL .$BreakLine;
			exit();
		}
		if ($authorization['_'] === 'account.password') {
			$help = $authorization['hint']; // راهنمای پسورد
			if($RunInTerminal){
				$_GET['pass'] = readline('Password: ('.$help.')');
			}else{
				if(!isset($_GET['pass'])){
					echo "Password Help: ".$help. PHP_EOL .$BreakLine;
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
			$authorization = $MadelineProto[$phones[0]['number']]->complete_2fa_login($_GET['pass']);
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
			$authorization = $MadelineProto[$phones[0]['number']]->complete_signup($_GET['first_name'],$_GET['last_name']);
		}
		echo 'کد تایید شد.'. PHP_EOL .$BreakLine;
		$updates = $MadelineProto[$phones[0]['number']]->get_updates();
		$MadelineProto[$phones[0]['number']]->API->get_updates_difference();
		$MadelineProto[$phones[0]['number']]->API->store_db([], true);
		$MadelineProto[$phones[0]['number']]->API->reset_session();
		\danog\MadelineProto\Serialization::serialize($sessionFile, $MadelineProto[$phones[0]['number']]);
		echo 'حالا میتوانید با از سشن زیر جهت استفاده از اکانت خود استفاده نمایید:'. PHP_EOL .$BreakLine;
		echo $sessionFile. PHP_EOL .$BreakLine;
	}
