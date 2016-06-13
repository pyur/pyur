<?php

/************************************************************************/
/*  Хосты  v1.oo                                                        */
/************************************************************************/


if (!isset($body))  die ('error: this is a pluggable module and cannot be used standalone.');


$ghst = getn('hst');
$gsrv = getn('srv');


$db_log_format = array(
  0 => '-',
  1 => 'default',
  2 => 'bdsx',
  );





if (!$act) {

  include 'm/log/const.php';

  $host = $db->
    table('host')->
    col('id', 'ip', 'ipe', 'dateb', 'datee', 'metric', 'desc', 'stat', 'color', 'type')->
    key('id')->
    r();


    // ---- submenu ---- //
  if (p('edit'))  $submenu['Добавить хост'] = '/'.$mod.'/hse/';
  $submenu['Серверы'] = '/'.$mod.'/srv/';
  submenu();
    // ---- end: submenu ---- //




  b('<p class="h1">Хосты</p>');
  b();


  if ($host) {
    css_table(array(24, 100, 100, 80, 80, 20, 200, 20, 60, 140));

    b('<table class="lst f10">');
    b('<tr>');
    b('<td>id');
    b('<td>ip');
    b('<td>ipe');
    b('<td>dateb');
    b('<td>datee');
    b('<td>mt');
    b('<td>desc');
    b('<td>st');
    b('<td>color');
    b('<td>type');

    foreach ($host as $k=>$v) {
      b('<tr>');

      b('<td>');
      b($k);


      b('<td>');
      b(inet_ntoa($v['ip']));

      b('<td>');
      b(inet_ntoa($v['ipe']));


      b('<td>');
      if ($v['dateb'] != '0000-00-00')  b(dateh($v['dateb']));

      b('<td>');
      if ($v['datee'] != '0000-00-00')  b(dateh($v['datee']));


      b('<td>');
      b($v['metric'] ? $v['metric'] : '');


      b('<td>');
      if (p('edit'))  b('<a href="/'.$mod.'/hse/?hst='.$k.'">');
      b($v['desc']);
      if (p('edit'))  b('</a>');


      b('<td>');
      b($v['stat'] ? $v['stat'] : '');


      b('<td>');
      if ($v['color'])  b('<span style="color: '.$v['color'].';">');
      b($v['color']);
      if ($v['color'])  b('</span>');


      b('<td>');
      $stylet = array();
      if ($db_hit_type[$v['type']]['tc'])  $stylet[] = 'color: '.$db_hit_type[$v['type']]['tc'];
      if ($db_hit_type[$v['type']]['bc'])  $stylet[] = 'background-color: '.$db_hit_type[$v['type']]['bc'];
      if ($stylet)  b('<span style="'.implode('; ', $stylet).'">');

      b($db_hit_type[$v['type']]['d']);
      if ($stylet)  b('</span>');
      }

    b('</table>');
    }

  else {
    b('<p class="p">Ошибка: данные отсутствуют.');
    }

  }




  // -------------------------- host add / edit -------------------------- //

