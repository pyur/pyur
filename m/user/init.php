<?php

/************************************************************************/
/*  Пользователи  v1.oo                                                 */
/************************************************************************/


if (!isset($body))  die ('error: this is a pluggable module and cannot be used standalone.');



$module['name'] = 'Пользователи';
//$module['nameb'] = 'Пользователи';

$module['perm'] = array('edit'       => 'редактирование профилей',
                        'add'        => 'добавление нового профиля',

                        'edit_login' => 'редактирование логина и пароля',

                        'edit_cat'   => 'ред. кат. доступа',
                        );


?>