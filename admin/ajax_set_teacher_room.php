<?php
//  ------------------------------------------------------------------------ //
// 本模組由 prolin 製作
// 製作日期：2014-07-01
// $Id:$
// ------------------------------------------------------------------------- //

/*-----------引入檔案區--------------*/
include_once "header.php";
include_once "../function.php";

if ($_GET['id']) {
    $id = intval($_GET['id']);
    $myts = &MyTextSanitizer::getInstance();
    $new = $myts->htmlspecialchars($xoopsDB->escape($_GET['setdata']));
        //$new = $_GET['setdata'] ;
    if ($_GET['do'] == 'plus') {
        $kind = intval($new);
        $sql = ' UPDATE  '.$xoopsDB->prefix('es_timetable')." SET c_kind = '$kind'  where   course_id= '$id' ";
    } else {
        $sql = ' UPDATE  '.$xoopsDB->prefix('es_timetable')." SET room = '$new'  where   course_id= '$id' ";
    }

    $result = $xoopsDB->queryF($sql) or die($sql.'<br>'.$xoopsDB->error());
    //echo $_GET['do'] . $sql ;
}