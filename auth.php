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
  global $db;



    // ---------------- init ---------------- //

  $login = '';
  $pass = '';
  $auth = array(
    'id' => 0,
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







    // ------------------------------------ read COOKIE ------------------------------------- //

  if (cookieb('s')) {
    $sess = $db->
      table('sess')->
      col('id', 'user', 'ip', 'ua')->
      where('`stat` = 0', '`sid` = ?')->
      wa(unhex(cookieh('s')))->
      r();

    if ($sess) {
      $auth['sid'] = $sess['id'];
      $ua = substr($_SERVER['HTTP_USER_AGENT'],0,512);
      $ipn = inet_aton($_SERVER['REMOTE_ADDR']);
      $set = array();
      $set['datel'] = $curr['datetime'];
      if ($sess['ip'] != $ipn)  $set['ip'] = $ipn;
      if ($sess['ua'] != $ua)  $set['ua'] = $ua;
      $db->table('sess')->set($set)->where('`sid` = ?')->wa(unhex(cookieh('s')))->u();
      $sess = $sess['user'];
      }

    else {
      header ("Cache-Control: no-cache, must-revalidate");
      header ("Expires: Thu, 17 Apr 1991 12:00:00 GMT");
      setcookie('s', '', time()-60*60, '/');
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

    $user = $db->
      table('user', 'user_cat')->
      col('user`.`name', 'user_cat`.`perm')->
      where('`user`.`id` = '.$sess, '`user_cat`.`id` = `user`.`cat`')->
      r();

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


  apache_note('sid', $auth['sid']);




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