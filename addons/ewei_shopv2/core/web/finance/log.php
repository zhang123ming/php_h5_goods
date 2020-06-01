<?php
if (!(defined('IN_IA'))) 
{
	exit('Access Denied');
}
class Log_EweiShopV2Page extends WebPage 
{
	protected function main($type = 0) 
	{
		global $_W;
		global $_GPC;
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		$condition = ' and log.uniacid=:uniacid and log.type=:type and log.money<>0';
		$condition1 = '';
		$params = array(':uniacid' => $_W['uniacid'], ':type' => $type);
		if (!(empty($_GPC['keyword']))) 
		{
			$_GPC['keyword'] = trim($_GPC['keyword']);
			if ($_GPC['searchfield'] == 'logno') 
			{
				$condition .= ' and log.logno like :keyword';
			}
			else if ($_GPC['searchfield'] == 'member') 
			{
				$condition1 .= ' and (m.realname like :keyword or m.nickname like :keyword or m.mobile like :keyword)';
			}
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
			$condition .= ' AND log.createtime >= :starttime AND log.createtime <= :endtime ';
			$params[':starttime'] = $starttime;
			$params[':endtime'] = $endtime;
		}
		if (!(empty($_GPC['level']))) 
		{
			$condition1 .= ' and m.level=' . intval($_GPC['level']);
		}
		if (!(empty($_GPC['groupid']))) 
		{
			$condition1 .= ' and m.groupid=' . intval($_GPC['groupid']);
		}
		$member_sql = '';
		if ($condition1 != '') 
		{
			$member_sql = ' and openid IN (SELECT openid FROM ims_ewei_shop_member WHERE uniacid = :uniacid ' . $condition1 . ') OR openid IN (SELECT CONCAT(\'sns_wa_\',openid_wa) FROM ims_ewei_shop_member WHERE uniacid = :uniacid ' . $condition1 . ')';
		}
		if (!(empty($_GPC['rechargetype']))) 
		{
			$_GPC['rechargetype'] = trim($_GPC['rechargetype']);
			if ($_GPC['rechargetype'] == 'system1') 
			{
				$condition .= ' AND log.rechargetype=\'system\' and log.money<0';
			}
			else 
			{
				$condition .= ' AND log.rechargetype=:rechargetype';
				$params[':rechargetype'] = $_GPC['rechargetype'];
			}
		}
		if ($_GPC['status'] != '') 
		{
			$condition .= ' and log.status=' . intval($_GPC['status']);
		}

		if ($_GPC['rechargemoney']) {
			if ($_GPC['rechargemoney']['min']>$_GPC['rechargemoney']['max']) {
				$_GPC['rechargemoney']['min']=$_GPC['rechargemoney']['max'];
			}
			if ($_GPC['rechargemoney']['min']||$_GPC['rechargemoney']['max']) {
				$condition .= " and log.money BETWEEN {$_GPC[rechargemoney][min]} and {$_GPC[rechargemoney][max]} "; 
			}
		}
		$sql = 'select log.id,log.openid,log.logno,log.type,log.status,log.rechargetype,log.sendmoney,log.money,log.createtime,log.realmoney,log.deductionmoney,log.charge,log.remark,log.alipay,log.bankname,log.bankcard,log.realname as applyrealname,log.applytype,m.nickname,m.id as mid,m.avatar,m.level,m.groupid,m.realname,m.mobile,g.groupname,l.levelname from ' . tablename('ewei_shop_member_log') . ' log ' . ' left join ' . tablename('ewei_shop_member') . ' m on m.openid = log.openid ' . ' left join ' . tablename('ewei_shop_member_group') . ' g on g.id = m.groupid ' . ' left join ' . tablename('ewei_shop_member_level') . ' l on l.id = m.level ' . ' where 1 ' . $condition . ' ' . $condition1 . ' GROUP BY log.id ORDER BY log.createtime DESC ';
		if (empty($_GPC['export'])) 
		{
			$sql .= 'LIMIT ' . (($pindex - 1) * $psize) . ',' . $psize;
		}
		$list = pdo_fetchall($sql, $params);
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
		$total = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_member_log') . ' log ' . ' where 1 ' . $condition . ' ' . $member_sql, $params);
		$pager = pagination2($total, $pindex, $psize);
		$groups = m('member')->getGroups();
		$levels = m('member')->getLevels();
		include $this->template();
	}
	public function refund($tid = 0, $fee = 0, $reason = '') 
	{
		global $_W;
		global $_GPC;
		$set = $_W['shopset']['shop'];
		$id = intval($_GPC['id']);
		$log = pdo_fetch('select * from ' . tablename('ewei_shop_member_log') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $id, ':uniacid' => $_W['uniacid']));
		if (empty($log)) 
		{
			show_json(0, '未找到记录!');
		}
		if (!(empty($log['type']))) 
		{
			show_json(0, '非充值记录!');
		}
		if ($log['rechargetype'] == 'system') 
		{
			show_json(0, '后台充值无法退款!');
		}
		$current_credit = m('member')->getCredit($log['openid'], 'credit2');
		if ($current_credit < $log['money']) 
		{
			show_json(0, '会员账户余额不足，无法进行退款!');
		}
		$out_refund_no = 'RR' . substr($log['logno'], 2);
		if ($log['rechargetype'] == 'wechat') 
		{
			if (empty($log['isborrow'])) 
			{
				$result = m('finance')->refund($log['openid'], $log['logno'], $out_refund_no, $log['money'] * 100, $log['money'] * 100, (!(empty($log['apppay'])) ? true : false));
			}
			else 
			{
				$result = m('finance')->refundBorrow($log['openid'], $log['logno'], $out_refund_no, $log['money'] * 100, $log['money'] * 100);
			}
		}
		else if ($log['rechargetype'] == 'alipay') 
		{
			$sec = m('common')->getSec();
			$sec = iunserializer($sec['sec']);
			if (!(empty($log['apppay']))) 
			{
				if (empty($sec['app_alipay']['private_key']) || empty($sec['app_alipay']['appid'])) 
				{
					show_json(0, '支付参数错误，私钥为空或者APPID为空!');
				}
				$params = array('out_trade_no' => $log['logno'], 'refund_amount' => $log['money'], 'refund_reason' => '会员充值退款: ' . $log['money'] . '元 订单号: ' . $log['logno'] . '/' . $out_refund_no);
				$config = array('app_id' => $sec['app_alipay']['appid'], 'privatekey' => $sec['app_alipay']['private_key'], 'publickey' => '', 'alipublickey' => '');
				$result = m('finance')->newAlipayRefund($params, $config);
			}
			else 
			{
				if (empty($log['transid'])) 
				{
					show_json(0, '仅支持 升级后此功能后退款的订单!');
				}
				$setting = uni_setting($_W['uniacid'], array('payment'));
				if (!(is_array($setting['payment']))) 
				{
					return error(1, '没有设定支付参数');
				}
				$alipay_config = $setting['payment']['alipay'];
				$batch_no_money = $log['money'] * 100;
				$batch_no = date('Ymd') . 'RC' . $log['id'] . 'MONEY' . $batch_no_money;
				$res = m('finance')->AlipayRefund(array('trade_no' => $log['transid'], 'refund_price' => $log['money'], 'refund_reason' => '会员充值退款: ' . $log['money'] . '元 订单号: ' . $log['logno'] . '/' . $out_refund_no), $batch_no, $alipay_config);
				if (is_error($res)) 
				{
					show_json(0, $res['message']);
				}
				show_json(1, array('url' => $res));
			}
		}
		else 
		{
			$result = m('finance')->pay($log['openid'], 1, $log['money'] * 100, $out_refund_no, $set['name'] . '充值退款');
		}
		if (is_error($result)) 
		{
			show_json(0, $result['message']);
		}
		pdo_update('ewei_shop_member_log', array('status' => 3), array('id' => $id, 'uniacid' => $_W['uniacid']));
		$refundmoney = $log['money'] + $log['gives'];
		m('member')->setCredit($log['openid'], 'credit2', -$refundmoney, array(0, $set['name'] . '充值退款'));
		$money = com_run('sale::getCredit1', $log['openid'], (double) $log['money'], 21, 2, 1);
		if (0 < $money) 
		{
			m('notice')->sendMemberPointChange($log['openid'], $money, 1);
		}
		m('notice')->sendMemberLogMessage($log['id']);
		$member = m('member')->getMember($log['openid']);
		plog('finance.log.refund', '充值退款 ID: ' . $log['id'] . ' 金额: ' . $log['money'] . ' <br/>会员信息:  ID: ' . $member['id'] . ' / ' . $member['openid'] . '/' . $member['nickname'] . '/' . $member['realname'] . '/' . $member['mobile']);
		show_json(1, array('url' => referer()));
	}
	public function wechat() 
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		$log = pdo_fetch('select * from ' . tablename('ewei_shop_member_log') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $id, ':uniacid' => $_W['uniacid']));
		if (empty($log)) 
		{
			show_json(0, '未找到记录!');
		}
		if ($log['deductionmoney'] == 0) 
		{
			$realmoney = $log['money'];
		}
		else 
		{
			$realmoney = $log['realmoney'];
		}
		$set = $_W['shopset']['shop'];
		$data = m('common')->getSysset('pay');
		if (!(empty($data['paytype']['withdraw']))) 
		{
			$result = m('finance')->payRedPack($log['openid'], $realmoney * 100, $log['logno'], $log, $set['name'] . '余额提现', $data['paytype']);
			pdo_update('ewei_shop_member_log', array('sendmoney' => $result['sendmoney'], 'senddata' => json_encode($result['senddata'])), array('id' => $log['id']));
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
		pdo_update('ewei_shop_member_log', array('status' => 1), array('id' => $id, 'uniacid' => $_W['uniacid']));
		m('notice')->sendMemberLogMessage($log['id']);
		$member = m('member')->getMember($log['openid']);
		plog('finance.log.wechat', '余额提现 ID: ' . $log['id'] . ' 方式: 微信 提现金额: ' . $log['money'] . ' ,到账金额: ' . $realmoney . ' ,手续费金额 : ' . $log['deductionmoney'] . '<br/>会员信息:  ID: ' . $member['id'] . ' / ' . $member['openid'] . '/' . $member['nickname'] . '/' . $member['realname'] . '/' . $member['mobile']);
		show_json(1);
	}
	public function alipay() 
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		$log = pdo_fetch('select * from ' . tablename('ewei_shop_member_log') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $id, ':uniacid' => $_W['uniacid']));
		if (empty($log)) 
		{
			show_json(0, '未找到记录!');
		}
		if ($log['deductionmoney'] == 0) 
		{
			$realmoeny = $log['money'];
		}
		else 
		{
			$realmoeny = $log['realmoney'];
		}
		$set = $_W['shopset']['shop'];
		$sec = m('common')->getSec();
		$sec = iunserializer($sec['sec']);
		if (!(empty($sec['alipay_pay']['open']))) 
		{
			$batch_no_money = $realmoeny * 100;
			$batch_no = 'D' . date('Ymd') . 'RW' . $log['id'] . 'MONEY' . $batch_no_money;
			$res = m('finance')->AliPay(array('account' => $log['alipay'], 'name' => $log['realname'], 'money' => $realmoeny), $batch_no, $sec['alipay_pay'], $log['title']);
			if (is_error($res)) 
			{
				show_json(0, $res['message']);
			}
			show_json(1, array('url' => $res));
		}
		show_json(0, '未开启,支付宝打款!');
	}
	public function manual() 
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		$log = pdo_fetch('select * from ' . tablename('ewei_shop_member_log') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $id, ':uniacid' => $_W['uniacid']));
		if (empty($log)) 
		{
			show_json(0, '未找到记录!');
		}
		$member = m('member')->getMember($log['openid']);
		pdo_update('ewei_shop_member_log', array('status' => 1), array('id' => $id, 'uniacid' => $_W['uniacid']));
		m('notice')->sendMemberLogMessage($log['id']);
		plog('finance.log.manual', '余额提现 方式: 手动 ID: ' . $log['id'] . ' <br/>会员信息: ID: ' . $member['id'] . ' / ' . $member['openid'] . '/' . $member['nickname'] . '/' . $member['realname'] . '/' . $member['mobile']);
		show_json(1);
	}
	public function refuse() 
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		$log = pdo_fetch('select * from ' . tablename('ewei_shop_member_log') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $id, ':uniacid' => $_W['uniacid']));
		if (empty($log)) 
		{
			show_json(0, '未找到记录!');
		}
		pdo_update('ewei_shop_member_log', array('status' => -1), array('id' => $id, 'uniacid' => $_W['uniacid']));
		if (0 < $log['money']) 
		{
			m('member')->setCredit($log['openid'], 'credit2', $log['money'], array(0, $set['name'] . '余额提现退回'));
		}
		m('notice')->sendMemberLogMessage($log['id']);
		plog('finance.log.refuse', '拒绝余额度提现 ID: ' . $log['id'] . ' 金额: ' . $log['money'] . ' <br/>会员信息:  ID: ' . $member['id'] . ' / ' . $member['openid'] . '/' . $member['nickname'] . '/' . $member['realname'] . '/' . $member['mobile']);
		show_json(1);
	}
	public function recharge() 
	{
		$this->main(0);
	}
	public function withdraw() 
	{
		$this->main(1);
	}
	public function collect()
	{
		global $_W;
		global $_GPC;
		$params = array();
		$params[':uniacid'] = $_W['uniacid'];
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		$condition = '';
		if($_GPC['status']!=''){
			$condition .= 'and mo.status=:status';
			$params[':status'] = $_GPC['status'];
		}
		if(!empty($_GPC['keyword'])){
			$condition .= ' and ( locate(:keyword,m.nickname)>0 or locate(:keyword,m.id)>0 or locate(:keyword,m.mobile)>0)';
			$params[':keyword'] = $_GPC['keyword'];
		}
		if(!empty($_GPC['time']['start'])&&!empty($_GPC['time']['end'])){
			$start = strtotime($_GPC['time']['start']);
			$end = strtotime($_GPC['time']['end']);
			$condition .= ' and mo.createtime BETWEEN :start and :end ';
			$params[':start'] = $start;
			$params[':end'] = $end;
		}

		$list = pdo_fetchall('select mo.id,mo.openid,mo.createtime,mo.amount,mo.status,mo.images,m.nickname,m.avatar,m.id as mid,m.mobile from '.tablename('ewei_shop_member_collect').' as mo,'.tablename('ewei_shop_member').' as m where mo.openid=m.openid and mo.uniacid=m.uniacid and m.uniacid=:uniacid and mo.uniacid=:uniacid '.$condition.' order by createtime desc limit '.($pindex-1)*$psize.','.$psize,$params);
		$total = pdo_fetchcolumn('select count(*) from '.tablename('ewei_shop_member_collect').' as mo,'.tablename('ewei_shop_member').' as m where mo.openid=m.openid and mo.uniacid=m.uniacid and m.uniacid=:uniacid and mo.uniacid=:uniacid '.$condition,$params);
		
		$pager = pagination2($total, $pindex, $psize);

		include $this->template();
	}

	public function images()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		$row = pdo_fetch('select mm.content,mm.images,m.nickname,m.avatar,m.mobile from '.tablename('ewei_shop_member').' as m,'.tablename('ewei_shop_member_collect').' as mm where mm.openid=m.openid and mm.uniacid=m.uniacid and mm.id=:id',array(':id'=>$id));
		$row['images'] = json_decode($row['images']);
		include $this->template();
	}

	public function collect_check()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		$status = intval($_GPC['status']);
		if(empty($id)||empty($status)){
			return false;
		}
		$row = pdo_fetch('select status,openid,amount from '.tablename('ewei_shop_member_collect').' where id=:id and uniacid=:uniacid ',array(':id'=>$id,':uniacid'=>$_W['uniacid']));
		if(!empty($row['openid'])&&$row['amount']>0){
			$a = pdo_update('ewei_shop_member_collect',array('status'=>$status,'paytype'=>time()),array('id'=>$id,'uniacid'=>$_W['uniacid']));
			m('member')->setCredit($row['openid'], 'credit2', $row['amount'], array($_W['uid'], '后台会员充值' . $typestr . ' ' . $remark));
			$set = m('common')->getSysset('shop');
			$logno = m('common')->createNO('member_log', 'logno', 'RC');
			$data = array('openid' => $row['openid'], 'logno' => $logno, 'uniacid' => $_W['uniacid'], 'type' => '0', 'createtime' => TIMESTAMP, 'status' => '1', 'title' => '转账审核通过充值', 'money' => $row['amount'], 'remark' => '转账审核通过充值', 'rechargetype' => 'system');
			pdo_insert('ewei_shop_member_log', $data);
			$logid = pdo_insertid();
			m('notice')->sendMemberLogMessage($logid, 0, true);
			show_json(1);
		}
	}

	public function collect_delete()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		if(empty($id)){
			return false;
		}
		pdo_delete('ewei_shop_member_collect',array('id'=>$id));
		show_json(1);

	}
	/*芭提雅 充值计算返利
	*$param $openid 充值者openid
	*$param $amount 充值金额
	*
	*return 无返回值
	*/
	public function bty_rechargeCommission($openid,$amount)
	{
		global $_W;
		$set = p('commission')->getSet();
		$member = m('member')->getMember($openid);
		$commissions = array();
		$three_level_id = array();
		if(!empty($member)){
			$levels = p('commission')->getLevels();
			//计算三级返利
			foreach ($levels as $level) {
				$cinfo['commission1']['level' . $level['id']] = 1 <= $set['level'] ? $level['commission1']: 0;
				$cinfo['commission2']['level' . $level['id']] = 2 <= $set['level'] ? $level['commission2']: 0;
				$cinfo['commission3']['level' . $level['id']] = 3 <= $set['level'] ? $level['commission3']: 0;
			}
			$cinfo['commission1']['level0'] = $set['commission1'];
			$cinfo['commission2']['level0'] = $set['commission2'];
			$cinfo['commission3']['level0'] = $set['commission3'];
			$m1 = m('member')->getMember($member['agentid']);
			if(!empty($m1)){
				$ml_alevel = p('commission')->getLevel($ml['openid']);
				if(!empty($ml_alevel)){
					$commissions['level1'][$m1['id']] =  $cinfo['commission1']['level'.$ml_alevel['id']];	
				}else{
					$commissions['level1'][$m1['id']] = $cinfo['commission1']['level0'];
				}
				array_push($three_level_id,$m1['id']);
			}
			if(!empty($m1['agentid'])){
				$m2 = m('member')->getMember($m1['agentid']);

				if(!empty($m2)){
					$m2_alevel = p('commission')->getLevel($m2['openid']);
					if(!empty($m2_alevel)){
						$commissions['level2'][$m2['id']] =  $cinfo['commission2']['level'.$m2_alevel['id']];	
					}else{
						$commissions['level2'][$m2['id']] =  $cinfo['commission2']['level0'];
					}
					array_push($three_level_id,$m2['id']);
				}
				if(!empty($m2['agentid'])){
					$m3 = m('member')->getMember($m2['agentid']);
					if(!empty($m3)){
						$m3_alevel = p('commission')->getLevel($m3['openid']);
						if(!empty($m3_alevel)){
							$commissions['level3'][$m3['id']] =  $cinfo['commission3']['level'.$m3_alevel['id']];	
						}else{
							$commissions['level3'][$m3['id']] =  $cinfo['commission3']['level0'];
						}
					}
					array_push($three_level_id,$m3['id']);
				}
			}
			
			$agentInfo[100] = m('member')->getLevel($member['agent100']);
			$agentInfo[99] =  m('member')->getLevel($member['agent99']);
			$agentInfo[98] =  m('member')->getLevel($member['agent98']);
			$agentInfo[97] =  m('member')->getLevel($member['agent97']);
			$agentInfo[96] =  m('member')->getLevel($member['agent96']);
			$agentInfo[95] =  m('member')->getLevel($member['agent95']);
			$agentInfo[94] =  m('member')->getLevel($member['agent94']);
			$agentInfo[93] =  m('member')->getLevel($member['agent93']);
			$agentInfo[92] =  m('member')->getLevel($member['agent92']);
			$agentInfo[91] =  m('member')->getLevel($member['agent91']);

			if(!empty($member['agent96']) && !in_array($member['agent96'],$three_level_id)){
				$downLevelNum = pdo_fetchcolumn('select count(*) from '.tablename('ewei_shop_member').' as m,'.tablename('ewei_shop_member_level').' as ml where m.level=ml.id and ml.level>92 and m.isagent=1 and m.status=1 and m.uniacid=:uniacid and find_in_set(:id,m.fids) ',array(':uniacid'=>$_W['uniacid'],':id'=>$member['agent96']));
				if($downLevelNum>=$set['downNumLevel'][96]){
					$agentInfo[96]['commission']=$set['ForeverEnjoy'][96];
				}else{
					$agentInfo[96]['commission']=0;
				}
			}
			if(!empty($member['agent94']) && !in_array($member['agent94'],$three_level_id)){
				$downNum = pdo_fetchcolumn('select count(*) from '.tablename('ewei_shop_member').'  where  isagent=1 and status=1 and uniacid=:uniacid and find_in_set(:id,fids) ',array(':uniacid'=>$_W['uniacid'],':id'=>$member['agent94']));
				if($downNum>=$set['downNum'][94]){
					$agentInfo[94]['commission']=$set['ForeverEnjoy'][94];
				}else{
					$agentInfo[94]['commission']=0;
				}
			}


			$commissions['agent100'][$member['agent100']] =$agentInfo[100]['commission'] ? $agentInfo[100]['commission'] : 0;	
			$commissions['agent99'][$member['agent99']]  =$agentInfo[99]['commission'] ? $$agentInfo[99]['commission'] : 0;
			$commissions['agent98'][$member['agent98']]  =$agentInfo[98]['commission'] ? $agentInfo[98]['commission'] : 0;
			$commissions['agent97'][$member['agent97']]  =$agentInfo[97]['commission'] ? $agentInfo[97]['commission'] : 0;
			$commissions['agent96'][$member['agent96']]  =$agentInfo[96]['commission'] ? $agentInfo[96]['commission']: 0;
			$commissions['agent95'][$member['agent95']]  =$agentInfo[95]['commission'] ? $agentInfo[95]['commission'] : 0;
			$commissions['agent94'][$member['agent94']]  =$agentInfo[94]['commission'] ? $agentInfo[94]['commission'] : 0;
			$commissions['agent93'][$member['agent93']]  =$agentInfo[93]['commission'] ? $agentInfo[93]['commission'] : 0;
			$commissions['agent92'][$member['agent92']]  =$agentInfo[92]['commission'] ? $agentInfo[92]['commission'] : 0;
			$commissions['agent91'][$member['agent91']]  =$agentInfo[91]['commission'] ? $agentInfo[91]['commission'] : 0;

			foreach ($commissions as $ck => $cv) {
				$string = substr($ck, 0,5);
				if($string=='level'){
					if($cv[key($cv)]>0){
						switch ($ck) {
							case 'level1':
									$openid = $this->getOpenid(key($cv));
									$this->setLevelRecord($openid,$cv[key($cv)],'充值'.$_W['shopset']['commission']['texts']['c1'].'奖励');
									unset($openid);
								break;
							case 'level2':
									$openid = $this->getOpenid(key($cv));
									$this->setLevelRecord($openid,$cv[key($cv)],'充值'.$_W['shopset']['commission']['texts']['c2'].'奖励');
									unset($openid);
								break;
							case 'level3':
									$openid = $this->getOpenid(key($cv));
									$this->setLevelRecord($openid,$cv[key($cv)],'充值'.$_W['shopset']['commission']['texts']['c3'].'奖励');
									unset($openid);
								break;
						}
					}
				}elseif($string=='agent'){
					if($cv[key($cv)]>0){
						$openid = $this->getOpenid(key($cv));
						$levelName = m('member')->getLevel(key($cv));
						$this->setLevelRecord($openid,$cv[key($cv)],'充值'.$levelName['levelname'].'代理奖励');
						unset($openid);
					}
				}


			}
		}
	}

	public function getOpenid($id)
	{
		global $_W;
		if(!empty($id)){
			$openid = pdo_fetchcolumn('select openid from '.tablename('ewei_shop_member').' where uniacid=:uniacid and id=:id',array(':uniacid'=>$_W['uniacid'],':id'=>$id));
			if($openid){
				return $openid;
			}	
		}else{
			return '不存在的id';
		}
	}
	public function setLevelRecord($openid,$amount,$remark)
	{
		global $_W;
		m('common')->setRecord($openid,$amount,array($remark,array('111'=>222)));
		$total = pdo_fetchcolumn('select sum(amount) from '.tablename('ewei_shop_commission_record').' where uniacid=:uniacid and openid=:openid',array(':uniacid'=>$_W['uniacid'],':openid'=>$openid));
		pdo_update('ewei_shop_member',array('credit3'=>$total),array('uniacid'=>$_W['uniacid'],'openid'=>$openid));
	}
}
?>