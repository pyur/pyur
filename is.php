<?php

/************************************************************************/
/*  img_stripe  v1.oo                                                   */
/************************************************************************/


  // ---- init --------------------------------------------------------------------------------- //

$body = '';
$redirect = '';

include 'l/lib.php';
db_open();


  // ---- auth --------------------------------------------------------------------------------- //

//include 'auth.php';




  // ---- get --------------------------------------------------------------------------------- //

if (isset($_SERVER['QUERY_STRING'])) {
  $q = explode(',', $_SERVER['QUERY_STRING']);

  if (count($q == 2)) {
    $filter = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z','_','-');
    $db = filter($q[0], $filter);

    $filter = array('0','1','2','3','4','5','6','7','8','9', 'A','B','C','D','E','F', 'a','b','c','d','e','f', '-');
    $ids = explode('-', filter($q[1], $filter));

    $tw = 128;  $th = 96;
    $stripe = imagecreatetruecolor ($tw, $th * count($ids));

    foreach ($ids as $k=>$v) {

      $id = hexdec($v);

      $img = img_get_fdb($db, $id);
      $img = imagecreatefromstring($img['data']);
      // imagecopy ( resource $dst_im , resource $src_im , int $dst_x , int $dst_y , int $src_x , int $src_y , int $src_w , int $src_h )
      imagecopy ($stripe, $img, 0,($th * $k), 0,0, imagesx($img), imagesy($img));
      }

    header('Content-Type: image/jpeg');
    echo  imagejpeg($stripe, NULL, 50);
    }
  }


?>