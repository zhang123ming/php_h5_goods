<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Index_EweiShopV2Page extends WebPage
{

	public function main()
	{
		global $_W;
		global $_GPC;
		$uniacid = intval($_W['uniacid']);
		$data = m('common')->getPluginset('danmu');
		$pindex = max(1, intval($_GPC['page']));
		$psize = 10;
		$condition = ' and uniacid=:uniacid';
		$params = array(':uniacid' => $uniacid);

		$recordlist = pdo_fetchall('SELECT * FROM '.tablename("ewei_shop_danmu_record")." WHERE 1 $condition", $params);

		$total = pdo_fetchcolumn('SELECT count(*) FROM ' . tablename('ewei_shop_danmu_record') . ' WHERE 1 ' . $condition . ' ', $params);
		$pager = pagination2($total, $pindex, $psize);
		include $this->template();
	}

	public function opentask()
	{
		$data = m('common')->getPluginset('danmu');
		$data['isopendanmu'] = 1;
		m('common')->updatePluginset(array('danmu' => $data));
		header('location: ' . webUrl('sale.danmu'));
	}

	public function closetask()
	{
		$data = m('common')->getPluginset('danmu');
		$data['isopendanmu'] = 0;
		m('common')->updatePluginset(array('danmu' => $data));
		header('location: ' . webUrl('sale.danmu'));
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
		$uniacid = intval($_W['uniacid']);
		$id = intval($_GPC['id']);

		if (!empty($id)) {
			$item = pdo_fetch('SELECT *  FROM ' . tablename('ewei_shop_danmu_record') . ' WHERE uniacid = ' . $uniacid . ' and id =' . $id);
			$item = set_medias($item, array('thumb'));

		}

		if ($_W['ispost']) {


			$createtime = strtotime($_GPC['createtime']);
			if (empty($createtime) || (time() < $createtime)) {
				$createtime = time();
			}

			$data = array('uniacid' => $_W['uniacid'], 'nickname' => trim($_GPC['nickname']), 'headimgurl' => trim($_GPC['headimgurl']),'type' => trim($_GPC['type']), 'createtime' => $createtime);

			if (empty($data['nickname'])) {
				$data['nickname'] = pdo_fetchcolumn('select nickname from ' . tablename('mc_members') . ' where nickname<>\'\' order by rand() limit 1');
			}

			if (empty($data['headimgurl'])) {
				$data['headimgurl'] = pdo_fetchcolumn('select avatar from ' . tablename('mc_members') . ' where avatar<>\'\' order by rand() limit 1');
			}
			
			
			if (!empty($id)) {
				pdo_update('ewei_shop_danmu_record', $data, array('id' => $id));
			
			}
			else {
				pdo_insert('ewei_shop_danmu_record', $data);
				$id = pdo_insertid();
			
			}

			show_json(1, array('url' => webUrl('sale.danmu')));
		}


		include $this->template();
	}

	public function delete()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		$item = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_danmu_record') . ' WHERE id  = :id  and uniacid=:uniacid ', array(':id' => $id, ':uniacid' => $_W['uniacid']));

		if (!empty($item)) {
			pdo_delete('ewei_shop_danmu_record', array('id' => $id, 'uniacid' => $_W['uniacid']));
		}

		show_json(1);
	}
}

?>
