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
<form name="searchform" action="?m=oattendance&c=oattendance&a=attenselect&menuid=<?php echo $_GET['menuid'];?>" method="post" >
<table cellspacing="0" class="search-form">
    <tbody>
		<tr>
		<td><div class="explain-col">����:  <?php echo form::select($depart_status,$departid,'name="search[departid]" id="departlist"', L('all'))?>      ����:  <?php echo form::select($user_status,$userid,'name="search[userid]" id="userlist"', L('all'))?>&nbsp;&nbsp;&nbsp;&nbsp;����:  <?php echo form::date('search[start_time]',$start_time,'')?> ��   <?php echo form::date('search[end_time]',$end_time,'')?>    <input type="submit" value="  ��ѯ  " class="button" name="dosubmit">
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
        <th>ѡ��</th><th>����</th><th>����</th><th>����</th><th>ǩ��ʱ��</th><th>����</th><th>��ע</th><th>���״̬</th><th>����</th>
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
		<td align="center" width="10%"><?php if($info['flag']=='1'){?>�����<?php }else{?><span style='color:red;'>δ���</span><?php }?></td>
		<td align="center" width="13%">
		<a href='###' onClick='edit(<?php echo $info['attid']?>)' title="�޸�">�޸�</a>|
		<a href='###' onClick='ocheck(<?php echo $info['attid']?>)' title="���">���</a>
		</td>
    </tr>
    <?php } } ?>
	</tbody>
</table>
<div class="btn"><a href="#"
	onClick="selectuc()">ѡ��δ���</a>/<a	href="#"
	onClick="javascript:$('input[type=checkbox]').attr('checked', false)">ȡ��</a>&nbsp;&nbsp;
<input name="submit" type="submit" class="button"
	value="ͨ�����" onClick="check()">&nbsp;&nbsp;</div>
<div id="pages"><?php echo $pages?></div>
</div>
</form>
</div>
<script type="text/javascript">
function selectuc(){
	$('#attable').find("tr").each(function(){
		var tdarr = $(this).children();
		var flag = tdarr.eq(7).text();
		if(flag=="δ���"){
			tdarr.eq(0).find('input').attr('checked',true);
		}
	});
}
function edit(id) {
	window.top.art.dialog({id:'edit'}).close();
	window.top.art.dialog({title:'�����޸� ',id:'edit',iframe:'?m=oattendance&c=oattendance&a=update&attid='+id,width:'500',height:'200'}, function(){var d = window.top.art.dialog({id:'edit'}).data.iframe;var form = d.document.getElementById('dosubmit');form.click();return false;}, function(){window.top.art.dialog({id:'edit'}).close()});
}
function ocheck(id) {
	window.top.art.dialog({id:'check'}).close();
	window.top.art.dialog({title:'ͨ����� ',id:'check',iframe:'?m=oattendance&c=oattendance&a=check&attid='+id,width:'500',height:'200'}, function(){var d = window.top.art.dialog({id:'check'}).data.iframe;var form = d.document.getElementById('dosubmit');form.click();return false;}, function(){window.top.art.dialog({id:'check'}).close()});
}
function checkid(){
	var ids='';
	$("input[name='attid[]']:checked").each(function(i, n){
		ids += $(n).val() + ',';
	});
	if(ids=='') {
		window.top.art.dialog({content:"��ѡ���ٽ��в���",lock:true,width:'200',height:'50',time:1.2},function(){});
		return false;
	} else {
		myform.submit();
	}
}
</script>
</body>
</html>