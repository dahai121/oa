function time()
{
  var now= new Date();
  var year=now.getFullYear();
  var month=now.getMonth();
  var date=now.getDate();
  var week=new Array("������","����һ","���ڶ�","������","������","������","������");
  var echoweek = week[now.getDay()];

  //д����Ӧid
  document.getElementById("echoData").innerHTML="�����ǣ�"+year+"��"+(month+1)+"��"+date+"��"+"&nbsp;&nbsp;"+echoweek;
} 