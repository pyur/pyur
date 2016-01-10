<?php

/************************************************************************/
/*  interface functions                                                 */
/************************************************************************/


if (!isset($body))  die ('error: this is a pluggable module and cannot be used standalone.');



  // -------------------------------- submenu v2.5 -------------------------------- //



$submenu = array();
$modules_h = (floor(count($modules) / 32)+1) * 32;

function submenu() {  //$subm
  global $submenu;
  global $submenu_icons;
  global $modules_h;
  if (!$submenu)  return;

  b('<div class="smenu">');

  $n = 1;
  foreach ($submenu as $k=>$v) {
    $href = '';
    $blank = FALSE;
    $onclick = '';
    $ctx = FALSE;
    $cnf = FALSE;
    $ke = explode(';', $k);

    if ($ke[0][0] == '#') {
      $ke[0] = substr($ke[0],1);
      $onclick = $v;
      }

    elseif ($ke[0][0] == '?') {
      $ke[0] = substr($ke[0],1);
      $cnf = TRUE;
      }

    else {

      if (is_array($v)) {
        $ctx = TRUE;
        }

      else {
        $href = $v;
        if ($ke[0][0] == '!') {
          $ke[0] = substr($ke[0],1);
          $blank = TRUE;
          }
        }
      }


      // ---- pop-up ---- //
    if ($ctx) {
      b('<div class="smenuo">');

      b('<div class="smenuc" id="submenu'.$n.'" style="display: none;">');

        // ---- cols ---- //
      b('<div class="smenucc">');

      $row = 0;
      foreach ($v as $ck=>$cv) {

        if ($row >= 20) {
          b('</div>');
          b('<div class="smenucc">');
          $row = 0;
          }

        $chref = '';
        $cblank = FALSE;
        $conclick = '';
        $cke = explode(';', $ck);

        if ($cke[0][0] == '#') {
          $conclick = $cv;
          $cke[0] = substr($cke[0],1);
          }

        else {
          $chref = $cv;
          if ($cke[0][0] == '!') {
            $cblank = TRUE;
            $cke[0] = substr($cke[0],1);
            }
          }


        b('<div class="smenucr">');
        if ($chref)  b('<a class="smenu" href="'.$chref.'"'.($cblank?' target="_blank"':'').'>');
        b('<div class="smenucb" unselectable="on"');
        // styles
        if ($conclick)  b(' onclick="'.$conclick.'"');
        b('>');

        if (isset($cke[1]) && $cke[1]) {
          $i = array_search($cke[1], $submenu_icons);
          if ($i === FALSE)  $i = array_search('na', $submenu_icons);
          if ($i !== FALSE) {
            $x = ($i % 64) * 16;
            $y = floor($i / 64) * 16  + $modules_h;
            b('<div class="smenui" unselectable="on" style="background-position: '.($x?('-'.$x.'px'):'0').' '.($y?('-'.$y.'px'):'0').';"></div>');
            }
          }

        b('<div class="smenut" unselectable="on">');
        b($cke[0]);
        b('</div>');

        b('</div>');  // smenucb
        if ($chref)  b('</a>');
        b('</div>');  // smenucr

        $row++;
        }

      b('</div>');    // smenuсc
      b('</div>');    // smenuc
      //b('</div>');    // smenuo - moved further

      $onclick = 'var a = $.id(\'submenu'.$n.'\');  if (a.style.display == \'none\')  a.style.display=\'block\'; else a.style.display=\'none\'; $.nc_hide(\'submenu'.$n.'\');';
      }
      // ---- end: pop-up ---- //


      // ---- confirm ---- //
    if ($cnf) {
      if (!is_array($v))  $v = array('Подтвердить' => $v);

      b('<div class="smenuo">');
      b('<div class="smenuc" id="submenu'.$n.'" style="display: none;">');

      foreach ($v as $ck=>$cv) {
        $chref = '';
        $cblank = FALSE;
        $conclick = '';
        $cke = explode(';', $ck);

        if ($cke[0][0] == '#') {
          $conclick = $cv;
          $cke[0] = substr($cke[0],1);
          }

        else {
          $chref = $cv;
          if ($cke[0][0] == '!') {
            $cblank = TRUE;
            $cke[0] = substr($cke[0],1);
            }
          }

        if ($chref)  b('<a class="smenu" href="'.$chref.'"'.($cblank?' target="_blank"':'').'>');
        b('<div class="smenub" unselectable="on"');
        // styles
        if ($conclick)  b(' onclick="'.$conclick.'"');
        b('>');

        if (isset($cke[1]) && $cke[1]) {
          $i = array_search($cke[1], $submenu_icons);
          if ($i === FALSE)  $i = array_search('na', $submenu_icons);
          if ($i !== FALSE) {
            $x = ($i % 64) * 16;
            $y = floor($i / 64) * 16  + $modules_h;
            b('<div class="smenui" unselectable="on" style="background-position: '.($x?('-'.$x.'px'):'0').' '.($y?('-'.$y.'px'):'0').';"></div>');
            }
          }

        b('<div class="smenut" unselectable="on">');
        b($cke[0]);
        b('</div>');

        b('</div>');  // smenucb
        if ($chref)  b('</a>');
        //b('</div>');  // smenucr
        }

      b('</div>');    // smenuc

      $onclick = 'var a = $.id(\'submenu'.$n.'\');  if (a.style.display == \'none\')  a.style.display=\'block\'; else a.style.display=\'none\'; $.nc_hide(\'submenu'.$n.'\');';
      }
      // ---- end: confirm ---- //


    if ($href)  b('<a class="smenu" href="'.$href.'"'.($blank?' target="_blank"':'').'>');

    b('<div class="smenub" unselectable="on"');
    // styles
    if ($onclick)  b(' onclick="'.$onclick.'"');
    b('>');


    if (isset($ke[1]) && $ke[1]) {
      $i = array_search($ke[1], $submenu_icons);
      if ($i === FALSE)  $i = array_search('na', $submenu_icons);
      if ($i !== FALSE) {
        $x = ($i % 64) * 16;
        $y = floor($i / 64) * 16  + $modules_h;
        b('<div class="smenui" unselectable="on" style="background-position: '.($x?('-'.$x.'px'):'0').' '.($y?('-'.$y.'px'):'0').';"></div>');
        }
      }

    b('<div class="smenut" unselectable="on">');
    b($ke[0]);
    if ($ctx)  b(' &#x25BC;');
    b('</div>');

    b('</div>');  // smenub
    if ($href)  b('</a>');

    if ($ctx || $cnf)  b('</div>');  // smenuo
    $n++;
    }

  b('</div>');
  }




  // -------------------------------- css for table -------------------------------- //

