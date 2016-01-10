<?php

/************************************************************************/
/*  Расходы  v1.1o                                                      */
/************************************************************************/


if (!isset($body))  die ('error: this is a pluggable module and cannot be used standalone.');

$gexp = getn('exp');
$gexi = getn('exi');
$gexd = getn('exd');
$grow = getn('row');

$gdate = gets('date', $curr['date']);
$gyear = gets('year', $curr['year']);


$db_type = array(
  //0 => array('- - - -', '#000;'),
  1 => array('продукты', '#060'),
  2 => array('компьютерная техника', '#a90'),
  3 => array('прочая техника', '#960'),
  4 => array('бытовая продукция', '#00f'),
  5 => array('стройматериалы', '#00f'),
  6 => array('стройинструмент', '#00f'),
  7 => array('канцтовары', '#00f'),
  8 => array('гамес', '#f0f'),
  9 => array('инет', '#f0f'),
  10 => array('автобус', '#00f'),
  11 => array('трамвай', '#00f'),
  12 => array('телефон', '#00f'),
  13 => array('кредит', '#f8f'),
  14 => array('коммунальные платежи', '#a0f'),
  15 => array('бензин', '#00f'),
  16 => array('авто-обслуживание', '#00f'),
  17 => array('авто-документы', '#00f'),
  18 => array('семечки', '#00f'),

  99 => array('прочее', '#f00'),
  );

$db_doc_type = array(
  //0 => array('- - - -', '#000;'),
  1 => array('кассовый чек', '#060'),
  2 => array('аквиринговый отчёт', '#606'),
  3 => array('счёт', '#606'),
  4 => array('гарантийный талон', '#606'),

  99 => array('прочее', '#f00'),
  );

$db_store = array(
  0 => array('- - - -', '#000;'),
  1 => array('Линия', '#00f'),
  2 => array('Пятёрочка', '#00f'),
  3 => array('Карусель', '#00f'),
  4 => array('Маскарад', '#00f'),
  5 => array('Европа', '#00f'),
  6 => array('Battle.net', '#00f'),
  7 => array('sc2tv', '#00f'),
  8 => array('Steam', '#00f'),
  9 => array('Велта', '#00f'),
  10 => array('Магнит', '#00f'),
  11 => array('DNS-Shop', '#00f'),
  12 => array('Осколнэт', '#00f'),
  13 => array('Рет', '#00f'),
  14 => array('Уютерра', '#00f'),
  15 => array('Славянка', '#00f'),
  16 => array('Квант', '#00f'),
  17 => array('Стройдепо', '#00f'),
  18 => array('M-Видео', '#00f'),
  19 => array('Компьютер-Центр', '#00f'),
  20 => array('Best-hoster', '#00f'),
  21 => array('Aliexpress', '#00f'),
  22 => array('рац', '#00f'),
  23 => array('Лукойл', '#00f'),

  //99 => array('прочее', '#f00'),
  );






