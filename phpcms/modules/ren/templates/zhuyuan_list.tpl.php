<?php defined('IN_ADMIN') or exit('No permission resources.');?>
<?php include $this->admin_tpl('header', 'admin');?>
<div class="pad-lr-10">
<form name="searchform" action="" method="get" >
<input type="hidden" value="ren" name="m">
<input type="hidden" value="master" name="c">
<input type="hidden" value="search" name="a">
<input type="hidden" value="879" name="menuid">
<table width="100%" cellspacing="0" class="search-form">
    <tbody>
		<tr>
		<td>
		<div class="explain-col">
			��Ա����3��
			<select name="rylx">
				<option value='77' <?php if(isset($_GET['rylx']) && $_GET['rylx']==77){?>selected<?php }?>><?php echo L('��ְ')?></option>
				<option value='79' <?php if(isset($_GET['rylx']) && $_GET['rylx']==79){?>selected<?php }?>><?php echo L('����')?></option>
				<option value='78' <?php if(isset($_GET['rylx']) && $_GET['rylx']==78){?>selected<?php }?>><?php echo L('����')?></option>
				<option value='80' <?php if(isset($_GET['rylx']) && $_GET['rylx']==80){?>selected<?php }?>><?php echo L('������')?></option>
			</select>
			<?php echo L('�������')?>��
			<?php echo form::date('start_time', $start_time)?>-
			<?php echo form::date('end_time', $end_time)?>
			������<input name="keyword" type="text" value="<?php if(isset($_GET['keyword'])) {echo $_GET['keyword'];} ?>" class="input-text" />
				 <input type="submit" name="search" class="button" value="<?php echo L('search')?>" />
	</div>
		</td>
		</tr>
    </tbody>
</table>
</form>

<form name="myform" action="?m=ren&c=health&a=delete" method="post" onsubmit="checkuid();return false;">
<div class="table-list">
<table width="100%" cellspacing="0">
	<thead>
		<tr>
			<th  align="left" width="20"><input type="checkbox" value="" id="check_box" onclick="selectall('userid[]');"></th>
			<th align="left"><?php echo L('id')?></th>
			<th align="left"><?php echo L('����')?></th>
			<th align="left"><?php echo L('ְ��')?></th>
			<th align="left"><?php echo L('���')?></th>
			<th align="left"><?php echo L('ҽ�ƴ���')?></th>
			<th align="left"><?php echo L('��Ҫ��֢')?></th>
			<th align="left"><?php echo L('������Ϣ')?></th>
			<th align="left"><?php echo L('�Ƿ�����')?></th>

		</tr>
	</thead>
<tbody>
<?php
	if(is_array($data)){
	foreach($data as $k=>$v) {
?>
    <tr>
		<td align="left"><input type="checkbox" value="<?php echo $v['id']?>" name="userid[]"></td>
		<td align="left"><?php echo $v['id']?></td>
		<td align="left">
			<a href="javascript:;" onclick="javascript:openwinx('?m=ren&c=health&a=edit&id=<?php echo $v['id'] ?>','')"> <?php echo $v['uinfo']['title'] ?> </a>
		</td>
		<td align="left"><?php echo $v['uinfo']['zj'] ?></td>
		<td align="left"><?php echo $v['ybxh']?></td>
		<td align="left">
			<?php if($v['ybxh'] == 42 ){echo "ҽ��";}elseif($v['ybxh'] == 41){ echo "��ҽ��";}?>
		</td>
		<td align="left"><?php echo $v['zybz']?></td>
		<td align="left"><a>������Ϣ</a></td>
		<td align="left"><?php  if($v['islock'] =='1' ){echo  "����";}else{echo "δ����";}?></td>

    </tr>
<?php
	}
}
?>
</tbody>
</table>

<div class="btn">
<label for="check_box"><?php echo L('select_all')?>/<?php echo L('cancel')?></label>
	<input type="submit" class="button" name="dosubmit" onclick="document.myform.action='?m=ren&c=health&a=lock'" value="<?php echo L('lock')?>"/>
	<input type="submit" class="button" name="dosubmit" onclick="document.myform.action='?m=ren&c=health&a=unlock'" value="<?php echo L('unlock')?>"/>
	<input type="submit" class="button" name="dosubmit" value="<?php echo L('delete')?>" onclick="return confirm('<?php echo L('sure_delete')?>')"/>
<!--input type="button" class="button" name="dosubmit" onclick="move();return false;" value="<!--?php echo L('move')?>"/-->
</div>

<div id="pages"><?php echo $pages?></div>
</div>
</form>
</div>
<script type="text/javascript">
<!--
function edit(id, name) {
	window.top.art.dialog({id:'edit'}).close();
	window.top.art.dialog({title:'<?php echo L('edit').L('people')?>��'+name+'��',id:'edit',iframe:'?m=ren&c=health&a=edit&userid='+id,width:'400',height:'300'}, function(){var d = window.top.art.dialog({id:'edit'}).data.iframe;d.document.getElementById('dosubmit').click();return false;}, function(){window.top.art.dialog({id:'edit'}).close()});
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