<?php
//  ------------------------------------------------------------------------ //
// 本模組由 prolin 製作
// 製作日期：2014-07-20
// $Id:$
// ------------------------------------------------------------------------- //
/*-----------引入檔案區--------------*/
$xoopsOption['template_main'] = 'es_timet_import_edu.html';
include_once "header.php";
include_once "../function.php";


//匯入判別


	if ($_FILES['edutable']['name'] ) {

		$file_up = XOOPS_ROOT_PATH."/uploads/" .$_FILES['edutable']['name'] ;
		copy($_FILES['edutable']['tmp_name'] , $file_up );

		$main="開始匯入" . $file_up .'<br>';

		//副檔名
		$file_array= preg_split('/[.]/', $_FILES['edutable']['name'] ) ;
		$ext= strtoupper(array_pop($file_array)) ;

		if ($ext=='XLSX')
			import_edu_excel($file_up , 2007) ;

		unlink($file_up)  ;

        //進行各資料表內容
        do_import($_POST['year'] , $_POST['semester']) ;
 	}



//excel 格式
function import_edu_excel($file_up,$ver=2007) {

    global $xoopsDB,$c_year ,$xoopsTpl ,$message ;

    //清空教育部暫存檔
    $sql= "TRUNCATE TABLE   " . $xoopsDB->prefix("es_timetable_tmp")  ;
    $result = $xoopsDB->queryF($sql) or redirect_header($_SERVER['PHP_SELF'],3, $xoopsDB->error());

	include_once '../../tadtools/PHPExcel/IOFactory.php';

	$reader = PHPExcel_IOFactory::createReader('Excel2007');

	$PHPExcel = $reader->load( $file_up ); // 檔案名稱
	$sheet = $PHPExcel->getSheet(0); // 讀取第一個工作表(編號從 0 開始)
	$highestRow = $sheet->getHighestRow(); // 取得總列數

	// 一次讀取一列
	for ($row = 2; $row <= $highestRow; $row++) {
		$v=array();
		//讀取一列中的每一格
		for ($col = 0; $col < 12; $col++) {
            try {
                $val =  $sheet->getCellByColumnAndRow($col, $row)->getCalculatedValue();
            } catch (\Exception $e) {
                $val = '' ;
            }

			if(!get_magic_quotes_runtime()) {
				$v[$col]=addSlashes($val);
			}else{
				$v[$col]= $val ;
			}

		}

		if ($v[1]){
                $sql=  "INSERT INTO " . $xoopsDB->prefix("es_timetable_tmp") .
    			           " VALUES ('0' , '{$v[0]}' , '{$v[1]}' , '{$v[2]}', '{$v[3]}', '{$v[4]}', '{$v[5]}', '{$v[6]}', '{$v[7]}', '{$v[8]}', '{$v[9]}', '{$v[10]}', '{$v[11]}' ) " ;
        }

            //echo "$sql <br>" ;
			$result = $xoopsDB->query($sql) ;
            if ($xoopsDB->error() )
                 echo  $xoopsDB->error() . $sql ."<br />" ;
		}

}


