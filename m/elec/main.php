<?php

/************************************************************************/
/*  Электричество  v1.oo                                                */
/************************************************************************/


if (!isset($body))  die ('error: this is a pluggable module and cannot be used standalone.');

$gelc = getn('elc');

$gdate = gets('date', $curr['date']);
$gyear = gets('year', $curr['year']);






if (!$act) {

  $gyear = datee($gdate);
  $gmon = datee($gdate,'m');
  $gmdays = date('t', mktime(0, 0, 0, $gmon, 1, $gyear));

  $where = array('`elec`.`dt` >= \''.datesql($gyear, $gmon, 1, 0, 0, 0).'\'',
                 '`elec`.`dt` <= \''.datesql($gyear, $gmon, $gmdays, 23, 59, 59).'\'',
                 );

  $elec = db_read(array('table' => 'elec',
                        'col' => array('id', 'dt', 'val', 'desc'),
                        'where' => $where,
                        'order' => '`dt`',
                        'key' => 'id',
                        ));

  $prev = db_read(array('table' => 'elec',
                        'col' => 'val',
                        'where' => '`elec`.`dt` < \''.datesql($gyear, $gmon, 1, 0, 0, 0).'\'',
                        'order' => '`dt` DESC',
                        ));




    // ---- submenu ---- //

  $date_prev = datesql(mktime(0,0,0, ($gmon-1),1,$gyear));
  $date_next = datesql(mktime(0,0,0, ($gmon+1),1,$gyear));

  $submenu[datee($date_prev).'.'.datee($date_prev, 'M').';navigation-180-button'] = '/'.$mod.'/?date='.$date_prev;
  $submenu[datee($date_next).'.'.datee($date_next, 'M').';navigation-000-button'] = '/'.$mod.'/?date='.$date_next;

  $submenu['Календарь;calendar-select'] = '/'.$mod.'/cdr/';

  if (p('edit'))  $submenu['Добавить;plus-button'] = '/'.$mod.'/ele/';
  submenu();

    // ---- end: submenu ---- //


    b('<style type="text/css">
<!--

@media (max-width: 1339px) {
#chart {width: 1300px;  height: 300px;}
}
  /* for 1366 */
@media (min-width: 1340px) and (max-width: 1579px) {
#chart {width: 800px;  height: 200px;}
}
  /* for 1600 */
@media (min-width: 1580px) and (max-width: 1899px) {
#chart {width: 1300px;  height: 300px;}
}
  /* for 1920 */
@media (min-width: 1900px) and (max-width: 2499px) {
#chart {width: 1300px;  height: 300px;}
}
  /* for 2560 */
@media (min-width: 2500px) {
#chart {width: 1300px;  height: 300px;}
}
-->
</style>
');




  b('<p class="h1">Электричество ('.substr('00'.$gmon,-2,2).'.'.$gyear.')</p>');
  b();


  if ($elec) {
    b('<div id="chart"></div>');
    b();

    css_table(array(140, 70, 50, 320, 18));
    icona(array('pencil-button'));

    b('<table class="lst f10">');
    b('<tr>');
    b('<td>Дата, время');
    b('<td>Значение');
    b('<td>Расход');
    b('<td>Примечание');
    b('<td>Д.');

    $chart = array();
    foreach ($elec as $k=>$v) {

      b('<tr>');

      b('<td>');
      //if (p('edit'))  b('<a href="/'.$mod.'/ele/?elc='.$k.'">');  // target="_blank"
      //b($k);
      b(dateh($v['dt']));
      //if (p('edit'))  b('</a>');


      b('<td>');
      b(substr($v['val'],0,-1));

      //b('<span style="font-size: 8pt;">');
      b('<span style="color: red;">');
      b(substr($v['val'],-1,1));
      b('</span>');


      b('<td>');
      if ($prev)  $diff = $v['val'] - $prev;  else $diff = 0;
      b(substr($diff,0,-1));
      b(',');
      b(substr($diff,-1,1));
      $chart[] = $diff/10;


      b('<td>');
      b($v['desc']);


      b('<td>');
      if (p('edit'))  b(icona('/'.$mod.'/ele/?elc='.$k));


      $prev = $v['val'];
      }

    b('</table>');


    // ---------------- inline JavaScript ---------------- //

  b('<script>');
  $file = 'chart.js';
  b(fread (fopen ($file, 'rb'), filesize ($file) ));

  b('
var ch = new $chart("chart");
ch.elec(['.implode(',',$chart).']);
');
  b('</script>');
    }

  else {
    b('<p class="p">Ошибка: данные отсутствуют.');
    }

  }






  // ---------------------------------------------------- add / edit  elec --------------------------------------------------------- //



  // -------------------------- add / edit -------------------------- //

if ($act == 'ele' && p('edit') ) {

  $elec = array('dt' => $curr['datetime'],
                'val' => 0,
                'desc' => '',
                );
  $elec['dt'][17] = '0';
  $elec['dt'][18] = '0';

  if ($gelc) {
    $col = array();
    foreach ($elec as $k=>$v)  $col[] = $k;

    $elec = db_read(array('table' => 'elec',
                          'col' => $col,
                          'where' => '`id` = '.$gelc,
                          ));
    }
  else {
    $last = db_read(array('table' => 'elec',
                          'col' => 'val',
                          'order' => '`dt` DESC',
                          ));

    $elec['val'] = $last;
    }


    // ---- submenu ---- //                               // '/'.$mod.'/elu/?elc='.$gelc;
  if (p() && $gelc)  $submenu['?Удалить;minus-button'] = array('#Подтвердить;tick-button' => form_sbd('/'.$mod.'/elu/?elc='.$gelc));  // , '#Нет;cross-button' => ''
  submenu();
    // ---- end: submenu ---- //




  b('<p class="h1">');
  if (!$gelc)  b('Добавление');
  else         b('Редактирование');
  b('</p>');
  b();


  b(form('elec', '/'.$mod.'/elu/?'
    .($gelc ? '&elc='.$gelc : '')
    ));

  b('<table class="edt">');


  b('<tr><td>');
  b('Дата, время:');
  b('<td>');
  b(form_dt(array('f_elec_date_y;2000', 'f_elec_date_m', 'f_elec_date_d', 'f_elec_date_h', 'f_elec_date_i', 'f_elec_date_s'),  $elec['dt'] ));


  b('<tr><td>');
  b('Показание:');
  b('<td>');
  b(form_n('f_elec_val', $elec['val'], 80, 1));


  b('<tr><td>');
  b('Описание:');
  b('<td>');
  b(form_t('f_elec_desc', $elec['desc'], 300));


  b('</table>');


  b(form_sb());
  b('</form>');
  }




  // ------------------------------------------- ajax: update ------------------------------------------------ //

if ($act == 'elu' && p('edit') ) {
  $ajax = TRUE;

  $post = postb('f_elec_desc');

  $table = 'elec';
  $where = '`id` = '.$gelc;


  if ($post) {
    $set = array();
    $set['dt'] = datesql(postn('f_elec_date_y'), postn('f_elec_date_m'), postn('f_elec_date_d'), postn('f_elec_date_h'), postn('f_elec_date_i'), postn('f_elec_date_s'));
    $set['val'] = postn('f_elec_val');
    $set['desc'] = post('f_elec_desc');

    if ($gelc) {
      db_write(array('table'=>$table, 'set'=>$set, 'where'=>$where));
      }

    else {
      $gelc = db_write(array('table'=>$table, 'set'=>$set));
      }

    b('/'.$mod.'/?date='.substr($set['dt'],0,10));
    }


    // ---- deletion ---- //
  if (!$post && $gelc && p()) {
    $pdate = db_read(array('table' => 'elec',
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


?>