<?php

/************************************************************************/
/*  user agent string  v1.31                                            */
/************************************************************************/


if (!isset($body))  die ('error: this is a pluggable module and cannot be used standalone.');



$db_uas_browser = array(
  0  => array('d' => '- - - - - - - -',   'i' => ''),
  1  => array('d' => 'Chrome',            'i' => 'ch'),
  2  => array('d' => 'Firefox',           'i' => 'ff'),
  3  => array('d' => 'Opera',             'i' => 'op'),
  4  => array('d' => 'Internet explorer', 'i' => 'ie'),
  5  => array('d' => 'Safari',            'i' => 'sf'),
  6  => array('d' => 'Яндекс',            'i' => 'ya'),
  7  => array('d' => 'Chromium',          'i' => 'cu'),
  8  => array('d' => 'Нихром',            'i' => 'ni'),
  9  => array('d' => 'Амиго',             'i' => 'am'),
  10 => array('d' => 'Iceweasel',         'i' => 'iw'),
  11 => array('d' => 'Android browser',   'i' => 'ab'),

  12 => array('d' => 'none',              'i' => ''),
  13 => array('d' => 'robot Yandex Images', 'i' => 'yi'),

  14 => array('d' => 'Edge',              'i' => 'ie'),
  );


$db_uas_os = array(
  0  => array('d' => '- - - - - - - -',  'i' => ''),
  1  => array('d' => 'Windows 2000',     'i' => 'w2'),
  2  => array('d' => 'Windows XP',       'i' => 'wx'),
  3  => array('d' => 'Windows 2003/XP64','i' => 'w3'),
  4  => array('d' => 'Windows Vista',    'i' => 'wv'),
  5  => array('d' => 'Windows 7',        'i' => 'w7'),
  6  => array('d' => 'Windows 8',        'i' => 'w8'),
  7  => array('d' => 'Windows 8.1',      'i' => 'w9'),
  8  => array('d' => 'Windows 10',       'i' => 'w10'),
  9  => array('d' => 'Windows',          'i' => 'w'),

  10 => array('d' => 'Macintosh',        'i' => 'mc'),
  11 => array('d' => 'iPhone',           'i' => 'ih'),
  12 => array('d' => 'iPad',             'i' => 'id'),

  13 => array('d' => 'Android',          'i' => 'a'),
  14 => array('d' => 'Android 2',        'i' => 'a2'),
  15 => array('d' => 'Android 3',        'i' => 'a3'),
  16 => array('d' => 'Android 4',        'i' => 'a4'),
  17 => array('d' => 'Android 5',        'i' => 'a5'),
  27 => array('d' => 'Android 6',        'i' => 'a5'),

  18 => array('d' => 'Linux',            'i' => 'l'),
  19 => array('d' => 'Ubuntu',           'i' => 'lu'),

  20 => array('d' => 'J2ME/MIDP',        'i' => 'jv'),

  21 => array('d' => 'Yandex robot',      'i' => 'y'),
  22 => array('d' => 'Google robot',      'i' => 'g'),
  23 => array('d' => 'Ahrefs robot',      'i' => 'ah'),
  24 => array('d' => 'Bing robot',        'i' => 'bg'),
  25 => array('d' => 'Yahoo robot',       'i' => 'yh'),
  26 => array('d' => 'Sputnik robot',     'i' => 'sp'),
  //28
  );

// Windows NT 6.3	Windows 8.1
// Windows NT 6.2	Windows 8
// Windows NT 6.1	Windows 7
// Windows NT 6.0	Windows Vista
// Windows NT 5.2	Windows Server 2003; Windows XP x64 Edition
// Windows NT 5.1	Windows XP
// Windows NT 5.01	Windows 2000, Service Pack 1 (SP1)
// Windows NT 5.0	Windows 2000
// Windows NT 4.0	Microsoft Windows NT 4.0
// Windows 98; Win 9x 4.90	Windows Millennium Edition (Windows Me)
// Windows 98	Windows 98
// Windows 95	Windows 95
// Windows CE	Windows CE
// Windows NT 10.0      Windows 10


