<?php
defined('IN_PHPCMS') or exit('Access Denied');
defined('INSTALL') or exit('Access Denied');
$parentid = $menu_db->insert(array('name'=>'oattendance', 'parentid'=>'29', 'm'=>'oattendance', 'c'=>'oattendance', 'a'=>'init', 'data'=>'', 'listorder'=>0, 'display'=>'1'), true);
$menu_db->insert(array('name'=>'atten_count', 'parentid'=>$parentid, 'm'=>'oattendance', 'c'=>'oattendance', 'a'=>'attenstatic', 'data'=>'', 'listorder'=>0, 'display'=>'1'));

$language = array('oattendance'=>'¿¼ÇÚ²éÑ¯', 'atten_count'=>'¿¼ÇÚÍ³¼Æ');
?>