<?php

/************************************************************************/
/*  Люди  v1.oo                                                         */
/************************************************************************/


if (!isset($body))  die ('error: this is a pluggable module and cannot be used standalone.');

$gid = getn('id',1);
//$gppl = getn('ppl');
$gsym = getn('sym');

$gdate = gets('date', $curr['date']);


include 'm/'.$mod.'/const.php';






  // -------------------------------- embed: именинники v2 -------------------------------- //

if (!$act) {

    // ---- collect birthdays ---- //

  $where = array();
  $where[] = '`people`.`id` = `people_junc`.`t`';
  $where[] = '`people_junc`.`f` = '.$gid;
  $where[] = 'MONTH(`birthdate`) != 0';
  if ($gsym)  $where[] = '`people_junc`.`symp` <= '.$gsym;

  $bdpeople = db_read(array('table' => array('people', 'people_junc'),
                            'col' => array('people`.`id', 'people`.`surname', 'people`.`name', 'people`.`otchestvo', 'people`.`birthdate',
                                           '!MONTH(`people`.`birthdate`) AS `bd_mon`', '!DAYOFMONTH(`people`.`birthdate`) AS `bd_day`',
                                           'people_junc`.`symp',
                                           ),
                            'order' => '`people_junc`.`symp`',
                            'where' => $where,

                            'key' => array('bd_mon', 'bd_day', 'id'),
                            ));


  //b('<p class="f16 b c">Именинники</p>');
  $title = 'Именинники';
  b('<table class="tabc">');

  $day_limit = 30;
  $date = $curr['date'];
  while ($day_limit > 0) {
    $year = datee($date);
    $mon = datee($date,'m');
    $day = datee($date,'d');
    $wkd = date('w', datesqltime($date));

    b('<tr>');
    b('<td class="li');
    //if ($wkd == 6 || $wkd == 0)  b(' bgr');
    b('" style="width:300px; height: 76px; vertical-align: top;');
    if ($wkd == 6 || $wkd == 0)  b(' background-color: #fdd;');
    b('">');

    b('<p style="text-align: center; font-size: 8pt; font-weight: bold;">');
    b(dateh($date).', '.$weekdayn[$wkd]);

    if (isset($bdpeople[$mon][$day])) {
      //$n = 1;
      foreach ($bdpeople[$mon][$day] as $k=>$v) {
        b('<p class="f10"');
        //b('< style="opacity:">');
        if ($v['symp'] > 69)  b(' style="opacity: 0.2;"');
        elseif ($v['symp'] > 59)  b(' style="opacity: 0.4;"');
        elseif ($v['symp'] > 49)  b(' style="opacity: 0.6;"');
        b('>');
        //b(($n++).'. ');
        b(fiof($v['surname'], $v['name'], $v['otchestvo']));
        $byear = datee($v['birthdate']);
        if ($byear) {
          $age = $year - $byear;
          b(', '.$age.' '.pend($age,'year'));
          }
        }
      }


    $date = datesql(mktime (0,0,0, $mon, $day+1, $year));
    $day_limit--;
    }  // end: while

  b('</table>');

//  b('
//<style>
//body {
//  overflow: hidden;
//  }
//</style>
//');

  b('
<script>

//  var vw = window.innerWidth;
//  var vh = window.innerHeight;
//  var vw = window.outerWidth;
//  var vh = window.outerHeight;

//  var vw = window.screen.availWidth;
//  var vh = window.screen.availHeight;

window.moveTo(window.screen.availWidth - 344,0);
window.resizeTo(344,window.screen.availHeight);

</script>
');
  }




  // -------------------------------- embed: именинники v1 -------------------------------- //

if ($act == 'v1') {

    // ---- collect birthdays ---- //

  $where = array();
  $where[] = '`people`.`id` = `people_junc`.`t`';
  $where[] = '`people_junc`.`f` = '.$gid;
  $where[] = 'MONTH(`birthdate`)';
  if ($gsym)  $where[] = '`people_junc`.`symp` <= '.$gsym;

  $bdpeople = db_read(array('table' => array('people', 'people_junc'),
                            'col' => array('people`.`id', 'people`.`surname', 'people`.`name', 'people`.`otchestvo', 'people`.`birthdate',
                                           '!MONTH(`people`.`birthdate`) AS `bd_mon`', '!DAYOFMONTH(`people`.`birthdate`) AS `bd_day`'
                                           ),
                            'where' => $where,
                            'order' => array('`bd_mon`', '`bd_day`'),

                            'key' => array('bd_mon', 'bd_day', 'id'),
                            ));



    // -------- output birthdates -------- //
  if ($bdpeople) {
    b('<p class="f16 b c">Именинники</p>');
    b('<table class="tabc">');
    b('<tr>');
  
    $row_limit = 30;
    $day_limit = 365;
    $curr_date = $curr['date'];
    while ($row_limit > 0 && $day_limit > 0) {
  
      $curr_mon = datee($curr_date, 'm');
      $curr_day = datee($curr_date, 'd');
  
      if (isset($bdpeople[$curr_mon][$curr_day])) {
  
        $first = 1;
        foreach ($bdpeople[$curr_mon][$curr_day] as $ksot=>$vsot) {
  
          //$age = $curr['year'] - datee($vsot['birthdate'], 'y') + (($curr['mon'] > datee($vsot['birthdate'], 'm')) ? 1 : 0);
          $age = dateage($vsot['birthdate'], array('e'=>0));
          $days = datediff ($curr['date'], $curr_date);
          if ($days)  $age += 1;
          b('<tr>');
  
          if ($first) {
            b('<td class="li"');
            if (count($bdpeople[$curr_mon][$curr_day]) > 1)  b(' rowspan="'.count($bdpeople[$curr_mon][$curr_day]).'"');
            b(' width="106">');
            b(datee($vsot['birthdate'], 'D').'.'.datee($vsot['birthdate'], 'M'));
            b(', ');
            b($weekdaynss[date('w', datesqltime($curr_date))]);
            if ($days)  b(', '.$days);
    
            $first = 0;
            }
    
          b('<td class="li');
          if (!$days)  b(' bgr');
          b('" width="290">');
    
          b('<abbr');
          b(' title="');
          //b($age.' '.ends($age, 'year').' ('.datee($vsot['birthdate'], 'y').' г.р.)');
          b($age.' '.' ('.datee($vsot['birthdate'], 'y').' г.р.)');
          b('">');
    
          b(fiof($vsot['surname'], $vsot['name'], $vsot['otchestvo']));
    
          b('</abbr>');
  
          $row_limit--;
          }
        }
  
      $curr_date = datesql(mktime (0,0,0, $curr_mon, $curr_day+1, datee($curr_date)));
      $day_limit--;
      }  // end: while

    b('</table>');
    }  //  end: if $bdpeople

  }




  // -------------------------------- embed: именинники calendar -------------------------------- //

if ($act == 'cdr') {

    // ---- collect birthdays ---- //

  $where = array();
  $where[] = '`people`.`id` = `people_junc`.`t`';
  $where[] = '`people_junc`.`f` = '.$gid;
  $where[] = 'MONTH(`birthdate`) != 0';
  if ($gsym)  $where[] = '`people_junc`.`symp` <= '.$gsym;

  $bdpeople = db_read(array('table' => array('people', 'people_junc'),
                            'col' => array('people`.`id', 'people`.`surname', 'people`.`name', 'people`.`otchestvo', 'people`.`birthdate',
                                           '!MONTH(`people`.`birthdate`) AS `bd_mon`', '!DAYOFMONTH(`people`.`birthdate`) AS `bd_day`',
                                           'people_junc`.`symp',
                                           ),
                            'order' => '`people_junc`.`symp`',
                            'where' => $where,

                            'key' => array('bd_mon', 'bd_day', 'id'),
                            ));


  $gyear = datee($gdate);
  $gmon = datee($gdate,'m');
  $gday = datee($gdate,'d');
  $gdays = date('t', mktime(0, 0, 0, datee($gdate,'m'), 1, datee($gdate)));

  //$datebeg = datesql(mktime(0, 0, 0, $gmon, $gday - date('N', mktime(0, 0, 0, $gmon, $gday, $gyear)) -6, $gyear));
  $datebeg = datesql(mktime(0, 0, 0, $gmon, $gday - date('N', mktime(0, 0, 0, $gmon, $gday, $gyear)) +1, $gyear));
  $dateend = datesql(mktime(0, 0, 0, datee($datebeg,'m'), datee($datebeg,'d')+41, datee($datebeg)));


  b('
<style>
td.vsc {
	text-align: left;
	padding: 0 0 0 2px;
	vertical-align: top;
	min-width: 188px;
	max-width: 188px;
	height: 100px;
	}

div.vsn {
	font-size: 10pt;
	float: right;
	width: 17px;
	height: 17px;
	margin: 2px;
	border: 1px solid #ccc;
	border-radius: 4px;
	text-align: center;
	}

div.vsv {
	font-size: 10pt;
	text-indent: -46px;
	padding-left: 46px;
	}

div.vsm {
	display: inline;
	}

div.vsd {
	color: #888;
	display: inline;
	cursor: pointer;
	}

a.vsd {
	color: #888;
	}
</style>
');


  //b('<p class="f16 b c">Именинники</p>');
  $title = 'Именинники';
  b('<table class="tabc">');


  $date = $datebeg;
  while (1) {
    b('<tr>');

    for ($x = 0; $x < 7; $x++) {
      $year = datee($date);
      $mon = datee($date,'m');
      $day = datee($date,'d');
      $wkd = date('w', datesqltime($date));

      b('<td class="vsc"');
      if (datee($date,'m') != $gmon)  b(' style="opacity: 0.3;');
      b('">');

      b('<div class="vsn"');
      if ($date == $curr['date'])  b(' style="background-color: #ff8;"');
      b('>');
      b(datee($date,'d'));
      b('</div>');

      if (isset($bdpeople[$mon][$day])) {

        foreach ($bdpeople[$mon][$day] as $k=>$v) {
          b('<p class="f10"');
          //b('< style="opacity:">');
          if ($v['symp'] > 69)  b(' style="opacity: 0.2;"');
          elseif ($v['symp'] > 59)  b(' style="opacity: 0.4;"');
          elseif ($v['symp'] > 49)  b(' style="opacity: 0.6;"');
          b('>');
          //b(($n++).'. ');
          //b(fiof($v['surname'], $v['name'], $v['otchestvo']));
          b(fiof($v['surname'], $v['name'], ''));
          $byear = datee($v['birthdate']);
          if ($byear) {
            $age = $year - $byear;
            b(', '.$age.' '.pend($age,'year'));
            }

          }
        }

      $date = datesql(mktime(0,0,0, datee($date,'m'), datee($date,'d')+1, datee($date)));
      }

    if (datesqltime($date) > datesqltime($dateend))  break;
    }


  b('</table>');

  b('
<script>

//window.moveTo(window.screen.availWidth - 350,0);
//window.resizeTo(350,window.screen.availHeight);

</script>
');
  }


?>