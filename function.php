<?php
//  ------------------------------------------------------------------------ //
// 本模組由 prolin 製作
// 製作日期：2014-02-16
// $Id:$
// ------------------------------------------------------------------------- //
//引入TadTools的函式庫
if(!file_exists(XOOPS_ROOT_PATH."/modules/tadtools/tad_function.php")){
 redirect_header("http://www.tad0616.net/modules/tad_uploader/index.php?of_cat_sn=50",3, _TAD_NEED_TADTOOLS);
}
include_once XOOPS_ROOT_PATH."/modules/tadtools/tad_function.php";


/********************* 自訂函數 *********************/

$DEF_SET['days']=   $xoopsModuleConfig['es_tt_days'] +1 ;
$DEF_SET['sects']=   $xoopsModuleConfig['es_tt_sects'] +1 ;
$DEF_SET['m_sects']=   $xoopsModuleConfig['es_tt_m_sects'] +1  ;
$DEF_SET['input']=   $xoopsModuleConfig['es_tt_class_input']  ;
$DEF_SET['grade'] = preg_split('/[,]/' ,$xoopsModuleConfig['es_tt_grade']) ;
$DEF_SET['teacher_group'] = $xoopsModuleConfig['es_tt_teacher_group'] ;
 
//


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

 
function get_subject_list() {
	//取得目前的科目名稱
	global  $xoopsDB ;
	$sql =  "  SELECT  subject_id , subject_name   FROM " . $xoopsDB->prefix("es_timetable_subject") . " order by subject_id  " ;
	$result = $xoopsDB->query($sql) or die($sql."<br>". mysql_error()); 
	while($row=$xoopsDB->fetchArray($result)){
		$data[$row['subject_id']] = $row['subject_name'] ;
	}	
	return $data ;	
	
}	

function get_subject_grade_list() {
	//取得目前的各年級使用科目
	global  $xoopsDB ;
	$sql =  "  SELECT  subject_id , grade   FROM " . $xoopsDB->prefix("es_timetable_subject_year") . " order by grade  " ;
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

 
	$sql =  "  SELECT  *  FROM " . $xoopsDB->prefix("es_timetable_teacher") ." order by hide "   ;
	$result = $xoopsDB->query($sql) or die($sql."<br>". mysql_error()); 
	while($row=$xoopsDB->fetchArray($result)){
 		$table_teacher[$row['teacher_id']] = $row  ;
		
	}	
	return $table_teacher ;	
 
}

