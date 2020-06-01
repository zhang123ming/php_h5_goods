<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Success_EweiShopV2Page extends MobileLoginPage
{
public function main()
	{
		global $_W;
		global $_GPC;
		$openid = $_W['openid'];
		$uniacid = $_W['uniacid'];
		$member = m('member')->getMember($openid, true);
		$orderid = intval($_GPC['id']);

		if (empty($orderid)) {
			$this->message('参数错误', mobileUrl('order'), 'error');
		}

		$order = pdo_fetch('select * from ' . tablename('ewei_shop_order') . ' where id=:id and uniacid=:uniacid and openid=:openid limit 1', array(':id' => $orderid, ':uniacid' => $uniacid, ':openid' => $openid));

		pdo_update('ewei_shop_order',array('status' => '3'),array('id' => $order['id'],'uniacid' => $_W['uniacid']));

		if (com('coupon')) {
			com('coupon')->sendcouponsbytask($order['id']);
		}

		if (com('coupon') && !empty($order['couponid'])) {
			com('coupon')->backConsumeCoupon($order['id']);
		}
		@session_start();

		/*20180419汇招商支付完成之后可以回跳的逻辑*/
		$backurl = $_GPC['backurl'];
		if (!empty($backurl)) {
			$showQrcode = 1;
		 	$backurl = base64_decode($_GPC['backurl'])."&showQrcode=".$showQrcode;
		}

		unset($_SESSION[EWEI_SHOPV2_PREFIX . '_order_pay_complete']);
		$hasverifygood = m('order')->checkhaveverifygoods($orderid);
		$isonlyverifygoods = m('order')->checkisonlyverifygoods($orderid);
		$ispeerpay = m('order')->checkpeerpay($orderid);

		if (!empty($ispeerpay)) {
		}
		else {
			if (!empty($order['istrade'])) {
				if (($order['status'] == 1) && ($order['tradestatus'] == 1)) {
					$order['price'] = $order['dowpayment'];
				}
				else {
					if (($order['status'] == 1) && ($order['tradestatus'] == 2)) {
						$order['price'] = $order['betweenprice'];
					}
				}
			}

			$merchid = $order['merchid'];
			$goods = pdo_fetchall('select og.goodsid,og.price,g.title,g.thumb,og.total,g.credit,og.optionid,og.optionname as optiontitle,g.isverify,g.storeids from ' . tablename('ewei_shop_order_goods') . ' og ' . ' left join ' . tablename('ewei_shop_goods') . ' g on g.id=og.goodsid ' . ' where og.orderid=:orderid and og.uniacid=:uniacid ', array(':uniacid' => $uniacid, ':orderid' => $orderid));

			$address = false;
			if($goods){
				foreach($goods as $v){
					// 升级包升级
					p('commission')->upgradebag($openid,$v['goodsid']);
				}
				
			}
			if (!empty($order['addressid'])) {
				$address = iunserializer($order['address']);

				if (!is_array($address)) {
					$address = pdo_fetch('select * from  ' . tablename('ewei_shop_member_address') . ' where id=:id limit 1', array(':id' => $order['addressid']));
				}
			}

			$carrier = @iunserializer($order['carrier']);
			if (!is_array($carrier) || empty($carrier)) {
				$carrier = false;
			}

			$store = false;

			if (!empty($order['storeid'])) {
				if (0 < $merchid) {
					$store = pdo_fetch('select * from  ' . tablename('ewei_shop_merch_store') . ' where id=:id limit 1', array(':id' => $order['storeid']));
				}
				else {
					$store = pdo_fetch('select * from  ' . tablename('ewei_shop_store') . ' where id=:id limit 1', array(':id' => $order['storeid']));
				}
			}

			$stores = false;

			if ($order['isverify']) {
			}

			if (p('lottery')) {
				$lottery_changes = p('lottery')->check_isreward();
			}

			$activity = com('coupon')->activity($order['price']);

			if ($activity) {
				$share = true;
			}
			else {
				$share = false;
			}
		}

		if (!empty($order['virtual']) && !empty($order['virtual_str'])) {
			$ordervirtual = m('order')->getOrderVirtual($order);
			$virtualtemp = pdo_fetch('SELECT linktext, linkurl FROM ' . tablename('ewei_shop_virtual_type') . ' WHERE id=:id AND uniacid=:uniacid LIMIT 1', array(':id' => $order['virtual'], ':uniacid' => $_W['uniacid']));
		}


 		include $this->template();
	}
}