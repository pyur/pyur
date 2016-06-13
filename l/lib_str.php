<?php

/************************************************************************/
/*  string functions                                                    */
/************************************************************************/


if (!isset($body))  die ('error: this is a pluggable module and cannot be used standalone.');



  // ---- body buffering ----------------------------------------------------------------------- //

function b($b = '<br>') {
  global $body;
  $body .= $b;
  }


  // ---- GET, POST getting, and smart 'stripping & parsing' ----------------------------------- //
  // ---- GET (obsloleted) ---------------- //
function get($a, $b = '') {
  if (isset($_GET[$a]))  $b = $_GET[$a];
  // TODO: smart 'stripping & parsing'
  return $b;
  }

  // ---- GET number ---------------- //
function getn($a, $b = 0) {
  if (isset($_GET[$a])) {
      // smart 'stripping & parsing'
    $parse = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
    $b = str_split($_GET[$a]);
    $c = '';
    foreach ($b as $v)  if (in_array($v, $parse))  $c .= $v;
    if (!$c)  $c = 0;
    $b = $c;
    }
  //else {
  //  $b = FALSE;
  //  }

  return  $b;
  }

  // ---- GET hex ---------------- //
function geth($a, $b = 0) {
  if (isset($_GET[$a])) {
    $parse = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f');
    $c = str_split($_GET[$a]);
    $b = '';
    foreach ($c as $v)  if (in_array($v, $parse))  $b .= $v;
    if (!$b)  $b = 0;
    }
  return  $b;
  }


  // ---- GET string ---------------- //
function gets($a, $b = '') {
  if (isset($_GET[$a])) {
      // smart 'stripping & parsing'
    $parse = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9',
                   'a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z',
                   '%', '+', '-', '_', '.'
                   );
    $b = str_split(strtolower($_GET[$a]));
    $c = '';
    foreach ($b as $v)  if (in_array($v, $parse))  $c .= $v;
    $b = $c;
    }

  return  $b;
  }

  // ---- GET val set or not set ---------------- //
function getb($a) {
  return isset($_GET[$a])?TRUE:FALSE;
  }



  // ---- POST ---------------- //
function postr($a, $c = 0, $b = '') {
  if (isset($_POST[$a]))  $b = $_POST[$a];
  return $b;
  }


function post($a, $c = 0, $b = '') {
  if (isset($_POST[$a]))  $b = $_POST[$a];

    // -- /" => " -- //
  $b = strtr($b, array(chr(92).'"'=>'"') );

    // smart 'stripping & parsing'
  //? убрать все, кроме разрешённых, символы
  
  //  // замена кавычек
  //$b = strtr($b, array(chr(92).'"'=>'"') );
  //$first = 1;
  //while (mb_strpos($b, '"') !== FALSE) {
  //  //echo $b.'<br>';
  //  $b = mb_substr($b, 0, mb_strpos($b, '"') ).($first?'«':'»').mb_substr($b, mb_strpos($b, '"') +1 );
  //  //echo $b.'<br>';
  //  //echo '<br>';
  //  $first = 0;
  //  }

    // -- замена кавычек 2.0 -- //
  $infl = array('0','1','2','3','4','5','6','7','8','9',
                'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
                'a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z',
                'А','Б','В','Г','Д','Е','Ё','Ж','З','И','Й','К','Л','М','Н','О','П','Р','С','Т','У','Ф','Х','Ц','Ч','Ш','Щ','Ъ','Ы','Ь','Э','Ю','Я',
                'а','б','в','г','д','е','ё','ж','з','и','й','к','л','м','н','о','п','р','с','т','у','ф','х','ц','ч','ш','щ','ъ','ы','ь','э','ю','я',
                '"','«','»'
                );

  $quote = 0;
  while (($pos = mb_strpos($b, '"')) !== FALSE) {

    if ($pos)  $left = mb_substr($b, $pos-1, 1);
    else       $left = ' ';

    if (mb_substr($b, $pos+1, 1))  $right = mb_substr($b, $pos+1, 1);
    else                           $right = ' ';


    if     ($left == ' ' && $right != ' ')  $quote = 0;
    elseif ($left != ' ' && $right == ' ')  $quote = 1;
    elseif (!in_array($left, $infl) && in_array($right, $infl))  $quote = 0;
    elseif (in_array($left, $infl) && !in_array($right, $infl))  $quote = 1;
    else  $quote ^= 1;


    $b = mb_substr($b, 0, $pos ).($quote?'»':'«').mb_substr($b, $pos + 1);
    }


    // -- remove trailing spaces and spaces  at begin -- //
  $b = trim($b);

    // -- char limiting -- //
  if ($c)  $b = mb_substr($b, 0, $c);

  return $b;
  }

