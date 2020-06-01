<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Withdrawal_EweiShopV2Page extends ComWebPage
{
	public function __construct($_com = 'verify')
	{
		parent::__construct($_com);
	}

	public function main($type = 1) 
	{
		global $_W;
		global $_GPC;
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		$condition = '  and w.amount<>0';
		$params = array(':uniacid' => $_W['uniacid']);
		if (!(empty($_GPC['keyword']))) 
		{
			$_GPC['keyword'] = trim($_GPC['keyword']);
			$condition .= ' and (m.realname like :keyword or m.nickname like :keyword or m.mobile like :keyword or s.storename like :keyword)';
			$params[':keyword'] = '%' . $_GPC['keyword'] . '%';
		}
		if (empty($starttime) || empty($endtime)) 
		{
			$starttime = strtotime('-1 month');
			$endtime = time();
		}
		if (!(empty($_GPC['time']['start'])) && !(empty($_GPC['time']['end']))) 
		{
			$starttime = strtotime($_GPC['time']['start']);
			$endtime = strtotime($_GPC['time']['end']);
			$condition .= ' AND w.createtime >= :starttime AND w.createtime <= :endtime ';
			$params[':starttime'] = $starttime;
			$params[':endtime'] = $endtime;
		}
		if ($_GPC['status'] != '') 
		{
			$condition .= ' and w.status=' . intval($_GPC['status']);
		}else{
			$condition .= ' and w.status<>0';
		}
		$sql = 'select m.id as mid,m.avatar,m.nickname,m.mobile,m.realname,s.storename,w.* from '.tablename('ewei_shop_member').' as m,'.tablename('ewei_shop_store').' as s,'.tablename('ewei_shop_store_withdrawal').' as w where w.openid=m.openid and w.storeid=s.id and w.openid=s.distributor '.$condition.' and w.uniacid=:uniacid and m.uniacid=:uniacid and s.uniacid=:uniacid order by w.createtime desc ';
		$params[':uniacid'] = $_W['uniacid'];
		if (empty($_GPC['export'])) 
		{
			$sql .= 'LIMIT ' . (($pindex - 1) * $psize) . ',' . $psize;
		}
		$list = pdo_fetchall($sql, $params);
		// echo '<pre>';
		// var_dump($list);exit;
		$apply_type = array(0 => '微信钱包', 2 => '支付宝', 3 => '银行卡');
		if (!(empty($list))) 
		{
			$openids = array();
			foreach ($list as $key => $value ) 
			{
				$list[$key]['typestr'] = $apply_type[$value['applytype']];
				if ($value['deductionmoney'] == 0) 
				{
					$list[$key]['realmoney'] = $value['money'];
				}
				if (!(strexists($value['openid'], 'sns_wa_'))) 
				{
					array_push($openids, $value['openid']);
				}
				else 
				{
					array_push($openids, substr($value['openid'], 7));
				}
			}
			$members_sql = 'select id as mid, realname,avatar,weixin,nickname,mobile,openid,openid_wa from ' . tablename('ewei_shop_member') . ' where uniacid=:uniacid and openid IN (\'' . implode('\',\'', array_unique($openids)) . '\') OR openid_wa IN (\'' . implode('\',\'', array_unique($openids)) . '\')';
			$members = pdo_fetchall($members_sql, array(':uniacid' => $_W['uniacid']), 'openid');
			$rs = array();
			if (!(empty($members))) 
			{
				foreach ($members as $key => &$row ) 
				{
					if (!(empty($row['openid_wa']))) 
					{
						$rs['sns_wa_' . $row['openid_wa']] = $row;
					}
					else 
					{
						$rs[] = $row;
					}
				}
			}
			$member_openids = array_keys($members);
			foreach ($list as $key => $value ) 
			{
				if (in_array($list[$key]['openid'], $member_openids)) 
				{
					$list[$key] = array_merge($list[$key], $members[$list[$key]['openid']]);
				}
				else 
				{
					$list[$key] = array_merge($list[$key], (isset($rs[$list[$key]['openid']]) ? $rs[$list[$key]['openid']] : array()));
				}
			}
		}
		if ($_GPC['export'] == 1) 
		{
			if ($_GPC['type'] == 1) 
			{
				plog('finance.log.withdraw.export', '导出提现记录');
			}
			else 
			{
				plog('finance.log.recharge.export', '导出充值记录');
			}
			foreach ($list as &$row ) 
			{
				$row['createtime'] = date('Y-m-d H:i', $row['createtime']);
				$row['groupname'] = ((empty($row['groupname']) ? '无分组' : $row['groupname']));
				$row['levelname'] = ((empty($row['levelname']) ? '普通会员' : $row['levelname']));
				$row['typestr'] = $apply_type[$row['applytype']];
				if ($row['status'] == 0) 
				{
					if ($row['type'] == 0) 
					{
						$row['status'] = '未充值';
					}
					else 
					{
						$row['status'] = '申请中';
					}
				}
				else if ($row['status'] == 1) 
				{
					if ($row['type'] == 0) 
					{
						$row['status'] = '充值成功';
					}
					else 
					{
						$row['status'] = '完成';
					}
				}
				else if ($row['status'] == -1) 
				{
					if ($row['type'] == 0) 
					{
						$row['status'] = '';
					}
					else 
					{
						$row['status'] = '失败';
					}
				}
				if ($row['rechargetype'] == 'system') 
				{
					$row['rechargetype'] = '后台';
				}
				else if ($row['rechargetype'] == 'wechat') 
				{
					$row['rechargetype'] = '微信';
				}
				else if ($row['rechargetype'] == 'alipay') 
				{
					$row['rechargetype'] = '支付宝';
				}
			}
			unset($row);
			$columns = array();
			$columns[] = array('title' => '昵称', 'field' => 'nickname', 'width' => 12);
			$columns[] = array('title' => '姓名', 'field' => 'realname', 'width' => 12);
			$columns[] = array('title' => '手机号', 'field' => 'mobile', 'width' => 12);
			$columns[] = array('title' => '会员等级', 'field' => 'levelname', 'width' => 12);
			$columns[] = array('title' => '会员分组', 'field' => 'groupname', 'width' => 12);
			$columns[] = array('title' => (empty($type) ? '充值金额' : '提现金额'), 'field' => 'money', 'width' => 12);
			if (!(empty($type))) 
			{
				$columns[] = array('title' => '到账金额', 'field' => 'realmoney', 'width' => 12);
				$columns[] = array('title' => '手续费金额', 'field' => 'deductionmoney', 'width' => 12);
				$columns[] = array('title' => '提现方式', 'field' => 'typestr', 'width' => 12);
				$columns[] = array('title' => '提现姓名', 'field' => 'applyrealname', 'width' => 24);
				$columns[] = array('title' => '支付宝', 'field' => 'alipay', 'width' => 24);
				$columns[] = array('title' => '银行', 'field' => 'bankname', 'width' => 24);
				$columns[] = array('title' => '银行卡号', 'field' => 'bankcard', 'width' => 24);
				$columns[] = array('title' => '申请时间', 'field' => 'applytime', 'width' => 24);
			}
			$columns[] = array('title' => (empty($type) ? '充值时间' : '提现申请时间'), 'field' => 'createtime', 'width' => 12);
			if (empty($type)) 
			{
				$columns[] = array('title' => '充值方式', 'field' => 'rechargetype', 'width' => 12);
			}
			$columns[] = array('title' => '备注', 'field' => 'remark', 'width' => 24);
			m('excel')->export($list, array('title' => ((empty($type) ? '会员充值数据-' : '会员提现记录')) . date('Y-m-d-H-i', time()), 'columns' => $columns));
		}
		$total = pdo_fetchcolumn('select count(*) from ' .tablename('ewei_shop_member').' as m,'.tablename('ewei_shop_store').' as s,'.tablename('ewei_shop_store_withdrawal').' as w where w.openid=m.openid and w.storeid=s.id and w.openid=s.distributor '.$condition.' and w.uniacid=:uniacid and m.uniacid=:uniacid and s.uniacid=:uniacid', $params);
		$pager = pagination2($total, $pindex, $psize);
		include $this->template();
	}

	public function alipay() 
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		$log = pdo_fetch('select * from ' . tablename('ewei_shop_store_withdrawal') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $id, ':uniacid' => $_W['uniacid']));

		if (empty($log)) 
		{
			show_json(0, '未找到记录!');
		}
		$realmoeny = round($log['amount'],2);
		$set = $_W['shopset']['shop'];
		$sec = m('common')->getSec();
		$sec = iunserializer($sec['sec']);
		if (!(empty($sec['alipay_pay']['open']))) 
		{
			//新提现方法
			if(empty($sec['alipay_pay']['appid']||empty($sec['alipay_pay']['appprikey'])||empty($sec['alipay_pay']['public_key']))){
				show_json(0,"请配置好支付参数");
			}

			$batch_no = 'D' . date('Ymd') . 'CP' . $log['id'] . 'MONEY' . $realmoeny;
			require_once EWEI_SHOPV2_PLUGIN."api/core/mobile/alipayconfig/alipay.php";
			$data = array(
				'out_biz_no'=>$batch_no,
				'payee_account'=>$log['alipay'],
				'amount'=>$realmoeny,
				'remark'=>'门店收益提现'
			);
			$arr = new alipay;
			$res = $arr->ali($data,$sec['alipay_pay']);
			$resfail = $res['alipay_fund_trans_toaccount_transfer_response']['sub_msg'];
			if($res['alipay_fund_trans_toaccount_transfer_response']['code']!='10000'){
				show_json(0,$resfail);	
			}
			pdo_update('ewei_shop_store_withdrawal',array('status'=>2,'sendmoney'=>$realmoeny),array('id'=>$id));
			m('notice')->sendStoreWithdrawalMessage($log['id']);	
			$member = m('member')->getMember($log['openid']);
			plog('store.withdrawwal.alipay', '门店收益提现 ID: ' . $log['id'] . ' 方式: 支付宝 提现金额: ' . $log['amount'] . ' ,到账金额: ' . $realmoney . ' ,手续费金额 : ' . $log['deductionmoney'] . '<br/>会员信息:  ID: ' . $member['id'] . ' / ' . $member['openid'] . '/' . $member['nickname'] . '/' . $member['realname'] . '/' . $member['mobile']);
			show_json(1);
		}
	}

	public function wechat() 
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		$log = pdo_fetch('select * from ' . tablename('ewei_shop_store_withdrawal') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $id, ':uniacid' => $_W['uniacid']));
		if (empty($log)) 
		{
			show_json(0, '未找到记录!');
		}
		$realmoney = round($log['amount'],2);
		$set = $_W['shopset']['shop'];
		$data = m('common')->getSysset('pay');
		if (!(empty($data['paytype']['withdraw']))) 
		{
			$batch_no = 'D' . date('Ymd') . 'CP' . $log['id'] . 'MONEY' . $realmoney;
			$result = m('finance')->payRedPack($log['openid'], $realmoney * 100, $batch_no, $log, $set['name'] . '门店收益提现', $data['paytype']);
			pdo_update('ewei_shop_store_withdrawal', array('sendmoney' => $result['sendmoney']), array('id' => $log['id']));
			if ($result['sendmoney'] == $realmoney) 
			{
				$result = true;
			}
			else 
			{
				$result = $result['error'];
			}
		}
		else 
		{
			$result = m('finance')->pay($log['openid'], 1, $realmoney * 100, $log['logno'], $set['name'] . '余额提现');
		}
		if (is_error($result)) 
		{
			show_json(0, array('message' => $result['message']));
		}
		pdo_update('ewei_shop_store_withdrawal', array('status' => 2,), array('id' => $id, 'uniacid' => $_W['uniacid']));
		m('notice')->sendStoreWithdrawalMessage($log['id']);
		$member = m('member')->getMember($log['openid']);
		plog('store.withdrawwal.wechat', '门店收益提现 ID: ' . $log['id'] . ' 方式: 微信 提现金额: ' . $log['amount'] . ' ,到账金额: ' . $realmoney . ' ,手续费金额 : ' . $log['deductionmoney'] . '<br/>会员信息:  ID: ' . $member['id'] . ' / ' . $member['openid'] . '/' . $member['nickname'] . '/' . $member['realname'] . '/' . $member['mobile']);
		show_json(1);
	}

	public function manual() 
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		$log = pdo_fetch('select * from ' . tablename('ewei_shop_store_withdrawal') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $id, ':uniacid' => $_W['uniacid']));
		if (empty($log)) 
		{
			show_json(0, '未找到记录!');
		}
		$member = m('member')->getMember($log['openid']);
		pdo_update('ewei_shop_store_withdrawal', array('status' => 2,'sendmoney'=>round($log['amount'],2)), array('id' => $id, 'uniacid' => $_W['uniacid']));
		m('notice')->sendStoreWithdrawalMessage($log['id']);
		plog('store.withdrawwal.manual', '余额提现 方式: 手动 ID: ' . $log['id'] . ' <br/>会员信息: ID: ' . $member['id'] . ' / ' . $member['openid'] . '/' . $member['nickname'] . '/' . $member['realname'] . '/' . $member['mobile']);
		show_json(1);
	}

	public function refuse() 
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		$log = pdo_fetch('select * from ' . tablename('ewei_shop_store_withdrawal') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $id, ':uniacid' => $_W['uniacid']));
		if (empty($log)) 
		{
			show_json(0, '未找到记录!');
		}
		pdo_update('ewei_shop_store_withdrawal', array('status' => -1), array('id' => $id, 'uniacid' => $_W['uniacid']));
		// m('notice')->sendMemberLogMessage($log['id']);
		plog('store.withdrawwal.refuse', '拒绝余额度提现 ID: ' . $log['id'] . ' 金额: ' . $log['amount'] . ' <br/>会员信息:  ID: ' . $member['id'] . ' / ' . $member['openid'] . '/' . $member['nickname'] . '/' . $member['realname'] . '/' . $member['mobile']);
		show_json(1);
	}
}

?>
