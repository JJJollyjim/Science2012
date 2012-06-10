<?php
	$s0IO = fopen("reset.io", "r");
	$switch = fread($s0IO, 1);
	$output["reset"] = $switch;
	fclose($s0IO);
?>