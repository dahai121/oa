function time()
{
  var now= new Date();
  var year=now.getFullYear();
  var month=now.getMonth();
  var date=now.getDate();
  var week=new Array("星期日","星期一","星期二","星期三","星期四","星期五","星期六");
  var echoweek = week[now.getDay()];

  //写入相应id
  document.getElementById("echoData").innerHTML="今天是："+year+"年"+(month+1)+"月"+date+"日"+"&nbsp;&nbsp;"+echoweek;
} 