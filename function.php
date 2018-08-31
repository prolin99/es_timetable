<?php
//  ------------------------------------------------------------------------ //
// 本模組由 prolin 製作
// 製作日期：2014-07-20
// $Id:$
// ------------------------------------------------------------------------- //
//引入TadTools的函式庫
if (!file_exists(XOOPS_ROOT_PATH.'/modules/tadtools/tad_function.php')) {
    redirect_header('http://www.tad0616.net/modules/tad_uploader/index.php?of_cat_sn=50', 3, _TAD_NEED_TADTOOLS);
}
include_once XOOPS_ROOT_PATH.'/modules/tadtools/tad_function.php';

if (!file_exists(XOOPS_ROOT_PATH.'/modules/e_stud_import/es_comm_function.php')) {
    redirect_header('http://campus-xoops.tn.edu.tw/modules/tad_modules/index.php?module_sn=33', 3, '需要單位名稱模組(e_stud_import)1.9以上');
}
include_once XOOPS_ROOT_PATH.'/modules/e_stud_import/es_comm_function.php';
/********************* 自訂函數 *********************/
//把  ONLY_FULL_GROUP_BY 移除

$DEF_SET['days'] = $xoopsModuleConfig['es_tt_days'];
$DEF_SET['days_sm'] = $xoopsModuleConfig['es_tt_days'] + 1;
$DEF_SET['sects'] = $xoopsModuleConfig['es_tt_sects'];
$DEF_SET['sects_sm'] = $xoopsModuleConfig['es_tt_sects'] + 1;
$DEF_SET['m_sects'] = $xoopsModuleConfig['es_tt_m_sects'];
$DEF_SET['sects_first_show'] = $xoopsModuleConfig['es_tt_m_sects_first_show'];
$DEF_SET['sects_first'] = $xoopsModuleConfig['es_tt_m_sects_first'];
$DEF_SET['input'] = $xoopsModuleConfig['es_tt_class_input'];
$DEF_SET['es_tt_local'] = $xoopsModuleConfig['es_tt_local'];
$DEF_SET['grade'] = preg_split('/[,]/', $xoopsModuleConfig['es_tt_grade']);
$DEF_SET['teacher_group'] = $xoopsModuleConfig['es_tt_teacher_group'];

$DEF_SET['es_tt_week_D'] = $xoopsModuleConfig['es_tt_week_D'];

$DEF_SET['es_tt_showYear'] = $xoopsModuleConfig['es_tt_sm__OpenYear'];
$DEF_SET['es_tt_showSemester'] = $xoopsModuleConfig['es_tt_sm__OpenSemester'];
$DEF_SET['noon_show'] = $xoopsModuleConfig['es_tt_noon_show'];

//$DEF_SET['es_tt_begin']=   $xoopsModuleConfig['es_tt_begin']  ;

//中文節次
$DEF_SET['sects_cht'] = preg_split('/[,]/', $xoopsModuleConfig['es_tt_m_sects_cht']);

$i = 1;
foreach ($DEF_SET['sects_cht']  as $oi => $sect_name) {
    $DEF_SET['sects_cht_list'][$i] = $sect_name;
    ++$i;
}

//time
$time_list = preg_split('/[,]/', $xoopsModuleConfig['es_tt_m_sects_time']);
$i = 1;
foreach ($time_list  as $oi => $sect_name) {
    $DEF_SET['time_list'][$i] = $sect_name;
    ++$i;
}

//特殊班 9901
$DEF_SET['spe_class'] = preg_split('/[,]/', $xoopsModuleConfig['es_tt_spe_class']);
$i = 9901;
foreach ($DEF_SET['spe_class']  as $oi => $spe_class_name) {
    //班名_教師名
    $spec_class_teach = preg_split('/[_]/', $spe_class_name);
    if ($spec_class_teach[0]) {
        $DEF_SET['spe_class_list'][$i] = $spec_class_teach[0];
        if ($spec_class_teach[1]){
            //教師以名字做判別
            $stea= trim( $spec_class_teach[1]) ;
            $DEF_SET['spe_class_list_teacher'][$i] = $stea;
            $DEF_SET['spe_teacher_in_class'][$stea] = $i ;
        }
        ++$i;
    }
}

