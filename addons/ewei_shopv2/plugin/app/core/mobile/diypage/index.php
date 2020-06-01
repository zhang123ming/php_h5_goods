<?php

?>
<?php
if (!(defined('IN_IA'))) {
	exit('Access Denied');
}


require EWEI_SHOPV2_PLUGIN . 'app/core/page_mobile.php';
class Index_EweiShopV2Page extends AppMobilePage
{
	public function main()
	{
		global $_W;
		global $_GPC;

		$pageid = intval($_GPC['id']);
		$logo = $_W['shopset']['shop']['logo'];
		$shopname = $_W['shopset']['shop']['name'];

		if ($_W['setting']['remote']['type'] == ATTACH_FTP) {
			$_W['attachurl'] = $_W['attachurl_remote'] = $_W['setting']['remote']['ftp']['url'] . '/';
		} elseif ($_W['setting']['remote']['type'] == ATTACH_OSS) {
			$_W['attachurl'] = $_W['attachurl_remote'] = $_W['setting']['remote']['alioss']['url'] . '/';
		} elseif ($_W['setting']['remote']['type'] == ATTACH_QINIU) {
			$_W['attachurl'] = $_W['attachurl_remote'] = $_W['setting']['remote']['qiniu']['url'] . '/';
		} elseif ($_W['setting']['remote']['type'] == ATTACH_COS) {
			$_W['attachurl'] = $_W['attachurl_remote'] = $_W['setting']['remote']['cos']['url'] . '/';
		}

		if (empty($pageid)) {
			$pageid = trim($_GPC['type']);
		}


		if (empty($pageid)) {
			app_error(AppError::$PageNotFound);
		}

		$goods = pdo_fetchall('select id,statustimestart,statustimeend,status,isstatustime from ' . tablename('ewei_shop_goods') . ' where uniacid = ' . $_W['uniacid'] . ' and isstatustime > 0 and deleted = 0 and status < 2');

		foreach ($goods as $key => $value) {
			if (($value['statustimestart'] < time()) && (time() < $value['statustimeend'])) {

				$value['status'] = 1;
			}
			else {
				$value['status'] = 0;
			}

			pdo_update('ewei_shop_goods', array('status' => $value['status']), array('id' => $value['id']));
		}


		$page = $this->model->getPage($pageid, true);
		if (empty($page) || empty($page['data'])) {
			app_error(AppError::$PageNotFound);
		}


		$startadv = array();

		if (is_array($page['data']['page']) && !(empty($page['data']['page']['diyadv']))) {
			$startadvitem = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_wxapp_startadv') . ' WHERE id=:id AND uniacid=:uniacid', array(':id' => intval($page['data']['page']['diyadv']), ':uniacid' => $_W['uniacid']));

			if (!(empty($startadvitem)) && !(empty($startadvitem['data']))) {
				$startadv = base64_decode($startadvitem['data']);
				$startadv = json_decode($startadv, true);
				$startadv['status'] = intval($startadvitem['status']);

				if (!(empty($startadv['data']))) {
					foreach ($startadv['data'] as $itemid => &$item ) {
						$item['imgurl'] = tomedia($item['imgurl']);
					}

					unset($itemid, $item);
				}


				if (is_array($startadv['params'])) {
					$startadv['params']['style'] = 'small-bot';
				}


				if (is_array($startadv['style'])) {
					$startadv['style']['opacity'] = '0.6';
				}

			}

		}
	
		$goodsData='';
		foreach ($page['data']['items'] as $k => $v) {
			if ($v['id']=='goods') {
				if ($_GPC['cate']) {
					$args=array('cate'=>$_GPC['cate']);
					$goods = m('goods')->getList($args);
					
					foreach($goods['list'] as $key=>$value) {

						$value[gid]=$value[id];
						$value[price]=$value[minprice];
					}

					$page['data']['items'][$k]['data']=$goods['list'];

				}
				
				$goodsData=$page['data']['items'][$k]['data'];
				
			
				foreach ($goodsData as $key => $val) {
					$sql = "SELECT m.avatar,m.nickname FROM ".tablename('ewei_shop_order_goods')." as o LEFT JOIN ".tablename('ewei_shop_member')." as m ON o.openid=m.openid WHERE o.goodsid=$val[gid] AND m.uniacid=$_W[uniacid] limit 3";
					$buyer = pdo_fetchall($sql);
					$page['data']['items'][$k]['data'][$key]['buyerList']=$buyer;
				}
			}
		}

		$this->diypage('home');
		$cpinfos = com('coupon')->getInfo();

		$cpinfo = cache_load('cpinfos');
		cache_delete('cpinfos');		
		foreach ($cpinfo as $k=>$cptime) {
			if($cptime['timelimit']==0){
				$cpinfo[$k]['timestart']=date('Y-m-d',TIMESTAMP);
				$cpinfo[$k]['timeend']=date('Y-m-d',TIMESTAMP+(60*60*24*intval($cp['timedays'])));

			}else{
				$cpinfo[$k]['timestart'] = date('y-m-d',$cptime['timestart']);
				$cpinfo[$k]['timeend'] = date('y-m-d',$cptime['timeend']);
			}
		}
		$result = array('diypage' => $page['data'], 'startadv' => $startadv, 'customer' => intval($_W['shopset']['app']['customer']), 'phone' => intval($_W['shopset']['app']['phone']),'qrcode'=>intval($_W['shopset']['app']['qrcode']),'cpinfos'=>$cpinfo,'credittext'=>$_W['shopset']['trade']['credittext']);
		if($_GPC['type']=='goodsdetail'){
			$shoptips = pdo_fetchall('select * from ' . tablename('ewei_shop_shoptips') . ' where uniacid = ' . $_W['uniacid'] . ' and showtype = "2" and status = "1" and istop = "1" ORDER BY displayorder DESC');
			if(!empty($shoptips)){
				foreach ($shoptips as $key => $value) {
					$showtype = explode(',',$value['showtype']);
					if(!empty($value['thumb'])){
						$shoptips[$key]['thumb'] = tomedia($value['thumb']);
					}
				}
				$result['shoptips'] = $shoptips;
			}
		} else {
			$shoptips = pdo_fetchall('select * from ' . tablename('ewei_shop_shoptips') . ' where uniacid = ' . $_W['uniacid'] . ' and showtype = "1" and status = "1" and istop = "1" ORDER BY displayorder DESC');
			if(!empty($shoptips)){
				foreach ($shoptips as $key => $value) {
					$showtype = explode(',',$value['showtype']);
					if(!empty($value['thumb'])){
						$shoptips[$key]['thumb'] = tomedia($value['thumb']);
					}
				}
				$result['shoptips'] = $shoptips;
			}
			$shoptipstitle = pdo_fetchall('select * from ' . tablename('ewei_shop_shoptips') . ' where uniacid = ' . $_W['uniacid'] . ' and showtype = "1" and status = "1" and istop = "1" ORDER BY displayorder DESC limit 3');
			if(!empty($shoptips)){
				foreach ($shoptipstitle as $key => $value) {
					if(!empty($value['thumb'])){
						$shoptipstitle[$key]['thumb'] = tomedia($value['thumb']);
					}
				}
				$result['shoptipstitle'] = $shoptipstitle;
			}
		}

		if (!(empty($result['customer']))) {
			$result['customercolor'] = ((empty($_W['shopset']['app']['customercolor']) ? '#ff5555' : $_W['shopset']['app']['customercolor']));
		}

		if (!(empty($result['qrcode']))) {
			$result['qrcodethumb'] = tomedia($_W['shopset']['app']['qrcodethumb']);
		}

		$result['logo'] = $logo;
		$result['shopname'] = $shopname;
		if (!(empty($result['phone']))) {
			$result['phonecolor'] = ((empty($_W['shopset']['app']['phonecolor']) ? '#ff5555' : $_W['shopset']['app']['phonecolor']));
			$result['phonenumber'] = ((empty($_W['shopset']['app']['phonenumber']) ? '#ff5555' : $_W['shopset']['app']['phonenumber']));
		}


		app_json($result);
	}

