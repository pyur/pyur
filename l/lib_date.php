<?php

/************************************************************************/
/*  date functions  v2.oo                                               */
/************************************************************************/


if (!isset($body))  die ('error: this is a pluggable module and cannot be used standalone.');



  // ---- dateh - SQL (or component) print as human readable ---------------- //

function dateh ($dt, $mode = '') {
  // int    - unix timestamp
  // string - sql
  // array  - 'y', 'm', 'd', 'h', 'i', 's'
  //
  // modes:   'd' - дата без г., без времени
  //          --'g' - дата с г., без времени
  //          'dd', дата с г., без времени
  //          't',  - только время
  //          'tt', - только время, без секунд
  //          'dt' - dd.mm.yyyy г. hh:mm:ss
  //          'dtt' - dd.mm.yyyy г. hh:mm

  if (is_int($dt)) {
    $date = getdate($dt);
    $y = $date['year'];
    $m = $date['mon'];
    $d = $date['mday'];
    $h = $date['hours'];
    $i = $date['minutes'];
    $s = $date['seconds'];
    }


  elseif (is_string($dt)) {
    $dte = explode(' ', $dt);

    $de = explode('-', $dte[0]);

    $y = (int)$de[0] ? $de[0] : FALSE;
    $m = (int)$de[1] ? $de[1] : FALSE;
    $d = (int)$de[2] ? $de[2] : FALSE;

    if (isset($dte[1])) {
      $te = explode(':', $dte[1]);

      $h = $te[0];
      $i = $te[1];
      $s = $te[2];
      }

    else {
      $h = FALSE;
      $i = FALSE;
      $s = FALSE;
      }
    }

  //elseif (is_array($dt)) {
  //  $y = $dt['y'];
  //  $m = $dt['m'];
  //  $d = $dt['d'];
  //  $h = $dt['h'];
  //  $i = $dt['i'];
  //  $s = $dt['s'];
  //  }


    // -------- autodetect mode -------- //
  if (!$mode) {
    if (!$y && !$m && $h === FALSE)  $mode = '';
    elseif (($y || $m) && $h === FALSE)  $mode = 'dd';
    elseif (($y || $m) && $h !== FALSE)  $mode = 'dt';
    elseif (!$y && !$m && $h !== FALSE)  $mode = 't';
    }



  if (!$mode) {
    $r = '–';
    }

  else {
    $date = '';
    if ($mode == 'd' || $mode == 'dd' || $mode == 'dt' || $mode == 'dtt') {

      $tmp = array();
      if ($y) {
        if ($mode != 'd')  $date = ' г.'.$date;
        $tmp[] = $y;
        }

      if ($m) {
        $tmp[] = substr('00'.$m,-2,2);
        if ($d)  $tmp[] = substr('00'.$d,-2,2);
        }

      krsort($tmp);
      $date = implode('.', $tmp).$date;
      }


    $time = '';
    if ($mode == 't' || $mode == 'tt' || $mode == 'dt' || $mode == 'dtt') {
      $time = substr('00'.$h,-2,2);
      $time .= ':'.substr('00'.$i,-2,2);

      if ($mode != 'tt' && $mode != 'dtt') {
        $time .= ':'.substr('00'.$s,-2,2);
        }
      }

    $idt = array();
    if ($date)  $idt[] = $date;
    if ($time)  $idt[] = $time;
    $r = implode(' ', $idt);
    }

  return  $r;
  }




  // ---- date age ---------------------------------------------------------------------------- //
function dateage($d, $params = array('e'=>1, 'd'=>0) ) {
  global $curr;

  $dd = substr($d,8,2);
  $dm = substr($d,5,2);
  $dy = substr($d,0,4);

  $age = $curr['year'] - $dy -1;
  if ($curr['mon'] > $dm)  $age++;
  elseif ($curr['mon'] == $dm && $curr['mday'] >= $dd)  $age++;

  $r = $age;

  if ($params['e'])  $r = $r.' '.ends($age,'year');

  return  $r;
  }




  // ---- date difference ---------------- //
function datef($b, $e) {
  global $montha;
  $r = '';
  $r .= date('j', $b);
  if (date('n', $b) != date('n', $e))  $r .= ' '.$montha[date('n', $b)];
  if (date('Y', $b) != date('Y', $e))  $r .= ' '.date('Y', $b).' г.';
  $r .= ' - ';
  $r .= date('j', $e);
  $r .= ' '.$montha[date('n', $e)];
  $r .= ' '.date('Y', $e).' г.';
  return $r;
  }

  // ---- date - time to SQL ---------------- //
