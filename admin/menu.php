<?php
//  ------------------------------------------------------------------------ //
// 本模組由 prolin 製作
// 製作日期：2014-03-01
// $Id:$
// ------------------------------------------------------------------------- //
$adminmenu = array();

$i = 0;
$adminmenu[$i]['title'] =_MI_TAD_ADMIN_HOME;
$adminmenu[$i]['link'] = 'admin/index.php' ;
$adminmenu[$i]['desc'] = _MI_TAD_ADMIN_HOME_DESC ;
$adminmenu[$i]['icon'] = 'images/admin/home.png' ;

++$i;
$adminmenu[$i]['title'] = '學期課表';
$adminmenu[$i]['link'] = 'admin/main.php';
$adminmenu[$i]['desc'] = '報名管理';
$adminmenu[$i]['icon'] = 'images/admin/button.png';

++$i;
$adminmenu[$i]['title'] = '科目設定';
$adminmenu[$i]['link'] = 'admin/set_subject.php';
$adminmenu[$i]['desc'] = '科目設定';
$adminmenu[$i]['icon'] = 'images/admin/genadm.png';

++$i;
$adminmenu[$i]['title'] = '任課教師';
$adminmenu[$i]['link'] = 'admin/set_teacher.php';
$adminmenu[$i]['desc'] = '任課教師';
$adminmenu[$i]['icon'] = 'images/admin/main.png';

++$i;
$adminmenu[$i]['title'] = '編排課表';
$adminmenu[$i]['link'] = 'admin/ed_timetable.php';
$adminmenu[$i]['desc'] = '課表編排';
$adminmenu[$i]['icon'] = 'images/admin/timetable.png';

++$i;
$adminmenu[$i]['title'] = '指定教室，超鐘點';
$adminmenu[$i]['link'] = 'admin/set_room.php';
$adminmenu[$i]['desc'] = '指定教室';
$adminmenu[$i]['icon'] = 'images/admin/logadm.png';

++$i;
$adminmenu[$i]['title'] = '關於';
$adminmenu[$i]['link'] = 'admin/about.php';
$adminmenu[$i]['desc'] = '說明';
$adminmenu[$i]['icon'] = 'images/admin/about.png';
