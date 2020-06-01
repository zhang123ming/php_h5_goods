<?php 
if(empty($_W['isfounder'])) {
	message('访问非法.');
}

$do = in_array($do, array('profile', 'device', 'callback', 'appstore', 'buyversion', 'buybranch', 'sms')) ? $do : 'profile';

load()->model('cloud');

if($do == 'profile') {
	$iframe = cloud_auth_url('profile').'&ip='.cloud_auth_ip();
	$title = '注册站点';
}

if($do == 'sms') {
	$iframe = cloud_auth_url('sms');
	$title = '酷微米云短信';
}

if($do == 'appstore') {
	$iframe = cloud_auth_url('appstore');
	$title = '应用商城';
	header("Location: $iframe");
	exit;
}

if($do == 'device') {
	$iframe = cloud_auth_url('device');
	$title = '设备';
}

if($do == 'promotion') {
	if(empty($_W['setting']['site']['key']) || empty($_W['setting']['site']['token'])) {
		message("你的程序需要在酷微米酷微米云服务平台注册你的站点资料, 来接入酷微米云平台服务后才能使用推广功能.", url('cloud/profile'), 'error');
	}
	$iframe = cloud_auth_url('promotion');
	$title = '我要推广';
}

if ($do == 'buyversion') {
	load()->func('communication');
	
	$auth = array();
	$auth['name'] = $_GPC['m'];
	$auth['is_upgrade'] = 1;
	$auth['version'] = $_GPC['version'];
	
	$url = cloud_auth_url('buyversion', $auth);
	$response = ihttp_request($url);
	if (is_error($response)) {
		message($response['message'], '', 'error');
	}
	$response = json_decode($response['content'], true);
	switch ($response['message']['errno']) {
		case '-1':
		case '-2':
			message('应用不存在或是未有更新的版本。', url('extension/module'), 'error');
		break;
		case '-3':
			message('您的交易币不足以支付此次升级费用。', url('extension/module'), 'error');
		break;
		case '2':
			message('您已经购买过此升级版本，系统将直接跳转至升级界面。', url('cloud/process', array('m' => $auth['name'], 'is_upgrade' => 1, 'is_buy' => 1)), 'success');
			break;
		case '1':
			message('购买应用升级版本成功，系统将直接跳转至升级界面。', url('cloud/process', array('m' => $auth['name'], 'is_upgrade' => 1, 'is_buy' => 1)), 'success');
			exit;
		break;
	}
	message($response['message']['message']);
}

if ($do == 'buybranch') {
	load()->func('communication');
	
	$auth = array();
	$auth['name'] = $_GPC['m'];
	$auth['branch'] = intval($_GPC['branch']);

	$url = cloud_auth_url('buybranch', $auth);
	
	$response = ihttp_request($url);
	$response = json_decode($response['content'], true);

	if (is_error($response['message'])) {
		message($response['message']['message'], url('extension/module'), 'error');
	}

	$params = array(
		'is_upgrade' => 1,
		'is_buy' => 1,
	);
	if (trim($_GPC['type']) == 'theme') {
		$params['t'] = $auth['name'];
	} else {
		$params['m'] = $auth['name'];
	}

	message($response['message']['message'], url('cloud/process', $params), 'success');
}

if($do == 'callback') {
	$secret = $_GPC['token'];
	if(strlen($secret) == 32) {
		$cache = cache_read('cloud:auth:transfer');
		cache_delete('cloud:auth:transfer');
		if(!empty($cache) && $cache['secret'] == $secret) {
			$site = $cache;
			unset($site['secret']);
			setting_save($site, 'site');
			$auth = array();
			$auth['key'] = $site['key'];
			$auth['password'] = md5($site['key'] . $site['token']);
			$url = cloud_auth_url('profile', $auth);
			header('Location: ' . $url);
			exit();
		}
	}
	message('访问错误.');
}

template('cloud/frame');
