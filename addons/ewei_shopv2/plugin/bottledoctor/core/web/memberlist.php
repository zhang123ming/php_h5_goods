<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Memberlist_EweiShopV2Page extends PluginWebPage
{
	public function main()
	{

		global $_W;
		global $_GPC;
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		$condition = 'uniacid=:uniacid';
		$params = array(':uniacid' => $_W['uniacid']);

		if ($_GPC['communicationgrade'] != '') {
			$condition .= ' and communicationgrade = :communicationgrade';
			$params[':communicationgrade'] = $_GPC['communicationgrade'];
		}

		if (isset($_GPC['keyword'])) {
			$_GPC['keyword'] = trim($_GPC['keyword']);			
			$condition .= ' and (nickname like :keyword)';
			$params[':keyword'] = '%' . $_GPC['keyword'] . '%';
		}

		$list = pdo_fetchall('SELECT * FROM ' . tablename('ewei_shop_member') . " WHERE openid like 'sns_wa_%' and ". $condition .' ORDER BY id desc LIMIT ' . (($pindex - 1) * $psize) . ',' . $psize, $params);
		$total = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_member') . ' where ' . $condition, $params);

		$pager = pagination2($total, $pindex, $psize);

		include $this->template();
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
		$id = trim($_GPC['id']);
		$set = $this->getSet();

		if ($id == 'default') {
			
		}
		else {
			$memberlist = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_member') . ' WHERE id=:id and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':id' => intval($id)));		
		}
		if ($_W['ispost']) {
			$communicationgrade = intval($_GPC['communicationgrade']);
			$data = array('uniacid' => $_W['uniacid'], 'communicationgrade' => trim($_GPC['communicationgrade']));
			
			if (!empty($id)) {
				if ($id == 'default') {
					$set['communicationgrade'] = $data['communicationgrade'];
					$this->updateSet($set);
					plog('bottledoctor.memberlist.edit', '修改等级' . $updatecontent);
				}
				else {
					$updatecontent = '<br/>等级名称: ' . $memberlist['communicationgrade'] . '->' . $data['communicationgrade'] ;
					pdo_update('ewei_shop_member', $data, array('id' => $id, 'uniacid' => $_W['uniacid']));
					plog('bottledoctor.memberlist.edit', '修改会员等级 ID: ' . $id . $updatecontent);
				}
			}

			show_json(1, array('url' => webUrl('bottledoctor/memberlist')));
		}
		include $this->template();
	}

	public function delete()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);

		if (empty($id)) {
			$id = (is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0);
		}
		$items = pdo_fetchall('SELECT id,communicationgrade FROM ' . tablename('ewei_shop_member') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);

		foreach ($items as $item) {
			pdo_delete('ewei_shop_member', array('id' => $item['id']));
			plog('bottledoctor.memberlist.delete', '删除等级 ID: ' . $item['id'] . ' 标题: ' . $item['communicationgrade'] . ' ');
		}

		show_json(1, array('url' => referer()));
	}
}
?>
