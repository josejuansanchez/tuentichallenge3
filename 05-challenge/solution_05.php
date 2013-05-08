<?php
// José Juan Sánchez Hernández
// @josejuansanchez

global $max;

$testCases = readInputFromServer();
findMaximumPath($testCases);

function readInputFromServer() {
	$f = fopen("php://stdin", "r");	
	$numberOfTests = intval(fgets($f));
	for($i = 0; $i < $numberOfTests; $i++) {
		// width and height of the grid
		$line = fgets($f);
		$dimensions = explode(",",$line);
		$testCases[$i]["m"] = intval($dimensions[0]);
		$testCases[$i]["n"] = intval($dimensions[1]);		

		// initial position on the grid
		$line = fgets($f);
		$position = explode(",",$line);
		$testCases[$i]["x"] = intval($position[0]);
		$testCases[$i]["y"] = intval($position[1]);		

		// number of seconds until the gems disappear
		$testCases[$i]["z"] = intval(fgets($f));				
		
		// number of gems in the grid
		$testCases[$i]["g"] = intval(fgets($f));				
		
		// coordinates of the gems
		$line = fgets($f);		
		$gemList = explode("#",$line);
		
		// grid
		$grid = initializeGrid($testCases[$i]["m"], $testCases[$i]["n"]);		
		foreach($gemList as $gem) {
			$coordinatesValue = explode(",", $gem);
			$grid[$coordinatesValue[0]][$coordinatesValue[1]] = intval($coordinatesValue[2]);
		}				
		$testCases[$i]["grid"] = $grid;		
	}
	fclose($f);	
	
	return $testCases;
}

function initializeGrid($m, $n) {
	$grid = array();
	for($i = 0; $i < $n; $i++) {
		for($j = 0; $j < $m; $j++) {
			$grid[$i][$j] = 0;
		}
	}
	return $grid;
}

function findPath($grid, $x, $y, $m, $n, $blockx, $blocky, $z, $sum) {
	global $max;

	if ($sum > $max) $max = $sum;
	if ($z < 0) return;
	if (($x < 0) || ($x >= $m)) return;
	if (($y < 0) || ($y >= $n)) return;

	if (($x + 1 != $blockx) || ($y != $blocky)) {
		$sum = $sum + $grid[$x][$y];
		$grid[$x][$y] = 0;
		findPath($grid, $x + 1, $y, $m, $n, $x, $y, $z - 1, $sum);
	}

	if (($x - 1 != $blockx) || ($y != $blocky)) {	
		$sum = $sum + $grid[$x][$y];
		$grid[$x][$y] = 0;	
		findPath($grid, $x - 1, $y, $m, $n, $x, $y, $z - 1, $sum);
	}

	if (($x != $blockx) || ($y + 1 != $blocky)) {
		$sum = $sum + $grid[$x][$y];
		$grid[$x][$y] = 0;	
		findPath($grid, $x, $y + 1, $m, $n, $x, $y, $z - 1, $sum);	
	}
	
	if (($x != $blockx) || ($y - 1 != $blocky)) {
		$sum = $sum + $grid[$x][$y];
		$grid[$x][$y] = 0;		
		findPath($grid, $x, $y - 1, $m, $n, $x, $y, $z - 1, $sum);
	}
}

function findMaximumPath($testCases) {
	global $max;
	foreach($testCases as $tc) {	
		$max = -1;	
		findPath($tc["grid"], $tc["x"], $tc["y"], $tc["m"], $tc["n"], -1, -1, $tc["z"], 0);
		echo "$max\n";
	}
}
?>