<?php
//  ------------------------------------------------------------------------ //
// 本模組由 prolin 製作
// 製作日期：2014-07-20
// $Id:$
// ------------------------------------------------------------------------- //
/*-----------引入檔案區--------------*/
//include_once 'header_admin.php';

include_once 'header.php';

require_once XOOPS_ROOT_PATH . '/modules/tadtools/vendor/autoload.php';

use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;

/*-----------function區--------------*/

//取得中文班名
//$class_list_c = es_class_name_list_c('long')  ;
$class_list_c = get_timetable_class_list_c('long');

/*-----------執行動作判斷區----------*/
//檢查目前的課表
$data['info'] = get_timetable_info();

    $mid = 'class_id';
    $n_class_id = $_GET['class_id'];

    //取得 班級模式，$n_class_id 班
    $timetable = get_timetable_data($mid, $data['info']['year'], $data['info']['semester'], $n_class_id);

    //var_dump( $timetable ) ;
    //科目
    $subject = get_subject_list();
    //讀取人名
    $teacher_list = get_table_teacher_data();

    //取得級任姓名
    $class_teacher_list = get_class_teacher_list();

    $PHPWord = new PHPWord();
    //$section = $PHPWord->createSection();

 /*
    $PHPWord->setDefaultFontName('標楷體'); //設定預設字型
    $PHPWord->setDefaultFontSize(14);     //設定預設字型大小
    */

/*
    $sectionStyle = array('orientation' => null,  'marginLeft' => 900); //頁面設定（orientation 的值可以是橫向landscape或直向portrait。設定項目有：orientation、marginTop、marginLeft、marginRight、marginBottom、borderTopSize、borderTopColor、borderLeftSize、borderLeftColor、borderRightSize、borderRightColor、borderBottomSize、borderBottomColor）
    $section = $PHPWord->createSection($sectionStyle); //建立一個頁面
*/
    $section = $PHPWord->createSection(); //建立一個頁面
    $styleFont_h1 = array('name' => 'Tahoma',  'size' => 24, 'bold' => true);
    $styleParagraph_h1 = array('align' => 'center', 'spaceAfter' => 100);
    $styleFont_h2 = array('name' => 'Tahoma',  'size' => 18, 'bold' => true);
    $styleParagraph_h2 = array('align' => 'center', 'spaceAfter' => 100);
    //
    $style_cell = array('align' => 'center');
    $styleFont_cell = array('name' => 'Tahoma',  'size' => 14);
    $styleFont_cell_red = array('name' => 'Tahoma',  'size' => 14 ,  'color' => 'red');
    //有單雙週小字
    $styleFont_d_cell = array('name' => 'Tahoma',  'size' => 10);
    $styleFont_d_cell_red = array('name' => 'Tahoma',  'size' => 10 ,  'color' => 'red');
    //
    $cellStyle = array('bgColor' => 'EEEEEE','valign' => 'center');
    //cell 字型

    $styleFont_cell_top = array('name' => 'Tahoma',  'size' => 14 , 'bold' => true);
    $style_cell_top = array('align' => 'center');

    $styleFont_cell_left = array('name' => 'Tahoma',  'size' => 10 , 'bold' => true);
    $style_cell_left = array('align' => 'center');

    $styleTable = array('borderColor' => '000000', 'borderSize' => 6, 'cellMargin' => 50);
    $styleFirstRow = array('bgColor' => 'EEEEEE'); //首行樣式

    $page = 0;