function postn($a, $c = 0, $b = '') {
  return  (int)post($a, $c, $b);
  }

function postb($a) {
  return  (isset($_POST[$a])?TRUE:FALSE);
  }

// htmlspecialchars() // only  & " ' < >
// htmlentities()     // all
// get_html_translation_table()
//echo '<pre>';
//echo print_r(get_html_translation_table());
//echo print_r(get_html_translation_table(HTML_ENTITIES));
//echo '</pre>';


  // ---- filter functions ---- //
function filter($text, $filter) {
  $len = mb_strlen($text);
  $out = '';
  for ($i = 0; $i < $len; $i++) {
    $chr = mb_substr($text, $i, 1);
    if (in_array($chr, $filter))  $out .= $chr;
    }
  return  $out;
  }

function filter_rlns($text) {
  $filter = array('0','1','2','3','4','5','6','7','8','9',
                  'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
                  'a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z',
                  'А','Б','В','Г','Д','Е','Ё','Ж','З','И','Й','К','Л','М','Н','О','П','Р','С','Т','У','Ф','Х','Ц','Ч','Ш','Щ','Ъ','Ы','Ь','Э','Ю','Я',
                  'а','б','в','г','д','е','ё','ж','з','и','й','к','л','м','н','о','п','р','с','т','у','ф','х','ц','ч','ш','щ','ъ','ы','ь','э','ю','я',
                  ' ','!','#','$','%','&','(',')','*','+',',','-','.','/',':',';','<','=','>','?','@','[',']','^','_','`','{','|','}','~',
                  //" ' \
                  );
  $text = filter($text, $filter);
  return  $text;
  }

function filter_lns($text) {
  $filter = array('0','1','2','3','4','5','6','7','8','9',
                  'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
                  'a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z',
                  ' ','!','#','$','%','&','(',')','*','+',',','-','.','/',':',';','<','=','>','?','@','[',']','^','_','`','{','|','}','~',
                  );
  $text = filter($text, $filter);
  return  $text;
  }

function filter_ln($text) {
  $filter = array('0','1','2','3','4','5','6','7','8','9',
                  'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
                  'a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z',
                  );
  $text = filter($text, $filter);
  return  $text;
  }

function filter_url($text) {
  $filter = array('0','1','2','3','4','5','6','7','8','9',  // RFC 3986 section 2.3 Unreserved Characters (January 2005)
                  'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
                  'a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z',
                  '-','.','_','~'
                  // $-_.+!*'(),
                  // !*'();:@&=+$,/?#[]  RFC 3986 section 2.2 Reserved Characters (January 2005)
                  // {}| unknown state
                  );
  $text = filter($text, $filter);
  return  $text;
  }

function filter_n($text) {
  $filter = array('0','1','2','3','4','5','6','7','8','9');
  $text = filter($text, $filter);
  return  $text;
  }

function filter_nf($text) {
  $filter = array('0','1','2','3','4','5','6','7','8','9','.',',');
  $text = filter($text, $filter);
  return  $text;
  }

