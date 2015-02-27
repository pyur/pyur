<?php

/************************************************************************/
/*  Пользователи  v1.oo                                                 */
/************************************************************************/


if (!isset($body))  die ('error: this is a pluggable module and cannot be used standalone.');



  // ---- GET ---- //

$gusr = getn('usr');
$guct = getn('uct');




  // --------------------------------------- Список пользователей -------------------------------------------- //

if (!$act && !$gusr) {

   $user = db_read(array('table' => 'user',
                         'col' => array('id', 'name', 'cat', 'login'),

                         'key' => 'id',
                         ));


   $user_cat = db_read(array('table' => 'user_cat',
                             'col' => array('id', 'desc'),

                             'key' => 'id',
                             ));


    // ---- submenu ---- //
  if (p('add'))  $submenu['Добавить пользователя;user-green--plus'] = '/'.$mod.'/use/';
  $submenu['Поиск пользователей;user-green--magnifier'] = '/'.$mod.'/usc/';
  if (p('edit_cat'))  $submenu['Категории доступа;key'] = '/'.$mod.'/ucl/';
  submenu();
    // ---- end: submenu ---- //




  b('<p class="h1">Пользователи</p>');
  b();

  if ($user) {

    css_table(array(250, 110, 350, 18));
    icona(array('pencil-button'));

    b('<table class="lst f10">');
    b('<tr>');
    b('<td id="name">Имя');
    b('<td>Логин');
    b('<td>Категория доступа');
    b('<td>Д.');

    foreach ($user as $k=>$v) {
      b('<tr');
      if (!$v['cat'])  b(' style="opacity: 0.4;"');
      b('>');


      b('<td>');
      b($v['name']);


      b('<td>');
      b($v['login']);


      b('<td>');
      if ($v['cat'])  b((isset($user_cat[$v['cat']])) ? $user_cat[$v['cat']]['desc'] : $v['cat']);


      b('<td>');
      b(icona('/'.$mod.'/use/?usr='.$k));
      }

    b('</table>');
    }

  else {
    b('<p class="p">Ошибка: нет пользователей.');
    }

  }




  // --------------------------------------- поиск пользователей -------------------------------------------- //

