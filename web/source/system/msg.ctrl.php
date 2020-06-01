<?php 

defined('IN_IA') or exit('Access Denied');


$msg = $_W['setting']['sms'];

$msg = $msg?$msg: array();

if (checksubmit('submit')) {

	$msg = array(
		'appkey' => $_GPC['appkey'],
		'pingtai' => $_GPC['pingtai'],
		'type'=>intval($_GPC['type']),
		'password' => $_GPC['password'],
		'secret' => $_GPC['secret'],
		'qianming' => $_GPC['qianming'],
		'sms_id' => $_GPC['sms_id'],
	);

	$test = setting_save($msg, 'sms');


	if ($_GPC['mobile']){
		sendsms($_GPC['mobile'], array('code'=>'系统短信测试'),$_GPC['sms_id'],$msg);
	}
}

template('system/msg');

?>