//外聘教師類別
$DEF_SET['es_tt_ex_teach_kind'] = preg_split('/[,]/', $xoopsModuleConfig['es_tt_ex_teach_kind']);

$i = 2;
foreach ($DEF_SET['es_tt_ex_teach_kind']  as $oi => $over_name) {
    $DEF_SET['es_tt_exteach'][$i] = $over_name;
    //說明文字
    $DEF_SET['es_tt_exteach_message'] .= $i.'-'.$over_name.' , ';
    ++$i;
}

//校內減課經費來源
$DEF_SET['es_tt_over'] = preg_split('/[,]/', $xoopsModuleConfig['es_tt_over_list']);

$i = 1;
foreach ($DEF_SET['es_tt_over']  as $oi => $over_name) {
    $DEF_SET['es_tt_over_list'][$i] = $over_name;
    $DEF_SET['es_tt_over_list2'][$i] = $i.'-'.$over_name;
    ++$i;
}

$DEF_SET['week'] = array('', '週一', '週二', '週三', '週四', '週五', '週六', '週日');

$DEF_SET['es_tt_Holiday_KW'] = preg_split('/[,]/', $xoopsModuleConfig['es_tt_holiday_kw']);

//是否獨立模式
$DEF_SET['es_tt_single_mode'] = $xoopsModuleConfig['es_tt_single_mode'];
//獨立模式班級
$sm_class_num = preg_split('/[,]/', $xoopsModuleConfig['es_tt_sm_class_num']);
//獨立模式班級列表
foreach ($DEF_SET['grade']  as $gi => $gg) {
    for ($i = 1; $i <= $sm_class_num[$gi]; ++$i) {
        $c_id = sprintf('%03d', $gg * 100 + $i);
        $DEF_SET['sm_class_id'][$c_id] = $c_id;
    }
}

//由 tad_cal 中取得放假日
function get_tad_cal_holiday($kword, $beg_date = '', $end_date = '')
{
    global  $xoopsDB;

    //無行事曆則略去
    $sql = 'select count(`start`)  from '.$xoopsDB->prefix('tad_cal_event');
    $result = $xoopsDB->query($sql);
    if (empty($result)) {
        return;
    }

    $myts = &MyTextSanitizer::getInstance();

    //假日關鍵字
    $first = true;
    foreach ($kword as  $k => $w) {
        $w = $myts->addSlashes($w);
        $w = $myts->htmlspecialchars($w);
        if ($first) {
            $query_sql .=  "   `title` like '%$w%'   ";
            $first = 0;
        } else {
            $query_sql .=  "   or  `title` like '%$w%'   ";
        }
    }

    //學期
    $n_month = date('n');

    if ($n_month < 2) {
        $b_date = (date('Y') - 1).'-08-01';
        $e_date = date('Y').'-01-31';
    } elseif ($n_month > 7) {
        $b_date = date('Y').'-08-01';
        $e_date = (date('Y') + 1).'-01-31';
    } else {
        $b_date = date('Y').'-02-01';
        $e_date = date('Y').'-07-31';
    }

    if ($beg_date == '') {
        $beg_date = $b_date;
    }
    if ($end_date == '') {
        $end_date = $e_date;
    }

    $sql = ' select  start , title  FROM  '.$xoopsDB->prefix('tad_cal_event')." where start >= '$beg_date'  and  start<= '$end_date'  and    ( $query_sql )    order by  start  ";

    $result = $xoopsDB->query($sql) or die($sql.'<br>'.$xoopsDB->error());
    while ($row = $xoopsDB->fetchArray($result)) {
        $h_d = substr($row['start'], 0, 10);
        $data[$h_d] = $row['title'];
    }

    return $data;
}

