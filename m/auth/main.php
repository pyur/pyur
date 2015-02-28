<?php

/************************************************************************/
/*  Авторизация  v1.oo                                                  */
/************************************************************************/


if (!isset($body))  die ('error: this is a pluggable module and cannot be used standalone.');




  // -------------------------------- login promt ------------------------------------ //

if (!postb('f_login')) {

  b('<p class="h1">Авторизация</p>');
  b();
  b();
  b();
                                                  // action="/'.$mod.'/" 
  b('<form name="auth" enctype="multipart/form-data" method="post" onsubmit="  
//event.preventDefault();

$.ajax(this.action, 

  function(r) {
    if (r == 1) {
      window.location = \'/\';
      }
    else if (r == 2) {
      $.id(\'login\').style.backgroundColor = \'#fbb\';
      }
    else if (r == 3) {
      $.id(\'login\').style.backgroundColor = \'#fbb\';
      $.id(\'password\').style.backgroundColor = \'#fbb\';
      }
    },

  {
    post: this,
    fail: function(r) { $.note(\'{error: \'+r.status+\'} \'+r.responseText, 10, \'#fcc\'); }
    }

  );

return  false;
">');

  b('<style>table.login {margin: 0 auto;} table.login td:nth-child(1) {padding: 0 0 16px 0; text-align: left; border: none; font-weight: bold; min-width: 80px; max-width: 80px;} table.login td:nth-child(2) {padding: 0 0 16px 0; text-align: left; border: none;}</style>');

  b('<table class="login">');
  b('<tr><td>');
  b('Логин:');
  b('<td>');
  b(form_t('@f_login,login', '', 200));


  b('<tr><td style="padding: 0;">');
  b('Пароль:');
  b('<td style="padding: 0;">');
  b('<input id="password" name="f_password" type="password" style="width: 200px;">');

  b('<tr><td><td>');
  b('<input name="fshp" type="checkbox"'.(post('fshp')?' checked':''));
  b(' onchange="var a = $.id(\'password\'); if (this.checked == true) a.type = \'text\'; else a.type = \'password\';"');
  b('> <span style="font-size: 8pt;">отображать при вводе</span>');


  b('<tr>');
  b('<td class="t" colspan="2" style="font-weight: normal;">');
  b('Сохранить пароль ');
  b('<input name="f_savepassword" type="checkbox" checked>');


  b('</table>');


  b(form_sb('войти'));

  b('</form>');
  }




else {
  $ajax = TRUE;

  $login = post('f_login');
  $pass = hash('sha512', post('f_password'));


  if ($login) {
    $user_id = 0;


      // --------------------- hardwired (embedded), not DB-MySQL users: --------------------------- //

    include 'l/hu.php';

    foreach($harduser as $k=>$v) {
      if ($v['login'] == $login) {
        if (hash ('sha512', $v['pass']) == $pass) {
          $user_id = 65504 + $k;
          }
        }
      }


      // --------------------------------- read & check `user` --------------------------------------- //

    if (!$user_id) {

      $user = db_read(array('table' => 'user',
                            'col' => array('id', '#pass'),
                            'where' => array('`login` = \''.$login.'\'',
                                             '`cat` != 0',
                                             ),
                            ));

      if ($user) {
        if (strtolower($user['#pass']) == $pass) {
          $user_id = $user['id'];
          }
        }
      }


    if ($user_id) {
      while(1) {
        $sid = md5(microtime().$_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT']);
        if (!db_read(array('table'=>'sess', 'col'=>'id', 'where'=>'`sid`=UNHEX(\''.$sid.'\')')))  break;
        }

      $set = array();
      $set['#sid'] = $sid;
      $set['stat'] = 0;
      $set['user'] = $user_id;
      $set['@ip'] = $_SERVER['REMOTE_ADDR'];
      $set['ua'] = substr($_SERVER['HTTP_USER_AGENT'],0,512);
      $set['date'] = $curr['datetime'];
      $set['datel'] = $curr['datetime'];
      db_write(array('table'=>'sess', 'set'=>$set));

        // -------- set COOKIE -------- //
      header ("Cache-Control: no-cache, must-revalidate");
      header ("Expires: Thu, 17 Apr 1991 12:00:00 GMT");  // Wed
      setcookie ('bdsx_sid', $sid, ( post('f_savepassword') ? time()+60*60*24*30*12*5 : 0), '/');

      b(1);
      }

    else {
        // -------- no user found / password matched -------- //
      b(3);
      }

    }  // if $login

  else {
      // -------- no login provided -------- //
    b(2);
    }
  }


?>