if ($act == 'usc') {

    // ---- submenu ---- //
  if (p('add'))  $submenu['Добавить пользователя;user-green--plus'] = '/'.$mod.'/use/';
  $submenu['Список пользователей;user-green--list'] = '/'.$mod.'/';
  submenu();
    // ---- end: submenu ---- //


  b('<p class="h1">Пользователи</p>');
  b();
  b();


    // ---------------- поиск ---------------- //
  b('<table class="edt w2">');

  b('<tr><td>');
  b('Поиск по имени:');
  b('<td>');
  b(form_t('@,f_search', '', 100));

  b('</table>');


    // ---------------- DIV ---------------- //
  b('<div id="result">');
  b('</div>');


  b('<script>
$.event("f_search", "keyup", $.delay(function() {$.ajax("/'.$mod.'/sch/", function(r) {$.id("result").innerHTML = r}, {post: {sch: $.id("f_search").value}} ) }, 0.5) );
</script>');
  }




if ($act == 'sch') {
  $ajax = TRUE;

    // ---- init GET vars ---- //
  $psch = post('sch');

  $psch = strtr(mb_strtolower($psch), array('a'=>'ф','b'=>'и','c'=>'с','d'=>'в','e'=>'у','f'=>'а','g'=>'п','h'=>'р','i'=>'ш','j'=>'о','k'=>'л','l'=>'д','m'=>'ь','n'=>'т','o'=>'щ','p'=>'з','q'=>'й','r'=>'к','s'=>'ы','t'=>'е','u'=>'г','v'=>'м','w'=>'ц','x'=>'ч','y'=>'н','z'=>'я','`'=>'ё','['=>'х',']'=>'ъ',','=>'б','.'=>'ю',';'=>'ж','\''=>'э'));



  $user = array();

  if ($psch) {
    $user = db_read(array('table' => 'user',
                            'col' => array('id', 'name'),
                            'where' => '`name` LIKE \''.$psch.'%\'',
                            'order' => '`name`',
                            'limit' => '100',

                            'key' => 'id',
                            ));
    }


    // ---------------- output ---------------- //

  if ($user) {
    b('<hr class="h">');
    b('<p class="h4">Результат поиска</p>');

    $n = 1;
    foreach ($user as $k=>$v) {
      b('<p style="margin: 8px;">');
      b($n.'. ');

      b('<a href="/'.$mod.'/?usr='.$k.'">');
      b($v['name']);
      b('</a>');

      $n++;
      }
    }
  }




  // ------------------------------------------- просмотр пользователя ------------------------------------------------ //

if (!$act && $gusr) {

  $user = db_read(array('table' => 'user',
                        'col' => array('name', 'login'),
                        'where' => '`id` = '.$gusr,
                        ));

  $user_cat = db_read(array('table' => array('user', 'user_cat'),
                            'col' => 'desc',
                            'where' => array('`user`.`id` = '.$gusr,
                                             '`user_cat`.`id` = `user`.`cat`',
                                             ),
                            ));


    // ---- submenu ---- //
  if (p('edit'))  $submenu['Редактировать;user-green--pencil'] = '/'.$mod.'/use/?usr='.$gusr;
  submenu();
    // ---- end: submenu ---- //




    // ---------------- printing table ---------------- //

  b('<p class="h1">Пользователь</p>');
  b();


  b('<table class="edt">');
  b('<tr><td>');
  b('Имя пользователя:');
  b('<td>');
  b($user['name']);

  if (p('edit_login')) {

    b('<tr><td>');
    b('Логин:');
    b('<td>');
    b($user['login']);


    b('<tr><td>');
    b('Категория доступа:');
    b('<td>');
    b( $user_cat ? $user_cat : 'не определено' );

    b('</table>');
    }

  b('</table>');
  }                	




  // ------------------------------------------ Редактирование пользователя  --------------------------------------------------- //

if ($act == 'use' && p('edit')) {

  $user = array('name' => '',
                'cat' => 0,
                'login' => '',
                //'pass' => '',
                );

  if ($gusr) {
    $col = array();
    foreach ($user as $k=>$v)  $col[] = $k;

    $user = db_read(array('table' => 'user',
                          'col' => $col,
                          'where' => '`id` = '.$gusr,
                          ));
    }

  $user_cat = db_read(array('table' => 'user_cat',
                            'col' => array('id', 'desc'),

                            'key' => 'id',
                            'value' => 'desc',
                            ));


    // ---- submenu ---- //
  if ($gusr && p())  $submenu['?Удалить;user-green--minus'] = array('#Подтвердить;tick-button' => form_sbd('/'.$mod.'/usu/?usr='.$gusr));
  submenu();
    // ---- end: submenu ---- //




  b('<p class="h1">');
  if ($gusr)  b('Редактирование пользователя');
  else        b('Добавление пользователя');
  b('</p>');
  b();

  b(form('user', '/'.$mod.'/usu/?'
    .($gusr ? '&usr='.$gusr : '')
    ));

  b('<table class="edt">');


  b('<tr><td>');
  b('Имя пользователя:');
  b('<td>');
  b(form_t('@f_usr_name', $user['name'], 300));


    // ---------------- логин и пароль ---------------- //

  if (p('edit_login')) {
    b('<tr><td>');
    b('Логин:');
    b('<td>');
    b(form_t('f_usr_login,login', $user['login'], 150));
  

    b('<tr><td>');
    b('Пароль:');
    b('<td>');
    b(form_t('f_usr_pass', '', 150));
    b(' <span class="f8">(ввести только если необходимо поменять; 0 - чтобы убрать пароль и заблокировать доступ)</span>');
  
  
    b('<tr><td>');
    b('Категория доступа:');
    b('<td>');
    b(form_s('!f_usr_cat', $user_cat, $user['cat']));
    }


  b('</table>');


  b(form_sb());


  b('</form>');


  b('<script>
$.event("login", "keyup", $.delay(function() {$.ajax("/'.$mod.'/clg/", function(r) { $.id("login").style.backgroundColor = (r ? "#fcc" : "#cfc")  }, {post:{login:$.id("login").value}} ) }, 0.5) );
</script>');
  }




  // --------------------------------------- update user ----------------------------------------- //

if ($act == 'usu' && p('edit')) {
  $ajax = TRUE;

  $post = postb('f_usr_name');
  $table = 'user';
  $where = '`id` = '.$gusr;

  if ($post) {

    $set = array();
    $set['name'] = post('f_usr_name');

    if (p('edit_login')) {
      $check = db_read(array('table' => 'user',
                             'col' => 'id',
                             'where' => '`login` = \''.post('f_usr_login').'\'',
                             ));
      if ($check && $check != $gusr)  die('error: user with the same login is exists.');

      $set['login'] = post('f_usr_login');
      $set['cat'] = postn('f_usr_cat');

      if (post('f_usr_pass'))  $f_usr_pass = hash('sha512', post('f_usr_pass', 15));
      elseif (postb('f_usr_pass') && post('f_usr_pass') === '0')  $f_usr_pass = '';
      if (isset($f_usr_pass))  $set['#pass'] = $f_usr_pass;
      }

    if (!$gusr) {
      if (!isset($f_usr_pass))  $set['#pass'] = '';
      $gusr = db_write(array('table' => $table, 'set' => $set));
      }

    else {
      db_write(array('table' => $table, 'set' => $set, 'where' => $where));
      }
  
    b('/'.$mod.'/');
    }


    // ---- deletion ---- //
  if (!$post && $gusr && p()) {
    db_write(array('table' => $table, 'where' => $where));
    //if (mysql_affected_rows($link_sql))  b('ok');
    //else                                 b('failed');
    b('/'.$mod.'/');
    }
  }




    // -------------------------------- ajax: check login -------------------------------- //

if ($act == 'clg') {
  $ajax = TRUE;

  $psch = post('login');
  $psch = filter_rlns($psch);

  if ($psch) {
    $user = db_read(array('table' => 'user',
                          'col' => 'id',
                          'where' => '`login` = \''.$psch.'\'',
                          ));
    if ($user)  b('1');
    }

  else {
    b('2');
    }

  }




  // --------------------------------------------------------------------------------------------------------------------------- //
  // ------------------------------------------ Настройка категорий доступа ---------------------------------------------------- //
  // --------------------------------------------------------------------------------------------------------------------------- //


  // -------------------------------- Список категорий доступа, разрешений ----------------------------------------- //

if ($act == 'ucl' && p('edit_cat')) {
  include 'c/mcache.php';

  $user_cat = db_read(array('table' => 'user_cat',
                            'col' => array('id', 'desc', 'perm'),

                            'key' => 'id'
                            ));


  $user = db_read(array('table' => array('user_cat', 'user'),
                        'col' => array('user_cat`.`id` AS `cat_id', 
                                       'user`.`id', 'user`.`name',
                                       ),
                        'where' => '`user`.`cat` = `user_cat`.`id`',

                        'key' => array('cat_id', 'id'),
                        ));
  

    // ---- submenu ---- //
  $submenu['Добавить категорию;plus-button'] = '/'.$mod.'/uce/';
  submenu();
    // ---- end: submenu ---- //



    // ---------------- output ---------------- //

  b('<p class="h1">Категории доступа</p>');
  b();


  if ($user_cat) {
    b('<table><tr>');
    b('<td class="b f10">Наименование категории');
    b('<td class="b f10">Доступ');
    b('<td class="b f10">Пользователи, состящие в категории');

    foreach ($user_cat as $k=>$v) {
      b('<tr>');

      b('<td class="li" width="220">');
      b('<a href="/'.$mod.'/uce/?uct='.$k.'">');
      b($v['desc']);
      b('</a>');


        // ---- perm ---- //

      b('<td class="li at f8" width="250">');
      b('<a href="/'.$mod.'/upe/?uct='.$k.'">');

      if ($v['perm']) {
        //b($v['perm'].'<br>');

        $tmp = explode (';', $v['perm']);

        $btmp = array();
        foreach ($tmp as $kk=>$vv) {

          $tmp2 = explode (':', $vv);

          if (isset($modules[$tmp2[0]])) {
            $btmp[] = '<span class="f10">&bull; '.$modules[$tmp2[0]]['name'].'</span>';
            }
          else {
            $btmp[] = '<span class="f10 red">&bull; '.$tmp2[0].'</span>';
            }

          $tmp3 = array();
          if (isset($tmp2[1]))  $tmp3 = explode (',', $tmp2[1]);


          if ($tmp3) {
            //$c = '';
            //for ($i = 0; $i < 32; $i++) {
            //  if ($tmp2[1] & (1<<$i)) {
            //    if ($c)  b('<br>├ '.$c);
            //    if (isset($dir[$tmp2[0]]['perm'][$i]))  $c = $dir[$tmp2[0]]['perm'][$i];
            //    else  $c = '<span class="red">'.$i.'</span>';
            //    }
            //  }
            //if ($c)  b('<br>└ '.$c);

            $c = 1;
            foreach ($tmp3 as $kkk=>$vvv) {
              $btmp[] = '&nbsp; &nbsp; '.
                        ((count($tmp3) == $c++) ? '└' : '├' ).' '.
                        (isset($modules[$tmp2[0]]['perm'][$vvv])?$modules[$tmp2[0]]['perm'][$vvv]:('<span class="red">'.$vvv.'</span>'));
              }

            }
          }
        b(implode('<br>', $btmp));
        }

      else {
        b('&lt;не имеет прав доступа&gt;');
        }

      b('</a>');


        // ---- Состящие в категории ---- //

      b('<td class="li at f10" width="290">');

      if (isset($user[$k])) {
        $tmp = array();
        foreach ($user[$k] as $kk=>$vv) {
          $tmp[] = '<a href="/'.$mod.'/?usr='.$kk.'">'.
                   $vv['name'].
                   '</a>';
          }
        b(implode('<br>', $tmp));
        }

      }
    }

  else {
    b('<p class="p">Ошибка: не определено ни одной категории доступа.');
    }

  b('</table>');
  }




  // -------------------------------- category edit ----------------------------------------- //

if ($act == 'uce' && p('edit_cat')) {

  $desc = db_read(array('table' => 'user_cat',
                        'col' => 'desc',
                        'where' => '`id`='.$guct,
                        ));


    // ---- submenu ---- //
  if ($guct && p())  $submenu['?Удалить категорию;minus-button'] = array('#Подтвердить;tick-button' => form_sbd('/'.$mod.'/ucu/?uct='.$guct));
  submenu();
    // ---- end: submenu ---- //




  b('<p class="h1">');
  if ($desc)  b($desc);
  else        b('Добавить новую категорию');
  b('</p>');
  b();


  b(form('user_cat', '/'.$mod.'/ucu/?'
    .($guct ? '&uct='.$guct : '')
    ));

  b('<table class="edt w2">');


  b('<tr><td>Название категории:');
  b('<td>');
  b(form_t('@f_uct_desc', $desc, 500));


  b('</table>');


  b(form_sb());

  b('</form>');
  }




  // ------------------------------------------------ category update ------------------------------------------------ //

if ($act == 'ucu' && p('edit_cat')) {
  $ajax = TRUE;

  $post = postb('f_uct_desc');

  $table = 'user_cat';
  $where = '`id` = '.$guct;

  if ($post) {

    $set = array();
    $set['desc'] = post('f_uct_desc');

    if ($guct) {
      $result = db_write(array('table' => $table, 'set' => $set, 'where' => $where));
      b('/'.$mod.'/ucl/');
      }

    else {
      $set['perm'] = '';

      $guct = db_write(array('table' => $table, 'set' => $set));
      b('/'.$mod.'/upe/?uct='.$guct);
      }
    }


    // -- delete -- //
  if (!$post && $guct && p()) {
    $result = db_write(array('table' => $table, 'where' => $where));
  
    b('/'.$mod.'/ucl/');
    }

  }




  // ----------------------------------------- permissions edit ----------------------------------------- //

if ($act == 'upe' && $guct && p('edit_cat')) {
  include 'c/mcache.php';

  $user_cat = db_read(array('table' => 'user_cat',
                            'col' => array('desc', 'perm'),
                            'where' => '`id`='.$guct,
                            ));


    // ---- parse params ---- //
  $tmp = explode (';', $user_cat['perm']);
  $field = array();
  foreach ($tmp as $v) {
    $tmp2 = explode (':', $v);

    $field[$tmp2[0]] = array();

    if (isset($tmp2[1])) {
      $tmp3 = explode (',', $tmp2[1]);

      foreach($tmp3 as $kk=>$vv) {
        $field[$tmp2[0]][$vv] = 1;
        }
      }

    }



    // ---------------- output ---------------- //

  b('<p class="h1">'.$user_cat['desc'].'</p>');
  b();


  b(form('ucp', '/'.$mod.'/upu/?uct='.$guct));

  foreach ($modules as $k=>$v) {
    if ($v['acc'])  continue;

    b('<p class="b">');
    b('<input name="fm_'.$k.'" type="checkbox" value="1"'.((isset($field[$k]))?' checked':'').'> ');
    b($v['name']);

    $c = 1;
    foreach ($v['perm'] as $kk=>$vv) {
      b('<p class="">&nbsp;');
      b( (count($v['perm']) == $c++) ? '└' : '├' );
      b(' <input name="fp_'.$k.';'.$kk.'" type="checkbox" value="1"'.((isset($field[$k][$kk]))?' checked':'').'> ');
      b($vv);
      }

    b('<p>&nbsp;');
    }


  b(form_sb());

  b('</form>');
  }




  // -------------------------------- permissions update ----------------------------------------- //

if ($act == 'upu' && $guct && p('edit_cat')) {
  $ajax = TRUE;

  $table = 'user_cat';
  $where = '`id` = '.$guct;

  $f_module = array();
  foreach ($_POST as $k=>$v) {
    if (substr($k, 0,3) == 'fm_')  $f_module[substr($k, 3)] = array();
    if (substr($k, 0,3) == 'fp_')  {
      $tmp = explode (';', substr($k, 3));
      if (isset($f_module[$tmp[0]]))  $f_module[$tmp[0]] [$tmp[1]] = 1;
      }
    }

  $tmp = array();
  foreach ($f_module as $k=>$v) {

    $tmp2 = array();
    foreach ($v as $kk=>$vv) {
      $tmp2[] = $kk;
      }
    $tmp2 = implode(',', $tmp2);

    $tmp[] = $k.($tmp2?(':'.$tmp2):'');
    }
  $f_module = implode(';', $tmp);

  if ($guct) {
    db_write(array('table' => $table, 'set' => array('perm' => $f_module), 'where' => $where));
    }
  //else {
  //  db_write(array('table' => 'user_cat', 'set' => array('perm' => $f_module)) );
  //  }

  b('/'.$mod.'/ucl/');
  }


?>