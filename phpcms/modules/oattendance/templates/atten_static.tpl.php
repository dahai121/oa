<?php
defined('IN_ADMIN') or exit('No permission resources');
include $this->admin_tpl('header', 'admin');
?>
<script type="text/javascript">
$(document).ready(function(){
  $("#departlist").change(function(){
	if(this.value==null)return;
	$.ajax({
		type:'POST',
		url:'?m=oattendance&c=oattendance&a=public_ajax_getulist',
		dataType:'json',
		data:"groupid="+this.value,
		success:function(data){
			if(typeof data == "underfined" || data == "null" || data == "" || data.length==0){
				return;
			}else{
				$("#userlist").empty();
				$("#userlist").append("<option value='0'>全部</option>");
				$.each(data,function(index,term){
					$("#userlist").append("<option value='"+term.userid+"'>"+term.username+"</option>");
				});
			}
		}
	});
  });
});
</script>
<div class="pad-lr-10" style="width:85%">
<form name="myform" action="?m=oattendance&c=oattendance&a=export" method="post">
<table cellspacing="0" class="search-form">
    <tbody>
		<tr>
		<td><div class="explain-col">
			处室:  <?php echo form::select($depart_status,$groupid,'id="groupid" name="groupid"', L('all'))?> &nbsp;&nbsp;
			年度:  <?php echo form::select($year_status,$year,'id="year" name="year"')?>&nbsp;&nbsp; 
			月份:   <?php echo form::select($month_status,$month,'id="month" name="month"')?>&nbsp;&nbsp;
			应登录天数: <input style="width:60px;" id="workdays" name="workdays" value="<?php echo $workdays?>">&nbsp;&nbsp;
			<input onClick="ssearch();" type="button" value="  查询  " class="button">&nbsp;&nbsp;&nbsp;&nbsp;
			<input name="submit" type="submit" class="button" value=" 导出Excel ">
		</div>
		</td>
		</tr>
    </tbody>
</table>
</form>
<div class="table-list">
<table cellspacing="0" id="stable">
	<thead>
    <tr>
        <th align="center" width="5%">姓名</th><th align="center" width="6%">未登录次数</th><th align="center" width="20%">原因</th><th align="center" width="6%">未按时登录次数</th><th align="center" width="20%">原因</th>
    </tr>
	</thead>
	<tbody>
    <?php
    if(is_array($infos)){
    foreach($infos as $info){
    ?>
    <tr>
        <td align="center"><a href="#" onClick="show(<?php echo $info['userid']?>);"><span style="color:blue;"><?php echo $info['username']?></span></a></td>
		<td align="center"><?php echo $info['wcq']?></td>
		<td align="center"><?php echo $info['wcqyy']?></td>
		<td align="center"><?php echo $info['wascq']?></td>
		<td align="center"><?php echo $info['wascqyy']?></td>
    </tr>
    <?php } } ?>
	</tbody>
</table>
<div class="btn">&nbsp;&nbsp;</div>
</div>
</div>
<script type="text/javascript">
function show(uid){
	var year = $("#year").val();
	var month = $("#month").val();
	var param = "year="+year+"&month="+month+"&uid="+uid;
	window.top.art.dialog({id:'show',iframe:'?m=oattendance&c=oattendance&a=show&'+param, title:'考勤日历', width:'700', height:'520'});void(0);
}
function ssearch(){
	$("#stable tbody tr").empty();
	var groupid = 0;
	if($("#groupid").val()!=null && $("#groupid").val()!=''){
		groupid = $("#groupid").val();
	}
	$.ajax({
		type:'POST',
		url:'?m=oattendance&c=oattendance&a=public_ajax_getslist',
		dataType:'json',
		data:{
			groupid:groupid,
			year:$("#year").val(),
			month:$("#month").val(),
			workdays:$("#workdays").val()
		},
		success:function(data){
			var trHTML = '';
			if(typeof data == "underfined" || data == null || data == "" || data.length==0){
				return;
			}else{
				$.each(data,function(index,term){
					trHTML +='<tr><td align="center"><a href="#" onClick="show('+term.userid+');"><span style="color:blue;">'+term.username+'</span></a></td>'
						+'<td align="center">'+term.wcq+'</td>'
						+'<td align="center">'+term.wcqyy+'</td>'
						+'<td align="center">'+term.wascq+'</td>'
						+'<td align="center">'+term.wascqyy+'</td></tr>';
				});
			}
			$("#stable tbody").append(trHTML);	
		}
	});
}
</script>
</body>
</html>