if ($act == 'hse' && p('edit') ) {

  include 'm/log/const.php';

  $host = array(
    '@ip' => '',
    '@ipe' => '',
    'dateb' => '0000-00-00',
    'datee' => '0000-00-00',
    'metric' => 0,
    'desc' => '',
    'stat' => 0,
    'color' => '',
    'type' => 0,
    );

  if ($ghst) {
    $col = array();
    foreach ($host as $k=>$v)  $col[] = $k;
    $host = $db->table('host')->col($col)->where('`id` = '.$ghst)->r();
    }


    // ---- submenu ---- //
  if (p() && $ghst)  $submenu['?Удалить;minus-button'] = array('#Подтвердить;tick-button' => form_sbd('/'.$mod.'/hsu/?hst='.$ghst));
  submenu();
    // ---- end: submenu ---- //




  b('<p class="h1">');
  if (!$ghst)  b('Добавление');
  else         b('Редактирование');
  b('</p>');
  b();
  b();


  b(form('host', '/'.$mod.'/hsu/?'
    .($ghst ? '&hst='.$ghst : '')
    ));

  b('<table class="edt">');

  b('<tr><td>');
  b('IP:');
  b('<td>');
  b(form_t('@f_hst_ip', $host['@ip'], 100));


  b('<tr><td>');
  b('IP end:');
  b('<td>');
  b(form_t('f_hst_ipe', $host['@ipe'], 100));


  b('<tr><td>');
  b('Metric:');
  b('<td>');
  b(form_n('f_hst_metric', $host['metric'], 40));


  b('<tr><td>');
  b('Дата начальная:');
  b('<td>');
  b(form_dt(array('!f_hst_dateb_y;2012', '!f_hst_dateb_m', '!f_hst_dateb_d'), $host['dateb']));


  b('<tr><td>');
  b('Дата конечная:');
  b('<td>');
  b(form_dt(array('!f_hst_dateb_y;2012', '!f_hst_dateb_m', '!f_hst_dateb_d'), $host['dateb']));


  b('<tr><td>');
  b('Описание:');
  b('<td>');
  b(form_t('f_hst_desc', $host['desc'], 300));


  b('<tr><td>');
  b('Stat:');
  b('<td>');
  b(form_n('f_hst_stat', $host['stat'], 40));


  b('<tr><td>');
  b('Цвет:');
  b('<td>');
  b(form_t('f_hst_color', $host['color'], 50));


  b('<tr><td>');
  b('Тип:');
  b('<td>');
  b(form_s('f_hst_type;d', $db_hit_type, $host['type']));


  b('</table>');


  b(form_sb());
  b('</form>');
  }




  // ------------------------------------------- ajax: host update ------------------------------------------------ //

if ($act == 'hsu' && p('edit') ) {
  $ajax = TRUE;

  $post = postb('f_hst_desc');

  $table = 'host';
  $where = '`id` = '.$ghst;

  if ($post) {
    $set = array();
    $set['@ip'] = post('f_hst_ip');
    $set['@ipe'] = post('f_hst_ipe');
    $set['dateb'] = datesql(postn('f_hst_dateb_y'), postn('f_hst_dateb_m'), postn('f_hst_dateb_d'));
    $set['datee'] = datesql(postn('f_hst_datee_y'), postn('f_hst_datee_m'), postn('f_hst_datee_d'));
    $set['metric'] = post('f_hst_metric');
    $set['desc'] = post('f_hst_desc');
    $set['stat'] = postn('f_hst_stat');
    $set['color'] = post('f_hst_color');
    $set['type'] = post('f_hst_type');

    if ($ghst) {
      $db->table($table)->set($set)->where($where)->u();
      }

    else {
      //$check = db_!read(array('table'=>'host', 'col'=>'id', 'where'=>'`address`=\''.$set['address'].'\''));
      //if ($check)  die('error: such ip already exists.');

      $db->table($table)->set($set)->i();
      }

    b('/'.$mod.'/');
    }


    // ---- deletion ---- //
  if (!$post && $ghst && p()) {
    $result = $db->table($table)->where($where)->d();

    b('/'.$mod.'/');
    }  // end: delete

  }






  // ------------------------------------------------------------------------------------------------------------------------------------------------- //
  // ------------------------------------------------------------------ server ----------------------------------------------------------------------- //
  // ------------------------------------------------------------------------------------------------------------------------------------------------- //

if ($act == 'srv') {

  $server = $db->
    table('server')->
    col('id', 'tp', 'desc', 'logname', 'rhost', 'format')->
    key('tp')->
    r();
  $server = tsort($server);


    // ---- submenu ---- //
  if (p('edit'))  $submenu['Добавить сервер'] = '/'.$mod.'/sre/';
  submenu();
    // ---- end: submenu ---- //




  b('<p class="h1">Серверы</p>');
  b();


  if ($server) {
    css_table(array(24, 30, 200, 200, '20', 100));

    b('<table class="lst f10">');
    b('<tr>');
    b('<td>id');
    b('<td>tp');
    b('<td>desc');
    b('<td>logname');
    b('<td>rh');
    b('<td>format');

    foreach ($server as $k=>$v) {
      b('<tr>');

      b('<td>');
      b($k);


      b('<td>');
      b($v['tp']);


      b('<td>');
      if (p('edit'))  b('<a href="/'.$mod.'/sre/?srv='.$k.'">');
      b($v['desc']);
      if (p('edit'))  b('</a>');


      b('<td>');
      b($v['logname']);


      b('<td>');
      b($v['rhost'] ? '+' : '');


      b('<td>');
      b($db_log_format[$v['format']]);
      }

    b('</table>');
    }

  else {
    b('<p class="p">Ошибка: данные отсутствуют.');
    }

  }




  // ---------------------------------------------------- server add / edit --------------------------------------------------------- //

