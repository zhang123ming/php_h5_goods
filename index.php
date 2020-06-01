<?php
/**
 * [WECHAT 2017]
 * [WECHAT  a free software]
 */
require './framework/bootstrap.inc.php';
$host = $_SERVER['HTTP_HOST'];
// 更改后请删除一下判断信息 开始 $config['setting']['authkey'] = 's100standard'
if($_W['config']['setting']['authkey']=='s100standard'||empty($_W['config']['setting']['authkey'])){
	echo "请更改 /data/config.php 文件中的变量 $ config['setting']['authkey'] 的值，建议更改为当前项目的名称，避免与其他站点重复,更改后删除本部分判断逻辑";
	die;
}
// 更改后请删除以上判断信息结束
if (!empty($host)) {
	$bindhost = pdo_fetch("SELECT * FROM ".tablename('site_multi')." WHERE bindhost = :bindhost", array(':bindhost' => $host));
	if (!empty($bindhost)) {
		header("Location: ". $_W['siteroot'] . 'app/index.php?i='.$bindhost['uniacid'].'&t='.$bindhost['id']);
		exit;
	}
}
if($_W['os'] == 'mobile' && (!empty($_GPC['i']) || !empty($_SERVER['QUERY_STRING']))) {
	header('Location: ./app/index.php?' . $_SERVER['QUERY_STRING']);
} else {
	header('Location: ./web/index.php?' . $_SERVER['QUERY_STRING']);
}