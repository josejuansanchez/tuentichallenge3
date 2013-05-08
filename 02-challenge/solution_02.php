<?php
// José Juan Sánchez Hernández
// @josejuansanchez

readInputFromServer();

function readInputFromServer() {
	$fin = fopen("php://stdin", "r");
	// Read first comment
	fgets($fin);
	$dictionary = trim(trim(fgets($fin),"\n"));
	// Read second comment
	fgets($fin);	
	$numberOfwords = intval(fgets($fin));	
	// Read third comment
	fgets($fin);

	for($i = 0; $i < $numberOfwords; $i++ ) {
		$word = trim(trim(fgets($fin),"\n"));
		echo $word." -> ".implode(" ", searchSuggestion($dictionary,$word))."\n";
	}
	fclose($fin);	
}

function searchSuggestion($dictionary, $word) {
	$suggestion = array();
	$fp = fopen($dictionary, "r");
	while (!feof($fp)) {
    	$line = fgets($fp);
    	$line = trim($line);

		if (strlen($line) != strlen($word)) continue;		
		if (!haveSameChars($line, $word)) continue;
		if (strcmp($line, $word) == 0) continue;
		
	    $suggestion[]=$line;
	}
	fclose($fp);	
	sort($suggestion);	
	return $suggestion;
}

function haveSameChars($suggest, $word) {
	$arraySuggest = str_split($suggest);
	$arrayWord = str_split($word);
	sort($arraySuggest);
	sort($arrayWord);	
	return $arraySuggest == $arrayWord; 	
}
?>