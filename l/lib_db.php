<?php

/************************************************************************/
/*  database functions  v2.oo                                           */
/************************************************************************/


if (!isset($body))  die ('error: this is a pluggable module and cannot be used standalone.');



  // ---- MySQL connectivity -------------------------------------------------------------------------- //

  // ---------------- open ---------------- //

function db_open($db = FALSE, $user = FALSE, $password = FALSE) {
  global $db_link;
  if (!isset($db_link))  $db_link = array();

  if (!$db) {
      // -------- default db connection -------- //
    $name = '@';
    $driver = 'mysqli';
    $db = 'pyur';
    $user = 'pyur';
    $password = 'password';
    }

  else {
    $db_e = explode(':', $db);
    if (count($db_e) == 1) {
      $name = $db;
      $driver = 'mysqli';
      }
    else {
      $name = $db = $db_e[1];
      $driver = $db_e[0];
      }
    }

  if ($driver == 'mysqli') {
    $link = mysqli_connect('127.0.0.1', $user, $password, $db);
    mysqli_query($link, 'SET CHARACTER SET utf8');
    }

  elseif ($driver == 'sqlite') {
    //$link = new PDO('sqlite:db/db');
    $link = new SQLite3('d/'.$db); 
    }

  elseif ($driver == 'postgres') {
    }

  $db_link[$name] = array($driver, $link);
  }



  // ---------------- close ---------------- //

function db_close($db = '@') {
  global $db_link;

  if ($db_link[$db][0] == 'mysqli') {
    mysqli_close($db_link[$db][1]);
    }

  elseif ($db_link[$db][0] == 'sqlite') {
    $db_link[$db][1]->close();
    }
  }






  // -------------------------------- MySQL - SELECT v3 (path) -------------------------------- //

function db_read_path ($r, $p, $db, $value) {

  if ($p) {
    $ret_k = $db[array_shift ($p)];
    if (!isset($r[$ret_k]))  $r[$ret_k] = array();

    $r[$ret_k] = db_read_path ($r[$ret_k], $p, $db, $value);

    return  $r;
    }


  else {
    $r = array();

    if (!$value) {
    //if ($value === NULL) {   // !
      foreach ($db as $k=>$v)  $r[$k] = $v;
      }

    //elseif ($value !== '') { // !
    //  $r = $db[$value];      // !
    //  }                      // !
    else {
      $r = $db[$value];
      //$r = '';               // !
      }

    return  $r;
    }

  }




  // -------------------------------- MySQL - SELECT v3 -------------------------------- //

