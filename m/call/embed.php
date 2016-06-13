<?php

/************************************************************************/
/*  Call  v1.oo                                                         */
/************************************************************************/


if (!isset($body))  die ('error: this is a pluggable module and cannot be used standalone.');

//$gid = getn('id',1);
//$gppl = getn('ppl');
//$gsym = getn('sym');

//$gdate = gets('date', $curr['date']);


//include 'm/'.$mod.'/const.php';


//ini_set("log_errors", 1);
//ini_set("error_log", "/loga-error.log");
//error_log( "Hello, errors!" );





  // -------------------------------- embed:  -------------------------------- //

if (!$act) {


  }




  // -------------------------------- embed: receive calllog -------------------------------- //

if ($act == 'mfa') {
  $ajax = TRUE;

  $db_imeir = array(
    '111111111111111' => 1,
    //'222222222222222' => 2,
    '333333333333333' => 3,
    '444444444444444' => 4,
    );


  $raw_post = file_get_contents("php://input");
  fwrite (fopen ('m/'.$mod.'/debug/debug '.time(), 'wb'),  $raw_post);  clearstatcache();
  //$file = 'm/'.$mod.'/debug/debug 1449492382b';  $raw_post = fread (fopen ($file, 'rb'), filesize ($file));


    // ---------------- write to db ---------------- //

  $json = json_decode($raw_post, TRUE);
  if (!isset($json['calls']))  die('error: wrong json');
  //$json['calls'] = $json;
  //d(count($json['calls']));

  if (isset($db_imeir[$json['imei']]))  $imei = $db_imeir[$json['imei']];
  else  $imei = 255;


  foreach ($json['calls'] as $k=>$v) {
    $phone = filter_n($v['phone']);
    $phone = substr($phone,-10,10);

    $check = $db->
      table('call')->
      col('id')->
      where('`imei` = '.$imei,
            '`pid` = '.$v['id'])->
      r();

    if (!$check) {
      $duration = 0;
      if (isset($v['duration'])) $duration = (int)$v['duration'];
      if ($duration < 0)  $duration = 65535;

      $set = array();
      $set['imei'] = $imei;
      $set['pid'] = $v['id'];
      $dt = datesql(substr($v['dt'],0,-3), 1);
      $set['date'] = substr($dt, 0,10);
      $set['time'] = substr($dt, 11,8);
      $set['phone'] = $phone;
      $set['type'] = $v['type'];
      $set['duration'] = $duration;
      $set['name'] = (isset($v['name']) ? $v['name'] : '');
      //d($set);

      $db->table('call')->set($set)->i();
      }


    }


    // ---------------- answer ---------------- //

  $json = array();
  $json['result'] = 'ok';
  b(json_encode($json));
  }


?>