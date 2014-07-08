<?php
//  ------------------------------------------------------------------------ //
// 本模組由 prolin 製作
// 製作日期：2014-07-01
// $Id:$
// ------------------------------------------------------------------------- //

/*-----------引入檔案區--------------*/
include_once "header_admin.php";
//樣版
$xoopsOption['template_main'] = "es_timet_ad_index_tpl.html";
include_once "header.php";
 
//複製課表到新年學
//if ($_POST['do_key']) {
	

/*-----------function區--------------*/
sync_teacher($DEF_SET['teacher_group']) ;

//檢查目前的課表
$data['info'] = get_timetable_info() ;
 

//目前班級數
$data['school_class_num'] = get_class_num() ;
 


 


/*-----------秀出結果區--------------*/

$xoopsTpl->assign( "data" , $data ) ; 
 

 
include_once 'footer.php';
?>