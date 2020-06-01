<?php

if (!defined('IN_IA')) {
	exit('Access Denied');
}

require __DIR__ . '/base.php';
class record_EweiShopV2Page extends Base_EweiShopV2Page
{
	public function get_list()
	{
		global $_W;
		global $_GPC;
 		$member = $this->model->getinfo2($_W['openid']);
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		$condition = ' and openid=:openid and uniacid=:uniacid ';
		$params = array(
			':uniacid'=>$_W['uniacid'],
			':openid'=>$_W['openid']
		);
		$list = pdo_fetchall('select * from ' . tablename('ewei_shop_commission_record') . ' where 1 ' . $condition . ' order by createtime desc LIMIT ' . (($pindex - 1) * $psize) . ',' . $psize, $params);
		$total = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_commission_record') . ' where 1 ' . $condition, $params);
		foreach ($list as &$row) {
			$row['createtime'] = date('Y-m-d H:i', $row['createtime']);
		}
		unset($row);
		app_json(array('total' => $total,'record'=>$member['credit3'],'list' => $list, 'pagesize' => $psize, 'commissioncount' => number_format($commissioncount, 2), 'textyuan' => $this->set['texts']['yuan'], 'textcomm' => $this->set['texts']['commission'], 'textcomd' => '佣金明细'));
	}

	public function detail_list()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$openid = $_W['openid'];
		$member = m('member')->getMember($_W['openid']);
		$id = intval($_GPC['id']);
		$pindex = max(1, intval($_GPC['page']));
		$psize = 5;
		$apply = pdo_fetch('select orderids from ' . tablename('ewei_shop_commission_apply') . ' where id=:id and `mid`=:mid and uniacid=:uniacid limit 1', array(':id' => $id, ':mid' => $member['id'], ':uniacid' => $_W['uniacid']));

		if (empty($apply)) {
			show_json(0, array('message' => '未找到提现申请!'));
		}

		$orderids = iunserializer($apply['orderids']);
		if (!is_array($orderids) || (count($orderids) <= 0)) {
			show_json(0, array('message' => '未找到订单信息!'));
		}
		$order1 = array();
		$ordera = array();
		$orderg = array();
		foreach ($orderids as $oo) {
			if (!empty($oo['level'])&&$oo['level']<=3) {
				$order1[] = $oo;
			}
			if ($oo['alevel']>95) {
				$or = $oo;
				$or['alevel'] = $oo['alevel'];
				$ordera[] = $or;
			}
		}
		
