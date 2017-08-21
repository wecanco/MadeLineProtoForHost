<?php
	// این فایل به این شکل استفاده شود:
	// http://mydomain.ir/mymadeline/GetChannelMessages.php?phone=+989357973301&channel=@wecangp&ids=78,79,80
	include_once('UserLogin.php'); // خواندن سشن
	if(isset($_GET['channel']) && isset($_GET['id'])){
		$ids = explode(',',$_GET['ids']);
		$channel = $_GET['channel'];
		$messages_Messages = $MadelineProto->channels->getMessages(['channel' => $channel, 'id' => $ids  ]);
		print_r($messages_Messages);
	}else{
		echo "پارمترهای channel  و id را وارد نمایید.<br>";
	}
