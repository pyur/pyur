<?php

/************************************************************************/
/*  Люди  v1.oo                                                         */
/************************************************************************/


if (!isset($body))  die ('error: this is a pluggable module and cannot be used standalone.');

$gid = getn('id',1);
$gppl = getn('ppl');
//$gfce = getn('fce',1);
$gjnc = getn('jnc');

$gdate = gets('date', $curr['date']);
$gyear = gets('year', $curr['year']);


include 'm/'.$mod.'/const.php';






  // --------------------------------------- список -------------------------------------------- //

if ($act == 'a') {
  $ajax = TRUE;
  $act = '';
  }


if (!$act) {

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
                          'col' => array('people`.`id', 'people`.`surname', 'people`.`name', 'people`.`otchestvo', 'people`.`surnamef', 'people`.`nickname', 'people`.`birthdate', 'people`.`addr', 'people`.`phone', 'people`.`vk', 'people`.`ok', 'people`.`fb',
                                         'people`.`lat', 'people`.`lon',
                                         ),
                          'where' => $where,
                          'order' => array('`people`.`surname`', '`people`.`name`', '`people`.`otchestvo`'),
                          'key' => 'id',
                          ));


  $people_junc = db_read(array('table' => 'people_junc',
                               'col' => array('people_junc`.`id', 'people_junc`.`f', 'people_junc`.`t', 'people_junc`.`know', 'people_junc`.`rel', 'people_junc`.`symp', 'people_junc`.`misc'),
                               'where' => '`people_junc`.`f` = '.$gid,
                               'key' => 't',
                               ));

  //$people_junc_c = db_read(array('table' => 'people_junc',
  //                               'col' => array('people_junc`.`id', 'people_junc`.`f', 'people_junc`.`t', 'people_junc`.`know', 'people_junc`.`rel', 'people_junc`.`symp', 'people_junc`.`misc'),
  //                               'where' => '`people_junc`.`f` = '.$gid,
  //                               'key' => array('t', 'id'),
  //                               ));


  if (!$ajax) {
      // ---- submenu ---- //
    if (p('edit'))  $submenu['Добавить человека;user-green--plus'] = '/'.$mod.'/ppe/?id='.$gid;
    if (p('edit'))  $submenu['Редактировать;user-green--pencil'] = '/'.$mod.'/ppe/?id='.$gid.'&ppl='.$gid;
    //if (p('edit'))  $submenu['Добавить связь'] = '/'.$mod.'/jne/?id='.$gid;
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
    css_table(array(
      //'#' => array(),
      0    => array('f7', 160, 60, 160, 60, 60, 120, 86, 54),
      1024 => array('f8', 190, 70, 180, 70, 70, 140, 176, 54),
      1280 => array('f10', 230, 80, 200, 100, 100, 200, 162, 54),
      1366 => array('f10', 230, 80, 200, 100, 100, 200, 342, 54),
      1600 => array('f10', 230, 80, 200, 100, 100, 200, 542, 54),
      1920 => array('f12', 280, 100, 400, 120, 120, 240, 546, 54),
      2560 => array('f12', 280, 100, 400, 120, 120, 240, 546, 54),
      ));
    icona(array('user-green--pencil','user-green--chain','table'));

    b('<table class="lst">');  // id="people_table"

    b('<tr>');
    b('<td>Ф.И.О.');
    b('<td>Дата рожд.');
    b('<td>Адрес, Телефон, ВК, ОК, ФБ');
    b('<td>Глуб. знаком.');
    b('<td>Симпатия');
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
      //if (p('edit'))  b('</a>');


      b('<td>');
      $tmp = array();
      $addr = $v['addr'];
      if ($v['lat']) {
        if (!$addr)  $addr = '@';
        // http://www.openstreetmap.org/?mlat=51.34187&mlon=37.89237#map=17/51.34187/37.89237
        //$addr = '<a href="https://href.li/?http://www.openstreetmap.org/?mlat='.geoaf($v['lat']).'&mlon='.geoof($v['lon']).'#map=17/'.geoaf($v['lat']).'/'.geoof($v['lon']).'" target="_blank">'.$addr.'</a>';
        $addr = '<a href="/'.$mod.'/mpv/?ppl='.$k.'" target="_blank">'.$addr.'</a>';
        }
      if ($addr)  $tmp[] = $addr;
      if ($v['phone'])  $tmp[] = $v['phone'];
      if ($v['vk'])  $tmp[] = 'vk: '.'<a href="https://href.li/?http://vk.com/id'.$v['vk'].'" target="_blank">'.$v['vk'].'</a>';
      if ($v['ok'])  $tmp[] = 'ok: '.'<a href="https://href.li/?http://ok.ru/profile/'.$v['ok'].'" target="_blank">'.$v['ok'].'</a>';
      if ($v['fb'])  $tmp[] = 'fb: '.'<a href="https://href.li/?https://www.facebook.com/profile.php?id='.$v['fb'].'" target="_blank">'.$v['fb'].'</a>';
      if ($tmp)  b(implode(', ', $tmp));


      b('<td>');
      if (isset($people_junc[$k])) {
        //b($db_people_know[$people_junc[$k]['know']]);
        //$w1 = round($people_junc[$k]['know'],1);
        $w1 = $people_junc[$k]['know'];
        $w2 = 100 - $w1;

        b('<div style="height: 8px;  border-radius: 5px;  overflow: hidden;">');  //   border: 1px solid #888;  
        b('<div style="display: inline-block;  height: 100%; width:'.$w2.'%;  vertical-align: top;  background-color: #8f8;  box-shadow: inset #fff 1px 2px 4px -1px, inset #000 -1px -2px 6px -2px, #000 0 0 6px -2px;"></div>');   // background: linear-gradient(to bottom, #f2f6f8 0%, #d8e1e7 50%, #b5c6d0 51%, #e0eff9 100%);
        b('<div style="display: inline-block;  height: 100%; width:'.$w1.'%;  vertical-align: top;  background-color: #faa;  box-shadow: inset #fff 1px 2px 4px -1px, inset #000 -1px -2px 6px -2px, #000 0 0 6px -2px;"></div>');
        b('</div>');
        }


      b('<td>');
      //if (isset($people_junc[$k]))  b($db_people_symp[$people_junc[$k]['symp']]);
      if (isset($people_junc[$k])) {
        $w1 = $people_junc[$k]['symp'];
        $w2 = 100 - $w1;

        b('<div style="height: 8px;  border: 1px solid #888;  border-radius: 5px;  overflow: hidden;">');
        b('<div style="display: inline-block;  height: 100%; width:'.$w2.'%;  vertical-align: top;  background-color: #8f8;"></div>');
        b('<div style="display: inline-block;  height: 100%; width:'.$w1.'%;  vertical-align: top;  background-color: #faa;"></div>');
        b('</div>');
        }


      b('<td>');
      if (isset($people_junc[$k]))  b($db_people_rel[$people_junc[$k]['rel']]);


      //b('<td onclick="$.tdl(this)">');
      b('<td>');
      //if (isset($people_junc[$k]) && $people_junc_c[$k])  b('['.count($people_junc_c[$k]).'] ');
      //if (p('edit'))  b('<a href="/'.$mod.'/jne/?id='.$gid.'&ppl='.$k.'">');
      if (isset($people_junc[$k]))  b($people_junc[$k]['misc']);
      //if (p('edit'))  b('</a>');


      b('<td>');
      if (p('edit'))  b(icona('/'.$mod.'/ppe/?id='.$gid.'&ppl='.$k));
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

  $people = array('surname' => '',
                  'name' => '',
                  'otchestvo' => '',
                  'surnamef' => '',
                  'nickname' => '',
                  'birthdate' => '0000-00-00',
                  'addr' => '',
                  'lat' => 0,
                  'lon' => 0,
                  'phone' => '',
                  'vk' => '',
                  'ok' => '',
                  'fb' => '',
                  );

  if ($gppl) {
    $col = array();
    foreach ($people as $k=>$v)  $col[] = $k;

    $people = db_read(array('table' => 'people',
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


  b(form('people', '/'.$mod.'/ppu/?id='.$gid
    .($gppl ? '&ppl='.$gppl : '')
    ));

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
  b('Адрес:');
  b('<td>');
  b(form_t('f_people_addr', $people['addr'], 500));
  b(' '.form_t('f_lat,lat', ($people['lat'] ? geoaf($people['lat']) : ''), 90));
  b(' '.form_t('f_lon,lon', ($people['lon'] ? geoof($people['lon']) : ''), 90));
  b(' <input type="button" style="width: 20px; height: 20px; padding: 0;" value="O" onclick="window.open(\'/people/mpc/\');">');


  b('<tr><td>');
  b('Телефон:');
  b('<td>');
  b(form_t('f_people_phone', $people['phone'], 500));


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
    $set['birthdate'] = datesql(postn('f_people_date_y'), postn('f_people_date_m'), postn('f_people_date_d'), postn('f_people_date_h'), postn('f_people_date_i'), postn('f_people_date_s'));
    $set['addr'] = post('f_people_addr');
    //$geo = geoi(post('f_people_lat'), post('f_people_lon'));
    $set['lat'] = (post('f_lat') ? geoai(post('f_lat')) : 0);
    $set['lon'] = (post('f_lon') ? geooi(post('f_lon')) : 0);
    $set['phone'] = post('f_people_phone');
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
    $gjnc = (int)db_read(array('table' => 'people_junc',
                               'col' => 'id',
                               'where' => array('`f` = '.$gid,
                                                '`t` = '.$gppl,
                                                ),
                               ));
    }




  $junc = db_read(array('table' => 'people_junc',
                        'col' => array('id', 'f', 't', 'know', 'rel', 'symp', 'misc'),
                        'where' => '`id` = '.$gjnc,
                        ));

  if ($junc) {
    $gjnc = $junc['id'];
    }

  else {
    $junc = array('id' => 0,
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


  b(form('junc', '/'.$mod.'/jnu/?id='.$gid
    .($gjnc ? '&jnc='.$gjnc : '')
    ));

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

  $people = db_read(array('table' => 'people',
                          'col' => 'id',
                          'where' => array('`surname` = \''.$psurname.'\'',
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

    $people = db_read(array('table' => 'people',
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

  $people = db_read(array('table' => 'people',
                          'col' => array('lat', 'lon'),
                          'where' => '`id` = '.$gppl,
                          ));

  b('<script>
var default_id = '.$gppl.';
var default_lat = '.geoaf($people['lat']).';
var default_lon = '.geoof($people['lon']).';
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


  $markers = db_read(array('table' => 'people',
                           'col' => array('id', 'surname', 'name', 'otchestvo', 'lat', 'lon'),
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
        'title' => fiof($v['surname'], $v['name'], $v['otchestvo']),
        'lat' => geoaf($v['lat']),
        'lon' => geoof($v['lon']),
        );
      }
    b(json_encode($obj));
    }

  }


?>