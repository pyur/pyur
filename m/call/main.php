<?php

/************************************************************************/
/*  Звонки  v1.oo                                                       */
/************************************************************************/


if (!isset($body))  die ('error: this is a pluggable module and cannot be used standalone.');

$gcll = getn('cll');
$gphn = getn('phn');
$glne = getn('lne');

$gdate = gets('date', $curr['date']);
$gyear = gets('year', $curr['year']);

$db_imeir = array(
  '111111111111111' => 1,
  //'222222222222222' => 2,
  '333333333333333' => 3,
  '444444444444444' => 4,
  );
//DELETE FROM `call` WHERE `imei` = 3 AND `pid` < 541

$db_imei = array(
  1   => array('d' => 'Samsung Galaxy Tab P-1000', 'c'=>'#d2ffcc'),
  2   => array('d' => 'Xiaomi MI3',                'c'=>'#e9ceff'),
  3   => array('d' => 'Xiaomi MI3',                'c'=>'#ffd7ae'),
  4   => array('d' => 'Xiaomi Redmi Note 2',       'c'=>'#ffd4ee'),
  255 => array('d' => 'неопознанный',              'c'=>'#f00'),
  );


$db_call_type = array(
  1  => array('d' => 'входящий',    'c'=>'#0a0', 'i'=>'arrow-180-m-green'),
  2  => array('d' => 'исходящий',   'c'=>'#a00', 'i'=>'arrow-000-m-red'),
  3  => array('d' => 'не принятый', 'c'=>'#aa0', 'i'=>'arrow-315-m-yellow'),
  10 => array('d' => 'доб.контакт', 'c'=>'#06a', 'i'=>'plus-button'),
  );


function  duration ($src) {
  $min = floor($src/60);
  $sec = $src%60;

  if (!$min) {
    $r = $sec;
    }
  else {
    $r = ($min ? $min.':' : '').substr('00'.$sec,-2,2);
    }

  return  $r;
  }