function  css_table ($p, $name='lst') {

  $pn = array();
  $prev = 0;
  foreach ($p as $k=>$v) {
    if (!is_array($v)) { $p = array(1=>$p);  break; }
    if ($k === '#')  continue;

    $pn[$prev] = $k;
    $prev = $k;
    }


  b('<style>'."\n");

  foreach ($p as $kk=>$vv) {

    if ($kk === '#') {
      $n = 1;
      foreach ($vv as $k=>$v) {
        if ($v)  b('table.'.$name.' td:nth-child('.($n).') {white-space: nowrap; overflow: hidden;}'."\n");
        $n++;
        }
      continue;
      }

    if ($kk != 1) {
      $min = ($kk ? ((int)$kk - 20) : 0);
      $max = (isset($pn[$kk]) ? ($pn[$kk] - 21) : 0);
      $tmp = array();
      if ($min)  $tmp[] = '(min-width: '.$min.'px)';
      if ($max)  $tmp[] = '(max-width: '.$max.'px)';
      b('@media '.implode(' and ', $tmp).' {'."\n");
      }

    $n = 1;
    foreach ($vv as $k=>$v) {
      $align = '';
      $padding = '';

      if (is_string($v)) {
        $ve = explode(',', $v);

        if ($ve[0][0] == 'f') {
          b('table.'.$name.' {font-size: '.substr($ve[0],1).'pt;}'."\n");
          continue;
          }

        if ($ve[0][0] == 'p') {
          b('table.'.$name.' td {padding: 0 0 0 '.substr($ve[0],1).'px;}'."\n");
          continue;
          }

        $align = ' text-align: center;';
        $padding = ' padding: 0;';
        if (isset($ve[1]))  $padding = ' padding: '.$ve[1].';';
        $v = $ve[0];
        }

      b('table.'.$name.' td:nth-child('.($n).') {'. ( $v ? ('min-width: '.$v.'px; max-width: '.$v.'px;') : ('display: none;') )    .$align.$padding.'}'."\n");
      $n++;
      }

    if ($kk != 1) {
      b('}'."\n");
      }
    }

  b('</style>');
  }




  // -------------------------------- css icons -------------------------------- //

