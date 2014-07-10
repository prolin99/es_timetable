<?php
//  ------------------------------------------------------------------------ //
// 本模組由 prolin 製作
// 製作日期：2014-07-01
// $Id:$
// ------------------------------------------------------------------------- //

/*-----------引入檔案區--------------*/
include_once "header_admin.php";
 
include_once "header.php";

if ( $_GET['id'] and $_GET['setdata']) {
	$id= $_GET['id']  ;
	$new = $_GET['setdata'] ;
	if ($_GET['do']=='hide') 
		$sql = " UPDATE  "  . $xoopsDB->prefix("es_timetable_teacher") .  " SET hide = not hide  where   teacher_id= '$id' " ;
	else	
		$sql = " UPDATE  "  . $xoopsDB->prefix("es_timetable_teacher") .  " SET name = '$new'  where   teacher_id= '$id' " ;
		
	$result = $xoopsDB->queryF($sql) or die($sql."<br>". mysql_error()); 	
	echo $_GET['do'] . $sql ;
}

?>