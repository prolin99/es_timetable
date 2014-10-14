<?php
//  ------------------------------------------------------------------------ //
// 本模組由 prolin 製作
// 製作日期：2014-07-20
// $Id:$
// ------------------------------------------------------------------------- //
/*-----------引入檔案區--------------*/
include_once "header_admin.php";

include_once "header.php";

include_once "../../tadtools/PHPExcel.php";
require_once '../../tadtools/PHPExcel/IOFactory.php';    
/*-----------function區--------------*/

//取得中文班名
$class_list_c = es_class_name_list_c()  ;
/*-----------執行動作判斷區----------*/
//檢查目前的課表
$data['info'] = get_timetable_info() ;

if  ($_GET['mode']) {
	$mid =$_GET['mode'] ;
 
	//取得教師課表 
	$timetable=get_timetable_data($mid ,$data['info']['year']  ,$data['info']['semester'] ) ;
 
 
	//科目
 	$subject= get_subject_list() ;	
	//讀取人名
	$teacher_list = get_table_teacher_data() ;
	
	//取得級任姓名
	$class_teacher_list = get_class_teacher_list() ;
	$teacher_list = get_table_teacher_list('all') ;
 

 	$objPHPExcel = new PHPExcel();
	$objPHPExcel->setActiveSheetIndex(0);  //設定預設顯示的工作表
	$objActSheet = $objPHPExcel->getActiveSheet(); //指定預設工作表為 $objActSheet
	$objActSheet->setTitle("教師總表");  //設定標題	
  	//設定框線
	$objBorder=$objActSheet->getDefaultStyle()->getBorders();
	$objBorder->getBottom()
          	->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN)
          	->getColor()->setRGB('000000'); 
	$objActSheet->getDefaultRowDimension()->setRowHeight(15);

	
	$row= 1 ;
       //標題行
      	$objPHPExcel->setActiveSheetIndex(0) 
            ->setCellValue('A' . $row, '姓名');
       $col ='A' ;
       //列寬
       $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth('10');
 
	for ($i=1 ; $i <= $DEF_SET['days'] ; $i++)  
		for ($s=1 ; $s <= $DEF_SET['sects'] ; $s++ )  {
			$col++ ;
			$col_str =$col .$row ;
			$objPHPExcel->getActiveSheet()->getColumnDimension($col)->setWidth('6');
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue($col_str , "$i-$s") ;
		}	
			

 
     //資料區
     foreach ( $timetable  as $teacher_id => $mytable )  {
			$row++ ;
			//行高
			$objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(34);
 

			$col ='A' ;
			$col_str =$col .$row ;
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue($col_str , $teacher_list[$teacher_id] ) ;
			for ($i=1 ; $i <= $DEF_SET['days'] ; $i++)  
				for ($s=1 ; $s <= $DEF_SET['sects'] ; $s++ )  {
					$col++ ;
					if ($mytable[$i][$s]['ss_id']) {
					$col_str =$col .$row ;
					
					//echo $mytable[$i][$s]['class_id'].$subject[$mytable[$i][$s]['ss_id']] ;
					$short_ss = mb_substr($subject[$mytable[$i][$s]['ss_id']],0,2,"utf-8") ;
					$tstr = $class_list_c[$mytable[$i][$s]['class_id']] ."\n" .$short_ss;
 					$objPHPExcel->getActiveSheet()->getStyle($col_str)->getAlignment()->setWrapText(true); //自動換行
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue($col_str , $tstr) ;
					}
			}			
 
	}	
  
 
 
 	
	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename=teacher_all'.date("mdHi").'.xls' );
	header('Cache-Control: max-age=0');

	//$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	ob_clean();
	$objWriter->save('php://output');
	exit;		
 
}	
?>