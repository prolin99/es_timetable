
<{$toolbar}>
<link rel="stylesheet" href="<{$xoops_url}>/modules/tadtools/jquery/themes/base/jquery-ui.css">
<script src="<{$xoops_url}>/modules/tadtools/jquery/ui/jquery-ui.js"></script>
<style>
.groupbox {

    border: 1px dotted gray;
    background-color: #EEE;

}
.nobox {
    border: 1px dotted gray;
    background-color: #AAA;
}


</style>

 <div class="row" >
      <div class="col-xs-8" >
 <h3><{$data.n_y}> 學年度<{$data.n_s}> 學期   <{$data.class_list_c[$data.my_class_id]}>功課表 </h3>
 	</div>
 	<div class="col-xs-3" >
 	<a class="btn btn-info" href="export.php?class_id=<{$data.my_class_id}>">下載課表</a>
 	</div>
 </div>

      <div class="row" > <!-- box1 -->

      <div class="col-xs-2" > <!-- left  -->

        <div id="kmo_div" class="row" >
        <!--  科目 -->
      	<{foreach  key=s_key item=subject    from= $data.subject_name }>
      		   <span class="col-xs-12 col-xs-4 subj" data_ref="subj_<{$s_key}>_<{$subject}>"  >
                     <label  id="subj_<{$s_key}>" title='<{$subject}>' name_title='<{$subject}>' class="label label-success">
      		                 <{$subject}>
                    </label>
                </span>
      	<{/foreach }>
      	</div>

      </div> <!--left end-->

      <div class="col-xs-10" id="table_div">  <!--               table                          -->


      	<div class="row" >
      		<span id="sub_count" class="col-xs-12 col-xs-12" ></span>
      	</div>

      <table class="table table-bordered">
      <tr>
      <td class="col-xs-1 col-xs-1">節</td>


      <{section name=di  start=1  loop=$DEF_SET.days_sm  step=1  }>
      <td  class="col-xs-2 col-xs-2"><{$DEF_SET.week[$smarty.section.di.index]}></td>
      <{ /section }>
      </tr>
      <{section name=si  start=1  loop=$DEF_SET.sects_sm    step=1  }>
      <tr>
      <td class="col-xs-1 col-xs-1"><{$DEF_SET.sects_cht_list[$smarty.section.si.index]}></td>
      <{section name=di  start=1  loop=$DEF_SET.days_sm    step=1  }>
      <td class="col-xs-2 col-xs-2">
      <{assign var="cell_showed"  value=0 }>

      <{assign var="cell_tab" value=$data.my_table[$smarty.section.di.index][$smarty.section.si.index]}>
      <{foreach  from=$cell_tab  key=w item=cell_data  }>

          <{if ( ($cell_data.class_id) and ( $cell_data.class_id <> $data.my_class_id)) }>  <{*有課且不同班*}>
            <{assign var="cell_showed"  value=1 }>
            <div class="nobox" data_ref="sect_<{$smarty.section.di.index}>_<{$smarty.section.si.index}>" id="sect_<{$smarty.section.di.index}>_<{$smarty.section.si.index}>" style="background:#AAA;" title='在他班有課，不可排入'>
            </div>
              <{if $w==1}>單週-<{/if}>
              <{if $w==2}>雙週-<{/if}>
            <{$data.class_list_c[$cell_data.class_id]}>_<{$cell_data.subject_name}>
          <{/if}>
       <{/foreach}>
       <{if ($cell_showed==0) }>  <{* 自已的課 *}>
            <div class="groupbox " data_ref="sect_<{$smarty.section.di.index}>_<{$smarty.section.si.index}>" id="sect_<{$smarty.section.di.index}>_<{$smarty.section.si.index}>" style="background:#EEE;">
            </div>
       <{/if}>

      </td>
      <{ /section }>
      </tr>
      <{ /section }>
      </table>


      </div>      <!-- table_div end-->


      </div><!-- box1 end-->

      <div class="row">

            <span class="label label-info col-xs-1 col-xs-12">說明</span>
            <div class="col-xs-10">
           拖拉左方的科目到節次中，新增課程，科任課無法更動。<br/>
           先排好的科目，可以拖拉到其它節次中。<br/>
           級任無法排單雙週課表，只有管理員才可以排單雙週課表。<br/>
            </div>

      </div>

