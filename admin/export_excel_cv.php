<?php
//  ------------------------------------------------------------------------ //
// 本模組由 prolin 製作
// 製作日期：2015-09-13
// $Id:$
// ------------------------------------------------------------------------- //
/*-----------引入檔案區--------------*/
include_once "header.php";
include_once "../function.php";

require_once XOOPS_ROOT_PATH . '/modules/tadtools/vendor/phpoffice/phpexcel/Classes/PHPExcel.php'; //引入 PHPExcel 物件庫
require_once XOOPS_ROOT_PATH . '/modules/tadtools/vendor/phpoffice/phpexcel/Classes/PHPExcel/IOFactory.php'; //引入PHPExcel_IOFactory 物件庫

/*-----------function區--------------*/

//取得中文班名
//$class_list_c = es_class_name_list_c()  ;
$class_list_c = get_timetable_class_list_c();

/*-----------執行動作判斷區----------*/
//檢查目前的課表
$data['info'] = get_timetable_info();

if ($_GET['mode']) {
    $mid = $_GET['mode'];

    //取得教師課表
    $timetable = get_timetable_data($mid, $data['info']['year'], $data['info']['semester']);

    //科目
    $subject = get_subject_list();
    //讀取人名
    $teacher_list = get_table_teacher_data();

    //取得級任姓名
    $class_teacher_list = get_class_teacher_list();
    //$teacher_list = get_table_teacher_list('all') ;
    $teacher_list = get_table_teacher_list();

    $fontcolor = new PHPExcel_Style_Color();
    $fontcolor->setRGB('000000');
    $redfontcolor = new PHPExcel_Style_Color();
    $redfontcolor->setRGB('d92184');

    $objPHPExcel = new PHPExcel();
    $objPHPExcel->setActiveSheetIndex(0);  //設定預設顯示的工作表
    $objActSheet = $objPHPExcel->getActiveSheet(); //指定預設工作表為 $objActSheet
    $objActSheet->setTitle('班級總表');  //設定標題
    //設定框線
    $objBorder = $objActSheet->getDefaultStyle()->getBorders();
    $objBorder->getBottom()
              ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN)
              ->getColor()->setRGB('000000');
    if ($DEF_SET['es_tt_week_D']) {
        $objActSheet->getDefaultRowDimension()->setRowHeight(67);
    } else {
        $objActSheet->getDefaultRowDimension()->setRowHeight(34);
    }

    $row = 1;
    $objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(15);
       //標題行
          $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.$row, '班級');
    $col = 'A';
       //列寬
       $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth('10');

    //最右方節次
    for ($i = 1; $i <= $DEF_SET['days']; ++$i) {
        for ($s = 1; $s <= $DEF_SET['sects']; ++$s) {
            ++$row;
            $col_str = $col.$row;
            $objPHPExcel->getActiveSheet()->getColumnDimension($col)->setWidth('6');
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col_str, "$i-".mb_substr($DEF_SET['sects_cht_list'][$s], 1, 1, 'utf-8'));
        }
    }

     //資料區
    //foreach ($teacher_list as $teacher_id=>$tname ) {
    //	$mytable = $timetable[$teacher_id]  ;
    foreach ($timetable  as $t_id => $mytable) {
        $row = 1;
        ++$col;
            //行高
            //$objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(34);


            //教師姓名
            $col_str = $col.$row;
            //$objPHPExcel->setActiveSheetIndex(0)->setCellValue($col_str , $teacher_list[$teacher_id] ) ;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col_str, $class_list_c[$t_id]);
        for ($i = 1; $i <= $DEF_SET['days']; ++$i) {
            for ($s = 1; $s <= $DEF_SET['sects']; ++$s) {
                //$col++ ;
                    ++$row;
                $tstr = '';
                for ($w = 0;$w <= 2; ++$w) {
                    if ($mytable[$i][$s][$w]['ss_id']) {
                        $short_ss = mb_substr($subject[$mytable[$i][$s][$w]['ss_id']], 0, 2, 'utf-8');
                        $is_class_teacher = ($class_teacher_list[$t_id] == $teacher_list[$mytable[$i][$s][$w]['teacher']]);
                        if ($tstr != '') {
                            $tstr .= ",\n";
                        }
                        if ($mytable[$i][$s][$w]['other']) {
                            //$tstr .= $class_list_c[$mytable[$i][$s][$w]['class_id']] ."&\n" .$short_ss;
                                $tstr .= $teacher_list[$mytable[$i][$s][$w]['teacher']]."&\n".$short_ss;
                        } else {
                            $tstr .= $teacher_list[$mytable[$i][$s][$w]['teacher']]."\n".$short_ss;
                        }
                                //$tstr .= $class_list_c[$mytable[$i][$s][$w]['class_id']] ."\n" .$short_ss;
                            if ($w == 1) {
                                $tstr .= '*';
                            }
                        if ($w == 2) {
                            $tstr .= '#';
                        }
                    }
                }
                $col_str = $col.$row;
                $objPHPExcel->getActiveSheet()->getStyle($col_str)->getAlignment()->setWrapText(true); //自動換行
                    //級任
                    if ($is_class_teacher) {
                        $objPHPExcel->setActiveSheetIndex(0)->getStyle($col_str)->getFont()->setColor($fontcolor);
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col_str, $tstr);
                    } else {
                        $objPHPExcel->setActiveSheetIndex(0)->getStyle($col_str)->getFont()->setColor($redfontcolor);
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col_str, $tstr);
                    }
            }
        }
    }

    //header('Content-Type: application/vnd.ms-excel');
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename=class_v_all'.date('mdHi').'.xlsx');
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    //$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    ob_clean();
    $objWriter->save('php://output');
    exit;
}
