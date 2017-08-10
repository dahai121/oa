<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header','admin');
?>
<div class="pad-lr-10">
<form action="index.php?m=oattendance&c=oattendance&a=update&attid=<?php echo $attid?>" method="post" name="myform" id="myform">
<table cellpadding="2" cellspacing="1" class="table_form" width="100%">
	<tr>
		<th>姓名:</th>
		<td><input type="text" name="uname" id="uname" value="<?php echo $uname ?>" readonly></td>
	</tr>
	<tr>
		<th>处室:</th>
		<td><input type="text" name="dname" id="dname" value="<?php echo $dname ?>" readonly></td>
	</tr>
	<tr>
		<th width="60">选择类型:</th>
		<td><select name="atten[attype]" id="attype">
		<?php
			$i=0;
			foreach($attypelist as $key=>$val){
				$i++;
		?>
		<option value="<?php echo $key ?>" <?php if($key==$attype){?>selected<?php } ?>>&nbsp;&nbsp;<?php echo $val ?>&nbsp;&nbsp;</option>
		<?php } ?></select></td>
	</tr>
	<tr>
		<th>考勤日期:</th>
		<td><input type="text" name="atten[attdate]" id="attdate" value="<?php echo $attdate;?>" readonly></td>
	</tr>
	<tr>
		<th>备注:</th>
		<td><textarea name="atten[comment]" id="comment" cols="60" rows="1"><?php echo $comment;?></textarea></td>
	</tr>
	<tr><th></th><td>
		<input type="submit" name="dosubmit" id="dosubmit" class="dialog" value=" <?php echo L('submit')?> "></td>
	</tr>
</table>
</form>
</div>
</body>
</html>