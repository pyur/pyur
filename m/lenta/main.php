<?php

/************************************************************************/
/*  Лента.ру  v1.oo                                                     */
/************************************************************************/


if (!isset($body))  die ('error: this is a pluggable module and cannot be used standalone.');

$gdate = gets('date');


$db_link_type = array(
  1 => 'news',
  2 => 'articles',
  3 => 'photo',
  4 => 'video',
  5 => 'onlines',
  6 => 'columns',
  );





  // ---------------------------------------------------- fluid lenta --------------------------------------------------------- //

if (!$act) {

  $pref = db_read(array('table' => 'lenta_pref',
                        'col' => array('id', 'lenta'),
                        'where' => '`id` = 1',
                        ));


    // ---- submenu ---- //
  $submenu['За день'] = '/'.$mod.'/lnd/';
  submenu();
    // ---- end: submenu ---- //




  b('<style type="text/css">
<!--
');
$file = 'm/'.$mod.'/lenta.css';
b(fread (fopen ($file, 'rb'), filesize ($file) ));
b('
-->
</style>
');


    // ---------------- inline JavaScript ---------------- //

  b('<script>');

  b('
var curr_id = '.$pref['lenta'].'
');

  b('</script>');

  b('<script type="text/javascript" src="/m/'.$mod.'/lenta_fluid.js"></script>');
  }




 // -------------------------------- update item -------------------------------- //

if ($act == 'upi') {
  $ajax = TRUE;

  $gid = getn('id');

  db_write(array('table'=>'lenta_pref', 'set'=>array('lenta'=>$gid), 'where'=>'`id` = 1'));
  }  // act == upi






 // -------------------------------- fluid lenta item -------------------------------- //

if ($act == 'fli') {
  $ajax = TRUE;

  $db_link_type = array(
    //1 => 'news',
    //2 => 'articles',
    3 => 'photo',
    4 => 'video',
    5 => 'onlines',
    6 => 'columns',
    );


  $gitm = getn('itm');
  $gitn = getn('itn');

  if ($gitm) {
    $new_id = $gitm;
    }

  elseif ($gitn) {

    $new_id = db_read(array('table' => 'lenta',
                            'col' => 'id',
                            'where' => '`id` > '.$gitn,
                            'order' => '`id`',
                            ));
    }


  $curr_new = db_read(array('table' => 'lenta',
                            'col' => array('id', 'date', 'time', 'link_type', 'link_date', 'link', 'desc', 'descf', 'stat'),
                            'where' => '`id` = '.$new_id,
                            ));

  $new_img = db_read(array('table' => 'lenta_img',
                           'col' => array('id', 'w', 'h'),
                           'where' => '`lenta` = '.$new_id,
                           'key' => 'id',
                           ));



  $lenta = db_read(array('table' => 'lenta',
                         'col' => array('id', 'time'),
                         'where' => '`date` = \''.$curr_new['date'].'\'',
                         'order' => '`time`',
                         'key' => 'id',
                         ));


  if ($curr_new) {
    b('<div class="caption"');
    if ($curr_new['link_type'] == 2)  b(' style="color: #848"');
    b('>');

    //if ($curr_new['link_type'] == 1 || $curr_new['link_type'] == 2) {
    if (!isset($db_link_type[$curr_new['link_type']])) {
      b(mb_convert_encoding($curr_new['desc'], 'UTF-8', 'CP1251'));
      }

    else {
      $de = explode('-', $curr_new['link_date']);
      b('<a href="https://href.li/?http://lenta.ru/'.$db_link_type[$curr_new['link_type']].'/'.$de[0].'/'.$de[1].'/'.$de[2].'/'.$curr_new['link'].'" target="_blank">');
      b(mb_convert_encoding($curr_new['desc'], 'UTF-8', 'CP1251'));
      b('</a>');
      }

    b('</div>');

    b('<div class="time">');
    b(dateh($curr_new['date']).' – '.$curr_new['time']);
    b('</div>');


    b('<div class="timeb">');

    $time_prev = 0;
    foreach ($lenta as $k=>$v) {
      $time = $v['time'][0] * 600 + $v['time'][1] * 60 + $v['time'][3] * 10 + $v['time'][4];
      $size = ($time - $time_prev) / 14.4;
      b('<div class="timebf" style="width: '.round($size, 2).'%;'.(($k == $new_id) ? ' box-shadow:inset -1px 0 0 #ffc;' : '').'"></div>');
      $time_prev = $time;
      }

    b('</div>');


    if (isset($db_link_type[$curr_new['link_type']])) {
      b('<div class="descf">');
      b($db_link_type[$curr_new['link_type']]);
      b('</div>');
      }

    if ($new_img) {
      foreach ($new_img as $ki=>$vi) {
        b('<div style="text-align: center;"><img src="/i/lenta_img,'.dechex($ki).'"></div>');
        //b('<div id="i'.dechex($ki).'" class="image" style="width: '.$vi['w'].'px;  height: '.$vi['h'].'px;"></div>');
        //$th_id[] = dechex($ki);
        }
      }

    b('<div class="descf">');
    if ($curr_new['descf'])  b(mb_convert_encoding(gzinflate($curr_new['descf']), 'UTF-8', 'CP1251'));
    b('</div>');
    }

  //echo  json_encode(array('curr'=>$body, 'next'=>$next_id, 'prev'=>$prev_id));
  echo  json_encode(array('div'=>$body, 'new_id'=>$new_id));
  $body = '';
  }  // act == fli






  // ---------------------------------------------------- lenta static, day --------------------------------------------------------- //

if ($act == 'lnd') {

  $pref = db_read(array('table' => 'lenta_pref',
                        'col' => array('id', 'lenta'),
                        'where' => '`id` = 1',
                        ));

  if (!$gdate) {
    $gdate = db_read(array('table' => 'lenta',
                           'col' => 'date',
                           'where' => '`id` = \''.$pref['lenta'].'\'',
                           ));
    }


  $news = db_read(array('table' => 'lenta',
                        'col' => array('id', 'time', 'link_type', 'desc', 'descf', 'stat'),
                        'where' => '`date` = \''.$gdate.'\'',
                        'order' => '`time`',
                        'key' => 'id',
                        ));


  $news_img = db_read(array('table' => array('lenta', 'lenta_img'),
                            'col' => array('lenta`.`id', 'lenta_img`.`id` AS `img_id', 'lenta_img`.`w', 'lenta_img`.`h'),
                            'where' => array('`lenta`.`id` = `lenta_img`.`lenta`',
                                             '`lenta`.`date` = \''.$gdate.'\'',
                                             ),
                            'key' => array('id', 'img_id'),
                            ));


    // ---- submenu ---- //

  $date_prev = datesql(mktime(0,0,0, datee($gdate,'m'), datee($gdate,'d')-1, datee($gdate) ));
  $date_next = datesql(mktime(0,0,0, datee($gdate,'m'), datee($gdate,'d')+1, datee($gdate) ));

  $submenu[datee($date_prev).'.'.datee($date_prev, 'M').'.'.datee($date_prev, 'D').';navigation-180-button'] = '/'.$mod.'/lnd/?date='.$date_prev;
  $submenu[datee($date_next).'.'.datee($date_next, 'M').'.'.datee($date_next, 'D').';navigation-000-button'] = '/'.$mod.'/lnd/?date='.$date_next;

  //$submenu['Календарь'] = '/'.$mod.'/cdr/;calendar-select';

  //$submenu['Собрать'] = '/'.$mod.'/parse/';
  //$submenu['Потоком'] = '/'.$mod.'/flu/';
  submenu();

    // ---- end: submenu ---- //




  b('<p class="h1">Lenta.ru ('.dateh($gdate).')</p>');
  b();


  $th_id = array();
  if ($news) {

    b('<style>
');
$file = 'm/'.$mod.'/lenta.css';
b(fread (fopen ($file, 'rb'), filesize ($file) ));
b('
</style>
');

    foreach ($news as $k=>$v) {

      b('<div id="'.$k.'" class="container">');

      b('<div class="caption">');
      //b($v['desc']);
      b(mb_convert_encoding($v['desc'], 'UTF-8', 'CP1251'));
      b('</div>');

      b('<div class="time">');
      b($v['time']);
      b('</div>');


      if (!$v['descf']) {
        b('<div class="descf">');
        b($db_link_type[$v['link_type']]);
        b('</div>');
        }

      if (isset($news_img[$k])) {
        foreach ($news_img[$k] as $ki=>$vi) {
          //b('<div style="text-align: center;"><img src="i.php?lenta_img,'.dechex($ki).'"></div>');
          b('<div id="i'.dechex($ki).'" class="image" style="width: '.$vi['w'].'px;  height: '.$vi['h'].'px;"></div>');
          $th_id[] = dechex($ki);
          }
        }

      b('<div class="descf">');
      //b($v['descf']);
      //b(mb_convert_encoding(gzinflate($v['descf']), 'UTF-8', 'CP1251'));
      if ($v['descf'])  b(mb_convert_encoding(gzinflate($v['descf']), 'UTF-8', 'CP1251'));
      b('</div>');

      b('</div>');
      }

    }

  else {
    b('<p class="p">Ошибка: данные отсутствуют.');
    }


    // ---------------- inline JavaScript ---------------- //

  b('<script>');

//var curr_id = '.( ($gdate == $pref['last_date']) ? $pref['lenta'] : 0 ).'
  b('
var curr_id = '. $pref['lenta'].'
var th_id = "'.implode('-',$th_id).'";
');

  b('</script>');

  b('<script type="text/javascript" src="/m/'.$mod.'/lenta.js"></script>');
  }






  // ---------------------------------------------------- parse --------------------------------------------------------- //

if ($act == 'parse') {

  include 'l/lib_parse.php';


  $socket = fsockopen('lenta.ru', 80, $errno, $errstr, 10);


  if (!$socket) {
    //
    }

  else {
    $date_scan = $curr['time'] - 3600;
    $date_scanc = date('Y-m-d', $date_scan);

    $send  = '';
    $send .= 'GET /'.date('Y/m/d', $date_scan).'/ HTTP/1.1'."\r\n";  //  '2014/06/17'
    $send .= 'Host: lenta.ru'."\r\n";
    $send .= 'User-Agent: lenta.ru spider'."\r\n";
    $send .= 'Connection: close'."\r\n";
    $send .= "\r\n";


    $result = fwrite($socket, $send);

    $receive = '';
    while (!feof($socket)) {
      $receive .= fread($socket, 8192);
      }

    fclose($socket);


    //fwrite (fopen ('t/'.$mod.'/ln/ln '.time(), 'wb'), $receive);
    //clearstatcache();

    http_dechunk($receive);

    //$file = 't/'.$mod.'/'.'Lenta.ru.htm';
    //$receive = fread (fopen ($file, 'rb'), filesize ($file) );

    $skip = 0;

    b('<table class="f10">');

    //$watch_dog = 100;
    while (pos_after_gl('<div class=') !== FALSE) {

      $div_class = cut_between_gl('"', '"');

      if ($div_class == 'item' || $div_class == 'article item') {

        $div_end = pos_div_end($receive, $skip);
        $new = substr($receive, $skip, $div_end-$skip);
        $skip = $div_end;
        $sk = 0;

        pos_after($new, 'span class="time"', $sk);
        $item_time = cut_between($new, '>', '<', $sk);

        pos_after($new, '<div class="titles', $sk);

        pos_after($new, '<a', $sk);
        pos_after($new, 'href=', $sk);
        $item_link = cut_between($new, '"', '"', $sk);
        $item_link = explode('/', $item_link);
        //$item_link = $item_link[count($item_link) -2];
        //$item_ltype = isset($db_link_type[$item_link[1]]) ? $db_link_type[$item_link[1]] : 0;
        $item_ltype = in_array($item_link[1], $db_link_type) ? array_search($item_link[1], $db_link_type) : 0;
        $item_ldate = $item_link[2].'-'.$item_link[3].'-'.$item_link[4];
        $item_link = $item_link[5];


        $item_desc = cut_between($new, '>', '</a', $sk);
        $item_desc = trim(strip_tags(strtr($item_desc, array('&nbsp;'=>' '))));
        //if ($div_class == 'article item')  $item_desc = '[!] '.$item_desc;


        b('<tr>');
        b('<td class="li" width="60">'.$item_time);
        b('<td class="li" width="160">'.$item_link);
        b('<td class="li" width="600">'.$item_desc);
        //b('<td class="li" width="600">'.htmlentities($item_desc));
        //b('<td class="li" width="1300">'.htmlentities($new));


          // ---------------- check DB ---------------- //

        $check = db_read(array('table' => 'lenta',
                               'col' => 'id',
                               'where' => array('`date` = \''.$date_scanc.'\'',
                                                '`link` = \''.$item_link.'\'',
                                                ),
                               ));

        if (!$check) {
          //$new++;
          //$new_total++;

            // ---------------- write DB ---------------- //

          $set = array();
          $set['date'] = $date_scanc;
          $set['time'] = $item_time;
          $set['link_type'] = $item_ltype;
          $set['link_date'] = $item_ldate;
          $set['link'] = $item_link;
          $set['desc'] = mb_convert_encoding($item_desc, 'CP1251', 'UTF-8');
          $set['descf'] = '';
          $set['stat'] = ( ($item_ltype == 1 || $item_ltype == 2) ? 0 : 9);

          db_write(array('table'=>'lenta', 'set'=>$set));
//      $desc = mb_convert_encoding($v['desc_u'], 'CP1251', 'UTF-8');
//      $descf = gzdeflate(mb_convert_encoding($v['descf_u'], 'CP1251', 'UTF-8'), 9);
//      $descf = gzdeflate($descf, 9);
          }

        }  // right div

      }  // while

    b('</table>');

    //if (!$watch_dog--)  break;
    }  // end: receive



    // -------------------------- grab details -------------------------- //

  $news = db_read(array('table' => 'lenta',
                        'col' => array('id', 'date', 'link_type', 'link_date', 'link'),
                        'where' => '`stat` = 0',
                        //'limit' => 1,

                        'key' => 'id',
                        ));


  foreach ($news as $k=>$v) {
    sleep(2);


    $socket = fsockopen('lenta.ru', 80, $errno, $errstr, 10);

    if (!$socket) {
      //
      }

    else {
      $de = explode('-', $v['link_date']);

      $send  = '';
      $send .= 'GET /'.$db_link_type[$v['link_type']].'/'.$de[0].'/'.$de[1].'/'.$de[2].'/'.$v['link'].'/ HTTP/1.1'."\r\n";  //  http://lenta.ru/news/2014/06/17/medvedev/
      //$send .= 'GET /news/'.$v['link'].'/ HTTP/1.1'."\r\n";  //  http://lenta.ru/news/2014/06/17/medvedev/
      $send .= 'Host: lenta.ru'."\r\n";
      $send .= 'User-Agent: lenta.ru details spider'."\r\n";
      $send .= 'Connection: close'."\r\n";
      $send .= "\r\n";


      $result = fwrite($socket, $send);

      $receive = '';
      while (!feof($socket)) {
        $receive .= fread($socket, 8192);
        }

      fclose($socket);


      //fwrite (fopen ('m/'.$mod.'/lnd/lnd '.time(), 'wb'), $receive);
      //clearstatcache();

      $request_code = substr($receive, 9,3);
      if ($request_code != '200') {
        $set = array();
        $set['descf'] = gzdeflate(mb_convert_encoding($request_code, 'CP1251', 'UTF-8'), 9);
        $set['stat'] = 2;

        db_write(array('table'=>'lenta', 'set'=>$set, 'where'=>'`id` = '.$k));

        continue;
        }

      http_dechunk($receive);

      //fwrite (fopen ('m/'.$mod.'/lnd/lnd '.time(), 'wb'), $receive);
      //clearstatcache();



        // -------------------------------- parser for `news` -------------------------------- //

      if ($v['link_type'] == 1) {

        $skip = 0;
        //if (pos_after_gl('itemprop="articleBody"') !== FALSE) {
        if (pos_after_gl('<div class="b-topic__title') !== FALSE) {

          $fi = cut_between_gl('>', 'itemprop="articleBody">');
          $sfi = 0;
          $item_img = '';
          if (pos_after($fi, '<img', $sfi)) {
            pos_after($fi, 'src=', $sfi);
            $item_img = cut_between($fi, '"', '"', $sfi);
            }

          //pos_after_gl('itemprop="articleBody">');

          $div_end = pos_div_end($receive, $skip);
          $new = substr($receive, $skip, $div_end-$skip);
          $skip = $div_end;


            // ---- strip материалы по теме ---- //

          $sk = TRUE;
          while ($sk) {
            $sk = 0;
            $strb = '<aside';
            if (pos_after($new, $strb, $sk) !== FALSE) {
              $skb = $sk;
              $ske = pos_after($new, '</aside>', $sk);

              //$new = substr($new, 0, $skb-6) . substr($new, $ske);
              $new = substr($new, 0, $skb-strlen($strb)) .'&lt;aside&gt;'. substr($new, $ske);
              }
            }


            // ---- strip iframe ---- //

          $sk = TRUE;
          while ($sk) {
            $sk = 0;
            $strb = '<iframe';
            if (pos_after($new, $strb, $sk) !== FALSE) {
              $skb = $sk;
              $ske = pos_after($new, '</iframe>', $sk);

              $new = substr($new, 0, $skb-strlen($strb)) .'&lt;iframe&gt;'. substr($new, $ske);
              }
            }


            // ---- strip img ---- //

          $sk = TRUE;
          while ($sk) {
            $sk = 0;
            $strb = '<img';
            if (pos_after($new, $strb, $sk) !== FALSE) {
              $skb = $sk;
              $ske = pos_after($new, '>', $sk);

              $new = substr($new, 0, $skb-strlen($strb)) .'&lt;img&gt;'. substr($new, $ske);
              }
            }


            // ---- strip instagram ---- //

          $sk = TRUE;
          while ($sk) {
            $sk = 0;
            $strb = '<blockquote class="instagram';
            if (pos_after($new, $strb, $sk) !== FALSE) {
              $skb = $sk;
              $ske = pos_after($new, '</blockquote>', $sk);

              $new = substr($new, 0, $skb-strlen($strb)) .'&lt;instagram&gt;'. substr($new, $ske);
              }
            }


            // ---- strip script ---- //

          $sk = TRUE;
          while ($sk) {
            $sk = 0;
            $strb = '<script';
            if (pos_after($new, $strb, $sk) !== FALSE) {
              $skb = $sk;
              $ske = pos_after($new, '</script>', $sk);

              $new = substr($new, 0, $skb-strlen($strb)) .'&lt;script&gt;'. substr($new, $ske);
              }
            }



            // ---- update ---- //

          $set = array();
          //$set['descf'] = mb_substr(utf_trim_bmp($new),0,21000);
          $set['descf'] = gzdeflate(substr(mb_convert_encoding($new, 'CP1251', 'UTF-8'),0,32000), 9);
          $set['stat'] = 1;

          db_write(array('table'=>'lenta', 'set'=>$set, 'where'=>'`id` = '.$k));
          }
        }



        // -------------------------------- parser for `articles` -------------------------------- //

      elseif ($v['link_type'] == 2) {

        $skip = 0;
        if (pos_after_gl('<h1 class="b-topic__title') !== FALSE) {

          $fi = cut_between_gl('>', 'itemprop="articleBody">');
          $sfi = 0;
          $item_img = '';
          if (pos_after($fi, '<img', $sfi)) {
            pos_after($fi, 'src=', $sfi);
            $item_img = cut_between($fi, '"', '"', $sfi);
            }

          //pos_after_gl('itemprop="articleBody">');

          $div_end = pos_div_end($receive, $skip);
          $new = substr($receive, $skip, $div_end-$skip);
          $skip = $div_end;


            // ---- strip материалы по теме ---- //

          $sk = TRUE;
          while ($sk) {
            $sk = 0;
            $strb = '<aside';
            if (pos_after($new, $strb, $sk) !== FALSE) {
              $skb = $sk;
              $ske = pos_after($new, '</aside>', $sk);

              //$new = substr($new, 0, $skb-6) . substr($new, $ske);
              $new = substr($new, 0, $skb-strlen($strb)) .'&lt;aside&gt;'. substr($new, $ske);
              }
            }


            // ---- strip iframe ---- //

          $sk = TRUE;
          while ($sk) {
            $sk = 0;
            $strb = '<iframe';
            if (pos_after($new, $strb, $sk) !== FALSE) {
              $skb = $sk;
              $ske = pos_after($new, '</iframe>', $sk);

              $new = substr($new, 0, $skb-strlen($strb)) .'&lt;iframe&gt;'. substr($new, $ske);
              }
            }


            // ---- strip img ---- //

          $sk = TRUE;
          while ($sk) {
            $sk = 0;
            $strb = '<img';
            if (pos_after($new, $strb, $sk) !== FALSE) {
              $skb = $sk;
              $ske = pos_after($new, '>', $sk);

              $new = substr($new, 0, $skb-strlen($strb)) .'&lt;img&gt;'. substr($new, $ske);
              }
            }


            // ---- strip instagram ---- //

          $sk = TRUE;
          while ($sk) {
            $sk = 0;
            $strb = '<blockquote class="instagram';
            if (pos_after($new, $strb, $sk) !== FALSE) {
              $skb = $sk;
              $ske = pos_after($new, '</blockquote>', $sk);

              $new = substr($new, 0, $skb-strlen($strb)) .'&lt;instagram&gt;'. substr($new, $ske);
              }
            }


            // ---- strip script ---- //

          $sk = TRUE;
          while ($sk) {
            $sk = 0;
            $strb = '<script';
            if (pos_after($new, $strb, $sk) !== FALSE) {
              $skb = $sk;
              $ske = pos_after($new, '</script>', $sk);

              $new = substr($new, 0, $skb-strlen($strb)) .'&lt;script&gt;'. substr($new, $ske);
              }
            }



            // ---- update ---- //

          $set = array();
          //$set['descf'] = mb_substr(utf_trim_bmp($new),0,21000);
          $set['descf'] = gzdeflate(substr(mb_convert_encoding($new, 'CP1251', 'UTF-8'),0,32000), 9);
          $set['stat'] = 1;

          db_write(array('table'=>'lenta', 'set'=>$set, 'where'=>'`id` = '.$k));
          }

        }

      }  // socket




      // ---------------- grab image ---------------- //

    if ($item_img) {
      // http://icdn.lenta.ru/images/2014/06/21/17/20140621174550373/pic_4bd7a3f4d725107585f04f23e88b37f8.jpg
    $item_img_e = explode('/', $item_img);
    array_shift($item_img_e);
    array_shift($item_img_e);
    $host = array_shift($item_img_e);
    $item_img = implode('/', $item_img_e);

    $socket = fsockopen($host, 80, $errno, $errstr, 10);

    if (!$socket) {
      //
      }

    else {
      $send  = '';
      $send .= 'GET /'.$item_img.' HTTP/1.1'."\r\n";  //  http://lenta.ru/news/2014/06/17/medvedev/
      $send .= 'Host: '.$host."\r\n";
      $send .= 'User-Agent: lenta.ru details spider'."\r\n";
      $send .= 'Connection: close'."\r\n";
      $send .= "\r\n";


      $result = fwrite($socket, $send);

      $receive = '';
      while (!feof($socket)) {
        $receive .= fread($socket, 8192);
        }

      fclose($socket);


      //fwrite (fopen ('t/'.$mod.'/lni/lni '.time(), 'wb'), $receive);
      //clearstatcache();

      $request_code = substr($receive, 9,3);
      if ($request_code != '200') {
        //$set = array();
        //$set['descf'] = $request_code;
        //$set['stat'] = 2;

        //db_write(array('table'=>'lenta', 'set'=>$set, 'where'=>'`id` = '.$k));

        continue;
        }

      http_dechunk($receive);


      $image = imagecreatefromstring($receive);

      $img_w = imagesx($image);
      $img_h = imagesy($image);


        // -------- make thumb -------- //

      $imaget = imagecreatetruecolor (128, 96);
      imagecopyresampled ($imaget, $image, 0, 0, 0, 0, 128, 96, $img_w, $img_h);


        // -------- shrink -------- //

      $size_w = $img_w;
      $size_h = $img_h;
      $req_w = 500;  $req_h = 500;
      $resize = FALSE;

      if ($size_h > $req_h) {
        $size_h = $req_h;
        $size_w = ceil($img_w * ($req_h / $img_h));
        $resize = TRUE;
        }

      if ($size_w > $req_w) {
        $size_w = $req_w;
        $size_h = ceil($img_h * ($req_w / $img_w));
        $resize = TRUE;
        }

      if ($resize) {
        $img_n = imagecreatetruecolor ($size_w, $size_h);
        imagecopyresampled ($img_n, $image, 0, 0, 0, 0, $size_w, $size_h, $img_w, $img_h);
        $image = $img_n;
        }


      $set = array();
      $set['lenta'] = $k;
      $set['w'] = $size_w;
      $set['h'] = $size_h;
      $glni = db_write(array('table'=>'lenta_img', 'set'=>$set));

      $file = img_upload_fdb ('lenta_img', $glni, chr(255).chr(216));
      imagejpeg($image, $file, 50);


      $file = img_upload_fdb ('lenta_img_t', $glni, chr(255).chr(216));
      imagejpeg($imaget, $file, 50);
      }}  // socket img

    }  // foreach


  }




  // -------------------------- find multiple pictures -------------------------- //
/*
if ($act == 'fmp') {

  $offset = 0;
  $limit = 5000;

  css_table(array(50, 100));
  b('<table class="lst f10">');
  b('<tr>');
  b('<td>id');
  b('<td>count');
  while (1) {
    $lenta = db_read(array('table' => 'lenta',
                           'col' => 'id',
                           //'where' => '`id` = '.$new_id,
                           'limit' => $offset.', '.$limit,
                           'order' => '`id`',

                           'key' => 'id',
                           //'verbose' => 1,
                           ));
    if (!$lenta)  break;


    foreach($lenta as $k=>$v) {

      $count = db_read(array('table' => 'lenta_img',
                             'col' => array('id', '!COUNT(*)'),
                             'where' => '`lenta` = '.$k,
                             ));

      if ($count['COUNT(*)'] > 1) {
        b('<tr>');
        b('<td>');
        b($k);

        b('<td>');
        b($count['COUNT(*)']);
        }

      }

    $offset += $limit;
    }

  b('</table>');
  }
*/



  // -------------------------- delete multiple pictures -------------------------- //
/*
if ($act == 'dmp') {
  $lenta_id = 26494;
  //$save = '6b9b';

  if (isset($save)) {
    db_write(array('table' => 'lenta_img',
                   'where' => array('`lenta` = '.$lenta_id,
                                    '`id` != '.hexdec($save),
                                    ),
                   ));
    }

  $new_img = db_read(array('table' => 'lenta_img',
                           'col' => array('id', 'w', 'h'),
                           'where' => '`lenta` = '.$lenta_id,
                           'order' => '`id`',
                           'key' => 'id',
                           ));


  foreach ($new_img as $ki=>$vi) {
    b('<div style="text-align: center;"><img src="/i/lenta_img,'.dechex($ki).'"></div>');
    }

  }
*/


?>