$db_uai = array(
  'ow' => 'os-winxp',
  'ow2' => 'os-winxp',
  'owx' => 'os-winxp',
  'owv' => 'os-winvista',
  'ow7' => 'os-win7',
  'ow8' => 'os-win8',
  'ow9' => 'os-win81',
  'ow10' => 'os-win10',
  'ow3' => 'os-winserver',
  'omc' => 'os-mac',
  'oih' => 'os-iphone',
  'oid' => 'os-ipad',
  'oa' => 'os-android',
  'oa2' => 'os-android2',
  'oa3' => 'os-android3',
  'oa4' => 'os-android4',
  'oa5' => 'os-android5',
  'ol' => 'os-linux',
  'olu' => 'os-ubuntu',
  'ojv' => 'os-java',
  'oy' => 'os-yandex',
  'og' => 'os-google',
  'oah' => 'os-ahrefs',
  'obg' => 'os-bing',
  'oyh' => 'os-yahoo',
  'osp' => 'os-sputnik',

  'x' => 'os-64',

  'bch' => 'br-chrome',
  'bff' => 'br-firefox',
  'bop' => 'br-opera',
  'bie' => 'br-ie',
  'bsf' => 'br-safari',
  'bya' => 'br-yandex',
  'bcu' => 'br-chromium',
  'bni' => 'br-nichrom',
  'bam' => 'br-amigo',
  'biw' => 'br-iceweasel',
  'bab' => '',
  'b' => '',
  'byi' => 'br-yandex',
  );




  // ------------------------------------------------ parse ua str ------------------------------------------------ //