function filter_h($text) {
  $filter = array('0','1','2','3','4','5','6','7','8','9', 'A','B','C','D','E','F', 'a','b','c','d','e','f');
  $text = filter($text, $filter);
  return  $text;
  }

function filter_phone($text) {
  $filter = array('0','1','2','3','4','5','6','7','8','9',
                  ' ','(',')','+',',','-',
                  );
  $text = filter($text, $filter);
  return  $text;
  }

function filter_phonen($text) {
  $filter = array('0','1','2','3','4','5','6','7','8','9');
  $text = filter($text, $filter);
  if (strlen($text) > 11)  $text = substr($text, 0, 11);
  if (strlen($text) == 11) {
    if     ($text[0] == '8' && ($text[1] == '9' || $text[1] == '4'))  $text = substr($text, 1);
    elseif ($text[0] == '7' && ($text[1] == '9' || $text[1] == '4'))  $text = substr($text, 1);
    }
  if (strlen($text) > 10)  $text = substr($text, 0, 10);
  return  $text;
  }


  // ---- COOKIE number ---------------- //
function cookien($a, $b = 0) {
  if (isset($_COOKIE[$a])) {
      // smart 'stripping & parsing'
    $parse = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
    $b = str_split($_COOKIE[$a]);
    $c = '';
    foreach ($b as $v)  if (in_array($v, $parse))  $c .= $v;
    if (!$c)  $c = 0;
    $b = $c;
    }

  return  $b;
  }

  // ---- COOKIE hex ---------------- //
function cookieh($a, $b = 0) {
  if (isset($_COOKIE[$a])) {
    $parse = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f');
    $c = str_split($_COOKIE[$a]);
    $b = '';
    foreach ($c as $v)  if (in_array($v, $parse))  $b .= $v;
    if (!$b)  $b = 0;
    }

  return  $b;
  }

  // ---- COOKIE string ---------------- //
function cookies($a, $b = '') {
  if (isset($_COOKIE[$a])) {
      // smart 'stripping & parsing'
    $parse = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9',
                   'a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z',
                   '%', '+', '-', '_', '.'
                   );
    $b = str_split(strtolower($_COOKIE[$a]));
    $c = '';
    foreach ($b as $v)  if (in_array($v, $parse))  $c .= $v;
    $b = $c;
    }

  return  $b;
  }

  // ---- COOKIE val set or not set ---------------- //
function cookieb($a) {
  return isset($_COOKIE[$a])?TRUE:FALSE;
  }




  // -------------------------------- phonen -------------------------------- //

//function  phonen($item_phone) {
//  $nums = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
//
//  $phonen = '';
//  if ($item_phone) {
//    for ($i = 0; $i < strlen($item_phone); $i++) {
//      if (in_array($item_phone[$i], $nums))  $phonen .= $item_phone[$i];
//      }
//
//    if     (strlen($phonen) > 11)  $phonen = substr($phonen, 0, 11);
//
//    if     (strlen($phonen) == 11 && substr($phonen, 0, 2) == '89')  $phonen = substr($phonen, 1);
//    elseif (strlen($phonen) == 11 && substr($phonen, 0, 2) == '79')  $phonen = substr($phonen, 1);
//    elseif (strlen($phonen) == 11 && substr($phonen, 0, 2) == '84')  $phonen = substr($phonen, 1);
//    elseif (strlen($phonen) == 11 && substr($phonen, 0, 2) == '74')  $phonen = substr($phonen, 1);
//    }
//
//  return  $phonen;
//  }


  // -------------------------------- make phone human readable -------------------------------- //
  // -------------------------------- rederive dashes in phone number -------------------------------- //