foreach ($timetable as $key => $table_data) {
    if ($page > 0) {
        $section->addPageBreak();
    }  //換頁 (第一次不換頁)
     ++$page;
    //標題處
    $section->addText($data['info']['year'].'學年度第'.$data['info']['semester'].'學期課表', $styleFont_h1, $styleParagraph_h1);

    //
    if ($mid == 'teacher') {
        $h2_title = $teacher_list[$key]['name'].'-教師課表';
    }
    if ($mid == 'class_id') {
        $h2_title = $class_list_c[$key].' 班級課表  (級任：'.$class_teacher_list[$key].')';
    }
    if ($mid == 'room') {
        $h2_title = $key.'-教室課表';
    }
    $section->addText($h2_title, $styleFont_h2, $styleParagraph_h2);

    $PHPWord->addTableStyle('myTable', $styleTable, $styleFirstRow); //建立表格樣式
    //$PHPWord->addTableStyle('myTable'); //建立表格樣式
    $table = $section->addTable('myTable');//建立表格


    $table->addRow(1000); //新增一列
/*
    //$cellStyle =array('textDirection'=>PHPWord_Style_Cell::TEXT_DIR_BTLR, 'bgColor'=>'C0C0C0'); //儲存格樣式（設定項：valign、textDirection、bgColor、borderTopSize、borderTopColor、borderLeftSize、borderLeftColor、borderRightSize、borderRightColor、borderBottomSize、borderBottomColor）
    $table->addCell(2000, $cellStyle)->addText('節次', $styleFont_cell_top, $style_cell_top); //新增一格

    for ($i = 1; $i <= $DEF_SET['days']; ++$i) {
        $table->addCell(1000, $cellStyle)->addText($DEF_SET['week'][$i], $styleFont_cell_top, $style_cell_top);
    } //新增一格
*/
    //$cellStyle =array('textDirection'=>PHPWord_Style_Cell::TEXT_DIR_BTLR, 'bgColor'=>'C0C0C0'); //儲存格樣式（設定項：valign、textDirection、bgColor、borderTopSize、borderTopColor、borderLeftSize、borderLeftColor、borderRightSize、borderRightColor、borderBottomSize、borderBottomColor）
    $table->addCell(1000, $cellStyle)->addText('節', $styleFont_cell_top, $style_cell_top); //新增一格

    for ($i = 1; $i <= $DEF_SET['days']; ++$i) {
        $table->addCell(1600, $cellStyle)->addText($DEF_SET['week'][$i], $styleFont_cell_top, $style_cell_top);
    } //新增一格

    //課表內容
    for ($s = 1; $s <= $DEF_SET['sects']; ++$s) {
        $table->addRow(); //新增一列
        $time_str = preg_replace('/[~-]/', "~<w:br/>", $DEF_SET['time_list'][$s]);
        $table->addCell(1000, $cellStyle)->addText($DEF_SET['sects_cht_list'][$s]."<w:br/> $time_str", $styleFont_cell_left, $style_cell_left); //新增一格
        for ($i = 1; $i <= $DEF_SET['days']; ++$i) {
            $cell_doc = '';
            $show_w = 0;    //做有無單雙週課表
            for ($w = 0; $w <= 2;++$w) {
                if ($table_data[$i][$s][$w]['class_id']) {
                    if ($w == 1) {
                        $cell_doc .= '(單)';
                    }
                    if ($w == 2) {
                        $cell_doc .= '(雙)';
                    }
                    if ($mid == 'teacher') {
                        $cell_doc .= $class_list_c[$table_data[$i][$s][$w]['class_id']]."<w:br/>".$subject[$table_data[$i][$s][$w]['ss_id']]."<w:br/>".$table_data[$i][$s][$w]['room']."";
                    }
                    if ($mid == 'class_id') {
                        $cell_doc .=  $subject[$table_data[$i][$s][$w]['ss_id']]."<w:br/>".$teacher_list[$table_data[$i][$s][$w]['teacher']]['name']."<w:br/>".$table_data[$i][$s][$w]['room']."";
                    }
                    if ($mid == 'room') {
                        $cell_doc .= $class_list_c[$table_data[$i][$s][$w]['class_id']]."<w:br/>".$subject[$table_data[$i][$s][$w]['ss_id']]."<w:br/>".$teacher_list[$table_data[$i][$s][$w]['teacher']]['name']."";
                    }
                    $show_w = $w;

                    //班級課表，科任
                    if (($mid == 'class_id') and ($teacher_list[$table_data[$i][$s][$w]['teacher']]['name'] != $class_teacher_list[$key])) {
                        if ($show_w  > 0) {
                            $font = $styleFont_d_cell_red;
                        } else {
                            $font = $styleFont_cell_red;
                        }
                    } else {
                        if ($show_w  > 0) {
                            $font = $styleFont_d_cell;
                        } else {
                            $font = $styleFont_cell;
                        }
                    }
                }
            }
            $table->addCell(1600)->addText($cell_doc, $font, $style_cell); //新增一格
        }
        if ( ($s == $DEF_SET['m_sects']) and  $DEF_SET['noon_show']  ) {
            //上午節數
            $table->addRow(500); //新增一列
            $table->addCell(1000, $cellStyle)->addText('午休', $styleFont_cell_top, $style_cell_top); //新增一格
            for ($i = 1; $i <= $DEF_SET['days']; ++$i) {
                $table->addCell(1600, $cellStyle)->addText('', $styleFont_cell_top, $style_cell_top); //新增一格
            }
        }
    }
}

    //header('Content-Type: application/vnd.ms-word');
    header('Content-Type:application/vnd.openxmlformats-officedocument.wordprocessingml.document');
    header('Content-Disposition: attachment;filename=功課表.docx');
    header('Cache-Control: max-age=0');
    $objWriter = IOFactory::createWriter($PHPWord, 'Word2007');
    ob_clean();
    $objWriter->save('php://output');
    exit;
