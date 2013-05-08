<?php
// José Juan Sánchez Hernández
// @josejuansanchez

ini_set('memory_limit', '4096M');

// Chunk size of 20 MB
define("CHUNK_SIZE", 20*1024*1024);	
define("FILE_INTEGERS", "integers");
define("FILE_CHUNKS", "chunks");
define("FILE_INFO", "info");
define("FILE_MERGE", "merge");

$numbers = readInputFromServer();
chunkAndSortFileOfIntegers();
mergeWithMissingNumbers();
checkNumbers($numbers);

function readInputFromServer() {
	$fin = fopen("php://stdin", "r");
	$totalNumber = intval(fgets($fin));	
	for($i = 0; $i < $totalNumber; $i++ ) {
		$numbers[] = intval(fgets($fin));
	}	
	fclose($fin);
	return $numbers;	
}

function readInputFile($filename) {
	$lines = file($filename);	
	$numberOfLines = intval($lines[0]);		
	for($i = 1; $i <= $numberOfLines; $i++ ) {
		$numbers[] = $lines[$i];			
	}
	return $numbers;	
}

function chunkAndSortFileOfIntegers() {
	$fin = fopen(FILE_INTEGERS, "rb");	
	$foutc = fopen(FILE_CHUNKS, "w");
	$fouti = fopen(FILE_INFO, "w");
	$offset = 0;

	while (!feof($fin)) {    	
		$data = fread($fin, CHUNK_SIZE);		
		$intList = array_merge(unpack("i*", $data));		
		sort($intList, SORT_NUMERIC);
		foreach ($intList as $value) {
			$binData .= pack("i", $value);
		}				
		fwrite($foutc, $binData);		
		unset($binData);
		
		// Min Value - Max Value - Number of elements - Total accumulated (Offset)
		$info = $intList[0]." ".$intList[sizeof($intList)-1]." ".count($intList)." ".$offset."\n";
		fputs($fouti, $info);		

		$offset = $offset + count($intList);		
	}
	fclose($fin);
	fclose($foutc);	
	fclose($fouti);		
}

function findChunksWithMissingNumbers() {
	$chunkwmn = array();
	$f = fopen(FILE_INFO, "r");
	while(!feof($f)) {
		$line = fgets($f);
		$chunkInfo = explode(" ", $line);
		$intervalSize = ($chunkInfo[1] - $chunkInfo[0]) + 1;
		if ($intervalSize != $chunkInfo[2]) {
			$chunkwmn[] = $chunkInfo;
		}
	}
	fclose($f);
	return $chunkwmn;
}

function findChunksWithoutMissingNumbers() {
	$chunkwomn = array();
	$f = fopen(FILE_INFO, "r");
	while(!feof($f)) {
		$line = fgets($f);
		$chunkInfo = explode(" ", $line);
		$intervalSize = ($chunkInfo[1] - $chunkInfo[0]) + 1;
		if ($intervalSize == $chunkInfo[2]) {
			$chunkwomn[] = $chunkInfo;
		}
	}
	fclose($f);
	return $chunkwomn;
}

function readSortedChunk($offset) {
	$f = fopen(FILE_CHUNKS, "rb");
	fseek($f, $offset);
	$data = fread($f, CHUNK_SIZE);
	fclose($f);
	$intList = array_merge(unpack("i*", $data));
	return $intList;
}

function mergeWithMissingNumbers() {	
	$chunkwmn = findChunksWithMissingNumbers();

	$intList = array();
	$temp = array();
	foreach($chunkwmn as $chunkInfo) {
		$temp = readSortedChunk($chunkInfo[3]*4);
		$intList = array_merge($intList, $temp);
		unset($temp);		
	}	
	sort($intList, SORT_NUMERIC);
	$f = fopen(FILE_MERGE, "wb");
	foreach ($intList as $value) {
		$binData .= pack("i", $value);
	}				
	fwrite($f, $binData);
	fclose($f);
}

function checkNumbers($numbers) {
	$chunkwomn = findChunksWithoutMissingNumbers();

	$intList = array();
	$f = fopen(FILE_MERGE, "r");
	while (!feof($f)) {    	
		$data = fread($f, CHUNK_SIZE);		
		$temp = array_merge(unpack("i*", $data));
		$intList = array_merge($intList, $temp);		
	}
	fclose($f);

	$listmn = array();		
	$size = sizeof($intList);
	for($i = 0; $i <= $size-2; $i++) {
		if ($intList[$i+1] > $intList[$i] + 1) {

			$found = false;
			foreach($chunkwomn as $chunkInfo) {
				if (($intList[$i] + 1 >= $chunkInfo[0]) && ($intList[$i] + 1 <= $chunkInfo[1])) {
					$found = true;
					break;				
				}
			}

			if ($found) continue;

			$found = in_array($intList[$i] + 1, $intList);
		
			if (!$found) {
				$listmn[] = $intList[$i] + 1;	
			}
		}			
	}		
	
	foreach($numbers as $n) {
		echo $listmn[$n-1]."\n";
	}	
}
?>