function  db_read ($q) {
  global $db_link;

  if (!isset($q['db']))       $q['db'] = '@';
  if (!isset($q['table']) || !$q['table'])    die('error: `table` not specified.');
  if (!isset($q['col']))      $q['col'] = '';
  if (!isset($q['where']))    $q['where'] = '';
  //if (!isset($q['group']))    $q['group'] = '';
  if (!isset($q['order']))    $q['order'] = '';
  if (!isset($q['limit']))    $q['limit'] = '';

  if (!isset($q['key']))      $q['key'] = NULL;
  if (!isset($q['value']))    $q['value'] = NULL;  // can be (string) or '', arrays not allowed (yet)
  if (!isset($q['verbose']))  $q['verbose'] = '';

  //if (!isset($q['format']))   $q['format'] = NULL;  // return:  NULL - multiple,  1 - one first value,  2 - raw resource object
  //if (!isset($q['raw']))      $q['raw'] = '';


  $query  = 'SELECT ';


  if (!is_array($q['col']))  $q['col'] = array($q['col']);
  $col = array();
  foreach ($q['col'] as $v) {
    if     ($v == '')      $col[] = 'COUNT(*)';
    elseif ($v[0] == '!')  $col[] = addslashes(substr($v, 1));
    elseif ($v[0] == '#')  $col[] = 'HEX(`'.addslashes(substr($v, 1)).'`) AS `'.addslashes($v).'`';
    elseif ($v[0] == '@')  $col[] = 'INET_NTOA(`'.addslashes(substr($v, 1)).'`) AS `'.addslashes($v).'`';
    else                   $col[] = addslashes('`'.$v.'`');
    }
  $query .= implode(', ', $col);


  $query .= ' FROM ';
  if (!is_array($q['table']))   $q['table'] = array($q['table']);
  $table = array();
  foreach ($q['table'] as $v)  $table[] = '`'.addslashes($v).'`';
  $query .= implode(', ', $table);


  if ($q['where']) {
    if (!is_array($q['where']))   $q['where'] = array($q['where']);
    $query .= ' WHERE '.implode(' AND ', $q['where']);
    }


  //if ($q['group']) {
  //  $query .= ' GROUP BY '.$q['group'];
  //  }


  if ($q['order']) {
    if (!is_array($q['order']))   $q['order'] = array($q['order']);
    $query .= ' ORDER BY '.implode(', ', $q['order']);
    }


  if (!$q['key']) {
    $query .= ' LIMIT 1';
    }
  elseif ($q['limit']) {
    $query .= ' LIMIT '.$q['limit'];
    }


  if ($q['verbose'])  message_window('SQL', $query);


  $r = FALSE;

    // ---------------- mysqli ---------------- //

  if ($db_link[$q['db']][0] == 'mysqli') {
    $link = $db_link[$q['db']][1];

    $result = mysqli_query($link, $query);

    if (mysqli_error($link))  message_window('SQL error', mysqli_error($link));


    if (mysqli_num_rows($result)) {

        // ---- single ---- //
      if (!$q['key']) {
        $r = mysqli_fetch_assoc($result);
        if (count($q['col']) == 1)  $r = implode($r);
        }

        // ---- multiple ---- //
      else {
        if (!is_array($q['key']))  $q['key'] = array($q['key']);
        //if (!is_array($q['value']))  $col = $q['col'];  else  $col = '';

        $r = array();
        $n = 0;
        $l0 = array_shift ($q['key']);
        while ($row = mysqli_fetch_assoc($result)) {
          if ($l0)  $n = $row[$l0];
          if (!isset($r[$n]))  $r[$n] = array();

          $r[$n] = db_read_path ($r[$n], $q['key'], $row, $q['value']);

          $n++;
          }
        }

      }

      // ---- empty result ---- //
    else {
      if (!$q['key'] && count($q['col']) == 1)  $r = '';
      else  $r = array();
      }
    }  // driver: mysqli



    // ---------------- sqlite ---------------- //

  if ($db_link[$q['db']][0] == 'sqlite') {
    $link = $db_link[$q['db']][1];

    //$result = mysqli_query($link, $query);
    $result = $link->query($query);

    if ($result === FALSE)  message_window('SQL error', $link->lastErrorMsg());


    if ($result) {

        // ---- single ---- //
      if (!$q['key']) {
        //$r = mysqli_fetch_assoc($result);
        $r = $result->fetchArray(SQLITE3_ASSOC);
        if (count($q['col']) == 1 && is_array($r))  $r = implode($r);
        }

        // ---- multiple ---- //
      else {
        if (!is_array($q['key']))  $q['key'] = array($q['key']);

        $r = array();
        $n = 0;
        $l0 = array_shift ($q['key']);
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
          if ($l0)  $n = $row[$l0];
          if (!isset($r[$n]))  $r[$n] = array();

          $r[$n] = db_read_path ($r[$n], $q['key'], $row, $q['value']);

          $n++;
          }
        }

      }

      // ---- empty result ---- //
    else {
      if (!$q['key'] && count($q['col']) == 1)  $r = '';
      else  $r = array();
      }
    }  // driver: sqlite


  return  $r;
  }






  // -------------------------------- MySQL - INSERT, UPDATE, DELETE v2 -------------------------------- //

