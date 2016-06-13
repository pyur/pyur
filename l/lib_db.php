<?php

/************************************************************************/
/*  database functions  v4.oo                                           */
/************************************************************************/


if (!isset($body))  die ('error: this is a pluggable module and cannot be used standalone.');

/* ---- usage examples ----

  $db = new dbMySQL();
  $db2 = new dbMySQL('database', 'user', 'password', 8306);
  $db3 = new dbSQLite('database');

  $elec = $db
    ->table('elec')
    ->col('id', 'dt', 'val', 'desc')
    ->where('`dt` > ?')
    ->wa('2016-02-27 00:00:00')
    ->order('`id` DESC')
    ->key('id')
    ->r();

  $second = $db2
    ->table('second')
    ->col('id', 'a', 'b')
    ->key('id')
    ->r();

    // ---- count ---- //
  $elec = $db
    ->table('elec')
    ->where('`dt` >= ?', '`dt` <= ?')
    ->wa('2016-02-01 00:00:00', '2016-02-15 23:59:59')
    ->r();



    // ---- insert ---- //
  $second = $db2
    ->table('second')
    ->set(array('a' => rand(1,10), 'b'=>str_repeat(chr(rand(97,122)), rand(2,5)) ))
    ->i(1);

  $second = $db2
    ->table('second')
    ->set(array('id' => 3, 'a' => rand(1,10), 'b'=>str_repeat(chr(rand(97,122)), rand(2,5)) ))
    ->i(1);


    // ---- update ---- //
  $second = $db2
    ->table('second')
    ->set(array('a' => rand(1,10), 'b'=>str_repeat(chr(rand(97,122)), rand(2,5)) ))
    ->where('`id` = ?')
    ->wa(3)
    ->u(1);


    // ---- delete ---- //
  $second = $db2
    ->table('second')
    ->where('`id` = ?')
    ->wa(3)
    ->d(1);

*/



//$db = new dbMySQL('pyur', 'user', 'password');
$db = new dbSQLite('pyur.sqlite3');


  // ---- MySQL connectivity -------------------------------------------------------------------------- //

class dbMySQL {

  private $link;

  private $db;
  private $user;
  private $password;
  private $port = 3306;

  private $table = FALSE;
  private $col = FALSE;
  private $where = FALSE;
  private $wa = FALSE;
  private $group = FALSE;
  private $order = FALSE;
  private $limit = FALSE;
  private $key = FALSE;
  private $value = FALSE;

  private $set = FALSE;



    // ---------------- constructor ---------------- //

  function __construct($db = FALSE, $user = FALSE, $password = FALSE, $port = FALSE) {
    if ($db === FALSE)  die('error: you must specify `db`');
    if ($user === FALSE)  die('error: you must specify `user`');
    if ($password === FALSE)  die('error: you must specify `password`');

    $this->db = $db;
    $this->user = $user;
    $this->password = $password;

    if ($port !== FALSE)  $this->port = $port;

    $this->link = new mysqli('127.0.0.1', $this->user, $this->password, $this->db, $this->port);
    $this->link->query('SET CHARACTER SET utf8');
    }


    // ---------------- open ---------------- //

  //public function open($db = FALSE, $user = FALSE, $password = FALSE, $port = FALSE) {
  //  }


  public function close() {
    $this->link->close();
    }



    // ---------------- setters ---------------- //

  //public function setDb($db)  { $this->db = $db; }
  //public function setUser($user)  { $this->user = $user; }
  //public function setPort($password)  { $this->password = $password; }
  //public function setPort($port)  { $this->port = $port; }




    // ---------------- varargs ---------------- //

    // ---- 5.6+ ---- //
  //function sum(...$numbers) {
  //  $acc = 0;
  //  foreach ($numbers as $n) {
  //    $acc += $n;
  //    }
  //  return $acc;
  //  }

  public function table() {
    //$numargs = func_num_args();
    //$param = func_get_arg(1);
    $params = func_get_args();
    if (count($params) == 0)  return FALSE;
    //elseif (count($params) == 1)  $this->table = $params[0];
    elseif (count($params) == 1 && is_array($params[0]))  $this->table = $params[0];
    else  $this->table = $params;
    return $this;
    }

  public function col() {
    $params = func_get_args();
    //if (count($params) == 0)  $this->col = 'id';
    //elseif (count($params) == 1)  $this->col = $params[0];
    if (count($params) == 1 && is_array($params[0]))  $this->col = $params[0];
    else  $this->col = $params;
    return $this;
    }

  public function where() {
    $params = func_get_args();
    if (count($params) == 0)  return FALSE;
    //elseif (count($params) == 1)  $this->where = $params[0];
    elseif (count($params) == 1 && is_array($params[0]))  $this->where = $params[0];
    else  $this->where = $params;
    return $this;
    }

  public function wa() {
    $params = func_get_args();
    if (count($params) == 0)  return FALSE;
    //elseif (count($params) == 1)  $this->wa = $params[0];
    elseif (count($params) == 1 && is_array($params[0]))  $this->wa = $params[0];
    else  $this->wa = $params;
    return $this;
    }

  public function group() {
    $params = func_get_args();
    if (count($params) == 0)  return FALSE;
    //elseif (count($params) == 1)  $this->group = $params[0];
    else  $this->group = $params;
    return $this;
    }

  public function order() {
    $params = func_get_args();
    if (count($params) == 0)  return FALSE;
    //elseif (count($params) == 1)  $this->order = $params[0];
    elseif (count($params) == 1 && is_array($params[0]))  $this->order = $params[0];
    else  $this->order = $params;
    return $this;
    }

  public function limit() {
    $params = func_get_args();
    if (count($params) == 0)  return FALSE;
    elseif (count($params) == 1)  $this->limit = $params[0];
    else  $this->limit = $params;
    return $this;
    }

  public function key() {
    $params = func_get_args();
    if (count($params) == 0)  return FALSE;
    elseif (count($params) == 1)  $this->key = $params[0];
    else  $this->key = $params;
    return $this;
    }

