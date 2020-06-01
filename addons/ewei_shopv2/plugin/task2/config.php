<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

$isnew = false;
require_once 'core/model.php';
$isnew = new Task2Model();


if ($isnew) {
	return array(
	'version' => '2.0',
	'id'      => 'task',
	'name'    => '招商中心',
	'v3'      => true,
	'menu'    => array(
		'plugincom' => 1,
		'items'     => array(
			array('title' => '任务概述', 'route' => 'task.main'),
			array('title' => '任务管理', 'route' => 'tasklist'),
			array('title' => '任务记录', 'route' => 'record'),
			array('title' => '入口设置', 'route' => 'setting')
			)
		)
	);
}

?>
