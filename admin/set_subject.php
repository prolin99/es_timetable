<?php
//  ------------------------------------------------------------------------ //
// 本模組由 prolin 製作
// 製作日期：2014-07-01
// $Id:$
// ------------------------------------------------------------------------- //

/*-----------引入檔案區--------------*/
include_once "header_admin.php";
//樣版
$xoopsOption['template_main'] = "es_timet_ad_set_sub_tpl.html";
include_once "header.php";
 

/*-----------function區--------------*/
//取得參數


 
/*-----------執行動作判斷區----------*/
 
//取得目前科目名
$data['subject_name'] = get_subject_list() ;
 
//各科目使用的年級
$data['grade_subject'] = get_subject_grade_list() ;



/*-----------秀出結果區--------------*/

$xoopsTpl->assign( "data" , $data ) ; 
$xoopsTpl->assign( "DEF_SET" , $DEF_SET ) ; 
 

 
include_once 'footer.php';
?>