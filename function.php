<?php
//  ------------------------------------------------------------------------ //
// 本模組由 prolin 製作
// 製作日期：2014-07-20
// $Id:$
// ------------------------------------------------------------------------- //
//引入TadTools的函式庫
if(!file_exists(XOOPS_ROOT_PATH."/modules/tadtools/tad_function.php")){
 redirect_header("http://www.tad0616.net/modules/tad_uploader/index.php?of_cat_sn=50",3, _TAD_NEED_TADTOOLS);
}
include_once XOOPS_ROOT_PATH."/modules/tadtools/tad_function.php";

if(!file_exists(XOOPS_ROOT_PATH."/modules/e_stud_import/es_comm_function.php")){
 redirect_header("http://campus-xoops.tn.edu.tw/modules/tad_modules/index.php?module_sn=33",3, '需要單位名稱模組(e_stud_import)1.9以上');
}
include_once XOOPS_ROOT_PATH."/modules/e_stud_import/es_comm_function.php";
/********************* 自訂函數 *********************/

$DEF_SET['days']=   $xoopsModuleConfig['es_tt_days']  ;
$DEF_SET['days_sm']=   $xoopsModuleConfig['es_tt_days'] +1 ;
$DEF_SET['sects']=   $xoopsModuleConfig['es_tt_sects']  ;
$DEF_SET['sects_sm']=   $xoopsModuleConfig['es_tt_sects'] +1 ;
$DEF_SET['m_sects']=   $xoopsModuleConfig['es_tt_m_sects'] ;
$DEF_SET['input']=   $xoopsModuleConfig['es_tt_class_input']  ;
$DEF_SET['es_tt_local']=   $xoopsModuleConfig['es_tt_local']  ;
$DEF_SET['grade'] = preg_split('/[,]/' ,$xoopsModuleConfig['es_tt_grade']) ;
$DEF_SET['teacher_group'] = $xoopsModuleConfig['es_tt_teacher_group'] ;

//$DEF_SET['es_tt_begin']=   $xoopsModuleConfig['es_tt_begin']  ;

//中文節次
$DEF_SET['sects_cht'] = preg_split('/[,]/' ,$xoopsModuleConfig['es_tt_m_sects_cht']) ;

$i = 1 ;
foreach ( $DEF_SET['sects_cht']  as $oi => $sect_name ) {
	$DEF_SET['sects_cht_list'][$i]  = $sect_name ;
	$i ++ ;
}

//time
$time_list= preg_split('/[,]/' ,$xoopsModuleConfig['es_tt_m_sects_time']) ;
$i = 1 ;
foreach ( $time_list  as $oi => $sect_name ) {
	$DEF_SET['time_list'][$i]  = $sect_name ;
	$i ++ ;
}

//特殊班 9901 
$DEF_SET['spe_class'] = preg_split('/[,]/' ,$xoopsModuleConfig['es_tt_spe_class']) ;
$i=9901 ;
foreach ( $DEF_SET['spe_class']  as $oi => $spe_class_name ) {
	$DEF_SET['spe_class_list'][$i]  =$spe_class_name  ;
	$i++ ;
}

$DEF_SET['week'] = array('' ,'週一' ,'週二','週三','週四','週五','週六','週日' );

//課表中要用到特殊班
function get_timetable_class_list_c($mode='short') {
	global $DEF_SET ;
	$class_list = es_class_name_list_c($mode) ;


	foreach ($DEF_SET['spe_class_list'] as $class_id => $class_name) 
		$class_list[$class_id] = $class_name ;
 
	return $class_list ;
}


function get_timetable_info() {
	//取得課表內容--最近的年度、期別
	global  $xoopsDB ;
	$sql =  "  SELECT  school_year , semester  FROM " . $xoopsDB->prefix("es_timetable") . "  order by school_year DESC , semester DESC  " ;
	$result = $xoopsDB->query($sql) or die($sql."<br>". mysql_error()); 
	$row=$xoopsDB->fetchArray($result) ;
	$data['year']=$row['school_year'] ;
	$data['semester']=$row['semester'] ;
	
	$sql =  "  SELECT  count(*) as cc   FROM " . $xoopsDB->prefix("es_timetable") . " where school_year='{$data['school_year']}' and semester ='{$data['semester']}'  group by  class_id  " ;
	$result = $xoopsDB->query($sql) or die($sql."<br>". mysql_error()); 
	$row=$xoopsDB->fetchArray($result) ;
	$data['class'] =  $row['cc'] ;	
	
	return $data ;
}	

