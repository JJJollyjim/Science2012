<?php
	$jsonR = fopen("moreInfo.io", "r");
	$json = json_decode(fread($jsonR, 20048));
	fclose($jsonR);

	unset($json->$_POST["id"]);
	$json->$_POST["id"]->name = $_POST["name"];
	$json->$_POST["id"]->state = $_POST["state"];
	$json->$_POST["id"]->dists = $_POST["dists"];
	$json->$_POST["id"]->Qs = $_POST["Qs"];
	$json->$_POST["id"]->quit = $_POST["quit"];
	$json->$_POST["id"]->acsf = $_POST["acsf"];

	$jsonW = fopen("moreInfo.io", "w+");
	fwrite($jsonW, json_encode($json));
	fclose($jsonW);
?>