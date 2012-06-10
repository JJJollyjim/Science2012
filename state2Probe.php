<?php
	$output = array();

	$jsonR = fopen("list.io", "r");
	$json = json_decode(fread($jsonR, 10024));
	fclose($jsonR);
	
	include 'reset.php';

	$json->state2->$_POST["id"] = $_POST["name"];
	$json->state2score->$_POST["id"] = $_POST["correct"];
	unset($json->state0->$_POST["id"]);
	unset($json->state1->$_POST["id"]);
	unset($json->state1quit->$_POST["id"]);
	$output["yourName"] = $json->state2->$_POST["id"];

	$jsonW = fopen("list.io", "w+");
	fwrite($jsonW, json_encode($json));
	fclose($jsonW);

	echo json_encode($output);
?>