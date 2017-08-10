<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header','admin');
$selDate = str_replace(' ','-',$_GET['selDate']);
?>
<script type="text/javascript">
</script>
<div class="pad-lr-10">
<form action="index.php?m=oacarmgn&c=oacarmgn&a=updatecarbook&bid=<?php echo $bid?>" method="post" name="myform" id="myform">
<table cellpadding="2" cellspacing="1" class="table_form" width="100%">

	<tr>
		<th width="20%">车辆名称 :</th>
		<td><select name="oacarbook[carname]" id="carname">
		<?php
			$i=0;
			foreach($carlist as $carid=>$car_group){
				$i++;
		?>
		<option value="<?php echo $car_group['carname'];?>" <?php if($car_group['carname']==$carname){?>selected<?php }?>><?php echo $car_group['carname'];?></option>
		<?php }?>
		</select></td>
	</tr>
	<tr>
		<th>预约日期 :</th>
		<td><?php echo form::date('oacarbook[bdate]',$bdate)?></td>
	</tr>
	<tr>
		<th>具体时间 :</th>
		<td><p><label><input type="radio" value="1" id="btime" name="oacarbook[btime]" <?php if(1==$btime){?>checked<?php }?> > 上午</label>
		<label><input type="radio" value="2" id="btime" name="oacarbook[btime]" <?php if(2==$btime){?>checked<?php }?>> 下午</label>
		<label><input type="radio" value="3" id="btime" name="oacarbook[btime]" <?php if(3==$btime){?>checked<?php }?>> 全天</label></p>	
		</td>
	</tr>
	<tr>
		<th>备注 :</th>
		<td><textarea name="oacarbook[comment]" id="comment" cols="60" rows="4"><?php echo $comment;?></textarea></td>
	</tr>
	<input type="submit" name="dosubmit" id="dosubmit" class="dialog"
		value=" <?php echo L('submit')?> "></td>
</table>
</form>
</div>
</body>
</html>