		$a = $order1;
		$b = $ordera;
		$d = $orderg;
		$c=array();
		foreach($a as $e)	$c[$e['orderid']]=$e;
		foreach($b as $e)   $c[$e['orderid']]=isset($c[$e['orderid']])? $c[$e['orderid']]+$e : $e;
		foreach($d as $e)   $c[$e['orderid']]=isset($c[$e['orderid']])? $c[$e['orderid']]+$e : $e;	
		$ids = array();
		$orderids = $c;
		foreach ($orderids as $o) {
			$ids[] = $o['orderid'];
		}
		$list = pdo_fetchall('select o.id,o.agentid, o.ordersn,o.price,o.goodsprice, o.dispatchprice,o.createtime, o.paytype from ' . tablename('ewei_shop_order') . ' o ' . ' left join ' . tablename('ewei_shop_member') . ' m on o.openid = m.openid ' . ' where  o.id in ( ' . implode(',', $ids) . ' ) LIMIT ' . (($pindex - 1) * $psize) . ',' . $psize);
		$total = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_order') . ' o ' . ' left join ' . tablename('ewei_shop_member') . ' m on o.openid = m.openid ' . ' where  o.id in ( ' . implode(',', $ids) . ' ) ');
		$totalcommission = 0;
		$totalpay = 0;
		$mLevels = m('member')->getLevels();
		foreach ($list as &$row) {
			$ordercommission = 0;
			$orderpay = 0;

			foreach ($orderids as $o) {
				if ($o['orderid'] == $row['id']) {
					$row['level'] = $o['level'];
					$row['alevel'] = $o['alevel'];
					$row['agent'] = $o['agent'];
					break;
				}
			}
			foreach ($mLevels as $mll) {
				if ($mll['level']==$row['alevel']) {
					$row['aname'] = $mll['levelname'];
				}
			}

			$condition = '';
			$status = trim($_GPC['status']);

			if ($status != '') {
				$condition .= ' and status=' . intval($status);
			}
			$asql = " ,og.status100,og.status99,og.status98,og.status97,og.status96,og.status95,og.status94,og.status93,og.status92,og.status91,og.content100,og.content99,og.content98,og.content97,og.content96,og.content95,og.content94,og.content93,og.content92,og.content91";
			$goods = pdo_fetchall('SELECT og.id,og.goodsid,g.thumb,og.price,og.total,g.title,og.optionname,' . 'og.commission1,og.commission2,og.commission3,og.commissions,' . 'og.status1,og.status2,og.status3,' . 'og.content1,og.content2,og.content3 '.$asql.' from ' . tablename('ewei_shop_order_goods') . ' og' . ' left join ' . tablename('ewei_shop_goods') . ' g on g.id=og.goodsid  ' . ' where og.orderid=:orderid and og.nocommission=0 and og.uniacid = :uniacid order by og.createtime  desc ', array(':uniacid' => $_W['uniacid'], ':orderid' => $row['id']));
			$goods = set_medias($goods, 'thumb');

			foreach ($goods as &$g) {
				$commissions = iunserializer($g['commissions']);

				if ($row['level'] == 1) {
					$commission = iunserializer($g['commission1']);

					if (empty($commissions)) {
						$g['commission'] = isset($commission['level' . $agentLevel['id']]) ? $commission['level' . $agentLevel['id']] : $commission['default'];
					}
					else {
						$g['commission'] = isset($commissions['level1']) ? floatval($commissions['level1']) : 0;
					}

					$totalcommission += $g['commission'];
					$ordercommission += $g['commission'];

					if (2 <= $g['status1']) {
						$totalpay += $g['commission'];
						$orderpay += $g['commission'];
					}
				}

				if ($row['level'] == 2) {
					$commission = iunserializer($g['commission2']);
					$g['commission_pay'] = 0;

					if (empty($commissions)) {
						$g['commission'] = isset($commission['level' . $agentLevel['id']]) ? $commission['level' . $agentLevel['id']] : $commission['default'];
					}
					else {
						$g['commission'] = isset($commissions['level2']) ? floatval($commissions['level2']) : 0;
					}

					$totalcommission += $g['commission'];
					$ordercommission += $g['commission'];

					if (2 <= $g['status2']) {
						$g['commission_pay'] = $g['commission'];
						$totalpay += $g['commission'];
						$orderpay += $g['commission'];
					}
				}

				if ($row['level'] == 3) {
					$commission = iunserializer($g['commission3']);

					if (empty($commissions)) {
						$g['commission'] = isset($commission['level' . $agentLevel['id']]) ? $commission['level' . $agentLevel['id']] : $commission['default'];
					}
					else {
						$g['commission'] = isset($commissions['level3']) ? floatval($commissions['level3']) : 0;
					}

					$totalcommission += $g['commission'];
					$ordercommission += $g['commission'];

					if (2 <= $g['status3']) {
						$totalpay += $g['commission'];
						$orderpay += $g['commission'];
					}
				}

				// 查询代理佣金开始
				$orderAgent = $this->model->getAgents($row['id']);//查分销商
				
				if($this->set['commissionMode']){
					foreach ($commissions as $cid => $ca) {
						$aid = array_keys($ca);
						if ($row['level']==1&&!$row['agent']){
							$row['agent']=$orderAgent[0]['id'];
						} 
						if ($row['level']==2&&!$row['agent']){
							$row['agent']=$orderAgent[1]['id'];
						} 
						if ($row['level']==3&&!$row['agent']){
							$row['agent']=$orderAgent[2]['id'];
						} 
						$ispay=$isok=false;
						if(!empty($aid[0])&&$row['agent']==$aid[0]){
							switch ($cid) {
								case 'agent100':
										$isok=true;
										$g['acommission'] = (isset($ca[$row['agent']]) ? $ca[$row['agent']] : 0);
										$row['alevel'] = substr($cid, 5);
										if($g['status100']>=2) $ispay=true;
									break;
								case 'agent99':
										$isok=true;
										$g['acommission'] = (isset($ca[$row['agent']]) ? $ca[$row['agent']] : 0);
										$row['alevel'] = substr($cid, 5);
										if($g['status99']>=2) $ispay=true;
									break;
								case 'agent98':
										$isok=true;
										$g['acommission'] = (isset($ca[$row['agent']]) ? $ca[$row['agent']] : 0);
										$row['alevel'] = substr($cid, 5);
										if($g['status98']>=2) $ispay=true;
									break;
								case 'agent97':
										$isok=true;
										$g['acommission'] = (isset($ca[$row['agent']]) ? $ca[$row['agent']] : 0);
										$row['alevel'] = substr($cid, 5);
										if($g['status97']>=2) $ispay=true;
									break;
								case 'agent96':
										$isok=true;
										$g['acommission'] = (isset($ca[$row['agent']]) ? $ca[$row['agent']] : 0);
										$row['alevel'] = substr($cid, 5);
										if($g['status96']>=2) $ispay=true;
									break;
								case 'agent95':
										$isok=true;
										$g['acommission'] = (isset($ca[$row['agent']]) ? $ca[$row['agent']] : 0);
										$row['alevel'] = substr($cid, 5);
										if($g['status95']>=2) $ispay=true;
									break;
								case 'agent94':
										$isok=true;
										$g['acommission'] = (isset($ca[$row['agent']]) ? $ca[$row['agent']] : 0);
										$row['alevel'] = substr($cid, 5);
										if($g['status94']>=2) $ispay=true;
									break;
								case 'agent93':
										$isok=true;
										$g['acommission'] = (isset($ca[$row['agent']]) ? $ca[$row['agent']] : 0);
										$row['alevel'] = substr($cid, 5);
										if($g['status93']>=2) $ispay=true;
									break;
								case 'agent92':
										$isok=true;
										$g['acommission'] = (isset($ca[$row['agent']]) ? $ca[$row['agent']] : 0);
										$row['alevel'] = substr($cid, 5);
										if($g['status92']>=2) $ispay=true;
									break;
								case 'agent91':
										$isok=true;
										$g['acommission'] = (isset($ca[$row['agent']]) ? $ca[$row['agent']] : 0);
										$row['alevel'] = substr($cid, 5);
										if($g['status91']>=2) $ispay=true;
									break;
								default:
										$ispay = false;
									break;
							}
						}
						if($isok){
							$totalcommission += (isset($ca[$row['agent']]) ? $ca[$row['agent']] : 0);
							$totalcommission += $g['acommission'];
							$ordercommission += $g['acommission'];
							// $g['commission'] += $g['acommission'];
						}
						if($ispay) {
							$totalpay += (isset($ca[$row['agent']]) ? $ca[$row['agent']] : 0);
							$g['commission_pay'] = $g['acommission'];
							$orderpay += $g['acommission'];
						}
					}

				}
				// 查询代理佣金结束
				$g['level'] = $row['level'];
				if($row['alevel']>95) $g['alevel'] = $row['alevel'];
				$g['aname'] = $row['aname'];
				$status = $g['status' . $row['level']];

				if ($status == 1) {
					$g['statusstr'] = '待审核';
					$g['dealtime'] = date('Y-m-d H:i', $row['applytime' . $row['level']]);
				}
				else if ($status == 2) {
					$g['statusstr'] = '待打款';
					$g['dealtime'] = date('Y-m-d H:i', $row['checktime' . $row['level']]);
				}
				else if ($status == 3) {
					$g['statusstr'] = '已打款';
					$g['dealtime'] = date('Y-m-d H:i', $row['checktime' . $row['level']]);
				}
				else {
					if ($status == -1) {
						$g['dealtime'] = date('Y-m-d H:i', $row['invalidtime' . $row['level']]);
						$g['statusstr'] = '无效';
					}
				}

				$g['status'] = $status;
				$g['content'] = $g['content' . $row['level']];
				$g['level'] = $row['level'];
			
				if ($row['level'] == 1) {
					$g['level'] = '一';
					$g['levelnum'] = 1;
				}
				else if ($row['level'] == 2) {
					$g['level'] = '二';
					$g['levelnum'] = 2;
				}
				else {
					if ($row['level'] == 3) {
						$g['level'] = '三';
						$g['levelnum'] = 3;
					}
				}
			}
			
			unset($g);
			$row['goods'] = $goods;
			$row['ordercommission'] = $ordercommission;
			$row['orderpay'] = $orderpay;
		}

		unset($row);
		app_json(array('list' => $list, 'pagesize' => $psize, 'total' => $total, 'totalcommission' => $totalcommission, 'textyuan' => $this->set['texts']['yuan'], 'textcomm' => $this->set['texts']['commission']));
	}
}

?>
