<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}
function order_sort($a, $b)
{
	if ($a['createtime'] == $b['createtime']) {
		return 0;
	}

	return $a['createtime'] < $b['createtime'] ? 1 : -1;
}

require EWEI_SHOPV2_PLUGIN . 'commission/core/page_login_mobile.php';
class Order_EweiShopV2Page extends CommissionMobileLoginPage
{
	public function main()
	{
		global $_W;
		global $_GPC;
		$pluginset = m('common')->getPluginset();
		// var_dump($pluginset['subject']['background']);die;
		// $member = $this->model->getInfo($_W['openid'], array('total', 'ordercount0'));
		$member = $this->model->getInfo2($_W['openid']);
		$level = m('member')->getLevel($member['openid']);
		include $this->template();
	}
	public function get_list()
	{
		global $_W,$_GPC;
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		$openid = $_W['openid'];
		$member = $this->model->getInfo2($openid);
		$agentLevel = $this->model->getLevel($openid);
		$level = intval($this->set['level']);
		$set = $this->getSet();
		$status = trim($_GPC['status']);
		$condition = '';
		if ($status !='') {
			$condition .=' and log.issend = '.$status;
		}

		$sql = 'select log.*,o.ordersn,o.openid as buyer_openid from '.tablename('ewei_shop_commission_order_log').' log left join '.tablename('ewei_shop_order').' o on o.id=log.orderid where log.uniacid=:uniacid and log.openid=:openid '.$condition.' order by log.createtime desc limit '.($pindex - 1) * $psize.','. $psize;
		$params[':uniacid']=$_W['uniacid'];
		$params[':openid'] = $openid;
		$list = pdo_fetchall($sql,$params);
		foreach ($list as &$v) {
			$v['createtime'] = date('Y-m-d H:i:s',$v['createtime']);
			if(!empty($v['sendtime'])){
				$v['sendtime'] = date('Y-m-d H:i:s',$v['sendtime']);
			}

			if($v['issend']==1 && empty($v['sendtime'])){
				$v['sendtime'] = $v['createtime'];
			}
			if($v['issend']==0){
				$v['status'] = '未发放';
			} else if($v['issend']==1){
				$v['status'] = '已发放';
			} else if($v['issend']==-1){
				$v['status'] = '已取消';
				$v['sendtime'] = 0;
			}

			if (!empty($this->set['openorderdetail'])) {
					$goods = pdo_fetchall('SELECT og.id,og.goodsid,g.thumb,og.price,og.total,g.title,og.optionname from ' . tablename('ewei_shop_order_goods') . ' og' . ' left join ' . tablename('ewei_shop_goods') . ' g on g.id=og.goodsid  ' . ' where og.orderid=:orderid and og.nocommission=0 and og.uniacid = :uniacid order by og.createtime  desc ', array(':uniacid' => $_W['uniacid'], ':orderid' => $v['orderid']));
					$goods = set_medias($goods, 'thumb');
					$v['order_goods'] = set_medias($goods, 'thumb');
				}
				if (!empty($this->set['openorderbuyer'])) {
					$v['buyer'] = m('member')->getMember($v['buyer_openid']);
				}
		}
		$total = pdo_fetchcolumn('select count(*) from '.tablename('ewei_shop_commission_order_log').' log where log.uniacid=:uniacid and log.openid=:openid '.$condition,array(':uniacid'=>$_W['uniacid'],':openid'=>$openid));
		show_json(1, array('list' => $list, 'pagesize' => $psize, 'total' => $total,'mode'=>$set['commissionMode']));

	}
	public function get_list1()
	{
		global $_W;
		global $_GPC;
		$openid = $_W['openid'];
		$member = $this->model->getInfo($openid, array('ordercount0'));
		$agentLevel = $this->model->getLevel($openid);
		$level = intval($this->set['level']);
		$set = $this->getSet();
		$status = trim($_GPC['status']);
		
		if($set['commissionsettletype']==1){
			$condition = ' and o.status>=1';
		}else if($set['commissionsettletype']==2){
			$condition = ' and o.status>=2';
		}else if($set['commissionsettletype']==3){
			$condition = ' and o.status>=3';
		}else{
			$condition = ' and o.status>=0';
		}


		if ($status != '') {
			$condition = ' and o.status=' . intval($status);
		}

		$commissionspay = $set['commissionspay'];

		if($commissionspay == 0){
			$status = 0;
		}else if($commissionspay == 1){			
			$status = 1;	
		}else if($commissionspay == 2){
			$status = 2;
		}else if($commissionspay == 3){
			$status = 3;
		}
		
		// if ($_GPC['statuvalue']==1) {
		// 	$start = strtotime(date('Y-m-01 00:00:00'));
		// 	$end = strtotime(date('Y-m-d H:i:s'));
			// $condition =' and o.status>0 and (o.createtime>='.strtotime(date('Y-m-d 00:00:00')).' and o.createtime <'.strtotime(date('Y-m-d H:i:s')).')';
		// 	$condition =' and o.status>0 and (o.createtime>='.$start.' and o.createtime <'.$end.')';

		// }

		$orders = array();
		$level1 = $member['level1'];
		$level2 = $member['level2'];
		$level3 = $member['level3'];
		$ordercount = $member['ordercount0'];
		if (1 <= $level) {
			$level1_memberids = pdo_fetchall('select id from ' . tablename('ewei_shop_member') . ' where uniacid=:uniacid and agentid=:agentid', array(':uniacid' => $_W['uniacid'], ':agentid' => $member['id']), 'id');
			$level1_orders = pdo_fetchall('select commission1,o.id,o.createtime,o.price,og.commissions from ' . tablename('ewei_shop_order_goods') . ' og ' . ' left join  ' . tablename('ewei_shop_order') . ' o on og.orderid=o.id ' . ' where o.uniacid=:uniacid and o.agentid=:agentid ' . $condition . ' and og.status1>=0 and og.nocommission=0', array(':uniacid' => $_W['uniacid'], ':agentid' => $member['id']));

			foreach ($level1_orders as $o) {
				if (empty($o['id'])) {
					continue;
				}

				$commissions = iunserializer($o['commissions']);
				$commission = iunserializer($o['commission1']);

				if (empty($commissions)) {
					$commission_ok = (isset($commission['level' . $agentLevel['id']]) ? $commission['level' . $agentLevel['id']] : (isset($commission['default']) ? $commission['default'] : 0));
				}
				else {
					$commission_ok = (isset($commissions['level1']) ? floatval($commissions['level1']) : 0);
				}

				$hasorder = false;

				foreach ($orders as &$or) {
					if (($or['id'] == $o['id']) && ($or['level'] == 1)) {
						$or['commission'] += $commission_ok;
						$hasorder = true;
						break;
					}
				}

				unset($or);

				if (!$hasorder) {
					$orders[] = array('id' => $o['id'], 'commission' => $commission_ok, 'createtime' => $o['createtime'], 'level' => 1);
				}
			}
		}

		if (2 <= $level) {
			if (0 < $level1) {
				$level2_orders = pdo_fetchall('select commission2 ,o.id,o.createtime,o.price,og.commissions   from ' . tablename('ewei_shop_order_goods') . ' og ' . ' left join  ' . tablename('ewei_shop_order') . ' o on og.orderid=o.id ' . ' where o.uniacid=:uniacid and o.agentid in( ' . implode(',', array_keys($member['level1_agentids'])) . ')  ' . $condition . '  and og.status2>=0 and og.nocommission=0 ', array(':uniacid' => $_W['uniacid']));

				foreach ($level2_orders as $o) {
					if (empty($o['id'])) {
						continue;
					}

					$commissions = iunserializer($o['commissions']);
					$commission = iunserializer($o['commission2']);

					if (empty($commissions)) {
						$commission_ok = (isset($commission['level' . $agentLevel['id']]) ? $commission['level' . $agentLevel['id']] : $commission['default']);
					}
					else {
						$commission_ok = (isset($commissions['level2']) ? floatval($commissions['level2']) : 0);
					}

					$hasorder = false;

					foreach ($orders as &$or) {
						if (($or['id'] == $o['id']) && ($or['level'] == 2)) {
							$or['commission'] += $commission_ok;
							$hasorder = true;
							break;
						}
					}

					unset($or);

					if (!$hasorder) {
						$orders[] = array('id' => $o['id'], 'commission' => $commission_ok, 'createtime' => $o['createtime'], 'level' => 2);
					}
				}
			}
		}

		if (3 <= $level) {
			if (0 < $level2) {
				$level3_orders = pdo_fetchall('select commission3 ,o.id,o.createtime,o.price,og.commissions  from ' . tablename('ewei_shop_order_goods') . ' og ' . ' left join  ' . tablename('ewei_shop_order') . ' o on og.orderid=o.id ' . ' where o.uniacid=:uniacid and o.agentid in( ' . implode(',', array_keys($member['level2_agentids'])) . ')  ' . $condition . ' and og.status3>=0 and og.nocommission=0', array(':uniacid' => $_W['uniacid']));

				foreach ($level3_orders as $o) {
					if (empty($o['id'])) {
						continue;
					}

					$commissions = iunserializer($o['commissions']);
					$commission = iunserializer($o['commission3']);

					if (empty($commissions)) {
						$commission_ok = (isset($commission['level' . $agentLevel['id']]) ? $commission['level' . $agentLevel['id']] : $commission['default']);
					}
					else {
						$commission_ok = (isset($commissions['level3']) ? floatval($commissions['level3']) : 0);
					}

					$hasorder = false;

					foreach ($orders as &$or) {
						if (($or['id'] == $o['id']) && ($or['level'] == 3)) {
							$or['commission'] += $commission_ok;
							$hasorder = true;
							break;
						}
					}

					unset($or);
					if (!$hasorder) {
						$orders[] = array('id' => $o['id'], 'commission' => $commission_ok, 'createtime' => $o['createtime'], 'level' => 3);
					}
				}
			}
		}

		if ($orders) {
			usort($orders, 'order_sort');
		}
		// 代理订单开始
		$mLevel = m('member')->getLevel($openid);//代理等级
		$commissiona_ok =0;
		$agentOrderids = array();
		if ($mLevel['level']>90||($_W['shopset']['merch']['is_member']==1)) {
			$levela_orders = pdo_fetchall('select o.id,o.merchid,o.createtime,o.price,og.commissions ,og.status100,og.status99,og.status98,og.status97,og.status96,og.status95,og.status94,og.status93,og.status92,og.status91 from ' . tablename('ewei_shop_order_goods') . ' og ' . ' left join  ' . tablename('ewei_shop_order') . ' o on og.orderid=o.id ' . ' where o.uniacid=:uniacid and (o.agent100='.$member['id'].' or o.agent99='.$member['id'].' or o.agent98='.$member['id'].' or o.agent97='.$member['id'].' or o.agent96='.$member['id'].' or o.agent95='.$member['id'].' or o.agent94='.$member['id'].' or o.agent93='.$member['id'].' or o.agent92='.$member['id'].' or o.agent91='.$member['id'].')  ' . $condition . ' and og.nocommission=0 order by o.id desc', array(':uniacid' => $_W['uniacid']));


			
			foreach ($levela_orders as $o) {
				if (empty($o['id'])) {
					continue;
				}
				$commissionAgent = iunserializer($o['commissions']);
				$commissiona_ok=0;
				$alevel = 0;
				foreach ($commissionAgent as $cid => $ca) {
					$aid = array_keys($ca);
					$isOk = false;
					if($member['id']==$aid[0]){
						switch ($cid) {
							case 'agent100':
									if ($o['status100']>=0) {
										$isOk = true;
										$alevel=100;
										$commissiona_ok = (isset($ca[$member['id']]) ? $ca[$member['id']] : 0);
									}
								break;
							case 'agent99':
									if ($o['status99']>=0) {
										$isOk = true;
										$alevel=99;
										$commissiona_ok = (isset($ca[$member['id']]) ? $ca[$member['id']] : 0);
									}
								break;
							case 'agent98':
									if ($o['status98']>=0) {
										$isOk = true;
										$alevel=98;
										$commissiona_ok = (isset($ca[$member['id']]) ? $ca[$member['id']] : 0);
									}
								break;
							case 'agent97':
									if ($o['status97']>=0) {
										$isOk = true;
										$alevel=97;
										$commissiona_ok = (isset($ca[$member['id']]) ? $ca[$member['id']] : 0);
									}
								break;
							case 'agent96':
									if ($o['status96']>=0) {
										$isOk=true;	
										$alevel=96;
										$commissiona_ok = (isset($ca[$member['id']]) ? $ca[$member['id']] : 0);
									}
								break;
							case 'agent95':
									if ($o['status95']>=0) {
										$isOk=true;	
										$alevel=95;
										$commissiona_ok = (isset($ca[$member['id']]) ? $ca[$member['id']] : 0);
									}
								break;
							case 'agent94':
									if ($o['status94']>=0) {
										$isOk=true;	
										$alevel=94;
										$commissiona_ok = (isset($ca[$member['id']]) ? $ca[$member['id']] : 0);
									}
								break;
							case 'agent93':
									if ($o['status93']>=0) {
										$isOk=true;	
										$alevel=93;
										$commissiona_ok = (isset($ca[$member['id']]) ? $ca[$member['id']] : 0);
									}
								break;
							case 'agent92':
									if ($o['status92']>=0) {
										$isOk=true;	
										$alevel=92;
										$commissiona_ok = (isset($ca[$member['id']]) ? $ca[$member['id']] : 0);
									}
								break;
							case 'agent91':
									if ($o['status91']>=0) {
										$isOk=true;	
										$alevel=91;
										$commissiona_ok = (isset($ca[$member['id']]) ? $ca[$member['id']] : 0);
									}
								break;
							default:
									$isOk = false;
								break;
						}
					}
					if($isOk) $agentOrderids[] = array('id' => $o['id'], 'commissiona' => $commissiona_ok, 'createtime' => $o['createtime'], 'alevel' => $alevel);
				}
			}

		}
		if ($agentOrderids) {
			usort($agentOrderids, 'order_sort');
		}
		function mergeById(&$a,&$b){
			$c=array();
			foreach($a as $e)	$c[$e['id']]=$e;
			foreach($b as $e){
				if (isset($c[$e['id']])) {
					 $e['commissiona']=$c[$e['id']]['commissiona']+$e['commissiona'];
					 $c[$e['id']]=$e+$c[$e['id']];
				}else{
					$c[$e['id']]=$e;
				}
				
			}
			return $c;
		}
		$orders = mergeById($orders,$agentOrderids);

		usort($orders, 'order_sort');
		
		// 代理订单结束

		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		$orders1 = array_slice($orders, ($pindex - 1) * $psize, $psize);
		$orderids = array();

		foreach ($orders1 as $o) {
			$orderids[$o['id']] = $o;
		}

		$list = array();
		$mLevels = m('member')->getLevels();
		if (!empty($orderids)) {
			$list = pdo_fetchall('select id,ordersn,merchid,openid,createtime,status from ' . tablename('ewei_shop_order') . '  where uniacid =' . $_W['uniacid'] . ' and status>='.$status.' and id in ( ' . implode(',', array_keys($orderids)) . ') order by createtime desc');
			
			foreach ($list as $key=>&$row) {
				if ($_W['shopset']['merch']['is_member']==1 && $row['merchid']>0) {
					$merchmLevels = m('member')->getmerch_Levels($row['merchid']);
					if($merchmLevels){
						$mLevels = $merchmLevels;
						$mlevelname = true;
					}
				}
				$row['commission'] = number_format((double) $orderids[$row['id']]['commission'], 2);
				$row['createtime'] = date('Y-m-d H:i', $row['createtime']);
				$row['commissiona'] = number_format((double) $orderids[$row['id']]['commissiona']+(double) $orderids[$row['id']]['commissiong'], 2);
				$row['alevel'] = $orderids[$row['id']]['alevel'];
				foreach ($mLevels as $mll) {
					if ($mll['level']==$row['alevel']) {
						if ($mlevelname) {
							$row['aname'] = '('.$mll['merchname'].')'.$mll['levelname'];	
						}else{
							$row['aname'] = $mll['levelname'];	
						}
					}
				}
				if ($row['status'] == 0) {
					$row['status'] = '待付款';
				}
				else if ($row['status'] == 1) {
					$row['status'] = '已付款';
				}
				else if ($row['status'] == 2) {
					$row['status'] = '待收货';
				}
				else {
					if ($row['status'] == 3) {
						$row['status'] = '已完成';
					}
				}

				if ($orderids[$row['id']]['level'] == 1) {
					$row['level'] = $this->set['texts']['c1'];
				}
				else if ($orderids[$row['id']]['level'] == 2) {
					$row['level'] = $this->set['texts']['c2'];
				}
				else {
					if ($orderids[$row['id']]['level'] == 3) {
						$row['level'] = $this->set['texts']['c3'];
					}
				}

				if (!empty($this->set['openorderdetail'])) {
					$goods = pdo_fetchall('SELECT og.id,og.goodsid,g.thumb,og.price,og.total,g.title,og.optionname,' . 'og.commission1,og.commission2,og.commission3,og.commissions,' . 'og.status1,og.status2,og.status3,' . 'og.content1,og.content2,og.content3 from ' . tablename('ewei_shop_order_goods') . ' og' . ' left join ' . tablename('ewei_shop_goods') . ' g on g.id=og.goodsid  ' . ' where og.orderid=:orderid and og.nocommission=0 and og.uniacid = :uniacid order by og.createtime  desc ', array(':uniacid' => $_W['uniacid'], ':orderid' => $row['id']));
					$goods = set_medias($goods, 'thumb');
					
					foreach ($goods as &$g) {


						$commissions = iunserializer($g['commissions']);

						if ($orderids[$row['id']]['level'] == 1) {
							$commission = iunserializer($g['commission1']);
							if (empty($commissions)) {
								$g['commission'] = isset($commission['level' . $agentLevel['id']]) ? $commission['level' . $agentLevel['id']] : $commission['default'];
							}
							else {
								$g['commission'] = isset($commissions['level1']) ? floatval($commissions['level1']) : 0;


							}
							
						}
						else if ($orderids[$row['id']]['level'] == 2) {
							$commission = iunserializer($g['commission2']);

							if (empty($commissions)) {
								$g['commission'] = isset($commission['level' . $agentLevel['id']]) ? $commission['level' . $agentLevel['id']] : $commission['default'];
							}
							else {
								$g['commission'] = isset($commissions['level2']) ? floatval($commissions['level2']) : 0;
							}
							
						}
						else {
							if ($orderids[$row['id']]['level'] == 3) {
								$commission = iunserializer($g['commission3']);

								if (empty($commissions)) {
									$g['commission'] = isset($commission['level' . $agentLevel['id']]) ? $commission['level' . $agentLevel['id']] : $commission['default'];
								}
								else {
									$g['commission'] = isset($commissions['level3']) ? floatval($commissions['level3']) : 0;
								}
								
							}
						}
						$g['commissiona'] = isset($commissions['agent'.$mLevel[level]][$member[id]])?floatval($commissions['agent'.$mLevel[level]][$member[id]]):0;
					}
					
					unset($g);
					$row['order_goods'] = set_medias($goods, 'thumb');
				}
				if (!empty($this->set['openorderbuyer'])) {
					$row['buyer'] = m('member')->getMember($row['openid']);
				}
			}

			unset($row);
		}

		show_json(1, array('list' => $list, 'pagesize' => $psize, 'total' => $ordercount,'mode'=>$set['commissionMode']));
	}
}

?>
