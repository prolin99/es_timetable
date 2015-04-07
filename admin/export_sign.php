<?php
if  (@$_POST['do_plus']) {
	$beg_date =  ($_POST['beg_date'] )  ;
	$end_date =  ($_POST['end_date']  ) ;
	$over_id =  ($_POST['over_id']  ) ;
	$week_m = intval($_POST['week_m']); 
	header( "Location: export_sign_plus.php?beg_date=$beg_date&end_date=$end_date&over_id=$over_id&week_m=$week_m" ) ;
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
$week_cht = array('' ,'一' ,'二','三','四','五','六','日' );

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

function cell_show_title($objPHPExcel  , $row , $title) {

	$objPHPExcel->getActiveSheet()->getStyle('A'.$row)->getFont()->setSize(14);
  	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . $row,$title);	
  	$objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(20);
  	$row++ ;			
       	//節次
       	$col ='A' ;
 
       	$col_str =$col . ($row) ;
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue($col_str , '編號') ;       	
		$objPHPExcel->getActiveSheet()->getColumnDimension($col)->setWidth('3');
		//$objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight('15');		
		//框線
		cell_border($objPHPExcel , $col_str )  ;	       	
		$col++ ;
 
       	$col_str = $col . ($row) ;
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue($col_str , '日期') ;       
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth('8');	
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
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth('16');
		//$objPHPExcel->getActiveSheet()->getRowDimension($row+2)->setRowHeight('15');		
		//框線
		cell_border($objPHPExcel , $col_str )  ;	
		$col++ ;

       	$col_str =$col . ($row) ;
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue($col_str , '編號') ;       	
		$objPHPExcel->getActiveSheet()->getColumnDimension($col)->setWidth('3');
		//$objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight('15');		
		//框線
		cell_border($objPHPExcel , $col_str )  ;	       	
		$col++ ;

       	$col_str = $col . ($row) ;
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue($col_str , '日期') ;       
		$objPHPExcel->getActiveSheet()->getColumnDimension($col)->setWidth('8');
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
		$objPHPExcel->getActiveSheet()->getColumnDimension($col)->setWidth('16');
		//$objPHPExcel->getActiveSheet()->getRowDimension($row+2)->setRowHeight('15');		
		//框線
		cell_border($objPHPExcel , $col_str )  ;		
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
 
 
	//讀取 tad_cal 行事曆
	$holiday  = get_tad_cal_holiday( $DEF_SET['es_tt_Holiday_KW'] ,date('Y-m-d' , $beg_date )  ,date('Y-m-d' , $end_date )) ;

	$week_m = intval($_POST['week_m']); 
 
	$beg_week_num = (int)date('W',  $beg_date  )  ; 
 
	// 找到雙週基準點
	if ( ($beg_week_num %2) == 0 ) {
		if  ($week_m==1 ) $beg_week_num-=1 ;	
 
	}else {   //單
 
		if  ($week_m==1 ) $beg_week_num= $beg_week_num-1 ;
 
	}	
  
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
		       			for ($w=0 ; $w<=2 ;$w++) {
			       			if ($table_data[$d][$ss][$w]['ss_id'])  {
			       				$wd_have_class[]= $d ;	
			       				continue ;
			       			}
		       			}
		       		}		

		      	 }
	   		
		    	for  ($do_day  =  $beg_date ; $do_day<= $end_date ; $do_day = $do_day + 60*60*24 ) {
  
				
				if ($holiday[date('Y-m-d',$do_day)])	continue;	//當天放假，略過

				$m = date('n' , $do_day ) ;		 

				if  ($om <> $m ) {
					//換月加入換頁
					if  ($row>0) {
						if ($o_key == $key) {
							//還是同一人時
	  						$row++ ;
	  						$col_str = 'A' . $row;
	  						$objPHPExcel->setActiveSheetIndex(0)->setCellValue($col_str , "共 ". ($ord_i-1) ."  節"   ) ;

	  					}
						$objPHPExcel->setActiveSheetIndex(0)->setBreak('A' . $row  , PHPExcel_Worksheet::BREAK_ROW);
					}
					$o_key = $key ;	
					$om=$m ;
					$ord_i =1 ;					

					$row++ ;		
			       		//標題行
					$title_str =  $data['info']['year'].'學年度第' .$data['info']['semester'] .'學期教育部增置教師授課 ' .  $teacher_list[$key]['name'] . date('Y 年 n 月',$do_day) . "簽到表"  ;
					cell_show_title($objPHPExcel , $row , $title_str ) ;
					$row++ ;	
					//echo $row ;

				}	   		

			 	//課表，只呈現有課的當天
				$d  = date( 'N' , $do_day    ) ;		//星期?
				$d_cht = $week_cht[$d]  ;

				

				if ( ($d <= $DEF_SET['days'])  and in_array($d, $wd_have_class) ) {		//在課表週內，且有課
					$sign_week = ((int)date('W',  $do_day  ) - $beg_week_num) % 2  ; 	//單週/雙週

			 		for ($ss=1 ; $ss <= $DEF_SET['sects'] ; $ss++ )  {
			 			$this_sect_data ='' ;
			 		      	for ($w=0 ; $w<=2 ; $w++ )	{
				 			if ($table_data[$d][$ss][$w]['class_id']) {	
				 				$week_mark='' ;

								if ($w==1) $week_mark='(單)' ;
								if ($w==2) $week_mark='(雙)' ;

								if  ($this_sect_data) $this_sect_data .= ',' ;
								if ($w==0)
									$this_sect_data .= $class_list_c[$table_data[$d][$ss][$w]['class_id']] .$week_mark ;
								if (($sign_week)  and ($w==1) )
									$this_sect_data .= $class_list_c[$table_data[$d][$ss][$w]['class_id']] .$week_mark  ;
								if ((!$sign_week)  and ($w==2) )
									$this_sect_data .= $class_list_c[$table_data[$d][$ss][$w]['class_id']] .$week_mark  ;
		 					}
						}
						if ($this_sect_data){	//有上課
			 				
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
							$objPHPExcel->setActiveSheetIndex(0)->setCellValue($col_str , date( 'n-d' , $do_day ) . "($d_cht)" ) ;
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
							$objPHPExcel->setActiveSheetIndex(0)->setCellValue($col_str ,  $this_sect_data) ;
							//框線
							cell_border($objPHPExcel , $col_str )  ;	
							$col++ ;		

							$col_str = $col . $prow;
							$objPHPExcel->setActiveSheetIndex(0)->setCellValue($col_str , '') ;
							//框線
							cell_border($objPHPExcel , $col_str )  ;			

	 
							$ord_i ++ ;						
			 			}//有課
 
			 		}//每節	
			 	}//該天有課
	  		}//每天
	  		$row++ ;
	  		$col_str = 'A' . $row;
	  		$objPHPExcel->setActiveSheetIndex(0)->setCellValue($col_str , "共 ". ($ord_i-1) ."  節"   ) ;
	  	}//身份2688
	}

 
 
	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename=2688'.date("mdHi").'.xlsx' );
	header('Cache-Control: max-age=0');

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	//$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	ob_clean();
	$objWriter->save('php://output');
	exit;
 
}	
 