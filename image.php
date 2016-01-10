<?php

/************************************************************************/
/*  image  v2.oo                                                        */
/************************************************************************/



  // -------------------------------- init -------------------------------- //

$body = '';
$redirect = '';

include 'l/lib.php';
db_open();



  // ---------------- parse request URI ---------------- //

$uri_e = explode('/', $_SERVER['REQUEST_URI']);

$mod = 'default';



  // ---------------- authorization ---------------- //

include 'auth.php';



  // ---------------- parse request URI ---------------- //

if (count($uri_e) == 3) {
  $q = explode(',', $uri_e[2]);

  if (count($q) == 2) {
    $db = filter($q[0], array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z','0','1','2','3','4','5','6','7','8','9','_','-'));
    $id = hexdec(filter_h($q[1]));
    $fname = '';

    if ($db == 'bill_doc' && p(array('bill'))) {
      //$bill = db_select(array('from' => 'bill',
      //                        'col' => array('id', 'desc'),
      //                        'where' => '`id` = '.$id,
      //                        ));
      $fname = 'Чек '.$id;
      }


    $img = img_get_fdb($db, $id);

    if (ob_get_level())  { ob_end_clean(); }
    header('Content-Type: '.$img['mime']);
    header('Content-Length: '.strlen($img['data']));
    $fname = mb_convert_encoding($fname, 'Windows-1251', 'UTF-8');
    // //header('Content-Disposition: attachment; filename="'.$fname.'"');
    header('Content-Disposition: filename="'.$fname.'"');
    echo $img['data'];
    }
  }


?>