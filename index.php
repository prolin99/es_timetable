<?php
//  ------------------------------------------------------------------------ //
// 本模組由 prolin 製作
// 製作日期：2014-07-20
// $Id:$
// ------------------------------------------------------------------------- //
/*-----------引入檔案區--------------*/
include_once "header.php";
$xoopsOption['template_main'] = set_bootstrap("es_timet_index_tpl.html");

include_once XOOPS_ROOT_PATH."/header.php";

/*
 if (!$xoopsUser)
  	redirect_header(XOOPS_URL,3, "需要登入，才能使用！");
 */

/*-----------function區--------------*/
//取得中文班名
//$data['class_list_c'] = es_class_name_list_c('long')  ;
$data['class_list_c'] = get_timetable_class_list_c('long')  ;

//檢查目前的課表
$data['info'] = get_timetable_info() ;

$data['n_y']= $data['info']['year'] ;
$data['n_s']= $data['info']['semester'];

//目前班級數
$data['class_list'] = get_class_list() ;

//取得目前科目名
$data['subject_name'] = get_subject_list() ;

  //教師名冊
$data['teacher_list'] =get_table_teacher_list($DEF_SET['teacher_group']) ;

//取得級任名冊
$data['class_teacher'] = get_class_teacher_list() ;

//有指定班級
if ($_GET['class_id'])
	$data['select_class_id'] = intval($_GET['class_id']) ;

/*-----------秀出結果區--------------*/
$xoopsTpl->assign( "toolbar" , toolbar_bootstrap($interface_menu)) ;
$xoopsTpl->assign( "data" , $data ) ;
$xoopsTpl->assign( "DEF_SET" , $DEF_SET ) ;
$xoopsTpl->assign( "isUser" , $xoopsUser ) ;


include_once XOOPS_ROOT_PATH.'/footer.php';

?>
