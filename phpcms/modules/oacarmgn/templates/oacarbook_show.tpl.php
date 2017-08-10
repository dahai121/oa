<?php defined('IN_ADMIN') or exit('No permission resources. - oacarbook_show.tpl.php');
$show_dialog = 1;
include $this->admin_tpl('header', 'admin');
$url=dirname('http://'.$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"]); 
$basepath=$url.'/phpcms/modules/oacarmgn/templates/';
?>
<html>
<head>
<meta charset="utf-8">
<link rel="stylesheet" type="text/css" href="<?php echo $basepath?>fullcalendar/fullcalendar.css">
<link rel="stylesheet" type="text/css" href="<?php echo $basepath?>fullcalendar/fullcalendar.print.css">
<script src='<?php echo $basepath?>fullcalendar/fullcalendar.js'></script>
<script type="text/javascript">
/** calendar配置 **/
$(document).ready(function() {
	var date = new Date();
	$('#calendar').fullCalendar({
		header : {
			left : 'prev,next',
			center : 'title',
			right : 'today'
		},
		/*buttonText: {
				prev: "<span class='fc-text-arrow'>&lsaquo;上一月</span>",
				next: "<span class='fc-text-arrow'>下一月&rsaquo;</span>",
		},*/
		editable: true,
		firstDay:0,//每行第一天为周日
		//events: <?php echo $show_data?>,
		viewDisplay:function(view){
			var date =$.fullCalendar.formatDate(view.start,'yyyy-MM-dd');
			$('#calendar').fullCalendar('removeEvents');
			$.ajax({
				type:'POST',
				url:'?m=oacarmgn&c=oacarmgn&a=public_ajax_getblist',
				dataType:'json',
				data:"seldate="+date,
				success:function(data){
					if(typeof data == "underfined" || data == "null" || data == "" || data.length==0){
						return;
					}else{
						//var showdata = jQuery.parseJSON(data);
						$.each(data,function(index,term){
							$('#calendar').fullCalendar('renderEvent',term,true);
						});
					}
				}
			});
		},
		dayClick: function(date, allDay, jsEvent, view) {
			var selDate =$.fullCalendar.formatDate(date,'yyyy+MM+dd');
			window.top.art.dialog({id:'add',iframe:'?m=oacarmgn&c=oacarmgn&a=addbook&selDate='+selDate, title:'车辆预约', width:'600', height:'200'}, function(){var d = window.top.art.dialog({id:'add'}).data.iframe;var form = d.document.getElementById('dosubmit');form.click();return false;}, function(){window.top.art.dialog({id:'add'}).close()});
			void(0);
    	}
	});
});
</script>
</head>
<body>
<div id="main" style="width:90%">
   <div><center><h4 id="booktitle"></h4></center></div>
   <div style="width:90%; margin:20px auto 10px auto;" id='calendar'></div>
</div>
</body>
</html>