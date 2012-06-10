<?php
	$output = array();

	$readyR = fopen("s0Ready.io", "r");
	$ready = fread($readyR, 1);
	fclose($readyR);

	$readyR = fopen("hs.io", "r");
	$highscores = fread($readyR, 1);
	fclose($readyR);

	if($highscores == 1) {
		$output["state"] = 2;

		$output["hsNames"] = array();
		$output["hsScores"] = array();

		$jsonR = fopen("list.io", "r");
		$json = json_decode(fread($jsonR, 10024));
		fclose($jsonR);

		$scores = (array) $json->state2score;
		$ascores = $scores;
		$names = (array) $json->state2;

		rsort($scores);
		arsort($ascores);
		
		for($i=0;$i<10;$i++) {
			$output["hsScores"][$i] = $scores[$i];
			$output["hsNames"][$i] = $names[keyAt($ascores, $i)];
		}
		
	} else if($ready == 1) {
		$output["state"] = 1;
	} else {
		$output["state"] = 0;
	}

	echo json_encode($output);

	function keyAt($array, $at) {
		$kaI=0;
		foreach ($array as $key => $value) {
			if($kaI == $at) {
				return $key;
			}

			$kaI++;
		}
	}

	function valueAt($array, $at) {
		$kaI=0;
		foreach ($array as $key => $value) {
			if($kaI == $at) {
				return $value;
			}

			$kaI++;
		}
	}
?>