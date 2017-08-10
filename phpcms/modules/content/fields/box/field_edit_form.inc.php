<?php defined('IN_PHPCMS') or exit('No permission resources.');?>
<table cellpadding="2" cellspacing="1" width="98%">
	<tr> 
      <td>选项类型</td>
      <td>
		  <?php foreach ($xia_info as $k => $v){ ?>
			  <input type="radio" name="setting[boxtype]" value="<?php echo $v['id'] ?>" <?php if($v['id'] == $setting['boxtype']){ echo "checked"; } ?> /><?php echo $v['name']?>
		  <?php } ?>
	  </td>
    </tr>


</table>
<SCRIPT LANGUAGE="JavaScript">
<!--
	function fieldtype_setting(obj) {
	if(obj!='varchar') {
		$('#minnumber').css('display','');
	} else {
		$('#minnumber').css('display','none');
	}
}


//-->
</SCRIPT>