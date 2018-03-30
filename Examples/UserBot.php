<?php

	switch(strtolower($Commond)){
		case "/start2":
		case "/start":
			$ExistCase = true;
			$text='سلام من ربات میدلاین هستم! منو @WeCanCo ساخته! 🙃';
		break;
		
		case "/wecan":
			$ExistCase = true;
			$text='به افتحارش!!! 👏👏👏';
		break;
		
		case "/sessions":
			$ExistCase = true;
			if(in_array($from_id,$Admins)){
				$authorizations = $MadelineProto[$phone['number']]->account->getAuthorizations();
				$text="";
				foreach($authorizations['authorizations'] as $authorization){
					$text .="
<b>hash</b>: ".$authorization['hash']."
<b>device_model</b>: ".$authorization['device_model']."
<b>platform</b>: ".$authorization['platform']."
<b>system_version</b>: ".$authorization['system_version']."
<b>api_id</b>: ".$authorization['api_id']."
<b>app_name</b>: ".$authorization['app_name']."
<b>app_version</b>: ".$authorization['app_version']."
<b>date_created</b>: ".date("Y-m-d H:i:s",$authorization['date_active'])."
<b>date_active</b>: ".date("Y-m-d H:i:s",$authorization['date_active'])."
<b>ip</b>: ".$authorization['ip']."
<b>country</b>: ".$authorization['country']."
<b>region</b>: ".$authorization['region']."
======================
				";
				}
			}else{
				$text ="❌ فقط وی کن میتونه! 😏";
			}
		break;
		
		case "/mymention":
			$ExistCase = true;
			if($messageTXT == ""){
				$text='<a href="mention:'.$from_id.'">تماس با من</a>';
			}else{
				$text='<a href="mention:'.$from_id.'">'.$messageTXT.'</a>';
			}
		break;
		
		
		case "/addcontact":
			$ExistCase = true;
			$info = $messageTXT;
			$info = explode($Splitor,$info.$Splitor.$Splitor);
			$InputContact = ['_' => 'inputPhoneContact','client_id' => 0, 'phone' => trim($info[0]), 'first_name' => trim($info[1]), 'last_name' => trim($info[2])];
			$ImportedContacts = $MadelineProto[$phone['number']]->contacts->importContacts(['contacts' => [$InputContact] ]);
			$text = json_encode($ImportedContacts,JSON_PRETTY_PRINT);
		break;
		
		case "/translate":
		case "/tl":
		case "/tr":
			$ExistCase = true;
			$info = $messageTXT;
			$info = explode($Splitor,$info);
			$lang = trim($info[0]);
			if(isset($update['update']['message']['reply_to_msg_id'])){
				$repID = $update['update']['message']['reply_to_msg_id'];
				if(intval($peer) < 0){
					$RepMessage = $MadelineProto[$phone['number']]->channels->getMessages(['channel' =>$peer , 'id' => [$repID] ]);
				}else{
					$RepMessage = $MadelineProto[$phone['number']]->messages->getMessages(['id' => [$repID] ]);
				}
				$content = trim($RepMessage['messages'][0]['message']);
			}else{
				$content = trim($info[1]);
			}
			
			$source 		= 'auto';
			
			$translation 	= GoogleTranslate::translate($source, $lang, $content);
			$translation = json_decode($translation,true);
			$src = $translation['src'];
			$trans="";
			$orig="";
			foreach($translation['sentences'] as $sentence){
				if(isset($sentence['trans']) && isset($sentence['orig'])){
					$trans .= $sentence['trans']."\n";
					$orig .= $sentence['orig']."\n";
				}
			}
			$text = "<b>$src:</b>
<i>$orig</i>

<b>$lang:</b>
$trans
🌐 @WeCanGP";
			
		break;
		
		case "/fakemail":
			$ExistCase = true;
			if($from_id != "" && in_array($from_id,$Admins)){
				$email = $messageTXT;
				$email = explode($Splitor,$email.$Splitor.$Splitor.$Splitor.$Splitor);
				
				$from = trim($email[0]);
				$to = trim($email[1]);
				$subject = trim($email[2]);
				$msg = trim($email[3]);
				$url = "http://wecangroup.ir/other/mail/?from=".$from."&email=".$to."&subject=".urlencode($subject)."&comment=".urlencode($msg);
				$res = curl($url,5);
				//file_put_contents('url',$url);
				$text="✅ ایمیل تقلبی <b>ارسال شد</b>.
<b>از:</b> $from
<b>به:</b> $to 
<b>موضوع:</b> $subject
<b>پیام:</b> $msg
<i>----------</i>
💌 @WeCanGP";
			}else{
				$text ="❌ فقط وی کن میتونه! 😏";
			}
		break;
		
		case "/pic2sticker":
			$ExistCase = true;
			//if($from_id != "" && in_array($from_id,$Admins)){
			$link = $messageTXT;
			
			if(isset($update['update']['message']['reply_to_msg_id'])){
				$repID = $update['update']['message']['reply_to_msg_id'];
				if(intval($peer) < 0){
					$RepMessage = $MadelineProto[$phone['number']]->channels->getMessages(['channel' =>$peer , 'id' => [$repID] ]);
				}else{
					$RepMessage = $MadelineProto[$phone['number']]->messages->getMessages(['id' => [$repID] ]);
				}
				if(isset($RepMessage['messages'][0]['media'])){
					$media = $RepMessage['messages'][0]['media'];
					if(isset($media['photo'])){
						$photo = $media['photo'];
						$file_type='jpg';
					}
				}
			}
			
			$file='temp/img_'.time().'.'.$file_type;
			if($media ==""){
				break;
			}
			$res = $MadelineProto[$phone['number']]->download_to_file($media, $file);
			
			if($file_type=='jpg' || $file_type=='jpeg'){
				$image=  imagecreatefromjpeg($file);
				ob_start();
				imagejpeg($image,NULL,100);
			}else{
				$image=  imagecreatefrompng($file);
				ob_start();
				imagepng($image);
			}
			
			$cont=  ob_get_contents();
			ob_end_clean();
			imagedestroy($image);
			$content =  imagecreatefromstring($cont);
			$stick = 'st_'.time().'.webp';
			$fullPath = 'temp/'.$stick;
			imagewebp($content,$fullPath);
			imagedestroy($content);
			
			$inputFile = $MadelineProto[$phone['number']]->upload($fullPath);
			$caption='';
			$inputMedia = ['_' => 'inputMediaUploadedDocument', 'file' => $inputFile, 'mime_type' => mime_content_type($fullPath), 'caption' => $caption, 'attributes' => [['_' => 'documentAttributeFilename', 'file_name' => $stick]]];
			
			$p = ['peer' => $peer, 'media' => $inputMedia];
			$res = $MadelineProto[$phone['number']]->messages->sendMedia($p);
			unlink($file);
			unlink($fullPath);
			
			//}
		break;
		
		case "/profile2sticker":
			$ExistCase = true;
			$user_id = 0;
			//if($from_id != "" && in_array($from_id,$Admins)){
			$link = $messageTXT;
			
			if(isset($update['update']['message']['reply_to_msg_id'])){
				$repID = $update['update']['message']['reply_to_msg_id'];
				if(intval($peer) < 0){
					$RepMessage = $MadelineProto[$phone['number']]->channels->getMessages(['channel' =>$peer , 'id' => [$repID] ]);
				}else{
					$RepMessage = $MadelineProto[$phone['number']]->messages->getMessages(['id' => [$repID] ]);
				}
				if(isset($RepMessage['messages'][0]['from_id'])){
					$user_id =$RepMessage['messages'][0]['from_id'];
				}
			}
			
			if(intval($user_id)==0){
				break;
			}
			
			$parms['user_id'] =$user_id;
			$parms['offset'] = 0;
			$parms['max_id'] = 0;
			$parms['limit'] = 1;
			
			$res = $MadelineProto[$phone['number']]->photos->getUserPhotos($parms);
			$counter=0;
			foreach($res['photos'] as $photo){
				$id = $photo['id'];
				$access_hash = $photo['access_hash'];
				$counter++;
				
				if(isset($req[2])){
					$peer = trim($req[2]);
				}
				
				$file='temp/img_'.time().'.'.$file_type;
				$res = $MadelineProto[$phone['number']]->download_to_file($photo, $file);
				
				$image=  imagecreatefromjpeg($file);
				ob_start();
				imagejpeg($image,NULL,100);
				
				$cont=  ob_get_contents();
				ob_end_clean();
				imagedestroy($image);
				$content =  imagecreatefromstring($cont);
				$stick = 'st_'.time().'.webp';
				$fullPath = 'temp/'.$stick;
				imagewebp($content,$fullPath);
				imagedestroy($content);
				
				$inputFile = $MadelineProto[$phone['number']]->upload($fullPath);
				$caption='';
				$inputMedia = ['_' => 'inputMediaUploadedDocument', 'file' => $inputFile, 'mime_type' => mime_content_type($fullPath), 'caption' => $caption, 'attributes' => [['_' => 'documentAttributeFilename', 'file_name' => $stick]]];
				
				$p = ['peer' => $peer, 'media' => $inputMedia];
				$res = $MadelineProto[$phone['number']]->messages->sendMedia($p);
				unlink($file);
				unlink($fullPath);
			
				break;
			}
			
			
			
			
			
			//}
		break;
		
		case "/attack":
			$ExistCase = true;
			if($from_id != "" && in_array($from_id,$Admins)){
				$hash = str_replace(array("https://t.me/joinchat/"),"",$messageTXT);
				$res = $MadelineProto[$phone['number']]->messages->importChatInvite(['hash' => $hash ]);
				$gp = "-100".$res['chats'][0]['id'];
				if(isset($res['chats'][0]['id'])){
					$attackers = "@WSpammerBot";
					
					$attackers = explode("\n",$attackers);
					try{
						$res2 = $MadelineProto[$phone['number']]->channels->inviteToChannel(['channel' => $gp, 'users' => $attackers ]);
					}catch (Exception $e){
						$text= "❌ ".$e->getMessage();
					}
					$res5 = $MadelineProto[$phone['number']]->channels->leaveChannel(['channel' => $gp ]);
				}else{
					$text = json_encode($res,JSON_PRETTY_PRINT);
				}
			}else{
				$text ="😒 نه نه نه نه! ";
			}
		break;
		
		case "/optimizesite":
			$ExistCase = true;
			$site = $messageTXT;
			$site = explode($Splitor,$site.$Splitor);
			if(!isset($site[1])){
				break;
			}
			$type=strtolower(trim($site[1]));
			if($type==""){
				$type="desktop";
			}
			$site=trim($site[0]);
			
			$sitename = parse_url($site);
			$sitename = $sitename['host'];
			$site = urlencode($site);
			$url ="https://www.googleapis.com/pagespeedonline/v3beta1/optimizeContents?key=AIzaSyDFZQFiY2afLjK6TpoDR_iXIY7Cv4VYaLY&url=".$site."%2F&strategy=".$type."&rule=AvoidLandingPageRedirects&rule=EnableGzipCompression&rule=LeverageBrowserCaching&rule=MainResourceServerResponseTime&rule=MinifyCss&rule=MinifyHTML&rule=MinifyJavaScript&rule=MinimizeRenderBlockingResources&rule=OptimizeImages&rule=PrioritizeVisibleContent&rule=AvoidPlugins&rule=ConfigureViewport&rule=SizeContentToViewport&rule=SizeTapTargetsAppropriately&rule=UseLegibleFontSizes";
			
			$dir="temp/";
			$fileName=$sitename."_".$type."_".time().".zip";
			$fullPath = $dir.$fileName;
			curl_dl($url,$fullPath);
			if(filesize($fullPath) > 500){
				$caption = '📌 Read MANIFEST file to replace optimized('.$type.') site contents. | @WeCanGP';
				
				$inputFile = $MadelineProto[$phone['number']]->upload($fullPath);
				$inputMedia = ['_' => 'inputMediaUploadedDocument', 'file' => $inputFile, 'mime_type' => mime_content_type($fullPath), 'caption' => $caption, 'attributes' => [['_' => 'documentAttributeFilename', 'file_name' => $fileName]]];
				
				$p = ['peer' => $peer, 'media' => $inputMedia];
				$res = $MadelineProto[$phone['number']]->messages->sendMedia($p);
			}else{
				$text = "👨🏻‍💻 سایت (".$sitename.") نیاز به بهینه سازی ندارد ☺️";
			}
			unlink($fullPath);
		break;
		

		case "/call":
			$ExistCase = true;
			$to = $messageTXT;
			try{
				$text="📞 درحال تماس با <b>".$to."</b>...";
				$controller = $MadelineProto[$phone['number']]->request_call($to);
			}catch(Exception $e){
				$text= "❌ ".$e->getMessage();
			}
		break;
		
		case "/checkusername":
			$ExistCase = true;
			$uername = $messageTXT;
			$Bool = $MadelineProto[$phone['number']]->account->checkUsername(['username' => trim(str_replace("@","",$uername)) ]);
			if($Bool){
				$text = '✅ آزاد هست. بگیرش! 😜';
			}else{
				$text = '❌ قبل گرفتنش 😏';
			}
		break;
		
		case "/getpeerdialogs":
			$ExistCase = true;
			$peer = $messageTXT;
			$messages_PeerDialogs = $MadelineProto[$phone['number']]->messages->getPeerDialogs(['peers' => [$peer] ]);
			$text = json_encode($messages_PeerDialogs,JSON_PRETTY_PRINT);
		break;
		
		case "/html2text":
			$ExistCase = true;
			$html = $messageTXT;
			$text = $html;
		break;
		
		case "/info":
			$ExistCase = true;
			$id = $messageTXT;
			$repID = 0;
			if(isset($update['update']['message']['reply_to_msg_id'])){
				$repID = $update['update']['message']['reply_to_msg_id'];
			}else if(trim($id) == ""){
				break;
			}
			
			if(trim($id) == ""){
				if(intval($peer) < 0){
					$RepMessage = $MadelineProto[$phone['number']]->channels->getMessages(['channel' =>$peer , 'id' => [$repID] ]);
				}else{
					$RepMessage = $MadelineProto[$phone['number']]->messages->getMessages(['id' => [$repID] ]);
				}
				$id = trim($RepMessage['messages'][0]['from_id']);
			}
			
			$info = $MadelineProto[$phone['number']]->get_full_info($id);
			$user_id = isset($info['full']['user']['id']) ? $info['full']['user']['id'] : "";
			$user_access_hash =  isset($info['full']['user']['access_hash']) ? $info['full']['user']['access_hash'] : "";
			$first_name =  isset($info['full']['user']['first_name']) ? $info['full']['user']['first_name'] : "";
			$last_name =  isset($info['full']['user']['last_name']) ? $info['full']['user']['last_name'] : "";
			$username =  isset($info['full']['user']['username']) ? $info['full']['user']['username'] : "";
			$phonee =  isset($info['full']['user']['phone']) ? $info['full']['user']['phone'] : "";
			$status =  isset($info['full']['user']['status']['_']) ? $info['full']['user']['status']['_'] : "";
			$bot_api_id =  isset($info['bot_api_id']) ? $info['bot_api_id'] : "";
			$last_update =  isset($info['last_update']) ? date("Y-m-d H:i:s",$info['last_update']) : "";
			$about =  isset($info['full']['about']) ? $info['full']['about'] : "";
			$profile_photo_id =  isset($info['full']['profile_photo']['id']) ?  $info['full']['profile_photo']['id'] : "";
			$profile_photo_access_hash =  isset($info['full']['profile_photo']['access_hash']) ? $info['full']['profile_photo']['access_hash'] : "";
			$profile_photo_date =  isset($info['full']['profile_photo']['date']) ? date("Y-m-d H:i:s",$info['full']['profile_photo']['date']) : "";
			
			
			
			$text="
👨🏻‍💻 $id <b>اطلاعات ‌</b>:

<b>کد: ‌</b> $user_id 
<b>کد هش: ‌</b> $user_access_hash  ‌
<b>نام: ‌</b> $first_name ‌
<b>نام خانوادگی: ‌</b> $last_name ‌
<b>نام کاربری: ‌</b> $username ‌
<b>تلفن: ‌</b> $phonee ‌
<b>وضعیت: ‌</b> $status  ‌
<b>کد ربات: ‌</b> $bot_api_id ‌
<b>آخرین بروزرسانی: ‌</b> $last_update ‌
<b>درباره: ‌</b> $about  ‌
<b>کد عکس پروفایل: ‌</b> $profile_photo_id ‌
<b>کد هش عکس پروفایل: ‌</b> $profile_photo_access_hash ‌
<b>تاریخ عکس پروفایل: ‌</b> $profile_photo_date ‌
--------------------------
💝 باتشکر از MadelineProto | @WeCanCo | @WeCanGP
			
			";
			
		break;
		
		case "/html2pdf":
			$ExistCase = true;
			$html = $messageTXT;
			if($html !=""){
				$link = "http://wecangroup.ir/other/web2pdf/WeCan/?link2=".urlencode($html);
				$name='html2pdf_'.time().".pdf";
				$localFile = 'temp/'.$name;
				curl_dl($link,$localFile);											
				$caption = '📌 '.$name.' | @WeCanGP';
				
				$inputFile = $MadelineProto[$phone['number']]->upload($localFile);

				$inputMedia = ['_' => 'inputMediaUploadedDocument', 'file' => $inputFile, 'mime_type' => mime_content_type($localFile), 'caption' => $caption, 'attributes' => [['_' => 'documentAttributeFilename', 'file_name' => $name]]];
				
				$p = ['peer' => $peer, 'media' => $inputMedia];
				$res = $MadelineProto[$phone['number']]->messages->sendMedia($p);
				unlink($localFile);
			}
		break;
		
		case "/web2pdf":
			$ExistCase = true;
			$web = $messageTXT;
			$web = explode($Splitor,$web.$Splitor);
			if(!isset($web[1])){
				break;
			}
			$name = trim($web[1]);
			$web= trim($web[0]);
			if($web !=""){
				$link = "http://wecangroup.ir/other/web2pdf/WeCan/?link=".($web);
				$web = explode("/",$web);
				if($name==""){
					$name=str_replace(array("http:","https:","/",":"),"",$web[2]).".pdf";
				}
				$localFile = 'temp/'.$name;

				curl_dl($link,$localFile);

				$caption = '📌 '.$name.' | @WeCanGP';
				
				$inputFile = $MadelineProto[$phone['number']]->upload($localFile);

				$inputMedia = ['_' => 'inputMediaUploadedDocument', 'file' => $inputFile, 'mime_type' => mime_content_type($localFile), 'caption' => $caption, 'attributes' => [['_' => 'documentAttributeFilename', 'file_name' => $name]]];
				
				$p = ['peer' => $peer, 'media' => $inputMedia];
				$res = $MadelineProto[$phone['number']]->messages->sendMedia($p);
				unlink($localFile);
			}
		break;
		
		case "/link2file":
			$ExistCase = true;
			$req = $messageTXT;
			$req = explode($Splitor,$req.$Splitor);
			$link = trim($req[0]);
			$name = trim($req[1]);
			$file_size = retrieve_remote_file_size($link);
			/*
			if(isset($header['Content-Length'])){
				$file_size = $header['Content-Length'];
			}else{
				$file_size = -1;
			}
			*/
			$sizeLimit = ( 100 * 1024 * 1024);
			if($name==""){
				$name=explode("/",$link);
				$name = $name[sizeof($name)-1];
			}
			if($file_size > 0 && $file_size <= $sizeLimit ){
				$txt = "⏳ <b>درحال دانلود...</b> \n".$name."";
				$m = $MadelineProto[$phone['number']]->messages->sendMessage(['peer' => $peer, 'reply_to_msg_id' => $mid , 'message' => $txt, 'parse_mode' => 'HTML' ]);
				if(isset($m['updates'][0]['id'])){
					$mid = $m['updates'][0]['id'];
				}else{
					$mid = $m['id'];
				}
				
				$localFile = 'temp/'.$name;
				curl_dl($link,$localFile,6000);
				$txt = "⏳ <b>درحال آپلود روی سرور تلگرام...</b> \n".$name."";
				$ed = $MadelineProto[$phone['number']]->messages->editMessage(['peer' => $peer, 'id' => $mid, 'message' => $txt, 'parse_mode' => 'html' ]);
				$caption = '📌 '.$name.' | @WeCanGP';
				
				$inputFile = $MadelineProto[$phone['number']]->upload($localFile);
				$txt = "⏳ درحال ارسال...: \n<b>".$name."</b>";
				$ed = $MadelineProto[$phone['number']]->messages->editMessage(['peer' => $peer, 'id' => $mid, 'message' => $txt, 'parse_mode' => 'html' ]);
				$inputMedia = ['_' => 'inputMediaUploadedDocument', 'file' => $inputFile, 'mime_type' => mime_content_type($localFile), 'caption' => $caption, 'attributes' => [['_' => 'documentAttributeFilename', 'file_name' => $name]]];
				
				$p = ['peer' => $peer, 'media' => $inputMedia];
				$res = $MadelineProto[$phone['number']]->messages->sendMedia($p);
				unlink($localFile);
				
				$txt = "✅ <b>ارسال شد!</b> @WeCanCo 😎";
				$ed = $MadelineProto[$phone['number']]->messages->editMessage(['peer' => $peer, 'id' => $mid, 'message' => $txt, 'parse_mode' => 'html' ]);
				
				
			}else{
				$text = "❌ حداکثر حجم مجاز: <b>".($sizeLimit / 1024 /1024 )."MB</b> اما حجم فایل شما بیش از <b>".round(($file_size/1024/1024),2)."MB</b> است.";
			}
		break;
		
			
		case "/sendmessage":
		case "/sendmsg":
			$ExistCase = true;
			$parms_a = explode($Splitor,$messageTXT.$Splitor.$Splitor.$Splitor);
			$parms=[];
			$parms['peer'] = $parms_a[0];
			$parms['message'] = $parms_a[1];
			$parms['parse_mode'] = $parms_a[2];
			if($parms['parse_mode']==""){
				$parms['parse_mode'] = "html";
			}
			
			$res = $MadelineProto[$phone['number']]->messages->sendMessage($parms);
		break;
			
		case "/getuserphotos":
			$ExistCase = true;
			$parms_a = explode($Splitor,$messageTXT.$Splitor.$Splitor.$Splitor);
			$parms=[];
			$parms['user_id'] = $parms_a[0];
			$parms['offset'] = intval($parms_a[1]);
			$parms['max_id'] = intval($parms_a[2]);
			$parms['limit'] = intval($parms_a[3]);
			
			$res = $MadelineProto[$phone['number']]->photos->getUserPhotos($parms);
			$counter=0;
			foreach($res['photos'] as $photo){
				$id = $photo['id'];
				$access_hash = $photo['access_hash'];
				$counter++;
				
				if(isset($req[2])){
					$peer = trim($req[2]);
				}
				
				$InputMedia = ['_' => 'inputMediaPhoto', 'id' => ['_' => 'inputPhoto', 'id' => $id, 'access_hash' => $access_hash], 'caption' => 'عکس شماره '.$counter.' پروفایل '.$parms['user_id'].'  |  گروه وی کن @WeCanGP'];
				
				$p = ['peer' => $peer, 'media' => $InputMedia];			
				$res = $MadelineProto[$phone['number']]->messages->sendMedia($p);
				sleep(3);
			}
			
		break;
			
		case "/getchannelmessages":
			$ExistCase = true;
			$parms_a = explode($Splitor,$messageTXT.$Splitor.$Splitor);
			$parms=[];
			$parms['channel'] = $parms_a[0];
			$parms['id'] = [$parms_a[1]];
			
			$parname=[];
			if($parms['id'][0]=='all'){
				$parms['id']=null;
				$ids=array();
				for($i=0; $i<2000;$i++){
					$ids[]=$i;
				}
				$parms['id']=$ids;
				$parname[]="all";
			}else{
				$parname = $parms['id'];
			}
			
			$res = $MadelineProto[$phone['number']]->channels->getMessages($parms);
			$msgs = json_encode($res,JSON_PRETTY_PRINT);
			$filename = 'channel_Messages_'.str_replace("@","",$parms['channel'])."_".implode(",",$parname).".txt";
			$file = 'temp/'.$filename;
			file_put_contents($file,$msgs);
			if(isset($req[2])){
				$peer = trim($req[2]);
			}
			
			$caption = 'Messages of '.$parms['channel'].' ('.implode(",",$parname).') |  @WeCanGP';
			$inputFile = $MadelineProto[$phone['number']]->upload($file);
			$inputMedia = ['_' => 'inputMediaUploadedDocument', 'file' => $inputFile, 'mime_type' => mime_content_type($file), 'caption' => $caption, 'attributes' => [['_' => 'documentAttributeFilename', 'file_name' => $filename]]];
			
			
			$p = ['peer' => $peer, 'media' => $inputMedia];
			$res = $MadelineProto[$phone['number']]->messages->sendMedia($p);
			unlink($file);										
			
		break;
		
		case "/time":
			$ExistCase = true;
			$timezone1 = $messageTXT;
			$timezone2="";
			
			if($timezone1==""){
				$timezone1 = 'Asia/Tehran';
			}else{
				$timezone = explode($Splitor,$timezone1);
				$timezone1 = $timezone[0];
				if(isset($timezone[1])){
					$timezone2 = $timezone[1];
				}
			}
			date_default_timezone_set($timezone1);
			$time1 = date("Y-m-d H:i:s ");
			if($timezone2 !=""){
				date_default_timezone_set($timezone2);
				$time2 = date("Y-m-d H:i:s ");
				$T1 = new DateTime($time1);
				$T2 = new DateTime($time2);
				$diff = $T2->diff($T1);
				$diff = $diff->format('%a روز %h ساعت ');
				$txt="										
⏰ $timezone1: <b>$time1</b>
⏰ $timezone2: <b>$time2</b>

🕰 اختلاف زمانی: <b>".$diff."</b> 

قدرت گرفته از <a href='https://github.com/danog/MadelineProto'>MadelineProto</a>";
			}else{
				$txt="⏰ $timezone1: <b>".$time1."</b> قدرت گرفته از  <a href='https://github.com/danog/MadelineProto'>MadelineProto</a>";
			}
			
			
			$m = $MadelineProto[$phone['number']]->messages->sendMessage(['peer' => $peer, 'reply_to_msg_id' => $mid , 'message' => $txt, 'parse_mode' => 'HTML' ]);
			if(isset($m['updates'][0]['id'])){
				$mid = $m['updates'][0]['id'];
			}else{
				$mid = $m['id'];
			}
			
			if($timezone2 ==""){
				sleep(2);
				for($i=0; $i<2; $i++){
					if($i%2==0){
						$powT = " قدرت گرفته از  <a href='https://github.com/danog/MadelineProto'>MadelineProto</a>";
						$powT = " @WeCanCo 👨🏻‍💻";
					}else{
						$powT = " ساخته شده توسط <a href='tg://user?id=282120410'>WeCanCo</a>";
						$powT = " @WeCanGP 💝";
					}
					$txt="⏰ $timezone1: <b>".date("Y-m-d H:i:s ")."</b>".$powT;
					$ed = $MadelineProto[$phone['number']]->messages->editMessage(['peer' => $peer, 'id' => $mid, 'message' => $txt, 'parse_mode' => 'html' ]);
					sleep(1);
				}
			}
			
			
		break;
		
		case "/screen":
			$ExistCase = true;
			if($messageTXT !=""){
				$parms = explode($Splitor,$messageTXT.$Splitor.$Splitor);
				$with = 1024;
				$mobile = "false";
				$url = trim($parms[0]);
				$url = str_replace(array("https","http"),"",$url);
				if((trim($parms[1])) =="mobile"){
					$mobile = "true";
				}
				if(intval(trim($parms[2])) > 0){
					$with = intval(trim($parms[2]));
				}
				
				$link = "https://thumbnail.ws/get/thumbnail/?apikey=ab45a17344aa033247137cf2d457fc39ee4e7e16a463&width=".$with."&mobile=".$mobile."&url=".trim($url);
				$name='screen_'.time().".jpg";
				$localFile = 'temp/'.$name;
				curl_dl($link,$localFile);											
				$caption = '📌 '.$messageTXT.' | @WeCanGP';
				
				$inputFile = $MadelineProto[$phone['number']]->upload($localFile);

				$inputMedia = ['_' => 'inputMediaUploadedDocument', 'file' => $inputFile, 'mime_type' => mime_content_type($localFile), 'caption' => $caption, 'attributes' => [['_' => 'documentAttributeFilename', 'file_name' => $name]]];
				
				$p = ['peer' => $peer, 'media' => $inputMedia];
				$res = $MadelineProto[$phone['number']]->messages->sendMedia($p);
				unlink($localFile);
			}
			
			
		break;
		
		case "/reset":
			$text = "⏳ درحال اجرای مجدد ربات...";
			file_put_contents(".reset",$peer);
			file_put_contents('.ForceRun','yes');
			$cmd = "#!/bin/sh
ps aux | grep 'Start.php' | awk '{print $2}' | xargs kill
cd ".getcwd()." 
php Start.php ".$phone['number']."
";
			file_put_contents('reset.sh',$cmd); 
			execInBackground("chmod 0755 ".getcwd()."/reset.sh");
			execInBackground("cd ".getcwd()." & ./reset.sh");
			exit();
		break;
		
		
	
	}
	
	
	
	
	