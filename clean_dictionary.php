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
			$dict[] = array(
				'word' => $word,
				'speech' => $speech,
				'definition' => $definition
				);
			//echo "$word\n";
			//echo "$word\n$part\n$definition\n\n";
		}
	}
}
print_r($dict);
?>