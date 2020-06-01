<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Inventory_EweiShopV2Page extends WebPage
{
	protected function orderData($status, $st)
	{
		global $_W;
		global $_GPC;
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		$merch_plugin = p('merch');
		$merch_data = m('common')->getPluginset('merch');
		if ($merch_plugin && $merch_data['is_openmerch']) {
			$is_openmerch = 1;
			$this->updateChildOrderPay();
		}
		else {
			$is_openmerch = 0;
		}

		if ($st == 'main') {
			$st = '';
		}
		else {
			$st = '.' . $st;
		}

		$sendtype = (!isset($_GPC['sendtype']) ? 0 : $_GPC['sendtype']);
		$condition = ' o.uniacid = :uniacid and o.ismr=0 and o.deleted=0 and o.isparent=0 and o.istrade=0 ';
		$uniacid = $_W['uniacid'];
		$paras = $paras1 = array(':uniacid' => $uniacid);
		$merch_data = m('common')->getPluginset('merch');
		if (empty($starttime) || empty($endtime)) {
			$starttime = strtotime('-1 month');
			$endtime = time();
		}
		$searchtime = trim($_GPC['searchtime']);
		if (!empty($searchtime) && is_array($_GPC['time']) && in_array($searchtime, array('create', 'pay', 'send', 'finish'))) {
			$starttime = strtotime($_GPC['time']['start']);
			$endtime = strtotime($_GPC['time']['end']);
			$condition .= ' AND o.' . $searchtime . 'time >= :starttime AND o.' . $searchtime . 'time <= :endtime ';
			$paras[':starttime'] = $starttime;
			$paras[':endtime'] = $endtime;
		}

		if (!empty($_GPC['fid'])) {
			$groupSet = p('groupaward')->getSet();
			
			$awardMode = $groupSet['awardMode'];
			$year = $_GPC['billyear'];
			$month = $_GPC['billmonth'];
			$week = $_GPC['billweek'];
			$days = get_last_day($year, $month);
			$starttime = strtotime($year . '-' . $month . '-1');
			$endtime = strtotime($year . '-' . $month . '-' . $days);
			if ((1 <= $week) && ($week <= 4)) 
			{
				$weekdays = array();
				$i = $starttime;
				while ($i <= $endtime) 
				{
					$ds = explode('-', date('Y-m-d', $i));
					$day = intval($ds[2]);
					$w = ceil($day / 7);
					if (4 < $w) 
					{
						$w = 4;
					}
					if ($week == $w) 
					{
						$weekdays[] = $i;
					}
					$i += 86400;
				}
				$starttime = $weekdays[0];
				$endtime = strtotime(date('Y-m-d', $weekdays[count($weekdays) - 1]) . ' 23:59:59');
			}
			elseif (empty($month)) {
				$starttime = strtotime($year.'-01-01');
				$endtime = strtotime($year.'-12-31 23:59:59');
			}
			else 
			{
				$endtime = strtotime($year . '-' . $month . '-' . $days . ' 23:59:59');
			}
			$searchtime = 'finish';
			if ($awardMode==2) {
				$searchtime = 'pay';
			}
			if ($awardMode==1||$awardMode==3) {
				$condition .= ' and (o.agent100=:fid or o.agent99=:fid or o.agent98=:fid or o.agent97=:fid or o.agent96=:fid or o.agent95=:fid or o.agent94=:fid or o.agent93=:fid or o.agent92=:fid or o.agent91=:fid';
				$paras[':fid'] = $_GPC['fid'];
			}else{
				$condition .= ' and find_in_set(:fid,o.fids)';
				$paras[':fid'] = $_GPC['fid'];
			}
			$condition .= ' AND o.' . $searchtime . 'time >= :starttime AND o.' . $searchtime . 'time <= :endtime ';
			$paras[':starttime'] = $starttime;
			$paras[':endtime'] = $endtime;
			if (!empty($groupSet['orderpaytype'])) {
				$condition .= ' and o.isdeclaration=1';
			}
		}
		if ($_GPC['status'] != '') {
			if ($_GPC['status'] == '0') {
				$condition .= ' AND ( o.status =0 )';
			}else if($_GPC['status'] == '3'){
				$condition .= ' AND o.status =3';
			}else if($_GPC['status'] == '1'){
				$condition .= ' AND o.status =1';
			}else if($_GPC['status'] == '2'){
				$condition .= ' AND o.status =2';
			}
			else {
				$condition .= ' AND o.status =-1';
			}
		}

		if ($st=='.prepay') {
			$condition .=' AND o.isprepay =1';
		}

		if (!empty($_GPC['searchfield']) && !empty($_GPC['keyword'])) {
			$searchfield = trim(strtolower($_GPC['searchfield']));
			$_GPC['keyword'] = trim($_GPC['keyword']);
			$paras[':keyword'] = htmlspecialchars_decode($_GPC['keyword'], ENT_QUOTES);
			$sqlcondition = '';

			if ($searchfield == 'ordersn') {
				$condition .= ' AND locate(:keyword,o.ordersn)>0';
			}
			else if ($searchfield == 'member') {
				$condition .= ' AND (locate(:keyword,m.realname)>0 or locate(:keyword,m.mobile)>0 or locate(:keyword,m.nickname)>0)';
			}
			else if ($searchfield == 'mid') {
				$condition .= ' AND m.id = :keyword';
			}
			else if ($searchfield == 'address') {
				$condition .= ' AND ( locate(:keyword,a.realname)>0 or locate(:keyword,a.mobile)>0 or locate(:keyword,o.carrier)>0)';
			}
			else if ($searchfield == 'location') {
				$condition .= ' AND ( locate(:keyword,a.province)>0 or locate(:keyword,a.city)>0 or locate(:keyword,a.area)>0 or locate(:keyword,a.address)>0)';
			}
			else if ($searchfield == 'expresssn') {
				$condition .= ' AND locate(:keyword,o.expresssn)>0';
			}
			else if ($searchfield == 'saler') {
				$condition .= ' AND (locate(:keyword,sm.realname)>0 or locate(:keyword,sm.mobile)>0 or locate(:keyword,sm.nickname)>0 or locate(:keyword,s.salername)>0 )';
			}
			else if ($searchfield == 'verifycode') {
				$condition .= ' AND (verifycode=:keyword or locate(:keyword,o.verifycodes)>0)';
			}
			else if ($searchfield == 'store') {
				$condition .= ' AND (locate(:keyword,store.storename)>0)';
				$sqlcondition = ' left join ' . tablename('ewei_shop_store') . ' store on store.id = o.verifystoreid and store.uniacid=o.uniacid';
			}
			else if ($searchfield == 'goodstitle') {
				$sqlcondition = ' inner join ( select DISTINCT(og.orderid) from ' . tablename('ewei_shop_order_goods') . ' og left join ' . tablename('ewei_shop_goods') . ' g on g.id=og.goodsid where og.uniacid = \'' . $uniacid . '\' and (locate(:keyword,g.title)>0)) gs on gs.orderid=o.id';
			}
			else if ($searchfield == 'goodssn') {
				$sqlcondition = ' inner join ( select DISTINCT(og.orderid) from ' . tablename('ewei_shop_order_goods') . ' og left join ' . tablename('ewei_shop_goods') . ' g on g.id=og.goodsid where og.uniacid = \'' . $uniacid . '\' and (((locate(:keyword,g.goodssn)>0)) or (locate(:keyword,og.goodssn)>0))) gs on gs.orderid=o.id';
			}
			else if ($searchfield == 'goodsoptiontitle') {
				$sqlcondition = ' inner join ( select  DISTINCT(og.orderid) from ' . tablename('ewei_shop_order_goods') . ' og left join ' . tablename('ewei_shop_goods') . ' g on g.id=og.goodsid where og.uniacid = \'' . $uniacid . '\' and (locate(:keyword,og.optionname)>0)) gs on gs.orderid=o.id';
			}
			else if ($searchfield == 'attachstore') {
				$condition .= ' AND (locate(:keyword,store.storename)>0)';
				$sqlcondition = ' left join ' . tablename('ewei_shop_store') . ' store on store.id = o.storeid and store.uniacid=o.uniacid';
				
			}
			else {
				if ($searchfield == 'merch') {
					if ($merch_plugin) {
						$condition .= ' AND (locate(:keyword,merch.merchname)>0)';
						$sqlcondition = ' left join ' . tablename('ewei_shop_merch_user') . ' merch on merch.id = o.merchid and merch.uniacid=o.uniacid';
					}
				}else if ($searchfield=='machineid') {
					$condition .=' AND (locate(:keyword,o.machineid)>0)';
				}
			}
		}else if ($_GPC['searchfield'] == 'order_sheng') {
				$condition .= ' AND (ml.level=100)';
			}else if ($_GPC['searchfield'] == 'order_shi') {
				$condition .= ' AND (ml.level=99)';
			}else if ($_GPC['searchfield'] == 'order_qu') {
				$condition .= ' AND (ml.level=98)';
			}

		$statuscondition = '';

		if ($status !== '') {
			if ($status == '-1') {
				$statuscondition = ' AND o.status=-1';
			}
			else if ($status == '0') {
				$statuscondition = ' AND o.status = 0 ';
			}
			else if ($status == '3') {
				$statuscondition = ' AND ( o.status = 3 )';
			}
		}
		$agentid = intval($_GPC['agentid']);
		$p = p('commission');
		$level = 0;

		if ($p) {
			$cset = $p->getSet();
			$level = intval($cset['level']);
		}

		$olevel = intval($_GPC['olevel']);
		if (!empty($agentid) && (0 < $level)) {
			$agent = $p->getInfo($agentid, array());

			if (!empty($agent)) {
				$agentLevel = $p->getLevel($agentid);
			}

			if (empty($olevel)) {
				if (1 <= $level) {
					$condition .= ' and  ( o.agentid=' . intval($_GPC['agentid']);
				}

				if ((2 <= $level) && (0 < $agent['level2'])) {
					$condition .= ' or o.agentid in( ' . implode(',', array_keys($agent['level1_agentids'])) . ')';
				}

				if ((3 <= $level) && (0 < $agent['level3'])) {
					$condition .= ' or o.agentid in( ' . implode(',', array_keys($agent['level2_agentids'])) . ')';
				}

				if (1 <= $level) {
					$condition .= ')';
				}
			}
			else if ($olevel == 1) {
				$condition .= ' and  o.agentid=' . intval($_GPC['agentid']);
			}
			else if ($olevel == 2) {
				if (0 < $agent['level2']) {
					$condition .= ' and o.agentid in( ' . implode(',', array_keys($agent['level1_agentids'])) . ')';
				}
				else {
					$condition .= ' and o.agentid in( 0 )';
				}
			}
			else {
				if ($olevel == 3) {
					if (0 < $agent['level3']) {
						$condition .= ' and o.agentid in( ' . implode(',', array_keys($agent['level2_agentids'])) . ')';
					}
					else {
						$condition .= ' and o.agentid in( 0 )';
					}
				}
			}
		}
		$authorid = intval($_GPC['authorid']);
		$author = p('author');
		if ($author && !empty($authorid)) {
			$condition .= ' and o.authorid = :authorid';
			$paras[':authorid'] = $authorid;
		}

		if (($condition != ' o.uniacid = :uniacid and o.ismr=0 and o.deleted=0 and o.isparent=0 and o.istrade=0 ') || !empty($sqlcondition)) {
			$sql = "select o.* , a.realname as arealname,a.mobile as amobile,a.province as aprovince ,a.city as acity , a.area as aarea, a.street as astreet,a.address as aaddress,\r\n                  d.dispatchname,m.nickname,m.id as mid,m.realname as mrealname,m.mobile as mmobile,sm.id as salerid,sm.nickname as salernickname,s.salername,\r\n                  r.rtype,r.status as rstatus,o.sendtype from " . tablename('ewei_shop_order') . ' o' . ' left join ' . tablename('ewei_shop_order_refund') . ' r on r.id =o.refundid ' . ' left join ' . tablename('ewei_shop_member') . ' m on m.openid=o.openid and m.uniacid =  o.uniacid ' . ' left join ' . tablename('ewei_shop_member_address') . ' a on a.id=o.addressid ' . ' left join ' . tablename('ewei_shop_dispatch') . ' d on d.id = o.dispatchid ' . ' left join ' . tablename('ewei_shop_saler') . ' s on s.openid = o.verifyopenid and s.uniacid=o.uniacid' . ' left join ' . tablename('ewei_shop_member') . ' sm on sm.openid = s.openid and sm.uniacid=s.uniacid left join '.tablename('ewei_shop_member_level') . ' ml on m.level = ml.id and m.uniacid = ml.uniacid  ' . $sqlcondition . ' where ' . $condition . ' ' . $statuscondition . ' GROUP BY o.id ORDER BY o.id DESC  ';
			if (empty($_GPC['export'])) {
				$sql .= 'LIMIT ' . (($pindex - 1) * $psize) . ',' . $psize;
			}

			$list = pdo_fetchall($sql, $paras);
			

		}
		else {
			$status_condition = str_replace('o.', '', $statuscondition);
			$sql = 'select * from ' . tablename('ewei_shop_order') . ' where uniacid = :uniacid and ismr=0 and deleted=0 and isparent=0 ' . $status_condition . ' GROUP BY id ORDER BY id DESC  ';

			if (empty($_GPC['export'])) {
				$sql .= 'LIMIT ' . (($pindex - 1) * $psize) . ',' . $psize;
			}

			$list = pdo_fetchall($sql, $paras);

			if (!empty($list)) {
				$refundid = '';
				$openid = '';
				$addressid = '';
				$dispatchid = '';
				$verifyopenid = '';

				foreach ($list as $key => $value) {
					$refundid .= ',\'' . $value['refundid'] . '\'';
					$openid .= ',\'' . $value['openid'] . '\'';
					$addressid .= ',\'' . $value['addressid'] . '\'';
					$dispatchid .= ',\'' . $value['dispatchid'] . '\'';
					$verifyopenid .= ',\'' . $value['verifyopenid'] . '\'';
				}

				$refundid = ltrim($refundid, ',');
				$openid = ltrim($openid, ',');
				$addressid = ltrim($addressid, ',');
				$dispatchid = ltrim($dispatchid, ',');
				$verifyopenid = ltrim($verifyopenid, ',');
				$refundid_array = pdo_fetchall('SELECT id,rtype,status as rstatus FROM ' . tablename('ewei_shop_order_refund') . ' WHERE id IN (' . $refundid . ')', array(), 'id');
				$openid_array = pdo_fetchall('SELECT openid,nickname,id as mid,realname as mrealname,mobile as mmobile FROM ' . tablename('ewei_shop_member') . ' WHERE openid IN (' . $openid . ') AND uniacid=' . $_W['uniacid'], array(), 'openid');
				$addressid_array = pdo_fetchall('SELECT id,realname as arealname,mobile as amobile,province as aprovince ,city as acity , area as aarea,address as aaddress FROM ' . tablename('ewei_shop_member_address') . ' WHERE id IN (' . $addressid . ')', array(), 'id');
				$dispatchid_array = pdo_fetchall('SELECT id,dispatchname FROM ' . tablename('ewei_shop_dispatch') . ' WHERE id IN (' . $dispatchid . ')', array(), 'id');
				$verifyopenid_array = pdo_fetchall('SELECT sm.id as salerid,sm.nickname as salernickname,sm.openid,s.salername FROM ' . tablename('ewei_shop_saler') . ' s LEFT JOIN ' . tablename('ewei_shop_member') . ' sm ON sm.openid = s.openid and sm.uniacid=s.uniacid WHERE s.openid IN (' . $verifyopenid . ')', array(), 'openid');

				foreach ($list as $key => &$value) {
					$list[$key]['rtype'] = $refundid_array[$value['refundid']]['rtype'];
					$list[$key]['rstatus'] = $refundid_array[$value['refundid']]['rstatus'];
					$list[$key]['nickname'] = $openid_array[$value['openid']]['nickname'];
					$list[$key]['mid'] = $openid_array[$value['openid']]['mid'];
					$list[$key]['mrealname'] = $openid_array[$value['openid']]['mrealname'];
					$list[$key]['mmobile'] = $openid_array[$value['openid']]['mmobile'];
					$list[$key]['arealname'] = $addressid_array[$value['addressid']]['arealname'];
					$list[$key]['amobile'] = $addressid_array[$value['addressid']]['amobile'];
					$list[$key]['aprovince'] = $addressid_array[$value['addressid']]['aprovince'];
					$list[$key]['acity'] = $addressid_array[$value['addressid']]['acity'];
					$list[$key]['aarea'] = $addressid_array[$value['addressid']]['aarea'];
					$list[$key]['astreet'] = $addressid_array[$value['addressid']]['astreet'];
					$list[$key]['aaddress'] = $addressid_array[$value['addressid']]['aaddress'];
					$list[$key]['dispatchname'] = $dispatchid_array[$value['dispatchid']]['dispatchname'];
					$list[$key]['salerid'] = $verifyopenid_array[$value['verifyopenid']]['salerid'];
					$list[$key]['salernickname'] = $verifyopenid_array[$value['verifyopenid']]['salernickname'];
					$list[$key]['salername'] = $verifyopenid_array[$value['verifyopenid']]['salername'];
				}

				unset($value);
			}
		}
		// echo '<pre>';
		// var_dump($list);exit;
		// if ($_SERVER['REMOTE_ADDR']=='219.136.95.100') {
		// 	var_dump($sql);die;
		// }
		$paytype = array(
			0  => array('css' => 'default', 'name' => '未支付'),
			1  => array('css' => 'danger', 'name' => '余额支付'),
			11 => array('css' => 'default', 'name' => '后台付款'),
			2  => array('css' => 'danger', 'name' => '在线支付'),
			21 => array('css' => 'success', 'name' => '微信支付'),
			22 => array('css' => 'warning', 'name' => '支付宝支付'),
			23 => array('css' => 'warning', 'name' => '银联支付'),
			3  => array('css' => 'primary', 'name' => '货到付款')
			);
		$orderstatus = array(
			-1 => array('css' => 'default', 'name' => '已拒绝'),
			0  => array('css' => 'danger', 'name' => '待支付'),
			1  => array('css' => 'info', 'name' => '待发货'),
			2  => array('css' => 'warning', 'name' => '待收货'),
			3  => array('css' => 'success', 'name' => '已完成')
			);
		$is_merch = array();
		$is_merchname = 0;

		if ($merch_plugin) {
			$merch_user = $merch_plugin->getListUser($list, 'merch_user');

			if (!empty($merch_user)) {
				$is_merchname = 1;
			}
		}

		if (!empty($list)) {
			$diy_title_data = array();
			$diy_data = array();

			foreach ($list as &$value) {
				if ($is_merchname == 1) {
					$value['merchname'] = $merch_user[$value['merchid']]['merchname'] ? $merch_user[$value['merchid']]['merchname'] : '';
				}

				$s = $value['status'];
				$pt = $value['paytype'];
				$value['statusvalue'] = $s;
				$value['statuscss'] = $orderstatus[$value['status']]['css'];
				$value['status'] = $orderstatus[$value['status']]['name'];
				if (($pt == 3) && empty($value['statusvalue'])) {
					$value['statuscss'] = $orderstatus[1]['css'];
					$value['status'] = $orderstatus[1]['name'];
				}

				if ($s == 1) {
					if ($value['isverify'] == 1) {
						$value['status'] = '待使用';

						if (0 < $value['sendtype']) {
							$value['status'] = '部分使用';
						}
					}
					else if (empty($value['addressid'])) {
						$value['status'] = '待取货';
					}
					else {
						if (0 < $value['sendtype']) {
							$value['status'] = '部分发货';
						}
					}
				}

				if ($s == -1) {
					if (!empty($value['refundtime'])) {
						$value['status'] = '已退款';
					}
				}

				$value['paytypevalue'] = $pt;
				$value['css'] = $paytype[$pt]['css'];
				$value['paytype'] = $paytype[$pt]['name'];
				$value['dispatchname'] = empty($value['addressid']) ? '自提' : $value['dispatchname'];

				if (empty($value['dispatchname'])) {
					$value['dispatchname'] = '快递';
				}

				$isonlyverifygoods = m('order')->checkisonlyverifygoods($value['id']);

				if ($isonlyverifygoods) {
					$value['dispatchname'] = '记次/时商品';
				}

				if ($pt == 3) {
					$value['dispatchname'] = '货到付款';
				}
				else if ($value['isverify'] == 1) {
					$value['dispatchname'] = '线下核销';
				}
				else if ($value['isvirtual'] == 1) {
					$value['dispatchname'] = '虚拟物品';
				}
				else {
					if (!empty($value['virtual'])) {
						$value['dispatchname'] = '虚拟物品(卡密)<br/>自动发货';
					}
				}

				if (($value['dispatchtype'] == 1) || !empty($value['isverify']) || !empty($value['virtual']) || !empty($value['isvirtual'])) {
					$value['address'] = '';
					$carrier = iunserializer($value['carrier']);

					if (is_array($carrier)) {
						$value['addressdata']['realname'] = $value['realname'] = $carrier['carrier_realname'];
						$value['addressdata']['mobile'] = $value['mobile'] = $carrier['carrier_mobile'];
					}
				}
				else {
					$address = iunserializer($value['address']);
					$isarray = is_array($address);
					$value['realname'] = $isarray ? $address['realname'] : $value['arealname'];
					$value['mobile'] = $isarray ? $address['mobile'] : $value['amobile'];
					$value['province'] = $isarray ? $address['province'] : $value['aprovince'];
					$value['city'] = $isarray ? $address['city'] : $value['acity'];
					$value['area'] = $isarray ? $address['area'] : $value['aarea'];
					$value['street'] = $isarray ? $address['street'] : $value['astreet'];
					$value['address'] = $isarray ? $address['address'] : $value['aaddress'];
					$value['address_province'] = $value['province'];
					$value['address_city'] = $value['city'];
					$value['address_area'] = $value['area'];
					$value['address_street'] = $value['street'];
					$value['address_address'] = $value['address'];
					$value['address'] = $value['province'] . ' ' . $value['city'] . ' ' . $value['area'] . ' ' . $value['address'];
					$value['addressdata'] = array('realname' => $value['realname'], 'mobile' => $value['mobile'], 'address' => $value['address']);
				}

				$commission1 = -1;
				$commission2 = -1;
				$commission3 = -1;
				$m1 = false;
				$m2 = false;
				$m3 = false;
				if (!empty($level) && empty($agentid)) {
					if (!empty($value['agentid'])) {
						$m1 = m('member')->getMember($value['agentid']);
						$commission1 = 0;
						if (!empty($m1['agentid']) && (1 < $level)) {
							$m2 = m('member')->getMember($m1['agentid']);
							$commission2 = 0;
							if (!empty($m2['agentid']) && (2 < $level)) {
								$m3 = m('member')->getMember($m2['agentid']);
								$commission3 = 0;
							}
						}
					}
				}

				if (!empty($agentid)) {
					$magent = m('member')->getMember($agentid);
				}
				$order_goods = pdo_fetchall("select og.dingjin, g.id,g.title,g.thumb,g.goodssn,og.goodssn as option_goodssn, g.productsn,og.productsn as option_productsn, og.total,\r\n                    og.price,og.optionname as optiontitle, og.realprice,og.changeprice,og.oldprice,og.commission1,og.commission2,og.commission3,og.commissions,og.diyformdata,\r\n                    og.diyformfields,op.specs,g.merchid,og.seckill,og.seckill_taskid,og.seckill_roomid,g.ispresell from " . tablename('ewei_shop_order_goods') . ' og ' . ' left join ' . tablename('ewei_shop_goods') . ' g on g.id=og.goodsid ' . ' left join ' . tablename('ewei_shop_goods_option') . ' op on og.optionid = op.id ' . ' where og.uniacid=:uniacid and og.orderid=:orderid ', array(':uniacid' => $uniacid, ':orderid' => $value['id']));
				$goods = '';

				foreach ($order_goods as &$og) {
					$og['seckill_task'] = false;
					$og['seckill_room'] = false;

					if ($og['seckill']) {
						$og['seckill_task'] = plugin_run('seckill::getTaskInfo', $og['seckill_taskid']);
						$og['seckill_room'] = plugin_run('seckill::getRoomInfo', $og['seckill_taskid'], $og['seckill_roomid']);
					}

					if (!empty($og['specs'])) {
						$thumb = m('goods')->getSpecThumb($og['specs']);

						if (!empty($thumb)) {
							$og['thumb'] = $thumb;
						}
					}

					if (!empty($level) && empty($agentid)) {
						$commissions = iunserializer($og['commissions']);

						if (!empty($m1)) {
							if (is_array($commissions)) {
								$commission1 += (isset($commissions['level1']) ? floatval($commissions['level1']) : 0);
							}
							else {
								$c1 = iunserializer($og['commission1']);
								$l1 = $p->getLevel($m1['openid']);

								if (!empty($c1)) {
									$commission1 += (isset($c1['level' . $l1['id']]) ? $c1['level' . $l1['id']] : $c1['default']);
								}
							}
						}

						if (!empty($m2)) {
							if (is_array($commissions)) {
								$commission2 += (isset($commissions['level2']) ? floatval($commissions['level2']) : 0);
							}
							else {
								$c2 = iunserializer($og['commission2']);
								$l2 = $p->getLevel($m2['openid']);

								if (!empty($c2)) {
									$commission2 += (isset($c2['level' . $l2['id']]) ? $c2['level' . $l2['id']] : $c2['default']);
								}
							}
						}

						if (!empty($m3)) {
							if (is_array($commissions)) {
								$commission3 += (isset($commissions['level3']) ? floatval($commissions['level3']) : 0);
							}
							else {
								$c3 = iunserializer($og['commission3']);
								$l3 = $p->getLevel($m3['openid']);

								if (!empty($c3)) {
									$commission3 += (isset($c3['level' . $l3['id']]) ? $c3['level' . $l3['id']] : $c3['default']);
								}
							}
						}
					}

					$goods .= '' . $og['title'] . "\r\n";

					if (!empty($og['optiontitle'])) {
						$goods .= ' 规格: ' . $og['optiontitle'];
					}

					if (!empty($og['option_goodssn'])) {
						$og['goodssn'] = $og['option_goodssn'];
					}

					if (!empty($og['option_productsn'])) {
						$og['productsn'] = $og['option_productsn'];
					}

					if (!empty($og['goodssn'])) {
						$goods .= ' 商品编号: ' . $og['goodssn'];
					}

					if (!empty($og['productsn'])) {
						$goods .= ' 商品条码: ' . $og['productsn'];
					}

					$goods .= ' 单价: ' . ($og['price'] / $og['total']) . ' 折扣后: ' . ($og['realprice'] / $og['total']) . ' 数量: ' . $og['total'] . ' 总价: ' . $og['price'] . ' 折扣后: ' . $og['realprice'] . "\r\n ";
					if (p('diyform') && !empty($og['diyformfields']) && !empty($og['diyformdata'])) {
						$diyformdata_array = p('diyform')->getDatas(iunserializer($og['diyformfields']), iunserializer($og['diyformdata']), 1);
						$diyformdata = '';
						$dflag = 1;

						foreach ($diyformdata_array as $da) {
							if (!empty($diy_title_data)) {
								if (array_key_exists($da['key'], $diy_title_data)) {
									$dflag = 0;
								}
							}

							if ($dflag == 1) {
								$diy_title_data[$da['key']] = $da['name'];
							}

							$og['goods_' . $da['key']] = $da['value'];
							$diyformdata .= $da['name'] . ': ' . $da['value'] . " \r\n";
						}

						$og['goods_diyformdata'] = $diyformdata;
					}
				}

				unset($og);
				if (!empty($level) && empty($agentid)) {
					$value['commission1'] = $commission1;
					$value['commission2'] = $commission2;
					$value['commission3'] = $commission3;
				}

				$value['goods'] = set_medias($order_goods, 'thumb');
				$value['goods_str'] = $goods;
				if (!empty($agentid) && (0 < $level)) {
					$commission_level = 0;

					if ($value['agentid'] == $agentid) {
						$value['level'] = 1;
						$level1_commissions = pdo_fetchall('select commission1,commissions  from ' . tablename('ewei_shop_order_goods') . ' og ' . ' left join  ' . tablename('ewei_shop_order') . ' o on o.id = og.orderid ' . ' where og.orderid=:orderid and o.agentid= ' . $agentid . '  and o.uniacid=:uniacid', array(':orderid' => $value['id'], ':uniacid' => $uniacid));

						foreach ($level1_commissions as $c) {
							$commission = iunserializer($c['commission1']);
							$commissions = iunserializer($c['commissions']);

							if (empty($commissions)) {
								$commission_level += (isset($commission['level' . $agentLevel['id']]) ? $commission['level' . $agentLevel['id']] : $commission['default']);
							}
							else {
								$commission_level += (isset($commissions['level1']) ? floatval($commissions['level1']) : 0);
							}
						}
					}
					else if (in_array($value['agentid'], array_keys($agent['level1_agentids']))) {
						$value['level'] = 2;

						if (0 < $agent['level2']) {
							$level2_commissions = pdo_fetchall('select commission2,commissions  from ' . tablename('ewei_shop_order_goods') . ' og ' . ' left join  ' . tablename('ewei_shop_order') . ' o on o.id = og.orderid ' . ' where og.orderid=:orderid and  o.agentid in ( ' . implode(',', array_keys($agent['level1_agentids'])) . ')  and o.uniacid=:uniacid', array(':orderid' => $value['id'], ':uniacid' => $uniacid));

							foreach ($level2_commissions as $c) {
								$commission = iunserializer($c['commission2']);
								$commissions = iunserializer($c['commissions']);

								if (empty($commissions)) {
									$commission_level += (isset($commission['level' . $agentLevel['id']]) ? $commission['level' . $agentLevel['id']] : $commission['default']);
								}
								else {
									$commission_level += (isset($commissions['level2']) ? floatval($commissions['level2']) : 0);
								}
							}
						}
					}
					else {
						if (in_array($value['agentid'], array_keys($agent['level2_agentids']))) {
							$value['level'] = 3;

							if (0 < $agent['level3']) {
								$level3_commissions = pdo_fetchall('select commission3,commissions from ' . tablename('ewei_shop_order_goods') . ' og ' . ' left join  ' . tablename('ewei_shop_order') . ' o on o.id = og.orderid ' . ' where og.orderid=:orderid and  o.agentid in ( ' . implode(',', array_keys($agent['level2_agentids'])) . ')  and o.uniacid=:uniacid', array(':orderid' => $value['id'], ':uniacid' => $uniacid));

								foreach ($level3_commissions as $c) {
									$commission = iunserializer($c['commission3']);
									$commissions = iunserializer($c['commissions']);

									if (empty($commissions)) {
										$commission_level += (isset($commission['level' . $agentLevel['id']]) ? $commission['level' . $agentLevel['id']] : $commission['default']);
									}
									else {
										$commission_level += (isset($commissions['level3']) ? floatval($commissions['level3']) : 0);
									}
								}
							}
						}
					}

					$value['commission'] = $commission_level;
				}
			}
		}

		unset($value);
		set_time_limit(0);

		if ($_GPC['export'] == 1) {
			plog('order.op.export', '导出订单');
			$columns = array(
				array('title' => '订单编号', 'field' => 'ordersn', 'width' => 24),
				array('title' => '粉丝昵称', 'field' => 'nickname', 'width' => 12),
				array('title' => '会员姓名', 'field' => 'mrealname', 'width' => 12),
				array('title' => 'openid', 'field' => 'openid', 'width' => 24),
				array('title' => '会员手机手机号', 'field' => 'mmobile', 'width' => 12),
				array('title' => '收货姓名(或自提人)', 'field' => 'realname', 'width' => 12),
				array('title' => '联系电话', 'field' => 'mobile', 'width' => 12),
				array('title' => '收货地址', 'field' => 'address_province', 'width' => 12),
				array('title' => '', 'field' => 'address_city', 'width' => 12),
				array('title' => '', 'field' => 'address_area', 'width' => 12),
				array('title' => '', 'field' => 'address_street', 'width' => 12),
				array('title' => '', 'field' => 'address_address', 'width' => 12),
				array('title' => '商品名称', 'field' => 'goods_title', 'width' => 24),
				array('title' => '商品编码', 'field' => 'goods_goodssn', 'width' => 12),
				array('title' => '商品规格', 'field' => 'goods_optiontitle', 'width' => 12),
				array('title' => '商品数量', 'field' => 'goods_total', 'width' => 12),
				array('title' => '商品单价(折扣前)', 'field' => 'goods_price1', 'width' => 12),
				array('title' => '商品单价(折扣后)', 'field' => 'goods_price2', 'width' => 12),
				array('title' => '商品价格(折扣前)', 'field' => 'goods_rprice1', 'width' => 12),
				array('title' => '商品价格(折扣后)', 'field' => 'goods_rprice2', 'width' => 12),
				array('title' => '支付方式', 'field' => 'paytype', 'width' => 12),
				array('title' => '配送方式', 'field' => 'dispatchname', 'width' => 12),
				array('title' => '自提门店', 'field' => 'pickname', 'width' => 24),
				array('title' => '商品小计', 'field' => 'goodsprice', 'width' => 12),
				array('title' => '运费', 'field' => 'dispatchprice', 'width' => 12),
				array('title' => ''.$_W['shopset']['trade']['credittext'].'抵扣', 'field' => 'deductprice', 'width' => 12),
				array('title' => '余额抵扣', 'field' => 'deductcredit2', 'width' => 12),
				array('title' => '满额立减', 'field' => 'deductenough', 'width' => 12),
				array('title' => '优惠券优惠', 'field' => 'couponprice', 'width' => 12),
				array('title' => '订单改价', 'field' => 'changeprice', 'width' => 12),
				array('title' => '运费改价', 'field' => 'changedispatchprice', 'width' => 12),
				array('title' => '应收款', 'field' => 'price', 'width' => 12),
				array('title' => '状态', 'field' => 'status', 'width' => 12),
				array('title' => '下单时间', 'field' => 'createtime', 'width' => 24),
				array('title' => '付款时间', 'field' => 'paytime', 'width' => 24),
				array('title' => '发货时间', 'field' => 'sendtime', 'width' => 24),
				array('title' => '完成时间', 'field' => 'finishtime', 'width' => 24),
				array('title' => '快递公司', 'field' => 'expresscom', 'width' => 24),
				array('title' => '快递单号', 'field' => 'expresssn', 'width' => 24),
				array('title' => '订单备注', 'field' => 'remark', 'width' => 36),
				array('title' => '卖家订单备注', 'field' => 'remarksaler', 'width' => 36),
				array('title' => '核销员', 'field' => 'salerinfo', 'width' => 24),
				array('title' => '核销门店', 'field' => 'storeinfo', 'width' => 36),
				array('title' => '订单自定义信息', 'field' => 'order_diyformdata', 'width' => 36),
				array('title' => '商品自定义信息', 'field' => 'goods_diyformdata', 'width' => 36),
				array('title' => '是否支持货到付款(为1时表示支持货到付款)', 'field' => 'isCash', 'width' => 36),
				array('title' => '单个商品订金', 'field' => 'dingjin', 'width' => 36),
				array('title' => '已付款', 'field' => 'earnest', 'width' => 36),
				array('title' => '还需付款', 'field' => 'needmoney', 'width' => 36)
				);
			if (!empty($agentid) && (0 < $level)) {
				$columns[] = array('title' => '分销级别', 'field' => 'level', 'width' => 24);
				$columns[] = array('title' => '分销佣金', 'field' => 'commission', 'width' => 24);
			}

			if (!empty($diy_title_data)) {
				foreach ($diy_title_data as $key => $value) {
					$field = 'goods_' . $key;
					$columns[] = array('title' => $value . '(商品自定义信息)', 'field' => $field, 'width' => 24);
				}
			}

			if ($merch_plugin) {
				$columns[] = array('title' => '商户名称', 'field' => 'merchname', 'width' => 24);
			}
			foreach ($list as &$row) {
				$row['realname'] = str_replace('=', '', $row['realname']);
				$row['nickname'] = str_replace('=', '', $row['nickname']);
				$row['ordersn'] = $row['ordersn'] . ' ';				
				// $row['needmoney']=intval($row['price']-$row['earnest']). ' ';

				if (0 < $row['deductprice']) {
					$row['deductprice'] = '-' . $row['deductprice'];
				}

				if (0 < $row['deductcredit2']) {
					$row['deductcredit2'] = '-' . $row['deductcredit2'];
				}

				if (0 < $row['deductenough']) {
					$row['deductenough'] = '-' . $row['deductenough'];
				}

				if ($row['changeprice'] < 0) {
					$row['changeprice'] = '-' . $row['changeprice'];
				}
				else {
					if (0 < $row['changeprice']) {
						$row['changeprice'] = '+' . $row['changeprice'];
					}
				}

				if ($row['changedispatchprice'] < 0) {
					$row['changedispatchprice'] = '-' . $row['changedispatchprice'];
				}
				else {
					if (0 < $row['changedispatchprice']) {
						$row['changedispatchprice'] = '+' . $row['changedispatchprice'];
					}
				}

				if (0 < $row['couponprice']) {
					$row['couponprice'] = '-' . $row['couponprice'];
				}

				$row['nickname'] = strexists($row['nickname'], '^') ? '\'' . $row['nickname'] : $row['nickname'];
				$row['expresssn'] = $row['expresssn'] . ' ';
				$row['createtime'] = date('Y-m-d H:i:s', $row['createtime']);
				$row['paytime'] = !empty($row['paytime']) ? date('Y-m-d H:i:s', $row['paytime']) : '';
				$row['sendtime'] = !empty($row['sendtime']) ? date('Y-m-d H:i:s', $row['sendtime']) : '';
				$row['finishtime'] = !empty($row['finishtime']) ? date('Y-m-d H:i:s', $row['finishtime']) : '';
				$row['salerinfo'] = '';
				$row['storeinfo'] = '';
				$row['pickname'] = '';

				if (!empty($row['verifyopenid'])) {
					if (!empty($row['salerid']) || !empty($row['salername']) || !empty($row['salernickname'])) {
						$row['salerinfo'] = '[' . $row['salerid'] . ']' . $row['salername'] . '(' . $row['salernickname'] . ')';
					}
				}
				else {
					if (!empty($row['verifyinfo'])) {
						$verifyinfo = iunserializer($row['verifyinfo']);

						if (!empty($verifyinfo)) {
							foreach ($verifyinfo as $k => $v) {
								$verifyopenid = $v['verifyopenid'];

								if (!empty($verifyopenid)) {
									$verify_member = com('verify')->getSalerInfo($verifyopenid);
									$row['salerinfo'] .= '[' . $verify_member['salerid'] . ']' . $verify_member['salername'] . '(' . $verify_member['salernickname'] . ')';
								}
							}
						}
					}
				}

				if (!empty($row['verifystoreid'])) {
					if (0 < $row['merchid']) {
						$row['storeinfo'] = pdo_fetchcolumn('select storename from ' . tablename('ewei_shop_merch_store') . ' where id=:storeid limit 1 ', array(':storeid' => $row['verifystoreid']));
					}
					else {
						$row['storeinfo'] = pdo_fetchcolumn('select storename from ' . tablename('ewei_shop_store') . ' where id=:storeid limit 1 ', array(':storeid' => $row['verifystoreid']));
					}
				}
				else {
					if (!empty($row['verifyinfo'])) {
						$verifyinfo = iunserializer($row['verifyinfo']);

						if (!empty($verifyinfo)) {
							foreach ($verifyinfo as $k => $v) {
								$verifystoreid = $v['verifystoreid'];

								if (!empty($verifystoreid)) {
									$verify_store = com('verify')->getStoreInfo($verifystoreid);
									$row['storeinfo'] .= $verify_store['storename'];
								}
							}
						}
					}
				}

				if (!empty($row['storeid'])) {
					if (0 < $row['merchid']) {
						$row['pickname'] = pdo_fetchcolumn('select storename from ' . tablename('ewei_shop_merch_store') . ' where id=:storeid limit 1 ', array(':storeid' => $row['storeid']));
					}
					else {
						$row['pickname'] = pdo_fetchcolumn('select storename from ' . tablename('ewei_shop_store') . ' where id=:storeid limit 1 ', array(':storeid' => $row['storeid']));
					}
				}

				if (p('diyform') && !empty($row['diyformfields']) && !empty($row['diyformdata'])) {
					$diyformdata_array = p('diyform')->getDatas(iunserializer($row['diyformfields']), iunserializer($row['diyformdata']));
					$diyformdata = '';

					foreach ($diyformdata_array as $da) {
						$diyformdata .= $da['name'] . ': ' . $da['value'] . "\r\n";
					}

					$row['order_diyformdata'] = $diyformdata;
				}

				if ($row['isverify']) {
					if (is_array($verifyinfo)) {
						if (empty($row['dispatchtype'])) {
							$v = $verifyinfo[0];
							if ($v['verified'] || ($row['verifytype'] == 1)) {
								$v['storename'] = pdo_fetchcolumn('select storename from ' . tablename('ewei_shop_merch_store') . ' where id=:id limit 1', array(':id' => $v['verifystoreid']));

								if (empty($v['storename'])) {
									$v['storename'] = '总店';
								}

								$row['storeinfo'] = $v['storename'];
								$v['nickname'] = pdo_fetchcolumn('select nickname from ' . tablename('ewei_shop_member') . ' where openid=:openid and uniacid=:uniacid limit 1', array(':openid' => $v['verifyopenid'], ':uniacid' => $_W['uniacid']));
								$v['salername'] = pdo_fetchcolumn('select salername from ' . tablename('ewei_shop_merch_saler') . ' where openid=:openid and uniacid=:uniacid and merchid = :merchid limit 1', array(':openid' => $v['verifyopenid'], ':uniacid' => $_W['uniacid'], ':merchid' => $_W['merchid']));
								if (!empty($v['nickname']) || !empty($v['nickname'])) {
									$row['salerinfo'] = $v['salername'] . '(' . $v['nickname'] . ')';
								}
							}

							unset($v);
						}
					}
				}
			}

			unset($row);
			$exportlist = array();

			foreach ($list as &$r) {
				$ogoods = $r['goods'];
				unset($r['goods']);
				
				foreach ($ogoods as $k => $g) {
					$r['needmoney']=intval($r['price']-$r['earnest']);
					if (0 < $k) {
						$r['ordersn'] = '';
						$r['realname'] = '';
						$r['mobile'] = '';
						$r['openid'] = '';
						$r['nickname'] = '';
						$r['mrealname'] = '';
						$r['mmobile'] = '';
						$r['address'] = '';
						$r['address_province'] = '';
						$r['address_city'] = '';
						$r['address_area'] = '';
						$r['address_street'] = '';
						$r['address_address'] = '';
						$r['paytype'] = '';
						$r['dispatchname'] = '';
						$r['dispatchprice'] = '';
						$r['goodsprice'] = '';
						$r['earnest'] = '';
						$r['isCash'] = '';
						$r['status'] = '';
						$r['createtime'] = '';
						$r['sendtime'] = '';
						$r['finishtime'] = '';
						$r['expresscom'] = '';
						$r['expresssn'] = '';
						$r['deductprice'] = '';
						$r['deductcredit2'] = '';
						$r['deductenough'] = '';
						$r['changeprice'] = '';
						$r['changedispatchprice'] = '';
						$r['price'] = '';
						$r['needmoney']='';
						$r['order_diyformdata'] = '';
					}

					$r['goods_title'] = $g['title'];
					$r['goods_goodssn'] = $g['goodssn'];
					$r['goods_optiontitle'] = $g['optiontitle'];
					$r['goods_total'] = $g['total'];
					$r['goods_price1'] = $g['price'] / $g['total'];
					$r['goods_price2'] = $g['realprice'] / $g['total'];
					$r['goods_rprice1'] = $g['price'];
					$r['goods_rprice2'] = $g['realprice'];
					$r['goods_diyformdata'] = $g['goods_diyformdata'];					
					// $r['dingjin'] = $g['dingjin']*$g['total'];
					$r['dingjin'] = $g['dingjin'];

					foreach ($diy_title_data as $key => $value) {
						$field = 'goods_' . $key;
						$r[$field] = $g[$field];
					}

					$exportlist[] = $r;
				}
			}

			unset($r);
			m('excel')->export($exportlist, array('title' => '订单数据', 'columns' => $columns));
		}

		if (($condition != ' o.uniacid = :uniacid and o.ismr=0 and o.deleted=0 and o.isparent=0') || !empty($sqlcondition)) {
			$t = pdo_fetch('SELECT COUNT(DISTINCT(o.id)) as count, ifnull(sum(o.price),0) as sumprice   FROM ' . tablename('ewei_shop_order') . ' o ' . ' left join ' . tablename('ewei_shop_order_refund') . ' r on r.id =o.refundid ' . ' left join ' . tablename('ewei_shop_member') . ' m on m.openid=o.openid  and m.uniacid =  o.uniacid' . ' left join ' . tablename('ewei_shop_member_address') . ' a on o.addressid = a.id ' . ' left join ' . tablename('ewei_shop_saler') . ' s on s.openid = o.verifyopenid and s.uniacid=o.uniacid' . ' left join ' . tablename('ewei_shop_member') . ' sm on sm.openid = s.openid and sm.uniacid=s.uniacid' . ' ' . $sqlcondition . ' WHERE ' . $condition . ' ' . $statuscondition, $paras);
		}
		else {
			$t = pdo_fetch('SELECT COUNT(*) as count, ifnull(sum(price),0) as sumprice   FROM ' . tablename('ewei_shop_order') . ' WHERE uniacid = :uniacid and ismr=0 and deleted=0 and isparent=0 ' . $status_condition, $paras);
		}

		$total = $t['count'];
		$totalmoney = $t['sumprice'];
		$pager = pagination2($total, $pindex, $psize);
		$stores = pdo_fetchall('select id,storename from ' . tablename('ewei_shop_store') . ' where uniacid=:uniacid ', array(':uniacid' => $uniacid));
		$r_type = array('退款', '退货退款', '换货');
		
		load()->func('tpl');
		include $this->template('order/inventory');
	}

	public function main()
	{
		global $_W;
		global $_GPC;
		$orderData = $this->orderData('', 'main');
	}
	public function status()
	{
		global $_W;
		global $_GPC;
		$orderData = $this->orderData('', 'main');
	}
	public function status0()
	{
		global $_W;
		global $_GPC;
		$orderData = $this->orderData('0', 'main');
	}
	public function status1()
	{
		global $_W;
		global $_GPC;
		$orderData = $this->orderData('1', 'main');
	}
	public function status2()
	{
		global $_W;
		global $_GPC;
		$orderData = $this->orderData('2', 'main');
	}
	public function status3()
	{
		global $_W;
		global $_GPC;
		$orderData = $this->orderData('3', 'main');
	}
	public function status4()
	{
		global $_W;
		global $_GPC;
		$orderData = $this->orderData('-1', 'main');
	}

	public function detail()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		$p = p('commission');
		$item = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_order') . ' WHERE id = :id and uniacid=:uniacid', array(':id' => $id, ':uniacid' => $_W['uniacid']));
		$item['statusvalue'] = $item['status'];
		$item['paytypevalue'] = $item['paytype'];
		$isonlyverifygoods = m('order')->checkisonlyverifygoods($item['id']);
		$order_goods = array();

		if (0 < $item['sendtype']) {
			$order_goods = pdo_fetchall('SELECT orderid,goodsid,sendtype,expresssn,expresscom,express,sendtime FROM ' . tablename('ewei_shop_order_goods') . "\r\n            WHERE orderid = " . $id . ' and sendtime > 0 and uniacid=' . $_W['uniacid'] . ' and sendtype > 0 group by sendtype order by sendtime desc ');

			foreach ($order_goods as $key => $value) {
				$order_goods[$key]['goods'] = pdo_fetchall('select g.id,g.title,g.thumb,og.sendtype,g.ispresell,og.realprice from ' . tablename('ewei_shop_order_goods') . ' og ' . ' left join ' . tablename('ewei_shop_goods') . ' g on g.id=og.goodsid ' . ' where og.uniacid=:uniacid and og.orderid=:orderid and og.sendtype=' . $value['sendtype'] . ' ', array(':uniacid' => $_W['uniacid'], ':orderid' => $id));
			}

			$item['sendtime'] = $order_goods[0]['sendtime'];
		}

		$shopset = m('common')->getSysset('shop');

		if (empty($item)) {
			$this->message('抱歉，订单不存在!', referer(), 'error');
		}

		if ($_W['ispost']) {
			pdo_update('ewei_shop_order', array('remark' => trim($_GPC['remark'])), array('id' => $item['id'], 'uniacid' => $_W['uniacid']));
			plog('order.op.remarksaler', '订单保存备注  ID: ' . $item['id'] . ' 订单号: ' . $item['ordersn']);
			$this->message('订单备注保存成功！', webUrl('order', array('op' => 'detail', 'id' => $item['id'])), 'success');
		}

		$member = m('member')->getMember($item['openid']);
		$dispatch = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_dispatch') . ' WHERE id = :id and uniacid=:uniacid and merchid=0', array(':id' => $item['dispatchid'], ':uniacid' => $_W['uniacid']));


		if (empty($item['addressid'])) {
			$user = unserialize($item['carrier']);
		
		}
		else {

			if (!is_array($user)) {
				$user = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_member_address') . ' WHERE id = :id and uniacid=:uniacid', array(':id' => $item['addressid'], ':uniacid' => $_W['uniacid']));
			}
			$address_info = $user['address'];
			$user['address'] = $user['province'] . ' ' . $user['city'] . ' ' . $user['area'] . ' ' . $user['street'] . ' ' . $user['address'];
			$item['addressdata'] = array('realname' => $user['realname'], 'mobile' => $user['mobile'], 'address' => $user['address']);
		}

		$refund = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_order_refund') . ' WHERE orderid = :orderid and uniacid=:uniacid order by id desc', array(':orderid' => $item['id'], ':uniacid' => $_W['uniacid']));
		$diyformfields = '';
		$showdiyform = false;

		if (p('diyform')) {
			$diyformfields = ',o.diyformfields,o.diyformdata';
		}


		/*返利奖励start*/
		$thisPerm = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_perm_plugin') . ' WHERE acid =:acid limit 1', array(':acid' => $_W['uniacid']));

		if ($thisPerm) {
			$item_plugins=explode(',',$thisPerm['plugins']);
			if (in_array('rebate',$item_plugins)) {
				$rebateMode= true;
			}
		}
		if ($rebateMode) {
			$sql="SELECT * FROM ".tablename('ewei_shop_rebate_log')." WHERE uniacid=:uniacid AND orderid='{$_GPC[id]}'";
			$params = array(':uniacid'=>$_W['uniacid']);
			$rebateLog = pdo_fetch($sql,$params);
			$reward_data = unserialize($rebateLog['reward_data']);
		}
		/*返利奖励end*/

		$goods = pdo_fetchall('SELECT g.*, o.goodssn as option_goodssn, o.productsn as option_productsn,o.total,g.type,o.optionname,o.optionid,o.price as orderprice,o.realprice,o.changeprice,o.oldprice,o.commission1,o.commission2,o.commission3,o.commissions,o.seckill,o.seckill_taskid,o.seckill_roomid' . $diyformfields . ' FROM ' . tablename('ewei_shop_order_goods') . ' o left join ' . tablename('ewei_shop_goods') . ' g on o.goodsid=g.id ' . ' WHERE o.orderid=:orderid and o.uniacid=:uniacid', array(':orderid' => $id, ':uniacid' => $_W['uniacid']));
		$is_merch = false;

		foreach ($goods as &$r) {
			$r['seckill_task'] = false;

			if ($r['seckill']) {
				$r['seckill_task'] = plugin_run('seckill::getTaskInfo', $r['seckill_taskid']);
				$r['seckill_room'] = plugin_run('seckill::getRoomInfo', $r['seckill_taskid'], $r['seckill_roomid']);
			}

			if (!empty($r['option_goodssn'])) {
				$r['goodssn'] = $r['option_goodssn'];
			}

			if (!empty($r['option_productsn'])) {
				$r['productsn'] = $r['option_productsn'];
			}

			$r['marketprice'] = $r['orderprice'] / $r['total'];

			if (p('diyform')) {
				$r['diyformfields'] = iunserializer($r['diyformfields']);
				$r['diyformdata'] = iunserializer($r['diyformdata']);
			}

			if (!empty($r['merchid'])) {
				$is_merch = true;
			}

			if (!empty($r['diyformdata']) && ($r['diyformdata'] != 'false') && !$showdiyform) {
				$showdiyform = true;
			}
		}

		unset($r);
		$item['goods'] = $goods;
		$agents = array();

		if ($p) {
			$agents = $p->getAgents($id);
			$m1 = (isset($agents[0]) ? $agents[0] : false);
			$m2 = (isset($agents[1]) ? $agents[1] : false);
			$m3 = (isset($agents[2]) ? $agents[2] : false);
			$commission1 = 0;
			$commission2 = 0;
			$commission3 = 0;

			foreach ($goods as &$og) {
				$oc1 = 0;
				$oc2 = 0;
				$oc3 = 0;
				$commissions = iunserializer($og['commissions']);

				if (!empty($m1)) {
					if (is_array($commissions)) {
						$oc1 = (isset($commissions['level1']) ? floatval($commissions['level1']) : 0);
					}
					else {
						$c1 = iunserializer($og['commission1']);
						$l1 = $p->getLevel($m1['openid']);
						$oc1 = (isset($c1['level' . $l1['id']]) ? $c1['level' . $l1['id']] : $c1['default']);
					}

					$og['oc1'] = $oc1;
					$commission1 += $oc1;
				}

				if (!empty($m2)) {
					if (is_array($commissions)) {
						$oc2 = (isset($commissions['level2']) ? floatval($commissions['level2']) : 0);
					}
					else {
						$c2 = iunserializer($og['commission2']);
						$l2 = $p->getLevel($m2['openid']);
						$oc2 = (isset($c2['level' . $l2['id']]) ? $c2['level' . $l2['id']] : $c2['default']);
					}

					$og['oc2'] = $oc2;
					$commission2 += $oc2;
				}

				if (!empty($m3)) {
					if (is_array($commissions)) {
						$oc3 = (isset($commissions['level3']) ? floatval($commissions['level3']) : 0);
					}
					else {
						$c3 = iunserializer($og['commission3']);
						$l3 = $p->getLevel($m3['openid']);
						$oc3 = (isset($c3['level' . $l3['id']]) ? $c3['level' . $l3['id']] : $c3['default']);
					}

					$og['oc3'] = $oc3;
					$commission3 += $oc3;
				}
			}

			unset($og);
			$commission_array = array($commission1, $commission2, $commission3);

			foreach ($agents as $key => $value) {
				$agents[$key]['commission'] = $commission_array[$key];

				if (2 < $key) {
					unset($agents[$key]);
				}
			}
		}

		$condition = ' o.uniacid=:uniacid and o.deleted=0';
		$paras = array(':uniacid' => $_W['uniacid']);
		$totals = array();
		$coupon = false;
		if (com('coupon') && !empty($item['couponid'])) {
			$coupon = com('coupon')->getCouponByDataID($item['couponid']);
		}

		$order_fields = false;
		$order_data = false;

		if (p('diyform')) {
			$diyform_set = p('diyform')->getSet();

			foreach ($goods as $g) {
				if (!empty($g['diyformdata'])) {
					break;
				}
			}

			if (!empty($item['diyformid'])) {
				$orderdiyformid = $item['diyformid'];

				if (!empty($orderdiyformid)) {
					$order_fields = iunserializer($item['diyformfields']);
					$order_data = iunserializer($item['diyformdata']);
				}
			}
		}

		if (com('verify')) {
			$verifyinfo = iunserializer($item['verifyinfo']);

			if (!empty($item['verifyopenid'])) {
				$saler = m('member')->getMember($item['verifyopenid']);

				if (empty($item['merchid'])) {
					$saler['salername'] = pdo_fetchcolumn('select salername from ' . tablename('ewei_shop_saler') . ' where openid=:openid and uniacid=:uniacid limit 1 ', array(':uniacid' => $_W['uniacid'], ':openid' => $item['verifyopenid']));
				}
				else {
					$saler['salername'] = pdo_fetchcolumn('select salername from ' . tablename('ewei_shop_merch_saler') . ' where openid=:openid and merchid=:merchid and uniacid=:uniacid limit 1 ', array(':uniacid' => $_W['uniacid'], ':openid' => $item['verifyopenid'], ':merchid' => $item['merchid']));
				}
			}

			if (!empty($item['verifystoreid'])) {
				if (empty($item['merchid'])) {
					$store = pdo_fetch('select * from ' . tablename('ewei_shop_store') . ' where id=:storeid limit 1 ', array(':storeid' => $item['verifystoreid']));
				}else {
					$store = pdo_fetch('select * from ' . tablename('ewei_shop_merch_store') . ' where id=:storeid limit 1 ', array(':storeid' => $item['verifystoreid']));
				}
			}
			
			if ($item['isverify']) {
				if (is_array($verifyinfo)) {
					if (empty($item['dispatchtype'])) {
						foreach ($verifyinfo as &$v) {
							if ($v['verified'] || ($item['verifytype'] == 1)) {
								if (empty($item['merchid'])) {
									$v['storename'] = pdo_fetchcolumn('select storename from ' . tablename('ewei_shop_store') . ' where id=:id limit 1', array(':id' => $v['verifystoreid']));
								}
								else {
									$v['storename'] = pdo_fetchcolumn('select storename from ' . tablename('ewei_shop_merch_store') . ' where id=:id limit 1', array(':id' => $v['verifystoreid']));
								}

								if (empty($v['storename'])) {
									$v['storename'] = '总店';
								}

								$v['nickname'] = pdo_fetchcolumn('select nickname from ' . tablename('ewei_shop_member') . ' where openid=:openid and uniacid=:uniacid limit 1', array(':openid' => $v['verifyopenid'], ':uniacid' => $_W['uniacid']));

								if (empty($item['merchid'])) {
									$v['salername'] = pdo_fetchcolumn('select salername from ' . tablename('ewei_shop_saler') . ' where openid=:openid and uniacid=:uniacid limit 1', array(':openid' => $v['verifyopenid'], ':uniacid' => $_W['uniacid']));
								}
								else {
									$v['salername'] = pdo_fetchcolumn('select salername from ' . tablename('ewei_shop_merch_saler') . ' where openid=:openid and merchid=:merchid and uniacid=:uniacid limit 1', array(':openid' => $v['verifyopenid'], ':uniacid' => $_W['uniacid'], ':merchid' => $item['merchid']));
								}
							}
						}

						unset($v);
					}
				}
			}
		}
		if($p){
			$cSet = $p->getSet();
		}
		if (!empty($item['storeid'])) {
				if (empty($item['merchid'])) {
					$storePlan = pdo_fetch('select * from ' . tablename('ewei_shop_store') . ' where id=:storeid limit 1 ', array(':storeid' => $item['storeid']));
				}else {
					$storePlan = pdo_fetch('select * from ' . tablename('ewei_shop_merch_store') . ' where id=:storeid limit 1 ', array(':storeid' => $item['storeid']));
				}
		}
		load()->func('tpl');
		include $this->template();
		exit();
	}
	public function images()
	{
		global $_W,$_GPC;
		$id = intval($_GPC['id']);
		$row = pdo_fetch('select mm.upload_image,m.nickname,m.avatar,m.mobile from '.tablename('ewei_shop_member').' as m,'.tablename('ewei_shop_order').' as mm where mm.uniacid=:uniacid and m.uniacid=:uniacid and mm.openid=m.openid and mm.uniacid=m.uniacid and mm.id=:id',array(':id'=>$id,':uniacid'=>$_W['uniacid']));
		include $this->template('order/inventory/images');
	}
	public function check($id,$status)
	{
		global $_W,$_GPC;
		if(!empty($id)){
			$_GPC['status'] = $status;
			if($_GPC['status']==3){

				$list = pdo_fetch('select openid,id from '.tablename('ewei_shop_order').' where uniacid=:uniacid and id=:id',array(':uniacid'=>$_W['uniacid'],':id'=>$id));
				$goods = pdo_fetchall('select og.id,og.total,og.optionid,og.optionname,g.id as gid from '.tablename('ewei_shop_order_goods').' as og,'.tablename('ewei_shop_goods').' as g where og.goodsid=g.id and og.uniacid=:uniacid and g.uniacid=:uniacid and og.uniacid=g.uniacid and og.orderid=:id ',array(':uniacid'=>$_W['uniacid'],':id'=>$id));
				$num = 0;
				foreach($goods as $g){
					$num += $g['total'];
					if(!empty($g['optionid'])){
						$isset_goods = pdo_fetchcolumn('select count(*) from '.tablename('ewei_shop_member_inventory').' where uniacid=:uniacid and openid=:openid and goodsid=:gid and optionid=:optionid',array(':uniacid'=>$_W['uniacid'],':openid'=>$list['openid'],':gid'=>$g['gid'],':optionid'=>$g['optionid']));
						if($isset_goods>0){
							$sql = 'update '.tablename('ewei_shop_member_inventory').' set stock=stock+'.$g['total'].' where uniacid='.$_W['uniacid'].' and openid="'.$list['openid'].'" and goodsid='.$g['gid'].' and optionid='.$g['optionid'].' and pid>0 ';
							pdo_query($sql);
						}else{
							$data = array(
								'uniacid'=>$_W['uniacid'],
								'openid'=>$list['openid'],
								'goodsid'=>$g['gid'],
								'optionid'=>$g['optionid'],
								'stock'=>$g['total'],
								'pid'=>$g['gid']
							);
							pdo_insert('ewei_shop_member_inventory',$data);
						}

					}else{
						$isset_goods = pdo_fetchcolumn('select count(*) from '.tablename('ewei_shop_member_inventory').' where uniacid=:uniacid and openid=:openid and goodsid=:gid ',array(':uniacid'=>$_W['uniacid'],':openid'=>$list['openid'],':gid'=>$g['gid']));
						if($isset_goods>0){
							$sql = 'update '.tablename('ewei_shop_member_inventory').' set stock=stock+'.$g['total'].' where uniacid='.$_W['uniacid'].' and openid="'.$list['openid'].'" and goodsid='.$g['gid'].' and pid=0 ';
							pdo_query($sql);
						}else{
							$data = array(
								'uniacid'=>$_W['uniacid'],
								'openid'=>$list['openid'],
								'goodsid'=>$g['gid'],
								'stock'=>$g['total']
							);
							pdo_insert('ewei_shop_member_inventory',$data);
						}	
					}

				}
				$adata = array(
					'uniacid'=>$_W['uniacid'],
					'jopenid'=>$list['openid'],
					'orderid'=>$list['id'],
					'status'=>1,
					'total'=>$num,
					'createtime'=>time(),
					'remark'=>'后台审核通过,云商品库存总量+'.$num
				);
				pdo_insert('ewei_shop_member_inventory_record',$adata);
				pdo_update('ewei_shop_order',array('status'=>$_GPC['status'],'paytype'=>1),array('id'=>$id,'uniacid'=>$_W['uniacid']));

			}else{
				pdo_update('ewei_shop_order',array('status'=>$_GPC['status'],'paytype'=>1),array('id'=>$id,'uniacid'=>$_W['uniacid']));
				show_json(1);
			}
		}else{
			show_json(0);
		}
	}

	public function send()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$item = pdo_fetch('select * from '.tablename('ewei_shop_order').' where uniacid=:uniacid and id=:id ',array(':uniacid'=>$_W['uniacid'],':id'=>$id));

		$set = p('commission')->getSet();


		if (!empty($item['isprepay'])&&empty($item['isprepaysuccess'])) {
			show_json(0, '订单未结清尾款');
		}

		if (empty($item['addressid'])) {
			show_json(0, '无收货地址，无法发货！');
		}

		if ($item['paytype'] != 3) {
			if ($item['status'] != 1) {
				show_json(0, '订单未付款，无法发货！');
			}
		}

		if ($_W['ispost']) {
			if (!empty($_GPC['isexpress']) && empty($_GPC['expresssn'])) {
				show_json(0, '请输入快递单号！');
			}

			if (!empty($item['transid'])) {
			}

			$time = time();
			$data = array('sendtype' => 0 < $item['sendtype'] ? $item['sendtype'] : intval($_GPC['sendtype']), 'express' => trim($_GPC['express']), 'expresscom' => trim($_GPC['expresscom']), 'expresssn' => trim($_GPC['expresssn']), 'sendtime' => $time);
			if ((intval($_GPC['sendtype']) == 1) || (0 < $item['sendtype'])) {
				if (empty($_GPC['ordergoodsid'])) {
					show_json(0, '请选择发货商品！');
				}

				$ogoods = array();
				$ogoods = pdo_fetchall('select sendtype from ' . tablename('ewei_shop_order_goods') . "\r\n                    where orderid = " . $item['id'] . ' and uniacid = ' . $_W['uniacid'] . ' order by sendtype desc ');
				$senddata = array('sendtype' => $ogoods[0]['sendtype'] + 1, 'sendtime' => $data['sendtime']);
				$data['sendtype'] = $ogoods[0]['sendtype'] + 1;
				$goodsid = $_GPC['ordergoodsid'];

				foreach ($goodsid as $key => $value) {
					pdo_update('ewei_shop_order_goods', $data, array('id' => $value, 'uniacid' => $_W['uniacid']));
				}

				$send_goods = pdo_fetch('select * from ' . tablename('ewei_shop_order_goods') . "\r\n                    where orderid = " . $item['id'] . ' and sendtype = 0 and uniacid = ' . $_W['uniacid'] . ' limit 1 ');

				if (empty($send_goods)) {
					$senddata['status'] = 2;
					if ($set['commissionsettletype']==2) {
			 			p('commission')->payRebateManage($item['id'],$_W['uniacid'],$item['openid']);
			 		}
				}

				pdo_update('ewei_shop_order', $senddata, array('id' => $item['id'], 'uniacid' => $_W['uniacid']));
				
		 		

			}
			else {
				$data['status'] = 2;
				pdo_update('ewei_shop_order', $data, array('id' => $item['id'], 'uniacid' => $_W['uniacid']));
				
		 		if ($set['commissionsettletype']==2) {
		 			p('commission')->payRebateManage($item['id'],$_W['uniacid'],$item['openid']);
		 		}
			}

			if (!empty($item['refundid'])) {
				$refund = pdo_fetch('select * from ' . tablename('ewei_shop_order_refund') . ' where id=:id limit 1', array(':id' => $item['refundid']));

				if (!empty($refund)) {
					pdo_update('ewei_shop_order_refund', array('status' => -1, 'endtime' => $time), array('id' => $item['refundid']));
					pdo_update('ewei_shop_order', array('refundstate' => 0), array('id' => $item['id']));
				}
			}

			if ($item['paytype'] == 3) {
				m('order')->setStocksAndCredits($item['id'], 1);
			}

			/*发货时判断*/
			$thisPerm = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_perm_plugin') . ' WHERE acid =:acid limit 1', array(':acid' => $_W['uniacid']));

			if ($thisPerm) {
				$item_plugins=explode(',',$thisPerm['plugins']);
				if (in_array('rebate',$item_plugins)) {
					$rebateMode= true;
				}
			}

			$commissionSet = p('commission')->getSet();
			$uplevelmember = m('member')->getMember($item['agentid']);

			if ($rebateMode&&$commissionSet['commissionMode']==3) {
				
				$sql="SELECT l.*,j.goodsid,j.popenid FROM ".tablename('ewei_shop_rebate_log')."as l LEFT JOIN ".tablename('ewei_shop_rebate_join')." as j on j.id=l.join_id WHERE l.uniacid=:uniacid AND l.orderid='{$_GPC[id]}'";
				$params = array(':uniacid'=>$_W['uniacid']);
				$rebateLogList = pdo_fetchall($sql,$params);


				$alreadycommission=array();
				foreach ($rebateLogList as $rebateLog) {
					$popenid = $rebateLog['popenid'];
					$reward_data = unserialize($rebateLog['reward_data']);

					foreach ($reward_data as $key => $value) {

						foreach ($value as $kk => $vv) {
							
							if ($kk=='credit') {
								/*更新返利佣金信息*/
								$orderid  = $_GPC['id'];

								$ordergoods= pdo_fetch("SELECT commissions FROM ".tablename('ewei_shop_order_goods')." WHERE uniacid=:uniacid AND goodsid='{$rebateLog[goodsid]}' and orderid=:orderid",array(':uniacid' =>$_W['uniacid'],':orderid'=>$_GPC[id]));
					
							

								$alreadycommission = iunserializer($ordergoods['commissions']);

								$commissions = array('level1' => 0, 'level2' => 0, 'level3' => 0);

								$commissions['level1']=$alreadycommission['level1']?$alreadycommission['level1']+round($vv,2):round($vv,2);
							
								pdo_update('ewei_shop_order_goods',array('commissions' => iserializer($commissions)),array('orderid'=>$orderid,'uniacid'=>$_W['uniacid'],'goodsid'=>$rebateLog['goodsid']));
							}

							if ($kk=='integral') {
								/*积分返到积分账号中去*/
								$typestr = $_W['shopset']['trade']['credittext'];
								$num= floatval($vv);
								m('member')->setCredit($rebateLog['popenid'],'credit1',$num , array($_W['uid'], '任务返利' . $typestr));
								$profile=m('member')->getMember($rebateLog['popenid']);
								plog('order.op.send','任务返利' . $typestr . ': ' . $num . ' <br/>会员信息: ID: ' . $profile['id'] . ' /  ' . $profile['openid'] . '/' . $profile['nickname'] . '/' . $profile['realname'] . '/' . $profile['mobile']);
							}

							if ($kk=='goods') {


								$address = pdo_fetch('select * from ' . tablename('ewei_shop_member_address') . ' where  openid=:openid and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':openid' => $rebateLog['popenid']));

								$order_goods=$order=array();

								foreach ($vv as  $k => $v) {
						
									$sql="SELECT * FROM ".tablename('ewei_shop_goods')." WHERE uniacid=:uniacid AND id=:id ";
									$params= array(':uniacid'=>$_W['uniacid'],':id'=>$k);
									$goods = pdo_fetch($sql,$params);
									$ordersn = m('common')->createNO('order', 'ordersn', 'SH');

									$order['uniacid'] = $_W['uniacid'];
									$order['openid'] = $rebateLog['popenid'];
									$order['ordersn'] = $ordersn;
									$order['price'] = $goods['marketprice'];
									$order['oldprice'] = $goods['marketprice'];
									$order['status'] = 1;   //待发货
									$order['addressid'] = $address ? $address[id] : 0;
									$order['goodsprice'] = $goods[price];
									$order['createtime'] = time();
									if (!empty($address)) {
										$order['address'] = iserializer($address);
									}
									
									pdo_insert('ewei_shop_order', $order);
									$orderid = pdo_insertid();

									$order_goods['merchid'] = $goods['merchid'];
									$order_goods['merchsale'] = $goods['merchsale'];
									$order_goods['uniacid'] = $_W['uniacid'];
									$order_goods['orderid'] = $orderid;
									$order_goods['goodsid'] = $goods['id'];
									$order_goods['price'] = $goods['marketprice'];
									$order_goods['total'] =1;
									$order_goods['optionid'] = $goods['optionid'];
									$order_goods['createtime'] = time();
									$order_goods['optionname'] = $goods['optiontitle'];
									$order_goods['goodssn'] = $goods['goodssn'];
									$order_goods['productsn'] = $goods['productsn'];
									$order_goods['realprice'] = $goods['marketprice']*1;
									$exchangetitle .= $goods['title'];
						

									$order_goods['oldprice'] = $goods['ggprice'];

									if ($goods['discounttype'] == 1) {
										$order_goods['isdiscountprice'] = $goods['isdiscountprice'];
									}
									else {
										$order_goods['isdiscountprice'] = 0;
									}

									$order_goods['openid'] = $rebateLog['popenid'];

									pdo_insert('ewei_shop_order_goods', $order_goods);
									$order_goodsid = pdo_insertid();
								}

							}
							
						}
						
					}

					pdo_update('ewei_shop_rebate_log',array('status' =>1),array('uniacid'=>$_W['uniacid'],'orderid'=>$_GPC[id]));


					$sql="SELECT SUM(total) FROM ".tablename('ewei_shop_rebate_log')." WHERE  uniacid='{$_W[uniacid]}' AND join_id ='{$rebateLog[join_id]}' AND status=1";
					$total= pdo_fetchcolumn($sql);

					if ($total>=3) {
						/*给代理返利*/
						$sql="SELECT orderid FROM ".tablename('ewei_shop_rebate_log')." WHERE  uniacid='{$_W[uniacid]}' AND join_id ='{$rebateLog[join_id]}' AND status=1";
						$orderIdArr = pdo_fetchall($sql);
						$orderArr = array_column($orderIdArr,'orderid');

						/*当前订单信息*/
						$sql="SELECT * FROM ".tablename('ewei_shop_order')." WHERE uniacid='$_W[uniacid]' AND id='{$_GPC[id]}'";
						$orderinfo = pdo_fetch($sql);
						
						$pcommission=p('commission');
						pdo_update('ewei_shop_rebate_join',array('status'=>1),array('uniacid'=>$_W['uniacid'],'id'=>$rebateLog['join_id']));
						//c端完成任务升级
						$level = pdo_fetch('select * from '.tablename('ewei_shop_member_level').' where uniacid=:uniacid and level>90 order by level asc limit 1',array(':uniacid'=>$_W['uniacid']));
						if (!empty($commissionSet['c_to_agent'])) {
							set_time_limit(0);
							$cmember = m('member')->getMember($popenid);
							$agentdata = $pcommission->getAgentId($cmember['agentid'],$cmember);
							$agentdata['level'] = $level['id'];
							pdo_update('ewei_shop_member',$agentdata,array('id'=>$cmember['id']));
							$cmember['level'] = $level['id'];
							$teamlist = pdo_fetchall('select * from '.tablename('ewei_shop_member').' where uniacid=:uniacid and find_in_set('.$cmember.',fids)  order by fids asc',array(':uniacid'=>$_W['uniacid']));
							foreach ($teamlist as $k => $v) {
								$agentdata1 = $pcommission->getAgentId($v['agentid'],$v);
								pdo_update('ewei_shop_member',$agentdata1,array('id'=>$v['id']));
							}

						}
						
					}

	
				}
	
			}

			m('notice')->sendOrderMessage($item['id']);
			plog('order.op.send', '订单发货 ID: ' . $item['id'] . ' 订单号: ' . $item['ordersn'] . ' <br/>快递公司: ' . $_GPC['expresscom'] . ' 快递单号: ' . $_GPC['expresssn']);

			$this->check($id,3);

			show_json(1);
		}

		$noshipped = array();
		$shipped = array();

		if (0 < $item['sendtype']) {
			$noshipped = pdo_fetchall('select og.id,g.title,g.thumb,og.sendtype,g.ispresell from ' . tablename('ewei_shop_order_goods') . ' og ' . ' left join ' . tablename('ewei_shop_goods') . ' g on g.id=og.goodsid ' . ' where og.uniacid=:uniacid and og.sendtype = 0 and og.orderid=:orderid ', array(':uniacid' => $_W['uniacid'], ':orderid' => $item['id']));
			$i = 1;

			while ($i <= $item['sendtype']) {
				$shipped[$i]['sendtype'] = $i;
				$shipped[$i]['goods'] = pdo_fetchall('select g.id,g.title,g.thumb,og.sendtype,g.ispresell from ' . tablename('ewei_shop_order_goods') . ' og ' . ' left join ' . tablename('ewei_shop_goods') . ' g on g.id=og.goodsid ' . ' where og.uniacid=:uniacid and og.sendtype = ' . $i . ' and og.orderid=:orderid ', array(':uniacid' => $_W['uniacid'], ':orderid' => $item['id']));
				++$i;
			}
		}

		$order_goods = pdo_fetchall('select og.id,g.title,g.thumb,og.sendtype,g.ispresell from ' . tablename('ewei_shop_order_goods') . ' og ' . ' left join ' . tablename('ewei_shop_goods') . ' g on g.id=og.goodsid ' . ' where og.uniacid=:uniacid and og.orderid=:orderid ', array(':uniacid' => $_W['uniacid'], ':orderid' => $item['id']));

		$address = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_member_address') . ' WHERE id = :id and uniacid=:uniacid', array(':id' => $item['addressid'], ':uniacid' => $_W['uniacid']));

		$express_list = m('express')->getExpressList();
		include $this->template();
	}

	public function ajaxgettotals()
	{
		global $_GPC;
		$merch = intval($_GPC['merch']);
		$totals = m('order')->getTotals($merch);
		$result = (empty($totals) ? array() : $totals);
		show_json(1, $result);
	}

	public function updateChildOrderPay()
	{
		global $_W;
		$params = array();
		$params[':uniacid'] = $_W['uniacid'];
		$sql = 'select id,parentid from ' . tablename('ewei_shop_order') . ' where parentid>0 and status>0 and paytype=0 and uniacid=:uniacid';
		$list = pdo_fetchall($sql, $params);

		if (!empty($list)) {
			foreach ($list as $k => $v) {
				$params[':orderid'] = $v['parentid'];
				$sql1 = 'select paytype from ' . tablename('ewei_shop_order') . ' where id=:orderid and status>0 and paytype>0 and uniacid=:uniacid';
				$item = pdo_fetch($sql1, $params);

				if (0 < $item['paytype']) {
					pdo_update('ewei_shop_order', array('paytype' => $item['paytype']), array('id' => $v['id']));
				}
			}
		}
	}
}

?>
