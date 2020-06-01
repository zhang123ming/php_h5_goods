<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class List_EweiShopV2Page extends WebPage
{
	protected function achievementData($status, $st)
	{
		global $_W;
		global $_GPC;
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;		
		$params = array(':uniacid' => $_W['uniacid']);
		
		if (empty($starttime) || empty($endtime)) {
			$starttime = strtotime('-1 month');
			$endtime = time();
		}

		$params_cu = array();
		if (is_array($_GPC['time'])) {
			$starttime = strtotime($_GPC['time']['start']);
			$endtime = strtotime($_GPC['time']['end']);
			$condition_cu .= ' AND o.finishtime >= :starttime AND o.finishtime <= :endtime ';
			$params_cu[':starttime'] = $starttime;
			$params_cu[':endtime'] = $endtime;
		}else{
			$starttime = '';
			$endtime = '';
		}

		$_GPC['keyword'] = trim($_GPC['keyword']);

		if($_GPC['keyword']){
			$condition .=' and (m.nickname like :keyword or m.mobile like :keyword or m.openid like :keyword)';
			$params[':keyword'] = '%'.$_GPC['keyword'].'%';
		}
		// if ($st == 'main') {
		// 	$st = '';
		// }
		// else {
		// 	$st = '.' . $st;
		// }
		if($_GPC['type']=='salesman'){
			/*业务等级设置20*/
			$membersql = 'select m.id,m.realname,m.avatar,m.nickname,m.realname,m.mobile,m.openid,ml.level,ml.levelname from'.tablename('ewei_shop_member'). 'm' .' left join '.tablename('ewei_shop_member_level'). 'ml'. ' on m.level = ml.id '.' where m.uniacid = :uniacid and ml.level = 20' .$condition;
			$membersql .= ' LIMIT ' . (($pindex - 1) * $psize) . ',' . $psize;
			$member = pdo_fetchall($membersql,$params);
			foreach ($member as $key1 => $m1) {
				$doctor_achievements = 0;

				$member2 = pdo_fetchall('select m2.openid,m2.id,ml2.level from'.tablename('ewei_shop_member').'m2'.' left join'.tablename('ewei_shop_member_level').' ml2 '.' on m2.level=ml2.id '.' where m2.uniacid = '.$_W['uniacid']. ' and ml2.level = 10 and m2.agentid = '.$m1['id']);	
				$member_total = count($member2);
				$customer_achievements = 0;

				foreach ($member2 as $key2 => $m2) {					
					/*推广一级客户*/
					$member3 = pdo_fetchall('select m3.openid,m3.agentid,m3.id,ml3.level from'.tablename('ewei_shop_member').'m3'.' left join'.tablename('ewei_shop_member_level').' ml3 '.' on m3.level=ml3.id '.' where m3.uniacid = '.$_W['uniacid']. ' and m3.level = 0 and m3.agentid = '.$m2['id']);
					$member_total = count($member3);
					foreach ($member3 as $key3 => $m3) {
						/*推广二级客户*/
						$member4 = pdo_fetchall('select m4.openid,ml4.level from'.tablename('ewei_shop_member').'m4'.' left join'.tablename('ewei_shop_member_level').' ml4 '.' on m4.level=ml4.id '.' where m4.uniacid = '.$_W['uniacid']. ' and m4.level = 0 and m4.agentid = '.$m3['id']);
							foreach ($member4 as $key4 => $m4) {

								$achievements_goods2_sql = "select og.price,o.id from " . tablename('ewei_shop_order') . ' o ' . ' left join ' . tablename('ewei_shop_order_goods') . ' og on o.id=og.orderid ' . ' where o.uniacid='.$_W['uniacid'].' and o.openid='."'".$m4['openid']."'".' and o.status >= 3 '.$condition_cu;
								$achievements_goods2 = pdo_fetchall($achievements_goods2_sql,$params_cu);

								foreach ($achievements_goods2 as $ag2) {
									$customer_achievements += $ag2['price'];
								}
							}
						$achievements_goods_sql = "select og.price,o.id from " . tablename('ewei_shop_order') . ' o ' . ' left join ' . tablename('ewei_shop_order_goods') . ' og on o.id=og.orderid ' . ' where o.uniacid='.$_W['uniacid'].' and o.openid='."'".$m3['openid']."'".' and o.status >= 3 '.$condition_cu;
						$achievements_goods = pdo_fetchall($achievements_goods_sql,$params_cu);

						foreach ($achievements_goods as $ag) {
							$customer_achievements += $ag['price'];
						}
					}

					$doctor_achievements += $customer_achievements;
					
				}				
				$member[$key1]['direct_award'] = $doctor_achievements;	
				$member[$key1]['member_total'] = $member_total;
			}
		}else if($_GPC['type']=='doctor'){	
			/*医生等级设置10*/		
			$membersql = 'select m.id,m.realname,m.avatar,m.nickname,m.realname,m.mobile,m.openid,ml.level,ml.levelname from'.tablename('ewei_shop_member').'m'.' left join'.tablename('ewei_shop_member_level').' ml '.' on m.level=ml.id '.' where m.uniacid = :uniacid and ml.level = 10 ' .$condition;
			$membersql .= ' LIMIT ' . (($pindex - 1) * $psize) . ',' . $psize;
			$member = pdo_fetchall($membersql,$params);		
			$customer_achievements = 0;	
			foreach ($member as $key => $md) {				
				/*医生等级设置0*/
				/*推广一级客户*/
				$member_customer1 = pdo_fetchall('select m.openid,m.id,ml.level from'.tablename('ewei_shop_member').'m'.' left join'.tablename('ewei_shop_member_level').' ml '.' on m.level=ml.id '.' where m.uniacid = '.$_W['uniacid']. ' and m.level = 0 and m.agentid = '.$md['id']);
				$member_total = count($member_customer1);
				foreach ($member_customer1 as $key1 => $mc) {
					/*推广二级客户*/
					$member_customer2 = pdo_fetchall('select m4.openid,ml4.level from'.tablename('ewei_shop_member').'m4'.' left join'.tablename('ewei_shop_member_level').' ml4 '.' on m4.level=ml4.id '.' where m4.uniacid = '.$_W['uniacid']. ' and m4.level = 0 and m4.agentid = '.$mc['id']);
					$member_total += count($member_customer2);
							foreach ($member_customer2 as $key2 => $m2) {
								$achievements_goods2_sql = "select og.price,o.id from " . tablename('ewei_shop_order') . ' o ' . ' left join ' . tablename('ewei_shop_order_goods') . ' og on o.id=og.orderid ' . ' where o.uniacid='.$_W['uniacid'].' and o.openid='."'".$m2['openid']."'".' and o.status >= 3 '.$condition_cu;
								$achievements_goods2 = pdo_fetchall($achievements_goods2_sql,$params_cu);
								foreach ($achievements_goods2 as $ag2) {
									$customer_achievements += $ag2['price'];
								}
							}

					$achievements_goods_sql = "select og.price,o.id from " . tablename('ewei_shop_order') . ' o ' . ' left join ' . tablename('ewei_shop_order_goods') . ' og on o.id=og.orderid ' . ' where o.uniacid='.$_W['uniacid'].' and o.openid='."'".$mc['openid']."'".' and o.status >= 3 '.$condition_cu;
					$achievements_goods = pdo_fetchall($achievements_goods_sql,$params_cu);
					foreach ($achievements_goods as $ag) {
						$customer_achievements += $ag['price'];
					}
				}

				$member[$key]['direct_award'] = $customer_achievements;	
				$member[$key]['member_total'] = $member_total;
			}
		}
		
		if ($_GPC['export'] == '1') {
			plog('member.list', '导出会员业绩');

			foreach ($member as &$row) {
				$row['realname'] = str_replace('=', '', $row['realname']);
				$row['direct_award'] = str_replace('=', '', $row['direct_award']);
				$row['member_total'] = str_replace('=', '', $row['member_total']);
			}

			unset($row);
			m('excel')->export($member, array(
				'title'   => '会员业绩-' . date('Y-m-d-H-i', time()),
				'columns' => array(
					array('title' => '昵称', 'field' => 'nickname', 'width' => 12),
					array('title' => '姓名', 'field' => 'realname', 'width' => 12),
					array('title' => '手机号', 'field' => 'mobile', 'width' => 12),
					array('title' => 'openid', 'field' => 'openid', 'width' => 24),
					array('title' => '会员等级', 'field' => 'levelname', 'width' => 12),
					array('title' => '推广人数', 'field' => 'member_total', 'width' => 12),
					array('title' => '业绩', 'field' => 'direct_award', 'width' => 12)
					)
			));		
		}
		include $this->template('member/achievement/list');
	}

	public function main()
	{
		global $_W;
		global $_GPC;		
		$achievementData = $this->achievementData('', 'salesman');
	}

	public function salesman()
	{
		global $_W;
		global $_GPC;
		$achievementData = $this->achievementData(0, 'salesman');
	}

	public function doctor()
	{
		global $_W;
		global $_GPC;
		$achievementData = $this->achievementData(1, 'doctor');
	}
}

?>
