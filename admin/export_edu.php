<?php
//  ------------------------------------------------------------------------ //
// 本模組由 prolin 製作
// 製作日期：2014-07-20
// $Id:$
// ------------------------------------------------------------------------- //
/*-----------引入檔案區--------------*/
include_once "header.php";
include_once "../function.php";

require_once XOOPS_ROOT_PATH . '/modules/tadtools/vendor/phpoffice/phpexcel/Classes/PHPExcel.php'; //引入 PHPExcel 物件庫
require_once XOOPS_ROOT_PATH . '/modules/tadtools/vendor/phpoffice/phpexcel/Classes/PHPExcel/IOFactory.php'; //引入PHPExcel_IOFactory 物件庫

/*-----------function區--------------*/

//取得中文班名
//$class_list_c = es_class_name_list_c('long')  ;
$class_list_c = get_timetable_class_list_c('long');

/*-----------執行動作判斷區----------*/
//檢查目前的課表
$data['info'] = get_timetable_info();
$y = $data['info']['year'];
$s = $data['info']['semester'];

    //var_dump($max) ;


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
             ->setCellValue('A'.$row, '週次')
            ->setCellValue('B'.$row, '節次')
            ->setCellValue('C'.$row, '年級')
            ->setCellValue('D'.$row, '班級')
            ->setCellValue('E'.$row, '教師姓名')
            ->setCellValue('F'.$row, '身分證')
            ->setCellValue('G'.$row, '類別')
            ->setCellValue('H'.$row, '領域')
            ->setCellValue('I'.$row, '科目')
            ->setCellValue('J'.$row, '語言別')
            ->setCellValue('K'.$row, '校訂課程名稱(選填)')
            ->setCellValue('L'.$row, '上課頻率');

        //新分頁  sheet 做教師身份證號
        $objActExcelPID = $objPHPExcel->createSheet(1);
        $objActExcelPID->setTitle('personid');
        $objActExcelPID->setCellValue('A1', '教師姓名');
        $objActExcelPID->setCellValue('B1', '教師身份證號');
        $objActExcelPID->setCellValue('E1', '說明：');
        $objActExcelPID->setCellValue('E2', '以此格式輸入或直接貼上(順序不需一致)');
        $objActExcelPID->setCellValue('E3', '教師總表頁中的教師身分證號可以自動調整');
        $objActExcelPID->setCellValue('E4', '總表頁中，本土語言語言別記得再檢查');
        $b_row = 1;

    //資料開始
    //科目
    $subject = get_subject_data_list();
    //班級
    //$class_list = get_class_list() ;
    //人員
    $teacher_list = get_table_teacher_list('all');

    $ch_num = array('','一','二','三','四','五','六','七','八','九');

    $sql = ' select  *   FROM  '.$xoopsDB->prefix('es_timetable')." where school_year= '$y'  and  semester= '$s'   order by  teacher , day ,  sector  ";

    $result = $xoopsDB->query($sql) or die($sql.'<br>'.$xoopsDB->error());
    //$mi=0 ;
    while ($row_data = $xoopsDB->fetchArray($result)) {
        //$day ='週' . $ch_num[$row_data['day'] ];

        $day = $DEF_SET['week'][$row_data['day']];
        $sector = $DEF_SET['sects_cht_list'][$row_data['sector']];

        if (substr($row_data['class_id'], 0, -2) == 99) {
            //特殊班
            $class_year = '??';
            $class_id = $DEF_SET['spe_class_list'][$row_data['class_id']];
        } else {
            $class_year = $ch_num[substr($row_data['class_id'], 0, -2)].'年級';
            $class_id = '第'.substr($row_data['class_id'], -2).'班';
        }
        $teacher = $teacher_list[$row_data['teacher']];

        $sub_name = trim($subject[$row_data['ss_id']]['e_subject']);
        $sub_s_name = trim($subject[$row_data['ss_id']]['s_subject']);
        $sub_scope = trim($subject[$row_data['ss_id']]['scope']);

        $sub_local = '';
        if ($sub_name == '本土語言') {
            $sub_local = $DEF_SET['es_tt_local'];
        }

        if ($sub_scope == '彈性課程') {
            $sub_kind = '彈性學習';
        } else {
            $sub_kind = '領域學習';
        }

        $week_d = '每週上課';
        if ($row_data['week_d'] == 1) {
            $week_d = '單週上課';
        }
        if ($row_data['week_d'] == 2) {
            $week_d = '雙週上課';
        }

        ++$row;
            //行高
            $objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(20);

        $col = 'A';
        $col_str = $col.$row;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col_str, $day);

        ++$col;
        $col_str = $col.$row;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col_str, $sector);

        ++$col;
        $col_str = $col.$row;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col_str, $class_year);

        ++$col;
        $col_str = $col.$row;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col_str, $class_id);

        ++$col;
        $col_str = $col.$row;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col_str, $teacher);

        ++$col;
        $col_str = $col.$row;
            //要搜尋配合的身份證號
            $pid_search = " VLOOKUP( \"$teacher\" , personid!A2:B200,2, ) ";

            //$objPHPExcel->setActiveSheetIndex(0)->setCellValue($col_str , $teacher .'id') ;
                 $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col_str, '='.$pid_search);

        ++$col;
        $col_str = $col.$row;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col_str, $sub_kind);

            //教育部領域
            ++$col;
        $col_str = $col.$row;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col_str, $sub_scope);

            //教育部科目
            ++$col;
        $col_str = $col.$row;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col_str, $sub_name);

        ++$col;
        $col_str = $col.$row;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col_str, $sub_local);

            //校訂科目
            ++$col;
        $col_str = $col.$row;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col_str, $sub_s_name);

        ++$col;
        $col_str = $col.$row;
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col_str, $week_d);

            //sheet 只列一次教師名
            if ($teacher != $old_teacher) {
                $b_col = 'A';
                ++$b_row;
                $b_col_str = $b_col.$b_row;
                $objActExcelPID->setCellValue($b_col_str, $teacher);
                $old_teacher = $teacher;
            }
    }

  //header('Content-Type: application/vnd.ms-excel');
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename=teacher_all'.date('mdHi').'.xlsx');
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    //if ($this->_hasCharts) $objWriter->setIncludeCharts(TRUE);
    //因為有分頁 並使用  VLOOKUP ，所以指定以下，否則無法產生檔案。
    $objWriter->setPreCalculateFormulas(false);
    //$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    ob_clean();
    $objWriter->save('php://output');
    exit;
