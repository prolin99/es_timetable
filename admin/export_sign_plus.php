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


 
 
	//取得 
	$timetable=get_timetable_data('teacher' ,$data['info']['year']  ,$data['info']['semester'] ,'all','plus') ;
 
	//科目
 	$subject= get_subject_list() ;	
	//讀取人名
	$teacher_list = get_table_teacher_data() ;
 
	//取得級任姓名 
	$class_teacher_list = get_class_teacher_list() ;

	$beg_date =  strtotime($_GET['beg_date'] )  ;
	$end_date =  strtotime($_GET['end_date']  ) ;
 




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


for ( $m = $beg_date ; $m<= $end_date ;  $m=strtotime( date('Y-m-d',$m ) .'+1 month')  ) {
	$show_end_date =    strtotime( date('Y-m-d',$m ) .'+1 month')  ;
 	//echo date('Y-m-d',$m ) ;
	foreach ($timetable as $key =>	$table_data) {
 
		$om=0 ;
		unset($wd_have_class) ;

		//有排課的那些天
		for ($d=1 ; $d <= $DEF_SET['days'] ; $d++ )  {
			for ($ss=1 ; $ss <= $DEF_SET['sects'] ; $ss++ )  {
				if ($table_data[$d][$ss]['ss_id'])  {
					$wd_have_class[]= $d ;	
					continue ;
				}
			}		

		}
				$row++ ;
				$col ='A' ;
 				
				$objPHPExcel->getActiveSheet()->getStyle('A'.$row)->getFont()->setSize(14);
				$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . $row,$teacher_list[$key]['name'] );			
 
				$row++ ;
		 		$objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . $row, '日期' );	
		 		$objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($row+1), '節次'  );	
		 		$objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($row+2), '班級'  );		
		 		$objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($row+3), '簽到'  );				
 
 
		for  ($do_day  =  $beg_date ; $do_day<= $end_date ; $do_day = $do_day + 60*60*24 ) {

			if  ( ($do_day >= $m) and  ($do_day < $show_end_date)   ) {
 
  				//echo $do_day  ;
  				//課表，只呈現有課的當天

				$d  = date( 'N' , $do_day    ) ;
				if ( ($d <= $DEF_SET['days'])  and in_array($d, $wd_have_class) ) {

			 		for ($ss=1 ; $ss <= $DEF_SET['sects'] ; $ss++ )  {
			 			if ($table_data[$d][$ss]['class_id']) {	
			 				$col ++ ;
			 				$objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . $row, date('Y-m-d' , $do_day)  );	
			 				$objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($row+1), $DEF_SET['sects_cht_list'][$ss]  );	
			 				$objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($row+2), $class_list_c[$table_data[$d][$ss]['class_id']]  );		
			 				$objPHPExcel->setActiveSheetIndex(0)->setCellValue($col . ($row+3), ''  );				
			 				echo date('Y-m-d' , $do_day)   ;
			 			}
			 		}		
			 	}	
		 	}
		}
		$row =$row+3 ;

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
 

 