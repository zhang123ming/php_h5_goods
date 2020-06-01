<?php
  
?>
<?php
if (!(defined('IN_IA'))) {
	exit('Access Denied');
}


require EWEI_SHOPV2_PLUGIN . 'app/core/page_mobile.php';
class Index_EweiShopV2Page extends AppMobilePage
{
	protected function merchData()
	{
		$merch_plugin = p('merch');
		$merch_data = m('common')->getPluginset('merch');
		if ($merch_plugin && $merch_data['is_openmerch']) {
			$is_openmerch = 1;
		}
		 else {
			$is_openmerch = 0;
		}

		return array('is_openmerch' => $is_openmerch, 'merch_plugin' => $merch_plugin, 'merch_data' => $merch_data);
	}

	public function get_list()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$openid = $_W['openid'];

		if (empty($openid)) {
			app_error(AppError::$ParamsError);
		}


		$pindex = max(1, intval($_GPC['page']));
		$psize = 10;
		$show_status = $_GPC['status'];
		$r_type = array('退款', '退货退款', '换货');
		$condition = ' and openid=:openid and ismr=0 and deleted=0 and uniacid=:uniacid ';
		$params = array(':uniacid' => $uniacid, ':openid' => $openid);
		$merchdata = $this->merchData();
		extract($merchdata);
		$condition .= ' and merchshow=0 ';

		if ($show_status != '') {
			$show_status = intval($show_status);

			switch ($show_status) {
			case 0:
				$condition .= ' and status=0 and paytype!=3';
				break;

			case 2:
				$condition .= ' and (status=2 or status=0 and paytype=3)';
				break;

			case 4:
				$condition .= ' and refundstate>0';
				break;

			case 5:
				$condition .= ' and userdeleted=1 ';
				break;

			default:
				$condition .= ' and status=' . intval($show_status);
			}

			if ($show_status != 5) {
				$condition .= ' and userdeleted=0 ';
			}

		}
		 else {
			$condition .= ' and userdeleted=0 ';
		}

		$com_verify = com('verify');
		$list = pdo_fetchall('select id,ordersn,price,invoice,invoiceprice,userdeleted,isparent,refundstate,paytype,status,addressid,refundid,isverify,dispatchtype,verifytype,verifyinfo,verifycode,iscomment,isCash,earnest,isprepay,balance,isprepaysuccess from ' . tablename('ewei_shop_order') . ' where 1 ' . $condition . ' order by createtime desc LIMIT ' . (($pindex - 1) * $psize) . ',' . $psize, $params);
		$total = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_order') . ' where 1 ' . $condition, $params);
		$refunddays = intval($_W['shopset']['trade']['refunddays']);

		if ($is_openmerch == 1) {
			$merch_user = $merch_plugin->getListUser($list, 'merch_user');
		}


