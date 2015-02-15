<?php
//  ------------------------------------------------------------------------ //
// 本模組由 prolin 製作
// 製作日期：2014-07-01
// $Id:$
// ------------------------------------------------------------------------- //

/*-----------引入檔案區--------------*/
include_once "header_admin.php";
 
include_once "header.php";

if ( $_GET['year'] and $_GET['semester']  and $_GET['do']  and $_GET['id']   ) {
	//讀取科目
	$subject= get_subject_list() ;
	//讀取人名
	$teacher_list = get_table_teacher_data() ;
 	

	if ($_GET['sect']) {
		list( $cell , $d,$s  ) =preg_split('/[_]/' ,$_GET['sect'])  ;
	}
	for ($w=0 ; $w<=2 ; $w++) {
		$data[$d][$s][$w]['ss_id']= 0 ;
		$data[$d][$s][$w]['week_d']= 0 ;
	}	
	
	$sql = " select *  FROM  "  . $xoopsDB->prefix("es_timetable") .  
		" where school_year= '{$_GET['year']}'  and  semester= '{$_GET['semester']}'    and  class_id= '{$_GET['id']}'  
		and  day='$d' and  sector='$s' order by day,sector   " ;
 	sleep(1) ;
 	$result = $xoopsDB->queryF($sql) or die($sql."<br>". mysql_error());
	while($row=$xoopsDB->fetchArray($result)){
		$row['subject_name']= $subject[$row['ss_id']] ;
		$row['teacher_name']= $teacher_list[$row['teacher']]['name'] ;
		$w= $row['week_d'] ;
		$data[$row['day']][$row['sector']][$w]= $row ;
	} 	

	
	echo json_encode($data,JSON_FORCE_OBJECT);
	
	
}
//echo $_GET['do'] .$_GET['year'] . $_GET['semester']  . $_GET['do']  . $_GET['class_id'] . $_GET['sect']  ;

?>