if (!$act) {

  $gyear = datee($gdate);
  $gmon = datee($gdate,'m');
  $gmdays = date('t', mktime(0, 0, 0, $gmon, 1, $gyear));

  $where = array('`expense`.`dt` >= \''.datesql($gyear, $gmon, 1, 0, 0, 0).'\'',
                 '`expense`.`dt` <= \''.datesql($gyear, $gmon, $gmdays, 23, 59, 59).'\'',
                 );

  $expense = db_read(array('table' => 'expense',
                           'col' => array('id', 'dt', 'price', 'store', 'desc'),
                           'where' => $where,
                           'order' => '`dt`',
                           'key' => 'id',
                           ));


  $where_ = $where;
  $where_[] = '`expense`.`id` = `expense_item`.`p`';

  $expense_item = db_read(array('table' => array('expense', 'expense_item'),
                                'col' => array('expense`.`id', 'expense_item`.`id` AS `bi_id', 'expense_item`.`price', 'expense_item`.`quantity', 'expense_item`.`type', 'expense_item`.`desc'),
                                'where' => $where_,
                                'key' => array('id', 'bi_id'),
                                ));


  $where_ = $where;
  $where_[] = '`expense`.`id` = `expense_doc`.`p`';

  $expense_doc = db_read(array('table' => array('expense', 'expense_doc'),
                               'col' => array('expense`.`id', 'expense_doc`.`id` AS `bd_id', 'expense_doc`.`type'),
                               'where' => $where_,
                               'key' => array('id', 'bd_id'),
                               ));




    // ---- submenu ---- //

  $date_prev = datesql(mktime(0,0,0, ($gmon-1),1,$gyear));
  $date_next = datesql(mktime(0,0,0, ($gmon+1),1,$gyear));

  $submenu[datee($date_prev).'.'.datee($date_prev, 'M').';navigation-180-button'] = '/'.$mod.'/?date='.$date_prev;
  $submenu[datee($date_next).'.'.datee($date_next, 'M').';navigation-000-button'] = '/'.$mod.'/?date='.$date_next;

  $submenu['Календарь;calendar-select'] = '/'.$mod.'/cdr/';

  if (p('edit'))  $submenu['Добавить;plus-button'] = '/'.$mod.'/exe/';
  submenu();

    // ---- end: submenu ---- //




  b('<p class="h1">Расходы ('.substr('00'.$gmon,-2,2).'.'.$gyear.')</p>');
  b();


  if ($expense) {
    b('<style>
table.lst td {vertical-align: top;}
</style>');
    css_table(array(140, 80, 120, 310, '34', 54));
    icona(array('pencil-button','plus-button','receipt-text--paper-clip'));

    b('<table id="expense_table" class="lst f10">');
    b('<tr>');
    //b('<td class="f10 b">id');
    b('<td>Дата, время');
    b('<td>Сумма');
    b('<td>Примечание');
    b('<td id="items">items');
    b('<td id="docs">docs');
    b('<td>Действ.');

    foreach ($expense as $k=>$v) {

      b('<tr>');

      b('<td id="'.$k.'">');
      //if (p('edit'))  b('<a href="/'.$mod.'/exe/?exp='.$k.'">');
      //b($k);
      b(dateh($v['dt']));
      //if (p('edit'))  b('</a>');


      b('<td>');
      //b(frach($v['price']).' р');
      $pricee = explode(',', frach($v['price']));
      if (count($pricee) == 1)  $pricee[1] = 0;

      b('<div style="white-space: nowrap;">');

      b('<div style="display: inline-block; width: 40px; text-align: right;">');  // border:1px solid red;
      //b('<a class="k" href="/'.$mod.'/eie/?exp='.$k.'">');
      b($pricee[0]);
      //b('</a>');
      b('</div>');

      b('<div style="display: inline-block; width: 30px;">');  // border:1px solid blue;
      //b('<a class="k" href="/'.$mod.'/ede/?exp='.$k.'">');
      if ($pricee[1])  b(','.$pricee[1]);
      b('&nbsp;р');
      //b('</a>');
      b('</div>');

      b('</div>');



      b('<td>');
      if ($v['store']) {
        b('<span style="color: '.$db_store[$v['store']][1].'">'.$db_store[$v['store']][0].'</span>');
        }
      if ($v['desc']) {
        b('<span class="cg">'.$v['desc'].'</span>');
        }


      b('<td>');

      if (isset($expense_item[$k])) {

        //b('<table class="f10">');
        //foreach ($expense_item[$k] as $kk=>$vv) {
        //  b('<tr>');
        //
        //  b('<td class="li t" width="200">');
        //  if (p('edit'))  b('<a href="/'.$mod.'/eie&exi='.$kk.'">');
        //  b($vv['desc']);
        //  if (p('edit'))  b('</a>');
        //
        //  b('<td class="r t" style="padding-right:3px;" width="110">');
        //  if ($vv['quantity'])  b(fth($vv['quantity']).' x ');
        //  b('<b>'.frach($vv['price']).' р</b>');
        //  }
        //b('</table>');

        foreach ($expense_item[$k] as $kk=>$vv) {
          b('<div style="padding: 0 3px 0 3px;">');  // border:1px solid blue;  background: #afa;

          b('<div>');  // style="background: #faa;"

          b('<div style="float: right;  padding-left: 5px;">');  // border:1px solid blue;  background: #aaf;
          if ($vv['quantity'])  b(fth($vv['quantity']).' x ');
          b(frach($vv['price']).' р');
          b('</div>');

          if (p('edit'))  b('<a style="color: '.$db_type[$vv['type']][1].'" href="/'.$mod.'/eie/?exi='.$kk.'">');
          b($vv['desc']);
          if (p('edit'))  b('</a>');

          b('</div>');

          b('</div>');
          }
        }


      b('<td>');
      //b('<a href="/'.$mod.'/edv&exp='.$k.'" target="_blank">'. (isset($expense_doc[$k]) ? count($expense_doc[$k]) : '–') .'</a>');
      if (isset($expense_doc[$k])) {
        $tmp = array();
        $n = 1;
        foreach ($expense_doc[$k] as $kk=>$vv) {
          $tmp[] = '<a style="color: '.$db_doc_type[$vv['type']][1].'" href="/i/expense_doc,'.dechex($kk).'" target="_blank">'
                  .$n++
                  .'</a>';
          }
        b(implode(' ', $tmp));
        }


      b('<td>');
      if (p('edit'))  b(icona('/'.$mod.'/exe/?exp='.$k));
      if (p('edit'))  b(icona('/'.$mod.'/eie/?exp='.$k));
      if (p('edit'))  b(icona('/'.$mod.'/ede/?exp='.$k));
      }

    b('</table>');


  //b('<script>');
  //b('
  //
  //$.context("expense_table", {
  //
  //  menu: {
  //
  //    items: [
  //      {
  //      desc: "Добавить",
  //      href: "/"+mod+"/eie/?"
  //      }
  //      ],
  //
  //    docs: [
  //      {
  //      desc: "Добавить чек",
  //      href: "/"+mod+"/ede/?bdt=1"
  //      },
  //
  //      {
  //      desc: "Добавить ак.отчёт",
  //      href: "/"+mod+"/ede/?bdt=2"
  //      },
  //
  //      {
  //      desc: "Добавить прочее",
  //      href: "/"+mod+"/ede/?bdt=99"
  //      }
  //      ]
  //    }
  //
  //  });

  //');
  //b('</script>');
    }

  else {
    b('<p class="p">Ошибка: данные отсутствуют.');
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






  // ---------------------------------------------------- add / edit  expense --------------------------------------------------------- //



  // -------------------------- add / edit -------------------------- //

if ($act == 'exe' && p('edit') ) {

  asort($db_store);

  $expense = array('dt' => $curr['datetime'],
                   'price' => 0,
                   'store' => 0,
                   'desc' => '',
                   );
  $expense['dt'][17] = '0';
  $expense['dt'][18] = '0';

  if ($gexp) {
    $col = array();
    foreach ($expense as $k=>$v)  $col[] = $k;

    $expense = db_read(array('table' => 'expense',
                             'col' => $col,
                             'where' => '`id` = '.$gexp,
                             ));
    }


    // ---- submenu ---- //
  if (p() && $gexp)  $submenu['?Удалить;minus-button'] = array('#Подтвердить;tick-button' => form_sbd('/'.$mod.'/exu/?exp='.$gexp));
  submenu();
    // ---- end: submenu ---- //




  b('<p class="h1">');
  if (!$gexp)  b('Добавление');
  else         b('Редактирование');
  b('</p>');
  b();
  b();


  b(form('expense', '/'.$mod.'/exu/?'
    .($gexp ? '&exp='.$gexp : '')
    ));

  b('<table class="edt">');


  b('<tr><td>');
  b('Дата, время:');
  b('<td class="t">');
  b(form_dt(array('f_expense_date_y;2000', 'f_expense_date_m', '@f_expense_date_d', 'f_expense_date_h', 'f_expense_date_i', 'f_expense_date_s'),  $expense['dt'] ));


  b('<tr><td>');
  b('Сумма:');
  b('<td>');
  b(form_t('f_expense_price', frach($expense['price']), 100));


  b('<tr><td>');
  b('Магазин:');
  b('<td>');
  b(form_s('f_expense_store;0', $db_store, $expense['store']));


  b('<tr><td>');
  b('Описание:');
  b('<td>');
  b(form_t('f_expense_desc', $expense['desc'], 300));


  b('</table>');


  b(form_sb());

  b('</form>');
  }




  // ------------------------------------------- ajax: update ------------------------------------------------ //

if ($act == 'exu' && p('edit') ) {
  $ajax = TRUE;

  $post = postb('f_expense_desc');

  $table = 'expense';
  $where = '`id` = '.$gexp;

  //function  history_copy() {
  //  global  $table, $where;
  //  global  $gexp;
  //
  //  $copy = db_read(array('table' => $table,
  //                          'col' => array('line', 'phone', 'dt', 'len', 'datex', 'userx'),
  //                          'where' => $where,
  //                          ));
  //  $copy['parent'] = $gexp;
  //
  //  db_insert($table.'_h', $copy);
  //  }

  if ($post) {

    $set = array();
    $set['dt'] = datesql(postn('f_expense_date_y'), postn('f_expense_date_m'), postn('f_expense_date_d'), postn('f_expense_date_h'), postn('f_expense_date_i'), postn('f_expense_date_s'));
    $set['price'] = fraci(post('f_expense_price'));
    $set['store'] = postn('f_expense_store');
    $set['desc'] = post('f_expense_desc');


    if ($gexp) {
      //if (!p('edit_all')) {
      //  $expense = db_read(array('table' => 'expense',
      //                          'col' => 'userx',
      //                          'where' => '`id` = '.$gexp,
      //                          ));
      //  if ($expense != $auth['userx'])  die('error: user not match.');
      //  }
      //
      //history_copy();
      db_write(array('table'=>$table, 'set'=>$set, 'where'=>$where));
      }

    else {
      $gexp = db_write(array('table'=>$table, 'set'=>$set));
      }

    b('/'.$mod.'/?date='.substr($set['dt'],0,10));
    }


    // ---- deletion ---- //
  if (!$post && $gexp && p()) {
    $pdate = db_read(array('table' => 'expense',
                           'col' => '!DATE(`dt`)',
                           'where' => '`id` = '.$gexp,
                           ));

    //history_copy();
    $result = db_write(array('table'=>$table, 'where'=>$where));

    //if ($result)  b('ok');
    //else          b('failed');

    b('/'.$mod.'/?date='.$pdate);
    }  // end: delete

  }






  // -------------------------- add / edit  item -------------------------- //

if ($act == 'eie' && p('edit') ) {

  if (!$gexp && $grow)  $gexp = $grow;

  $exp_item = array('p' => $gexp,
                    'price' => 0,
                    'quantity' => 0,
                    'type' => 1,
                    'desc' => '',
                    );

  if ($gexi) {
    $col = array();
    foreach ($exp_item as $k=>$v)  $col[] = $k;

    $exp_item = db_read(array('table' => 'expense_item',
                              'col' => $col,
                              'where' => '`id` = '.$gexi,
                              ));
    }


    // ---- submenu ---- //
  if (p() && $gexi)  $submenu['?Удалить;minus-button'] = array('#Подтвердить;tick-button' => form_sbd('/'.$mod.'/eiu/?exi='.$gexi));
  submenu();
    // ---- end: submenu ---- //




  b('<p class="h1">');
  if (!$gexi)  b('Добавление');
  else         b('Редактирование');
  b('</p>');
  b();
  b();


  b(form('exp_item', '/'.$mod.'/eiu/?'
    .($gexp ? '&exp='.$gexp : '')
    .($gexi ? '&exi='.$gexi : '')
    ));

  b('<table class="edt">');


  b('<tr><td>');
  b('Описание:');
  b('<td>');
  b(form_t('@f_ei_desc', $exp_item['desc'], 300));


  b('<tr><td>');
  b('Категория:');
  b('<td>');
  b(form_s('f_ei_type;0', $db_type, $exp_item['type']));


  b('<tr><td>');
  b('Цена:');
  b('<td>');
  b(form_t('f_ei_price', frach($exp_item['price']), 100));


  b('<tr><td>');
  b('Кол-во / вес:');
  b('<td>');
  b(form_t('f_ei_quantity', fth($exp_item['quantity']), 100));


  b('</table>');


  b(form_sb());

  b('</form>');
  }




  // ------------------------------------------- ajax: update ------------------------------------------------ //

if ($act == 'eiu' && p('edit') ) {
  $ajax = TRUE;

  $post = postb('f_ei_desc');

  $table = 'expense_item';
  $where = '`id` = '.$gexi;


  if ($post) {
    $set = array();
    $set['desc'] = post('f_ei_desc');
    $set['price'] = fraci(post('f_ei_price'));
    $set['quantity'] = fti(post('f_ei_quantity'));
    $set['type'] = postn('f_ei_type');

    if ($gexi) {
      db_write(array('table'=>$table, 'set'=>$set, 'where'=>$where));

      $pdate = db_read(array('table' => array('expense', 'expense_item'),
                             'col' => '!DATE(`expense`.`dt`)',
                             'where' => array('`expense`.`id` = `expense_item`.`p`',
                                              '`expense_item`.`id` = '.$gexi,
                                              ),
                             ));
      }

    elseif ($gexp) {
      $set['p'] = $gexp;
      $gexi = db_write(array('table'=>$table, 'set'=>$set));

      $pdate = db_read(array('table' => 'expense',
                             'col' => '!DATE(`dt`)',
                             'where' => '`id` = '.$gexp,
                             ));
      }

    b('/'.$mod.'/?date='.$pdate);
    }


    // ---- deletion ---- //
  if (!$post && $gexi && p()) {
    $pdate = db_read(array('table' => array('expense', 'expense_item'),
                           'col' => '!DATE(`expense`.`dt`)',
                           'where' => array('`expense`.`id` = `expense_item`.`p`',
                                            '`expense_item`.`id` = '.$gexi,
                                            ),
                           ));

    $result = db_write(array('table'=>$table, 'where'=>$where));

    //if ($result)  b('ok');
    //else          b('failed');

    b('/'.$mod.'/?date='.$pdate);
    }  // end: delete

  }






  // -------------------------- view doc -------------------------- //

//if ($act == 'edv') {
//  b('todo');
//  }




  // -------------------------- add / edit  doc -------------------------- //

if ($act == 'ede' && p('edit') ) {

  if (!$gexp && $grow)  $gexp = $grow;
  $gedt = getn('edt', 1);

  $exp_doc = array('p' => $gexp,
                   'type' => $gedt,
                   );

  if ($gexd) {
    $col = array();
    foreach ($exp_doc as $k=>$v)  $col[] = $k;

    $exp_doc = db_read(array('table' => 'expense_doc',
                             'col' => $col,
                             'where' => '`id` = '.$gexd,
                             ));
    }


    // ---- submenu ---- //
  if (p() && $gexd)  $submenu['?Удалить;minus-button'] = array('#Подтвердить;tick-button' => form_sbd('/'.$mod.'/edu/?exd='.$gexd));
  submenu();
    // ---- end: submenu ---- //




  b('<p class="h1">');
  if (!$gexd)  b('Добавление');
  else         b('Редактирование');
  b('</p>');
  b();
  b();


  b(form('exp_doc', '/'.$mod.'/edu/?'
    .($gexp ? '&exp='.$gexp : '')
    .($gexd ? '&exd='.$gexd : '')
    ));

  b('<table class="edt">');


  b('<tr><td>');
  b('Категория:');
  b('<td>');
  b(form_s('@f_ed_type;0', $db_doc_type, $exp_doc['type']));


  b('<tr><td>');
  b('Файл:');
  b('<td>');
  b('<input name="f_ed_file" type="file" size="60" onchange="this.form.onsubmit();">');


  b('</table>');


  b(form_sb());

  b('</form>');
  }




  // ------------------------------------------- ajax: update ------------------------------------------------ //

if ($act == 'edu' && p('edit') ) {
  $ajax = TRUE;

  $post = postb('f_ed_type');

  $table = 'expense_doc';
  $where = '`id` = '.$gexd;


  if ($post) {
    $set = array();
    $set['type'] = postn('f_ed_type');

    if ($gexd) {
      db_write(array('table'=>$table, 'set'=>$set, 'where'=>$where));

      $pdate = db_read(array('table' => array('expense', 'expense_doc'),
                             'col' => '!DATE(`expense`.`dt`)',
                             'where' => array('`expense`.`id` = `expense_doc`.`p`',
                                              '`expense_doc`.`id` = '.$gexd,
                                              ),
                             ));
      }

    elseif ($gexp) {
      $set['p'] = $gexp;
      $gexd = db_write(array('table'=>$table, 'set'=>$set));

      $pdate = db_read(array('table' => 'expense',
                             'col' => '!DATE(`dt`)',
                             'where' => '`id` = '.$gexp,
                             ));

      $ed_file = $_FILES['f_ed_file']['tmp_name'];

      if (file_exists($ed_file)) {
        $data = fread (fopen ($ed_file, 'rb'), filesize($ed_file));
        img_upload_fdb ('expense_doc', $gexd, $data);
        }

      }

    b('/'.$mod.'/?date='.$pdate);
    }


    // ---- deletion ---- //
  if (!$post && $gexd && p()) {
    $pdate = db_read(array('table' => array('expense', 'expense_doc'),
                           'col' => '!DATE(`expense`.`dt`)',
                           'where' => array('`expense`.`id` = `expense_doc`.`p`',
                                            '`expense_doc`.`id` = '.$gexd,
                                            ),
                           ));

    $result = db_write(array('table'=>$table, 'where'=>$where));

    //img_upload_fdb ('expense_doc', $gexd, $data);  remove physical file

    //if ($result)  b('ok');
    //else          b('failed');

    b('/'.$mod.'/?date='.$pdate);
    }  // end: delete

  }


?>