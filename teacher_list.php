<?php
//  ------------------------------------------------------------------------ //
// 本模組由 prolin 製作
// 製作日期：2014-09-01
// $Id:$
// ------------------------------------------------------------------------- //

/*-----------引入檔案區--------------*/
 
//樣版
$xoopsOption['template_main'] = "es_timetable_show.html";
 
include_once "header.php";
include_once XOOPS_ROOT_PATH."/header.php"; 

 if (!$xoopsUser) 
    redirect_header("index.php",3, "需要登入，才能使用！");

//校內教師群組代號
if (! in_array(   $DEF_SET['teacher_group'] , $xoopsUser->groups() )  ) 
  	redirect_header("index.php",3, "教職員，才能使用！");
  
/*-----------function區--------------*/
//取得參數


 
/*-----------執行動作判斷區----------*/

//取得中文班名
//$data['class_list_c'] = es_class_name_list_c('long')  ;
$data['class_list_c'] = get_timetable_class_list_c('long')  ; 
//------------------------------------------------------------------------------------------------------------------------------------------------------------------
//檢查目前的課表
$data['info'] = get_timetable_info() ;
 

$n_year =$data['info']['year'] ;
$n_semester = $data['info']['semester'] ;

$data['n_y']=$n_year ;
$data['n_s']=$n_semester ;
 
  //教師名冊
$data['teacher_list'] =get_table_teacher_list() ;
 
//教室名稱陣列
$data['room_list'] = get_class_room_list($n_year , $n_semester ) ;

 if  (intval($_GET['room_id']) ) 
    $data['room_sel'] = intval($_GET['room_id']) ;
else 
    $data['room_sel'] = 0 ;

 if  (intval($_GET['teacher_id']) ) 
    $data['teacher_sel'] = intval($_GET['teacher_id']) ;
else 
    $data['teacher_sel'] = key($data['teacher_list']) ;
 

//echo  $data['teacher_sel']   ;
if ($data['room_sel'] >0 ) {
    $room_name = $data['room_list'][$data['room_sel']] ;
    $tab = get_ones_timetable( 'room' , $n_year , $n_semester  ,  $room_name  ) ;

}else 
    $tab = get_ones_timetable( 'teacher' , $n_year , $n_semester  , $data['teacher_sel']  ) ;
//var_dump($tab) ;

$data['error'] = check_timetable_double($data['info']['year'],$data['info']['semester']) ;

/*-----------秀出結果區--------------*/
$xoopsTpl->assign( "toolbar" , toolbar_bootstrap($interface_menu)) ;
$xoopsTpl->assign( "data" , $data ) ; 
$xoopsTpl->assign( "DEF_SET" , $DEF_SET ) ; 
$xoopsTpl->assign( "tab" , $tab ) ; 
 
 
include_once XOOPS_ROOT_PATH.'/footer.php';
?>