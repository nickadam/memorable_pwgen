<?php
/*

███╗   ███╗███████╗███╗   ███╗ ██████╗ ██████╗  █████╗ ██████╗ ██╗     ███████╗
████╗ ████║██╔════╝████╗ ████║██╔═══██╗██╔══██╗██╔══██╗██╔══██╗██║     ██╔════╝
██╔████╔██║█████╗  ██╔████╔██║██║   ██║██████╔╝███████║██████╔╝██║     █████╗  
██║╚██╔╝██║██╔══╝  ██║╚██╔╝██║██║   ██║██╔══██╗██╔══██║██╔══██╗██║     ██╔══╝  
██║ ╚═╝ ██║███████╗██║ ╚═╝ ██║╚██████╔╝██║  ██║██║  ██║██████╔╝███████╗███████╗
╚═╝     ╚═╝╚══════╝╚═╝     ╚═╝ ╚═════╝ ╚═╝  ╚═╝╚═╝  ╚═╝╚═════╝ ╚══════╝╚══════╝
                                                                               
██████╗  █████╗ ███████╗███████╗██╗    ██╗ ██████╗ ██████╗ ██████╗             
██╔══██╗██╔══██╗██╔════╝██╔════╝██║    ██║██╔═══██╗██╔══██╗██╔══██╗            
██████╔╝███████║███████╗███████╗██║ █╗ ██║██║   ██║██████╔╝██║  ██║            
██╔═══╝ ██╔══██║╚════██║╚════██║██║███╗██║██║   ██║██╔══██╗██║  ██║            
██║     ██║  ██║███████║███████║╚███╔███╔╝╚██████╔╝██║  ██║██████╔╝            
╚═╝     ╚═╝  ╚═╝╚══════╝╚══════╝ ╚══╝╚══╝  ╚═════╝ ╚═╝  ╚═╝╚═════╝             
                                                                               
 ██████╗ ███████╗███╗   ██╗███████╗██████╗  █████╗ ████████╗ ██████╗ ██████╗   
██╔════╝ ██╔════╝████╗  ██║██╔════╝██╔══██╗██╔══██╗╚══██╔══╝██╔═══██╗██╔══██╗  
██║  ███╗█████╗  ██╔██╗ ██║█████╗  ██████╔╝███████║   ██║   ██║   ██║██████╔╝  
██║   ██║██╔══╝  ██║╚██╗██║██╔══╝  ██╔══██╗██╔══██║   ██║   ██║   ██║██╔══██╗  
╚██████╔╝███████╗██║ ╚████║███████╗██║  ██║██║  ██║   ██║   ╚██████╔╝██║  ██║  
 ╚═════╝ ╚══════╝╚═╝  ╚═══╝╚══════╝╚═╝  ╚═╝╚═╝  ╚═╝   ╚═╝    ╚═════╝ ╚═╝  ╚═╝  
                                                                               

@nickadam

*/

//using posix words
$word_file="/usr/share/dict/words";

//used for testing stuff like the definition part
define('DEBUG', true);

function get_short_word_array($word_file){
	if(!is_resource($word_file)){die("get_short_word_array requires a resource.\n");}
	$words = array();
	while(!feof($word_file)){
		$line = str_replace(array("\r", "\n"), "", fgets($word_file));
		$length = strlen($line);
		if($length <= 8 and $length >=3 ){
			$words[] = $line;
		}
	}
	return $words;
}

function get_word($words){
	if(!is_array($words)){die("get_word requires an array of words.\n");}
	$word = $words[array_rand($words)];
	return $word;
}

function define_word($word){
	if(DEBUG){echo "Looking for definition of \"$word\"\n";}
	$definition = array();
	$result = '';
	//go find the defenition of the word from dict.org
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "dict://dict.org/d:$word");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$raw_result = curl_exec($ch);

	//loop through each line until you find the first set of definitions
	$raw_result = explode("\n", $raw_result);
	$capture = false;
	foreach($raw_result as $line){
		if($capture and preg_match("/^[0-9]/", $line)){ $capture = false; }
		if($capture){ $result = "$result\n$line"; }
		if(preg_match("/^151/", $line)){ $capture = true; }
	}
	$find_speech_part = preg_replace("/[^a-z \.]/i", "", $result); 
	if(preg_match("/\b(a\.|n|v|adj|adv)\b/i", $find_speech_part, $m, PREG_OFFSET_CAPTURE)){
		$speech_part = $m[1][0];
		$definition['word'] = $word;
		$definition['speech_part'] = $speech_part;
		$definition['result'] = $result;
		return $definition;
	}
}


$word_file = fopen($word_file, "r");
$words = get_short_word_array($word_file);
$this_password = array();
while(count($this_password) < 3){
	$word = array();
	while(empty($word)){
		$word = define_word(get_word($words));
	}
	$this_password[$word['speech_part']] = $word;
}
foreach($this_password as $speech_part => $word){
	$this_word = $word['word'];
	echo "$this_word ($speech_part.) ";
}
echo "\n";

?>
