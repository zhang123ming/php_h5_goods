<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Count_EweiShopV2Page extends WebPage
{
	public function main()
	{
		global $_W;
		global $_GPC;
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		$condition = ' og.uniacid = :uniacid ';
		$params = array(':uniacid' => $_W['uniacid']);

		if (!empty($_GPC['id'])) {
			$_GPC['id'] = trim($_GPC['id']);
			$condition .= ' and og.categoryid like :categoryid';
			$params[':categoryid'] = '%' . $_GPC['id'] . '%';
		}


		$orderby = empty($_GPC['orderby']) ? 'desc' : 'asc';

		$sql = "SELECT o.id,o.name,o.thumb, count(og.id) as total FROM ". tablename('ewei_shop_bottledoctor_category') . " o left join ".tablename('ewei_shop_bottledoctor_communion')." og on og.categoryid=o.id WHERE  " . $condition .  " group by o.id"." ORDER BY total ".$orderby .'';

		$sql .= ' LIMIT ' . (($pindex - 1) * $psize) . ',' . $psize;	
		$list = pdo_fetchall($sql, $params);
		$total = pdo_fetchcolumn('select  count(*) from ' . tablename('ewei_shop_bottledoctor_communion') . ' og ' . ' WHERE uniacid=:uniacid ', array(':uniacid' => $_W['uniacid']));
		
		$categorys = pdo_fetchall('SELECT * FROM ' . tablename('ewei_shop_bottledoctor_category') .' WHERE uniacid=:uniacid ', array(':uniacid' => $_W['uniacid']));

		foreach ($list as &$row) {
			$row['percent'] = round(($row['total'] / $total)  * 100, 2);
			$row['picthumb'] = tomedia($row['thumb']);
		}

		unset($row);	
		$pager = pagination2($total, $pindex, $psize);

		if ($_GPC['export'] == 1) {
			ca('bottledoctor.count.export');
			m('excel')->export($list, array(
	'title'   => '问题咨询报告-' . date('Y-m-d-H-i', time()),
	'columns' => array(
		array('title' => '问题类型', 'field' => 'name', 'width' => 24),
		array('title' => '问题个数', 'field' => 'total', 'width' => 12),
		array('title' => '问题占比(%)', 'field' => 'percent', 'width' => 12)
		)
	));
			plog('bottledoctor.count.export', '导出问题咨询报告');
		}

		load()->func('tpl');
		include $this->template('bottledoctor/count');
	}
}

?>
