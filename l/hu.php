<?php

/************************************************************************/
/*  hard users  v1.oo                                                   */
/************************************************************************/


if (!isset($body))  die ('error: this is a pluggable module and cannot be used standalone.');


$harduser = array();

$harduser[1] = array('login' => 'admin',
                     'pass'  => 'password',
                     'perm'  => 'all',
                     'desc'  => 'Администратор',
                     );


?>