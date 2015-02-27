<?php

/************************************************************************/
/*  initialization  v1.oo                                           бом */
/************************************************************************/


$cache = 'c/mcache.php';
if (file_exists($cache))  die();



  // --------------------------------- modules cache --------------------------------- //

$body = '';
$var_export = '';
$var_export .= '<?php'."\r\n";


  // ---- read dir ---- //
$modules = array();
$dh = opendir('m');
$n = 0;
while (($file = readdir($dh)) !== false) {
  if ($file == '.' || $file == '..')  continue;

  $module = array(//'num' => $n++,
                  //'dir' => $file,
                  'name' => '',
                  'nameb' => '',
                  'acc' => 0,  // безусловно выводить для [auth:state]
                  //'vis' => TRUE,  // visibility
                  'opt' => 0,  // 1 - visibility, 2 - custom button
                  'perm' => array(),
                  'pos' => 50,
                  );

  include 'm/'.$file.'/init.php';

  if (!$module['nameb'])  $module['nameb'] = $module['name'];
  if (file_exists('m/'.$file.'/button.php'))  $module['opt'] |= 2;
  //if (file_exists('m/'.$mod.'/style.css'))
  //if (file_exists('m/'.$mod.'/script.js'))

  $modules[$file] = $module;
  }
closedir($dh);


$var_export .= '$modules = '.var_export($modules, true).';';



  // -------------------------------- generate sprite -------------------------------- //

  // -------- read subicons dir -------- //

$path = 'i/fugue';
$dh = opendir($path);

$dir = array();
while (($file = readdir($dh)) !== FALSE) {
  if ($file == '.' || $file == '..')  continue;
  $dir[] = $file;
  }



$micon_w = 32;
$micon_h = 32;
$msprite_w = (count($modules) > 32) ? ($micon_w * 32) : ($micon_w * count($modules));
$msprite_h = (floor(count($modules) / 32)+1) * $micon_h;

$icon_w = 16;
$icon_h = 16;
$sprite_w = (count($dir) > 64) ? ($icon_w * 64) : ($icon_w * count($dir));
$sprite_h = (floor(count($dir) / 64)+1) * $icon_h;

if ($msprite_w > $sprite_w)  $sprite_w = $msprite_w;
$sprite_h += $msprite_h;

$sprite = imagecreatetruecolor($sprite_w, $sprite_h);
imagealphablending($sprite, false);
imagesavealpha($sprite, true);
$transp = imagecolorallocatealpha ($sprite, 255, 255, 255, 127);
imagefilledrectangle ($sprite,  0, 0,  $sprite_w, $sprite_h,  $transp);


//$image = imagecreatetruecolor(count($modules)*32, 32);
//imagealphablending($image, false);
//imagesavealpha($image, true);
//$transp = imagecolorallocatealpha ($image, 255, 255, 255, 127);
//imagefilledrectangle($image, 0, 0, (count($modules)*32)-1, 31, $transp);

$num = 0;
foreach ($modules as $k=>$v) {
  $file = 'm/'.$k.'/icon.png';
  if (file_exists($file))  $img = imagecreatefrompng($file);
  else                     $img = imagecreatefrompng('i/na.png');

  $x = ($num % 32) * $micon_w;
  $y = floor($num / 32) * $micon_h;
  imagecopy($sprite, $img,  $x, $y,  0, 0,  $micon_w, $micon_h);
  $num++;
  }

//$file = 'c/s.png';
//imagepng($image, $file);





  // -------------------------------- generate subicons -------------------------------- //


  // -------- generate sprite -------- //

$submenu_icons = array();
foreach ($dir as $k=>$v) {
  $file = $path.'/'.$v;

  $img = imagecreatefrompng($file);
  $w = imagesx($img);
  $h = imagesy($img);
  if ($w > 16)  $w = 16;
  if ($h > 16)  $h = 16;
  $x = ($k % 64) * $icon_w;
  $y = floor($k / 64) * $icon_h  + $msprite_h;
  imagecopy($sprite, $img,  $x, $y,  0, 0,  $w, $h);

    // ---- names ---- //
  $name = explode('.', $v);
  array_pop($name);
  $name = implode('.', $name);
  $submenu_icons[] = $name;
  }

imagepng($sprite, 'c/s.png', 9);
clearstatcache();

//fwrite (fopen ('c/i.cache', 'wb'), json_encode($submenu_icons) );
//clearstatcache();

$var_export .= "\r\n".'$submenu_icons = '.var_export($submenu_icons, true).';';



$var_export .= "\r\n".'?>';

fwrite( fopen($cache, 'wb'), $var_export);
clearstatcache();


?>