function  phoned($item_phone) {

  if ( strpos($item_phone, '-') !== FALSE )  return  $item_phone;



  if (strlen($item_phone) == 11) {

      // 8-915-575-55-50, 7-915-575-55-50
    if (substr($item_phone, -10,1) == '9') {

      $item_phone =
                  substr($item_phone, -11,1).
                  '-'.
                  substr($item_phone, -10,3).
                  '-'.
                  substr($item_phone, -7,3).
                  '-'.
                  substr($item_phone, -4,2).
                  '-'.
                  substr($item_phone, -2,2);
      }

    elseif (substr($item_phone, -10,5) == '47241') {
      // 8-47241-2-36-46 - губкин
      $item_phone =
                  substr($item_phone, -11,1).
                  '-'.
                  substr($item_phone, -10,5).
                  '-'.
                  substr($item_phone, -5,1).
                  '-'.
                  substr($item_phone, -4,2).
                  '-'.
                  substr($item_phone, -2,2);
      }

    else {
      // 8-4722-37-42-67 - белгород, страрый оскол
      $item_phone =
                  substr($item_phone, -11,1).
                  '-'.
                  substr($item_phone, -10,4).
                  '-'.
                  substr($item_phone, -6,2).
                  '-'.
                  substr($item_phone, -4,2).
                  '-'.
                  substr($item_phone, -2,2);
      }

    }


  elseif (strlen($item_phone) == 10) {
    // 915-575-55-50
    if (substr($item_phone, -10,1) == '9') {
      $item_phone =
                  substr($item_phone, -10,3).
                  '-'.
                  substr($item_phone, -7,3).
                  '-'.
                  substr($item_phone, -4,2).
                  '-'.
                  substr($item_phone, -2,2);
      }

    elseif (substr($item_phone, -10,2) == '49') {
      // 495-580-91-92, 499-504-43-06 - москва
      $item_phone =
                  substr($item_phone, -10,3).
                  '-'.
                  substr($item_phone, -7,3).
                  '-'.
                  substr($item_phone, -4,2).
                  '-'.
                  substr($item_phone, -2,2);
      }

    elseif (substr($item_phone, -10,5) == '47241') {
      // 47241-2-36-46 - губкин
      $item_phone =
                  substr($item_phone, -10,5).
                  '-'.
                  substr($item_phone, -5,1).
                  '-'.
                  substr($item_phone, -4,2).
                  '-'.
                  substr($item_phone, -2,2);
      }

    else {
      // 4722-37-42-67 - белгород, страрый оскол
      $item_phone =
                  substr($item_phone, -10,4).
                  '-'.
                  substr($item_phone, -6,2).
                  '-'.
                  substr($item_phone, -4,2).
                  '-'.
                  substr($item_phone, -2,2);
      }

    }


  elseif (strlen($item_phone) == 7) {
    // 232-27-86 - воронеж
    $item_phone = 
                  substr($item_phone, -7,3).
                  '-'.
                  substr($item_phone, -4,2).
                  '-'.
                  substr($item_phone, -2,2);
    }

  elseif (strlen($item_phone) == 6) {
    $item_phone = 
                  substr($item_phone, -6,2).
                  '-'.
                  substr($item_phone, -4,2).
                  '-'.
                  substr($item_phone, -2,2);
    }

  elseif (strlen($item_phone) == 5) {
    $item_phone = 
                  substr($item_phone, -5,1).
                  '-'.
                  substr($item_phone, -4,2).
                  '-'.
                  substr($item_phone, -2,2);
    }


    // ---------------- по два номера ---------------- //

  elseif (strlen($item_phone) == 12) {
    // 43-30-80, 43-30-04
    $item_phone = 
                  substr($item_phone, -12,2).
                  '-'.
                  substr($item_phone, -10,2).
                  '-'.
                  substr($item_phone, -8,2).
                  ', '.
                  substr($item_phone, -6,2).
                  '-'.
                  substr($item_phone, -4,2).
                  '-'.
                  substr($item_phone, -2,2);
    }

  elseif (strlen($item_phone) == 17) {

    // 55-44-27, 8-919-226-91-57
    if (substr($item_phone, -11,2) == '89') {
      $item_phone = 
                  substr($item_phone, -17,2).
                  '-'.
                  substr($item_phone, -15,2).
                  '-'.
                  substr($item_phone, -13,2).
                 ', '.
                  substr($item_phone, -11,1).
                  '-'.
                  substr($item_phone, -10,3).
                  '-'.
                  substr($item_phone, -7,3).
                  '-'.
                  substr($item_phone, -4,2).
                  '-'.
                  substr($item_phone, -2,2);
      }

    // 8-919-226-91-57, 55-44-27
    if (substr($item_phone, 0,2) == '89' || substr($item_phone, 0,2) == '79') {
      $item_phone = 
                  substr($item_phone, -17,1).
                  '-'.
                  substr($item_phone, -16,3).
                  '-'.
                  substr($item_phone, -13,3).
                  '-'.
                  substr($item_phone, -10,2).
                  '-'.
                  substr($item_phone, -8,2).
                 ', '.
                  substr($item_phone, -6,2).
                  '-'.
                  substr($item_phone, -4,2).
                  '-'.
                  substr($item_phone, -2,2);
      }
    }

  elseif (strlen($item_phone) == 21) {

    if (substr($item_phone, -21,2) == '89') {
      // 8-910-360-03-63, 4-722-33-05-00
      $item_phone = 
                  substr($item_phone, -21,1).
                  '-'.
                  substr($item_phone, -20,3).
                  '-'.
                  substr($item_phone, -17,3).
                  '-'.
                  substr($item_phone, -14,2).
                  '-'.
                  substr($item_phone, -12,2).
                  ', '.
                  substr($item_phone, -10,1).
                  '-'.
                  substr($item_phone, -9,3).
                  '-'.
                  substr($item_phone, -6,2).
                  '-'.
                  substr($item_phone, -4,2).
                  '-'.
                  substr($item_phone, -2,2);
      }
    }

  elseif (strlen($item_phone) == 22) {
    // 8-915-562-57-80, 8-951-147-11-53
    $item_phone = 
                  substr($item_phone, -22,1).
                  '-'.
                  substr($item_phone, -21,3).
                  '-'.
                  substr($item_phone, -18,3).
                  '-'.
                  substr($item_phone, -15,2).
                  '-'.
                  substr($item_phone, -13,2).
                  ', '.
                  substr($item_phone, -11,1).
                  '-'.
                  substr($item_phone, -10,3).
                  '-'.
                  substr($item_phone, -7,3).
                  '-'.
                  substr($item_phone, -4,2).
                  '-'.
                  substr($item_phone, -2,2);
    }


    // ---------------- по два номера ---------------- //

  elseif (strlen($item_phone) == 33) {
    // 8-961-172-03-07, 8-915-570-40-97, 8-919-438-39-99
    $item_phone = 
                  substr($item_phone, -33,1).
                  '-'.
                  substr($item_phone, -32,3).
                  '-'.
                  substr($item_phone, -29,3).
                  '-'.
                  substr($item_phone, -26,2).
                  '-'.
                  substr($item_phone, -24,2).
                  ', '.
                  substr($item_phone, -22,1).
                  '-'.
                  substr($item_phone, -21,3).
                  '-'.
                  substr($item_phone, -18,3).
                  '-'.
                  substr($item_phone, -15,2).
                  '-'.
                  substr($item_phone, -13,2).
                  ', '.
                  substr($item_phone, -11,1).
                  '-'.
                  substr($item_phone, -10,3).
                  '-'.
                  substr($item_phone, -7,3).
                  '-'.
                  substr($item_phone, -4,2).
                  '-'.
                  substr($item_phone, -2,2);
    }

  return  $item_phone;
  }




  // -------------------------------- fractional number human readable -------------------------------- //

