<?php
defined('IN_PHPCMS') or exit('Access Denied');
defined('INSTALL') or exit('Access Denied');
$parentid = $menu_db->insert(array('name'=>'import', 'parentid'=>29, 'm'=>'import', 'c'=>'import', 'a'=>'init', 'data'=>'', 'listorder'=>0, 'display'=>'1'), true);
$menu_db->insert(array('name'=>'add_import', 'parentid'=>$parentid, 'm'=>'import', 'c'=>'import', 'a'=>'add', 'data'=>'', 'listorder'=>0, 'display'=>'1'));
$menu_db->insert(array('name'=>'delete_import', 'parentid'=>$parentid, 'm'=>'import', 'c'=>'import', 'a'=>'delete', 'data'=>'', 'listorder'=>0, 'display'=>'0'));
$language = array('import'=>'�ⲿ���ݵ���', 'add_import'=>'������ݵ������', 'delete_import'=>'ɾ�����ݵ�������');
?>