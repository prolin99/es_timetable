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
$class_list_c = es_class_name_list_c('long')  ;
/*-----------執行動作判斷區----------*/
//檢查目前的課表
$data['info'] = get_timetable_info() ;
$y = $data['info']['year'] ;
$s = $data['info']['semester']  ;
 

		
	//var_dump($max) ;	
		

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
             ->setCellValue('A' . $row, '週次')
            ->setCellValue('B' . $row, '節次')
            ->setCellValue('C' . $row, '年級')
            ->setCellValue('D' . $row, '班級')
            ->setCellValue('E' . $row, '教師姓名')
            ->setCellValue('F' . $row, '身分證')
            ->setCellValue('G' . $row, '類別')
            ->setCellValue('H' . $row, '領域')
            ->setCellValue('I' . $row, '科目')
            ->setCellValue('J' . $row, '語言別')
            ->setCellValue('K' . $row, '上課頻率');
 
 
			

 	//資料開始
	//科目
 	$subject = get_subject_list() ;	
 	//班級
 	$class_list = get_class_list() ;
 	//人員
 	$teacher_list = get_table_teacher_list('all') ;

 	$ch_num = array('','一','二','三','四','五','六','七','八','九') ;
 
 	$sql = " select *   FROM  "  . $xoopsDB->prefix("es_timetable") .  " where school_year= '$y'  and  semester= '$s'   order by  teacher , day ,  sector  " ; 
 
 	$result = $xoopsDB->query($sql) or die($sql."<br>". mysql_error()); 
 	//$mi=0 ;
	while($row_data=$xoopsDB->fetchArray($result)){
		$day ='週' . $ch_num[$row_data['day'] ];
		$sector = '第' .   $row_data['sector']   .'節'   ;
		$class_year= $ch_num[substr($row_data['class_id'],0,1)]  . '年級';
		$class_id=  '第' .  substr($row_data['class_id'],1)   .'班'   ;
		$teacher = $teacher_list[$row_data['teacher']];
		$ss_name = $subject[$row_data['ss_id']];
		$sub_name_sp = preg_split("/[_]+/", $ss_name );
		$sub_scope = $sub_name_sp[0] ;
		if  ($sub_name_sp[1]) 
			$sub_name = $sub_name_sp[1] ;
		else 
			$sub_name = $sub_name_sp[0] ;

	 
		$pos = strpos($ss_name, '彈');
		if ($pos === false) 
			$sub_kind='領域學習'  ;
		else 
			$sub_kind='彈性學習'  ;
		

 
			$row++ ;
			//行高
			$objPHPExcel->getActiveSheet()->getRowDimension($row)->setRowHeight(20);
 

			$col ='A' ;
			$col_str =$col .$row ;
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue($col_str , $day ) ;

			$col ++ ;
			$col_str =$col .$row ;
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue($col_str , $sector ) ;	

			$col ++ ;
			$col_str =$col .$row ;
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue($col_str , $class_year ) ;			

			$col ++ ;
			$col_str =$col .$row ;
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue($col_str , $class_id ) ;		

			$col ++ ;
			$col_str =$col .$row ;
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue($col_str , $teacher ) ;	

			$col ++ ;
			$col_str =$col .$row ;
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue($col_str , $teacher .'id') ;		


			$col ++ ;
			$col_str =$col .$row ;
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue($col_str , $sub_kind ) ;		

			$col ++ ;
			$col_str =$col .$row ;
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue($col_str , $sub_scope ) ;		

			$col ++ ;
			$col_str =$col .$row ;
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue($col_str , $sub_name) ;								


			$col ++ ;
			$col_str =$col .$row ;
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue($col_str , '') ;		


			$col ++ ;
			$col_str =$col .$row ;
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue($col_str , '每周上課') ;		

 
	}	
  
 
 
 	
	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename=teacher_all'.date("mdHi").'.xls' );
	header('Cache-Control: max-age=0');

	//$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	ob_clean();
	$objWriter->save('php://output');
	exit;		
 
 
?>