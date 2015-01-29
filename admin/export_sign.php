<?php
if  ($_POST['do_plus']) {
	$beg_date =  ($_POST['beg_date'] )  ;
	$end_date =  ($_POST['end_date']  ) ;
	header( "Location: export_sign_plus.php?beg_date=$beg_date&end_date=$end_date" ) ;
	exit ;
}
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




 
if  ($_POST['do_2688']) {
 
	//取得 
	$timetable=get_timetable_data('teacher' ,$data['info']['year']  ,$data['info']['semester'] ) ;
 
	//科目
 	$subject= get_subject_list() ;	
	//讀取人名
	$teacher_list = get_table_teacher_data() ;
 
	//取得級任姓名 
	$class_teacher_list = get_class_teacher_list() ;
 
 
 
	$beg_date =  strtotime($_POST['beg_date'] )  ;
	$end_date =  strtotime($_POST['end_date']  ) ;
 




	//--------------------------------------------------------------------  

 	$objPHPExcel = new PHPExcel();


	$objPHPExcel->setActiveSheetIndex(0);  //設定預設顯示的工作表
 	//橫向
	//$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);	

	//大小
	$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);


	$objActSheet = $objPHPExcel->getActiveSheet(); //指定預設工作表為 $objActSheet
	$objActSheet->setTitle("簽名表");  //設定標題	

 

	$objActSheet->getDefaultRowDimension()->setRowHeight(15);
	$objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setSize(11);
	  	


	$row= 0 ;


	foreach ($timetable as $key =>	$table_data) {
		$om=0 ;
		unset($wd_have_class) ;
	   	if ( $teacher_list[$key]['kind'] ==2 ) {
		      	  //有排課的那些天
		       	for ($d=1 ; $d <= $DEF_SET['days'] ; $d++ )  {
		       		for ($ss=1 ; $ss <= $DEF_SET['sects'] ; $ss++ )  {
		       			if ($table_data[$d][$ss]['ss_id'])  {
		       				$wd_have_class[]= $d ;	
		       				continue ;
		       			}
		       		}		

		      	 }
	   		
		    for  ($do_day  =  $beg_date ; $do_day<= $end_date ; $do_day = $do_day + 60*60*24 ) {
  
				$m = date('n' , $do_day ) ;
 				if  ($om <> $m ) {
					$om=$m ;
					$ord_i =1 ;
					//加入換頁
					if  ($row>0)
						$objPHPExcel->setActiveSheetIndex(0)->setBreak('A' . $row  , PHPExcel_Worksheet::BREAK_ROW);
					$row++ ;		
		       		//標題行
		   		$title_str =  $data['info']['year'].'學年度第' .$data['info']['semester'] .'學期教育部增置教師授課 ' .  $teacher_list[$key]['name'] . " $m 月簽到表"  ;
		   		$objPHPExcel->getActiveSheet()->getStyle('A'.$row)->getFont()->setSize(14);
		      		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . $row,$title_str);	
		      		$objActSheet->getDefaultRowDimension()->setRowHeight(20);
		      		$row++ ;			
			       	//節次
			       	$col ='A' ;
			       	$col_str =$col . ($row) ;
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue($col_str , '編號') ;       	
					$objPHPExcel->getActiveSheet()->getColumnDimension($col)->setWidth('4');
					//$objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight('15');		
					//框線
					cell_border($objPHPExcel , $col_str )  ;	       	
					$col++ ;

			       	$col_str = $col . ($row) ;
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue($col_str , '日期') ;       
					$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth('11');	
					//$objPHPExcel->getActiveSheet()->getRowDimension($row+1)->setRowHeight('15');		
					//框線
					cell_border($objPHPExcel , $col_str )  ;	
					$col++ ;

				$col_str = $col . ($row) ;
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue($col_str , '節次') ;
					$objPHPExcel->getActiveSheet()->getColumnDimension($col)->setWidth('6');
					//$objPHPExcel->getActiveSheet()->getRowDimension($row+2)->setRowHeight('15');		
					//框線
					cell_border($objPHPExcel , $col_str )  ;	
					$col++ ;

				$col_str = $col . ($row) ;
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue($col_str , '班級') ;
					$objPHPExcel->getActiveSheet()->getColumnDimension($col)->setWidth('8');
					//$objPHPExcel->getActiveSheet()->getRowDimension($row+2)->setRowHeight('15');		
					//框線
					cell_border($objPHPExcel , $col_str )  ;		
					$col++ ;

				$col_str = $col . ($row) ;
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue($col_str , '簽到') ;
					$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth('12');
					//$objPHPExcel->getActiveSheet()->getRowDimension($row+2)->setRowHeight('15');		
					//框線
					cell_border($objPHPExcel , $col_str )  ;	
					$col++ ;

			       	$col_str =$col . ($row) ;
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue($col_str , '編號') ;       	
					$objPHPExcel->getActiveSheet()->getColumnDimension($col)->setWidth('4');
					//$objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight('15');		
					//框線
					cell_border($objPHPExcel , $col_str )  ;	       	
					$col++ ;

			       	$col_str = $col . ($row) ;
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue($col_str , '日期') ;       
					$objPHPExcel->getActiveSheet()->getColumnDimension($col)->setWidth('11');
					//$objPHPExcel->getActiveSheet()->getRowDimension($row+1)->setRowHeight('15');		
					//框線
					cell_border($objPHPExcel , $col_str )  ;	
					$col++ ;

				$col_str = $col . ($row) ;
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue($col_str , '節次') ;
					$objPHPExcel->getActiveSheet()->getColumnDimension($col)->setWidth('6');
					//$objPHPExcel->getActiveSheet()->getRowDimension($row+2)->setRowHeight('15');		
					//框線
					cell_border($objPHPExcel , $col_str )  ;	
					$col++ ;

				$col_str = $col . ($row) ;
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue($col_str , '班級') ;
					$objPHPExcel->getActiveSheet()->getColumnDimension($col)->setWidth('6');
					//$objPHPExcel->getActiveSheet()->getRowDimension($row+2)->setRowHeight('15');		
					//框線
					cell_border($objPHPExcel , $col_str )  ;		
					$col++ ;

				$col_str = $col . ($row) ;
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue($col_str , '簽到') ;
					$objPHPExcel->getActiveSheet()->getColumnDimension($col)->setWidth('12');
					//$objPHPExcel->getActiveSheet()->getRowDimension($row+2)->setRowHeight('15');		
					//框線
					cell_border($objPHPExcel , $col_str )  ;						

				}	   		

		 	//課表，只呈現有課的當天
			$d  = date( 'N' , $do_day    ) ;
			if ( ($d <= $DEF_SET['days'])  and in_array($d, $wd_have_class) ) {

		 		for ($ss=1 ; $ss <= $DEF_SET['sects'] ; $ss++ )  {
		 			if ($table_data[$d][$ss]['class_id']) {	

		 				
		 				if ($ord_i % 2 ==0) {
		 				  	$col = 'F' ;
		 				  	
		 				}  else  {
		 					$row++ ;
		 					$col ='A' ;

		 				}	
		 				$prow = $row ;
 
						// 
						$col_str = $col . $prow;
						//$objPHPExcel->getActiveSheet()->getColumnDimension($col)->setWidth('7');
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue($col_str , $ord_i     ) ;
						//框線
						cell_border($objPHPExcel , $col_str  )  ;	  
						$col++ ;					

						//日期
						$col_str = $col . $prow;
						//$objPHPExcel->getActiveSheet()->getColumnDimension($col)->setWidth('7');
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue($col_str , date( 'Y-m-d' , $do_day    ) ) ;
						//框線
						cell_border($objPHPExcel , $col_str  )  ;	 
						$col++ ;		

 
						$col_str = $col . $prow;
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue($col_str , $DEF_SET['sects_cht_list'][$ss] ) ;
						//框線
						cell_border($objPHPExcel , $col_str )  ;		
						$col++ ;		

						//班級
						$col_str = $col . $prow;
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue($col_str , $class_list_c[$table_data[$d][$ss]['class_id']] ) ;
						//框線
						cell_border($objPHPExcel , $col_str )  ;	
						$col++ ;		

						$col_str = $col . $prow;
						$objPHPExcel->setActiveSheetIndex(0)->setCellValue($col_str , '') ;
						//框線
						cell_border($objPHPExcel , $col_str )  ;			

 
						$ord_i ++ ;						
		 			}
		 		}	
		 	}
  		    }
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
 
}	
 