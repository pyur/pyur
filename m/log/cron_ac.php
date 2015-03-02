<?php

/************************************************************************/
/*  access logs (поиск новых строк)  v1.oo                              */
/************************************************************************/


// 5 0 * * * /usr/bin/php /var/www/m/log/cron_ac.php > /dev/null 2>&1
// "C:\Program Files (x86)\php\php.exe" "c:\www\m\log\cron_ac.php"


  // ---- init --------------------------------------------------------------------------------- //

$body = '';
$redirect = '';
$mod = 'log';


chdir ('c:/www/loga');
//chdir ('/var/wwws');
//chdir ('../../');
include 'l/lib.php';

db_open();




  // ---- auth --------------------------------------------------------------------------------- //

function p($a = NULL) {
  return  true;
  }




  // ---- executing desirable code ------------------------------------------------------------------------- //

include 'l/lib_ua.php';


include 'm/'.$mod.'/log_share.php';
$errorc = 0;
$warningc = 0;




  // ---- db server ---- //

$server = db_read(array('table' => 'server',
                        'col' => array('id', 'logname', 'rhost', 'format'),
                        'where' => '`format` != 0',
                        'key' => 'logname',
                        ));


  // ---- read dir ---- //

$dir = array();

$path = 'c:/Apache/logs';
$dh = opendir($path);


while (($file = readdir($dh)) !== false) {
  if ($file == '.' || $file == '..')  continue;

  $log_name = explode('.', $file);
  if ($log_name[0] != 'access')  continue;

  array_pop($log_name);
  array_pop($log_name);
  array_shift($log_name);
  $log_name = implode('.', $log_name);

  $imp_server = 0;
  $enable_coloring = FALSE;
  $log_format = '';


  if (isset($server[$log_name])) {
    $imp_server = $server[$log_name]['id'];
    $enable_coloring = ($server[$log_name]['rhost'] ? TRUE : FALSE);
    $log_format = $server[$log_name]['format'];
    }


  if ($imp_server) {

    if (filesize ($path.'/'.$file)) {
      $log = fread (fopen ($path.'/'.$file, 'rb'), filesize ($path.'/'.$file) );
      }
    else {
      $log = '';
      }

    $count = count_chars($log);
    $curr_row = $count[10];

    $last_row = db_read(array('table' => 'log_check',
                              'col' => array('id', 'last_row'),
                              'where' => '`file` = \''.$file.'\'',
                              ));

    if (!$last_row)  $last_row = array('id'=>0, 'last_row'=>0);



        // -------------------------------- import new rows -------------------------------- //

    if ($curr_row > $last_row['last_row']) {
      $set = array();
      $set['last_row'] = $curr_row;

      if ($last_row['id']) {
        db_write(array('table'=>'log_check', 'set'=>$set, 'where'=>'`id` = '.$last_row['id']));
        }
      else {
        $set['file'] = $file;
        db_write(array('table'=>'log_check', 'set'=>$set));
        }



      $lines = explode("\n", strtr($log, array("\r"=>'')));

      for ($i = $last_row['last_row']; $i < $curr_row; $i++) {
        $v = $lines[$i];
        if (!$v)  continue;

        if ($log_format == 2)  $line = log_parse_line_bdsx($v);  // bdsx
        else                   $line = log_parse_line($v);       // default


        $uan = db_read(array('table' => 'ua',
                             'col' => array('id', 'type'),
                             'where' => '`ua` = \''.addslashes($line['ua']).'\'',
                             ));

        if (!$uan) {
          $spcf = parse_ua($line['ua']);

          $set = array();
          $set['ua'] = $line['ua'];
          $set['type'] = 0;
          //$set['spcf'] = $spcf['on'] + ($spcf['x'] << 7) + ($spcf['bn'] << 8) + ((int)$spcf['v'] << 16) + ((int)$spcf['m'] << 24);
          $set['spcf'] = $spcf['on'] + ($spcf['x'] << 7) + ($spcf['bn'] << 8) + ( (((int)$spcf['v'] >255) ? 255 : (int)$spcf['v']) << 16) + ( (((int)$spcf['m'] >127) ? 127 : (int)$spcf['m']) << 24);

          $uan['id'] = db_write(array('table'=>'ua', 'set'=>$set));
          }


        $type = 0;
        if ($enable_coloring) {
          $ip = parse_ip($line['ip'], substr($line['datesql'], 0, 10) );
          if ($ip['type'])  $type = $ip['type'];
          elseif (isset($uan['type']))  $type = $uan['type'];
          }


        $set = array();
        $set['server'] = $imp_server;
        $set['@ip'] = $line['ip'];
        $set['datetime'] = $line['datesql'];
        $set['methodn'] = $line['methodn'];
        $set['uri'] = $line['uri'];
        $set['httpvn'] = $line['httpvn'];
        $set['resultn'] = $line['resultn'];
        $set['bytes'] = ($line['bytes'] == '-' ? 0 : $line['bytes']);
        $set['referer'] = ($line['referer'] == '-' ? '' : $line['referer']);
        $set['uan'] = $uan['id'];
        $set['userx'] = ($line['userx'] == '-' ? 0 : $line['userx']);
        $set['@ipf'] = ($line['ipf'] == '-' ? '0.0.0.0' : $line['ipf']);
        $set['type'] = $type;

        db_write(array('table'=>'log', 'set'=>$set));
        }
      }

    }

  }

closedir($dh);


?>