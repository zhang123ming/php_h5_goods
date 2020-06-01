<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Index_EweiShopV2Page extends MobilePage
{
	public function detail()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		$merchid = intval($_GPC['merchid']);

		if ($merchid) {
			$item = pdo_fetch('select * from ' . tablename('ewei_shop_merch_store') . ' where id =:id and uniacid=:uniacid and merchid=:merchid', array(':id' => $id, ':uniacid' => $_W['uniacid'], 'merchid' => $merchid));
		}
		else {
			$item = pdo_fetch('select * from ' . tablename('ewei_shop_store') . ' where id =:id and uniacid=:uniacid', array(':id' => $id, ':uniacid' => $_W['uniacid']));
		}

		$item['logo'] = tomedia($item['logo']);

		if (!empty($item['tag'])) {
			$tags = explode(',', $item['tag']);

			if (!empty($tags)) {
				foreach ($tags as &$tag) {
					if (2 < mb_strlen($tag, 'UTF-8')) {
						$lable = mb_substr($tag, 0, 2, 'UTF-8');
					}
				}

				unset($tag);
			}

			$item['taglist'] = $tags;
			$item['hastag'] = 1;
		}
		else {
			$item['hastag'] = 0;
		}

		if (!empty($item['label'])) {
			$lables = explode(',', $item['label']);

			if (!empty($lables)) {
				foreach ($lables as &$lable) {
					if (4 < mb_strlen($lable, 'UTF-8')) {
						$lable = mb_substr($lable, 0, 4, 'UTF-8');
					}
				}

				unset($lable);
			}

			$item['labellist'] = $lables;
			$item['haslabel'] = 1;
		}
		else {
			$item['haslabel'] = 0;
		}

		include $this->template();
	}

	public function storelist(){
		global $_GPC,$_W;
		include $this->template();
	}
	public function verifystorelist(){
		global $_GPC,$_W;
		include $this->template();
	}

	public function getstorelist() 
	{
		global $_W;
		global $_GPC;
		$data = array();
		$pindex = max(1, intval($_GPC['page']));
		$psize = 30;
		$lat = floatval($_GPC['lat']);
		$lng = floatval($_GPC['lng']);
		$sorttype = $_GPC['sorttype'];
		$range = $_GPC['range'];
		$param='';

		if (empty($range)) 
		{
			$range = 10;
		}
		if (!(empty($_GPC['keyword']))) 
		{
			$param =" and storename like '%".$_GPC['keyword']."%'";
		}

		if (!(empty($sorttype))) 
		{
			$param .= " order by id desc";
		}

		$merchuser = pdo_fetchall('select id,uniacid,storename,logo,address,tel,lng,lat from ' . tablename('ewei_shop_store') . ' where status!=0 and uniacid=:uniacid'.$param, array(':uniacid' => $_W['uniacid']));

		
		if (!(empty($merchuser))) 
		{
			foreach ($merchuser as $k => $v ) 
			{
				if (($lat != 0) && ($lng != 0) && !(empty($v['lat'])) && !(empty($v['lng']))) 
				{
					$distance = m('util')->GetDistance($lat, $lng, $v['lat'], $v['lng'], 2);
					if ((0 < $range) && ($range < $distance)) 
					{
						unset($merchuser[$k]);
						continue;
					}
					$merchuser[$k]['distance'] = $distance;
				}
				else 
				{
					$merchuser[$k]['distance'] = 100000;
				}
				// $merchuser[$k]['catename'] = $cate_list[$v['cateid']]['catename'];
				$merchuser[$k]['url'] = mobileUrl('store/map', array('id' => $v['id']));
				$merchuser[$k]['merch_url'] = mobileUrl('store/detail', array('id' => $v['id']));
				$merchuser[$k]['logo'] = tomedia($v['logo']);
			}
		}

		$total = count($merchuser);
		if ($sorttype == 0 && !empty($merchuser)) 
		{
			$merchuser = m('util')->multi_array_sort($merchuser, 'distance');
		}

		$start = ($pindex - 1) * $psize;
		if (!(empty($merchuser))) 
		{
			$merchuser = array_slice($merchuser, $start, $psize);
		}
		// var_dump($merchuser,$start,$psize,$pindex);die;
		show_json(1, array('list' => $merchuser, 'total' => $total, 'pagesize' => $psize));
	}

}

?>
