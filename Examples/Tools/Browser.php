<?php
	include('../inc/WeCanFunctions.php');
	
	echo '
	<form method="post">
	<textarea colspan=20 rospan=20 name="web" >'.$_REQUEST['web'].'</textarea>
	<input type="submit" value="برو!" />
	</form>
	
';
	
	if(isset($_REQUEST['web'])){
		$web = curl($_REQUEST['web'],0);
		$host = str_replace(array('https://','http://'),'',$_REQUEST['web']);
		$host = "http://".explode('/',$host)[0];
		$web = str_replace('src="/','src="'.$host.'/',$web);
		$web = str_replace('href="/','href="'.$host.'/',$web);
		
		$web = str_replace("src='/","src='".$host.'/',$web);
		$web = str_replace("href='/","href='".$host.'/',$web);
		
		echo $web;
	}
	
