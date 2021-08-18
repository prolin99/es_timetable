<?php
use XoopsModules\Tadtools\Utility;

function xoops_module_update_es_timetable(&$module, $old_version)
{
    global $xoopsDB;
    //級任課表保護，教學組排級任科目，級任不可刪除
    if (!chk_add_class_power_chk()) {
        go_update_add_class_power_chk();
    }

    //edu 暫存表
    if (!chk_add_edu_tmp()) {
        go_update_add_edu_tmp();
    }

    //領域
    if (!chk_add_subject_scope()) {
        go_update_add_subject_scope();
    }

    //單雙週排課
    if (!chk_add_week_d()) {
        go_update_add_week_d();
    }

    //教育部科目、校定科目
    if (!chk_add_subject_esubject()) {
        go_update_add_subject_esubject();
    }

    //科目代碼欄位tinyint 改 int
    if (!chk_subject_int()) {
        go_update_subject_int();
    }

    return true;
}


//------------------------------------
//欄位加寬
function chk_subject_int(){
    global $xoopsDB;
    $sql = 'select count(`subject_id_size`)  from '.$xoopsDB->prefix('es_timetable_subject');
    $result = $xoopsDB->query($sql);
    if (empty($result)) {
        return false;
    }

    return true;
}

function go_update_subject_int()
{
    global $xoopsDB;
    $sql = ' ALTER TABLE  '.$xoopsDB->prefix('es_timetable_subject'). "  CHANGE `subject_id` `subject_id` INT UNSIGNED NOT NULL AUTO_INCREMENT , ADD `subject_id_size` int NOT NULL DEFAULT '0'  ";
    $xoopsDB->queryF($sql);

    $sql = ' ALTER TABLE  '.$xoopsDB->prefix('es_timetable_subject_year'). "  CHANGE `y_id` `y_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,CHANGE `subject_id` `subject_id` INT NOT NULL DEFAULT '0'  ";
    $xoopsDB->queryF($sql);
}

//----------------------------------------------------------------------------------------
function chk_add_subject_esubject()
{
    global $xoopsDB;
    $sql = 'select count(`e_subject`)  from '.$xoopsDB->prefix('es_timetable_subject');
    $result = $xoopsDB->query($sql);
    if (empty($result)) {
        return false;
    }

    return true;
}

function go_update_add_subject_esubject()
{
    global $xoopsDB;
    $sql = ' ALTER TABLE  '.$xoopsDB->prefix('es_timetable_subject').' ADD `e_subject` varchar(80) DEFAULT NULL ,  ADD `s_subject` varchar(80) DEFAULT NULL  ';

    $xoopsDB->queryF($sql);
}

//----------------------------------------------------------------------------------------
function chk_add_subject_scope()
{
    global $xoopsDB;
    $sql = 'select count(`subject_scope`)  from '.$xoopsDB->prefix('es_timetable_subject');
    $result = $xoopsDB->query($sql);
    if (empty($result)) {
        return false;
    }

    return true;
}

function go_update_add_subject_scope()
{
    global $xoopsDB;
    $sql = ' ALTER TABLE  '.$xoopsDB->prefix('es_timetable_subject').' ADD `subject_scope` varchar(80) DEFAULT NULL ';

    $xoopsDB->queryF($sql);
}

//----------------------------------------------------------------------------------------
function chk_add_week_d()
{
    global $xoopsDB;
    $sql = 'select count(`week_d`)  from '.$xoopsDB->prefix('es_timetable');
    $result = $xoopsDB->query($sql);
    if (empty($result)) {
        return false;
    }

    return true;
}

function go_update_add_week_d()
{
    global $xoopsDB;
    $sql = ' ALTER TABLE  '.$xoopsDB->prefix('es_timetable')." ADD `week_d` TINYINT NOT NULL DEFAULT '0' ";

    $xoopsDB->queryF($sql);
}


//----------------------------------------------------------------------------------------
function chk_add_edu_tmp()
{
    global $xoopsDB;
    $sql = 'select count(`id`)  from '.$xoopsDB->prefix('es_timetable_tmp');
    $result = $xoopsDB->query($sql);
    if (empty($result)) {
        return false;
    }

    return true;
}

function go_update_add_edu_tmp()
{
    global $xoopsDB;
    $sql = '  CREATE TABLE  '.$xoopsDB->prefix('es_timetable_tmp').
    " (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `weekday` varchar(30) NOT NULL,
      `sect` varchar(30) NOT NULL,
      `class_year` varchar(30) NOT NULL,
      `class_id` varchar(30) NOT NULL,
      `teacher` varchar(30) NOT NULL,
      `teacher_id` varchar(30) NOT NULL,
      `subject_mode` varchar(30) NOT NULL,
      `subject_class` varchar(30) NOT NULL,
      `subject` varchar(30) NOT NULL,
      `subject_lang` varchar(30) NOT NULL,
      `subject_short` varchar(30) NOT NULL,
      `in_week` varchar(30) NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=MyISAM COMMENT='教育部匯入課表'
    ";

    $xoopsDB->queryF($sql);
}



//----------------------------------------------------------------------------------------
function chk_add_class_power_chk()
{
    global $xoopsDB;
    $sql = 'select count(`self_chk`)  from '.$xoopsDB->prefix('es_timetable_tmp');
    $result = $xoopsDB->query($sql);
    if (empty($result)) {
        return false;
    }

    return true;
}

function go_update_add_class_power_chk()
{
    global $xoopsDB;
    $sql = ' ALTER TABLE  '.$xoopsDB->prefix('es_timetable')." ADD `self_chk` TINYINT NOT NULL DEFAULT '0' ";

    $xoopsDB->queryF($sql);
}