if ($act == 'sre' && p('edit') ) {

  $pserver = $db->
    table('server')->
    col('id', 'tp', 'desc')->
    key('tp')->
    r();
  $pserver = tsort($pserver, 'desc', $gsrv);


  $server = array(
    'tp' => 0,
    'desc' => '',
    'logname' => '',
    'rhost' => 0,
    'format' => 0,
    );

  if ($gsrv) {
    $col = array();
    foreach ($server as $k=>$v)  $col[] = $k;
    $server = $db->table('server')->col($col)->where('`id` = '.$gsrv)->r();
    }


    // ---- submenu ---- //
  if (p() && $gsrv)  $submenu['?Удалить;minus-button'] = array('#Подтвердить;tick-button' => form_sbd('/'.$mod.'/sru/?srv='.$gsrv));
  submenu();
    // ---- end: submenu ---- //




  b('<p class="h1">');
  if (!$gsrv)  b('Добавление');
  else         b('Редактирование');
  b('</p>');
  b();
  b();


  b(form('server', '/'.$mod.'/sru/', array(
    $gsrv ? 'srv='.$gsrv : '',
    )));

  b('<table class="edt">');

  b('<tr><td>');
  b('Вставить после:');
  b('<td>');
  b(form_s('f_srv_tp', $pserver, $server['tp']));


  b('<tr><td>');
  b('Описание:');
  b('<td>');
  b(form_t('f_srv_desc', $server['desc'], 150));


  b('<tr><td>');
  b('Имя лог-файла:');
  b('<td>');
  b(form_t('f_srv_logname', $server['logname'], 150));


  b('<tr><td>');
  b('Определить хост:');
  b('<td>');
  b(form_s('f_srv_rhost', array(0=>'Нет', 1=>'Да'), $server['rhost']));


  b('<tr><td>');
  b('Формать лога:');
  b('<td>');
  b(form_s('f_srv_format', $db_log_format, $server['format']));


  b('</table>');


  b(form_sb());
  b('</form>');
  }




  // ------------------------------------------- ajax: server update ------------------------------------------------ //

if ($act == 'sru' && p('edit') ) {
  $ajax = TRUE;

  $post = postb('f_srv_desc');

  $table = 'server';
  $where = '`id` = '.$gsrv;

  if ($post) {
    $set = array();
    $set['tp'] = postn('f_srv_tp');
    $set['desc'] = post('f_srv_desc');
    $set['logname'] = post('f_srv_logname');
    $set['rhost'] = postn('f_srv_rhost');
    $set['format'] = postn('f_srv_format');

    if ($gsrv) {
      $rem = $db->table($table)->col('tp')->where($where)->r();
      if ($rem !== '' && $rem != $set['tp'])  $db->table($table)->set('tp', $rem)->where('`tp`='.$gsrv)->u();
      }
    $ins = $db->table($table)->col('id')->where('`tp`='.$set['tp'])->r();

    if ($gsrv) {
      $db->table($table)->set($set)->where($where)->u();
      }

    else {
      $gsrv = $db->table($table)->set($set)->i();
      }

    if ($ins && $ins != $gsrv)  $db->table($table)->set('tp', $gsrv)->where('`id`='.$ins)->u();

    b('/'.$mod.'/srv/');
    }


    // ---- deletion ---- //
  if (!$post && $gsrv && p()) {
    $rem = $db->table($table)->col('tp')->where($where)->r();
    if ($rem !== '')  $db->table($table)->set('tp',$rem)->where('`tp`='.$gsrv)->u();

    $result = $db->table($table)->where($where)->d();

    b('/'.$mod.'/srv/');
    }  // end: delete

  }


?>