//課表中要用到特殊班
function get_timetable_class_list_c($mode = 'short')
{
    global $DEF_SET;
    //獨立模式
    if ($DEF_SET['es_tt_single_mode']) {
        $class_list = $DEF_SET['sm_class_id'];
    } else {
        $class_list = es_class_name_list_c($mode);
    }
    //特殊班
    foreach ($DEF_SET['spe_class_list'] as $class_id => $class_name) {
        $class_list[$class_id] = $class_name;
    }

    return $class_list;
}

function get_timetable_info($adm= false)
{
    //取得課表內容--最近的年度、期別
    global  $xoopsDB, $DEF_SET ;

    $sql = '  SELECT  school_year , semester  FROM '.$xoopsDB->prefix('es_timetable').'  order by school_year DESC , semester DESC  ';
    $result = $xoopsDB->query($sql) or die($sql.'<br>'.$xoopsDB->error());
    $row = $xoopsDB->fetchArray($result);

    #如果在偏好中設定，只取得
    if ($adm or $DEF_SET['es_tt_showSemester']==0){
        $data['year'] = $row['school_year'];
        $data['semester'] = $row['semester'];
    }else {
        $data['year'] = $DEF_SET['es_tt_showYear'];
        $data['semester'] = $DEF_SET['es_tt_showSemester'];
    }



    $sql = '  SELECT  count(*) as cc   FROM '.$xoopsDB->prefix('es_timetable')." where school_year='{$data['school_year']}' and semester ='{$data['semester']}'  group by  class_id  ";
    $result = $xoopsDB->query($sql) or die($sql.'<br>'.$xoopsDB->error());
    $row = $xoopsDB->fetchArray($result);
    $data['class'] = $row['cc'];
    /*
    if (!$data['year']) {
        $data['year']=104;
        $data['semester'] =1 ;

    }
*/

    return $data;
}

//取出課表內容
function get_timetable_data($mode, $y, $s, $class_sel = 'all', $over_id = '')
{
    global  $xoopsDB;

    if (intval($over_id) > 0) {
        //超鐘點部份，只在 teacher 模式
        $where_plus = "  and c_kind ='$over_id'  ";
    }

    if ($mode == 'teacher') {
        $sql = ' select *  FROM  '.$xoopsDB->prefix('es_timetable')." where school_year= '$y'  and  semester= '$s'   $where_plus     order by teacher,day,sector ";
    }

    if ($mode == 'class_id') {
        if ($class_sel == 'all') {
            $sql = ' select *  FROM  '.$xoopsDB->prefix('es_timetable')." where school_year= '$y'  and  semester= '$s'     order by class_id,day,sector   ";
        } else {
            $sql = ' select *  FROM  '.$xoopsDB->prefix('es_timetable')." where school_year= '$y'  and  semester= '$s'   and class_id='$class_sel'    order by class_id,day,sector   ";
        }
    }

    if ($mode == 'room') {
        $sql = ' select *  FROM  '.$xoopsDB->prefix('es_timetable')." where school_year= '$y'  and  semester= '$s'   and room <>''   order by room,day,sector   ";
    }

    $result = $xoopsDB->query($sql) or die($sql.'<br>'.$xoopsDB->error());
    while ($row = $xoopsDB->fetchArray($result)) {
        $key = $row[$mode];        //取得該師目前代號或該班代號

        if ($old_key == '') {
            $old_key = $key;
        }
        if ($old_key !=    $key) {
            $data[$old_key] = $tab;
            $old_key = $key;
            unset($tab);
        }

        $d = $row['day'];
        $s = $row['sector'];
        $w = $row['week_d'];
        $cell['class_id'] = $row['class_id'];
        $cell['teacher'] = $row['teacher'];
        $cell['ss_id'] = $row['ss_id'];
        $cell['room'] = $row['room'];

        $cell['other'] = '';
        //如果同教師教兩班???
        if ($tab[$d][$s][$w]) {
            $cell['other'] = $tab[$d][$s][$w]['class_id'];
        }
        $tab[$d][$s][$w] = $cell;
    }
    $data[$old_key] = $tab;

    //var_dump($data);
    return $data;
}
/*
//取出課表 教師條列式
function get_timetable_data_list(  $y ,$s ,  $plus='') {
    global  $xoopsDB ;

    if ($plus=='plus')
        //超鐘點部份，只在 teacher 模式
        $where_plus = "  and c_kind ='1'  " ;

        $sql = " select *  FROM  "  . $xoopsDB->prefix("es_timetable")  .  " where school_year= '$y'  and  semester= '$s'   $where_plus     order by teacher,day,sector " ;


    $result = $xoopsDB->query($sql) or die($sql."<br>". $xoopsDB->error());
    while($row=$xoopsDB->fetchArray($result)){
        $key =$row['teacher'] ;
        $data[$key][] = $row ;
    }
    return $data ;

}
*/