function  icon ($icon) {

  if (is_array($icon)) {
    global $submenu_icons;
    global $modules_h;

    b('<style>'."\n");

    foreach ($icon as $v) {
      $w = FALSE;
      $h = FALSE;
      if (is_array($v)) {
        $w = $v[1];
        if (isset($v[2]))  $h = $v[2];
        $v = $v[0];
        }

      $i = array_search($v, $submenu_icons);
      if ($i !== FALSE) {
        $x = ($i % 64) * 16;
        $y = floor($i / 64) * 16  + $modules_h;

        b('hr.'.$v.' {background-position: '.($x?('-'.$x.'px'):'0').' '.($y?('-'.$y.'px'):'0').';');
        if ($w)  b(' width: '.$w.'px; height: '.($h?$h:$w).'px;');
        b('}'."\n");
        }
      }

    b('</style>');
    }


  else {
    return  '<hr class="'.$icon.'">';
    }
  }


function  icona ($p, $px = 'i') {
  static $n;
  static $m;

  if (!isset($n[$px]))  $n[$px] = -1;
  if (!isset($m[$px]))  $m[$px] = 0;


  if (is_array($p)) {
    global $submenu_icons;
    global $modules_h;

    b('<style>'."\n");

    foreach ($p as $k=>$v) {
      $i = array_search($v, $submenu_icons);
      if ($i !== FALSE) {
        $x = ($i % 64) * 16;
        $y = floor($i / 64) * 16  + $modules_h;
        b('a.'.$px.$k.' {display: inline-block; width: 16px; height: 16px; margin: 0 2px 0 0; vertical-align: bottom; background-image: url(\'/c/s.png\'); background-position: '.($x?('-'.$x.'px'):'0').' '.($y?('-'.$y.'px'):'0').';}'."\n");
        }
      }

    b('</style>');
    $m[$px] = count($p);
    }

  else {
    $n[$px]++;
    $r = '';
    if ($p[0] == '!') {
      $p = substr($p, 1);
      $r = '<a class="'.$px.($n[$px]%$m[$px]).'" href="'.$p.'" target="_blank"></a>';
      }
    elseif ($p[0] == '#') {
      $p = substr($p, 1);
      $r = '<a class="'.$px.($n[$px]%$m[$px]).'" onclick="'.$p.'"></a>';
      }
    else {
      $r = '<a class="'.$px.($n[$px]%$m[$px]).'" href="'.$p.'"></a>';
      }
    return $r;
    }
  }




  // -------------------------------- form elements -------------------------------- //

  // ---------------- select date ---------------- //

