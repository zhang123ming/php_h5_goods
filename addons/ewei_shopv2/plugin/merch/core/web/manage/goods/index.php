<?php

//dezend by http://www.yunlu99.com/ QQ:270656184
if (!defined('IN_IA')) {
	exit('Access Denied');
}
require EWEI_SHOPV2_PLUGIN . 'merch/core/inc/page_merch.php';
class Index_EweiShopV2Page extends MerchWebPage
{
	public function main($goodsfrom = '')
	{
		global $_W;
		global $_GPC;
		if (empty($_W['shopversion'])) {
			$goodsfrom = strtolower(trim($_GPC['goodsfrom']));
			if (empty($goodsfrom)) {
				header('location: ' . webUrl('goods', array('goodsfrom' => 'sale')));
			}
		}
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		$sqlcondition = $groupcondition = '';
		//门店的condition：g.`merchid`=:merchid限制了只能查看门店的商品
		$condition = ' WHERE g.`uniacid` = :uniacid and g.`merchid`=:merchid';
		$params = array(':uniacid' => $_W['uniacid'], ':merchid' => $_W['merchid']);
		// 从ewei_shop_sysset获取是否开启商品同步状态，0为关闭，1为开启，默认关闭
		$set = p('merch')->getPluginsetByMerch('merch');
		$synchro_status = $set['is_opensynchro'];
		// 判断是否开启商品同步，开启则显示全部商品（总店+自身多商户）
		if ($synchro_status==1) {
			$condition = ' WHERE g.`uniacid` = :uniacid and g.`merchid` in (:merchid,0)';
		}
		$not_add = 0;
		$merch_user = $_W['merch_user'];
		$maxgoods = intval($merch_user['maxgoods']);

		if (0 < $maxgoods) {
			$sql = 'SELECT COUNT(1) FROM ' . tablename('ewei_shop_goods') . ' where uniacid=:uniacid and merchid=:merchid';
			$goodstotal = pdo_fetchcolumn($sql, $params);

			if ($maxgoods <= $goodstotal) {
				$not_add = 1;

			}
		}

		if (!empty($_GPC['keyword'])) {
			$_GPC['keyword'] = trim($_GPC['keyword']);
			$sqlcondition = ' left join ' . tablename('ewei_shop_goods_option') . ' op on g.id = op.goodsid';
			$groupcondition = ' group by g.`id`';
			$condition .= ' AND (g.`id` = :id or g.`title` LIKE :keyword or g.`goodssn` LIKE :keyword or g.`productsn` LIKE :keyword or op.`title` LIKE :keyword or op.`goodssn` LIKE :keyword or op.`productsn` LIKE :keyword)';
			$params[':keyword'] = '%' . $_GPC['keyword'] . '%';
			$params[':id'] = $_GPC['keyword'];
		}
		if (!empty($_GPC['cate'])) {
			$_GPC['cate'] = intval($_GPC['cate']);
			$condition .= ' AND FIND_IN_SET(' . $_GPC['cate'] . ',cates)<>0 ';
		}
		if (empty($goodsfrom)) {
			$goodsfrom = $_GPC['goodsfrom'];
		}
		if (empty($goodsfrom)) {
			$goodsfrom = 'sale';
		}
		
		if ($goodsfrom == 'sale') {
			$condition .= ' AND g.`status` = 1  and g.`total`>0 and g.`deleted`=0  AND g.`checked`=0';
			$status = 1;
		} else {
			if ($goodsfrom == 'out') {
				$condition .= ' AND g.`total` <= 0 and g.`deleted`=0  AND g.`checked`=0';
				$status = 1;
			} else {
				if ($goodsfrom == 'stock') {
					$status = 0;
					$condition .= ' AND g.`status` = 0 and g.`deleted`=0 AND g.`checked`=0';
				} else {
					if ($goodsfrom == 'cycle') {
						$status = 0;
						$condition .= ' AND g.`deleted`=1';
					} else {
						if ($goodsfrom == 'check') {
							$status = 0;
							$condition .= ' AND g.`checked`=1 and g.`deleted`=0';
						} else {
							if ($goodsfrom == 'select') {
								
							}
						}
					}
				}
			}
		}
		$sql = 'SELECT COUNT(g.`id`) FROM ' . tablename('ewei_shop_goods') . 'g' . $sqlcondition . $condition . $groupcondition;
		$total = pdo_fetchcolumn($sql, $params);
		$list = array();
		if (!empty($total)) {
			$sql = 'SELECT g.* FROM ' . tablename('ewei_shop_goods') . 'g' . $sqlcondition . $condition . $groupcondition . " ORDER BY g.`status` DESC, g.`merchdisplayorder` DESC,\r\n                g.`id` DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize;
			$list = pdo_fetchall($sql, $params);
			foreach ($list as $key => &$value) {
				$url = mobileUrl('goods/detail', array('id' => $value['id']), true);
				$value['qrcode'] = m('qrcode')->createQrcode($url);
			}
			$pager = pagination2($total, $pindex, $psize);
		}
		$categorys = m('shop')->getFullCategory(true);
		$category = array();
		foreach ($categorys as $cate) {
			$category[$cate['id']] = $cate;
		}
		include $this->template('goods');
	}
	public function add()
	{
		$this->post();
	}
	public function edit()
	{
		$this->post();
	}
	public function sale()
	{
		$this->main('sale');
	}
	public function out()
	{
		$this->main('out');
	}
	public function stock()
	{
		$this->main('stock');
	}
	public function cycle()
	{
		$this->main('cycle');
	}
	public function verify()
	{
		$this->main('verify');
	}
	public function check()
	{
		$this->main('check');
	}
	
