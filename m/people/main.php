<?php

/************************************************************************/
/*  Люди  v1.oo                                                         */
/************************************************************************/


if (!isset($body))  die ('error: this is a pluggable module and cannot be used standalone.');

$gid = getn('id',1);
$gppl = getn('ppl');
//$gfce = getn('fce',1);
$gjnc = getn('jnc');
$gphn = getn('phn');
$gadr = getn('adr');
$ggrp = getn('grp');
$ggpp = getn('gpp');

$gdate = gets('date', $curr['date']);
$gyear = gets('year', $curr['year']);


include 'm/'.$mod.'/const.php';






  // --------------------------------------- список -------------------------------------------- //

if ($act == 'a') {
  $ajax = TRUE;
  $act = '';
  }


if (!$act) {

  $pgroup = db_read(array(
    'table' => 'pgroup',
    'col' => array('id', 'desc'),
    'key' => 'id',
    ));

  if ($ajax) {
    $pfio = post('fio');
    }


  if (!$ajax && $gid != 1) {
    $id = db_read(array('table' => 'people',
                        'col' => array('surname', 'name', 'otchestvo'),  // , 'surnamef', 'nickname', 'birthdate', 'addr', 'phone', 'vk', 'ok'
                        'where' => '`id` = '.$gid,
                        ));
    }


  $where = array();
  $where[] = '`people`.`id` = `people_junc`.`t`';
  $where[] = '`people_junc`.`f` = '.$gid;

  if ($ajax) {
    $pfio = strtr(mb_strtolower($pfio), array('a'=>'ф','b'=>'и','c'=>'с','d'=>'в','e'=>'у','f'=>'а','g'=>'п','h'=>'р','i'=>'ш','j'=>'о','k'=>'л','l'=>'д','m'=>'ь','n'=>'т','o'=>'щ','p'=>'з','q'=>'й','r'=>'к','s'=>'ы','t'=>'е','u'=>'г','v'=>'м','w'=>'ц','x'=>'ч','y'=>'н','z'=>'я','`'=>'ё','['=>'х',']'=>'ъ',','=>'б','.'=>'ю',';'=>'ж','\''=>'э'));

    while(($pos = strpos($pfio, '  ')) !== FALSE) {
      $pfio = substr($pfio,0,$pos).substr($pfio,$pos+1);
      }

    $pfio = explode(' ', $pfio);
    $where[] = '(`surname` LIKE \''.((mb_strlen($pfio[0]) > 3)?'%':'').$pfio[0].'%\' OR `surnamef` LIKE \''.((mb_strlen($pfio[0]) > 3)?'%':'').$pfio[0].'%\' OR `nickname` LIKE \''.((mb_strlen($pfio[0]) > 3)?'%':'').$pfio[0].'%\')';
    if (isset($pfio[1]))  $where[] = '`name` LIKE \''.$pfio[1].'%\'';
    if (isset($pfio[2]))  $where[] = '`otchestvo` LIKE \''.$pfio[2].'%\'';
    }

  $people = db_read(array('table' => array('people', 'people_junc'),
                          'col' => array('people`.`id', 'people`.`surname', 'people`.`name', 'people`.`otchestvo', 'people`.`surnamef', 'people`.`nickname', 'people`.`birthdate', 'people`.`deathdate', 'people`.`vk', 'people`.`ok', 'people`.`fb'),
                          'where' => $where,
                          'order' => array('`people`.`surname`', '`people`.`name`', '`people`.`otchestvo`'),
                          'key' => 'id',
                          ));


    // ---- lame read_all ---- //
  $people_junc = db_read(array('table' => 'people_junc',
                               'col' => array('people_junc`.`id', 'people_junc`.`f', 'people_junc`.`t', 'people_junc`.`rel', 'people_junc`.`misc'),  // , 'people_junc`.`know', 'people_junc`.`symp'
                               'where' => '`people_junc`.`f` = '.$gid,
                               'key' => 't',
                               ));

  //$people_junc_c = db_read(array('table' => 'people_junc',
  //                               'col' => array('people_junc`.`id', 'people_junc`.`f', 'people_junc`.`t', 'people_junc`.`know', 'people_junc`.`rel', 'people_junc`.`symp', 'people_junc`.`misc'),
  //                               'where' => '`people_junc`.`f` = '.$gid,
  //                               'key' => array('t', 'id'),
  //                               ));

    // ---- lame read_all ---- //
  $people_phone = db_read(array('table' => array('people', 'phone'),
                                'col' => array('phone`.`id', 'phone`.`pid', 'phone`.`num', 'phone`.`desc'),
                                'where' => '`people`.`id` = `phone`.`pid`',
                                'key' => array('pid', 'id'),
                                ));


    // ---- lame read_all ---- //
  $people_addr = db_read(array('table' => array('people', 'people_addr'),
                               'col' => array('people_addr`.`id', 'people_addr`.`pid', 'people_addr`.`addr', 'people_addr`.`lat', 'people_addr`.`lon'),
                               'where' => '`people`.`id` = `people_addr`.`pid`',
                               'key' => array('pid', 'id'),
                               ));


    // ---- lame read_all ---- //
  $people_group = db_read(array('table' => array('people', 'people_group'),
                                'col' => array('people_group`.`id', 'people_group`.`people', 'people_group`.`group'),
                                'where' => '`people`.`id` = `people_group`.`people`',
                                'key' => array('people', 'id'),
                                ));


  if (!$ajax) {
      // ---- submenu ---- //
    if (p('edit'))  $submenu['Добавить человека;user-green--plus'] = '/'.$mod.'/ppe/?id='.$gid;
    if (p('edit'))  $submenu['Редактировать;user-green--pencil'] = '/'.$mod.'/ppe/?id='.$gid.'&ppl='.$gid;
    //if (p('edit'))  $submenu['Добавить связь'] = '/'.$mod.'/jne/?id='.$gid;
    if (p('edit'))  $submenu['Группы;users'] = '/'.$mod.'/grp/';
    $submenu['#Фильтр;funnel'] = 'var a = $.id(\'flt\');  if (a.style.display == \'none\') {a.style.display=\'block\'; $.id(\'flt_fio\').focus();}  else a.style.display=\'none\'';
    submenu();

    b('<div id="flt" style="display: none;  background-color: #eee;  padding: 4px 8px;">');
    //b('<input name="flt_fio" type="text" placeholder="фио" value="'.($grst?$grst:'').'" style="border: 1px solid #ccc;  width: 100px;" onkeypress="var x;  if(window.event) {x=event.keyCode;}  else if(event.which) {x=event.which;}  if(x == 13) submit();">');
    b('<input id="flt_fio" type="text" placeholder="фио" style="border: 1px solid #ccc;  width: 160px;">');
    b('</div>');
      // ---- end: submenu ---- //



    b('<p class="h1">');
    if ($gid==1)  b('Люди');
    else          b(fiof($id['surname'], $id['name'], $id['otchestvo']));
    b(' <span class="f14">('.count($people).')</span>');
    b('</p>');
    b();


    b('<div id="people_list">');
    }

  if ($people) {
    $icona = array();
    $icona[] = 'user-green--pencil';
    $icona[] = 'home--plus';
    $icona[] = 'telephone--plus';
    $icona[] = 'users--plus';
    $icona[] = 'user-green--chain';
    $icona[] = 'table';
    $icw = count($icona) *18;
    $icv = count($icona) *18;

    css_table(array(
      //'#' => array(),
      0    => array('f7',  160, 60,  280, 120, 152-$icv, $icw),
      1024 => array('f8',  190, 70,  320, 140, 242-$icv, $icw),
      1280 => array('f10', 230, 80,  400, 200, 226-$icv, $icw),
      1366 => array('f10', 230, 80,  400, 200, 404-$icv, $icw),
      1600 => array('f10', 230, 80,  400, 200, 608-$icv, $icw),
      1920 => array('f12', 280, 100, 640, 240, 612-$icv, $icw),
      2560 => array('f12', 280, 100, 640, 240, 612-$icv, $icw),
      ));
    icona($icona);

    b('<table class="lst">');  // id="people_table"

    b('<tr>');
    b('<td>Ф.И.О.');
    b('<td>Дата рожд.');
    b('<td>Адрес, Телефон, ВК, ОК, ФБ');
    //b('<td>Глуб. знаком.');
    //b('<td>Симпатия');
    b('<td>Степень отношений');
    b('<td id="misc">Откуда знаю');
    b('<td>Д.');

    $chart = array();
    foreach ($people as $k=>$v) {

      b('<tr>');

      b('<td id="'.$k.'">');
      if (p('edit'))  b('<a href="/'.$mod.'/?id='.$k.'">');
      b(fiof($v['surname'], $v['name'], $v['otchestvo'], $v['surnamef'], $v['nickname']));
      if (p('edit'))  b('</a>');


      b('<td>');
      //if (p('edit'))  b('<a href="/'.$mod.'/ppe/?id='.$gid.'&ppl='.$k.'">');
      b(dateh($v['birthdate']));
      if ($v['deathdate'] != '0000-00-00')  b('<br>'.dateh($v['deathdate']));
      //if (p('edit'))  b('</a>');


      b('<td>');
      $tmp = array();

      //$addr = $v['addr'];
      //if ($v['lat']) {
      //  if (!$addr)  $addr = '@';
      //  // http://www.openstreetmap.org/?mlat=51.34187&mlon=37.89237#map=17/51.34187/37.89237
      //  //$addr = '<a href="https://href.li/?http://www.openstreetmap.org/?mlat='.geoaf($v['lat']).'&mlon='.geoof($v['lon']).'#map=17/'.geoaf($v['lat']).'/'.geoof($v['lon']).'" target="_blank">'.$addr.'</a>';
      //  $addr = '<a style="color: orange;" href="/'.$mod.'/mpv/?ppl='.$k.'" target="_blank">'.$addr.'</a>';
      //  }
      //if ($addr)  $tmp[] = '<span style="color: magenta;">'.$addr.'</span>';

      if (isset($people_addr[$k])) {
        foreach ($people_addr[$k] as $kk=>$vv) {
          $addr = '<a href="/'.$mod.'/ade/?id='.$gid.'&adr='.$kk.'">'.($vv['addr'] ? $vv['addr'] : '#').'</a>';
          if ($vv['lat'])  $addr .= ' <a href="/'.$mod.'/mpv/?adr='.$kk.'" target="_blank">@</a>';
          $tmp[] = $addr;
          }
        }

      //if ($v['phone'])  $tmp[] = '<span style="color: red;">'.$v['phone'].'</span>';
      if (isset($people_phone[$k])) {
        foreach ($people_phone[$k] as $kk=>$vv)  $tmp[] = '<a href="/'.$mod.'/phe/?id='.$gid.'&phn='.$kk.'">'.phoned($vv['num']).($vv['desc'] ? ' ('.$vv['desc'].')' : '').'</a>';
        }

      if ($v['vk'])  $tmp[] = 'vk: '.'<a href="https://href.li/?http://vk.com/id'.$v['vk'].'" target="_blank">'.$v['vk'].'</a>';
      if ($v['ok'])  $tmp[] = 'ok: '.'<a href="https://href.li/?http://ok.ru/profile/'.$v['ok'].'" target="_blank">'.$v['ok'].'</a>';
      if ($v['fb'])  $tmp[] = 'fb: '.'<a href="https://href.li/?https://www.facebook.com/profile.php?id='.$v['fb'].'" target="_blank">'.$v['fb'].'</a>';
      if ($tmp)  b(implode(', ', $tmp));


      //b('<td>');
      //if (isset($people_phone[$k])) {
      //  $tmp = array();
      //  foreach ($people_phone[$k] as $kk=>$vv) {
      //    $tmp[] = $vv['num'];
      //    }
      //  b(implode(', ', $tmp));
      //  }


      //b('<td>');
      //if (isset($people_junc[$k])) {
      //  //b($db_people_know[$people_junc[$k]['know']]);
      //  //$w1 = round($people_junc[$k]['know'],1);
      //  $w1 = $people_junc[$k]['know'];
      //  $w2 = 100 - $w1;
      //
      //  b('<div style="height: 8px;  border-radius: 5px;  overflow: hidden;">');  //   border: 1px solid #888;  
      //  b('<div style="display: inline-block;  height: 100%; width:'.$w2.'%;  vertical-align: top;  background-color: #8f8;  box-shadow: inset #fff 1px 2px 4px -1px, inset #000 -1px -2px 6px -2px, #000 0 0 6px -2px;"></div>');   // background: linear-gradient(to bottom, #f2f6f8 0%, #d8e1e7 50%, #b5c6d0 51%, #e0eff9 100%);
      //  b('<div style="display: inline-block;  height: 100%; width:'.$w1.'%;  vertical-align: top;  background-color: #faa;  box-shadow: inset #fff 1px 2px 4px -1px, inset #000 -1px -2px 6px -2px, #000 0 0 6px -2px;"></div>');
      //  b('</div>');
      //  }


      //b('<td>');
      ////if (isset($people_junc[$k]))  b($db_people_symp[$people_junc[$k]['symp']]);
      //if (isset($people_junc[$k])) {
      //  $w1 = $people_junc[$k]['symp'];
      //  $w2 = 100 - $w1;
      //
      //  b('<div style="height: 8px;  border: 1px solid #888;  border-radius: 5px;  overflow: hidden;">');
      //  b('<div style="display: inline-block;  height: 100%; width:'.$w2.'%;  vertical-align: top;  background-color: #8f8;"></div>');
      //  b('<div style="display: inline-block;  height: 100%; width:'.$w1.'%;  vertical-align: top;  background-color: #faa;"></div>');
      //  b('</div>');
      //  }


      b('<td>');
      if (isset($people_junc[$k]))  b($db_people_rel[$people_junc[$k]['rel']]);


      //b('<td onclick="$.tdl(this)">');
      b('<td>');
      $tmp = array();

      if (isset($people_group[$k])) {
        foreach ($people_group[$k] as $kk=>$vv) {
          $tmp[] = '<a href="/'.$mod.'/gpe/?id='.$gid.'&gpp='.$kk.'">'.$pgroup[$vv['group']]['desc'].'</a>';
          }
        }

      if (isset($people_junc[$k]) && $people_junc[$k]['misc'])  $tmp[] = $people_junc[$k]['misc'];
      if ($tmp)  b(implode(', ', $tmp));


      b('<td>');
      if (p('edit'))  b(icona('/'.$mod.'/ppe/?id='.$gid.'&ppl='.$k));
      if (p('edit'))  b(icona('/'.$mod.'/ade/?id='.$gid.'&ppl='.$k));
      if (p('edit'))  b(icona('/'.$mod.'/phe/?id='.$gid.'&ppl='.$k));
      if (p('edit'))  b(icona('/'.$mod.'/gpe/?id='.$gid.'&ppl='.$k));
      if (p('edit'))  b(icona('/'.$mod.'/jne/?id='.$gid.'&ppl='.$k));
      if (p('edit'))  b(icona('!/'.$mod.'/pgr/?ppl='.$k));
      }

    b('</table>');


  //  b('<script>');
  //
  //  b('
  //$.context("people_table", {
  //
  //menu: {
  //
  //  misc: [
  //
  //      {
  //      desc: "Связь",
  //      href: "/'.$mod.'/jne/?id='.$gid.'"
  //      //,blank: true
  //      }
  //
  //    ]  // end: phone
  //
  //  }  // end: context
  //
  //});
  //');
  //
  //  b('</script>');
    }

  //else {
  //  b('<p class="p">Ошибка: данные отсутствуют.');
  //  }

  if (!$ajax) {
    b('</div>');  // people_list
    b('<script>
  $.tdl = function(e) {
    if (e.childNodes.length && e.firstChild.tagName == "A") {
      location = e.firstChild.href;
      }
    }

  $.event("flt_fio", "keyup", $.delay(function() {$.ajax("/'.$mod.'/a/?id='.$gid.'", function(r) {$.id("people_list").innerHTML = r}, {post: {fio: $.id("flt_fio").value}} ) }, 0.5) );
  </script>');
    }
  }






  // ---------------------------------------------------- add / edit  people --------------------------------------------------------- //



  // -------------------------- add / edit -------------------------- //

if ($act == 'ppe' && p('edit') ) {

  $people = array(
    'surname' => '',
    'name' => '',
    'otchestvo' => '',
    'surnamef' => '',
    'nickname' => '',
    'birthdate' => '0000-00-00',
    'deathdate' => '0000-00-00',
    'vk' => '',
    'ok' => '',
    'fb' => '',
    );

  if ($gppl) {
    $col = array();
    foreach ($people as $k=>$v)  $col[] = $k;

    $people = db_read(array(
      'table' => 'people',
      'col' => $col,
      'where' => '`id` = '.$gppl,
      ));
    }


    // ---- submenu ---- //
  if (p() && $gppl)  $submenu['?Удалить;minus-button'] = array('#Подтвердить;tick-button' => form_sbd('/'.$mod.'/ppu/?id='.$gid.'ppl='.$gppl));
  submenu();
    // ---- end: submenu ---- //




  b('<p class="h1">');
  if (!$gppl)  b('Добавление');
  else         b('Редактирование');
  b('</p>');
  b();
  b();


  b(form('people', '/'.$mod.'/ppu/', array(
    'id='.$gid,
    $gppl ? 'ppl='.$gppl : '',
    )));

  b('<table class="edt">');


  b('<tr><td>');
  b('Фамилия, имя, отчество:');
  b('<td>');
  b(form_t('@f_people_surname,surname', $people['surname'], 100));
  b(' '.form_t('f_people_name,name', $people['name'], 100));
  b(' '.form_t('f_people_otchestvo', $people['otchestvo'], 100));


  b('<tr><td>');
  b('Старая фамилия, никнейм:');
  b('<td>');
  b(form_t('f_people_surnamef', $people['surnamef'], 100));
  b(' '.form_t('f_people_nickname', $people['nickname'], 100));


  b('<tr><td>');
  b('Дата рождения:');
  b('<td>');
  b(form_dt(array('!f_people_date_y;1930', '!f_people_date_m', '!f_people_date_d'),  $people['birthdate'] ));


  b('<tr><td>');
  b('Дата смерти:');
  b('<td>');
  b(form_dt(array('!f_people_ddate_y;1930', '!f_people_ddate_m', '!f_people_ddate_d'),  $people['deathdate'] ));


  //b('<tr><td>');
  //b('Адрес:');
  //b('<td>');
  //b(form_t('f_people_addr', $people['addr'], 500));
  //b(' '.form_t('f_lat,lat', ($people['lat'] ? geoaf($people['lat']) : ''), 90));
  //b(' '.form_t('f_lon,lon', ($people['lon'] ? geoof($people['lon']) : ''), 90));
  //b(' <input type="button" style="width: 20px; height: 20px; padding: 0;" value="O" onclick="window.open(\'/people/mpc/\');">');


  //b('<tr><td>');
  //b('Телефон:');
  //b('<td>');
  //b(form_t('f_people_phone', $people['phone'], 500));


  b('<tr><td>');
  b('ВК:');
  b('<td>');
  b(form_t('f_people_vk', $people['vk'], 300));


  b('<tr><td>');
  b('ОК:');
  b('<td>');
  b(form_t('f_people_ok', $people['ok'], 300));


  b('<tr><td>');
  b('ФБ:');
  b('<td>');
  b(form_t('f_people_fb', $people['fb'], 300));


  b('</table>');


  b(form_sb());

  b('</form>');

  if (!$gppl) {
  b('<script>
$.event("surname", "keyup", $.delay(function() {if ($.id("surname").value && $.id("name").value)  $.ajax("/'.$mod.'/cf/", function(r) { $.id("surname").style.backgroundColor = $.id("name").style.backgroundColor = (r ? "#fcc" : "#cfc")  }, {post:{surname:$.id("surname").value, name:$.id("name").value}} ) }, 1));
$.event("name", "keyup", $.delay(function() {if ($.id("surname").value && $.id("name").value)  $.ajax("/'.$mod.'/cf/", function(r) { $.id("surname").style.backgroundColor = $.id("name").style.backgroundColor = (r ? "#fcc" : "#cfc")  }, {post:{surname:$.id("surname").value, name:$.id("name").value}} ) }, 1));
</script>');
    }
  }




  // ------------------------------------------- ajax: update ------------------------------------------------ //

if ($act == 'ppu' && p('edit') ) {
  $ajax = TRUE;

  $post = postb('f_people_surname');

  $table = 'people';
  $where = '`id` = '.$gppl;


  if ($post) {
    $set = array();
    $set['surname'] = post('f_people_surname');
    $set['name'] = post('f_people_name');
    $set['otchestvo'] = post('f_people_otchestvo');
    $set['surnamef'] = post('f_people_surnamef');
    $set['nickname'] = post('f_people_nickname');
    $set['birthdate'] = datesql(postn('f_people_date_y'), postn('f_people_date_m'), postn('f_people_date_d'));  // , postn('f_people_date_h'), postn('f_people_date_i'), postn('f_people_date_s')
    $set['deathdate'] = datesql(postn('f_people_ddate_y'), postn('f_people_ddate_m'), postn('f_people_ddate_d'));
    //$set['addr'] = post('f_people_addr');
    ////$geo = geoi(post('f_people_lat'), post('f_people_lon'));
    //$set['lat'] = (post('f_lat') ? geoai(post('f_lat')) : 0);
    //$set['lon'] = (post('f_lon') ? geooi(post('f_lon')) : 0);
    //$set['phone'] = post('f_people_phone');
    $set['vk'] = post('f_people_vk');
    $set['ok'] = post('f_people_ok');
    $set['fb'] = post('f_people_fb');

    if ($gppl) {
      db_write(array('table'=>$table, 'set'=>$set, 'where'=>$where));
      b('/'.$mod.'/?id='.$gid);
      }

    else {
      $gppl = db_write(array('table'=>$table, 'set'=>$set));
      b('/'.$mod.'/jne/?id='.$gid.'&ppl='.$gppl);
      }

    }


    // ---- deletion ---- //
  elseif (!$post && $gppl && p()) {
    $result = db_write(array('table'=>$table, 'where'=>$where));

    b('/'.$mod.'/?id='.$gid);
    }  // end: delete

  }




  // ------------------------------------------- junction ------------------------------------------------ //

if ($act == 'jne' && p('edit')) {

  if (!$gppl && $grow)  $gppl = $grow;

  if (!$gjnc && $gppl) {
    $gjnc = (int)db_read(array(
      'table' => 'people_junc',
      'col' => 'id',
      'where' => array(
        '`f` = '.$gid,
        '`t` = '.$gppl,
        ),
      ));
    }


  $junc = db_read(array(
    'table' => 'people_junc',
    'col' => array('id', 'f', 't', 'know', 'rel', 'symp', 'misc'),
    'where' => '`id` = '.$gjnc,
    ));

  if ($junc) {
    $gjnc = $junc['id'];
    }

  else {
    $junc = array(
      'id' => 0,
      'f' => $gid,
      't' => $gppl,
      'know' => 50,
      'rel' => 0,
      'symp' => 60,
      'misc' => '',
      );
    }



    // ---- submenu ---- //
  if (p() && $gjnc)  $submenu['?Удалить;minus-button'] = array('#Подтвердить;tick-button' => form_sbd('/'.$mod.'/jnu/?id='.$gid.'&jnc='.$gjnc));
  submenu();
    // ---- end: submenu ---- //




  b('<p class="h1">');
  if (!$gjnc)  b('Добавление');
  else         b('Редактирование');
  b('</p>');
  b();
  b();


  b(form('junc', '/'.$mod.'/jnu/', array(
    'id='.$gid,
    $gjnc ? 'jnc='.$gjnc : '',
    )));

  b('<table class="edt">');
  b('<tr><td>');

  b('From, to:');
  b('<td>');
  //--b('<input name="f_junc_f" type="text" size="4" value="'.$junc['f'].'">');
  //b(' <input id="t" name="f_junc_t" type="text" size="4" value="'.$junc['t'].'">');
  b(form_t('f_junc_t,t', $junc['t'], 40));
  if (!$gppl)  b(' <input id="tsearch" name="f_junc_t_search" type="text" size="30" value="" autocomplete="off"'.(!$gppl ? ' autofocus' : '').'>');


  b('<tr><td>');
  b('Глубина знакомства:');
  b('<td>');
  b('<select id="know" name="f_junc_know"'.($gppl ? ' autofocus' : '').'>');
  foreach ($db_people_know as $k=>$v)  if($v)  b('<option value="'.$k.'"'.(($k == $junc['know'])?' selected':'').'>'.$v);
  b('</select>');
  //b(form_s('f_junc_know', $db_people_know, $junc['know']));


  b('<tr><td>');
  b('Степень отношений:');
  b('<td>');
  b('<select name="f_junc_rel">');
  foreach ($db_people_rel as $k=>$v)  if($v)  b('<option value="'.$k.'"'.(($k == $junc['rel'])?' selected':'').'>'.$v);
  b('</select>');
  //b(form_s('f_junc_rel', $db_people_rel, $junc['rel']));


  b('<tr><td>');
  b('Симпатия:');
  b('<td>');
  b('<select name="f_junc_symp">');
  foreach ($db_people_symp as $k=>$v)  if($v)  b('<option value="'.$k.'"'.(($k == $junc['symp'])?' selected':'').'>'.$v);
  b('</select>');
  //b(form_s('f_junc_symp', $db_people_symp, $junc['symp']));


  b('<tr><td>');
  b('Примечания:');
  b('<td>');
  //b('<input name="f_junc_misc" type="text" size="90" value="'.$junc['misc'].'">');
  b(form_t('f_junc_misc', $junc['misc'], 300));


  b('</table>');


  b(form_sb());

  b('</form>');


  if (!$gppl) {
  b('<script>
$.event("tsearch", "keyup", 
  $.delay(
    function() {
      $.ajax(
        "/'.$mod.'/ts/", suggest_r,
        {
          post:{tsearch:$.id("tsearch").value}
          }
        )
      }, 0.5
    )
  );



var input = $.id("tsearch");

var br = document.createElement("BR");
var select = document.createElement("SELECT");
select.style.display = "none";
//select.size = "6";
select.style.width = input.offsetWidth + 6 + "px";

select.style.position = "absolute";
select.tabIndex = "999";

input.parentNode.appendChild(br);
input.parentNode.appendChild(select);



function  suggest_r(r) {
  while (select.length)  select.remove(0);

  if (r) {
    r = r.split("\n");

    for (i in r) {
      var r_e = r[i].split("\t");
      var option = document.createElement("OPTION");
      option.value = r_e[0];
      option.text = r_e[1];
      option.selected = false;
      select.add(option, null);
      }

    select.size = ((r.length > 12) ? 12 : r.length);
    select.style.display = "block";
    //if (popup_len > 1)  {select.selectedIndex = "999";}
    }

  else {
    select.style.display = "none";
    }

  }


$.event(input, "keydown", function(e) {
  if (select.style.display == "block" && e.keyCode == 40) {
    //e.stopPropagation();
    e.preventDefault();

    select.selectedIndex = "0";
    select.focus();
    }
  });


$.event(select, "keydown", function(e) {
  if (e.keyCode == 13) {
    e.preventDefault();
    $.id("t").value = e.target.value;
    $.id("know").focus();  // next element
    select.style.display = "none";

    //return  false;
    }

    // ---- return to parent field ---- //
  if (e.keyCode == 38 && select.selectedIndex == 0) {
    e.preventDefault();
    input.focus();
    select.style.display = "none";
    // todo: remove reemit of popup after focus
    //return  false;
    }

  });




</script>');
    }

  }




  // ------------------------------------------- update ------------------------------------------------ //

if ($act == 'jnu' && p('edit') ) {
  $ajax = TRUE;

  $post = postb('f_junc_know');

  $table = 'people_junc';
  $where = '`id` = '.$gjnc;


  if ($post) {
    $set = array();
    $set['f'] = $gid;
    $set['t'] = postn('f_junc_t');
    $set['know'] = postn('f_junc_know');
    $set['rel'] = postn('f_junc_rel');
    $set['symp'] = postn('f_junc_symp');
    $set['misc'] = post('f_junc_misc');

    if ($gjnc) {
      db_write(array('table'=>$table, 'set'=>$set, 'where'=>$where));
      }

    else {
      $gjnc = db_write(array('table'=>$table, 'set'=>$set));
      }

    b('/'.$mod.'/?id='.$gid);
    }


    // ---- deletion ---- //
  if (!$post && $gjnc && p()) {
    $result = db_write(array('table'=>$table, 'where'=>$where));

    b('/'.$mod.'/?id='.$gid);
    }  // end: delete

  }




  // -------------------------------- ajax: check fio -------------------------------- //

if ($act == 'cf') {
  $ajax = TRUE;

  $psurname = filter_rlns(post('surname'));
  $pname = filter_rlns(post('name'));

  $people = db_read(array(
    'table' => 'people',
    'col' => 'id',
    'where' => array(
      '`surname` = \''.$psurname.'\'',
      '`name` = \''.$pname.'\'',
      ),
    ));
  if ($people)  b('1');
  }




  // -------------------------------- ajax: to search -------------------------------- //

if ($act == 'ts') {
  $ajax = TRUE;

  $gsch = filter_rlns(post('tsearch'));

  $gsch = strtr(mb_strtolower($gsch), array('a'=>'ф','b'=>'и','c'=>'с','d'=>'в','e'=>'у','f'=>'а','g'=>'п','h'=>'р','i'=>'ш','j'=>'о','k'=>'л','l'=>'д','m'=>'ь','n'=>'т','o'=>'щ','p'=>'з','q'=>'й','r'=>'к','s'=>'ы','t'=>'е','u'=>'г','v'=>'м','w'=>'ц','x'=>'ч','y'=>'н','z'=>'я','`'=>'ё','['=>'х',']'=>'ъ',','=>'б','.'=>'ю',';'=>'ж','\''=>'э'));


  if ($gsch) {
    while(($pos = strpos($gsch, '  ')) !== FALSE) {
      $gsch = substr($gsch,0,$pos).substr($gsch,$pos+1);
      }

    $gsch = explode(' ', $gsch);
    $where = array('(`surname` LIKE \''.((mb_strlen($gsch[0]) > 3)?'%':'').$gsch[0].'%\' OR `surnamef` LIKE \''.((mb_strlen($gsch[0]) > 3)?'%':'').$gsch[0].'%\' OR `nickname` LIKE \''.((mb_strlen($gsch[0]) > 3)?'%':'').$gsch[0].'%\')');
    if (isset($gsch[1]))  $where[] = '`name` LIKE \''.$gsch[1].'%\'';
    if (isset($gsch[2]))  $where[] = '`otchestvo` LIKE \''.$gsch[2].'%\'';

    $people = db_read(array(
      'table' => 'people',
      'col' => array('id', 'surname', 'name', 'otchestvo'),
      'where' => $where,
      'order' => array('`surname`', '`name`', '`otchestvo`'),
      'limit' => '100',

      'key' => 'id',
      ));


      // -------------------------------- output -------------------------------- //

    if ($people) {
      $tmp = array();
      foreach ($people as $k=>$v) {
        $tmp[] = $k."\t".fiof($v['surname'], $v['name'], $v['otchestvo']);
        }
      b(implode("\n", $tmp));
      }
    }
  } // end: act==ts




  // ---------------------------------------------------- add / edit  addr --------------------------------------------------------- //

  // -------------------------- add / edit -------------------------- //

if ($act == 'ade' && p('edit') ) {

  $addr = array(
    'pid' => 0,
    'addr' => '',
    'lat' => 0,
    'lon' => 0,
    );

  if ($gadr) {
    $col = array();
    foreach ($addr as $k=>$v)  $col[] = $k;

    $addr = db_read(array(
      'table' => 'people_addr',
      'col' => $col,
      'where' => '`id` = '.$gadr,
      ));
    }


    // ---- submenu ---- //
  if (p() && $gadr)  $submenu['?Удалить;minus-button'] = array('#Подтвердить;tick-button' => form_sbd('/'.$mod.'/adu/?adr='.$gadr));
  submenu();
    // ---- end: submenu ---- //




  b('<p class="h1">');
  if (!$gadr)  b('Добавление');
  else         b('Редактирование');
  b('</p>');
  b();


  b(form('addr', '/'.$mod.'/adu/', array(
    $gadr ? 'adr='.$gadr : '',
    $gppl ? 'ppl='.$gppl : '',
    )));

  b('<table class="edt">');


  //b('<tr><td>');
  //b('Дата, время:');
  //b('<td>');
  //b(form_dt(array('f_addr_date_y;2000', 'f_addr_date_m', 'f_addr_date_d', 'f_addr_date_h', 'f_addr_date_i', 'f_addr_date_s'),  $addr['dt'] ));


  b('<tr><td>');
  b('Адрес:');
  b('<td>');
  b(form_t('@f_addr', $addr['addr'], 500));
  b(' '.form_t('f_lat,lat', ($addr['lat'] ? geoaf($addr['lat']) : ''), 90));
  b(' '.form_t('f_lon,lon', ($addr['lon'] ? geoof($addr['lon']) : ''), 90));
  b(' <input type="button" style="width: 20px; height: 20px; padding: 0;" value="O" onclick="window.open(\'/people/mpc/\');">');


  b('</table>');


  b(form_sb());
  b('</form>');
  }




  // ------------------------------------------- ajax: update ------------------------------------------------ //

if ($act == 'adu' && p('edit') ) {
  $ajax = TRUE;

  $post = postb('f_addr');

  $table = 'people_addr';
  $where = '`id` = '.$gadr;


  if ($post) {
    $set = array();
    $set['addr'] = post('f_addr');
    $set['lat'] = (post('f_lat') ? geoai(post('f_lat')) : 0);
    $set['lon'] = (post('f_lon') ? geooi(post('f_lon')) : 0);

    if ($gadr) {
      db_write(array('table'=>$table, 'set'=>$set, 'where'=>$where));
      }

    elseif ($gppl) {
      $set['pid'] = $gppl;
      $gadr = db_write(array('table'=>$table, 'set'=>$set));
      }

    b('/'.$mod.'/');
    }


    // ---- deletion ---- //
  if (!$post && $gadr && p()) {
    $result = db_write(array('table'=>$table, 'where'=>$where));

    b('/'.$mod.'/');

    //http_response_code(418);
    //b('failed');
    }  // end: delete

  }




  // ---------------------------------------------------- add / edit  phone --------------------------------------------------------- //

  // -------------------------- add / edit -------------------------- //

if ($act == 'phe' && p('edit') ) {

  $phone = array(
    'pid' => 0,
    'num' => '',
    'desc' => '',
    );

  if ($gphn) {
    $col = array();
    foreach ($phone as $k=>$v)  $col[] = $k;

    $phone = db_read(array(
      'table' => 'phone',
      'col' => $col,
      'where' => '`id` = '.$gphn,
      ));
    }


    // ---- submenu ---- //
  if (p() && $gphn)  $submenu['?Удалить;minus-button'] = array('#Подтвердить;tick-button' => form_sbd('/'.$mod.'/phu/?phn='.$gphn));
  submenu();
    // ---- end: submenu ---- //




  b('<p class="h1">');
  if (!$gphn)  b('Добавление');
  else         b('Редактирование');
  b('</p>');
  b();


  b(form('phone', '/'.$mod.'/phu/', array(
    $gphn ? 'phn='.$gphn : '',
    $gppl ? 'ppl='.$gppl : '',
    )));

  b('<table class="edt">');


  //b('<tr><td>');
  //b('Дата, время:');
  //b('<td>');
  //b(form_dt(array('f_phone_date_y;2000', 'f_phone_date_m', 'f_phone_date_d', 'f_phone_date_h', 'f_phone_date_i', 'f_phone_date_s'),  $phone['dt'] ));


  b('<tr><td>');
  b('Номер:');
  b('<td>');
  b(form_t('@f_phone_num', $phone['num'], 80));


  b('<tr><td>');
  b('Описание:');
  b('<td>');
  b(form_t('f_phone_desc', $phone['desc'], 300));


  b('</table>');


  b(form_sb());
  b('</form>');
  }




  // ------------------------------------------- ajax: update ------------------------------------------------ //

if ($act == 'phu' && p('edit') ) {
  $ajax = TRUE;

  $post = postb('f_phone_num');

  $table = 'phone';
  $where = '`id` = '.$gphn;


  if ($post) {
    $set = array();
    $set['num'] = post('f_phone_num');
    $set['desc'] = post('f_phone_desc');

    if ($gphn) {
      db_write(array('table'=>$table, 'set'=>$set, 'where'=>$where));
      }

    elseif ($gppl) {
      $set['pid'] = $gppl;
      $gphn = db_write(array('table'=>$table, 'set'=>$set));
      }

    b('/'.$mod.'/');
    }


    // ---- deletion ---- //
  if (!$post && $gphn && p()) {
    $result = db_write(array('table'=>$table, 'where'=>$where));

    b('/'.$mod.'/');

    //http_response_code(418);
    //b('failed');
    }  // end: delete

  }




  // ------------------------------------------------------------------------------------------------------------------------------------------------- //
  // ------------------------------------------------------------------ group ----------------------------------------------------------------------- //
  // ------------------------------------------------------------------------------------------------------------------------------------------------- //

if ($act == 'grp') {

  $pgroup = db_read(array(
    'table' => 'pgroup',
    'col' => array('id', 'desc'),
    'key' => 'id',
    ));


    // ---- submenu ---- //
  $submenu['Добавить группу'] = '/'.$mod.'/gre/';
  submenu();
    // ---- end: submenu ---- //




  b('<p class="h1">Группы</p>');
  b();


  if ($pgroup) {
    $icona = array();
    $icona[] = 'pencil-button';
    $icw = count($icona) *18;
    //$icv = count($icona) *18;

    css_table(array(24, 200, $icw));
    icona($icona);


    b('<table class="lst">');
    b('<tr>');
    b('<td>id');
    b('<td>desc');
    b('<td>Д.');

    foreach ($pgroup as $k=>$v) {
      b('<tr>');

      b('<td>');
      b($k);


      b('<td>');
      b($v['desc']);


      b('<td>');
      b(icona('/'.$mod.'/gre/?grp='.$k));
      }

    b('</table>');
    }

  else {
    b('<p class="p">Ошибка: данные отсутствуют.');
    }

  }




  // ---------------------------------------------------- pgroup add / edit --------------------------------------------------------- //

if ($act == 'gre' && p('edit') ) {

  $pgroup = array(
    'id' => 0,
    'desc' => '',
    );

  if ($ggrp) {
    $col = array();
    foreach ($pgroup as $k=>$v)  $col[] = $k;

    $pgroup = db_read(array(
      'table' => 'pgroup',
      'col' => $col,
      'where' => '`id` = '.$ggrp,
      ));
    }


    // ---- submenu ---- //
  if (p() && $ggrp)  $submenu['?Удалить;minus-button'] = array('#Подтвердить;tick-button' => form_sbd('/'.$mod.'/gru/?grp='.$ggrp));
  submenu();
    // ---- end: submenu ---- //




  b('<p class="h1">');
  if (!$ggrp)  b('Добавление');
  else         b('Редактирование');
  b('</p>');
  b();
  b();


  b(form('pgroup', '/'.$mod.'/gru/', array(
    $ggrp ? 'grp='.$ggrp : '',
    )));

  b('<table class="edt">');

  //b('<tr><td>');
  //b('Вставить после:');
  //b('<td>');
  //b(form_s('f_pgr_tp', $ppgroup, $pgroup['tp']));


  b('<tr><td>');
  b('Описание:');
  b('<td>');
  b(form_t('@f_grp_desc', $pgroup['desc'], 150));


  b('</table>');


  b(form_sb());
  b('</form>');
  }




  // ------------------------------------------- ajax: pgroup update ------------------------------------------------ //

if ($act == 'gru' && p('edit') ) {
  $ajax = TRUE;
  //http_response_code(418);

  $post = postb('f_grp_desc');

  $table = 'pgroup';
  $where = '`id` = '.$ggrp;

  if ($post) {
    $set = array();
    $set['desc'] = post('f_grp_desc');

    if ($ggrp) {
      db_write(array('table'=>$table, 'set'=>$set, 'where'=>$where));
      }

    else {
      $ggrp = db_write(array('table'=>$table, 'set'=>$set));
      }

    b('/'.$mod.'/grp/');
    }


    // ---- deletion ---- //
  if (!$post && $ggrp && p()) {
    $result = db_write(array('table'=>$table, 'where'=>$where));

    b('/'.$mod.'/grp/');
    }  // end: delete

  }




  // ---------------------------------------------------- people_group --------------------------------------------------------- //

  // -------------------------- add / edit -------------------------- //

if ($act == 'gpe' && p('edit') ) {

  $pgroup = db_read(array(
    'table' => 'pgroup',
    'col' => array('id', 'desc'),
    'key' => 'id',
    ));

  $people_group = array(
    'id' => 0,
    'people' => 0,
    'group' => 0,
    );

  if ($ggpp) {
    $col = array();
    foreach ($people_group as $k=>$v)  $col[] = $k;

    $people_group = db_read(array(
      'table' => 'people_group',
      'col' => $col,
      'where' => '`id` = '.$ggpp,
      ));
    }

  $people = db_read(array(
    'table' => 'people',
    'col' => array('id', 'surname', 'name', 'otchestvo'),
    'where' => '`id` = '.($gppl ? $gppl : $people_group['people']),
    ));


    // ---- submenu ---- //
  if (p() && $ggpp)  $submenu['?Удалить;minus-button'] = array('#Подтвердить;tick-button' => form_sbd('/'.$mod.'/gpu/?gpp='.$ggpp));
  submenu();
    // ---- end: submenu ---- //




  b('<p class="h1">');
  if (!$ggpp)  b('Добавление в группу');
  else         b('Редактирование');
  b(' – '.fiof($people['surname'], $people['name'], $people['otchestvo']));
  b('</p>');
  b();


  b(form('people_group', '/'.$mod.'/gpu/', array(
    $ggpp ? 'gpp='.$ggpp : '',
    $gppl ? 'ppl='.$gppl : '',
    )));

  b('<table class="edt">');


  b('<tr><td>');
  b('Группа:');
  b('<td>');
  b(form_s('@f_group;desc', $pgroup, $people_group['group']));


  b('</table>');


  b(form_sb());
  b('</form>');
  }




  // ------------------------------------------- ajax: update ------------------------------------------------ //

if ($act == 'gpu' && p('edit') ) {
  $ajax = TRUE;
  //http_response_code(418);

  $post = postb('f_group');

  $table = 'people_group';
  $where = '`id` = '.$ggpp;


  if ($post) {
    $set = array();
    $set['group'] = postn('f_group');

    if ($ggpp) {
      db_write(array('table'=>$table, 'set'=>$set, 'where'=>$where));
      }

    elseif ($gppl) {
      $set['people'] = $gppl;
      $ggpp = db_write(array('table'=>$table, 'set'=>$set));
      }

    b('/'.$mod.'/');
    }


    // ---- deletion ---- //
  if (!$post && $ggpp && p()) {
    $result = db_write(array('table'=>$table, 'where'=>$where));

    b('/'.$mod.'/');
    }  // end: delete

  }






  // -------------------------------------------------------------------------------------------------------------------- //
  // -------------------------------------------------- other ----------------------------------------------------------- //
  // -------------------------------------------------------------------------------------------------------------------- //


  // ---------------------------------------------------------------- graph weekly ---------------------------------------------------------------- //

if ($act == 'pgr') {

  for ($i = 2008; $i <= $curr['year']; $i++) {
    b('<p class="h2">'.$i);
    b('<div><img src="/'.$mod.'/grw/?ppl='.$gppl.'&year='.$i.'"></div>');
    }
  }


if ($act == 'grw') {
  $ajax = TRUE;

  $visit = db_read(array('table' => 'visit',
                         'col' => array('id', 'dt',
                                        '!MONTH(`dt`) AS `dt_mon`', '!DAYOFMONTH(`dt`) AS `dt_day`',
                                        ),
                         'where' => array('`people` = '.$gppl,
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

  b('<!DOCTYPE html>'."\n\r".'<html><head>');

  b('<title>Просмотр точки на карте</title>');

  b('<meta charset="UTF-8">');
  b('<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">');
  b('<link rel="StyleSheet" type="text/css" href="/sh/leaflet.css">');
  b('<script type="text/javascript" src="/sh/leaflet.js"></script>');
  b('<script type="text/javascript" src="/j.js"></script>');
  b('<script>var mod = "'.$mod.'";</script>');
  b('</head><body style="padding:0; margin:0;">');

  $addr = db_read(array('table' => 'people_addr',
                        'col' => array('lat', 'lon'),
                        'where' => '`id` = '.$gadr,
                        ));

  b('<script>
var default_id = '.$gadr.';
var default_lat = '.geoaf($addr['lat']).';
var default_lon = '.geoof($addr['lon']).';
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


  $markers = db_read(array('table' => array('people', 'people_addr'),
                           'col' => array('people`.`surname', 'people`.`name', 'people`.`otchestvo',
                                          'people_addr`.`id', 'people_addr`.`pid', 'people_addr`.`lat', 'people_addr`.`lon',
                                          ),
                           'where' => array('`people`.`id` = `people_addr`.`pid`',
                                            '`people_addr`.`lat` < '.$pnlat,
                                            '`people_addr`.`lat` > '.$pslat,
                                            '`people_addr`.`lon` > '.$pwlon,
                                            '`people_addr`.`lon` < '.$pelon,
                                            ),
                           'limit' => 300,
                           'key' => 'id',
                           ));

  $obj = array();
  if ($markers) {
    foreach ($markers as $k=>$v) {
      $obj[$k] = array(
        'title' => fiof($v['surname'], $v['name'], $v['otchestvo']),
        'lat' => geoaf($v['lat']),
        'lon' => geoof($v['lon']),
        );
      }
    b(json_encode($obj));
    }

  }




  // -------------------------------- client: sync contacts people -------------------------------- //

if ($act == 'csyncp') {
  $ajax = TRUE;

  //$raw_post = file_get_contents("php://input");
  //fwrite (fopen ('m/'.$mod.'/debug/debug '.time(), 'wb'),  $raw_post);  clearstatcache();

  //$json = json_decode($raw_post, TRUE);
  //if (!isset($json['calls']))  die('error: wrong json');


  $people = db_read(array(
    'table' => 'people',
    'col' => array('id', 'surname', 'name', 'otchestvo'),
    //'where' => '`people`.`id` = `phone`.`pid`',
    //'order' => '`id` DESC',
    //'limit' => '20',
    'key' => 'id',
    ));
//d($people);

  $phone = db_read(array(
    'table' => array('people', 'phone'),
    'col' => array('phone`.`id', 'phone`.`pid', 'phone`.`num'),
    'where' => '`people`.`id` = `phone`.`pid`',
    'key' => array('pid', 'id'),
    ));
//d($phone);

  $contacts = array();
  foreach($people as $k=>$v) {
    if (isset($phone[$k])) {
      $tmp = $v;
      $tmp['phones'] = array();
      foreach($phone[$k] as $kk=>$vv) {
        unset($vv['pid']);
        if (strlen($vv['num']) == 10)  $vv['num'] = '+7'.$vv['num'];
        $tmp['phones'][] = $vv;
        }
      $contacts[] = $tmp;
      }
    }

    // ---------------- answer ---------------- //

  $json = array();

  if ($contacts) {
    $json['result'] = 'ok';
    $json['contacts'] = $contacts;
    }

  else {
    $json['result'] = 'error';
    $json['error'] = 'empty result';
    }

  b(json_encode($json));
  }


?>