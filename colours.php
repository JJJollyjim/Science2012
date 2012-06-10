<style>
	.cR { color: red; }
	.cY { color: yellow; }
	.cB { color: blue; }
	.cP { color: purple; }
	.cG { color: green; }
	.cO { color: orange; }
</style>

<?php
$array = array("Red", "Yellow", "Blue", "Purple", "Green", "Orange");
$arrayCSS = array("cR", "cY", "cB", "cP", "cG", "cO");

echo "<span class='".$arrayCSS[array_rand($arrayCSS)]."'>".$array[array_rand($array)]."</span>, ";
echo "<span class='".$arrayCSS[array_rand($arrayCSS)]."'>".$array[array_rand($array)]."</span>, ";
echo "<span class='".$arrayCSS[array_rand($arrayCSS)]."'>".$array[array_rand($array)]."</span>, ";
echo "<span class='".$arrayCSS[array_rand($arrayCSS)]."'>".$array[array_rand($array)]."</span>, ";
echo "<span class='".$arrayCSS[array_rand($arrayCSS)]."'>".$array[array_rand($array)]."</span>";
?>