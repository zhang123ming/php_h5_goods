<?php
if (!(defined('IN_IA'))) {
	exit('Access Denied');
}

class Index_EweiShopV2Page extends MobilePage
{
	public function main()
	{
		global $_W;
		global $_GPC;
		$allcategory = m('shop')->getCategory();
		$catlevel = intval($_W['shopset']['category']['level']);
		$opencategory = true;
		$plugin_commission = p('commission');
		if ($plugin_commission && (0 < intval($_W['shopset']['commission']['level']))) {
			$mid = intval($_GPC['mid']);

			if (!(empty($mid)) && empty($_W['shopset']['commission']['closemyshop']) && !(empty($_W['shopset']['commission']['select_goods']))) {
				$shop = p('commission')->getShop($mid);

				if (empty($shop['selectcategory']) && !(empty($shop['selectgoods']))) {
					$opencategory = false;
				}

			}

		}


		include $this->template();
	}

	public function gift()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$giftid = intval($_GPC['id']);
		$gift = pdo_fetch('select * from ' . tablename('ewei_shop_gift') . ' where uniacid = ' . $uniacid . ' and id = ' . $giftid . ' and starttime <= ' . time() . ' and endtime >= ' . time() . ' and status = 1 ');
		$giftgoodsid = explode(',', $gift['giftgoodsid']);
		$giftgoods = array();

		if (!(empty($giftgoodsid))) {
			foreach ($giftgoodsid as $key => $value ) {
				$giftgoods[$key] = pdo_fetch('select id,status,title,thumb,marketprice from ' . tablename('ewei_shop_goods') . ' where uniacid = ' . $uniacid . ' and deleted = 0 and total > 0 and id = ' . $value . ' and status = 2 ');
			}

			$giftgoods = array_filter($giftgoods);
		}


		include $this->template();
	}

	public function get_list()
	{
		global $_GPC;
		global $_W;
		$args = array('pagesize' => 10, 'page' => intval($_GPC['page']), 'isnew' => trim($_GPC['isnew']), 'ishot' => trim($_GPC['ishot']), 'isrecommand' => trim($_GPC['isrecommand']), 'isdiscount' => trim($_GPC['isdiscount']), 'istime' => trim($_GPC['istime']), 'issendfree' => trim($_GPC['issendfree']), 'keywords' => trim($_GPC['keywords']), 'cate' => trim($_GPC['cate']), 'order' => trim($_GPC['order']), 'by' => trim($_GPC['by']),'group'=>trim($_GPC['group']));
		
		$plugin_commission = p('commission');

		if ($plugin_commission && (0 < intval($_W['shopset']['commission']['level'])) && empty($_W['shopset']['commission']['closemyshop']) && !(empty($_W['shopset']['commission']['select_goods']))) {
			$frommyshop = intval($_GPC['frommyshop']);
			$mid = intval($_GPC['mid']);

			if (!(empty($mid)) && !(empty($frommyshop))) {
				$shop = p('commission')->getShop($mid);

				if (!(empty($shop['selectgoods']))) {
					$args['ids'] = $shop['goodsids'];
				}

			}

		}


		$this->_condition($args);
	}

	public function query()
	{
		global $_GPC;
		global $_W;
		$args = array('pagesize' => 10, 'page' => intval($_GPC['page']), 'isnew' => trim($_GPC['isnew']), 'ishot' => trim($_GPC['ishot']), 'isrecommand' => trim($_GPC['isrecommand']), 'isdiscount' => trim($_GPC['isdiscount']), 'istime' => trim($_GPC['istime']), 'keywords' => trim($_GPC['keywords']), 'cate' => trim($_GPC['cate']), 'order' => trim($_GPC['order']), 'by' => trim($_GPC['by']));
		$this->_condition($args);
	}

	private function _condition($args)
	{
		global $_GPC;
		global $_W;
		
		$merch_plugin = p('merch');
		if (trim($_GPC["keywords"])) {
			$dateTime = (date("yy-m-d", time()));
			$keywords = pdo_fetch('select * from ' . tablename('ewei_search_keywords') . ' where search_keywords = "' . trim($_GPC['keywords']) . '" and dateTime = "' . $dateTime . '"');
			if (!(empty($keywords))) {
				// dump("存在 ");die;
				// $count = array(
				// 	"search_count" => ($keywords["search_count"] + 1)
				// );
				// pdo_update("ewei_search_keywords", $count, array("search_id" => $keywords["search_id"]));
			} else {
				// 不存在
				$data = array('search_keywords' => $_GPC["keywords"], "dateTime" => $dateTime);
				pdo_insert('ewei_search_keywords', $data);
			}
		}
		$merch_data = m('common')->getPluginset('merch');

		if ($merch_plugin && $merch_data['is_openmerch']) {
			$args['merchid'] = intval($_GPC['merchid']);
		}

		if (isset($_GPC['nocommission'])) {
			$args['nocommission'] = intval($_GPC['nocommission']);
		}
		$goods = m('goods')->getList($args);

		show_json(1, array('list' => $goods['list'], 'total' => $goods['total'], 'pagesize' => $args['pagesize']));
	}
}


?>