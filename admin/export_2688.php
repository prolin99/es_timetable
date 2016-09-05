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
$class_list_c = get_timetable_class_list_c();

/*-----------執行動作判斷區----------*/
//檢查目前的課表
$data['info'] = get_timetable_info();

if ($_POST['do_2688']) {

    //取得
    $timetable = get_timetable_data('teacher', $data['info']['year'], $data['info']['semester']);

    //var_dump( $timetable ) ;
    //科目
    $subject = get_subject_list();
    //讀取人名
    $teacher_list = get_table_teacher_data();
    //var_dump($teacher_list ) ;

    //取得級任姓名
    $class_teacher_list = get_class_teacher_list();

 //

    $date = date('Y-m', strtotime($_POST['beg_date'])).'-01';

    $w = date('N', strtotime($date)) - 1;
    $this_m = date('m', strtotime($date));
    $this_y = date('Y', strtotime($date)) - 1911;
    $beg_week_date = strtotime("- $w day", strtotime($date));

    $next_m = strtotime($date.'+ 1 months');

    //$this_month_last  = date( 'd' , strtotime(date('Ymd' ,$next_m)."-1 day") ) ;
    $this_month_last = date('d',  $next_m - 60 * 60 * 24);

    function cell_border($objPHPExcel, $cell, $thick_left = false)
    {
        //設定框線
        $objPHPExcel->getActiveSheet()->getStyle($cell)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $objPHPExcel->getActiveSheet()->getStyle($cell)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        if ($thick_left) {
            $objPHPExcel->getActiveSheet()->getStyle($cell)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
        } else {
            $objPHPExcel->getActiveSheet()->getStyle($cell)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        }
        $objPHPExcel->getActiveSheet()->getStyle($cell)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    }

//--------------------------------------------------------------------

    $objPHPExcel = new PHPExcel();

    $objPHPExcel->setActiveSheetIndex(0);  //設定預設顯示的工作表
    //橫向
    $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);

    //大小
    $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

    $objActSheet = $objPHPExcel->getActiveSheet(); //指定預設工作表為 $objActSheet
    $objActSheet->setTitle('2688簽名');  //設定標題

 /*
    //設定框線
    $objBorder=$objActSheet->getDefaultStyle()->getBorders();
    $objBorder->getBottom()
              ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN)
              ->getColor()->setRGB('000000');


    //$objBorder=$objActSheet->getDefaultStyle()->getBorders();
    $objBorder->getLeft()
              ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN)
              ->getColor()->setRGB('000000');
*/

    $objActSheet->getDefaultRowDimension()->setRowHeight(30);
    $objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setSize(10);

    $row = 1;
    foreach ($timetable as $key => $table_data) {
        if ($teacher_list[$key]['kind'] >= 2) {
            unset($wd_have_class);
            $sect_list = 0;

            //標題行
            $objPHPExcel->getActiveSheet()->getStyle('A'.$row)->getFont()->setSize(14);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$row, $data['info']['year'].'學年度第'.$data['info']['semester'].'學期教育部增置教師授課 '.$teacher_list[$key]['name']."$this_y 年 $this_m 月份簽到簿");
            //$objPHPExcel->getActiveSheet()->mergeCells('A'.$row.':K'.$row);
            $col = 'A';

            //列寬
            //$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth('10');

            ++$row;
            $do_day = strtotime($date);

            //有排課的那些天
            for ($d = 1; $d <= $DEF_SET['days']; ++$d) {
               for ($ss = 1; $ss <= $DEF_SET['sects']; ++$ss) {
                   if ($table_data[$d][$ss]['ss_id']) {
                       $wd_have_class[] = $d;
                       continue;
                   }
               }
            }
            //var_dump($wd_have_class) ;

            //節次
            $col_str = 'A'.($row);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col_str, '周別');
            $objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight('15');
            //框線
            cell_border($objPHPExcel, $col_str);

            $col_str = 'A'.($row + 1);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col_str, '日期');
            $objPHPExcel->getActiveSheet()->getRowDimension($row + 1)->setRowHeight('15');
            //框線
            cell_border($objPHPExcel, $col_str);

            $col_str = 'A'.($row + 2);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col_str, '星期');
            $objPHPExcel->getActiveSheet()->getRowDimension($row + 2)->setRowHeight('15');
            //框線
            cell_border($objPHPExcel, $col_str);

            for ($i = 1; $i <= $DEF_SET['sects']; ++$i) {
                $col_str = 'A'.($row + $i + 2);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col_str, $DEF_SET['sects_cht_list'][$i]);
            //框線
            cell_border($objPHPExcel, $col_str);
            }

            $col_str = 'A'.($row + $DEF_SET['sects'] + 3);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col_str, "簽 \n\n到");
            $objPHPExcel->getActiveSheet()->getRowDimension($row + $DEF_SET['sects'] + 3)->setRowHeight('60');
            //框線
            cell_border($objPHPExcel, $col_str);

            //加入換頁
            $objPHPExcel->setActiveSheetIndex(0)->setBreak('A'.($row + $DEF_SET['sects'] + 4), PHPExcel_Worksheet::BREAK_ROW);

            //課表，只呈現有課的當天
            for ($i = 1; $i <= $this_month_last; ++$i) {
                $s = date('N', $do_day);
                if (($s <= $DEF_SET['days'])  and in_array($s, $wd_have_class)) {
                    ++$col;

                    //周別
                    $col_str = $col.$row;
                    //框線
                    $objPHPExcel->getActiveSheet()->getStyle($col_str)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
                    $week = date('W', $do_day);
                    if ($old_week != $week) {
                        //left
                        $objPHPExcel->getActiveSheet()->getStyle($col_str)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
                        $old_week = $week;
                        $thick_left = true;
                    } else {
                        $thick_left = false;
                    }

                    //日期
                    $col_str = $col.($row + 1);
                    $objPHPExcel->getActiveSheet()->getColumnDimension($col)->setWidth('7');
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col_str, date('m-d', $do_day));
                    //框線
                    cell_border($objPHPExcel, $col_str, $thick_left);

                    $row2 = $row + 2;
                    //星期...
                    $col_str = $col.$row2;
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col_str, $DEF_SET['week'][$s]);
                    //框線
                    cell_border($objPHPExcel, $col_str, $thick_left);

                    for ($ss = 1; $ss <= $DEF_SET['sects']; ++$ss) {
                        $row2 = $row + $ss + 2;
                        $col_str = $col.$row2;
                        $cell_doc = $class_list_c[$table_data[$s][$ss]['class_id']]."\n".$subject[$table_data[$s][$ss]['ss_id']];
                        if ($table_data[$s][$ss]['class_id']) {
                            ++$sect_list;
                        }        //有課數
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col_str, $cell_doc);
                        //框線
                        cell_border($objPHPExcel, $col_str, $thick_left);
                    }

                    //簽到格框線
                    $col_str = $col.($row + $DEF_SET['sects'] + 3);
                    //框線
                    cell_border($objPHPExcel, $col_str, $thick_left);
                }

                $do_day = $do_day + 60 * 60 * 24;
            }
            //周次右線
            $col_str = $col.$row;
            $objPHPExcel->getActiveSheet()->getStyle($col_str)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

            $col_str = 'B'.($row + $DEF_SET['sects'] + 4);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col_str, "共  $sect_list   節");
            $objPHPExcel->getActiveSheet()->getRowDimension($row + $DEF_SET['sects'] + 4)->setRowHeight('20');
            $objPHPExcel->getActiveSheet()->getStyle($col_str)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $objPHPExcel->getActiveSheet()->getStyle('C'.($row + $DEF_SET['sects'] + 4))->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

            $row = $row + $DEF_SET['sects'] + 5;
        }
    }

    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename=2688'.date('mdHi').'.xlsx');
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    //$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    ob_clean();
    $objWriter->save('php://output');
    exit;
}
