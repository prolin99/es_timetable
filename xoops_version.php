<?php
//  ------------------------------------------------------------------------ //
// 本模組由 prolin 製作
// 製作日期：2014-05-1
// $Id:$
// ------------------------------------------------------------------------- //

//---基本設定---//

$modversion['name'] = '課表';                //模組名稱
$modversion['version'] = '2.66';                //模組版次
$modversion['author'] = 'prolin(prolin99@gmail.com)';        //模組作者
$modversion['description'] = '展示功課表';            //模組說明
$modversion['credits'] = 'prolin';                //模組授權者
$modversion['license'] = 'GPL see LICENSE';        //模組版權
$modversion['official'] = 0;                //模組是否為官方發佈1，非官方0
$modversion['image'] = 'images/logo.png';        //模組圖示
$modversion['dirname'] = basename(dirname(__FILE__));        //模組目錄名稱

//---模組狀態資訊---//

$modversion['release_date'] = '2023-07-30';
$modversion['module_website_url'] = 'https://github.com/prolin99/es_timetable';
$modversion['module_website_name'] = 'prolin';
$modversion['module_status'] = 'release';
$modversion['author_website_url'] = 'http://www.syps.tn.edu.tw';
$modversion['author_website_name'] = 'prolin';
$modversion['min_php'] = 5.2;

//---啟動後台管理界面選單---//
$modversion['system_menu'] = 1;//---資料表架構---//
$modversion['sqlfile']['mysql'] = 'sql/mysql.sql';
$modversion['tables'][1] = 'es_timetable_subject';
$modversion['tables'][2] = 'es_timetable_subject_year';
$modversion['tables'][3] = 'es_timetable';
$modversion['tables'][4] = 'es_timetable_teacher';
$modversion['tables'][5] = 'es_timetable_tmp';

//---管理介面設定---//
$modversion['hasAdmin'] = 1;
$modversion['adminindex'] = 'admin/index.php';
$modversion['adminmenu'] = 'admin/menu.php';

//---使用者主選單設定---//
$modversion['hasMain'] = 1;

//---安裝設定---//
$modversion['onInstall'] = 'include/onInstall.php';
$modversion['onUpdate'] = 'include/onUpdate.php';

//---樣板設定---要有指定，才會編譯動作，//
$modversion['templates'] = array();
$i = 1;

$modversion['templates'][$i]['file'] = 'es_timet_index.tpl';
$modversion['templates'][$i]['description'] = 'es_timet_index.tpl';


++$i;
$modversion['templates'][$i]['file'] = 'es_timet_ad_index_tpl.html';
$modversion['templates'][$i]['description'] = 'es_timet_ad_index_tpl.html';

++$i;
$modversion['templates'][$i]['file'] = 'es_timet_ad_set_sub_tpl.html';
$modversion['templates'][$i]['description'] = 'es_timet_ad_set_sub_tpl.html';


++$i;
$modversion['templates'][$i]['file'] = 'es_timet_ad_set_teacher_tpl.html';
$modversion['templates'][$i]['description'] = 'es_timet_ad_set_teacher_tpl.html';

++$i;
$modversion['templates'][$i]['file'] = 'es_timet_ad_table_tpl.html';
$modversion['templates'][$i]['description'] = 'es_timet_ad_table_tpl.html';

++$i;
$modversion['templates'][$i]['file'] = 'es_timet_setroom_tpl.html';
$modversion['templates'][$i]['description'] = 'es_timet_setroom_tpl.html';

++$i;
$modversion['templates'][$i]['file'] = 'es_timet_class_table.tpl';
$modversion['templates'][$i]['description'] = 'es_timet_class_table.tpl';


++$i;
$modversion['templates'][$i]['file'] = 'es_timetable_show.tpl';
$modversion['templates'][$i]['description'] = 'es_timetable_show.tpl';

++$i;
$modversion['templates'][$i]['file'] = 'es_timet_import_edu.html';
$modversion['templates'][$i]['description'] = 'es_timet_import_edu.html';

$i = 0;
//偏好設定
++$i;
//預設額外欄位數
$modversion['config'][$i]['name'] = 'es_tt_days';
$modversion['config'][$i]['title'] = '_MI_ES_TT_CONFIG_T1';
$modversion['config'][$i]['description'] = '_MI_ES_TT_CONFIG_D1';
$modversion['config'][$i]['formtype'] = 'textbox';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = '5';

