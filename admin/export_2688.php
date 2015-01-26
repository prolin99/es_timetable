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
//$class_list_c = es_class_name_list_c('long')  ;
$class_list_c = get_timetable_class_list_c()  ;

/*-----------執行動作判斷區----------*/
//檢查目前的課表
$data['info'] = get_timetable_info() ;



 
if  ($_POST['do_2688']) {
 
	//取得 
	$timetable=get_timetable_data('teacher' ,$data['info']['year']  ,$data['info']['semester'] ) ;
 
	//var_dump( $timetable ) ;
	//科目
 	$subject= get_subject_list() ;	
	//讀取人名
	$teacher_list = get_table_teacher_data() ;
	//var_dump($teacher_list ) ;

	//取得級任姓名 
	$class_teacher_list = get_class_teacher_list() ;
 
 
 //
 
	$date = date('Y-m' , strtotime($_POST['beg_date']) ) . '-01' ;
 
	$w = date( 'N' ,strtotime($date)) -1 ;
	$this_m =  date('m' , strtotime($date))  ;
	$beg_week_date =    strtotime("- $w day",strtotime($date)) ;

 
	$next_m = strtotime($date."+ 1 months");
 
	//$this_month_last  = date( 'd' , strtotime(date('Ymd' ,$next_m)."-1 day") ) ;
	$this_month_last  = date( 'd' ,  $next_m-60*60*24 ) ;
  

 	$objPHPExcel = new PHPExcel();


	$objPHPExcel->setActiveSheetIndex(0);  //設定預設顯示的工作表
 	//橫向
	$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);	

	//大小
	$objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);


	$objActSheet = $objPHPExcel->getActiveSheet(); //指定預設工作表為 $objActSheet
	$objActSheet->setTitle("教師總表");  //設定標題	

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

	$objActSheet->getDefaultRowDimension()->setRowHeight(45);

	
	$row= 1 ;
foreach ($timetable as $key =>	$table_data) {
   if ($teacher_list[$key]['kind'] ==2 ) {
 
 
       //標題行
      	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . $row,$data['info']['year'].'學年度第' .$data['info']['semester'] .'學期教育部增置教師授課 ' .  $teacher_list[$key]['name'] . '104年1月份簽到簿' );
      	$objPHPExcel->getActiveSheet()->mergeCells('A'.$row.':K'.$row);
       	$col ='A' ;

       	//列寬
       	//$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth('10');

	$row++ ;
       	$do_day = strtotime($date) ;

       	//check day of week have class 
       	for ($d=1 ; $d <= $DEF_SET['days'] ; $d++ )  {
       		for ($ss=1 ; $ss <= $DEF_SET['sects'] ; $ss++ )  {
       			if ($table_data[$d][$ss]['ss_id'])  {
       				$wd_have_class[]= $d ;	
       				continue ;
       			}
       		}		

       	}
       	//var_dump($wd_have_class) ;

       	//節次
       	$col_str = 'A' . ($row) ;
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue($col_str , '日期') ;       	
		$objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight('20');		
       	$col_str = 'A' . ($row+1) ;
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue($col_str , '星期') ;
		$objPHPExcel->getActiveSheet()->getRowDimension($row+1)->setRowHeight('20');		
       	for ($i =1 ; $i <= $DEF_SET['sects'] ; $i++ ) {
       		$col_str = 'A' . ($row+$i+1) ;
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue($col_str , $DEF_SET['sects_cht_list'][$i] ) ;
       	}
       	$col_str = 'A' . ($row+$i) ;
       	$objPHPExcel->setActiveSheetIndex(0)->setCellValue($col_str , '簽到' ) ;
       	$col_str = 'B' . ($row+$i +1 ) ;
       	$objPHPExcel->setActiveSheetIndex(0)->setCellValue($col_str , '共                  節' ) ;
       	$objPHPExcel->getActiveSheet()->getRowDimension($row+$i+1)->setRowHeight('20');	
	//加入換頁
	$objPHPExcel->setActiveSheetIndex(0)->setBreak('A' . ($row+$i +2 )  , PHPExcel_Worksheet::BREAK_ROW);
 	
	for ($i=1 ; $i <=$this_month_last ; $i++)  {
		$s  = date( 'N' , $do_day    ) ;
		if ( ($s <= $DEF_SET['days'])  and in_array($s, $wd_have_class) ) {

 

			$col++ ;
			$row2 = $row+1 ;
			$col_str =$col . $row ;
			$objPHPExcel->getActiveSheet()->getColumnDimension($col)->setWidth('6');
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue($col_str , date( 'm-d' , $do_day    ) ) ;
			$col_str =$col . $row2 ;
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue($col_str , $DEF_SET['week'][$s] ) ;

			for ($ss=1 ; $ss <= $DEF_SET['sects'] ; $ss++ )  {
				$row2 = $row+$ss+1 ;
				$col_str =$col . $row2 ;
 				$cell_doc = $class_list_c[$table_data[$s][$ss]['class_id']] ."\n" . $subject[$table_data[$s][$ss]['ss_id']]   ;
 				$objPHPExcel->setActiveSheetIndex(0)->setCellValue($col_str , $cell_doc  ) ;

 			}	
 
 		}
 
		$do_day =  $do_day+ 60*60*24  ;
	}	

	$row= $row+11 ;
 
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