<{$toolbar}>
<style>
.groupbox {

    border: 1px dotted gray;
    background-color: #EEE;
    <{if  ($DEF_SET.es_tt_week_D)}>
      height:7em;
    <{else}>
      height:5em;
    <{/if}>

}
.weekd{
  font-size: 0.8em;
}

</style>

 <h3><{$data.n_y}> 學年度<{$data.n_s}> 學期</h3>





        <div class="row" >
          <label for="class_id" class="col-md-2 control-label">班級：</label>
          <div  class="col-md-4">
    	  <{html_options name=class_id id=class_id  class="form-control" options=$data.class_list_c  selected=$data.select_class_id  onchange="class_change();"    }>
    	  </div>
          <label for="class_id" class="col-md-3 control-label">級任：<span id="class_teacher_name"></span></label>

        </div>



      <table class="table table-bordered">
      <tr>
      <th scope="col" >節</th>
      <{section name=di  start=1  loop=$DEF_SET.days_sm  step=1  }>
      <th scope="col" ><{$DEF_SET.week[$smarty.section.di.index]}></th>
      <{/section }>
      </tr>
      <{section name=si  start=1  loop=$DEF_SET.sects_sm    step=1  }>
      <tr>
          <th scope="row">
            <{$DEF_SET.sects_cht_list[$smarty.section.si.index]}><br/>
            <{$DEF_SET.time_list[$smarty.section.si.index]}>
          </th>
          <{section name=di  start=1  loop=$DEF_SET.days_sm    step=1  }>
          <td style="width:16%">
          	<div   data_ref="sect_<{$smarty.section.di.index}>_<{$smarty.section.si.index}>" id="sect_<{$smarty.section.di.index}>_<{$smarty.section.si.index}>" ><{$group}><br /><br /></div>
          </td>
          <{/section }>
      </tr>
      <{/section }>
      </table>





<SCRIPT type="text/javascript">
//指定教師
var now_teacher_array =[] ;
var now_teacher_ref='' ;
//目前班級排課科目統計
var subject_name =[] ;
var subject_count =[] ;

//目前教師已排的節數
var teacher_has_sect=0 ;

var now_class_teacher = '' ;
var class_teacher_array = [] ;
//級任
      	<!--  級任教師 -->
      	<{foreach  key=t_key item=teacher    from= $data.class_teacher }>
      	<{if ($t_key) }>
		class_teacher_array[<{$t_key}>]= '<{$teacher}>' ;
		<{/if}>
      	<{/foreach }>

//科目
var subject_list_array = [] ;
      	<{foreach  key=s_key item=subject    from= $data.subject_name }>
    subject_list_array[<{$s_key}>]='<{$subject}>'
      	<{/foreach }>


//轉成使用班級名稱
function class_name( class_id) {
  var class_name_array= [];
  <{foreach  key=cid item=cname    from= $data.class_list_c }>
     class_name_array[<{$cid}>] = '<{$cname}>' ;
  <{/foreach}>
  return class_name_array[class_id] ;
}



$(function () {
	//第一次進入要列出班級課表
    class_change() ;

});



