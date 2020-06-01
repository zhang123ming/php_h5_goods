<?php

if (!defined('IN_IA')) {
	exit('Access Denied');
}

require EWEI_SHOPV2_PLUGIN . 'app/core/page_mobile.php';
class Store_EweiShopV2Page extends AppMobilePage
{
	/**
     * 门店选择
     */
	public function selector()
	{
		global $_W;
		global $_GPC;
		$lat = floatval($_GPC['lat']);
		$lng = floatval($_GPC['lng']);
		$member= m('member')->getMember($_W['openid']);
		$ids = trim($_GPC['ids'])?intval($_GPC['ids']):$member['store_id'];
		$type = intval($_GPC['type']);
		$merchid = intval($_GPC['merchid']);
		$condition = '';

		$verifyset=m('common')->getSysset('verify');

		if (!empty($verifyset['store']['showstyle'])) {
			$ids='';
		}else{

			if (empty($member['store_id'])) {

				if (!empty($verifyset['store']['unbindHidden'])) {
					$ids='undefined';
				}
			
			}

		}

		if (!empty($ids)) {
			$condition = ' and id in(' . $ids . ')';
		}
		if (empty($type)) {

			$condition .= ' and type !=0 ';

		}else{
			if ($type == 1) {
				$condition .= ' and type in(1,3) ';
			}
			else {
				if ($type == 2) {
					$condition .= ' and type in(2,3) ';
				}


			}
		}

		if (0 < $merchid) {
			$list = pdo_fetchall('select * from ' . tablename('ewei_shop_merch_store') . ' where  uniacid=:uniacid and merchid=:merchid and status=1 ' . $condition . ' order by displayorder desc,id desc', array(':uniacid' => $_W['uniacid'], ':merchid' => $merchid));
		}
		else {
			$list = pdo_fetchall('select * from ' . tablename('ewei_shop_store') . ' where  uniacid=:uniacid and status=1 ' . $condition . ' order by displayorder desc,id desc', array(':uniacid' => $_W['uniacid']));
		}
		$list = set_medias($list, 'logo');
		$arr = array();
		foreach ($list as $key => &$value) {
			if ($value['id']!=$member['store_id']) {
				$list[$key]['storename'] = $list[$key]['storename']."(门店ID:".$value[id].")";
				if(!empty($_GPC['lat'])&&!empty($_GPC['lng'])){
					$distance = m('store')->getDistance($_GPC['lat'],$_GPC['lng'],$value['lat'],$value['lng']);
					$value['distance'] = round($distance/1000,2);
					$arr[$key] = $value['distance'];
				}
			}


			$list[$key]['detailaddress'] = $value['province'].$value['city'].$value['area'].$value['address'];
			$gcj02 = $this->Convert_BD09_To_GCJ02($value['lat'], $value['lng']);
			$value['lat'] = $gcj02['lat'];
			$value['lng'] = $gcj02['lng'];
			
		}
		unset($value);
		array_multisort($arr,SORT_ASC,SORT_REGULAR,$list);

		if (!empty($verifyset['store']['salerTelHidden'])) {

			foreach ($list as $key => $value) {
				$list[$key]['mobile'] = substr_replace($value['mobile'], '****', 3, 4);
			}

			
		}
			$list = m('util')->multi_array_sort($list, 'distance');
		app_json(array('list' => $list));
	}

	/**
     * 门店地图
     */
	public function map()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		$merchid = intval($_GPC['merchid']);

		$verifyset=m('common')->getSysset('verify');
		if (0 < $merchid) {
			$store = pdo_fetch('select * from ' . tablename('ewei_shop_merch_store') . ' where id=:id and uniacid=:uniacid and merchid=:merchid', array(':id' => $id, ':uniacid' => $_W['uniacid'], ':merchid' => $merchid));
		}
		else {
			$store = pdo_fetch('select * from ' . tablename('ewei_shop_store') . ' where id=:id and uniacid=:uniacid', array(':id' => $id, ':uniacid' => $_W['uniacid']));
		}

		if (!empty($verifyset['store']['salerTelHidden'])) {

			$store['tel'] = substr_replace($store['tel'], '****', 3, 4);
	
		}

		$store['logo'] = empty($store['logo']) ? $_W['shopset']['shop']['logo'] : $store['logo'];
		$store['logo'] = tomedia($store['logo']);
		$gcj02 = $this->Convert_BD09_To_GCJ02($store['lat'], $store['lng']);
		$store['lat'] = $gcj02['lat'];
		$store['lng'] = $gcj02['lng'];
		app_json(array('store' => $store));
	}

	public function Convert_BD09_To_GCJ02($lat, $lng)
	{
		$x_pi = (3.1415926535897931 * 3000) / 180;
		$x = $lng - 0.0064999999999999997;
		$y = $lat - 0.0060000000000000001;
		$z = sqrt(($x * $x) + ($y * $y)) - (2.0000000000000002E-5 * sin($y * $x_pi));
		$theta = atan2($y, $x) - (3.0000000000000001E-6 * cos($x * $x_pi));
		$lng = $z * cos($theta);
		$lat = $z * sin($theta);
		return array('lat' => $lat, 'lng' => $lng);
	}
}

?>
