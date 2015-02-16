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
$class_list_c = get_timetable_class_list_c()  ;

/*-----------執行動作判斷區----------*/
//檢查目前的課表
$data['info'] = get_timetable_info() ;

//框線
function cell_border($objPHPExcel , $cell  ,$thick_left = false ) {
		//設定框線
		$objPHPExcel->getActiveSheet()->getStyle($cell)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN );  
		$objPHPExcel->getActiveSheet()->getStyle($cell)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN );  
		if ($thick_left )
			$objPHPExcel->getActiveSheet()->getStyle($cell)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK );  
		else 	
			$objPHPExcel->getActiveSheet()->getStyle($cell)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN );  
		$objPHPExcel->getActiveSheet()->getStyle($cell)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN );  	
}


 
 

 
	//科目
 	//$subject= get_subject_list() ;	
	//讀取人名
	$teacher_list = get_table_teacher_data() ;
 
	//取得級任姓名 
	//$class_teacher_list = get_class_teacher_list() ;

	$beg_date =  strtotime($_GET['beg_date'] )  ;
	$end_date =  strtotime($_GET['end_date']  ) ;
	$over_id =  intval($_GET['over_id']  ) ;
 
	//取得 
	$timetable=get_timetable_data('teacher' ,$data['info']['year']  ,$data['info']['semester'] ,'all', $over_id ) ;



	//--------------------------------------------------------------------  

 	$objPHPExcel = new PHPExcel();


	$objPHPExcel->setActiveSheetIndex(0);  //設定預設顯示的工作表
 	//橫向
	//$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);	

	//大小
	$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);


	$objActSheet = $objPHPExcel->getActiveSheet(); //指定預設工作表為 $objActSheet
	$objActSheet->setTitle("簽名表");  //設定標題	

 

	$objActSheet->getDefaultRowDimension()->setRowHeight(14);
	if ($DEF_SET['es_tt_week_D'])
		$objActSheet->getDefaultColumnDimension()->setWidth(7);
	else	
		$objActSheet->getDefaultColumnDimension()->setWidth(6);
	$objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setSize(10);
 
	  	


	$row= 0 ;

//分月份重覆列出
for ( $m = $beg_date ; $m<= $end_date ;  $m=strtotime( date('Y-m-01',$m ) .'+1 month')  ) {
	$show_end_date =    strtotime( date('Y-m-01',$m ) .'+1 month')  ;
	if  ($row>0) 	$objPHPExcel->setActiveSheetIndex(0)->setBreak('A' . $row  , PHPExcel_Worksheet::BREAK_ROW);	
 	//超鐘點課表
	foreach ($timetable as $key =>	$table_data) {
 
		$om=0 ;
		$add_sects= 0 ;
		unset($wd_have_class) ;

		//有排課的那些天
		for ($d=1 ; $d <= $DEF_SET['days'] ; $d++ )  {
			for ($ss=1 ; $ss <= $DEF_SET['sects'] ; $ss++ )  {
		       			for ($w=0 ; $w<=2 ;$w++) {
			       			if ($table_data[$d][$ss][$w]['ss_id'])  {
			       				$wd_have_class[]= $d ;	
			       				continue ;
			       			}
		       			}
			}		
		}
		$row++ ;

		//左方標題處
		$col ='A' ;
			
		$objPHPExcel->getActiveSheet()->getStyle('A'.$row)->getFont()->setSize(12);
		$objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight('20');		
		//$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . $row,$teacher_list[$key]['name'] .date('Y 年 n 月',$m) .'超鐘點(共       節)')  ;			

		$row++ ;
 		$objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . $row, '日期' );	
 		$objPHPExcel->getActiveSheet()->getColumnDimension($col)->setWidth(5);
 		cell_border($objPHPExcel , $col . $row )  ;	
 		$objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($row+1), '星期' );	
 		cell_border($objPHPExcel , $col . ($row+1) )  ;	
 		$objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($row+2), '節次'  );	
 		cell_border($objPHPExcel , $col . ($row+2))  ;	
 		$objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($row+3), '班級'  );		
 		cell_border($objPHPExcel , $col . ($row+3) )  ;	
 		$objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($row+4), "簽\n到"  );	
 		cell_border($objPHPExcel , $col . ($row+4) )  ;	
 		$objPHPExcel->getActiveSheet()->getRowDimension($row+4)->setRowHeight('45');				
 
		//此月各週 
		for  ($do_day  =  $beg_date ; $do_day<= $end_date ; $do_day = $do_day + 60*60*24 ) {

			if  ( ($do_day >= $m) and  ($do_day < $show_end_date)   ) {
 
  				//課表，只呈現有課的當天

				$d  = date( 'N' , $do_day    ) ;
				if ( ($d <= $DEF_SET['days'])  and in_array($d, $wd_have_class) ) {

			 		for ($ss=1 ; $ss <= $DEF_SET['sects'] ; $ss++ )  {
			 			for ($w=0 ;$w<=2;$w++ ) {
				 			if ($table_data[$d][$ss][$w]['class_id']) {	
				 				$week_mark='' ;
								if ($w==1) $week_mark='(單)' ;
								if ($w==2) $week_mark='(雙)' ;
				 				$col ++ ;
				 				$objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . $row, date('m-d' , $do_day)  );	
				 				cell_border($objPHPExcel , $col . $row )  ;	
				 				$objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($row+1),  $DEF_SET['week'][$d]  );	
				 				cell_border($objPHPExcel , $col . ($row+1) )  ;	
				 				$objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($row+2), $DEF_SET['sects_cht_list'][$ss]  );	
				 				cell_border($objPHPExcel , $col . ($row+2) )  ;	
				 				$objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($row+3), $class_list_c[$table_data[$d][$ss][$w]['class_id']] .$week_mark );		
				 				cell_border($objPHPExcel , $col . ($row+3) )  ;	
				 				$objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($row+4), ''  );				
				 				cell_border($objPHPExcel , $col . ($row+4) )  ;	
				 				$add_sects ++ ;

				 			}
			 			}
			 		}		
			 	}	
		 	}
		}
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . ($row -1),$teacher_list[$key]['name'] .date('Y 年 n 月',$m) . $DEF_SET['es_tt_over_list'][$over_id] . " (共 $add_sects 節)")  ;
		$row =$row+4 ;

	}

} 
 
	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename=2688'.date("mdHi").'.xls' );
	header('Cache-Control: max-age=0');

	//$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	ob_clean();
	$objWriter->save('php://output');
	exit;
 

 