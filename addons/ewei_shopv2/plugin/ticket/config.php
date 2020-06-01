<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

	return array(
	'version' => '1.0',
	'id'      => 'ticket',
	'name'    => '用户小票',
	'v3'      => true,
	'menu'    => array(
		'plugincom' => 1,
		'icon'      => 'page',
		'items'     => array(

			array('title' => '用户小票管理', 'route' => 'index'),
			array('title' => '小票充值记录', 'route' => 'rechargelog'),
			array('title' => '小票操作记录', 'route' => 'log')
			)
		)
	);



?>
