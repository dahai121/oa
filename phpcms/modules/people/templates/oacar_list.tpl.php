<?php
defined('IN_ADMIN') or exit('No permission resources. - oacar_list.tpl.php');
$show_dialog = 1;
include $this->admin_tpl('header', 'admin');
?>
<div class="pad-lr-10" style="width:60%">
<form name="myform" id="myform" action="?m=oacarmgn&c=oacarmgn&a=deletecar" method="post">
<div class="table-list">
<table cellspacing="0" style="width:100%">
	<thead>
    <tr>
        <th><input type="checkbox" /></th><th>车辆名称</th><th>车辆类型</th><th>登记时间</th><th>备注</th><th>管理操作</th>
    </tr>
	</thead>
	<tbody>
    <?php
    if(is_array($infos)){
    foreach($infos as $info){
    ?>
    <tr>
        <td align="center" width="5%"><input type="checkbox" name="carid[]" value="<?php echo $info['carid']?>"></td>
        <td align="center" width="10%"><?php echo $info['carname']?></td>
        <td align="center" width="10%"><?php echo $info['cartype']?></td>
        <td align="center" width="20%"><?php echo date('Y-m-d H:i:s',strtotime($info['cardate']));?></td>
		<td align="center" width="25%"><?php echo $info['comment']?></td>
        <td align="center" width="15%">
        <a href="###" onclick="edit(<?php echo $info['carid']?>, '<?php echo new_addslashes($info['carname'])?>')" title="修改信息">修改</a> |
        <a href='?m=oacarmgn&c=oacarmgn&a=deletecar&carid=<?php echo $info['carid']?>'
         onClick="return confirm('<?php echo L('confirm', array('message' => new_addslashes($info['carname'])))?>')">
         <?php echo L('删除')?>
        </a>
        </td>
    </tr>
    <?php } } ?>
	</tbody>
</table>
<div class="btn">
<input type="submit" name="dosubmit" value="<?php echo L(' 删除 ')?>" />
</div>
</div>
</form>
</div>
</body>
</html>
<script type="text/javascript">
function edit(id, name) {
	window.top.art.dialog({id:'edit'}).close();
	window.top.art.dialog({title:'<?php echo L('edit')?> '+name+' ',id:'edit',iframe:'?m=oacarmgn&c=oacarmgn&a=updatecar&carid='+id,width:'500',height:'150'}, function(){var d = window.top.art.dialog({id:'edit'}).data.iframe;var form = d.document.getElementById('dosubmit');form.click();return false;}, function(){window.top.art.dialog({id:'edit'}).close()});
}
</script>