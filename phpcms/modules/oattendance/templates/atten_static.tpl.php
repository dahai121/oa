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
				$("#userlist").append("<option value='0'>ȫ��</option>");
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
			����:  <?php echo form::select($depart_status,$groupid,'id="groupid" name="groupid"', L('all'))?> &nbsp;&nbsp;
			���:  <?php echo form::select($year_status,$year,'id="year" name="year"')?>&nbsp;&nbsp; 
			�·�:   <?php echo form::select($month_status,$month,'id="month" name="month"')?>&nbsp;&nbsp;
			Ӧ��¼����: <input style="width:60px;" id="workdays" name="workdays" value="<?php echo $workdays?>">&nbsp;&nbsp;
			<input onClick="ssearch();" type="button" value="  ��ѯ  " class="button">&nbsp;&nbsp;&nbsp;&nbsp;
			<input name="submit" type="submit" class="button" value=" ����Excel ">
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
        <th align="center" width="5%">����</th><th align="center" width="6%">δ��¼����</th><th align="center" width="20%">ԭ��</th><th align="center" width="6%">δ��ʱ��¼����</th><th align="center" width="20%">ԭ��</th>
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
	window.top.art.dialog({id:'show',iframe:'?m=oattendance&c=oattendance&a=show&'+param, title:'��������', width:'700', height:'520'});void(0);
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