<?php
//  ------------------------------------------------------------------------ //
// 本模組由 prolin 製作
// 製作日期：2014-07-01
// $Id:$
// ------------------------------------------------------------------------- //

/*-----------引入檔案區--------------*/
include_once 'header_admin.php';

include_once 'header.php';

if ($_GET['id'] and $_GET['setdata']) {
    list($name, $id) = preg_split('/[_]/', $_GET['id']);
    $nsubj = trim($_GET['setdata']);
    $id = intval($id);

    if ($name == 'scope') {
        $sql = ' UPDATE  '.$xoopsDB->prefix('es_timetable_subject')." SET subject_scope ='$nsubj'  where   subject_id= '$id' ";
    }
    if ($name == 'esubject') {
        $sql = ' UPDATE  '.$xoopsDB->prefix('es_timetable_subject')." SET e_subject ='$nsubj'  where   subject_id= '$id' ";
    }
    if ($name == 'ssubject') {
        $sql = ' UPDATE  '.$xoopsDB->prefix('es_timetable_subject')." SET s_subject ='$nsubj'  where   subject_id= '$id' ";
    }
    if ($name == 'name') {
        $sql = ' UPDATE  '.$xoopsDB->prefix('es_timetable_subject')." SET subject_name ='$nsubj'  where   subject_id= '$id' ";
    }

    $result = $xoopsDB->queryF($sql) or die($sql.'<br>'.$xoopsDB->error());
    echo $_GET['do'].$sql;
}