if (!$act) {

  $gyear = datee($gdate);
  $gmon = datee($gdate,'m');
  $gmdays = date('t', mktime(0, 0, 0, $gmon, 1, $gyear));

  $datebeg = datesql(mktime(0, 0, 0, $gmon, 2 - date('N', mktime(0, 0, 0, $gmon, 1, $gyear)), $gyear));
  $dateend = datesql(mktime(0, 0, 0, $gmon, $gmdays + (7-date('N', mktime(0, 0, 0, $gmon, $gmdays, $gyear))), $gyear));
  $weeks = round((datesqltime($dateend) - datesqltime($datebeg) +86400) / (86400 * 7));


  $where = array('`call`.`dt` >= \''.$datebeg.' 00:00:00\'',
                 '`call`.`dt` <= \''.$dateend.' 23:59:59\'',
                 );

  $call = db_read(array(
    'table' => 'call',
    'col' => array('id', 'imei', 'pid', 'dt', 'phone', 'type', 'duration', 'name', '!DATE(`dt`) AS `dated`'),
    'where' => $where,
    'order' => '`dt`',
    'key' => array('dated', 'id'),
    ));


  $phone = db_read(array(
    'table' => array('phone', 'people'),
    'col' => array(
      'phone`.`num', 'phone`.`desc',  // 'phone`.`id', 
      'people`.`surname', 'people`.`name',  // 'people`.`id', 
      ),
    'where' => '`phone`.`pid` = `people`.`id`',
    'key' => 'num',
    ));



    // ---- submenu ---- //

  $date_prev = datesql(mktime(0,0,0, ($gmon-1),1,$gyear));
  $date_next = datesql(mktime(0,0,0, ($gmon+1),1,$gyear));

  $submenu[datee($date_prev).'.'.datee($date_prev, 'M').';navigation-180-button'] = '/'.$mod.'/?date='.$date_prev;
  $submenu[datee($date_next).'.'.datee($date_next, 'M').';navigation-000-button'] = '/'.$mod.'/?date='.$date_next;

  $submenu['Календарь;calendar-select'] = '/'.$mod.'/cdr/';

  if (p('edit'))  $submenu['Добавить;plus-button'] = '/'.$mod.'/cle/';
  submenu();

    // ---- end: submenu ---- //



  b('<p class="h4">Звонки ('.substr('00'.$gmon,-2,2).'.'.$gyear.')</p>');


  if ($call) {

    b('<style>'."\n");
    $file = 'm/'.$mod.'/cl_style.css';
    b(fread (fopen ($file, 'rb'), filesize ($file) ));
    b("\n".'</style>'."\n");

    $icon = array();
    foreach ($db_call_type as $v) {$icon[] = array($v['i'],12);}
    icon($icon);


    b('<table>');

    $date = $datebeg;
    for ($y = 0; $y < $weeks; $y++) {
      b('<tr>');

      for ($x = 0; $x < 7; $x++) {
        b('<td class="call_cell"');
        if (datee($date,'m') != $gmon)  b(' style="opacity: 0.3;');
        b('">');

        b('<div class="call_day"');
        if ($date == $curr['date'])  b(' style="background-color: #ff8;"');
        b('>');
        b(datee($date,'d'));
        b('</div>');

        if (isset($call[$date])) {

          //$tmp = array();
          foreach ($call[$date] as $kk=>$vv) {
            //b('<div class="vsv">');

            $color = $db_imei[$vv['imei']]['c'];
            $color = '#'
            .substr('00'.dechex(hexdec(substr($color, 1,2))*0.95), -2,2)
            .substr('00'.dechex(hexdec(substr($color, 3,2))*0.95), -2,2)
            .substr('00'.dechex(hexdec(substr($color, 5,2))*0.95), -2,2);

            b('<div class="call_row" style="background-color: '.$db_imei[$vv['imei']]['c'].'; border-color: '.$color.'">');

            b('<div class="call_time">');
            b(substr($vv['dt'],11,5));
            b('</div>');

            if (isset($db_call_type[$vv['type']])) {
              b('<div class="call_type">'.icon($db_call_type[$vv['type']]['i']).'</div>');
              }
            else {
              b(' ? ');
              }

            b('<div class="call_desc">');
            $ab = '';
            $ae = '';
            if (p('edit')) {
              $ab = '<a class="call_desc" href="/'.$mod.'/cle/?cll='.$kk.'">';
              $ae = '</a>';
              }
            $tmp = array();

            if ($vv['phone']) {

              if (isset($phone[$vv['phone']])) {
                $tmp[] = $ab.'<span>'.$phone[$vv['phone']]['surname'].' '.$phone[$vv['phone']]['name'].'</span>'.$ae;
                }
              else {
                $tmp[] = $ab.'<span style="background-color: #faf;">'.($vv['name'] ? $vv['name'] : $vv['phone']).'</span>'.$ae;
                }

              }

            if ($vv['duration']) {
              $tmp[] = $ab.'<span style="color: #a0a;">'.duration($vv['duration']).'</span>'.$ae;
              }

            if (!$tmp) $tmp[] = $ab.'–'.$ae;

            b(implode(', ', $tmp));

            b('</div>');  // call_desc
            b('</div>');  // call_row
            }
          }

        $date = datesql(mktime(0,0,0, datee($date,'m'), datee($date,'d')+1, datee($date)));
        }
      }

    b('</table>');
    }

  else {
    b('<p class="p">Ошибка: данные отсутствуют.');
    }

  }






  // ---------------------------------------------------- add / edit  call --------------------------------------------------------- //



  // -------------------------- add / edit -------------------------- //

