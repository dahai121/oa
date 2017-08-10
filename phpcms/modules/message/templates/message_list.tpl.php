<?php
defined('IN_ADMIN') or exit('No permission resources.');
$show_dialog = 1;
include $this->admin_tpl('header','admin');
?>
<link rel="stylesheet" href="<?php echo CSS_PATH;?>zebra_tooltips.css" type="text/css">
<div class="pad-lr-10">
<!--form name="searchform" action="?m=message&c=message&a=search_message&menuid=<?php echo $_GET['menuid'];?>" method="post" >
<table width="100%" cellspacing="0" class="search-form">
    <tbody>
		<tr>
		<td><div class="explain-col"><?php echo L('query_type')?>:<?php echo form::select($trade_status,$status,'name="search[status]"', L('all'))?>      <?php echo L('username')?>:  <input type="text" value="" class="input-text" name="search[username]">  <?php echo L('time')?>:  <?php echo form::date('search[start_time]','','')?> <?php echo L('to')?>   <?php echo form::date('search[end_time]','','')?>    <input type="submit" value="<?php echo L('search')?>" class="button" name="dosubmit">
		</div>
		</td>
		</tr>
    </tbody>
</table>
</form-->
<form name="myform" action="?m=message&c=message&a=delete" method="post" onsubmit="checkuid();return false;">
<div class="table-list">
<table width="100%" cellspacing="0">
	<thead>
		<tr>
			<th width="35" align="center"><input type="checkbox" value="" id="check_box" onclick="selectall('messageid[]');"></th>
			<th><?php echo L('subject')?></th>
			<th width="35%" align="center"><?php echo L('content')?></th>
			<th width="10%" align="center"><?php echo L('fromuserid')?></th>
			<th width='10%' align="center"><?php echo L('touserid')?></th>
			<th width='10%' align="center">¸½¼þ</th>
			<th width="5%" align="center"><?php echo L('operations_manage')?></th>
		</tr>
	</thead>
<tbody>
<?php
if(is_array($infos)){
	foreach($infos as $info){
		?>
	<tr>
		<td align="center" width="35"><input type="checkbox"
			name="messageid[]" value="<?php echo $info['messageid']?>"></td>
		<td><?php echo $info['subject']?></td>
		<td align="" widht="35%"><?php echo new_html_special_chars($info['content']);?></td>
		<td align="center" width="10%"><?php if(get_memberinfo_buyusername($info['send_id'],'nickname')){echo get_memberinfo_buyusername($info['send_id'],'nickname');}else{echo $info['send_id'];}?></td>
		<td align="center" width="10%"><a href="#" class="zebra_tips1" title="
			<?php 
			$recipientid = explode(',',$info['recipientid']);
			foreach ($recipientid as $_k) {
					if(isset($_k)) {
					$message_info = pc_base::load_model('message_info_model');
					$messageinfo = $message_info->get_one(array('recipientid'=>$_k,'messageid'=>$info['messageid']));
						if(isset($messageinfo['recipientid']) || is_numeric($messageinfo['recipientid'])){
						$viewed=$messageinfo['viewed'];
						$recipientname=$messageinfo['recipientname'];
						if($viewed==0){echo "<font color=red>".$recipientname."</font>¡¡";}else{echo $recipientname."¡¡";}
						}
					}
				}
			//echo $info['recipientname'];
			?>"><?php echo str_cut($info['recipientname'],15,'')."..." ?></a></td>

		<td align="center" width="10%">
		<?php $xia = string2array($info['downfiles']);
		foreach($xia as $d){
		echo "<a href=".$d[fileurl].">".$d[filename]."</a><br />";
		}
		?>
		</td>
		<td align="center" width="5%"> <a
			href='?m=message&c=message&a=delete&messageid=<?php echo $info['messageid']?>'
			onClick="return confirm('<?php echo L('confirm', array('message' => new_addslashes($info['subject'])))?>')"><?php echo L('delete')?></a>
		</td>
	</tr>
	<?php
	}
}
?>
</tbody>
</table>
<div class="btn"><a href="#"
	onClick="javascript:$('input[type=checkbox]').attr('checked', true)"><?php echo L('selected_all')?></a>/<a
	href="#"
	onClick="javascript:$('input[type=checkbox]').attr('checked', false)"><?php echo L('cancel')?></a>
<input name="submit" type="submit" class="button"
	value="<?php echo L('remove_all_selected')?>"
	onClick="return confirm('<?php echo L('confirm', array('message' => L('selected')))?>')">&nbsp;&nbsp;</div>
<div id="pages"><?php echo $pages?></div>
</div>
</form>
</div>
<script type="text/javascript" src="<?php echo JS_PATH; ?>zebra_tooltips.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH; ?>tipsmain.js"></script>
<script type="text/javascript">

function see_all(id, name) {
	window.top.art.dialog({id:'sell_all'}).close();
	window.top.art.dialog({title:'<?php echo L('details');//echo L('edit')?> '+name+' ',id:'edit',iframe:'?m=message&c=message&a=see_all&messageid='+id,width:'700',height:'450'}, function(){var d = window.top.art.dialog({id:'see_all'}).data.iframe;var form = d.document.getElementById('dosubmit');form.click();return false;}, function(){window.top.art.dialog({id:'see_all'}).close()});
}
function checkuid() {
	var ids='';
	$("input[name='messageid[]']:checked").each(function(i, n){
		ids += $(n).val() + ',';
	});
	if(ids=='') {
		window.top.art.dialog({content:"<?php echo L('before_select_operation')?>",lock:true,width:'200',height:'50',time:1.5},function(){});
		return false;
	} else {
		myform.submit();
	}
}

</script>
</body>
</html>
