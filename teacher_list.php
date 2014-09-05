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

/*-----------function區--------------*/
//取得參數


 
/*-----------執行動作判斷區----------*/

 
//------------------------------------------------------------------------------------------------------------------------------------------------------------------
//檢查目前的課表
$data['info'] = get_timetable_info() ;
 
if ( ($_GET['year'] > $data['info']['year'] ) or  (($_GET['year'] == $data['info']['year'] ) and ($_GET['semester'] >= $data['info']['semester'] ))  ){
	$n_year = $_GET['year'] ;
	$n_semester =$_GET['semester'] ;
} else {
	$n_year =$data['info']['year'] ;
	$n_semester = $data['info']['semester'] ;
}	

if (($n_year==0 ) or  ($n_semester==0) ) {
    //取得現在學年
    $n_year = date("Y") -1911 ;
    if  (date("m")< 8) $n_year-=1 ;	
    if ( (date("m")< 8) and (date("m")> 1 ) ) 
    	$n_semester=2 ;	
    else 
    	$n_semester=1 ;	
 
}

$data['n_y']=$n_year ;
$data['n_s']=$n_semester ;
/*
//目前班級數
$data['class_list'] = get_class_list() ;
 
//取得目前科目名
$data['subject_name'] = get_subject_list() ;
*/
//


  //教師名冊
$data['teacher_list'] =get_table_teacher_list() ;
//$data['teacher_list'][0] = '選擇教師' ;
//ksort($data['teacher_list']) ;
 //var_dump($data['teacher_list'] ) ; 

$data['room_list'] = get_class_room_list($n_year , $n_semester ) ;
 if  (intval($_POST['room_id']) ) 
    $data['room_sel'] = intval($_POST['room_id']) ;
else 
    $data['room_sel'] = 0 ;

 if  (intval($_POST['teacher_id']) ) 
    $data['teacher_sel'] = intval($_POST['teacher_id']) ;
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