function datesql($y, $m=NULL, $d=NULL, $h=NULL, $i=NULL, $s=NULL) {

  if     ($m === NULL && $d === NULL)                 $r = date('Y-m-d', $y);
  elseif ($m !== NULL && $d === NULL)                 $r = date('Y-m-d H:i:s', $y);
  elseif ($m !== NULL && $d !== NULL && $h === NULL)  $r = substr('0000'.$y,-4,4).'-'.substr('00'.$m,-2,2).'-'.substr('00'.$d,-2,2);
  elseif ($m !== NULL && $d !== NULL && $h !== NULL)  $r = substr('0000'.$y,-4,4).'-'.substr('00'.$m,-2,2).'-'.substr('00'.$d,-2,2).' '.substr('00'.$h,-2,2).':'.substr('00'.$i,-2,2).':'.substr('00'.$s,-2,2);

  return  $r;
  }

  // ---- date - SQL to time ---------------- //
function datesqltime($y, $m=NULL, $d=NULL, $h=0, $i=0, $s=0) {
  if (!$m)  {
    $h = datee($y, 'h');
    $i = datee($y, 'i');
    $s = datee($y, 's');
    $m = datee($y, 'm');
    $d = datee($y, 'd');
    $y = datee($y, 'y');
    }
  return  mktime($h,$i,$s, $m,$d,$y);
  }

  // ---- date - year, month, day implode to SQL (warning! dupe of datesql) ---------------- //
function datei($y,$m,$d) {
  //return  substr('0000'.$y,-4,4).'-'.substr('00'.$m,-2,2).'-'.substr('00'.$d,-2,2);
  echo '&lt;datei&gt;';
  return '';
  }

  // ---- date - SQL explode to year, month, day ---------------- //
function datee($a, $b = 'y') {
  $r = NULL;

  if ($b == 'y')  $r = (int)substr($a, 0, 4);
  if ($b == 'm')  $r = (int)substr($a, 5, 2);
  if ($b == 'd')  $r = (int)substr($a, 8, 2);
  if ($b == 'h')  $r = (int)substr($a, 11, 2);
  if ($b == 'i')  $r = (int)substr($a, 14, 2);
  if ($b == 's')  $r = (int)substr($a, 17, 2);

  if ($b == 'Y')  $r = substr($a, 0, 4);
  if ($b == 'M')  $r = substr($a, 5, 2);
  if ($b == 'D')  $r = substr($a, 8, 2);
  if ($b == 'H')  $r = substr($a, 11, 2);
  if ($b == 'I')  $r = substr($a, 14, 2);
  if ($b == 'S')  $r = substr($a, 17, 2);

  return  $r;
  }



  // ---- dates difference in days  ($date_b - $date_a) ---------------- //