//取出課表內容
function get_timetable_data($mode, $y ,$s , $class_sel='all' ) {
	global  $xoopsDB ;
 
	if ($mode == 'teacher' )  
		$sql = " select *  FROM  "  . $xoopsDB->prefix("es_timetable")  .  " where school_year= '$y'  and  semester= '$s'     order by teacher,day,sector " ;
 	


 	if ($mode =='class_id' )  {
		if  ($class_sel =='all') 
			$sql = " select *  FROM  "  . $xoopsDB->prefix("es_timetable") .  " where school_year= '$y'  and  semester= '$s'     order by class_id,day,sector   " ; 
		else 
			$sql = " select *  FROM  "  . $xoopsDB->prefix("es_timetable") .  " where school_year= '$y'  and  semester= '$s'   and class_id='$class_sel'    order by class_id,day,sector   " ; 
	}	
 	
	if ($mode =='room' )  
		$sql = " select *  FROM  "  . $xoopsDB->prefix("es_timetable") .  " where school_year= '$y'  and  semester= '$s'   and room <>''   order by room,day,sector   " ; 
 
 	$result = $xoopsDB->query($sql) or die($sql."<br>". mysql_error()); 
	while($row=$xoopsDB->fetchArray($result)){
		$key =$row[$mode] ;
		if ($old_key =='') $old_key =	$key ;
		if ($old_key <>	$key) {	
			$data[$old_key] = $tab  ;
			$old_key =	$key ;
			unset($tab) ;
		}		
		$d= $row['day'] ;
		$s= $row['sector'] ;
		$cell['class_id']= $row['class_id'] ;
		$cell['teacher']= $row['teacher'] ;
		$cell['ss_id']= $row['ss_id'] ;
		$cell['room']= $row['room'] ;
		$tab[$d][$s] = $cell ;
	
	}	
	$data[$old_key] = $tab  ;
 
	return $data ;			
}	

//取出課表  room 
function get_class_room_list( $y ,$s ) {
	global  $xoopsDB ;

	$data[0] = '選擇查看教室' ;
 
	$sql = " select  room   FROM  "  . $xoopsDB->prefix("es_timetable")  .  " where school_year= '$y'  and  semester= '$s'     and room <>''   group by  room " ;
 
 	$result = $xoopsDB->query($sql) or die($sql."<br>". mysql_error()); 
	while($row=$xoopsDB->fetchArray($result)){
		$data[] = $row['room'] ;
	}	
 
	return $data ;			
}	


function get_subject_list() {
	//取得科目名稱
	global  $xoopsDB ;
	$sql =  "  SELECT  subject_id , subject_name   FROM " . $xoopsDB->prefix("es_timetable_subject") . " order by subject_id  " ;
	$result = $xoopsDB->query($sql) or die($sql."<br>". mysql_error()); 
	while($row=$xoopsDB->fetchArray($result)){
		$data[$row['subject_id']] = $row['subject_name'] ;
	}	
	return $data ;	
	
}	

function get_subject_data_list() {
	//取得科目資料庫中多欄位
	global  $xoopsDB ;
	$sql =  "  SELECT  subject_id , subject_name ,subject_scope   FROM " . $xoopsDB->prefix("es_timetable_subject") . " order by subject_id  " ;
	$result = $xoopsDB->query($sql) or die($sql."<br>". mysql_error()); 
	while($row=$xoopsDB->fetchArray($result)){
		$data[$row['subject_id']]['subject'] = $row['subject_name'] ;
		$data[$row['subject_id']]['scope'] = $row['subject_scope'] ;
	}	
	return $data ;	
	
}	

function get_subject_grade_list() {
	//取得目前的各年級使用科目
	global  $xoopsDB ;
	$sql =  "  SELECT  subject_id , grade   FROM " . $xoopsDB->prefix("es_timetable_subject_year") . " order by grade ,subject_id " ;
	$result = $xoopsDB->query($sql) or die($sql."<br>". mysql_error()); 
	while($row=$xoopsDB->fetchArray($result)){
		$data[$row['grade']][$row['subject_id']] = $row['subject_id'] ;
	}	
	return $data ;	
	
}	


