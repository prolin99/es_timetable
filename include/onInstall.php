<?php
function xoops_module_install_es_timetable(&$module) {
 	global $xoopsDB;
 	$sql="select count(*) as cc from ".$xoopsDB->prefix("es_timetable_subject");
  	$result=$xoopsDB->query($sql);
  	$row=$xoopsDB->fetchArray($result) ;
  	echo  $row['cc'] ;
  	if($row['cc']<= 0 ) {
		$sql = "INSERT INTO ".$xoopsDB->prefix("es_timetable_subject")." (`subject_id`, `subject_name`, `subject_school`, `subject_kind`, `enable`) VALUES 
		(1, '本國語文', '', 'subject', '1'),
		(2, '本土語文', '', 'subject', '1'),
		(3, '數學', '', 'scope', '1'),
		(4, '生活', '', 'scope', '1'),
		(5, '健康與體育', '', 'scope', '1'),
		(6, '綜合活動', '', 'scope', '1'),
		(7, '彈性課程', '', 'scope', '1'),
		(8, '英語', '', 'subject', '1'),
		(9, '自然與生活科技', '', 'scope', '1'),
		(10, '健康與體育_健', '', 'subject', '1'),
		(11, '健康與體育_體', '', 'subject', '1'),
		(12, '社會', '', 'scope', '1'),
		(13, '視覺藝術', '', 'subject', '1'),
		(14, '音樂', '', 'subject', '1'),
		(15, '彈性課程', '', 'subject', '1'),
		(16, '彈性-書法', '', 'subject', '1'),
		(17, '彈性-國語', '', 'subject', '1'),
		(18, '彈性-英語', '', 'subject', '1'),
		(19, '彈性-數學', '', 'subject', '1'),
		(20, '彈性-資訊', '', 'subject', '1'),
		(21, '彈性-音樂', '', 'subject', '1'),
		(22, '輔導活動', '', 'scope', '1'),
		(23, '專題研究', '', 'scope', '1'); " ;
	}
	return true;
}

 

?>
