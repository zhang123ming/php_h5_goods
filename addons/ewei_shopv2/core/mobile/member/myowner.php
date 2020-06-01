<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Myowner_EweiShopV2Page extends MobileLoginPage
{
	public function main()
	{
		global $_W;
		global $_GPC;
		$is_owner = pdo_fetch('select id,storename from '.tablename('ewei_shop_store').' where uniacid=:uniacid and distributor=:openid',array(':uniacid'=>$_W['uniacid'],':openid'=>$_W['openid']));
		if(empty($is_owner)){
			$this->message('您不是店主身份,无法进入门店管理');
		}
		// $member = m('member')->getMember('sns_wa_op8bq4jbGlFB_2psHKLXpHL5RBCg');
		// $usernotice = unserialize($member['noticeset']);
		// echo '<pre>';
		// var_dump($usernotice);exit;
		$member=m('member')->getMember($_W['openid']);
		$level=m('member')->getLevel($member['id']);
		$order_count = pdo_fetchcolumn('select count(*) from '.tablename('ewei_shop_order').' where uniacid=:uniacid and storeid=:storeid',array(':uniacid'=>$_W['uniacid'],':storeid'=>$is_owner['id']));
		$amount_total= pdo_fetchcolumn('select sum(amount) from '.tablename('ewei_shop_store_withdrawal').' where status=0 and uniacid=:uniacid and openid=:openid and storeid=:storeid',array(':uniacid'=>$_W['uniacid'],':openid'=>$_W['openid'],':storeid'=>$is_owner['id']));
		$already_amout = pdo_fetchcolumn('select sum(amount) from '.tablename('ewei_shop_store_withdrawal').' where status>0 and uniacid=:uniacid and openid=:openid and storeid=:storeid',array(':uniacid'=>$_W['uniacid'],':openid'=>$_W['openid'],':storeid'=>$is_owner['id']));
		$can_amount = $amount_total-$already_amout;

		$amount_total = empty($amount_total)?0.00:$amount_total;
		include $this->template('member/myowner');
	}

	public function storewidthdrawal()
	{
		global $_W;
		global $_GPC;
		$set = $_W['shopset']['trade'];
		$is_owner = pdo_fetch('select id,storename from '.tablename('ewei_shop_store').' where uniacid=:uniacid and distributor=:openid',array(':uniacid'=>$_W['uniacid'],':openid'=>$_W['openid']));
		if(empty($is_owner)){
			$this->message('您不是店主身份,无法进入门店管理');
		}
		$amount_total= pdo_fetchcolumn('select sum(amount) from '.tablename('ewei_shop_store_withdrawal').' where status=0 and uniacid=:uniacid and openid=:openid and storeid=:storeid',array(':uniacid'=>$_W['uniacid'],':openid'=>$_W['openid'],':storeid'=>$is_owner['id']));
		$already_amout = pdo_fetchcolumn('select sum(amount) from '.tablename('ewei_shop_store_withdrawal').' where status>0 and uniacid=:uniacid and openid=:openid and storeid=:storeid',array(':uniacid'=>$_W['uniacid'],':openid'=>$_W['openid'],':storeid'=>$is_owner['id']));
		$can_amount = $amount_total-$already_amout;

		
		$status = 1;
		$openid = $_W['openid'];

		if (empty($set['withdraw'])) {
			$this->message('系统未开启提现!', '', 'error');
		}

		$withdrawcharge = $set['withdrawcharge'];
		$withdrawbegin = floatval($set['withdrawbegin']);
		$withdrawend = floatval($set['withdrawend']);
		$credit = m('member')->getCredit($_W['openid'], 'credit2');
		$last_data = $this->getLastApply($openid);
		$canusewechat = !strexists($openid, 'wap_user_') && !strexists($openid, 'sns_qq_') && !strexists($openid, 'sns_wx_') && !strexists($openid, 'sns_wa_');
		$type_array = array();
		// if (($set['withdrawcashweixin'] == 1) && $canusewechat) {
			$type_array[0]['title'] = '提现到微信钱包';
		// }

		if ($set['withdrawcashalipay'] == 1) {
			$type_array[2]['title'] = '提现到支付宝';

			if (!empty($last_data)) {
				if ($last_data['applytype'] != 2) {
					$type_last = $this->getLastApply($openid, 2);

					if (!empty($type_last)) {
						$last_data['alipay'] = $type_last['alipay'];
					}
				}
			}
		}

		if ($set['withdrawcashcard'] == 1) {
			$type_array[3]['title'] = '提现到银行卡';

			if (!empty($last_data)) {
				if ($last_data['applytype'] != 3) {
					$type_last = $this->getLastApply($openid, 3);

					if (!empty($type_last)) {
						$last_data['bankname'] = $type_last['bankname'];
						$last_data['bankcard'] = $type_last['bankcard'];
					}
				}
			}
			// echo '<pre>';
			// var_dump($type_array);exit;
			$condition = ' and uniacid=:uniacid';
			$params = array(':uniacid' => $_W['uniacid']);
			$banklist = pdo_fetchall('SELECT * FROM ' . tablename('ewei_shop_commission_bank') . ' WHERE 1 ' . $condition . '  ORDER BY displayorder DESC', $params);
		}

		if (!empty($last_data)) {
			if (array_key_exists($last_data['applytype'], $type_array)) {
				$type_array[$last_data['applytype']]['checked'] = 1;
			}
		}

		include $this->template('member/storewithdraw');
	}

	public function submit()
	{
		global $_W;
		global $_GPC;
		$set = $_W['shopset']['trade'];
		if (empty($set['withdraw'])) {
			show_json(0, '系统未开启提现!');
		}
		$set_array = array();
		$set_array['charge'] = $set['withdrawcharge'];
		$set_array['begin'] = floatval($set['withdrawbegin']);
		$set_array['end'] = floatval($set['withdrawend']);
		$money = floatval($_GPC['money']);

		if ($money <= 0) {
			show_json(0, '提现金额错误!');
		}

		$apply = array();
		$type_array = array();

		if ($set['withdrawcashweixin'] == 1) {
			$type_array[0]['title'] = '提现到微信钱包';
		}

		if ($set['withdrawcashalipay'] == 1) {
			$type_array[2]['title'] = '提现到支付宝';
		}

		if ($set['withdrawcashcard'] == 1) {
			$type_array[3]['title'] = '提现到银行卡';
			$condition = ' and uniacid=:uniacid';
			$params = array(':uniacid' => $_W['uniacid']);
			$banklist = pdo_fetchall('SELECT * FROM ' . tablename('ewei_shop_commission_bank') . ' WHERE 1 ' . $condition . '  ORDER BY displayorder DESC', $params);
		}

		$applytype = intval($_GPC['applytype']);

		if (!array_key_exists($applytype, $type_array)) {
			show_json(0, '未选择提现方式，请您选择提现方式后重试!');
		}

		if ($applytype == 2) {
			$realname = trim($_GPC['realname']);
			$alipay = trim($_GPC['alipay']);
			$alipay1 = trim($_GPC['alipay1']);

			if (empty($realname)) {
				show_json(0, '请填写姓名!');
			}

			if (empty($alipay)) {
				show_json(0, '请填写支付宝帐号!');
			}

			if (empty($alipay1)) {
				show_json(0, '请填写确认帐号!');
			}

			if ($alipay != $alipay1) {
				show_json(0, '支付宝帐号与确认帐号不一致!');
			}

			$apply['realname'] = $realname;
			$apply['alipay'] = $alipay;
		}
		else {
			if ($applytype == 3) {
				$realname = trim($_GPC['realname']);
				$bankname = trim($_GPC['bankname']);
				$bankcard = trim($_GPC['bankcard']);
				$bankcard1 = trim($_GPC['bankcard1']);

				if (empty($realname)) {
					show_json(0, '请填写姓名!');
				}

				if (empty($bankname)) {
					show_json(0, '请选择银行!');
				}

				if (empty($bankcard)) {
					show_json(0, '请填写银行卡号!');
				}

				if (empty($bankcard1)) {
					show_json(0, '请填写确认卡号!');
				}

				if ($bankcard != $bankcard1) {
					show_json(0, '银行卡号与确认卡号不一致!');
				}

				$apply['realname'] = $realname;
				$apply['bankname'] = $bankname;
				$apply['bankcard'] = $bankcard;
			}
		}

		$realmoney = $money;

		if (!empty($set_array['charge'])) {
			$money_array = m('member')->getCalculateMoney($money, $set_array);

			if ($money_array['flag']) {
				$realmoney = $money_array['realmoney'];
				$deductionmoney = $money_array['deductionmoney'];
			}
		}

		$data = array(
			'uniacid'=>$_W['uniacid'],
			'openid'=>$_W['openid'],
			'createtime'=>time(),
			'amount'=>$realmoney,
			'paytype'=>$_GPC['applytype'],
			'status'=>1,
			'storeid'=>$_GPC['storeid'],
			'alipay'=>$_GPC['alipay'],
			'realname'=>$_GPC['realname'],
			'bankname'=>$_GPC['bankname'],
			'remark'=>'门店提现'
		);
		pdo_insert('ewei_shop_store_withdrawal',$data);
		show_json(1);
	}

	public function withdrawallog()
	{
		global $_W,$_GPC;
		include $this->template('member/myowner/log');
	}

	public function get_list()
	{
		global $_W,$_GPC;
		$type = intval($_GPC['type']);
		$pindex = max(1, intval($_GPC['page']));
		$psize = 10;
		$apply_type = array(0 => '微信钱包', 2 => '支付宝', 3 => '银行卡');
		$condition = ' and openid=:openid and uniacid=:uniacid ';
		if($_GPC['type']==0){
			$condition = ' and status=0 ';
		}else{
			$condition = ' and status<>0 ';
		}
		$params = array(':uniacid' => $_W['uniacid'], ':openid' => $_W['openid']);
		$list = pdo_fetchall('select * from ' . tablename('ewei_shop_store_withdrawal') . ' where 1 ' . $condition . ' order by createtime desc LIMIT ' . (($pindex - 1) * $psize) . ',' . $psize, $params);
		$total = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_store_withdrawal') . ' where 1 ' . $condition, $params);
		foreach ($list as &$row) {
			$row['createtime'] = date('Y-m-d H:i', $row['createtime']);
			$row['typestr'] = $apply_type[$row['paytype']];
			if($row['orderid']){
				$ordersn = pdo_fetchcolumn('select ordersn from '.tablename('ewei_shop_order').' where id=:id and uniacid=:uniacid ',array(':id'=>$row['orderid'],':uniacid'=>$_W['uniacid']));
				if(!empty($ordersn)){
					$row['ordersn'] = $ordersn;
				} 
			}
		}
		unset($row);
		show_json(1, array('list' => $list, 'total' => $total, 'pagesize' => $psize));
	}
}

?>
