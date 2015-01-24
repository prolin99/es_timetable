<?php

function xoops_module_update_es_timetable(&$module, $old_version) {
    GLOBAL $xoopsDB;

    if(!chk_add_subject_scope()) go_update_add_subject_scope();

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


?>