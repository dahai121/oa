<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header','admin');?>
<form name="myform" action="" method="post">
<div class="table-list">
    <table width="100%" cellspacing="0" >
            <tr>
			 <th width="70">��������</th>
				<td>
					<select name="info[pid]" id="">
						<option value="0">--��������--</option>
						<?php foreach ($data as $v){ ?>
						<option value="<?php echo $v['id'] ?>"><?php echo  $v['name'] ?></option>
						<?php } ?>
					</select>
				</td>
            </tr>
		<tr>
			<th>�������ƣ�</th>
			<td>
				<input class="common-text required" id="title" name="info[name]" size="50" value="" type="text">
			</td>
		</tr>
    </tbody>
    </table>
   <div class="btn">
	   <input type="submit" class="button" name="dosubmit" value="<?php echo L('�ύ');?>" />
   </div>
</div>
</form>
</div>
</body>
</html>
