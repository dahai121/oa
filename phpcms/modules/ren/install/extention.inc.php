<?php
defined('IN_PHPCMS') or exit('Access Denied');
defined('INSTALL') or exit('Access Denied');
$parentid = $menu_db->insert(array('name'=>'ren', 'parentid'=>'29', 'm'=>'master', 'c'=>'master', 'a'=>'init', 'data'=>'', 'listorder'=>0, 'display'=>'1'), true);
$menu_db->insert(array('name'=>'add_people', 'parentid'=>$parentid, 'm'=>'master', 'c'=>'master', 'a'=>'add', 'data'=>'', 'listorder'=>0, 'display'=>'0'));
$language = array('master'=>'人事管理');
?>