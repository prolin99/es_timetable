
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
  font-size: 8px;
}
 </style>
 <h3><{$data.n_y}> 學年度<{$data.n_s}> 學期</h3>

      <div class="row" > <!-- box1 -->



      <div class="col-10" id="table_div">

      	<div class="row" >

      	<div class='col-5'>
          <form action="teacher_list.php"  method="GET" class="form-inline">
            <div class="form-group">
                <{if ($data.isteacher)}>
              <label for="teacher_id" class="control-label">查詢教師:</label>
              <div >
              <{html_options name=teacher_id id=teacher_id  options=$data.teacher_list  selected=$data.teacher_sel  onchange="submit();"  class="form-control" }>
            </div>
            <{/if}>
            </div>
        </form>
      	</div>

      <div class='col-5'>
          <form action="teacher_list.php"  method="GET" class="form-inline" >
              <div class="form-group">
                <label for="room_id" class="control-label">查詢教室:</label>
                <div  >
                <{html_options name=room_id id=room_id  options=$data.room_list  selected=$data.room_sel  onchange="submit();"   class="form-control" }>
        </div>
        </div>
        </form>
      </div>
      	</div>

      <table class="table table-bordered">
      <tr>
      <th scope="col">節</th>
      <{section name=di  start=1  loop=$DEF_SET.days_sm  step=1  }>
      <th scope="col"><{$DEF_SET.week[$smarty.section.di.index]}></th>
      <{ /section }>
      </tr>
      <{section name=si  start=1  loop=$DEF_SET.sects_sm    step=1  }>
      <tr>
      <td ><{$DEF_SET.sects_cht_list[$smarty.section.si.index]}><br />
        <{$DEF_SET.time_list[$smarty.section.si.index]}>
      </td>
      <{section name=di  start=1  loop=$DEF_SET.days_sm    step=1  }>
      <td  >


      	<div  data_ref="sect_<{$smarty.section.di.index}>_<{$smarty.section.si.index}>" id="sect_<{$smarty.section.di.index}>_<{$smarty.section.si.index}>" >
          <{assign var="cell_tab" value=$tab[$smarty.section.di.index][$smarty.section.si.index]}>
          <{foreach  from=$cell_tab  key=w item=cell_data  }>

            <{if ($cell_data.class_id) }>
                <{if  ($w==0) }>
                  <div ><span  >
                <{/if}>
                <{if  ($w==1) }>
                  <div ><span class="label label-info"  title="單週">
                <{/if}>
                <{if  ($w==2) }>
                  <div ><span class="label  label-warning" title="雙週">
                <{/if}>

                <a href="index.php?class_id=<{$cell_data.class_id}>">
                <{$data.class_list_c[$cell_data.class_id]}>
                </a></span>

                <{$cell_data.subject_name}><br />
                <{*       同時多班                *}>
                <{if  ($cell_data.other)  }>(同節:<{$cell_data.other }>) <{/if}>
                <{if ($data.room_sel ) }>
                    <{if ($data.isteacher)}>
                        <a href="teacher_list.php?teacher_id=<{$cell_data.teacher}>">
                            <{$cell_data.teacher_name}>
                        </a>
                    <{else }>
                        <{$cell_data.teacher_name}>
                    <{/if}>
                    <br />
                <{/if}>

                <{if ($cell_data.room) }>
                    <a href="teacher_list.php?room_id=<{$cell_data.room_id}>">
                    <{$cell_data.room}>
                    </a>
                <{/if}>
              </div>
             <{/if}>
         <{/foreach }>

          </div>

      </td>
      <{ /section }>
      </tr>
      <{ /section }>
      </table>


      </div>      <!-- table_div end-->


      </div><!-- box1 end-->
