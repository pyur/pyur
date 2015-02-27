<?php

/************************************************************************/
/*  functions for page parsing  v1.oo                                   */
/************************************************************************/


if (!isset($body))  die ('error: this is a pluggable module and cannot be used standalone.');



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



  // -------------------------------- global search -------------------------------- //

function  pos_begin_gl($tag) {
  global  $receive, $skip;
  $pos = strpos($receive, $tag, $skip);
  if ($pos !== FALSE)  $skip = $pos;
  return  $pos;
  }



function  pos_after_gl($tag) {
  global  $receive, $skip;
  $pos = strpos($receive, $tag, $skip);
  if ($pos !== FALSE) { $pos += strlen($tag);  $skip = $pos; }
  return  $pos;
  }



function  cut_between_gl($tagb, $tage) {
  global  $receive, $skip;

  $posb = strpos($receive, $tagb, $skip);

  if ($posb === FALSE)  return  FALSE;

  $posb += strlen($tagb);

  $skip = $posb;

  $pose = strpos($receive, $tage, $skip);

  if ($pose === FALSE)  return  FALSE;

  $cut = substr($receive, $posb, $pose-$posb);

  $skip = $pose + strlen($tage);

  return  $cut;
  }




  // -------------------------------- local search -------------------------------- //

function  pos_begin($string, $tag, &$skip) {
  $pos = strpos($string, $tag, $skip);
  if ($pos !== FALSE)  $skip = $pos;
  return  $pos;
  }


function  pos_after($string, $tag, &$skip) {
  $pos = strpos($string, $tag, $skip);
  if ($pos !== FALSE) { $pos += strlen($tag);  $skip = $pos; }
  return  $pos;
  }


function  cut_between($string, $tagb, $tage, &$skip) {

  if ($tagb)  $posb = strpos($string, $tagb, $skip);
  else        $posb = 0;

  if ($posb === FALSE)  return  FALSE;

  $posb += strlen($tagb);

  $skip = $posb;

  $pose = strpos($string, $tage, $skip);

  if ($pose === FALSE)  return  FALSE;

  $cut = substr($string, $posb, $pose-$posb);

  $skip = $pose + strlen($tage);

  return  $cut;
  }


function  pos_div_end($string, $skip) {

  $nest = 1;
  while ($nest) {
    $posb = strpos($string, '<div', $skip);
    $pose = strpos($string, '</div', $skip);

    if ($posb === FALSE)  $posb = $pose;
    if ($pose === FALSE)  return  FALSE;

    if ($posb < $pose)  { $nest++;  $skip = $posb + 4; }
    else                { $nest--;  $skip = $pose + 5; }
    }

  return  $pose;
  }



    // ---------------- dechunk ---------------- //

function  http_dechunk(&$receive) {

  if (($header_end = strpos($receive, "\r\n\r\n")) === FALSE)  return;

  $http_header = substr($receive, 0, $header_end);


  if (strpos($http_header, 'Transfer-Encoding: chunked') === FALSE
   && strpos($http_header, 'Transfer-Encoding:  chunked') === FALSE
      ) {
    $receive = substr($receive, $header_end+4);
    }  // not chunked

  else {
    $receive_dechunked = '';
    $pos_size = $header_end +4;

    while (1) {
      $pos_content = strpos($receive, "\r\n", $pos_size);
      if ($pos_content === FALSE)  break;
      $pos_content += 2;

      $content_size = hexdec(substr($receive, $pos_size, $pos_content-$pos_size));

      if (!isset($receive[$pos_content]))  break;

      $receive_dechunked .= substr($receive, $pos_content, $content_size);

      $pos_size = $pos_content + $content_size;
      }

    //d($receive_dechunked);
    $receive = $receive_dechunked;
    }  // chunked

  if (strpos($http_header, 'Content-Encoding: gzip') !== FALSE) {
    $receive = gzdecode($receive);
    }

  }




    // ---------------- find_phone ---------------- //

$find_phone = array(
  '8-903',
  '8-904',
  '8-905',
  '8-906',
  '8-908',
  '8-909',
  '8-910',
  '8 910',
  '8-915',
  '8-919',
  '8-920',
  '8-929',
  '8-950',
  '8-951',
  '8-952',
  '8-953',
  '8-960',
  '8-961',
  '8-962',
  '8-980',

  '8903',
  '8904',
  '8905',
  '8906',
  '8908',
  '8909',
  '8910',
  '8910',
  '8915',
  '8919',
  '8920',
  '8929',
  '8950',
  '8951',
  '8952',
  '8953',
  '8960',
  '8961',
  '8962',
  '8980',

  '903',
  '904',
  '905',
  '906',
  '908',
  '909',
  '910',
  '910',
  '915',
  '919',
  '920',
  '929',
  '950',
  '951',
  '952',
  '953',
  '960',
  '961',
  '962',
  '980',

  //'908-',
  //'920-',
  //'8952',
  //'8915',
  '8 910',

  '48-',
  '37-',
  '41-',
  '42-',
  '44-',
  //'',
  );

$find_phone_nums = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '+', '-', '(', ')');

function  find_phone($text) {
  global  $find_phone, $find_phone_nums;

  $phone = '';
  foreach ($find_phone as $v) {
    if (($pos = strpos($text, $v)) !== FALSE) {
      while (isset($text[$pos]) && in_array($text[$pos], $find_phone_nums)) {
        $phone .= $text[$pos];
        $pos++;
        }
      break;
      }
    }

  return  $phone;
  }


?>