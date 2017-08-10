<?php
defined('IN_PHPCMS') or exit('Access Denied');
defined('INSTALL') or exit('Access Denied');
$parentid = $menu_db->insert(array('name'=>'people', 'parentid'=>'29', 'm'=>'people', 'c'=>'people', 'a'=>'init', 'data'=>'', 'listorder'=>0, 'display'=>'1'), true);
$menu_db->insert(array('name'=>'add_people', 'parentid'=>$parentid, 'm'=>'people', 'c'=>'people', 'a'=>'add', 'data'=>'', 'listorder'=>0, 'display'=>'0'));
$language = array('peopel'=>'人事管理');
?>