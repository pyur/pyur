<?php

/************************************************************************/
/*  log (share)  v1.oo                                                  */
/************************************************************************/


if (!isset($body))  die ('error: this is a pluggable module and cannot be used standalone.');



$db_method = array(
  'GET'     => 1,
  'POST'    => 2,
  'HEAD'    => 3,
  'PUT'     => 4,
  'DELETE'  => 5,
  'TRACE'   => 6,
  'OPTIONS' => 7,
  'CONNECT' => 8,
  'PATCH'   => 9,
    // WebDAV
  'PROPFIND'  => 10,
  'PROPPATCH' => 11,
  'MKCOL'     => 12,
  'COPY'      => 13,
  'MOVE'      => 14,
  'LOCK'      => 15,
  'UNLOCK'    => 16,
    // trash
  'GGET'    => 101,
  'SCANNER' => 102,
  'COOK'    => 103,
  );

$db_httpv = array(
  'HTTP/1.1' => 1,
  'HTTP/1.0' => 2,
  'HTTP/0.9' => 3,
  );

$mon_apache = array('Jan' => 1, 'Feb' => 2, 'Mar' => 3, 'Apr' => 4, 'May' => 5, 'Jun' => 6, 'Jul' => 7, 'Aug' => 8, 'Sep' => 9, 'Oct' => 10, 'Nov' => 11, 'Dec' => 12);
/*
$db_result = array(
  '100' => 0,  // Continue
  '101' => 1,  // Switching Protocols
  '102' => 2,  // Processing (WebDAV; RFC 2518)
  '103' => 3,  //
  '104' => 4,  //
  '105' => 5,  //
  '106' => 6,  /// exist  flowplayer related

  '200' => 20,  /// OK
  '201' => 21,  // Created
  '202' => 22,  // Accepted
  '203' => 23,  // Non-Authoritative Information (since HTTP/1.1)
  '204' => 24,  // No Content
  '205' => 25,  // Reset Content
  '206' => 26,  /// Partial Content
  '207' => 27,  // Multi-Status (WebDAV; RFC 4918)
  '208' => 28,  // Already Reported (WebDAV; RFC 5842)
  '226' => 29,  // IM Used (RFC 3229)

  '300' => 70,  // Multiple Choices
  '301' => 71,  /// Moved Permanently
  '302' => 72,  /// Found
  '303' => 73,  // See Other (since HTTP/1.1)
  '304' => 74,  /// Not Modified
  '305' => 75,  // Use Proxy (since HTTP/1.1)
  '306' => 76,  // Switch Proxy
  '307' => 77,  // Temporary Redirect (since HTTP/1.1)
  '308' => 78,  // Permanent Redirect (Experimental RFC; RFC 7238)

  '400' => 100,  /// Bad Request
  '401' => 101,  // Unauthorized
  '402' => 102,  // Payment Required
  '403' => 103,  /// Forbidden
  '404' => 104,  /// Not Found
  '405' => 105,  /// Method Not Allowed
  '406' => 106,  /// Not Acceptable
  '407' => 107,  // Proxy Authentication Required
  '408' => 108,  /// Request Timeout
  '409' => 109,  // Conflict
  '410' => 110,  // Gone
  '411' => 111,  // Length Required
  '412' => 112,  /// Precondition Failed
  '413' => 113,  // Request Entity Too Large
  '414' => 114,  /// Request-URI Too Long
  '415' => 115,  /// Unsupported Media Type

  '416' => 116,  // Requested Range Not Satisfiable
  '417' => 117,  // Expectation Failed
  '418' => 118,  // I'm a teapot (RFC 2324)
  '419' => 119,  // Authentication Timeout (not in RFC 2616)
  '420' => 120,  // Method Failure (Spring Framework)  // Enhance Your Calm (Twitter)
  '422' => 122,  // Unprocessable Entity (WebDAV; RFC 4918)
  '423' => 123,  // Locked (WebDAV; RFC 4918)
  '424' => 124,  // Failed Dependency (WebDAV; RFC 4918)
  '426' => 126,  // Upgrade Required
  '428' => 128,  // Precondition Required (RFC 6585)
  '429' => 129,  // Too Many Requests (RFC 6585)
  '431' => 131,  // Request Header Fields Too Large (RFC 6585)
  '440' => 140,  // Login Timeout (Microsoft)
  '444' => 144,  // No Response (Nginx)
  '449' => 149,  // Retry With (Microsoft)
  '450' => 150,  // Blocked by Windows Parental Controls (Microsoft)
  '451' => 151,  // Unavailable For Legal Reasons (Internet draft)  // Redirect (Microsoft)
  '494' => 194,  // Request Header Too Large (Nginx)
  '495' => 195,  // Cert Error (Nginx)
  '496' => 196,  // No Cert (Nginx)
  '497' => 197,  // HTTP to HTTPS (Nginx)
  '498' => 198,  // Token expired/invalid (Esri)
  '499' => 199,  /// Client Closed Request (Nginx)  // Token required (Esri)

  '500' => 200,  /// Internal Server Error
  '501' => 201,  /// Not Implemented
  '502' => 202,  /// Bad Gateway
  '503' => 203,  /// Service Unavailable
  '504' => 204,  /// Gateway Timeout

  '505' => 205,  // HTTP Version Not Supported
  '506' => 206,  // Variant Also Negotiates (RFC 2295)
  '507' => 207,  // Insufficient Storage (WebDAV; RFC 4918)
  '508' => 208,  // Loop Detected (WebDAV; RFC 5842)
  '509' => 209,  // Bandwidth Limit Exceeded (Apache bw/limited extension)[26]
  '510' => 210,  // Not Extended (RFC 2774)
  '511' => 211,  // Network Authentication Required (RFC 6585)
  '520' => 220,  // Origin Error (CloudFlare)
  '521' => 221,  // Web server is down (CloudFlare)
  '522' => 222,  // Connection timed out (CloudFlare)
  '523' => 223,  // Proxy Declined Request (CloudFlare)
  '524' => 224,  // A timeout occurred (CloudFlare)
  '598' => 248,  // Network read timeout error (Unknown)
  '599' => 249,  // Network connect timeout error (Unknown)
  );
*/


  // ------------------------------------------------ parse apache log line ------------------------------------------------ //

  // ------------------------------------ combined ------------------------------------ //

