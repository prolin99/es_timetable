<?php
//  ------------------------------------------------------------------------ //
// 本模組由 prolin 製作
// 製作日期：2014-07-01
// $Id:$
// ------------------------------------------------------------------------- //

/*-----------引入檔案區--------------*/
include_once 'header_admin.php';
//樣版
$xoopsOption['template_main'] = 'es_timet_ad_index_tpl.html';
include_once 'header.php';

//複製課表到新年學
//if ($_POST['do_key']) {


/*-----------function區--------------*/
sync_teacher($DEF_SET['teacher_group']);

//檢查目前的課表
$data['info'] = get_timetable_info();

//目前班級數
$data['school_class_num'] = get_class_num();

//清空
if ($_POST['act_clear'] == '刪除') {
    $sql = ' DELETE  FROM  '.$xoopsDB->prefix('es_timetable')." where school_year= '{$data['info']['year']}'  and  semester= '{$data['info']['semester']}' ";
    //echo $sql ;
    $result = $xoopsDB->query($sql) or die($sql.'<br>'.mysql_error());
    $data['info'] = get_timetable_info();
}

//複製
if (($_POST['year'] > $data['info']['year']) or  (($_POST['year'] == $data['info']['year']) and ($_POST['semester'] > $data['info']['semester']))) {
    $sql = ' select *  FROM  '.$xoopsDB->prefix('es_timetable')." where school_year= '{$data['info']['year']}'  and  semester= '{$data['info']['semester']}'     order by class_id,day,sector ";
    //echo $sql ;
    $result = $xoopsDB->query($sql) or die($sql.'<br>'.mysql_error());
    while ($row = $xoopsDB->fetchArray($result)) {
        $class_id = $row['class_id'];
        $sql2 = ' INSERT INTO   '.$xoopsDB->prefix('es_timetable').
                ' (`school_year`, `semester`, `class_id`, `teacher`,`day` , sector ,ss_id,room )  '.
                "  VALUES  ( '{$_POST['year']}' , '{$_POST['semester']}','{$row['class_id']}' ,'{$row['teacher']}' ,'{$row['day']}','{$row['sector']}' , '{$row['ss_id']}' , '{$row['room']}' )   ";
        $result2 = $xoopsDB->queryF($sql2) or die($sql.'<br>'.mysql_error());
    }

    //檢查目前的課表
    $data['info'] = get_timetable_info();
}

//讀取 tad_cal 行事曆
$data['holiday'] = get_tad_cal_holiday($DEF_SET['es_tt_Holiday_KW']);

$data['beg_date'] = date('Y-m').'-01';
$next_m = strtotime($data['beg_date'].'+ 1 months -1 day');
$data['end_date'] = date('Y-m-d', $next_m);
//check double

$data['error'] = check_timetable_double($data['info']['year'], $data['info']['semester']);

/*-----------秀出結果區--------------*/

$xoopsTpl->assign('data', $data);
$xoopsTpl->assign('DEF_SET', $DEF_SET);

include_once 'footer.php';
