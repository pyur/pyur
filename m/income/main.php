<?php

/************************************************************************/
/*  Прибыль  v1.oo                                                      */
/************************************************************************/


if (!isset($body))  die ('error: this is a pluggable module and cannot be used standalone.');

$ginc = getn('inc');

$gdate = gets('date', $curr['date']);
$gyear = gets('year', $curr['year']);



$db_person = array(
  0 => array('- - - -', '#000;'),
  1 => array('Источник 1', '#00f'),
  2 => array('Источник 2', '#00f'),
  3 => array('Источник 3', '#00f'),
  4 => array('Источник 4', '#00f'),
  5 => array('Источник 5', '#00f'),
  6 => array('Источник 6', '#00f'),
  7 => array('Источник 7', '#00f'),
  );






if (!$act) {

  $gyear = datee($gdate);
  $gmon = datee($gdate,'m');
  $gmdays = date('t', mktime(0, 0, 0, $gmon, 1, $gyear));

  $where = array('`income`.`dt` >= \''.datesql($gyear, $gmon, 1, 0, 0, 0).'\'',
                 '`income`.`dt` <= \''.datesql($gyear, $gmon, $gmdays, 23, 59, 59).'\'',
                 );

  $income = db_read(array('table' => 'income',
                          'col' => array('id', 'dt', 'money', 'person', 'desc'),
                          'where' => $where,
                          'order' => '`dt`',
                          'key' => 'id',
                          ));


    // ---- submenu ---- //

  $date_prev = datesql(mktime(0,0,0, ($gmon-1),1,$gyear));
  $date_next = datesql(mktime(0,0,0, ($gmon+1),1,$gyear));

  $submenu[datee($date_prev).'.'.datee($date_prev, 'M').';navigation-180-button'] = '/'.$mod.'/?date='.$date_prev;
  $submenu[datee($date_next).'.'.datee($date_next, 'M').';navigation-000-button'] = '/'.$mod.'/?date='.$date_next;

  $submenu['Календарь;calendar-select'] = '/'.$mod.'/cdr/';

  if (p('edit'))  $submenu['Добавить;plus-button'] = '/'.$mod.'/ine/';
  submenu();

    // ---- end: submenu ---- //




  b('<p class="h1">Прибыль ('.substr('00'.$gmon,-2,2).'.'.$gyear.')</p>');
  b();


  if ($income) {
    css_table(array(140, 80, 220, 18));
    icona(array('pencil-button'));

    b('<table class="lst f10">');
    b('<tr>');
    //b('<td class="f10 b">id');
    b('<td>Дата, время');
    b('<td>Сумма');
    b('<td>Примечание');
    b('<td>Д.');
    //b('<td>Де-я');

    foreach ($income as $k=>$v) {

      b('<tr>');

      b('<td>');
      //if (p('edit'))  b('<a href="/'.$mod.'/ine/?inc='.$k.'">');
      //b($k);
      b(dateh($v['dt']));
      //if (p('edit'))  b('</a>');


      b('<td>');
      //b(frach($v['money']).' р');
      $moneye = explode(',', frach($v['money']));
      if (count($moneye) == 1)  $moneye[1] = 0;

      b('<span style="display: inline-block; width: 40px; text-align: right;">');
      b($moneye[0]);
      b('</span>');

      b('<span style="display: inline-block; width: 30px;">');
      if ($moneye[1])  b(','.$moneye[1]);
      b('&nbsp;р');
      b('</span>');


      b('<td>');
      if ($v['person']) {
        b('<span style="color: '.$db_person[$v['person']][1].'">'.$db_person[$v['person']][0].'</span>');
        }
      if ($v['desc']) {
        b('<span class="cg">'.$v['desc'].'</span>');
        }


      b('<td>');
      if (p('edit'))  b(icona('/'.$mod.'/ine/?inc='.$k));
      }

    b('</table>');
    }

  else {
    b('<p class="p">Ошибка: данные отсутствуют.');
    }

  }






  // ---------------------------------------------------- add / edit  income --------------------------------------------------------- //



  // -------------------------- add / edit -------------------------- //

if ($act == 'ine' && p('edit') ) {

  //asort($db_person);

  $income = array('dt' => $curr['datetime'],
                  'money' => 0,
                  'person' => 0,
                  'desc' => '',
                  );
  $income['dt'][17] = '0';
  $income['dt'][18] = '0';

  if ($ginc) {
    $col = array();
    foreach ($income as $k=>$v)  $col[] = $k;

    $income = db_read(array('table' => 'income',
                            'col' => $col,
                            'where' => '`id` = '.$ginc,
                            ));
    }


    // ---- submenu ---- //
  if (p() && $ginc)  $submenu['?Удалить;minus-button'] = array('#Подтвердить;tick-button' => form_sbd('/'.$mod.'/inu/?inc='.$ginc));
  submenu();
    // ---- end: submenu ---- //




  b('<p class="h1">');
  if (!$ginc)  b('Добавление');
  else         b('Редактирование');
  b('</p>');
  b();
  b();


  b(form('income', '/'.$mod.'/inu/?'
    .($ginc ? '&inc='.$ginc : '')
    ));

  b('<table class="edt">');


  b('<tr><td>');
  b('Дата, время:');
  b('<td>');
  b(form_dt(array('f_income_date_y;2000', 'f_income_date_m', 'f_income_date_d', 'f_income_date_h', 'f_income_date_i', 'f_income_date_s'),  $income['dt'] ));



  b('<tr><td>');
  b('Сумма:');
  b('<td>');
  b(form_t('@f_income_money', frach($income['money']), 100));


  b('<tr><td>');
  b('Откуда:');
  b('<td>');
  b(form_s('f_income_person;0', $db_person, $income['person']));


  b('<tr><td>');
  b('Описание:');
  b('<td>');
  b(form_t('f_income_desc', $income['desc'], 300));


  b('</table>');


  b(form_sb());

  b('</form>');
  }




  // ------------------------------------------- ajax: update ------------------------------------------------ //

if ($act == 'inu' && p('edit') ) {
  $ajax = TRUE;

  $post = postb('f_income_desc');

  $table = 'income';
  $where = '`id` = '.$ginc;


  if ($post) {
    $set = array();
    $set['dt'] = datesql(postn('f_income_date_y'), postn('f_income_date_m'), postn('f_income_date_d'), postn('f_income_date_h'), postn('f_income_date_i'), postn('f_income_date_s'));
    $set['money'] = fraci(post('f_income_money'));
    $set['person'] = postn('f_income_person');
    $set['desc'] = post('f_income_desc');

    if ($ginc) {
      db_write(array('table'=>$table, 'set'=>$set, 'where'=>$where));
      }

    else {
      $ginc = db_write(array('table'=>$table, 'set'=>$set));
      }

    b('/'.$mod.'/?date='.substr($set['dt'],0,10));
    }


    // ---- deletion ---- //
  if (!$post && $ginc && p()) {
    $pdate = db_read(array('table' => 'income',
                           'col' => '!DATE(`dt`)',
                           'where' => $where,
                           ));

    $result = db_write(array('table'=>$table, 'where'=>$where));

    b('/'.$mod.'/?date='.$pdate);
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


?>