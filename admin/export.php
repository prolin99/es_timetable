<?php
//  ------------------------------------------------------------------------ //
// 本模組由 prolin 製作
// 製作日期：2014-05-01
// $Id:$
// ------------------------------------------------------------------------- //
/*-----------引入檔案區--------------*/
include_once "header_admin.php";
 
include_once "header.php";

include_once "../../tadtools/PHPWord.php";

/*-----------function區--------------*/


/*-----------執行動作判斷區----------*/
//檢查目前的課表
$data['info'] = get_timetable_info() ;

if  ($_GET['mode']) {
	$mid =$_GET['mode'] ;

	//取得 
	$timetable=get_timetable_data($mid ,$data['info']['year']  ,$data['info']['semester'] ) ;
 
	//var_dump( $timetable ) ;
	//科目
 	$subject= get_subject_list() ;	
	//讀取人名
	$teacher_list = get_table_teacher_data() ;
	
	//取得級任姓名
	$class_teacher_list = get_class_teacher_list() ;
 
 
	$PHPWord = new PHPWord();
	//$section = $PHPWord->createSection();
	

 	$PHPWord->setDefaultFontName('標楷體'); //設定預設字型
	$PHPWord->setDefaultFontSize(14);     //設定預設字型大小
	

	$sectionStyle = array('orientation' => null,  'marginLeft' => 900); //頁面設定（orientation 的值可以是橫向landscape或直向portrait。設定項目有：orientation、marginTop、marginLeft、marginRight、marginBottom、borderTopSize、borderTopColor、borderLeftSize、borderLeftColor、borderRightSize、borderRightColor、borderBottomSize、borderBottomColor）
	$section = $PHPWord->createSection([$sectionStyle]); //建立一個頁面
	$styleFont_h1 = array('name'=>'Tahoma',  'size'=>32, 'bold'=>true);
	$styleParagraph_h1 = array('align'=>'center', 'spaceAfter'=>100);
	$styleFont_h2 = array('name'=>'Tahoma',  'size'=>24, 'bold'=>true);
	$styleParagraph_h2 = array('align'=>'center', 'spaceAfter'=>100);	
	//
	$style_cell = array('align'=>'center');	
	$styleFont_cell =  array('name'=>'Tahoma',  'size'=>14);
	$styleFont_cell_red =  array('name'=>'Tahoma',  'size'=>14 ,  'color'=>'red');
	
	//
	$cellStyle = array(	 'bgColor'=>'C0C0C0','valign'=>'center');
	//cell 字型
	
	$styleFont_cell_top =  array('name'=>'Tahoma',  'size'=>14 , 'bold'=>true);
	$style_cell_top = array('align'=>'center');	
	
	/*
	$styleTable = array('borderColor'=>'006699', 'borderSize'=>6, 'cellMargin'=>50); //表格樣式（可用設定：cellMarginTop、cellMarginLeft、cellMarginRight、cellMarginBottom、cellMargin、bgColor、 borderTopSize、borderTopColor、borderLeftSize、borderLeftColor、borderRightSize、borderRightColor、borderBottomSize、borderBottomColor、borderInsideHSize、borderInsideHColor、borderInsideVSize、borderInsideVColor、borderSize、borderColor）
	$styleFirstRow = array('bgColor'=>'66BBFF'); //首行樣式	
	*/
	$page =0 ;
foreach ($timetable as $key =>	$table_data) {
	 if ($page >0) $section->addPageBreak();  //換頁 (第一次不換頁)
	 $page++ ;
	//標題處
	$section->addText($data['info']['year'].'學年度 第' .$data['info']['semester'] .'學期 課表', $styleFont_h1,$styleParagraph_h1);
 
	//
	if ($mid =='teacher') $h2_title = $teacher_list[$key]['name']. '-教師課表'; 
	if ($mid =='class_id') $h2_title = $key . '-班級課表  (級任：' .$class_teacher_list[$key] .')' ; 
	if ($mid =='room') $h2_title = $key . '-教室課表'; 
	$section->addText( $h2_title ,$styleFont_h2,$styleParagraph_h2);
 
 


	//$PHPWord->addTableStyle('myTable', $styleTable, $styleFirstRow); //建立表格樣式
	$PHPWord->addTableStyle('myTable' ); //建立表格樣式
	$table = $section->addTable('myTable');//建立表格

	
	$table->addRow(1000); //新增一列

	//$cellStyle =array('textDirection'=>PHPWord_Style_Cell::TEXT_DIR_BTLR, 'bgColor'=>'C0C0C0'); //儲存格樣式（設定項：valign、textDirection、bgColor、borderTopSize、borderTopColor、borderLeftSize、borderLeftColor、borderRightSize、borderRightColor、borderBottomSize、borderBottomColor）
	$table->addCell(2000,$cellStyle )->addText('節次',$styleFont_cell_top ,$style_cell_top); //新增一格
	
	for ($i=1 ; $i <= $DEF_SET['days'] ; $i++)  
		$table->addCell(1000,$cellStyle )->addText('星期'.$i ,$styleFont_cell_top ,$style_cell_top ); //新增一格
 
	//課表內容	
 	for ($s=1 ; $s <= $DEF_SET['sects'] ; $s++ )  {
		$table->addRow(); //新增一列
		
		$table->addCell(1000,$cellStyle )->addText("第 $s 節",$styleFont_cell_top,$style_cell_top); //新增一格
		for ($i=1 ; $i <= $DEF_SET['days'] ; $i++)  {
			if ($mid =='teacher') $cell_doc = $table_data[$i][$s]['class_id'] ."\n" . $subject[$table_data[$i][$s]['ss_id']] ."\n" .$table_data[$i][$s]['room'] ;
			if ($mid =='class_id') $cell_doc =  $subject[$table_data[$i][$s]['ss_id']] ."\n" .$teacher_list[$table_data[$i][$s]['teacher']]['name'] ."\n" .$table_data[$i][$s]['room'] ;
			if ($mid =='room') $cell_doc = $table_data[$i][$s]['class_id'] ."\n" . $subject[$table_data[$i][$s]['ss_id']] ."\n" .$teacher_list[$table_data[$i][$s]['teacher']]['name'] ;
			
			//班級課表，科任
			if( ($mid =='class_id') and ($teacher_list[$table_data[$i][$s]['teacher']]['name']<> $class_teacher_list[$key]) )
				
				$table->addCell(2000   )->addText($cell_doc ,$styleFont_cell_red,$style_cell); //新增一格	
			else 
				$table->addCell(2000   )->addText($cell_doc ,$styleFont_cell,$style_cell); //新增一格	
		}	
		if ($s == $DEF_SET['m_sects']) {//上午節數
			$table->addRow(500); //新增一列
			$table->addCell(1000,$cellStyle )->addText("午休",$styleFont_cell_top,$style_cell_top); //新增一格
			for ($i=1 ; $i <= $DEF_SET['days'] ; $i++)  {
				$table->addCell(1000,$cellStyle )->addText("",$styleFont_cell_top,$style_cell_top); //新增一格
			}	
			
		}	
	}	
 	
}

	header('Content-Type: application/vnd.ms-word');
	header('Content-Disposition: attachment;filename=功課表.docx');
	header('Cache-Control: max-age=0');
	$objWriter = PHPWord_IOFactory::createWriter($PHPWord, 'Word2007');
	$objWriter->save('php://output');	
	exit;		 	
}	
?>