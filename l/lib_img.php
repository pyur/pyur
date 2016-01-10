<?php

/************************************************************************/
/*  image functions  v1.oo                                              */
/************************************************************************/


if (!isset($body))  die ('error: this is a pluggable module and cannot be used standalone.');




  // -------- insert <img ...> -------- //

function  img ($type, $id, $mode = '') {
    // mode:   emb,  ext,  link

  $extension = 'jpg';


  if ($type == 'foto_last') {
    $type = 'foto';
    $id = db_read(array('table'=>$type, 'col'=>'id', 'where'=>'`foto`='.$id, 'order'=>'`dates` DESC'));  //, 'limit'=>1
    }

  if ($type == 'some_doc')  $extension = 'pdf';



  $tmp = substr('000000'.dechex($id), -6,6);
  $file = 'p/'.$type.'/'.substr($tmp, 0,2).'/'.substr($tmp, 2,2).'/'.substr($tmp, 4,2).'.'.$extension;
  //if (config('port_81')) {
  //  $file_81 = $file;
  //  $file = '../wwws/'.$file;
  //  }

  if (file_exists($file)) {

    $image = fread (fopen ($file, 'rb'), filesize ($file) );

    if (!$mode) {
      if (strlen($image) < 4000)  $mode = 'emb';
      else                        $mode = 'ext';
      }

    if ($mode == 'emb') { // 1
      $image = '<img src="data:image/png;base64,'.base64_encode($image).'">';
      }

    if ($mode == 'ext') {  // 2
      $size = getimagesize($file);  //, $info
      $image = '<img '.$size[3].' src="'.$file.'">';
      }

    //if ($mode == 3)  reserved for RAW

    if ($mode == 'link') { // 4
      $image = $file;
      //if (config('port_81')) {
      //  $image = 'https://'.$_SERVER['SERVER_ADDR'].'/'.$file_81;
      //  }
      }


    return  $image;
    }

  else {
    return  false;
    }

  }




  // -------------------------------- upload image -------------------------------- //

function  img_upload ($type, $id, $data) {

  $extension = 'jpg';

  //if (!$type)  $type = 'p';  // reserved for various graphics
  //elseif ($type == 1)  $type = 'foto';
  if ($type == 'some_doc')  $extension = 'pdf';


  $tmp = substr('000000'.dechex($id), -6,6);
  $file_out = 'p/'.$type;
  $file_out .= '/'.substr($tmp, 0,2);
  if (!file_exists($file_out))  mkdir($file_out, 0757);
  $file_out .= '/'.substr($tmp, 2,2);
  if (!file_exists($file_out))  mkdir($file_out, 0757);
  $file_out .= '/'.substr($tmp, 4,2).'.'.$extension;

  //if (config('port_81')) {
  //  $file_out = '../wwws/'.$file_out;
  //  }

  fwrite (fopen ($file_out, 'wb'), $data);
  clearstatcache();
  }



  // -------------------------------- delete image -------------------------------- //

function  img_delete ($type, $id) {

  $extension = 'jpg';

  //if (!$type)  $type = 'p';  // reserved for various graphics
  //elseif ($type == 1)  $type = 'foto';
  if ($type == 'some_doc')  $extension = 'pdf';


  $tmp = substr('000000'.dechex($id), -6,6);
  $file = 'p/'.$type.'/'.substr($tmp, 0,2).'/'.substr($tmp, 2,2).'/'.substr($tmp, 4,2).'.'.$extension;

  //if (config('port_81')) {
  //  $file = '../wwws/'.$file;
  //  }

  if (file_exists($file))  unlink ($file);  
  }






  // -------- insert <img ...> with 90 deg rotated text -------- //
