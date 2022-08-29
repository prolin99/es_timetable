<?php
//  ------------------------------------------------------------------------ //
// 本模組由 prolin 製作
// 製作日期：2014-07-01
// $Id:$
// ------------------------------------------------------------------------- //

/*-----------引入檔案區--------------*/

$xoopsOption['template_main'] = 'es_timet_ad_index_tpl.html';
include_once "header.php";
include_once "../function.php";

//設定

if ($_POST['do_key']) {

    $sql=  " update   " . $xoopsDB->prefix("config") ." set conf_value='{$_POST['class_teacher_input']}' where conf_name='es_tt_class_input' ; "  ;
	$result = $xoopsDB->queryF($sql) or redirect_header($_SERVER['PHP_SELF'],3, $xoopsDB->error());

    $sql =  " update   " . $xoopsDB->prefix("config") ." set conf_value='{$_POST['OpenYear']}' where conf_name='es_tt_sm__OpenYear' ; "  ;
	$result = $xoopsDB->queryF($sql) or redirect_header($_SERVER['PHP_SELF'],3, $xoopsDB->error());

    $sql =  " update   " . $xoopsDB->prefix("config") ." set conf_value='{$_POST['OpenSemester']}' where conf_name='es_tt_sm__OpenSemester' ; "  ;
	$result = $xoopsDB->queryF($sql) or redirect_header($_SERVER['PHP_SELF'],3, $xoopsDB->error());
    redirect_header($_SERVER['PHP_SELF']) ;
}
/*-----------function區--------------*/


//sync_teacher($DEF_SET['teacher_group']);

//檢查目前的課表
$data['info'] = get_timetable_info(true);

//目前班級數
$data['school_class_num'] = get_class_num();

//刪除舊學期
if ($_POST['act_clear_old'] == '刪除舊學期') {
    $sql = ' DELETE  FROM  '.$xoopsDB->prefix('es_timetable')." where school_year < '{$data['info']['year']}'   ";
    //echo $sql ;
    $result = $xoopsDB->query($sql) or die($sql.'<br>'.$xoopsDB->error());
    $data['info'] = get_timetable_info(true);
}

//清空本學期
if ($_POST['act_clear'] == '刪除') {
    $sql = ' DELETE  FROM  '.$xoopsDB->prefix('es_timetable')." where school_year= '{$data['info']['year']}'  and  semester= '{$data['info']['semester']}' ";
    //echo $sql ;
    $result = $xoopsDB->query($sql) or die($sql.'<br>'.$xoopsDB->error());
    $data['info'] = get_timetable_info(true);
}

//複製
if (($_POST['year'] > $data['info']['year']) or  (($_POST['year'] == $data['info']['year']) and ($_POST['semester'] > $data['info']['semester']))) {
    $sql = ' select *  FROM  '.$xoopsDB->prefix('es_timetable')." where school_year= '{$data['info']['year']}'  and  semester= '{$data['info']['semester']}'     order by class_id,day,sector ";
    //echo $sql ;
    $result = $xoopsDB->query($sql) or die($sql.'<br>'.$xoopsDB->error());
    while ($row = $xoopsDB->fetchArray($result)) {
        $class_id = $row['class_id'];
        $sql2 = ' INSERT INTO   '.$xoopsDB->prefix('es_timetable').
                ' (`school_year`, `semester`, `class_id`, `teacher`,`day` , sector ,ss_id,room )  '.
                "  VALUES  ( '{$_POST['year']}' , '{$_POST['semester']}','{$row['class_id']}' ,'{$row['teacher']}' ,'{$row['day']}','{$row['sector']}' , '{$row['ss_id']}' , '{$row['room']}' )   ";
        $result2 = $xoopsDB->queryF($sql2) or die($sql.'<br>'.$xoopsDB->error());
    }

    //檢查目前的課表
    $data['info'] = get_timetable_info(true);
}

//讀取 tad_cal 行事曆
$data['holiday'] = get_tad_cal_holiday($DEF_SET['es_tt_Holiday_KW']);

$data['beg_date'] = date('Y-m').'-01';
$next_m = strtotime($data['beg_date'].'+ 1 months -1 day');
$data['end_date'] = date('Y-m-d', $next_m);
//check double

//  嚴格模式做檢查
if ($data['info']['year'] and  $data['info']['semester'])
 $data['error'] = check_timetable_double($data['info']['year'], $data['info']['semester']);

/*-----------秀出結果區--------------*/

$xoopsTpl->assign('data', $data);
$xoopsTpl->assign('DEF_SET', $DEF_SET);

include_once 'footer.php';
