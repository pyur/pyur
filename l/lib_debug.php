<?php

/************************************************************************/
/*  functions & constants v2.2o                                         */
/************************************************************************/


if (!isset($body))  die ('error: this is a pluggable module and cannot be used standalone.');



  // -------------------------------- message window -------------------------------- //

function message_window ($caption='', $message=NULL) {
  echo '<p style="background-color:#000;color:#FFF;padding: 1px 20px;margin-top:4px;margin-bottom:0;text-align:left;font-size: 8pt;">';
  echo $caption?$caption:'!';
  echo '</p>';

  echo '<p style="border:1px solid #000;padding: 0 8px;margin-top:0;margin-bottom:4px;text-align:left;font-size: 8pt;">';
  echo $message;
  echo '</p>';
  }




  // -------------------------------- debug message window -------------------------------- //

function d ($var) {
  if (!$var) {
    $var = '&lt;empty&gt;';
    if (is_array($var))       $var = '&lt;empty array&gt;';
    elseif (is_string($var))  $var = '&lt;empty string&gt;';
    elseif (is_int($var))     $var = '&lt;empty int&gt;';
    }

    // ---- print array ---- //
  if (is_array($var)) {
    echo '<p style="background-color:#000;color:#FFF;padding: 1px 20px;margin-top:4px;margin-bottom:0;text-align:left;font-size: 8pt;">';
    echo 'array ('.count($var).')';
    echo '</p>';

    echo '<p style="border:1px solid #000;padding: 0 8px;margin-top:0;margin-bottom:4px;text-align:left;font-size: 8pt; white-space: pre;">';
    print_r($var);
    // TODO: !!!!
    //foreach ($var as $k=>$v) {
    //  htmlspecialchars($mail->body, ENT_SUBSTITUTE);
    //  }
    echo '</p>';
    }


    // ---- print other than array ---- //
  else {
    echo '<table style="text-align: left;  font-size: 8pt;  margin: 0 0 1px 0;">';
    echo '<td style="border: 1px solid #000;  padding: 0 4px;  white-space: pre;">';
    if (is_string($var))   echo 'String ('.mb_strlen($var).')';
    elseif (is_int($var))  echo 'Integer';
    else   echo gettype($var);
    echo '<td style="border: 1px solid #000;  padding: 0 4px;  width: 100%;">';
    echo htmlspecialchars($var, ENT_SUBSTITUTE);
    echo '</table>';
    }

  }




  // -------------------------------- debug hex -------------------------------- //

function  dh ($t, $w = 16) {
  $o = '';
  $o .= '<div style="border: 1px solid #888; padding: 0 2px; font-family: monospace; font-size: 8pt; white-space: pre;">';

  $i = 0;
  $e = strlen($t);
  $h = '';
  $s = '';
  while ($e--) {
    $d = ord($t[$i]);
    $h .= substr('0'.dechex($d), -2,2).' ';
    $s .= ($d > 31 && $d < 128) ? htmlentities($t[$i]) : '.';

    $i++;
    if (!($i % $w) || !$e) {
      $o .= $h;
      if (!$e && ($i % $w))  $o .= str_repeat('   ', $w-($i % $w));
      $o .= '| '.$s;
      if (!$e && ($i % $w))  $o .= str_repeat(' ', $w-($i % $w));
      if ($e)  $o .= "\n";
      $h = '';
      $s = '';
      }
    }

  $o .= '</div>';
  return $o;
  }



  // -------------------------------- debug hex with utf strings -------------------------------- //

