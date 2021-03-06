<?php

/************************************************************************/
/*  cabinet  v1.oo                                                      */
/************************************************************************/


if (!isset($body))  die ('error: this is a pluggable module and cannot be used standalone.');



$grow = getn('row');


include 'm/log/const.php';




  // -------------------------------- Personal cabinet ------------------------------------ //

if (!$act) {
  include 'l/lib_ua.php';

  $sess = $db->
    table('sess')->
    col('id', 'ip', 'ua', 'date', 'datel')->
    where('`user` = '.$auth['id'],
          '`stat` = 0')->
    order('`datel` DESC')->
    key('id')->
    r();


    // ---- submenu ---- //
  $submenu['Выйти;lock'] = '/'.$mod.'/lof/';
  submenu();
    // ---- end: submenu ---- //




  b('<p class="h1">Личный кабинет</p>');

  if ($sess) {
    b('<p class="h2">Активные сессии</p>');

    css_table(array(30, 100, 150, 150, 250, 18));
    icona(array('cross-button'));

    b('<table class="lst f10">');
      b('<tr>');
      b('<td>id');
      b('<td>ip');
      b('<td>date');
      b('<td>datel');
      b('<td>ua');
      b('<td>ac');

    foreach ($sess as $k=>$v) {
      b('<tr');
      if (datesqltime($v['datel']) < ($curr['time'] -1209600))  b(' style="opacity: 0.2;"');
      elseif (datesqltime($v['datel']) < ($curr['time'] -259200))  b(' style="opacity: 0.4;"');
      elseif (datesqltime($v['datel']) < ($curr['time'] -86400))  b(' style="opacity: 0.6;"');
      b('>');


      b('<td>');
      b($k);


      b('<td>');
      b(inet_ntoa($v['ip']));


      b('<td>');
      b(dateh($v['date']));


      b('<td>');
      b(dateh($v['datel']));


      b('<td>');
      b(ua_str($v['ua']));


      b('<td>');
      b(icona('/'.$mod.'/srk/?row='.$k));
      }

    b('</table>');
    }

  else {
    b('error: no `sess`.');
    }
  }




  // -------------------------------- session revoke proxy ------------------------------------ //

if ($act == 'srk') {
  $db->table('sess')->set('stat', 2)->where('`id` = '.$grow)->u();

  $redirect = '/'.$mod.'/';
  }




  // -------------------------------- graceful Logout ------------------------------------ //

if ($act == 'lof') {

  $db->table('sess')->set('stat', 1)->where('`sid` = ?')->wa(unhex(cookieh('s')))->u();


    // -- clear cookie -- //

  header ("Cache-Control: no-cache, must-revalidate");
  header ("Expires: Thu, 17 Apr 1991 12:00:00 GMT");

  setcookie('s', '', time()-60*60, '/');

  $redirect = '/';
  }


?>