function  log_parse_line ($v) {
  global  $mon_apache;
  global  $db_method, $db_httpv, $db_result;
  global  $errorc, $warningc;


  $r = array();
  $error = FALSE;
  $warning = FALSE;
  $pos = 0;

  $vs = strtr( strtr($v, array('\\\\' => '~~')), array('\\"' => '~~'));

  $pose = strpos($v, ' ', $pos);
  $r['ip'] = substr($v, $pos, $pose-$pos);
  if (strpos($r['ip'], '.') === FALSE) {
    if (strpos($r['ip'], ':') !== FALSE) {
      $r['ip'] = '127.0.0.2';
      }
    else {
      $error = '<br>error: ip not v4 nor v6<br>'.htmlspecialchars($v);
      }
    }
  elseif (strlen($r['ip']) > 15) {
    $error = '<br>error: ip size exceeds 15 characters<br>'.htmlspecialchars($v);
    }
  $pos = $pose + 1;

  $pose = strpos($v, ' ', $pos);
  $r['lname'] = substr($v, $pos, $pose-$pos);
  $pos = $pose + 1;

  $pose = strpos($v, ' ', $pos);
  $r['ruser'] = substr($v, $pos, $pose-$pos);
  $pos = $pose + 1;


  if ($v[$pos] == '[') {
    $pos++;
    $pose = strpos($v, ']', $pos);
    $r['datetime'] = substr($v, $pos, $pose-$pos);
    $pos = $pose + 2;
    }
  else  $error = '<br>error: no date found<br>'.htmlspecialchars($v);


  if ($v[$pos] == '"') {
    $pos++;
    $pose = strpos($vs, '"', $pos);
    $r['request'] = substr($v, $pos, $pose-$pos);
    if ($pose-$pos > 4096)  $warning = '<br>warning: request lenght exceeds 4096 characters<br>'.htmlspecialchars($v);
    $pos = $pose + 2;
    }
  else  $error = '<br>error: request not a string<br>'.htmlspecialchars($v);


  $pose = strpos($v, ' ', $pos);
  $r['result'] = substr($v, $pos, $pose-$pos);
  if (in_array($r['result'],$db_result)) {
    $r['resultn'] = array_search($r['result'],$db_result);
    $r['result'] = '';
    }
  else {
    $r['resultn'] = 0;
    $warning = '<br>warning: not enlisted result<br>'.htmlspecialchars($v);
    }
  $pos = $pose + 1;

  $pose = strpos($v, ' ', $pos);
  $r['bytes'] = substr($v, $pos, $pose-$pos);
  $pos = $pose + 1;

  if ($v[$pos] == '"') {
    $pos++;
    $pose = strpos($vs, '"', $pos);
    $r['referer'] = substr($v, $pos, $pose-$pos);
    if ($pose-$pos > 4096)  $warning = '<br>warning: referer lenght exceeds 4096 characters<br>'.htmlspecialchars($v);
    $pos = $pose + 2;
    }
  else  $error = '<br>error: referer not a string<br>'.htmlspecialchars($v);

  if ($v[$pos] == '"') {
    $pos++;
    $pose = strpos($vs, '"', $pos);
    $r['ua'] = substr($v, $pos, $pose-$pos);
    $r['ua'] = substr($r['ua'], 0, 4096);
    if ($pose-$pos > 4096)  $warning = '<br>warning: ua lenght exceeds 4096 characters<br>'.htmlspecialchars($v);
    $pos = $pose + 2;
    }
  else  $error = '<br>error: ua not a string<br>'.htmlspecialchars($v);

  $custom = explode(' ', substr($v, $pos));

  $r['userx'] = isset($custom[0]) ? (int)$custom[0] : '-';

  $r['ipf'] = isset($custom[1]) ? $custom[1] : '-';


  if (!$error) {

      // ---------------- parse request ---------------- //

    // 0   1                2
    // |   |                |
    // GET /vt/00/00/24.jpg HTTP/1.1
    $tmp = explode(' ', $r['request']);
    if (count($tmp) == 3) {

      if (isset($db_method[$tmp[0]])) {
        $r['method'] = '';
        $r['methodn'] = $db_method[$tmp[0]];
        }
      else {
        $r['method'] = $tmp[0];
        $r['methodn'] = 0;
        $warning = '<br>warning: not enlisted method<br>'.htmlspecialchars($v);
        }

      $r['uri'] = $tmp[1];

      if (isset($db_httpv[$tmp[2]])) {
        $r['httpv'] = '';
        $r['httpvn'] = $db_httpv[$tmp[2]];
        }
      else {
        $r['httpv'] = $tmp[2];
        $r['httpvn'] = 0;
        $warning = '<br>warning: not enlisted httpv<br>'.htmlspecialchars($v);
        }
      }

    else {
      $r['method'] = '';
      $r['methodn'] = 0;
      $r['uri'] = $r['request'];
      $r['httpv'] = '';
      $r['httpvn'] = 0;
      }



      // ---------------- parse datetime ---------------- //

    // 0  3   7    12 15 18
    // |  |   |    |  |  |
    // 14/Aug/2011:15:03:57 +0400
    //$r['day']  = substr($r['datetime'], 0, 2);
    //$r['mon']  = substr($r['datetime'], 3, 3);
    //$r['year'] = substr($r['datetime'], 7, 4);
    //$r['hour'] = substr($r['datetime'], 12, 2);
    //$r['min']  = substr($r['datetime'], 15, 2);
    //$r['sec']  = substr($r['datetime'], 18, 2);
    //$r['datesql'] = datesql($r['year'], $mon_apache[$r['mon']], $r['day'],  $r['hour'], $r['min'], $r['sec']);
    $r['datesql'] = datesql(substr($r['datetime'], 7, 4), $mon_apache[substr($r['datetime'], 3, 3)], substr($r['datetime'], 0, 2),  substr($r['datetime'], 12, 2), substr($r['datetime'], 15, 2), substr($r['datetime'], 18, 2));
    }

  else {
    $r = array(
      'ip' => '0.0.0.0',
      'lname' => '',
      'ruser' => '',
      'datetime' => '0000-00-00 00:00:00',
      'methodn' => 0,
      'uri' => $v,
      'httpvn' => 0,
      'result' => 0,
      'resultn' => 0,
      'bytes' => 0,
      'referer' => $error,
      'ua' => '',
      'userx' => 0,
      'ipf' => '0.0.0.0',
      );
    }

  if ($error)  { b($error);  $errorc++; }
  if ($warning)  { b($warning);  $warningc++; }


  return  $r;
  }




  // ------------------------------------ bdsx ------------------------------------ //