function get_table_teacher_list() {
	//取得目前教師
	global  $xoopsDB ;
	//由學校資料表中取得

 
	$sql =  "  SELECT  teacher_id , user_id , name   FROM " . $xoopsDB->prefix("es_timetable_teacher")  ." where  hide='0'  "   ;
	$result = $xoopsDB->query($sql) or die($sql."<br>". mysql_error()); 
	while($row=$xoopsDB->fetchArray($result)){
 		$table_teacher[$row['teacher_id']] = $row['name'] ;
		
	}	
	return $table_teacher ;	
 
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

function get_class_students( $class_id , $mode='class') {
	//取得該班的學生姓名資料   $mode =class ,grade (全學年) , all (全校)
	global  $xoopsDB ;
	if ($mode =='all')  
		$sql =  "  SELECT  *  FROM " . $xoopsDB->prefix("e_student") . "     ORDER BY class_id,  `class_sit_num`  " ;
	elseif ( $mode=='grade') {
		$grade = substr($class_id,0,1) ;
		$sql =  "  SELECT  *  FROM " . $xoopsDB->prefix("e_student") . "  where class_id like '$grade%'   ORDER BY class_id,  `class_sit_num`  " ;
	}else 
		$sql =  "  SELECT  *  FROM " . $xoopsDB->prefix("e_student") . "   where class_id='$class_id'   ORDER BY  `class_sit_num`  " ;
 
		$result = $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'],3, mysql_error());
		while($stud=$xoopsDB->fetchArray($result)){
 
			$data[$stud['tn_id'] ]=$stud ;
	
		}		
	return $data ;		
	//echo $sql ;
	
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


//=====================================================
function get_sign_kind($id =0 , $mode='list' ,$class_id=0  ) {
	//取得填報項目 ($id 指定單筆資料， list/admin 列出全部(填報)/擁有者管理    $class_id 班級判斷可否填報  
	//預設只出現最近可以填報的項目
	global  $xoopsDB , $xoopsUser ;
	
	
	if ($id <>0) {
		//單筆記錄
		$sql =  "  SELECT  *  ,( end_date >=  (NOW() - INTERVAL 1 DAY ) ) as cando  , (datediff(`end_date`,now()) +1)  as d_days FROM " . 
				$xoopsDB->prefix("sign_kind") .  "  where id = '$id'  "  ;
	}else 	{
 
		if (in_array(1,$xoopsUser->groups())  or  ($mode=='list' )  )		{ //系統管理員，列出全部
			$sql =  "  SELECT  *  ,( end_date >=  (NOW() - INTERVAL 1 DAY ) ) as cando , (datediff(`end_date`,now()) +1)  as d_days   FROM " . 
					$xoopsDB->prefix("sign_kind") .  " order by   id DESC  LIMIT 0 , 15 "  ;
		}else {	
			//列出全部記錄
			$sql =  "  SELECT  *  ,( end_date >=  (NOW() - INTERVAL 1 DAY ) ) as cando , (datediff(`end_date`,now()) +1)  as d_days   FROM " . 
				$xoopsDB->prefix("sign_kind") .  " where admin ='". $xoopsUser->uid()  ."'   order by   id  DESC  LIMIT 0 , 15 "  ;
		 }
	}	
 
 	//echo $sql  ;
	$result = $xoopsDB->query($sql) or die($sql."<br>". mysql_error()); 
	
	while($row=$xoopsDB->fetchArray($result)){
			//把輸入欄位轉換陣列  D1__dd__d__1__##   
			//以 ## 分欄數， 代號__中文欄名__格式__欄寬
			$i =0 ;
			$fi= preg_split("/##/" ,$row['input_data_item']) ;
			foreach ($fi as $k => $v) {
				if (trim($v)<>'')  { 
					$i ++ ;
					$fi_list[$i]= preg_split("/__/" ,$v) ;
					
					//如果欄寬未設定
					if ($fi_list[$i][3]==0)
						$fi_list[$i][3]=1 ;
				}	
			}	
			$row['field_input'] = $fi_list ;
 
			//把擷取欄位轉換陣列  birthday,class_sit_num, 以逗號作分隔的資料欄名
			$field_get_array= preg_split("/,/" ,$row['get_data_item']) ;
			foreach ($field_get_array  as $k =>$v) {
				if (trim($v))
					$field_get_set[$v]=$v  ;
			}		
			$row['field_get']= $field_get_set ;
			
			//年級限轉成陣列
			$grade_array = preg_split("/,/" ,$row['input_classY']) ;
			foreach ($grade_array  as $k =>$v) {
				if (trim($v))
					$grade_set[$v]=$v  ;
			}		
			$row['grade'] = $grade_set ;
 
			//有班級查看，
			if ($class_id) {
				//是否能填報
				$row['need']= in_array(substr($class_id,0,1) , $grade_set ) ;
				//是否已填報過了
				$row['inputed']=check_class_input( $row['id'] ,$class_id) ;
 
			}	else {
				$row['need']= 1;
			}	
 
			$row['uid']= $row['admin']  ;
			//取得管理員姓名
		    	$uid_name=XoopsUser::getUnameFromId($row['admin'],1);
    			if(empty($uid_name))$uid_name=XoopsUser::getUnameFromId($row['admin'],0);
    			$row['admin'] = $uid_name ;
    			
 			$data[$row['id']] = $row ;
 			
	}	
	return $data  ;	
}	


//檢查班級是否已填報過了 
function check_class_input($kid , $class_id)  {
	global  $xoopsDB ;
	$sql =  "  SELECT  count(*) as cc   FROM " . $xoopsDB->prefix("sign_data") .  " where  kind= '$kid' and  class_id='$class_id'   "  ;	
 
	$result = $xoopsDB->query($sql) or die($sql."<br>". mysql_error()); 
	while($row=$xoopsDB->fetchArray($result)){
		$cc = $row['cc'] ;
	}	
 
	return $cc  ;	
}	

function get_sign_kind_item( ) {
	//取得填報項目只有代號、名稱
 
	global  $xoopsDB ;
	$sql =  "  SELECT  id, title   FROM " . $xoopsDB->prefix("sign_kind") .  " order by   id  DESC   "  ;
 
	$result = $xoopsDB->query($sql) or die($sql."<br>". mysql_error()); 
	while($row=$xoopsDB->fetchArray($result)){
		
 			$data[$row['id']] = $row['title'] ;
	}	
	return $data  ;	
}	


function sum_sign_data() {
	//統計已填報班數
	global  $xoopsDB ;
	$sql = "select  kind, count( class_id ) AS cc FROM   ". 
			" (  SELECT  kind , class_id   FROM " . $xoopsDB->prefix("sign_data") .  " group by kind ,class_id  )ss  GROUP BY kind " ;
			//echo $sql ;
	$result = $xoopsDB->query($sql) or die($sql."<br>". mysql_error()); 
	while($row=$xoopsDB->fetchArray($result)){
		
 			$data[$row['kind']] = $row['cc'] ;
 			//echo $row['kind']  .'='.  $row['cc'] ;
	}	
	return $data  ;		
	
}	

function get_all_sign_list($id) {

	//統計應填報班級
	global  $xoopsDB ;
	//應該班級
	$sql = "SELECT input_classY  FROM " . $xoopsDB->prefix("sign_kind") .  " where id= '$id'    "  ;
	$result = $xoopsDB->query($sql) or die($sql."<br>". mysql_error()); 
	$row=$xoopsDB->fetchArray($result);
	$class_Y =preg_split("/,/" ,$row['input_classY'])  ;
	
	//全部班級
	$all_class = get_class_list() ;

	//有填報的班級人數統計
	$sql = "   SELECT  class_id ,count(*) as cc   FROM " . $xoopsDB->prefix("sign_data") .  " where   kind ='$id' group by class_id   " ;
	$result = $xoopsDB->query($sql) or die($sql."<br>". mysql_error()); 
	while($row=$xoopsDB->fetchArray($result)){
 			$sign_class[$row['class_id']] = $row['cc'] ;
	}	
	
	//有填報的班級，但為 -99  即無人要報名
	$sql = "   SELECT  class_id ,count(*) as cc   FROM " . $xoopsDB->prefix("sign_data") .  " where   kind ='$id' and order_pos='-99'  group by class_id   " ;
	$result = $xoopsDB->query($sql) or die($sql."<br>". mysql_error()); 
	while($row=$xoopsDB->fetchArray($result)){
 			$sign_class_no[$row['class_id']] = $row['cc'] ;
 
	}		
 
	//選擇只列出需填報班級
	foreach ($all_class  as $k =>$v ) {
 
		if  ( in_array(substr($k,0,1), $class_Y) ) {
			if  ( $sign_class[$k] == 1 and  $sign_class_no[$k] ) 
				$data[$k]= '無人報名'  ;
			else 	
				$data[$k]= $sign_class[$k]  ;
 
		}	
 
	}
	return $data  ;		
 
}	



function get_sign_data($kind_id, $class_id) {
	//取得該項的填報學生，
	//$class_id =all 表示全部，
	//order_pos = -99 代表已填報無學生參加，但不影響
	global  $xoopsDB ;
	if ( $class_id <>'all')
	   $class_sql = "   and  class_id ='$class_id'   " ;
	$sql =  "  SELECT  *    FROM " . $xoopsDB->prefix("sign_data") .  " where kind ='$kind_id'  and order_pos <> -99   $class_sql   order by   class_id,  order_pos    "  ;
 
	$result = $xoopsDB->query($sql) or die($sql."<br>". mysql_error()); 
	while($row=$xoopsDB->fetchArray($result)){
			//輸入值  D1__dd__d__1__##   
			//以 ## 分欄數， 代號__中文欄名__格式__欄寬
			$fi= preg_split("/##/" ,$row['data_input']) ;
 
			foreach ($fi as $k => $v) {
				if (trim($v)<>'')  {
					list($fn,$fv)= preg_split("/__/" ,$v) ;
					
					$row['in_'.$fn] = $fv ;
				}	
			}		
			//擷取欄   birthday,class_sit_num, 以逗號作分隔的資料欄名
			$show_data='' ;
			$get_stud_data='' ;
			
 			$fi= preg_split("/,/" ,$row['data_get']) ;
 
			foreach ($fi as $k => $v) {
				if (trim($v)<>'')  {
					list($fn,$fv)= preg_split("/:/" ,$v) ;
					$get_stud_data[$fn]=$fv ; 
					$show_data .= '<span class="label">'.$fv . '</span>';
 				}	
			}	
			$row['get_hide']="<span class='label del'><i class='icon-remove' title='刪除'></i></span>$show_data<input type='hidden' name='get_data[". $row['order_pos'] ."]'  id='get_data_".  $row['order_pos']  ."' value='" . $row['data_get'] ."'  > " ;
			$row['get_field_2'] = $get_stud_data ;
 			//$data[$row['class_id']][$row['order_pos']] = $row ;
 			$data[$row['class_id']][$row['order_pos']] = $row ;
	}	
	return $data  ;		
	
}	

function delete_sign_kind($kind_id) {
	//刪除整份填報

	global  $xoopsDB ;
	$sql =  "  DELETE    FROM " . $xoopsDB->prefix("sign_data") .  " where kind ='$kind_id'   "  ;
 	$result = $xoopsDB->queryF($sql) or die($sql."<br>". mysql_error()); 
 	
 	
	$sql =  "  DELETE    FROM " . $xoopsDB->prefix("sign_kind") .  " where id ='$kind_id'   "  ;
 	$result = $xoopsDB->queryF($sql) or die($sql."<br>". mysql_error()); 
 
}	

function clear_sign_kind($kind_id) {
	//清空填報資料

	global  $xoopsDB ;
	$sql =  "  DELETE    FROM " . $xoopsDB->prefix("sign_data") .  " where kind ='$kind_id'   "  ;
 	$result = $xoopsDB->queryF($sql) or die($sql."<br>". mysql_error()); 
 	
 
}	


function get_student_data_array($class_id , $class_sit_id  ,$tid  , $fi ) {
	//由班級、座號、所在格、匯出欄
		global  $xoopsDB ;
		$sql =  "  SELECT  *  FROM " . $xoopsDB->prefix("e_student") . "   where class_id = '$class_id'  and class_sit_num='$class_sit_id'   " ;
 
		$result = $xoopsDB->query($sql) or die($sql."<br>". mysql_error()); 
		while($row=$xoopsDB->fetchArray($result)){
			 $data= $row ;
		}
 	
		if ($data) {
			$show_data = '<span class="label del"><i class="icon-remove" title="刪除"></i>'.$data['name']. '</span>';
			$hide_data =  'name:' .$data['name'] .','; 
			$field_get= preg_split("/,/" ,$fi ) ;
			$json_array['name'] = $data['name'] ;
			foreach ($field_get as $k =>$v ) {
				//有欄位資料
				if ($data[$v]) {
					$show_data .= '<span class="label">'.$data[$v] . '</span>';
					$hide_data .=  "$v:" .$data[$v] .','; 
 
				}
			}
			list($tid_n,$tid)=  preg_split("/_/" ,$tid ) ;
			
			//最後要呈現的 html 
			$json_array['hide_data'] =$hide_data ;
			$json_array['html']  =  "$show_data<input type='hidden' name='get_data[$tid]'  id='get_data_$tid' value='$hide_data'  > " ;
			return $json_array ;
		}	
			
}