<?php
defined('IN_PHPCMS') or exit('Access Denied');
defined('INSTALL') or exit('Access Denied');
$parentid = $menu_db->insert(array('name'=>'oacarmgn', 'parentid'=>'29', 'm'=>'oacarmgn', 'c'=>'oacarmgn', 'a'=>'init', 'data'=>'', 'listorder'=>0, 'display'=>'1'), true);
$menu_db->insert(array('name'=>'car_list', 'parentid'=>$parentid, 'm'=>'oacarmgn', 'c'=>'oacarmgn', 'a'=>'carlist', 'data'=>'', 'listorder'=>0, 'display'=>'1'));
$menu_db->insert(array('name'=>'car_add', 'parentid'=>$parentid, 'm'=>'oacarmgn', 'c'=>'oacarmgn', 'a'=>'addcar', 'data'=>'', 'listorder'=>0, 'display'=>'1'));

$language = array('oacarmgn'=>'预约列表', 'car_list'=>'车辆列表', 'car_add'=>'车辆添加');
?>