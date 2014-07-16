<?php
//  ------------------------------------------------------------------------ //
// 本模組由 prolin 製作
// 製作日期：2014-07-20
// $Id:$
// ------------------------------------------------------------------------- //
/*-----------引入檔案區--------------*/

$xoopsOption['template_main'] = "es_timet_class_table_tpl.html";
include_once "header.php";
include_once XOOPS_ROOT_PATH."/header.php";

  
 if (!$xoopsUser) 
  	redirect_header(XOOPS_URL,3, "需要登入，才能使用！");
 
  	
/*-----------function區--------------*/
//取得所在級任班級
$data['my_class_id']=get_my_class_id() ;
if (!$data['my_class_id']) 
  	redirect_header('index.php',3, "非級任，無法設定班級課表！");

//在課表人員的代號  	
$data['my_teacher_id'] = get_my_id_in_timetable() ;
$data['my_name'] = $xoopsUser->name() ;


//檢查目前的課表
$data['info'] = get_timetable_info() ;
 
$data['n_y']= $data['info']['year'] ;
$data['n_s']= $data['info']['semester'];

//目前班級數
$data['class_list'] = get_class_list() ;
 
//取得目前科目名
$all_subject_name = get_subject_list() ;
 
//該學年的科目
$grade_subject = get_subject_grade_list() ;
$y = substr($data['my_class_id'],0,1) ;
foreach ($grade_subject[$y] as $id =>$sid) {
	$subject_name[$id] =$all_subject_name[$id]  ;
}	
$data['subject_name'] = $subject_name ;

  //教師名冊
$data['teacher_list'] =get_table_teacher_list($DEF_SET['teacher_group']) ;

//取得級任名冊
$data['class_teacher'] = get_class_teacher_list() ;

 

 
/*-----------秀出結果區--------------*/
$xoopsTpl->assign( "toolbar" , toolbar_bootstrap($interface_menu)) ;
$xoopsTpl->assign( "data" , $data ) ; 
$xoopsTpl->assign( "DEF_SET" , $DEF_SET ) ; 
 
 
include_once XOOPS_ROOT_PATH.'/footer.php';

?>