function  frach($num) {
  $r = floor($num /100);
  if ($num % 100)  $r .= ','.substr($num,-2,2);
  return  $r;
  }



  // -------------------------------- fractional number to int_f -------------------------------- //

function  fraci($num) {
  $num = explode('.', strtr(filter_nf($num), array(','=>'.')));
  $r = $num[0]*100 + (isset($num[1]) ? substr($num[1].'00',0,2) : 0);
  return  $r;
  }


  // -------------------------------- fractional number (triple) -------------------------------- //

function  fth($num) {
  $r = floor($num /1000);
  if ($num % 1000)  $r .= ','.substr($num,-3,3);
  return  $r;
  }


function  fti($num) {
  $num = explode('.', strtr(filter_nf($num), array(','=>'.')));
  $r = $num[0]*1000 + (isset($num[1]) ? substr($num[1].'000',0,3) : 0);
  return  $r;
  }




  // -------------------------------- geo convert -------------------------------- //

function  geoai($lat) {
  return  round(($lat + 90)* 93206.75);
  }
function  geooi($lon) {
  return  round(($lon + 180)* 46603.375);
  }

function  geoaf($lati) {
  return  round($lati / 93206.75 - 90, 5);
  }
function  geoof($loni) {
  return  round($loni / 46603.375 - 180, 5);
  }




  // -------------------------------- reset default values -------------------------------- //

