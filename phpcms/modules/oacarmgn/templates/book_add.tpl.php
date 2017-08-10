<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header','admin');
$selDate = str_replace(' ','-',$_GET['selDate']);
?>
<script type="text/javascript">
</script>
<div class="pad_10">
<form action="index.php?m=oacarmgn&c=oacarmgn&a=addbook" method="post" name="myform" id="myform">
<table cellpadding="2" cellspacing="1" class="table_form" width="100%">

	<tr>
		<th width="20%">车辆名称 :</th>
		<td><select name="oacarbook[carname]" id="carname">
		<?php
			$i=0;
			foreach($carlist as $carid=>$car_group){
				$i++;
		?>
		<option value="<?php echo $car_group['carname'];?>"><?php echo $car_group['carname'];?></option>
		<?php }?>
		</select></td>
	</tr>
	<tr>
		<th>预约日期 :</th>
		<td><input type="text" name="oacarbook[bdate]" id="bdate" value="<?php echo $selDate;?>" readonly>
		<label><input type="radio" value="1" id="btime" name="oacarbook[btime]" checked> 上午</label>
		<label><input type="radio" value="2" id="btime" name="oacarbook[btime]"> 下午</label>
		<label><input type="radio" value="3" id="btime" name="oacarbook[btime]"> 全天</label></p>	
		</td>
	</tr>
	<tr>
		<th>备注 :</th>
		<td><textarea name="oacarbook[comment]" id="comment" cols="60" rows="1"></textarea></td>
	</tr>
	<input type="submit" name="dosubmit" id="dosubmit" class="dialog"
		value=" <?php echo L('submit')?> ">
</table>
</form>
</div>
</body>
</html>