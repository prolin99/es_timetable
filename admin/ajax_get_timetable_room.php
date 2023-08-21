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

    for ($d = 1; $d <= $DEF_SET['days'];++$d) {
        for ($s = 1; $s <= $DEF_SET['sects'];++$s) {
            $data[$d][$s]['ss_id'] = 0;
        }
    }

    if ($_GET['do'] == 'room') {
        $room_list = get_class_room_list($_GET['year'], $_GET['semester']);
        $room = $room_list[$_GET['id']];
        $sql = ' select *  FROM  '.$xoopsDB->prefix('es_timetable').
        " where school_year= '{$_GET['year']}'  and  semester= '{$_GET['semester']}'    and  room='$room'   order by day,sector   ";
    }else{
        $room_list = get_class_room_list($_GET['year'], $_GET['semester']);
        $in = array_search($_GET['id'], $room_list);

        if ($in){
            $sql = ' select *  FROM  '.$xoopsDB->prefix('es_timetable').
            " where school_year= '{$_GET['year']}'  and  semester= '{$_GET['semester']}'    and  room='{$_GET['id']}'   order by day,sector   ";
        }
    }

    $result = $xoopsDB->queryF($sql) or die($sql.'<br>'.$xoopsDB->error());
    while ($row = $xoopsDB->fetchArray($result)) {
        $data[$row['day']][$row['sector']]['room'] = '1' ;
    }
    //echo $sql ;
    //清空輸出的緩沖區
    ob_clean() ;
    echo json_encode($data, JSON_FORCE_OBJECT);
}