function sync_teacher($teacher_group=4) {
	//同步
	$school_teacher= get_teacher_list($teacher_group ,0 ) ;
	foreach ($school_teacher as $uid =>$user ) {
		$teach_list[$uid]= $user['name'] ;
		//echo $uid .$user['name']  ;
		join_table_teacher($uid ,$user['name']) ;
	}	
}


function join_table_teacher($uid ,$user) {
	//加入教師
	global  $xoopsDB ;
	$sql =  "  SELECT  teacher_id , user_id , name   FROM " . $xoopsDB->prefix("es_timetable_teacher")   . " where  user_id='$uid' or name ='$user' "  ;

	$result = $xoopsDB->query($sql) or die($sql."<br>". mysql_error()); 
	$row=$xoopsDB->fetchArray($result) ;
	if ( (!$row['user_id']) and ($row['name']=='') ){
		$sql = " INSERT INTO   "  . $xoopsDB->prefix("es_timetable_teacher") .  
				" (`user_id`, `name`)  " .
				"  VALUES  ( '$uid' , '$user' )   " ; 
	}elseif (!$row['user_id'])   {
		$teacher_id = $row['teacher_id'] ;
		$sql = " UPDATE "  . $xoopsDB->prefix("es_timetable_teacher") .  " SET  user_id='$uid' where  teacher_id='$teacher_id' " ; 
	}	
	$result = $xoopsDB->queryF($sql) or die($sql."<br>". mysql_error()); 
	
}

function get_table_teacher_data() {
	//取得目前教師
	global  $xoopsDB ;
	//由學校資料表中取得

 
	$sql =  "  SELECT  *  FROM " . $xoopsDB->prefix("es_timetable_teacher") ." order by hide , name  "   ;
	$result = $xoopsDB->query($sql) or die($sql."<br>". mysql_error()); 
	while($row=$xoopsDB->fetchArray($result)){
 		$table_teacher[$row['teacher_id']] = $row  ;
		
	}	
	return $table_teacher ;	
 
}

function get_table_teacher_list($mode='hide') {
	//取得目前教師
	global  $xoopsDB ;
	//由學校資料表中取得

 	if  ($mode == 'all') 
 		//全部
 		$sql =  "  SELECT  teacher_id , user_id , name   FROM " . $xoopsDB->prefix("es_timetable_teacher")  ."     order by name "   ;
 	else 
 		//不出現隱去者
		$sql =  "  SELECT  teacher_id , user_id , name   FROM " . $xoopsDB->prefix("es_timetable_teacher")  ." where  hide='0'  order by name  "   ;
	$result = $xoopsDB->query($sql) or die($sql."<br>". mysql_error()); 
	while($row=$xoopsDB->fetchArray($result)){
 		$table_teacher[$row['teacher_id']] = $row['name'] ;
		
	}	
	return $table_teacher ;	
 
}	

function get_my_id_in_timetable($uid =0   ) {
	//取得$uid 在課表中 teacher_id
	global  $xoopsDB ,$xoopsUser  ;
	if (!$uid)  
		$uid = $xoopsUser->uid() ;
	$sql =  "  SELECT  teacher_id  FROM " . $xoopsDB->prefix("es_timetable_teacher") . 
	               " where user_id= '$uid'   " ;
 
	$result = $xoopsDB->query($sql) or die($sql."<br>". mysql_error()); 
	while($data_row=$xoopsDB->fetchArray($result)){
 			$teacher_id = $data_row['teacher_id'] ;
	}	
	return $teacher_id  ;
}



