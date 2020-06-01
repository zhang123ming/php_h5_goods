<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

require EWEI_SHOPV2_PLUGIN . 'commission/core/page_login_mobile.php';
class Down_EweiShopV2Page extends CommissionMobileLoginPage
{
	public function main()
	{
		global $_W;
		global $_GPC;
		global $_S;
		$member = $this->model->getInfo($_W['openid']);
		$levelcount1 = $member['level1'];
		$levelcount2 = $member['level2'];
		$levelcount3 = $member['level3'];
		$level1 = $level2 = $level3 = 0;
		$level1 = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_member') . ' where agentid=:agentid and uniacid=:uniacid limit 1', array(':agentid' => $member['id'], ':uniacid' => $_W['uniacid']));
		$lev1 = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_member') . ' where agentid=:agentid and uniacid=:uniacid and status=:status and isagent=1 limit 1', array(':agentid' => $member['id'], ':uniacid' => $_W['uniacid'],'status'=>0));
		if ((2 <= $this->set['level']) && (0 < $levelcount1)) {
			$level2 = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_member') . ' where agentid in( ' . implode(',', array_keys($member['level1_agentids'])) . ') and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid']));
			$lev2 = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_member') . ' where agentid in( ' . implode(',', array_keys($member['level1_agentids'])) . ') and uniacid=:uniacid and status=:status and isagent=1 limit 1', array(':uniacid' => $_W['uniacid'],'status' => 0));
		}

		if ((3 <= $this->set['level']) && (0 < $levelcount2)) {
			$level3 = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_member') . ' where agentid in( ' . implode(',', array_keys($member['level2_agentids'])) . ') and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid']));
			$lev3 = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_member') . ' where agentid in( ' . implode(',', array_keys($member['level2_agentids'])) . ') and uniacid=:uniacid and status=:status and isagent=1 limit 1', array(':uniacid' => $_W['uniacid'],':status' => 0));
		}
		$datas = m('common')->getPluginset('commission');

		$total = $level1 + $level2 + $level3;
		$lists= array();
		if ($this->set['pageway']==1) {

			$result = $this->get_list();
			$lists = $result['list'];
			$pager = $result['pager'];
		}
		
		include $this->template();
	}

	public function get_list()
	{
		global $_W;
		global $_GPC;
		global $_S;
		$openid = $_W['openid'];
		$member = $this->model->getInfo($openid);
		$total_level = 0;
		$level = intval($_GPC['level']);
		((3 < $level) || ($level <= 0)) && ($level = 1);
		$condition = '';
		$levelcount1 = $member['level1'];
		$levelcount2 = $member['level2'];
		$levelcount3 = $member['level3'];
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		if ($level == 1) {
			$condition = ' and agentid=' . $member['id'];
			$hasangent = true;
			$total_level = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_member') . ' where agentid=:agentid and uniacid=:uniacid limit 1', array(':agentid' => $member['id'], ':uniacid' => $_W['uniacid']));
		}
		else if ($level == 2) {
			if (empty($levelcount1)) {
				if ($this->set['pageway']==1) {
					$pager = pagination2(0, $pindex, $psize,'',array('before'=>1,'after'=>1));
					return array(
						'list'=>array(),
						'pager'=>$pager
					);
				}
				show_json(1, array(
	'list'     => array(),
	'total'    => 0,
	'pagesize' => $psize
	));
			}

			$condition = ' and agentid in( ' . implode(',', array_keys($member['level1_agentids'])) . ')';
			$hasangent = true;
			$total_level = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_member') . ' where agentid in( ' . implode(',', array_keys($member['level1_agentids'])) . ') and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid']));
		}
		else {
			if ($level == 3) {
				if (empty($levelcount2)) {
					if ($this->set['pageway']==1) {
						$pager = pagination2(0, $pindex, $psize,'',array('before'=>1,'after'=>1));
						return array(
							'list'=>array(),
							'pager'=>$pager
						);
					}
					show_json(1, array(
	'list'     => array(),
	'total'    => 0,
	'pagesize' => $psize
	));
				}

				$condition = ' and agentid in( ' . implode(',', array_keys($member['level2_agentids'])) . ')';
				$hasangent = true;
				$total_level = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_member') . ' where agentid in( ' . implode(',', array_keys($member['level2_agentids'])) . ') and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid']));
			}
		}

		$list = pdo_fetchall('select * from ' . tablename('ewei_shop_member') . ' where uniacid = ' . $_W['uniacid'] . ' ' . $condition . '  ORDER BY isagent desc,id desc limit ' . (($pindex - 1) * $psize) . ',' . $psize);

		foreach ($list as &$row) {
			if ($member['isagent'] && $member['status']) {
				$info = $this->model->getInfo($row['openid'], array('total'));
				$record = m("common")->record($row['openid'],'getsum',array('total'));
				$row['commission_total'] = $info['commission_total']+$info['commissiona_total']+$record['total'];
				$row['agentcount'] = $info['agentcount'];
				$row['agenttime'] = date('Y-m-d H:i', $row['agenttime']);
			}

			$ordercount = pdo_fetchcolumn('select count(id) from ' . tablename('ewei_shop_order') . ' where openid=:openid and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':openid' => $row['openid']));
			$row['ordercount'] = number_format(intval($ordercount), 0);
			$moneycount = pdo_fetchcolumn('select sum(og.realprice) from ' . tablename('ewei_shop_order_goods') . ' og ' . ' left join ' . tablename('ewei_shop_order') . ' o on og.orderid=o.id where o.openid=:openid  and o.status>=1 and o.uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':openid' => $row['openid']));
			$row['moneycount'] = number_format(floatval($moneycount), 2);
			$row['createtime'] = date('Y-m-d H:i', $row['createtime']);
		}

		unset($row);
		if ($this->set['pageway']==1) {
			$pager = pagination2($total_level, $pindex, $psize,'',array('before'=>1,'after'=>1));
			return array(
				'list'=>$list,
				'pager'=>$pager
			);
		}
		
		show_json(1, array('list' => $list, 'total' => $total_level, 'pagesize' => $psize));
	}
}

?>
