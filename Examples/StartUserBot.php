<?php
	// فایل راه انداز ربات یوزری
	// این فایل تنها یک بار در مرورگر با پارامتر فون اجرا شود
	// جهت متوقف کردن بات نام فایل
	// _stop_bot.disabled
	// را به 
	// _stop_bot
	// تغییرنام دهید و جهت استارت مجدد اول نام فایل بالا را به حال اول برگردانده و دوباره فایل جاری را با متد فون یکبار درمرورگر اجرا کنید
	//?phone=+989905586201
	
	$phone = $_GET['phone'];
	$stopBotFile = "_stop_bot_".str_replace(array("+"," ","(",")"),"",$phone);
    file_put_contents($stopBotFile,"1");
    sleep(1);
    unlink($stopBotFile);
	include('__madeline_config.php');
	
    $comm = "wget --timeout=6000 -qO- ".$MadelineURL."/UserBot.php?phone=".urlencode($phone)." &> /dev/null";
    exec($comm);


	// نمونه کرون جاب:
	//  */5 	* 	* 	* 	* 	wget -qO- http://tlbots.cf/_MadelineTest/Examples/StartUserBot.php?phone=+989357973301 &> /dev/null
	
		
	// @WeCanGP | WeCan-Co.ir
	
