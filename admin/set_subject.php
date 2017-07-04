<?php
//  ------------------------------------------------------------------------ //
// 本模組由 prolin 製作
// 製作日期：2014-07-20
// $Id:$
// ------------------------------------------------------------------------- //

/*-----------引入檔案區--------------*/

//樣版
$xoopsOption['template_main'] = 'es_timet_ad_set_sub_tpl.html';
include_once "header.php";
include_once "../function.php";

/*-----------function區--------------*/
//取得參數
if ($_GET['do'] = 'del') {
    $id = $_GET['id'];
    $sql = ' DELETE FROM '.$xoopsDB->prefix('es_timetable_subject')."    where   subject_id= '$id' ";
    $result = $xoopsDB->queryF($sql) or die($sql.'<br>'.$xoopsDB->error());

    //年級科目
    $sql = ' DELETE FROM '.$xoopsDB->prefix('es_timetable_subject_year')."    where   subject_id= '$id' ";
    $result = $xoopsDB->queryF($sql) or die($sql.'<br>'.$xoopsDB->error());
}

//加入新科目，以逗號做分隔
if ($_POST['new_kmo']) {
    $myts = &MyTextSanitizer::getInstance();

    $kmo = preg_split('/[,]/', $myts->addSlashes($_POST['new_kmo']));
    foreach ($kmo as $k => $v) {
        $kmo_o = trim($v);
        $sql = ' INSERT INTO   '.$xoopsDB->prefix('es_timetable_subject').
                ' (subject_id ,`subject_name`, `subject_school`, `subject_kind`, `enable` ,subject_scope)  '.
                "  VALUES  ( 0 , '$kmo_o' , '','subject' ,'1'  ,'' )   ";
        $result = $xoopsDB->queryF($sql) or die($sql.'<br>'.$xoopsDB->error());
    }
}

/*-----------執行動作判斷區----------*/

//取得目前科目名
//$data['subject_name'] = get_subject_list() ;
$data['subject_name'] = get_subject_data_list();

//各科目使用的年級
$data['grade_subject'] = get_subject_grade_list();

/*-----------秀出結果區--------------*/

$xoopsTpl->assign('data', $data);
$xoopsTpl->assign('DEF_SET', $DEF_SET);

include_once 'footer.php';