function  db_write ($q) {
  global $db_link;

  if (!isset($q['db']))       $q['db'] = '@';
  if (!isset($q['table']))    $q['table'] = FALSE;
  if (!isset($q['set']))      $q['set'] = FALSE;    // INSERT, UPDATE, ------
  if (!isset($q['where']))    $q['where'] = FALSE;  // ------, UPDATE, DELETE

  if (!isset($q['verbose']))  $q['verbose'] = FALSE;


  $r = FALSE;

  if     ($q['set'] && !$q['where'])  $query  = 'INSERT INTO ';
  elseif ($q['set'] && $q['where'])   $query  = 'UPDATE ';
  elseif (!$q['set'] && $q['where'])  $query  = 'DELETE FROM ';
  else  die('error: no `set` either `where`');


  if ($q['table']) {
    $query .= '`'.addslashes($q['table']).'`';
    }


  if ($q['set']) {
    if (!$q['where']) {
      $into = array();
      $values = array();
      foreach ($q['set'] as $k=>$v) {
        if     ($k[0] == '!') { $into[] = '`'.substr($k,1).'`';  $values[] = addslashes($v); }
        elseif ($k[0] == '#') { $into[] = '`'.substr($k,1).'`';  $values[] = 'UNHEX(\''.addslashes($v).'\')'; }
        elseif ($k[0] == '@') { $into[] = '`'.substr($k,1).'`';  $values[] = 'INET_ATON(\''.addslashes($v).'\')'; }
        else                  { $into[] = '`'.$k.'`';  $values[] = '\''.addslashes($v).'\''; }
        }
      $query .= '('.implode(', ', $into).')';
      $query .= ' VALUES ('.implode(', ', $values).')';
      }

    else {
      $query .= ' SET ';
      $set = array();
      foreach ($q['set'] as $k=>$v) {
        if     ($k[0] == '!')  $set[] = '`'.substr($k,1).'` = '.addslashes($v);
        elseif ($k[0] == '#')  $set[] = '`'.substr($k,1).'` = UNHEX(\''.addslashes($v).'\')';
        elseif ($k[0] == '@')  $set[] = '`'.substr($k,1).'` = INET_ATON(\''.addslashes($v).'\')';
        else                   $set[] = '`'.$k.'` = \''.addslashes($v).'\'';
        }
      $query .= implode(', ', $set);
      }
    }


  if ($q['where']) {
    if (is_array($q['where']))  $query .= ' WHERE '.implode(' AND ', $q['where']);
    else                        $query .= ' WHERE '.$q['where'];
    }


  if ($q['verbose'])  message_window('SQL', $query);

    // ---------------- mysqli ---------------- //

  if ($db_link[$q['db']][0] == 'mysqli') {
    $link = $db_link[$q['db']][1];
    mysqli_query($link, $query);
    if (mysqli_error($link))  message_window('SQL error', mysqli_error($link));


    if     ($q['set'] && !$q['where'])  $r = mysqli_insert_id($link);
    elseif ($q['set'] && $q['where'])   $r = mysqli_affected_rows($link);
    elseif (!$q['set'] && $q['where'])  $r = mysqli_affected_rows($link);
    }  // driver: mysqli


    // ---------------- sqlite ---------------- //

  if ($db_link[$q['db']][0] == 'sqlite') {
    $link = $db_link[$q['db']][1];
    //mysqli_query($link, $query);
    $result = $link->exec($query);
    //if (mysqli_error($link))  message_window('SQL error', mysqli_error($link));
    if ($result === FALSE)  message_window('SQL error', $link->lastErrorMsg());


    if     ($q['set'] && !$q['where'])  $r = $link->lastInsertRowID();  // mysqli_insert_id($link);
    elseif ($q['set'] && $q['where'])   $r = $link->changes();  // mysqli_affected_rows($link);
    elseif (!$q['set'] && $q['where'])  $r = $link->changes();  // mysqli_affected_rows($link);
    }  // driver: sqlite


  return  $r;
  }




  // -------------------------------- Tree sort v2 -------------------------------- //

            //  ($db_array, $column, $gid, $enum);
function  tsort ($a, $c = FALSE, $id = FALSE, $db = FALSE) {
  $pid = 0;
  $s = array();
  if ($id !== FALSE)  $s[0] = '- - в начало - -';
  while(isset($a[$pid])) {
    if ($id === FALSE)  $s[$a[$pid]['id']] = $a[$pid];
    else  $s[$a[$pid]['id']] = ($db ? $db[$a[$pid][$c]] : $a[$pid][$c]);
    $pid = $a[$pid]['id'];
    }
  if ($id)  unset($s[$id]);
  return $s;
  }




  // ---------------- inet_aton ---------------- //

function  inet_aton ($addr) {
  $e = explode('.', $addr);
  return  ($e[0] * 16777216) + ($e[1] * 65536) + ($e[2] * 256) + $e[3];
  }


?>