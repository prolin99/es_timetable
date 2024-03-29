<?php
//  ------------------------------------------------------------------------ //
// 本模組由 prolin 製作
// 製作日期：2014-03-01
// $Id:$
// ------------------------------------------------------------------------- //

/*-----------引入檔案區--------------*/

//樣版
$xoopsOption['template_main'] = 'es_timet_setroom_tpl.html';
include_once "header.php";
include_once "../function.php";

/*-----------function區--------------*/
//取得參數


/*-----------執行動作判斷區----------*/
//取得中文班名
//$data['class_list_c'] = es_class_name_list_c('long')  ;
$data['class_list_c'] = get_timetable_class_list_c('long');
//
//

//------------------------------------------------------------------------------------------------------------------------------------------------------------------
//檢查目前的課表
$data['info'] = get_timetable_info(true);

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
/*
//目前班級數
$data['class_list'] = get_class_list() ;

//取得目前科目名
$data['subject_name'] = get_subject_list() ;
*/
  //教師名冊
$data['teacher_list'] = get_table_teacher_list();
 //var_dump($data['teacher_list'] ) ;

 if (intval($_POST['teacher_id'])) {
     $data['teacher_sel'] = intval($_POST['teacher_id']);
 } elseif (intval($_GET['teacher_id'])) {
    $data['teacher_sel'] = intval($_GET['teacher_id']);
} else {
    $data['teacher_sel'] = key($data['teacher_list']);
}

if (intval($_POST['over_id'])) {
    $data['over_id'] = intval($_POST['over_id']);
} else {
    $data['over_id'] = 1;
}

//echo  $data['teacher_sel']   ;
$tab = get_ones_timetable('teacher', $n_year, $n_semester, $data['teacher_sel']);
//var_dump($tab) ;

$data['error'] = check_timetable_double($data['info']['year'], $data['info']['semester']);

//各教室已設定的情形
$room_list = get_class_room_list($n_year, $n_semester);

//$data_room = get_room_array($n_year, $n_semester);
//var_dump($data_room); 
/*-----------秀出結果區--------------*/

$xoopsTpl->assign('data', $data);
$xoopsTpl->assign('data_room', $data_room);
$xoopsTpl->assign('room_list', $room_list);
$xoopsTpl->assign('DEF_SET', $DEF_SET);
$xoopsTpl->assign('tab', $tab);

include_once 'footer.php';
