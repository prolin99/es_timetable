<?php
//  ------------------------------------------------------------------------ //
// 本模組由 prolin 製作
// 製作日期：2014-07-20
// 超鐘點 匯出成 excel
// $Id:$
// ------------------------------------------------------------------------- //
/*-----------引入檔案區--------------*/
include_once "header.php";
include_once "../function.php";

include_once '../../tadtools/PHPExcel.php';
require_once '../../tadtools/PHPExcel/IOFactory.php';
/*-----------function區--------------*/

//取得中文班名
$class_list_c = get_timetable_class_list_c();

/*-----------執行動作判斷區----------*/
//檢查目前的課表
$data['info'] = get_timetable_info();

//框線
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

//左方標題
function left_title($objPHPExcel, $row)
{
    $col = 'A' ;
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.$row, '日期');
    $objPHPExcel->getActiveSheet()->getColumnDimension($col)->setWidth(5);
    cell_border($objPHPExcel, $col.$row);
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.($row + 1), '星期');
    cell_border($objPHPExcel, $col.($row + 1));
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.($row + 2), '節次');
    cell_border($objPHPExcel, $col.($row + 2));
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.($row + 3), '班級');
    cell_border($objPHPExcel, $col.($row + 3));
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.($row + 4), "簽\n到");
    cell_border($objPHPExcel, $col.($row + 4));
    $objPHPExcel->getActiveSheet()->getRowDimension($row + 4)->setRowHeight('45');

}

    //科目
    //$subject= get_subject_list() ;
    //讀取人名 級任前、科任後
    $teacher_list = get_table_teacher_data_order_classid($data['info']['year'], $data['info']['semester']);
    //$teacher_list = get_table_teacher_data() ;

    //取得級任姓名
    //$class_teacher_list = get_class_teacher_list() ;

    $beg_date = strtotime($_GET['beg_date']);
    $end_date = strtotime($_GET['end_date']);
    $over_id = intval($_GET['over_id']);

    $week_m = intval($_GET['week_m']);
    $beg_week_num = (int) date('W',  $beg_date);
    // 找到雙週基準點
    if (($beg_week_num % 2) == 0) {
        if ($week_m == 1) {
            $beg_week_num -= 1;
        }
    } else {   //單
        if ($week_m == 1) {
            $beg_week_num = $beg_week_num - 1;
        }
    }

    //取得超鐘點的節次
    $timetable = get_timetable_data('teacher', $data['info']['year'], $data['info']['semester'], 'all', $over_id);

    //讀取 tad_cal 行事曆
    $holiday = get_tad_cal_holiday($DEF_SET['es_tt_Holiday_KW'], date('Y-m-d', $beg_date), date('Y-m-d', $end_date));

    //--------------------------------------------------------------------

    $objPHPExcel = new PHPExcel();

    $objPHPExcel->setActiveSheetIndex(0);  //設定預設顯示的工作表
    //橫向
    //$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);

    //紙張大小
    $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

    $objActSheet = $objPHPExcel->getActiveSheet(); //指定預設工作表為 $objActSheet
    $objActSheet->setTitle('簽名表');  //設定標題

    $objActSheet->getDefaultRowDimension()->setRowHeight(14);
    if ($DEF_SET['es_tt_week_D']) {
        $objActSheet->getDefaultColumnDimension()->setWidth(7);
    } else {
        $objActSheet->getDefaultColumnDimension()->setWidth(6);
    }
    $objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setSize(10);

    $row = 0;