	public function main2()
	{
		global $_W;
		global $_GPC;
		$diypage = p('diypage');

		if (!($diypage)) {
			app_error(AppError::$PluginNotFound);
		}


		$pagetype = trim($_GPC['type']);

		if (!(empty($pagetype))) {
			$pageid = $this->type2Pageid($pagetype);
		}
		 else {
			$pageid = intval($_GPC['id']);
		}

		if (empty($pageid)) {
			app_error(AppError::$PageNotFound);
		}


		$page = $diypage->getPage($pageid, true);
		if (empty($page) || empty($page['data'])) {
			app_error(AppError::$PageNotFound);
		}


		app_json(array('diypage' => $page['data']));
	}

	public function diyPage($type)
	{
		global $_W;
		global $_GPC;
		if (empty($type) || !p('diypage')) {
			return false;
		}

		$merch = intval($_GPC['merchid']);
		if ($merch && ($type != 'member') && ($type != 'commission')) {
			if (!p('merch')) {
				return false;
			}

			$diypagedata = p('merch')->getSet('diypage', $merch);
		}
		else {
			$diypagedata = m('common')->getPluginset('diypage');
		}
		if (!empty($diypagedata)) {
			$diypageid = $diypagedata['page'][$type];
			if (!empty($diypageid)) {
				$page = p('diypage')->getPage($diypageid, true);
				if (!empty($page)) {
					p('diypage')->setShare($page);
					$diyitems = $page['data']['items'];
					$diyitem_search = array();
					if (!empty($diyitems) && is_array($diyitems)) {
						$jsondiyitems = json_encode($diyitems);

						if (strexists($jsondiyitems, 'fixedsearch')) {
							foreach ($diyitems as $diyitemid => $diyitem) {
								if ($diyitem['id'] == 'fixedsearch') {
									$diyitem_search = $diyitem;
									unset($diyitems[$diyitemid]);
								}
							}

							unset($diyitem);
						}
						unset($diyitem);
					}

					$startadv = p('diypage')->getStartAdv($page['diyadv']);
					if ($type == 'home') {
						$cpinfos = com('coupon')->getInfo();
					}
				}
			}
		}
	}