		foreach ($list as &$row ) {
			$param = array();

			if ($row['isparent'] == 1) {
				$scondition = ' og.parentorderid=:parentorderid';
				$param[':parentorderid'] = $row['id'];
			}
			 else {
				$scondition = ' og.orderid=:orderid';
				$param[':orderid'] = $row['id'];
			}

			$sql = 'SELECT og.goodsid,og.total,g.title,g.thumb,og.price,og.optionname as optiontitle,og.optionid,op.specs,g.merchid,g.status FROM ' . tablename('ewei_shop_order_goods') . ' og ' . ' left join ' . tablename('ewei_shop_goods') . ' g on og.goodsid = g.id ' . ' left join ' . tablename('ewei_shop_goods_option') . ' op on og.optionid = op.id ' . ' where ' . $scondition . ' order by og.id asc';
			$goods = pdo_fetchall($sql, $param);
			$goods = set_medias($goods, array('thumb'));
			$ismerch = 0;
			$merch_array = array();
			$g = 0;
			$nog = 0;

			foreach ($goods as &$r ) {
				$merchid = $r['merchid'];
				$merch_array[$merchid] = $merchid;

				if (!(empty($r['specs']))) {
					$thumb = m('goods')->getSpecThumb($r['specs']);

					if (!(empty($thumb))) {
						$r['thumb'] = tomedia($thumb);
					}

				}


				if ($r['status'] == 2) {
					$row['gift'][$g] = $r;
					++$g;
				}
				 else {
					$row['nogift'][$nog] = $r;
					++$nog;
				}
			}

			unset($r);

			if (!(empty($merch_array))) {
				if (1 < count($merch_array)) {
					$ismerch = 1;
				}

			}


			if (empty($goods)) {
				$goods = array();
			}


			foreach ($goods as &$r ) {
				$r['thumb'] .= '?t=' . random(50);
			}

			unset($r);
			$goods_list = array();
			$i = 0;

			if ($ismerch) {
				$getListUser = $merch_plugin->getListUser($goods);
				$merch_user = $getListUser['merch_user'];

				foreach ($getListUser['merch'] as $k => $v ) {
					if (empty($merch_user[$k]['merchname'])) {
						$goods_list[$i]['shopname'] = $_W['shopset']['shop']['name'];
					}
					 else {
						$goods_list[$i]['shopname'] = $merch_user[$k]['merchname'];
					}

					$goods_list[$i]['goods'] = $v;
					++$i;
				}
			}
			 else {
				if ($merchid == 0) {
					$goods_list[$i]['shopname'] = $_W['shopset']['shop']['name'];
				}
				 else {
					$merch_data = $merch_plugin->getListUserOne($merchid);
					$goods_list[$i]['shopname'] = $merch_data['merchname'];
				}

				$goods_list[$i]['goods'] = $goods;
			}

			$row['goods'] = $goods_list;
			$statuscss = 'text-cancel';

			switch ($row['status']) {
			case '-1':
				$status = '已取消';
				break;

			case '0':
				if ($row['paytype'] == 3) {
					$status = '待发货';
				}
				 else {
					$status = '待付款';
				}

				$statuscss = 'text-cancel';
				break;

			case '1':
				if ($row['isverify'] == 1) {
					$status = '使用中';
				}
				 else if (empty($row['addressid'])) {
					$status = '待取货';
				}
				 else {
					$status = '待发货';
				}

				$statuscss = 'text-warning';
				break;

			case '2':
				$status = '待收货';
				$statuscss = 'text-danger';
				break;

			case '3':
				if (empty($row['iscomment'])) {
					if ($show_status == 5) {
						$status = '已完成';
					}
					 else {
						$status = ((empty($_W['shopset']['trade']['closecomment']) ? '待评价' : '已完成'));
					}
				}
				 else {
					$status = '交易完成';
				}

				$statuscss = 'text-success';
				break;
			}

			$row['statusstr'] = $status;
			$row['statuscss'] = $statuscss;

			if ((0 < $row['refundstate']) && !(empty($row['refundid']))) {
				$refund = pdo_fetch('select * from ' . tablename('ewei_shop_order_refund') . ' where id=:id and uniacid=:uniacid and orderid=:orderid limit 1', array(':id' => $row['refundid'], ':uniacid' => $uniacid, ':orderid' => $row['id']));

				if (!(empty($refund))) {
					$row['statusstr'] = '待' . $r_type[$refund['rtype']];
				}

			}


			$row['canverify'] = false;
			$canverify = false;

			if ($com_verify) {
				$showverify = $row['dispatchtype'] || $row['isverify'];

				if ($row['isverify']) {
					if (($row['verifytype'] == 0) || ($row['verifytype'] == 1)) {
						$vs = iunserializer($row['verifyinfo']);
						$verifyinfo = array(
							array('verifycode' => $row['verifycode'], 'verified' => ($row['verifytype'] == 0 ? $row['verified'] : $row['goods'][0]['total'] <= count($vs)))
							);

						if ($row['verifytype'] == 0) {
							$canverify = empty($row['verified']) && $showverify;
						}
						 else if ($row['verifytype'] == 1) {
							$canverify = (count($vs) < $row['goods'][0]['total']) && $showverify;
						}

					}
					 else {
						$verifyinfo = iunserializer($row['verifyinfo']);
						$last = 0;

						foreach ($verifyinfo as $v ) {
							if (!($v['verified'])) {
								++$last;
							}

						}

						$canverify = (0 < $last) && $showverify;
					}
				}
				 else if (!(empty($row['dispatchtype']))) {
					$canverify = ($row['status'] == 1) && $showverify;
				}

			}


			$row['canverify'] = $canverify;

			if ($is_openmerch == 1) {
				$row['merchname'] = (($merch_user[$row['merchid']]['merchname'] ? $merch_user[$row['merchid']]['merchname'] : $_W['shopset']['shop']['name']));
			}


			$row['cancancel'] = !($row['userdeleted']) && !($row['status']);
			$row['canpay'] = ($row['paytype'] != 3) && !($row['userdeleted']) && ($row['status'] == 0);
			$row['canpaybalance'] = ($row['paytype'] != 3) && !($row['userdeleted']) && ($row['status'] == 1)&& ($row['isprepay'] == 1)&&($row['isprepaysuccess'] == 0)&&(floatval($row['balance']));
			$row['canverify'] = $row['canverify'] && ($row['status'] != -1) && ($row['status'] != 0);
			$row['candelete'] = ($row['status'] == 3) || ($row['status'] == -1);
			$row['cancomment'] = ($row['status'] == 3) && ($row['iscomment'] == 0) && empty($_W['shopset']['trade']['closecomment']);
			$row['cancomment2'] = ($row['status'] == 3) && ($row['iscomment'] == 1) && empty($_W['shopset']['trade']['closecomment']);
			$row['cancomplete'] = $row['status'] == 2;
			$row['cancancelrefund'] = (0 < $row['refundstate']) && isset($refund) && ($refund['status'] != 5);
			$row['candelete2'] = $row['userdeleted'] == 1;
			$row['canrestore'] = $row['userdeleted'] == 1;
			$row['hasexpress'] = (1 < $row['status']) && (0 < $row['addressid']);
		}

