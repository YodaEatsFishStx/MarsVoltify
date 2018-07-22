<?php

function getSynonyms($word){
  $hex = bin2hex($word);
  $word = hex2bin($hex);
  ob_start();
  foreach(array("n", "v", "a", "r") as $type){
    $command = "/usr/bin/wn $word -syns$type | grep -v -e '^$'";
    passthru($command);;
  }
  $result = ob_get_clean();

  if(!$result){
    return array();
  }

  $result = explode("\n", $result);
  
  foreach($result as $i => &$r){
    if(strpos($r, "Synonyms/") !== false){
      unset ($result[$i]);
      continue;
    }else if(1 === preg_match('~[0-9]~', $r)){
      unset ($result[$i]);
      continue;
    }else if(strpos($r, "-") !== false){
      unset ($result[$i]);
      continue;
    }else if(strpos($r, "(") !== false){
      unset ($result[$i]);
      continue;
    }else if ($r == ""){
      unset ($result[$i]);
      continue;
    }else{
      $r = preg_replace('/\s+/', '', $r);
      $r = str_replace("=>", "", $r);
    }
  }

  $result = implode(",", $result);
  $result = explode(",", $result);
  $result = array_flip($result);
  unset ($result[$word]);
  $result = array_flip($result);
  $result = array_values($result);
  return $result;
}

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

$synonyms = array();
foreach($wordArr as $lyric){  

  if(in_array($lyric, array(".","?","!"))){
    continue;
  }

  $matches = getSynonyms($lyric);
  if(count($matches) > 0){
    $synonym = $matches[array_rand($matches)];
    $synonyms[$lyric] = $synonym;
  }
}

unset($synonyms['a']);
unset($synonyms['it']);
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