	/**
     * 根据type获取id
     * @param null $type
     * @return int
     */
	public function type2Pageid($type = NULL)
	{
		if (empty($type)) {
			return 0;
		}


		$set = m('common')->getPluginset('diypage');
		$pageset = $set['page'];
		$pageid = intval($pageset[$type . '_wxapp']);
		return $pageid;
	}

	public function getInfo()
	{
		global $_GPC;
		global $_W;
		$dataurl = $_GPC['dataurl'];

		if (empty($dataurl)) {
			app_json(array(
			'goods' => array(),
			'type'  => 'stores'
			));
		}


		if (!(empty($_GPC['num'])) && ($_GPC['paramsType'] == 'stores')) {
			$storenum = 6 + intval($_GPC['num']);
		}
		 else {
			$storenum = 6;
		}

		if (!(empty($_GPC['num'])) && ($_GPC['paramsType'] == 'goods')) {
			$goodsnum = 20 + intval($_GPC['num']);
		}
		 else {
			$goodsnum = 20;
		}

		if (!(empty($dataurl))) {
			if (strpos($dataurl, '/pages/') === false) {
				$dataParams = explode('=', $dataurl);

				if ($dataParams[0] == 'category') {
					$args = array('cate' => $dataParams[1]);
					$lists = m('goods')->getList($args);
					$list['list'] = array_slice($lists['list'], 0, $goodsnum);
					$list['count'] = count($lists['list']);
					app_json(array('goods' => $list, 'type' => 'goods'));
				}
				 else if ($dataParams[0] == 'groups') {
					$sql = 'SELECT * FROM ' . tablename('ewei_shop_goods_group') . ' WHERE id = :id AND uniacid = :uniacid';
					$params = array(':uniacid' => $_W['uniacid'], ':id' => $dataParams[1]);
					$groupsData = pdo_fetch($sql, $params);
					$goodsid = $groupsData['goodsids'];
					$goodsql = 'SELECT id,title,subtitle,thumb,marketprice,productprice,minprice,maxprice,isdiscount,isdiscount_time,isdiscount_discounts,sales,salesreal,total,description,bargain,`type`,ispresell,`virtual`,hasoption,video FROM ' . tablename('ewei_shop_goods') . ' WHERE id in(' . $goodsid . ') AND uniacid =' . $_W['uniacid'] . ' limit 0,' . $goodsnum;
					$count = pdo_fetch('SELECT count(id) as count FROM ' . tablename('ewei_shop_goods') . ' WHERE id in(' . $goodsid . ') AND uniacid =' . $_W['uniacid']);
					$list['list'] = pdo_fetchall($goodsql);
					$list['count'] = $count['count'];

					foreach ($list['list'] as $k => $v ) {
						$list['list'][$k]['thumb'] = tomedia($v['thumb']);
					}

					app_json(array('goods' => $list, 'type' => 'goods'));
				}
				 else if ($dataParams[0] == 'goodsids') {
					$goodsids = explode(',', $dataParams[1]);

					if (!(empty($goodsids))) {
						foreach ($goodsids as $gk => $gv ) {
							if ($gv == '') {
								unset($goodsids[$gk]);
							}

						}

						$goodsid = implode(',', $goodsids);
						$sql = 'SELECT id,title,subtitle,thumb,marketprice,productprice,minprice,maxprice,isdiscount,isdiscount_time,isdiscount_discounts,sales,salesreal,total,description,bargain,`type`,ispresell,`virtual`,hasoption,video FROM ' . tablename('ewei_shop_goods') . ' WHERE id in(' . $goodsid . ') AND uniacid =' . $_W['uniacid'] . ' limit 0,' . $goodsnum;
						$count = pdo_fetch('SELECT count(id) as count FROM ' . tablename('ewei_shop_goods') . ' WHERE id in(' . $goodsid . ') AND uniacid =' . $_W['uniacid']);
						$list['list'] = pdo_fetchall($sql);
						$list['count'] = $count['count'];

						foreach ($list['list'] as $k => $v ) {
							$list['list'][$k]['thumb'] = tomedia($v['thumb']);
						}

						app_json(array('goods' => $list, 'type' => 'goods'));
					}

				}
				 else if ($dataParams[0] == 'stores') {
					$urlValue = explode('?', $dataParams[1]);
					$storesids = explode(',', $urlValue[0]);

					if (!(empty($storesids))) {
						foreach ($storesids as $gk => $gv ) {
							if ($gv == '') {
								unset($storesids[$gk]);
							}

						}

						$storesid = implode(',', $storesids);
						$sql = 'SELECT id,storename FROM ' . tablename('ewei_shop_store') . ' WHERE id in(' . $storesid . ') AND uniacid =' . $_W['uniacid'] . ' limit 0,' . $storenum;
						$count = pdo_fetch('SELECT count(id) as count FROM ' . tablename('ewei_shop_store') . ' WHERE id in(' . $storesid . ') AND uniacid =' . $_W['uniacid']);
						$list['list'] = pdo_fetchall($sql);
						$list['count'] = $count['count'];
						app_json(array('goods' => $list, 'type' => 'stores'));
					}

				}

			}

		}

	}

}


?>