function  form_dt ($n, $dt='0000-00-00 00:00:00') {
  global $curr;

  $o = '';

  $yb = $curr['year'] -10;
  $ye = $curr['year'] +2;
  $y0 = 0;
  $m0 = 0;
  $d0 = 0;
  $yf = 0;
  $mf = 0;
  $df = 0;
  $hf = 0;

  while (1) {
    if ($n[0][0] == '@') {$yf = 1;  $n[0] = substr($n[0], 1);  continue;}
    if ($n[0][0] == '!') {$y0 = 1;  $n[0] = substr($n[0], 1);  continue;}
    break;
    }

  $yx = explode(';', $n[0]);
  $y = $yx[0];
  if (isset($yx[1]))  $yb = $yx[1];
  if (isset($yx[2]))  $ye = $yx[2];

  while (1) {
    if ($n[1][0] == '@') {$mf = 1;  $n[1] = substr($n[1], 1);  continue;}
    if ($n[1][0] == '!') {$m0 = 1;  $n[1] = substr($n[1], 1);  continue;}
    break;
    }
  $m = $n[1];

  while (1) {
    if ($n[2][0] == '@') {$df = 1;  $n[2] = substr($n[2], 1);  continue;}
    if ($n[2][0] == '!') {$d0 = 1;  $n[2] = substr($n[2], 1);  continue;}
    break;
    }
  $d = $n[2];

  if (isset($n[3])) {
    if ($n[3][0] == '@') {$hf = 1;  $n[3] = substr($n[3], 1);}
    $h = $n[3];
    $i = $n[4];
    $s = $n[5];
    }


  $o .= '<select name="'.$d.'"'.($df ? ' autofocus' : '').'>';
  if ($d0)  $o .= '<option value="0">- -';
  $dte = datee($dt,'d');
  for ($j = 1; $j < 32; $j++)  $o .= '<option value="'.$j.'"'.(($j == $dte)?' selected':'').'>'.substr('00'.$j,-2,2);
  $o .= '</select>';

  $o .= ' <select name="'.$m.'"'.($mf ? ' autofocus' : '').'>';
  if ($m0)  $o .= '<option value="0">- -';
  $dte = datee($dt,'m');
  for ($j = 1; $j < 13; $j++)  $o .= '<option value="'.$j.'"'.(($j == $dte)?' selected':'').'>'.substr('00'.$j,-2,2);
  $o .= '</select>';

  $o .= ' <select name="'.$y.'"'.($yf ? ' autofocus' : '').'>';
  if ($y0)  $o .= '<option value="0">- - - -';
  $dte = datee($dt);
  for ($j = $yb; $j <= $ye; $j++)  $o .= '<option value="'.$j.'"'.(($j == $dte)?' selected':'').'>'.$j;
  $o .= '</select> г.';

  if (isset($n[3])) {
    $o .= ' <select name="'.$h.'"'.($hf ? ' autofocus' : '').'>';
    $dte = datee($dt,'h');
    for ($j = 0; $j < 24; $j++)  $o .= '<option value="'.$j.'"'.(($j == $dte)?' selected':'').'>'.substr('00'.$j,-2,2);
    $o .= '</select>';

    $o .= ' <select name="'.$i.'">';
    $dte = datee($dt,'i');
    for ($j = 0; $j < 60; $j++)  $o .= '<option value="'.$j.'"'.(($j == $dte)?' selected':'').'>'.substr('00'.$j,-2,2);
    $o .= '</select>';

    $o .= ' <select name="'.$s.'">';
    $dte = datee($dt,'s');
    for ($j = 0; $j < 60; $j++)  $o .= '<option value="'.$j.'"'.(($j == $dte)?' selected':'').'>'.substr('00'.$j,-2,2);
    $o .= '</select>';
    }

  return $o;
  }



  // ---------------- input text ---------------- //

