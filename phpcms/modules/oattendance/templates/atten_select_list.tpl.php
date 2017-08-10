<?php
defined('IN_ADMIN') or exit('No permission resources. - atten_select.tpl.php');
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
<form name="searchform" action="?m=oattendance&c=oattendance&a=attenselect&menuid=<?php echo $_GET['menuid'];?>" method="post" >
<table cellspacing="0" class="search-form">
    <tbody>
		<tr>
		<td><div class="explain-col">处室:  <?php echo form::select($depart_status,$departid,'name="search[departid]" id="departlist"', L('all'))?>      姓名:  <?php echo form::select($user_status,$userid,'name="search[userid]" id="userlist"', L('all'))?>&nbsp;&nbsp;&nbsp;&nbsp;日期:  <?php echo form::date('search[start_time]',$start_time,'')?> 至   <?php echo form::date('search[end_time]',$end_time,'')?>    <input type="submit" value="  查询  " class="button" name="dosubmit">
		</div>
		</td>
		</tr>
    </tbody>
</table>
</form>
<form name="myform" action="?m=oattendance&c=oattendance&a=allcheck" method="post" onsubmit="checkid();return false;">
<div class="table-list">
<table cellspacing="0" id="attable">
	<thead>
    <tr>
        <th>选择</th><th>姓名</th><th>处室</th><th>日期</th><th>签到时间</th><th>类型</th><th>备注</th><th>审核状态</th><th>操作</th>
    </tr>
	</thead>
	<tbody>
    <?php
    if(is_array($infos)){
    foreach($infos as $info){
    ?>
    <tr>
	    <td align="center" width="35"><input type="checkbox"
			name="attid[]" value="<?php echo $info['attid']?>"></td>
        <td align="center" width="10%"><?php echo $info['username']?></td>
        <td align="center" width="10%"><?php echo $info['dname']?></td>
        <td align="center" width="15%"><?php echo date('Y-m-d',strtotime($info['attdate']));?></td>
		<td align="center" width="15%"><?php echo date('Y-m-d H:i:s',strtotime($info['addtime']));?></td>	
		<td align="center" width="10%"><?php echo $info['attype']?></td>
		<td align="center" width="12%"><?php echo $info['comment']?></td>
		<td align="center" width="10%"><?php if($info['flag']=='1'){?>已审核<?php }else{?><span style='color:red;'>未审核</span><?php }?></td>
		<td align="center" width="13%">
		<a href='###' onClick='edit(<?php echo $info['attid']?>)' title="修改">修改</a>|
		<a href='###' onClick='ocheck(<?php echo $info['attid']?>)' title="审核">审核</a>
		</td>
    </tr>
    <?php } } ?>
	</tbody>
</table>
<div class="btn"><a href="#"
	onClick="selectuc()">选中未审核</a>/<a	href="#"
	onClick="javascript:$('input[type=checkbox]').attr('checked', false)">取消</a>&nbsp;&nbsp;
<input name="submit" type="submit" class="button"
	value="通过审核" onClick="check()">&nbsp;&nbsp;</div>
<div id="pages"><?php echo $pages?></div>
</div>
</form>
</div>
<script type="text/javascript">
function selectuc(){
	$('#attable').find("tr").each(function(){
		var tdarr = $(this).children();
		var flag = tdarr.eq(7).text();
		if(flag=="未审核"){
			tdarr.eq(0).find('input').attr('checked',true);
		}
	});
}
function edit(id) {
	window.top.art.dialog({id:'edit'}).close();
	window.top.art.dialog({title:'考勤修改 ',id:'edit',iframe:'?m=oattendance&c=oattendance&a=update&attid='+id,width:'500',height:'200'}, function(){var d = window.top.art.dialog({id:'edit'}).data.iframe;var form = d.document.getElementById('dosubmit');form.click();return false;}, function(){window.top.art.dialog({id:'edit'}).close()});
}
function ocheck(id) {
	window.top.art.dialog({id:'check'}).close();
	window.top.art.dialog({title:'通过审核 ',id:'check',iframe:'?m=oattendance&c=oattendance&a=check&attid='+id,width:'500',height:'200'}, function(){var d = window.top.art.dialog({id:'check'}).data.iframe;var form = d.document.getElementById('dosubmit');form.click();return false;}, function(){window.top.art.dialog({id:'check'}).close()});
}
function checkid(){
	var ids='';
	$("input[name='attid[]']:checked").each(function(i, n){
		ids += $(n).val() + ',';
	});
	if(ids=='') {
		window.top.art.dialog({content:"请选择再进行操作",lock:true,width:'200',height:'50',time:1.2},function(){});
		return false;
	} else {
		myform.submit();
	}
}
</script>
</body>
</html>