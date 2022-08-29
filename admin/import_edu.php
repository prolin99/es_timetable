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

    if ($_POST['sect_remove']){
        //echo 'cccc' ;
        //清空科目設定
        $sql= "TRUNCATE TABLE   " . $xoopsDB->prefix("es_timetable_subject")  ;
        $result = $xoopsDB->queryF($sql) or redirect_header($_SERVER['PHP_SELF'],3, $xoopsDB->error());


        $sql= "TRUNCATE TABLE   " . $xoopsDB->prefix("es_timetable_subject_year")  ;
        $result = $xoopsDB->queryF($sql) or redirect_header($_SERVER['PHP_SELF'],3, $xoopsDB->error());
        $message = "清空舊科目名稱</br>" ;

        $sql= "TRUNCATE TABLE   " . $xoopsDB->prefix("es_timetable_teacher")  ;
        $result = $xoopsDB->queryF($sql) or redirect_header($_SERVER['PHP_SELF'],3, $xoopsDB->error());
        $message .= "清空舊任課教師名冊</br>" ;
    }

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
	//$highestRow = $sheet->getHighestRow(); // 取得總列數
	//取得最後行數，改成
	$maxCell = $PHPExcel->getActiveSheet()->getHighestRowAndColumn();
	$highestRow = $maxCell['row'] ;

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

		if ($v[1]<>""){
            $sql=  "INSERT INTO " . $xoopsDB->prefix("es_timetable_tmp") .
    			           " VALUES (0 , '{$v[0]}' , '{$v[1]}' , '{$v[2]}', '{$v[3]}', '{$v[4]}', '{$v[5]}', '{$v[6]}', '{$v[7]}', '{$v[8]}', '{$v[9]}', '{$v[10]}', '{$v[11]}' ) " ;

            //echo "$sql <br>" ;
            $result = $xoopsDB->query($sql) ;
            if ($xoopsDB->error() )
                echo  $xoopsDB->error() . $sql ."<br />" ;
        }


	}

}

function add_new_teacher($teacher_name){
    global $xoopsDB ;
    $insert_sql =  " INSERT INTO " . $xoopsDB->prefix("es_timetable_teacher") .
    "  (`teacher_id`, `user_id`, `name`  )
    VALUES ('0' , '0' , '{$teacher_name}' )         " ;
    //echo $insert_sql ." <br> ";
    $result2 = $xoopsDB->query($insert_sql) ;

    $sql = "select teacher_id , name from "  . $xoopsDB->prefix("es_timetable_teacher") ." where name='{$teacher_name}' " ;
    $result = $xoopsDB->query($sql) or die($sql.'<br>'.$xoopsDB->error());
    while ($row = $xoopsDB->fetchArray($result)) {
        return  $row['teacher_id'] ;
    }
}