//取出課表  room
function get_class_room_list($y, $s)
{
    global  $xoopsDB;

    $data[0] = '選擇查看教室';

    $sql = ' select  room   FROM  '.$xoopsDB->prefix('es_timetable')." where school_year= '$y'  and  semester= '$s'     and room <>''   group by  room ";

    $result = $xoopsDB->query($sql) or die($sql.'<br>'.$xoopsDB->error());
    while ($row = $xoopsDB->fetchArray($result)) {
        $data[] = $row['room'];
    }

    return $data;
}

function get_subject_list()
{
    //取得科目名稱
    global  $xoopsDB;
    $sql = '  SELECT  subject_id , subject_name   FROM '.$xoopsDB->prefix('es_timetable_subject').' order by subject_id  ';
    $result = $xoopsDB->query($sql) or die($sql.'<br>'.$xoopsDB->error());
    while ($row = $xoopsDB->fetchArray($result)) {
        $data[$row['subject_id']] = $row['subject_name'];
    }

    return $data;
}

function get_subject_data_list()
{
    //取得科目資料庫中多欄位
    global  $xoopsDB;
    $sql = '  SELECT  subject_id , subject_name ,subject_scope ,e_subject ,s_subject    FROM '.$xoopsDB->prefix('es_timetable_subject').' order by subject_id  ';
    $result = $xoopsDB->query($sql) or die($sql.'<br>'.$xoopsDB->error());
    while ($row = $xoopsDB->fetchArray($result)) {
        $data[$row['subject_id']]['subject'] = $row['subject_name'];
        $data[$row['subject_id']]['scope'] = $row['subject_scope'];
        $data[$row['subject_id']]['e_subject'] = $row['e_subject'];
        $data[$row['subject_id']]['s_subject'] = $row['s_subject'];
    }

    return $data;
}

function get_subject_grade_list()
{
    //取得目前的各年級使用科目
    global  $xoopsDB;
    $sql = '  SELECT  subject_id , grade   FROM '.$xoopsDB->prefix('es_timetable_subject_year').' order by grade ,subject_id ';
    $result = $xoopsDB->query($sql) or die($sql.'<br>'.$xoopsDB->error());
    while ($row = $xoopsDB->fetchArray($result)) {
        $data[$row['grade']][$row['subject_id']] = $row['subject_id'];
    }

    return $data;
}

function sync_teacher($teacher_group = 4)
{
    //同步
    $school_teacher = get_teacher_list($teacher_group, 0);
    foreach ($school_teacher as $uid => $user) {
        $teach_list[$uid] = $user['name'];
        //echo $uid .$user['name']  ;
        join_table_teacher($uid, $user['name']);
    }
}

