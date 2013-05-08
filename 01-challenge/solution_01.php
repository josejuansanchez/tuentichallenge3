<?php
// José Juan Sánchez Hernández
// @josejuansanchez

global $exchangeRates; 
global $maxBugdet;

readInputFromServer();

function readInputFromServer() {
	global $exchangeRates;
	global $maxBugdet;

	$fin = fopen("php://stdin", "r");
	$line = fgets($fin);
	for($i = 1; $i <= intval($line); $i = $i + 1) {
		$initialBudget = intval(fgets($fin));
		$exchangeRates = array_map('intval', explode(' ', fgets($fin)));
		$maxBugdet = $initialBudget;
		calculateMaximumAmountOfEuros($initialBudget, 0, 0, sizeof($exchangeRates), 0);
		echo $maxBugdet."\n";
	}
	fclose($fin);
}

function calculateMaximumAmountOfEuros($budget, $bitcoins, $position, $size, $purchasePrice) {
	global $exchangeRates;
	global $maxBugdet;

	if ($position >= $size) return;
	
	if (($budget >= $exchangeRates[$position]) && ($exchangeRates[$position] < $exchangeRates[$position + 1])) {
		// Buy Bitcoins
		$bitcoins = $bitcoins + intval($budget / $exchangeRates[$position]);
		$budget = $budget % $exchangeRates[$position];
		$purchasePrice = $exchangeRates[$position];	
	}
	
	if (($bitcoins > 0) && ($exchangeRates[$position] > $purchasePrice) && ($exchangeRates[$position] > $exchangeRates[$position + 1])){
		// Sell Bitcoins
		$budget = $budget + ($bitcoins * $exchangeRates[$position]);
		$bitcoins = 0;			
		if ($budget > $maxBugdet) $maxBugdet = $budget;
		$purchasePrice = 0;
	}

	calculateMaximumAmountOfEuros($budget, $bitcoins, $position+1, $size, $purchasePrice);	
}
?>