++$i;
$modversion['config'][$i]['name'] = 'es_tt_sects';
$modversion['config'][$i]['title'] = '_MI_ES_TT_CONFIG_T2';
$modversion['config'][$i]['description'] = '_MI_ES_TT_CONFIG_D2';
$modversion['config'][$i]['formtype'] = 'textbox';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = '7';

++$i;
$modversion['config'][$i]['name'] = 'es_tt_m_sects';
$modversion['config'][$i]['title'] = '_MI_ES_TT_CONFIG_T3';
$modversion['config'][$i]['description'] = '_MI_ES_TT_CONFIG_D3';
$modversion['config'][$i]['formtype'] = 'textbox';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = '4';

++$i;
$modversion['config'][$i]['name'] = 'es_tt_m_sects_cht';
$modversion['config'][$i]['title'] = '_MI_ES_TT_CONFIG_T7';
$modversion['config'][$i]['description'] = '_MI_ES_TT_CONFIG_D7';
$modversion['config'][$i]['formtype'] = 'textbox';
$modversion['config'][$i]['valuetype'] = 'text';
$modversion['config'][$i]['default'] = '第一節,第二節,第三節,第四節,第五節,第六節,第七節';

++$i;
$modversion['config'][$i]['name'] = 'es_tt_m_sects_time';
$modversion['config'][$i]['title'] = '_MI_ES_TT_CONFIG_Time';
$modversion['config'][$i]['description'] = '_MI_ES_TT_CONFIG_DTime';
$modversion['config'][$i]['formtype'] = 'textbox';
$modversion['config'][$i]['valuetype'] = 'text';
$modversion['config'][$i]['default'] = '8:40~9:20,9:30~10:10,10:30~11:10,11:10~12:00,13:30~14:10,14:20~15:00,15:10~15:50';


++$i;
$modversion['config'][$i]['name'] = 'es_tt_m_sects_first_show';
$modversion['config'][$i]['title'] = '_MI_ES_TT_CONFIG_TFirst_c';
$modversion['config'][$i]['description'] = '_MI_ES_TT_CONFIG_DFirst_c';
$modversion['config'][$i]['formtype'] = 'yesno';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = false;


++$i;
$modversion['config'][$i]['name'] = 'es_tt_m_sects_first';
$modversion['config'][$i]['title'] = '_MI_ES_TT_CONFIG_TFirst';
$modversion['config'][$i]['description'] = '_MI_ES_TT_CONFIG_DFirst';
$modversion['config'][$i]['formtype'] = 'textbox';
$modversion['config'][$i]['valuetype'] = 'text';
$modversion['config'][$i]['default'] = '早自習';

++$i;
$modversion['config'][$i]['name'] = 'es_tt_noon_show';
$modversion['config'][$i]['title'] = '_MI_ES_TT_CONFIG_noon';
$modversion['config'][$i]['description'] = '_MI_ES_TT_CONFIG_D_noon';
$modversion['config'][$i]['formtype'] = 'yesno';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = True;

++$i;
$modversion['config'][$i]['name'] = 'es_tt_class_input';
$modversion['config'][$i]['title'] = '_MI_ES_TT_CONFIG_T4';
$modversion['config'][$i]['description'] = '_MI_ES_TT_CONFIG_D4';
$modversion['config'][$i]['formtype'] = 'yesno';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = false;

++$i;
$modversion['config'][$i]['name'] = 'es_tt_class_self_chk';
$modversion['config'][$i]['title'] = '_MI_ES_TT_CONFIG_class_self_chk';
$modversion['config'][$i]['description'] = '_MI_ES_TT_CONFIG_D_class_self_chk';
$modversion['config'][$i]['formtype'] = 'yesno';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = false;

++$i;
$modversion['config'][$i]['name'] = 'es_tt_grade';
$modversion['config'][$i]['title'] = '_MI_ES_TT_CONFIG_T5';
$modversion['config'][$i]['description'] = '_MI_ES_TT_CONFIG_D5';
$modversion['config'][$i]['formtype'] = 'textbox';
$modversion['config'][$i]['valuetype'] = 'text';
$modversion['config'][$i]['default'] = '1,2,3,4,5,6';

++$i;
$modversion['config'][$i]['name'] = 'es_tt_spe_class';
$modversion['config'][$i]['title'] = '_MI_ES_TT_CONFIG_T8';
$modversion['config'][$i]['description'] = '_MI_ES_TT_CONFIG_D8';
$modversion['config'][$i]['formtype'] = 'textbox';
$modversion['config'][$i]['valuetype'] = 'text';
$modversion['config'][$i]['default'] = '';

