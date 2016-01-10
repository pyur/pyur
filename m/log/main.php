<?php

/************************************************************************/
/*  Log  v1.oo                                                          */
/************************************************************************/


if (!isset($body))  die ('error: this is a pluggable module and cannot be used standalone.');

$gcnf = getn('cnf');
$goff = getn('off');


$gyear = post('f_log_year', 0, gets('year', $curr['year']) );
$gmon = postn('f_log_mon', 0, getn('mon', $curr['mon']) );
$gday = postn('f_log_day', 0, getn('day', (gets('year') ? 0 : $curr['mday'])) );


$gsrv = postn('f_log_server', 0, getn('srv', 1) );
$gip = post('f_log_ip', 0, get('ip') );
if (substr($gip,0,1)=='!')  {$gipw=substr($gip,1); $gipn=TRUE;}  else  {$gipw=$gip; $gipn=FALSE;}
$grst = postn('f_log_rst', 0, getn('rst') );
$guan = postn('f_log_uan', 0, getn('uan') );
$gusr = postn('f_log_usr', 0, getn('usr') );
$gipf = post('f_log_ipf', 0, get('ipf') );

$guas = getn('uas');

$gscl = getn('scl',1);


include 'm/'.$mod.'/const.php';






if (!$act) {
  include 'l/lib_ua.php';

  $page_limit = 4000;


  if (!$gmon) {
    $dateb = datesql($gyear, 1, 1);
    $datee = datesql($gyear, 12, 31);
    }
  elseif (!$gday) {
    $dateb = datesql($gyear, $gmon, 1);
    $datee = datesql($gyear, $gmon, date('t', mktime (0,0,0, $gmon, 1, $gyear)) );
    }
  else {
    $dateb = datesql($gyear, $gmon, $gday);
    $datee = datesql($gyear, $gmon, $gday);
    }



    // ---- queryDB ---- //

  $server = db_read(array('table' => 'server',
                          'col' => array('id', 'tp', 'desc'),
                          'key' => 'tp',
                          ));
  $server = tsort($server);




  $where = array();
  //$where[] = '`server` = '.$gsrv;
  $where[] = '`datetime` >= \''.$dateb.' 00:00:00\'';
  $where[] = '`datetime` <= \''.$datee.' 23:59:59\'';
  if ($gsrv)  $where[] = '`server` = '.$gsrv;
  if ($gip)  $where[] = '`ip` '.($gipn?'!':'').'= INET_ATON(\''.$gipw.'\')';
  if ($grst)  $where[] = '`result` = '.$grst;
  if ($guan)  $where[] = '`uan` = '.$guan;
  if ($gusr)  $where[] = '`userx` = '.$gusr;
  if ($gipf)  $where[] = '`ipf` = INET_ATON(\''.$gipf.'\')';

  $count = db_read(array('table' => 'log',
                         'where' => $where,
                         ));


  $log = FALSE;
  if ($gmon) {
    $where[] = '`ua`.`id` = `log`.`uan`';

    $log = db_read(array('table' => array('log', 'ua'),
                         'col' => array('log`.`id', '@ip', 'log`.`datetime', 'log`.`methodn', 'log`.`httpvn', 'log`.`resultn', 'log`.`bytes', 'log`.`userx', 'log`.`type',
                                        'log`.`server',
                                        'log`.`uri', 
                                        'log`.`referer',
                                        'log`.`uan',
                                        '@ipf',
                                        'ua`.`ua', 'ua`.`spcf',
                                        ),
                         'where' => $where,
                         //'order' => '`dt`',
                         'limit' => $page_limit * $goff.', '.$page_limit,

                         'key' => 'id',
                         ));
    }



    // ---- submenu ---- //
  if ($gmon) {
    $date_prev = datesql(mktime(0,0,0, ($gmon-1),1,$gyear));
    $date_next = datesql(mktime(0,0,0, ($gmon+1),1,$gyear));

    $submenu[datee($date_prev).'.'.datee($date_prev, 'M').';navigation-180-button'] = '/'.$mod.'/?'.($gsrv?'&srv='.$gsrv:'').($gip?'&ip='.$gip:'').'&year='.datee($date_prev).'&mon='.datee($date_prev, 'm').($grst?'&rst='.$grst:'').($guan?'&uan='.$guan:'').($gusr?'&usr='.$gusr:'').($gipf?'&ipf='.$gipf:'');
    $submenu[datee($date_next).'.'.datee($date_next, 'M').';navigation-000-button'] = '/'.$mod.'/?'.($gsrv?'&srv='.$gsrv:'').($gip?'&ip='.$gip:'').'&year='.datee($date_next).'&mon='.datee($date_next, 'm').($grst?'&rst='.$grst:'').($guan?'&uan='.$guan:'').($gusr?'&usr='.$gusr:'').($gipf?'&ipf='.$gipf:'');
    }

  if ($gday) {
    $date_prev = datesql(mktime(0,0,0, $gmon,($gday-1),$gyear));
    $date_next = datesql(mktime(0,0,0, $gmon,($gday+1),$gyear));

    $submenu[dateh($date_prev).';navigation-180-button'] = '/'.$mod.'/?'.($gsrv?'&srv='.$gsrv:'').($gip?'&ip='.$gip:'').'&year='.datee($date_prev).'&mon='.datee($date_prev, 'm').'&day='.datee($date_prev, 'd').($grst?'&rst='.$grst:'').($guan?'&uan='.$guan:'').($gusr?'&usr='.$gusr:'').($gipf?'&ipf='.$gipf:'');
    $submenu[dateh($date_next).';navigation-000-button'] = '/'.$mod.'/?'.($gsrv?'&srv='.$gsrv:'').($gip?'&ip='.$gip:'').'&year='.datee($date_next).'&mon='.datee($date_next, 'm').'&day='.datee($date_next, 'd').($grst?'&rst='.$grst:'').($guan?'&uan='.$guan:'').($gusr?'&usr='.$gusr:'').($gipf?'&ipf='.$gipf:'');
    }

  //if (p('edit'))  $submenu['Добавить'] = '/'.$mod.'/pne';
  $submenu['User-agent string'] = '/'.$mod.'/uas/';
  $submenu['Импорт'] = '/'.$mod.'/imp/?srv='.$gsrv;
  $submenu['Определить тип'] = '/'.$mod.'/dft/?srv='.$gsrv;
  //$submenu['Temporary'] = '/'.$mod.'/lt/';
  //$submenu['Parse UA'] = '/'.$mod.'/pua/';
  submenu();
    // ---- end: submenu ---- //




  b('<p class="h4">Журнал Apache'.( $gsrv ? (' – '.$server[$gsrv]['desc']) : '' ).' – '.( ($gday || !$gmon) ? dateh($dateb) : (dateh($dateb).' - '.dateh($datee)) ).'</p>');  //dateh($gdate).
  b();


    // ---------------- filter parameters bar ---------------- //

  b('<form name="log" enctype="multipart/form-data" action="/'.$mod.'/');
  //if ($gip)  b('&ip='.$gip);
  //if ($gsrv)  b('&srv='.$gsrv);
  //if ($gyear)  b('&year='.$gyear);
  //if ($gmon)  b('&mon='.$gmon);
  b('" method="post">');

  b('<table class="f">');
  b('<tr>');
  b('<td class="t">');

  b('<select name="f_log_server" style="border: 1px solid #ccc;" onchange="submit();">');
  b('<option value="0">- - - - все - - - -');
  foreach ($server as $k=>$v)  b('<option value="'.$k.'"'.(($k == $gsrv)?' selected':'').'>'.$v['desc']);
  b('</select>');


  b('<td class="t">');
  b('&nbsp;&nbsp;&nbsp;&nbsp;');

  // <label for="f_log_ip">ip address:</label>
  b('<input name="f_log_ip" type="text" size="12" placeholder="ip address" value="'.$gip.'" style="border: 1px solid #ccc;" onkeypress="var x;  if(window.event) {x=event.keyCode;}  else if(event.which) {x=event.which;}  if(x == 13) submit();">');


  b('<td class="t">');
  b('&nbsp;&nbsp;&nbsp;&nbsp;');

  b('<select name="f_log_year" style="border: 1px solid #ccc;" onchange="submit();">');
  for ($i = 2008; $i <= $curr['year']; $i++)  b('<option value="'.$i.'"'.(($i == $gyear)?' selected':'').'>'.$i);
  b('</select>');


  b('<td class="t">');
  b('&nbsp;');

  b('<select name="f_log_mon" style="border: 1px solid #ccc;" onchange="submit();">');
  b('<option value="0">- -');
  for ($i = 1; $i <= 12; $i++)  b('<option value="'.$i.'"'.(($i == $gmon)?' selected':'').'>'.$i);
  b('</select>');


  b('<td class="t">');
  b('&nbsp;');

  b('<select name="f_log_day" style="border: 1px solid #ccc;" onchange="submit();">');
  b('<option value="0">- -');
  for ($i = 1; $i <= 31; $i++)  b('<option value="'.$i.'"'.(($i == $gday)?' selected':'').'>'.$i);
  b('</select>');


  b('<td class="t">');
  b('&nbsp;&nbsp;&nbsp;&nbsp;');

  //b('<input name="f_log_rst" type="text" size="3" placeholder="result" value="'.($grst?$grst:'').'" style="border: 1px solid #ccc;" onkeypress="var x;  if(window.event) {x=event.keyCode;}  else if(event.which) {x=event.which;}  if(x == 13) submit();">');
  b('<select name="f_log_rst" style="border: 1px solid #ccc;" onchange="submit();">');
  b('<option value="0">- -');
  foreach ($db_result as $k=>$v)  b('<option value="'.$k.'"'.(($k == $grst)?' selected':'').'>'.$v);
  b('</select>');


  b('<td class="t">');
  b('&nbsp;&nbsp;&nbsp;&nbsp;');

  b('<input name="f_log_uan" type="text" size="4" placeholder="uan" value="'.($guan?$guan:'').'" style="border: 1px solid #ccc;" onkeypress="var x;  if(window.event) {x=event.keyCode;}  else if(event.which) {x=event.which;}  if(x == 13) submit();">');


  b('<td class="t">');
  b('&nbsp;&nbsp;&nbsp;&nbsp;');

  b('<input name="f_log_usr" type="text" size="4" placeholder="userx" value="'.($gusr?$gusr:'').'" style="border: 1px solid #ccc;" onkeypress="var x;  if(window.event) {x=event.keyCode;}  else if(event.which) {x=event.which;}  if(x == 13) submit();">');


  b('<td class="t">');
  b('&nbsp;&nbsp;&nbsp;&nbsp;');

  b('<input name="f_log_ipf" type="text" size="12" placeholder="ip forward" value="'.$gipf.'" style="border: 1px solid #ccc;" onkeypress="var x;  if(window.event) {x=event.keyCode;}  else if(event.which) {x=event.which;}  if(x == 13) submit();">');


  b('</table>');
  b('</form>');
  b();


  if (!$gsrv && $gday && $log) {
    b('<img src="/'.$mod.'/grds/?');
    if ($gip)  b('&ip='.$gip);
    b('&year='.$gyear);
    b('&mon='.$gmon);
    b('&day='.$gday);
    if ($grst)  b('&rst='.$grst);
    if ($guan)  b('&uan='.$guan);
    if ($gusr)  b('&usr='.$gusr);
    if ($gipf)  b('&ipf='.$gipf);
    b('">');
    b();
    b();
    }

  elseif ($gday && $log) {
    b('<img src="/'.$mod.'/grdi/?');
    b('&srv='.$gsrv);
    if ($gip)  b('&ip='.$gip);
    b('&year='.$gyear);
    b('&mon='.$gmon);
    b('&day='.$gday);
    if ($grst)  b('&rst='.$grst);  // rawurlencode()
    if ($guan)  b('&uan='.$guan);
    if ($gusr)  b('&usr='.$gusr);
    if ($gipf)  b('&ipf='.$gipf);
    b('">');
    //b();
    //b();
    }


  //b('<img src="ajax.php/'.$mod.'/');
  //if ($gyear && $gmon && $gday)  b('grd&scl=2');
  //if ($gyear && $gmon && !$gday) b('grm');
  //if ($gyear && !$gmon)          b('gry');
  //b('&srv='.$gsrv);
  //if ($gip)  b('&ip='.$gip);
  //b('&year='.$gyear);
  //if ($gmon)  b('&mon='.$gmon);
  //if ($gday)  b('&day='.$gday);
  //if ($grst)  b('&rst='.$grst);  // rawurlencode()
  //if ($guan)  b('&uan='.$guan);
  //if ($gusr)  b('&usr='.$gusr);
  //if ($gipf)  b('&ipf='.$gipf);
  //b('">');

  $lg = FALSE;
  //if ($gyear && $gmon && $gday) {
  //  $lg = array('m' => 'd',
  //              'w' => 450,
  //              'h' => 192,
  //              's1' => 2,
  //              's2' => 2,
  //              's3' => 3,
  //              's4' => 4,
  //              's5' => 5,
  //              'ha' => 0,
  //              );
  //  }
  if ($gyear && $gmon && !$gday) {
    $days = date('t', mktime (0,0,0, $gmon, 1, $gyear));
    $lg = array('m' => 'm',
                'w' => $days*10,
                'h' => 144,
                's1' => 3,
                's2' => 3,
                's3' => 4,
                's4' => 5,
                's5' => 6,
                'ha' => 14,
                );
    }
  if ($gyear && !$gmon) {
    $days = 365 + date('L', mktime (0,0,0, 1, 1, $gyear));
    $lg = array('m' => 'y3',
                'w' => $days*3,
                'h' => 480,
                's1' => 1,
                's2' => 1,
                's3' => 1,
                's4' => 2,
                's5' => 2,
                'ha' => 12,
                );
    }

  if ($lg) {
    $lgu = '/'.$mod.'/gr'.$lg['m'].'/?';
    $lgu .= '&srv='.$gsrv;
    if ($gip)  $lgu .= '&ip='.$gip;
    $lgu .= '&year='.$gyear;
    if ($gmon)  $lgu .= '&mon='.$gmon;
    if ($gday)  $lgu .= '&day='.$gday;
    if ($grst)  $lgu .= '&rst='.$grst;
    if ($guan)  $lgu .= '&uan='.$guan;
    if ($gusr)  $lgu .= '&usr='.$gusr;
    if ($gipf)  $lgu .= '&ipf='.$gipf;

    b('<style>
@media (max-width: 1339px) {
div.log_graph {width: '.($lg['w']*$lg['s1']).'px;  height: '.($lg['h']*$lg['s1']+$lg['ha']).'px;  background-image: url(\''.$lgu.'&scl='.$lg['s1'].'\');  background-repeat: no-repeat;}
}
  /* for 1366 */
@media (min-width: 1340px) and (max-width: 1579px) {
div.log_graph {width: '.($lg['w']*$lg['s2']).'px;  height: '.($lg['h']*$lg['s2']+$lg['ha']).'px;  background-image: url(\''.$lgu.'&scl='.$lg['s2'].'\');  background-repeat: no-repeat;}
}
  /* for 1600 */
@media (min-width: 1580px) and (max-width: 1899px) {
div.log_graph {width: '.($lg['w']*$lg['s3']).'px;  height: '.($lg['h']*$lg['s3']+$lg['ha']).'px;  background-image: url(\''.$lgu.'&scl='.$lg['s3'].'\');  background-repeat: no-repeat;}
}
  /* for 1920 */
@media (min-width: 1900px) and (max-width: 2499px) {
div.log_graph {width: '.($lg['w']*$lg['s4']).'px;  height: '.($lg['h']*$lg['s4']+$lg['ha']).'px;  background-image: url(\''.$lgu.'&scl='.$lg['s4'].'\');  background-repeat: no-repeat;}
}
  /* for 2560 */
@media (min-width: 2500px) {
div.log_graph {width: '.($lg['w']*$lg['s5']).'px;  height: '.($lg['h']*$lg['s5']+$lg['ha']).'px;  background-image: url(\''.$lgu.'&scl='.$lg['s5'].'\');  background-repeat: no-repeat;}
}
</style>
');

    b('<div class="log_graph"></div>');
    }


  b('<p class="p">Количество хитов: <b>'.$count.' ('.($page_limit*$goff).' – '.($page_limit*($goff+1) -1).')</b>');

  if ($count > $page_limit) {
    //b('<div style="text-align: center;  border: 1px solid blue;">');
    b('<div style="text-align: center;  margin-bottom: 4px;">');

    $pages = ceil($count/$page_limit);
    for ($i = 0; $i < $pages; $i++) {
      if ($goff == $i)  $current_page = TRUE;
      else              $current_page = FALSE;

      b('<div style="display: inline-block;  margin: 0 3px;  padding: 2px;  font-size: 12pt;  width: 19px;  height: 19px;  background-color: '.($current_page?'yellow':'#eee').';  border: 1px solid black;  border-radius: 6px;">');

      if (!$current_page)  b('<a href="/'.$mod.'/?'.($gsrv?'&srv='.$gsrv:'').($gip?'&ip='.$gip:'').($gyear?'&year='.$gyear:'').($gmon?'&mon='.$gmon:'').($gday?'&day='.$gday:'').($grst?'&rst='.$grst:'').($guan?'&uan='.$guan:'').($gusr?'&usr='.$gusr:'').($gipf?'&ipf='.$gipf:'').($i?'&off='.$i:'').'">');
      b($i+1);
      if (!$current_page)  b('</a>');

      b('</div>');
      }

    b('</div>');
    }


  if ($log) {

    //$lc_parse = FALSE;
    $ip_sess = array();

    $log_color = 'm/'.$mod.'/log_color_'.$gsrv.'.php';
    if (file_exists($log_color)) {
      include $log_color;
      }


      // ---------------- default colorer ---------------- //

    if (!function_exists('log_color')) {
      function  log_color() {
        global  $db_hit_type;
        global  $ip_sess;
        global  $v;

        $log_color = array('row' => array(),
                           'ip' => array(),
                           'datetime' => array(),
                           'uri' => array(),
                           'server' => array(),
                           'result' => array(),
                           'bytes' => array(),
                           'referer' => array(),
                           'ua' => array(),
                           'userx' => array(),
                           'ipf' => array(),
                           );

        $line_time = datesqltime($v['datetime']);
        $start_session = TRUE;
        if (isset($ip_sess[$v['@ip']]) && $line_time - $ip_sess[$v['@ip']] < 180)  $start_session = FALSE;
        $ip_sess[$v['@ip']] = $line_time;
     
        //if (!$v['referer'])  $style_row = 'background-color: #ddf';
        if ($start_session)  $log_color['row'][] = 'background-color: #fdd';
        //if (substr($v['referer'],0,strlen($sever_root)) != $sever_root)  $style_row = 'background-color: #fdd';
     
        if ($db_hit_type[$v['type']]['tc'])  $log_color['row'][] = 'color: '.$db_hit_type[$v['type']]['tc'];
        if ($db_hit_type[$v['type']]['bc'])  $log_color['row'][] = 'background-color: '.$db_hit_type[$v['type']]['bc'];


        if ($v['resultn'] != 20 && $v['resultn'] != 74)  {$log_color['result'][] = 'color: #ff0';  $log_color['result'][] = 'background-color: #f00';}


        foreach ($log_color as $kk=>$vv) {
          if ($vv)  $log_color[$kk] = ' style="'.implode('; ', $vv).'"';
          }

        return  $log_color;
        }
      }


        // ---------------- default rewriter ---------------- //

    if (!function_exists('lc_parse')) {
      function  lc_parse() {
        global  $v;

        if (substr($v['referer'],0,17) == 'http://yandex.ru/') {
          $ss = FALSE;
          if (($beg = strpos($v['referer'], 'text=')) !== FALSE) {
            $beg += 5;
            if (($end = strpos($v['referer'], '&', $beg)) === FALSE)  $end = strlen($v['referer']);
            $ss = rawurldecode(substr($v['referer'], $beg, $end-$beg));
            }
          if (($beg = strpos($v['referer'], 'etext=')) !== FALSE) {
            $beg += 6;
            if (($end = strpos($v['referer'], '&', $beg)) === FALSE)  $end = strlen($v['referer']);
            $ss = rawurldecode(substr($v['referer'], $beg, $end-$beg));
            }
        
          if ($ss !== FALSE)  $v['referer'] = '[yandex] ['.$ss.']';
          }


        if (substr($v['referer'],0,24) == 'http://go.mail.ru/search') {
          $ss = FALSE;
          $beg = strpos($v['referer'], 'q=');
          if ($beg !== FALSE) {
            $beg += 2;
            $end = strpos($v['referer'], '&', $beg);
            if ($end === FALSE)  $end = strlen($v['referer']);
            $ss = rawurldecode(substr($v['referer'], $beg, $end-$beg));
            }

          if ($ss !== FALSE)  $v['referer'] = '[go.mail.ru] ['.$ss.']';
          }

        }
      }


    $ua_col = 8;
    if (!$gsrv)  $ua_col = 9;

    b('<style type="text/css">
table.lst td:nth-child('.$ua_col.') hr {width: 12px; height: 12px; margin: 0 2px 0 0;}
table.lst td:nth-child('.$ua_col.') i {display: inline; vertical-align: baseline; background-image: none; font-size: 7pt; color: #888; font-style: normal;}

');

    foreach ($db_uai as $kic=>$vic) {
      $resize = '';
      if ($kic == 'x') {
        $resize = 'width: 7px; height: 5px; vertical-align: 4px; margin: 0 2px 0 -2px; ';
	}

      $i = array_search($vic.'-12', $submenu_icons);
      if ($resize)  $i = array_search($vic, $submenu_icons);
      if ($i !== FALSE) {
        $x = ($i % 64) * 16;
        $y = floor($i / 64) * 16  + $modules_h;

        b('table.lst td:nth-child('.$ua_col.') hr.'.$kic.' {'.$resize.'background-position: '.($x?('-'.$x.'px'):'0').' '.($y?('-'.$y.'px'):'0').';}'."\n");
        }
      else {
        b('table.lst td:nth-child('.$ua_col.') hr.'.$kic.' {display: none;}'."\n");
        }
      }

b('
@media (max-width: 1899px) {
table.lst td:nth-child('.$ua_col.') hr {width: 9px; height: 9px; margin: 0 2px 0 0;}
');

    foreach ($db_uai as $kic=>$vic) {
      $resize = '';
      if ($kic == 'x') {
        $resize = 'width: 7px; height: 5px; vertical-align: 4px; margin: 0 2px 0 -2px; ';
	}

      $i = array_search($vic.'-9', $submenu_icons);
      if ($resize)  $i = array_search($vic, $submenu_icons);
      if ($i !== FALSE) {
        $x = ($i % 64) * 16;
        $y = floor($i / 64) * 16  + $modules_h;

        b('table.lst td:nth-child('.$ua_col.') hr.'.$kic.' {'.$resize.'background-position: '.($x?('-'.$x.'px'):'0').' '.($y?('-'.$y.'px'):'0').';}'."\n");
        }
      else {
        b('table.lst td:nth-child('.$ua_col.') hr.'.$kic.' {display: none;}'."\n");
        }
      }

b('
}
</style>
');

    if (!$gsrv) {
      css_table(array(
        '#'  => array(            0,  0,  0,   0,  1,   0,  0,  1,   0,  1,   0,  0),
        0    => array('f7',       0,  71, 97,  80, 130, 17, 0,  160, 54, 0,   27, 71),
        1024 => array('f7',       0,  71, 97,  80, 300, 17, 36, 290, 54, 0,   27, 71),
        1280 => array('f7',       41, 71, 97,  80, 300, 17, 46, 300, 54, 108, 27, 71),
        1366 => array('f7',       41, 71, 97,  80, 300, 17, 46, 300, 54, 194, 27, 71),
        1600 => array('f7',       41, 71, 97,  80, 360, 30, 50, 360, 54, 290, 27, 71),
        1920 => array('f8',       50, 86, 117, 80, 400, 20, 50, 400, 54, 478, 31, 86),
        2560 => array('f8', 'p4', 50, 86, 117, 80, 400, 20, 50, 400, 54, 558, 31, 86),
        ));
      }

    else {
      css_table(array(
        '#'  => array(            0,  0,  0,   1,   0,  0,  1,   0,  1,   0,  0),
        0    => array('f7',       0,  71, 97,  130, 17, 0,  160, 54, 0,   27, 71),
        1024 => array('f7',       0,  71, 97,  300, 17, 36, 290, 54, 0,   27, 71),
        1280 => array('f7',       41, 71, 97,  300, 17, 46, 300, 54, 188, 27, 71),
        1366 => array('f7',       41, 71, 97,  300, 17, 46, 300, 54, 274, 27, 71),
        1600 => array('f7',       41, 71, 97,  360, 30, 50, 360, 54, 370, 27, 71),
        1920 => array('f8',       50, 86, 117, 400, 20, 50, 400, 54, 558, 31, 86),
        2560 => array('f8', 'p4', 50, 86, 117, 400, 20, 50, 400, 54, 558, 31, 86),
        ));
      }

    b('<table class="lst" id="log_table">');
    b('<tr>');
    b('<td id="id">id');
    b('<td id="ip">ip');
    b('<td id="datetime">datetime');
    if (!$gsrv)  b('<td id="server">server');
    b('<td id="uri">uri');
    b('<td id="rst">rst');
    b('<td id="bytes">bytes');
    b('<td id="referer">referer');
    b('<td id="uan">uan');
    b('<td id="ua">ua');
    b('<td id="usr">usr');
    b('<td id="ipf">ip fwd');

    foreach ($log as $k=>$v) {

     // $line_time = datesqltime($v['datetime']);
     // $start_session = TRUE;
     // if (isset($ip_sess[$v['@ip']]) && $line_time - $ip_sess[$v['@ip']] < 180)  $start_session = FALSE;
     // $ip_sess[$v['@ip']] = $line_time;
     //
     // $style_row = array();
     // //if (!$v['referer'])  $style_row = 'background-color: #ddf';
     // if ($start_session)  $style_row[] = 'background-color: #fdd';
     // //if (substr($v['referer'],0,strlen($sever_root)) != $sever_root)  $style_row = 'background-color: #fdd';
     //
     // //if ($ip['color'])  $style_row[] = 'color: '.$ip['color'];
     // //elseif ($v['type'] == 1)  $style_row[] = 'color: #fc8';
     // if ($db_hit_type[$v['type']]['tc'])  $style_row[] = 'color: '.$db_hit_type[$v['type']]['tc'];
     // if ($db_hit_type[$v['type']]['bc'])  $style_row[] = 'background-color: '.$db_hit_type[$v['type']]['bc'];

      $log_color = log_color();

      lc_parse();

      b('<tr');
     // if ($style_row)  b(' style="'.implode('; ', $style_row).'"');
      if ($log_color['row'])  b($log_color['row']);
      b('>');

      b('<td id="'.$k.'">');
      //if (p('edit'))  b('<a href="/'.$mod.'/pne&pan='.$k.'" target="_blank">');
      b($k);
      //if (p('edit'))  b('</a>');


      b('<td');
      if ($log_color['ip'])  b($log_color['ip']);
      b('>');
      b($v['@ip']);


      b('<td');
      if ($log_color['datetime'])  b($log_color['datetime']);
      b('>');
      //b($v['datetime']);
      b(dateh($v['datetime']));


      //b('<td class="t6">');
      //b($v['methodn']);


      if (!$gsrv) {
        b('<td>'.$server[$v['server']]['desc']);
        }


      b('<td');
      if ($log_color['uri'])  b($log_color['uri']);
      b('>');
      //b('<div class="t7">');
      b(htmlspecialchars($v['uri']));
      //b('</div>');


      //b('<td class="t8">');
      //b($v['httpvn']);


      b('<td');
      if ($log_color['result'])  b($log_color['result']);
      b('>');
      b($db_result[$v['resultn']]);


      b('<td');
      if ($log_color['bytes'])  b($log_color['bytes']);
      b('>');
      b($v['bytes']);


      b('<td');
      if ($log_color['referer'])  b($log_color['referer']);
      b('>');
      b(htmlspecialchars($v['referer']));


      b('<td');
      if ($log_color['ua'])  b($log_color['ua']);
      b('>');

      if ($v['spcf']) {
        $uas_os      = ($v['spcf'] & 0x7F);
        $uas_bit     = ($v['spcf'] & 0x80) >> 7;
        $uas_browser = ($v['spcf'] & 0xFF00) >> 8;
        $uas_ver     = ($v['spcf'] & 0xFF0000) >> 16;
        $uas_min     = ($v['spcf'] & 0xFF000000) >> 24;
        }
      else  $uas_browser = $uas_os = $uas_ver = $uas_min = 0;

      if ($uas_os) {
        b('<hr class="o'.$db_uas_os[$uas_os]['i'].'">');
        if ($uas_bit)  b('<hr class="x">');
        }
      if ($uas_browser) {
        b('<hr class="b'.$db_uas_browser[$uas_browser]['i'].'">');
        if ($uas_ver) {
          b('<i>');
          b($uas_ver);
          if ($uas_min)  b('.'.$uas_min);
          b('</i> ');
          }
        }

      //if ($auth['id'] != 65506) {
        b('<td class="tf"');
        if ($log_color['ua'])  b($log_color['ua']);
        b('>');
        b(htmlspecialchars($v['ua']));
      //  }


      b('<td');
      if ($log_color['userx'])  b($log_color['userx']);
      b('>');
      b($v['userx']);


      b('<td');
      if ($log_color['ipf'])  b($log_color['ipf']);
      b('>');
      b(($v['@ipf'] == '0.0.0.0') ? '' : $v['@ipf']);
      }

    b('</table>');
    }

  else {
    b('<p class="p">Нет данных</b>');
    }

  }




  // ------------------------------------------- user-agent string search ------------------------------------------------ //

if ($act == 'uas') {
  include 'l/lib_ua.php';

    // ---- submenu ---- //
  //$submenu['Импорт'] = '/'.$mod.'/imp';
  //submenu();
    // ---- end: submenu ---- //



  b('<p class="h4">User-agent string</p>');
  b();
  b();



    // ---------------- поиск ---------------- //

  b('<table class="l">');

  b('<tr><td class="th" width="200">');
  b('User-agent string:');
  b('<td class="t">');
  b(form_t('@,f_search', '', 1000));
  
  b('</table>');



    // ---------------- DIV ---------------- //

    b('<style type="text/css">
hr {width: 12px; height: 12px; margin: 0 2px 0 0;}
i {display: inline; vertical-align: baseline; background-image: none; font-size: 7pt; color: #888; font-style: normal;}

');
    foreach ($db_uai as $kic=>$vic) {
      $resize = '';
      if ($kic == 'x') {
        $resize = 'width: 7px; height: 5px; vertical-align: 4px; margin: 0 2px 0 -2px; ';
	}

      $i = array_search($vic.'-12', $submenu_icons);
      if ($resize)  $i = array_search($vic, $submenu_icons);
      if ($i !== FALSE) {
        $x = ($i % 64) * 16;
        $y = floor($i / 64) * 16;

        b('hr.'.$kic.' {'.$resize.'background-position: '.($x?('-'.$x.'px'):'0').' '.($y?('-'.$y.'px'):'0').';}'."\n");
        }
      else {
        b('hr.'.$kic.' {display: none;}'."\n");
        }
      }
b('
}
</style>
');

  b('<div id="sch_result"></div>');


  b('<script>
$.event("f_search", "keyup", $.delay(function() {$.ajax("/'.$mod.'/uasa/?sch=" + $.id("f_search").value, function(r) {$.id("sch_result").innerHTML = r} ) }, 0.5) );
</script>');
  }






  // ------------------------------------------- edit uas ------------------------------------------------ //

if ($act == 'uae' && $guas) {
  include 'l/lib_ua.php';

  $uas = array('ua' => '',
               'type' => 0,
               'spcf' => 0,
               );

  if ($guas) {
    $col = array();
    foreach ($uas as $k=>$v)  $col[] = $k;

    $uas = db_read(array('table' => 'ua',
                         'col' => $col,
                         'where' => '`id` = '.$guas,
                         ));
    }

  $uas_os      = ($uas['spcf'] & 0x7F);
  $uas_bit     = ($uas['spcf'] & 0x80) >> 7;
  $uas_browser = ($uas['spcf'] & 0xFF00) >> 8;
  $uas_ver     = ($uas['spcf'] & 0xFF0000) >> 16;
  $uas_min     = ($uas['spcf'] & 0xFF000000) >> 24;


    // ---- submenu ---- //
  if (p() && $guas)  $submenu['?Удалить;minus-button'] = array('#Подтвердить;tick-button' => form_sbd('/'.$mod.'/uau/?uas='.$guas));
  submenu();
    // ---- end: submenu ---- //




  b('<p class="h1">');
  if (!$guas)  b('Добавление');
  else         b('Редактирование');
  b('</p>');
  b();
  b();

  b('<div style="font-size: 14pt;">');
  b($uas['ua']);
  b('</div>');
  b();
  b();


  b(form('uas', '/'.$mod.'/uau/?'
    .($guas ? '&uas='.$guas : '')
    ));

  b('<table class="edt">');


  b('<tr><td>');
  b('Операц. система:');
  b('<td>');
  b(form_s('f_uas_os;d', $db_uas_os, $uas_os));

  b(' <input name="f_uas_bit" type="radio" value="0"'.(($uas_bit == 0)?' checked':'').'> x86, ');
  b('<input name="f_uas_bit" type="radio" value="1"'.(($uas_bit == 1)?' checked':'').'> x64');


  b('<tr><td>');
  b('Браузер:');
  b('<td>');
  b(form_s('@f_uas_browser;d', $db_uas_browser, $uas_browser));


  b('<tr><td>');
  b('Версия:');
  b('<td>');
  b(form_t('f_uas_ver', ($uas_ver?$uas_ver:''), 100));


  b('<tr><td>');
  b('Субверсия:');
  b('<td>');
  b(form_t('f_uas_min', ($uas_min?$uas_min:''), 100));


  b('<tr><td>');
  b('Тип:');
  b('<td>');
  b(form_s('f_uas_type;d', $db_hit_type, $uas['type']));


  b('</table>');


  b(form_sb());

  b('</form>');
  }




  // ------------------------------------------- ajax: update ------------------------------------------------ //

if ($act == 'uau') {
  $ajax = TRUE;

  $post = postb('f_uas_browser');

  $table = 'ua';
  $where = '`id` = '.$guas;

  if ($post) {
    $set = array();
    $set['type'] = postn('f_uas_type');
    $set['spcf'] = postn('f_uas_os') + (postn('f_uas_bit') << 7) + (postn('f_uas_browser') << 8) + (postn('f_uas_ver') << 16) + (postn('f_uas_min') << 24);

    if ($guas) {
      db_write(array('table'=>$table, 'set'=>$set, 'where'=>$where));
      }

    else {
      //$guas = db_write(array('table'=>$table, 'set'=>$set));
      }

    }


    // ---- deletion ---- //
  if (!$post && $guas && p()) {
    //$result = db_write(array('table'=>$table, 'where'=>$where));
  
    //if ($result)  b('ok');
    //else          b('failed');
    }  // end: delete

  b('/'.$mod.'/uas/');
  }




  // ------------------------------------------------ ajax: User-agent string search ------------------------------------------------ //

if ($act == 'uasa') {
  $ajax = TRUE;
  //http_response_code(418);

  include 'l/lib_ua.php';

  function filter_uas($text) {
    $filter = array('0','1','2','3','4','5','6','7','8','9',
                    'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
                    'a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z',
                    ' ','(',')','-','.','/',',','_',';',':',  // '+',
                    );
    $text = filter($text, $filter);
    return  $text;
    }

  $gsch = filter_uas(get('sch'));

  if (!$gsch)  exit;

  $where = '`ua` LIKE \'%'.$gsch.'%\'';

  $uasc = db_read(array('table' => 'ua',
                        //'col' => '*',
                        'where' => $where,
                        ));

  $uas = db_read(array('table' => 'ua',
                       'col' => array('id', 'ua', 'type', 'spcf'),
                       'where' => $where,
                       'limit' => '1000',

                       'key' => 'id',
                       ));

  if ($uas) {
    b('<hr class="h">');
    b('<div style="font-size: 12pt; margin: 4px 0;">');
    b('Результатов поиска: <b>'.$uasc.'</b>');
    b('</div>');

    foreach ($uas as $k=>$v) {

      if ($v['spcf']) {
        $uas_os      = ($v['spcf'] & 0x7F);
        $uas_bit     = ($v['spcf'] & 0x80) >> 7;
        $uas_browser = ($v['spcf'] & 0xFF00) >> 8;
        $uas_ver     = ($v['spcf'] & 0xFF0000) >> 16;
        $uas_min     = ($v['spcf'] & 0xFF000000) >> 24;
        }
      else  $uas_browser = $uas_os = $uas_ver = $uas_min = 0;

      b('<div style="font-size: 12pt; margin: 4px 0;">');
      if ($uas_os) {
        //b('<i class="o'.$db_uas_os[$uas_os]['i'].'"></i>');
        b('<hr class="o'.$db_uas_os[$uas_os]['i'].'">');
        if ($uas_bit)  b('<hr class="x"></i>');
        }
      if ($uas_browser) {
        b('<hr class="b'.$db_uas_browser[$uas_browser]['i'].'">');
        if ($uas_ver) {
          b('<i>');
          b($uas_ver);
          if ($uas_min)  b('.'.$uas_min);
          b('</i> ');
          }
        }

      b('<a href="/'.$mod.'/uae/?uas='.$k.'"');
      if ($db_hit_type[$v['type']])  b(' style="'.( $db_hit_type[$v['type']]['tc'] ? 'color: '.$db_hit_type[$v['type']]['tc'] : '') . ( $db_hit_type[$v['type']]['bc'] ? 'background-color: '.$db_hit_type[$v['type']]['bc'] : '') .'"' );
      b('>');
      b(htmlspecialchars($v['ua']));
      b('</a>');

      b('</div>');
      }
    }

  }






  // ------------------------------------------- Раскраска (define type) ------------------------------------------------ //

if ($act == 'dft') {
  set_time_limit(3600 * 4);  // 4 hours

  if (!$gcnf) {
  
    $server = db_read(array('table' => 'server',
                            'col' => array('id', 'tp', 'desc'),
                            'key' => 'tp',
                            ));
    $server = tsort($server);

  
    b('<p class="h1">определение типа</p>');
    b();
  
    b('<form name="import" enctype="multipart/form-data" action="/'.$mod.'/'.$act.'/?cnf=1" method="post">');
  
    b('<table class="l">');
    b('<tr><td class="th" width="200">');
    b('Сервер:');
    b('<td class="t">');
    b('<select name="f_server" autofocus>');
    foreach ($server as $k=>$v)  b('<option value="'.$k.'"'.(($k == $gsrv)?' selected':'').'>'.$v['desc']);
    b('</select>');


    b('<tr><td class="t"><td class="t">&nbsp;');


    b('<tr><td class="th">');
    b('Начальная дата:');
    b('<td class="t">');
    b('<select name="f_date_d">');
    //b('<option value="0">- -');
    for ($i = 1; $i < 32; $i++)  b('<option value="'.$i.'">'.substr('00'.$i,-2,2));
    b('</select> ');

    b('<select name="f_date_m">');
    //b('<option value="0">- -');
    for ($i = 1; $i < 13; $i++)  b('<option value="'.$i.'">'.substr('00'.$i,-2,2));
    b('</select> ');

    b('<select name="f_date_y">');
    b('<option value="0">- - - -');
    for ($i = 2009; $i < 2021; $i++)  b('<option value="'.$i.'"'.(($i == $curr['year'])?' selected':'').'>'.$i);
    b('</select> г. ');


    b('</table>');

    b();
    b();
    b('<p class="center"><input type="submit" name="f_submit" value="продолжить"></p>');

    b('</form>');
    }
  
  
  else {

    $gsrv = postn('f_server');
    $gyear = post('f_date_y');
    $gmon = postn('f_date_m');
    $gday = postn('f_date_d');

    //include '../share/lp.php';
    include 'm/'.$mod.'/log_share.php';


    $where = array();
    $where[] = '`log`.`server` = '.$gsrv;
    if ($gyear)  $where[] = '`log`.`datetime` >= \''.datesql($gyear, $gmon, $gday).' 00:00:00\'';

    $count = db_read(array(
      'table' => 'log',
      'where' => $where,
      ));


    $where[] = '`ua`.`id` = `log`.`uan`';

    $n = 0;
    $nsize = 50000;
    $updates = 0;
    while (1) {
      $offset = $n * $nsize;

      $log = db_read(array('table' => array('log', 'ua'),
                           'col' => array('log`.`id', '@ip', 'log`.`datetime', 'log`.`type',
                                          'ua`.`type` AS `uatype',
                                          ),
                           'where' => $where,

                           'limit' => ($n*$nsize).','.$nsize,

                           'key' => 'id',
                           ));

      $update = array();
      foreach ($log as $k=>$v) {

        $type = 0;
        $ip = parse_ip($v['@ip'], substr($v['datetime'], 0, 10) );
        if ($ip['type'])  $type = $ip['type'];
        elseif ($v['uatype'])  $type = $v['uatype'];

        //if ($type)  $update[$k] = $type;
        //elseif ($v['extype'])  $update[$k] = 0;  // only for repaint
        if ($type != $v['type'])  $update[$k] = $type;
        }


      foreach ($update as $k=>$v) {
        db_write(array('table'=>'log', 'set'=>array('type'=>$v), 'where'=>'`id` = '.$k));
        $updates++;
        }


      echo $n.' of '.ceil($count/$nsize).' done<br>';

      $n++;
      if ($n * $nsize > $count)  break;
      }

    echo '<br>all done';
    echo '<br>made <b>'.$updates.'</b> updates';
    }

  }






  // ------------------------------------------- import ------------------------------------------------ //
  // ---------------------------------------------------- log to db --------------------------------------------------------- //

if ($act == 'imp') {
  set_time_limit(3600 * 4);  // 4 hours

  $imp_file = isset($_FILES['f_file']['tmp_name']) ? $_FILES['f_file']['tmp_name'] : FALSE;


    // -------------------------------- диалог выбора файла -------------------------------- //

  if (!$imp_file) {

    $server = db_read(array('table' => 'server',
                            'col' => array('id', 'tp', 'desc'),
                            'key' => 'tp',
                            ));
    $server = tsort($server);


    b('<p class="h1">Импорт</p>');
    b();

    b('<form name="import" enctype="multipart/form-data" action="/'.$mod.'/'.$act.'/" method="post">');

    b('<table class="l">');
    b('<tr><td class="t" width="150">');
    b('Сервер:');
    b('<td class="t">');
    b('<select name="f_server" autofocus>');
    foreach ($server as $k=>$v)  b('<option value="'.$k.'"'.(($k == $gsrv)?' selected':'').'>'.$v['desc']);
    b('</select>');

    b('<tr><td class="t"><td class="t">&nbsp;');

    b('<tr><td class="t">');
    b('Файл:');
    b('<td class="t">');
    b('<input name="f_file" type="file" size="25" onchange="submit();">');

    b('</table>');

    //b();
    //b();
    //b('<p class="center"><input type="submit" name="f_submit" value="выбрать"></p>');

    b('</form>');
    }

  else {

      // -------------------------------- валидация и импорт -------------------------------- //

    //include 'log_share.php';
    include 'm/'.$mod.'/log_share.php';

      // ---- collect logs ---- //

    if (!file_exists($imp_file))  die('error: file not exists.');
    $imp = fread (fopen ($imp_file, 'rb'), filesize ($imp_file) );

    $imp = explode("\n", strtr($imp, array("\r"=>'')));




    b('<p class="h4">Импорт логов Apache в БД</p>');
    b();


    $enable_echo = FALSE;
    $errorc = 0;
    $warningc = 0;

    if ($enable_echo) {
      b('<table class="f7">');
      b('<tr>');
      b('<td class="b" width="76">ip address');
      b('<td class="b" width="12">ln');
      b('<td class="b" width="12">ru');
      b('<td class="b" width="104">date, time');
      b('<td class="b" width="20">mt');
      b('<td class="b" width="20">mtn');
      b('<td class="b">uri');
      b('<td class="b" width="20">hv');
      b('<td class="b" width="20">hvn');
      b('<td class="b" width="24">rslt');
      b('<td class="b" width="50">bytes');
      b('<td class="b">referer');
      b('<td class="b">user agent');
      b('<td class="b" width="30">uan');
      b('<td class="b" width="40">userx');
      }

    foreach ($imp as $v) {
      if (!$v)  continue;
      //if (substr($v,0,3) == '::1')  continue;

      $line = log_parse_line($v);

      if ($enable_echo) {
        b('<tr>');

        b('<td class="li">'.$line['ip']);

        b('<td class="li">'.$line['lname']);
        b('<td class="li">'.$line['ruser']);

        b('<td class="li">'.dateh($line['datesql']));

        b('<td class="li">'.$line['method']);
        b('<td>'.$line['methodn']);

        b('<td class="li" style="min-width: 270px; max-width: 270px;  white-space: nowrap;  overflow: hidden;">'.$line['uri']);

        b('<td class="li">'.$line['httpv']);
        b('<td>'.$line['httpvn']);

        b('<td>'.$line['result']);
        b('<td class="li">'.$line['bytes']);
        b('<td class="li" style="min-width: 300px; max-width: 300px;  white-space: nowrap;  overflow: hidden;">'.$line['referer']);

        b('<td class="li" style="min-width: 300px; max-width: 300px;  white-space: nowrap;  overflow: hidden;">'.$line['ua']);
        b('<td class="li">');

        b('<td class="li">'.$line['userx']);
        }

      }

    if ($enable_echo)  b('</table>');


        // -------------------------------- second loop -------------------------------- //

    if (!$errorc && !$warningc) {
    //if (!$errorc && !$warningc && 0) {

      $new = 0;
      $imp_server = postn('f_server');

      foreach ($imp as $v) {
        if (!$v)  continue;
        //if (substr($v,0,3) == '::1')  continue;

        $line = log_parse_line($v);


        $uan = db_read(array('table' => 'ua',
                             'col' => 'id',
                             'where' => '`ua` = \''.addslashes($line['ua']).'\'',
                             ));

        if (!$uan) {
          $set = array();
          $set['ua'] = $line['ua'];
          $set['type'] = 0;
          $set['spcf'] = 0;

          $uan = db_write(array('table'=>'ua', 'set'=>$set));
          }


        $set = array();
        $set['server'] = $imp_server;
        $set['@ip'] = $line['ip'];
        //$set['lname'] = ($line['lname'] == '-' ? '' : $line['lname']);
        //$set['ruser'] = ($line['ruser'] == '-' ? '' : $line['ruser']);
        $set['datetime'] = $line['datesql'];
        $set['methodn'] = $line['methodn'];
        $set['uri'] = $line['uri'];
        $set['httpvn'] = $line['httpvn'];
        $set['resultn'] = $line['resultn'];
        $set['bytes'] = ($line['bytes'] == '-' ? 0 : $line['bytes']);
        $set['referer'] = ($line['referer'] == '-' ? '' : $line['referer']);
        $set['uan'] = $uan;
        $set['userx'] = ($line['userx'] == '-' ? 0 : $line['userx']);
        $set['@ipf'] = ($line['ipf'] == '-' ? '0.0.0.0' : $line['ipf']);
        $set['type'] = 0;

        db_write(array('table'=>'log', 'set'=>$set));

        $new++;
        }

      b('<p class="p">Импортировано строк: '.$new);
      }

    else {
      b();
      b();
      b('<p>Errors: <b>'.$errorc.'</b>');
      b('<p>Warnings: <b>'.$warningc.'</b>');
      }

    }


  }




  // -------------------------------- поиск роботов -------------------------------- //
/*
if ($act == 'stat') {

  $log = db_read(array('table' => 'log',
                       'col' => array('id', '@ip', 'datetime'),
                       'where' => array('`server` = 27',
                                        '`datetime` >= \'2013-09-01 00:00:00\'',
                                        '`datetime` < \'2013-11-01 00:00:00\'',
                                        //'`ip` = INET_ATON(\'0.0.0.0\')',
                                        ),
                       'order' => '`datetime`',

                       'key' => 'id',
                       ));
  b(count($log));


  $last = array();
  $stat = array();
  foreach ($log as $k=>$v) {

    if (!isset($last[$v['@ip']])) {
      $last[$v['@ip']] = datesqltime($v['datetime']);
      }
    else {
      $diff = round((datesqltime($v['datetime']) - $last[$v['@ip']]) / 60);

      $last[$v['@ip']] = datesqltime($v['datetime']);

      if (!isset($stat[$v['@ip']][$diff]))  $stat[$v['@ip']][$diff] = 0;
      $stat[$v['@ip']][$diff]++;
      }

    }


  $stat2 = array();
  foreach ($stat as $k=>$v) {

    foreach ($v as $kk=>$vv) {
      if ($kk > 4 && $vv > 20)  $stat2[$k][$kk] = $vv;
      }

    }


  foreach ($stat2 as $k=>$v) {
    b('<p>'.$k);

    b('<table class="f8">');
    foreach ($v as $kk=>$vv) {
      b('<tr>');
      b('<td width="100">');
      b($kk);

      b('<td width="100">');
      b($vv);
      }
    b('</table>');
    }

  }
*/

/*
if ($act == 'collate') {

  $log = db_read(array('table' => 'log',
                       'col' => array('id', '@ip', 'uri', 'datetime', 'userx'),
                       'where' => array('`server` = 22',
                                        '`datetime` >= \'2013-10-10 00:00:00\'',
                                        '`datetime` < \'2013-11-06 00:00:00\'',
                                        //'`ip` = INET_ATON(\'0.0.0.0\')',
                                        ),
                       'order' => '`datetime`',

                       'key' => 'id',
                       ));
  b(count($log));

  $log2 = array();
  foreach ($log as $v) {
    //if (substr($v['uri'],0,9) == '/main.php' || substr($v['uri'],0,9) == '/ajax.php') {
    if (substr($v['uri'],0,8) == '/img.php') {
      $log2[$v['datetime']][] = $v;
      }
    }


  //$imp_file = 't/'.$mod.'/2013-10-12';
  $imp_file = 't/'.$mod.'/logf';
  //$imp_file = 't/'.$mod.'/2013-11-05';
  if (!file_exists($imp_file))  die('error: file not exists.');
  $imp = fread (fopen ($imp_file, 'rb'), filesize ($imp_file) );

  $imp = explode("\n", strtr($imp, array("\r"=>'')));


  $logf = array();
  foreach ($imp as $v) {
    if (!$v)  continue;

    $imp_e = explode("\t", $v);
    $logf[$imp_e[0]][] = $imp_e;
    }


  b('<table class="f8">');
  $miss = 0;
  foreach ($log2 as $k=>$v) {
    foreach ($v as $kk=>$vv) {

    //  b('<tr>');
    //  b('<td class="li" width="80">');
    //  b($vv['@ip']);
    //
    //  b('<td class="li" width="120">');
    //  b($vv['datetime']);
    //
    //  b('<td class="li" width="300">');
    //  b($vv['uri']);
    //
    //  b('<td class="li" width="50">');
    //  b($vv['userx']);
    //
    //  if (substr($vv['uri'],0,9) == '/main.php' || substr($vv['uri'],0,9) == '/ajax.php') {
    //
    //    if (isset($logf[$k][$kk])) {
    //      b('<td class="li" width="50">');
    //      b($logf[$k][$kk][1]);
    //      }
    //    else {
    //      b('<td class="bgr" width="50">? ? ?');
    //      $miss++;
    //      }
    //    }
    //
    //  else {
    //    b('<td class="bgbe" width="50">- - -');
    //    }

      if (isset($logf[$k][$kk])) {
        //db_write('loga', array('userx'=>$logf[$k][$kk][1]), '`id`='.$vv['id'], 1,0 );
        //db_write('log', array('userx'=>$logf[$k][$kk][1]), '`id`='.$vv['id']);
        }

      else {
        b();
        b($vv['id']);
        $miss++;
        }

      }
    }
  b('</table>');

  b('<p>miss '.$miss);
  }
*/





  // ------------------------------------------------ re-parse ua ------------------------------------------------ //
/*
if ($act == 'pua') {

  include 'l/lib_ua.php';

  $ua = db_read(array('table' => 'ua',
                      'col' => array('id', 'ua', 'spcf'),
                      //'where' => array(),

                      'key' => 'id',
                      ));

  b('<p>Всего UA: <b>'.count($ua).'</b>');


  $ua2 = array();
  $changed = 0;
  foreach ($ua as $k=>$v) {
    $spcf = parse_ua($v['ua']);
//echo $spcf['v'].' - '.$spcf['m'].'<br>';
//echo (((int)$spcf['m'] >255) ? 255 : (int)$spcf['m']).'<br>';
    $spcf = $spcf['on'] + ($spcf['x'] << 7) + ($spcf['bn'] << 8) + ( (((int)$spcf['v'] >255) ? 255 : (int)$spcf['v']) << 16) + ( (((int)$spcf['m'] >127) ? 127 : (int)$spcf['m']) << 24);
//echo $spcf.'<br>';

    if ($spcf && $spcf != $v['spcf']) {
      db_write(array('table'=>'ua', 'set'=>array('spcf' => $spcf), 'where'=>'`id` = '.$k));
      $changed++;
      }
    }

  b('<p>Обновлено: <b>'.$changed.'</b>');
  }
*/





  // ------------------------------------------------------------------------ AJAX -------------------------------------------------------------- //


  // ------------------------------------------------ График - Месяц ------------------------------------------------ //

if ($act == 'grm') {
  $ajax = TRUE;

  if (!$gday) {
    $dateb = datesql($gyear, $gmon, 1);
    $datee = datesql($gyear, $gmon, date('t', mktime (0,0,0, $gmon, 1, $gyear)) );
    }
  else {
    $dateb = datesql($gyear, $gmon, $gday);
    $datee = datesql($gyear, $gmon, $gday);
    }


  $date = array();

  $datet = $dateb;
  $date[] = $datet;

  while ($datet != $datee) {
    $datet = datesql(mktime (0,0,0, datee($datet, 'm'), datee($datet, 'd') +1, datee($datet)));
    $date[] = $datet;
    }



    // -------- collecting data -------- //

  $where = array();
  //$where[] = '`ua`.`id` = `log`.`uan`';
  //$where[] = '`server` = '.$gsrv;
  $where[] = '`datetime` >= \''.$dateb.' 00:00:00\'';
  $where[] = '`datetime` <= \''.$datee.' 23:59:59\'';
  if ($gsrv)  $where[] = '`server` = '.$gsrv;
  if ($gip)  $where[] = '`ip` '.($gipn?'!':'').'= INET_ATON(\''.$gipw.'\')';
  if ($grst)  $where[] = '`result` = '.$grst;
  if ($guan)  $where[] = '`uan` = '.$guan;
  if ($gusr)  $where[] = '`userx` = '.$gusr;
  if ($gipf)  $where[] = '`ipf` = INET_ATON(\''.$gipf.'\')';


  $log = db_read(array('table' => 'log',
                       'col' => array('id', '@ip', 'datetime', 'type'),
                       'where' => $where,
                       'key' => 'id',
                       ));


  $set = array();
  foreach ($log as $v) {
    $dt = substr($v['datetime'], 0, 16);

    if (!isset($set[$dt])) {
      $set[$dt] = $v['type'];
      }

    elseif ( $db_hit_type[$set[$dt]]['m']  <  $db_hit_type[$v['type']]['m']  ) {
      $set[$dt] = $v['type'];
      }

    }



  $par['h'] = 144;
  $par['w'] = count($date) * 10;

  $image_graph = imagecreatetruecolor ($par['w'], $par['h']);

  $transp = imagecolorallocate ($image_graph, 255, 255, 255);
  imagecolortransparent ($image_graph, $transp);
  $black = imagecolorallocate ($image_graph, 0, 0, 0);


    // ---------------- рисуем график ---------------- //

  imagefilledrectangle ($image_graph, 0, 0, $par['w']-1, $par['h']-1, $transp);

  $color = array();
  foreach ($date as $k=>$v) {

    $ox = $k * 10;
    //$oy = 7;

    for ($y = 0; $y < 144; $y++) {
      for ($x = 0; $x < 10; $x++) {
        $tmp = $y * 10 + $x;
        $dt = $v.' '. substr('00'.floor($tmp / 60), -2,2) .':'. substr('00'.($tmp % 60), -2,2);
        if (isset($set[$dt])) {
          if (!isset($color[$set[$dt]]))  $color[$set[$dt]] = imagecolorallocate ($image_graph, hexdec(substr($db_hit_type[$set[$dt]]['c'],1,1).substr($db_hit_type[$set[$dt]]['c'],1,1)), hexdec(substr($db_hit_type[$set[$dt]]['c'],2,1).substr($db_hit_type[$set[$dt]]['c'],2,1)), hexdec(substr($db_hit_type[$set[$dt]]['c'],3,1).substr($db_hit_type[$set[$dt]]['c'],3,1)));
          imagesetpixel ($image_graph, $x+$ox, $y, $color[$set[$dt]]);
          }
        }
      }

    }



    // ---------------- сетка ---------------- //

  $image = imagecreatetruecolor ($par['w']*$gscl, $par['h']*$gscl+14);

  $black = imagecolorallocate ($image, 0, 0, 0);
  $white = imagecolorallocate ($image, 255, 255, 255);
  $red = imagecolorallocate ($image, 255, 0, 0);
  $green = imagecolorallocate ($image, 0, 255, 0);
  $blue = imagecolorallocate ($image, 0, 0, 255);

  $grey = imagecolorallocate ($image, 128, 128, 128);
  $bg_even = imagecolorallocate ($image, 216, 216, 216);
  $bg_odd = imagecolorallocate ($image, 232, 232, 232);
  $bg_weekend = imagecolorallocate ($image, 255, 216, 216);


  imagefilledrectangle ($image, 0, 0, $par['w']*$gscl-1, $par['h']*$gscl-1 +14, $white);


    // -------- дни недели -------- //
  foreach ($date as $k=>$v) {

    $day = datee($v, 'd');
    $mon_day = date('w', mktime(0,0,0, datee($v, 'm'), $day, datee($v)));
    if ($mon_day) {
      if ($day%2)  $bg_color = $bg_even;  else  $bg_color = $bg_odd;
      $text_color = $black;
      }
    else {
      $bg_color = $bg_weekend;
      $text_color = $red;
      }

    imagefilledrectangle ($image, $k*$gscl*10, 0, (($k+1)*$gscl*10)-1, 144*$gscl, $bg_color);


    $box = imagettfbbox (10, 0, 'i/tahoma.ttf', $day);
    imagettftext ($image, 10, 0, ($k*$gscl*10 + ($gscl*5) - round(($box[2] - $box[0]) / 2)), ($par['h']*$gscl+12), $text_color, 'i/tahoma.ttf', $day);
    //imagettftext ($image, 10, 0, (30 + 15) , 100, $text_color, 'i/tahoma.ttf', $day);
    }

  //imageline ($image, 0, 433, $par['w']*3-1, 433, $grey);  // горизонтальная линия

  imageline ($image, 0, (6*$gscl)*3, $par['w']*$gscl-1, (6*$gscl)*3, $grey);    // 3:00
  imageline ($image, 0, (6*$gscl)*9, $par['w']*$gscl-1, (6*$gscl)*9, $red);     // 9:00
  imageline ($image, 0, (6*$gscl)*12, $par['w']*$gscl-1, (6*$gscl)*12, $grey);  // 12:00
  imageline ($image, 0, (6*$gscl)*15, $par['w']*$gscl-1, (6*$gscl)*15, $grey);  // 15:00
  imageline ($image, 0, (6*$gscl)*18, $par['w']*$gscl-1, (6*$gscl)*18, $red);   // 18:00
  imageline ($image, 0, (6*$gscl)*21, $par['w']*$gscl-1, (6*$gscl)*21, $grey);  // 21:00
  //imageline ($image, $offset_x-1, 109, $offset_x + $par['w'], 109, $red);  // 17:00

  //imagettftext ($image, 10, 0, 20, 50, $black, '../../p/tahoma.ttf', $gusr);

  //$box = imagettfbbox ($par['f'], 0, 'p/tahoma.ttf', $v);
  //imagettftext ($image, $par['f'], 0, $y, $x, $black, 'p/tahoma.ttf', $v);
  //$image = imagerotate ($image, 90, $black);

  imagecopyresized ($image, $image_graph, 0,0 , 0,0 , $par['w']*$gscl , $par['h']*$gscl , $par['w'] , $par['h'] );


  header('Content-Type: image/png');

  imagepng ($image);
  }






  // ------------------------------------------------ График - День ------------------------------------------------ //
/*
if ($act == 'grd') {
  $ajax = TRUE;

  $date = datesql($gyear, $gmon, $gday);


    // -------- collecting data -------- //

  $where = array();
  //$where[] = '`server` = '.$gsrv;
  $where[] = '`datetime` >= \''.$date.' 00:00:00\'';
  $where[] = '`datetime` <= \''.$date.' 23:59:59\'';
  if ($gsrv)  $where[] = '`server` = '.$gsrv;
  if ($gip)  $where[] = '`ip` '.($gipn?'!':'').'= INET_ATON(\''.$gipw.'\')';
  if ($grst)  $where[] = '`result` = '.$grst;
  if ($guan)  $where[] = '`uan` = '.$guan;
  if ($gusr)  $where[] = '`userx` = '.$gusr;
  if ($gipf)  $where[] = '`ipf` = INET_ATON(\''.$gipf.'\')';


  $log = db_read(array('table' => 'log',
                       'col' => array('id', '@ip', 'datetime', 'type'),
                       'where' => $where,
                       'key' => 'id',
                       ));

  $set = array();
  foreach ($log as $v) {

    if (!isset($set[$v['datetime']])) {
      $set[$v['datetime']] = $v['type'];
      }

    elseif ( $db_hit_type[$set[$v['datetime']]]['m']  <  $db_hit_type[$v['type']]['m']  ) {
      $set[$v['datetime']] = $v['type'];
      }

    }


  $par['h'] = 192;
  $par['w'] = 450;

  $image_graph = imagecreatetruecolor ($par['w'], $par['h']);

  $transp = imagecolorallocate ($image_graph, 255, 255, 255);
  imagecolortransparent ($image_graph, $transp);
  $black = imagecolorallocate ($image_graph, 0, 0, 0);


    // ---------------- рисуем график ---------------- //

  imagefilledrectangle ($image_graph, 0, 0, $par['w']-1, $par['h']-1, $transp);

  $color = array();

  for ($y = 0; $y < $par['h']; $y++) {
    for ($x = 0; $x < $par['w']; $x++) {
      $tmp = $y * $par['w'] + $x;
      $sec = substr('00'.($tmp % 60), -2,2);
      $tmp = ($tmp - $sec) / 60;
      $min = substr('00'.($tmp % 60), -2,2);
      $hour = substr('00'.floor($tmp / 60), -2,2);

      $dt = $date.' '. $hour.':'.$min.':'.$sec;

      if (isset($set[$dt])) {
        if (!isset($color[$set[$dt]]))  $color[$set[$dt]] = imagecolorallocate ($image_graph, hexdec(substr($db_hit_type[$set[$dt]]['c'],1,1).substr($db_hit_type[$set[$dt]]['c'],1,1)), hexdec(substr($db_hit_type[$set[$dt]]['c'],2,1).substr($db_hit_type[$set[$dt]]['c'],2,1)), hexdec(substr($db_hit_type[$set[$dt]]['c'],3,1).substr($db_hit_type[$set[$dt]]['c'],3,1)));
        imagesetpixel ($image_graph, $x, $y, $color[$set[$dt]]);
        }
      }
    }


    // ---------------- сетка ---------------- //

  $image = imagecreatetruecolor ($par['w']*$gscl, $par['h']*$gscl);

  $black = imagecolorallocate ($image, 0, 0, 0);
  $white = imagecolorallocate ($image, 255, 255, 255);
  $red = imagecolorallocate ($image, 255, 0, 0);
  $green = imagecolorallocate ($image, 0, 255, 0);
  $blue = imagecolorallocate ($image, 0, 0, 255);

  $grey = imagecolorallocate ($image, 128, 128, 128);
  $bg = imagecolorallocate ($image, 240, 240, 240);


  imagefilledrectangle ($image, 0, 0, $par['w']*$gscl-1, $par['h']*$gscl-1, $bg);


  $scly = 8;
  imageline ($image, 0, ($scly*$gscl)*3,  $par['w']*$gscl-1, ($scly*$gscl)*3, $grey);    // 3:00
  imageline ($image, 0, ($scly*$gscl)*9,  $par['w']*$gscl-1, ($scly*$gscl)*9, $red);     // 9:00
  imageline ($image, 0, ($scly*$gscl)*12, $par['w']*$gscl-1, ($scly*$gscl)*12, $grey);  // 12:00
  imageline ($image, 0, ($scly*$gscl)*15, $par['w']*$gscl-1, ($scly*$gscl)*15, $grey);  // 15:00
  imageline ($image, 0, ($scly*$gscl)*18, $par['w']*$gscl-1, ($scly*$gscl)*18, $red);   // 18:00
  imageline ($image, 0, ($scly*$gscl)*21, $par['w']*$gscl-1, ($scly*$gscl)*21, $grey);  // 21:00


  imagecopyresized ($image, $image_graph, 0,0 , 0,0 , $par['w']*$gscl , $par['h']*$gscl , $par['w'] , $par['h'] );


  header('Content-Type: image/png');

  imagepng ($image);
  }
*/





  // ------------------------------------------------ График на год ------------------------------------------------ //
/*
if ($act == 'gry') {
  $ajax = TRUE;

  $dateb = datesql($gyear, 1, 1);
  $datee = datesql($gyear, 12, 31);
  $days = 365 + date('L', mktime (0,0,0, 1, 1, $gyear));


    // -------- collecting data -------- //

  $par['w'] = $days * 4;
  $par['h'] = 360 + 12;

  $image = imagecreatetruecolor ($par['w'], $par['h']);
  $image_text = imagecreatetruecolor ($par['w'], 12);

  $transp = imagecolorallocate ($image, 255, 255, 255);
  imagecolortransparent ($image, $transp);
  $black = imagecolorallocate ($image, 0, 0, 0);

  $white = imagecolorallocate ($image, 255, 255, 255);
  $red = imagecolorallocate ($image, 255, 0, 0);
  $green = imagecolorallocate ($image, 0, 255, 0);
  $blue = imagecolorallocate ($image, 0, 0, 255);

  $grey = imagecolorallocate ($image, 128, 128, 128);
  $bg_even = imagecolorallocate ($image, 216, 216, 216);
  $bg_odd = imagecolorallocate ($image, 232, 232, 232);
  $bg_weekend = imagecolorallocate ($image, 255, 216, 216);


  $red_text = imagecolorallocate ($image_text, 255, 0, 0);
  $black_text = imagecolorallocate ($image_text, 0, 0, 0);
  $transp_text = imagecolorallocate ($image_text, 255, 255, 255);
  imagecolortransparent ($image_text, $transp_text);


  imagefilledrectangle ($image, 0, 0, $par['w']-1, $par['h']-1, $white);  // $green  $white
  imagefilledrectangle ($image_text, 0, 0, $par['w']-1, 11, $transp_text);


    // ---------------- сетка ---------------- //

  imageline ($image, 0, (15)*9, $par['w']-1, (15)*9, $red);    // 9:00
  imageline ($image, 0, (15)*18, $par['w']-1, (15)*18, $red);  // 18:00


    // ---------------- рисуем график ---------------- //


  $where = array();
  //$where[] = '`ua`.`id` = `log`.`uan`';
  //$where[] = '`server` = '.$gsrv;
  //$where[] = '`datetime` >= \''.$dateb.' 00:00:00\'';
  //$where[] = '`datetime` <= \''.$datee.' 23:59:59\'';
  if ($gsrv)  $where[] = '`server` = '.$gsrv;
  if ($gip)  $where[] = '`ip` '.($gipn?'!':'').'= INET_ATON(\''.$gipw.'\')';
  if ($grst)  $where[] = '`result` = '.$grst;
  if ($guan)  $where[] = '`uan` = '.$guan;
  if ($gusr)  $where[] = '`userx` = '.$gusr;
  if ($gipf)  $where[] = '`ipf` = INET_ATON(\''.$gipf.'\')';


  $color = array();

  $datet = $dateb;
  $dateb_t = FALSE;
  $datee_t = FALSE;

  $n = 0;
  while (1) {

    if ($dateb_t === FALSE)  $dateb_t = $datet;



      // ----------------------- //

    $mon_day = date('w', mktime(0,0,0, datee($datet, 'm'), datee($datet, 'd'), datee($datet)));
    if ($mon_day) {
      if ($n%2)  $bg_color = $bg_even;  else  $bg_color = $bg_odd;
      $text_color = $black;
      }
    else {
      $bg_color = $bg_weekend;
      $text_color = $red;
      }

    imagefilledrectangle ($image, $n*4, 0, $n*4+3, 359, $bg_color);


      // ----------------------- //

    imagefilledrectangle ($image, $n*4, 360, $n*4+3, 371, ((datee($datet, 'm') % 2) ? $bg_even : $bg_odd) );


    $n++;
    if (!($n % 5)) {
      $box = imagettfbbox (7, 0, 'i/tahoma.ttf', datee($datet, 'd'));
      imagettftext ($image_text, 7, 0, ($n*4 - 2 - round(($box[2] - $box[0]) / 2)), 10, $text_color, 'i/tahoma.ttf', datee($datet, 'd'));
      }



      // ----------------------- //

    if (!($n % 30) || $n >= $days) {
      $datee_t = $datet;

      $where2 = $where;
      $where2[] = '`datetime` >= \''.$dateb_t.' 00:00:00\'';
      $where2[] = '`datetime` <= \''.$datee_t.' 23:59:59\'';

      $log = db_read(array('table' => 'log',
                           'col' => array('id', '@ip', 'datetime', 'type'),
                           'where' => $where2,

                           'key' => 'id',
                           ));

      if (!$log)  $log = array();


      $set = array();
      foreach ($log as $v) {
        $dt = substr($v['datetime'], 0, 16);
        //$ip = parse_ip($v['@ip']);

        if (!isset($set[$dt])) {
          $set[$dt] = $v['type'];
          }

        elseif ( $db_hit_type[$set[$dt]]['m']  <  $db_hit_type[$v['type']]['m']  ) {
          $set[$dt] = $v['type'];
          }

        //$px_color = '#000';
        //if (isset($set[$dt]))  $px_color = $set[$dt];
        //
        //
        //if ($ip['color'] == '#f00') {
        //  $px_color = '#f00';
        //  }
        //
        //elseif (isset($set[$dt]) && $px_color != '#fc8') {
        //  $px_color = $ip['color'];
        //  }
        //
        //elseif (!isset($set[$dt])) {
        //  if ($ip['color'])  $px_color = $ip['color'];
        //  elseif ($v['type'] == 1)  $px_color = '#fc8';
        //  }
        //
        //else {
        //  // preserve current color
        //  }

         //                1
         // 1       9      6
         // 2013-11-11 23:59:09
        //$set[$dt] = $px_color;
        }


      foreach ($set as $k=>$v) {
        $dt_z = date('z', mktime(0,0,0, datee($k, 'm'), datee($k, 'd'), $gyear));
        $dt_hi = datee($k, 'h') * 60 + datee($k, 'i');
        $dt_x = $dt_hi % 4;
        $dt_y = floor($dt_hi / 4);

         //                1
         // 1       9      6
         // 2013-11-11 23:59:09

        if (!isset($color[$v]))  $color[$v] = imagecolorallocate ($image, hexdec(substr($db_hit_type[$v]['c'],1,1).substr($db_hit_type[$v]['c'],1,1)), hexdec(substr($db_hit_type[$v]['c'],2,1).substr($db_hit_type[$v]['c'],2,1)), hexdec(substr($db_hit_type[$v]['c'],3,1).substr($db_hit_type[$v]['c'],3,1)));
        imagesetpixel ($image, $dt_z*4 + $dt_x, $dt_y, $color[$v]);
        }


      $dateb_t = FALSE;
      $datee_t = FALSE;
      }

    if ($n >= $days)  break;


    $datet = datesql(mktime (0,0,0, datee($datet, 'm'), datee($datet, 'd') +1, datee($datet)));
    }

  imagecopymerge ($image, $image_text, 0,360, 0,0, $par['w'],12, 100);


  header('Content-Type: image/png');

  imagepng ($image);
  }
*/





  // ------------------------------------------------ График на год (3) ------------------------------------------------ //

if ($act == 'gry3') {
  $ajax = TRUE;

  $dateb = datesql($gyear, 1, 1);
  $datee = datesql($gyear, 12, 31);
  $days = 365 + date('L', mktime (0,0,0, 1, 1, $gyear));


    // -------- collecting data -------- //

  $par['w'] = $days * 3;
  $par['h'] = 480 + 12;

  $image = imagecreatetruecolor ($par['w'], $par['h']);
  $image_text = imagecreatetruecolor ($par['w'], 12);

  $transp = imagecolorallocate ($image, 255, 255, 255);
  imagecolortransparent ($image, $transp);
  $black = imagecolorallocate ($image, 0, 0, 0);

  $white = imagecolorallocate ($image, 255, 255, 255);
  $red = imagecolorallocate ($image, 255, 0, 0);
  $green = imagecolorallocate ($image, 0, 255, 0);
  $blue = imagecolorallocate ($image, 0, 0, 255);

  $grey = imagecolorallocate ($image, 128, 128, 128);
  $bg_even = imagecolorallocate ($image, 216, 216, 216);
  $bg_odd = imagecolorallocate ($image, 232, 232, 232);
  $bg_weekend = imagecolorallocate ($image, 255, 216, 216);


  $red_text = imagecolorallocate ($image_text, 255, 0, 0);
  $black_text = imagecolorallocate ($image_text, 0, 0, 0);
  $transp_text = imagecolorallocate ($image_text, 255, 255, 255);
  imagecolortransparent ($image_text, $transp_text);


  imagefilledrectangle ($image, 0, 0, $par['w']-1, $par['h']-1, $white);  // $green  $white
  imagefilledrectangle ($image_text, 0, 0, $par['w']-1, 11, $transp_text);


    // ---------------- сетка ---------------- //

  imageline ($image, 0, (15)*9, $par['w']-1, (15)*9, $red);    // 9:00
  imageline ($image, 0, (15)*18, $par['w']-1, (15)*18, $red);  // 18:00


    // ---------------- рисуем график ---------------- //


  $where = array();
  //$where[] = '`ua`.`id` = `log`.`uan`';
  //$where[] = '`server` = '.$gsrv;
  //$where[] = '`datetime` >= \''.$dateb.' 00:00:00\'';
  //$where[] = '`datetime` <= \''.$datee.' 23:59:59\'';
  if ($gsrv)  $where[] = '`server` = '.$gsrv;
  if ($gip)  $where[] = '`ip` '.($gipn?'!':'').'= INET_ATON(\''.$gipw.'\')';
  if ($grst)  $where[] = '`result` = '.$grst;
  if ($guan)  $where[] = '`uan` = '.$guan;
  if ($gusr)  $where[] = '`userx` = '.$gusr;
  if ($gipf)  $where[] = '`ipf` = INET_ATON(\''.$gipf.'\')';


  $color = array();

  $datet = $dateb;
  $dateb_t = FALSE;
  $datee_t = FALSE;

  $n = 0;
  while (1) {

    if ($dateb_t === FALSE)  $dateb_t = $datet;



      // ----------------------- //

    $mon_day = date('w', mktime(0,0,0, datee($datet, 'm'), datee($datet, 'd'), datee($datet)));
    if ($mon_day) {
      if ($n%2)  $bg_color = $bg_even;  else  $bg_color = $bg_odd;
      $text_color = $black;
      }
    else {
      $bg_color = $bg_weekend;
      $text_color = $red;
      }

    imagefilledrectangle ($image, $n*3, 0, $n*3+2, 479, $bg_color);


      // ----------------------- //

    imagefilledrectangle ($image, $n*3, 480, $n*4+3, 491, ((datee($datet, 'm') % 2) ? $bg_even : $bg_odd) );


    $n++;
    if (!($n % 5)) {
      $box = imagettfbbox (7, 0, 'i/tahoma.ttf', datee($datet, 'd'));
      imagettftext ($image_text, 7, 0, ($n*4 - 2 - round(($box[2] - $box[0]) / 2)), 10, $text_color, 'i/tahoma.ttf', datee($datet, 'd'));
      }



      // ----------------------- //

    if (!($n % 30) || $n >= $days) {
      $datee_t = $datet;

      $where2 = $where;
      $where2[] = '`datetime` >= \''.$dateb_t.' 00:00:00\'';
      $where2[] = '`datetime` <= \''.$datee_t.' 23:59:59\'';

      $log = db_read(array('table' => 'log',
                           'col' => array('id', '@ip', 'datetime', 'type'),
                           'where' => $where2,

                           'key' => 'id',
                           ));

      if (!$log)  $log = array();


      $set = array();
      foreach ($log as $v) {
        $dt = substr($v['datetime'], 0, 16);

        if (!isset($set[$dt])) {
          $set[$dt] = $v['type'];
          }

        elseif ( $db_hit_type[$set[$dt]]['m']  <  $db_hit_type[$v['type']]['m']  ) {
          $set[$dt] = $v['type'];
          }

        }


      foreach ($set as $k=>$v) {
        $dt_z = date('z', mktime(0,0,0, datee($k, 'm'), datee($k, 'd'), $gyear));
        $dt_hi = datee($k, 'h') * 60 + datee($k, 'i');
        $dt_x = $dt_hi % 3;
        $dt_y = floor($dt_hi / 3);

         //                1
         // 1       9      6
         // 2013-11-11 23:59:09

        if (!isset($color[$v]))  $color[$v] = imagecolorallocate ($image, hexdec(substr($db_hit_type[$v]['c'],1,1).substr($db_hit_type[$v]['c'],1,1)), hexdec(substr($db_hit_type[$v]['c'],2,1).substr($db_hit_type[$v]['c'],2,1)), hexdec(substr($db_hit_type[$v]['c'],3,1).substr($db_hit_type[$v]['c'],3,1)));
        imagesetpixel ($image, $dt_z*3 + $dt_x, $dt_y, $color[$v]);
        }


      $dateb_t = FALSE;
      $datee_t = FALSE;
      }

    if ($n >= $days)  break;


    $datet = datesql(mktime (0,0,0, datee($datet, 'm'), datee($datet, 'd') +1, datee($datet)));
    }

  imagecopymerge ($image, $image_text, 0,480, 0,0, $par['w'],12, 100);


  header('Content-Type: image/png');

  imagepng ($image);
  }






  // ------------------------------------------------ График - День (server) ------------------------------------------------ //

if ($act == 'grds') {
//echo floor(log(4, 4)) +1;
//die;
  $ajax = TRUE;

  $gq = 3;  // quantizer (minutes in pixel)
  $gs = 2;  // scale
  $gsh = 12;  // scale height
  $gsf = 3;  // scale factor
// (2 - 1, 2, 4, 8, 16, 32)
// (3 - 1, 3, 9, 27, 81, 243)
// (4 - 1, 4, 16, 64, 256, 1024)

  $date = datesql($gyear, $gmon, $gday);


  $server = db_read(array('table' => 'server',
                          'col' => array('id', 'tp', 'desc'),
                          'key' => 'tp',
                          ));
  $server = tsort($server);


    // -------- collecting data -------- //

  $where = array();
  $where[] = '`datetime` >= \''.$date.' 00:00:00\'';
  $where[] = '`datetime` <= \''.$date.' 23:59:59\'';
  if ($gip)  $where[] = '`ip` '.($gipn?'!':'').'= INET_ATON(\''.$gipw.'\')';
  if ($grst)  $where[] = '`result` = '.$grst;
  if ($guan)  $where[] = '`uan` = '.$guan;
  if ($gusr)  $where[] = '`userx` = '.$gusr;
  if ($gipf)  $where[] = '`ipf` = INET_ATON(\''.$gipf.'\')';


  $log = db_read(array('table' => 'log',
                       'col' => array('id', '@ip', 'datetime', 'type', 'server'),
                       'where' => $where,
                       'key' => 'id',
                       ));

  $set = array();
  $sett = array();
  foreach ($log as $v) {
    $mi = datee($v['datetime'],'h') * 60 + datee($v['datetime'],'i');
    $mi = floor($mi/$gq);

    $set[$v['server']][$mi][] = TRUE;
    if (!isset($sett[$v['server']][$mi])) {
      $sett[$v['server']][$mi] = $v['type'];
      }
    }


  $gw = 1440 / $gq;
  $gh = count($server);
  $ghb = $gsh / $gs;  // height of bar in non stretched pixels
  $ghp = $gh * $ghb;  // total height in non stretched pixels

  //$imageg = imagecreatetruecolor ($gw, $gh);
  $imageg = imagecreatetruecolor ($gw, $ghp);

  $transp = imagecolorallocate ($imageg, 255, 255, 255);
  imagecolortransparent ($imageg, $transp);
  $black = imagecolorallocate ($imageg, 0, 0, 0);
  $red = imagecolorallocate ($imageg, 255, 0, 0);


    // ---------------- рисуем график ---------------- //

  imagefilledrectangle ($imageg, 0, 0, $gw-1, $ghp-1, $transp);

  $color = array();
  foreach ($db_hit_type as $k=>$v) {
    $color[$k] = imagecolorallocate ($imageg, hexdec(substr($db_hit_type[$k]['c'],1,1).substr($db_hit_type[$k]['c'],1,1)), hexdec(substr($db_hit_type[$k]['c'],2,1).substr($db_hit_type[$k]['c'],2,1)), hexdec(substr($db_hit_type[$k]['c'],3,1).substr($db_hit_type[$k]['c'],3,1)));
    }

  $y = 0;
  foreach ($server as $sk=>$sv) {
    for ($x = 0; $x < $gw; $x++) {
      if (isset($set[$sk][$x])) {
        //imagesetpixel ($imageg, $x, $y, $black);
        //if (!isset($color[$set[$sk][$x]]))  $color[$set[$sk][$x]] = imagecolorallocate ($imageg, hexdec(substr($db_hit_type[$set[$sk][$x]]['c'],1,1).substr($db_hit_type[$set[$sk][$x]]['c'],1,1)), hexdec(substr($db_hit_type[$set[$sk][$x]]['c'],2,1).substr($db_hit_type[$set[$sk][$x]]['c'],2,1)), hexdec(substr($db_hit_type[$set[$sk][$x]]['c'],3,1).substr($db_hit_type[$set[$sk][$x]]['c'],3,1)));
        //imagesetpixel ($imageg, $x, $y, $color[$set[$sk][$x]]);

        $bar_height = floor(log(count($set[$sk][$x]), $gsf)) +1;
        //$bar_height = floor(log(102400, 4)) +1;

        if ($bar_height > $ghb)  $bar_height = $ghb;
        //imagefilledrectangle ($imageg, $x, ($y * $ghb), $x, (($y+1) * $ghb)-1, $color[$sett[$sk][$x]]);
        imagefilledrectangle ($imageg, $x, (($y+1) * $ghb) -$bar_height, $x, (($y+1) * $ghb)-1, $color[$sett[$sk][$x]]);
        }
      }
    $y++;
    }


    // ---------------- сетка ---------------- //
  $gto = 100;
  $gws = $gw*$gs + $gto;
  //$ghs = $gh*$gsh;
  $ghs = $ghp * $gs;

  $image = imagecreatetruecolor ($gws, $ghs);

  $black = imagecolorallocate ($image, 0, 0, 0);
  $white = imagecolorallocate ($image, 255, 255, 255);
  $red = imagecolorallocate ($image, 255, 0, 0);
  $green = imagecolorallocate ($image, 0, 255, 0);
  $blue = imagecolorallocate ($image, 0, 0, 255);

  $grey = imagecolorallocate ($image, 128, 128, 128);
  $greyt = imagecolorallocate ($image, 192, 192, 192);
  $greyl = imagecolorallocate ($image, 216, 216, 216);
  $bg = imagecolorallocate ($image, 240, 240, 240);


  imagefilledrectangle ($image, 0, 0, $gws-1, $ghs-1, $bg);


  $colory = array();

  $y = 0;
  //foreach ($setc as $sk=>$sv) {
  foreach ($server as $sk=>$sv) {
    //if (!isset($colory[$sett[$sk]]))  $colory[$sett[$sk]] = imagecolorallocate ($image, hexdec(substr($db_hit_type[$sett[$sk]]['c'],1,1).substr($db_hit_type[$sett[$sk]]['c'],1,1)), hexdec(substr($db_hit_type[$sett[$sk]]['c'],2,1).substr($db_hit_type[$sett[$sk]]['c'],2,1)), hexdec(substr($db_hit_type[$sett[$sk]]['c'],3,1).substr($db_hit_type[$sett[$sk]]['c'],3,1)));
    //imagesetpixel ($image, $x, $y, $color[$sett[$sk]]);

    imageline ($image, 0, ($y+1)*$gsh-0,  $gws-1, ($y+1)*$gsh-0, $greyl);
    imagettftext ($image, 8, 0, 1, ($y+1)*$gsh-2, (isset($set[$sk])?$black:$greyt), 'i/tahoma.ttf', $sv['desc']);
    //imagettftext ($image, 8, 0, 1, ($y+1)*$gsh-2, $colory[$sett[$sk]], 'i/tahoma.ttf', $sk);
    //$box = imagettfbbox (8, 0, 'p/tahoma.ttf', $sk);
    //imagettftext ($image, 8, 0, 98-($box[2] - $box[0]), ($y+1)*$gsh-2, (isset($set[$sk])?$black:$greyt), 'p/tahoma.ttf', $sk);
    $y++;
    }

  imageline ($image, $gto-1, 0,  $gto-1, $ghs, $black);    // 0:00
  imageline ($image, 60/$gq*$gs*3  +$gto, 0,  60/$gq*$gs*3  +$gto, $ghs, $greyt);  // 3:00
  imageline ($image, 60/$gq*$gs*9  +$gto, 0,  60/$gq*$gs*9  +$gto, $ghs, $red);    // 9:00
  imageline ($image, 60/$gq*$gs*12 +$gto, 0,  60/$gq*$gs*12 +$gto, $ghs, $greyt);  // 12:00
  imageline ($image, 60/$gq*$gs*15 +$gto, 0,  60/$gq*$gs*15 +$gto, $ghs, $greyt);  // 15:00
  imageline ($image, 60/$gq*$gs*18 +$gto, 0,  60/$gq*$gs*18 +$gto, $ghs, $red);    // 18:00
  imageline ($image, 60/$gq*$gs*21 +$gto, 0,  60/$gq*$gs*21 +$gto, $ghs, $greyt);  // 21:00

  //imagecopyresized ($image, $imageg, 100,0 , 0,0 , $gws-$gto , $ghs , $gw , $gh );
  imagecopyresized ($image, $imageg, 100,0 , 0,0 , $gws-$gto , $ghs , $gw , $ghp );


  header('Content-Type: image/png');

  imagepng ($image);
  }






  // ------------------------------------------------ График - День (пользователи) ------------------------------------------------ //
/*
if ($act == 'grdu') {
  $ajax = TRUE;

  include 'm/'.$mod.'/log_color_2.php';

  $gq = 3;  // quantizer
  $gs = 2;  // scale
  $gsh = 12;  // scale height

  $date = datesql($gyear, $gmon, $gday);


    // -------- collecting data -------- //

  $where = array();
  $where[] = '`server` = '.$gsrv;
  $where[] = '`datetime` >= \''.$date.' 00:00:00\'';
  $where[] = '`datetime` <= \''.$date.' 23:59:59\'';
  if ($gip)  $where[] = '`ip` '.($gipn?'!':'').'= INET_ATON(\''.$gipw.'\')';
  if ($grst)  $where[] = '`result` = '.$grst;
  if ($guan)  $where[] = '`uan` = '.$guan;
  if ($gusr)  $where[] = '`userx` = '.$gusr;
  if ($gipf)  $where[] = '`ipf` = INET_ATON(\''.$gipf.'\')';


  $log = db_read(array('table' => 'log',
                       'col' => array('id', '@ip', 'datetime', 'userx', '@ipf'),
                       'where' => $where,
                       'key' => 'id',
                       ));

  $set = array();
  foreach ($log as $v) {
    if (!$v['userx'])  continue;

    $mi = datee($v['datetime'],'h') * 60 + datee($v['datetime'],'i');
    $mi = floor($mi/$gq);

    if (!isset($lg_user[$v['userx']])) {
      $v['userx'] = 99999;
      if (!isset($lg_user[99999])) $lg_user[99999] = array('d'=>'неизвестно','i'=>array());
      }

    if (!isset($set[$v['userx']][$mi])) {
      $set[$v['userx']][$mi] = (in_array($v['@ipf'], $lg_user[$v['userx']]['i']) ? 0 : 1);
      }

    elseif (!in_array($v['@ipf'], $lg_user[$v['userx']]['i'])) {
      $set[$v['userx']][$mi] = 1;
      }

    }


  $gw = 1440 / $gq;
  $gh = count($lg_user);

  $imageg = imagecreatetruecolor ($gw, $gh);

  $transp = imagecolorallocate ($imageg, 255, 255, 255);
  imagecolortransparent ($imageg, $transp);
  $black = imagecolorallocate ($imageg, 0, 0, 0);
  $red = imagecolorallocate ($imageg, 255, 0, 0);


    // ---------------- рисуем график ---------------- //

  imagefilledrectangle ($imageg, 0, 0, $gw-1, $gh-1, $transp);

  $y = 0;
  foreach ($lg_user as $uk=>$uv) {
    for ($x = 0; $x < $gw; $x++) {
      if (isset($set[$uk][$x])) {
        imagesetpixel ($imageg, $x, $y, ($set[$uk][$x] ? $red : $black) );
        }
      }
    $y++;
    }


    // ---------------- сетка ---------------- //
  $gto = 100;
  $gws = $gw*$gs + $gto;
  $ghs = $gh*$gsh;

  $image = imagecreatetruecolor ($gws, $ghs);

  $black = imagecolorallocate ($image, 0, 0, 0);
  $white = imagecolorallocate ($image, 255, 255, 255);
  $red = imagecolorallocate ($image, 255, 0, 0);
  $green = imagecolorallocate ($image, 0, 255, 0);
  $blue = imagecolorallocate ($image, 0, 0, 255);

  $grey = imagecolorallocate ($image, 128, 128, 128);
  $greyt = imagecolorallocate ($image, 192, 192, 192);
  $greyl = imagecolorallocate ($image, 216, 216, 216);
  $bg = imagecolorallocate ($image, 240, 240, 240);


  imagefilledrectangle ($image, 0, 0, $gws-1, $ghs-1, $bg);


  $y = 0;
  foreach ($lg_user as $uk=>$uv) {
    imageline ($image, 0, ($y+1)*$gsh-0,  $gws-1, ($y+1)*$gsh-0, $greyl);
    imagettftext ($image, 8, 0, 1, ($y+1)*$gsh-2, (isset($set[$uk])?$black:$greyt), 'i/tahoma.ttf', $uv['d']);
    $box = imagettfbbox (8, 0, 'i/tahoma.ttf', $uk);
    imagettftext ($image, 8, 0, 98-($box[2] - $box[0]), ($y+1)*$gsh-2, (isset($set[$uk])?$black:$greyt), 'i/tahoma.ttf', $uk);
    $y++;
    }

  imageline ($image, $gto-1, 0,  $gto-1, $ghs, $black);    // 0:00
  imageline ($image, 60/$gq*$gs*3  +$gto, 0,  60/$gq*$gs*3  +$gto, $ghs, $greyt);    // 3:00
  imageline ($image, 60/$gq*$gs*(17/2)  +$gto, 0,  60/$gq*$gs*(17/2)  +$gto, $ghs, $red);    // 8:30
  imageline ($image, 60/$gq*$gs*13  +$gto, 0,  60/$gq*$gs*13  +$gto, $ghs, $greyt);    // 13:00
  imageline ($image, 60/$gq*$gs*14  +$gto, 0,  60/$gq*$gs*14  +$gto, $ghs, $greyt);    // 14:00
  imageline ($image, 60/$gq*$gs*(35/2) +$gto, 0,  60/$gq*$gs*(35/2) +$gto, $ghs, $red);    // 17:30
  imageline ($image, 60/$gq*$gs*21  +$gto, 0,  60/$gq*$gs*21  +$gto, $ghs, $greyt);    // 21:00

  imagecopyresized ($image, $imageg, 100,0 , 0,0 , $gws-$gto , $ghs , $gw , $gh );


  header('Content-Type: image/png');

  imagepng ($image);
  }
*/





  // ------------------------------------------------ График - День (ip адреса) ------------------------------------------------ //

if ($act == 'grdi') {
  $ajax = TRUE;

  $gq = 3;  // quantizer
  $gs = 2;  // scale
  $gsh = 12;  // scale height
  $gsf = 3;  // scale factor

  $date = datesql($gyear, $gmon, $gday);


    // -------- collecting data -------- //

  $where = array();
  $where[] = '`server` = '.$gsrv;
  $where[] = '`datetime` >= \''.$date.' 00:00:00\'';
  $where[] = '`datetime` <= \''.$date.' 23:59:59\'';
  if ($gip)  $where[] = '`ip` '.($gipn?'!':'').'= INET_ATON(\''.$gipw.'\')';
  if ($grst)  $where[] = '`result` = '.$grst;
  if ($guan)  $where[] = '`uan` = '.$guan;
  if ($gusr)  $where[] = '`userx` = '.$gusr;
  if ($gipf)  $where[] = '`ipf` = INET_ATON(\''.$gipf.'\')';


  $log = db_read(array('table' => 'log',
                       'col' => array('id', '@ip', 'datetime', 'type'),
                       'where' => $where,
                       'key' => 'id',
                       ));

  $set = array();
  $setc = array();
  $sett = array();
  foreach ($log as $v) {
    $mi = datee($v['datetime'],'h') * 60 + datee($v['datetime'],'i');
    $mi = floor($mi/$gq);

    $set[$v['@ip']][$mi][] = TRUE;

    if (!isset($setc[$v['@ip']])) { $setc[$v['@ip']] = 1;  $sett[$v['@ip']] = $v['type']; }
    else  $setc[$v['@ip']]++;
    }
  arsort($setc);


  $gw = 1440 / $gq;
  $gh = count($set);
  $ghb = $gsh / $gs;  // height of bar in non stretched pixels
  $ghp = $gh * $ghb;  // total height in non stretched pixels

  $imageg = imagecreatetruecolor ($gw, $ghp);

  $transp = imagecolorallocate ($imageg, 255, 255, 255);
  imagecolortransparent ($imageg, $transp);
  $black = imagecolorallocate ($imageg, 0, 0, 0);
  $red = imagecolorallocate ($imageg, 255, 0, 0);


    // ---------------- рисуем график ---------------- //

  imagefilledrectangle ($imageg, 0, 0, $gw-1, $ghp-1, $transp);

  $color = array();
  foreach ($db_hit_type as $k=>$v) {
    $color[$k] = imagecolorallocate ($imageg, hexdec(substr($db_hit_type[$k]['c'],1,1).substr($db_hit_type[$k]['c'],1,1)), hexdec(substr($db_hit_type[$k]['c'],2,1).substr($db_hit_type[$k]['c'],2,1)), hexdec(substr($db_hit_type[$k]['c'],3,1).substr($db_hit_type[$k]['c'],3,1)));
    }

  $y = 0;
  foreach ($setc as $sk=>$sv) {
    for ($x = 0; $x < $gw; $x++) {
      if (isset($set[$sk][$x])) {
        $bar_height = floor(log(count($set[$sk][$x]), $gsf)) +1;
        if ($bar_height > $ghb)  $bar_height = $ghb;
        imagefilledrectangle ($imageg, $x, (($y+1) * $ghb) -$bar_height, $x, (($y+1) * $ghb)-1, $color[$sett[$sk]]);
        }
      }
    $y++;
    }


    // ---------------- сетка ---------------- //
  $gto = 100;
  $gws = $gw*$gs + $gto;
  $ghs = $ghp * $gs;

  $image = imagecreatetruecolor ($gws, $ghs);

  $black = imagecolorallocate ($image, 0, 0, 0);
  $white = imagecolorallocate ($image, 255, 255, 255);
  $red = imagecolorallocate ($image, 255, 0, 0);
  $green = imagecolorallocate ($image, 0, 255, 0);
  $blue = imagecolorallocate ($image, 0, 0, 255);

  $grey = imagecolorallocate ($image, 128, 128, 128);
  $greyt = imagecolorallocate ($image, 192, 192, 192);
  $greyl = imagecolorallocate ($image, 216, 216, 216);
  $bg = imagecolorallocate ($image, 240, 240, 240);


  imagefilledrectangle ($image, 0, 0, $gws-1, $ghs-1, $bg);


  $colory = array();

  $y = 0;
  foreach ($setc as $sk=>$sv) {
    if (!isset($colory[$sett[$sk]]))  $colory[$sett[$sk]] = imagecolorallocate ($image, hexdec(substr($db_hit_type[$sett[$sk]]['c'],1,1).substr($db_hit_type[$sett[$sk]]['c'],1,1)), hexdec(substr($db_hit_type[$sett[$sk]]['c'],2,1).substr($db_hit_type[$sett[$sk]]['c'],2,1)), hexdec(substr($db_hit_type[$sett[$sk]]['c'],3,1).substr($db_hit_type[$sett[$sk]]['c'],3,1)));
    //imagesetpixel ($image, $x, $y, $color[$sett[$sk]]);

    imageline ($image, 0, ($y+1)*$gsh-0,  $gws-1, ($y+1)*$gsh-0, $greyl);
    //imagettftext ($image, 8, 0, 1, ($y+1)*$gsh-2, (isset($set[$sk])?$black:$greyt), 'p/tahoma.ttf', $sk);
    imagettftext ($image, 8, 0, 1, ($y+1)*$gsh-2, $colory[$sett[$sk]], 'i/tahoma.ttf', $sk);
    //$box = imagettfbbox (8, 0, 'p/tahoma.ttf', $sk);
    //imagettftext ($image, 8, 0, 98-($box[2] - $box[0]), ($y+1)*$gsh-2, (isset($set[$sk])?$black:$greyt), 'p/tahoma.ttf', $sk);
    $y++;
    }

  imageline ($image, $gto-1, 0,  $gto-1, $ghs, $black);    // 0:00
  imageline ($image, 60/$gq*$gs*3  +$gto, 0,  60/$gq*$gs*3  +$gto, $ghs, $greyt);  // 3:00
  imageline ($image, 60/$gq*$gs*9  +$gto, 0,  60/$gq*$gs*9  +$gto, $ghs, $red);    // 9:00
  imageline ($image, 60/$gq*$gs*12 +$gto, 0,  60/$gq*$gs*12 +$gto, $ghs, $greyt);  // 12:00
  imageline ($image, 60/$gq*$gs*15 +$gto, 0,  60/$gq*$gs*15 +$gto, $ghs, $greyt);  // 15:00
  imageline ($image, 60/$gq*$gs*18 +$gto, 0,  60/$gq*$gs*18 +$gto, $ghs, $red);    // 18:00
  imageline ($image, 60/$gq*$gs*21 +$gto, 0,  60/$gq*$gs*21 +$gto, $ghs, $greyt);  // 21:00

  imagecopyresized ($image, $imageg, 100,0 , 0,0 , $gws-$gto , $ghs , $gw , $ghp );


  header('Content-Type: image/png');

  imagepng ($image);
  }


?>