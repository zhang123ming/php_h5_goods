<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

require EWEI_SHOPV2_PLUGIN . 'commission/core/page_login_mobile.php';
class DownOrders_EweiShopV2Page extends CommissionMobileLoginPage
{
	public function main()
	{
		global $_W;
		global $_GPC;
		$openid = m('member')->getMember($_GPC['id'])['openid'];
		$switch = true;
		include $this->template();
	}

	public function get_list()
	{
		global $_W;
		global $_GPC;
		$openid = m('member')->getMember($_GPC['id'])['openid'];
		if($_GPC['status']!='default'){
			$statusstr = " and status = ".$_GPC['status'];
		}else{
			$statusstr = " and 1=1 ";
		}
		$count = pdo_fetch("select count(*) as count from".tablename('ewei_shop_order').'where uniacid = :uniacid and openid = :openid'.$statusstr,array(':uniacid'=>$_W['uniacid'],':openid'=>$openid))['count'];
		$total = pdo_fetch("select sum(price) as count from".tablename('ewei_shop_order').'where uniacid = :uniacid and openid = :openid'.$statusstr,array(':uniacid'=>$_W['uniacid'],':openid'=>$openid))['count'];
		$total = empty($total)?0:$total;
		$psize = 20;
		$pindex = max(1,$_GPC['page']);
		$pstart = ($pindex-1)*$psize;
		$list = pdo_fetchall("select id,status,price,ordersn from".tablename('ewei_shop_order').'where uniacid = :uniacid and openid = :openid '.$statusstr.' order by id desc limit '.$pstart.','.$psize,array(':uniacid'=>$_W['uniacid'],':openid'=>$openid));
		function getStatusStr($status){
			switch ($status) {
				case 0:
					return '待付款';
					break;
				case 1:
					return '待发货';
					break;
				case 2:
					return '待收货';
					break;
				case 3:
					return '已完成';
					break;
				case -1:
					return '已关闭';
					break;
			}
		}
		foreach($list as &$order){
			$order['statusstr'] = getStatusStr($order['status']);
			$order['goods'] = pdo_fetchall("select g.marketprice,g.thumb,g.title,og.total,op.marketprice as rprice from".tablename('ewei_shop_order_goods').' as og left join '.tablename('ewei_shop_goods').' as g on og.goodsid=g.id left join '.tablename('ewei_shop_goods_option').'  as op on op.id=og.optionid where og.orderid = '.$order['id']);
			foreach ($order['goods'] as &$v) {
				if ($v['rprice']) $v['marketprice'] = $v['rprice'];
				$v['thumb'] = tomedia($v['thumb']);
			}
		}
		unset($order);
		show_json(1, array('list' => $list, 'pagesize' => $psize,'count'=>$count,'total'=>$total));
	}
}

?>
