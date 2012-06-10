<?php
	$s0IO = fopen("hs.io", "r");
	$s0 = fread($s0IO, 1);
	fclose($s0IO);

	if($s0 == 0) {
		$swapped = 1;
	} else {
		$swapped = 0;
	}

	$s0IO = fopen("hs.io", "w+");
	fwrite($s0IO, $swapped);
	fclose($s0IO);	
?>