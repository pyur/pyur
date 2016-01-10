<?php

/************************************************************************/
/*  Автомобиль  v1.oo                                                   */
/************************************************************************/


if (!isset($body))  die ('error: this is a pluggable module and cannot be used standalone.');

$gaut = getn('aut');

$gdate = gets('date', $curr['date']);
$gyear = gets('year', $curr['year']);






if (!$act) {

  $gyear = datee($gdate);
  $gmon = datee($gdate,'m');
  $gmdays = date('t', mktime(0, 0, 0, $gmon, 1, $gyear));

  $where = array('`auto`.`dt` >= \''.datesql($gyear, $gmon, 1, 0, 0, 0).'\'',
                 '`auto`.`dt` <= \''.datesql($gyear, $gmon, $gmdays, 23, 59, 59).'\'',
                 );

  $auto = db_read(array('table' => 'auto',
                        'col' => array('id', 'dt', 'val', 'desc'),
                        'where' => $where,
                        'order' => '`dt`',
                        'key' => 'id',
                        ));

  $prev = db_read(array('table' => 'auto',
                        'col' => 'val',
                        'where' => '`auto`.`dt` < \''.datesql($gyear, $gmon, 1, 0, 0, 0).'\'',
                        'order' => '`dt` DESC',
                        ));




    // ---- submenu ---- //

  $date_prev = datesql(mktime(0,0,0, ($gmon-1),1,$gyear));
  $date_next = datesql(mktime(0,0,0, ($gmon+1),1,$gyear));

  $submenu[datee($date_prev).'.'.datee($date_prev, 'M').';navigation-180-button'] = '/'.$mod.'/?date='.$date_prev;
  $submenu[datee($date_next).'.'.datee($date_next, 'M').';navigation-000-button'] = '/'.$mod.'/?date='.$date_next;

  $submenu['Календарь;calendar-select'] = '/'.$mod.'/cdr/';

  if (p('edit'))  $submenu['Добавить;plus-button'] = '/'.$mod.'/aue/';
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




  b('<p class="h1">Пробег ('.substr('00'.$gmon,-2,2).'.'.$gyear.')</p>');
  b();


  if ($auto) {
    b('<div id="chart"></div>');
    b();

    css_table(array(140, 50, 30, 320, 18));
    icona(array('pencil-button'));

    b('<table class="lst f10">');
    b('<tr>');
    b('<td>Дата, время');
    b('<td>Пробег');
    b('<td>Км.');
    b('<td>Примечание');
    b('<td>Д.');

    $chart = array();
    for ($i = 1; $i < 32; $i++) {
      $chart[$i] = 0;
      }

    foreach ($auto as $k=>$v) {

      b('<tr>');

      b('<td>');
      b(dateh($v['dt']));


      b('<td>');
      b($v['val']);


      b('<td>');
      if ($prev)  $diff = $v['val'] - $prev;  else $diff = 0;
      b($diff);
      //b(substr($diff,0,-1));
      //b(',');
      //b(substr($diff,-1,1));
      $chart[datee($v['dt'],'d')] = $diff/10;


      b('<td>');
      b($v['desc']);


      b('<td>');
      if (p('edit'))  b(icona('/'.$mod.'/aue/?aut='.$k));


      $prev = $v['val'];
      }

    b('</table>');


    // ---------------- inline JavaScript ---------------- //

  b('<script>');
  $file = 'chart.js';
  b(fread (fopen ($file, 'rb'), filesize ($file) ));

  b('
var ch = new $chart("chart");
ch.auto(['.implode(',',$chart).']);
');
  b('</script>');
    }

  else {
    b('<p class="p">Ошибка: данные отсутствуют.');
    }

  }






  // ---------------------------------------------------- add / edit  auto --------------------------------------------------------- //



  // -------------------------- add / edit -------------------------- //

if ($act == 'aue' && p('edit') ) {

  $auto = array('dt' => $curr['datetime'],
                'val' => 0,
                'desc' => '',
                );
  $auto['dt'][17] = '0';
  $auto['dt'][18] = '0';

  if ($gaut) {
    $col = array();
    foreach ($auto as $k=>$v)  $col[] = $k;

    $auto = db_read(array('table' => 'auto',
                          'col' => $col,
                          'where' => '`id` = '.$gaut,
                          ));
    }
  else {
    $last = db_read(array('table' => 'auto',
                          'col' => 'val',
                          'order' => '`dt` DESC',
                          ));

    $auto['val'] = $last;
    }


    // ---- submenu ---- //                               // '/'.$mod.'/auu/?aut='.$gaut;
  if (p() && $gaut)  $submenu['?Удалить;minus-button'] = array('#Подтвердить;tick-button' => form_sbd('/'.$mod.'/auu/?aut='.$gaut));  // , '#Нет;cross-button' => ''
  submenu();
    // ---- end: submenu ---- //




  b('<p class="h1">');
  if (!$gaut)  b('Добавление');
  else         b('Редактирование');
  b('</p>');
  b();


  b(form('auto', '/'.$mod.'/auu/?'
    .($gaut ? '&aut='.$gaut : '')
    ));

  b('<table class="edt">');


  b('<tr><td>');
  b('Дата, время:');
  b('<td>');
  b(form_dt(array('f_auto_date_y;2000', 'f_auto_date_m', 'f_auto_date_d', 'f_auto_date_h', 'f_auto_date_i', 'f_auto_date_s'),  $auto['dt'] ));


  b('<tr><td>');
  b('Показание:');
  b('<td>');
  b(form_n('@f_auto_val', $auto['val'], 80));


  b('<tr><td>');
  b('Описание:');
  b('<td>');
  b(form_t('f_auto_desc', $auto['desc'], 300));


  b('</table>');


  b(form_sb());
  b('</form>');
  }




  // ------------------------------------------- ajax: update ------------------------------------------------ //

if ($act == 'auu' && p('edit') ) {
  $ajax = TRUE;

  $post = postb('f_auto_desc');

  $table = 'auto';
  $where = '`id` = '.$gaut;


  if ($post) {
    $set = array();
    $set['dt'] = datesql(postn('f_auto_date_y'), postn('f_auto_date_m'), postn('f_auto_date_d'), postn('f_auto_date_h'), postn('f_auto_date_i'), postn('f_auto_date_s'));
    $set['val'] = postn('f_auto_val');
    $set['desc'] = post('f_auto_desc');

    if ($gaut) {
      db_write(array('table'=>$table, 'set'=>$set, 'where'=>$where));
      }

    else {
      $gaut = db_write(array('table'=>$table, 'set'=>$set));
      }

    b('/'.$mod.'/?date='.substr($set['dt'],0,10));
    }


    // ---- deletion ---- //
  if (!$post && $gaut && p()) {
    $pdate = db_read(array('table' => 'auto',
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