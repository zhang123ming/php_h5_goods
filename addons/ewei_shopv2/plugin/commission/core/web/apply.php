<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Apply_EweiShopV2Page extends PluginWebPage
{
	public function main()
	{
		global $_W;
		global $_GPC;
		$set = $this->getSet();

		$status = intval($_GPC['status']);
		empty($status) && ($status = 1);

		if ($status == -1) {
			if (!cv('commission.apply.view_1')) {
				$this->message('你没有相应的权限查看');
			}
		}
		else {
			if (!cv('commission.apply.view' . $status)) {
				$this->message('你没有相应的权限查看');
			}
		}

		$apply_type = array('余额', '微信钱包', '支付宝', '银行卡');
		$agentlevels = $this->model->getLevels();
		$level = $this->set['level'];
		$pindex = max(1, intval($_GPC['page']));
		$psize = $_GPC['psize']?:20;
		$condition = ' and a.uniacid=:uniacid and a.status=:status';
		$params = array(':uniacid' => $_W['uniacid'], ':status' => $status);
		$searchfield = strtolower(trim($_GPC['searchfield']));
		$keyword = trim($_GPC['keyword']);
		if (!empty($searchfield) && !empty($keyword)) {
			if ($searchfield == 'applyno') {
				$condition .= ' and a.applyno like :keyword';
			}
			else {
				if ($searchfield == 'member') {
					$condition .= ' and (m.realname like :keyword or m.nickname like :keyword or m.mobile like :keyword)';
				}
			}

			$params[':keyword'] = '%' . $keyword . '%';
		}

		if (empty($starttime) || empty($endtime)) {
			$starttime = strtotime('-1 month');
			$endtime = time();
		}

		$timetype = $_GPC['timetype'];

		if (!empty($_GPC['timetype'])) {
			$starttime = strtotime($_GPC['time']['start']);
			$endtime = strtotime($_GPC['time']['end']);

			if (!empty($timetype)) {
				$condition .= ' AND a.' . $timetype . ' >= :starttime AND a.' . $timetype . '  <= :endtime ';
				$params[':starttime'] = $starttime;
				$params[':endtime'] = $endtime;
			}
		}

		if (!empty($_GPC['agentlevel'])) {
			$condition .= ' and m.agentlevel=' . intval($_GPC['agentlevel']);
		}

		if (3 <= $status) {
			$orderby = 'paytime';
		}
		else if (2 <= $status) {
			$orderby = ' checktime';
		}
		else {
			$orderby = 'applytime';
		}

		$applytitle = '';

		if ($status == 1) {
			$applytitle = '待审核';
		}
		else if ($status == 2) {
			$applytitle = '待打款';
		}
		else if ($status == 3) {
			$applytitle = '已打款';
		}
		else {
			if ($status == -1) {
				$applytitle = '已无效';
			}
		}

		$sql = 'select a.*, m.nickname,m.avatar,m.realname,m.mobile,m.agentlevel,l.levelname,l.levelname,a.realname as applyrealname,a.sendmoney,a.recordid,a.bankcard,a.bankname from ' . tablename('ewei_shop_commission_apply') . ' a ' . ' left join ' . tablename('ewei_shop_member') . ' m on m.id = a.mid' . ' left join ' . tablename('ewei_shop_commission_level') . ' l on l.id = m.agentlevel' . ' where 1 ' . $condition . ' ORDER BY ' . $orderby . ' desc ';

		if (empty($_GPC['export'])) {
			$sql .= '  limit ' . (($pindex - 1) * $psize) . ',' . $psize;
		}

		$list = pdo_fetchall($sql, $params);
		if ($status == 3) {
			$realmoney_total = (double) pdo_fetchcolumn('select sum(a.realmoney) from ' . tablename('ewei_shop_commission_apply') . ' a ' . ' left join ' . tablename('ewei_shop_member') . ' m on m.id = a.mid' . ' left join ' . tablename('ewei_shop_commission_level') . ' l on l.id = m.agentlevel' . ' where 1 ' . $condition, $params);
		}

		foreach ($list as &$row) {
			$row['agentlevel'] = intval($row['agentlevel']);
			$row['levelname'] = empty($row['levelname']) ? (empty($this->set['levelname']) ? '普通等级' : $this->set['levelname']) : $row['levelname'];
			$row['typestr'] = $apply_type[$row['type']];


			// 计算提现总额
			if($row['type']==0){
				$count1+=$row['commission'];
			}else if($row['type']==1){
				$count2+=$row['commission'];
			}else if($row['type']==2){
				$count3+=$row['commission'];	
			}else{
				$count4+=$row['commission'];
			}
		}

		unset($row);


		if ($_GPC['export'] == '1') {
			ca('commission.apply.export');

			if ($status == 1) {
				$statustext = '待审核';
			}
			else if ($status == 2) {
				$statustext = '待打款';
			}
			else if ($status == 3) {
				$statustext = '已打款';
			}
			else {
				if ($status == -1) {
					$statustext = '已无效';
				}
			}

			plog('commission.apply.export', $statustext . '提现申请 导出数据');

			foreach ($list as &$row) {
				$row['applytime'] = (1 <= $status) || ($status == -1) ? date('Y-m-d H:i', $row['applytime']) : '--';
				$row['checktime'] = 2 <= $status ? date('Y-m-d H:i', $row['checktime']) : '--';
				$row['paytime'] = 3 <= $status ? date('Y-m-d H:i', $row['paytime']) : '--';
				$row['invalidtime'] = $status == -1 ? date('Y-m-d H:i', $row['invalidtime']) : '--';
			}

			unset($row);
			$totalcommission = 0;
			$totalpay = 0;
			$rowcount = 0;
			$ordercount = 0;
			$goodscount = 0;
			$lastgoodscount = 0;
			foreach ($list as &$row) {
				if (($status == 2) || ($status == 3)) {
					$charge_flag = 0;
					$set_array = array();
					$set_array['charge'] = $row['charge'];
					$set_array['begin'] = $row['beginmoney'];
					$set_array['end'] = $row['endmoney'];
				}
				$row['bankcard'] = "\t". $row['bankcard'] ."\t";
			}

			unset($row);
			$exportlist = array();
			$i = 0;

			while ($i < $rowcount) {
				$exportlist['row' . $i] = array();
				++$i;
			}
			$rowindex = 0;
			$len = count($list);
			$set =  $this->getSet();
			foreach ($list as $index => $row) {
				$exportlist['row' . $rowindex] = $row;
				$exportlist['row' . $rowindex]['id'] = $row['id'];
				$exportlist['row' . $rowindex]['applyno'] = $row['applyno'];
				$exportlist['row' . $rowindex]['nickname'] = $row['nickname'];
				$exportlist['row' . $rowindex]['realname'] = $row['realname'];
				$exportlist['row' . $rowindex]['mobile'] = $row['mobile'];
				$exportlist['row' . $rowindex]['price'] = $row['commission'];
				$exportlist['row' . $rowindex]['realprice'] = $row['realmoney'];
				$exportlist['row' . $rowindex]['deductionmoney'] = $row['deductionmoney'];
				$exportlist['row' . $rowindex]['charge'] = $row['charge'];
				$exportlist['row' . $rowindex]['area_free'] = $row['beginmoney'].'--'.$row['endmoney'];
				$exportlist['row' . $rowindex]['typestr'] = $row['typestr'];
				$exportlist['row' . $rowindex]['applyrealname'] = $row['applyrealname'];
				$exportlist['row' . $rowindex]['alipay'] = $row['alipay'];
				$exportlist['row' . $rowindex]['bankname'] = $row['bankname'];
				$exportlist['row' . $rowindex]['bankcard'] = "\t".$row['bankcard']."\t";
				$exportlist['row' . $rowindex]['applytime'] = $row['applytime'];
				$exportlist['row' . $rowindex]['checktime'] = $row['checktime'];
				$exportlist['row' . $rowindex]['paytime'] = $row['paytime'];
				$exportlist['row' . $rowindex]['invalidtime'] = $row['invalidtime'];
				$rowindex++;	
			}	
			$columns = array();			
			$columns[] = array('title' => 'ID', 'field' => 'id', 'width' => 12);
			$columns[] = array('title' => '提现单号', 'field' => 'applyno', 'width' => 24);
			$columns[] = array('title' => '粉丝', 'field' => 'nickname', 'width' => 12);
			$columns[] = array('title' => '姓名', 'field' => 'realname', 'width' => 12);
			$columns[] = array('title' => '手机号码', 'field' => 'mobile', 'width' => 12);
			$columns[] = array('title' => '提现金额', 'field' => 'price', 'width' => 24);
			$columns[] = array('title' => '实际到账金额', 'field' => 'realprice', 'width' => 24);
			$columns[] = array('title' => '手续费(元)', 'field' => 'deductionmoney', 'width' => 24);
			$columns[] = array('title' => '手续费%', 'field' => 'charge', 'width' => 12);
			$columns[] = array('title' => '手续费减免区间', 'field' => 'area_free', 'width' => 24);
			if (($status == 2) || ($status == 3)) {
				if ($status == 2) {
					$column_title1 = '应该打款';
					$column_title2 = '实际佣金';
				}
				else {
					$column_title1 = '实际打款';
					$column_title2 = '实际到账';
				}

				$columns[] = array('title' => $column_title1, 'field' => 'passmoney', 'width' => 12);
				$columns[] = array('title' => $column_title2, 'field' => 'realmoney', 'width' => 12);
			}

			$columns[] = array('title' => '提现方式', 'field' => 'typestr', 'width' => 12);
			$columns[] = array('title' => '提现姓名', 'field' => 'applyrealname', 'width' => 24);
			$columns[] = array('title' => '支付宝', 'field' => 'alipay', 'width' => 24);
			$columns[] = array('title' => '银行', 'field' => 'bankname', 'width' => 24);
			$columns[] = array('title' => '银行卡号', 'field' => 'bankcard', 'width' => 24);
			$columns[] = array('title' => '申请时间', 'field' => 'applytime', 'width' => 24);
			$columns[] = array('title' => '审核时间', 'field' => 'checktime', 'width' => 24);
			$columns[] = array('title' => '打款时间', 'field' => 'paytime', 'width' => 24);
			$columns[] = array('title' => '设置无效时间', 'field' => 'invalidtime', 'width' => 24);

			m('excel')->export($exportlist, array('title' => $applytitle . '佣金申请数据-' . date('Y-m-d-H-i', time()), 'columns' => $columns));
		}

		$total = pdo_fetchcolumn('select count(a.id) from' . tablename('ewei_shop_commission_apply') . ' a ' . ' left join ' . tablename('ewei_shop_member') . ' m on m.uid = a.mid' . ' left join ' . tablename('ewei_shop_commission_level') . ' l on l.id = m.agentlevel' . ' where 1 ' . $condition, $params);
		$pager = pagination2($total, $pindex, $psize);
		include $this->template();
	}

	protected function applyData()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		$apply = pdo_fetch('select * from ' . tablename('ewei_shop_commission_apply') . ' where uniacid=:uniacid and id=:id limit 1', array(':uniacid' => $_W['uniacid'], ':id' => $id));
		if (empty($apply)) {
			if ($_W['isajax']) {
				show_json(0, '提现申请不存在!');
			}

			$this->message('提现申请不存在!', '', 'error');
		}
		$status = intval($_GPC['status']);
		empty($status) && ($status = 1);

		if ($apply['status'] == -1) {
			ca('commission.apply.view_1');
		}
		else {
			ca('commission.apply.view' . $apply['status']);
		}

		$agentid = $apply['mid'];
		$member = $this->model->getInfo($agentid, array('total', 'ok', 'apply', 'lock', 'check'));
		$record = m("common")->record($agentid,'getsum',array('total'))['total'];
		$member['record_total'] = $record;
		$hasagent = 0 < $member['agentcount'];
		$agentLevel = $this->model->getLevel($apply['mid']);

		if (empty($agentLevel['id'])) {
			$agentLevel = array('levelname' => empty($this->set['levelname']) ? '普通等级' : $this->set['levelname'], 'commission1' => $this->set['commission1'], 'commission2' => $this->set['commission2'], 'commission3' => $this->set['commission3']);
		}
		$memberSetting = m('member')->getLevels();
		$memberSetting = array_reverse($memberSetting);
		$memberLevel = array();

		foreach ($memberSetting as $mk => $mv) {
			if ($mv['level']>95) {
				$memberLevel[]=$mv;
			}
		}
		$orderids = iunserializer($apply['orderids']);
		if ((!is_array($orderids) || (count($orderids) <= 0))&&(strlen($apply['recordid'])<=0||$apply['recordamount']<=0)) {
			$this->message('无任何订单，无法查看!', '', 'error');
		}
		$temp = array();
		$counts = count($orderids);;
		$applyOrder = array();
		foreach($orderids as $item){
		    extract($item);
		    $key = $orderid;
		    if(!isset($applyOrder[$key])){
		        $applyOrder[$key] = $item;
		    }else{ //合并相同订单号
		        if($orderid!=$applyOrder[$key]['orderid']){
		            $applyOrder[$key]= $item;
		        }else{
		        	$applyOrder[$key] = $applyOrder[$key]+$item;
		        }
		    }
		}
		// 提取数组
		
		$orderids = $applyOrder;
		$ids = array();

		foreach ($orderids as $o) {
			$ids[] = $o['orderid'];
		}
		$list = array();
		$list = pdo_fetchall('select id,agentid,agent100,agent99,agent98,agent97,agent96,agent95,agent94,agent93,agent92,agent91, ordersn,price,goodsprice, dispatchprice,createtime, paytype from ' . tablename('ewei_shop_order') . ' where  id in ( ' . implode(',', $ids) . ' );');
		$apply['recordid'] = rtrim($apply['recordid'],",");
		$recordTotal = m("common")->record($member['openid'],'getsum',array('total'))['total'];
		$arr = pdo_fetchall("select remark,createtime,amount,content,status,id from".tablename("ewei_shop_commission_record")."where id in (".$apply['recordid'].")");
		
		switch($_GPC['status']){
			case '1':
				$member['record_apply'] = pdo_fetch("select sum(amount) as total from".tablename("ewei_shop_commission_record")."where id in (".$apply['recordid'].") and status = :status",array(":status"=>$_GPC['status']))['total'];
				break;
			case '2':
				$member['record_check'] = pdo_fetch("select sum(amount) as total from".tablename("ewei_shop_commission_record")."where id in (".$apply['recordid'].") and status = :status",array(":status"=>$_GPC['status']))['total'];
				break;
			case '3':
				$member['record_check'] = pdo_fetch("select sum(amount) as total from".tablename("ewei_shop_commission_record")."where id in (".$apply['recordid'].") and status = :status",array(":status"=>$_GPC['status']))['total'];
				break;
		}
		unset($item);
		$totalcommission = 0;
		$totalpay = 0;
		foreach ($list as &$row) {
			foreach ($orderids as $o) {
				if ($o['orderid'] == $row['id']) {
					$row['level'] = $o['level'];
					$row['alevel'] = $o['alevel'];
					$row['agent'] = $o['agent'];
					break;
				}
			}
			$asql = " ,og.status100,og.status99,og.status98,og.status97,og.status96,og.status95,og.status94,og.status93,og.status92,og.status91,og.content100,og.content99,og.content98,og.content97,og.content96";
			$goods = pdo_fetchall('SELECT og.id,g.thumb,og.price,og.realprice, og.total,g.title,o.paytype,og.optionname,og.commission1,og.commission2,og.commission3,og.commissions,og.status1,og.status2,og.status3,og.content1,og.content2,og.content3'.$asql.' from ' . tablename('ewei_shop_order_goods') . ' og' . ' left join ' . tablename('ewei_shop_goods') . ' g on g.id=og.goodsid  ' . ' left join ' . tablename('ewei_shop_order') . ' o on o.id=og.orderid  ' . ' where og.uniacid = :uniacid and og.orderid=:orderid and og.nocommission=0 order by og.createtime  desc ', array(':uniacid' => $_W['uniacid'], ':orderid' => $row['id']));

			foreach ($goods as &$g) {
				$commissions = iunserializer($g['commissions']);
				
				if (1 <= $this->set['level']) {
					$commission = iunserializer($g['commission1']);

					if (empty($commissions)) {
						$g['commission1'] = isset($commission['level' . $agentLevel['id']]) ? $commission['level' . $agentLevel['id']] : $commission['default'];
					}
					else {
						$g['commission1'] = isset($commissions['level1']) ? floatval($commissions['level1']) : 0;
					}

					if ($row['level'] == 1) {
						$totalcommission += $g['commission1'];

						if (2 <= $g['status1']) {
							$totalpay += $g['commission1'];
						}
					}
				}

				if (2 <= $this->set['level']) {
					$commission = iunserializer($g['commission2']);

					if (empty($commissions)) {
						$g['commission2'] = isset($commission['level' . $agentLevel['id']]) ? $commission['level' . $agentLevel['id']] : $commission['default'];
					}
					else {
						$g['commission2'] = isset($commissions['level2']) ? floatval($commissions['level2']) : 0;
					}

					if ($row['level'] == 2) {
						$totalcommission += $g['commission2'];

						if (2 <= $g['status2']) {
							$totalpay += $g['commission2'];
						}
					}
				}

				if (3 <= $this->set['level']) {
					$commission = iunserializer($g['commission3']);

					if (empty($commissions)) {
						$g['commission3'] = isset($commission['level' . $agentLevel['id']]) ? $commission['level' . $agentLevel['id']] : $commission['default'];
					}
					else {
						$g['commission3'] = isset($commissions['level3']) ? floatval($commissions['level3']) : 0;
					}

					if ($row['level'] == 3) {
						$totalcommission += $g['commission3'];

						if (2 <= $g['status3']) {
							$totalpay += $g['commission3'];
						}
					}
				}
			
				if($this->set['commissionMode']){
					foreach ($commissions as $cid => $ca) {
						$aid = array_keys($ca);
						$ispay=$isok=false;
						if(!empty($aid[0])&&$row['agent']==$aid[0]){
							switch ($cid) {
								case 'agent100':
										$isok=true;
										$g['acommission'] = (isset($ca[$row['agent']]) ? $ca[$row['agent']] : 0);
										if($g['status100']>=2) $ispay=true;
									break;
								case 'agent99':
										$isok=true;
										$g['acommission'] = (isset($ca[$row['agent']]) ? $ca[$row['agent']] : 0);
										if($g['status99']>=2) $ispay=true;
									break;
								case 'agent98':
										$isok=true;
										$g['acommission'] = (isset($ca[$row['agent']]) ? $ca[$row['agent']] : 0);
										if($g['status98']>=2) $ispay=true;
									break;
								case 'agent97':
										$isok=true;
										$g['acommission'] = (isset($ca[$row['agent']]) ? $ca[$row['agent']] : 0);
										if($g['status97']>=2) $ispay=true;
									break;
								case 'agent96':
										$isok=true;
										$g['acommission'] = (isset($ca[$row['agent']]) ? $ca[$row['agent']] : 0);
										if($g['status96']>=2) $ispay=true;
									break;
								case 'agent95':
										$isok=true;
										$g['acommission'] = (isset($ca[$row['agent']]) ? $ca[$row['agent']] : 0);
										if($g['status95']>=2) $ispay=true;
									break;
								case 'agent94':
										$isok=true;
										$g['acommission'] = (isset($ca[$row['agent']]) ? $ca[$row['agent']] : 0);
										if($g['status94']>=2) $ispay=true;
									break;
								case 'agent93':
										$isok=true;
										$g['acommission'] = (isset($ca[$row['agent']]) ? $ca[$row['agent']] : 0);
										if($g['status93']>=2) $ispay=true;
									break;
								case 'agent92':
										$isok=true;
										$g['acommission'] = (isset($ca[$row['agent']]) ? $ca[$row['agent']] : 0);
										if($g['status92']>=2) $ispay=true;
									break;
								case 'agent91':
										$isok=true;
										$g['acommission'] = (isset($ca[$row['agent']]) ? $ca[$row['agent']] : 0);
										if($g['status91']>=2) $ispay=true;
									break;
								default:
										$ispay = false;
									break;
							}
						}
						if($isok) $totalcommission += (isset($ca[$row['agent']]) ? $ca[$row['agent']] : 0);
						
						if($ispay) $totalpay += (isset($ca[$row['agent']]) ? $ca[$row['agent']] : 0);
					}
					
				}
				// 查询代理佣金结束
				$g['level'] = $row['level'];
				if($row['alevel']>90) $g['alevel'] = $row['alevel'];
			}
			unset($g);
			$row['goods'] = $goods;
			$totalmoney += $row['price'];
		}

		unset($row);
		$totalcount = $total = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_order') . ' o ' . ' left join ' . tablename('ewei_shop_member') . ' m on o.openid = m.openid ' . ' left join ' . tablename('ewei_shop_member_address') . ' a on a.id = o.addressid ' . ' where o.id in ( ' . implode(',', $ids) . ' );');
		$set_array = array();
		$set_array['charge'] = $apply['charge'];
		$set_array['begin'] = $apply['beginmoney'];
		$set_array['end'] = $apply['endmoney'];
		$totalcommission += $apply['recordamount']; 
		$totalpay += $member['record_check'];
		$realmoney = $totalpay;
		$deductionmoney = 0;

		if (!empty($set_array['charge'])) {
			$money_array = m('member')->getCalculateMoney($totalpay, $set_array);

			if ($money_array['flag']) {
				$realmoney = $money_array['realmoney'];
				$deductionmoney = $money_array['deductionmoney'];
			}
		}


	
		$apply_type = array('余额', '微信钱包', '支付宝', '银行卡');
		return array('id' => $id, 'status' => $status, 'apply' => $apply,'arr'=>$arr,'list' => $list, 'totalcount' => $totalcount, 'totalmoney' => $totalmoney, 'member' => $member, 'totalpay' => $totalpay, 'totalcommission' => $totalcommission, 'realmoney' => $realmoney, 'deductionmoney' => $deductionmoney, 'charge' => $set_array['charge'], 'agentLevel' => $agentLevel, 'set_array' => $set_array, 'apply_type' => $apply_type,'memberLevel'=>$memberLevel);
	}

	public function detail()
	{
		global $_W;
		global $_GPC;
		$applyData = $this->applyData();
		extract($applyData);
		include $this->template();
	}

	public function check()
	{
		global $_W;
		global $_GPC;
		$set = $this->getSet();
		
		$id = $_GPC['id'];
		$status = $_GPC['status'];
		$apply = pdo_fetch('select * from ' . tablename('ewei_shop_commission_apply') . ' where uniacid=:uniacid and id=:id limit 1', array(':uniacid' => $_W['uniacid'], ':id' => $id));
		if (empty($apply)) {
			if ($_W['isajax']) {
				show_json(0, '提现申请不存在!');
			}

			$this->message('提现申请不存在!', '', 'error');
		}
		$time = time();
		$totalpay = $apply['commission'];

		pdo_update('ewei_shop_commission_apply', array('status' => 2, 'checktime' => $time), array('id' => $id, 'uniacid' => $_W['uniacid']));
		$rmoney = $apply['commission'];
		$dmoney = $apply['deductionmoney'];
		
		$mcommission = $paycommission;

		if (!empty($dmoney)) {
			$mcommission .= ',实际到账金额:' . $rmoney . ',提现手续费金额:' . $dmoney;
		}

		$this->model->sendMessage($member['openid'], array('commission' => $mcommission, 'type' => $apply_type[$apply['type']]), TM_COMMISSION_CHECK);

		plog('commission.apply.check', '佣金审核 ID: ' . $id . ' 申请编号: ' . $apply['applyno'] . ' 总佣金: ' . $totalmoney . ' 审核通过佣金: ' . $paycommission . ' ');
		show_json(1, array('url' => webUrl('commission/apply', array('status' => $apply['status']))));
	}

	public function cancel()
	{
		global $_W;
		global $_GPC;
		$set = $this->getSet();
		
		$id = $_GPC['id'];
		$status = $_GPC['status'];
		$apply = pdo_fetch('select * from ' . tablename('ewei_shop_commission_apply') . ' where uniacid=:uniacid and id=:id limit 1', array(':uniacid' => $_W['uniacid'], ':id' => $id));
		if (empty($apply)) {
			if ($_W['isajax']) {
				show_json(0, '提现申请不存在!');
			}

			$this->message('提现申请不存在!', '', 'error');
		}
		$time = time();
		
		pdo_update('ewei_shop_commission_apply', array('status' => 1, 'checktime' => 0, 'invalidtime' => 0), array('id' => $id, 'uniacid' => $_W['uniacid']));

		plog('commission.apply.cancel', '重新审核申请 ID: ' . $id . ' 申请编号: ' . $apply['applyno'] . ' ');
		show_json(1, array('url' => webUrl('commission/apply', array('status' => 1))));
	}

	public function refuse()
	{
		global $_W;
		global $_GPC;
		$set = $this->getSet();
		
		$id = $_GPC['id'];
		$status = $_GPC['status'];
		$apply = pdo_fetch('select * from ' . tablename('ewei_shop_commission_apply') . ' where uniacid=:uniacid and id=:id limit 1', array(':uniacid' => $_W['uniacid'], ':id' => $id));
		if (empty($apply)) {
			if ($_W['isajax']) {
				show_json(0, '提现申请不存在!');
			}

			$this->message('提现申请不存在!', '', 'error');
		}
		$time = time();
		/*驳回申请返回给该用户开始*/
		$member = m('member') -> getMember($apply['mid']);
		if(!empty($id)){
			$log['uniacid'] = $apply['uniacid'];
			$log['money'] =$apply['commission'];
			$log['openid'] = $member['openid'];
			$log['remark'] = '驳回提现申请'. $apply['commission'];
			m('member')->setCredit3($log);
		}
		/*驳回申请返回给该用户结束*/

		pdo_update('ewei_shop_commission_apply', array('status' => -2, 'checktime' => 0, 'invalidtime' => 0, 'refusetime' => time()), array('id' => $id, 'uniacid' => $_W['uniacid']));
		
		plog('commission.apply.refuse', '驳回申请 ID: ' . $id . ' 申请编号: ' . $apply['applyno'] . ' ');
		show_json(1, array('url' => webUrl('commission/apply', array('status' => 0))));
	}

	public function pay($params = array(), $mine = array())
	{
		global $_W;
		global $_GPC;
		$set = $this->getSet();
		
		
		$id = $_GPC['id'];
		$status = $_GPC['status'];
		$apply = pdo_fetch('select * from ' . tablename('ewei_shop_commission_apply') . ' where uniacid=:uniacid and id=:id limit 1', array(':uniacid' => $_W['uniacid'], ':id' => $id));
		$member = m('member')->getMember($apply['mid']);			
		if (empty($apply)) {
			if ($_W['isajax']) {
				show_json(0, '提现申请不存在!');
			}

			$this->message('提现申请不存在!', '', 'error');
		}
		$time = time();
		$realmoney = $apply['realmoney'];
		$charge = $apply['charge'];
		$deductionmoney = $apply['deductionmoney'];
		$totalpay = $apply['commission'];
		$totalcommission = $apply['commission'];
		
		if ($apply['status'] != 2) {
			show_json(0, '此申请不能打款!');
		}
		$time = time();
		$pay = round($realmoney, 2);


		if ($apply['type'] < 2) {
			if ($apply['type'] == 1) {
				$pay *= 100;
			}

			$data = m('common')->getSysset('pay');
			if (!empty($data['paytype']['commission']) && ($apply['type'] == 1)) {
				$result = m('finance')->payRedPack($member['openid'], $pay, $apply['applyno'], $apply, $set['texts']['commission'] . '打款', $data['paytype']);
				pdo_update('ewei_shop_commission_apply', array('sendmoney' => $result['sendmoney'], 'senddata' => json_encode($result['senddata'])), array('id' => $apply['id']));

				if ($result['sendmoney'] == $realmoney) {
					$result = true;
				}
				else {
					$result = $result['error'];
				}
			}
			else {
				$result = m('finance')->pay($member['openid'], $apply['type'], $pay, $apply['applyno'], $set['texts']['commission'] . '打款');
			}

			if (is_error($result)) {
				show_json(0, $result['message']);
			}
		}
		//新提现方法
		if ($apply['type'] == 2) {
			$sec = m('common')->getSec();
			$sec = iunserializer($sec['sec']);
			if(empty($sec['alipay_pay']['appid']||empty($sec['alipay_pay']['appprikey'])||empty($sec['alipay_pay']['public_key']))){
				show_json(0,"请配置好支付参数");
			}
			if (!empty($set['texts']['commission'])) {
				$batch_no = 'D' . date('Ymd') . 'CP' . $apply['id'] . 'MONEY' . $batch_no_money;
				require_once EWEI_SHOPV2_PLUGIN."api/core/mobile/alipayconfig/alipay.php";
				$data = array(
					'out_biz_no'=>$batch_no,
					'payee_account'=>$apply['alipay'],
					'amount'=>$pay,
					'remark'=>'佣金提现'
				);
				$arr = new alipay;
				$res = $arr->ali($data,$sec['alipay_pay']);
				$resfail = $res['alipay_fund_trans_toaccount_transfer_response']['sub_msg'];
				if($res['alipay_fund_trans_toaccount_transfer_response']['code']!='10000'){
					show_json(0,$resfail);	
				}
			}
		}

		 
		pdo_update('ewei_shop_commission_apply', array('status' => 3, 'paytime' => $time, 'commission_pay' => $totalpay, 'realmoney' => $realmoney, 'deductionmoney' => $deductionmoney), array('id' => $id, 'uniacid' => $_W['uniacid']));
		$log = array('uniacid' => $_W['uniacid'], 'applyid' => $apply['id'], 'mid' => $member['id'], 'commission' => $totalcommission, 'commission_pay' => $totalpay, 'realmoney' => $realmoney, 'deductionmoney' => $deductionmoney, 'charge' => $charge, 'createtime' => $time, 'type' => $apply['type']);
		pdo_insert('ewei_shop_commission_log', $log);
		$mcommission = $totalpay;

		if (!empty($deductionmoney)) {
			$mcommission .= ',实际到账金额:' . $realmoney . ',提现手续费金额:' . $deductionmoney;
		}

		$this->model->sendMessage($member['openid'], array('commission' => $mcommission, 'type' => $apply_type[$apply['type']]), TM_COMMISSION_PAY);
		$this->model->upgradeLevelByCommissionOK($member['openid']);

		if (p('globous')) {
			p('globous')->upgradeLevelByCommissionOK($member['openid']);
		}

		plog('commission.apply.pay', '佣金打款 ID: ' . $id . ' 申请编号: ' . $apply['applyno'] . ' 打款方式: ' . $apply_type[$apply['type']] . ' 总佣金: ' . $totalcommission . ' 审核通过佣金: ' . $totalpay . ' 实际到账金额: ' . $realmoney . ' 提现手续费金额: ' . $deductionmoney . ' 提现手续费税率: ' . $charge . '%');
		show_json(1, array('url' => webUrl('commission/apply', array('status' => $apply['status']))));
	}

	public function payed($params = array(), $mine = array())
	{
		global $_W;
		global $_GPC;
		$set = $this->getSet();
		$apply_type = array('余额', '微信钱包', '支付宝', '银行卡');
				
		$id = $_GPC['id'];
		$status = $_GPC['status'];
		$apply = pdo_fetch('select * from ' . tablename('ewei_shop_commission_apply') . ' where uniacid=:uniacid and id=:id limit 1', array(':uniacid' => $_W['uniacid'], ':id' => $id));
		$member = m('member')->getMember($apply['mid']);
		if (empty($apply)) {
			if ($_W['isajax']) {
				show_json(0, '提现申请不存在!');
			}

			$this->message('提现申请不存在!', '', 'error');
		}
		$time = time();
		$realmoney = $apply['realmoney'];
		$charge = $apply['charge'];
		$deductionmoney = $apply['deductionmoney'];
		$totalpay = $apply['commission'];
		$totalcommission = $apply['commission'];
		
		

		pdo_update('ewei_shop_commission_apply', array('status' => 3, 'paytime' => $time, 'commission_pay' => $totalpay, 'realmoney' => $realmoney, 'deductionmoney' => $deductionmoney), array('id' => $id, 'uniacid' => $_W['uniacid']));
	
		$log = array('uniacid' => $_W['uniacid'], 'applyid' => $apply['id'], 'mid' => $member['id'], 'commission' => $totalcommission, 'commission_pay' => $totalpay, 'realmoney' => $realmoney, 'deductionmoney' => $deductionmoney, 'charge' => $charge, 'createtime' => $time, 'type' => $apply['type']);
		pdo_insert('ewei_shop_commission_log', $log);
		$mcommission = $realmoney;

		if (!empty($deductionmoney)) {
			$mcommission .= ',实际到账金额:' . $realmoney . ',提现手续费金额:' . $deductionmoney;
		}

		$this->model->upgradeLevelByCommissionOK($member['openid']);
		$this->model->sendMessage($member['openid'], array('commission' => $mcommission, 'type' => $apply_type[$apply['type']]), TM_COMMISSION_PAY);
		if (p('globous')) {
			p('globous')->upgradeLevelByCommissionOK($member['openid']);
		}

		plog('commission.apply.pay', '佣金打款 ID: ' . $id . ' 申请编号: ' . $apply['applyno'] . ' 打款方式: 已经手动打款 总佣金: ' . $totalcommission . ' 审核通过佣金: ' . $totalpay . ' 实际到账金额: ' . $realmoney . ' 提现手续费金额: ' . $deductionmoney . ' 提现手续费税率: ' . $charge . '%');
		show_json(1, array('url' => webUrl('commission/apply', array('status' => $apply['status']))));
	}

	public function changecommission()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		$set = $this->set;
		$order = pdo_fetch('select * from ' . tablename('ewei_shop_order') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $id, ':uniacid' => $_W['uniacid']));

		if (empty($order)) {
			if ($_W['ispost']) {
				show_json(0, array('message' => '未找到订单!'));
			}

			exit('fail');
		}

		$member = m('member')->getMember($order['openid']);
		$agentid = $order['agentid'];
		$agentLevel = $this->model->getLevel($agentid);//获取分销商等级
		$ogid = intval($_GPC['ogid']);
		$order_goods_change = pdo_fetchall('select og.id,g.merchid,g.title,g.thumb,g.goodssn,og.goodssn as option_goodssn, g.productsn,og.productsn as option_productsn, og.total,og.price,og.optionname as optiontitle, og.realprice,og.oldprice,og.commission1,og.commission2,og.commission3,og.commissions,og.status1,og.status2,og.status3 from ' . tablename('ewei_shop_order_goods') . ' og ' . ' left join ' . tablename('ewei_shop_goods') . ' g on g.id=og.goodsid ' . ' where og.uniacid=:uniacid and og.orderid=:orderid ', array(':uniacid' => $_W['uniacid'], ':orderid' => $id));

		if (empty($order_goods_change)) {
			if ($_W['ispost']) {
				show_json(0, array('message' => '未找到订单商品，无法修改佣金!'));
			}

			exit('fail');
		}

		if ($_W['ispost']) {
			$cm1 = $_GPC['cm1'];
			$cm2 = $_GPC['cm2'];
			$cm3 = $_GPC['cm3'];
			$cm100 = $_GPC['cm100'];//返利信息修改
			$cm99 = $_GPC['cm99'];
			$cm98 = $_GPC['cm98'];
			$cm97 = $_GPC['cm97'];
			$cm96 = $_GPC['cm96'];
			$cm95 = $_GPC['cm95'];
			$cm94 = $_GPC['cm94'];
			$cm93 = $_GPC['cm93'];
			$cm92 = $_GPC['cm92'];
			$cm91 = $_GPC['cm91'];

			if (!is_array($cm1) && !is_array($cm2) && !is_array($cm3)) {
				show_json(0, array('message' => '未找到修改数据!'));
			}
			foreach ($order_goods_change as $og) {
				$cmk100 = isset($cm100)?current(array_keys($cm100[$og['id']])) : 0;
				$cmv100 =isset($cm100)? current(array_values($cm100[$og['id']])) : 0;
				$cmk99 = isset($cm99)?current(array_keys($cm99[$og['id']])) : 0;
				$cmv99 = isset($cm99)?current(array_values($cm99[$og['id']])) : 0;
				$cmk98 = isset($cm98)?current(array_keys($cm98[$og['id']])) : 0;
				$cmv98 = isset($cm98)?current(array_values($cm98[$og['id']])) : 0;
				$cmk97 = isset($cm97)?current(array_keys($cm97[$og['id']])) : 0;
				$cmv97 = isset($cm97)?current(array_values($cm97[$og['id']])) : 0;
				$cmk96 = isset($cm96)?current(array_keys($cm96[$og['id']])) : 0;
				$cmv96 = isset($cm96)?current(array_values($cm96[$og['id']])) : 0;

				$cmk95 = isset($cm95)?current(array_keys($cm95[$og['id']])) : 0;
				$cmv95 =isset($cm95)? current(array_values($cm95[$og['id']])) : 0;
				$cmk94 = isset($cm94)?current(array_keys($cm94[$og['id']])) : 0;
				$cmv94 = isset($cm94)?current(array_values($cm94[$og['id']])) : 0;
				$cmk93 = isset($cm93)?current(array_keys($cm93[$og['id']])) : 0;
				$cmv93 = isset($cm93)?current(array_values($cm93[$og['id']])) : 0;
				$cmk92 = isset($cm92)?current(array_keys($cm92[$og['id']])) : 0;
				$cmv92 = isset($cm92)?current(array_values($cm92[$og['id']])) : 0;
				$cmk91 = isset($cm91)?current(array_keys($cm91[$og['id']])) : 0;
				$cmv91 = isset($cm91)?current(array_values($cm91[$og['id']])) : 0;

				$commissions = iunserializer($og['commissions']);
				$commissions['level1'] = isset($cm1[$og['id']]) ? round($cm1[$og['id']], 2) : $commissions['level1'];
				$commissions['level2'] = isset($cm2[$og['id']]) ? round($cm2[$og['id']], 2) : $commissions['level3'];
				$commissions['level3'] = isset($cm3[$og['id']]) ? round($cm3[$og['id']], 2) : $commissions['level2'];
				$commissions['agent100'][$cmk100] = isset($cmv100) ? round($cmv100, 2) : $commissions['agent100'][$cmk100];
				$commissions['agent99'][$cmk99] = isset($cmv99) ? round($cmv99, 2) : $commissions['agent99'][$cmk99];
				$commissions['agent98'][$cmk98] = isset($cmv98) ? round($cmv98, 2) : $commissions['agent98'][$cmk98];
				$commissions['agent97'][$cmk97] = isset($cmv97) ? round($cmv97, 2) : $commissions['agent97'][$cmk97];
				$commissions['agent96'][$cmk96] = isset($cmv96) ? round($cmv96, 2) : $commissions['agent96'][$cmk96];

				$commissions['agent95'][$cmk95] = isset($cmv95) ? round($cmv95, 2) : $commissions['agent95'][$cmk95];
				$commissions['agent94'][$cmk94] = isset($cmv94) ? round($cmv94, 2) : $commissions['agent94'][$cmk94];
				$commissions['agent93'][$cmk93] = isset($cmv93) ? round($cmv93, 2) : $commissions['agent93'][$cmk93];
				$commissions['agent92'][$cmk92] = isset($cmv92) ? round($cmv92, 2) : $commissions['agent92'][$cmk92];
				$commissions['agent91'][$cmk91] = isset($cmv91) ? round($cmv91, 2) : $commissions['agent91'][$cmk91];
				pdo_update('ewei_shop_order_goods', array('commissions' => iserializer($commissions)), array('id' => $og['id']));
			}

			plog('commission.apply.changecommission', '修改佣金 订单号: ' . $order['ordersn']);
			show_json(1, array('url' => referer()));
		}

		$cm1 = m('member')->getMember($agentid);

		if (!empty($cm1['agentid'])) {
			$cm2 = m('member')->getMember($cm1['agentid']);

			if (!empty($cm2['agentid'])) {
				$cm3 = m('member')->getMember($cm2['agentid']);
			}
		}

		foreach ($order_goods_change as &$og) {
			$commissions = iunserializer($og['commissions']);

			if (1 <= $set['level']) {
				$commission = iunserializer($og['commission1']);

				if (empty($commissions)) {
					$og['c1'] = isset($commission['level' . $agentLevel['id']]) ? $commission['level' . $agentLevel['id']] : $commission['default'];
				}
				else {
					$og['c1'] = isset($commissions['level1']) ? floatval($commissions['level1']) : 0;
				}
			}

			if (2 <= $set['level']) {
				$commission = iunserializer($og['commission2']);

				if (empty($commissions)) {
					$og['c2'] = isset($commission['level' . $agentLevel['id']]) ? $commission['level' . $agentLevel['id']] : $commission['default'];
				}
				else {
					$og['c2'] = isset($commissions['level2']) ? floatval($commissions['level2']) : 0;
				}
			}

			if (3 <= $set['level']) {
				$commission = iunserializer($og['commission3']);

				if (empty($commissions)) {
					$og['c3'] = isset($commission['level' . $agentLevel['id']]) ? $commission['level' . $agentLevel['id']] : $commission['default'];
				}
				else {
					$og['c3'] = isset($commissions['level3']) ? floatval($commissions['level3']) : 0;
				}
			}
			$og['c100'] =$commissions['agent100'];
			$og['c99'] =$commissions['agent99'];
			$og['c98'] =$commissions['agent98'];
			$og['c97'] =$commissions['agent97'];
			$og['c96'] =$commissions['agent96'];
			$og['c95'] =$commissions['agent95'];
			$og['c94'] =$commissions['agent94'];
			$og['c93'] =$commissions['agent93'];
			$og['c92'] =$commissions['agent92'];
			$og['c91'] =$commissions['agent91'];
			$og['co'] = $this->model->getOrderCommissions($id, $og['id']);
		}
		if($_W['shopset']['merch']['is_member']==1 && $og['merchid']>0){
			$merchmLevels = m('member')->getmerch_Levels($og['merchid']);

			if($merchmLevels){
				$mLevels = $merchmLevels;
				$mlevelname = true;
			}else{
				$mLevels = m('member')->getLevels();
			}
		}else{
			$mLevels = m('member')->getLevels();
		}
		foreach ($mLevels as $value) {
			if($mlevelname){
				$mLevels[$value['level']]='('.$value['merchname'].')'.$value['levelname'];
			}else{
				$mLevels[$value['level']]=$value['levelname'];	
			}
		}

		if (!empty($order['agent100'])) $cma100 = m('member')->getMember($order['agent100']);
		if (!empty($order['agent99'])) $cma99 = m('member')->getMember($order['agent99']);
		if (!empty($order['agent98'])) $cma98 = m('member')->getMember($order['agent98']);
		if (!empty($order['agent97'])) $cma97 = m('member')->getMember($order['agent97']);
		if (!empty($order['agent96'])) $cma96 = m('member')->getMember($order['agent96']);
		if (!empty($order['agent95'])) $cma95 = m('member')->getMember($order['agent95']);
		if (!empty($order['agent94'])) $cma94 = m('member')->getMember($order['agent94']);
		if (!empty($order['agent93'])) $cma93 = m('member')->getMember($order['agent93']);
		if (!empty($order['agent92'])) $cma92 = m('member')->getMember($order['agent92']);
		if (!empty($order['agent91'])) $cma91 = m('member')->getMember($order['agent91']);
		unset($og);
		include $this->template();
	}

	public function recorddetails(){
		global $_W;
		global $_GPC;
		$id = !empty($_GPC['id'])?$_GPC['id']:'';
		$list = pdo_fetchall();
		include $this->template();
	}

	public function export(){
		include $this->template();
	}

}

?>
