<?php
echo "\r\n";

if (!defined('IN_IA')) {
	exit('Access Denied');
}

return array(
	'version' => '1.0',
	'id'      => 'rebate',
	'name'    => '返利奖励',
	'v3'      => true,
	'menu'    => array(
		'title'     => '页面',
		'plugincom' => 1,
		'icon'      => 'page',
		'items'     => array(
			array('title' => '返利设置', 'route' => ''),
			array('title' => '参与记录','route'=>'log'),
			// array('title' => '基本设置', 'route' => 'setting')
			)
		)
	);

?>
