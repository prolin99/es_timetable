<?php
//  ------------------------------------------------------------------------ //
// 本模組由 prolin 製作
// 製作日期：2014-07-01
// $Id:$
// ------------------------------------------------------------------------- //

/*-----------引入檔案區--------------*/
include_once "header_admin.php";
 
include_once "header.php";

if ( $_GET['id']  ) {
	//讀取科目
	$subject= get_subject_list() ;	
	
	$y = substr($_GET['id'],0,1) ;
	$sql = " select *  FROM  "  . $xoopsDB->prefix("es_timetable_subject_year") .  " where grade='$y' " ;
		
	$result = $xoopsDB->queryF($sql) or die($sql."<br>". mysql_error()); 	
	$i = 0 ;
	while($row=$xoopsDB->fetchArray($result)){
 		$i++ ;
 		
 		$data[$i]['id'] = $row['subject_id'] ;
 		$data[$i]['name'] = $subject[$row['subject_id']] ;
	} 
	$data[0]['id'] = $i ;
	echo json_encode($data,JSON_FORCE_OBJECT);
}


?>