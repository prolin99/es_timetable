<?php
//  ------------------------------------------------------------------------ //
// 本模組由 prolin 製作
// 製作日期：2014-07-01
// $Id:$
// ------------------------------------------------------------------------- //

/*-----------引入檔案區--------------*/
include_once "header.php";
include_once "../function.php";

if ($_GET['year'] and $_GET['semester']  and $_GET['do']  and $_GET['id']) {
    $class_list_c = get_timetable_class_list_c('short');
    //讀取科目
    $subject = get_subject_list();
    //讀取人名
    $teacher_list = get_table_teacher_data();
    for ($d = 1; $d <= $DEF_SET['days'];++$d) {
        for ($s = 1; $s <= $DEF_SET['sects'];++$s) {
            for ($w = 0; $w <= 2; ++$w) {
                $data[$d][$s][$w]['ss_id'] = 0;
                $data[$d][$s][$w]['week_d'] = 0;
                $data[$d][$s][$w]['other'] = 0;
            }
        }
    }

    if ($_GET['do'] == 'teacher') {
        $sql = ' select *  FROM  '.$xoopsDB->prefix('es_timetable').
        " where school_year= '{$_GET['year']}'  and  semester= '{$_GET['semester']}'    and  teacher= '{$_GET['id']}'   order by day,sector   ";
    } else {
        $sql = ' select *  FROM  '.$xoopsDB->prefix('es_timetable').
        " where school_year= '{$_GET['year']}'  and  semester= '{$_GET['semester']}'    and  class_id= '{$_GET['id']}'  order by day,sector,week_d   ";
    }

    $result = $xoopsDB->queryF($sql) or die($sql.'<br>'.$xoopsDB->error());
    while ($row = $xoopsDB->fetchArray($result)) {
        $row['subject_name'] = $subject[$row['ss_id']];
        $row['teacher_name'] = $teacher_list[$row['teacher']]['name'];
        $w = $row['week_d'];
        //如果同教師教兩班???
        if ($data[$row['day']][$row['sector']][$w]['class_id']) {
            $o_row = $data[$row['day']][$row['sector']][$w];
            $row['other'] = $class_list_c[$o_row['class_id']].$subject[$o_row['ss_id']].' '.$o_row['other'];
        }
        $data[$row['day']][$row['sector']][$w] = $row;
    }
    //echo $sql ;
    //var_dump( $data );
    echo json_encode($data, JSON_FORCE_OBJECT);
}
//echo $_GET['do'] .$_GET['year'] . $_GET['semester']  . $_GET['do']  . $_GET['class_id'] . $_GET['sect']  ;
;