//取得教師名冊， 群組代碼， 顯示模式(0:只取資料， 1:轉換EMAIL、職稱)
function get_teacher_list($teach_group_id ,$show=0){

	global  $xoopsDB   ;
/*
SELECT u.uid, u.name, u.user_occ, g.groupid ,c.class_id
FROM `xx_groups_users_link` AS g 
LEFT JOIN `xx_users` AS u ON u.uid = g.uid
left join  xx_e_classteacher as c on u.uid = c.uid 
WHERE g.groupid =4 
group by u.uid
order by  u.user_occ ,c.class_id  	
*/
 	$sql =  "  SELECT  u.uid, u.name , u.email ,u.user_viewemail , u.url , c.staff , g.groupid ,c.class_id   FROM  " .
 			$xoopsDB->prefix("groups_users_link") .  "  AS g LEFT JOIN  " .  $xoopsDB->prefix("users") .  "  AS u ON u.uid = g.uid " .
 			" left join " . $xoopsDB->prefix("e_classteacher") ." as c on u.uid = c.uid "  .
 	        "  WHERE g.groupid ='$teach_group_id'  group by u.uid   order by  c.staff , c.class_id , u.name " ;

 	$result = $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'],3, mysql_error());
	while($row=$xoopsDB->fetchArray($result)){
		if ($show) {
			//email
			if ($row['email'] and $row['user_viewemail']) {		//EMAIL 顯示做保護
				$row['email_show']= email_protect($row['email']) ;
			}	
			//班級
 			//$job_arr = preg_split('/[-]/' ,$row['user_occ']) ;
 			$job_arr = preg_split('/[-]/' ,$row['staff']) ;
 			$row['staff'] = $job_arr[1] ;
 			if ($row['class_id'])
 				$row['staff'] .= '-' .$row['class_id'] .'班' ;

		}
 	 	$teacher[$row['uid']]= $row ;
	}	
	return $teacher ;
}	


function check_timetable_double($y , $s) {
	//檢查是否有重複
	global  $xoopsDB ;
	
	//取得中文班名
	$class_list_c = es_class_name_list_c()  ;

	$sql =  "   SELECT * , count(*) as cc FROM  " . $xoopsDB->prefix("es_timetable") . "  where school_year= '$y'  and  semester= '$s'   group by  class_id , day ,  sector  HAVING cc>1    " ;
	$result = $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'],3, mysql_error());
	while($row= $xoopsDB->fetchArray($result)){
		$data .= $class_list_c[$row['class_id']]  .$row['day'] .'-' .$row['sector'] .'  <br />' ;
 	}

 	$teacher_list = get_table_teacher_data() ;
 	
	$sql =  "   SELECT * , count(*) as cc FROM  " . $xoopsDB->prefix("es_timetable") . "  where school_year= '$y'  and  semester= '$s'   group by  teacher , day ,  sector  HAVING cc>1    " ;
	$result = $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'],3, mysql_error());
	while($row= $xoopsDB->fetchArray($result)){
		$data .= $teacher_list[$row['teacher']]['name']   .' 教師--' .$row['day'] .'-' .$row['sector'] .'節 --  ' ;
		//取得該教師那一節課
		$sql2 =  "   SELECT class_id  FROM  " . $xoopsDB->prefix("es_timetable") . 
			"  where school_year= '$y'  and  semester= '$s'    and  teacher='{$row['teacher']}' and `day`='{$row['day']}'  and  sector='{$row['sector']}'       " ;

		$result2 = $xoopsDB->query($sql2) or die( mysql_error());
		while($row2= $xoopsDB->fetchArray($result2)){
			$data .= $class_list_c[$row2['class_id']]  . '  , ' ;
		}
 
		$data .= "<br />" ;
 	} 	

	$sql =  "   SELECT * , count(*) as cc FROM  " . $xoopsDB->prefix("es_timetable") . "  where school_year= '$y'  and  semester= '$s'   and room <> '' group by  room , day ,  sector  HAVING cc>1    " ;
	//echo $sql ;
	$result = $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'],3, mysql_error());
	while($row= $xoopsDB->fetchArray($result)){
		$data .= $row['room'] .' 教室--' .$row['day'] .'-' . $row['sector'] .'節 --  ' ;
		$sql2 =  "   SELECT class_id , teacher  FROM  " . $xoopsDB->prefix("es_timetable") . 
			"  where school_year= '$y'  and  semester= '$s'    and  room='{$row['room']}' and `day`='{$row['day']}'  and  sector='{$row['sector']}'       " ;

		$result2 = $xoopsDB->query($sql2) or die( mysql_error());
		while($row2= $xoopsDB->fetchArray($result2)){
			$data .= $class_list_c[$row2['class_id']]  . ' (' . $teacher_list[$row2['teacher']]['name'] .  ') , ' ;
		}
 
		$data .= "<br />" ;		

 	} 
 
 	return $data ;
}

