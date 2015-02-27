<?php

/************************************************************************/
/*  functions & constants v2.oo                                         */
/************************************************************************/


if (!isset($body))  die ('error: this is a pluggable module and cannot be used standalone.');


mb_internal_encoding('UTF-8');

include 'c/mcache.php';

include 'l/const.php';
include 'l/lib_date.php';
include 'l/lib_db.php';
include 'l/lib_debug.php';
include 'l/lib_img.php';
include 'l/lib_str.php';
include 'l/lib_if.php';


?>