function do_import( $y , $s){
    global $xoopsDB , $DEF_SET ,$message   ;
    $sql= "TRUNCATE TABLE   " . $xoopsDB->prefix("es_timetable")  ;
   	$result = $xoopsDB->queryF($sql) or redirect_header($_SERVER['PHP_SELF'],3, $xoopsDB->error());    
    /*
   	//清空學資料庫中  es_timetable_subject , es_timetable_subject_year ,es_timetable , es_timetable_teacher
   	$sql= "TRUNCATE TABLE   " . $xoopsDB->prefix("es_timetable_subject")  ;
   	$result = $xoopsDB->queryF($sql) or redirect_header($_SERVER['PHP_SELF'],3, $xoopsDB->error());

    $sql= "TRUNCATE TABLE   " . $xoopsDB->prefix("es_timetable_subject_year")  ;
   	$result = $xoopsDB->queryF($sql) or redirect_header($_SERVER['PHP_SELF'],3, $xoopsDB->error());

    $sql= "TRUNCATE TABLE   " . $xoopsDB->prefix("es_timetable_teacher")  ;
   	$result = $xoopsDB->queryF($sql) or redirect_header($_SERVER['PHP_SELF'],3, $xoopsDB->error());
    */
    //由舊教師名冊中取得教師姓名及 代號
    $sql = "select teacher_id , name from "  . $xoopsDB->prefix("es_timetable_teacher")  ;
    $result = $xoopsDB->query($sql) or die($sql.'<br>'.$xoopsDB->error());
    while ($row = $xoopsDB->fetchArray($result)) {
        $tea_i = $row['name'] ;
        $edu['teacher'][$tea_i]= $row['teacher_id'] ;
    }


    //教師
    $message .=  "匯入教師名單 <br>" ;
    $sql = "select teacher  from "  . $xoopsDB->prefix("es_timetable_tmp") . " group by teacher, teacher_id " ;

    $result = $xoopsDB->query($sql) or die($sql.'<br>'.$xoopsDB->error());
    while ($row = $xoopsDB->fetchArray($result)) {
        $tea_i = $row['teacher'];
        //找不到，要新增
        if (! $edu['teacher'][$row['teacher']] ) {
            $nid = add_new_teacher($row['teacher']) ;
            $edu['teacher'][$tea_i]= $nid ;
        }
    }

    //科目 --原來的
    $sql = "select subject_id , s_subject from "  . $xoopsDB->prefix("es_timetable_subject") .' order by subject_id' ;
    $result = $xoopsDB->query($sql) or die($sql.'<br>'.$xoopsDB->error());
    while ($row = $xoopsDB->fetchArray($result)) {
        $edu['subject'][$row['s_subject']] = $row['subject_id'] ;
        $get_max['subid'] = $row['subject_id'] ;
    }

    //匯入 新科目
    $message .= "科目設定完成 <br>" ;
    $sql = "select subject_mode , subject_class, subject , subject_short from "  . $xoopsDB->prefix("es_timetable_tmp") . " group by subject_class, subject , subject_short  order by  subject_class, subject   " ;

    $result_sect = $xoopsDB->query($sql) or die($sql.'<br>'.$xoopsDB->error());
    while ($row = $xoopsDB->fetchArray($result_sect)) {
        if (!$edu['subject'][$row['subject']]) {
            $new_subject= $row['subject'] ;
            //科目不存在 ，新增
            $insert_sql =  " INSERT INTO " . $xoopsDB->prefix("es_timetable_subject") .
            "   (`subject_id`, `subject_name`, `subject_school`, `subject_kind`, `enable`, `subject_scope`,e_subject ,s_subject) VALUES
            ( '0' ,  '{$row['subject_short']}' , '', 'subject', '1', '{$row['subject_mode']}' ,'{$row['subject_class']}' , '$new_subject' )
            " ;
            //echo $insert_sql ." <br> ";
            $result2 = $xoopsDB->query($insert_sql) ;

            $sql = "select subject_id , s_subject from "  . $xoopsDB->prefix("es_timetable_subject") . " where  s_subject = '$new_subject' " ;
            $result3 = $xoopsDB->query($sql) or die($sql.'<br>'.$xoopsDB->error());

            //echo "--" . $sql ." <br> ";
            while ($row2 = $xoopsDB->fetchArray($result3)) {
                $tid = $row2['subject_id'] ;
                $edu['subject'][$new_subject] = $row2['subject_id'] ;

                //新科目的年級設定（全年級）
                foreach($DEF_SET['grade'] as $ity=>$ty) {
                    $insert_sql =  " INSERT INTO " . $xoopsDB->prefix("es_timetable_subject_year") .
                    "(`y_id`, `grade`, `subject_id`) VALUES (0, $ty , $tid) " ;
                    //echo '0000-' . $insert_sql ." <br> ";
                    $result4 = $xoopsDB->query($insert_sql) ;
                }
            }

        }
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
        $c_id = mb_substr($row['class_id'],1 ,2 , 'UTF-8' )+0 ;
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

        $ss_subject = $row['subject'];

        $ss_id  = $edu['subject'][$ss_subject] ;
        if ($ss_id ==0 ){
            echo '*****ss_id .0.' . $ss_subject ."<br>" ;
            var_dump($edu['subject']) ;
        }
        $insert_sql =  " INSERT INTO " . $xoopsDB->prefix("es_timetable") .
        " ( course_id , school_year ,semester , class_id ,teacher , day ,sector , ss_id ,room )
        VALUES
        ( '0' ,  '$y' , '$s',   '$c_ord'   ,  '$tea_id' ,   '$day' ,  '$sect' ,'$ss_id' ,''    )
        " ;
		//echo $tea_i . '---' . $insert_sql . "<br>" ;
        $result2 = $xoopsDB->query($insert_sql) ;

    }





    $message .= "匯入完成<br>" ;
    $message .= "再進入 <a href='set_teacher.php'>任課教師</a> 頁中檢查教師名冊是否完整。" ;

}


/*-----------秀出結果區--------------*/

$xoopsTpl->assign('message', $message);
$xoopsTpl->assign('tab', $tab);

include_once 'footer.php';
