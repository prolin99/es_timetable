<?php
//  ------------------------------------------------------------------------ //
// 本模組由 prolin 製作
// 製作日期：2014-07-01
// $Id:$
// ------------------------------------------------------------------------- //

/*-----------引入檔案區--------------*/
include_once 'header.php';
include_once XOOPS_ROOT_PATH.'/header.php';

$_GET['year'] = intval($_GET['year']);
$_GET['semester'] = intval($_GET['semester']);
$_GET['class_id'] = intval($_GET['class_id']);

if ($_GET['year'] and $_GET['semester']  and $_GET['do']  and $_GET['class_id'] and $_GET['sect']) {
    list($name, $day, $sect) = preg_split('/[_]/', $_GET['sect']);
    list($tea0, $tea_id, $tea) = preg_split('/[_]/', $_GET['teacher']);
    //把舊節除去
    $nsubj = $_GET['setdata'];
    if ($_GET['do'] == 'del') {
        $sql = ' DELETE FROM  '.$xoopsDB->prefix('es_timetable')." where school_year= '{$_GET['year']}'  and  semester= '{$_GET['semester']}'   and day='$day' and sector='$sect'  and  class_id= '{$_GET['class_id']}' and  teacher= '$tea_id'    ";
        $result = $xoopsDB->queryF($sql) or die($sql.'<br>'.$xoopsDB->error());
        //echo $_GET['do'] . $sql ;
    }

    if ($_GET['do'] == 'add') {
        list($name, $day, $sect) = preg_split('/[_]/', $_GET['sect']);
        list($subj0, $subj_id, $subj) = preg_split('/[_]/', $_GET['subject']);

        /*
        //教師這節要去除
        $sql = " DELETE FROM  "  . $xoopsDB->prefix("es_timetable") .  " where school_year= '{$_GET['year']}'  and  semester= '{$_GET['semester']}'   and day='$day' and sector='$sect'  and  teacher= '$tea_id'    " ;
        $result = $xoopsDB->queryF($sql) or die($sql."<br>". $xoopsDB->error());
        echo $_GET['do'] . $sql ;
        */

        //班級這節要去除
        $sql = ' DELETE FROM  '.$xoopsDB->prefix('es_timetable')." where school_year= '{$_GET['year']}'  and  semester= '{$_GET['semester']}'   and day='$day' and sector='$sect'  and  class_id= '{$_GET['class_id']}' and  teacher= '$tea_id'   ";
        $result = $xoopsDB->queryF($sql) or die($sql.'<br>'.$xoopsDB->error());

        $sql = ' INSERT INTO   '.$xoopsDB->prefix('es_timetable').
                ' (`school_year`, `semester`, `class_id`, `teacher`,`day` , sector ,ss_id,room , self_chk )  '.
                "  VALUES  ( '{$_GET['year']}' , '{$_GET['semester']}','{$_GET['class_id']}' ,'$tea_id' ,'$day','$sect' , '$subj_id' , '{$_GET['room']}' , '1' )   ";
        $result = $xoopsDB->queryF($sql) or die($sql.'<br>'.$xoopsDB->error());
        //echo $_GET['do'] . $sql ;
    }
}
//echo $_GET['do'] .$_GET['year'] . $_GET['semester']  . $_GET['do']  . $_GET['class_id'] . $_GET['sect']  ;
;
