<?php
// José Juan Sánchez Hernández
// @josejuansanchez

$testCases = readInputFromServer();
processCases($testCases);

function readInputFromServer() {
	$f = fopen("php://stdin", "r");
	$numberOfTests = fgets($f);
	for($i = 0; $i < $numberOfTests; $i++) {
		// width and height of the canyon
		$line = fgets($f);
		$config = explode(" ",$line);
		$testCases[$i]["w"] = intval($config[0]);
		$testCases[$i]["h"] = intval($config[1]);		

		// price to train a soldier
		$testCases[$i]["s"] = intval($config[2]);		

		// price to trigger a crematorium
		$testCases[$i]["c"] = intval($config[3]);

		// amount of gold that you have
		$testCases[$i]["g"] = intval($config[4]);
		
		for($j = 0; $j < $testCases[$i]["h"]; $j++) {
			$testCases[$i]["cannon"][$j] = 0;				
		}				
	}
	fclose($f);
	return $testCases;
}

function processCases($testCases) {
	foreach($testCases as $tc) {
		$listOfOptions = calculateCombinations($tc["s"], $tc["c"], $tc["g"]);								
		$maxTime = -1;
		foreach($listOfOptions as $option) {

			// we can take on the Zorg overrun forever
			if ($option["soldiers"] >= $tc["w"]) { 
				$time = -1;
				break;
			}
			$time = calculateNumberOfSeconds($tc, $option["soldiers"], $option["crematorioums"]);
			if ($time > $maxTime) $maxTime = $time;
		}		
		echo $maxTime."\n";
	}
}

function calculateCombinations($s, $c, $g) {
	$listOfOptions = array();	
	for($soldiers = intval($g / $s); $soldiers > 0; $soldiers--) {
		
		$crematorioums = 0;
		if ($g - ($soldiers * $s) >= $c) {
			$crematorioums = intval(($g - ($soldiers * $s)) / $c);	
		}
	
		if (!existBetterOption($listOfOptions, $soldiers, $crematorioums)) {	
			$option["soldiers"] = $soldiers;
			$option["crematorioums"] = $crematorioums;
			$listOfOptions[] = $option;
		}
	}
	$option["soldiers"] = 0;
	$option["crematorioums"] = intval($g / $c);	
	if (!existBetterOption($listOfOptions, $option["soldiers"], $option["crematorioums"])) {	
		$listOfOptions[] = $option;
	}		
	return $listOfOptions;
}

function existBetterOption($listOfOptions, $soldiers, $crematorioums) {
	$size = sizeof($listOfOptions);
	for($i = 0; $i < $size; $i++) {
		
		if (($listOfOptions[$i]["soldiers"] > $soldiers) && ($listOfOptions[$i]["crematorioums"] >= $crematorioums)) return true;	
	}
	return false;
}

function calculateNumberOfSeconds($tc, $soldiers, $crematorioums) {

	// initial values
	$time = 1;
	$top = 0;
	$tc["cannon"][0] = $tc["w"];
	
	while($top < $tc["h"]) {
		// trigger a Crematorium
		if (($crematorioums > 0) && ($top == $tc["h"]-1)) {
			for($i = 0; $i < $tc["h"]; $i++) {
				$tc["cannon"][$i] = 0;
			}
			$crematorioums--;
			$top = 0;
			$tc["cannon"][0] = $tc["w"];
			$time++;
			continue;			
		}
		
		// soldiers shoot
		$tc["cannon"][$top] = $tc["cannon"][$top] - $soldiers;
		if ($tc["cannon"][$top] < 0) {
			$tc["cannon"][$top-1] = $tc["cannon"][$top-1] + $tc["cannon"][$top];
			$tc["cannon"][$top] = 0;
		}				
		
		// the enemy advances
		for($i = $top+1; $i >=0 && $tc["cannon"][$i] < $tc["w"]; $i--) {
			$tc["cannon"][$i] = $tc["cannon"][$i-1];
		}
		$tc["cannon"][0] = $tc["w"];
		
		if ($tc["cannon"][$top+1]!=0) $top++;
		$time++;
	}
	return $time -1 ;
}
?>