<SCRIPT type="text/javascript">
var zindex=1000 ;
//可拖拉
$(function () {

        $(".subj").draggable({
                revert: "valid",
                start: function(event, ui) { $(this).css("z-index", zindex++ ); } ,
                drag: function (event, ui) {

                }
        });

        $(".groupbox").droppable({
                drop: function (event, ui) {
                	var sect = $(this).attr("data_ref")  ;
                	if (sect) {	//科任時為空值
                    	var user_data = ui.draggable.attr('data_ref') ;
                    	var old_sect = ui.draggable.attr('old_sect') ;
                    	sect_show(sect , user_data , now_teacher_array , old_sect) ;
                    }


                }

        });


});
//指定教師
var now_teacher_array =[] ;
now_teacher_array[1]= <{$data.my_teacher_id}> ;
now_teacher_array[2]= '<{$data.my_name}>' ;

var now_teacher_ref='tea_<{$data.my_teacher_id}>_<{$data.my_name}>' ;
//目前班級排課科目統計
var subject_name =[] ;
var subject_count =[] ;

//科目
var subject_list_array = [] ;
      	<{foreach  key=s_key item=subject    from= $data.subject_name }>
    subject_list_array[<{$s_key}>]='<{$subject}>'
      	<{/foreach }>



$(function () {
	//第一次進入要列出班級課表
    class_change() ;


});


//顯示出已排課表內容
function teacher_sect_show(do_mode , teacher_tab) {


  	//班級
	for (d = 1;d<=<{$DEF_SET.days}>;d++) {
		for (s =1 ; s<=<{$DEF_SET.sects}> ; s++) {
			var sectt = 'sect_'+ d +'_'+ s ;
			$('#' + sectt).html('<br/><br/>') ;

                   var sect_str='' ;
                   var sect_str2='' ;

                   for (w= 0 ; w<=2;w++)  {
        			if (teacher_tab[d][s][w]['ss_id'] > 0){
        				if (teacher_tab[d][s][w]['teacher'] ==	<{$data.my_teacher_id}>) {
        				  //級任
                                  if (w==0)
                                      sect_str += '<span id="subj_' + teacher_tab[d][s][w]['ss_id'] +'" data_ref="subj_'+teacher_tab[d][s][w]['ss_id'] +'_'+ teacher_tab[d][s][w]['subject_name'] +'" class="label label-success subj" old_sect="'+ sectt + '">'+teacher_tab[d][s][w]['subject_name']+
                                      '<span class="glyphicon glyphicon-remove" aria-hidden="true"   data_ref="'+ sectt + '" data_ref_sub="'+ teacher_tab[d][s][w]['ss_id'] + '" title="刪除"></span>  </span><br /><br />' ;
                                  if (w==1)
                                            sect_str += '<span  class="label label"  title="單雙週級任不可自行修改" >單週-'+teacher_tab[d][s][w]['subject_name']+'</span><br /><br />' ;
                                  if (w==2)
                                            sect_str += '<span  class="label label-warning"  title="單雙週級任不可自行修改" >雙週-'+teacher_tab[d][s][w]['subject_name']+'</span><br /><br />' ;
        				  $('#' + sectt).html( sect_str ) ;
                                    if (w>0)
                                      $('#' + sectt).attr("data_ref" ,'') ;     //單雙週情形下時去除此值
        				}else {
        				  //科任	不可放入
                                    if (w==0)
                                        sect_str2 +='<span class="label label-default"  title="科任不可自行修改">'+teacher_tab[d][s][w]['subject_name']+'</span><br/><span class="label label-info">'+ teacher_tab[d][s][w]['teacher_name']+'</span><br/><span class="label label-default">'+teacher_tab[d][s][w]['room']+'</span> ' ;
                                     if (w==1)
                                        sect_str2 +='<span class="label label-warning"  title="科任不可自行修改">單週-'+teacher_tab[d][s][w]['subject_name']+'</span><br/><span class="label label-info">'+ teacher_tab[d][s][w]['teacher_name']+'</span><br/><span class="label label-default">'+teacher_tab[d][s][w]['room']+'</span> ' ;
                                     if (w==2)
                                        sect_str2 +='<span class="label label-warning"  title="科任不可自行修改">雙週-'+teacher_tab[d][s][w]['subject_name']+'</span><br/><span class="label label-info">'+ teacher_tab[d][s][w]['teacher_name']+'</span><br/><span class="label label-default">'+teacher_tab[d][s][w]['room']+'</span> ' ;

        				  $('#' + sectt).html( sect_str2 ) ;
        				  $('#' + sectt).attr("data_ref" ,'') ;     //科任時去除此值
        				}
        	                     if (w>0)
         				   subject_count[teacher_tab[d][s][w]['ss_id']]  = subject_count[teacher_tab[d][s][w]['ss_id']]  +0.5 ;
                                  else
                                     subject_count[teacher_tab[d][s][w]['ss_id']]  = subject_count[teacher_tab[d][s][w]['ss_id']]  +1 ;

        			}	//ss_id
              	} //w0 ~2
		}
	}

        $(".subj").draggable({
                revert: "valid",
                start: function(event, ui) { $(this).css("z-index", zindex++ ); } ,
        });

	//顯示科目的已排節數
 	show_subject_count() ;



}

