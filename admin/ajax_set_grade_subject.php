<?php
//  ------------------------------------------------------------------------ //
// 本模組由 prolin 製作
// 製作日期：2014-07-01
// $Id:$
// ------------------------------------------------------------------------- //

/*-----------引入檔案區--------------*/
include_once "header_admin.php";
 
include_once "header.php";

if ( $_GET['id'] and $_GET['do']) {
	list($chk , $grade,$subject) =preg_split('/[_]/' ,$_GET['id'])  ;
	if ($_GET['do']== 'do_true' ) 
		$sql = " INSERT INTO   "  . $xoopsDB->prefix("es_timetable_subject_year") .  
				" (`grade`, `subject_id`)  " .
				"  VALUES  ( '$grade' , '$subject' )   " ; 
	else 
		$sql = " DELETE FROM  "  . $xoopsDB->prefix("es_timetable_subject_year") .  " where grade='$grade' and subject_id= '$subject' " ;
		
	$result = $xoopsDB->queryF($sql) or die($sql."<br>". mysql_error()); 	
	//echo $_GET['do'] . $sql ;
}


?>