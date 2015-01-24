<?php
function xoops_module_install_es_timetable(&$module) {
 	global $xoopsDB;
 	$sql="select count(*) as cc from ".$xoopsDB->prefix("es_timetable_subject");
  	$result=$xoopsDB->query($sql);
  	$row=$xoopsDB->fetchArray($result) ;
  	echo  $row['cc'] ;
  	if($row['cc']<= 0 ) {
		$sql = "INSERT INTO ".$xoopsDB->prefix("es_timetable_subject")." (`subject_id`, `subject_name`, `subject_school`, `subject_kind`, `enable` ,subject_scope) VALUES 
		(1, '國語', '', 'subject', '1' ,'語文領域'),
		(2, '本土語言', '', 'subject', '1','語文領域'),
		(3, '數學', '', 'scope', '1','數學領域'),
		(4, '生活', '', 'scope', '1','生活領域'),
		(5, '健康與體育', '', 'scope', '1','健康與體育領域'),
		(6, '綜合活動', '', 'scope', '1','綜合活動領域'),
		(7, '彈性課程', '', 'scope', '1','彈性課程'),
		(8, '英語', '', 'subject', '1','語文領域'),
		(9, '自然', '', 'scope', '1','自然與生活科技領域'),
		(10, '健康', '', 'subject', '1','健康與體育領域'),
		(11, '體育', '', 'subject', '1','健康與體育領域'),
		(12, '社會', '', 'scope', '1','社會領域'),
		(13, '視覺藝術', '', 'subject', '1','藝術與人文領域'),
		(14, '音樂', '', 'subject', '1','藝術與人文領域'),
		(15, '書法', '', 'subject', '1','彈性課程'),
		(16, '國語-彈', '', 'subject', '1','彈性課程'),
		(17, '英語-彈', '', 'subject', '1','彈性課程'),
		(18, '數學-彈', '', 'subject', '1','彈性課程'),
		(19, '電腦', '', 'subject', '1','彈性課程') ; " ;
	}
	return true;
}

 

?>