	/*一键同步商品*/
	public function synchro()
	{
		global $_W;
		global $_GPC;
		$set = p('merch')->getPluginsetByMerch('merch');
		$synchro_status = $set['is_opensynchro'];
		if ($synchro_status==1) {
			$params = array(':uniacid' => $_W['uniacid']);
			$condition = ' WHERE `uniacid` = :uniacid and `merchid` = 0';
			$sql_synchro = 'SELECT * FROM ' . tablename('ewei_shop_goods') . $condition;
			$list_synchro = pdo_fetchall($sql_synchro,$params);
			foreach ($list_synchro as $k => $v) {
				$sql_check = "SELECT title FROM " .tablename('ewei_shop_goods') . " WHERE `uniacid` = :uniacid and `merchid` = '{$_W[merchid]}' AND title='{$v[title]}'";
				$list_check = pdo_fetch($sql_check,$params);
				if ($list_check) {
					// 如果发现商品有重复的标题则不执行任何操作
				}else{
				unset($v['id']);// 置空id
				$v['merchid'] = $_W['uniaccount']['merchid'];// 把merchid改为分店的merchid
				$v['merchsale'] = 1;
				$v['showsales'] = 1;
				pdo_insert('ewei_shop_goods',$v);
				}
				header('location:' . webUrl('goods'));
				// 执行同步之后则跳转回当前页面
			}
		}
		

	}
	protected function post()
	{
		require dirname(__FILE__) . '/post.php';
	}
	public function delete()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		if (empty($id)) {
			$id = is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0;
		}
		$items = pdo_fetchall('SELECT id,title FROM ' . tablename('ewei_shop_goods') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);
		foreach ($items as $item) {
			pdo_update('ewei_shop_goods', array('deleted' => 1), array('id' => $item['id']));
			mplog('goods.delete', '删除商品 ID: ' . $item['id'] . ' 商品名称: ' . $item['title'] . ' ');
		}
		show_json(1, array('url' => referer()));
	}
	public function status()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		if (empty($id)) {
			$id = is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0;
		}
		$items = pdo_fetchall('SELECT id,title FROM ' . tablename('ewei_shop_goods') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);
		foreach ($items as $item) {
			pdo_update('ewei_shop_goods', array('status' => intval($_GPC['status'])), array('id' => $item['id']));
			mplog('goods.edit', '修改商品状态<br/>ID: ' . $item['id'] . '<br/>商品名称: ' . $item['title'] . '<br/>状态: ' . $_GPC['enabled'] == 1 ? '上架' : '下架');
		}
		show_json(1, array('url' => referer()));
	}
	public function delete1()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		if (empty($id)) {
			$id = is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0;
		}
		$items = pdo_fetchall('SELECT id,title FROM ' . tablename('ewei_shop_goods') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);
		foreach ($items as $item) {
			pdo_delete('ewei_shop_goods', array('id' => $item['id']));
			mplog('goods.edit', '从回收站彻底删除商品<br/>ID: ' . $item['id'] . '<br/>商品名称: ' . $item['title']);
		}
		show_json(1, array('url' => referer()));
	}
	public function restore()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		if (empty($id)) {
			$id = is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0;
		}
		$items = pdo_fetchall('SELECT id,title FROM ' . tablename('ewei_shop_goods') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);
		foreach ($items as $item) {
			pdo_update('ewei_shop_goods', array('deleted' => 0), array('id' => $item['id']));
			mplog('goods.edit', '从回收站恢复商品<br/>ID: ' . $item['id'] . '<br/>商品名称: ' . $item['title']);
		}
		show_json(1, array('url' => referer()));
	}
	public function property()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		$type = $_GPC['type'];
		$data = intval($_GPC['data']);
		if (in_array($type, array('new', 'hot', 'recommand', 'discount', 'time', 'sendfree', 'nodiscount'))) {
			pdo_update('ewei_shop_goods', array('is' . $type => $data), array('id' => $id, 'uniacid' => $_W['uniacid']));
			if ($type == 'new') {
				$typestr = '新品';
			} else {
				if ($type == 'hot') {
					$typestr = '热卖';
				} else {
					if ($type == 'recommand') {
						$typestr = '推荐';
					} else {
						if ($type == 'discount') {
							$typestr = '促销';
						} else {
							if ($type == 'time') {
								$typestr = '限时卖';
							} else {
								if ($type == 'sendfree') {
									$typestr = '包邮';
								} else {
									if ($type == 'nodiscount') {
										$typestr = '不参与折扣状态';
									}
								}
							}
						}
					}
				}
			}
			mplog('goods.edit', '修改商品' . $typestr . '状态   ID: ' . $id);
		}
		if (in_array($type, array('status'))) {
			pdo_update('ewei_shop_goods', array($type => $data), array('id' => $id, 'uniacid' => $_W['uniacid']));
			mplog('goods.edit', '修改商品上下架状态   ID: ' . $id);
		}
		if (in_array($type, array('type'))) {
			pdo_update('ewei_shop_goods', array($type => $data), array('id' => $id, 'uniacid' => $_W['uniacid']));
			mplog('goods.edit', '修改商品类型   ID: ' . $id);
		}
		show_json(1);
	}
	public function change()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		if (empty($id)) {
			show_json(0, array('message' => '参数错误'));
		}
		$type = trim($_GPC['type']);
		$value = trim($_GPC['value']);
		if (!in_array($type, array('title', 'marketprice', 'total', 'goodssn', 'productsn', 'merchdisplayorder'))) {
			show_json(0, array('message' => '参数错误'));
		}
		$goods = pdo_fetch('select id,hasoption from ' . tablename('ewei_shop_goods') . ' where id=:id and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':id' => $id));
		if (empty($goods)) {
			show_json(0, array('message' => '参数错误'));
		}
		pdo_update('ewei_shop_goods', array($type => $value), array('id' => $id));
		if ($goods['hasoption'] == 0) {
			$sql = 'update ' . tablename('ewei_shop_goods') . ' set minprice = marketprice,maxprice = marketprice where id = ' . $goods['id'] . ' and hasoption=0;';
			pdo_query($sql);
		}
		show_json(1);
	}
	public function tpl()
	{
		global $_GPC;
		global $_W;
		$tpl = trim($_GPC['tpl']);
		if ($tpl == 'option') {
			$tag = random(32);
			include $this->template('goods/tpl/option');
		} else {
			if ($tpl == 'spec') {
				$spec = array('id' => random(32), 'title' => $_GPC['title']);
				include $this->template('goods/tpl/spec');
			} else {
				if ($tpl == 'specitem') {
					$spec = array('id' => $_GPC['specid']);
					$specitem = array('id' => random(32), 'title' => $_GPC['title'], 'show' => 1);
					include $this->template('goods/tpl/spec_item');
				} else {
					if ($tpl == 'param') {
						$tag = random(32);
						include $this->template('goods/tpl/param');
					}
				}
			}
		}
	}
	public function query()
	{
		global $_W;
		global $_GPC;
		$kwd = trim($_GPC['keyword']);
		$params = array();
		$params[':uniacid'] = $_W['uniacid'];
		$params[':merchid'] = $_W['merchid'];
		$condition = ' and status=1 and deleted=0 and uniacid=:uniacid and merchid=:merchid';
		if (!empty($kwd)) {
			$condition .= ' AND (`title` LIKE :keywords OR `keywords` LIKE :keywords)';
			$params[':keywords'] = '%' . $kwd . '%';
		}
		$ds = pdo_fetchall('SELECT id,title,thumb,marketprice,productprice,share_title,share_icon,description,minprice FROM ' . tablename('ewei_shop_goods') . ' WHERE 1 ' . $condition . ' order by createtime desc', $params);
		$ds = set_medias($ds, array('thumb', 'share_icon'));
		if ($_GPC['suggest']) {
			exit(json_encode(array('value' => $ds)));
		}
		include $this->template();
	}
	public function diyform_tpl()
	{
		global $_W;
		global $_GPC;
		$globalData = mp('diyform')->globalData();
		extract($globalData);
		$addt = $_GPC['addt'];
		$kw = $_GPC['kw'];
		$flag = intval($_GPC['flag']);
		$data_type = $_GPC['data_type'];
		$tmp_key = $kw;
		include $this->template('diyform/temp/tpl');
	}
}