		unset($row);
		app_json(array('list' => $list, 'pagesize' => $psize, 'total' => $total, 'page' => $pindex));
	}

	public function detail()
	{
		global $_W;
		global $_GPC;
		$openid = $_W['openid'];
		$uniacid = $_W['uniacid'];
		$orderid = intval($_GPC['id']);
		if (empty($orderid) || empty($openid)) {
			app_error(AppError::$ParamsError);
		}

		$member=m('member')->getMember($_W['openid']);
		$order = pdo_fetch('select * from ' . tablename('ewei_shop_order') . ' where id=:id and uniacid=:uniacid and openid=:openid limit 1', array(':id' => $orderid, ':uniacid' => $uniacid, ':openid' => $openid));

		if (empty($order)) {
			app_error(AppError::$OrderNotFound);
		}


		if ($order['merchshow'] == 1) {
			app_error(AppError::$OrderNotFound);
		}


		if ($order['userdeleted'] == 2) {
			app_error(AppError::$OrderNotFound);
		}


		$merchdata = $this->merchData();
		extract($merchdata);
		$merchid = $order['merchid'];
		$diyform_plugin = p('diyform');
		$diyformfields = '';

		if ($diyform_plugin) {
			$diyformfields = ',og.diyformfields,og.diyformdata';
		}


		$param = array();
		$param[':uniacid'] = $_W['uniacid'];

		if ($order['isparent'] == 1) {
			$scondition = ' og.parentorderid=:parentorderid';
			$param[':parentorderid'] = $orderid;
		}
		 else {
			$scondition = ' og.orderid=:orderid';
			$param[':orderid'] = $orderid;
		}

		$gift = array();
		$nogift = array();
		$gn = 0;
		$nog = 0;
		$goods = pdo_fetchall('select g.id,og.goodsid,og.price,g.title,g.thumb,g.status,og.total,g.credit,og.optionid,og.optionname as optiontitle,g.isverify,g.isfullback,g.taxation,g.storeids' . $diyformfields . '  from ' . tablename('ewei_shop_order_goods') . ' og ' . ' left join ' . tablename('ewei_shop_goods') . ' g on g.id=og.goodsid ' . ' where ' . $scondition . ' and og.uniacid=:uniacid ', $param);
		$goods = set_medias($goods, array('thumb'));

		$taxationcount = 0;
		if (!(empty($goods))) {
			foreach ($goods as &$g ) {
				if ($g['isfullback']) {
					$fullbackgoods = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_fullback_goods') . ' WHERE goodsid = :goodsid and uniacid = :uniacid  limit 1 ', array(':goodsid' => $g['goodsid'], ':uniacid' => $uniacid));

					if ($g['optionid']) {
						$option = pdo_fetch('select `day`,allfullbackprice,fullbackprice,allfullbackratio,fullbackratio,isfullback' . "\r\n" . '                      from ' . tablename('ewei_shop_goods_option') . ' where id = :id and uniacid = :uniacid ', array(':id' => $g['optionid'], ':uniacid' => $uniacid));
						$fullbackgoods['minallfullbackallprice'] = $option['allfullbackprice'];
						$fullbackgoods['fullbackprice'] = $option['fullbackprice'];
						$fullbackgoods['minallfullbackallratio'] = $option['allfullbackratio'];
						$fullbackgoods['fullbackratio'] = $option['fullbackratio'];
						$fullbackgoods['day'] = $option['day'];
					}


					$g['fullbackgoods'] = $fullbackgoods;
					unset($fullbackgoods, $option);
				}


				if (!(empty($g['optionid']))) {
					$thumb = m('goods')->getOptionThumb($g['goodsid'], $g['optionid']);

					if (!(empty($thumb))) {
						$g['thumb'] = $thumb;
					}

				}
				if(!empty($g['taxation'])){
					$taxationcount += $g['taxation']*$g['total'];
				}
			}
		}


		$diyform_flag = 0;

		if ($diyform_plugin) {
			foreach ($goods as &$g ) {
				$g['diyformfields'] = iunserializer($g['diyformfields']);
				$g['diyformdata'] = iunserializer($g['diyformdata']);
				unset($g);
			}

			if (!(empty($order['diyformfields'])) && !(empty($order['diyformdata']))) {
				$order_fields = iunserializer($order['diyformfields']);
				$order_data = iunserializer($order['diyformdata']);
			}

		}


		$address = false;

		if (!(empty($order['addressid']))) {
			$address = iunserializer($order['address']);

			if (!(is_array($address))) {
				$address = pdo_fetch('select * from  ' . tablename('ewei_shop_member_address') . ' where id=:id limit 1', array(':id' => $order['addressid']));
			}

		}


		$carrier = @iunserializer($order['carrier']);
		if (!(is_array($carrier)) || empty($carrier)) {
			$carrier = false;
		}


		$store = false;

		if (!(empty($order['storeid']))) {
			if (0 < $merchid) {
				$store = pdo_fetch('select * from  ' . tablename('ewei_shop_merch_store') . ' where id=:id limit 1', array(':id' => $order['storeid']));
			}
			 else {
				$store = pdo_fetch('select * from  ' . tablename('ewei_shop_store') . ' where id=:id limit 1', array(':id' => $order['storeid']));
			}
		}


		$stores = false;
		$showverify = false;
		$canverify = false;
		$verifyinfo = false;

		if (com('verify')) {
			$showverify = $order['dispatchtype'] || $order['isverify'];

			if ($order['isverify']) {
				$storeids = array();

				foreach ($goods as $g ) {
					if (!(empty($g['storeids']))) {
						$storeids = array_merge(explode(',', $g['storeids']), $storeids);
					}

				}

				if (empty($storeids)) {
					if (0 < $merchid) {
						$stores = pdo_fetchall('select * from ' . tablename('ewei_shop_merch_store') . ' where  uniacid=:uniacid and merchid=:merchid and status=1 and type in(2,3)', array(':uniacid' => $_W['uniacid'], ':merchid' => $merchid));
					}
					 else {
						$stores = pdo_fetchall('select * from ' . tablename('ewei_shop_store') . ' where  uniacid=:uniacid and status=1 and type in(2,3)', array(':uniacid' => $_W['uniacid']));
					}
				}
				 else if (0 < $merchid) {
					$stores = pdo_fetchall('select * from ' . tablename('ewei_shop_merch_store') . ' where id in (' . implode(',', $storeids) . ') and uniacid=:uniacid and merchid=:merchid and status=1 and type in(2,3)', array(':uniacid' => $_W['uniacid'], ':merchid' => $merchid));
				}
				 else {
					$stores = pdo_fetchall('select * from ' . tablename('ewei_shop_store') . ' where id in (' . implode(',', $storeids) . ') and uniacid=:uniacid and status=1 and type in(2,3)', array(':uniacid' => $_W['uniacid']));
				}

				if (($order['verifytype'] == 0) || ($order['verifytype'] == 1)) {
					$vs = iunserializer($order['verifyinfo']);
					$verifyinfo = array(
						array('verifycode' => $order['verifycode'], 'verified' => ($order['verifytype'] == 0 ? $order['verified'] : $goods[0]['total'] <= count($vs)))
						);

					if ($order['verifytype'] == 0) {
						$canverify = empty($order['verified']) && $showverify;
					}
					 else if ($order['verifytype'] == 1) {
						$canverify = (count($vs) < $goods[0]['total']) && $showverify;
					}

				}
				 else {
					$verifyinfo = iunserializer($order['verifyinfo']);
					$last = 0;

					foreach ($verifyinfo as $v ) {
						if (!($v['verified'])) {
							++$last;
						}

					}

					$canverify = (0 < $last) && $showverify;
				}
			}
			 else if (!(empty($order['dispatchtype']))) {
				$verifyinfo = array(
					array('verifycode' => $order['verifycode'], 'verified' => $order['status'] == 3)
					);
				$canverify = ($order['status'] == 1) && $showverify;
			}

		}

		if ($order['status']<3) {
			$verifyset=m('common')->getSysset('verify');
			if (!empty($verifyset['store']['salerTelHidden'])&&$store) {

				$store['mobile'] = substr_replace($store['mobile'], '****', 3, 4);
		
			}	
		}
		

		$order['canverify'] = $canverify;
		$order['showverify'] = $showverify;
		if (($order['status'] == 1) || ($order['status'] == 2)) {
			$canrefund = true;

			if (($order['status'] == 2) && ($order['price'] == $order['dispatchprice'])) {
				if (0 < $order['refundstate']) {
					$canrefund = true;
				}
				 else {
					$canrefund = false;
				}
			}

		}
		 else if ($order['status'] == 3) {
			if (($order['isverify'] != 1) && empty($order['virtual'])) {
				if (0 < $order['refundstate']) {
					$canrefund = true;
				}
				 else {
					$tradeset = m('common')->getSysset('trade');
					$refunddays = intval($tradeset['refunddays']);

					if (0 < $refunddays) {
						$days = intval((time() - $order['finishtime']) / 3600 / 24);

						if ($days <= $refunddays) {
							$canrefund = true;
						}

					}

				}
			}

		}


		$order['canrefund'] = $canrefund;
		$express = false;

		if ((2 <= $order['status']) && empty($order['isvirtual']) && empty($order['isverify'])) {
			$expresslist = m('util')->getExpressList($order['express'], $order['expresssn']);

			if (0 < count($expresslist)) {
				$express = $expresslist[0];
			}

		}


		$shopname = $_W['shopset']['shop']['name'];

		if (!(empty($order['merchid'])) && ($is_openmerch == 1)) {
			$merch_user = $merch_plugin->getListUser($order['merchid']);
			$shopname = $merch_user['merchname'];
			$shoplogo = tomedia($merch_user['logo']);
		}


		$order['statusstr'] = '';

		if (empty($order['status'])) {
			if ($order['paytype'] == 3) {
				$order['statusstr'] = '货到付款，等待发货';
			}
			 else {
				$order['statusstr'] = '等待付款';
			}
		}
		 else if ($order['status'] == 1) {
			$order['statusstr'] = '买家已付款';
		}
		 else if ($order['status'] == 2) {
			$order['statusstr'] = '卖家已发货';
		}
		 else if ($order['status'] == 3) {
			$order['statusstr'] = '交易完成';
		}
		 else if ($order['status'] == -1) {
			$order['statusstr'] = '交易关闭';
		}


		if (is_array($verifyinfo) && isset($verifyinfo)) {
			foreach ($verifyinfo as &$v ) {
				$status = '';

				if ($v['verified']) {
					$status = '已使用';
				}
				 else if ($order['dispatchtype']) {
					$status = '未取货';
				}
				 else if ($order['verifytype'] == 1) {
					$status = '剩余' . ($goods[0]['total'] - count($vs)) . '次';
				}
				 else {
					$status = '未使用';
				}

				$v['status'] = $status;
			}

			unset($v);
		}


		$newFields = array();
		if (is_array($order_fields) && !(empty($order_fields))) {
			foreach ($order_fields as $k => $v ) {
				$v['diy_type'] = $k;
				$newFields[] = $v;

				if (($v['data_type'] == 5) && !(empty($order_data[$k])) && is_array($order_data[$k])) {
					$order_data[$k] = set_medias($order_data[$k]);
				}

			}
		}


		if (!(empty($verifyinfo)) && empty($order['status'])) {
			foreach ($verifyinfo as &$lala ) {
				$lala['verifycode'] = '';
			}

			unset($lala);
		}


		$icon = '';

		if (empty($order['status'])) {
			if ($order['paytype'] == 3) {
				$icon = 'e623';
			}
			 else {
				$icon = 'e711';
			}
		}
		 else if ($order['status'] == 1) {
			$icon = 'e74c';
		}
		 else if ($order['status'] == 2) {
			$icon = 'e623';
		}
		 else if ($order['status'] == 3) {
			$icon = 'e601';
		}
		 else if ($order['status'] == -1) {
			$icon = 'e60e';
		}
		if ($order['isCash']==1) {
			$dingjin='';
			$dingjin=pdo_fetch('select sum(dingjin) as dingjintotal from'.tablename('ewei_shop_order_goods').'where uniacid=:uniacid and orderid=:orderid',array(':uniacid'=>$uniacid,':orderid'=>$orderid));

			$needmoney=$order['price']-$order['earnest'];
		}

		if($order['invoice']==1){
			$invoicemoney=$order['invoiceprice']-$order['price'];
		}

		$order = array('id' => $order['id'], 'ordersn' => $order['ordersn'], 'createtime' => date('Y-m-d H:i:s', $order['createtime']), 'paytime' => (!(empty($order['paytime'])) ? date('Y-m-d H:i:s', $order['paytime']) : ''), 'sendtime' => (!(empty($order['sendtime'])) ? date('Y-m-d H:i:s', $order['sendtime']) : ''), 'finishtime' => (!(empty($order['finishtime'])) ? date('Y-m-d H:i:s', $order['finishtime']) : ''), 'status' => $order['status'], 'statusstr' => $order['statusstr'], 'price' => $order['price'], 'earnest' => ($order['isCash'] == 1 ? $order['earnest'] : '0'),'dingjin' => ($order['isCash'] == 1 ? $dingjin['dingjintotal']: '0'),'invoiceprice' => ($order['invoice'] == 1 ? $order['invoiceprice']: ''),'invoice' => ($order['invoice'] == 1 ? $order['invoice']: ''),'invoicemoney' => ($order['invoice']==1? $invoicemoney :''),'needmoney' => ($order['isCash'] == 1 ? $needmoney: '0'), 'goodsprice' => $order['goodsprice'], 'dispatchprice' => $order['dispatchprice'], 'ispackage' => $order['ispackage'], 'deductenough' => $order['deductenough'], 'couponprice' => $order['couponprice'], 'discountprice' => $order['discountprice'], 'isdiscountprice' => $order['isdiscountprice'], 'deductprice' => $order['deductprice'], 'deductcredit2' => $order['deductcredit2'], 'diyformfields' => (empty($newFields) ? array() : $newFields), 'diyformdata' => (empty($order_data) ? array() : $order_data), 'showverify' => $order['showverify'], 'verifytitle' => ($order['dispatchtype'] ? '自提码' : '消费码'), 'dispatchtype' => $order['dispatchtype'], 'verifyinfo' => $verifyinfo, 'merchid' => intval($order['merchid']), 'virtual' => $order['virtual'], 'virtual_str' => ($order['status'] == 3 ? $order['virtual_str'] : ''), 'virtual_info' => ($order['status'] == 3 ? $order['virtual_info'] : ''), 'isvirtualsend' => $order['isvirtualsend'], 'virtualsend_info' => (empty($order['virtualsend_info']) ? '' : $order['virtualsend_info']), 'canrefund' => $order['canrefund'], 'refundtext' => (($order['status'] == 1 ? '申请退款' : '申请售后')) . ((!(empty($order['refundstate'])) ? '中' : '')), 'refundtext_btn' => '', 'cancancel' => !($order['userdeleted']) && !($order['status']), 'canpay' => ($order['paytype'] != 3) && !($order['userdeleted']) && ($order['status'] == 0), 'canverify' => $order['canverify'] && ($order['status'] != -1) && ($order['status'] != 0), 'candelete' => ($order['status'] == 3) || ($order['status'] == -1), 'cancomment' => ($order['status'] == 3) && ($order['iscomment'] == 0) && empty($_W['shopset']['trade']['closecomment']), 'cancomment2' => ($order['status'] == 3) && ($order['iscomment'] == 1) && empty($_W['shopset']['trade']['closecomment']), 'cancomplete' => $order['status'] == 2, 'cancancelrefund' => 0 < $order['refundstate'], 'candelete2' => $order['userdeleted'] == 1, 'canrestore' => $order['userdeleted'] == 1, 'verifytype' => $order['verifytype'], 'refundstate' => $order['refundstate'], 'icon' => $icon, 'city_express_state' => $order['city_express_state'],'isprepay'=>$order['isprepay'],'balance'=>$order['balance']);

		if ($order['canrefund']) {
			if (!(empty($order['refundstate']))) {
				$order['refundtext_btn'] = '查看';
			}


			if ($order['status'] == 1) {
				$order['refundtext_btn'] .= '申请退款';
			}
			 else {
				$order['refundtext_btn'] .= '申请售后';
			}

			if (!(empty($order['refundstate']))) {
				$order['refundtext_btn'] .= '进度';
			}

		}


		$allgoods = array();

		foreach ($goods as $g ) {
			$newFields = array();

			if (is_array($g['diyformfields'])) {
				foreach ($g['diyformfields'] as $k => $v ) {
					$v['diy_type'] = $k;
					$newFields[] = $v;
				}
			}


			$allgoods[] = array('id' => $g['goodsid'], 'title' => $g['title'], 'price' => $g['price'], 'thumb' => tomedia($g['thumb']), 'total' => $g['total'], 'isfullback' => $g['isfullback'], 'fullbackgoods' => $g['fullbackgoods'], 'status' => $g['status'], 'optionname' => $g['optiontitle'], 'diyformdata' => (empty($g['diyformdata']) ? array() : $g['diyformdata']), 'diyformfields' => $newFields);
		}

		if (!(empty($allgoods))) {
			foreach ($allgoods as $gk => $og ) {
				if ($og['status'] == 2) {
					$gift[$gn] = $og;
					++$gn;
				}
				 else {
					$nogift[$nog] = $og;
					++$nog;
				}
			}
		}


		$shop = array('name' => $shopname, 'logo' => $shoplogo);
		$result = array('order' => $order, 'goods' => $allgoods, 'gift' => $gift, 'nogift' => $nogift, 'address' => $address, 'express' => $express, 'carrier' => $carrier, 'store' => $store, 'stores' => $stores, 'shop' => $shop, 'customer' => intval($_W['shopset']['app']['customer']), 'phone' => intval($_W['shopset']['app']['phone']));

		if (!(empty($result['customer']))) {
			$result['customercolor'] = ((empty($_W['shopset']['app']['customercolor']) ? '#ff5555' : $_W['shopset']['app']['customercolor']));
		}
		$taxationcount = sprintf("%.2f",$taxationcount);
		$result['taxationcount'] = $taxationcount;

		if (!(empty($result['phone']))) {
			$result['phonecolor'] = ((empty($_W['shopset']['app']['phonecolor']) ? '#ff5555' : $_W['shopset']['app']['phonecolor']));
			$result['phonenumber'] = ((empty($_W['shopset']['app']['phonenumber']) ? '#ff5555' : $_W['shopset']['app']['phonenumber']));
		}


		if (!(empty($order['virtual'])) && !(empty($order['virtual_str']))) {
			if ($order['status'] == 3) {
				$result['ordervirtual'] = m('order')->getOrderVirtual($order);
			}
			 else {
				$result['ordervirtual'] = '';
			}

			$result['virtualtemp'] = pdo_fetch('SELECT linktext, linkurl FROM ' . tablename('ewei_shop_virtual_type') . ' WHERE id=:id AND uniacid=:uniacid LIMIT 1', array(':id' => $order['virtual'], ':uniacid' => $_W['uniacid']));
		}

		$result['store_id']=$member['store_id'];
		// echo "<pre>";
		// var_dump($result);die;

		app_json($result);
	}

	public function express()
	{
		global $_W;
		global $_GPC;
		$openid = $_W['openid'];
		$uniacid = $_W['uniacid'];
		$orderid = intval($_GPC['id']);

		if (empty($orderid)) {
			app_error(AppError::$OrderNotFound);
		}


		$order = pdo_fetch('select expresscom,expresssn,addressid,status,express from ' . tablename('ewei_shop_order') . ' where id=:id and uniacid=:uniacid and openid=:openid limit 1', array(':id' => $orderid, ':uniacid' => $uniacid, ':openid' => $openid));

		if (empty($order)) {
			app_error(AppError::$OrderNotFound);
		}


		if (empty($order['addressid'])) {
			app_error(AppError::$OrderNoExpress);
		}


		if ($order['status'] < 2) {
			app_error(AppError::$OrderNoExpress);
		}


		$goods = pdo_fetchall('select og.goodsid,og.price,g.title,g.thumb,og.total,g.credit,og.optionid,og.optionname as optiontitle,g.isverify,g.storeids  from ' . tablename('ewei_shop_order_goods') . ' og ' . ' left join ' . tablename('ewei_shop_goods') . ' g on g.id=og.goodsid ' . ' where og.orderid=:orderid and og.uniacid=:uniacid ', array(':uniacid' => $uniacid, ':orderid' => $orderid));
		$expresslist = m('util')->getExpressList($order['express'], $order['expresssn']);
		$status = '';

		if (!(empty($expresslist))) {
			if (strexists($expresslist[0]['step'], '已签收')) {
				$status = '已签收';
			}
			 else if (count($expresslist) <= 2) {
				$status = '备货中';
			}
			 else {
				$status = '配送中';
			}
		}


		app_json(array('com' => $order['expresscom'], 'sn' => $order['expresssn'], 'status' => $status, 'count' => count($goods), 'thumb' => tomedia($goods[0]['thumb']), 'expresslist' => $expresslist));
	}
	/**
	 * [getRentList 蓝牙设备租用列表]
	 * @Author   seanyang                 QQ：305492760
	 * @DateTime 2019-01-12T15:13:21+0800
	 * @return   [type]                   [description]
	 */
	public function getRentList(){
		global $_W,$_GPC;
		$uniacid = $_W['uniacid'];
		$openid = $_W['openid'];
		if (empty($openid)) {
			app_error(AppError::$ParamsError);
		}


		$pindex = max(1, intval($_GPC['page']));
		$psize = 10;
		$r_type = array('限期租用', '永久使用', '禁用');
		$condition = ' and openid=:openid and uniacid=:uniacid ';
		$params = array(':uniacid' => $uniacid, ':openid' => $openid);
		$merchdata = $this->merchData();
		extract($merchdata);

		$com_verify = com('verify');
		$list = pdo_fetchall('select id,macid,begintime,endtime,periodtime,status,orderid,goodsid,statusa from ' . tablename('ewei_shop_machine_rent_record') . ' where 1 ' . $condition . ' order by createtime desc LIMIT ' . (($pindex - 1) * $psize) . ',' . $psize, $params);
		$total = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_machine_rent_record') . ' where 1 ' . $condition, $params);
		foreach ($list as &$row ) {
			$param = array();
			$scondition = ' og.orderid=:orderid';
			$param[':orderid'] = $row['orderid'];
			

			$sql = 'SELECT og.goodsid,og.total,g.title,g.thumb,og.price,og.optionname as optiontitle,og.optionid,op.specs,g.merchid,g.status,g.renttype FROM ' . tablename('ewei_shop_order_goods') . ' og ' . ' left join ' . tablename('ewei_shop_goods') . ' g on og.goodsid = g.id ' . ' left join ' . tablename('ewei_shop_goods_option') . ' op on og.optionid = op.id ' . ' where ' . $scondition . ' order by og.id asc';
			$goods = pdo_fetchall($sql, $param);
			$goods = set_medias($goods, array('thumb'));
			$ismerch = 0;
			$merch_array = array();
			$g = 0;
			$nog = 0;

			foreach ($goods as &$r ) {
				$merchid = $r['merchid'];
				$merch_array[$merchid] = $merchid;

				if (!(empty($r['specs']))) {
					$thumb = m('goods')->getSpecThumb($r['specs']);

					if (!(empty($thumb))) {
						$r['thumb'] = tomedia($thumb);
					}

				}


				if ($r['status'] == 2) {
					$row['gift'][$g] = $r;
					++$g;
				}
				 else {
					$row['nogift'][$nog] = $r;
					++$nog;
				}
			}

			unset($r);

			if (!(empty($merch_array))) {
				if (1 < count($merch_array)) {
					$ismerch = 1;
				}

			}


			if (empty($goods)) {
				$goods = array();
			}


			foreach ($goods as &$r ) {
				$r['thumb'] .= '?t=' . random(50);
			}

			unset($r);
			$goods_list = array();
			$i = 0;

			if ($ismerch) {
				$getListUser = $merch_plugin->getListUser($goods);
				$merch_user = $getListUser['merch_user'];

				foreach ($getListUser['merch'] as $k => $v ) {
					if (empty($merch_user[$k]['merchname'])) {
						$goods_list[$i]['shopname'] = $_W['shopset']['shop']['name'];
					}
					 else {
						$goods_list[$i]['shopname'] = $merch_user[$k]['merchname'];
					}

					$goods_list[$i]['goods'] = $v;
					++$i;
				}
			}
			 else {
				if ($merchid == 0) {
					$goods_list[$i]['shopname'] = $_W['shopset']['shop']['name'];
				}
				 else {
					$merch_data = $merch_plugin->getListUserOne($merchid);
					$goods_list[$i]['shopname'] = $merch_data['merchname'];
				}

				$goods_list[$i]['goods'] = $goods;
			}

			$row['goods'] = $goods_list;
			$statuscss = 'text-cancel';

			switch ($row['status']) {
			case '-1':
				$status = '禁用';
				break;

			case '0':
					$status = '待绑定';
				$statuscss = 'text-cancel';
				break;

			case '1':
				$status = '永久使用';

				$statuscss = 'text-success';
				break;

			case '2':
				$status = '限期租用';
				$statuscss = 'text-success';
				break;
			}

			$row['statusstr'] = $status;
			$row['statuscss'] = $statuscss;



			if ($is_openmerch == 1) {
				$row['merchname'] = (($merch_user[$row['merchid']]['merchname'] ? $merch_user[$row['merchid']]['merchname'] : $_W['shopset']['shop']['name']));
			}
			$row['begintime'] = empty($row['begintime'])?'未激活':date('Y-m-d H:i:s',$row['begintime']);
			$row['endtime'] = empty($row['endtime'])?'未激活':date('Y-m-d H:i:s',$row['endtime']);
			if ($row['status']==1) {
				$row['endtime'] = '不限期';
				$row['periodtime'] = ' 不限期';
			}
		}

		unset($row);
		app_json(array('list' => $list, 'pagesize' => $psize, 'total' => $total, 'page' => $pindex));
	}
	public function rentDetail(){
		global $_GPC,$_W;
		$id = $_GPC['id'];
		$data['name'] = $_GPC['name'];
		$data['mobile'] = $_GPC['mobile'];
		$data['reason'] = $_GPC['reason'];
		$data['reply'] = $_GPC['reply'];
		if (empty($data['mobile'])) {
			$message = pdo_fetch('select message,statusa from '.tablename('ewei_shop_machine_rent_record').' where id=:id',array(':id'=>$id));
			$msg =empty($message['message'])? '': unserialize($message['message']);
			$msg['statusa'] = $message['statusa'];
			app_json(array('data'=>$msg));
		}else{
			pdo_update('ewei_shop_machine_rent_record',array('message'=>serialize($data)),array('id'=>$id));
			app_json(array('msg' => 'update success'));
		}
		
	}
	
		public function confirm_receipt() 
	{
		global $_W;
		global $_GPC;
		$p = p("commission");
		$pcoupon = com("coupon");
		$id = $_GPC["id"];
		$orderid = $_GPC["orderid"];
		if( empty($id) ) 
		{
			app_json(0, array( "message" => "参数错误" ));
		}
		if( empty($orderid) ) 
		{
			app_json(0, array( "message" => "参数错误" ));
		}
		$order = pdo_fetch("select * from " . tablename("ewei_shop_order") . " where uniacid=:uniacid and id=:id  limit 1", array( ":uniacid" => $_W["uniacid"], ":id" => $orderid ));
		$last_periods = pdo_fetch("select * from " . tablename("ewei_shop_cycelbuy_periods") . " where uniacid=:uniacid and orderid=:orderid order by id desc  limit 1", array( ":uniacid" => $_W["uniacid"], ":orderid" => $orderid ));
		if( !empty($last_periods) ) 
		{
			if( $last_periods["id"] == $id ) 
			{
				pdo_update("ewei_shop_order", array( "status" => 3, "finishtime" => time() ), array( "id" => $orderid, "status" => 2 ));
				$result = pdo_update("ewei_shop_cycelbuy_periods", array( "status" => 2, "finishtime" => time() ), array( "orderid" => $orderid, "uniacid" => $_W["uniacid"] ));
				m("member")->upgradeLevel($order["openid"], $orderid);
				m("order")->setGiveBalance($orderid, 1);
				m("notice")->sendOrderMessage($orderid);
				m("order")->fullback($orderid);
				m("order")->setStocksAndCredits($orderid, 3);
				if( $pcoupon ) 
				{
					com("coupon")->sendcouponsbytask($orderid);
					if( !empty($order["couponid"]) ) 
					{
						$pcoupon->backConsumeCoupon($orderid);
					}
				}
				if( $p ) 
				{
					$p->checkOrderFinish($orderid);
				}
			}
			else 
			{
				$result = pdo_update("ewei_shop_cycelbuy_periods", array( "status" => 2, "finishtime" => time() ), array( "id" => $id, "uniacid" => $_W["uniacid"] ));
			}
			if( !empty($result) ) 
			{
				app_json(1, array( "message" => "收货成功" ));
			}
			else 
			{
				app_json(0, array( "message" => "操作失败" ));
			}
		}
	}
	public function cycel_orderList() 
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W["uniacid"];
		$openid = $_W["openid"];
		if( empty($openid) ) 
		{
			app_error(AppError::$ParamsError);
		}
		$pindex = max(1, intval($_GPC["page"]));
		$psize = 10;
		$show_status = $_GPC["status"];
		$r_type = array( "退款", "退货退款", "换货" );
		$condition = " and openid=:openid and ismr=0 and deleted=0 and uniacid=:uniacid and iscycelbuy = 1";
		$params = array( ":uniacid" => $uniacid, ":openid" => $openid );
		$merchdata = $this->merchData();
		extract($merchdata);
		$condition .= " and merchshow=0 ";
		if( $show_status != "" ) 
		{
			$show_status = intval($show_status);
			switch( $show_status ) 
			{
				case 0: $condition .= " and status=0 and paytype!=3";
				break;
				case 2: $condition .= " and (status=2 or status=0 and paytype=3)";
				break;
				case 4: $condition .= " and refundstate>0";
				break;
				case 5: $condition .= " and userdeleted=1 ";
				break;
				default: $condition .= " and status=" . intval($show_status);
			}
			if( $show_status != 5 ) 
			{
				$condition .= " and userdeleted=0 ";
			}
		}
		else 
		{
			$condition .= " and userdeleted=0 ";
		}
		$com_verify = com("verify");
		$list = pdo_fetchall("select id,ordersn,price,userdeleted,isparent,refundstate,paytype,status,addressid,refundid,isverify,dispatchtype,verifytype,verifyinfo,verifycode,iscomment from " . tablename("ewei_shop_order") . " where 1 " . $condition . " order by createtime desc LIMIT " . ($pindex - 1) * $psize . "," . $psize, $params);
		$total = pdo_fetchcolumn("select count(*) from " . tablename("ewei_shop_order") . " where 1 " . $condition, $params);
		$refunddays = intval($_W["shopset"]["trade"]["refunddays"]);
		if( $is_openmerch == 1 ) 
		{
			$merch_user = $merch_plugin->getListUser($list, "merch_user");
		}
		foreach( $list as &$row ) 
		{
			$param = array( );
			if( $row["isparent"] == 1 ) 
			{
				$scondition = " og.parentorderid=:parentorderid";
				$param[":parentorderid"] = $row["id"];
			}
			else 
			{
				$scondition = " og.orderid=:orderid";
				$param[":orderid"] = $row["id"];
			}
			$sql = "SELECT og.goodsid,og.total,g.title,g.thumb,og.price,og.optionname as optiontitle,og.optionid,op.specs,g.merchid,g.status FROM " . tablename("ewei_shop_order_goods") . " og " . " left join " . tablename("ewei_shop_goods") . " g on og.goodsid = g.id " . " left join " . tablename("ewei_shop_goods_option") . " op on og.optionid = op.id " . " where " . $scondition . " order by og.id asc";
			$goods = pdo_fetchall($sql, $param);
			$goods = set_medias($goods, array( "thumb" ));
			$ismerch = 0;
			$merch_array = array( );
			$g = 0;
			$nog = 0;
			foreach( $goods as &$r ) 
			{
				$merchid = $r["merchid"];
				$merch_array[$merchid] = $merchid;
				if( !empty($r["specs"]) ) 
				{
					$thumb = m("goods")->getSpecThumb($r["specs"]);
					if( !empty($thumb) ) 
					{
						$r["thumb"] = tomedia($thumb);
					}
				}
				if( $r["status"] == 2 ) 
				{
					$row["gift"][$g] = $r;
					$g++;
				}
				else 
				{
					$row["nogift"][$nog] = $r;
					$nog++;
				}
			}
			unset($r);
			if( !empty($merch_array) && 1 < count($merch_array) ) 
			{
				$ismerch = 1;
			}
			if( empty($goods) ) 
			{
				$goods = array( );
			}
			foreach( $goods as &$r ) 
			{
				$r["thumb"] .= "?t=" . random(50);
			}
			unset($r);
			$goods_list = array( );
			$i = 0;
			if( $ismerch ) 
			{
				$getListUser = $merch_plugin->getListUser($goods);
				$merch_user = $getListUser["merch_user"];
				foreach( $getListUser["merch"] as $k => $v ) 
				{
					if( empty($merch_user[$k]["merchname"]) ) 
					{
						$goods_list[$i]["shopname"] = $_W["shopset"]["shop"]["name"];
					}
					else 
					{
						$goods_list[$i]["shopname"] = $merch_user[$k]["merchname"];
					}
					$goods_list[$i]["goods"] = $v;
					$i++;
				}
			}
			else 
			{
				if( $merchid == 0 ) 
				{
					$goods_list[$i]["shopname"] = $_W["shopset"]["shop"]["name"];
				}
				else 
				{
					$merch_data = $merch_plugin->getListUserOne($merchid);
					$goods_list[$i]["shopname"] = $merch_data["merchname"];
				}
				$goods_list[$i]["goods"] = $goods;
			}
			$row["goods"] = $goods_list;
			$statuscss = "text-cancel";
			switch( $row["status"] ) 
			{
				case "-1": $status = "已取消";
				break;
				case "0": if( $row["paytype"] == 3 ) 
				{
					$status = "待发货";
				}
				else 
				{
					$status = "待付款";
				}
				$statuscss = "text-cancel";
				break;
				case "1": if( $row["isverify"] == 1 ) 
				{
					$status = "使用中";
				}
				else 
				{
					if( empty($row["addressid"]) ) 
					{
						$status = "待取货";
					}
					else 
					{
						$status = "待发货";
					}
				}
				$statuscss = "text-warning";
				break;
				case "2": $status = "待收货";
				$statuscss = "text-danger";
				break;
				case "3": if( empty($row["iscomment"]) ) 
				{
					if( $show_status == 5 ) 
					{
						$status = "已完成";
					}
					else 
					{
						$status = (empty($_W["shopset"]["trade"]["closecomment"]) ? "待评价" : "已完成");
					}
				}
				else 
				{
					$status = "交易完成";
				}
				$statuscss = "text-success";
				break;
			}
			$row["statusstr"] = $status;
			$row["statuscss"] = $statuscss;
			if( 0 < $row["refundstate"] && !empty($row["refundid"]) ) 
			{
				$refund = pdo_fetch("select * from " . tablename("ewei_shop_order_refund") . " where id=:id and uniacid=:uniacid and orderid=:orderid limit 1", array( ":id" => $row["refundid"], ":uniacid" => $uniacid, ":orderid" => $row["id"] ));
				if( !empty($refund) ) 
				{
					$row["statusstr"] = "待" . $r_type[$refund["rtype"]];
				}
			}
			$row["canverify"] = false;
			$canverify = false;
			if( $com_verify ) 
			{
				$showverify = $row["dispatchtype"] || $row["isverify"];
				if( $row["isverify"] ) 
				{
					if( $row["verifytype"] == 0 || $row["verifytype"] == 1 ) 
					{
						$vs = iunserializer($row["verifyinfo"]);
						$verifyinfo = array( array( "verifycode" => $row["verifycode"], "verified" => ($row["verifytype"] == 0 ? $row["verified"] : $row["goods"][0]["total"] <= count($vs)) ) );
						if( $row["verifytype"] == 0 ) 
						{
							$canverify = empty($row["verified"]) && $showverify;
						}
						else 
						{
							if( $row["verifytype"] == 1 ) 
							{
								$canverify = count($vs) < $row["goods"][0]["total"] && $showverify;
							}
						}
					}
					else 
					{
						$verifyinfo = iunserializer($row["verifyinfo"]);
						$last = 0;
						foreach( $verifyinfo as $v ) 
						{
							if( !$v["verified"] ) 
							{
								$last++;
							}
						}
						$canverify = 0 < $last && $showverify;
					}
				}
				else 
				{
					if( !empty($row["dispatchtype"]) ) 
					{
						$canverify = $row["status"] == 1 && $showverify;
					}
				}
			}
			$row["canverify"] = $canverify;
			if( $is_openmerch == 1 ) 
			{
				$row["merchname"] = ($merch_user[$row["merchid"]]["merchname"] ? $merch_user[$row["merchid"]]["merchname"] : $_W["shopset"]["shop"]["name"]);
			}
			$row["cancancel"] = !$row["userdeleted"] && !$row["status"];
			$row["canpay"] = $row["paytype"] != 3 && !$row["userdeleted"] && $row["status"] == 0;
			$row["canverify"] = $row["canverify"] && $row["status"] != -1 && $row["status"] != 0;
			$row["candelete"] = $row["status"] == 3 || $row["status"] == -1;
			$row["cancomment"] = $row["status"] == 3 && $row["iscomment"] == 0 && empty($_W["shopset"]["trade"]["closecomment"]);
			$row["cancomment2"] = $row["status"] == 3 && $row["iscomment"] == 1 && empty($_W["shopset"]["trade"]["closecomment"]);
			$row["cancomplete"] = $row["status"] == 2;
			$row["cancancelrefund"] = 0 < $row["refundstate"] && isset($refund) && $refund["status"] != 5;
			$row["candelete2"] = $row["userdeleted"] == 1;
			$row["canrestore"] = $row["userdeleted"] == 1;
			$row["hasexpress"] = 1 < $row["status"] && 0 < $row["addressid"];
		}
		unset($row);
		app_json(array( "list" => $list, "pagesize" => $psize, "total" => $total, "page" => $pindex ));
	}
	public function express_number() 
	{
		global $_W;
		global $_GPC;
		$refundid = intval($_GPC["refundid"]);
		$uniacid = intval($_W["uniacid"]);
		if( $_GPC["submit"] ) 
		{
			if( empty($refundid) ) 
			{
				app_error(AppError::$ParamsError);
			}
			$refund = array( "status" => 4, "express" => trim($_GPC["express"]), "expresscom" => trim($_GPC["expresscom"]), "expresssn" => trim($_GPC["express_number"]), "sendtime" => time() );
			$res = pdo_update("ewei_shop_order_refund", $refund, array( "id" => $refundid, "uniacid" => $uniacid ));
			if( $res ) 
			{
				app_json();
			}
		}
		$sql = "SELECT * FROM " . tablename("ewei_shop_order_refund") . " WHERE id = :id AND uniacid = :uniacid";
		$params = array( ":id" => $refundid, ":uniacid" => $uniacid );
		$refund = pdo_fetch($sql, $params);
		$express_list = m("express")->getExpressList();
		$index = "";
		if( !empty($refund["express"]) ) 
		{
			foreach( $express_list as $k => $v ) 
			{
				if( $v["express"] == $refund["express"] ) 
				{
					$index = $k;
				}
			}
		}
		app_json(array( "express" => $refund["express"], "expresscom" => $refund["expresscom"], "express_number" => $refund["expresssn"], "express_list" => $express_list, "index" => $index ));
	}

}


?>