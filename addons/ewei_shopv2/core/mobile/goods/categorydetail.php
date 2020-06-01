<?php
if (!(defined('IN_IA'))) {
	exit('Access Denied');
}

class Categorydetail_EweiShopV2Page extends MobilePage
{
	public function main()
	{
		global $_W;
		global $_GPC;
		$categorys = m('shop')->getCategory();
		
		$categoryid = $_GPC['categoryid'];
		$child1 = pdo_fetchall('select id,name from '.tablename('ewei_shop_category').' where enabled=1 and  parentid =:id and uniacid=:uniacid',array(':uniacid'=>$_W['uniacid'],':id'=>$categoryid));
		if(count($child1)){
			foreach($child1 as &$item){
				$item['child2'] = pdo_fetchall('select id,thumb,name from '.tablename('ewei_shop_category').' where enabled=1 and  parentid =:id and uniacid=:uniacid',array(':uniacid'=>$_W['uniacid'],':id'=>$item['id']));
			}
		}
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

	public function categorylist()
	{
		global $_GPC;
		global $_W;
		$args = array('pagesize' => 10, 'page' => intval($_GPC['page']), 'isnew' => trim($_GPC['isnew']), 'ishot' => trim($_GPC['ishot']), 'isrecommand' => trim($_GPC['isrecommand']), 'isdiscount' => trim($_GPC['isdiscount']), 'istime' => trim($_GPC['istime']), 'issendfree' => trim($_GPC['issendfree']), 'keywords' => trim($_GPC['keywords']), 'order' => trim($_GPC['order']), 'by' => trim($_GPC['by']));
	
		if(!empty($_GPC['cid'])){
			if (json_decode(htmlspecialchars_decode($_GPC['cid']),true)) {
				$args['cates'] = json_decode(htmlspecialchars_decode($_GPC['cid']),true);
				$args['cate'] = trim($_GPC['cate']);
			}
		}else{
			$args['cate'] = trim($_GPC['cate']);
		}
		
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
	private function _condition($args)
	{
		global $_GPC;
		$merch_plugin = p('merch');
		$merch_data = m('common')->getPluginset('merch');
	//判断公众号的所有商品界面是否获取到is_synchro，获取到就可以看到全部商品（总店+多商户自身）
		$set = p('merch')->getPluginsetByMerch('merch');

		if ($merch_plugin && $merch_data['is_openmerch']) {
			$args['merchid'] = intval($_GPC['merchid']);
		}
		//var_dump($merch_data['is_synchro']);die;
		//var_dump($merch_data);die;
		if ($merch_data['is_opensynchro'] == 1) {
			$args['is_opensynchro'] = 1;
		} 
		//var_dump($args['is_opensynchro']);die;

		if (isset($_GPC['nocommission'])) {
			$args['nocommission'] = intval($_GPC['nocommission']);
		}
		$goods = m('goods')->categorylist($args);

		show_json(1, array('list' => $goods['list'], 'total' => $goods['total'], 'pagesize' => $args['pagesize']));
	}
}


?>