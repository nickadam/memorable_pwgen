<?php
/*
 ██████╗██╗     ███████╗ █████╗ ███╗   ██╗                                 
██╔════╝██║     ██╔════╝██╔══██╗████╗  ██║                                 
██║     ██║     █████╗  ███████║██╔██╗ ██║                                 
██║     ██║     ██╔══╝  ██╔══██║██║╚██╗██║                                 
╚██████╗███████╗███████╗██║  ██║██║ ╚████║                                 
 ╚═════╝╚══════╝╚══════╝╚═╝  ╚═╝╚═╝  ╚═══╝                                 
                                                                           
██████╗ ██╗ ██████╗████████╗██╗ ██████╗ ███╗   ██╗ █████╗ ██████╗ ██╗   ██╗
██╔══██╗██║██╔════╝╚══██╔══╝██║██╔═══██╗████╗  ██║██╔══██╗██╔══██╗╚██╗ ██╔╝
██║  ██║██║██║        ██║   ██║██║   ██║██╔██╗ ██║███████║██████╔╝ ╚████╔╝ 
██║  ██║██║██║        ██║   ██║██║   ██║██║╚██╗██║██╔══██║██╔══██╗  ╚██╔╝  
██████╔╝██║╚██████╗   ██║   ██║╚██████╔╝██║ ╚████║██║  ██║██║  ██║   ██║   
╚═════╝ ╚═╝ ╚═════╝   ╚═╝   ╚═╝ ╚═════╝ ╚═╝  ╚═══╝╚═╝  ╚═╝╚═╝  ╚═╝   ╚═╝   
                                                                           
@nickadam

This will clean this guys file 
https://raw2.github.com/sujithps/Dictionary/master/Oxford%20English%20Dictionary.txt
into something I can use for this the memorable password generator

*/

if(!(isset($argv[1]) and is_file($argv[1]))){
	die("Usage: php clean_dictionary.php {Dictionary File}\n");
}

$dict = array();
$file = $argv[1];
$file_handle = fopen($file, 'r');
while(!feof($file_handle)){
	$line = str_replace(array("\r", "\n"), "", fgets($file_handle));
	//this line starts with a letter
	if(preg_match("/^[a-z]/i", $line)){ 
		//this line has a common part of speech
		if(preg_match("/( |-)(n\.|v\.|adj\.|adv\.)/", $line, $match, PREG_OFFSET_CAPTURE)){
			$speech = $match[2][0];
			//this line can be sperated by a double space
			if(preg_match("/^([^ ]*)  (.*)/", $line, $match, PREG_OFFSET_CAPTURE)){
				$word = preg_replace("/\d/", "", $match[1][0]);
				$definition = $match[2][0];
			}
			$dict[$speech][] = array(
				'word' => $word,
				'speech' => $speech,
				'definition' => $definition
				);
			//echo "$word\n";
			//echo "$word\n$part\n$definition\n\n";
		}
	}
}

$speech_parts = array("n.", "v.", "adj.", "adv.");
$n = 1;
$pw = array();
while($n <= 3 ){
	$n++;
	$speech = $speech_parts[array_rand($speech_parts)];
	$pw[] = $dict[$speech][array_rand($dict[$speech])];
}
$words = '';
$exclude = array();
foreach($pw as $word){
	$excluded = strtolower(substr($word['word'], 0, 1));
	$exclude[$excluded] = true;
	$words .= $word['word'];
}
$words = preg_replace("/[^a-z]/", "", strtolower($words));
//echo $words."\n";
$chars = array();
foreach(str_split($words) as $char){
	if(!isset($chars[$char])){$chars[$char] = 0;}
	$chars[$char]++;
}
foreach($chars as $letter => $count){
	if(!isset($exclude[$letter]) and $count == 1){
		$capitalize = $letter;
		break;
	}
}

foreach($pw as $word){
	echo str_replace($capitalize, strtoupper($capitalize), strtolower($word['word'])).' ';
}
echo "\n";

?>