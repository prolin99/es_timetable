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
    $id = $_GET['id'];
    $new = $_GET['setdata'];

    if ($_GET['do'] == 'edit_kind') {
        $new = intval($_GET['setdata']);
        $sql = ' UPDATE  '.$xoopsDB->prefix('es_timetable_teacher')." SET kind = '$new'  where   teacher_id= '$id' ";
    } elseif ($_GET['do'] == 'hide') {
        $sql = ' UPDATE  '.$xoopsDB->prefix('es_timetable_teacher')." SET hide =( not hide)  where   teacher_id= '$id' ";
    } elseif ($_GET['do'] == 'user_id') {
        $sql = ' UPDATE  '.$xoopsDB->prefix('es_timetable_teacher')." SET user_id = '0'  where   teacher_id= '$id' and user_id= '$new' ";
    } else {
        $sql = ' UPDATE  '.$xoopsDB->prefix('es_timetable_teacher')." SET name = '$new'  where   teacher_id= '$id' ";
    }

    $result = $xoopsDB->queryF($sql) or die($sql.'<br>'.$xoopsDB->error());
    echo $_GET['do'].$sql;
}
