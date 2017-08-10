<?php defined('IN_ADMIN') or exit('No permission resources.');?>
<?php include $this->admin_tpl('header', 'admin');?>
<div class="pad-lr-10">

<form name="myform" action="?m=member&c=member&a=delete" method="post" onsubmit="checkuid();return false;">
<div class="table-list">
<table width="100%" cellspacing="0">
	<thead>
		<tr>
			<th align="left"><?php echo L('id')?></th>
			<th align="left"><?php echo L('pid')?></th>
			<th align="left"><?php echo L('名称')?></th>
			<th align="left"><?php echo L('operation')?></th>
		</tr>
	</thead>
<tbody>
<?php
	if(is_array($data)){
	foreach($data as $k=>$v) {
?>
    <tr>
		<td align="left"><?php echo $v['id']?></td>
		<td align="left"><?php echo $v['pid']?></td>
		<td align="left" style="padding-left:<?php echo $v['level']*30?>px;"  ><?php echo $v['name']?></td>
		<td align="left">
			<a href="javascript:edit(<?php echo $v['id']?>, '<?php echo $v['name']?>')">[<?php echo L('edit')?>]</a>
			<a href="javascript:confirmurl('?m=people&amp;c=people&amp;a=delete_xiala&amp;pid=<?php echo $v['pid'] ?>&amp;id=<?php echo $v['id'] ?>','确认要删除 『 <?php echo $v['name']  ?> 』 吗？')">[删除]</a>
		</td>
    </tr>
<?php
	}
}
?>
</tbody>
</table>
</div>
</form>
</div>
<script type="text/javascript">
<!--
function edit(id, name) {
	window.top.art.dialog({id:'edit'}).close();
	window.top.art.dialog(
		{title:'<?php echo L('edit').L('下拉列表')?>《'+name+'》',id:'edit',iframe:'?m=people&c=people&a=edit_xiala&id='+id,width:'700',height:'500'},
		function(){var d = window.top.art.dialog({id:'edit'}).data.iframe;d.document.getElementById('dosubmit').click();return false;},
		function(){window.top.art.dialog({id:'edit'}).close()}
	);
}

function del(id, name) {
	window.top.art.dialog({id:'edit'}).close();
	window.top.art.dialog(
		{title:'<?php echo L('delete').L('下拉列表')?>《'+name+'》',id:'edit',iframe:'?m=people&c=people&a=edit_xiala&id='+id,width:'700',height:'500'},
		function(){var d = window.top.art.dialog({id:'edit'}).data.iframe;d.document.getElementById('dosubmit').click();return false;},
		function(){window.top.art.dialog({id:'edit'}).close()}
	);

}




function checkuid() {
	var ids='';
	$("input[name='userid[]']:checked").each(function(i, n){
		ids += $(n).val() + ',';
	});
	if(ids=='') {
		window.top.art.dialog({content:'<?php echo L('plsease_select').L('member')?>',lock:true,width:'200',height:'50',time:1.5},function(){});
		return false;
	} else {
		myform.submit();
	}
}

function member_infomation(userid, modelid, name) {
	window.top.art.dialog({id:'modelinfo'}).close();
	window.top.art.dialog({title:'<?php echo L('memberinfo')?>',id:'modelinfo',iframe:'?m=member&c=member&a=memberinfo&userid='+userid+'&modelid='+modelid,width:'700',height:'500'}, function(){var d = window.top.art.dialog({id:'modelinfo'}).data.iframe;d.document.getElementById('dosubmit').click();return false;}, function(){window.top.art.dialog({id:'modelinfo'}).close()});
}

//-->
</script>
</body>
</html>