function join_table_teacher($uid, $user)
{
    //加入教師
    global  $xoopsDB;
    $sql = '  SELECT  teacher_id , user_id , name   FROM '.$xoopsDB->prefix('es_timetable_teacher')." where  user_id='$uid' or name ='$user' ";

    $result = $xoopsDB->query($sql) or die($sql.'<br>'.$xoopsDB->error());
    $row = $xoopsDB->fetchArray($result);
    if ((!$row['user_id']) and ($row['name'] == '')) {
        $sql = ' INSERT INTO   '.$xoopsDB->prefix('es_timetable_teacher').
                ' (`user_id`, `name`)  '.
                "  VALUES  ( '$uid' , '$user' )   ";
    } elseif (!$row['user_id']) {
        $teacher_id = $row['teacher_id'];
        $sql = ' UPDATE '.$xoopsDB->prefix('es_timetable_teacher')." SET  user_id='$uid' where  teacher_id='$teacher_id' ";
    }
    $result = $xoopsDB->queryF($sql) or die($sql.'<br>'.$xoopsDB->error());
}

function get_table_teacher_data()
{
    //取得目前教師
    global  $xoopsDB;
    //由學校資料表中取得

    $sql = '  SELECT  *  FROM '.$xoopsDB->prefix('es_timetable_teacher').' order by hide ,  teacher_id DESC  ';
    $result = $xoopsDB->query($sql) or die($sql.'<br>'.$xoopsDB->error());
    while ($row = $xoopsDB->fetchArray($result)) {
        $table_teacher[$row['teacher_id']] = $row;
    }

    return $table_teacher;
}

function get_table_teacher_list($mode = 'hide' , $sortByname ='' )
{
    //取得目前教師
    global  $xoopsDB;
    //由學校資料表中取得

    if ($mode == 'all') {
        //全部
        $sql = '  SELECT  teacher_id , user_id , name   FROM '.$xoopsDB->prefix('es_timetable_teacher').'     order by name ';
    } else {
        //不出現隱去者
        //$sql =  "  SELECT  teacher_id , user_id , name   FROM " . $xoopsDB->prefix("es_timetable_teacher")  ." where  hide='0'  order by name  "   ;
        if ($sortByname)
          $sql = '  SELECT  a.teacher_id , a.user_id , a.name , a.kind ,b.class_id , b.staff   FROM '.
            $xoopsDB->prefix('es_timetable_teacher').' a  '.
            ' LEFT JOIN '.$xoopsDB->prefix('e_classteacher').' b '.
            ' On a.user_id = b.uid '.
            " where  a.hide='0'   order  by a.name  ";
        else
          $sql = '  SELECT  a.teacher_id , a.user_id , a.name , a.kind ,b.class_id , b.staff   FROM '.
            $xoopsDB->prefix('es_timetable_teacher').' a  '.
            ' LEFT JOIN '.$xoopsDB->prefix('e_classteacher').' b '.
            ' On a.user_id = b.uid '.
            " where  a.hide='0'   order by a.kind , b.staff  , b.class_id , a.name  ";
    }
    //echo  $sql ;
    $result = $xoopsDB->query($sql) or die($sql.'<br>'.$xoopsDB->error());
    while ($row = $xoopsDB->fetchArray($result)) {
        $table_teacher[$row['teacher_id']] = $row['name'];
    }

    return $table_teacher;
}

function get_my_id_in_timetable($uid = 0)
{
    //取得$uid 在課表中 teacher_id
    global  $xoopsDB ,$xoopsUser;
    if (!$uid) {
        $uid = $xoopsUser->uid();
    }
    $sql = '  SELECT  teacher_id  FROM '.$xoopsDB->prefix('es_timetable_teacher').
                   " where user_id= '$uid'   ";

    $result = $xoopsDB->query($sql) or die($sql.'<br>'.$xoopsDB->error());
    while ($data_row = $xoopsDB->fetchArray($result)) {
        $teacher_id = $data_row['teacher_id'];
    }

    return $teacher_id;
}

