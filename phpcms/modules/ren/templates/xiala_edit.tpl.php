<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header','admin');?>
<form name="myform" action="" method="post">
<div class="table-list">
    <table width="100%" cellspacing="0" >
		<tr>
			<th>下拉名称：</th>
			<td>
				<input class="common-text required" id="" name="info[name]" size="50" value="<?php echo $data['name'] ?>" type="text">
				<input type="hidden" name="info[id]" value="<?php echo $data['id']?> ">
			</td>
		</tr>
    </tbody>
    </table>
   <div class="btn">
	   <input type="submit" class="button" name="dosubmit" value="<?php echo L('提交');?>" />
   </div>
</div>
</form>
</div>
</body>
</html>
