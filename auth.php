<?php

/************************************************************************/
/*  authorization v1.oo                                             бом */
/************************************************************************/


if (!isset($body))  die ('error: this is a pluggable module and cannot be used standalone.');



  // -------------------------------- authorization -------------------------------- //

function authorization() {

  global $body;
  global $mod;
  global $curr;
  global $modules;



    // ---------------- init ---------------- //

  $login = '';
  $pass = '';
  $auth = array('id' => 0,
                'desc' => '',
                'state' => 1,
                'perm' => '',  // module1:perm1,perm2,perm3;module2:perm3;module5
                'sid' => 0,
                );

  //  state:
  // 1  - sess exists
  // 2  - ok
  // 4  - sess not exists
  // 8  - user for sess_id not exists




    // ------------------------------------ identificate organization ------------------------------------ //

  $auth['org'] = 0;
  $auth['org_desc'] = 'Добро пожаловать в Pyur CRM-Framework';



    // ---- DoS filter --------------------------------------------------------------------------- //
/*
    // -- `IP` filter -- //
  $remote_addr = explode('.', (isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:'0.0.0.0') );
  $remote_addr = $remote_addr[0] * 16777216 + $remote_addr[1] * 65536 + $remote_addr[2] * 256 + $remote_addr[3];

  //$result = db_read('login_ip', array('date', 'count'), '`ip` = '.$remote_addr);
  $result = db_read(array('table'=>'login_ip', 'col'=>array('date', 'count'), 'where'=>'`ip` = '.$remote_addr));
  if ($result) {
    //$db = mysql_fetch_array($result, MYSQL_ASSOC);
    $auth_ip_date = datesqltime($result['date']);
    $auth_ip_count = $result['count'];

    if ($auth_ip_count > 15)  $auth['state'] = 16;
    }
  else {
    $result = db_write(array('table'=>'login_ip', 'set'=>array('ip' => $remote_addr, 'count' => 0));
    $auth_ip_date = $curr['time'];
    $auth_ip_count = 0;
    }
*/




    // ------------------------------------ read COOKIE ------------------------------------- //

  if (cookieb('bdsx_sid')) {
    $sess = db_read(array('table' => 'sess',
                          'col' => array('id', 'user', '@ip', 'ua'),
                          'where' => array('`sid` = UNHEX(\''.cookieh('bdsx_sid').'\')',
                                           '`stat` = 0',
                                           ),
                          ));
    if ($sess) {
      $auth['sid'] = $sess['id'];
      $ua = substr($_SERVER['HTTP_USER_AGENT'],0,512);
      $set = array();
      $set['datel'] = $curr['datetime'];
      if ($sess['@ip'] != $_SERVER['REMOTE_ADDR'])  $set['@ip'] = $_SERVER['REMOTE_ADDR'];
      if ($sess['ua'] != $ua)  $set['ua'] = $ua;
      db_write(array('table'=>'sess', 'set'=>$set, 'where'=>'`sid` = UNHEX(\''.cookieh('bdsx_sid').'\')'));
      $sess = $sess['user'];
      }

    else {
      header ("Cache-Control: no-cache, must-revalidate");
      header ("Expires: Thu, 17 Apr 1991 12:00:00 GMT");
      setcookie('bdsx_sid', '', time()-60*60, '/');
      $auth['state'] = 4;
      }
    }

  else {
    $auth['state'] = 4;
    }



    // --------------------- hardwired (embedded), not DB-MySQL users: --------------------------- //

  if ($auth['state'] == 1 && $sess > 65503) {

    include 'l/hu.php';

    if (isset($harduser[$sess-65504])) {
      $auth['id'] = $sess;
      $auth['desc'] = $harduser[$sess-65504]['desc'];
      $auth['perm'] = $harduser[$sess-65504]['perm'];
      $auth['state'] = 2;
      }

    else {
      $auth['state'] = 8;
      }
    }




    // --------------------------------- read & check `user` --------------------------------------- //

  if ($auth['state'] == 1) {

    $user = db_read(array('table' => array('user', 'user_cat'),
                          'col' => array('user`.`name',
                                         'user_cat`.`perm',
                                         ),
                          'where' => array(//'`user`.`org` = '.$auth['org'],
                                           '`user`.`id` = \''.$sess.'\'',
                                           '`user_cat`.`id` = `user`.`cat`',
                                           ),
                          ));

    if ($user) {
      $auth['id'] = $sess;
      $auth['desc'] = $user['name'];
      $auth['perm'] = $user['perm'];
      $auth['state'] = 2;
      }
    else {
      $auth['state'] = 8;
      }
    }


  apache_note('userx', $auth['id']);




    // --------------------------------- modules cache --------------------------------- //




    // --------------------------------- permissions --------------------------------- //

  $perm = array();
  //$auth['perm'] = 'stud:ank_edit,doc,stipen;test';

  if ($auth['perm'] == 'all') {
    $tmp = array();

    foreach ($modules as $k=>$v) {
      if (!$v['acc'] || $v['acc'] & $auth['state']) {
        $perm[$k] = array();
        foreach ($v['perm'] as $kk=>$vv) {
          $perm[$k][$kk] = 1;
          }
        }
      }
    }


  else {
      // ---- user's explicit permissions ---- //

    $tmp = explode (';', $auth['perm']);
    foreach ($tmp as $v) {
      $tmp2 = explode (':', $v);

      $perm[$tmp2[0]] = array();

      if (isset($tmp2[1])) {
        $tmp3 = explode (',', $tmp2[1]);
        foreach ($tmp3 as $vv) {
          //if (isset($modules[$tmp2[0]]))
          //$tmp3[$vv] = '1';
          $perm[$tmp2[0]][$vv] = 1;
          }
        }
      //$perm[$tmp2[0]] = $tmp3;
      }
    }


  $menu = array();

  $num = 0;
  foreach ($modules as $k=>$v) {
    if (isset($perm[$k])  || $v['acc'] & $auth['state'] ) {
      $v['icon'] = $num;
      $v['sort'] = substr('000'.$v['pos'], -3,3).$v['name'];
      $menu[$k] = $v;
      }
    $num++;
    }




/*
    // -------- bruteforce control -------- //
  if ($auth['state'] == 4 && $auth_ip_date > ($curr['time']-30) ) {
      // ---- increment `count` on wrong password ---- //
    $result = db_write(array('table'=>'login_ip', 'set'=>array('date' => datesql($curr['time'],1), 'count' => $auth_ip_count+1), 'where'=>'`ip` = '.$remote_addr));
    }

  elseif ($auth_ip_count && $auth_ip_date < ($curr['time']-3600) ) {
      // ---- reset after 1 hour cooldown ---- //
    $result = db_write(array('table'=>'login_ip', 'set'=>array('date' => datesql($curr['time'],1), 'count' => 0), 'where'=>'`ip` = '.$remote_addr));
    }
*/



    // -------------------------------------------------- activity log rotate -------------------------------------------------------- //
/*
    // SELECT `id`, COUNT(`id`) as `count` FROM `log_rotate` LIMIT 1
  $log_rotate = db_read(array('table' => 'log_rotate',
                              'col' => array('id', '!COUNT(`id`) as `count`'),
                              //verbose=>1
                              ));

  if ($log_rotate['count'] > 1999) {
    $query  = 'DELETE FROM `log_rotate` ORDER BY `id` LIMIT '.($log_rotate['count'] - 1999);
    mysql_query($query);
    }

  db_write(array('table'=>'log_rotate',
                 'set' => array('host' => $remote_addr,
                                'time'=>date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']),
                                'user' => $auth['userx'],
                                'request' => $_SERVER['REQUEST_URI'],
                                )));
*/
    // -------------------------------------------------- end: activity log rotate -------------------------------------------------------- //



    // ---- access control ---- //
  if (!isset($menu[$mod])) {
    $mod = 'default';
    }


  if ($auth['perm'] == 'all')  $auth['perm_su'] = 1;
  $auth['menu'] = $menu;
  $auth['perm'] = $perm;

  return  $auth;
  } // end: authorization()




  // ---- check permission accessibility ---- //

function p($a = NULL) {
  // <empty> - check `super_user`
  // string  - check `perm` of current module
  // array (one parameter)  - check other `module` accesibility
  // array (two parameters) - check `perm` of other `module` accesibility

  global $auth;
  global $mod;

  $r = false;

  if (!is_array($a)) {
      // -- check current_module,super_user accessibility -- //
    if ($a === NULL) { 
      if (isset($auth['perm_su']))  $r = true;
      }

      // -- check current_module,permission accessibility -- //
    elseif (isset($auth['perm'][$mod][$a]))  $r = true;
    //elseif ($a === NULL)  e('null');
    }

  else {
    if (!isset($a[1])) {
        // -- check module accessibility -- //
      if (isset($auth['perm'][$a[0]]))  $r = true;
      }

    else {
        // -- check module,permission accessibility -- //
      if (isset($auth['perm'][$a[0]][$a[1]]))  $r = true;
      }
    }

  return  $r;
  }


$auth = authorization();

unset($modules);


?>