//換班級
function class_change() {
	//陣列初始值
  	for (i = 1;i<=50;i++) {

  		subject_count[i]=0 ;
  	}

	//現在班級
	var class_id = <{$data.my_class_id}> ;

	//秀出是否已有課的記號
	ajax_get_table('class' , class_id) ;



}


function show_subject_count() {
	//顯示出各科排課節數
	var smsg='' ;
	for (i = 1;i<=50;i++) {
		if (subject_count[i]>0 )
			smsg = smsg + subject_list_array[i] + ':' + subject_count[i] + ',' ;
	}



	$('#sub_count').text(  smsg) ;
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
                  //  alert(msg);
                  var json_obj = jQuery.parseJSON(data) ;

                  teacher_sect_show( do_mode ,json_obj ) ;
                },

                 error:function(xhr, ajaxOptions, thrownError){
                    alert('error:' + xhr.status);
                    alert(thrownError);
                 }
           })
 }

 //排課，放入科目
function sect_show(sect , subject_data , now_teacher ,old_sect) {


		var splits = subject_data.split('_') ;
		if (splits[0]=='subj') {
			var room = $('#room').val() ;

			//外加的科目，非移動的
			if (old_sect == undefined ) {
		    	 //科目節數 +1
    			 subject_count[splits[1]] = subject_count[splits[1]] + 1  ;
    			 show_subject_count() ;
    		       }
			$('#' + sect).html('<span id="' + subject_data +'" data_ref="' + subject_data +
                            '" class="label label-success subj" old_sect="'+sect+'" >'+splits[2]+
                            '<span class="glyphicon glyphicon-remove" aria-hidden="true"  data_ref="'+sect+'" data_ref_sub="'+ splits[1] + '"  title="刪除"></span></span><br /><br /> ') ;

		$(".subj").draggable({
                revert: "valid",
                start: function(event, ui) { $(this).css("z-index", zindex++ ); } ,
                drag: function (event, ui) {

              }
        	});
        	ajax_time_table('del' , old_sect , '' , now_teacher_ref , '') ;

			ajax_time_table('add' , sect , subject_data , now_teacher_ref , room ) ;
        	//清空格子
    		$('#' + old_sect).html('<br/><br/>') ;
		}


}


    //消除 ----------------------------------------------------------------------------------
  $(document).on("click", ".glyphicon-remove", function(){

    var mark_id = $(this).attr("data_ref")  ;
    var ss_id = $(this).attr("data_ref_sub")  ;
    //清空格子
    $('#' + mark_id).html('<br/><br/>') ;
    //科目節數 -1
    subject_count[ss_id] = subject_count[ss_id] -1 ;
    show_subject_count() ;
 	ajax_time_table('del' , mark_id , '' , now_teacher_ref , '') ;


  });

 //增刪課表 動作
 var ajax_time_table=function( do_mode ,sect_num , subject_data , user ,room_place ){

      	  //記錄
            var URLs="ajax_set_timetable.php" ;
            var class_id = <{$data.my_class_id}> ;


            $.ajax({
                url: URLs,
                type:"GET",
                dataType:'text',
				data:{year:<{$data.n_y}> , semester:<{$data.n_s}> ,class_id:class_id , sect:sect_num, subject:subject_data , teacher:user, room:room_place , do:do_mode },
                success: function(data){
                    //alert(data);

                },

                 error:function(xhr, ajaxOptions, thrownError){
                    alert('error:' + xhr.status);
                    alert(thrownError);
                 }
           })
 }

</script>
