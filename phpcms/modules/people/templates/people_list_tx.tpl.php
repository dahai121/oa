<?php defined('IN_ADMIN') or exit('No permission resources.');?>
<?php include $this->admin_tpl('header', 'admin');?>
<div class="pad-lr-10">
<form name="searchform" action="" method="get" >
<input type="hidden" value="people" name="m">
<input type="hidden" value="people" name="c">
<input type="hidden" value="search" name="a">
<input type="hidden" value="879" name="menuid">
<input type="hidden" value="<?php echo $modelid ?>" name="modelid">
<table width="100%" cellspacing="0" class="search-form">
    <tbody>
		<tr>
		<td>
		<div class="explain-col">
			人员类型：
			<select name="rylx">
				<option value='77' <?php if(isset($_GET['rylx']) && $_GET['rylx']==1){?>selected<?php }?>><?php echo L('在职')?></option>
				<option value='79' <?php if(isset($_GET['rylx']) && $_GET['rylx']==2){?>selected<?php }?>><?php echo L('退休')?></option>
				<option value='78' <?php if(isset($_GET['rylx']) && $_GET['rylx']==3){?>selected<?php }?>><?php echo L('离休')?></option>
				<option value='80' <?php if(isset($_GET['rylx']) && $_GET['rylx']==4){?>selected<?php }?>><?php echo L('离退休')?></option>
			</select>

			<?php echo L('添加日期')?>：
			<?php echo form::date('start_time', $start_time)?>-
			<?php echo form::date('end_time', $end_time)?>
			姓名：<input name="keyword" type="text" value="<?php if(isset($_GET['keyword'])) {echo $_GET['keyword'];} ?>" class="input-text" />
				<input type="submit" name="search" class="button" value="<?php echo L('search')?>" />
	</div>
		</td>
		</tr>
    </tbody>
</table>
</form>

<form name="myform" action="?m=people&c=people&a=delete&modelid=<?php echo $modelid ?>" method="post" onsubmit="checkuid();return false;">
<div class="table-list">
<table width="100%" cellspacing="0">
	<thead>
		<tr>
			<th  align="left" width="20"><input type="checkbox" value="" id="check_box" onclick="selectall('userid[]');"></th>
			<th align="left"><?php echo L('id')?></th>
			<th align="left"><?php echo L('姓名')?></th>
			<th align="left"><?php echo L('民族')?></th>
			<th align="left"><?php echo L('身份证')?></th>
			<th align="left"><?php echo L('籍贯')?></th>
			<th align="left"><?php echo L('住址')?></th>
			<th align="left"><?php echo L('派出所')?></th>
			<th align="left"><?php echo L('联系电话')?></th>
			<th align="left"><?php echo L('联系地址')?></th>
			<th align="left"><?php echo L('是否锁定')?></th>
			<th align="left"><?php echo L('operation')?></th>
		</tr>
	</thead>
<tbody>
<?php
	if(is_array($memberlist_arr)){
	foreach($memberlist_arr as $k=>$v) {
?>
    <tr>
		<td align="left"><input type="checkbox" value="<?php echo $v['id']?>" name="userid[]"></td>
		<td align="left"><?php echo $v['id']?></td>
		<td align="left"><?php echo $v['title']?></td>
		<td align="left"><?php echo $v['mz']?></td>
		<td align="left"><?php echo $v['sfz']?></td>
		<td align="left"><?php echo $v['jg']?></td>
		<td align="left"><?php echo $v['address']?></td>

		<td align="left"><?php echo $v['pcs']?></td>
		<td align="left"><?php echo $v['phone']?></td>
		<td align="left"><?php echo $v['lxdz']?></td>
		<td align="left"><?php  if($v['islock'] =='1' ){echo  "锁定";}else{echo "未锁定";}?></td>
		<td align="left">

			<a href="javascript:;" onclick="javascript:openwinx('?m=people&c=people&a=edit&modelid=<?php echo $modelid; ?>&id=<?php echo $v['id'] ?>','')"><?php echo L('edit'); ?></a>

		</td>
    </tr>
<?php
	}
}
?>
</tbody>
</table>

<div class="btn">
<label for="check_box"><?php echo L('select_all')?>/<?php echo L('cancel')?></label>
	<input type="submit" class="button" name="dosubmit" onclick="document.myform.action='?m=people&c=people&a=lock&modelid=<?php echo $modelid ?>'" value="<?php echo L('lock')?>"/>
	<input type="submit" class="button" name="dosubmit" onclick="document.myform.action='?m=people&c=people&a=unlock&modelid=<?php echo $modelid ?>'" value="<?php echo L('unlock')?>"/>
	<input type="submit" class="button" name="dosubmit" value="<?php echo L('delete')?>" onclick="return confirm('<?php echo L('sure_delete')?>')"/>
<!--input type="button" class="button" name="dosubmit" onclick="move();return false;" value="<!--?php echo L('move')?>"/-->
</div>

<div id="pages"><?php echo $pages?></div>
</div>
</form>
</div>
<script type="text/javascript">
<!--
function edit(id, name,modelid) {
	window.top.art.dialog({id:'edit'}).close();
	window.top.art.dialog({title:'<?php echo L('edit').L('people')?>《'+name+'》',id:'edit',iframe:'?m=people&c=people&a=edit&modelid='+modelid+'&userid='+id,width:'700',height:'500'}, function(){var d = window.top.art.dialog({id:'edit'}).data.iframe;d.document.getElementById('dosubmit').click();return false;}, function(){window.top.art.dialog({id:'edit'}).close()});
}
function move() {
	var ids='';
	$("input[name='userid[]']:checked").each(function(i, n){
		ids += $(n).val() + ',';
	});
	if(ids=='') {
		window.top.art.dialog({content:'<?php echo L('plsease_select').L('people')?>',lock:true,width:'200',height:'50',time:1.5},function(){});
		return false;
	}
	window.top.art.dialog({id:'move'}).close();
	window.top.art.dialog({title:'<?php echo L('move').L('people')?>',id:'move',iframe:'?m=member&c=member&a=move&ids='+ids,width:'700',height:'500'}, function(){var d = window.top.art.dialog({id:'move'}).data.iframe;d.$('#dosubmit').click();return false;}, function(){window.top.art.dialog({id:'move'}).close()});
}

function checkuid() {
	var ids='';
	$("input[name='userid[]']:checked").each(function(i, n){
		ids += $(n).val() + ',';
	});
	if(ids=='') {
		window.top.art.dialog({content:'<?php echo L('plsease_select').L('people')?>',lock:true,width:'200',height:'50',time:1.5},function(){});
		return false;
	} else {
		myform.submit();
	}
}

function member_infomation(userid, name) {
	window.top.art.dialog({id:'modelinfo'}).close();
	window.top.art.dialog({title:'<?php echo L('memberinfo')?>',id:'modelinfo',iframe:'?m=people&c=people&a=memberinfo&userid='+userid,width:'700',height:'500'}, function(){var d = window.top.art.dialog({id:'modelinfo'}).data.iframe;d.document.getElementById('dosubmit').click();return false;}, function(){window.top.art.dialog({id:'modelinfo'}).close()});
}

//-->
</script>
</body>
</html>