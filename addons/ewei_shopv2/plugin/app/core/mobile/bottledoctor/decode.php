<?php

if (!defined('IN_IA')) {
	exit('Access Denied');
}

require EWEI_SHOPV2_PLUGIN . 'app/core/page_mobile.php';
class Decode_EweiShopV2Page extends AppMobilePage
{
	public function __construct()
	{
		parent::__construct();
		$trade = m('common')->getSysset('trade');

		if (!empty($trade['closecomment'])) {
			app_error(AppError::$OrderCanNotComment);
		}
	}

	public function submit()
	{
		global $_W;
		global $_GPC;
		$openid = $_GPC['openid'];
		$uniacid = $_W['uniacid'];		
		$decode=$_GPC['decode'];
		$comment = array('uniacid' => $uniacid, 'decode' => $decode ,'openid' => $openid,'createTime' =>time());
		
		$items  = pdo_fetchall('SELECT code FROM ' . tablename('zmcn_fw_data') . ' WHERE uniacid=:uniacid and code = :code ', array(':uniacid' => $_W['uniacid'],':code' => $decode));

		$decode = pdo_fetchall('SELECT decode FROM' . tablename('ewei_shop_bottledoctor_decode') . ' WHERE   uniacid=:uniacid and decode = :decode ',array(':uniacid' => $uniacid, ':decode' => $decode));

		if ($items[0] && empty($decode)) {
				pdo_insert('ewei_shop_bottledoctor_decode', $comment);  
				$list['hint'] = '验证成功!'  ;   
				$list['valhint'] = 1;  
		}else{
			$list['hint'] = '没有该验证码或该验证码已被使用！';
			$list['valhint'] = 0; 
		}	
		$id = pdo_insertid();		
		app_json(array('list' => $list));
	}

	public function get()
	{
		global $_W;
		global $_GPC;
		// echo "<pre>";
		// var_dump($_GPC);die;
		$openid = $_GPC['openid'];
		$uniacid = $_W['uniacid'];	
		$list = pdo_fetch('SELECT * FROM' . tablename('ewei_shop_bottledoctor_decodeset') . ' WHERE   uniacid=:uniacid',array(':uniacid' => $uniacid));
		$decode = pdo_fetchall('SELECT decode FROM' . tablename('ewei_shop_bottledoctor_decode') . ' WHERE   uniacid=:uniacid and openid = :openid ',array(':uniacid' => $uniacid, ':openid' => $openid));
		app_json(array('decode' => $decode,'list' => $list));
	}

}

?>