//取得教師名冊， 群組代碼， 顯示模式(0:只取資料， 1:轉換EMAIL、職稱)
function get_teacher_list($teach_group_id, $show = 0)
{
    global  $xoopsDB;
/*
SELECT u.uid, u.name, u.user_occ, g.groupid ,c.class_id
FROM `xx_groups_users_link` AS g
LEFT JOIN `xx_users` AS u ON u.uid = g.uid
left join  xx_e_classteacher as c on u.uid = c.uid
WHERE g.groupid =4
group by u.uid
order by  u.user_occ ,c.class_id
*/
    $sql = '  SELECT  u.uid, u.name ,u.uname, u.email ,u.user_viewemail , u.url , c.staff , g.groupid ,c.class_id   FROM  '.
            $xoopsDB->prefix('groups_users_link').'  AS g LEFT JOIN  '.$xoopsDB->prefix('users').'  AS u ON u.uid = g.uid '.
            ' left join '.$xoopsDB->prefix('e_classteacher').' as c on u.uid = c.uid '.
            "  WHERE g.groupid ='$teach_group_id'  group by u.uid   order by  c.staff , c.class_id , u.name ";

    $result = $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'], 3, $xoopsDB->error());
    while ($row = $xoopsDB->fetchArray($result)) {
        if (!$row['name']) {
            $row['name'] = $row['uname'];
        }
        if ($show) {
            //email
            if ($row['email'] and $row['user_viewemail']) {        //EMAIL 顯示做保護
                $row['email_show'] = email_protect($row['email']);
            }
            //班級
            //$job_arr = preg_split('/[-]/' ,$row['user_occ']) ;
            $job_arr = preg_split('/[-]/', $row['staff']);
            $row['staff'] = $job_arr[1];
            if ($row['class_id']) {
                $row['staff'] .= '-'.$row['class_id'].'班';
            }
        }
        $teacher[$row['uid']] = $row;
    }

    return $teacher;
}

