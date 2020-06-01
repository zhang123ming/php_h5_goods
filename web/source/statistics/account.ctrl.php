<?php
/**
 * [WeEngine System] Copyright (c) 2014 WE7.CC
 * WeEngine is NOT a free software, it under the license terms, visited http://www.we7.cc/ for more details.
 */
defined('IN_IA') or exit('Access Denied');

load()->model('statistics');

$dos = array('display', 'get_account_api');
$do = in_array($do, $dos) ? $do : 'display';

if ($do == 'display') {
	$today = stat_visit_app_byuniacid('today', '', array(), true);
	$yesterday = stat_visit_app_byuniacid('yesterday', '', array(), true);
	$today_module_api = stat_all_visit_statistics('all_account', $today);
	$yesterday_module_api = stat_all_visit_statistics('all_account', $yesterday);
}

if ($do == 'get_account_api') {
	$accounts = array();
	$data = array();
	$account_table = table('account');
	$account_table->searchWithType(array(ACCOUNT_TYPE_OFFCIAL_NORMAL, ACCOUNT_TYPE_OFFCIAL_AUTH));
	$account_table->accountRankOrder();
	$account_list = $account_table->searchAccountList();
	foreach ($account_list as $key => $account) {
		$account_list[$key] = uni_fetch($account['uniacid']);
		$accounts[] = mb_substr($account_list[$key]['name'], 0, 5, 'utf-8');
	}

	$support_type = array(
		'time' => array('today', 'week', 'month', 'daterange'),
		'divide' => array('bysum', 'byavg', 'byhighest'),
	);
	$type = trim($_GPC['time_type']);
	$divide_type = trim($_GPC['divide_type']);
	if (!in_array($type, $support_type['time']) || !in_array($divide_type, $support_type['divide'])) {
		iajax(-1, '参数错误！');
	}
	$daterange = array();
	if (!empty($_GPC['daterange'])) {
		$daterange = array(
			'start' => date('Ymd', strtotime($_GPC['daterange']['startDate'])),
			'end' => date('Ymd', strtotime($_GPC['daterange']['endDate'])),
		);
	}
	$result = stat_visit_app_bydate($type, '', $daterange, true);
	if (empty($result)) {
		if ($type == 'today') {
			$data_x = date('Ymd');
		}
		if ($type == 'week') {
			$data_x = stat_date_range(date('Ymd', strtotime('-7 days')), date('Ymd'));
		}
		if ($type == 'month') {
			$data_x = stat_date_range(date('Ymd', strtotime('-30 days')), date('Ymd'));
		}
		if ($type == 'daterange') {
			$data_x = stat_date_range($daterange['start'], $daterange['end']);
		}
		foreach ($data_x as $val) {
			$data_y[] = 0;
		}
		iajax(0, array('data_x' => $data_x, 'data_y' => $data_y));
	}
	foreach ($result as $val) {
		$data_x[] = $val['date'];
		if ($divide_type == 'bysum') {
			$data_y[] = $val['count'];
		} elseif ($divide_type == 'byavg') {
			$data_y[] = $val['avg'];
		} elseif ($divide_type == 'byhighest') {
			$data_y[] = $val['highest'];
		}
	}
	iajax(0, array('data_x' => $data_x, 'data_y' => $data_y));
}

template('statistics/app-account');