<?php 

defined('IN_IA') or exit('Access Denied');

load()->model('system');

$dos = array('site_setting');
$do = in_array($do, $dos) ? $do : 'site_setting';
$_W['page']['title'] = '站点设置 - 工具  - 系统管理';

$site = $_W['setting']['site'];

if(empty($site) || !is_array($site)) {
	$site = array();
} else {
	$site = iunserializer($site);
}

	
if ($do == 'site_setting') {
	
		
	if ($_W['ispost']) {
		
			$data = array(
				'sitename' => intval($_GPC['sitename']),
				'siteurl' => intval($_GPC['siteurl']),
				'ip' => trim($_GPC['ip']),
				'key' => trim($_GPC['key']),
				'password' => trim($_GPC['password']),
			);
		
		

		$test = setting_save($data, 'site');

		itoast('更新设置成功！', url('system/site_setting'), 'success');
	}
}

template('system/site_setting');