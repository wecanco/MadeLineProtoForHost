<?php
	// فایل راه انداز ربات یوزری
	// این فایل تنها یک بار در مرورگر با پارامتر فون اجرا شود
	// جهت متوقف کردن بات نام فایل
	// _stop_bot.disabled
	// را به 
	// _stop_bot
	// تغییرنام دهید و جهت استارت مجدد اول نام فایل بالا را به حال اول برگردانده و دوباره فایل جاری را با متد فون یکبار درمرورگر اجرا کنید
	//?phone=+989905586201
	
	$MadelineURL = "http://tlbots.cf/_MadelineTest";
    exec('wget -qO- '.$MadelineURL.'/Examples/UserBot.php?phone='.urlencode($_GET['phone']).' &> /dev/null');
	

	// نمونه کرون جاب:
	//  */5 	* 	* 	* 	* 	wget -qO- http://tlbots.cf/_MadelineTest/Examples/StartUserBot.php?phone=+989357973301 &> /dev/null
	
		
	// @WeCanGP | WeCan-Co.ir
	
