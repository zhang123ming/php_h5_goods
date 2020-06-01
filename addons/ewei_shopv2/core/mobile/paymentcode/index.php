<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Index_EweiShopV2Page extends MobileLoginPage
{

	public function main()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$merchuser = pdo_fetch('select `id`,`logo`,`merchname`,`desc`,`status` from ' . tablename('ewei_shop_merch_user') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $_GPC['merchid'], ':uniacid' => $_W['uniacid']));
		$goodsinfo = pdo_fetch('select * from ' . tablename('ewei_shop_goods') . ' where type = 2 and marketprice = 0 and totalcnf = 2  and uniacid=:uniacid and merchid = :merchid and deleted=0 ', array(':uniacid' => $_W['uniacid'],':merchid'=>$_GPC['merchid']));
		if (empty($merchuser)||$merchuser['status']!=1||empty($goodsinfo)) {
			unset($_GPC['merchid']);
			$this->message('收款码无效,商家不存在或者未通过审核',mobileUrl(''));
		}
		$follow = m('user')->followed($_W['openid']);

		include $this->template();
	}


	
}

?>
