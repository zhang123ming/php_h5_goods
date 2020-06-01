<?php
if (!(defined('IN_IA'))) 
{
	exit('Access Denied');
}
class Bonus_EweiShopV2Page extends PluginWebPage 
{
	public function status0() 
	{
		$this->get_list(0);
	}
	public function status1() 
	{
		$this->get_list(1);
	}
	public function status2() 
	{
		$this->get_list(2);
	}
	protected function get_list($status = 0) 
	{
		global $_W;
		global $_GPC;
		$set = $this->getSet();
		$years = array();
		$current_year = date('Y');
		$i = $current_year - 10;
		while ($i <= $current_year) 
		{
			$years[] = $i;
			++$i;
		}
		$months = array();
		$i = 1;
		while ($i <= 12) 
		{
			$months[] = ((strlen($i) == 1 ? '0' . $i : $i));
			++$i;
		}
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		$condition = ' and uniacid=:uniacid and status=:status';
		$params = array(':uniacid' => $_W['uniacid'], ':status' => $status);
		if ($_GPC['year'] != '') 
		{
			$condition .= ' and `year`=' . intval($_GPC['year']);
		}
		if ($_GPC['month'] != '') 
		{
			$condition .= ' and `month`=' . intval($_GPC['month']);
		}
		if ($_GPC['week'] != '') 
		{
			$condition .= ' and `week`=' . intval($_GPC['week']);
		}
		$keyword = trim($_GPC['keyword']);
		if (!(empty($keyword))) 
		{
			$condition .= ' and billno like :keyword ';
			$params[':keyword'] = '%' . $keyword . '%';
		}
		$sql = 'select *  from ' . tablename('ewei_shop_groupaward_bill') . '  where 1 ' . $condition . ' ORDER BY createtime desc ';
		if (empty($_GPC['export'])) 
		{
			$sql .= '  limit ' . (($pindex - 1) * $psize) . ',' . $psize;
		}
		$list = pdo_fetchall($sql, $params);
		if ($_GPC['export'] == 1) 
		{
			ca('abonus.bonus.export');
			plog('abonus.bonus.export', '导出结算单');
			foreach ($list as &$row ) 
			{
				$row['createtime'] = ((empty($row['createtime']) ? '' : date('Y-m-d H:i', $row['createtime'])));
				$row['days'] = $row['year'] . '年' . $row['month'] . '月';
				if ($row['paytype'] == 2) 
				{
					$row['days'] .= '第' . $row['week'] . '周';
				}
				if (empty($row['status'])) 
				{
					$row['statusstr'] = '待确认';
				}
				else if ($row['status'] == 1) 
				{
					$row['statusstr'] = '待结算';
				}
				else if ($row['status'] == 2) 
				{
					$row['statusstr'] = '已结算';
				}
				$row['paytype'] = (($row['paytype'] == 2 ? '按周' : '按月'));
			}
			unset($row);
			m('excel')->export($list, array( 'title' => '结算单-' . time(), 'columns' => array( array('title' => 'ID', 'field' => 'id', 'width' => 12), array('title' => '结算类型', 'field' => 'paytype', 'width' => 12), array('title' => '单号', 'field' => 'billno', 'width' => 24), array('title' => '日期', 'field' => 'days', 'width' => 12), array('title' => '订单数', 'field' => 'ordercount', 'width' => 12), array('title' => '订单金额', 'field' => 'ordermoney', 'width' => 12), array('title' => '代理数', 'field' => 'agentcount', 'width' => 12), array('title' => '预计分红', 'field' => 'bonusmoney', 'width' => 12), array('title' => '预计分红', 'field' => 'bonusmoney', 'width' => 12), array('title' => '最终分红', 'field' => 'bonusmoney_send', 'width' => 12), array('title' => '状态', 'field' => 'statusstr', 'width' => 12) ) ));
		}
		$total = pdo_fetchcolumn('select count(*) from' . tablename('ewei_shop_groupaward_bill') . '  where 1 ' . $condition, $params);
		$totalmoney = pdo_fetch('select sum(totalProfit) as totalProfit  from' . tablename('ewei_shop_groupaward_bill') . '  where 1 ' . $condition, $params);
		$pager = pagination($total, $pindex, $psize);
		include $this->template('groupaward/bonus/index');
	}
	public function detail() 
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		$data = pdo_fetch('select * from ' . tablename('ewei_shop_groupaward_bill') . ' where id=:id and uniacid=:uniacid limit 1 ', array(':id' => $id, ':uniacid' => $_W['uniacid']));
		if (empty($data)) 
		{
			$this->message('结算单未找到!');
		}
		$data['agentcount'] = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_groupaward_billp') . ' b left join ' . tablename('ewei_shop_member') . ' m on b.openid=m.openid and b.uniacid=m.uniacid  where  b.billid=:billid and b.status=1 and b.uniacid=:uniacid', array(':uniacid' => $_W['uniacid'], ':billid' => $id));
		$condition = ' and b.billid=:billid and b.uniacid =:uniacid';
		$params = array(':billid' => $id, ':uniacid' => $_W['uniacid']);
		$keyword = trim($_GPC['keyword']);
		if (!(empty($keyword))) 
		{
			$condition .= ' and (m.realname like :keyword or m.nickname like :keyword or m.mobile like :keyword)';
			$params[':keyword'] = '%' . $keyword . '%';
		}
		if ($_GPC['status'] != '') 
		{
			if ($_GPC['status'] == 1) 
			{
				$condition .= ' and b.status=1';
			}
			else 
			{
				$condition .= ' and b.status=0 or b.status=-1';
			}
		}
		
