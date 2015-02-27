<?php

/************************************************************************/
/*  Визиты  v3.oo                                                       */
/************************************************************************/


if (!isset($body))  die ('error: this is a pluggable module and cannot be used standalone.');

$gvst = getn('vst');
$gplc = getn('plc');

$gdate = gets('date', $curr['date']);
$gyear = gets('year', $curr['year']);






  // ------------------------------------------- visit 3.0 ------------------------------------------------ //

if (!$act) {

  $gyear = datee($gdate);
  $gmon = datee($gdate,'m');
  $gmdays = date('t', mktime(0, 0, 0, $gmon, 1, $gyear));

  $datebeg = datesql(mktime(0, 0, 0, $gmon, 2 - date('N', mktime(0, 0, 0, $gmon, 1, $gyear)), $gyear));
  $dateend = datesql(mktime(0, 0, 0, $gmon, $gmdays + (7-date('N', mktime(0, 0, 0, $gmon, $gmdays, $gyear))), $gyear));
  $weeks = round((datesqltime($dateend) - datesqltime($datebeg) +86400) / (86400 * 7));


  $where = array('`visit`.`dt` >= \''.$datebeg.' 00:00:00\'',  // datesql($gyear, $gmon, 1, 0, 0, 0)
                 '`visit`.`dt` <= \''.$dateend.' 23:59:59\'',  // datesql($gyear, $gmon, $gmdays, 23, 59, 59)
                 );

  $visit = db_read(array('table' => 'visit',
                         'col' => array('id', 'dt', 'people', 'place', 'lat', 'lon', 'desc', '!DATE(`dt`) AS `dated`'),
                         'where' => $where,
                         'order' => '`dt`',
                         'key' => array('dated', 'id'),
                         ));
  $where2 = $where;
  $where2[] = '`people`.`id` = `visit`.`people`';
  $people = db_read(array('table' => array('people', 'visit'),
                          'col' => array('people`.`id', 'people`.`surname', 'people`.`name', 'people`.`otchestvo'),
                          'where' => $where2,
                          'key' => 'id',
                          ));

  $where2 = $where;
  $where2[] = '`place`.`id` = `visit`.`place`';
  $place = db_read(array('table' => array('place', 'visit'),
                         'col' => array('place`.`id', 'place`.`desc'),
                         'where' => $where2,
                         'key' => 'id',
                         'value' => 'desc',
                         ));


    // ---- submenu ---- //

  $date_prev = datesql(mktime(0,0,0, ($gmon-1),1,$gyear));
  $date_next = datesql(mktime(0,0,0, ($gmon+1),1,$gyear));

  $submenu[datee($date_prev).'.'.datee($date_prev, 'M').';navigation-180-button'] = '/'.$mod.'/?date='.$date_prev;
  $submenu[datee($date_next).'.'.datee($date_next, 'M').';navigation-000-button'] = '/'.$mod.'/?date='.$date_next;

  $submenu['Календарь;calendar-select'] = '/'.$mod.'/cdr/';
  //$submenu['Визиты 1.0'] = '/'.$mod.'/vs1/';
  $submenu['Места'] = '/'.$mod.'/plc/';

  if (p('edit'))  $submenu['Добавить;plus-button'] = '/'.$mod.'/vse/';
  submenu();

    // ---- end: submenu ---- //




  b('<p class="h4">Визиты ('.substr('00'.$gmon,-2,2).'.'.$gyear.')</p>');


  if ($visit) {

    b('<style>
');
$file = 'm/'.$mod.'/vs_style.css';
b(fread (fopen ($file, 'rb'), filesize ($file) ));
b('
</style>
');


    b('<table>');

    $date = $datebeg;
    for ($y = 0; $y < $weeks; $y++) {
      b('<tr>');

      for ($x = 0; $x < 7; $x++) {
        b('<td class="vsc"');
        if (datee($date,'m') != $gmon)  b(' style="opacity: 0.3;');
        b('">');

        b('<div class="vsn"');
        if ($date == $curr['date'])  b(' style="background-color: #ff8;"');
        b('>');
        b(datee($date,'d'));
        b('</div>');

        if (isset($visit[$date])) {

          //$tmp = array();
          foreach ($visit[$date] as $kk=>$vv) {
            b('<div class="vsv">');
            b(substr($vv['dt'],11,5));
            b('<div class="vsm"> - </div>');
            //if (p('edit'))  b('<a href="/'.$mod.'/vse/?vst='.$kk.'">');

            b('<div class="vsd">');
            $ab = '';
            $ae = '';
            if (p('edit')) {
              $ab = '<a class="vsd" href="/'.$mod.'/vse/?vst='.$kk.'">';
              $ae = '</a>';
              }
            $tmp = array();

            if ($vv['people']) {
              $tmp[] = $ab.'<span style="color: #0a0;">'.fiof($people[$vv['people']]['surname'], $people[$vv['people']]['name'], '').'</span>'.$ae;  // $people[$vv['people']]['otchestvo']
              }

            if ($vv['place']) {
              $tmp[] = $ab.'<span style="color: #a0a;">'.$place[$vv['place']].'</span>'.$ae;
              }

            if ($vv['lat']) {
              $tmp[] = '<a style="color: #a0a;" href="/'.$mod.'/mpv/?lat='.$vv['lat'].'&lon='.$vv['lon'].'" target="_blank">@</a>';
              }

            if ($vv['desc']) {
              $tmp[] = $ab.$vv['desc'].$ae;
              }

            if (!$tmp) $tmp[] = $ab.'–'.$ae;

            b(implode(', ', $tmp));
            b('</div>');

            //if (p('edit'))  b('</a>');
            b('</div>');  // vsv
            //$tmp[] = $tmpe;
            }
          //if ($tmp)  b(implode('<br>', $tmp));
          //if ($tmp)  b(implode($tmp));
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






  // ---------------------------------------------------- add / edit  visit --------------------------------------------------------- //



  // -------------------------- add / edit -------------------------- //

if ($act == 'vse' && p('edit') ) {

  $place = db_read(array('table' => 'place',
                         'col' => array('id', 'desc'),
                         'key' => 'id',
                         'value' => 'desc',
                         ));

  $place = array_reverse($place, true);
  $place[0] = '- - - - - - - - -';
  $place = array_reverse($place, true);


  $visit = array('dt' => $curr['datetime'],
                 'people' => 0,
                 'place' => 0,
                 'lat' => 0,
                 'lon' => 0,
                 'desc' => '',
                 );
  $visit['dt'][17] = '0';
  $visit['dt'][18] = '0';

  if ($gvst) {
    $col = array();
    foreach ($visit as $k=>$v)  $col[] = $k;

    $visit = db_read(array('table' => 'visit',
                           'col' => $col,
                           'where' => '`id` = '.$gvst,
                           ));
    }


  $people = array(0 => '- - - - - - - - -');
  if ($visit['people']) {
    $people1 = db_read(array('table' => 'people',
                             'col' => array('surname', 'name', 'otchestvo'),
                             'where' => '`id` = '.$visit['people'],
                             ));
    $people[$visit['people']] = fiof($people1['surname'], $people1['name'], $people1['otchestvo']);
    }


    // ---- submenu ---- //
  if (p() && $gvst)  $submenu['?Удалить;minus-button'] = array('#Подтвердить;tick-button' => form_sbd('/'.$mod.'/vsu/?vst='.$gvst));
  submenu();
    // ---- end: submenu ---- //




  b('<p class="h1">');
  if (!$gvst)  b('Добавление');
  else         b('Редактирование');
  b('</p>');
  b();
  b();


  b(form('visit', '/'.$mod.'/vsu/?'
    .($gvst ? '&vst='.$gvst : '')
    ));

  b('<table class="edt">');


  b('<tr><td>');
  b('Дата, время:');
  b('<td>');
  b(form_dt(array('f_visit_date_y;2000', 'f_visit_date_m', 'f_visit_date_d', '@f_visit_date_h', 'f_visit_date_i', 'f_visit_date_s'),  $visit['dt'] ));


  b('<tr><td>');
  b('Человек:');
  b('<td>');
  b(form_t(',peopleh', '', 100));
  b(' '.form_s('f_visit_people,people', $people, $visit['people']));
  b(' <input id="pplgeo" type="button" style="width: 20px; height: 20px; padding: 0;" value="O" onclick="if (pplgeo_lat) {$.id(\'lat\').value = pplgeo_lat; $.id(\'lon\').value = pplgeo_lon;}">');

  b('<script>
var people_geos = false;
var pplgeo_lat = false;
var pplgeo_lon = false;

var select = $.id(\'people\');
$.event(\'peopleh\', \'keyup\',
  $.delay(
    function() {
      $.ajax(\'/'.$mod.'/ph/\', function(r){
        if (r) {
          r = JSON.parse(r);
          while (select.length)  select.remove(0);
          people_geos = {};
          for (var i in r) {
            k = i.substr(1);
            var option = document.createElement("OPTION");
            option.value = k;
            option.text = r[i].fiof;
            //option.selected = false;
            select.add(option, null);
            people_geos[k] = {};
            people_geos[k].lat = r[i].lat;
            people_geos[k].lon = r[i].lon;
            }
          butgeo();
          }
        }, {post:{sch: $.id(\'peopleh\').value}})
      }, 0.5
    )
  );

var butgeo = function() {
  if (people_geos[select.value].lat != \'-90\') {
    pplgeo_lat = people_geos[select.value].lat;
    pplgeo_lon = people_geos[select.value].lon;
    }
  else {
    pplgeo_lat = false;
    pplgeo_lon = false;
    }
  }

$.event(\'people\', \'change\', butgeo);

</script>');


  b('<tr><td>');
  b('Место:');
  b('<td>');
  b(form_s('f_visit_place', $place, $visit['place']));


  b('<tr><td>');
  b('Координаты:');
  b('<td>');
  b(' '.form_t('f_lat,lat', ($visit['lat'] ? geoaf($visit['lat']) : ''), 90));
  b(' '.form_t('f_lon,lon', ($visit['lon'] ? geoof($visit['lon']) : ''), 90));
  b(' <input type="button" style="width: 20px; height: 20px; padding: 0;" value="O" onclick="window.open(\'/'.$mod.'/mpc/\');">');


  b('<tr><td>');
  b('Описание:');
  b('<td>');
  b(form_t('f_visit_desc', $visit['desc'], 300));


  b('</table>');


  b(form_sb());
  b('</form>');
  }




  // ------------------------------------------- ajax: update ------------------------------------------------ //

if ($act == 'vsu' && p('edit') ) {
  $ajax = TRUE;

  $post = postb('f_visit_desc');

  $table = 'visit';
  $where = '`id` = '.$gvst;

  if ($post) {

    $set = array();
    $set['dt'] = datesql(postn('f_visit_date_y'), postn('f_visit_date_m'), postn('f_visit_date_d'), postn('f_visit_date_h'), postn('f_visit_date_i'), postn('f_visit_date_s'));
    $set['people'] = postn('f_visit_people');
    $set['place'] = postn('f_visit_place');
    $set['lat'] = (post('f_lat') ? geoai(post('f_lat')) : 0);
    $set['lon'] = (post('f_lon') ? geooi(post('f_lon')) : 0);
    $set['desc'] = post('f_visit_desc');


    if ($gvst) {
      db_write(array('table'=>$table, 'set'=>$set, 'where'=>$where));
      }

    else {
      $gvst = db_write(array('table'=>$table, 'set'=>$set));
      }

    b('/'.$mod.'/?date='.substr($set['dt'],0,10));
    }


    // ---- deletion ---- //
  if (!$post && $gvst && p()) {
    $pdate = db_read(array('table' => 'visit',
                           'col' => '!DATE(`dt`)',
                           'where' => $where,
                           ));

    $result = db_write(array('table'=>$table, 'where'=>$where));
  
    //if ($result)  b('ok');
    //else          b('failed');

    b('/'.$mod.'/?date='.$pdate);
    }  // end: delete

  }




  // -------------------------------- ajax: people helper -------------------------------- //

if ($act == 'ph') {
  $ajax = TRUE;

  $gsch = filter_rlns(post('sch'));

  $gsch = strtr(mb_strtolower($gsch), array('a'=>'ф','b'=>'и','c'=>'с','d'=>'в','e'=>'у','f'=>'а','g'=>'п','h'=>'р','i'=>'ш','j'=>'о','k'=>'л','l'=>'д','m'=>'ь','n'=>'т','o'=>'щ','p'=>'з','q'=>'й','r'=>'к','s'=>'ы','t'=>'е','u'=>'г','v'=>'м','w'=>'ц','x'=>'ч','y'=>'н','z'=>'я','`'=>'ё','['=>'х',']'=>'ъ',','=>'б','.'=>'ю',';'=>'ж','\''=>'э'));


  if ($gsch) {
    while(($pos = strpos($gsch, '  ')) !== FALSE) {
      $gsch = substr($gsch,0,$pos).substr($gsch,$pos+1);
      }

    $gsch = explode(' ', $gsch);
    $where = array('(`surname` LIKE \''.((mb_strlen($gsch[0]) > 3)?'%':'').$gsch[0].'%\' OR `surnamef` LIKE \''.((mb_strlen($gsch[0]) > 3)?'%':'').$gsch[0].'%\' OR `nickname` LIKE \''.((mb_strlen($gsch[0]) > 3)?'%':'').$gsch[0].'%\')');
    if (isset($gsch[1]))  $where[] = '`name` LIKE \''.$gsch[1].'%\'';
    if (isset($gsch[2]))  $where[] = '`otchestvo` LIKE \''.$gsch[2].'%\'';

    $people = db_read(array('table' => 'people',
                            'col' => array('id', 'surname', 'name', 'otchestvo', 'lat', 'lon'),
                            'where' => $where,
                            'order' => array('`surname`', '`name`', '`otchestvo`'),
                            'limit' => '100',

                            'key' => 'id',
                            ));

      // -------------------------------- output -------------------------------- //

    if ($people) {
      $obj = array();
      foreach ($people as $k=>$v) {
        $obj['#'.$k] = array('fiof' => fiof($v['surname'], $v['name'], $v['otchestvo']),
                             'lat' => geoaf($v['lat']),
                             'lon' => geoof($v['lon']),
                             );
        }
      b(json_encode($obj));
      }
    }
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






  // -------------------------------------------------------------------------------------------------------------------- //
  // ---------------------------------------------------- Place --------------------------------------------------------- //
  // -------------------------------------------------------------------------------------------------------------------- //

if ($act == 'plc') {

  $place = db_read(array('table' => 'place',
                         'col' => array('id', 'lat', 'lon', 'desc'),
                         //'where' => $where,
                         //'order' => '`dt`',
                         'key' => 'id',
                         ));


    // ---- submenu ---- //
  if (p('edit'))  $submenu['Добавить;plus-button'] = '/'.$mod.'/ple/';
  submenu();
    // ---- end: submenu ---- //




  b('<p class="h1">Места</p>');
  b();


  if ($place) {
    css_table(array(30, 300, 36));
    icona(array('pencil-button','table'));

    b('<table class="lst f10">');
    b('<tr>');
    b('<td>id');
    b('<td>Описание');
    //b('<td>Д.');
    b('<td>Де-я');

    foreach ($place as $k=>$v) {

      b('<tr>');

      b('<td>');
      b($k);


      b('<td>');
      b('<a href="/'.$mod.'/mpv/?plc='.$k.'" target="_blank">'.$v['desc'].'</a>');


      b('<td>');
      if (p('edit'))  b(icona('/'.$mod.'/ple/?plc='.$k));
      if (p('edit'))  b(icona('/'.$mod.'/pgr/?plc='.$k));
      }

    b('</table>');
    }

  else {
    b('<p class="p">Ошибка: данные отсутствуют.');
    }

  }






  // ---------------------------------------------------- add / edit  place --------------------------------------------------------- //

if ($act == 'ple' && p('edit') ) {

  $place = array('lat' => 0,
                 'lon' => 0,
                 'desc' => '',
                 );

  if ($gplc) {
    $col = array();
    foreach ($place as $k=>$v)  $col[] = $k;

    $place = db_read(array('table' => 'place',
                           'col' => $col,
                           'where' => '`id` = '.$gplc,
                           ));
    }


    // ---- submenu ---- //
  if (p() && $gplc)  $submenu['?Удалить;minus-button'] = array('#Подтвердить;tick-button' => form_sbd('/'.$mod.'/plu/?plc='.$gplc));
  submenu();
    // ---- end: submenu ---- //




  b('<p class="h1">');
  if (!$gplc)  b('Добавление');
  else         b('Редактирование');
  b('</p>');
  b();
  b();


  b(form('place', '/'.$mod.'/plu/?'
    .($gplc ? '&plc='.$gplc : '')
    ));

  b('<table class="edt">');


  b('<tr><td>');
  b('Координаты:');
  b('<td>');
  b(' '.form_t('f_lat,lat', ($place['lat'] ? geoaf($place['lat']) : ''), 90));
  b(' '.form_t('f_lon,lon', ($place['lon'] ? geoof($place['lon']) : ''), 90));
  b(' <input type="button" style="width: 20px; height: 20px; padding: 0;" value="O" onclick="window.open(\'/'.$mod.'/mpc/\');">');


  b('<tr><td>');
  b('Описание:');
  b('<td>');
  b(form_t('@f_place_desc', $place['desc'], 300));


  b('</table>');


  b(form_sb());

  b('</form>');
  }




  // ------------------------------------------- ajax: update ------------------------------------------------ //

if ($act == 'plu' && p('edit') ) {
  $ajax = TRUE;

  $post = postb('f_place_desc');

  $table = 'place';
  $where = '`id` = '.$gplc;


  if ($post) {
    $set = array();
    $set['lat'] = (post('f_lat') ? geoai(post('f_lat')) : 0);
    $set['lon'] = (post('f_lon') ? geooi(post('f_lon')) : 0);
    $set['desc'] = post('f_place_desc');

    if ($gplc) {
      db_write(array('table'=>$table, 'set'=>$set, 'where'=>$where));
      }

    else {
      $gplc = db_write(array('table'=>$table, 'set'=>$set));
      }

    b('/'.$mod.'/plc/');
    }


    // ---- deletion ---- //
  if (!$post && $gplc && p()) {
    $pdate = db_read(array('table' => 'place',
                           'col' => '!DATE(`dt`)',
                           'where' => $where,
                           ));

    $result = db_write(array('table'=>$table, 'where'=>$where));

    b('/'.$mod.'/plc/');
    }  // end: delete

  }






  // -------------------------------------------------------------------------------------------------------------------- //
  // -------------------------------------------------- other ----------------------------------------------------------- //
  // -------------------------------------------------------------------------------------------------------------------- //


  // ---------------------------------------------------------------- graph weekly ---------------------------------------------------------------- //

if ($act == 'pgr') {

  for ($i = 2008; $i <= $curr['year']; $i++) {
    b('<p class="h2">'.$i);
    b('<div><img src="/'.$mod.'/grw/?plc='.$gplc.'&year='.$i.'"></div>');
    }
  }


if ($act == 'grw') {
  $ajax = TRUE;

  $visit = db_read(array('table' => 'visit',
                         'col' => array('id', 'dt',
                                        '!MONTH(`dt`) AS `dt_mon`', '!DAYOFMONTH(`dt`) AS `dt_day`',
                                        ),
                         'where' => array('`place` = '.$gplc,
                                          '`dt` >= \''.$gyear.'-01-01 00:00:00\'',
                                          '`dt` <= \''.$gyear.'-12-31 23:59:59\'',
                                          ),

                         'key' => array('dt_mon', 'dt_day', 'id'),
                         ));

  $size = 12;
  $spacing = ($size-1) - 2;

  $graph_w = $size*54;
  $graph_h = $size*7;

  $graph = imagecreatetruecolor($graph_w, $graph_h);
  imagealphablending($graph, false);
  imagesavealpha($graph, true);
  //$transp = imagecolorallocatealpha ($graph, 255, 255, 255, 127);
  //imagefilledrectangle ($graph,  0, 0,  $graph_w, $graph_h,  $transp);

  $back = imagecolorallocate($graph, 248, 248, 248);
  $empty = imagecolorallocate($graph, 216, 216, 216);
  $emptye = imagecolorallocate($graph, 232, 232, 232);
  $hit = imagecolorallocate($graph, 224, 64, 224);
  imagefilledrectangle ($graph,  0, 0,  $graph_w, $graph_h,  $back);


  $date = datesql(mktime(0,0,0, 1,1,$gyear));
  $datee = datesql(mktime(0,0,0, 1,1,$gyear+1));
  $wkd = date('N', datesqltime($date));
  $week = 0;
  while (1) {

    $x = $week * $size;
    $y = ($wkd-1) * $size;
    $month = datee($date,'m');
    $day = datee($date,'d');

    imagefilledrectangle ($graph,  $x, $y,  $x+$spacing, $y+$spacing,  (isset($visit[$month][$day]) ? $hit : ( $month%2 ? $empty : $emptye )));
    //d($date.' = '.($week*7).', '.(($wkd-1)*7).' - '.$month.', '.$day.' - '.(isset($visit[$month][$day]) ? '+' : '-'));

    $wkd++;
    if ($wkd > 7) {
      $wkd = 1;
      $week++;
      }

    $date = datesql(mktime(0,0,0, $month, $day+1, datee($date)));
    if ($date == $datee)  break;
    }


  header('Content-Type: image/png');
  imagepng($graph);
  clearstatcache();
  }




  // ---------------------------------------------------------------- ajax: map view ---------------------------------------------------------------- //

if ($act == 'mpv') {
  $ajax = TRUE;

  $glat = get('lat');
  $glon = get('lon');

  b('<!DOCTYPE html>'."\n\r".'<html><head>');

  b('<title>Просмотр точки на карте</title>');

  b('<meta charset="UTF-8">');
  b('<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">');
  b('<link rel="StyleSheet" type="text/css" href="/sh/leaflet.css">');
  b('<script type="text/javascript" src="/sh/leaflet.js"></script>');
  b('<script type="text/javascript" src="/j.js"></script>');
  b('<script>var mod = "'.$mod.'";</script>');
  b('</head><body style="padding:0; margin:0;">');

  if ($gplc) {
    $place = db_read(array('table' => 'place',
                           'col' => array('lat', 'lon'),
                           'where' => '`id` = '.$gplc,
                           ));
    }
  elseif ($glat) {
    $place = array('lat'=>$glat, 'lon'=>$glon);
    }

  b('<script>
var default_id = '.$gplc.';
var default_lat = '.geoaf($place['lat']).';
var default_lon = '.geoof($place['lon']).';
</script>');
  b('<script src="/sh/leaflet-mpv.js"></script>');

  b('</body></html>');
  }


  // ---------------------------------------------------------------- ajax: map choose ---------------------------------------------------------------- //

if ($act == 'mpc') {
  $ajax = TRUE;

  b('<!DOCTYPE html>'."\n\r".'<html><head>');

  b('<title>Выбор точки на карте</title>');

  b('<meta charset="UTF-8">');
  b('<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">');
  b('<link rel="StyleSheet" type="text/css" href="/sh/leaflet.css">');
  b('<script type="text/javascript" src="/sh/leaflet.js"></script>');
  b('<script type="text/javascript" src="/j.js"></script>');
  b('<script>var mod = "'.$mod.'";</script>');
  b('</head><body style="padding:0; margin:0;">');

  b('<script src="/sh/leaflet-mpc.js"></script>');

  b('</body></html>');
  }


  // ---------------------------------------------------------------- ajax: map points ---------------------------------------------------------------- //

if ($act == 'mpp') {
  $ajax = TRUE;

  $pnlat = geoai(post('nlat'));
  $pelon = geooi(post('elon'));
  $pslat = geoai(post('slat'));
  $pwlon = geooi(post('wlon'));


  $markers = db_read(array('table' => 'place',
                           'col' => array('id', 'lat', 'lon', 'desc'),
                           'where' => array('`lat` < '.$pnlat,
                                            '`lat` > '.$pslat,
                                            '`lon` > '.$pwlon,
                                            '`lon` < '.$pelon,
                                            ),
                           'limit' => 300,
                           'key' => 'id',
                           ));

  $obj = array();
  if ($markers) {
    foreach ($markers as $k=>$v) {
      $obj[$k] = array(
        'title' => $v['desc'],
        'lat' => geoaf($v['lat']),
        'lon' => geoof($v['lon']),
        );
      }
    b(json_encode($obj));
    }

  }


?>