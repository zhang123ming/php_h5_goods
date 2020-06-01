<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class QueuePrize_EweiShopV2Page extends PluginWebPage
{
	public function main()
	{
		global $_W;
		global $_GPC;
		global $_S;
		$set = $_S['commission'];
		$condition = '';
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		if($_GPC['keyword']){
			$condition .= " and (position(:keyword in m.nickname) or position(:keyword in m.mobile) )";
		}
		$total = pdo_fetchcolumn('select count(*) from '.tablename('ewei_shop_member').' as m,'.tablename('ewei_shop_member_level').' as ml where m.uniacid=:uniacid and ml.uniacid = :uniacid and m.level=ml.id and ml.level>=94 '.$condition,array(':uniacid'=>$_W['uniacid'],':keyword'=>$_GPC['keyword']));
		$list = pdo_fetchall('select m.id,m.openid,m.nickname,m.mobile,m.avatar,m.queue,m.useamount,ml.level,ml.levelname,ml.ordermoney from '.tablename('ewei_shop_member').' as m,'.tablename('ewei_shop_member_level').' as ml where m.uniacid=:uniacid and ml.uniacid = :uniacid and m.level=ml.id and ml.level>=94 '.$condition.' order by m.queue desc limit '.(($pindex - 1) * $psize).','.$psize,array(':uniacid'=>$_W['uniacid'],':keyword'=>$_GPC['keyword']));
		$num = 0;
		foreach($list as $k=>$v){
			$isset = pdo_fetch('select count(*) as count from'.tablename('ewei_shop_order').' where price>=10000 and uniacid=:uniacid and openid=:openid and status>=2',array(':uniacid'=>$_W['uniacid'],':openid'=>$v['openid']))['count'];
			if(!$isset>0){
				$num++;
				unset($list[$k]);
			}
		}
		$total = $total-$num;
		$pager = pagination2($total, $pindex, $psize);
		include $this->template();
	}

	public function add()
	{
		$this->post();
	}

	public function edit()
	{
		$this->post();
	}

	protected function post()
	{
		global $_W;
		global $_GPC;
		global $_S;
		$set = $_S['commission'];
		$leveltype = $set['leveltype'];
		$id = trim($_GPC['id']);

		if ($id == 'default') {
			$level = array('id' => 'default', 'levelname' => empty($set['levelname']) ? '默认等级' : $set['levelname'], 'commission1' => $set['commission1'], 'commission2' => $set['commission2'], 'commission3' => $set['commission3'], 'commission11' => $set['commission11'], 'commission22' => $set['commission22'], 'commission33' => $set['commission33']);
		}
		else {
			$level = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_commission_level') . ' WHERE id=:id and uniacid=:uniacid limit 1', array(':id' => intval($id), ':uniacid' => $_W['uniacid']));
		}

		if ($_W['ispost']) {
			$data = array('uniacid' => $_W['uniacid'], 'levelname' => trim($_GPC['levelname']), 'commission1' => trim(trim($_GPC['commission1']), '%'), 'commission2' => trim(trim($_GPC['commission2']), '%'), 'commission3' => trim(trim($_GPC['commission3']), '%'), 'commissionmoney' => trim($_GPC['commissionmoney'], '%'), 'ordermoney' => $_GPC['ordermoney'], 'ordercount' => intval($_GPC['ordercount']), 'downcount' => intval($_GPC['downcount']), 'commission11' => trim(trim($_GPC['commission11']), '%'), 'commission22' => trim(trim($_GPC['commission22']), '%'), 'commission33' => trim(trim($_GPC['commission33']), '%'),'level'=>$_GPC['level']);

			if (!empty($id)) {
				if ($id == 'default') {
					$updatecontent = '<br/>等级名称: ' . $set['levelname'] . '->' . $data['levelname'] . '<br/>一级佣金比例: ' . $set['commission1'] . '->' . $data['commission1'] . '<br/>二级佣金比例: ' . $set['commission2'] . '->' . $data['commission2'] . '<br/>三级佣金比例: ' . $set['commission3'] . '->' . $data['commission3'];
					$set['levelname'] = $data['levelname'];
					$set['commission1'] = $data['commission1'];
					$set['commission2'] = $data['commission2'];
					$set['commission3'] = $data['commission3'];
					$set['commission11'] = $data['commission11'];
					$set['commission22'] = $data['commission22'];
					$set['commission33'] = $data['commission33'];
					$this->updateSet($set);
					plog('commission.level.edit', '修改分销商默认等级' . $updatecontent);
				}
				else {
					$updatecontent = '<br/>等级名称: ' . $level['levelname'] . '->' . $data['levelname'] . '<br/>一级佣金比例: ' . $level['commission1'] . '->' . $data['commission1'] . '<br/>二级佣金比例: ' . $level['commission2'] . '->' . $data['commission2'] . '<br/>三级佣金比例: ' . $level['commission3'] . '->' . $data['commission3'];
					pdo_update('ewei_shop_commission_level', $data, array('id' => $id, 'uniacid' => $_W['uniacid']));
					plog('commission.level.edit', '修改分销商等级 ID: ' . $id . $updatecontent);
				}
			}
			else {
				pdo_insert('ewei_shop_commission_level', $data);
				$id = pdo_insertid();
				plog('commission.level.add', '添加分销商等级 ID: ' . $id);
			}

			show_json(1, array('url' => webUrl('commission/level')));
		}
		$level_array = array();
		$i = 0;

		while ($i < 101) {
			$level_array[$i] = $i;
			++$i;
		}

		include $this->template();
	}

	public function delete()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		$openid = pdo_fetch('select openid from'.tablename('ewei_shop_member').' wehre uniacid=:uniacid and id=:id',array(':uniacid'=>$_W['uniacid'],':id'=>$id));
		if (empty($id)) {
			$id = (is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0);
		}
		pdo_update('ewei_shop_member', array('level' => 0,'useamount'=>0,'queue'=>0),array('id'=>$id));
		pdo_delete('ewei_shop_commission_queuepaylog',array('openid'=>$openid,'uniacid'=>$_W['uniacid']));
		show_json(1);
	}
}

?>
