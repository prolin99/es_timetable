<?php
//  ------------------------------------------------------------------------ //
// 本模組由 prolin 製作
// 製作日期：2014-07-20
// $Id:$
// ------------------------------------------------------------------------- //

/*-----------引入檔案區--------------*/

//樣版
$xoopsOption['template_main'] = 'es_timet_ad_set_teacher_tpl.html';
include_once "header.php";
include_once "../function.php";

/*-----------function區--------------*/
//取得參數

//加入新任教師，以逗號做分隔
if ($_POST['new_teacher']) {
    $myts = &MyTextSanitizer::getInstance();

    $kmo = preg_split('/[,]/', $xoopsDB->escape($_POST['new_teacher']));
    foreach ($kmo as $k => $v) {
        $kmo_o = trim($v);
        $sql = ' INSERT INTO   '.$xoopsDB->prefix('es_timetable_teacher').
                ' (`name` )  '.
                "  VALUES  ( '$kmo_o' )   ";
        $result = $xoopsDB->queryF($sql) or die($sql.'<br>'.$xoopsDB->error());
    }
}

//清除 不在的教師名冊
if ($_POST['del_hide_key']=='del_all'){
    echo 'del' ;
    $sql = ' delete   from    '.$xoopsDB->prefix('es_timetable_teacher').
                ' where hide = 1   ';
    $result = $xoopsDB->queryF($sql) or die($sql.'<br>'.$xoopsDB->error());

}

sync_teacher($DEF_SET['teacher_group']);
//姓名重
$data['error'] .= ckeck_teacher_name_double() ;


/*-----------執行動作判斷區----------*/
 $data['teacher'] = get_table_teacher_data();

/*-----------秀出結果區--------------*/

$xoopsTpl->assign('data', $data);
$xoopsTpl->assign('DEF_SET', $DEF_SET);

include_once 'footer.php';
