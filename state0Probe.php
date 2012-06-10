<?php
	$output = array();

	$s0IO = fopen("s0Ready.io", "r");
	$switch = fread($s0IO, 1);
	$output["switchTo1"] = $switch;
	fclose($s0IO);

	include 'reset.php';

	$jsonR = fopen("list.io", "r");
	$json = json_decode(fread($jsonR, 10024));
	fclose($jsonR);

	$json->state0->$_POST["id"] = $_POST["name"];
	$output["yourName"] = $json->state0->$_POST["id"];

	$jsonW = fopen("list.io", "w+");
	fwrite($jsonW, json_encode($json));
	fclose($jsonW);

	echo json_encode($output);
?>