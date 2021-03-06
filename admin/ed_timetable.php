<?php
//  ------------------------------------------------------------------------ //
// 本模組由 prolin 製作
// 製作日期：2014-03-01
// $Id:$
// ------------------------------------------------------------------------- //

/*-----------引入檔案區--------------*/

//樣版
$xoopsOption['template_main'] = 'es_timet_ad_table_tpl.html';
include_once "header.php";
include_once "../function.php";

/*-----------function區--------------*/
//取得參數


/*-----------執行動作判斷區----------*/
//取得中文班名(有特殊班)
//$data['class_list_c'] = es_class_name_list_c()  ;
$data['class_list_c'] = get_timetable_class_list_c();

//------------------------------------------------------------------------------------------------------------------------------------------------------------------
//檢查目前的課表
$data['info'] = get_timetable_info( true);

if (($_GET['year'] > $data['info']['year']) or  (($_GET['year'] == $data['info']['year']) and ($_GET['semester'] >= $data['info']['semester']))) {
    $n_year = $_GET['year'];
    $n_semester = $_GET['semester'];
} else {
    $n_year = $data['info']['year'];
    $n_semester = $data['info']['semester'];
}

if (($n_year == 0) or  ($n_semester == 0)) {
    //取得現在學年
    $n_year = date('Y') - 1911;
    if (date('m') < 8) {
        $n_year -= 1;
    }
    if ((date('m') < 8) and (date('m') > 1)) {
        $n_semester = 2;
    } else {
        $n_semester = 1;
    }
}

$data['n_y'] = $n_year;
$data['n_s'] = $n_semester;

//目前班級數
//$data['class_list'] = get_class_list() ;

//取得目前科目名
$data['subject_name'] = get_subject_list();

  //教師名冊
$data['teacher_list'] = get_table_teacher_list($DEF_SET['teacher_group']);

//取得級任名冊
$data['class_teacher'] = get_class_teacher_list();

$data['select_class_id'] = $_GET['class_id'];

$data['error'] = check_timetable_double( $n_year , $n_semester );

/*-----------秀出結果區--------------*/

$xoopsTpl->assign('data', $data);
$xoopsTpl->assign('DEF_SET', $DEF_SET);

include_once 'footer.php';
