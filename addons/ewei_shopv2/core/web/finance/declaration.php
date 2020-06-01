<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Declaration_EweiShopV2Page extends WebPage
{
	public function main()
	{
		global $_W;
		global $_GPC;
		$set =  p('commission')->getSet();
		$type = trim($_GPC['type']);

		$id = intval($_GPC['id']);
		$profile = m('member')->getMember($id, true);
		if ($_W['ispost']) {
			
			$set = p('commission')->getSet();
			$commission = m('common')->getPluginset('commission');
			$detype = $_GPC['data']['declarationtype'] ? $_GPC['data']['declarationtype'] : "0";
			$num = floatval($_GPC['data']['moneycount']);
			$goodsid = $_GPC['become_goodsid'];

			if ($num <= 0) {
				show_json(0, array('message' => '请填写大于0的数字!'));
			}

			if(empty($goodsid)){
				//商品为空
				if($detype == '0') {
					$shopgoods = pdo_fetch('SELECT id FROM ' . tablename('ewei_shop_goods') . ' WHERE uniacid = :uniacid AND type = 2 and nocommission = 0 limit 1 ', array(':uniacid' => $_W['uniacid']));
				} else {
					$shopgoods = pdo_fetch('SELECT id FROM ' . tablename('ewei_shop_goods') . ' WHERE uniacid = :uniacid AND type = 2 and nocommission = 1 limit 1 ', array(':uniacid' => $_W['uniacid']));
				}
				
				if(empty($shopgoods)){
					show_json(0, array('message' => '未找到符合要求的商品'));
				}
				p('commission')->upgradebag($profile['openid'],$shopgoods['id']);
			} else {
				//商品输入
				if($detype == '0'){
					$goods = pdo_fetch('SELECT id FROM ' . tablename('ewei_shop_goods') . ' WHERE uniacid = :uniacid AND id = :id  and nocommission = 0 limit 1 ', array(':uniacid' => $_W['uniacid'],':id'=>$goodsid));
				} else {
					$goods = pdo_fetch('SELECT id FROM ' . tablename('ewei_shop_goods') . ' WHERE uniacid = :uniacid AND id = :id  and nocommission = 1 limit 1 ', array(':uniacid' => $_W['uniacid'],':id'=>$goodsid));
				}

				if(empty($goods)){
					show_json(0, array('message' => '商品属性不符合要求'));
				}
				p('commission')->upgradebag($profile['openid'],$goodsid);
			}

			$order = array(); //订单表数据
			$order['uniacid'] = $_W['uniacid'];
			$order['openid'] = $profile['openid'];
			$order['agentid'] = $profile['agentid'];
			$order['price'] = $num;
			$order['ordersn'] = m('common')->createNO('order', 'ordersn', 'SH');
			$order['goodsprice'] = $num;
			$order['oldprice'] = $num;
			$order['status'] = 3;
			$order['paytype'] = 11;
			$order['isdeclaration'] = 1;
			$order['createtime'] = time();
			$order['paytime'] = time();
			$order['finishtime'] = (time() + 1);
			$order['grprice'] = $num;
			if (!empty($profile['agentid'])) {
				$order['fids'] = $profile['fids'];
				$order['initialid'] = $profile['initialid'];
			}
			pdo_insert('ewei_shop_order', $order);
			$orderid = pdo_insertid();

			if(!empty($orderid)){
				$order_goods = array(); // 订单商品表数据
				$order_goods['uniacid'] = $_W['uniacid'];
				$order_goods['orderid'] = $orderid;
				$order_goods['goodsid'] = $goodsid ? $goodsid : $shopgoods['id'];
				$order_goods['price'] = $num;
				$order_goods['total'] = 1;
				$order_goods['createtime'] = time();
				$order_goods['realprice'] = $num;
				$order_goods['oldprice'] = $num;
				$order_goods['openid'] = $profile['openid'];
				pdo_insert('ewei_shop_order_goods', $order_goods);
			}
			
			//三级返利
			if(!empty($_W['shopset']['commission']['level'])){
				$pluginc = p('commission');
				$pluginc->checkOrderConfirm($orderid,0);
			}

			if($detype == '0'){
				if (p('commission')) {
					p('commission')->checkOrderFinish($orderid);
				}
			}

			if (p('commission')) {
  				p('commission')->checkOrderPay($orderid);
				p('commission')->checkOrderFinish($orderid);
				p('commission')->upgradeLevelByOrder($member['openid']);
 			}

			$isrecharge = $_W['shopset']['shop']['isrecharge'];
			// var_dump($isrecharge);die;
			//余额充值
			$types = "credit2";
			$typestr = '余额';
			$remark = '报单充值金额';

			if ($types == 'credit2' && $isrecharge == '1') {
				m('member')->setCredit($profile['openid'], $types, $num, array($_W['uid'], '后台会员充值' . $typestr . ' ' . $remark));
				$set = m('common')->getSysset('shop');
				$logno = m('common')->createNO('member_log', 'logno', 'RC');
				$data = array('openid' => $profile['openid'], 'logno' => $logno, 'uniacid' => $_W['uniacid'], 'type' => '0', 'createtime' => TIMESTAMP, 'status' => '1', 'title' => $set['name'] . '会员充值', 'money' => $num, 'remark' => $remark, 'rechargetype' => 'system');
				pdo_insert('ewei_shop_member_log', $data);
				$logid = pdo_insertid();
				m('notice')->sendMemberLogMessage($logid, 0, true);
			}
			if($_GPC['update']){
				$member_level = m('member')->getLevel($profile['id']);
				$up_level = pdo_fetch('SELECT level from'.tablename('ewei_shop_member_level').'where uniacid=:uniacid and id =:id',array('uniacid' => $_W['uniacid'],'id'=>$_GPC['update']));
				if(($member_level['level']<$up_level['level']) && $member_level['name']!='股东合伙人'){
					$update = array('up_time'=>time(),'level'=>$_GPC['update']);
					if($up_level['level'] >1){
						$update['down_time']=time()+7776000;
					}
					$a = pdo_update('ewei_shop_member',$update,array('id'=>$profile['id']));

					// 更新agent91-100
					$this_member = m('member')->getMember($profile['id']);
					$teamlist = pdo_fetchall('select * from '.tablename('ewei_shop_member').' where uniacid=:uniacid and find_in_set('.$this_member['agentid'].',fids) order by fids asc',array(':uniacid'=>$_W['uniacid']));
					foreach ($teamlist as $k2 => $v2) {
						$agentdata1 = p('commission')->getAgentId($v2['agentid'],$v2);
						unset($agentdata1['fids']);
						pdo_update('ewei_shop_member',$agentdata1,array('id'=>$v2['id']));
					}
				}
			}
			plog('finance.recharge.' . $types, '充值' . $typestr . ': ' . $num . ' <br/>会员信息: ID: ' . $profile['id'] . ' /  ' . $profile['openid'] . '/' . $profile['nickname'] . '/' . $profile['realname'] . '/' . $profile['mobile']);
			show_json(1, array('url' => referer()));
		}

		include $this->template();
	}
}

?>