if ($act == 'cle' && p('edit') ) {

  $call = array(
    'imei' => 0,
    'pid' => 0,
    'dt' => $curr['datetime'],
    'phone' => '',
    'type' => 0,
    'duration' => 0,
    'name' => '',
    );
  $call['dt'][17] = '0';
  $call['dt'][18] = '0';

  if ($gcll) {
    $col = array();
    foreach ($call as $k=>$v)  $col[] = $k;

    $call = db_read(array('table' => 'call',
                          'col' => $col,
                          'where' => '`id` = '.$gcll,
                          ));
    }


    // ---- submenu ---- //                               // '/'.$mod.'/clu/?cll='.$gcll;
  if (p() && $gcll)  $submenu['?Удалить;minus-button'] = array('#Подтвердить;tick-button' => form_sbd('/'.$mod.'/clu/?cll='.$gcll));  // , '#Нет;cross-button' => ''
  submenu();
    // ---- end: submenu ---- //




  b('<p class="h1">');
  if (!$gcll)  b('Добавление');
  else         b('Редактирование');
  b('</p>');
  b();


  b(form('call', '/'.$mod.'/clu/?'
    .($gcll ? '&cll='.$gcll : '')
    ));

  b('<table class="edt">');


  b('<tr><td>');
  b('IMEI:');
  b('<td>');
  b(form_s('!f_call_imei;d', $db_imei, $call['imei']));


  b('<tr><td>');
  b('Source_ID:');
  b('<td>');
  b(form_n('f_call_pid', $call['pid'], 80));


  b('<tr><td>');
  b('Дата, время:');
  b('<td>');
  b(form_dt(array('f_call_date_y;2000', 'f_call_date_m', 'f_call_date_d', 'f_call_date_h', 'f_call_date_i', 'f_call_date_s'),  $call['dt'] ));


  b('<tr><td>');
  b('Телефонный номер:');
  b('<td>');
  b(form_t('@f_call_phone', $call['phone'], 100));


  b('<tr><td>');
  b('Тип:');
  b('<td>');
  b(form_s('f_call_type;d', $db_call_type, $call['type']));


  b('<tr><td>');
  b('Продолжительность:');
  b('<td>');
  b(form_n('f_call_duration', $call['duration'], 80));


  b('<tr><td>');
  b('Описание:');
  b('<td>');
  b(form_t('f_call_name', $call['name'], 300));


  b('</table>');


  b(form_sb());
  b('</form>');
  }




  // ------------------------------------------- ajax: update ------------------------------------------------ //

if ($act == 'clu' && p('edit') ) {
  $ajax = TRUE;

  $post = postb('f_call_name');

  $table = 'call';
  $where = '`id` = '.$gcll;


  if ($post) {
    $set = array();
    $set['imei'] = postn('f_call_imei');
    $set['pid'] = postn('f_call_pid');
    $set['dt'] = datesql(postn('f_call_date_y'), postn('f_call_date_m'), postn('f_call_date_d'), postn('f_call_date_h'), postn('f_call_date_i'), postn('f_call_date_s'));
    $set['phone'] = post('f_call_phone');
    $set['type'] = postn('f_call_type');
    $set['duration'] = postn('f_call_duration');
    $set['name'] = post('f_call_name');

    if ($gcll) {
      db_write(array('table'=>$table, 'set'=>$set, 'where'=>$where));
      }

    else {
      $gcll = db_write(array('table'=>$table, 'set'=>$set));
      }

    b('/'.$mod.'/?date='.substr($set['dt'],0,10));
    }


    // ---- deletion ---- //
  if (!$post && $gcll && p()) {
    $pdate = db_read(array('table' => 'call',
                           'col' => '!DATE(`dt`)',
                           'where' => $where,
                           ));

    $result = db_write(array('table'=>$table, 'where'=>$where));

    b('/'.$mod.'/?date='.$pdate);

    //http_response_code(418);
    //b('failed');
    }  // end: delete

  }




  // ---------------------------------------------- Calendar -------------------------------------------------- //

