<?php

	require_once '__madeline_config.php'; // فایل کانفیگ
	require_once $libPath.'vendor/autoload.php'; // فراخوانی لودر کتابخانه میدلاین
	//require_once $libPath.'src/danog/MadelineProto/VoIP/php-libtgvoip.php';

	if(!file_exists($sessionsDir)){
		mkdir($sessionsDir);
	}

	$phone = str_replace(array(" ","(",")"),"",$_GET['phone']); // شماره موبایلی که با آن لاگین میشوید
	$sessionFile = $sessionsDir."/session_".str_replace(array("+","-","(",")"),"",$phone).""; // مسیر سشن

	if (file_exists($libPath.'web_data.php')) {
		require_once $libPath.'web_data.php';
	}

	$MadelineProto = false;
	echo "درحال آماده سازی...<br>";

	if(file_exists($sessionFile)){
		try {
			echo 'درحال خواندن سشن: ('.$sessionFile.')...'.PHP_EOL."<br>";
			$MadelineProto = \danog\MadelineProto\Serialization::deserialize($sessionFile);
		} catch (\danog\MadelineProto\Exception $e) {
			echo 'خطا: '.PHP_EOL."<br>";
			var_dump($e->getMessage());
			exit();
		}
	}

	echo 'سشن خوانده شد.'.PHP_EOL."<br>";

	if ($MadelineProto === false) {
		sleep(1);
		echo 'درحال اتصال به سرور تلگرام...'.PHP_EOL;
		$MadelineProto = new \danog\MadelineProto\API($settings);

		echo 'به سرور تلگرام متصل شد.'.PHP_EOL."<br>";

		echo 'درحال چک کردن شماره موبایل...'.PHP_EOL."<br>";
		$checkedPhone = $MadelineProto->auth->checkPhone(
		[
		'phone_number'     => $phone,
		]
		);

		echo 'موبایل چک شد.'.PHP_EOL."<br>";

		echo 'درحال ارسال کد جهت ورود به اکانت...'.PHP_EOL."<br>";
		$sentCode = $MadelineProto->phone_login($phone);
		$phone_code_hash = $sentCode['phone_code_hash'];
		\danog\MadelineProto\Serialization::serialize($sessionFile, $MadelineProto);
		if($phone_code_hash !==""){
			echo 'کد به تلگرام شما ارسال شد. لطفا با ارسال پارامتر code کد ارسالی را تایید و وارد اکانت خود شوید.'.PHP_EOL."<br>";
			}else{
			echo 'خطا در ارسال کد.'.PHP_EOL."<br>";
			exit();
		}


	}else if(isset($_GET['code'])){
		$code = $_GET['code'];
		echo 'درحال تایید کد...'.PHP_EOL."<br>";
		$authorization = $MadelineProto->complete_phone_login($code);

		if ($authorization['_'] === 'account.noPassword') {
			echo 'ورود دو مرحله ای شما فعال است و پسورد خود را وارد نکردید!'.PHP_EOL."<br>";
			exit();
		}
		if ($authorization['_'] === 'account.password') {
			$help = $authorization['hint']; // راهنمای پسورد
			echo $help;
			// ورود دو مرحله ای
			$authorization = $MadelineProto->complete_2fa_login($_GET['pass']);
		}
		if ($authorization['_'] === 'account.needSignup') {
			// اگر برای اولین بار است که اکانت تلگرام روی این شماره فعال می شود، نام و نام خانوادگی را دریافت کن
			$authorization = $MadelineProto->complete_signup($_GET['first_name'],$_GET['last_name']);
		}
		echo 'کد تایید شد.'.PHP_EOL."<br>";
		$updates = $MadelineProto->get_updates();
		$MadelineProto->API->get_updates_difference();
		$MadelineProto->API->store_db([], true);
		$MadelineProto->API->reset_session();
		\danog\MadelineProto\Serialization::serialize($sessionFile, $MadelineProto);
		echo 'حالا میتوانید با از سشن زیر جهت استفاده از اکانت خود استفاده نمایید:'.PHP_EOL."<br>";
		echo $sessionFile.PHP_EOL."<br>";
	}