  public function value() {
    $params = func_get_args();
    if (count($params) == 0)  return FALSE;
    elseif (count($params) == 1)  $this->value = $params[0];
    else  $this->value = $params;
    return $this;
    }

  public function set() {
    $params = func_get_args();
    if (count($params) == 0)  return FALSE;
    elseif (count($params) == 1 && is_array($params[0]))  $this->set = $params[0];
    else { //$this->set = $params[0];
      if (count($params)%2)  die('error: `set` count odd');
      $this->set = array();
      for ($i = 0; $i < count($params); $i+=2)  $this->set[$params[$i]] = $params[$i+1];
      }
    return $this;
    }




    // -------------------------------- DB - SELECT v3 (path) -------------------------------- //

  private function db_read_path ($r, $p, $db, $value) {

    if ($p) {
      $ret_k = $db[array_shift ($p)];
      if (!isset($r[$ret_k]))  $r[$ret_k] = array();

      $r[$ret_k] = $this->db_read_path ($r[$ret_k], $p, $db, $value);

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




    // -------------------------------- DB - SELECT v3 -------------------------------- //

  public function  r ($verbose = FALSE) {

    if (!$this->table)  die('error: you forgot to set `table`');  // you forgot to specify a table
    //if (!$this->col)  die('error: you forgot to set `col`');
    //if (!$this->where)  die('error: you forgot to set `where`');
    //  maybe some check for `key`


    $query  = 'SELECT ';


//    if (!is_array($this->col))  $this->col = array($this->col);
    $col = array();
    if ($this->col) {
      foreach ($this->col as $v) {
        //if     ($v == '')      $col[] = 'COUNT(*)';
        //elseif ($v[0] == '!')  $col[] = addslashes(substr($v, 1));
        if ($v[0] == '!')  $col[] = addslashes(substr($v, 1));
        //elseif ($v[0] == '#')  $col[] = 'HEX(`'.addslashes(substr($v, 1)).'`) AS `'.addslashes($v).'`';
        //elseif ($v[0] == '@')  $col[] = 'INET_NTOA(`'.addslashes(substr($v, 1)).'`) AS `'.addslashes($v).'`';
        //elseif ($v[0] == '@')  $col[] = '`'.addslashes(substr($v, 1)).'`) AS `'.addslashes($v).'`';  // TODO: n_to_a
        else                   $col[] = addslashes('`'.$v.'`');
        }
      }
    else {
      $col[] = 'COUNT(*)';
      }
    $query .= implode(', ', $col);


    $query .= ' FROM ';
//    if (!is_array($this->table))   $this->table = array($this->table);
    $table = array();
    foreach ($this->table as $v)  $table[] = '`'.addslashes($v).'`';
    $query .= implode(', ', $table);


    if ($this->where) {
//      if (!is_array($this->where))   $this->where = array($this->where);
      $where = ' WHERE '.implode(' AND ', $this->where);

      if (($wa_count = substr_count($where,'?')) && $wa_count != count($this->wa) )  die('error: `where args` does not match');

      if ($this->wa) {
        foreach ($this->wa as $v) {
          if (is_int($v)) {
            $screen = '';
            }
          else {
            $screen = '\'';
            $v = strtr($v, array('\''=>'\'\'', '\\'=>'\\\\'));
            }

          if (($posb = strpos($where, '?')) !== FALSE) {
            $where = substr($where, 0, $posb) . $screen . $v . $screen . substr($where, ($posb+1));
            }
          }
        }

      $query .= $where;
      }


    if ($this->group) {
      $query .= ' GROUP BY '.implode(', ', $this->group);
      }


    if ($this->order) {
//      if (!is_array($this->order))   $this->order = array($this->order);
      $query .= ' ORDER BY '.implode(', ', $this->order);
      }


    if (!$this->key) {
      $query .= ' LIMIT 1';
      }
    elseif ($this->limit) {
      $query .= ' LIMIT '.$this->limit;
      }


    if ($verbose)  message_window('SQL', $query);


    $r = FALSE;


    $result = $this->link->query($query);

    if ($this->link->error)  message_window('SQL error', $this->link->error);

    if ($result !== FALSE && $result->num_rows) {

        // ---- single ---- //
      if (!$this->key) {
        $r = $result->fetch_assoc();
        if (count($this->col) == 1)  $r = implode($r);
        }

        // ---- multiple ---- //
      else {
        if (!is_array($this->key))  $this->key = array($this->key);
        //if (!is_array($this->value))  $col = $this->col;  else  $col = '';

        $r = array();
        $n = 0;
        $l0 = array_shift ($this->key);
        while ($row = $result->fetch_assoc()) {
          if ($l0)  $n = $row[$l0];
          if (!isset($r[$n]))  $r[$n] = array();

          $r[$n] = $this->db_read_path ($r[$n], $this->key, $row, $this->value);

          $n++;
          }
        }

      }

      // ---- empty result ---- //
    else {
      if (!$this->key && count($this->col) == 1)  $r = '';
      else  $r = array();
      }


    $this->table = FALSE;
    $this->col = FALSE;
    $this->where = FALSE;
    $this->wa = FALSE;
    $this->group = FALSE;
    $this->order = FALSE;
    $this->limit = FALSE;
    $this->key = FALSE;
    $this->value = FALSE;


    return  $r;
    }






    // -------------------------------- DB - INSERT, UPDATE, DELETE v4 -------------------------------- //

  function  w ($verbose = FALSE) {

    if (!$this->table)  die('error: you forgot to set `table`');  // you forgot to specify a table

    $r = FALSE;

    if     ($this->set && !$this->where)  $query  = 'INSERT INTO ';
    elseif ($this->set && $this->where)   $query  = 'UPDATE ';
    elseif (!$this->set && $this->where)  $query  = 'DELETE FROM ';
    else  {b('error: no `set` either `where`');  return FALSE;}


    $query .= '`'.$this->table[0].'`';


    if ($this->set) {
        // ---- INSERT ---- //
      if (!$this->where) {
        $into = array();
        $values = array();
        foreach ($this->set as $k=>$v) {
          if ($k[0] == '@')  $k = substr($k,1);  // $k[0] == '!' || $k[0] == '#' || 
          $into[] = '`'.$k.'`';
          $values[] = '?';
          }
        $query .= '('.implode(', ', $into).')';
        $query .= ' VALUES ('.implode(', ', $values).')';
        }

        // ---- UPDATE ---- //
      else {
        $query .= ' SET ';
        $set = array();
        foreach ($this->set as $k=>$v) {
          if ($k[0] == '!')  $set[] = substr($k,1);  // raw functions
          else {
            if ($k[0] == '@')  $k = substr($k,1);  // $k[0] == '#' || 
            $set[] = '`'.$k.'` = ?';
            }
          }
        $query .= implode(', ', $set);
        }
      }


      // ---- UPDATE and DELETE---- //
    if ($this->where) {
      $where = ' WHERE '.implode(' AND ', $this->where);
      if (($wa_count = substr_count($where,'?')) && $wa_count != count($this->wa) )  die('error: `where args` does not match');

      if ($this->wa) {
        foreach ($this->wa as $v) {
          if (is_int($v)) {
            $screen = '';
            }
          else {
            $screen = '\'';
            $v = strtr($v, array('\''=>'\'\'', '\\'=>'\\\\'));
            }

          if (($posb = strpos($where, '?')) !== FALSE) {
            $where = substr($where, 0, $posb) . $screen . $v . $screen . substr($where, ($posb+1));
            }
          }
        }

      $query .= $where;
      }


    if ($verbose)  message_window('SQL', $query);


    $statement = $this->link->prepare($query);
    if ($this->link->error)  message_window('SQL error', $this->link->error);

    if ($this->set) {
      $bind_type = '';
      $bind_data = array();
      foreach ($this->set as $k=>$v) {
        if     ($k[0] == '!')  ;  // $set[] = '`'.substr($k,1).'` = '.addslashes($v);
        //elseif ($k[0] == '#')  {$bind_type .= 'b';  $bind_data[] = $v;}  // $set[] = '`'.substr($k,1).'` = UNHEX(\''.addslashes($v).'\')';
        elseif ($k[0] == '@')  {$bind_type .= 's';  $bind_data[] = inet_aton($v);}  // $set[] = '`'.substr($k,1).'` = INET_ATON(\''.addslashes($v).'\')';
        else                   {$bind_type .= 's';  $bind_data[] = $v;}  // $set[] = '`'.$k.'` = \''.addslashes($v).'\'';
        }

      $bind_data_ref = array($bind_type);
      foreach ($bind_data as $k=>$v) {
        $bind_data_ref[] = &$bind_data[$k];
        }

      if (count($bind_data_ref) > 1)  call_user_func_array(array($statement, "bind_param"), $bind_data_ref);
      }

    $statement->execute();

    if     ($this->set && !$this->where)  $r = $statement->insert_id;
    elseif ($this->where)  $r = $statement->affected_rows;

    if ($this->link->error)  message_window('SQL error', $this->link->error);
    $statement->close();


    $this->table = FALSE;
    $this->set = FALSE;
    $this->where = FALSE;
    $this->wa = FALSE;


    return  $r;
    }




    // -------------------------------- DB - INSERT v4 -------------------------------- //

  function  i ($verbose = FALSE) {

    if (!$this->table)  die('error: you forgot to set `table`');  // you forgot to specify a table
    if (!$this->set)  die('error: you forgot to set `set`');
    //if (!is_array($this->set))  die('error: `set` must be array');

    $r = FALSE;

    $query  = 'INSERT INTO ';
    $query .= '`'.$this->table[0].'`';


    $into = array();
    $values = array();
    foreach ($this->set as $k=>$v) {
      if ($k[0] == '@')  $k = substr($k,1);  // $k[0] == '!' || $k[0] == '#' || 
      $into[] = '`'.$k.'`';
      $values[] = '?';
      }
    $query .= '('.implode(', ', $into).')';
    $query .= ' VALUES ('.implode(', ', $values).')';


    if ($verbose)  message_window('SQL', $query);

    $statement = $this->link->prepare($query);
    if ($this->link->error)  message_window('SQL error', $this->link->error);

    $bind_type = '';
    $bind_data = array();
    foreach ($this->set as $k=>$v) {
      //if     ($k[0] == '!')  ;  // $set[] = '`'.substr($k,1).'` = '.addslashes($v);
      //elseif ($k[0] == '#')  {$bind_type .= 'b';  $bind_data[] = $v;}  // $set[] = '`'.substr($k,1).'` = UNHEX(\''.addslashes($v).'\')';
      if ($k[0] == '@')      {$bind_type .= 's';  $bind_data[] = inet_aton($v);}  // $set[] = '`'.substr($k,1).'` = INET_ATON(\''.addslashes($v).'\')';
      else                   {$bind_type .= 's';  $bind_data[] = $v;}  // $set[] = '`'.$k.'` = \''.addslashes($v).'\'';
      }

    //$bind_data_ref = array();  // !v1
    $bind_data_ref = array($bind_type);  // !v2
    foreach ($bind_data as $k=>$v) {
      $bind_data_ref[] = &$bind_data[$k];
      }

    //call_user_func_array(array($statement, "bind_param"), array_merge(array($bind_type), $bind_data_ref) );  // !v1
    call_user_func_array(array($statement, "bind_param"), $bind_data_ref );  // !v2

    $statement->execute();

    $r = $statement->insert_id;

    if ($this->link->error)  message_window('SQL error', $this->link->error);
    $statement->close();


    $this->table = FALSE;
    $this->set = FALSE;


    return  $r;
    }




    // -------------------------------- DB - UPDATE v4 -------------------------------- //

  function  u ($verbose = FALSE) {

    if (!$this->table)  die('error: you forgot to set `table`');  // you forgot to specify a table
    if (!$this->set)  die('error: you forgot to set `set`');
    //if (!is_array($this->set))  die('error: `set` must be array');
    if (!$this->where)  die('error: you forgot to set `where`');

    $r = FALSE;

    $query  = 'UPDATE ';
    $query .= '`'.$this->table[0].'`';


    $query .= ' SET ';
    $set = array();
    foreach ($this->set as $k=>$v) {
      if ($k[0] == '!')  $set[] = substr($k,1);  // raw functions
      else {
        if ($k[0] == '@')  $k = substr($k,1);  // $k[0] == '#' || 
        $set[] = '`'.$k.'` = ?';
        }
      }
    $query .= implode(', ', $set);


    $where = ' WHERE '.implode(' AND ', $this->where);
    if (($wa_count = substr_count($where,'?')) && $wa_count != count($this->wa) )  die('error: `where args` does not match');
    if ($this->wa) {
      foreach ($this->wa as $v) {
        if (is_int($v)) {
          $screen = '';
          }
        else {
          $screen = '\'';
          $v = strtr($v, array('\''=>'\'\'', '\\'=>'\\\\'));
          }

        if (($posb = strpos($where, '?')) !== FALSE) {
          $where = substr($where, 0, $posb) . $screen . $v . $screen . substr($where, ($posb+1));
          }
        }
      }
    $query .= $where;


    if ($verbose)  message_window('SQL', $query);

    $statement = $this->link->prepare($query);
    if ($this->link->error)  message_window('SQL error', $this->link->error);

    if ($this->set) {
      $bind_type = '';
      $bind_data = array();
      foreach ($this->set as $k=>$v) {
        if     ($k[0] == '!')  ;  // $set[] = '`'.substr($k,1).'` = '.addslashes($v);
        //elseif ($k[0] == '#')  {$bind_type .= 'b';  $bind_data[] = $v;}  // $set[] = '`'.substr($k,1).'` = UNHEX(\''.addslashes($v).'\')';
        elseif ($k[0] == '@')  {$bind_type .= 's';  $bind_data[] = inet_aton($v);}  // $set[] = '`'.substr($k,1).'` = INET_ATON(\''.addslashes($v).'\')';
        else                   {$bind_type .= 's';  $bind_data[] = $v;}  // $set[] = '`'.$k.'` = \''.addslashes($v).'\'';
        }

      //$bind_data_ref = array();  // !v1
      $bind_data_ref = array($bind_type);  // !v2
      foreach ($bind_data as $k=>$v) {
        $bind_data_ref[] = &$bind_data[$k];
        }

      //call_user_func_array(array($statement, "bind_param"), array_merge(array($bind_type), $bind_data_ref) );  // !v1
      if (count($bind_data_ref) > 1)  call_user_func_array(array($statement, "bind_param"), $bind_data_ref );  // !v2
      }

    $statement->execute();

    $r = $statement->affected_rows;

    if ($this->link->error)  message_window('SQL error', $this->link->error);
    $statement->close();


    $this->table = FALSE;
    $this->set = FALSE;
    $this->where = FALSE;
    $this->wa = FALSE;


    return  $r;
    }


    // -------------------------------- DB - DELETE v4 -------------------------------- //

  function  d ($verbose = FALSE) {

    if (!$this->table)  die('error: you forgot to set `table`');  // you forgot to specify a table
    if (!$this->where)  die('error: you forgot to set `where`');

    $r = FALSE;

    $query  = 'DELETE FROM ';
    $query .= '`'.$this->table[0].'`';


    $where = ' WHERE '.implode(' AND ', $this->where);
    if (($wa_count = substr_count($where,'?')) && $wa_count != count($this->wa) )  die('error: `where args` does not match');
    if ($this->wa) {
      foreach ($this->wa as $v) {
        if (is_int($v)) {
          $screen = '';
          }
        else {
          $screen = '\'';
          $v = strtr($v, array('\''=>'\'\'', '\\'=>'\\\\'));
          }

        if (($posb = strpos($where, '?')) !== FALSE) {
          $where = substr($where, 0, $posb) . $screen . $v . $screen . substr($where, ($posb+1));
          }
        }
      }
    $query .= $where;


    if ($verbose)  message_window('SQL', $query);

    $statement = $this->link->prepare($query);
    if ($this->link->error)  message_window('SQL error', $this->link->error);
    $statement->execute();

    $r = $statement->affected_rows;

    if ($this->link->error)  message_window('SQL error', $this->link->error);
    $statement->close();


    $this->table = FALSE;
    $this->set = FALSE;
    $this->where = FALSE;
    $this->wa = FALSE;


    return  $r;
    }

  }






  // ---- SQLite connectivity -------------------------------------------------------------------------- //

class dbSQLite {

  private $link;

  private $table = FALSE;
  private $col = FALSE;
  private $where = FALSE;
  private $wa = FALSE;
  private $order = FALSE;
  private $limit = FALSE;
  private $key = FALSE;
  private $value = FALSE;

  private $set = FALSE;



    // ---------------- constructor ---------------- //

  function __construct($db = FALSE) {
    if ($db === FALSE)  die('error: you must specify `db`');

    //$this->link = new PDO('sqlite:db/db');
    $this->link = new SQLite3('d/'.$db); 
    }


  public function close() {
    $this->link->close();
    }




    // ---------------- varargs ---------------- //

    // ---- 5.6+ ---- //
  //function sum(...$numbers) {
  //  $acc = 0;
  //  foreach ($numbers as $n) {
  //    $acc += $n;
  //    }
  //  return $acc;
  //  }

  public function table() {
    $params = func_get_args();
    if (count($params) == 0)  return FALSE;
    elseif (count($params) == 1 && is_array($params[0]))  $this->table = $params[0];
    else  $this->table = $params;
    return $this;
    }

  public function col() {
    $params = func_get_args();
    if (count($params) == 1 && is_array($params[0]))  $this->col = $params[0];
    else  $this->col = $params;
    return $this;
    }

  public function where() {
    $params = func_get_args();
    if (count($params) == 0)  return FALSE;
    elseif (count($params) == 1 && is_array($params[0]))  $this->where = $params[0];
    else  $this->where = $params;
    return $this;
    }

  public function wa() {
    $params = func_get_args();
    if (count($params) == 0)  return FALSE;
    else  $this->wa = $params;
    return $this;
    }

  public function order() {
    $params = func_get_args();
    if (count($params) == 0)  return FALSE;
    elseif (count($params) == 1 && is_array($params[0]))  $this->order = $params[0];
    else  $this->order = $params;
    return $this;
    }

  public function limit() {
    $params = func_get_args();
    if (count($params) == 0)  return FALSE;
    elseif (count($params) == 1)  $this->limit = $params[0];
    else  $this->limit = $params;
    return $this;
    }

  public function key() {
    $params = func_get_args();
    if (count($params) == 0)  return FALSE;
    elseif (count($params) == 1)  $this->key = $params[0];
    else  $this->key = $params;
    return $this;
    }

  public function value() {
    $params = func_get_args();
    if (count($params) == 0)  return FALSE;
    elseif (count($params) == 1)  $this->value = $params[0];
    else  $this->value = $params;
    return $this;
    }

  public function set() {
    $params = func_get_args();
    if (count($params) == 0)  return FALSE;
    elseif (count($params) == 1 && is_array($params[0]))  $this->set = $params[0];
    else {
      if (count($params)%2)  die('error: `set` count odd');
      $this->set = array();
      for ($i = 0; $i < count($params); $i+=2)  $this->set[$params[$i]] = $params[$i+1];
      }
    return $this;
    }




    // -------------------------------- DB - SELECT v3 (path) -------------------------------- //

  private function db_read_path ($r, $p, $db, $value) {

    if ($p) {
      $ret_k = $db[array_shift ($p)];
      if (!isset($r[$ret_k]))  $r[$ret_k] = array();

      $r[$ret_k] = $this->db_read_path ($r[$ret_k], $p, $db, $value);

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




    // -------------------------------- DB - SELECT v3 -------------------------------- //

  public function  r ($verbose = FALSE) {

    if (!$this->table)  die('error: you forgot to set `table`');


    $query  = 'SELECT ';


    $col = array();
    if ($this->col) {
      foreach ($this->col as $v) {
        //if     ($v == '')      $col[] = 'COUNT(*)';
        //elseif ($v[0] == '!')  $col[] = addslashes(substr($v, 1));
        if ($v[0] == '!')  $col[] = addslashes(substr($v, 1));
        //elseif ($v[0] == '#')  $col[] = 'HEX(`'.addslashes(substr($v, 1)).'`) AS `'.addslashes($v).'`';
        //elseif ($v[0] == '@')  $col[] = 'INET_NTOA(`'.addslashes(substr($v, 1)).'`) AS `'.addslashes($v).'`';
        //elseif ($v[0] == '@')  $col[] = '`'.addslashes(substr($v, 1)).'`) AS `'.addslashes($v).'`';  // TODO: n_to_a
        else                   $col[] = addslashes('`'.$v.'`');
        }
      }
    else {
      $col[] = 'COUNT(*)';
      }
    $query .= implode(', ', $col);


    $query .= ' FROM ';
    $table = array();
    foreach ($this->table as $v)  $table[] = '`'.addslashes($v).'`';
    $query .= implode(', ', $table);


    if ($this->where) {
      $where = ' WHERE '.implode(' AND ', $this->where);

      if (($wa_count = substr_count($where,'?')) && $wa_count != count($this->wa) )  die('error: `where args` does not match');

      if ($this->wa) {
        foreach ($this->wa as $v) {
          if (is_int($v)) {
            $screen = '';
            }
          else {
            $screen = '\'';
            $v = strtr($v, array('\''=>'\'\'', '\\'=>'\\\\'));
            }

          if (($posb = strpos($where, '?')) !== FALSE) {
            $where = substr($where, 0, $posb) . $screen . $v . $screen . substr($where, ($posb+1));
            }
          }
        }

      $query .= $where;
      }


    if ($this->order) {
      $query .= ' ORDER BY '.implode(', ', $this->order);
      }


    if (!$this->key) {
      $query .= ' LIMIT 1';
      }
    elseif ($this->limit) {
      $query .= ' LIMIT '.$this->limit;
      }


    if ($verbose)  message_window('SQL', $query);


    $r = FALSE;


    $result = $this->link->query($query);

    if ($result === FALSE)  message_window('SQL error', $this->link->lastErrorMsg());


    if ($result) {

        // ---- single ---- //
      if (!$this->key) {
        $r = $result->fetchArray(SQLITE3_ASSOC);
        if (count($this->col) == 1 && is_array($r))  $r = implode($r);
        }

        // ---- multiple ---- //
      else {
        if (!is_array($this->key))  $this->key = array($this->key);

        $r = array();
        $n = 0;
        $l0 = array_shift ($this->key);
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
          if ($l0)  $n = $row[$l0];
          if (!isset($r[$n]))  $r[$n] = array();

          $r[$n] = $this->db_read_path ($r[$n], $this->key, $row, $this->value);

          $n++;
          }
        }

      }

      // ---- empty result ---- //
    else {
      if (!$this->key && count($this->col) == 1)  $r = '';
      else  $r = array();
      }


    $this->table = FALSE;
    $this->col = FALSE;
    $this->where = FALSE;
    $this->wa = FALSE;
    $this->order = FALSE;
    $this->limit = FALSE;
    $this->key = FALSE;
    $this->value = FALSE;


    return  $r;
    }






    // -------------------------------- DB - INSERT, UPDATE, DELETE v4 -------------------------------- //

  function  w ($verbose = FALSE) {

    if (!$this->table)  die('error: you forgot to set `table`');  // you forgot to specify a table

    $r = FALSE;

    if     ($this->set && !$this->where)  $query  = 'INSERT INTO ';
    elseif ($this->set && $this->where)   $query  = 'UPDATE ';
    elseif (!$this->set && $this->where)  $query  = 'DELETE FROM ';
    else  {b('error: no `set` either `where`');  return FALSE;}


    $query .= '`'.$this->table[0].'`';


    if ($this->set) {
        // ---- INSERT ---- //
      if (!$this->where) {
        $into = array();
        $values = array();
        foreach ($this->set as $k=>$v) {
          if ($k[0] == '#' || $k[0] == '@')  $k = substr($k,1);  // $k[0] == '!' || 
          $into[] = '`'.$k.'`';
          $values[] = '?';
          }
        $query .= '('.implode(', ', $into).')';
        $query .= ' VALUES ('.implode(', ', $values).')';
        }

        // ---- UPDATE ---- //
      else {
        $query .= ' SET ';
        $set = array();
        foreach ($this->set as $k=>$v) {
          if ($k[0] == '!')  $set[] = substr($k,1);  // raw functions
          else {
            if ($k[0] == '#' || $k[0] == '@')  $k = substr($k,1);
            $set[] = '`'.$k.'` = ?';
            }
          }
        $query .= implode(', ', $set);
        }
      }


      // ---- UPDATE and DELETE---- //
    if ($this->where) {
      $where = ' WHERE '.implode(' AND ', $this->where);
      if (($wa_count = substr_count($where,'?')) && $wa_count != count($this->wa) )  die('error: `where args` does not match');

      if ($this->wa) {
        foreach ($this->wa as $v) {
          if (is_int($v)) {
            $screen = '';
            }
          else {
            $screen = '\'';
            $v = strtr($v, array('\''=>'\'\'', '\\'=>'\\\\'));
            }

          if (($posb = strpos($where, '?')) !== FALSE) {
            $where = substr($where, 0, $posb) . $screen . $v . $screen . substr($where, ($posb+1));
            }
          }
        }

      $query .= $where;
      }


    if ($verbose)  message_window('SQL', $query);


    $statement = $this->link->prepare($query);

    if ($statement !== FALSE) {
      $num = 1;
      foreach ($this->set as $k=>$v) {
        if     ($k[0] == '!')  ;
        elseif ($k[0] == '#')  {$statement->bindValue($num, $v, SQLITE3_BLOB);}
        elseif ($k[0] == '@')  {$statement->bindValue($num, inet_aton($v));}
        else                   {$statement->bindValue($num, $v);}
        $num++;
        }
      $statement->execute();

      if     ($this->set && !$this->where)  $r = $this->link->lastInsertRowID();
      elseif ($this->where)  $r = $this->link->changes();

      $statement->close();
      }

    else {
      message_window('SQL error', $this->link->lastErrorMsg());
      }


    $this->table = FALSE;
    $this->set = FALSE;
    $this->where = FALSE;
    $this->wa = FALSE;


    return  $r;
    }




    // -------------------------------- DB - INSERT v4 -------------------------------- //

  function  i ($verbose = FALSE) {

    if (!$this->table)  die('error: you forgot to set `table`');  // you forgot to specify a table
    if (!$this->set)  die('error: you forgot to set `set`');
    //if (!is_array($this->set))  die('error: `set` must be array');

    $r = FALSE;

    $query  = 'INSERT INTO ';
    $query .= '`'.$this->table[0].'`';


    $into = array();
    $values = array();
    foreach ($this->set as $k=>$v) {
      if ($k[0] == '!' || $k[0] == '#' || $k[0] == '@')  $k = substr($k,1);
      $into[] = '`'.$k.'`';
      $values[] = '?';
      }
    $query .= '('.implode(', ', $into).')';
    $query .= ' VALUES ('.implode(', ', $values).')';


    if ($verbose)  message_window('SQL', $query);


    $statement = $this->link->prepare($query);

    if ($statement !== FALSE) {
      $num = 1;
      foreach ($this->set as $k=>$v) {
        if     ($k[0] == '!')  ;
        elseif ($k[0] == '#')  {$statement->bindValue($num, $v, SQLITE3_BLOB);}
        elseif ($k[0] == '@')  {$statement->bindValue($num, inet_aton($v));}
        else                   {$statement->bindValue($num, $v);}
        $num++;
        }
      $statement->execute();

      $r = $this->link->lastInsertRowID();

      $statement->close();
      }

    else {
      message_window('SQL error', $this->link->lastErrorMsg());
      }


    $this->table = FALSE;
    $this->set = FALSE;


    return  $r;
    }


    // -------------------------------- DB - UPDATE v4 -------------------------------- //

  function  u ($verbose = FALSE) {

    if (!$this->table)  die('error: you forgot to set `table`');  // you forgot to specify a table
    if (!$this->set)  die('error: you forgot to set `set`');
    //if (!is_array($this->set))  die('error: `set` must be array');
    if (!$this->where)  die('error: you forgot to set `where`');

    $r = FALSE;

    $query  = 'UPDATE ';
    $query .= '`'.$this->table[0].'`';


    $query .= ' SET ';
    $set = array();
    foreach ($this->set as $k=>$v) {
      if ($k[0] == '!')  $set[] = substr($k,1);  // raw functions
      else {
        if ($k[0] == '#' || $k[0] == '@')  $k = substr($k,1);
        $set[] = '`'.$k.'` = ?';
        }
      }
    $query .= implode(', ', $set);


    $where = ' WHERE '.implode(' AND ', $this->where);
    if (($wa_count = substr_count($where,'?')) && $wa_count != count($this->wa) )  die('error: `where args` does not match');
    if ($this->wa) {
      foreach ($this->wa as $v) {
        if (is_int($v)) {
          $screen = '';
          }
        else {
          $screen = '\'';
          $v = strtr($v, array('\''=>'\'\'', '\\'=>'\\\\'));
          }

        if (($posb = strpos($where, '?')) !== FALSE) {
          $where = substr($where, 0, $posb) . $screen . $v . $screen . substr($where, ($posb+1));
          }
        }
      }
    $query .= $where;


    if ($verbose)  message_window('SQL', $query);


    $statement = $this->link->prepare($query);

    if ($statement !== FALSE) {
      $num = 1;
      foreach ($this->set as $k=>$v) {
        if     ($k[0] == '!')  ;
        elseif ($k[0] == '#')  {$statement->bindValue($num, $v, SQLITE3_BLOB);}
        elseif ($k[0] == '@')  {$statement->bindValue($num, inet_aton($v));}
        else                   {$statement->bindValue($num, $v);}
        $num++;
        }
      $statement->execute();

      $r = $this->link->changes();

      $statement->close();
      }

    else {
      message_window('SQL error', $this->link->lastErrorMsg());
      }


    $this->table = FALSE;
    $this->set = FALSE;
    $this->where = FALSE;
    $this->wa = FALSE;


    return  $r;
    }


    // -------------------------------- DB - DELETE v4 -------------------------------- //

  function  d ($verbose = FALSE) {

    if (!$this->table)  die('error: you forgot to set `table`');  // you forgot to specify a table
    if (!$this->where)  die('error: you forgot to set `where`');

    $r = FALSE;

    $query  = 'DELETE FROM ';
    $query .= '`'.$this->table[0].'`';


    $where = ' WHERE '.implode(' AND ', $this->where);
    if (($wa_count = substr_count($where,'?')) && $wa_count != count($this->wa) )  die('error: `where args` does not match');
    if ($this->wa) {
      foreach ($this->wa as $v) {
        if (is_int($v)) {
          $screen = '';
          }
        else {
          $screen = '\'';
          $v = strtr($v, array('\''=>'\'\'', '\\'=>'\\\\'));
          }

        if (($posb = strpos($where, '?')) !== FALSE) {
          $where = substr($where, 0, $posb) . $screen . $v . $screen . substr($where, ($posb+1));
          }
        }
      }
    $query .= $where;


    if ($verbose)  message_window('SQL', $query);


    $statement = $this->link->prepare($query);

    if ($statement !== FALSE) {
      $statement->execute();

      $r = $this->link->changes();

      $statement->close();
      }

    else {
      message_window('SQL error', $this->link->lastErrorMsg());
      }


    $this->table = FALSE;
    $this->set = FALSE;
    $this->where = FALSE;
    $this->wa = FALSE;


    return  $r;
    }


    // -------------------------------- DB - query -------------------------------- //

  function  q ($query) {
    $result = $this->link->query($query);
    //d($result);
    }

  }




  // -------------------------------- Copy history -------------------------------- //

function copy_history ($table, $id) {
  global $dbD;

  if ($table == 'table1')  $col = array('col1', 'col2', 'col3', 'col4', 'col5', 'col6', 'col7', 'col8', 'dtx', 'idx');
  elseif ($table == 'table2')  $col = array('col1', 'col2', 'col3', 'col5', 'col6', 'col7', 'dtx', 'idx');
  elseif ($table == 'table3')  $col = array('col1', 'col2', 'dtx', 'idx');
  elseif ($table == 'table4')  $col = array('col1', 'col2', 'col3', 'col4', 'col5', 'col6', 'col7', 'col8', 'dtx', 'idx');
  else  die('error: unknown table `'.$table.'`');

  $history = $dbD->
    table($table)->
    col($col)->
    where('`id` = '.$id)->
    r();

  if ($history) {
    $history['pid'] = $id;
    $dbD->
      table($table.'_h')->
      set($history)->
      i();
    }

  }





  // -------------------------------- Active Security Storage -------------------------------- //

class ass {
  private $db;
  private $password = FALSE;


    // ---------------- constructor ---------------- //

  function __construct() {
    $socket = @fsockopen('127.0.0.1', 22633, $errno, $errstr, 10);

    if ($socket) {
      $result = fwrite($socket, 'g');

      $receive = '';
      while (!feof($socket))  $receive .= fread($socket, 8192);

      fclose($socket);

      $this->password = $receive;
      $this->db = new dbSQLite('ass');
      }
    }


  public function p($table, $key, $value) {
    $e = openssl_encrypt($value, 'AES-256-CBC', $this->password, OPENSSL_RAW_DATA, md5($this->password, TRUE));

    $check = $this->db->
      table($table)->
      col('k')->
      where('`k` = ?')->
      wa($key)->
      r();

    //if (!$value) {  // delete
    //    }
    if ($check) {
      $this->db->
        table($table)->
        set('v', $e)->
        where('`k` = ?')->
        wa($key)->
        u();
      }

    else {
      $this->db->
        table($table)->
        set('k', $key,  'v', $e)->
        i();
      }

    }


  public function g($table, $key) {
    $v = $this->db->
      table($table)->
      col('v')->
      where('`k` = ?')->
      wa($key)->
      r();
    $d = openssl_decrypt($v, 'AES-256-CBC', $this->password, OPENSSL_RAW_DATA, md5($this->password, TRUE));
    return $d;
    }


  public function a($pass) {

    $try = 3;
    while ($try--) {
      $socket = @fsockopen('127.0.0.1', 22633, $errno, $errstr, 10);

      if (!$socket) {
        // shell_exec('"C:\Program Files (x86)\PHP\php.exe" "C:\www\loga\m\cab\ass.php"');
        // exec($cmd . " > /dev/null &"); 
        //$cmd = '"C:\Program Files (x86)\PHP\php.exe" "C:\www\loga\m\cab\ass.php"';
        //pclose(popen('start /B '. $cmd, 'r'));
        $cmd = '"C:\\Program Files (x86)\\PHP\\php.exe" "C:\\www\\loga\\m\\cab\\ass.php"';
        pclose(popen('start "bla" '. $cmd, 'r'));
        sleep(1);
        continue;
        }
      }

    if (!$socket)  die('fatal error: can\'t raise server.');

    $result = fwrite($socket, 's'.$pass);

    //$receive = '';
    //while (!feof($socket))  $receive .= fread($socket, 8192);

    fclose($socket);
    }


  public function c() {
    return ( ($this->password === FALSE || $this->password === '') ? $this->password : TRUE );
    }
  }




  // -------------------------------- file DB -------------------------------- //

function  fdb_get ($db, $id, $return_path = FALSE) {
  $tmp = substr('000000'.dechex($id), -6,6);
  $file = 'd/'.$db.'/'.substr($tmp, 0,2).'/'.substr($tmp, 2,2).'/'.substr($tmp, 4,2);

  $type = FALSE;
  if     (file_exists($file))  ;
  elseif (file_exists($file.'.jpg'))  $file .= '.jpg';
  elseif (file_exists($file.'.png'))  $file .= '.png';
  else  return  FALSE;

  if ($return_path) {
    return  $file;
    }

  // file_get_contents()
  return  fread (fopen ($file, 'rb'), filesize ($file) );
  }



function  fdb_put ($db, $id, $data) {
  $type = '';
  if (substr($receive, 0,3) == chr(255).chr(216).chr(255))  $type = '.jpg';
  elseif (substr($receive, 0,4) == chr(137).'PNG')          $type = '.png';
  //elseif (substr($receive, 0,3) == 'GIF')                   $type = '.gif';

  $tmp = substr('000000'.dechex($id), -6,6);
  $file_out = 'd/'.$db;
  if (!file_exists($file_out))  mkdir($file_out, 0757);
  $file_out .= '/'.substr($tmp, 0,2);
  if (!file_exists($file_out))  mkdir($file_out, 0757);
  $file_out .= '/'.substr($tmp, 2,2);
  if (!file_exists($file_out))  mkdir($file_out, 0757);
  $file_out .= '/'.substr($tmp, 4,2);

  $file_out .= $type;

  fwrite (fopen ($file_out, 'wb'), $data);
  clearstatcache();

  return  $file_out;
  }



function  fdb_delete ($db, $id) {
  $tmp = substr('000000'.dechex($id), -6,6);
  $file = 'd/'.$db.'/'.substr($tmp, 0,2).'/'.substr($tmp, 2,2).'/'.substr($tmp, 4,2);

  $type = FALSE;
  if     (file_exists($file))  ;
  elseif (file_exists($file.'.jpg'))  $file .= '.jpg';
  elseif (file_exists($file.'.png'))  $file .= '.png';
  else  return  FALSE;

  unlink ($file);  
  }




  // -------------------------------- sqlite blob storage DB -------------------------------- //

function  sdb_get ($db, $id) {
  $db = new SQLite3('d/'.$db); 

  $result = $db->query('SELECT `d` FROM `t` WHERE `id` = '.$id);
  $data = $result->fetchArray(SQLITE3_ASSOC);
  if ($data !== FALSE)  $data = $data['d'];

  $db->close();
  return  $data;
  }



function  sdb_put ($db, $id, $data = FALSE) {
  $db = new SQLite3('d/'.$db); 

  $db->query('CREATE TABLE IF NOT EXISTS `t` (`id` INTEGER PRIMARY KEY, `d` BLOB)');

  if ($data !== FALSE) {
    $exists = $db->query('SELECT `id` FROM `t` WHERE `id` = '.$id);

    if (!$exists->fetchArray(SQLITE3_ASSOC)) {
      $query = 'INSERT INTO `t` (`id`, `d`) VALUES ('.$id.', ?)';
      }
    else {
      $query = 'UPDATE `t` SET `d` = ? WHERE `id` = '.$id;
      }
    }

  else {
    $db->query('DELETE FROM `t` WHERE `id` = '.$id);
    }

  $prepare = $db->prepare($query);
  if ($data !== FALSE)  $prepare->bindParam(1, $data, SQLITE3_BLOB);
  $prepare->execute();

  $db->close();
  }




  // -------------------------------- sqlite key-value storage DB -------------------------------- //

function  get_storage ($db, $key) {
  $db = new SQLite3('d/'.$db); 

  $result = $db->query('SELECT `v` FROM `t` WHERE `k` = \''.$key.'\'');
  $value = $result->fetchArray(SQLITE3_ASSOC);
  if ($value !== FALSE)  $value = $value['v'];

  $db->close();
  return  $value;
  }



function  put_storage ($db, $key, $value = FALSE) {
  $db = new SQLite3('d/'.$db); 

  $db->query('CREATE TABLE IF NOT EXISTS `t` (`id` INTEGER PRIMARY KEY, `k` TEXT UNIQUE, `v` TEXT)');

  if ($value !== FALSE) {
    $exists = $db->query('SELECT `v` FROM `t` WHERE `k` = \''.$key.'\'');
    if (!$exists->fetchArray(SQLITE3_ASSOC)) {
      $query = 'INSERT INTO `t` (`k`, `v`) VALUES (\''.$key.'\', ?)';
      }
    else {
      $query = 'UPDATE `t` SET `v` = ? WHERE `k` = \''.$key.'\'';
      }
    }

  else {
    $query = 'DELETE FROM `t` WHERE `k` = \''.$key.'\'';
    }


  $prepare = $db->prepare($query);
  if ($value !== FALSE)  $prepare->bindParam(1, $value);
  $prepare->execute();

  $db->close();
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




  // ---------------- sign safe `addr` to `int` convertors ---------------- //

function  inet_aton ($addr) {
  $e = explode('.', $addr);
  return  ($e[0] * 16777216) + ($e[1] * 65536) + ($e[2] * 256) + $e[3];
  }


function  inet_ntoa ($int) {
  $msb = floor($int / 16777216);
  $oth = $int - ($msb * 16777216);
  return  implode('.', array($msb, ($oth & 0xFF0000) >> 16 , ($oth & 0xFF00) >> 8 , $oth & 0xFF ) );
  }



?>