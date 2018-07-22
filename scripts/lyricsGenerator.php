<?php

$text = $_POST['lyrics'];
$text = trim($text, " \t");
$text = str_replace(",", "@,", $text);
$text = str_replace(".", "@.", $text);
$text = str_replace("?", "@?", $text);
$text = str_replace("!", "@!", $text);
$text = str_replace(" ", "@", $text);
$wordArr = explode("@", $text);

$order = array();

foreach($wordArr as $word){
  $order[] = $word;
}

$apikey = "BPfvxgDc6aRJVJ7F5naR";
$language = "en_US";
$endpoint = "http://thesaurus.altervista.org/thesaurus/v1";
$synonyms = array();
foreach($wordArr as $lyric){  

  if(in_array($lyric, array(".","?","!"))){
    continue;
  }

  $ch = curl_init(); 
  curl_setopt($ch, CURLOPT_URL, "$endpoint?word=".urlencode($lyric)."&language=$language&key=$apikey&output=json"); 
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
  $data = curl_exec($ch); 
  $info = curl_getinfo($ch); 
  curl_close($ch);
  $data = json_decode($data, true)['response'];

  if(is_array($data)){
    foreach($data as $list){
      foreach($list as $words){
        $words = explode("|", $words['synonyms']);
        $synonym = $words[array_rand($words)];
        $synonym = preg_replace("/\([^)]+\)/","", $synonym);
        $synonym = trim($synonym, " \t.");
        $synonym = strtolower($synonym);
        $synonyms[$lyric] = $synonym;
      }
    }
  }

  
}

$ret = array();
foreach($order as $original_lyric){
  if(isset($synonyms[$original_lyric])){
    $ret[] = $synonyms[$original_lyric];
  }else{
    $ret[] = $original_lyric;
  }
}

$text = implode(" ", $ret);
$text = str_replace(" ,", ",", $text);
$text = str_replace(" .", ".", $text);
$text = str_replace(" ?", "?", $text);
$text = str_replace(" !", "!", $text);

$text = preg_replace('/([.!?])\s*(\w)/e', "strtoupper('\\1 \\2')", ucfirst(strtolower($text)));

echo $text;

?>