function  img_text ($text, $par = array() ) {

  if (!is_array($text))  $text = array($text);

  if (!is_array($par)) {
      // width - height - fontsize  - align - line-height
    $size = array(
      array(),
      array('w' => 17,  'h' => 100),                                    // 1 - ...
      array('w' => 10,  'h' => 70 , 'f' => 8 , 'a' => 1, 'l' => '8'),   // 2 - ...
      array('w' => 16,  'h' => 64 , 'f' => 6),
      array('w' => 12,  'h' => 100, 'f' => 8),                          // 4 - ...
      array('w' => 12,  'h' => 24 , 'f' => 8 , 'a' => 1),               // 5 - ...
      array('w' => 16,  'h' => 140),                                    // 6 - ...
      array('w' => 16,  'h' => 30 ,            'a' => 1),               // 7 - ...
      array('w' => 12,  'h' => 100, 'f' => 10,           'l' => '10'),  // 8 - ...
      array('w' => 16,  'h' => 40 ,            'a' => 1),               // 9 - ...
      array('w' => 10,  'h' => 70 , 'f' => 8 , 'a' => 1, 'l' => '9' ),  // 10 - ...
      array('w' => 12,  'h' => 100, 'f' => 10,           'l' => '10'),  // 11 - ...
      array('w' => 9,   'h' => 100, 'f' => 8 ,           'l' => '8' ),  // 12 - ...
      array('w' => 12,  'h' => 40 , 'f' => 10,           'l' => '10'),  // 13 - ...
      array('w' => 9,   'h' => 40 , 'f' => 8 ,           'l' => '8' ),  // 14 - ...
      );
  
    if (isset($size[$par]))  $par = $size[$par];
    else  $par = array();
    }

  $default = array('w' => 320, 'h' => 240, 'f' => 12, 'a' => 0, 'l' => 0);
  foreach ($default as $k=>$v) {
    if (!isset($par[$k]))  $par[$k] = $v;
    }

  if (!$par['l'])  $par['l'] = ($par['f'] + 2);



  $file = 'c/'.$par['w'].'-'.$par['h'].'-'.$par['f'].'-'.$par['a'].'-'.$par['l'].'-'.rawurlencode(substr(implode('-',$text),0,192));

  if (!file_exists($file)) {
  //if (1) {
      // create a new one
    $image = imagecreatetruecolor ($par['h'], $par['w']);
    $transp = imagecolorallocate ($image, 255, 255, 255);
    imagecolortransparent ($image, $transp);
    $black = imagecolorallocate ($image, 0, 0, 0);

    imagefilledrectangle ($image, 0, 0, $par['h']-1, $par['w']-1, $transp);
    //imagerectangle ($image, 0, 0, $par['h']-1, $par['w']-1, $black);

    $x = $par['l'];

    foreach ($text as $v) {
      $y = 2;

      if ($par['a'] == 1) {
        $box = imagettfbbox ($par['f'], 0, 'i/tahoma.ttf', $v);
        $y = round($par['h'] / 2) - round(($box[2] - $box[0]) / 2);
        }

      imagettftext ($image, $par['f'], 0, $y, $x, $black, 'i/tahoma.ttf', $v);
      $x += $par['l'];
      }

    $image = imagerotate ($image, 90, $black);

      // cache it & output
    imagepng ($image, $file);
    //if ($run_mode == 'pict')  imagepng ($image);
    //else  
    $image = fread (fopen ($file, 'rb'), filesize ($file) );
    }

  else {
      // read from cache
    $image = fread (fopen ($file, 'rb'), filesize ($file) );
    //if ($run_mode == 'pict')  echo  $image;
    }

  $image = '<img src="data:image/png;base64,'.base64_encode($image).'">';

  return  $image;
  }




  // -------------------------------- shrink image -------------------------------- //

function  shrink_image ($image, $required_width, $required_height) {

  $width = imagesx($image);
  $height = imagesy($image);


  $shrinked_width = $width;
  $shrinked_height = $height;
  $resize = FALSE;

  if ($shrinked_height > $required_height) {
    $shrinked_height = $required_height;
    $shrinked_width = ceil($width * ($required_height / $height));
    $resize = TRUE;
    }

  if ($shrinked_width > $required_width) {
    $shrinked_width = $required_width;
    $shrinked_height = ceil($height * ($required_width / $width));
    $resize = TRUE;
    }


  if ($resize) {
    $shrinked = imagecreatetruecolor ($shrinked_width, $shrinked_height);
    imagecopyresampled ($shrinked, $image, 0, 0, 0, 0, $shrinked_width, $shrinked_height, $width, $height);
    }

  else {
    $shrinked = $image;
    }


  return  $shrinked;
  }



  // -------------------------------- shrink image, with maintain square -------------------------------- //

