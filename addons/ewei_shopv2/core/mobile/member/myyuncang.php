<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Myyuncang_EweiShopV2Page extends MobileLoginPage
{

	public function main()
	{
		global $_W;
		global $_GPC;
		$list = pdo_fetchall('select id,title,thumb,marketprice as price,hasoption from '.tablename('ewei_shop_goods').' where uniacid=:uniacid and total>0 and status=1',array(':uniacid'=>$_W['uniacid']));
		foreach($list as &$v){
			if($v['hasoption']==1){
				$v['options'] = pdo_fetchall('select id,title,marketprice as mprice,productprice as pprice,specs from '.tablename('ewei_shop_goods_option').' where goodsid=:id and uniacid=:uniacid',array(':uniacid'=>$_W['uniacid'],':id'=>$v['id']));
				$sum = 0;
				foreach($v['options'] as &$vv){
					$vv['mstock'] = pdo_fetchcolumn('select stock from '.tablename('ewei_shop_member_inventory').' where uniacid=:uniacid and openid=:openid and optionid=:id and pid=:pid ',array(':uniacid'=>$_W['uniacid'],':openid'=>$_W['openid'],':id'=>$vv['id'],':pid'=>$v['id']));
					$vv['mstock'] = empty($vv['mstock'])?0:$vv['mstock'];
					$sum+=$vv['mstock'];
				}
				$v['mstock'] = $sum>0?$sum:0;
			}else{
				$v['mstock'] = pdo_fetchcolumn('select stock from '.tablename('ewei_shop_member_inventory').' where uniacid=:uniacid and openid=:openid and goodsid=:id ',array(':uniacid'=>$_W['uniacid'],':openid'=>$_W['openid'],':id'=>$v['id']));
				$v['mstock'] = empty($v['mstock'])?0:$v['mstock'];
			}
		}
		unset($v,$vv);

		include $this->template('member/myyuncang');
	}

	public function stock()
	{
		global $_W,$_GPC;
		$list = pdo_fetchall('select id,title,thumb,marketprice as price,hasoption from '.tablename('ewei_shop_goods').' where uniacid=:uniacid and total>0 and status=1',array(':uniacid'=>$_W['uniacid']));
		foreach($list as &$v){
			if($v['hasoption']==1){
				$v['options'] = pdo_fetchall('select id,title,marketprice as mprice,productprice as pprice,specs from '.tablename('ewei_shop_goods_option').' where goodsid=:id and uniacid=:uniacid',array(':uniacid'=>$_W['uniacid'],':id'=>$v['id']));
				$sum = 0;
				foreach($v['options'] as &$vv){
					$vv['mstock'] = pdo_fetchcolumn('select stock from '.tablename('ewei_shop_member_inventory').' where uniacid=:uniacid and openid=:openid and optionid=:id and pid=:pid ',array(':uniacid'=>$_W['uniacid'],':openid'=>$_W['openid'],':id'=>$vv['id'],':pid'=>$v['id']));
					$vv['mstock'] = empty($vv['mstock'])?0:$vv['mstock'];
					$sum+=$vv['mstock'];
				}
				$v['mstock'] = $sum>0?$sum:0;
			}else{
				$v['mstock'] = pdo_fetchcolumn('select stock from '.tablename('ewei_shop_member_inventory').' where uniacid=:uniacid and openid=:openid and goodsid=:id ',array(':uniacid'=>$_W['uniacid'],':openid'=>$_W['openid'],':id'=>$v['id']));
				$v['mstock'] = empty($v['mstock'])?0:$v['mstock'];
			}
		}
		unset($v,$vv);

		include $this->template();
	}

	public function confirm()
	{
		global $_W,$_GPC;
		// $opthions = $_GPC['opthions'];
		$openid = $_W['openid'];
		$id = $_GPC['id'];
		$member = m('member')->getMember($openid);
		$order = pdo_fetch('select id,ordersn,price from '.tablename('ewei_shop_order').' where uniacid=:uniacid and id=:id ',array(':uniacid'=>$_W['uniacid'],':id'=>$id));
		$credit = $_W['shopset']['pay']['credit'];

		$address = pdo_fetch('select * from ' . tablename('ewei_shop_member_address') . ' where  openid=:openid and uniacid=:uniacid order by isdefault desc limit 1', array(':uniacid' => $_W['uniacid'], ':openid' => $openid));	
		include $this->template();
	}

	public function submit()
	{
		global $_W,$_GPC;
		$member = m('member')->getMember($_W['openid']);
		$level = m('member')->getLevel($_W['openid']);
		$opthionsString = html_entity_decode($_GPC['opthions']);
		$opthions = json_decode($opthionsString,true);
		if(empty($_GPC['opthions'])){
			show_json(0,'未选购任何产品');
		}
		// if(empty($_GPC['images'])){
		// 	show_json(0,'未上传转账凭证');
		// }
		// if(empty($_GPC['addressid'])){
		// 	show_json(0,'未填写收货地址');
		// }
		// $opthions = $_GPC['opthions'];
		$image = $_GPC['images'];
		$order = array(
				'uniacid'=>$_W['uniacid'],
				'openid'=>$_W['openid'],
				'agentid'=>$member['agentid'],
				'createtime' => time());

		if($level['level']==99){
			$agentopenid = $member['agent100'];
			if($agentopenid){
				$agentopenid = pdo_fetchcolumn('select openid from '.tablename('ewei_shop_member').' where uniacid=:uniacid and id=:id',array(':uniacid'=>$_W['uniacid'],':id'=>$agentopenid));
			}
			$order['agent_superior'] = $agentopenid;		
		}elseif($level['level']==98){
			if(!empty($member['agent99'])){
				$agentopenid = $member['agent99'];
			}else{
				$agentopenid = $member['agent100'];
			}
			$agentopenid = pdo_fetchcolumn('select openid from '.tablename('ewei_shop_member').' where uniacid=:uniacid and id=:id',array(':uniacid'=>$_W['uniacid'],':id'=>$agentopenid));
			$order['agent_superior'] = $agentopenid;
		}
		$order['ordersn'] = m("common")->createNO("order", "ordersn", "JH");
		$price = 0;
		foreach($opthions as $value){
			$price += ($value['price']*$value['num']);
		}
		$order['price'] = $price;
		$order['status'] = 0;
		$order['isdeclaration'] = 1;
		$order['addressid'] = $_GPC['addressid'];
		$order['is_jinhuo'] = 1;
		// $order['remark'] = $level['levelname'].'进货订单';
		$order['upload_image'] = tomedia($image);
		for($i=91;$i<=100;$i++){
			$order['agent'.$i] = $member['agent'.$i];
		}

		pdo_insert('ewei_shop_order',$order);
		$id = pdo_insertid();
		if($id){
			foreach($opthions as $value){
				$order_goods = array();
				$order_goods['uniacid'] = $_W['uniacid'];
				$order_goods['orderid'] = $id;
				if($value['type']=='optionid'){
					$order_goods['goodsid'] = $value['pid'];
					$order_goods['optionid'] = $value['id'];
					$order_goods['optionname'] = pdo_fetchcolumn('select title from '.tablename('ewei_shop_goods_option').' where uniacid=:uniacid and id=:id',array(':uniacid'=>$_W['uniacid'],':id'=>$value['id']));
				}else{
					$order_goods['goodsid'] = $value['id'];
				}
				$order_goods['total'] = $value['num'];
				$order_goods['price'] = $value['price'];
				$order_goods['realprice'] = $value['price'];
				$order_goods['createtime'] = time();
				pdo_insert('ewei_shop_order_goods',$order_goods);

			}
		}
		$url = mobileUrl('member/myyuncang/confirm',array(),true);
		show_json(1,array('remark'=>'提交成功,请等待审核','url'=>$url,'id'=>$id));
	}

	public function pay()
	{
		global $_W,$_GPC;
		$openid = $_W['openid'];
		$id = $_GPC['id'];
		$ordersn = $_GPC['ordersn'];
		$type = $_GPC['type'];
		$addressid = $_GPC['addressid'];
		$order = pdo_fetch('select id,price,status from '.tablename('ewei_shop_order').' where uniacid=:uniacid and id=:id and ordersn=:ordersn ',array(':uniacid'=>$_W['uniacid'],':id'=>$id,':ordersn'=>$ordersn));
		if(empty($order)){
			return show_json(0,"订单不存在!");
		}
		$credits = m('member')->getCredit($openid, 'credit2');
		if($credits<$order['price']){
			return show_json(0,$_W['shopset']['trade']['moneytext']."不足,支付失败!");
		}

			$fee = floatval($order['price']);
			$result = m('member')->setCredit($openid, 'credit2', 0 - $fee, array($_W['member']['uid'], $_W['shopset']['shop']['name'] . '消费' . $fee));

			if (is_error($result)) {
				if ($_W['ispost']) {
					show_json(0, $result['message']);
				}
				else {
					return show_json(0,$result['message']);
				}
			}

			$record = array();
			$record['status'] = '1';
			$record['type'] = 'cash';
			pdo_update('core_paylog', $record, array('plid' => $log['plid']));

			m('order')->setOrderPayType($order['id'], 1, $gpc_ordersn);
			$ret = array();
			$ret['result'] = 'success';
			$ret['type'] = $log['type'];
			$ret['from'] = 'return';
			$ret['tid'] = $log['tid'];
			$ret['user'] = $log['openid'];
			$ret['fee'] = $log['fee'];
			$ret['weid'] = $log['weid'];
			$ret['uniacid'] = $log['uniacid'];
			@session_start();
			$_SESSION[EWEI_SHOPV2_PREFIX . '_order_pay_complete'] = 1;
			if (!empty($ispeerpay)) {
				$peerheadimg = m('member')->getInfo($member['openid']);

				if (empty($peerheadimg['avatar'])) {
					$peerheadimg['avatar'] = 'http://of6odhdq1.bkt.clouddn.com/d7fd47dc6163ec00abfe644ab3c33ac6.jpg';
				}
				m('order')->peerStatus(array('pid' => $ispeerpay['id'], 'uid' => $member['id'], 'uname' => $member['nickname'], 'usay' => '', 'price' => $log['fee'], 'createtime' => time(), 'headimg' => $peerheadimg['avatar'], 'openid' => $peerheadimg['openid'], 'usay' => trim($_GPC['peerpaymessage'])));
			}

			$pay_result = m('order')->payResult($ret);

			
			if ($order['invoice']==1) {
				$orderID = $order["ordersn"];
				$item['payment']=$order['status'];
				pdo_update('ewei_shop_invoice', array('payment'=>$record['status']), array('orderID' => $orderID, 'uniacid' => $_W['uniacid']));
			}

			if ($_W['ispost']) {
				if($pay_result){
					pdo_update('ewei_shop_order',array('status'=>1,'addressid'=>$addressid),array('uniacid'=>$_W['uniacid'],'id'=>$order['id']));
				}
				show_json(1, array('result' => $pay_result));
			}
			else {
				// var_dump($order['status']);die;
				header('location:' . mobileUrl('order/pay/success', array('id' => $order['id'], 'result' => $pay_result)));
			}


	}

	public function success()
	{
		global $_W,$_GPC;
		@session_start();

		if (!isset($_SESSION[EWEI_SHOPV2_PREFIX . '_order_pay_complete'])) {
			if (empty($order['istrade'])) {
				header('location: ' . mobileUrl('order'));
			}
			else {
				header('location: ' . mobileUrl('newstore/norder'));
			}

			exit();
		}

		include $this->template();
	}
}

?>
