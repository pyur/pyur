<?php

/************************************************************************/
/*  resources for cache  v1.oo                                      бом */
/************************************************************************/



$file = 'j.js';
$js = fread (fopen ($file, 'rb'), filesize ($file) );

$file = 's.css';
$css = fread (fopen ($file, 'rb'), filesize ($file) );

$file = 'c/s.png';
$sprite = fread (fopen ($file, 'rb'), filesize ($file) );

$css = strtr($css, array('/*~' => '', '~*/' => '', '~*~' => base64_encode($sprite) ) );


echo 'localStorage.js = \''.strtr($js, array('\''=>'\\\'', "\r"=>'\\r', "\n"=>'\\n') ).'\';'."\r\n";

echo 'localStorage.css = \''.strtr($css, array('\''=>'\\\'', "\r"=>'\\r', "\n"=>'\\n') ).'\';'."\r\n";

echo 'localStorage.v = v;'."\r\n";

echo 'location.reload(true);'."\r\n";
//echo 'alert(\'Stored successfully\');'."\r\n";


?>