function check_timetable_double($y, $s)
{
    //檢查是否有重複
    global  $xoopsDB ,$DEF_SET;

    if ((!$y) or (!$s)) {
        return;
    }

    //把  ONLY_FULL_GROUP_BY 移除
    //$sql = " SET sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', )); "  ;
    //$xoopsDB->queryF($sql)   ;

    //取得中文班名
    $class_list_c = es_class_name_list_c();
    //讀取科目
    $subject = get_subject_list();

    //班級同一節有兩科
    $sql = '   SELECT class_id , day ,  sector ,week_d , count(*) as cc FROM  '.$xoopsDB->prefix('es_timetable')."  where school_year= '$y'  and  semester= '$s'   group by  class_id , day ,  sector ,week_d HAVING cc>1    ";

    $result = $xoopsDB->query($sql) or die($sql.'<br/>'.$xoopsDB->error());
    while ($row = $xoopsDB->fetchArray($result)) {
        $data .= '(一班多節)'.$class_list_c[$row['class_id']].$DEF_SET['week'][$row['day']].'-'.$DEF_SET['sects_cht_list'][$row['sector']].'  (把該班該節科目先移除)<br />';
    }

    //教師同一節有兩科
    $teacher_list = get_table_teacher_data();

    $sql = '   SELECT teacher , day ,  sector ,week_d , count(*) as cc FROM  '.$xoopsDB->prefix('es_timetable')."  where school_year= '$y'  and  semester= '$s'   group by  teacher , day ,  sector ,week_d  HAVING cc>1    ";
    $result = $xoopsDB->query($sql) or die($sql.'<br/>'.$xoopsDB->error());

    while ($row = $xoopsDB->fetchArray($result)) {
        $data .= '(一師多節)'.$teacher_list[$row['teacher']]['name'].'  --  '.$DEF_SET['week'][$row['day']].$DEF_SET['sects_cht_list'][$row['sector']].' --- ';
        //取得該教師那一節課
        $sql2 = '   SELECT class_id , ss_id  FROM  '.$xoopsDB->prefix('es_timetable').
            "  where school_year= '$y'  and  semester= '$s'    and  teacher='{$row['teacher']}' and `day`='{$row['day']}'  and  sector='{$row['sector']}'       ";

        $result2 = $xoopsDB->query($sql2) or die($sql.'<br/>'.$xoopsDB->error());

        while ($row2 = $xoopsDB->fetchArray($result2)) {
            $data .= $class_list_c[$row2['class_id']].$subject[$row2['ss_id']].'  , ';
        }

        $data .= '(可能為大班級上課)<br />';
    }

    //教室
    $sql = '   SELECT room , day ,  sector , count(*) as cc FROM  '.$xoopsDB->prefix('es_timetable')."  where school_year= '$y'  and  semester= '$s'   and room <> '' group by  room , day ,  sector    HAVING cc>1    ";
    //echo $sql ;

    $result = $xoopsDB->query($sql) or   die($sql.'<br/>'.$xoopsDB->error());

    while ($row = $xoopsDB->fetchArray($result)) {
        $data .= $row['room'].' 教室--'.$DEF_SET['week'][$row['day']].'-'.$DEF_SET['sects_cht_list'][$row['sector']].'  ---  ';
        $sql2 = '   SELECT class_id , teacher  FROM  '.$xoopsDB->prefix('es_timetable').
            "  where school_year= '$y'  and  semester= '$s'    and  room='{$row['room']}' and `day`='{$row['day']}'  and  sector='{$row['sector']}'       ";

        $result2 = $xoopsDB->query($sql2) or die($sql.'<br/>'.$xoopsDB->error());
        while ($row2 = $xoopsDB->fetchArray($result2)) {
            $data .= $class_list_c[$row2['class_id']].' (<a href="set_room.php?teacher_id='.$row2['teacher'].'">'.$teacher_list[$row2['teacher']]['name'].'</a>) , ';
        }

        $data .= '<br />';
    }

    //單雙週有無配對
    $sql = '   SELECT class_id , day ,  sector  , sum(week_d) as wsum FROM  '.$xoopsDB->prefix('es_timetable')."  where school_year= '$y'  and  semester= '$s'    '' group by  class_id , day ,  sector    HAVING  (wsum>0  and  wsum<3)  ";

    //echo $sql ;
    $result = $xoopsDB->query($sql) or  die($sql.'<br/>'.$xoopsDB->error());
    while ($row = $xoopsDB->fetchArray($result)) {
        $data .= '<a href="ed_timetable.php?class_id='.$row['class_id'].'">'.$class_list_c[$row['class_id']].'</a>  單雙週配對不完整--'.$DEF_SET['week'][$row['day']].'-'.$DEF_SET['sects_cht_list'][$row['sector']];
        if ($row['wsum'] == 1) {
            $data .=  ' 缺雙週排課';
        }
        if ($row['wsum'] == 2) {
            $data .=  ' 缺單週排課';
        }
        $data .= '<br />';
    }

    return $data;
}

