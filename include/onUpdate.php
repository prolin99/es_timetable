<?php

function xoops_module_update_es_timetable(&$module, $old_version) {
    GLOBAL $xoopsDB;

    //領域
    if(!chk_add_subject_scope()) go_update_add_subject_scope();

    //單雙週排課
    if(!chk_add_week_d()) go_update_add_week_d();    

    return true;
}

 
//----------------------------------------------------------------------------------------
function chk_add_subject_scope(){
  global $xoopsDB;
  $sql="select count(`subject_scope`)  from ".$xoopsDB->prefix("es_timetable_subject");
  $result=$xoopsDB->query($sql);
  if(empty($result)) return false;
  return true;
}

function go_update_add_subject_scope(){
  global $xoopsDB;
  $sql= " ALTER TABLE  ".$xoopsDB->prefix("es_timetable_subject") ." ADD `subject_scope` varchar(80) DEFAULT NULL " ;

  $xoopsDB->queryF($sql)  ;
 
}

//----------------------------------------------------------------------------------------
function chk_add_week_d(){
  global $xoopsDB;
  $sql="select count(`week_d`)  from ".$xoopsDB->prefix("es_timetable");
  $result=$xoopsDB->query($sql);
  if(empty($result)) return false;
  return true;
}

function go_update_add_week_d(){
  global $xoopsDB;
  $sql= " ALTER TABLE  ".$xoopsDB->prefix("es_timetable") ." ADD `week_d` TINYINT NOT NULL DEFAULT '0' " ;

  $xoopsDB->queryF($sql)  ;
 
}


?>