function  dhu ($t, $w = 16) {
  $o = '';
  $o .= '<div style="border: 1px solid #888; padding: 0 2px; font-family: monospace; font-size: 8pt; white-space: pre;">';
  $o .= '<style>'."\r\n";
  $o .= 's {'."\r\n";
  $o .= 'text-decoration: none;'."\r\n";
  $o .= 'color: #0b0;'."\r\n";
  $o .= '}'."\r\n";
  $o .= 's.r {'."\r\n";
  $o .= 'color: #f00;'."\r\n";
  $o .= '}'."\r\n";
  $o .= 's.b {'."\r\n";
  $o .= 'color: #44f;'."\r\n";
  $o .= '}'."\r\n";
  $o .= '</style>'."\r\n";

  $i = 0;
  $e = strlen($t);
  $h = '';
  $s = '';
  $u = '';
  $uc = 0;
  while ($e--) {
    $d = ord($t[$i]);
    $h .= substr('0'.dechex($d), -2,2).' ';

    if ($d == 10)  $s .= '<s class="r">n</s>';
    else if ($d == 13)  $s .= '<s class="r">r</s>';
    else if ($d == 9)  $s .= '<s class="r">t</s>';
    else if ($d >= 32 && $d <= 127)  $s .= htmlentities($t[$i]);

    else if ($d >= 128 && $d <= 191 && $u) {
      $u .= chr($d);
      if ($uc == strlen($u))  { $s .= '<s>'.$u.'</s>';  $u = ''; }
      }
    else if ($d >= 192 && $d <= 223) {  // 1
      $u = chr($d);
      $uc = 2;
      $s .= '<s>&gt;</s>';
      }
    else if ($d >= 224 && $d <= 239) {  // 2
      $u = chr($d);
      $uc = 3;
      $s .= '<s>&gt;</s>';
      }
    else if ($d >= 240 && $d <= 247) {  // 3
      $u = chr($d);
      $uc = 4;
      $s .= '<s>&gt;</s>';
      }
    else if ($d >= 248 && $d <= 251) {  // 4
      $u = chr($d);
      $uc = 5;
      $s .= '<s>&gt;</s>';
      }
    else if ($d >= 252 && $d <= 253) {  // 5
      $u = chr($d);
      $uc = 6;
      $s .= '<s>&gt;</s>';
      }
    //else if ($d == 254)  {}  // 6u
    //else if ($d == 255)  {}  // 7u
    else $s .= '<s class="b">.</s>';


    $i++;
    if (!($i % $w) || !$e) {
      $o .= $h;
      if (!$e && ($i % $w))  $o .= str_repeat('   ', $w-($i % $w));
      $o .= '| '.$s;
      if (!$e && ($i % $w))  $o .= str_repeat(' ', $w-($i % $w));
      if ($e)  $o .= "\n";
      $h = '';
      $s = '';
      }
    }

  $o .= '</div>';
  return $o;
  }




  // -------------------------------- benchmark -------------------------------- //

function benchmark ($bench = NULL) {
  global  $benchmark;

  if (!$bench) {

    if (!$benchmark) {
      $benchmark = array('_start' => microtime(TRUE));
      }

    else {
      $pw = 200;  // profiler width
      $po = 0;    // profiler offset

      $benchmark['_end'] = microtime(TRUE);
      echo '<p style="background-color:#000;color:#FFF;padding: 1px 20px;margin-top:4px;margin-bottom:0;text-align:left;font-size: 8pt;">';
      echo 'время выполнения скрипта – '.round($benchmark['_end'] - $benchmark['_start'], 5);
      echo '</p>';

      echo '<div style="border: 1px solid #000;  padding: 0 0 0 8px;  margin: 0 0 1px 0;  text-align:left;  font-size: 8pt;">';

      $benchmark2 = array();
      $bench_prev = '';
      $bench_prevv = '';
      foreach ($benchmark as $k=>$v) {
        if ($k == '_start')  $k = 'от начала';

        if ($bench_prev) {
          $benchmark2[$bench_prev]['d'] = (($v - $bench_prevv > 0.0001)? ($v - $bench_prevv) : '–');
          $benchmark2[$bench_prev]['e'] = $v - $benchmark['_start'];
          }

        $bench_prev = $k;
        $bench_prevv = $v;
        }

      echo '<table class="l f8">';
      foreach ($benchmark2 as $k=>$v) {
        echo '<tr>';
        echo '<td class="t" width="120">'.$k;
        echo '<td class="t" width="'.($pw+10).'">';

          $poa = round($pw * $v['d'] / ($benchmark['_end'] - $benchmark['_start']));
          echo '<table>';
          echo '<tr>';
          echo '<td width="'.$po.'" style="border: none;">';
          echo '<td width="'.$poa.'" style="border: 1px solid #666;  background: #888;  height: 6px;">';
          echo '</table>';
          $po += $poa;

        echo '<td class="t" width="180">'.round($v['d'], 5);
        echo '<td class="t" width="80">'.round($v['e'], 5);
        }
      echo '</table>';

      echo '</div>';
      }

    }

  else {
    $benchmark[$bench] = microtime(TRUE);
    }

  }




  // ---------------- debug write ---------------- //

function dw($d, $p = FALSE) {
  global $mod;
  global $act;
  fwrite (fopen ('r/'.$mod.'/'.$act.' '.time().($p?' '.$p:''), 'wb'), $d);
  clearstatcache();
  }


?>