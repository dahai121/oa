<?php
defined('IN_ADMIN') or exit('No permission resources.');
include $this->admin_tpl('header','admin');
?>
<script type="text/javascript">
<!--
	$(function(){
	$.formValidator.initConfig({formid:"myform",autotip:true,onerror:function(msg,obj){window.top.art.dialog({content:msg,lock:true,width:'200',height:'50'}, function(){this.close();$(obj).focus();})}});
	$("#carname").formValidator({onshow:"<?php echo L("input").L('车辆名称')?>",onfocus:"<?php echo L("input").L('车辆名称')?>"}).inputValidator({min:1,onerror:"<?php echo L("input").L('车辆名称')?>"}).regexValidator({regexp:"notempty",datatype:"enum",param:'i',onerror:"<?php echo L('input_not_space')?>"});
	$("#cartype").formValidator({onshow:"<?php echo L("input").L('车牌号')?>",onfocus:"<?php echo L("input").L('车牌号')?>"}).inputValidator({min:1,onerror:"<?php echo L("input").L('车牌号')?>"}).regexValidator({regexp:"notempty",datatype:"enum",param:'i',onerror:"<?php echo L('input_not_space')?>"});	
	});
//-->
</script>
<form action="index.php?m=oacarmgn&c=oacarmgn&a=addcar" method="post" name="myform" id="myform">
<table cellpadding="2" cellspacing="1" class="table_form" width="100%">

	<tr>
		<th width="20%">车辆名称 :</th>
		<td><input type="text" name="oacar[carname]" id="carname"></td>
	</tr>
	<tr>
		<th>车牌号 :</th>
		<td><input type="text" name="oacar[cartype]" id="cartype"></td>
	</tr>
	<tr>
		<th>车辆备注 :</th>
		<td><textarea name="oacar[comment]" id="comment" cols="60" rows="1"></textarea></td>
	</tr>
	<tr><td>&nbsp;</td><td>
		<input type="submit" name="dosubmit" id="dosubmit" class="button" value=" <?php echo L('submit')?> ">&nbsp;<input type="reset" value=" 清除 " class="button"></td>
	</tr>
</table>
</form>
</body>
</html>