++$i;
$modversion['config'][$i]['name'] = 'es_tt_local';
$modversion['config'][$i]['title'] = '_MI_ES_TT_CONFIG_Tlocal';
$modversion['config'][$i]['description'] = '_MI_ES_TT_CONFIG_Dlocal';
$modversion['config'][$i]['formtype'] = 'textbox';
$modversion['config'][$i]['valuetype'] = 'text';
$modversion['config'][$i]['default'] = '閩南語';

++$i;
$modversion['config'][$i]['name'] = 'es_tt_teacher_group';
$modversion['config'][$i]['title'] = '_MI_ESTUDENTS_CONFIG_TITLE1';
$modversion['config'][$i]['description'] = '_MI_ESTUDENTS_CONFIG_DESC1';
$modversion['config'][$i]['formtype'] = 'group';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = 4;                    //配合校園網站輕鬆架，預設值

++$i;
$modversion['config'][$i]['name'] = 'es_tt_ex_teach_kind';
$modversion['config'][$i]['title'] = '_MI_ES_TT_CONFIG_TEAKIND';
$modversion['config'][$i]['description'] = '_MI_ES_TT_CONFIG_DESC_TEAKIND';
$modversion['config'][$i]['formtype'] = 'textbox';
$modversion['config'][$i]['valuetype'] = 'text';
$modversion['config'][$i]['default'] = '教育部增置教師,超鐘點教師,管控教師';

++$i;
$modversion['config'][$i]['name'] = 'es_tt_over_list';
$modversion['config'][$i]['title'] = '_MI_ES_TT_CONFIG_OVER';
$modversion['config'][$i]['description'] = '_MI_ES_TT_CONFIG_DESC_OVER';
$modversion['config'][$i]['formtype'] = 'textbox';
$modversion['config'][$i]['valuetype'] = 'text';
$modversion['config'][$i]['default'] = '超鐘點';

++$i;
$modversion['config'][$i]['name'] = 'es_tt_week_D';
$modversion['config'][$i]['title'] = '_MI_ES_TT_CONFIG_TWEEKD';
$modversion['config'][$i]['description'] = '_MI_ES_TT_CONFIG_DWEEKD';
$modversion['config'][$i]['formtype'] = 'yesno';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = false;

++$i;
$modversion['config'][$i]['name'] = 'es_tt_holiday_kw';
$modversion['config'][$i]['title'] = '_MI_ES_TT_CONFIG_HOLIDAY_K';
$modversion['config'][$i]['description'] = '_MI_ES_TT_CONFIG_D_HOLIDAY_K';
$modversion['config'][$i]['formtype'] = 'textbox';
$modversion['config'][$i]['valuetype'] = 'text';
$modversion['config'][$i]['default'] = '放假,補假';

++$i;
$modversion['config'][$i]['name'] = 'es_tt_single_mode';
$modversion['config'][$i]['title'] = '_MI_ES_TT_CONFIG_SINGLE_MODE';
$modversion['config'][$i]['description'] = '_MI_ES_TT_CONFIG_D_SINGLE_MODE';
$modversion['config'][$i]['formtype'] = 'yesno';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = false;

++$i;
$modversion['config'][$i]['name'] = 'es_tt_sm_class_num';
$modversion['config'][$i]['title'] = '_MI_ES_TT_CONFIG_SM_CLASS_NUM';
$modversion['config'][$i]['description'] = '_MI_ES_TT_CONFIG_D_SM_CLASS_NUM';
$modversion['config'][$i]['formtype'] = 'textbox';
$modversion['config'][$i]['valuetype'] = 'text';
$modversion['config'][$i]['default'] = '2,2,2,2,3,3';


++$i;
$modversion['config'][$i]['name'] = 'es_tt_sm__OpenYear';
$modversion['config'][$i]['title'] = '_MI_ES_TT_CONFIG_OpenYear';
$modversion['config'][$i]['description'] = '_MI_ES_TT_CONFIG_D_OpenYear';
$modversion['config'][$i]['formtype'] = 'textbox';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = '';

++$i;
$modversion['config'][$i]['name'] = 'es_tt_sm__OpenSemester';
$modversion['config'][$i]['title'] = '_MI_ES_TT_CONFIG_OpenSemester';
$modversion['config'][$i]['description'] = '_MI_ES_TT_CONFIG_D_OpenSemester';
$modversion['config'][$i]['formtype'] = 'textbox';
$modversion['config'][$i]['valuetype'] = 'int';
$modversion['config'][$i]['default'] = '';

