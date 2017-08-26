<?php
	
	// جلوگیری از تایم اوت شدن کد
	@ini_set('zlib.output_compression',0);
	@ini_set('implicit_flush',1);
	@ob_end_clean();
	set_time_limit(0);
	
	// برای یافت خطاها
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
	
	date_default_timezone_set('Asia/Tehran');
	
	ob_implicit_flush(1);
	
	$libPath ='../'; // مسیر روت کتابخانه میدلاین
	$sessionsDir = 'sessions/'; // پوشه ذخیره سشن ها
	$MadelineURL ='http://tlbots.cf/_MadelineTest/Examples/'; // آدرس پوشه ای که فایل StartUserBot.php در آن است.
	
	// تنظیمات ای پی آی میدلان جهت اتصال به سرور تلگرام
	$settings = json_decode('{"logger":{"logger":0},"app_info":{"api_id":6,"api_hash":"eb06d4abfb49dc3eeb1aeb98ae0f581e"}}', true) ?: [];