//顯示出已排課表內容
function teacher_sect_show(do_mode , teacher_tab) {
  <{if ($DEF_SET.sects_first_show)}>
  var fSect = '<{$DEF_SET.sects_first}>' ;
  <{else}>
  var fSect = '' ;
  <{/if}>

  	//班級
	for (d = 1;d<=<{$DEF_SET.days}>;d++) {
		for (s =1 ; s<= <{$DEF_SET.sects}> ; s++) {
			var sectt = 'sect_'+ d +'_'+ s ;
			$('#' + sectt).html('<br/><br/>') ;
                    var cell_str = '' ;
                    var cell_str_open='' ;
                    var cell_str2 = '' ;
                    var cell_str_open2='' ;
                    var week_str ='' ;
                    var cell_str_f='' ;

                    for (w=0 ; w<=2 ; w++ ) {
                      <{if ($DEF_SET.sects_first_show)}>
                        <{* 第一節為自修時出現自修名稱 *}>
                        if ( (s==1) && (teacher_tab[d][s][w]['ss_id']<= 0) &&w==0){
                          cell_str_f = '<div ><span>'+ fSect + '</span><br/>  \n'  ;
                          $('#' + sectt).html( cell_str_f ) ;
                        }
                      <{/if}>

                        if (teacher_tab[d][s][w]['ss_id']> 0){
                            if (w==0)  week_str ='<div ><span>' ;
                            if (w==1)  week_str ='<div ><span class="label label-info" title="單週">' ;
                            if (w==2)  week_str ='<div  "><span class="label label-warning" title="雙週">' ;


                            <{*  級任 *}>

                            if (now_class_teacher == teacher_tab[d][s][w]['teacher_name']) {
                                <{if ($isUser)}> <{* 有登入*}>

                                  cell_str +=week_str + teacher_tab[d][s][w]['subject_name']+' </span><br/> \n <a href=teacher_list.php?teacher_id='+ teacher_tab[d][s][w]['teacher']+ '>'+  teacher_tab[d][s][w]['teacher_name']+'</a> <br/> \n <a href=teacher_list.php?room_id='+teacher_tab[d][s][w]['room_id'] + '>' +teacher_tab[d][s][w]['room'] +'</a><br/></div> '  ;

                                  $('#' + sectt).html(cell_str  ) ;
                                <{else}>

                                      cell_str_open+= week_str+teacher_tab[d][s][w]['subject_name']+' </span><br/> \n'+ teacher_tab[d][s][w]['teacher_name']+' <br/> \n <a href=teacher_list.php?room_id='+teacher_tab[d][s][w]['room_id'] + '>' +teacher_tab[d][s][w]['room'] +'</a><br/></div>  \n'  ;
                                      $('#' + sectt).html( cell_str_open ) ;
                                <{/if}>

                             }else{
                                <{if ($isUser)}> <{* 有登入*}>

                                  cell_str2 += week_str+'<span text-wrap style="color:red">'+ teacher_tab[d][s][w]['subject_name']+'</span></span> <br/> \n<a href=teacher_list.php?teacher_id='+ teacher_tab[d][s][w]['teacher']+ '>'+ teacher_tab[d][s][w]['teacher_name']+'</a> <br/> \n<a href=teacher_list.php?room_id='+teacher_tab[d][s][w]['room_id'] + '>'+teacher_tab[d][s][w]['room'] +'</a><br/></div> '  ;

                                  $('#' + sectt).html( cell_str2 ) ;
                                <{else}>

                                      cell_str_open +=week_str+'<span text-wrap style="color:red">'+ teacher_tab[d][s][w]['subject_name']+'</span></span> <br/> \n '+ teacher_tab[d][s][w]['teacher_name']+' <br/>  \n <a href=teacher_list.php?room_id='+teacher_tab[d][s][w]['room_id'] + '>' +teacher_tab[d][s][w]['room'] +'</a><br/></div>   \n'  ;
                                      $('#' + sectt).html( cell_str_open ) ;
                                <{/if}>


                            }


                        }


                    }

		}
	}


}

//換班級
function class_change() {


	var class_id = $("#class_id").val() ;

	if (class_teacher_array[class_id] != undefined ) {
		now_class_teacher =  class_teacher_array[class_id] ;
	}else
		now_class_teacher='' ;
	$("#class_teacher_name").text(now_class_teacher) ;
	//秀出是否已有課的記號
	ajax_get_table('class' , class_id) ;

}




//讀取課表內容(教師 / 班級)
 var ajax_get_table=function( do_mode ,tid ){

      	  //記錄
            var URLs="ajax_get_timetable.php" ;


            $.ajax({
                url: URLs,
                type:"GET",
                dateType:'json', //接收資料格式
				data:{year:<{$data.n_y}> , semester:<{$data.n_s}> ,id:tid ,  do:do_mode },
                success: function(data){
                  //  alert(data);
                  var json_obj = jQuery.parseJSON(data) ;

                  teacher_sect_show( do_mode ,json_obj ) ;
                },

                 error:function(xhr, ajaxOptions, thrownError){
                    alert('error:' + xhr.status);
                    alert(thrownError);
                 }
           })
 }



</script>
