<?php
/**
 * [WeEngine System] Copyright (c) 2014 WE7.CC
 * WeEngine is NOT a free software, it under the license terms, visited http://www.we7.cc/ for more details.
 */
defined('IN_IA') or exit('Access Denied');

load()->model('cloud');
load()->func('communication');
load()->func('db');

$r = cloud_prepare();
if (is_error($r)) {
	itoast($r['message'], url('cloud/profile'), 'error');
}

$dos = array('upgrade');
$do = in_array($do, $dos) ? $do : 'upgrade';
uni_user_permission_check('system_cloud_upgrade');

if (empty($_W['setting']['site']['profile_perfect'])) {
	}
if ($do == 'upgrade') {
}
template('cloud/upgrade');