function  shrink_image2 ($image, $max_largest, $max_smallest) {

  $width = imagesx($image);
  $height = imagesy($image);

  $rotated = FALSE;
  if ($width < $height) {
    $rotated = TRUE;
    $tmp = $width;
    $width = $height;
    $height = $tmp;
    }


  $shrinked_width = $width;
  $shrinked_height = $height;
  $resize = FALSE;

  if ($shrinked_height > $max_smallest) {
    $shrinked_height = $max_smallest;
    $shrinked_width = ceil($width * ($max_smallest / $height));
    $resize = TRUE;
    }

  if ($shrinked_width > $max_largest) {
    $shrinked_width = $max_largest;
    $shrinked_height = ceil($height * ($max_largest / $width));
    $resize = TRUE;
    }

  if ($rotated) {
    $tmp = $width;
    $width = $height;
    $height = $tmp;

    $tmp = $shrinked_width;
    $shrinked_width = $shrinked_height;
    $shrinked_height = $tmp;
    }


  if ($resize) {
    $shrinked = imagecreatetruecolor ($shrinked_width, $shrinked_height);
    imagecopyresampled ($shrinked, $image, 0, 0, 0, 0, $shrinked_width, $shrinked_height, $width, $height);
    }

  else {
    $shrinked = $image;
    }


  return  $shrinked;
  }




  // ---------------- file DB ---------------- //

function  img_upload_fdb ($db, $id, $data) {

  $type = '';
  if (substr($data, 0,2) == chr(255).chr(216))  $type = '.jpg';
  elseif (substr($data, 1,3) == 'PNG')          $type = '.png';
  //elseif (substr($data, 0,3) == 'GIF')          $type = '.gif';
  //else  die('error: not supported type');

  $tmp = substr('000000'.dechex($id), -6,6);
  $file_out = 'd/'.$db.'/'.substr($tmp, 0,2);
  if (!file_exists($file_out))  mkdir($file_out, 0757);
  $file_out .= '/'.substr($tmp, 2,2);
  if (!file_exists($file_out))  mkdir($file_out, 0757);
  $file_out .= '/'.substr($tmp, 4,2);

  $file_out .= $type;

  fwrite (fopen ($file_out, 'wb'), $data);
  clearstatcache();

  return  $file_out;
  }



function  img_get_fdb ($db, $id, $path = FALSE) {
  $tmp = substr('000000'.dechex($id), -6,6);
  $file = 'd/'.$db.'/'.substr($tmp, 0,2).'/'.substr($tmp, 2,2).'/'.substr($tmp, 4,2);

  $type = FALSE;
  $mime = '';
  if     (file_exists($file.'.jpg'))  {$type = '.jpg';  $mime = 'image/jpeg';}
  elseif (file_exists($file.'.png'))  {$type = '.png';  $mime = 'image/png';}
  //elseif (file_exists($file.'.gif'))  {$type = '.gif';  $mime = 'image/gif';}
  //if     (file_exists($file))         $type = '';

  if ($path) {
    return  $file.$type;
    }

  else {
    if ($type)  $file .= $type;
    $data = fread (fopen ($file, 'rb'), filesize ($file) );

    return  array('mime'=>$mime, 'data'=>$data);
    }
  }



function  img_delete_fdb ($db, $id) {

  $tmp = substr('000000'.dechex($id), -6,6);
  $file = 'd/'.$db.'/'.substr($tmp, 0,2).'/'.substr($tmp, 2,2).'/'.substr($tmp, 4,2);

  $type = FALSE;
  if     (file_exists($file.'.jpg'))  {$type = '.jpg';  $mime = 'image/jpeg';}
  elseif (file_exists($file.'.png'))  {$type = '.png';  $mime = 'image/png';}

  if ($type)  $file .= $type;

  if (file_exists($file))  unlink ($file);  
  }


?>