		$sql = 'select b.*, m.nickname,m.avatar,m.realname,m.weixin,m.mobile,b.realProfit,l.levelname,m.id as mid from ' . tablename('ewei_shop_groupaward_billp') . ' b ' . ' left join ' . tablename('ewei_shop_member') . ' m on m.openid = b.openid and m.uniacid = b.uniacid left join '.tablename('ewei_shop_member_level') . ' l on m.level=l.id where 1 ' . $condition . ' ORDER BY status asc ';
		$list = pdo_fetchall($sql, $params);
		// echo '<pre>';
		// var_dump($list);exit;
		if ($_GPC['export'] == 1) 
		{
		
			ca('abonus.bonus.export');
			plog('abonus.bonus.export', '导出结算单');
			foreach ($list as &$row ) 
			{
				$row['createtime'] = date('Y-m-d H:y:s',$data['createtime']);
				$row['days'] = $row['year'] . '年' . $row['month'] . '月';
				if ($row['paytype'] == 2) 
				{
					$row['days'] .= '第' . $row['week'] . '周';
				}
				if (empty($row['status'])) 
				{
					$row['statusstr'] = '待确认';
				}
				else if ($row['status'] == 1) 
				{
					$row['statusstr'] = '成功';
				}
				else if ($row['status'] == -1) 
				{
					$row['statusstr'] = '已失效';
				}
				$row['paytype'] = (($row['paytype'] == 2 ? '按周' : '按月'));
			}
			unset($row);
			m('excel')->export(
				$list, 
					array( 'title' => '结算单-' . time(), 'columns' => array( 
							array('title' => 'ID', 'field' => 'id', 'width' => 12),
							array('title' => '结算类型', 'field' => 'paytype','width' => 12),
							array('title' => '单号', 'field' => 'payno', 'width' => 24),
							array('title' => '结算日期', 'field' => 'createtime', 'width' => 12), 
							array('title' => '会员id', 'field' => 'mid', 'width' => 12), 
							array('title' => '会员名', 'field' => 'nickname', 'width' => 12),
							array('title' => '团队业绩', 'field' => 'totalSale', 'width' => 12),
							array('title' => '同级分红', 'field' => 'samegrade', 'width' => 12),
							array('title' => '预计分红', 'field' => 'totalProfit', 'width' => 12), 
							array('title' => '实际分红', 'field' => 'realProfit', 'width' => 12),
							array('title' => '状态', 'field' => 'statusstr', 'width' => 12)
						 )
					)
			);
		}		
		$set = $this->getSet();
		$commissionSet = m('common')->getPluginset('commission');
		$levels = $this->model->getLevels();
		include $this->template();
	}
	public function delete() 
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		$data = pdo_fetch('select * from ' . tablename('ewei_shop_groupaward_bill') . ' where id=:id and uniacid=:uniacid limit 1 ', array(':id' => $id, ':uniacid' => $_W['uniacid']));
		if (empty($data)) 
		{
			show_json(0, '结算单未找到!');
		}
		if (!(empty($data['status']))&&$data['status']==2) 
		{
			show_json(0, '结算单已经结算，不能删除!');
		}
		pdo_query('delete from ' . tablename('ewei_shop_groupaward_bill') . ' where id=:id and uniacid=:uniacid limit 1 ', array(':id' => $id, ':uniacid' => $_W['uniacid']));
		pdo_query('delete from ' . tablename('ewei_shop_groupaward_billo') . ' where billid=:id and uniacid=:uniacid limit 1 ', array(':id' => $id, ':uniacid' => $_W['uniacid']));
		pdo_query('delete from ' . tablename('ewei_shop_groupaward_billp') . ' where billid=:id and uniacid=:uniacid limit 1 ', array(':id' => $id, ':uniacid' => $_W['uniacid']));
		pdo_query('delete from ' . tablename('ewei_shop_groupaward_fullback') . ' where billid=:id and uniacid=:uniacid limit 1 ', array(':id' => $id, ':uniacid' => $_W['uniacid']));
		plog('groupaward.bonus.delete', '删除结算单 ID:' . $data . ' 单号: ' . $data['billno'] . ' <br/>分红:' . $data['totalProfit'] .' <br/>代理人数: ' . $data['totalAgent'] );
		show_json(1);
	}
	public function totals() 
	{
		global $_W;
		$totals = $this->model->getTotals();
		show_json(1, $totals);
	}
	public function build() 
	{
		global $_W;
		global $_GPC;
		$set = $this->getSet();
		$commissionSet = m('common')->getPluginset('commission');
		if ($_W['ispost']) 
		{
			set_time_limit(0);
			$year = intval($_GPC['year']);
			$month = intval($_GPC['month']);
			$week = intval($_GPC['week']);
			$data = $this->model->getBonusData($year, $month, $week);
			if ($data['old']) 
			{
				show_json(1, array('old' => true));
			}
			
			$set = $this->getSet();
			$bill = array('uniacid' => $_W['uniacid'], 'billno' => m('common')->createNO('groupaward_bill', 'billno', 'GA'), 'paytype' => $set['paytype'], 'year' => $year, 'month' => $month, 'week' => $week, 'ordercount' => $data['ordercount'], 'starttime' => $data['starttime'], 'endtime' => $data['endtime'], 'createtime' => time(),'totalProfit'=>$data['totalProfit'],'totalAgent'=>$data['totalAgent'],'ordermoney'=>$data['ordermoney']);
			// foreach ($data['agentGroup'] as $ak => $va) {
			// 	$bill['performance'.$va['level']] = $va['performance'];
			// 	$bill['count'.$va['level']] = $va['num'];
			// }
			
			pdo_insert('ewei_shop_groupaward_bill', $bill);
			$billid = pdo_insertid();
			foreach ($data['orders'] as $order ) 
			{
				$bo = array('uniacid' => $_W['uniacid'], 'billid' => $billid, 'orderid' => $order['id'], 'ordermoney' => $order['realprice']);
				pdo_insert('ewei_shop_groupaward_billo', $bo);
			}

			foreach ($data['agents'] as $a ) 
			{
				$bp = array(
					'uniacid' => $_W['uniacid'], 
					'billid' => $billid, 
					'payno' => m('common')->createNO('groupaward_billp', 'payno', 'GP'), 
					'openid' => $a['openid'], 
					'totalSale' => $a['totalSale'], 
					'totalProfit' => $a['totalProfit'], 
					'realProfit' => $a['realProfit'], 
					'samegrade'=> $a['samegrade'],
					'status' => 0,
					// 'agent100'=>$a['agent100'],'agent99'=>$a['agent99'],'agent98'=>$a['agent98'],'agent97'=>$a['agent97'],'agent96'=>$a['agent96'],'agent95'=>$a['agent95'],'agent94'=>$a['agent94'],'agent93'=>$a['agent93'],'agent92'=>$a['agent92'],'agent91'=>$a['agent91']
				);
                if (($a['totalProfit']>0||$a['samegrade']>0)&&$a['realProfit']>=0)
                {
                    pdo_insert('ewei_shop_groupaward_billp', $bp);
                }
                if ($set['awardMode']==2&&$set['paytype']==1) {
                    // 业绩熔断处理
                    if (!empty($a['independent'])&&!empty($a['agentid'])&&!empty($set['amount'])) {
                        $agentInfo = pdo_fetch('select openid from '.tablename('ewei_shop_member').' where id=:id',array(':id'=>$a['agentid']));
                        $bp['payno']=m('common')->createNO('groupaward_billp', 'payno', 'GP');
                        $bp['totalSale'] = $bp['totalProfit']=0;
                        $bp['realProfit'] = $a['totalSale']*$set['sameRate']*0.01;
                        $bp['openid']= $agentInfo['openid'];
                        if ($bp['realProfit']>0) {
                            pdo_insert('ewei_shop_groupaward_billp', $bp);
                            pdo_update('ewei_shop_groupaward_bill',array('totalProfit +='=>$bp['realProfit'],'totalAgent +='=>1),array('id'=>$billid));
                        }
                    }
                    //业绩独立处理
                    if (!empty($set['amount'])&&$set['amount']>0&&$a['totalSale']>=$set['amount']&&empty($a['independent'])) {
                        $this->model->resetMemberFids($a['id']);
                        pdo_update('ewei_shop_member',array('independent' => 1),array('id'=>$a['id']));
                    }
                }
			}

			plog('groupaward.bonus.build', '创建结算单 ID:' . $billid . ' 单号: ' . $bill['billno'] . ' 团队业绩奖励: ' . $bill['bonusmoney_pay'] . ' 区域代理人数:  ' . $bill['partnercount']);
			show_json(1, array('old' => false));
		}
		$years = array();
		$current_year = date('Y');
		$i = $current_year - 10;
		while ($i <= $current_year) 
		{
			$years[] = $i;
			++$i;
		}
		$months = array();
		$i = 1;
		while ($i <= 12) 
		{
			$months[] = ((strlen($i) == 1 ? '0' . $i : $i));
			++$i;
		}
		$days = get_last_day(date('Y'), date('m'));
		$week = intval($days / date('d')) - 1;
		if (empty($week)) 
		{
			$week = 1;
		}
		$bill = pdo_fetch('select * from ' . tablename('ewei_shop_groupaward_bill') . ' where uniacid=:uniacid order by id desc limit 1 ', array(':uniacid' => $_W['uniacid']));
		include $this->template();
	}
	public function loaddetail() 
	{
		global $_W;
		global $_GPC;
		$year = intval($_GPC['year']);
		$month = intval($_GPC['month']);
		$week = intval($_GPC['week']);
		$commissionSet = m('common')->getPluginset('commission');
		$data = $this->model->getBonusData($year, $month, $week);
		include $this->template('groupaward/bonus/loaddetail');
		exit();
	}
	public function confirm() 
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		$data = pdo_fetch('select * from ' . tablename('ewei_shop_groupaward_bill') . ' where id=:id and uniacid=:uniacid limit 1 ', array(':id' => $id, ':uniacid' => $_W['uniacid']));
		if (empty($data)) 
		{
			show_json(0, '结算单未找到!');
		}
		if (!(empty($data['status']))) 
		{
			show_json(0, '结算单已经确认或已经结算!');
		}
		$time = time();
		pdo_query('update ' . tablename('ewei_shop_groupaward_bill') . ' set status=1,confirmtime=' . $time . ' where id=:id and uniacid=:uniacid', array(':id' => $id, ':uniacid' => $_W['uniacid']));
		plog('groupaward.bonus.confirm', '确认结算单 ID:' . $data['id'] . ' 单号: ' . $data['billno']);
		show_json(1);
	}
	public function pay($a = array(), $b = array()) 
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		$data = pdo_fetch('select * from ' . tablename('ewei_shop_groupaward_bill') . ' where id=:id and uniacid=:uniacid limit 1 ', array(':id' => $id, ':uniacid' => $_W['uniacid']));
		if (empty($data)) 
		{
			show_json(0, '结算单未找到!');
		}
		if ($data['status'] == 2) 
		{
			show_json(0, '结算单已经全部结算!');
		}
		if (empty($data['status'])) 
		{
			$orders = pdo_fetchall('select orderid from ' . tablename('ewei_shop_groupaward_billo') . ' where billid=:billid and uniacid=:uniacid', array(':uniacid' => $_W['uniacid'], ':billid' => $id), 'orderid');
			if (empty($orders)) 
			{
				show_json(0, '未找到任何结算订单!');
			}
			// pdo_query('update ' . tablename('ewei_shop_order') . ' set isabonus=1 where id in ( ' . implode(',', array_keys($orders)) . ' ) and uniacid=' . $_W['uniacid']);
		}
		$time = time();
		pdo_query('update ' . tablename('ewei_shop_groupaward_bill') . ' set paytime=' . $time . ' where id=:id and uniacid=:uniacid', array(':id' => $id, ':uniacid' => $_W['uniacid']));
		plog('groupaward.bonus.pay', '进行结算单结算 ID:' . $data['id'] . ' 单号: ' . $data['billno']);
		show_json(1);
	}
	public function payp() 
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		$baid = intval($_GPC['baid']);
		$set = $this->getSet();
		$commissionSet = m('common')->getPluginset('commission');
		$data = pdo_fetch('select * from ' . tablename('ewei_shop_groupaward_bill') . ' where id=:id and uniacid=:uniacid limit 1 ', array(':id' => $id, ':uniacid' => $_W['uniacid']));
		
		if (empty($data)) 
		{
			show_json(0, '结算单未找到!');
		}
		if ($data['status'] == 2) 
		{
			show_json(0, '结算单已经全部结算!');
		}
		if (empty($baid)) 
		{
			show_json(0, '参数错误!');
		}
		$agent = pdo_fetch('select *  from ' . tablename('ewei_shop_groupaward_billp') . ' where billid=:billid and id=:id and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':billid' => $id, ':id' => $baid));
		if (empty($agent)) 
		{
			show_json(0, '未找到代理!');
		}
		if ($agent['status'] == '1') 
		{
			show_json(0, '此代理已经全部结算!');
		}
		if (empty($agent['openid'])) 
		{
			show_json(0, '结算数据错误!');
		}
		if ($agent['realProfit'] <= 0) 
		{
			show_json(0, '结算数据错误!');
		}
		$member = m('member')->getMember($agent['openid']);
		$pay = $agent['realProfit'];
		$samegrade = $agent['samegrade'];
		$moneytype = intval($set['moneytype']);
		($moneytype <= 0) && ($moneytype = 0);
		if ($pay < 1) 
		{
			$moneytype = 0;
		}
		if (1 < $moneytype) 
		{
			show_json(0, '结算方式错误!');
		}
		if ($moneytype == 1) 
		{
			$pay *= 100;
		}

		if (!(empty($member))) 
		{
			$note = '';
			if ($data['year']) {
				$note.=$data['year'].'年-';
			}
			if ($data['month']) {
				$note.=$data['month'].'月 ';
			}
			if ($data['week']) {
				$note.='第'.$data['week'].'周 ';
			}
			if($data['paytype']==3){
				$note .= '年度';
			}
			if($data['paytype']==2){
				$note .= '周度';
			}
			if($data['paytype']==1){
				$note .= '月度';
			}
			$notename ='团队业绩奖励';
			if($samegrade>0){
				$notename .='+同级分红';
			}
			$detail = array(
				'samegrade'=>$samegrade,
				'totalProfit'=>$agent['totalProfit'],
				'totalSale'=>$agent['totalSale']
			);
			$result = m('common')->setRecord($agent['openid'],$pay,array($note.$notename,array("form"=>"teamorder","teamorderid"=>$agent['payno'])),$detail);
			// $result = m('finance')->pay($agent['openid'], $moneytype, $pay, $agent['payno'],$note.$notename, false);
			if (is_error($result)) 
			{
				pdo_update('ewei_shop_groupaward_billp', array('reason' => $result['message'], 'status' => -1), array('billid' => $id, 'id' => $baid));
			}
			else 
			{
				pdo_update('ewei_shop_groupaward_billp', array('reason' => '', 'status' => 1, 'paytime' => time()), array('billid' => $id, 'id' => $baid));
				// $this->model->upgradeLevelByBonus($agent['openid']);
				// $this->model->sendMessage($agent['openid'], array('paymoney1' => $agent['paymoney1'], 'paymoney2' => $agent['paymoney2'], 'paymoney3' => $agent['paymoney3'], 'aagenttype' => $member['aagenttype'], 'nickname' => $member['nickname'], 'type' => ($moneytype ? '微信钱包' : '余额')), TM_ABONUS_PAY);
			}
		}
		else 
		{
			pdo_update('ewei_shop_groupaward_billp', array('reason' => '未找到会员', 'status' => -1), array('billid' => $id, 'id' => $baid));
		}
		$agentcount = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_groupaward_billp') . ' b left join ' . tablename('ewei_shop_member') . ' m on b.openid=m.openid and b.uniacid=m.uniacid  where m.isagent=1 and m.status=1 and  b.billid=:billid and b.status=1 and b.uniacid=:uniacid', array(':uniacid' => $_W['uniacid'], ':billid' => $id));
		$allagentcount = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_groupaward_billp') . ' where billid=:billid and uniacid=:uniacid', array(':uniacid' => $_W['uniacid'], ':billid' => $id));
		
		$full = $agentcount == $allagentcount;
		if ($full) 
		{
			pdo_query('update ' . tablename('ewei_shop_groupaward_bill') . ' set status=2 where id=:id and uniacid=:uniacid', array(':id' => $id, ':uniacid' => $_W['uniacid']));
		}
		if (is_error($result)) 
		{
			show_json(0, array('message' => $result['message'], 'agentcount' => $agentcount, 'allagentcount' => $allagentcount, 'full' => $full));
		}
		else 
		{
			show_json(1, array('agentcount' => $agentcount,'allagentcount' => $allagentcount, 'full' => $full));
		}
	}
	public function paymoney_level() 
	{
		global $_W;
		global $_GPC;
		$level = intval($_GPC['level']);
		$paymoney1 = trim($_GPC['paymoney1']);
		$paymoney2 = trim($_GPC['paymoney2']);
		$paymoney3 = trim($_GPC['paymoney3']);
		if (($paymoney1 < 0) && ($paymoney2 < 0) && ($paymoney3 < 0)) 
		{
			show_json(0, '参数错误!');
		}
		$id = intval($_GPC['id']);
		$data = pdo_fetch('select * from ' . tablename('ewei_shop_groupaward_bill') . ' where id=:id and uniacid=:uniacid limit 1 ', array(':id' => $id, ':uniacid' => $_W['uniacid']));
		if (empty($data)) 
		{
			show_json(0, '结算单未找到!');
		}
		if (!(empty($data['status']))) 
		{
			show_json(0, '结算单已经确认或结算!');
		}
		$conditions = array();
		if ($paymoney1 != '') 
		{
			$conditions[] = 'b.paymoney1 = ' . $paymoney1;
		}
		if ($paymoney2 != '') 
		{
			$conditions[] = 'b.paymoney2 = ' . $paymoney2;
		}
		if ($paymoney3 != '') 
		{
			$conditions[] = 'b.paymoney3 = ' . $paymoney3;
		}
		if (!(empty($conditions))) 
		{
			$sql = 'update ' . tablename('ewei_shop_groupaward_billp') . ' b ,' . tablename('ewei_shop_member') . ' m set ' . implode(',', $conditions) . ' where b.openid = m.openid and b.uniacid = m.uniacid and m.aagentlevel=' . $level . ' and m.aagenttype=1 and b.billid=' . $id . ' and b.uniacid=' . $_W['uniacid'];
			pdo_query($sql);
		}
		$conditions = array();
		if ($paymoney2 != '') 
		{
			$conditions[] = 'b.paymoney2 = ' . $paymoney2;
		}
		if ($paymoney3 != '') 
		{
			$conditions[] = 'b.paymoney3 = ' . $paymoney3;
		}
		if (!(empty($conditions))) 
		{
			$sql = 'update ' . tablename('ewei_shop_groupaward_billp') . ' b ,' . tablename('ewei_shop_member') . ' m set  ' . implode(',', $conditions) . '  where b.openid = m.openid and b.uniacid = m.uniacid and m.aagentlevel=' . $level . ' and m.aagenttype=2 and b.billid=' . $id . ' and b.uniacid=' . $_W['uniacid'];
			pdo_query($sql);
		}
		$conditions = array();
		if ($paymoney3 != '') 
		{
			$conditions[] = 'b.paymoney3 = ' . $paymoney3;
		}
		if (!(empty($conditions))) 
		{
			$sql = 'update ' . tablename('ewei_shop_groupaward_billp') . ' b ,' . tablename('ewei_shop_member') . ' m set  ' . implode(',', $conditions) . '  where b.openid = m.openid and b.uniacid = m.uniacid and m.aagentlevel=' . $level . ' and b.billid=' . $id . ' and m.aagenttype=3 and b.uniacid=' . $_W['uniacid'];
			pdo_query($sql);
		}
		$totalmoney = pdo_fetch('select sum(paymoney1) as paymoney1, sum(paymoney2) as paymoney2,sum(paymoney3) as paymoney3 from ' . tablename('ewei_shop_groupaward_billp') . ' where billid=:billid and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':billid' => $id));
		$ret = array('bonusmoney_pay1' => $totalmoney['paymoney1'], 'bonusmoney_pay2' => $totalmoney['paymoney2'], 'bonusmoney_pay3' => $totalmoney['paymoney3']);
		pdo_update('ewei_shop_groupaward_bill', $ret, array('id' => $id, 'uniacid' => $_W['uniacid']));
		show_json(1, $ret);
	}
	public function paymoney_aagent() 
	{
		global $_W;
		global $_GPC;
		$value = floatval($_GPC['value']);
		if ($value < 0) 
		{
			show_json(0, '参数错误!');
		}
		$paymoneytype = intval($_GPC['paymoneytype']);
		if (($paymoneytype <= 0) || (3 < $paymoneytype)) 
		{
			show_json(0, '参数错误!');
		}
		$id = intval($_GPC['id']);
		$data = pdo_fetch('select * from ' . tablename('ewei_shop_groupaward_bill') . ' where id=:id and uniacid=:uniacid limit 1 ', array(':id' => $id, ':uniacid' => $_W['uniacid']));
		if (empty($data)) 
		{
			show_json(0, '结算单未找到!');
		}
		if (!(empty($data['status']))) 
		{
			show_json(0, '结算单已经确认或结算!');
		}
		$baid = intval($_GPC['baid']);
		$partner = pdo_fetch('select *  from ' . tablename('ewei_shop_groupaward_billp') . ' where billid=:billid and id=:id and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':billid' => $id, ':id' => $baid));
		if (empty($partner)) 
		{
			show_json(0, '未找到代理商!');
		}
		pdo_update('ewei_shop_groupaward_billp', array('realProfit' => $value), array('id' => $baid, 'billid' => $id, 'uniacid' => $_W['uniacid']));
		pdo_update('ewei_shop_groupaward_fullback',array('money'=>$value),array('billpid'=>$baid,'billid'=>$id,'uniacid'=>$_W['uniacid']));
		$totalmoney = pdo_fetch('select sum(realProfit) as totalProfit from ' . tablename('ewei_shop_groupaward_billp') . ' where billid=:billid and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':billid' => $id));
		$ret = array('totalProfit' => $totalmoney['totalProfit']);
		pdo_update('ewei_shop_groupaward_bill', $ret, array('id' => $id, 'uniacid' => $_W['uniacid']));
		show_json(1, $ret);
	}
}
?>