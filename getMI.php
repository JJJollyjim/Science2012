<?php
	$jsonR = fopen("moreInfo.io", "r");
	$json = json_decode(fread($jsonR, 20048));
	fclose($jsonR);

	echo json_encode($json->$_POST["id"]);
?>