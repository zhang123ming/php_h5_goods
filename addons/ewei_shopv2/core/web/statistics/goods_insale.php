<?php
if (!(defined('IN_IA'))) {
	exit('Access Denied');
}

class Goods_insale_EweiShopV2Page extends WebPage
{
	public function main($goodsfrom = 'sale')
	{
		global $_W;
		global $_GPC;
		
		if (empty($_W['shopversion'])) {
			$goodsfrom = strtolower(trim($_GPC['goodsfrom']));

			if (empty($goodsfrom)) {
				header('location: ' . webUrl('goods_insale', array('goodsfrom' => 'goods_insale')));
			}

		}
		 else if (!(empty($_GPC['goodsfrom']))) {
			header('location: ' . webUrl('statistics/' . $_GPC['goodsfrom']));
		}


		$merch_plugin = p('merch');
		$merch_data = m('common')->getPluginset('merch');
		if ($merch_plugin && $merch_data['is_openmerch']) {
			$is_openmerch = 1;
		}
		 else {
			$is_openmerch = 0;
		}

		$pindex = max(1, intval($_GPC['page']));
		$psize = 9999999;
		$sqlcondition = $groupcondition = '';
		$condition = ' WHERE g.`uniacid` = :uniacid';
		// var_dump($condition);die;
		$params = array(':uniacid' => $_W['uniacid']);

		if (!(empty($_GPC['keyword']))) {
			$_GPC['keyword'] = trim($_GPC['keyword']);
			$sqlcondition = ' left join ' . tablename('ewei_shop_goods_option') . ' op on g.id = op.goodsid';
			// 商品详情表，比如价钱、分类
			if ($merch_plugin) {
				$sqlcondition .= ' left join ' . tablename('ewei_shop_merch_user') . ' merch on merch.id = g.merchid and merch.uniacid=g.uniacid';
			}
			// 门店表，比如门店名称、主营项目

			$groupcondition = ' group by g.`id`';
			$condition .= ' AND (g.`id` = :id or g.`title` LIKE :keyword or g.`keywords` LIKE :keyword or g.`goodssn` LIKE :keyword or g.`productsn` LIKE :keyword or op.`title` LIKE :keyword or op.`goodssn` LIKE :keyword or op.`productsn` LIKE :keyword';
			
			if ($merch_plugin) {
				$condition .= ' or merch.`merchname` LIKE :keyword';
			}


			$condition .= ' )';
			$params[':keyword'] = '%' . $_GPC['keyword'] . '%';
			$params[':id'] = $_GPC['keyword'];
		}


		if (!(empty($_GPC['cate']))) {
			$_GPC['cate'] = intval($_GPC['cate']);
			$condition .= ' AND FIND_IN_SET(' . $_GPC['cate'] . ',cates)<>0 ';
		}


		empty($goodsfrom) && ($_GPC['goodsfrom'] = $goodsfrom = 'sale');
		$_GPC['goodsfrom'] = $goodsfrom;

		if ($goodsfrom == 'sale') {
			$condition .= ' AND g.`status` > 0 and g.`checked`=0 and g.`total`>0 and g.`deleted`=0';
			$status = 1;
		}
		 else if ($goodsfrom == 'out') {
			$condition .= ' AND g.`status` > 0 and g.`total` <= 0 and g.`deleted`=0 and g.type!=30';
			$status = 1;
		}
		 else if ($goodsfrom == 'stock') {
			$status = 0;
			$condition .= ' AND (g.`status` = 0 or g.`checked`=1) and g.`deleted`=0';
		}
		 else if ($goodsfrom == 'cycle') {
			$status = 0;
			$condition .= ' AND g.`deleted`=1';
		}
		 else if ($goodsfrom == 'verify') {
			$status = 0;
			$condition .= ' AND g.`deleted`=0 and merchid>0 and checked=1';
		}


		$sql = 'SELECT g.id FROM ' . tablename('ewei_shop_goods') . 'g' . $sqlcondition . $condition . $groupcondition;
		$total_all = pdo_fetchall($sql, $params);
		$total = count($total_all);
		
		unset($total_all);

		if (!(empty($total))) {
			$sql = 'SELECT g.* FROM ' . tablename('ewei_shop_goods') . 'g' . $sqlcondition . $condition . $groupcondition . ' ORDER BY g.`status` DESC, merchid ASC, g.`displayorder` DESC,' . "\r\n" . ' g.`id` DESC LIMIT ' . (($pindex - 1) * $psize) . ',' . $psize;
			if ($_SERVER['REMOTE_ADDR']=='219.136.74.111245') {
				var_dump($sql);die;
			}
			
			$list = pdo_fetchall($sql, $params);
			// echo "<pre>";
			// var_dump($list);die();
			//总店显示全部商品信息
			// foreach ($list as $key => &$value ) {
			// 	$url = mobileUrl('goods/detail', array('id' => $value['id']), true);
			// 	$value['qrcode'] = m('qrcode')->createQrcode($url);
			// }

			$pager = pagination2($total, $pindex, $psize);

			if ($merch_plugin) {
				$merch_user = $merch_plugin->getListUser($list, 'merch_user');

				if (!(empty($list)) && !(empty($merch_user))) {
					foreach ($list as &$row ) {
						$row['merchname'] = (($merch_user[$row['merchid']]['merchname'] ? $merch_user[$row['merchid']]['merchname'] : $_W['shopset']['shop']['name']));
					}
				}

			}

		}
		$categorys = m('shop')->getFullCategory(true);
		$category = array();

		foreach ($categorys as $cate ) {
			$category[$cate['id']] = $cate;
		}

		$goodstotal = intval($_W['shopset']['shop']['goodstotal']);
		if ($_GPC['export'] == 1) {
			m('excel')->export($list, array(
	'title'   => '在售商品报告-' . date('Y-m-d-H-i', time()),
	'columns' => array(
		array('title'=>'商品编号','field' => 'goodssn', 'width' => 24),
		array('title' => '商品名称', 'field' => 'title', 'width' => 48),
		array('title' => '价格/元', 'field' => 'productprice', 'width' => 12),
		array('title' => '库存', 'field' => 'total', 'width' => 12),
		array('title' => '出售量', 'field' => 'sales', 'width' => 12)
		)
	));
			plog('statistics.goods.export', '导出商品销售明细');
		}

		$sql = "SELECT g.title,op.title as optitle,op.goodssn,op.stock,op.productprice from" . tablename('ewei_shop_goods') ."as g right join". tablename('ewei_shop_goods_option') ."as op on g.id = op.goodsid where g.uniacid = :uniacid";
		$params = array(':uniacid'=>$_W['uniacid']);
		$oplist = pdo_fetchall($sql, $params);
		// var_dump($oplist);die();

		if ($_GPC['export'] == 2) {
			m('excel')->export($oplist, array(
	'title'   => '在售商品报告(分商品规格)-' . date('Y-m-d-H-i', time()),
	'columns' => array(
		array('title'=>'商品编号','field' => 'goodssn', 'width' => 24),
		array('title' => '商品名称', 'field' => 'title', 'width' => 48),
		array('title' => '商品规格', 'field' => 'optitle', 'width' => 12),
		array('title' => '价格/元', 'field' => 'productprice', 'width' => 12),
		array('title' => '库存量', 'field' => 'stock', 'width' => 12)
		)
	));
			plog('statistics.goods.export', '导出商品销售明细');
		}


		include $this->template();
	}
}