function  dfs(&$s) {
  return (isset($s) ? $s : '');
  }

function  dfi(&$i) {
  return (isset($i) ? (int)$i : 0);
  }

function  dfa(&$i) {
  return (isset($i) ? $i : array());
  }




  // -------------------------------- referer -------------------------------- //

function  ref($k) {
  $uri_e = explode('?', $_SERVER['HTTP_REFERER']);
  if (isset($uri_e[1]))  $query = $uri_e[1];  else  $query = FALSE;

  $uri_e = explode('/', $uri_e[0]);

  if ($k)  return $uri_e[$k];

  $uri_e['q'] = $query;
  return  $uri_e;
  }




  // -------------------------------- fio -------------------------------- //

  // ---- fio short ---- //
function fios($surname, $name, $otchestvo, $surnamef=FALSE, $nickname=FALSE) {
  $r = array();

  if ($surname) {
    if ($surnamef)  $r[] = '<abbr class="u" title="'.$surnamef.'">'.$surname.'</abbr>';
    else  $r[] = $surname;
    }

  if ($name) {
    if ($nickname)  $r[] = '<abbr class="u" title="'.$nickname.'">'.mb_substr($name, 0, 1).'.</abbr>';
    else  $r[] = mb_substr($name, 0, 1).'.';
    }

  if ($otchestvo) {
    $r[] = mb_substr($otchestvo, 0, 1).'.';
    }

  return  implode(' ', $r);
  }

  // ---- fio full ---- //
function fiof($surname, $name, $otchestvo, $surnamef=FALSE, $nickname=FALSE) {
  $r = array();

  if ($surname) {
    if ($surnamef)  $r[] = '<abbr class="u" title="'.$surnamef.'">'.$surname.'</abbr>';
    else  $r[] = $surname;
    }

  if ($name) {
    if ($nickname)  $r[] = '<abbr class="u" title="'.$nickname.'">'.$name.'</abbr>';
    else  $r[] = $name;
    }

  if ($otchestvo) {
    $r[] = $otchestvo;
    }

  return  implode(' ', $r);
  }




  // -------------------------------- padej -------------------------------- //

  // ---- padej ending ---- //
function pend($n, $r='') {

  if (is_array($r)) {
    $e = $r;
    }

  else {
                               //     0       1       2,3,4  (кроме 11,12,13,14)
        if ($r == 'year')  $e = array('лет',  'год',  'года'); // именительный падеж
    elseif ($r == 'yeara') $e = array('лет',  'года', 'лет');  // родительный падеж
    elseif ($r == 'mid')   $e = array('й',    'е',    'я');
    elseif ($r == '1')     $e = array('ь',    'я',    'и');
    elseif ($r == '2')     $e = array('ов',   '',     'а');
    elseif ($r == 'daya')  $e = array('дней', 'день', 'дня');  // родительный падеж
    else                   $e = array('',     '',     '');
    }


  if     (substr($n,-2,2) == '11' || substr($n,-2,2) == '12' || substr($n,-2,2) == '13' || substr($n,-2,2) == '14')  $n = $e[0];
  elseif (substr($n,-1,1) == '1')  $n = $e[1];
  elseif (substr($n,-1,1) == '2' || substr($n,-1,1) == '3' || substr($n,-1,1) == '4')  $n = $e[2];
  else  $n = $e[0];

  return $n;
  }




  // -------------------------------- UNHEX -------------------------------- //

