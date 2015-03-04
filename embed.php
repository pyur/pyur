<?php

/************************************************************************/
/*  bdsx embed  v1.oo                                                   */
/************************************************************************/



  // -------------------------------- init -------------------------------- //

$title = '';
$body = '';
$ajax = FALSE;

include 'l/lib.php';
db_open();



  // ---------------- parse request URI ---------------- //

$uri_e = explode('?', $_SERVER['REQUEST_URI']);
$uri_e = explode('/', $uri_e[0]);
$mod = 'default';
$act = '';
if (count($uri_e) == 4) {
  $mod = $uri_e[2];
  }
elseif (count($uri_e) == 5) {
  $mod = $uri_e[2];
  $act = $uri_e[3];
  }

$arow = getn('row');
$acol = gets('col');




  // ---- executing module ------------------------------------------------------------------------- //

include 'm/'.$mod.'/embed.php';




  // ---- echoeing output --------------------------------------------------------------------------------- //

if (!$ajax) {
  echo '<!DOCTYPE html>'."\r\n".'<html><head>';

  echo '<title>';
  //echo ($auth['type'] ? $config['page_title'] : 'Программа');
  //$title_add = ''; // TMP
  //echo ($title_add?(' - '.$title_add):'');
  if ($title)  echo ($title);
  echo '</title>';

  echo '<meta charset="UTF-8">';
  echo '<meta name="robots" content="none">';
  echo '<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">';
  echo '<link rel="StyleSheet" type="text/css" href="/s.css">';
  echo '<script type="text/javascript" src="/j.js"></script>';
  echo '<script>var mod = "'.$mod.'";</script>';

  echo '</head><body>';
  }

echo $body;

if (!$ajax) {
  echo '</body></html>';
  }


?>