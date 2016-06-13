<?php

/************************************************************************/
/*  bdsx  v1.oo                                                         */
/*  © 2016+ Пресняков Юрий Евгеньевич. Все права защищены.              */
/************************************************************************/



  // -------------------------------- init -------------------------------- //

//ini_set('display_errors', 0);
//ini_set('log_errors', 1);
//ini_set('error_log', 'r/error.log');

$benchmark = array('_start' => microtime(TRUE));

$title = '';
$body = '';
$redirect = '';
$ajax = FALSE;


include 'l/lib.php';



  // ---------------- parse request URI ---------------- //

$uri_e = explode('?', $_SERVER['REQUEST_URI']);
$uri_e = explode('/', $uri_e[0]);
$mod = 'default';
$act = '';
if (count($uri_e) == 3) {
  $mod = $uri_e[1];
  }
elseif (count($uri_e) == 4) {
  $mod = $uri_e[1];
  $act = $uri_e[2];
  }

$arow = getn('row');
$acol = gets('col');




  // -------------------------------- authorization -------------------------------- //

include 'auth.php';



  // -------------------------------- append menu -------------------------------- //

b('<div class="menu">');

$sort = array();
foreach ($auth['menu'] as $k=>$v)  $sort[$v['sort']] = $k;
ksort($sort);

foreach ($sort as $k) {
  $v = $auth['menu'][$k];
  if ($v['opt'] & 1)  continue;

  //if ($v['opt'] & 2) {
  //  include 'm/'.$v['dir'].'/button.php';
  //  }

  b('<a class="menu" href="/'.$k.'/">');
  b('<div class="menub" unselectable="on"');
  //if ($v['butcol']) {
  //  $butcol = explode(',', $v['butcol']);
  //  b(' style="border-color: '.$butcol[1].'; background-color: '.$butcol[0].';"');
  //  }
  b('>');

  $x = ($v['icon'] % 32) * 32;
  $y = floor($v['icon'] / 32) * 32;
  b('<div class="menui" unselectable="on" style="background-position: '.($x?('-'.$x.'px'):'0').' '.($y?('-'.$y.'px'):'0').';"></div>');

  b('<div class="menut">');
  b($v['nameb']);
  b('</div>');

  b('</div>');
  b('</a>');
  }

b('</div>');

unset($auth['menu'], $sort);
unset($k, $v, $x, $y);

$body_menu = $body;
$body = '';




  // ---- executing module's main.php ------------------------------------------------------------------------- //

include 'm/'.$mod.'/main.php';




  // ---- echoeing output --------------------------------------------------------------------------------- //

if (!$ajax) {
  echo '<!DOCTYPE html>'."\r\n".'<html><head>';

  echo '<title>';
  //echo ($auth['type'] ? $config['page_title'] : 'Программа');
  //$title_add = ''; // TMP
  //echo ($title_add?(' - '.$title_add):'');
  echo '</title>';

  echo '<meta charset="UTF-8">';
  if ($redirect)  echo '<meta http-equiv="Refresh" content="0; url='.$redirect.'">';
  echo '<meta name="robots" content="none">';
  echo '<meta http-equiv="cache-control" content="no-store">';
  echo '<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">';
  echo '<script>if (localStorage.v != (v=1)) {var u = document.createElement(\'SCRIPT\');  u.src = \'/res.php\';  document.head.appendChild(u);}</script>';
  echo '<script>var css = document.createElement(\'STYLE\');  css.innerHTML = localStorage.css;  document.head.appendChild(css);</script>'."\r\n";
  echo '<script>eval(localStorage.js);</script>';
  echo '<script>var mod = \''.$mod.'\';</script>';
  //if (file_exists('m/'.$mod.'/style.css'))  echo '<link rel="StyleSheet" type="text/css" href="m/'.$mod.'/style.css">';
  //if (file_exists('m/'.$mod.'/script.js'))  echo '<script type="text/javascript" src="m/'.$mod.'/script.js"></script>';
  echo '</head><body>';

  echo $body_menu;
  }


echo $body;


//if (p())  benchmark();

if (!$ajax) {
  echo '</body></html>';
  }


?>