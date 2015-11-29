<?php
//  ------------------------------------------------------------------------ //
// 本模組由 prolin 製作
// 製作日期：2014-07-20
// $Id:$
// ------------------------------------------------------------------------- //
/*-----------引入檔案區--------------*/
include_once 'header_admin.php';

include_once 'header.php';

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

    //科目
    $subject = get_subject_list();
    //班級
    //$class_list = get_class_list() ;
    $class_list = get_timetable_class_list_c('long');
    //人員
    $teacher_list = get_table_teacher_list('all');

    //單雙週為半節
    $sql = ' select class_id, ss_id, teacher ,count(*) as cc  FROM  '.$xoopsDB->prefix('es_timetable')." where school_year= '$y'  and  semester= '$s'  and  week_d>0  group by  class_id, ss_id ,teacher order by class_id, ss_id ,teacher  ";
    $result = $xoopsDB->query($sql) or die($sql.'<br>'.mysql_error());
    while ($row = $xoopsDB->fetchArray($result)) {
        $data_part[$row['class']][$row['ss_id']][$row['teacher']] = $row['cc'] / 2;
    }

    //整節
    $sql = ' select class_id, ss_id, teacher ,count(*) as cc  FROM  '.$xoopsDB->prefix('es_timetable')." where school_year= '$y'  and  semester= '$s'  and week_d = 0  group by  class_id, ss_id ,teacher order by class_id, ss_id ,teacher  ";
    $result = $xoopsDB->query($sql) or die($sql.'<br>'.mysql_error());
    while ($row = $xoopsDB->fetchArray($result)) {
        $sect['name'] = $row['teacher'];
        $sect['cc'] = $row['cc'] + $data_part[$row['class']][$row['ss_id']][$row['teacher']];
        $data[$row['class_id']][$row['ss_id']][] = $sect;

        //在每個班級中同一科有多人上課，會用到的最大格
        if ($max[$row['ss_id']] < count($data[$row['class_id']][$row['ss_id']])) {
            $max[$row['ss_id']] = count($data[$row['class_id']][$row['ss_id']]);
        }
    }

    $objPHPExcel = new PHPExcel();
    $objPHPExcel->setActiveSheetIndex(0);  //設定預設顯示的工作表
    $objActSheet = $objPHPExcel->getActiveSheet(); //指定預設工作表為 $objActSheet
    $objActSheet->setTitle('教師總表');  //設定標題
    //設定框線
    $objBorder = $objActSheet->getDefaultStyle()->getBorders();
    $objBorder->getBottom()
              ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN)
              ->getColor()->setRGB('000000');
    $objActSheet->getDefaultRowDimension()->setRowHeight(15);

    $row = 1;
       //標題行
          $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.$row, '班級');
       $col = 'A';
       //列寬
       $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth('10');

       foreach ($max as $id => $count) {
           for ($i = 0; $i < $count; ++$i) {
               ++$col;
               $col_str = $col.$row;
                //$objPHPExcel->getActiveSheet()->getColumnDimension($col)->setWidth('6');
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col_str, $subject[$id]);
               ++$col;
               $col_str = $col.$row;
                //$objPHPExcel->getActiveSheet()->getColumnDimension($col)->setWidth('6');
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col_str, '節數');
           }
       }

     //資料區
     foreach ($class_list  as $cid => $class_id) {
         ++$row;
            //行高
            $objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(20);

         $col = 'A';
         $col_str = $col.$row;
         $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col_str, $class_list_c[$cid]);
         foreach ($max as $id => $count) {
             for ($i = 0; $i < $count; ++$i) {
                 ++$col;
                 $col_str = $col.$row;
                        //$objPHPExcel->getActiveSheet()->getColumnDimension($col)->setWidth('6');
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col_str, $teacher_list[$data[$cid][$id][$i]['name']]);
                 ++$col;
                 $col_str = $col.$row;
                 $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col_str, $data[$cid][$id][$i]['cc']);
             }
         }
     }

  //header('Content-Type: application/vnd.ms-excel');
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename=teacher_all'.date('mdHi').'.xlsx');
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    //$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    ob_clean();
    $objWriter->save('php://output');
    exit;