function get_ones_timetable( $mode , $y ,$s  ,$id){
	global  $xoopsDB ;
	$subject= get_subject_list() ;

	$room_list = get_class_room_list( $y , $s ) ;
	//讀取人名
	$teacher_list = get_table_teacher_data() ;
	for ($d=1 ; $d<=$DEF_SET['days'] ;$d++) 
		for ($s=1 ; $s<=$DEF_SET['sects'] ;$s++) 
			$data[$d][$s]['ss_id']= 0 ;

	if ($mode =='teacher' ) 
		$sql = " select *  FROM  "  . $xoopsDB->prefix("es_timetable") .  " where school_year= '$y'  and  semester= '$s'    and  teacher= '$id'   order by day,sector   " ;
	elseif ($mode =='room') 
		$sql = " select *  FROM  "  . $xoopsDB->prefix("es_timetable") .  " where school_year= '$y'  and  semester= '$s'    and  room= '$id'  order by day,sector   " ;
	else 	
		$sql = " select *  FROM  "  . $xoopsDB->prefix("es_timetable") .  " where school_year= '$y'  and  semester= '$s'    and  class_id= '$id'  order by day,sector   " ;
 	//echo $sql ;
 	$result = $xoopsDB->queryF($sql) or die($sql."<br>". mysql_error());
	while($row=$xoopsDB->fetchArray($result)){
		$row['subject_name']= $subject[$row['ss_id']] ;
		$row['teacher_name']= $teacher_list[$row['teacher']]['name'] ;
		$row['room_id']= array_search( $row['room']  ,$room_list ) ;
		$data[$row['day']][$row['sector']]= $row ;
	}
	return $data ;
}	
//=================================================================================================
function get_class_list(  ) {
	//取得全校班級列表 
	global  $xoopsDB ;
 
		$sql =  "  SELECT  class_id  FROM " . $xoopsDB->prefix("e_student") . "   group by class_id   " ;
 
		$result = $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'],3, mysql_error());
		while($row=$xoopsDB->fetchArray($result)){
 
			$data[$row['class_id']]=$row['class_id'] ;
	
		}		
	return $data ;		
	
}

function get_class_grade_list(  ) {
	//取得全校年級列表 
	global  $xoopsDB ;
 		$sql =  "  SELECT  SUBSTR( `class_id` , 1, 1 ) AS grade   FROM " . $xoopsDB->prefix("e_student") . "   group by  SUBSTR( `class_id` , 1, 1 )   " ;
 		$result = $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'],3, mysql_error());
		while($row=$xoopsDB->fetchArray($result)){
 			$data[$row['grade']]=$row['grade'] ;
	
		}		
	return $data ;		
	
}

function get_class_num(  ) {
	//取得全校班級總數
	global  $xoopsDB ;
 
		$sql =  "  SELECT  count(class_id) as cc   FROM " . $xoopsDB->prefix("e_student") . "   group by class_id   " ;
 
		$result = $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'],3, mysql_error());
		while($row=$xoopsDB->fetchArray($result)){
 
			$data=$row['cc'] ;
	
		}		
	return $data ;		
	
}





Function get_class_teacher_list() {
	//取得全部級任名冊
	global  $xoopsDB ;
	$sql =  "  SELECT  t.uid, t.class_id , u.name  FROM " . $xoopsDB->prefix("e_classteacher") .'  t  , ' .   $xoopsDB->prefix("users")  .'  u    ' .  
	               " where t.uid= u.uid    " ;
 
	$result = $xoopsDB->query($sql) or die($sql."<br>". mysql_error()); 
	while($data_row=$xoopsDB->fetchArray($result)){
 			$class_id[$data_row['class_id']] = $data_row['name'] ;
	}	
	return $class_id  ;
}	

function get_my_class_id($uid =0   ) {
	//取得$uid 的任教班級
	global  $xoopsDB ,$xoopsUser  ;
	if (!$uid)  
		$uid = $xoopsUser->uid() ;
	$sql =  "  SELECT  class_id  FROM " . $xoopsDB->prefix("e_classteacher") . 
	               " where uid= '$uid'   " ;
 
	$result = $xoopsDB->query($sql) or die($sql."<br>". mysql_error()); 
	while($data_row=$xoopsDB->fetchArray($result)){
 			$class_id = $data_row['class_id'] ;
	}	
	return $class_id  ;
}

