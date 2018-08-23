<?php
//  ------------------------------------------------------------------------ //
// 本模組由 prolin 製作
// 製作日期：2014-06-16
// $Id:$
// ------------------------------------------------------------------------- //
include_once XOOPS_ROOT_PATH."/modules/tadtools/language/{$xoopsConfig['language']}/modinfo_common.php";

define("_MI_ESTIMETABLE_NAME","課表");
define("_MI_ESTIMETABLE_AUTHOR","prolin (prolin@tn.edu.tw)");
define("_MI_ESTIMETABLE_CREDITS","prolin");
define("_MI_ESTIMETABLE_DESC","課表設定");

define("_MI_ES_TT_CONFIG_T1","每周天數");
define("_MI_ES_TT_CONFIG_D1","星期一到星期幾");

define("_MI_ES_TT_CONFIG_T2","可排節數");
define("_MI_ES_TT_CONFIG_D2","可排節數");
define("_MI_ES_TT_CONFIG_T3","上午節數");
define("_MI_ES_TT_CONFIG_D3","上午節數");
define("_MI_ES_TT_CONFIG_T4","級任輸入課表");
define("_MI_ES_TT_CONFIG_D4","級任是否可以輸入課表");
define("_MI_ES_TT_CONFIG_T5","年級");
define("_MI_ES_TT_CONFIG_D5","以逗號分隔");

define("_MI_ES_TT_CONFIG_TFirst_c","是否出現自修");
define("_MI_ES_TT_CONFIG_DFirst_c","第一節是自修，而且未排課，要出現早自習等指定字串(配合下一項名稱設定)");

define("_MI_ES_TT_CONFIG_TFirst","早自修的出現名稱");
define("_MI_ES_TT_CONFIG_DFirst","早自習，要出現的名稱為：");

define("_MI_ES_TT_CONFIG_T7","節次中文名稱");
define("_MI_ES_TT_CONFIG_D7","導師時間/早自習,第一節,午休等排課節次,以逗號分隔");

define("_MI_ES_TT_CONFIG_T8","特殊班級");
define("_MI_ES_TT_CONFIG_D8","格式：班名_導師名(底線分格)，例如資源班_林**,智優三忠_王**  等,以逗號分隔");

define("_MI_ES_TT_CONFIG_Time","每節時間");
define("_MI_ES_TT_CONFIG_DTime","每節上課時間，如 8:40~9:10,以逗號分隔");

define("_MI_ES_TT_CONFIG_Tlocal","本土語言預設值");
define("_MI_ES_TT_CONFIG_Dlocal","匯出教育部課表預設值，閩南語  客語  原住民語");

define("_MI_ES_TT_CONFIG_TEAKIND","外聘教師身份別");
define("_MI_ES_TT_CONFIG_DESC_TEAKIND","外聘教師類別以逗號分隔(在任課身份別設定，方便製作簽名冊)，如：教育部增置教師,超鐘點教師,管控教師");

define("_MI_ES_TT_CONFIG_OVER","校內減授課經費來源類別");
define("_MI_ES_TT_CONFIG_DESC_OVER","校內教師減授課，經費來源類別以逗號分隔，如：超鐘點,圖書教師鐘點費,輔導教師減課鐘點費,縣領域輔導團減課鐘點費");

define("_MI_ES_TT_CONFIG_TWEEKD","單雙週排課");
define("_MI_ES_TT_CONFIG_DWEEKD","是否單雙週排課");

define("_MI_ES_TT_CONFIG_HOLIDAY_K","Tad_cal 行事曆中假日使用關鍵字");
define("_MI_ES_TT_CONFIG_D_HOLIDAY_K","如放假,補假 等，使用逗號做分隔，提供簽到、超鐘點表格使用");

define("_MI_ES_TT_CONFIG_SINGLE_MODE","獨立模式，不使用單位名冊班級");
define("_MI_ES_TT_CONFIG_D_SINGLE_MODE","如果沒有學生資料，不使用單位名冊模組(仍需安裝)，可以下方做班級數指定");

define("_MI_ES_TT_CONFIG_SM_CLASS_NUM","獨立模式，每年級班級數");
define("_MI_ES_TT_CONFIG_D_SM_CLASS_NUM","如 2,3,2,2,2,2 ，每年級班級數，逗號分隔 ");

define("_MI_ES_TT_CONFIG_OpenYear","可查看課表的年度");
define("_MI_ES_TT_CONFIG_D_OpenYear","可以查看的年度，正在輸入的課表不被查看。");

define("_MI_ES_TT_CONFIG_OpenSemester","可查看課表的學期");
define("_MI_ES_TT_CONFIG_D_OpenSemester","1或2，可以查看的學期，正在輸入的課表不被查看。");

?>
