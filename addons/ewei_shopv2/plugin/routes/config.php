<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

return array(
	'version' => '1.0',
	'id'      => 'routes',
	'name'    => '智慧行程',
	'v3'      => true,
	'menu'    => array(
		'plugincom' => 1,
		'icon'      => 'page',
		'items'     => array(
			array('title' => '路线管理', 'route' => 'routes'),
			array('title' => '车辆管理', 'route' => 'bus'),
			array('title' => '员工管理', 'route' => 'staff')
		)
	)
);