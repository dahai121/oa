<?php
defined('IN_ADMIN') or exit('No permission resources. - oacarbook_list.tpl.php');
include $this->admin_tpl('header', 'admin');
?>
<div class="pad-lr-10" style="width:80%">
<form name="searchform" action="?m=oacarmgn&c=oacarmgn&a=select&menuid=<?php echo $_GET['menuid'];?>" method="post" >
<table cellspacing="0" class="search-form">
    <tbody>
		<tr>
		<td><div class="explain-col">����:  <?php echo form::select($depart_status,$departid,'name="search[departid]" id="departlist"', L('all'))?>&nbsp;&nbsp;&nbsp;&nbsp;����:  <?php echo form::date('search[start_time]',$start_time,'')?> ��   <?php echo form::date('search[end_time]',$end_time,'')?>    <input type="submit" value="  ��ѯ  " class="button" name="dosubmit">&nbsp;&nbsp;&nbsp;&nbsp;<a href="?m=oacarmgn&c=index&a=init" target="_blank"><input type="button" value="  ԤԼ����  " class="button"></a>
		</div>
		</td>
		</tr>
    </tbody>
</table>
</form>
<form action="?m=oacarmgn&c=oacarmgn&a=deletecarbook" method="post" name="myform" id="myform">
<div class="table-list">
<table cellspacing="0" style="width:100%">
	<thead>
    <tr>
        <th width="5%"><input type="checkbox" /></th><th width="10%">��������</th><th width="10%">ԤԼ��</th>
		<th width="10%">ԤԼ����</th><th width="15%">ԤԼʱ��</th><th width="15%">��ע</th>
		<th width="15%">��������</th><th width="10%">�������</th>
    </tr>
	</thead>
	<tbody>
    <?php
    if(is_array($infos)){
		$btime='';
    foreach($infos as $info){
		switch($info['btime']){
			case '1':
				$btime='����';
				break;
			case '2':
				$btime='����';
				break;
			case '3':
				$btime='ȫ��';
				break;
		}
    ?>
    <tr>
        <td align="center"><input type="checkbox" name="bid[]" value="<?php echo $info['bid']?>"></td>
        <td align="center"><?php echo $info['carname']?></td>
        <td align="center"><?php echo $info['bperson']?></td>
        <td align="center"><?php echo $info['bdeparment'];?></td>
        <td align="center"><?php echo date('Y-m-d',strtotime($info['bdate'])).' '.$btime;?></td>
		<td align="center"><?php echo $info['comment'];?></td>
        <td align="center"><?php echo date('Y-m-d H:i:s',strtotime($info['addtime']));?></td>
        <td align="center"><!-- ������� -->
        <a href="###" onclick="edit(<?php echo $info['bid']?>, '<?php echo new_addslashes($info['bperson'])?>')" title="�޸�">�޸�</a> |
        <a href='?m=oacarmgn&c=oacarmgn&a=deletecarbook&bid=<?php echo $info['bid']?>'
         onClick="return confirm('<?php echo L('confirm', array('message' => new_addslashes($info['bperson'])))?>')">
         <?php echo L('ɾ��')?>
        </a>
        </td>
    </tr>
    <?php } } ?>
	</tbody>
</table>
<div class="btn">
<input type="submit" name="dosubmit" id="dosubmit" value="<?php echo L(' ɾ�� ')?>" /></div>
<div id="pages"><?php echo $pages?></div>
</div>
</form>
</div>
</body>
</html>
<script type="text/javascript">
function edit(id, name) {
	window.top.art.dialog({id:'edit'}).close();
	window.top.art.dialog({title:'<?php echo L('edit')?> '+name+' ',id:'edit',iframe:'?m=oacarmgn&c=oacarmgn&a=updatecarbook&bid='+id,width:'600',height:'320'}, function(){var d = window.top.art.dialog({id:'edit'}).data.iframe;var form = d.document.getElementById('dosubmit');form.click();return false;}, function(){window.top.art.dialog({id:'edit'}).close()});
}
</script>