function unhex($hexstr) {
  // terminate if size odd
  $hexstr = strtolower($hexstr);

  $hex = array('0'=>0, '1'=>1, '2'=>2, '3'=>3, '4'=>4, '5'=>5, '6'=>6, '7'=>7, '8'=>8, '9'=>9, 'a'=>10, 'b'=>11, 'c'=>12, 'd'=>13, 'e'=>14, 'f'=>15);

  $out = '';

  $size = strlen($hexstr);
  for ($i = 0; $i < $size; $i+=2) {
    $out .= chr( ($hex[$hexstr[$i]] << 4) + $hex[$hexstr[$i+1]] );
    }

  return  $out;
  }


function tohex($str) {
  $hexH = array(0=>'0', 16=>'1', 32=>'2', 48=>'3', 64=>'4', 80=>'5', 96=>'6', 112=>'7', 128=>'8', 144=>'9', 160=>'a', 176=>'b', 192=>'c', 208=>'d', 224=>'e', 240=>'f');
  $hexL = array(0=>'0', 1=>'1', 2=>'2', 3=>'3', 4=>'4', 5=>'5', 6=>'6', 7=>'7', 8=>'8', 9=>'9', 10=>'a', 11=>'b', 12=>'c', 13=>'d', 14=>'e', 15=>'f');

  $out = '';

  $size = strlen($str);
  for ($i = 0; $i < $size; $i++) {
    $byte = ord($str[$i]);
    $out .= $hexH[$byte & 0xF0];
    $out .= $hexL[$byte & 0xF];
    }

  return  $out;
  }


function php($unpacked) {
  $unpacked = filter_phonen($unpacked);

  $out = '';
  //for ($i = 0; $i < 12; $i++) {
  for ($i = 0; $i < 10; $i++) {
    if (isset($unpacked[$i]))  $byte = (int)$unpacked[$i] << 4;
    else $byte = 240;
    $i++;
    if (isset($unpacked[$i]))  $byte += (int)$unpacked[$i];
    else $byte += 15;
    $out .= chr($byte);
    }
  //echo dh($out);
  return  $out;
  }


function phu($packed) {
  static $hexH = FALSE;
  static $hexL = FALSE;

  if ($hexH === FALSE)  $hexH = array(0=>'0', 16=>'1', 32=>'2', 48=>'3', 64=>'4', 80=>'5', 96=>'6', 112=>'7', 128=>'8', 144=>'9', 160=>'!', 176=>'!', 192=>'!', 208=>'!', 224=>'!', 240=>'');
  if ($hexL === FALSE)  $hexL = array(0=>'0', 1=>'1', 2=>'2', 3=>'3', 4=>'4', 5=>'5', 6=>'6', 7=>'7', 8=>'8', 9=>'9', 10=>'!', 11=>'!', 12=>'!', 13=>'!', 14=>'!', 15=>'');

  $out = '';
  //for ($i = 0; $i < 6; $i++) {
  for ($i = 0; $i < 5; $i++) {
    $byte = ord($packed[$i]);
    $out .= $hexH[$byte & 0xF0];
    $out .= $hexL[$byte & 0xF];
    }

  return  $out;
  }


function cut($str, $len) {
  return  mb_substr($str, 0, $len);
  }


function condence_space(&$str) {
  while(($pos = strpos($str, '  ')) !== FALSE)  $str = strtr($str, array('  '=>' '));
  }



?>