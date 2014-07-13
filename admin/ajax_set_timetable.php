<?php
//  ------------------------------------------------------------------------ //
// 本模組由 prolin 製作
// 製作日期：2014-07-01
// $Id:$
// ------------------------------------------------------------------------- //

/*-----------引入檔案區--------------*/
include_once "header_admin.php";
 
include_once "header.php";

if ( $_GET['year'] and $_GET['semester']  and $_GET['do']  and $_GET['class_id'] and $_GET['sect'] ) {
	list( $name,$day,$sect ) =preg_split('/[_]/' ,$_GET['sect'])  ;
	list( $tea0,$tea_id,$tea ) =preg_split('/[_]/' ,$_GET['teacher'])  ;
 	//把舊節除去
		$nsubj = $_GET['setdata'] ;
	if ($_GET['do'] =='del' ) {
		$sql = " DELETE FROM  "  . $xoopsDB->prefix("es_timetable") .  " where school_year= '{$_GET['year']}'  and  semester= '{$_GET['semester']}'   and day='$day' and sector='$sect'  and  class_id= '{$_GET['class_id']}'    " ;
		$result = $xoopsDB->queryF($sql) or die($sql."<br>". mysql_error()); 	
		//echo $_GET['do'] . $sql ;
	}
	
	if ($_GET['do'] =='add' ) {
		list( $name,$day,$sect ) =preg_split('/[_]/' ,$_GET['sect'])  ;
		list( $subj0,$subj_id,$subj ) =preg_split('/[_]/' ,$_GET['subject'])  ;
		
		
		//教師這節要去除
		$sql = " DELETE FROM  "  . $xoopsDB->prefix("es_timetable") .  " where school_year= '{$_GET['year']}'  and  semester= '{$_GET['semester']}'   and day='$day' and sector='$sect'  and  teacher= '$tea_id'    " ;
		$result = $xoopsDB->queryF($sql) or die($sql."<br>". mysql_error());
		//echo $_GET['do'] . $sql ;
 		
		//班級這節要去除
		$sql = " DELETE FROM  "  . $xoopsDB->prefix("es_timetable") .  " where school_year= '{$_GET['year']}'  and  semester= '{$_GET['semester']}'   and day='$day' and sector='$sect'  and  class_id= '{$_GET['class_id']}'   " ;
		$result = $xoopsDB->queryF($sql) or die($sql."<br>". mysql_error());
 		
	
		$sql = " INSERT INTO   "  . $xoopsDB->prefix("es_timetable") .  
				" (`school_year`, `semester`, `class_id`, `teacher`,`day` , sector ,ss_id,room )  " .
				"  VALUES  ( '{$_GET['year']}' , '{$_GET['semester']}','{$_GET['class_id']}' ,'$tea_id' ,'$day','$sect' , '$subj_id' , '{$_GET['room']}' )   " ; 
		$result = $xoopsDB->queryF($sql) or die($sql."<br>". mysql_error()); 				
		//echo $_GET['do'] . $sql ;
		
	}	
	/*
	//統計部份
	//目前教師的節數
	if ($tea_id ){
		$sql = " select count(ss_id) as cc  FROM  "  . $xoopsDB->prefix("es_timetable") .  " where school_year= '{$_GET['year']}'  and  semester= '{$_GET['semester']}' and    teacher= '$tea_id'    " ;
		$result = $xoopsDB->queryF($sql) or die($sql."<br>". mysql_error());
		$row=$xoopsDB->fetchArray($result) ;
		$data= $row['cc'] ;
		echo json_encode($data,JSON_FORCE_OBJECT);
	}	
	*/
}
//echo $_GET['do'] .$_GET['year'] . $_GET['semester']  . $_GET['do']  . $_GET['class_id'] . $_GET['sect']  ;

?>