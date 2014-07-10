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
 	//把舊節除去
		$nsubj = $_GET['setdata'] ;
	
		$sql = " DELETE FROM  "  . $xoopsDB->prefix("es_timetable") .  "   school_year= '{$_GET['year']}'  and  semester= '{$_GET['semester']}'  and  class_id= '{$_GET['class_id']}'  and day='$day' and sector='$sect' " ;

		$result = $xoopsDB->queryF($sql) or die($sql."<br>". mysql_error()); 	
 
	if ($_GET['do'] =='add' ) {
		list( $name,$day,$sect ) =preg_split('/[_]/' ,$_GET['sect'])  ;
		list( $subj0,$subj_id,$subj ) =preg_split('/[_]/' ,$_GET['subject'])  ;
		list( $tea0,$tea_id,$tea ) =preg_split('/[_]/' ,$_GET['teacher'])  ;
 
 		//加入新節
 		
		$sql = " INSERT INTO   "  . $xoopsDB->prefix("es_timetable") .  
				" (`school_year`, `semester`, `class_id`, `teacher`,`day` , sector ,room )  " .
				"  VALUES  ( '$kmo_o' , '','subject' ,'1' )   " ; 
		$result = $xoopsDB->queryF($sql) or die($sql."<br>". mysql_error()); 				
		
	}	
	//echo $_GET['do'] . $sql ;
}

?>