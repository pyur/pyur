<?php

/************************************************************************/
/*  Сбор Lenta.ru  v1.oo                                                */
/************************************************************************/


// 5 0 * * * /usr/bin/php /var/wwws/e/rasp_autocopy.php > /dev/null 2>&1
// "C:\Program Files (x86)\php\php.exe" "c:\www\loga\t\lenta\cron_lenta.php"
// рассчитан на запуск раз в 30 минут


  // ---- init --------------------------------------------------------------------------------- //

$body = '';
$redirect = '';
$mod = 'lenta';


chdir ('c:/www/loga');
include 'l/lib.php';

db_open();

  // ---- auth ---- //
function p($a = NULL) {
  return  true;
  }




  // ---- executing desirible code ------------------------------------------------------------------------- //

set_time_limit(1780);

$act = 'parse';
include 'm/'.$mod.'/main.php';


?>