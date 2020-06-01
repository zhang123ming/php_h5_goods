<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Index_EweiShopV2Page extends MobilePage
{
	public function qrcode()
	{
		global $_W;
		global $_GPC;
		$orderid = intval($_GPC['id']);
		$verifycode = $_GPC['verifycode'];
		$query = array('id' => $orderid, 'verifycode' => $verifycode);
		$order = pdo_fetch('select istrade from ' . tablename('ewei_shop_order') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $orderid, ':uniacid' => $_W['uniacid']));

		if (empty($order['istrade'])) {
			$url = mobileUrl('verify/detail', $query, true);
		}
		else {
			$url = mobileUrl('verify/tradedetail', $query, true);
		}

		show_json(1, array('url' => m('qrcode')->createQrcode($url)));
	}

	public function serverqrcode()
	{
		global $_W;
		global $_GPC;
		$orderid = intval($_GPC['id']);
		$order = pdo_fetch('select server_status from ' . tablename('ewei_shop_order') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $orderid, ':uniacid' => $_W['uniacid']));
		if ($order['server_status']!=1) {
			$query = array('id' => $orderid, 'server_status' => $order['server_status']);
			$url = mobileUrl('verify/server_detail', $query, true);
		}
		show_json(1, array('url' => m('qrcode')->createQrcode($url)));
	}

	public function select()
	{
		global $_W;
		global $_GPC;
		$orderid = intval($_GPC['id']);
		$verifycode = trim($_GPC['verifycode']);
		if (empty($verifycode) || empty($orderid)) {
			show_json(0);
		}

		$order = pdo_fetch('select id,verifyinfo from ' . tablename('ewei_shop_order') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $orderid, ':uniacid' => $_W['uniacid']));

		if (empty($order)) {
			show_json(0);
		}

		$verifyinfo = iunserializer($order['verifyinfo']);

		foreach ($verifyinfo as &$v) {
			if ($v['verifycode'] == $verifycode) {
				if (!empty($v['select'])) {
					$v['select'] = 0;
				}
				else {
					$v['select'] = 1;
				}
			}
		}

		unset($v);
		pdo_update('ewei_shop_order', array('verifyinfo' => iserializer($verifyinfo)), array('id' => $orderid));
		show_json(1);
	}

	public function check()
	{
		global $_W;
		global $_GPC;
		$openid = $_W['openid'];
		$uniacid = $_W['uniacid'];
		$orderid = intval($_GPC['id']);
		$order = pdo_fetch('select id,status,isverify,verified from ' . tablename('ewei_shop_order') . ' where id=:id and uniacid=:uniacid and openid=:openid limit 1', array(':id' => $orderid, ':uniacid' => $uniacid, ':openid' => $openid));

		if (empty($order)) {
			show_json(0);
		}

		if (empty($order['isverify'])) {
			show_json(0);
		}

		if (($order['verifytype'] == 0) || ($order['verifytype'] == 3)) {
			if (empty($order['verified'])) {
				show_json(0);
			}
		}

		show_json(1);
	}

	public function detail()
	{
		global $_W;
		global $_GPC;

		$verifyset=m('common')->getSysset('verify');
		$tipforsaler =$verifyset['store']['tipforsaler'];
		
		$openid = $_W['openid'];
		$uniacid = $_W['uniacid'];
		$orderid = intval($_GPC['id']);
		$data = com('verify')->allow($orderid);

		if (is_error($data)) {
			$this->message($data['message']);
		}

		extract($data);
		include $this->template();
	}

	public function server_detail()
	{
		global $_W;
		global $_GPC;
		$orderid = intval($_GPC['id']);
		$server_detail = intval($_GPC['server_detail']);
		$nolevel = true;
		$set = $_W['shopset']['commission'];
		$order = pdo_fetch('select id,openid,agentid,server_status as status,server_detail,ordersn,price from ' . tablename('ewei_shop_order') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $orderid, ':uniacid' => $_W['uniacid']));
		$memberid = m('member')->getMid($_W['openid']);
		$agent_member = m('member')->getMember($order['openid']);
		$level = m('member')->getLevel($_W['openid']);
		if($level['level']<92){
			$nolevel = false;
		}
		if($orderid){
			$allgoods = pdo_fetchall('select g.title,g.thumb,og.price,og.total,g.id,g.isupgradebag,g.upgradebag from '.tablename('ewei_shop_goods').' as g,'.tablename('ewei_shop_order_goods').' as og where og.goodsid=g.id and og.uniacid=:uniacid and g.uniacid=:uniacid and og.orderid=:orderid',array(':uniacid'=>$_W['uniacid'],':orderid'=>$orderid));
		}
		$set_level = '';
		foreach ($allgoods as $k => $v) {
			$upgradebag = iunserializer($v['upgradebag']);
			if(!empty($upgradebag['memberlevel'])){
				$set_level = $upgradebag['memberlevel'];	
			}
		}
		$is_again = pdo_fetchcolumn('select count(*) from '.tablename('ewei_shop_order').' as o,'.tablename('ewei_shop_order_goods').' as og,'.tablename('ewei_shop_goods').' as sg where o.id=og.orderid and og.goodsid=sg.id and o.status>=1 and sg.isupgradebag=1 and o.openid=:openid and o.uniacid=:uniacid',array(':openid'=>$order['openid'],':uniacid'=>$_W['uniacid']));
		if($level['level']>91&&$m1_level['level']<94){
			$get_day_money = pdo_fetchcolumn('select sum(amount) from '.tablename('ewei_shop_commission_record').' where amount>0 openid=:openid and uniacid=:uniacid and createtime >:time ',array(':openid'=>$_W['openid'],':uniacid'=>$_W['uniacid'],':time'=>strtotime(date("Y-m-d"),time())));
			$can_money = $set['days_upperlimit'][$level['level']]-$get_day_money;
			$can_money = $can_money>0?$can_money:0;
		}
		if($is_again>0){
			$server_money = round(($order['price']*$set['again_server'][$level['level']])/100,2);
			$server_money = ($can_money-$server_money)>0?$server_money:$can_money;
		}else{
			$server_money = round(($set['again_server'][$set_level])/100,2);
			$server_money = ($can_money-$server_money)>0?$server_money:$can_money;
		}
		if(empty($server_money)){
			$server_money = 0.00;
		}
		if($agent_member['agentid']==$memberid){
			$is_server = true;
		}


		include $this->template();

	}

	public function tradedetail()
	{
		global $_W;
		global $_GPC;
		$openid = $_W['openid'];
		$uniacid = $_W['uniacid'];
		$orderid = intval($_GPC['id']);
		$data = com('verify')->allow($orderid);

		if (is_error($data)) {
			$this->message($data['message']);
		}

		extract($data);
		$createInfo = array();
		$createInfo['tradestatus'] = $order['tradestatus'];
		$createInfo['betweenprice'] = $order['betweenprice'];
		$newstore_plugin = p('newstore');
		$temp_type = $newstore_plugin->getTempType();
		$tempinfo = $newstore_plugin->getTempInfo($goods['tempid']);

		if (!empty($goods['peopleid'])) {
			$goods['peopleinfo'] = $newstore_plugin->getPeopleInfo($goods['peopleid']);
		}

		include $this->template();
	}

	public function complete()
	{
		global $_W;
		global $_GPC;
		$verifyset=m('common')->getSysset('verify');
		$orderid = intval($_GPC['id']);
		$sql="select * FROM ".tablename('ewei_shop_order')."where uniacid=$_W[uniacid] AND id='{$orderid}'";
		$order=pdo_fetch($sql);
		if ($verifyset['store']['stocktime']&&!$verifyset['store']['mobileverify']) {
			if (time()<$verifyset['store']['stocktime']*3600+$order['paytime']) {
				show_json(0,"尚未到核销时间");
			}
		}
		$times = intval($_GPC['times']);
		com('verify')->verify($orderid, $times);
		show_json(1);
	}
	public function server_complete()
	{
		global $_W;
		global $_GPC;
		$orderid = intval($_GPC['id']);
		$status = intval($_GPC['status']);
		$servermoney = round($_GPC['servermoney'],2);
		$rdata = array();
		if(!empty($orderid)&&!empty($status)){
			$order = pdo_fetch('select id,server_status from '.tablename('ewei_shop_order').' where id=:id and uniacid=:uniacid',array(':id'=>$orderid,':uniacid'=>$_W['uniacid']));
			if($order['server_status']==1){
				show_json(0,'此订单已服务过,无法重复服务');
			}
			$data = array(
				'server_status'=>$status,
				'server_detail'=>json_encode(array('openid'=>$_W['openid'],'time'=>time(),'amount'=>$servermoney))
			);
			pdo_update('ewei_shop_order',$data,array('id'=>$orderid,'uniacid'=>$_W['uniacid']));
			if($status==1) {
				$rdata['amount'] = $servermoney;
				$rdata['remark'] = '服务奖返利';
				$rdata['openid'] = $_W['openid'];
				$rdata['uniacid'] = $_W['uniacid'];
				$rdata['createtime'] = time();
				$a = pdo_insert('ewei_shop_commission_record',$rdata);
				show_json(1);	
			}else{
				show_json(-1);
			}


		}

	}
	public function success()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['orderid']);
		$times = intval($_GPC['times']);
		$this->message(array('title' => '操作完成', 'message' => '您可以退出浏览器了'), 'javascript:WeixinJSBridge.call("closeWindow");', 'success');
	}

	public function fail()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['orderid']);
		$times = intval($_GPC['times']);
		$this->message(array('title' => '已拒绝服务', 'message' => '您可以退出浏览器了'), 'javascript:WeixinJSBridge.call("closeWindow");', 'danger');
	}
}

?>