function  log_parse_line_bdsx ($v) {
  global  $mon_apache;
  global  $db_method, $db_httpv;
  global  $errorc, $warningc;

  $r = array();
  $error = FALSE;
  $warning = FALSE;
  $pos = 0;

  // 94.230.35.3 [19/Oct/2012:08:41:46 +0400] "GET /vt/00/00/24.jpg HTTP/1.1" 200 18035 "http://video.pyur.ru/?p=video&date=2012-06-26" "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0; .NET CLR 2.0.50727; .NET CLR 3.0.04506.648; .NET CLR 3.5.21022)"
  // 192.168.7.44 [06/Nov/2012:22:37:33 +0400] "GET /phama00/sql.php?db=kira&table=ad&goto=tbl_structure.php&back=tbl_operations.php&sql_query=TRUNCATE+TABLE+%60ad%60&reload=1&message_to_show=Table+ad+has+been+emptied&token=d3ae9c1aae23f855d7f6c0e15684165b&is_js_confirmed=1&ajax_request=true&_nocache=1352227052387608919 HTTP/1.1" 200 1688 "http://server-comp/phama00/tbl_operations.php?db=kira&table=ad&token=d3ae9c1aae23f855d7f6c0e15684165b" "Opera/9.80 (Windows NT 6.1; WOW64; U; ru) Presto/2.10.289 Version/12.02"

  $vs = strtr( strtr($v, array('\\\\' => '~~')), array('\\"' => '~~'));

  $pose = strpos($v, ' ', $pos);
  $r['ip'] = substr($v, $pos, $pose-$pos);
  if (strpos($r['ip'], '.') === FALSE) {
    if (strpos($r['ip'], ':') !== FALSE) {
      $r['ip'] = '127.0.0.2';
      }
    else {
      $error = '<br>error: ip not v4 nor v6<br>'.htmlspecialchars($v);
      }
    }
  elseif (strlen($r['ip']) > 15) {
    $error = '<br>error: ip size exceeds 15 characters<br>'.htmlspecialchars($v);
    }
  $pos = $pose + 1;

  //$pose = strpos($v, ' ', $pos);
  //$r['lname'] = substr($v, $pos, $pose-$pos);
  //$pos = $pose + 1;
  $r['lname'] = '-';

  //$pose = strpos($v, ' ', $pos);
  //$r['ruser'] = substr($v, $pos, $pose-$pos);
  //$pos = $pose + 1;
  $r['ruser'] = '-';


  if ($v[$pos] == '[') {
    $pos++;
    $pose = strpos($v, ']', $pos);
    $r['datetime'] = substr($v, $pos, $pose-$pos);
    $pos = $pose + 2;
    }
  else  $error = '<br>error: no date found<br>'.htmlspecialchars($v);


  if ($v[$pos] == '"') {
    $pos++;
    $pose = strpos($vs, '"', $pos);
    $r['request'] = substr($v, $pos, $pose-$pos);
    if ($pose-$pos > 4096)  $warning = '<br>warning: request lenght exceeds 4096 characters<br>'.htmlspecialchars($v);
    $pos = $pose + 2;
    }
  else  $error = '<br>error: request not a string<br>'.htmlspecialchars($v);


  $pose = strpos($v, ' ', $pos);
  $r['result'] = substr($v, $pos, $pose-$pos);
  $pos = $pose + 1;

  $pose = strpos($v, ' ', $pos);
  $r['bytes'] = substr($v, $pos, $pose-$pos);
  $pos = $pose + 1;

  if ($v[$pos] == '"') {
    $pos++;
    $pose = strpos($vs, '"', $pos);
    $r['referer'] = substr($v, $pos, $pose-$pos);
    if ($pose-$pos > 4096)  $warning = '<br>warning: referer lenght exceeds 4096 characters<br>'.htmlspecialchars($v);
    $pos = $pose + 2;
    }
  else  $error = '<br>error: referer not a string<br>'.htmlspecialchars($v);

  if ($v[$pos] == '"') {
    $pos++;
    $pose = strpos($vs, '"', $pos);
    $r['ua'] = substr($v, $pos, $pose-$pos);
    if ($pose-$pos > 4096)  $warning = '<br>warning: ua lenght exceeds 4096 characters<br>'.htmlspecialchars($v);
    $pos = $pose + 2;
    }
  else  $error = '<br>error: ua not a string<br>'.htmlspecialchars($v);

  $custom = explode(' ', substr($v, $pos));

  $r['userx'] = isset($custom[0]) ? (int)$custom[0] : '-';

  $r['ipf'] = isset($custom[1]) ? $custom[1] : '-';


  if (!$error) {

      // ---------------- parse request ---------------- //

    // 0   1                2
    // |   |                |
    // GET /vt/00/00/24.jpg HTTP/1.1
    $tmp = explode(' ', $r['request']);
    if (count($tmp) == 3) {

      if (isset($db_method[$tmp[0]])) {
        $r['method'] = '';
        $r['methodn'] = $db_method[$tmp[0]];
        }
      else {
        $r['method'] = $tmp[0];
        $r['methodn'] = 0;
        $warning = '<br>warning: not enlisted method<br>'.htmlspecialchars($v);
        }

      $r['uri'] = $tmp[1];

      if (isset($db_httpv[$tmp[2]])) {
        $r['httpv'] = '';
        $r['httpvn'] = $db_httpv[$tmp[2]];
        }
      else {
        $r['httpv'] = $tmp[2];
        $r['httpvn'] = 0;
        $warning = '<br>warning: not enlisted httpv<br>'.htmlspecialchars($v);
        }
      }

    else {
      $r['method'] = '';
      $r['methodn'] = 0;
      $r['uri'] = $r['request'];
      $r['httpv'] = '';
      $r['httpvn'] = 0;
      }



      // ---------------- parse datetime ---------------- //

    // 0  3   7    12 15 18
    // |  |   |    |  |  |
    // 14/Aug/2011:15:03:57 +0400
    //$r['day']  = substr($r['datetime'], 0, 2);
    //$r['mon']  = substr($r['datetime'], 3, 3);
    //$r['year'] = substr($r['datetime'], 7, 4);
    //$r['hour'] = substr($r['datetime'], 12, 2);
    //$r['min']  = substr($r['datetime'], 15, 2);
    //$r['sec']  = substr($r['datetime'], 18, 2);
    //$r['datesql'] = datesql($r['year'], $mon_apache[$r['mon']], $r['day'],  $r['hour'], $r['min'], $r['sec']);
    $r['datesql'] = datesql(substr($r['datetime'], 7, 4), $mon_apache[substr($r['datetime'], 3, 3)], substr($r['datetime'], 0, 2),  substr($r['datetime'], 12, 2), substr($r['datetime'], 15, 2), substr($r['datetime'], 18, 2));
    }

  else {
    $r = array(
      'ip' => '0.0.0.0',
      'lname' => '',
      'ruser' => '',
      'datetime' => '0000-00-00 00:00:00',
      'methodn' => 0,
      'uri' => $v,
      'httpvn' => 0,
      'result' => 0,
      'bytes' => 0,
      'referer' => $error,
      'ua' => '',
      'userx' => 0,
      'ipf' => '0.0.0.0',
      );
    }

  if ($error)  { b($error);  $errorc++; }
  if ($warning)  { b($warning);  $warningc++; }


  //d($r);
  //d(addslashes($r['ua']));
  //d($r['userx']);
  return  $r;
  }






  // ------------------------------------------------ parse ip address via `host` ------------------------------------------------ //

