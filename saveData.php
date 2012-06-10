<?php
	mysql_connect("localhost", "root", "chesey") or die(mysql_error());
	mysql_select_db("science") or die(mysql_error());

	$jsonR = fopen("moreInfo.io", "r");
	$json = json_decode(fread($jsonR, 20048));
	fclose($jsonR);

	foreach((array) $json as $key=>$value) {
		$aVal = (array) $value;
		
		$acsf = "";
		foreach($aVal["acsf"] as $insideKey=>$insideValue) {
			$acsf .= $insideValue." ";
		}
		
		$Qs = "";
		foreach($aVal["Qs"] as $insideKey=>$insideValue) {
			$Qs .= $insideValue." ";
		}
		
		$dists = "";
		foreach($aVal["dists"] as $insideKey=>$insideValue) {
			$dists .= $insideValue." ";
		}
		mysql_query("INSERT INTO results (rand_id, name, dists, quests, corrects, group_id) VALUES ('".$key."', '".$aVal["name"]."', '".$dists."', '".$Qs."', '".$acsf."', '".$_POST["groupId"]."')") or die(mysql_error());
	}
?>