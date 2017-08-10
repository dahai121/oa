<?php
	/*
	http://www.rhongsheng.com

	欢迎收听
	http://t.qq.com/rhongsheng

	接口说明：用于AJAX查询登陆状态的接口，无外挂参数
	*/
	defined('IN_PHPCMS') or exit('No permission resources.'); 
	$_usid = param::get_cookie('_userid');
	$_userinfo = get_memberinfo($_usid);
	$_userinfo['islogin'] = $_usid || 0;
	unset($_userinfo['password']);  //为了安全起见，注销了密码一栏，有需要的可去除。
	echo json_encode($_userinfo);
?>