function  parse_ua ($ua) {

  $gn = array();
  $oi = array();
  $vr = array();

  $osib = strpos($ua, '(');
  $gene = $osib;
  if ($gene === FALSE)  $gene = strlen($ua);
  $gens = trim(substr($ua,0,$gene));

  $pos = 0;
  while (isset($gens[$pos])) {
    if ($gens[$pos] == ' ') {
      $pos++;
      }

    elseif ($gens[$pos] == '(') {
      $pos++;
      $pose = strpos($gens, ')', $pos);
      if ($pose === FALSE)  $pose = strlen($gens);
      $gn[substr($gens, $pos-1, $pose-$pos+2)] = '';
      $pos = $pose + 1;
      }

    else {
      $pose = strpos($gens, ' ', $pos);
      if ($pose === FALSE)  $pose = strlen($gens);
      $tmp = explode('/', substr($gens, $pos, $pose-$pos));
      $gn[$tmp[0]] = (isset($tmp[1]) ? $tmp[1] : '');
      $pos = $pose + 1;
      }

    }


  if ($osib !== FALSE) {
    $osib++;
    $skip = $osib;

    $nest = 1;
    while ($nest) {
      $posb = strpos($ua, '(', $skip);
      $pose = strpos($ua, ')', $skip);

      if ($posb === FALSE)  $posb = $pose;
      if ($pose === FALSE)  {$skip = FALSE;  break;}

      if ($posb < $pose)  { $nest++;  $skip = $posb + 1; }
      else                { $nest--;  $skip = $pose + 1; }
      }

    if ($skip !== FALSE) {
      $os_infoe = explode(';', substr($ua, $osib, $skip-1-$osib));
      foreach ($os_infoe as $v) {
        $tmp = explode('/', trim($v));
        $oi[$tmp[0]] = (isset($tmp[1]) ? $tmp[1] : '');

        $tmp = explode(' ', trim($v));
        $tmp0 = array_shift($tmp);
        $oi['!'.$tmp0] = implode(' ',$tmp);
        }

      $varss = trim(substr($ua, $skip));

      $pos = 0;
      while (isset($varss[$pos])) {
        if ($varss[$pos] == ' ') {
          $pos++;
          }

        elseif ($varss[$pos] == '(') {
          $pos++;
          $pose = strpos($varss, ')', $pos);
          if ($pose === FALSE)  $pose = strlen($varss);
          $vr[substr($varss, $pos-1, $pose-$pos+2)] = '';
          $pos = $pose + 1;
          }

        else {
          $pose = strpos($varss, ' ', $pos);
          if ($pose === FALSE)  $pose = strlen($varss);
          $tmp = explode('/', substr($varss, $pos, $pose-$pos));
          $vr[$tmp[0]] = (isset($tmp[1]) ? $tmp[1] : '');
          $pos = $pose + 1;
          }

        }
      }

    }

  //$tmp = array();
  //foreach ($gn as $k=>$v)  $tmp[] = $k.' = '.$v;
  //
  //$tmp[] = '----------------';
  //
  ////$tmp[] = implode('<br>', $oi);
  //foreach ($oi as $k=>$v)  $tmp[] = $k.' = '.$v;
  //
  //$tmp[] = '----------------';
  //
  //foreach ($vr as $k=>$v)  $tmp[] = $k.' = '.$v;
  //$tmp = implode('<br>', $tmp);
  //
  //return  $tmp;


  $os = '';
  $os64 = 0;
  $br = '';
  $brv = 0;
  $brm = 0;

  $osn = 0;
  $brn = 0;


    // -------- Opera -------- //

  if (isset($oi['Opera Mini'])) {
    $br = 'Opera Mini';
    $brn = 3;
    $v = explode('.', $oi['Opera Mini']);
    if ($v[0])  $brv = $v[0];
    if (isset($v[1]))  $brm = $v[1];
    }


  elseif (isset($oi['Opera Mobi'])) {
    $br = 'Opera Mobile';
    $brn = 3;
    if (isset($vr['Version'])) {
      $v = explode('.', $vr['Version']);
      if ($v[0])  $brv = $v[0];
      if (isset($v[1]))  $brm = $v[1];
      }
    }


  elseif (isset($oi['Opera Tablet'])) {
    $br = 'Opera Tablet';
    $brn = 3;
    if (isset($vr['Version'])) {
      $v = explode('.', $vr['Version']);
      if ($v[0])  $brv = $v[0];
      if (isset($v[1]))  $brm = $v[1];
      }
    }


  elseif (isset($gn['Opera'])) {
    $br = 'Opera';
    $brn = 3;
    if ($gn['Opera'] == '9.80') {
      if (isset($vr['Version'])) {
        $v = explode('.', $vr['Version']);
        if ($v[0])  $brv = $v[0];
        if (isset($v[1]))  $brm = $v[1];
        }
      }
    else { // not 9.80
      $v = explode('.', $gn['Opera']);
      if ($v[0])  $brv = $v[0];
      if (isset($v[1]))  $brm = $v[1];
      }
    }


    // -------- OPR -------- //

  elseif (isset($vr['OPR'])) {
    $br = 'Opera Next';
    $brn = 3;
    $v = explode('.', $vr['OPR']);
    if ($v[0])  $brv = $v[0];
    if (isset($v[1]))  $brm = $v[1];
    }



    // -------- YaBrowser -------- //

  elseif (isset($vr['YaBrowser'])) {
    $br = 'Яндекс';
    $brn = 6;
    $v = explode('.', $vr['YaBrowser']);
    if ($v[0])  $brv = $v[0];
    if (isset($v[1]))  $brm = $v[1];
    }



    // -------- Chromium -------- //

  elseif (isset($vr['Chromium'])) {
    $br = 'Chromium';
    $brn = 7;
    $v = explode('.', $vr['Chromium']);
    if ($v[0])  $brv = $v[0];
    if (isset($v[1]))  $brm = $v[1];
    }



    // -------- Nichrome -------- //

  elseif (isset($vr['Nichrome'])) {
    $br = 'Нихром';
    $brn = 8;
    if (isset($vr['Chrome'])) {
      $v = explode('.', $vr['Chrome']);
      if ($v[0])  $brv = $v[0];
      if (isset($v[1]))  $brm = $v[1];
      }
    }



    // -------- Amigo -------- //

  elseif (isset($vr['Amigo'])) {
    $br = 'Амиго';
    $brn = 9;
    $v = explode('.', $vr['Amigo']);
    if ($v[0])  $brv = $v[0];
    if (isset($v[1]))  $brm = $v[1];
    }


    // -------- Amigo -------- //

  elseif (isset($vr['MRCHROME'])) {
    $br = 'Амиго';
    $brn = 9;
    if (isset($vr['Chrome'])) {
      $v = explode('.', $vr['Chrome']);
      if ($v[0])  $brv = $v[0];
      if (isset($v[1]))  $brm = $v[1];
      }
    }
  // MRCHROME - Интернет@mail.ru
  // ODKL - ? Одноклассники
  // SOC - ? туда же



    // -------- ChromePlus -------- //

  elseif (isset($vr['ChromePlus'])) {
    $br = 'ChromePlus';
    $v = explode('.', $vr['ChromePlus']);
    if ($v[0])  $brv = $v[0];
    if (isset($v[1]))  $brm = $v[1];
    }


    // -------- CoolNovo -------- //

  elseif (isset($vr['CoolNovo'])) {
    $br = 'CoolNovo';
    $v = explode('.', $vr['CoolNovo']);
    if ($v[0])  $brv = $v[0];
    if (isset($v[1]))  $brm = $v[1];
    }
  // CoolNovoChromePlus



    // -------- Iron -------- //

  elseif (isset($vr['Iron'])) {
    $br = 'Iron';
    $v = explode('.', $vr['Iron']);
    if ($v[0])  $brv = $v[0];
    if (isset($v[1]))  $brm = $v[1];
    }



    // -------- Comodo_Dragon -------- //

  elseif (isset($vr['Comodo_Dragon'])) {
    $br = 'Comodo Dragon';
    $v = explode('.', $vr['Comodo_Dragon']);
    if ($v[0])  $brv = $v[0];
    if (isset($v[1]))  $brm = $v[1];
    }



    // -------- Maxthon -------- //

  elseif (isset($vr['Maxthon'])) {
    $br = 'Maxthon';
    $v = explode('.', $vr['Maxthon']);
    if ($v[0])  $brv = $v[0];
    if (isset($v[1]))  $brm = $v[1];
    }



    // -------- PlayFreeBrowser -------- //

  elseif (isset($vr['PlayFreeBrowser'])) {
    $br = 'PlayFreeBrowser';
    $v = explode('.', $vr['PlayFreeBrowser']);
    if ($v[0])  $brv = $v[0];
    if (isset($v[1]))  $brm = $v[1];
    }



    // -------- CriOS -------- //

  elseif (isset($vr['CriOS'])) {
    $br = 'Chrome iOS';
    $brn = 1;
    $v = explode('.', $vr['CriOS']);
    if ($v[0])  $brv = $v[0];
    if (isset($v[1]))  $brm = $v[1];
    }


    // -------- MS Edge -------- //

  elseif (isset($vr['Edge'])) {
    $br = 'Edge';
    $brn = 14;
    $v = explode('.', $vr['Edge']);
    if ($v[0])  $brv = $v[0];
    if (isset($v[1]))  $brm = $v[1];
    }



// Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/42.0.2311.135 Safari/537.36 Edge/12.10240
// Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/46.0.2486.0 Safari/537.36 Edge/13.10586

  // RockMelt for Macintosh
  // Flock
  // Awesomium
  // Valve Steam GameOverlay


    // -------- Chrome -------- //

  elseif (isset($vr['Chrome'])) {

    if (isset($oi['Valve Steam GameOverlay'])) {
      $br = 'Steam';
      $v = explode('.', $vr['Chrome']);
      if ($v[0])  $brv = $v[0];
      if (isset($v[1]))  $brm = $v[1];
      }
    else {
      $br = 'Chrome';
      $brn = 1;
      $v = explode('.', $vr['Chrome']);
      if ($v[0])  $brv = $v[0];
      if (isset($v[1]))  $brm = $v[1];
      }
    }




    // -------- Fennec -------- //

  elseif (isset($vr['Fennec'])) {
    $br = 'Fennec';
    $v = explode('.', $vr['Fennec']);
    if ($v[0])  $brv = $v[0];
    if (isset($v[1]))  $brm = $v[1];
    }



    // -------- Camino -------- //

  elseif (isset($vr['Camino'])) {
    $br = 'Camino';
    $v = explode('.', $vr['Camino']);
    if ($v[0])  $brv = $v[0];
    if (isset($v[1]))  $brm = $v[1];
    }



    // -------- Skyfire -------- //

  elseif (isset($vr['Skyfire'])) {
    $br = 'Skyfire';
    $v = explode('.', $vr['Skyfire']);
    if ($v[0])  $brv = $v[0];
    if (isset($v[1]))  $brm = $v[1];
    }



    // -------- SeaMonkey -------- //

  elseif (isset($vr['SeaMonkey'])) {
    $br = 'SeaMonkey';
    $v = explode('.', $vr['SeaMonkey']);
    if ($v[0])  $brv = $v[0];
    if (isset($v[1]))  $brm = $v[1];
    }



    // -------- PaleMoon -------- //

  elseif (isset($vr['PaleMoon'])) {
    $br = 'PaleMoon';
    $v = explode('.', $vr['PaleMoon']);
    if ($v[0])  $brv = $v[0];
    if (isset($v[1]))  $brm = $v[1];
    }



    // -------- IceDragon -------- //

  elseif (isset($vr['IceDragon'])) {
    $br = 'IceDragon';
    $v = explode('.', $vr['IceDragon']);
    if ($v[0])  $brv = $v[0];
    if (isset($v[1]))  $brm = $v[1];
    }



    // -------- Waterfox -------- //

  elseif (isset($vr['Waterfox'])) {
    $br = 'Waterfox';
    $v = explode('.', $vr['Waterfox']);
    if ($v[0])  $brv = $v[0];
    if (isset($v[1]))  $brm = $v[1];
    }



    // -------- CometBird -------- //

  elseif (isset($vr['CometBird'])) {
    $br = 'CometBird';
    $v = explode('.', $vr['CometBird']);
    if ($v[0])  $brv = $v[0];
    if (isset($v[1]))  $brm = $v[1];
    }



    // -------- Firebird -------- //

  elseif (isset($vr['Firebird'])) {
    $br = 'Firebird';
    $v = explode('.', $vr['Firebird']);
    if ($v[0])  $brv = $v[0];
    if (isset($v[1]))  $brm = $v[1];
    }



    // -------- Iceweasel -------- //

  elseif (isset($vr['Iceweasel'])) {
    $br = 'Iceweasel';
    $brn = 10;
    $v = explode('.', $vr['Iceweasel']);
    if ($v[0])  $brv = $v[0];
    if (isset($v[1]))  $brm = $v[1];
    }



    // -------- Minefield -------- //

  elseif (isset($vr['Minefield'])) {
    $br = 'Minefield';
    $v = explode('.', $vr['Minefield']);
    if ($v[0])  $brv = $v[0];
    if (isset($v[1]))  $brm = $v[1];
    }




    // -------- Firefox -------- //

  elseif (isset($vr['Firefox'])) {
    $br = 'Firefox';
    $brn = 2;
    $v = explode('.', $vr['Firefox']);
    if ($v[0])  $brv = $v[0];
    if (isset($v[1]))  $brm = $v[1];
    }




    // -------- Internet Explorer -------- //

  elseif (isset($oi['!MSIE'])) {
    if (isset($oi['chromeframe'])) {
      $br = 'IE (chromeframe';
      $brn = 4;
      $v = explode('.', $oi['chromeframe']);
      if ($v[0])  $br .= ' '.$v[0];
      if (isset($v[1]))  $br .= '.'.$v[1];
      $br .= ')';
      $v = explode('.', $oi['!MSIE']);
      if ($v[0])  $brv = $v[0];
      if (isset($v[1]))  $brm = $v[1];
      }
    else {
      $br = 'Internet Explorer';
      $brn = 4;
      $v = explode('.', $oi['!MSIE']);
      if ($v[0])  $brv = $v[0];
      if (isset($v[1]))  $brm = $v[1];
      }
    }



    // -------- Internet Explorer 11+ -------- //

  elseif (isset($oi['Trident'])) {
    if (isset($oi['rv:11.0'])) {
      $br = 'Internet Explorer';
      $brn = 4;
      $brv = '11';
      }
    elseif (isset($oi['rv:12.0'])) {
      $br = 'Internet Explorer';
      $brn = 4;
      $brv = '12';
      }
    }






    // ---------------- OS version ---------------- //

  if (isset($oi['Windows NT 5.1'])) {
    $os = 'Windows XP';
    $osn = 2;
    }
  elseif (isset($oi['Windows NT 6.0'])) {
    $os = 'Windows Vista';
    $osn = 4;
    }
  elseif (isset($oi['Windows NT 6.1'])) {
    $os = 'Windows 7';
    $osn = 5;
    }
  elseif (isset($oi['Windows NT 6.2'])) {
    $os = 'Windows 8';
    $osn = 6;
    }
  elseif (isset($oi['Windows NT 6.3'])) {
    $os = 'Windows 8.1';
    $osn = 7;
    }
  elseif (isset($oi['Windows NT 6.4'])) {
    $os = 'Windows 10 beta';
    $osn = 8;
    }
  elseif (isset($oi['Windows NT 10.0'])) {
    $os = 'Windows 10';
    $osn = 8;
    }
  elseif (isset($oi['Windows NT 5.0'])) {
    $os = 'Windows 2000';
    $osn = 1;
    }
  elseif (isset($oi['Windows NT 5.01'])) {
    $os = 'Windows 2000 SP1';
    $osn = 1;
    }
  elseif (isset($oi['Windows NT 5.2'])) {
    $os = 'Windows Server 2003 / XP x64';
    $osn = 3;
    }

  //elseif (isset($oi['']))  $os = '';

  elseif (isset($oi['!Android'])) {
    $os = 'Android'.($oi['!Android']?' '.$oi['!Android']:'');
    $v = substr($oi['!Android'],0,1);
    if ($v == 2)  $osn = 14;
    elseif ($v == 3)  $osn = 15;
    elseif ($v == 4)  $osn = 16;
    elseif ($v == 5)  $osn = 17;
    elseif ($v == 6)  $osn = 27;
    else  $osn = 13;

    if (!$br) {
      $br = 'Android browser';
      $brn = 11;
      $v = explode('.', $oi['!Android']);
      if ($v[0])  $brv = $v[0];
      if (isset($v[1]))  $brm = $v[1];
      }
    }

  elseif (isset($oi['iPhone'])) {
    $os = 'iPhone';
    $osn = 11;
    }
  elseif (isset($oi['iPod'])) {
    $os = 'iPod';
    $osn = 11;
    }
  elseif (isset($oi['iPod touch'])) {
    $os = 'iPod';
    $osn = 11;
    }
  elseif (isset($oi['iPad'])) {
    $os = 'iPad';
    $osn = 12;
    }
  elseif (isset($oi['Macintosh'])) {
    $os = 'Macintosh';
    $osn = 10;
    }

  elseif (isset($oi['X11'])) {
    $os = 'Linux';
    $osn = 18;

    if (isset($vr['Ubuntu'])) {
      $os = 'Ubuntu';
      $osn = 19;
      $v = explode('.', $vr['Ubuntu']);
      if ($v[0])  $os .= ' '.$v[0];
      if (isset($v[1]))  $os .= '.'.$v[1];
      }

    elseif (isset($oi['!CrOS'])) {
      $os = 'ChromeOS';
      }

    elseif (isset($oi['!FreeBSD'])) {
      $os = 'FreeBSD';
      }

    elseif (isset($oi['!OpenBSD'])) {
      $os = 'OpenBSD';
      }

    elseif (isset($gn['ArchLinux'])) {
      $os = 'ArchLinux';
      $v = explode('.', $gn['ArchLinux']);
      if ($v[0])  $os .= ' '.$v[0];
      if (isset($v[1]))  $os .= '.'.$v[1];
      }

    elseif (isset($gn['Slackware'])) {
      $os = 'Slackware';
      $v = explode('.', $gn['Slackware']);
      if ($v[0])  $os .= ' '.$v[0];
      if (isset($v[1]))  $os .= '.'.$v[1];
      }

    elseif (isset($vr['CentOS'])) {
      $os = 'CentOS';
      $v = explode('.', $vr['CentOS']);
      if ($v[0])  $os .= ' '.$v[0];
      if (isset($v[1]))  $os .= '.'.$v[1];
      }

    elseif (isset($vr['Hat'])) {
      $os = 'Red Hat';
      $v = explode('.', $vr['Hat']);
      if ($v[0])  $os .= ' '.$v[0];
      if (isset($v[1]))  $os .= '.'.$v[1];
      }

    elseif (isset($vr['Fedora'])) {
      $os = 'Fedora';
      $v = explode('.', $vr['Fedora']);
      if ($v[0])  $os .= ' '.$v[0];
      if (isset($v[1]))  $os .= '.'.$v[1];
      }

    elseif (isset($vr['SUSE'])) {
      $os = 'SUSE';
      $v = explode('.', $vr['SUSE']);
      if ($v[0])  $os .= ' '.$v[0];
      if (isset($v[1]))  $os .= '.'.$v[1];
      }

    elseif (isset($vr['Mint'])) {
      $os = 'Linux Mint';
      $v = explode('.', $vr['Mint']);
      if ($v[0])  $os .= ' '.$v[0];
      if (isset($v[1]))  $os .= '.'.$v[1];
      }

    elseif (isset($vr['ALTLinux'])) {
      $os = 'ALTLinux';
      }

    elseif (isset($vr['Gentoo'])) {
      $os = 'Gentoo';
      }

    }

  elseif (isset($oi['Windows Mobile'])) {
    $os = 'Windows Mobile';
    $osn = 9;
    }
  elseif (isset($oi['Windows Phone OS 7.5'])) {
    $os = 'Windows Phone 7.5';
    $osn = 9;
    }
  elseif (isset($oi['Windows Phone 8.0'])) {
    $os = 'Windows Phone 8.0';
    $osn = 9;
    }

  elseif (isset($oi['Series40'])) {
    $os = 'Series40';
    }
  elseif (isset($oi['Series 60'])) {
    $os = 'Series 60';
    }
  elseif (isset($oi['S60'])) {
    $os = 'S60';
    }
  elseif (isset($oi['Symbian'])) {
    $os = 'Symbian';
    }
  elseif (isset($oi['SymbianOS'])) {
    $os = 'SymbianOS';
    }


  elseif (isset($oi['J2ME'])) {
    $os = 'Java';
    $osn = 20;
    }
  elseif (isset($oi['MAUI Runtime'])) {
    $os = 'MAUI Runtime';
    }
  elseif (isset($oi['MTK'])) {
    $os = 'MTK';
    }
  elseif (isset($oi['SpreadTrum'])) {
    $os = 'SpreadTrum';
    }
  elseif (isset($oi['AmigaOS'])) {
    $os = 'AmigaOS';
    }

  elseif (isset($oi['Windows NT 4.0'])) {
    $os = 'Windows NT 4.0';
    $osn = 9;
    }
  elseif (isset($oi['Windows 98'])) {
    $os = 'Windows 98';
    $osn = 9;
    }
  elseif (isset($oi['Windows 95'])) {
    $os = 'Windows 95';
    $osn = 9;
    }
  elseif (isset($oi['Windows CE'])) {
    $os = 'Windows CE';
    $osn = 9;
    }
  elseif (isset($oi['Win98'])) {
    $os = 'Windows 98';
    $osn = 9;
    }
  elseif (isset($oi['Win 9x 4.90'])) {
    $os = 'Windows Millennium';
    $osn = 9;
    }
  elseif (isset($oi['Windows NT'])) {
    $os = 'Windows NT';
    $osn = 9;
    }



  if (isset($oi['WOW64']))  $os64 = 1;
  elseif (isset($oi['Linux x86_64']))  $os64 = 1;  // Linux i686, Linux i686 on x86_64
  elseif (isset($oi['Linux amd64']))  $os64 = 1;
  elseif (isset($oi['Win64']))  $os64 = 1;
  elseif (isset($oi['x64']))  $os64 = 1;


    // ---------------- robots ---------------- //

    // -------- Yandex -------- //

  if (isset($oi['YandexBot'])) {
    $os = 'Yandex robot';
    $osn = 21;
    $brn = 12;
    $v = explode('.', $oi['YandexBot']);
    if ($v[0])  $brv = $v[0];
    if (isset($v[1]))  $brm = $v[1];
    }

  elseif (isset($oi['YandexImages'])) {
    $os = 'Yandex Images robot';
    $osn = 21;
    $brn = 13;
    $v = explode('.', $oi['YandexImages']);
    if ($v[0])  $brv = $v[0];
    if (isset($v[1]))  $brm = $v[1];
    }

  elseif (isset($oi['YandexFavicons'])) {
    $os = 'Yandex robot';
    $osn = 21;
    $brn = 12;
    $v = explode('.', $oi['YandexFavicons']);
    if ($v[0])  $brv = $v[0];
    if (isset($v[1]))  $brm = $v[1];
    }

  elseif (isset($oi['YandexWebmaster'])) {
    $os = 'Yandex robot';
    $osn = 21;
    $brn = 12;
    $v = explode('.', $oi['YandexWebmaster']);
    if ($v[0])  $brv = $v[0];
    if (isset($v[1]))  $brm = $v[1];
    }

  elseif (isset($oi['YandexAntivirus'])) {
    $os = 'Yandex robot';
    $osn = 21;
    $brn = 12;
    $v = explode('.', $oi['YandexAntivirus']);
    if ($v[0])  $brv = $v[0];
    if (isset($v[1]))  $brm = $v[1];
    }

  elseif (isset($oi['YandexImageResizer'])) {
    $os = 'Yandex robot';
    $osn = 21;
    $brn = 12;
    $v = explode('.', $oi['YandexImageResizer']);
    if ($v[0])  $brv = $v[0];
    if (isset($v[1]))  $brm = $v[1];
    }

  elseif (isset($oi['YandexDirect'])) {
    $os = 'Yandex robot';
    $osn = 21;
    $brn = 12;
    $v = explode('.', $oi['YandexDirect']);
    if ($v[0])  $brv = $v[0];
    if (isset($v[1]))  $brm = $v[1];
    }

  elseif (isset($oi['YandexMetrika'])) {
    $os = 'Yandex robot';
    $osn = 21;
    $brn = 12;
    $v = explode('.', $oi['YandexMetrika']);
    if ($v[0])  $brv = $v[0];
    if (isset($v[1]))  $brm = $v[1];
    }

  elseif (isset($oi['YandexPagechecker'])) {
    $os = 'Yandex robot';
    $osn = 21;
    $brn = 12;
    $v = explode('.', $oi['YandexPagechecker']);
    if ($v[0])  $brv = $v[0];
    if (isset($v[1]))  $brm = $v[1];
    }

  elseif (isset($oi['YandexMedia'])) {
    $os = 'Yandex robot';
    $osn = 21;
    $brn = 12;
    $v = explode('.', $oi['YandexMedia']);
    if ($v[0])  $brv = $v[0];
    if (isset($v[1]))  $brm = $v[1];
    }

  elseif (isset($oi['YandexSitelinks'])) {
    $os = 'Yandex robot';
    $osn = 21;
    $brn = 12;
    $v = explode('.', $oi['YandexSitelinks']);
    if ($v[0])  $brv = $v[0];
    if (isset($v[1]))  $brm = $v[1];
    }

  elseif (isset($oi['YandexMarket'])) {
    $os = 'Yandex robot';
    $osn = 21;
    $brn = 12;
    $v = explode('.', $oi['YandexMarket']);
    if ($v[0])  $brv = $v[0];
    if (isset($v[1]))  $brm = $v[1];
    }

  elseif (isset($oi['YandexCatalog'])) {
    $os = 'Yandex robot';
    $osn = 21;
    $brn = 12;
    $v = explode('.', $oi['YandexCatalog']);
    if ($v[0])  $brv = $v[0];
    if (isset($v[1]))  $brm = $v[1];
    }


    // -------- Google -------- //

  elseif (isset($oi['Googlebot'])) {
    $os = 'Google robot';
    $osn = 22;
    $brn = 12;
    $v = explode('.', $oi['Googlebot']);
    if ($v[0])  $brv = $v[0];
    if (isset($v[1]))  $brm = $v[1];
    }

  elseif (isset($vr['(compatible; Googlebot/2.1; +http://www.google.com/bot.html)'])) {
    $os = 'Google robot cloaked';
    $osn = 22;
    $brn = 5;
    //$v = explode('.', $vr['Googlebot']);
    //if ($v[0])  $brv = $v[0];
    //if (isset($v[1]))  $brm = $v[1];
    }

  elseif (isset($vr['(compatible; Googlebot-Mobile/2.1; +http://www.google.com/bot.html)'])) {
    $os = 'Google robot cloaked';
    $osn = 22;
    $brn = 5;
    }


    // -------- other robots -------- //

  elseif (isset($oi['AhrefsBot'])) {
    $os = 'Ahrefs robot';
    $osn = 23;
    $brn = 12;
    $v = explode('.', $oi['AhrefsBot']);
    if ($v[0])  $brv = $v[0];
    if (isset($v[1]))  $brm = $v[1];
    }

  elseif (isset($oi['bingbot'])) {
    $os = 'Bing robot';
    $osn = 24;
    $brn = 12;
    $v = explode('.', $oi['bingbot']);
    if ($v[0])  $brv = $v[0];
    if (isset($v[1]))  $brm = $v[1];
    }

  elseif (isset($oi['Yahoo! Slurp'])) {
    $os = 'Yahoo robot';
    $osn = 25;
    $brn = 12;
    $v = explode('.', $oi['Yahoo! Slurp']);
    if ($v[0])  $brv = $v[0];
    if (isset($v[1]))  $brm = $v[1];
    }

  elseif (isset($oi['SputnikBot'])) {
    $os = 'Sputnik robot';
    $osn = 26;
    $brn = 12;
    $v = explode('.', $oi['SputnikBot']);
    if ($v[0])  $brv = $v[0];
    if (isset($v[1]))  $brm = $v[1];
    }

  elseif (isset($oi['SputnikFaviconBot'])) {
    $os = 'Sputnik Favicon robot';
    $osn = 26;
    $brn = 12;
    $v = explode('.', $oi['SputnikFaviconBot']);
    if ($v[0])  $brv = $v[0];
    if (isset($v[1]))  $brm = $v[1];
    }


    // -------- correct -------- //

  if ($osn == 3 && !$os64) {
    $os = 'Windows Server 2003';
    }


  if (($osn == 11 || $osn == 12) && !$br) {
    $br = 'Safari';
    $brn = 5;
    if (isset($oi['!CPU'])) {
      $v = explode(' ', $oi['!CPU']);
      if (isset($v[0]) && $v[0] == 'OS' && isset($v[1])) {
        $v = explode('_', $v[1]);
        if ($v[0])  $brv = $v[0];
        if (isset($v[1]))  $brm = $v[1];
        }
      elseif (isset($v[1]) && $v[1] == 'OS' && isset($v[2])) {
        $v = explode('_', $v[2]);
        if ($v[0])  $brv = $v[0];
        if (isset($v[1]))  $brm = $v[1];
        }
      }
    elseif (isset($vr['Version'])) {
      $v = explode('.', $vr['Version']);
      if ($v[0])  $brv = $v[0];
      if (isset($v[1]))  $brm = $v[1];
      }
    }

  elseif ($osn == 10 && !$br) {
    $br = 'Safari';
    $brn = 5;
    if (isset($vr['Version'])) {
      $v = explode('.', $vr['Version']);
      if ($v[0])  $brv = $v[0];
      if (isset($v[1]))  $brm = $v[1];
      }
    elseif (isset($oi['!Intel'])) {
      $v = explode(' ', $oi['!Intel']);
      if (isset($v[1]) && $v[1] == 'OS' && isset($v[2])) {
        $v = explode('_', $v[2]);
        if ($v[0])  $brv = $v[0];
        if (isset($v[1]))  $brm = $v[1];
        }
      elseif (isset($v[0]) && $v[0] == 'OS' && isset($v[1])) {
        $v = explode('_', $v[1]);
        if ($v[0])  $brv = $v[0];
        if (isset($v[1]))  $brm = $v[1];
        }
      }
    }


  return array(
    'o' => $os,
    'on' => $osn,
    'x' => $os64,
    'b' => $br,
    'bn' => $brn,
    'v' => $brv,
    'm' => $brm,
    );
  }






  // ------------------------------------------------ ua to str ------------------------------------------------ //

function  ua_str ($ua) {
  $uap = parse_ua($ua);
  $tmp = array();
  if ($uap['b']) {
    $tmpb = $uap['b'];
    if ($uap['v']) {
      $tmpb .= ' '.$uap['v'];
      if ($uap['m'])  $tmpb .= '.'.$uap['m'];
      }
    $tmp[] = $tmpb;
    }
  if ($uap['o']) {
    $tmpo = $uap['o'];
    if ($uap['x'])  $tmpo .= ' x64';
    $tmp[] = $tmpo;
    }

  return  implode(', ', $tmp);
  }


?>