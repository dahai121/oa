<?php
defined('IN_ADMIN') or exit('No permission resources');
include $this->admin_tpl('header','admin');
$url=dirname('http://'.$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"]); 
$basepath=$url.'/phpcms/modules/oattendance/templates/';
$month = $month-1;
?>
<link href="{CSS_PATH}dialog.css" rel="stylesheet" type="text/css" />
<script src="{JS_PATH}dialog.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo $basepath?>/fullcalendar/fullcalendar.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo $basepath?>fullcalendar/fullcalendar.print.css"/>
<script src='<?php echo $basepath?>fullcalendar/fullcalendar.js'></script>
<script type="text/javascript">
/** calendar配置 **/
$(document).ready(function() {
	$('#calendar').fullCalendar({
		header : {
			left : '',
			center : 'title',
			right : ''
		},
		titleFormat:{month:'yyyy年MM月 <?php echo $username?>考勤日历'},
		//weekends: false,
		aspectRatio: 1.6,
		firstDay:0,
		editable: false,
		events:<?php echo $event_data?>
	});
	$('#calendar').fullCalendar('gotoDate','<?php echo $year?>','<?php echo $month?>');
});
</script>
<style>
#calendar {
width:88%;
margin:0 20px 0 20px;
float:center;
}
</style>
<!--main-->
<div id='calendar'></div>
</body>
</html>