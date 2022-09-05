<?php
//  ------------------------------------------------------------------------ //
// 本模組由 prolin 製作
// 製作日期：2022-09-5
// $Id:$
// ------------------------------------------------------------------------- //
/*-----------引入檔案區--------------*/
include_once "header.php";
include_once "../function.php";

include_once '../../tadtools/PHPExcel.php';
require_once '../../tadtools/PHPExcel/IOFactory.php';
/*-----------function區--------------*/

//取得中文班名
//$class_list_c = es_class_name_list_c('long')  ;
$class_list_c = get_timetable_class_list_c('long');
/*-----------執行動作判斷區----------*/
//檢查目前的課表
$data['info'] = get_timetable_info();
$y = $data['info']['year'];
$s = $data['info']['semester'];


    //$class_list = get_class_list() ;
    $class_list = get_timetable_class_list_c('long');
    //人員
    $teacher_list = get_table_teacher_list('all');

    //教師有授權班級資料
    $sql = ' select  teacher  , class_id    FROM  '.$xoopsDB->prefix('es_timetable')." where school_year= '$y'  and  semester= '$s'    group by  teacher , class_id  order by teacher ,class_id   ";
    $result = $xoopsDB->query($sql) or die($sql.'<br>'.$xoopsDB->error());
    while ($row = $xoopsDB->fetchArray($result)) {
        $data[$row['teacher']][$row['class_id']] = 1 ;
    }



    $objPHPExcel = new PHPExcel();
    $objPHPExcel->setActiveSheetIndex(0);  //設定預設顯示的工作表
    $objActSheet = $objPHPExcel->getActiveSheet(); //指定預設工作表為 $objActSheet
    $objActSheet->setTitle('教師任課班級清冊');  //設定標題
    //設定框線
    $objBorder = $objActSheet->getDefaultStyle()->getBorders();
    $objBorder->getBottom()
              ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN)
              ->getColor()->setRGB('000000');
    $objActSheet->getDefaultRowDimension()->setRowHeight(15);

    $row = 0;
    //標題行
    $objPHPExcel->setActiveSheetIndex(0)
        ->setCellValue('A'.$row, '教師');
    $col = 'A';
    //列寬
    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth('10');

    foreach ($data as $tid => $class_list) {
        $col = 'A';
        $col_str = $col.$row;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col_str, $teacher_list[$tid]);

        foreach ($class_list as $cid => $class_id) {
            ++$col;
            $col_str = $col.$row;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col_str, $class_list_c[$cid]);
        }
        $row ++ ;
    }



  //header('Content-Type: application/vnd.ms-excel');
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename=teacher_class_list_'.date('mdHi').'.xlsx');
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    //$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    ob_clean();
    $objWriter->save('php://output');
    exit;
