<?php
// José Juan Sánchez Hernández
// @josejuansanchez

define("UP", 0);
define("DOWN", 1);
define("LEFT", 2);
define("RIGHT", 3);

global $map;
global $speed;
global $timeStop;
global $w;
global $h;
global $timeList;

$testCases = readInputFromServer();
calculateTime($testCases);

function readInputFromServer() {
	$f = fopen("php://stdin", "r");
	$numberOfTests = fgets($f);
	for($i = 0; $i < $numberOfTests; $i++) {
		// width and height
		$line = fgets($f);
		$config = explode(" ",$line);
		$testCases[$i]["w"] = intval($config[0]);
		$testCases[$i]["h"] = intval($config[1]);		

		// speed
		$testCases[$i]["s"] = intval($config[2]);		

		// time
		$testCases[$i]["t"] = intval($config[3]);
		
		// map
		for($j = 0; $j < $testCases[$i]["h"]; $j++) {
			$testCases[$i]["map"][] = str_split(utf8_decode(trim(fgets($f), "\n")));
		}
	}
	fclose($f);	
	
	return $testCases;
}

function calculateTime($testCases) {
	global $map;
	global $speed;
	global $timeStop;
	global $w;
	global $h;
	global $timeList;

	foreach($testCases as $tc) {
		$map = $tc["map"];
		$speed = $tc["s"];
		$timeStop = $tc["t"];
		$w = $tc["w"];
		$h = $tc["h"];		
		$timeList = null;

		findInitialPosition($tc, &$posi, &$posj);

		if ($map[$posi - 1][$posj] != "#") minTime($posi - 1, $posj, UP, $tc["t"], 1);
		if ($map[$posi + 1][$posj] != "#") minTime($posi + 1, $posj, DOWN, $tc["t"], 1);
		if ($map[$posi][$posj - 1] != "#") minTime($posi, $posj - 1, LEFT, $tc["t"], 1);
		if ($map[$posi][$posj + 1] != "#") minTime($posi, $posj + 1, RIGHT, $tc["t"], 1);
		
		echo min($timeList)."\n";
	}
}

function findInitialPosition($tc, &$posi, &$posj) {
	for($i = 0; $i < $tc["h"]; $i++) {
		for($j = 0; $j < $tc["w"]; $j++) {
			if ($tc["map"][$i][$j] == "X") {
				$posi = $i;
				$posj = $j;
				return;
			} 
		}
	}		
}

function displayDirection($direction) {
	switch($direction) {
		case 0: return "UP";
		case 1: return "DOWN";
		case 2: return "LEFT";
		case 3: return "RIGHT";
	}
}

function minTime($i, $j, $direction, $time, $meters) {
	global $map;
	global $speed;
	global $timeStop;
	global $w;
	global $h;
	global $timeList;
	
	// Output conditions
	if (($meters > $w*$h) || ($time > $timeStop*(($w-2)*2 + ($h-2)*2))) return;		
	if ($map[$i][$j] == "O") { $timeList[] = $time + round($meters/$speed); return; }	
	if (($direction == UP) && ($map[$i-1][$j] == "#") && ($map[$i][$j+1] == "#") && ($map[$i][$j-1] == "#")) return;
	if (($direction == DOWN) && ($map[$i+1][$j] == "#") && ($map[$i][$j+1] == "#") && ($map[$i][$j-1] == "#")) return;	 
	if (($direction == LEFT) && ($map[$i][$j-1] == "#") && ($map[$i+1][$j] == "#") && ($map[$i-1][$j] == "#")) return;
	if (($direction == RIGHT) && ($map[$i][$j+1] == "#") && ($map[$i+1][$j] == "#") && ($map[$i-1][$j] == "#")) return;	

	// Change direction
	if (($direction == UP) && ($map[$i-1][$j] == "#") && ($map[$i][$j+1] != "#")) { minTime($i, $j+1, RIGHT, $time + $timeStop, $meters + 1);}
	if (($direction == UP) && ($map[$i-1][$j] == "#") && ($map[$i][$j-1] != "#")) { minTime($i, $j-1, LEFT, $time + $timeStop, $meters + 1);}
 	if (($direction == DOWN) && ($map[$i+1][$j] == "#") && ($map[$i][$j+1] != "#")) { minTime($i, $j+1, RIGHT, $time + $timeStop, $meters + 1);}
 	if (($direction == DOWN) && ($map[$i+1][$j] == "#") && ($map[$i][$j-1] != "#")) { minTime($i, $j-1, LEFT, $time + $timeStop, $meters + 1);}
 	if (($direction == LEFT) && ($map[$i][$j-1] == "#") && ($map[$i-1][$j] != "#")) { minTime($i-1, $j, UP, $time + $timeStop, $meters + 1);}
 	if (($direction == LEFT) && ($map[$i][$j-1] == "#") && ($map[$i+1][$j] != "#")) { minTime($i+1, $j, DOWN, $time + $timeStop, $meters + 1);} 	
 	if (($direction == RIGHT) && ($map[$i][$j+1] == "#") && ($map[$i-1][$j] != "#")) { minTime($i-1, $j, UP, $time + $timeStop, $meters + 1);}
 	if (($direction == RIGHT) && ($map[$i][$j+1] == "#") && ($map[$i+1][$j] != "#")) { minTime($i+1, $j, DOWN, $time + $timeStop, $meters + 1);} 	

	// Slide
	if (($direction == UP) && ($map[$i-1][$j]) != "#") minTime($i-1, $j, $direction, $time, $meters + 1);
	if (($direction == DOWN) && ($map[$i+1][$j]) != "#") minTime($i+1, $j, $direction, $time, $meters + 1);
	if (($direction == LEFT) && ($map[$i][$j-1]) != "#") minTime($i, $j-1, $direction, $time, $meters + 1);
	if (($direction == RIGHT) && ($map[$i][$j+1]) != "#") minTime($i, $j+1, $direction, $time, $meters + 1);
}
?>