//分月份重覆列出
for ($m = $beg_date; $m <= $end_date;  $m = strtotime(date('Y-m-01', $m).'+1 month')) {
    $show_end_date = strtotime(date('Y-m-01', $m).'+1 month');
    /*
    if ($row > 0) {
        $objPHPExcel->setActiveSheetIndex(0)->setBreak('A'.$row, PHPExcel_Worksheet::BREAK_ROW);
    }
    */



    //超鐘點課表  ---> 改以  $teacher_list  㾺順序  key  為教師

    //foreach ($timetable as $key => $table_data) {

    foreach ($teacher_list     as  $key=>$teacher_list_data) {
        if  ($timetable[$key])
            $table_data = $timetable[$key] ;
        else
            continue  ;


        $om = 0;
        $add_sects = 0;
        unset($wd_have_class);

        //有排課的那些天  整理放到  表中
        for ($d = 1; $d <= $DEF_SET['days']; ++$d) {
            for ($ss = 1; $ss <= $DEF_SET['sects']; ++$ss) {
                for ($w = 0; $w <= 2;++$w) {
                    if ($table_data[$d][$ss][$w]['ss_id']) {
                        $wd_have_class[] = $d;
                        continue;
                    }
                }
            }
        }
        //教師開始的列
        $tea_beg_row = $row  ;

        ++$row;
        $tea_beg_row = $row  ;

        //左方標題處
        $col = 'A';

        $objPHPExcel->getActiveSheet()->getStyle('A'.$row)->getFont()->setSize(14);
        $objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight('20');
        //$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . $row,$teacher_list[$key]['name'] .date('Y 年 n 月',$m) .'超鐘點(共       節)')  ;

        ++$row;
        left_title($objPHPExcel, $row) ;
        /*
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.$row, '日期');
        $objPHPExcel->getActiveSheet()->getColumnDimension($col)->setWidth(5);
        cell_border($objPHPExcel, $col.$row);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.($row + 1), '星期');
        cell_border($objPHPExcel, $col.($row + 1));
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.($row + 2), '節次');
        cell_border($objPHPExcel, $col.($row + 2));
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.($row + 3), '班級');
        cell_border($objPHPExcel, $col.($row + 3));
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.($row + 4), "簽\n到");
        cell_border($objPHPExcel, $col.($row + 4));
        $objPHPExcel->getActiveSheet()->getRowDimension($row + 4)->setRowHeight('45');
        */

        //此月各週
        for ($do_day = $beg_date; $do_day <= $end_date; $do_day = $do_day + 60 * 60 * 24) {
            if ($holiday[date('Y-m-d', $do_day)]) {
                continue;
            }    //當天放假，略過

            if (($do_day >= $m) and  ($do_day < $show_end_date)) {

                //課表，只呈現有課的當天

                $d = date('N', $do_day);
                if (($d <= $DEF_SET['days'])  and in_array($d, $wd_have_class)) {
                    $sign_week = ((int) date('W',  $do_day) - $beg_week_num) % 2;    //是否為單週

                    for ($ss = 1; $ss <= $DEF_SET['sects']; ++$ss) {
                        for ($w = 0;$w <= 2;++$w) {
                            if ($table_data[$d][$ss][$w]['class_id']) {
                                $week_mark = '';
                                //太多節，不要太長 取10 節
                                if ($col >= 'K'){
                                    $col = 'A' ;
                                    $row += 6 ;
                                    //左方標題
                                    left_title($objPHPExcel, $row) ;
                                }

                                if ($w == 1) {
                                    $week_mark = '(單)';
                                }
                                if ($w == 2) {
                                    $week_mark = '(雙)';
                                }
                                if ($w == 0) {
                                    ++$col;
                                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.$row, date('m-d', $do_day));
                                    cell_border($objPHPExcel, $col.$row);
                                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.($row + 1),  $DEF_SET['week'][$d]);
                                    cell_border($objPHPExcel, $col.($row + 1));
                                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.($row + 2), $DEF_SET['sects_cht_list'][$ss]);
                                    cell_border($objPHPExcel, $col.($row + 2));
                                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.($row + 3), $class_list_c[$table_data[$d][$ss][$w]['class_id']].$week_mark);
                                    cell_border($objPHPExcel, $col.($row + 3));
                                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.($row + 4), "\n\n");
                                    cell_border($objPHPExcel, $col.($row + 4));
                                    ++$add_sects;
                                }
                                if (($sign_week)  and ($w == 1)) {
                                    ++$col;
                                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.$row, date('m-d', $do_day));
                                    cell_border($objPHPExcel, $col.$row);
                                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.($row + 1),  $DEF_SET['week'][$d]);
                                    cell_border($objPHPExcel, $col.($row + 1));
                                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.($row + 2), $DEF_SET['sects_cht_list'][$ss]);
                                    cell_border($objPHPExcel, $col.($row + 2));
                                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.($row + 3), $class_list_c[$table_data[$d][$ss][$w]['class_id']].$week_mark);
                                    cell_border($objPHPExcel, $col.($row + 3));
                                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.($row + 4), "\n\n");
                                    cell_border($objPHPExcel, $col.($row + 4));
                                    ++$add_sects;
                                }
                                if ((!$sign_week)  and ($w == 2)) {
                                    ++$col;
                                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.$row, date('m-d', $do_day));
                                    cell_border($objPHPExcel, $col.$row);
                                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.($row + 1),  $DEF_SET['week'][$d]);
                                    cell_border($objPHPExcel, $col.($row + 1));
                                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.($row + 2), $DEF_SET['sects_cht_list'][$ss]);
                                    cell_border($objPHPExcel, $col.($row + 2));
                                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.($row + 3), $class_list_c[$table_data[$d][$ss][$w]['class_id']].$week_mark);
                                    cell_border($objPHPExcel, $col.($row + 3));
                                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col.($row + 4), "\n\n");
                                    cell_border($objPHPExcel, $col.($row + 4));
                                    ++$add_sects;
                                }
                            }
                        }
                    }
                }
            }
        }
        //人名的簽名標題
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$tea_beg_row , "*" .$teacher_list[$key]['name'].'-----' . date('Y 年 n 月', $m).$DEF_SET['es_tt_over_list'][$over_id]." (共 $add_sects 節)");
        $row = $row + 4;
    }
}

    //header('Content-Type: application/vnd.ms-excel');
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename=2688'.date('mdHi').'.xlsx');
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    //$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    ob_clean();
    $objWriter->save('php://output');
    exit;
