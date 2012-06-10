<?php
	$jsonR = fopen("list.io", "r");
	$json = json_decode(fread($jsonR, 10024));
	fclose($jsonR);

	$mijsonR = fopen("moreInfo.io", "r");
	$mijson = json_decode(fread($mijsonR, 10024));
	fclose($mijsonR);

	if($_POST["level"] == 0) {
		unset($json);
		unset($mijson);
	} else if($_POST["level"] == 1) {
		unset($json->state0);
	} else if($_POST["level"] == 2) {
		unset($json->state1);
		unset($json->state1quit);
	} else if($_POST["level"] == 3) {
		unset($json->state2);
	}

	$jsonW = fopen("list.io", "w+");
	fwrite($jsonW, json_encode($json));
	fclose($jsonW);

	$mijsonW = fopen("moreInfo.io", "w+");
	fwrite($mijsonW, json_encode($mijson));
	fclose($mijsonW);
?>