$ip_addr = array();
function  parse_ip ($ip, $pdate = FALSE) {
  global $ip_addr;
  global $curr;

  if (!$pdate)  $pdate = $curr['date'];  // TODO: temporary


  if (!isset($ip_addr[$ip][$pdate])) {

    $ipn = explode('.', $ip);
    $ipn = ($ipn[0] * 16777216) + ($ipn[1] * 65536) + ($ipn[2] * 256) + $ipn[3];

    $host = db_read(array('table' => 'host',
                          'col' => array('desc', 'color', 'type'),
                          'where' => array('`ip` <= '.$ipn,
                                           '`ipe` >= '.$ipn,
                                           '(`dateb` <= \''.$pdate.'\' OR `dateb` = \'0000-00-00\')',
                                           '(`datee` >= \''.$pdate.'\' OR `datee` = \'0000-00-00\')',
                                           ),
                          'order' => '`metric` DESC',
                          ));

    if ($host) {
      $ip_addr[$ip][$pdate]['desc'] = $host['desc'];
      $ip_addr[$ip][$pdate]['color'] = $host['color'];
      $ip_addr[$ip][$pdate]['type'] = $host['type'];
      }
    else {
      $ip_addr[$ip][$pdate]['desc'] = '';
      $ip_addr[$ip][$pdate]['color'] = '';
      $ip_addr[$ip][$pdate]['type'] = 0;
      }
    }
  
  $r = array();
  $r['ip'] = $ip;
  $r['desc'] = $ip_addr[$ip][$pdate]['desc'];
  $r['color'] = $ip_addr[$ip][$pdate]['color'];
  $r['type'] = $ip_addr[$ip][$pdate]['type'];
  //$r['host'] = (isset($ip_addr[$ip]) ? 'yes' : $ip);
  //$r['host'] = $ip;

  return $r;
  }




?>