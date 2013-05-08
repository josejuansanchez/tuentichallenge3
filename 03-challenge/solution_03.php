<?php
// José Juan Sánchez Hernández
// @josejuansanchez

readInputFromServer();

function readInputFromServer() {
	$fin = fopen("php://stdin", "r");
	$numberOfscripts = intval(fgets($fin));	
	for($i = 0; $i < $numberOfscripts; $i++ ) {
		$script = preg_split('/([.|>|<])/', trim(fgets($fin),"\n"), -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
		$keys = getUniqueScenesName($script);
		$script = compressScenesName($script, $keys);				
		echo checkScript($script, $keys)."\n";		
	}	
	fclose($fin);	
}

function checkScript($script, $keys) {
	$orderedScenes = array();
	$size = sizeof($script);
	
	// Get the ordered scenes
	for($i = 0; $i < $size; $i++) {
		if ($script[$i] == '.') {
			$orderedScenes[]=$script[$i+1];
		}			
	}
	
	if (countNumberOfRepetitions($orderedScenes)) return "invalid";		
	
	$scenes = array();
	$n = 0;
	for($i = 0; $i < $size; $i++) {
		switch($script[$i]) {
			case '.': 
				$scenes[]=$script[$i+1]; 
				$n++;
				break;

			case '<': 
				$scenes = insertAtPosition($scenes, $n-1, $script[$i+1]);
				$n++; 
				break;

			case '>': 
				$scenes = insertAtPosition($scenes, $n+1, $script[$i+1]);
				$n++; 
				break;				
		}		
	}

	$scenes = removeConsecutiveRepetitions($scenes);
	array_values($scenes);
	
	if (!isValid($orderedScenes, $scenes)) return "invalid";	
	if (countNumberOfRepetitions($scenes)) return "valid";	
	$decompress = decompressScenesName($scenes, $keys);
	return implode(",", $decompress);
}

function insertAtPosition($array, $position, $val) {
    $temp = array();
    $size = sizeof($array);
    $temp = array_slice($array, 0, $position);
    array_push($temp, $val);
    $temp = array_merge($temp, array_slice($array, $position, $size));
	return $temp;
}

function isValid($orderedScenes, $scenes) {
	$sizeOrderedScenes = sizeof($orderedScenes);
	$sizeScenes = sizeof($scenes);
	$pos = -1;
	for($i = 0; $i < $sizeOrderedScenes; $i++) {
		$count = 0;
		for($j = 0; $j < $sizeScenes; $j++) {
			if ($orderedScenes[$i] == $scenes[$j]) {
				$count++;
				if ($pos > $j) return false;
				$pos = $j;
			}
		}
		if ($count > 1) return false;			
	}
	return true;		
}

function countNumberOfRepetitions($array) {
	$countRepetitions = array_count_values($array);
    foreach($countRepetitions as $value) {
		if ($value > 1) return true;
    }
    return false;
}

function removeConsecutiveRepetitions($array) {
	$size = sizeof($array);
	$i = 0;
	while($i < $size-1) {
		if ($array[$i] == $array[$i+1]) {
			unset($array[$i]);
		} else {
			$i++;
		}		
	}
	return $array;
}

function getUniqueScenesName($script) {
	$unique = array_unique($script, SORT_STRING);
	return removeSymbols($unique);
}

function removeSymbols($script) {
	$i = 0;
	foreach($script as $value) {
		if (($value != '.') && ($value != '>') && ($value != '<')) {
			$remove[$i++] = $value;		
		}
	}
	return $remove;
}

function compressScenesName($script, $keys) {
	$rename = array();
	$size = sizeof($script);
	$sizeKeys = sizeof($keys);	
	for($i = 0; $i < $size; $i++) {
		if (($script[$i] != '.') && ($script[$i] != '>') && ($script[$i] != '<')) {
			for($j = 0; $j < $sizeKeys; $j++) {
				if (strcmp($keys[$j],$script[$i]) == 0) {
					$rename[$i] = strval($j);
					break;		
				}			
			}
		} else {
			$rename[$i] = $script[$i];
		}
	}
	return $rename;	
}

function decompressScenesName($script, $keys) {
	$rename = array();
	foreach($script as $s) {
		$rename[] = $keys[$s];			
	}
	return $rename;
}
?>