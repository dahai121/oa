<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header','admin');?>
<form name="myform" action="" method="post">
<div class="table-list">
    <table width="100%" cellspacing="0" >
            <tr>
			 <th width="70">父类名称</th>
				<td>
					<select name="info[pid]" id="">
						<option value="0">--顶级分类--</option>
						<?php foreach ($data as $v){ ?>
						<option value="<?php echo $v['id'] ?>"><?php echo  $v['name'] ?></option>
						<?php } ?>
					</select>
				</td>
            </tr>
		<tr>
			<th>下拉名称：</th>
			<td>
				<input class="common-text required" id="title" name="info[name]" size="50" value="" type="text">
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
