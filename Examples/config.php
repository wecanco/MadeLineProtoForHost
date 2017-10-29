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
	$RunInTerminal = false; // فایل فعالسازی اجرا در ترمینال
	$MadelineURL ='http://tlapi.cf/MadeLineProtoForHost/Examples/'; // آدرس پوشه ای که فایل StartUserBot.php در آن است.
	if(!file_exists('.admins')){
		file_put_contents('.admins',"282120410");
	}
	$Admins = explode("\n",file_get_contents('.admins')); //array('282120410');
	$Bots=array(
		'WeMadelineBot' => array(
							'token' => "437491880:AAGkh9Ytem_gtfyeW7hh4L_NqKnfaVkExSg",
							'active' => true
		)
	);
	
	// تنظیمات ای پی آی میدلان جهت اتصال به سرور تلگرام
	//$settings = json_decode('{"logger":{"logger":0},"app_info":{"api_id":6,"api_hash":"eb06d4abfb49dc3eeb1aeb98ae0f581e"}}', true) ?: [];
	$settings = 
	[
		'logger' => [
			'logger' => 0
		],
		/*
		'connection_settings' => [
            'all' => [
                    'protocol' => 'tcp_full',
                    'test_mode' => false,
					'ipv6' => '',
                    'timeout' => 2,
					'proxy' => '\Socket',
                    'proxy_extra' => [],

                ],

            'default_dc' => 4
        ],
		*/
		'app_info' => [
				'device_model' => 'WeCan ROBOT',
				'system_version' => ''.rand(1,10),
				'app_version' => 'No Proxy',
				'lang_code' => 'fa',
				'api_id' => 6,
				'api_hash' => 'eb06d4abfb49dc3eeb1aeb98ae0f581e'
		],


	];
	
	$settings_proxy = 
	[
		'logger' => [
			'logger' => 0
		],
		
		'connection_settings' => [
            'all' => [
                    'protocol' => 'tcp_full',
                    'test_mode' => false,
                    //'ipv6' => '',
                    'timeout' => 2,
                    'proxy' => '\SocksProxy',
                    'proxy_extra' => [
							'address' => '24.14.37.239',
							'port' => 38665
                    ],

                ],

            'default_dc' => 4
        ],

		'app_info' => [
				'device_model' => 'WeCan ROBOT',
				'system_version' => ''.rand(1,10),
				'app_version' => 'Proxy',
				'lang_code' => 'fa',
				'api_id' => 6,
				'api_hash' => 'eb06d4abfb49dc3eeb1aeb98ae0f581e'
		],


	];
	
	
	
	
	