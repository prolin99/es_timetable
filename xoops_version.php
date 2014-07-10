<?php
//  ------------------------------------------------------------------------ //
// 本模組由 prolin 製作
// 製作日期：2014-05-1
// $Id:$
// ------------------------------------------------------------------------- //

//---基本設定---//

$modversion['name'] ='課表';				//模組名稱
$modversion['version']	= '0.2';				//模組版次
$modversion['author'] = 'prolin(prolin@tn.edu.tw)';		//模組作者
$modversion['description'] ='展示功課表';			//模組說明
$modversion['credits']	= 'prolin';				//模組授權者
$modversion['license']		= "GPL see LICENSE";		//模組版權
$modversion['official']		= 0;				//模組是否為官方發佈1，非官方0
$modversion['image']		= "images/logo.png";		//模組圖示
$modversion['dirname'] = basename(dirname(__FILE__));		//模組目錄名稱

//---模組狀態資訊---//

$modversion['release_date'] = '2014-07-13';
$modversion['module_website_url'] = 'https://github.com/prolin99/es_timetable';
$modversion['module_website_name'] = 'prolin';
$modversion['module_status'] = 'release';
$modversion['author_website_url'] = 'http://www.syps.tn.edu.tw';
$modversion['author_website_name'] = 'prolin';
$modversion['min_php']= 5.2;



//---啟動後台管理界面選單---//
$modversion['system_menu'] = 1;//---資料表架構---//
$modversion['sqlfile']['mysql'] = "sql/mysql.sql";
$modversion['tables'][1] = "es_timetable_subject";
$modversion['tables'][2] = "es_timetable_subject_year";
$modversion['tables'][3] = "es_timetable";
$modversion['tables'][4] = "es_timetable_teacher";
 

//---管理介面設定---//
$modversion['hasAdmin'] = 1;
$modversion['adminindex'] = "admin/index.php";
$modversion['adminmenu'] = "admin/menu.php"; 	 	 		
 
//---使用者主選單設定---//
$modversion['hasMain'] = 1;

//---安裝設定---//
$modversion['onInstall'] = "include/onInstall.php";
 

//---樣板設定---要有指定，才會編譯動作，//
$modversion['templates'] = array();
$i=1;
$modversion['templates'][$i]['file'] = 'es_timet_index_tpl.html';
$modversion['templates'][$i]['description'] = 'es_timet_index_tpl.html';
$i++ ;
$modversion['templates'][$i]['file'] = 'es_timet_ad_index_tpl.html';
$modversion['templates'][$i]['description'] = 'es_timet_ad_index_tpl.html';

$i++ ;
$modversion['templates'][$i]['file'] = 'es_timet_ad_set_sub_tpl.html';
$modversion['templates'][$i]['description'] = 'es_timet_ad_set_sub_tpl.html';

$i++ ;
$modversion['templates'][$i]['file'] = 'es_timet_ad_set_teacher_tpl.html';
$modversion['templates'][$i]['description'] = 'es_timet_ad_set_teacher_tpl.html';

$i++ ;
$modversion['templates'][$i]['file'] = 'es_timet_ad_table_tpl.html';
$modversion['templates'][$i]['description'] = 'es_timet_ad_table_tpl.html';
 
$i=0 ;
//偏好設定
$i++ ;
//預設額外欄位數
$modversion['config'][$i]['name'] = 'es_tt_days';
$modversion['config'][$i]['title']   = '_MI_ES_TT_CONFIG_T1';
$modversion['config'][$i]['description'] = '_MI_ES_TT_CONFIG_D1';
$modversion['config'][$i]['formtype']    = 'textbox';
$modversion['config'][$i]['valuetype']   = 'int';
$modversion['config'][$i]['default'] ="5" ;

$i++ ;
$modversion['config'][$i]['name'] = 'es_tt_sects';
$modversion['config'][$i]['title']   = '_MI_ES_TT_CONFIG_T2';
$modversion['config'][$i]['description'] = '_MI_ES_TT_CONFIG_D2';
$modversion['config'][$i]['formtype']    = 'textbox';
$modversion['config'][$i]['valuetype']   = 'int';
$modversion['config'][$i]['default'] ="7" ;

$i++ ;
$modversion['config'][$i]['name'] = 'es_tt_m_sects';
$modversion['config'][$i]['title']   = '_MI_ES_TT_CONFIG_T3';
$modversion['config'][$i]['description'] = '_MI_ES_TT_CONFIG_D3';
$modversion['config'][$i]['formtype']    = 'textbox';
$modversion['config'][$i]['valuetype']   = 'int';
$modversion['config'][$i]['default'] ="4" ;

$i++ ;
$modversion['config'][$i]['name'] = 'es_tt_class_input';
$modversion['config'][$i]['title']   = '_MI_ES_TT_CONFIG_T4';
$modversion['config'][$i]['description'] = '_MI_ES_TT_CONFIG_D4';
$modversion['config'][$i]['formtype']    = 'yesno';
$modversion['config'][$i]['valuetype']   = 'int';
$modversion['config'][$i]['default'] = true  ;

$i++ ;
$modversion['config'][$i]['name'] = 'es_tt_grade';
$modversion['config'][$i]['title']   = '_MI_ES_TT_CONFIG_T5';
$modversion['config'][$i]['description'] = '_MI_ES_TT_CONFIG_D5';
$modversion['config'][$i]['formtype']    = 'textbox';
$modversion['config'][$i]['valuetype']   = 'text';
$modversion['config'][$i]['default'] ="1,2,3,4,5,6" ;

$i++ ;
$modversion['config'][$i]['name'] = 'es_tt_teacher_group';
$modversion['config'][$i]['title']   = '_MI_ESTUDENTS_CONFIG_TITLE1';
$modversion['config'][$i]['description'] = '_MI_ESTUDENTS_CONFIG_DESC1';
$modversion['config'][$i]['formtype']    = 'group';
$modversion['config'][$i]['valuetype']   = 'int';
$modversion['config'][$i]['default'] = 4 ;					//配合校園網站輕鬆架，預設值

$i++ ;
?>