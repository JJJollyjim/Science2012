<?php
	$jsonR = fopen("list.io", "r");
	echo "<tt>".fread($jsonR, 10024)."</tt>";
	echo "<script>window.location = window.location</script>";
	fclose($jsonR);
?>