function  form_t ($n, $v, $w = 100) {
  $f = 0;
  if ($n[0] == '@') {$f = 1;  $n = substr($n, 1);}
  $ne = explode(',', $n);
  return '<input'.(isset($ne[1]) ? ' id="'.$ne[1].'"' : '').($ne[0] ? ' name="'.$ne[0].'"' : '').' type="text" value="'.$v.'" style="width: '.$w.'px;"'.($f ? ' autofocus' : '').'>';
  }


  // ---------------- input number ---------------- //

function  form_n ($n, $v, $w = 100) {
  $f = 0;
  if ($n[0] == '@') {$f = 1;  $n = substr($n, 1);}
  $ne = explode(',', $n);
  return '<input'.(isset($ne[1]) ? ' id="'.$ne[1].'"' : '').($ne[0] ? ' name="'.$ne[0].'"' : '').' type="number" value="'.$v.'" style="width: '.$w.'px;"'.($f ? ' autofocus' : '').'>';
  }



  // ---------------- select ---------------- //

function  form_s ($n, $a, $dv = 0) {
  $o = '';
  $f = 0;
  $z = 0;

  while (1) {
    if ($n[0] == '@') {$f = 1;  $n = substr($n, 1);  continue;}
    if ($n[0] == '!') {$z = 1;  $n = substr($n, 1);  continue;}
    break;
    }

  $ne = explode(';', $n);
  $n = $ne[0];
  if (isset($ne[1]))  $ad = $ne[1];  else  $ad = FALSE;

  $ne = explode(',', $n);

  $o .= '<select'.(isset($ne[1]) ? ' id="'.$ne[1].'"' : '').($ne[0] ? ' name="'.$ne[0].'"' : '').($f ? ' autofocus' : '').'>';
  if ($z)  $o .= '<option value="0">- - - - -';
  foreach ($a as $k=>$v)  $o .= '<option value="'.$k.'"'.(($k == $dv)?' selected':'').'>'.(($ad !== FALSE) ? $v[$ad] : $v);
  $o .= '</select>';

  return $o;
  }



  // ---------------- checkbox ---------------- //

function  form_c ($n, $v) {
  return '<input name="'.$n.'" type="checkbox"'.($v ? ' checked' : '').'>';
  }



  // ---------------- radio (horizontal / vertical) ---------------- //

//function  form_r ($n, $a) {
//  }



  // ---------------- form ---------------- //

function  form ($n, $a, $p = '') {
  global $mod;

  if ($p) {
    $tmp = array();
    foreach ($p as $v) {
      if ($v)  $tmp[] = $v;
      }
    if ($tmp)  $p = '?'.implode('&', $tmp);
    else  $p = '';
    }
  else  $p = '';

  return   b('<form name="'.$n.'" enctype="multipart/form-data" action="'.$a.$p.'" method="post" onsubmit="
//event.preventDefault();

$.ajax(this.action, 

  function(r) {
    window.location = r;
    },

  {
    post: this,
    fail: function(r) { $.note(\'{error: \'+r.status+\'} \'+r.responseText, 10, \'#fcc\'); }
    }

  );

return  false;
">');

  }


  // ---------------- ajax: submit ---------------- //

function  form_sb ($v = 'сохранить') {
  global $mod;
  return '<input class="sbmt" type="submit" value="'.$v.'">';
  }



  // ---------------- ajax: submit delete ---------------- //

function  form_sbd ($uri) {
  return '
$.ajax(\''.$uri.'\', 

  function(r) {
    window.location = r;
    },

  {
    fail: function(r) { $.note(\'{error: \'+r.status+\'} \'+r.responseText, 10, \'#fcc\'); }
    }

  );

';

  return $o;
  }




  // -------------------------------- UTF-8 4+bytes string trim -------------------------------- //

function  utf_trim_bmp ($str) {
  if (empty($str))  return $str;

  $len = mb_strlen($str);
  $out = '';
  for ($i = 0; $i < $len; $i++) {
    $c = mb_substr($str, $i, 1);
    $out .= (strlen($c) > 3) ? '#' : $c;
    }

  return $out;
  }


?>