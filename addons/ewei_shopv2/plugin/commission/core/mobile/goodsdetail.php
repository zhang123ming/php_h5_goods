<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}
require EWEI_SHOPV2_PLUGIN . 'commission/core/page_login_mobile.php';
class Goodsdetail_EweiShopV2Page extends CommissionMobileLoginPage
{
	public function main()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$order = pdo_fetch("select id,uniacid,agentid,ordersn,goodsprice,createtime,finishtime,status from " . tablename('ewei_shop_order') . "where uniacid=:uniacid and id=:id",array(':uniacid' => $_W['uniacid'],'id' => $id));
		$goods = pdo_fetchall("select id,uniacid,orderid,goodsid,price,total,optionid,createtime,realprice from " . tablename('ewei_shop_order_goods') . "where uniacid=:uniacid and orderid=:orderid " , array(':uniacid' => $_W['uniacid'] , ':orderid' => $id));
		$agent = pdo_fetch("select realname,nickname from" . tablename("ewei_shop_member") . "where uniacid=:uniacid and id=:id" , array(':uniacid' => $_W['uniacid'],':id' => $order['agentid']));
		$count = 0;
		foreach ($goods as $key => $value) {
			$count += $value['total'];
			$res = pdo_fetch("select title,thumb from " . tablename('ewei_shop_goods') . "where uniacid=:uniacid and id=:id", array(':uniacid' => $_W['uniacid'],':id' => $value['goodsid']));
			if(!empty($res)){
				$goods[$key]['title'] = $res['title'];
				$goods[$key]['thumb'] = $res['thumb'];
			}
			if(!empty($value['optionid'])){
				$option = pdo_fetch("select title from " . tablename('ewei_shop_goods_option') . "where uniacid=:uniacid and id=:id", array(':uniacid' => $_W['uniacid'],':id' => $value['optionid']));
				if($option){
					$goods[$key]['optiontitle'] = $option['title'];
				}
			}
		}
		include $this->template();
	}

}

?>
