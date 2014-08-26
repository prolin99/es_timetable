<?php
//  ------------------------------------------------------------------------ //
// 本模組由 prolin 製作
// 製作日期：2014-07-01
// $Id:$
// ------------------------------------------------------------------------- //

/*-----------引入檔案區--------------*/
include_once "header_admin.php";
 
include_once "header.php";

if ( $_GET['id'] ) {
	$id= intval($_GET['id'] ) ;
	$myts =& MyTextSanitizer::getInstance();
	$new  =$myts->htmlspecialchars($myts->addSlashes ($_GET['setdata']  )  )  ;

	//$new = $_GET['setdata'] ;
	$sql = " UPDATE  "  . $xoopsDB->prefix("es_timetable") .  " SET room = '$new'  where   course_id= '$id' " ;
		
	$result = $xoopsDB->queryF($sql) or die($sql."<br>". mysql_error()); 	
	echo $_GET['do'] . $sql ;
}

?>