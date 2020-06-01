<?php 
/**
 * 
 * 
 */
$_W['page']['title'] = '酷微米云服务诊断 - 酷微米云服务';
$dos = array('display', 'testapi');
$do = in_array($do, $dos) ? $do : 'display';

if ($do == 'testapi') {
	load()->model('cloud');
	$starttime = microtime(true);
	$response = cloud_request('http://cloud.kuweimi.com', array(), array('ip' => $_GPC['ip']));
	$endtime = microtime(true);
	iajax(0,'请求接口成功，耗时 '.(round($endtime - $starttime, 5)).' 秒');
} else {
	if(checksubmit()) {
		load()->model('setting');
		setting_save('', 'site');
		message('成功清除站点记录.', 'refresh');
	}
	if (checksubmit('updateserverip')) {
		load()->model('setting');
		if (!empty($_GPC['ip'])) {
			setting_save(array('ip' => $_GPC['ip'], 'expire' => TIMESTAMP + 201600), 'cloudip');
		} else {
			setting_save(array(), 'cloudip');
		}
		message('修改酷微米云服务ip成功.', 'refresh');
	}
	if(empty($_W['setting']['site'])) {
		$_W['setting']['site'] = array();
	}
	$checkips = array();
	if (!empty($_W['setting']['cloudip']['ip'])) {
		$checkips[] = $_W['setting']['cloudip']['ip'];
	}
	if (strexists(strtoupper(PHP_OS), 'WINNT')) {
		$cloudip = gethostbyname('cloud.kuweimi.com');
		if (!in_array($cloudip, $checkips)) {
			$checkips[] = $cloudip;
		}
	} else {
		for ($i = 0; $i <= 10; $i++) {
			$cloudip = gethostbyname('cloud.kuweimi.com');
			if (!in_array($cloudip, $checkips)) {
				$checkips[] = $cloudip;
			}
		}
	}
	template('cloud/diagnose');
}
