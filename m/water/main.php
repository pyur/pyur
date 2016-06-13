<?php

/************************************************************************/
/*  Вода  v1.oo                                                         */
/************************************************************************/


if (!isset($body))  die ('error: this is a pluggable module and cannot be used standalone.');

$gwtr = getn('wtr');

$gdate = gets('date', $curr['date']);






if (!$act) {

  $gyear = datee($gdate);
  $gmon = datee($gdate,'m');
  $gmdays = date('t', mktime(0, 0, 0, $gmon, 1, $gyear));

  $where = array(
    '`water`.`dt` >= \''.datesql($gyear, $gmon, 1, 0, 0, 0).'\'',
    '`water`.`dt` <= \''.datesql($gyear, $gmon, $gmdays, 23, 59, 59).'\'',
    );

  $water = $db->
    table('water')->
    col('id', 'dt', 'val', 'val2', 'desc')->
    where($where)->
    order('`dt`')->
    key('id')->
    r();

  $prev = $db->
    table('water')->
    col('val','val2')->
    where('`water`.`dt` < \''.datesql($gyear, $gmon, 1, 0, 0, 0).'\'')->
    order('`dt` DESC')->
    r();


    // ---- submenu ---- //

  $date_prev = datesql(mktime(0,0,0, ($gmon-1),1,$gyear));
  $date_next = datesql(mktime(0,0,0, ($gmon+1),1,$gyear));

  $submenu[datee($date_prev).'.'.datee($date_prev, 'M').';navigation-180-button'] = '/'.$mod.'/?date='.$date_prev;
  $submenu[datee($date_next).'.'.datee($date_next, 'M').';navigation-000-button'] = '/'.$mod.'/?date='.$date_next;

  $submenu['Календарь;calendar-select'] = '/'.$mod.'/cdr/';

  if (p('edit'))  $submenu['Добавить;plus-button'] = '/'.$mod.'/wte/';
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




  b('<p class="h1">Вода ('.substr('00'.$gmon,-2,2).'.'.$gyear.')</p>');
  b();


  if ($water) {
    b('<div id="chart"></div>');
    b();

    css_table(array(140, 70, 70, 320, 18));
    icona(array('pencil-button'));

    b('<table class="lst f10">');
    b('<tr>');
    b('<td>Дата, время');
    b('<td>Холодная');
    b('<td>Горячая');
    b('<td>Примечание');
    b('<td>Д.');

    $chart_c = array();
    $chart_h = array();
    $prev_c = $prev ? $prev['val'] : 0;
    $prev_h = $prev ? $prev['val2'] : 0;
    foreach ($water as $k=>$v) {

      b('<tr>');

      b('<td>');
      b(dateh($v['dt']));


      b('<td>');
      b(substr($v['val'],0,-3));

      //b('<span style="font-size: 8pt;">');
      b('<span style="color: red;">');
      b(substr($v['val'],-3,3));
      b('</span>');


      b('<td>');
      b(substr($v['val2'],0,-3));

      //b('<span style="font-size: 8pt;">');
      b('<span style="color: red;">');
      b(substr($v['val2'],-3,3));
      b('</span>');


      b('<td>');
      b($v['desc']);


      b('<td>');
      if (p('edit'))  b(icona('/'.$mod.'/wte/?wtr='.$k));


      if ($prev_c)  $chart_c[] = $v['val'] - $prev_c;  else $chart_c[] = 0;
      if ($prev_h)  $chart_h[] = $v['val2'] - $prev_h;  else $chart_h[] = 0;

      $prev_c = $v['val'];
      $prev_h = $v['val2'];
      }

    b('</table>');


    // ---------------- inline JavaScript ---------------- //

  b('<script>');
  $file = 'chart.js';
  b(fread (fopen ($file, 'rb'), filesize ($file) ));

  b('
var ch = new $chart("chart");
ch.water([['.implode(',',$chart_c).'],['.implode(',',$chart_h).']]);
');
  b('</script>');
    }

  else {
    b('<p class="p">Ошибка: данные отсутствуют.');
    }

  }






  // ---------------------------------------------------- add / edit  water --------------------------------------------------------- //



  // -------------------------- add / edit -------------------------- //

if ($act == 'wte' && p('edit') ) {

  $water = array(
    'dt' => $curr['datetime'],
    'val' => 0,
    'val2' => 0,
    'desc' => '',
    );
  $water['dt'][17] = '0';
  $water['dt'][18] = '0';

  if ($gwtr) {
    $col = array();
    foreach ($water as $k=>$v)  $col[] = $k;

    $water = $db->
      table('water')->
      col($col)->
      where('`id` = '.$gwtr)->
      r();
    }
  else {
    $last = $db->
      table('water')->
      col('val', 'val2')->
      order('`dt` DESC')->
      r();

    $water['val'] = $last['val'];
    $water['val2'] = $last['val2'];
    }


    // ---- submenu ---- //
  if (p() && $gwtr)  $submenu['?Удалить;minus-button'] = array('#Подтвердить;tick-button' => form_sbd('/'.$mod.'/wtu/?wtr='.$gwtr));
  submenu();
    // ---- end: submenu ---- //




  b('<p class="h1">');
  if (!$gwtr)  b('Добавление');
  else         b('Редактирование');
  b('</p>');
  b();
  b();


  b(form('water', '/'.$mod.'/wtu/', array(
    $gwtr ? 'wtr='.$gwtr : ''
    )));

  b('<table class="edt">');


  b('<tr><td>');
  b('Дата, время:');
  b('<td>');
  b(form_dt(array('f_water_date_y;2000', 'f_water_date_m', 'f_water_date_d', 'f_water_date_h', 'f_water_date_i', 'f_water_date_s'),  $water['dt'] ));


  b('<tr><td>');
  b('Холодная:');
  b('<td>');
  b(form_n('@f_water_val', $water['val'], 80));


  b('<tr><td>');
  b('Горячая:');
  b('<td>');
  b(form_n('f_water_val2', $water['val2'], 80));


  b('<tr><td>');
  b('Описание:');
  b('<td>');
  b(form_t('f_water_desc', $water['desc'], 300));


  b('</table>');


  b(form_sb());

  b('</form>');
  }




  // ------------------------------------------- ajax: update ------------------------------------------------ //

if ($act == 'wtu' && p('edit') ) {
  $ajax = TRUE;

  $post = postb('f_water_desc');

  $table = 'water';
  $where = '`id` = '.$gwtr;


  if ($post) {
    $set = array();
    $set['dt'] = datesql(postn('f_water_date_y'), postn('f_water_date_m'), postn('f_water_date_d'), postn('f_water_date_h'), postn('f_water_date_i'), postn('f_water_date_s'));
    $set['val'] = postn('f_water_val');
    $set['val2'] = postn('f_water_val2');
    $set['desc'] = post('f_water_desc');

    if ($gwtr) {
      $db->table($table)->set($set)->where($where)->u();
      }

    else {
      $gwtr = $db->table($table)->set($set)->i();
      }

    b('/'.$mod.'/?date='.substr($set['dt'],0,10));
    }


    // ---- deletion ---- //
  if (!$post && $gwtr && p()) {
    $pdate = $db->
      table('water')->
      col('!DATE(`dt`)')->
      where($where)->
      r();

    $result = $db->table($table)->where($where)-d();

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