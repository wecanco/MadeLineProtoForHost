<?php
	
	// جلوگیری از تایم اوت شدن کد
	@ini_set('zlib.output_compression',0);
	@ini_set('implicit_flush',1);
	@ob_end_clean();
	set_time_limit(0);
	
	@ini_set('xdebug.max_nesting_level', 5000);
	@ini_set('register_argc_argv', 'On On');
	
	// برای یافت خطاها
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
	ini_set("error_log", "php-error.log");
	
	date_default_timezone_set('Asia/Tehran');
	
	ob_implicit_flush(1);
	
	$currentDIR = __DIR__;
	$preDIR = explode('/',$currentDIR);
	unset($preDIR[sizeof($preDIR)-1]);
	$preDIR = implode('/',$preDIR);
	
	$libPath =$preDIR."/"; // مسیر روت کتابخانه میدلاین
	$sessionsDir = $currentDIR.'/sessions'; // پوشه ذخیره سشن ها
	$RunInTerminal = false; // فایل فعالسازی اجرا در ترمینال
	$MadelineURL ='http://tlapi.cf/MadeLineProtoForHost/Examples/'; // آدرس پوشه ای که فایل StartUserBot.php در آن است.
	if(!file_exists('.admins')){
		file_put_contents('.admins',"282120410");
	}
	$Admins = explode("\n",file_get_contents('.admins')); //array('282120410');
	$Bots=array(
		'WeMadelineBot' => array(
							'token' => "437491880:AAGkh9Ytem_gtfyeW7hh4L_NqKnfaVkExSg",
							'active' => false
		)
	);
	
	// تنظیمات ای پی آی میدلان جهت اتصال به سرور تلگرام
	$settings = json_decode('{"logger":{"logger":0},"app_info":{"api_id":6,"api_hash":"eb06d4abfb49dc3eeb1aeb98ae0f581e"}}', true) ?: [];
	//$ipv6 = (bool) strlen(@file_get_contents('http://ipv6.test-ipv6.com/', false, stream_context_create(['http' => ['timeout' => 1]]))) > 0;
	$settings2 = 
	[
		'logger' => [
			'logger' => 0
		],
		'updates' => [
			'handle_updates' => true,
			'handle_old_updates' => false,
			'getdifference_interval' => -1,
			'callback' => "updates",
		],
		/*
		'connection_settings' => [
            'all' => [
                    'protocol' => 'tcp_full',
                    'test_mode' => false,
					'ipv6' => $ipv6,
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
				'app_version' => 'Socks Proxy',
				'lang_code' => 'fa',
				'api_id' => 6,
				'api_hash' => 'eb06d4abfb49dc3eeb1aeb98ae0f581e'
		],


	];
	
	$settings_http_proxy = 
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
                    'proxy' => '\HttpProxy',
                    'proxy_extra' => [
							'address' => '212.49.115.67',
							'port' => 8080
                    ],

                ],

            'default_dc' => 4
        ],

		'app_info' => [
				'device_model' => 'WeCan ROBOT',
				'system_version' => ''.rand(1,10),
				'app_version' => 'Http Proxy',
				'lang_code' => 'fa',
				'api_id' => 6,
				'api_hash' => 'eb06d4abfb49dc3eeb1aeb98ae0f581e'
		],


	];
	
	
	
	
	