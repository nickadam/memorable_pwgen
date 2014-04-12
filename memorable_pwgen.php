<?php
/*
Memorable Password Generator
@nickadam
*/
$generated = array();
//using posix words
$words="/usr/share/dict/words";

$word_file = fopen($words, "r");
//find the end of this file
fseek($word_file, "-1", SEEK_END);
$end=ftell($word_file);


function get_short_word($word_file, $end){
        //pick a random point in the file, note the two fgets may fail if the pointer ends up at the end
        $word = '';
	while($word == '' or strlen($word)>8){ //it's super inneficient to looks for short words this way
		$seek=rand(0, $end);
        	fseek($word_file, $seek);
        	fgets($word_file); //set pointer at next line
        	$word=strtolower(trim(fgets($word_file)));
	}
	return $word;
}

function define_word($word){
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
$word = array();
while(empty($word)){
	$word = define_word(get_short_word($word_file, $end));
}
print_r($word);

//while(count($generated) < 3){
//}
//print_r($generated);
?>