function  datediff ($date_a, $date_b = NULL) {

  if (!$date_b) {
    global $curr;
    $date_b = $curr['date'];
    }

  $mon_days = array('0' => array (1 => 31, 2 => 28, 3 => 31, 4 => 30, 5 => 31, 6 => 30, 7 => 31, 8 => 31, 9 => 30, 10 => 31, 11 => 30, 12 => 31),
                    '1' => array (1 => 31, 2 => 29, 3 => 31, 4 => 30, 5 => 31, 6 => 30, 7 => 31, 8 => 31, 9 => 30, 10 => 31, 11 => 30, 12 => 31) );

  $days = 0;


  $date_a_y = datee($date_a, 'y');
  $date_a_m = datee($date_a, 'm');
  $date_a_d = datee($date_a, 'd');

  $date_b_y = datee($date_b, 'y');
  $date_b_m = datee($date_b, 'm');
  $date_b_d = datee($date_b, 'd');



    // ---- проверка на инверсию ---- //
  $invert = 1;
  if ($date_a_y < $date_b_y) {
    $invert = 0;
    }

  elseif ($date_a_y == $date_b_y && $date_a_m < $date_b_m) {
    $invert = 0;
    }

  elseif ($date_a_y == $date_b_y && $date_a_m == $date_b_m && $date_a_d <= $date_b_d) {
    $invert = 0;
    }


  if ($invert) {
    $date_a_y = datee($date_b, 'y');
    $date_a_m = datee($date_b, 'm');
    $date_a_d = datee($date_b, 'd');

    $date_b_y = datee($date_a, 'y');
    $date_b_m = datee($date_a, 'm');
    $date_b_d = datee($date_a, 'd');
    }

  //echo '&nbsp;&nbsp;'.$date_a.' &ndash; '.$date_b.' ('.$invert.')<br>';





  if ($date_a_y < $date_b_y) {

      // ---- до конца месяца ---- //
    //echo '&nbsp;&nbsp;1) '.($mon_days [($date_a_y%4)?0:1] [$date_a_m] - $date_a_d +1).'<br>';
    $days += $mon_days [($date_a_y%4)?0:1] [$date_a_m++] - $date_a_d +1;

      // ---- до конца года ---- //
    while ($date_a_m < 13) {
      //echo '&nbsp;&nbsp;2) '.($mon_days [($date_a_y%4)?0:1] [$date_a_m]).'<br>';
      $days += $mon_days [($date_a_y%4)?0:1] [$date_a_m++];
      }


    $date_a_y++;

      // ---- средние года ---- //
    while ($date_a_y < $date_b_y) {
      //echo '&nbsp;&nbsp;3) '.((($date_a_y%4)?0:1) ? 366 : 365).'<br>';
      $days += ((($date_a_y++%4)?0:1) ? 366 : 365);
      }


      // ---- от начала года ---- //
    $date_a_m = 1;
    $date_a_d = 1;

    while ($date_a_m < $date_b_m) {
      //echo '&nbsp;&nbsp;4) '.($mon_days [($date_a_y%4)?0:1] [$date_a_m]).'<br>';
      $days += $mon_days [($date_a_y%4)?0:1] [$date_a_m++];
      }

    }

  elseif ($date_a_m < $date_b_m) {
    //echo '&nbsp;&nbsp;1b) '.($mon_days [($date_a_y%4)?0:1] [$date_a_m] - $date_a_d +1).'<br>';
    $days += $mon_days [($date_a_y%4)?0:1] [$date_a_m++] - $date_a_d +1;

    while ($date_a_m < $date_b_m) {
      //echo '&nbsp;&nbsp;2b) '.($mon_days [($date_a_y%4)?0:1] [$date_a_m]).'<br>';
      $days += $mon_days [($date_a_y%4)?0:1] [$date_a_m++];
      }

    $date_a_d = 1;
    }



    // ---- от начала месяца ---- //
  //echo '&nbsp;&nbsp;5) '.($date_b_d - $date_a_d).'<br>';
  $days += $date_b_d - $date_a_d;


  if ($invert)  $days = 0 - $days;
  return  $days;
  }


  // ---- dates difference in days  ($date_b - $date_a) ---------------- //
function  datediffy ($date_a, $date_b = NULL) {

  if (!$date_b) {
    global $curr;
    $date_b = $curr['date'];
    }

  $years = datee($date_b) - datee($date_a) -1;
  if (datee($date_b,'m') > datee($date_a,'m'))  $years++;
  elseif (datee($date_b,'m') == datee($date_a,'m') && datee($date_b,'d') >= datee($date_a,'d'))  $years++;

  return  $years;
  }


  // ---- date difference one week ---------------- //
function datedw($b) {
  global $montha;
  $e = $b + 86400 * 5;
  $r = '';
  $r .= date('j', $b);
  if (date('n', $b) != date('n', $e))  $r .= ' '.$montha[date('n', $b)];
  if (date('Y', $b) != date('Y', $e))  $r .= ' '.date('Y', $b).' г.';
  $r .= ' - ';
  $r .= date('j', $e);
  $r .= ' '.$montha[date('n', $e)];
  $r .= ' '.date('Y', $e).' г.';
  //return $r;

  echo '&lt;datedw&gt;';
  return '';
  }

  // ---- date - increment by one (or 'n') day(s) ---------------- //
//function dateinc($a, $b=1) {
//  //return  date(mktime(0, 0, 0, date('n', $a), date('j', $a) + $b, date('Y', $a)));
//  return  mktime(0, 0, 0, date('n', $a), date('j', $a) + $b, date('Y', $a));
//  }



?>