if ($act == 'cdr') {

  //if ($gdate)  $year = datee($gdate);

    // ---- submenu ---- //
  $submenu[($gyear-1).' г.;navigation-180-button'] = '/'.$mod.'/cdr/?year='.($gyear-1);
  $submenu[($gyear+1).' г.;navigation-000-button'] = '/'.$mod.'/cdr/?year='.($gyear+1);
  submenu();
    // ---- end: submenu ---- //

  $col = 6;
  $row = 12 / $col;

  b('<p class="h1">'.$gyear.' год</p>');

  b('<table class="tabc">');

  for ($i = 0; $i < $row; $i++) {
    b('<tr>');

    for ($j = 0; $j < $col; $j++) {
      b('<td class="t f10">');
      b('<div class="cdr">');

      $mon = ($i * $col + $j) +1;
      b('<b>'.$month[$mon].'</b>');

      $date = $gyear.'-'.$mon.'-01';
      $weeks = ceil((date('N', mktime (0,0,0, datee($date, 'm'), datee($date, 'd'), datee($date, 'y'))) + date('t', mktime (0,0,0, datee($date, 'm'), datee($date, 'd'), datee($date, 'y'))) - 1)/7);

      $dateb = $date;
      while (date('N', mktime (0,0,0, datee($dateb, 'm'), datee($dateb, 'd'), datee($dateb, 'y'))) != 1) {
        $dateb = datesql(mktime (0,0,0, datee($dateb, 'm'), datee($dateb, 'd') -1, datee($dateb, 'y')));
        }

      b('<table class="cdrm">');
      for ($l = 0; $l < 7; $l++) {
        b('<tr>');

        $datec = $dateb;
        for ($k = 0; $k < $weeks; $k++) {
          b('<td class="cdrd">');

          if (datee($datec, 'm') == datee($date, 'm')) {
            //if (isset($video[$datec])) {
            //  b('<a href="?p='.$mod.'&date='.$datec.'" class="cdra">');
            //  }

            b('<a href="/'.$mod.'/?date='.$datec.'" class="'.($datec == $curr['date']?'cdra':'k').'">');
            b(datee($datec, 'd'));
            b('</a>');

            //if (isset($video[$datec])) {
            //  b('</a>');
            //  }
            }

          $datec = datesql(mktime (0,0,0, datee($datec, 'm'), datee($datec, 'd') +7, datee($datec, 'y')));
          }

        $dateb = datesql(mktime (0,0,0, datee($dateb, 'm'), datee($dateb, 'd') +1, datee($dateb, 'y')));
        }

      b('</table>');
      b('</div>');
      }

    }

  b('</table>');
  }




  // ---------------------------------------------- import -------------------------------------------------- //
/*
if ($act == 'imp') {
  //$file = 'm/'.$mod.'/debug/debug 1444037896';
  //$file = 'm/'.$mod.'/debug/debug 1444043326-crop';

  //$file = 'm/'.$mod.'/debug/debug 1443811181';
  $file = 'm/'.$mod.'/debug/debug 1443811195-consolidate';
  //$file = 'm/'.$mod.'/debug/debug ';

  $file = fread (fopen ($file, 'rb'), filesize($file));

  //$file = strtr($file, array("\r"=>'') );
  //$line = explode("\n", $file);
  $json = json_decode($file, TRUE);
  //if (!isset($json['calls']))  die('error: wrong json');
  //$json['calls'] = $json;
  d(count($json['calls']));

  foreach ($json['calls'] as $k=>$v) {
    $phone = filter_n($v['v3']);
    $phone = substr($phone,-10,10);

    $check = db_read(array(
      'table' => 'call',
      'col' => 'id',
      'where' => '`pid` = '.$v['v1'],
      ));

    if (!$check) {
      $set = array();
      $set['imei'] = 2;
      $set['pid'] = $v['v1'];
      $set['dt'] = datesql(substr($v['v2'],0,-3), 1);
      $set['phone'] = $phone;
      $set['type'] = $v['v4'];
      $set['duration'] = $v['v5'];
      $set['name'] = (isset($v['v6']) ? $v['v6'] : '');
      d($set);

      //db_write(array('table'=>'call', 'set'=>$set));
      }


    }


  }
*/




  // ---------------------------------------------- move phones from people -------------------------------------------------- //
/*
if ($act == 'mfp') {
  $phone = db_read(array(
    'table' => 'people',
    'col' => array('id', 'phone'),
    //'where' => '',
    'key' => 'id',
    ));
  //d($phone);

  foreach ($phone as $k=>$v) {
    if (!$v)  continue;

    $phonee = explode(',', $v['phone']);
    foreach ($phonee as $vv) {
      $vv = trim($vv);
      if (isset($vv[7]) && isset($vv[10]) && $vv[7] == '-' && $vv[10] == '-') {
        b('<p>'.$vv.' +');
        db_write(array('table'=>'phone', 'set'=>array(
          'pid' => $k,
          'num' => filter_n($vv),
          'desc' => '',
          )));
        }
      else {
        b('<p>'.$vv.' ----');
        }

      }
    }

  }
*/


?>