<?php
	// فایل راه انداز ربات یوزری
	// این فایل تنها یک بار در مرورگر با پارامتر فون اجرا شود
	// جهت متوقف کردن بات نام فایل
	// _stop_bot.disabled
	// را به 
	// _stop_bot
	// تغییرنام دهید و جهت استارت مجدد اول نام فایل بالا را به حال اول برگردانده و دوباره فایل جاری را با متد فون یکبار درمرورگر اجرا کنید
	//?phone=+989357973301
	
	if(file_exists('.CurrentAC')){
		$phone = explode("\n",file_get_contents('.CurrentAC'))[0];
	}else if(isset($_GET['phone'])){
		$phone = $_GET['phone'];
	}else{
		exit();
	}
	
	//$stopBotFile = "_stop_bot_".str_replace(array("+"," ","(",")"),"",$phone);
	$stopBotFile = "_stop_bots";
    file_put_contents($stopBotFile,"1");
    sleep(1);
    unlink($stopBotFile);
	include('config.php');
	
    $comm = "wget --timeout=6000 -qO- ".$MadelineURL."/UserBot.php?phone=".urlencode($phone)." &> /dev/null";
    $res = exec($comm);
	file_put_contents('LastRunnedCronJob',date("Y-m-d H:i:s", time())."\n--------------\n".$res);

	// نمونه کرون جاب:
	//  */5 	* 	* 	* 	* 	wget -qO- http://tlbots.cf/_MadelineTest/Examples/StartUserBot.php?phone=+989357973301 &> /dev/null
	//  * 	* 	* 	* 	* 	php -q /home2/tlbotscf/public_html/MadeLineProtoForHost/Examples/UserBot.php +989357973301 &> /dev/null
	
		
	// @WeCanGP | WeCan-Co.ir
	