//取得單人、教室、班級課表
function get_ones_timetable($mode, $y, $s, $id)
{
    global  $xoopsDB;
    $class_list_c = get_timetable_class_list_c('short');
    $subject = get_subject_list();

    $room_list = get_class_room_list($y, $s);
    //讀取人名
    $teacher_list = get_table_teacher_data();
    for ($d = 1; $d <= $DEF_SET['days'];++$d) {
        for ($s = 1; $s <= $DEF_SET['sects'];++$s) {
            $data[$d][$s]['ss_id'] = 0;
        }
    }

    if ($mode == 'teacher') {
        $sql = ' select *  FROM  '.$xoopsDB->prefix('es_timetable')." where school_year= '$y'  and  semester= '$s'    and  teacher= '$id'   order by day,sector ,week_d   ";
    } elseif ($mode == 'room') {
        $sql = ' select *  FROM  '.$xoopsDB->prefix('es_timetable')." where school_year= '$y'  and  semester= '$s'    and  room= '$id'  order by day,sector  ,week_d  ";
    } else {
        $sql = ' select *  FROM  '.$xoopsDB->prefix('es_timetable')." where school_year= '$y'  and  semester= '$s'    and  class_id= '$id'  order by day,sector ,week_d   ";
    }
    //echo $sql ;
    $result = $xoopsDB->queryF($sql) or die($sql.'<br>'.$xoopsDB->error());
    while ($row = $xoopsDB->fetchArray($result)) {
        $row['subject_name'] = $subject[$row['ss_id']];
        $row['teacher_name'] = $teacher_list[$row['teacher']]['name'];
        $row['room_id'] = array_search($row['room'], $room_list);

        $row['other'] = '';

        //如果同教師教兩班???
        if ($data[$row['day']][$row['sector']][$row['week_d']]) {
            $orow = $data[$row['day']][$row['sector']][$row['week_d']];
            $row['other'] = $class_list_c[$orow['class_id']].$orow['subject_name'].' '.$orow['other'];
        }
        $data[$row['day']][$row['sector']][$row['week_d']] = $row;
    }
    //var_dump($data);
    return $data;
}
//=================================================================================================
function get_class_list()
{
    //取得全校班級列表
    global  $xoopsDB;

    $sql = '  SELECT  class_id  FROM '.$xoopsDB->prefix('e_student').'   group by class_id   ';

    $result = $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'], 3, $xoopsDB->error());
    while ($row = $xoopsDB->fetchArray($result)) {
        $data[$row['class_id']] = $row['class_id'];
    }

    return $data;
}

function get_class_grade_list()
{
    //取得全校年級列表
    global  $xoopsDB;
    $sql = '  SELECT  SUBSTR( `class_id` , 1, 1 ) AS grade   FROM '.$xoopsDB->prefix('e_student').'   group by  SUBSTR( `class_id` , 1, 1 )   ';
    $result = $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'], 3, $xoopsDB->error());
    while ($row = $xoopsDB->fetchArray($result)) {
        $data[$row['grade']] = $row['grade'];
    }

    return $data;
}

function get_class_num()
{
    //取得全校班級總數
    global  $xoopsDB;

    $sql = '  SELECT  count(class_id) as cc   FROM '.$xoopsDB->prefix('e_student').'   group by class_id   ';

    $result = $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'], 3, $xoopsDB->error());
    while ($row = $xoopsDB->fetchArray($result)) {
        $data = $row['cc'];
    }

    return $data;
}

function get_class_teacher_list()
{
    //取得全部級任名冊
    global  $xoopsDB , $DEF_SET;
    $sql = '  SELECT  t.uid, t.class_id , u.name  FROM '.$xoopsDB->prefix('e_classteacher').'  t  , '.$xoopsDB->prefix('users').'  u    '.
                   ' where t.uid= u.uid    ';

    $result = $xoopsDB->query($sql) or die($sql.'<br>'.$xoopsDB->error());
    while ($data_row = $xoopsDB->fetchArray($result)) {
        $class_id[$data_row['class_id']] = $data_row['name'];
    }
    //如果有特殊班指定
    foreach ($DEF_SET['spe_class_list'] as  $cid =>$cname  ){
        $class_id[$cid] = $DEF_SET['spe_class_list_teacher'][$cid].'' ;
    }


    return $class_id;
}

function get_my_class_id($uid = 0)
{
    //取得$uid 的任教班級
    global  $xoopsDB ,$xoopsUser, $DEF_SET;
    if (!$uid) {
        $uid = $xoopsUser->uid();
    }
    $uid_name=XoopsUser::getUnameFromId($uid,1);

    $sql = '  SELECT  class_id  FROM '.$xoopsDB->prefix('e_classteacher').
                   " where uid= '$uid'   ";

    $result = $xoopsDB->query($sql) or die($sql.'<br>'.$xoopsDB->error());
    while ($data_row = $xoopsDB->fetchArray($result)) {
        $class_id = $data_row['class_id'];
    }

    //特殊班中指定教師名
    if ($DEF_SET['spe_teacher_in_class'][$uid_name])
        $class_id =  $DEF_SET['spe_teacher_in_class'][$uid_name] ;


    return $class_id;
}
