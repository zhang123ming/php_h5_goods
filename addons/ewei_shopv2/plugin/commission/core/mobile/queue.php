<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}
require EWEI_SHOPV2_PLUGIN . 'commission/core/page_login_mobile.php';
class 	QueUe_EweiShopV2Page extends CommissionMobileLoginPage
{
	public function main()
	{
		global $_W;
		global $_GPC;
		$_GPC['type'] = intval($_GPC['type']);
		$record_totalall = pdo_fetchcolumn('select sum(amount) from '.tablename('ewei_shop_commission_queuepaylog').' where uniacid=:uniacid and openid=:openid',array(':uniacid'=>$_W['uniacid'],':openid'=>$_W['openid']));
		$totalok = pdo_fetchcolumn('select sum(amount) from '.tablename('ewei_shop_commission_queuepaylog').' where uniacid=:uniacid and openid=:openid and status=0',array(':uniacid'=>$_W['uniacid'],':openid'=>$_W['openid']));
		$other = $_GPC['other_commission'];
		$condition = ' and openid=:openid and uniacid=:uniacid ';
		$params = array(':uniacid' => $_W['uniacid'], ':openid' => $_W['openid']);
		$total = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_commission_queuepaylog') . ' where 1 ' . $condition, $params);
		$withdraw = empty($totalok)?0:$totalok;
		include $this->template();
	}

	public function get_list()
	{
		global $_W;
		global $_GPC;
		$type = intval($_GPC['type']);
		$pindex = max(1, intval($_GPC['page']));
		$psize = 10;
		$condition = ' and openid=:openid and uniacid=:uniacid and status = :status ';
		switch($type){
			case '0':
					$condition = ' and openid=:openid and uniacid=:uniacid';
					$params = array(':uniacid' => $_W['uniacid'], ':openid' => $_W['openid']);
				break;
			case '1':
					$params = array(':uniacid' => $_W['uniacid'], ':openid' => $_W['openid'],":status"=>$type-1);
				break;
			case '2':
					$params = array(':uniacid' => $_W['uniacid'], ':openid' => $_W['openid'],":status"=>$type-1);
				break;
		}
		$list = pdo_fetchall('select * from ' . tablename('ewei_shop_commission_queuepaylog') . ' where 1 ' . $condition . ' order by createtime desc LIMIT ' . (($pindex - 1) * $psize) . ',' . $psize, $params);
		$total = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_commission_queuepaylog') . ' where 1 ' . $condition, $params);
		$sum = pdo_fetchcolumn('select sum(amount) from ' . tablename('ewei_shop_commission_queuepaylog') . ' where 1 ' . $condition, $params);
		if(empty($sum)){
			$sum = 0;
		}
		foreach ($list as &$row) {
			$row['createtime'] = date('Y-m-d H:i', $row['createtime']);
		}

		unset($row);
		show_json(1, array('list' => $list, 'total' => $total,'sum'=>$sum,'pagesize' => $psize,'status'=>$type));
	}
}

?>
