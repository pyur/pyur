<?php

/************************************************************************/
/*  Звонки  v1.oo                                                       */
/************************************************************************/


if (!isset($body))  die ('error: this is a pluggable module and cannot be used standalone.');

$gcll = getn('cll');
$gphn = getn('phn');
$glne = getn('lne');
$gppl = getn('ppl');

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


  $where = array(
    '`call`.`date` >= \''.$datebeg.'\'',
    '`call`.`date` <= \''.$dateend.'\'',
    );

  $call = $db->
    table('call')->
    col('id', 'imei', 'pid', 'date', 'time', 'phone', 'type', 'duration', 'name')->
    where($where)->
    order('`date`, `time`')->
    key('date', 'id')->
    r();


  $phone = $db->
    table('phone', 'people')->
    col('phone`.`num', 'phone`.`desc',  // 'phone`.`id', 
        'people`.`surname', 'people`.`name')->  // 'people`.`id', 
    where('`phone`.`pid` = `people`.`id`')->
    key('num')->
    r();



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
            b(substr($vv['time'],0,5));
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
    'date' => $curr['date'],
    'time' => substr($curr['datetime'],11,6).'00',
    'phone' => '',
    'type' => 0,
    'duration' => 0,
    'name' => '',
    );

  if ($gcll) {
    $col = array();
    foreach ($call as $k=>$v)  $col[] = $k;

    $call = $db->
      table('call')->
      col($col)->
      where('`id` = '.$gcll)->
      r();
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


  b(form('call', '/'.$mod.'/clu/', array(
    $gcll ? 'cll='.$gcll : '',
    )));

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
  b(form_dt(array('f_call_date_y;2000', 'f_call_date_m', 'f_call_date_d', 'f_call_date_h', 'f_call_date_i', 'f_call_date_s'),  $call['date'].' '.$call['time'] ));


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
    $date = datesql(postn('f_call_date_y'), postn('f_call_date_m'), postn('f_call_date_d'), postn('f_call_date_h'), postn('f_call_date_i'), postn('f_call_date_s'));
    $set['date'] = substr($date,0,10);
    $set['time'] = substr($date,11,8);
    $set['phone'] = post('f_call_phone');
    $set['type'] = postn('f_call_type');
    $set['duration'] = postn('f_call_duration');
    $set['name'] = post('f_call_name');

    if ($gcll) {
      $db->table($table)->set($set)->where($where)->u();
      }

    else {
      $gcll = $db->table($table)->set($set)->i();
      }

    b('/'.$mod.'/?date='.substr($set['dt'],0,10));
    }


    // ---- deletion ---- //
  if (!$post && $gcll && p()) {
    $pdate = $db->
      table('call')->
      col('!DATE(`dt`)')->
      where($where)->
      r();

    $result = $db->
      table($table)->
      where($where)->
      d();

    b('/'.$mod.'/?date='.$pdate);

    //http_response_code(418);
    //b('failed');
    }  // end: delete

  }




  // ---------------------------------------------- Call graphic -------------------------------------------------- //

if ($act == 'cgr') {

  $gyear = datee($gdate);
  $gmon = datee($gdate,'m');
  $gmdays = date('t', mktime(0, 0, 0, $gmon, 1, $gyear));

  //$datebeg = datesql(mktime(0, 0, 0, $gmon, 2 - date('N', mktime(0, 0, 0, $gmon, 1, $gyear)), $gyear));
  //$dateend = datesql(mktime(0, 0, 0, $gmon, $gmdays + (7-date('N', mktime(0, 0, 0, $gmon, $gmdays, $gyear))), $gyear));
  //$weeks = round((datesqltime($dateend) - datesqltime($datebeg) +86400) / (86400 * 7));
  $datebeg = datesql(mktime(0, 0, 0, $gmon, 1, $gyear));
  $dateend = datesql(mktime(0, 0, 0, $gmon, $gmdays, $gyear));


  $where = array(
    '`call`.`date` >= \''.$datebeg.'\'',
    '`call`.`date` <= \''.$dateend.'\'',
    );
  $where[] = '`call`.`phone` = `phone`.`num`';
  $where[] = '`phone`.`pid` = '.$gppl;

  $call = $db->
    table('call', 'phone')->
    col('call`.`id', 'call`.`date', 'call`.`time', 'call`.`type')->  // , 'imei', 'pid', 'phone', 'duration', 'name'
    where($where)->
    key('date', 'id')->
    r();


  $call_time = array();
  if ($call)  foreach ($call as $k=>$v) {
    foreach ($v as $kk=>$vv) {
      $call_time[$k][(int)substr($vv['time'],0,2)][$kk] = $vv;
      }
    }


    // ---- submenu ---- //
  $date_prev = datesql(mktime(0,0,0, ($gmon-1),1,$gyear));
  $date_next = datesql(mktime(0,0,0, ($gmon+1),1,$gyear));

  $submenu[datee($date_prev).'.'.datee($date_prev, 'M').';navigation-180-button'] = '/'.$mod.'/'.$act.'/?date='.$date_prev.'&ppl='.$gppl;
  $submenu[datee($date_next).'.'.datee($date_next, 'M').';navigation-000-button'] = '/'.$mod.'/'.$act.'/?date='.$date_next.'&ppl='.$gppl;

  $submenu['Календарь;calendar-select'] = '/'.$mod.'/cdr/';
  submenu();
    // ---- end: submenu ---- //



  b('<p class="h4">Звонки ('.substr('00'.$gmon,-2,2).'.'.$gyear.')</p>');


  b('<div style="display: inline-block;">');  // table  border: 1px dashed red;

  $date = array();
  $datet = $datebeg;
  while (1) {
    if ($datet == $dateend)  break;
    $datet = datesql(mktime(0,0,0, datee($datet,'m'), datee($datet,'d')+1, datee($datet)));
    $date[] = $datet;
    }

  for ($i = 0; $i < 24; $i++) {
    b('<div style="white-space: nowrap;">');  // row  border: 1px dashed blue;

    b('<div style="display: inline-block;  width: 20px;  height: 20px;  vertical-align: top;">');
    b($i);
    b('</div>');
    foreach ($date as $d) {
      b('<div style="display: inline-block;  width: 20px;  height: 20px;  vertical-align: top;');  // row    border: 1px dashed magenta;
      if (isset($call_time[$d][$i]))  b(' background-color: #a00;');
      b('">');
      b('</div>');
      }

    b('</div>');
    }

    b('<div></div>');
    foreach ($date as $d) {
      b('<div style="display: inline-block;  width: 20px;  height: 20px;');  // row
      //if (isset($call_time[$d][$i]))  b(' background-color: #a00;');
      b('">');
      b(datee($d,'d'));
      b('</div>');
      }

  b('</div>');
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


?>