function do_import( $y , $s){
    global $xoopsDB , $DEF ,$message   ;
   	//清空學資料庫中  es_timetable_subject , es_timetable_subject_year ,es_timetable , es_timetable_teacher
   	$sql= "TRUNCATE TABLE   " . $xoopsDB->prefix("es_timetable_subject")  ;
   	$result = $xoopsDB->queryF($sql) or redirect_header($_SERVER['PHP_SELF'],3, $xoopsDB->error());

    $sql= "TRUNCATE TABLE   " . $xoopsDB->prefix("es_timetable_subject_year")  ;
   	$result = $xoopsDB->queryF($sql) or redirect_header($_SERVER['PHP_SELF'],3, $xoopsDB->error());

    $sql= "TRUNCATE TABLE   " . $xoopsDB->prefix("es_timetable")  ;
   	$result = $xoopsDB->queryF($sql) or redirect_header($_SERVER['PHP_SELF'],3, $xoopsDB->error());

    $sql= "TRUNCATE TABLE   " . $xoopsDB->prefix("es_timetable_teacher")  ;
   	$result = $xoopsDB->queryF($sql) or redirect_header($_SERVER['PHP_SELF'],3, $xoopsDB->error());

    //教師
    $message .=  "匯入教師名單 <br>" ;
    $sql = "select teacher  from "  . $xoopsDB->prefix("es_timetable_tmp") . " group by teacher, teacher_id " ;

    $tid=0 ;
    $result = $xoopsDB->query($sql) or die($sql.'<br>'.$xoopsDB->error());
    while ($row = $xoopsDB->fetchArray($result)) {
        $tid++ ;
        $tea_i = $row['teacher']. $row['teacher_id'] ;
        $insert_sql =  " INSERT INTO " . $xoopsDB->prefix("es_timetable_teacher") .
        "  (`teacher_id`, `user_id`, `name`  )
         VALUES ('$tid' , '$tid' , '{$row['teacher']}' )
         " ;
         $result2 = $xoopsDB->query($insert_sql) ;
         $edu['teacher'][$tea_i]= $tid ;
    }

    //科目
    $message .= "科目設定完成 <br>" ;
    $sql = "select subject_class, subject , subject_short from "  . $xoopsDB->prefix("es_timetable_tmp") . " group by subject_class, subject , subject_short  order by  subject_class, subject   " ;

    $tid=0 ;
    $result = $xoopsDB->query($sql) or die($sql.'<br>'.$xoopsDB->error());
    while ($row = $xoopsDB->fetchArray($result)) {
        $tid++ ;
        $insert_sql =  " INSERT INTO " . $xoopsDB->prefix("es_timetable_subject") .
        "   (`subject_id`, `subject_name`, `subject_school`, `subject_kind`, `enable`, `subject_scope`,e_subject ,s_subject) VALUES
        ( '$tid' ,  '{$row['subject_short']}' , '', 'subject', '1', '{$row['subject_class']}' , '{$row['subject']}', '{$row['subject_short']}')
         " ;
         $result2 = $xoopsDB->query($insert_sql) ;
         $edu['subject'][$row['subject_short']] = $tid ;
		 $get_max['subid'] = $tid ;
    }



    //課表
    $cy['一年級'] = 1 ;
    $cy['二年級'] = 2 ;
    $cy['三年級'] = 3 ;
    $cy['四年級'] = 4 ;
    $cy['五年級'] = 5 ;
    $cy['六年級'] = 6 ;
    $cy['七年級'] = 7 ;
    $cy['八年級'] = 8 ;
    $cy['九年級'] = 9 ;

    $cw['週一'] = 1 ;
    $cw['週二'] = 2 ;
    $cw['週三'] = 3 ;
    $cw['週四'] = 4 ;
    $cw['週五'] = 5 ;
    $cw['週六'] = 6 ;
    $cw['週日'] = 7 ;
    $cs['第一節'] =1  ;
    $cs['第二節'] =2  ;
    $cs['第三節'] =3  ;
    $cs['第四節'] =4  ;
    $cs['第五節'] =5  ;
    $cs['第六節'] =6  ;
    $cs['第七節'] =7  ;
    $cs['第八節'] =8  ;
    $cs['第九節'] =9  ;
    $cs['第十節'] =10  ;

    $message .= "寫入課表 <br>" ;
    $sql = "select *  from "  . $xoopsDB->prefix("es_timetable_tmp") ;


    $tid=0 ;
    $result = $xoopsDB->query($sql) or die($sql.'<br>'.$xoopsDB->error());
    while ($row = $xoopsDB->fetchArray($result)) {
        $tid++ ;
        $c_id = mb_substr($row['class_id'],1 ,2)+0 ;
		if ($get_max['min_y'] ==0)
			$get_max['min_y'] = $cy[$row['class_year']];
		if ($get_max['min_y'] > $cy[$row['class_year']])
			$get_max['min_y'] = $cy[$row['class_year']] ;

		if ($get_max['max_y'] < $cy[$row['class_year']])
			$get_max['max_y'] = $cy[$row['class_year']] ;

        $c_ord = $cy[$row['class_year']] *100+  $c_id ;
		$tea_i = $row['teacher']. $row['teacher_id'] ;
        $tea_id = $edu['teacher'][$tea_i] ;
        $day =$cw[$row['weekday']] ;

        $sect =$cs[$row['sect']] ;

        $ss_id  =$edu['subject'][$row['subject_short']] ;
        $insert_sql =  " INSERT INTO " . $xoopsDB->prefix("es_timetable") .
        " ( course_id , school_year ,semester , class_id ,teacher , day ,sector , ss_id ,room )
         VALUES
        ( '0' ,  '$y' , '$s',   '$c_ord'   ,  '$tea_id' ,   '$day' ,  '$sect' ,'$ss_id' ,''    )
         " ;
		 //echo $tea_i . '---' . $insert_sql . "<br>" ;
         $result2 = $xoopsDB->query($insert_sql) ;

    }


	//各年級使用科目
    $message .= "各年級使用科目設定 <br>" ;
	//echo $get_max['min_y'] .' - ' .  $get_max['max_y']  .' - ' .  $get_max['subid'] ;

	for ($ty=$get_max['min_y'] ; $ty<=$get_max['max_y'] ; $ty++    ){
		for ($tid=1 ; $tid<=$get_max['subid'] ; $tid++ ) {
			$insert_sql =  " INSERT INTO " . $xoopsDB->prefix("es_timetable_subject_year") .
			"(`y_id`, `grade`, `subject_id`) VALUES (0, $ty , $tid) " ;
			$result2 = $xoopsDB->query($insert_sql) ;
		}
    }


    $message .= "匯入完成<br>" ;

}


/*-----------秀出結果區--------------*/

$xoopsTpl->assign('message', $message);
$xoopsTpl->assign('tab', $tab);

include_once 'footer.php';
