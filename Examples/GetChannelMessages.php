<?php
// این فایل به این شکل استفاده شود:
// http://mydomain.ir/mymadeline/GetChannelMessages.php?phone=+989357973301&channel=@wecangp&id=[78,79,80]
include_once('UserLogin.php'); // خواندن سشن

header('Content-type: application/json'); // اعلام خروجی جی سون به مرورگر

if(isset($_GET['channel']) && isset($_GET['id']))
  $messages_Messages = $MadelineProto->channels->getMessages(['channel' => $_GET['channel'], 'id' => $_GET['id'] ]);
  echo json_encode($messages_Messages);
}else{
  echo json_encode(["ok"=>false,"des"=>"پارمترهای channel  و id را وارد نمایید."]);
}
