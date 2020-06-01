<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Select_EweiShopV2Page extends MobileLoginPage
{

	public function main()
	{
		global $_W;
		global $_GPC;
		$openid = $_W['openid'];
		$couponSet = $_W['shopset']['coupon'];

		$backurl=$_GPC['backurl']?$_GPC['backurl']:'';

		$liveid = $_GPC['liveid']?intval($_GPC['liveid']):0;
		
		$member = m('member')->getMember($openid, true);

		$merchinfo = pdo_fetch('select `id`,`logo`,`merchname` from ' . tablename('ewei_shop_merch_user') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $_GPC['merchid'], ':uniacid' => $_W['uniacid']));


		$goodsinfo = pdo_fetch('select * from ' . tablename('ewei_shop_goods') . ' where type = 2 and marketprice = 0 and totalcnf = 2  and uniacid=:uniacid and merchid = :merchid ', array(':uniacid' => $_W['uniacid'],':merchid'=>$_GPC['merchid']));

		$open_redis = function_exists('redis') && !is_error(redis());
		$seckillinfo = false;
		$uniacid = $_W['uniacid'];
		$openid = $_W['openid'];
		$goodsid = intval($goodsinfo['id']);
		$giftid = $_GPC['giftid']?intval($_GPC['giftid']):0;
		$giftGood = array();
		$sysset = m('common')->getSysset('trade');
		$invoiceset = m('common')->getSysset('invoice');
		$allow_sale = true;
		$area_set = m('util')->get_area_config_set();
		$new_area = intval($area_set['new_area']);
		$address_street = intval($area_set['address_street']);
		$packageid = $_GPC['packageid']?intval($_GPC['packageid']):0;
		
		if (!$packageid) {
			$merchdata = $this->merchData();
			extract($merchdata);
			$merch_array = array();
			$merchs = array();
			$merch_id = 0;
			$total_array = array();
			$member = m('member')->getMember($openid, true);
			$member['carrier_mobile'] = empty($member['carrier_mobile']) ? $member['mobile'] : $member['carrier_mobile'];
			$level = m('member')->getLevel($openid);
			$diyformdata = $this->diyformData($member);
			extract($diyformdata);
			$id = intval($goodsinfo['id']);
			$iswholesale = $_GPC['iswholesale']?intval($_GPC['iswholesale']):0;
			$bargain_id = $_GPC['bargainid']?intval($_GPC['bargainid']):0;
			$_SESSION['bargain_id'] = NULL;

			$optionid = $_GPC['optionid']?intval($_GPC['optionid']):0;
			$total = 1;

			if ($total < 1) {
				$total = 1;
			}

			$buytotal = $total;
			$errcode = 0;
			$isverify = false;
			$isvirtual = false;
			$isvirtualsend = false;
			$isonlyverifygoods = true;
			$changenum = false;
			$fromcart = 0;
			$hasinvoice = false;
			$invoicename = '';
			$buyagain_sale = true;
			$buyagainprice = 0;
			$goods = array();
			$hasinvoiceMoney=0;
			
			if (empty($id)) {

			}
			else {

				if (!empty($id) && !empty($iswholesale)) {
					$sql = 'SELECT id as goodsid,subtitle,type,title,weight,issendfree,isnodiscount,ispresell,presellprice,' . ' thumb,marketprice,storeids,isverify,deduct,hasoption,preselltimeend,presellsendstatrttime,presellsendtime,presellsendtype,' . ' manydeduct,`virtual`,maxbuy,usermaxbuy,discounts,total as stock,deduct2,g.coupondeduct,showlevels,' . ' ednum,edmoney,edareas,edareas_code,unite_total,' . ' diyformtype,diyformid,diymode,dispatchtype,dispatchid,dispatchprice,cates,minbuy, ' . ' isdiscount,isdiscount_time,isdiscount_discounts, ' . ' virtualsend,invoice,needfollow,followtip,followurl,merchid,checked,merchsale, ' . ' buyagain,buyagain_islong,buyagain_condition, buyagain_sale ,intervalprice ,intervalfloor ' . ' FROM ' . tablename('ewei_shop_goods') . ' where id=:id and uniacid=:uniacid  limit 1';
					$data = pdo_fetch($sql, array(':uniacid' => $uniacid, ':id' => $id));

					if (empty($data) || ($data['type'] != 4)) {
						$this->message('商品不存在!', '', 'error');
					}

					$intervalprice = iunserializer($data['intervalprice']);


					if (0 < $data['intervalfloor']) {
						$data['intervalprice1'] = $intervalprice[0]['intervalprice'];
						$data['intervalnum1'] = $intervalprice[0]['intervalnum'];
					}

					if (1 < $data['intervalfloor']) {
						$data['intervalprice2'] = $intervalprice[1]['intervalprice'];
						$data['intervalnum2'] = $intervalprice[1]['intervalnum'];
					}

					if (2 < $data['intervalfloor']) {
						$data['intervalprice3'] = $intervalprice[2]['intervalprice'];
						$data['intervalnum3'] = $intervalprice[2]['intervalnum'];
					}

					$buyoptions = $_GPC['buyoptions'];
					$optionsdata = json_decode(htmlspecialchars_decode($buyoptions, ENT_QUOTES), true);
					if (empty($optionsdata) || !is_array($optionsdata)) {
						$this->message('商品' . $data['title'] . '的规格不存在,请重新选择规格!', '', 'error');
					}

					$follow = m('user')->followed($openid);
					if (!empty($data['needfollow']) && !$follow && is_weixin()) {
						$followtip = (empty($goods['followtip']) ? '如果您想要购买此商品，需要您关注我们的公众号，点击【确定】关注后再来购买吧~' : $goods['followtip']);
						$followurl = (empty($goods['followurl']) ? $_W['shopset']['share']['followurl'] : $goods['followurl']);
						$this->message($followtip, $followurl, 'error');
					}

					$total = 0;

					foreach ($optionsdata as $option) {
						$good = $data;
						$num = intval($option['total']);

						if ($num <= 0) {
							continue;
						}

						$total = $total + $num;
						$good['total'] = $num;
						$good['optionid'] = $option['optionid'];

						if (0 < $option['optionid']) {
							$option = pdo_fetch('select id,title,marketprice,presellprice,goodssn,productsn,`virtual`,stock,weight,specs from ' . tablename('ewei_shop_goods_option') . ' where id=:id and goodsid=:goodsid and uniacid=:uniacid  limit 1', array(':uniacid' => $uniacid, ':goodsid' => $id, ':id' => $option['optionid']));

							if (!empty($option)) {
								$good['optiontitle'] = $option['title'];
								$good['virtual'] = $option['virtual'];

								if (empty($data['unite_total'])) {
									$data['stock'] = $option['stock'];

									if ($option['stock'] < $num) {
										$this->message('商品' . $data['title'] . '的购买数量超过库存剩余数量,请重新选择规格!', '', 'error');
									}
								}

								if (!empty($option['weight'])) {
									$data['weight'] = $option['weight'];
								}

								if (!empty($option['specs'])) {
									$thumb = m('goods')->getSpecThumb($option['specs']);

									if (!empty($thumb)) {
										$data['thumb'] = $thumb;
									}
								}
							}
							else {
								if (!empty($data['hasoption'])) {
									$this->message('商品' . $data['title'] . '的规格不存在,请重新选择规格!', '', 'error');
								}
							}
						}

						$goods[] = $good;
					}

					$goods = m('goods')->wholesaleprice($goods);


					foreach ($goods as $k => $v) {
						if ($v['type'] == 4) {
							$goods[$k]['marketprice'] = $v['wholesaleprice'];
						}
					}
				
				}
				else {
					
					$threensql = '';

					if (p('threen') && !empty($threenprice)) {
						$threensql .= ',threen';
					}

					$ishotelsql = '';

					if (p('hotelreservation')) {
						$ishotelsql .= ',ishotel';
					}

					$sql = 'SELECT id as goodsid,subtitle,type,title,weight,issendfree,isnodiscount,ispresell,presellprice,' . ' thumb,marketprice,liveprice,islive,storeids,isverify,deduct,hasoption,preselltimeend,presellsendstatrttime,presellsendtime,presellsendtype,' . ' manydeduct,`virtual`,maxbuy,usermaxbuy,discounts,total as stock,deduct2,coupondeduct,showlevels,' . ' ednum,edmoney,edareas,edareas_code,unite_total,' . ' diyformtype,diyformid,diymode,dispatchtype,dispatchid,dispatchprice,cates,minbuy, ' . ' isdiscount,isdiscount_time,isdiscount_discounts,isfullback, ' . ' virtualsend,invoice,needfollow,followtip,followurl,merchid,checked,merchsale, ' . ' buyagain,buyagain_islong,buyagain_condition, buyagain_sale' . $threensql . $ishotelsql . ' FROM ' . tablename('ewei_shop_goods') . ' where id=:id and uniacid=:uniacid  limit 1';
					$data = pdo_fetch($sql, array(':uniacid' => $uniacid, ':id' => $id));
					$threenprice = json_decode($data['threen'], 1);
					$data['marketprice'] = $_GPC['money'];
					$data['merchid'] = $_GPC['merchid'];
					$data['merchname'] = $merchinfo['merchname'];
					$data['merchlogo'] = $merchinfo['logo'];
					if ((0 < $data['ispresell']) && (($data['preselltimeend'] == 0) || (time() < $data['preselltimeend']))) {
						$data['marketprice'] = $data['presellprice'];
					}

					if (!empty($liveid)) {
						$isLiveGoods = p('live')->isLiveGoods($data['goodsid'], $liveid);

						if (!empty($isLiveGoods)) {
							$data['marketprice'] = price_format($isLiveGoods['liveprice']);
						}
					}

					if ($data['type'] == 4) {
						$this->message('商品信息错误!', '', 'error');
					}
					

					$data['seckillinfo'] = plugin_run('seckill::getSeckill', $data['goodsid'], $optionid, true, $_W['openid']);

					if ($data['seckillinfo'] && ($data['seckillinfo']['status'] == 0)) {
					}
					else {
						if ($giftid) {
							$gift = pdo_fetch('select id,title,thumb,activity,giftgoodsid,goodsid from ' . tablename('ewei_shop_gift') . "\r\n                where uniacid = " . $uniacid . ' and id = ' . $giftid . ' and status = 1 and starttime <= ' . time() . ' and endtime >= ' . time() . ' ');

							if (!strstr($gift['goodsid'], (string) $goodsid)) {
								$this->message('赠品与商品不匹配或者商品没有赠品!', '', 'error');
							}

							$giftGood = array();

							if (!empty($gift['giftgoodsid'])) {
								$giftGoodsid = explode(',', $gift['giftgoodsid']);

								if ($giftGoodsid) {
									foreach ($giftGoodsid as $key => $value) {
										$giftGood[$key] = pdo_fetch('select id,title,thumb,marketprice from ' . tablename('ewei_shop_goods') . ' where uniacid = ' . $uniacid . ' and total > 0 and status = 2 and id = ' . $value . ' and deleted = 0 ');
									}

									$giftGood = array_filter($giftGood);
								}
							}
						}
					}

					if (!empty($bargain_act)) {
						$data['marketprice'] = $bargain_act['now_price'];
					}

					$fullbackgoods = array();

					if ($data['isfullback']) {
						$fullbackgoods = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_fullback_goods') . ' WHERE uniacid = ' . $uniacid . ' and goodsid = ' . $data['goodsid'] . ' limit 1 ');
					}

					$follow = m('user')->followed($openid);
					if (!empty($data['needfollow']) && !$follow && is_weixin()) {
						$followtip = (empty($goods['followtip']) ? '如果您想要购买此商品，需要您关注我们的公众号，点击【确定】关注后再来购买吧~' : $goods['followtip']);
						$followurl = (empty($goods['followurl']) ? $_W['shopset']['share']['followurl'] : $goods['followurl']);
						$this->message($followtip, $followurl, 'error');
					}

					if ((0 < $data['minbuy']) && ($total < $data['minbuy'])) {
						$total = $data['minbuy'];
					}

					$data['total'] = 1;
					$data['optionid'] = $optionid;

					if (!empty($optionid)) {
						$option = pdo_fetch("select id,title,marketprice,liveprice,islive,presellprice,goodssn,productsn,`virtual`,stock,weight,specs,\r\n                    `day`,allfullbackprice,fullbackprice,allfullbackratio,fullbackratio,isfullback\r\n                    from " . tablename('ewei_shop_goods_option') . ' where id=:id and goodsid=:goodsid and uniacid=:uniacid  limit 1', array(':uniacid' => $uniacid, ':goodsid' => $id, ':id' => $optionid));

						if (!empty($option)) {
							$data['optionid'] = $optionid;
							$data['optiontitle'] = $option['title'];
							$data['marketprice'] = (0 < intval($data['ispresell'])) && ((time() < $data['preselltimeend']) || ($data['preselltimeend'] == 0)) ? $option['presellprice'] : $option['marketprice'];
							if ($isliving && !empty($option['islive']) && (0 < $option['liveprice'])) {
								$data['marketprice'] = $option['liveprice'];
							}

							if (!empty($liveid)) {
								$liveOption = p('live')->getLiveOptions($data['goodsid'], $liveid, array($option));
								if (!empty($liveOption) && !empty($liveOption[0])) {
									$data['marketprice'] = price_format($liveOption[0]['marketprice']);
								}
							}

							$data['virtual'] = $option['virtual'];

							if ($option['isfullback']) {
								$fullbackgoods['minallfullbackallprice'] = $option['allfullbackprice'];
								$fullbackgoods['fullbackprice'] = $option['fullbackprice'];
								$fullbackgoods['minallfullbackallratio'] = $option['allfullbackratio'];
								$fullbackgoods['fullbackratio'] = $option['fullbackratio'];
								$fullbackgoods['day'] = $option['day'];
							}

							if (empty($data['unite_total'])) {
								$data['stock'] = $option['stock'];
							}

							if (!empty($option['weight'])) {
								$data['weight'] = $option['weight'];
							}

							if (!empty($option['specs'])) {
								$thumb = m('goods')->getSpecThumb($option['specs']);

								if (!empty($thumb)) {
									$data['thumb'] = $thumb;
								}
							}
						}
						else {
							if (!empty($data['hasoption'])) {
								$this->message('商品' . $data['title'] . '的规格不存在,请重新选择规格!', '', 'error');
							}
						}
					}

					if ($giftid) {
						$changenum = false;
					}
					else {
						$changenum = true;
					}

					if ($data['seckillinfo'] && ($data['seckillinfo']['status'] == 0)) {
						$changenum = false;
					}

					$goods[] = $data;
				}
			}
			$goods = set_medias($goods, 'thumb');
			
			foreach ($goods as &$g) {

				if ($g['seckillinfo'] && ($g['seckillinfo']['status'] == 0)) {
					$g['is_task_goods'] = 0;
				}
				else {
					if (p('task')) {
						$task_id = intval($_SESSION[$id . '_task_id']);

						if (!empty($task_id)) {
							$rewarded = pdo_fetchcolumn('SELECT `rewarded` FROM ' . tablename('ewei_shop_task_extension_join') . ' WHERE id = :id AND openid = :openid AND  uniacid = :uniacid', array(':id' => $task_id, ':uniacid' => $_W['uniacid'], ':openid' => $_W['openid']));
							$taskGoodsInfo = unserialize($rewarded);
							$taskGoodsInfo = $taskGoodsInfo['goods'][$id];
							if (!empty($optionid) && !empty($taskGoodsInfo['option']) && ($optionid == $taskGoodsInfo['option'])) {
								$taskgoodsprice = $taskGoodsInfo['price'];
							}
							else {
								if (empty($optionid)) {
									$taskgoodsprice = $taskGoodsInfo['price'];
								}
							}
						}
					}

					$rank = intval($_SESSION[$id . '_rank']);
					$log_id = intval($_SESSION[$id . '_log_id']);
					$join_id = intval($_SESSION[$id . '_join_id']);
					$task_goods_data = m('goods')->getTaskGoods($openid, $id, $rank, $log_id, $join_id, $optionid);

					if (empty($task_goods_data['is_task_goods'])) {
						$g['is_task_goods'] = 0;
					}
					else {
						$allow_sale = false;
						$g['is_task_goods'] = $task_goods_data['is_task_goods'];
						$g['is_task_goods_option'] = $task_goods_data['is_task_goods_option'];
						$g['task_goods'] = $task_goods_data['task_goods'];
					}
				}

				if ($is_openmerch == 1) {
					$merchid = $g['merchid'];
					$merch_array[$merchid]['goods'][] = $g['goodsid'];
				}

				if ($g['isverify'] == 2) {
					$isverify = true;
				}

				if (!empty($g['virtual']) || ($g['type'] == 2) || ($g['type'] == 3) || ($g['type'] == 20)) {
					$isvirtual = true;

					if ($g['virtualsend']) {
						$isvirtualsend = true;
					}
				}

				if ($g['invoice']) {
					$hasinvoice = $g['invoice'];
					$hasinvoiceMoney +=($g['marketprice']*$g['total']);

				}

				if ($g['type'] != 5) {
					$isonlyverifygoods = false;
				}

				$totalmaxbuy = $g['stock'];
				if (!empty($g['seckillinfo']) && ($g['seckillinfo']['status'] == 0)) {
					$seckilllast = 0;

					if (0 < $g['seckillinfo']['maxbuy']) {
						$seckilllast = $g['seckillinfo']['maxbuy'] - $g['seckillinfo']['selfcount'];
					}

					$g['totalmaxbuy'] = $g['total'];
				}
				else {
					if (0 < $g['maxbuy']) {
						if ($totalmaxbuy != -1) {
							if ($g['maxbuy'] < $totalmaxbuy) {
								$totalmaxbuy = $g['maxbuy'];
							}
						}
						else {
							$totalmaxbuy = $g['maxbuy'];
						}
					}

					if (0 < $g['usermaxbuy']) {
						$order_goodscount = pdo_fetchcolumn('select ifnull(sum(og.total),0)  from ' . tablename('ewei_shop_order_goods') . ' og ' . ' left join ' . tablename('ewei_shop_order') . ' o on og.orderid=o.id ' . ' where og.goodsid=:goodsid and  o.status>=0 and o.openid=:openid  and og.uniacid=:uniacid ', array(':goodsid' => $g['goodsid'], ':uniacid' => $uniacid, ':openid' => $openid));
						$last = $data['usermaxbuy'] - $order_goodscount;

						if ($last <= 0) {
							$last = 0;
						}

						if ($totalmaxbuy != -1) {
							if ($last < $totalmaxbuy) {
								$totalmaxbuy = $last;
							}
						}
						else {
							$totalmaxbuy = $last;
						}
					}

					if (!empty($g['is_task_goods'])) {
						if ($g['task_goods']['total'] < $totalmaxbuy) {
							$totalmaxbuy = $g['task_goods']['total'];
						}
					}

					$g['totalmaxbuy'] = $totalmaxbuy;
					if (($g['totalmaxbuy'] < $g['total']) && !empty($g['totalmaxbuy'])) {
						$g['total'] = $g['totalmaxbuy'];
					}

					if ((0 < floatval($g['buyagain'])) && empty($g['buyagain_sale'])) {
						if (m('goods')->canBuyAgain($g)) {
							$buyagain_sale = false;
						}
					}
				}
			}

			unset($g);
		
			if ($hasinvoice) {
				$invoicename = pdo_fetchcolumn('select invoicename from ' . tablename('ewei_shop_order') . ' where openid=:openid and uniacid=:uniacid and ifnull(invoicename,\'\')<>\'\'', array(':openid' => $openid, ':uniacid' => $uniacid));

				if (empty($invoicename)) {
					$invoicename = $member['realname'];
				}
			}
			// 开启多商户

			if ($is_openmerch == 1) {
				foreach ($merch_array as $key => $value) {
					if (0 < $key) {
						$merch_id = $key;
						$merch_array[$key]['set'] = $merch_plugin->getSet('sale', $key);
						$merch_array[$key]['enoughs'] = $merch_plugin->getEnoughs($merch_array[$key]['set']);
					}
				}
			}
			
			$weight = 0;
			$total = 0;
			$goodsprice = 0;
			$realprice = 0;
			$deductprice = 0;
			$taskdiscountprice = 0;
			$lotterydiscountprice = 0;
			$discountprice = 0;
			$isdiscountprice = 0;
			$deductprice2 = 0;
			$coupondeduct = 0;
			$stores = array();
			$address = false;
			$carrier = false;
			$carrier_list = array();
			$dispatch_list = false;
			$dispatch_price = 0;
			$seckill_dispatchprice = 0;
			$seckill_price = 0;
			$seckill_payprice = 0;
			$ismerch = 0;

			if ($is_openmerch == 1) {
				if (!empty($merch_array)) {
					if (1 < count($merch_array)) {
						$ismerch = 1;
					}
				}
			}

			if (!$isverify && !$isvirtual && !$ismerch) {
				if (0 < $merch_id) {
					$carrier_list = pdo_fetchall('select * from ' . tablename('ewei_shop_merch_store') . ' where  uniacid=:uniacid and merchid=:merchid and status=1 and type in(1,3) order by displayorder desc,id desc', array(':uniacid' => $_W['uniacid'], ':merchid' => $merch_id));
				}
				else {
					$carrier_list = pdo_fetchall('select * from ' . tablename('ewei_shop_store') . ' where  uniacid=:uniacid and status=1 and type in(1,3) order by displayorder desc,id desc', array(':uniacid' => $_W['uniacid']));
				}
			}

			$sale_plugin = com('sale');
			$saleset = false;

			if ($sale_plugin && $buyagain_sale && $allow_sale) {
				$saleset = $_W['shopset']['sale'];
				$saleset['enoughs'] = $sale_plugin->getEnoughs();
			}

			foreach ($goods as &$g) {
				if (empty($g['total']) || (intval($g['total']) < 1)) {
					$g['total'] = 1;
				}

				if ($g['seckillinfo'] && ($g['seckillinfo']['status'] == 0)) {
					$gprice = $g['ggprice'] = $g['seckillinfo']['price'] * $g['total'];
					$seckill_payprice += $g['seckillinfo']['price'] * $g['total'];
					$seckill_price += ($g['marketprice'] * $g['total']) - $gprice;
				}
				else {
					$gprice = $g['marketprice'] * $g['total'];
					$prices = m('order')->getGoodsDiscountPrice($g, $level);
					$g['ggprice'] = $prices['price'];
					$g['unitprice'] = $prices['unitprice'];
				}

				if ($is_openmerch == 1) {
					$merchid = $g['merchid'];
					$merch_array[$merchid]['ggprice'] += $g['ggprice'];
					$merchs[$merchid] += $g['ggprice'];
				}

				$g['dflag'] = intval($g['ggprice'] < $gprice);
				if ($g['seckillinfo'] && ($g['seckillinfo']['status'] == 0)) {
				}
				else {
					if (empty($bargain_id)) {
						$taskdiscountprice += $prices['taskdiscountprice'];
						$lotterydiscountprice += $prices['lotterydiscountprice'];
						$g['taskdiscountprice'] = $prices['taskdiscountprice'];
						$g['lotterydiscountprice'] = $prices['lotterydiscountprice'];
						$g['discountprice'] = $prices['discountprice'];
						$g['isdiscountprice'] = $prices['isdiscountprice'];
						$g['discounttype'] = $prices['discounttype'];
						$g['isdiscountunitprice'] = $prices['isdiscountunitprice'];
						$g['discountunitprice'] = $prices['discountunitprice'];
						$buyagainprice += $prices['buyagainprice'];

						if ($prices['discounttype'] == 1) {
							$isdiscountprice += $prices['isdiscountprice'];
						}
						else {
							if ($prices['discounttype'] == 2) {
								$discountprice += $prices['discountprice'];
							}
						}

						if ($threenprice && !empty($threenprice['price'])) {
							$discountprice += $g['marketprice'] - $threenprice['price'];
						}
						else {
							if ($threenprice && !empty($threenprice['discount'])) {
								$discountprice += ((10 - $threenprice['discount']) / 10) * $g['marketprice'];
							}
						}
					}
				}

				$realprice += $g['ggprice'];

				if ($g['ggprice'] < $gprice) {
					$goodsprice += $gprice;
				}
				else {
					$goodsprice += $g['ggprice'];
				}

				$total += $g['total'];

				if (empty($bargain_id)) {
					if ($g['seckillinfo'] && ($g['seckillinfo']['status'] == 0)) {
						$g['deduct'] = 0;
					}
					else {
						if ((0 < floatval($g['buyagain'])) && empty($g['buyagain_sale'])) {
							if (m('goods')->canBuyAgain($g)) {
								$g['deduct'] = 0;
							}
						}
					}

					if ($g['seckillinfo'] && ($g['seckillinfo']['status'] == 0)) {
					}
					else {
						if ($open_redis) {
							if ($g['manydeduct']) {
								$deductprice += $g['deduct'] * $g['total'];
							}
							else {
								$deductprice += $g['deduct'];
							}

							if ($g['deduct2'] == 0) {
								$deductprice2 += $g['ggprice'];
							}
							else {
								if (0 < $g['deduct2']) {
									if ($g['ggprice'] < $g['deduct2']) {
										$deductprice2 += $g['ggprice'];
									}
									else {
										$deductprice2 += $g['deduct2'];
									}
								}
							}
						}
						if ($g['coupondeduct']>0) {
							$coupondeduct +=$g['coupondeduct'];
						}else{
							if ($coupondeduct>0) {
								$coupondeduct +=$g['ggprice']*$g['total'];
							}
						}
					}
				}
			}

			unset($g);

			if ($isverify) {
				$storeids = array();
				$merchid = 0;

				foreach ($goods as $g) {
					if (!empty($g['storeids'])) {
						$merchid = $g['merchid'];

						$storeids = array_merge(explode(',', $g['storeids']), $storeids);
					}
				}

				if (empty($storeids)) {
					if (0 < $merchid) {
						$stores = pdo_fetchall('select * from ' . tablename('ewei_shop_merch_store') . ' where  uniacid=:uniacid and merchid=:merchid and status=1 and type in(2,3) order by displayorder desc,id desc', array(':uniacid' => $_W['uniacid'], ':merchid' => $merchid));
					}
					else {
						$stores = pdo_fetchall('select * from ' . tablename('ewei_shop_store') . ' where  uniacid=:uniacid and status=1 and type in(2,3) order by displayorder desc,id desc', array(':uniacid' => $_W['uniacid']));
					}
				}
				else if (0 < $merchid) {
					$stores = pdo_fetchall('select * from ' . tablename('ewei_shop_merch_store') . ' where id in (' . implode(',', $storeids) . ') and uniacid=:uniacid and merchid=:merchid and status=1 and type in(2,3) order by displayorder desc,id desc', array(':uniacid' => $_W['uniacid'], ':merchid' => $merchid));
				}
				else {
					$stores = pdo_fetchall('select * from ' . tablename('ewei_shop_store') . ' where id in (' . implode(',', $storeids) . ') and uniacid=:uniacid and status=1 and type in(2,3) order by displayorder desc,id desc', array(':uniacid' => $_W['uniacid']));
				}
			}
			else {
				$address = pdo_fetch('select * from ' . tablename('ewei_shop_member_address') . ' where openid=:openid and deleted=0 and isdefault=1  and uniacid=:uniacid limit 1', array(':uniacid' => $uniacid, ':openid' => $openid));

				if (!empty($carrier_list)) {
					$carrier = $carrier_list[0];
				}

				if (!$isvirtual && !$isonlyverifygoods) {
					$dispatch_array = m('order')->getOrderDispatchPrice($goods, $member, $address, $saleset, $merch_array, 0);
					$dispatch_price = $dispatch_array['dispatch_price'] - $dispatch_array['seckill_dispatch_price'];
					$seckill_dispatchprice = $dispatch_array['seckill_dispatch_price'];
				}
			}

			if ($is_openmerch == 1) {
				if (empty($bargain_id)) {
					$merch_enough = m('order')->getMerchEnough($merch_array);
					$merch_array = $merch_enough['merch_array'];
					$merch_enough_total = $merch_enough['merch_enough_total'];
					$merch_saleset = $merch_enough['merch_saleset'];

					if (0 < $merch_enough_total) {
						$realprice -= $merch_enough_total;
					}
				}
			}
			// r_dump($merch_array);die;echo "<pre>";
			// va
			if ($saleset) {
				if (empty($bargain_id)) {
					foreach ($saleset['enoughs'] as $e) {
						if ((floatval($e['enough']) <= $realprice - $seckill_payprice) && (0 < floatval($e['money']))) {
							$saleset['showenough'] = true;
							$saleset['enoughmoney'] = $e['enough'];
							$saleset['enoughdeduct'] = $e['money'];
							$realprice -= floatval($e['money']);
							break;
						}
					}
				}

				if (empty($saleset['dispatchnodeduct'])) {
					$deductprice2 += $dispatch_price;
				}
			}

			$realprice += $dispatch_price + $seckill_dispatchprice;
			$deductcredit = 0;
			$deductmoney = 0;
			$deductcredit2 = 0;

			if (!empty($saleset)) {
				if (!empty($saleset['creditdeduct'])) {
					$credit = $member['credit1'];

					if (0 < $credit) {
						$credit = floor($credit);
					}

					$pcredit = intval($saleset['credit']);
					$pmoney = round(floatval($saleset['money']), 2);
					if ((0 < $pcredit) && (0 < $pmoney)) {
						if (($credit % $pcredit) == 0) {
							$deductmoney = round(intval($credit / $pcredit) * $pmoney, 2);
						}
						else {
							$deductmoney = round((intval($credit / $pcredit) + 1) * $pmoney, 2);
						}
					}

					if ($deductprice < $deductmoney) {
						$deductmoney = $deductprice;
					}

					if (($realprice - $seckill_payprice) < $deductmoney) {
						$deductmoney = $realprice - $seckill_payprice;
					}

					if (($pmoney * $pcredit) != 0) {
						$deductcredit = floor(($deductmoney / $pmoney) * $pcredit);
					}
				}

				if (!empty($saleset['moneydeduct'])) {
					$deductcredit2 = m('member')->getCredit($openid, 'credit2');

					if (($realprice - $seckill_payprice) < $deductcredit2) {
						$deductcredit2 = $realprice - $seckill_payprice;
					}

					if ($deductprice2 < $deductcredit2) {
						$deductcredit2 = $deductprice2;
					}
				}
			}

			$goodsdata = array();
			$goodsdata_temp = array();
			$gifts = array();
			$invoice='';
			foreach ($goods as $g) {
				$goodsdata[] = array('goodsid' => $g['goodsid'], 'total' => $g['total'], 'optionid' => $g['optionid'], 'marketprice' => $g['marketprice'], 'merchid' => $g['merchid'], 'cates' => $g['cates'], 'discounttype' => $g['discounttype'], 'isdiscountprice' => $g['isdiscountprice'], 'discountprice' => $g['discountprice'], 'isdiscountunitprice' => $g['isdiscountunitprice'], 'discountunitprice' => $g['discountunitprice'], 'type' => $g['type'], 'intervalfloor' => $g['intervalfloor'], 'intervalprice1' => $g['intervalprice1'], 'intervalnum1' => $g['intervalnum1'], 'intervalprice2' => $g['intervalprice2'], 'intervalnum2' => $g['intervalnum2'], 'intervalprice3' => $g['intervalprice3'], 'intervalnum3' => $g['intervalnum3'], 'wholesaleprice' => $g['wholesaleprice'], 'goodsalltotal' => $g['goodsalltotal'],'coupondeduct'=>$g['coupondeduct']);
				if ($g['seckillinfo'] && ($g['seckillinfo']['status'] == 0)) {
				}
				else if (0 < floatval($g['buyagain'])) {
					if (!m('goods')->canBuyAgain($g) || !empty($g['buyagain_sale'])) {
						$goodsdata_temp[] = array('goodsid' => $g['goodsid'], 'total' => $g['total'], 'optionid' => $g['optionid'], 'marketprice' => $g['marketprice'], 'merchid' => $g['merchid'], 'cates' => $g['cates'], 'discounttype' => $g['discounttype'], 'isdiscountprice' => $g['isdiscountprice'], 'discountprice' => $g['discountprice'], 'isdiscountunitprice' => $g['isdiscountunitprice'], 'discountunitprice' => $g['discountunitprice'], 'type' => $g['type'], 'intervalfloor' => $g['intervalfloor'], 'intervalprice1' => $g['intervalprice1'], 'intervalnum1' => $g['intervalnum1'], 'intervalprice2' => $g['intervalprice2'], 'intervalnum2' => $g['intervalnum2'], 'intervalprice3' => $g['intervalprice3'], 'intervalnum3' => $g['intervalnum3'], 'wholesaleprice' => $g['wholesaleprice'], 'goodsalltotal' => $g['goodsalltotal'],'coupondeduct'=>$g['coupondeduct']);
					}
				}
				else {
					$goodsdata_temp[] = array('goodsid' => $g['goodsid'], 'total' => $g['total'], 'optionid' => $g['optionid'], 'marketprice' => $g['marketprice'], 'merchid' => $g['merchid'], 'cates' => $g['cates'], 'discounttype' => $g['discounttype'], 'isdiscountprice' => $g['isdiscountprice'], 'discountprice' => $g['discountprice'], 'isdiscountunitprice' => $g['isdiscountunitprice'], 'discountunitprice' => $g['discountunitprice'], 'type' => $g['type'], 'intervalfloor' => $g['intervalfloor'], 'intervalprice1' => $g['intervalprice1'], 'intervalnum1' => $g['intervalnum1'], 'intervalprice2' => $g['intervalprice2'], 'intervalnum2' => $g['intervalnum2'], 'intervalprice3' => $g['intervalprice3'], 'intervalnum3' => $g['intervalnum3'], 'wholesaleprice' => $g['wholesaleprice'], 'goodsalltotal' => $g['goodsalltotal'],'coupondeduct'=>$g['coupondeduct']);
				}

				if ($g['seckillinfo'] && ($g['seckillinfo']['status'] == 0)) {
				}
				else if ($giftid) {
					$gift = array();
					$giftdata = pdo_fetch('select giftgoodsid from ' . tablename('ewei_shop_gift') . ' where uniacid = ' . $uniacid . ' and id = ' . $giftid . ' and status = 1 and starttime <= ' . time() . ' and endtime >= ' . time() . ' ');

					if ($giftdata['giftgoodsid']) {
						$giftgoodsid = explode(',', $giftdata['giftgoodsid']);

						foreach ($giftgoodsid as $key => $value) {
							$gift[$key] = pdo_fetch('select id as goodsid,title,thumb from ' . tablename('ewei_shop_goods') . ' where uniacid = ' . $uniacid . ' and total > 0 and status = 2 and id = ' . $value . ' and deleted = 0 ');

							if ($gift[$key]) {
								$gift[$key]['total'] = $total;
							}
						}

						$gift = array_filter($gift);
						$goodsdata = array_merge($goodsdata, $gift);
					}
				}
				else {
					$isgift = 0;
					$giftgoods = array();
					$gifts = pdo_fetchall('select id,goodsid,giftgoodsid,thumb,title from ' . tablename('ewei_shop_gift') . "\r\n                    where uniacid = " . $uniacid . ' and status = 1 and starttime <= ' . time() . ' and endtime >= ' . time() . ' and orderprice <= ' . $goodsprice . ' and activity = 1 ');

					foreach ($gifts as $key => $value) {
						$isgift = 1;
						$giftgoods = explode(',', $value['giftgoodsid']);

						foreach ($giftgoods as $k => $val) {
							$giftgoodsdetail = pdo_fetch('select id,title,thumb,marketprice from ' . tablename('ewei_shop_goods') . ' where uniacid = ' . $uniacid . ' and deleted = 0 and total > 0 and status = 2 and id = ' . $val . ' ');

							if ($giftgoodsdetail) {
								$gifts[$key]['gift'][$k] = $giftgoodsdetail;
							}
						}

						$gifts = array_filter($gifts);
						$gifttitle = ($gifts[$key]['gift'][$key]['title'] ? $gifts[$key]['gift'][$key]['title'] : '赠品');
					}
				}
			}

			if (!empty($gifts) && (count($gifts) == 1)) {
				$giftid = $gifts[0]['id'];
			}

			if ($g['invoice']==1) {
				$invoice+=$g['marketprice']*$g['total'];
			}

			$couponcount = com_run('coupon::consumeCouponCount', $openid, $realprice, $merch_array, $goodsdata_temp);
			$couponcount += com_run('wxcard::consumeWxCardCount', $openid, $merch_array, $goodsdata_temp);
			if (empty($goodsdata_temp) || !$allow_sale) {
				$couponcount = 0;
			}

			$mustbind = 0;
			if (!empty($_W['shopset']['wap']['open']) && !empty($_W['shopset']['wap']['mustbind']) && empty($member['mobileverify'])) {
				$mustbind = 1;
			}

			if ($is_openmerch == 1) {
				$merchs = $merch_plugin->getMerchs($merch_array);
			}

			$createInfo = array('id' => $id, 'gdid' => intval($_GPC['gdid']), 'fromcart' => $fromcart, 'addressid' => !empty($address) && !$isverify && !$isvirtual ? $address['id'] : 0, 'storeid' => !empty($carrier_list) && !$isverify && !$isvirtual ? $carrier_list[0]['id'] : 0, 'couponcount' => $couponcount, 'coupon_goods' => $goodsdata_temp, 'isvirtual' => $isvirtual, 'isverify' => $isverify, 'isonlyverifygoods' => $isonlyverifygoods, 'goods' => $goodsdata, 'merchs' => $merchs, 'orderdiyformid' => $orderdiyformid, 'giftid' => $giftid, 'mustbind' => $mustbind, 'fromquick' => intval($quickid), 'liveid' => intval($liveid),'backurl'=>$backurl,'commoninvoice'=>intval($invoiceset['commoninvoice'])/100,'addinvoice'=>intval($invoiceset['addinvoice'])/100,'couponMerge'=>intval($couponSet['mergecoupon']));
			
			$buyagain = $buyagainprice;
		}

		$goods_list = array();

		if ($ismerch) {

			$getListUser = $merch_plugin->getListUser($goods);
			$merch_user = $getListUser['merch_user'];

			foreach ($getListUser['merch'] as $k => $v) {
				if (empty($merch_user[$k]['merchname'])) {
					$goods_list[$k]['shopname'] = $_W['shopset']['shop']['name'];
				}
				else {
					$goods_list[$k]['shopname'] = $merch_user[$k]['merchname'];
				}

				$goods_list[$k]['goods'] = $v;
			}
		}
		else {

			if ($merchid == 0) {
				$goods_list[0]['shopname'] = $_W['shopset']['shop']['name'];
			}
			else {
				// var_dump($merchid,6);die;
				$merch_data = $merch_plugin->getListUserOne($merchid);
				$goods_list[0]['shopname'] = $merch_data['merchname'];
			}

			$goods_list[0]['goods'] = $goods;
		}

		$_W['shopshare']['hideMenus'] = array('menuItem:share:qq', 'menuItem:share:QZone', 'menuItem:share:email', 'menuItem:copyUrl', 'menuItem:openWithSafari', 'menuItem:openWithQQBrowser', 'menuItem:share:timeline', 'menuItem:share:appMessage');

		if (p('exchange')) {
			$exchangecha = $goodsprice - $exchangeprice;
		}

		if ($taskgoodsprice) {
			$goodsprice = $taskgoodsprice;
		}

		if ($invoiceset['set_invoice']==1) {
			$hasinvoiceMoney=$goodsprice;
		}	
		// echo "<pre>";
		// var_dump($couponcount);die;
	    include $this->template();
	}

	public function getcouponprice()
	{
		global $_GPC;

		// show_json(0,$_GPC);
		if (is_numeric($_GPC['couponid'])) {
			$couponid = intval($_GPC['couponid']);
		}else{
			$couponid = array();
		}
		$goodsarr = $_GPC['goods'];
		$goodsprice = $_GPC['goodsprice'];
		$discountprice = $_GPC['discountprice'];
		$isdiscountprice = $_GPC['isdiscountprice'];
		$contype = intval($_GPC['contype']);
		$wxid = intval($_GPC['wxid']);
		$wxcardid = $_GPC['wxcardid'];
		$wxcode = $_GPC['wxcode'];

		if (empty($couponid)) {
			$merchs = $_GPC['merchs'];
			$goods =$_GPC['coupon_goods'];
			$coupondeduct = 0;
			foreach ($goods as $g) {
				if ($g['coupondeduct']>0) {
					$coupondeduct +=$g['coupondeduct']*$g['total'];
				}else{
					$coupondeduct +=$g['marketprice']*$g['total'];
				}
				
			}
			$list = com_run('coupon::getAvailableCoupons', 0, 0, $merchs, $goods,'',true);
			$ids=array();
			$totalcoupon = 0;
			foreach ($list as $v) {
				if (empty($v['merchid'])&&$totalcoupon<$coupondeduct) {
					$totalcoupon += $v['amount']>0 ? $v['amount'] : $v['deduct'];
					$ids[]=$v['id'];
				}
			}
			if ($totalcoupon>0) {
				$couponid = array(
					'totalcoupon'=>$totalcoupon,
					'couponids'=>implode(',', $ids),
				);
			}else{
				show_json(2);
			}
		}
		// show_json(0,$_GPC);
		$result = $this->caculatecoupon($contype, $couponid, $wxid, $wxcardid, $wxcode, $goodsarr, $goodsprice, $discountprice, $isdiscountprice);

		if (empty($result)) {
			show_json(0);
		}
		else {
			show_json(1, $result);
		}
	}

	public function caculatecoupon($contype, $couponid, $wxid, $wxcardid, $wxcode, $goodsarr, $totalprice, $discountprice, $isdiscountprice, $isSubmit = 0, $discountprice_array = array(), $merchisdiscountprice = 0)
	{		
		global $_W;
		$openid = $_W['openid'];
		$uniacid = $_W['uniacid'];
		$couponall=false;
		if (empty($goodsarr)) {
			return false;
		}

		if ($contype == 0) {
			return NULL;
		}

		if ($contype == 1) {
			$sql = 'select id,uniacid,card_type,logo_url,title, card_id,least_cost,reduce_cost,discount,merchid,limitgoodtype,limitgoodcatetype,limitgoodcateids,limitgoodids,merchid,limitdiscounttype  from ' . tablename('ewei_shop_wxcard');
			$sql .= '  where uniacid=:uniacid  and id=:id and card_id=:card_id   limit 1';
			$data = pdo_fetch($sql, array(':uniacid' => $uniacid, ':id' => $wxid, ':card_id' => $wxcardid));
			$merchid = intval($data['merchid']);
		}
		else {
			if ($contype == 2) {
				if (!is_array($couponid)) {
					//单张使用
					$sql = 'SELECT d.id,d.couponid,d.amount,c.enough,c.backtype,c.deduct,c.discount,c.backmoney,c.backcredit,c.backredpack,c.merchid,c.limitgoodtype,c.limitgoodcatetype,c.limitgoodids,c.limitgoodcateids,c.limitdiscounttype  FROM ' . tablename('ewei_shop_coupon_data') . ' d';
					$sql .= ' left join ' . tablename('ewei_shop_coupon') . ' c on d.couponid = c.id';
					$sql .= ' where d.id=:id and d.uniacid=:uniacid and d.openid=:openid and d.used=0  limit 1';
					$data = pdo_fetch($sql, array(':uniacid' => $uniacid, ':id' => $couponid, ':openid' => $openid));
					$merchid = intval($data['merchid']);
					$data['deduct'] = $data['amount']>0 ? $data['amount'] : $data['deduct'];
				}else{
					// 使用多张
					$data['couponid'] =$couponid['couponids'];
					$data['amount'] = $couponid['totalcoupon'];
					$data['deduct'] = $couponid['totalcoupon'];
					$merchid = 0;
					$couponall = true;
				}
				
			}
		}
		
		if (empty($data)) {
			return NULL;
		}

		if (is_array($goodsarr)) {
			$goods = array();

			foreach ($goodsarr as $g) {
				if (empty($g)) {
					continue;
				}

				if ((0 < $merchid) && ($g['merchid'] != $merchid)) {
					continue;
				}

				$cates = explode(',', $g['cates']);
				$limitcateids = explode(',', $data['limitgoodcateids']);
				$limitgoodids = explode(',', $data['limitgoodids']);
				$pass = 0;
				if (($data['limitgoodcatetype'] == 0) && ($data['limitgoodtype'] == 0)) {
					$pass = 1;
				}

				if ($data['limitgoodcatetype'] == 1) {
					$result = array_intersect($cates, $limitcateids);

					if (0 < count($result)) {
						$pass = 1;
					}
				}

				if ($data['limitgoodtype'] == 1) {
					$isin = in_array($g['goodsid'], $limitgoodids);

					if ($isin) {
						$pass = 1;
					}
				}

				if ($pass == 1) {
					$goods[] = $g;
				}
			}

			$limitdiscounttype = intval($data['limitdiscounttype']);
			$coupongoodprice = 0;
			$coupondeduct = 0;
			$gprice = 0;

			foreach ($goods as $k => $g) {
				$gprice = (double) $g['marketprice'] * (double) $g['total'];
				$gcoupondeduct = $g['coupondeduct']>0 ? (double) $g['coupondeduct'] * (double) $g['total'] : (double) $g['marketprice'] * (double) $g['total'];

				switch ($limitdiscounttype) {
				case 1:
					$coupongoodprice += $gprice - ((double) $g['discountunitprice'] * (double) $g['total']);

					$discountprice_array[$g['merchid']]['coupongoodprice'] += $gprice - ((double) $g['discountunitprice'] * (double) $g['total']);

					if ($g['discounttype'] == 1) {
						$isdiscountprice -= (double) $g['isdiscountunitprice'] * (double) $g['total'];
						$discountprice += (double) $g['discountunitprice'] * (double) $g['total'];

						if ($isSubmit == 1) {													
							$totalprice = ($totalprice - $g['ggprice']) + $g['price2'];
							$discountprice_array[$g['merchid']]['ggprice'] = ($discountprice_array[$g['merchid']]['ggprice'] - $g['ggprice']) + $g['price2'];
							$goodsarr[$k]['ggprice'] = $g['price2'];
							$discountprice_array[$g['merchid']]['isdiscountprice'] -= (double) $g['isdiscountunitprice'] * (double) $g['total'];
							$discountprice_array[$g['merchid']]['discountprice'] += (double) $g['discountunitprice'] * (double) $g['total'];

							if (!empty($data['merchsale'])) {
								$merchisdiscountprice -= (double) $g['isdiscountunitprice'] * (double) $g['total'];
								$discountprice_array[$g['merchid']]['merchisdiscountprice'] -= (double) $g['isdiscountunitprice'] * (double) $g['total'];
							}
						}
					}

					break;

				case 2:
					$coupongoodprice += $gprice - ((double) $g['isdiscountunitprice'] * (double) $g['total']);
					$discountprice_array[$g['merchid']]['coupongoodprice'] += $gprice - ((double) $g['isdiscountunitprice'] * (double) $g['total']);

					if ($g['discounttype'] == 2) {
						$discountprice -= (double) $g['discountunitprice'] * (double) $g['total'];

						if ($isSubmit == 1) {							
							$totalprice = ($totalprice - $g['ggprice']) + $g['price1'];
							$discountprice_array[$g['merchid']]['ggprice'] = ($discountprice_array[$g['merchid']]['ggprice'] - $g['ggprice']) + $g['price1'];
							$goodsarr[$k]['ggprice'] = $g['price1'];
							$discountprice_array[$g['merchid']]['discountprice'] -= (double) $g['discountunitprice'] * (double) $g['total'];
						}
					}

					break;

				case 3:
					$coupongoodprice += $gprice;
					$discountprice_array[$g['merchid']]['coupongoodprice'] += $gprice;

					if ($g['discounttype'] == 1) {
						$isdiscountprice -= (double) $g['isdiscountunitprice'] * (double) $g['total'];

						if ($isSubmit == 1) {							
							$totalprice = ($totalprice - $g['ggprice']) + $g['price0'];
							$discountprice_array[$g['merchid']]['ggprice'] = ($discountprice_array[$g['merchid']]['ggprice'] - $g['ggprice']) + $g['price0'];
							$goodsarr[$k]['ggprice'] = $g['price0'];

							if (!empty($data['merchsale'])) {
								$merchisdiscountprice -= $g['isdiscountunitprice'] * (double) $g['total'];
								$discountprice_array[$g['merchid']]['merchisdiscountprice'] -= $g['isdiscountunitprice'] * (double) $g['total'];
							}

							$discountprice_array[$g['merchid']]['isdiscountprice'] -= $g['isdiscountunitprice'] * (double) $g['total'];
						}
					}
					else {
						if ($g['discounttype'] == 2) {
							$discountprice -= (double) $g['discountunitprice'] * (double) $g['total'];

							if ($isSubmit == 1) {
								$totalprice = ($totalprice - $g['ggprice']) + $g['price0'];
								$goodsarr[$k]['ggprice'] = $g['price0'];
								$discountprice_array[$g['merchid']]['ggprice'] = ($discountprice_array[$g['merchid']]['ggprice'] - $g['ggprice']) + $g['price0'];
								$discountprice_array[$g['merchid']]['discountprice'] -= (double) $g['discountunitprice'] * (double) $g['total'];
							}
						}
					}

					break;

				default:
					if ($g['discounttype'] == 1) {
						$coupongoodprice += $gprice - ((double) $g['isdiscountunitprice'] * (double) $g['total']);
						$discountprice_array[$g['merchid']]['coupongoodprice'] += $gprice - ((double) $g['isdiscountunitprice'] * (double) $g['total']);
					}
					else if ($g['discounttype'] == 2) {
						$coupongoodprice += $gprice - ((double) $g['discountunitprice'] * (double) $g['total']);
						$discountprice_array[$g['merchid']]['coupongoodprice'] += $gprice - ((double) $g['discountunitprice'] * (double) $g['total']);
					}
					else {
						if ($g['discounttype'] == 0) {
							$coupongoodprice += $gprice;
							$discountprice_array[$g['merchid']]['coupongoodprice'] += $gprice;
						}
					}

					break;
				}
				if ($gcoupondeduct>0) {
					$coupondeduct +=$gcoupondeduct;
					$discountprice_array[$g['merchid']]['coupongoodprice']=$gcoupondeduct;
				}else{
					if ($coupondeduct>0) {
						$coupondeduct +=$gprice;
					}
				}
			}
			if ($coupondeduct>0) {
				$coupongoodprice = $coupondeduct;
			}

			if ($contype == 1) {
				$deduct = (double) $data['reduce_cost'] / 100;
				$discount = (double) (100 - intval($data['discount'])) / 10;

				if ($data['card_type'] == 'CASH') {
					$backtype = 0;
				}
				else {
					if ($data['card_type'] == 'DISCOUNT') {
						$backtype = 1;
					}
				}
			}
			else {
				if ($contype == 2) {
					$deduct = (double) $data['deduct'];
					$discount = (double) $data['discount'];
					$backtype = (double) $data['backtype'];
				}
			}

			$deductprice = 0;
			$coupondeduct_text = '';
			if ((0 < $deduct) && ($backtype == 0||$backtype==3) && (0 < $coupongoodprice)) {
				if ($coupongoodprice < $deduct) {
					$deduct = $coupongoodprice;
				}

				if ($deduct <= 0) {
					$deduct = 0;
				}

				$deductprice = $deduct;
				$coupondeduct_text = '优惠券优惠';

				foreach ($discountprice_array as $key => $value) {
					$discountprice_array[$key]['deduct'] = ((double) $value['coupongoodprice'] / (double) $coupongoodprice) * $deduct;
				}

			}
			else {
				if ((0 < $discount) && ($backtype == 1)) {
					$deductprice = $coupongoodprice * (1 - ($discount / 10));

					if ($coupongoodprice < $deductprice) {
						$deductprice = $coupongoodprice;
					}

					if ($deductprice <= 0) {
						$deductprice = 0;
					}

					foreach ($discountprice_array as $key => $value) {
						$discountprice_array[$key]['deduct'] = (double) $value['coupongoodprice'] * (1 - ($discount / 10));
					}

					if (0 < $merchid) {
						$coupondeduct_text = '店铺优惠券折扣(' . $discount . '折)';
					}
					else {
						$coupondeduct_text = '优惠券折扣(' . $discount . '折)';
					}
				}
			}
		}

		$totalprice -= $deductprice;
		$return_array = array();
		$return_array['isdiscountprice'] = $isdiscountprice;
		$return_array['discountprice'] = $discountprice;
		$return_array['deductprice'] = $deductprice;
		$return_array['coupongoodprice'] = $coupongoodprice;
		$return_array['coupondeduct_text'] = $coupondeduct_text;
		$return_array['totalprice'] = $totalprice;
		$return_array['discountprice_array'] = $discountprice_array;
		$return_array['merchisdiscountprice'] = $merchisdiscountprice;
		$return_array['couponmerchid'] = $merchid;
		$return_array['$goodsarr'] = $goodsarr;
		$return_array['couponid'] = $couponall ? $couponid['couponids'] : 0;
		$return_array['totalcoupon'] = $couponall ? $couponid['totalcoupon'] : 0;
		return $return_array;
	}

	public function caculate()
	{
		global $_W;
		global $_GPC;
		$open_redis = function_exists('redis') && !is_error(redis());
		$openid = $_W['openid'];
		$uniacid = $_W['uniacid'];
		$merchdata = $this->merchData();
		extract($merchdata);
		$merch_array = array();
		$allow_sale = true;
		$realprice = 0;
		$nowsendfree = false;
		$isverify = false;
		$isvirtual = false;
		$taskdiscountprice = 0;
		$lotterydiscountprice = 0;
		$discountprice = 0;
		$isdiscountprice = 0;
		$deductprice = 0;
		$deductprice2 = 0;
		$coupondeduct = 0;
		$deductcredit2 = 0;
		$buyagain_sale = true;
		$isonlyverifygoods = true;
		$buyagainprice = 0;
		$seckill_price = 0;
		$seckill_payprice = 0;
		$seckill_dispatchprice = 0;
		$liveid = intval($_GPC['liveid']);
		if (p('live') && !empty($liveid)) {
			$isliving = p('live')->isLiving($liveid);

			if (!$isliving) {
				$liveid = 0;
			}
		}

		$dispatchid = intval($_GPC['dispatchid']);
		$totalprice = floatval($_GPC['totalprice']);
		$dflag = $_GPC['dflag'];
		$addressid = intval($_GPC['addressid']);
		$address = pdo_fetch('select * from ' . tablename('ewei_shop_member_address') . ' where  id=:id and openid=:openid and uniacid=:uniacid limit 1', array(':uniacid' => $uniacid, ':openid' => $openid, ':id' => $addressid));
		$member = m('member')->getMember($openid, true);
		$level = m('member')->getLevel($openid);
		$weight = floatval($_GPC['weight']);
		$dispatch_price = 0;
		$deductenough_money = 0;
		$deductenough_enough = 0;
		$goodsarr = $_GPC['goods'];
		if (is_array($goodsarr)) {
			$weight = 0;
			$allgoods = array();

			foreach ($goodsarr as &$g) {
				if (empty($g)) {
					continue;
				}

				$goodsid = $g['goodsid'];
				$optionid = $g['optionid'];
				$goodstotal = $g['total'];

				if ($goodstotal < 1) {
					$goodstotal = 1;
				}

				if (empty($goodsid)) {
					$nowsendfree = true;
				}

				$sql = 'SELECT id as goodsid,title,type, weight,total,issendfree,isnodiscount, thumb,marketprice,cash,isverify,goodssn,productsn,sales,istime,' . ' timestart,timeend,usermaxbuy,maxbuy,unit,buylevels,buygroups,deleted,status,deduct,ispresell,preselltimeend,manydeduct,`virtual`,' . ' discounts,deduct2,coupondeduct,ednum,edmoney,edareas,edareas_code,diyformid,diyformtype,diymode,dispatchtype,dispatchid,dispatchprice,presellprice,' . ' isdiscount,isdiscount_time,isdiscount_discounts ,virtualsend,merchid,merchsale,' . ' buyagain,buyagain_islong,buyagain_condition, buyagain_sale,bargain,unite_total,islive,liveprice,cates' . ' FROM ' . tablename('ewei_shop_goods') . ' where id=:id and uniacid=:uniacid  limit 1';
				$data = pdo_fetch($sql, array(':uniacid' => $uniacid, ':id' => $goodsid));
				if (!empty($g['merchid'])) {
					$data['merchid'] = $g['merchid'];
				}
				$data['seckillinfo'] = plugin_run('seckill::getSeckill', $goodsid, $optionid, true, $_W['openid']);
				if ((0 < $data['ispresell']) && (($data['preselltimeend'] == 0) || (time() < $data['preselltimeend']))) {
					$data['marketprice'] = $data['presellprice'];
				}

				if (!empty($liveid)) {
					$isLiveGoods = p('live')->isLiveGoods($data['goodsid'], $liveid);

					if (!empty($isLiveGoods)) {
						$data['marketprice'] = price_format($isLiveGoods['liveprice']);
					}
				}

				if (empty($data)) {
					$nowsendfree = true;
				}

				if ($data['status'] == 2) {
					$data['marketprice'] = 0;
				}

				if ($data['seckillinfo'] && ($data['seckillinfo']['status'] == 0)) {
					$data['is_task_goods'] = 0;
				}
				else {
					if (p('task')) {
						$task_id = intval($_SESSION[$goodsid . '_task_id']);

						if (!empty($task_id)) {
							$rewarded = pdo_fetchcolumn('SELECT `rewarded` FROM ' . tablename('ewei_shop_task_extension_join') . ' WHERE id = :id AND openid = :openid AND uniacid = :uniacid', array(':id' => $task_id, ':uniacid' => $_W['uniacid'], ':openid' => $_W['openid']));
							$taskGoodsInfo = unserialize($rewarded);
							$taskGoodsInfo = $taskGoodsInfo['goods'][$goodsid];
							if (!empty($optionid) && !empty($taskGoodsInfo['option']) && ($optionid == $taskGoodsInfo['option'])) {
								$taskgoodsprice = $taskGoodsInfo['price'];
							}
							else {
								if (empty($optionid)) {
									$taskgoodsprice = $taskGoodsInfo['price'];
								}
							}
						}
					}

					$rank = intval($_SESSION[$goodsid . '_rank']);
					$log_id = intval($_SESSION[$goodsid . '_log_id']);
					$join_id = intval($_SESSION[$goodsid . '_join_id']);
					$task_goods_data = m('goods')->getTaskGoods($openid, $goodsid, $rank, $log_id, $join_id, $optionid);

					if (empty($task_goods_data['is_task_goods'])) {
						$data['is_task_goods'] = 0;
					}
					else {
						$allow_sale = false;
						$data['is_task_goods'] = $task_goods_data['is_task_goods'];
						$data['is_task_goods_option'] = $task_goods_data['is_task_goods_option'];
						$data['task_goods'] = $task_goods_data['task_goods'];
					}
				}

				$data['stock'] = $data['total'];
				$data['total'] = $goodstotal;

				if (!empty($optionid)) {
					$option = pdo_fetch('select id,title,marketprice,presellprice,goodssn,productsn,stock,`virtual`,weight,liveprice,islive from ' . tablename('ewei_shop_goods_option') . ' where id=:id and goodsid=:goodsid and uniacid=:uniacid  limit 1', array(':uniacid' => $uniacid, ':goodsid' => $goodsid, ':id' => $optionid));

					if (!empty($option)) {
						$data['optionid'] = $optionid;
						$data['optiontitle'] = $option['title'];
						$data['marketprice'] = (0 < intval($data['ispresell'])) && ((time() < $data['preselltimeend']) || ($data['preselltimeend'] == 0)) ? $option['presellprice'] : $option['marketprice'];

						if (!empty($liveid)) {
							$liveOption = p('live')->getLiveOptions($data['goodsid'], $liveid, array($option));
							if (!empty($liveOption) && !empty($liveOption[0])) {
								$data['marketprice'] = price_format($liveOption[0]['marketprice']);
							}
						}

						if (empty($data['unite_total'])) {
							$data['stock'] = $option['stock'];
						}

						if (!empty($option['weight'])) {
							$data['weight'] = $option['weight'];
						}
					}
				}

				if ($data['type'] == 4) {
					$data['marketprice'] = $g['wholesaleprice'];
					$data['wholesaleprice'] = $g['wholesaleprice'];
				}

				if ($data['seckillinfo'] && ($data['seckillinfo']['status'] == 0)) {
					$data['ggprice'] = $data['seckillinfo']['price'] * $g['total'];
					$seckill_payprice += $data['ggprice'];
					$seckill_price += $data['marketprice'] * $g['total'];
				}
				else {
					$prices = m('order')->getGoodsDiscountPrice($data, $level);
					$data['ggprice'] = $prices['price'];
				}

				if ($is_openmerch == 1) {
					$merchid = $data['merchid'];
					$merch_array[$merchid]['goods'][] = $data['goodsid'];
					$merch_array[$merchid]['ggprice'] += $data['ggprice'];
				}

				if ($data['isverify'] == 2) {
					$isverify = true;
				}

				if (!empty($data['virtual']) || ($data['type'] == 2) || ($data['type'] == 3) || ($data['type'] == 20)) {
					$isvirtual = true;
				}

				if ($data['seckillinfo'] && ($data['seckillinfo']['status'] == 0)) {
					$g['taskdiscountprice'] = 0;
					$g['lotterydiscountprice'] = 0;
					$g['discountprice'] = 0;
					$g['isdiscountprice'] = 0;
					$g['discounttype'] = 0;
				}
				else {
					$g['taskdiscountprice'] = $prices['taskdiscountprice'];
					$g['lotterydiscountprice'] = $prices['lotterydiscountprice'];
					$g['discountprice'] = $prices['discountprice'];
					$g['isdiscountprice'] = $prices['isdiscountprice'];
					$g['discounttype'] = $prices['discounttype'];
					$taskdiscountprice += $prices['taskdiscountprice'];
					$lotterydiscountprice += $prices['lotterydiscountprice'];
					$buyagainprice += $prices['buyagainprice'];
				}

				if ($data['seckillinfo'] && ($data['seckillinfo']['status'] == 0)) {
				}
				else if ($prices['discounttype'] == 1) {
					$isdiscountprice += $prices['isdiscountprice'];
				}
				else {
					if ($prices['discounttype'] == 2) {
						$discountprice += $prices['discountprice'];
					}
				}

				$realprice += $data['ggprice'];
				$allgoods[] = $data;
				if ($data['seckillinfo'] && ($data['seckillinfo']['status'] == 0)) {
				}
				else {
					if ((0 < floatval($g['buyagain'])) && empty($g['buyagain_sale'])) {
						if (m('goods')->canBuyAgain($g)) {
							$buyagain_sale = false;
						}
					}
				}
			}

			unset($g);

			if ($is_openmerch == 1) {
				foreach ($merch_array as $key => $value) {
					if (0 < $key) {
						$merch_array[$key]['set'] = $merch_plugin->getSet('sale', $key);
						$merch_array[$key]['enoughs'] = $merch_plugin->getEnoughs($merch_array[$key]['set']);
					}
				}
			}

			$sale_plugin = com('sale');
			$saleset = false;
			if ($sale_plugin && $buyagain_sale && $allow_sale) {
				$saleset = $_W['shopset']['sale'];
				$saleset['enoughs'] = $sale_plugin->getEnoughs();
			}

			foreach ($allgoods as $g) {
				if ($g['type'] != 5) {
					$isonlyverifygoods = false;
				}

				if ($g['seckillinfo'] && ($g['seckillinfo']['status'] == 0)) {
					$g['deduct'] = 0;
				}
				else {
					if ((0 < floatval($g['buyagain'])) && empty($g['buyagain_sale'])) {
						if (m('goods')->canBuyAgain($g)) {
							$g['deduct'] = 0;
						}
					}
				}

				if ($g['seckillinfo'] && ($g['seckillinfo']['status'] == 0)) {
				}
				else {
					if ($open_redis) {
						if ($g['manydeduct']) {
							$deductprice += $g['deduct'] * $g['total'];
						}
						else {
							$deductprice += $g['deduct'];
						}

						if ($g['deduct2'] == 0) {
							$deductprice2 += $g['ggprice'];
						}
						else {
							if (0 < $g['deduct2']) {
								if ($g['ggprice'] < $g['deduct2']) {
									$deductprice2 += $g['ggprice'];
								}
								else {
									$deductprice2 += $g['deduct2'];
								}
							}
						}
					}
					if ($g['coupondeduct']>0) {
						$coupondeduct +=$g['coupondeduct']*$g['total'];
					}else{
						if ($coupondeduct>0) {
							$coupondeduct +=$g['ggprice']*$g['total'];
						}
					}
					
				}
			}

			if ($isverify || $isvirtual) {
				$nowsendfree = true;
			}

			if (!empty($allgoods) && !$nowsendfree && !$isonlyverifygoods) {
				$dispatch_array = m('order')->getOrderDispatchPrice($allgoods, $member, $address, $saleset, $merch_array, 1);
				$dispatch_price = $dispatch_array['dispatch_price'] - $dispatch_array['seckill_dispatch_price'];
				$nodispatch_array = $dispatch_array['nodispatch_array'];
				$seckill_dispatchprice = $dispatch_array['seckill_dispatch_price'];
			}

			if ($is_openmerch == 1) {
				$merch_enough = m('order')->getMerchEnough($merch_array);
				$merch_array = $merch_enough['merch_array'];
				$merch_enough_total = $merch_enough['merch_enough_total'];
				$merch_saleset = $merch_enough['merch_saleset'];

				if (0 < $merch_enough_total) {
					$realprice -= $merch_enough_total;
				}
			}

			if ($saleset) {
				foreach ($saleset['enoughs'] as $e) {
					if ((floatval($e['enough']) <= $realprice - $seckill_payprice) && (0 < floatval($e['money']))) {
						$deductenough_money = floatval($e['money']);
						$deductenough_enough = floatval($e['enough']);
						$realprice -= floatval($e['money']);
						break;
					}
				}
			}

			if ($dflag != 'true') {
				if (empty($saleset['dispatchnodeduct'])) {
					$deductprice2 += $dispatch_price;
				}
			}

			$goodsdata_coupon = array();

			foreach ($allgoods as $g) {
				if ($g['seckillinfo'] && ($g['seckillinfo']['status'] == 0)) {
				}
				else if (0 < floatval($g['buyagain'])) {
					if (!m('goods')->canBuyAgain($g) || !empty($g['buyagain_sale'])) {
						$goodsdata_coupon[] = array('goodsid' => $g['goodsid'], 'total' => $g['total'], 'optionid' => $g['optionid'], 'marketprice' => $g['marketprice'], 'merchid' => $g['merchid'], 'cates' => $g['cates'], 'discounttype' => $g['discounttype'], 'isdiscountprice' => $g['isdiscountprice'], 'discountprice' => $g['discountprice'], 'isdiscountunitprice' => $g['isdiscountunitprice'], 'discountunitprice' => $g['discountunitprice'], 'type' => $g['type'], 'wholesaleprice' => $g['wholesaleprice']);
					}
				}
				else {
					$goodsdata_coupon[] = array('goodsid' => $g['goodsid'], 'total' => $g['total'], 'optionid' => $g['optionid'], 'marketprice' => $g['marketprice'], 'merchid' => $g['merchid'], 'cates' => $g['cates'], 'discounttype' => $g['discounttype'], 'isdiscountprice' => $g['isdiscountprice'], 'discountprice' => $g['discountprice'], 'isdiscountunitprice' => $g['isdiscountunitprice'], 'discountunitprice' => $g['discountunitprice'], 'type' => $g['type'], 'wholesaleprice' => $g['wholesaleprice']);
				}
			}
			$machineid = $_GPC['machineid'];
			$couponcount = com_run('coupon::consumeCouponCount', $openid, $realprice - $seckill_payprice, $merch_array, $goodsdata_coupon,$machineid);
			// var_dump($couponcount,$goodsdata_coupon);die;
			$couponcount += com_run('wxcard::consumeWxCardCount', $openid, $merch_array, $goodsdata_coupon);
			if (empty($goodsdata_coupon) || !$allow_sale) {
				$couponcount = 0;
			}

			$realprice += $dispatch_price + $seckill_dispatchprice;
			$deductcredit = 0;
			$deductmoney = 0;

			if (!empty($saleset)) {
				$credit = $member['credit1'];

				if (0 < $credit) {
					$credit = floor($credit);
				}

				if (!empty($saleset['creditdeduct'])) {
					$pcredit = intval($saleset['credit']);
					$pmoney = round(floatval($saleset['money']), 2);
					if ((0 < $pcredit) && (0 < $pmoney)) {
						if (($credit % $pcredit) == 0) {
							$deductmoney = round(intval($credit / $pcredit) * $pmoney, 2);
						}
						else {
							$deductmoney = round((intval($credit / $pcredit) + 1) * $pmoney, 2);
						}
					}

					if ($deductprice < $deductmoney) {
						$deductmoney = $deductprice;
					}

					if (($realprice - $seckill_payprice) < $deductmoney) {
						$deductmoney = $realprice - $seckill_payprice;
					}

					$deductcredit = floor(($pmoney * $pcredit) == 0 ? 0 : ($deductmoney / $pmoney) * $pcredit);
				}

				if (!empty($saleset['moneydeduct'])) {
					$deductcredit2 = $member['credit2'];

					if (($realprice - $seckill_payprice) < $deductcredit2) {
						$deductcredit2 = $realprice - $seckill_payprice;
					}

					if ($deductprice2 < $deductcredit2) {
						$deductcredit2 = $deductprice2;
					}
				}
			}
		}

		if ($is_openmerch == 1) {
			$merchs = $merch_plugin->getMerchs($merch_array);
		}
		// var_dump($couponcount);die;
		$return_array = array();
		$return_array['price'] = $dispatch_price + $seckill_dispatchprice;
		$return_array['couponcount'] = $couponcount;
		$return_array['realprice'] = $realprice;
		$return_array['deductenough_money'] = $deductenough_money;
		$return_array['deductenough_enough'] = $deductenough_enough;
		$return_array['deductcredit2'] = $deductcredit2;
		$return_array['coupondeduct'] = $coupondeduct;
		$return_array['deductcredit'] = $deductcredit;
		$return_array['deductmoney'] = $deductmoney;
		$return_array['taskdiscountprice'] = $taskdiscountprice;
		$return_array['lotterydiscountprice'] = $lotterydiscountprice;
		$return_array['discountprice'] = $discountprice;
		$return_array['isdiscountprice'] = $isdiscountprice;
		$return_array['merch_showenough'] = $merch_saleset['merch_showenough'];
		$return_array['merch_deductenough_money'] = $merch_saleset['merch_enoughdeduct'];
		$return_array['merch_deductenough_enough'] = $merch_saleset['merch_enoughmoney'];
		$return_array['merchs'] = $merchs;
		$return_array['buyagain'] = $buyagainprice;
		$return_array['seckillprice'] = $seckill_price - $seckill_payprice;

		if (!empty($nodispatch_array['isnodispatch'])) {
			$return_array['isnodispatch'] = 1;
			$return_array['nodispatch'] = $nodispatch_array['nodispatch'];
		}
		else {
			$return_array['isnodispatch'] = 0;
			$return_array['nodispatch'] = '';
		}

		show_json(1, $return_array);
	}

	public function submit()
	{
		global $_W;
		global $_GPC;
		$openid = $_W['openid'];
		$uniacid = $_W['uniacid'];
		$realprice = $_GPC['realprice'];

		$member = m('member')->getMember($openid);

		$allow_sale = true;
		$packageid = $_GPC['packageid']?intval($_GPC['packageid']):0;
		$package = array();
		$packgoods = array();
		$packageprice = 0;

		$data = $this->diyformData($member);
		extract($data);
		$merchdata = $this->merchData();
		extract($merchdata);
		$merch_array = array();
		$ismerch = 0;
		$discountprice_array = array();
		$level = m('member')->getLevel($openid);
		$dispatchid = intval($_GPC['dispatchid']);
		$dispatchtype = intval($_GPC['dispatchtype']);
		$carrierid = intval($_GPC['carrierid']);

		$goods = $_GPC['goods'];
		$goodsinfo = pdo_fetch('select * from ' . tablename('ewei_shop_goods') . ' where type = 2 and status = 1 and marketprice = 0 and totalcnf = 2  and uniacid=:uniacid and merchid = 0 ', array(':uniacid' => $_W['uniacid']));

		if(empty($goods[0]['goodsid'])){
			$goods[0]['goodsid'] = $goodsinfo['id'];
		}
		$goods[0]['bargain_id'] = $_SESSION['bargain_id'];
		$_SESSION['bargain_id'] = NULL;

		if (!empty($goods[0]['bargain_id'])) {
			$allow_sale = false;
		}

		if (empty($goods) || !is_array($goods)) {
			show_json(0, '未找到任何商品');
		}

		$liveid = intval($_GPC['liveid']);
		if (p('live') && !empty($liveid)) {
			$isliving = p('live')->isLiving($liveid);

			if (!$isliving) {
				$liveid = 0;
			}
		}

		$allgoods = array();
		$tgoods = array();
		$totalprice = 0;
		$goodsprice = 0;
		$grprice = 0;
		$weight = 0;
		$taskdiscountprice = 0;
		$lotterydiscountprice = 0;
		$discountprice = 0;
		$isdiscountprice = 0;
		$merchisdiscountprice = 0;
		$cash = 1;
		$deductprice = 0;
		$deductprice2 = 0;
		$virtualsales = 0;
		$dispatch_price = 0;
		$seckill_price = 0;
		$seckill_payprice = 0;
		$seckill_dispatchprice = 0;
		$buyagain_sale = true;
		$buyagainprice = 0;
		$sale_plugin = com('sale');

		$saleset = false;
		if ($sale_plugin && $allow_sale) {
			$saleset = $_W['shopset']['sale'];
			$saleset['enoughs'] = $sale_plugin->getEnoughs();
		}

		$isvirtual = false;
		$isverify = false;
		$isonlyverifygoods = true;
		$isendtime = 0;
		$endtime = 0;
		$verifytype = 0;
		$isvirtualsend = false;
		$couponmerchid = 0;
		$total_array = array();
		$giftid = intval($_GPC['giftid']);

		if ($giftid) {
			$gift = array();
			$giftdata = pdo_fetch('select giftgoodsid from ' . tablename('ewei_shop_gift') . ' where uniacid = ' . $uniacid . ' and id = ' . $giftid . ' and status = 1 and starttime <= ' . time() . ' and endtime >= ' . time() . ' ');

			if ($giftdata['giftgoodsid']) {
				$giftgoodsid = explode(',', $giftdata['giftgoodsid']);

				foreach ($giftgoodsid as $key => $value) {
					$gift[$key] = pdo_fetch('select id as goodsid,title,thumb from ' . tablename('ewei_shop_goods') . ' where uniacid = ' . $uniacid . ' and deleted = 0 and status = 2 and id = ' . $value . ' and deleted = 0 ');
				}

				$gift = array_filter($gift);
				$goods = array_merge($goods, $gift);
			}
		}

		foreach ($goods as $g) {
			if (empty($g)) {
				continue;
			}

			$goodsid = intval($g['goodsid']);
			$goodstotal = $g['total']?intval($g['total']):1;
			$total_array[$goodsid]['total'] += $goodstotal;
		}

		if (p('threen')) {
			$threenvip = p('threen')->getMember($_W['openid']);

			if (!empty($threenvip)) {
				$threenprice = true;
			}
		}

		$goods = m('goods')->wholesaleprice($goods);

		foreach ($goods as $g) {
			if (empty($g)) {
				continue;
			}

			$goodsid = intval($g['goodsid']);
			$optionid = intval($g['optionid']);
			$goodstotal = intval($g['total']);

			if ($goodstotal < 1) {
				$goodstotal = 1;
			}

			if (empty($goodsid)) {
				show_json(0, '参数错误');
			}

			if (p('exchange')) {
				$sql_condition = 'exchange_stock,';
			}
			else {
				$sql_condition = '';
			}

			$threensql = '';
			if (p('threen') && !empty($threenprice)) {
				$threensql .= ',threen';
			}

			$sql = 'SELECT id as goodsid,' . $sql_condition . 'title,type,intervalfloor,intervalprice, weight,total,issendfree,isnodiscount, thumb,marketprice,liveprice,cash,isverify,verifytype,' . ' goodssn,productsn,sales,istime,timestart,timeend,hasoption,isendtime,usetime,endtime,ispresell,presellprice,preselltimeend,' . ' usermaxbuy,minbuy,maxbuy,unit,limitbuy,buylevels,buygroups,deleted,unite_total,' . ' status,deduct,manydeduct,`virtual`,discounts,deduct2,coupondeduct,ednum,edmoney,edareas,edareas_code,diyformtype,diyformid,diymode,' . ' dispatchtype,dispatchid,dispatchprice,merchid,merchsale,cates,' . ' isdiscount,isdiscount_time,isdiscount_discounts, virtualsend,' . ' buyagain,buyagain_islong,buyagain_condition, buyagain_sale ,verifygoodslimittype,verifygoodslimitdate  ' . $threensql . ' FROM ' . tablename('ewei_shop_goods') . ' where id=:id and uniacid=:uniacid  limit 1';
			$data = pdo_fetch($sql, array(':uniacid' => $uniacid, ':id' => $goodsid));

			$data['seckillinfo'] = plugin_run('seckill::getSeckill', $goodsid, $optionid, true, $_W['openid']);
			if ((0 < $data['ispresell']) && (($data['preselltimeend'] == 0) || (time() < $data['preselltimeend']))) {
				$data['marketprice'] = $data['presellprice'];
			}
			$data['marketprice'] = $g['marketprice'];

			$openidlevel = m('member')->getLevel($_W['openid']);//获取会员ID
			//会员单次购买限制
			$data = m('goods')->getGoodsLimit($data,$openidlevel['id']);

			if ($data['type'] != 5) {
				$isonlyverifygoods = false;
			}
			else {
				if (!empty($data['verifygoodslimittype'])) {
					$verifygoodslimitdate = intval($data['verifygoodslimitdate']);

					if ($verifygoodslimitdate < time()) {
						show_json(0, '商品:"' . $data['title'] . '"的使用时间已失效,无法购买!');
					}

					if (($verifygoodslimitdate - 7200) < time()) {
						show_json(0, '商品:"' . $data['title'] . '"的使用时间即将失效,无法购买!');
					}
				}
			}

			if (!empty($liveid)) {
				$isLiveGoods = p('live')->isLiveGoods($data['goodsid'], $liveid);

				if (!empty($isLiveGoods)) {
					$data['marketprice'] = price_format($isLiveGoods['liveprice']);
				}
			}

			if ($data['status'] == 2) {
				$data['marketprice'] = 0;
			}

			if (!empty($_SESSION['exchange']) && p('exchange')) {
				if (empty($data['status']) || !empty($data['deleted'])) {
					show_json(0, $data['title'] . '<br/> 已下架!');
				}
			}

			if (!empty($data['hasoption'])) {
				$opdata = m('goods')->getOption($data['goodsid'], $optionid);
				if (empty($opdata) || empty($optionid)) {
					show_json(0, '商品' . $data['title'] . '的规格不存在,请到购物车删除该商品重新选择规格!');
				}
			}

			$rank = intval($_SESSION[$goodsid . '_rank']);
			$log_id = intval($_SESSION[$goodsid . '_log_id']);
			$join_id = intval($_SESSION[$goodsid . '_join_id']);

			if (p('task')) {
				$task_id = intval($_SESSION[$goodsid . '_task_id']);

				if (!empty($task_id)) {
					$rewarded = pdo_fetchcolumn('SELECT `rewarded` FROM ' . tablename('ewei_shop_task_extension_join') . ' WHERE id = :id AND openid = :openid AND uniacid = :uniacid', array(':id' => $task_id, ':uniacid' => $_W['uniacid'], ':openid' => $_W['openid']));
					$taskGoodsInfo0 = unserialize($rewarded);
					$taskGoodsInfo = $taskGoodsInfo0['goods'][$goodsid];
					if (!empty($optionid) && !empty($taskGoodsInfo['option']) && ($optionid == $taskGoodsInfo['option'])) {
						$taskgoodsprice = $taskGoodsInfo['price'];
					}
					else {
						if (empty($optionid)) {
							$taskgoodsprice = $taskGoodsInfo['price'];
						}
					}
				}
			}

			if ($data['seckillinfo'] && ($data['seckillinfo']['status'] == 0)) {
				$data['is_task_goods'] = 0;
				$tgoods = false;
			}
			else {
				$task_goods_data = m('goods')->getTaskGoods($openid, $goodsid, $rank, $log_id, $join_id, $optionid);

				if (empty($task_goods_data['is_task_goods'])) {
					$data['is_task_goods'] = 0;
				}
				else {
					$allow_sale = false;
					$tgoods['title'] = $data['title'];
					$tgoods['openid'] = $openid;
					$tgoods['goodsid'] = $goodsid;
					$tgoods['optionid'] = $optionid;
					$tgoods['total'] = $goodstotal;
					$data['is_task_goods'] = $task_goods_data['is_task_goods'];
					$data['is_task_goods_option'] = $task_goods_data['is_task_goods_option'];
					$data['task_goods'] = $task_goods_data['task_goods'];
				}
			}

			$merchid = $g['merchid'];
			$merch_array[$merchid]['goods'][] = $data['goodsid'];

			if (0 < $merchid) {
				$ismerch = 1;
			}

			$virtualid = $data['virtual'];
			$data['stock'] = $data['total'];
			$data['total'] = $goodstotal;

			if ($data['cash'] != 2) {
				$cash = 0;
			}

			if (!empty($packageid)) {
				$cash = $package['cash'];
			}

			$unit = (empty($data['unit']) ? '件' : $data['unit']);
			if ($data['seckillinfo'] && ($data['seckillinfo']['status'] == 0)) {
				$check_buy = plugin_run('seckill::checkBuy', $data['seckillinfo'], $data['title'], $data['unit']);

				if (is_error($check_buy)) {
					show_json(-1, $check_buy['message']);
				}
			}
			else {
				if ($data['type'] != 4) {
					if (0 < $data['minbuy']) {
						if ($goodstotal < $data['minbuy']) {
							show_json(0, $data['title'] . '<br/> ' . $data['minbuy'] . $unit . '起售!');
						}
					}

					if (0 < $data['maxbuy']) {
						if ($data['maxbuy'] < $goodstotal) {
							show_json(0, $data['title'] . '<br/> 一次限购 ' . $data['maxbuy'] . $unit . '!');
						}
					}
				}

				if (0 < $data['usermaxbuy']) {
					$order_goodscount = pdo_fetchcolumn('select ifnull(sum(og.total),0)  from ' . tablename('ewei_shop_order_goods') . ' og ' . ' left join ' . tablename('ewei_shop_order') . ' o on og.orderid=o.id ' . ' where og.goodsid=:goodsid and  o.status>=0 and o.openid=:openid  and og.uniacid=:uniacid ', array(':goodsid' => $data['goodsid'], ':uniacid' => $uniacid, ':openid' => $openid));

					if ($data['usermaxbuy'] <= $order_goodscount) {
						show_json(0, $data['title'] . '<br/> 最多限购 ' . $data['usermaxbuy'] . $unit . '!');
					}
				}

				if (!empty($data['is_task_goods'])) {
					if ($data['task_goods']['total'] < $goodstotal) {
						show_json(0, $data['title'] . '<br/> 任务活动优惠限购 ' . $data['task_goods']['total'] . $unit . '!');
					}
				}

				if ($data['istime'] == 1) {
					if (time() < $data['timestart']) {
						show_json(0, $data['title'] . '<br/> 限购时间未到!');
					}

					if ($data['timeend'] < time()) {
						show_json(0, $data['title'] . '<br/> 限购时间已过!');
					}
				}

				$levelid = intval($member['level']);
				$groupid = intval($member['groupid']);

				if ($data['buylevels'] != '') {
					$buylevels = explode(',', $data['buylevels']);

					if (!in_array($levelid, $buylevels)) {
						show_json(0, '您的会员等级无法购买<br/>' . $data['title'] . '!');
					}
				}

				if ($data['buygroups'] != '') {
					$buygroups = explode(',', $data['buygroups']);

					if (!in_array($groupid, $buygroups)) {
						show_json(0, '您所在会员组无法购买<br/>' . $data['title'] . '!');
					}
				}
			}

			if (p('exchange')) {
				$sql_condition = ',exchange_stock';
			}
			else {
				$sql_condition = '';
			}

			if ($data['type'] == 4) {
				if (!empty($g['wholesaleprice'])) {
					$data['wholesaleprice'] = intval($g['wholesaleprice']);
				}

				if (!empty($g['goodsalltotal'])) {
					$data['goodsalltotal'] = intval($g['goodsalltotal']);
				}

				$data['marketprice'] == 0;
				$intervalprice = iunserializer($data['intervalprice']);

				foreach ($intervalprice as $intervalprice) {
					if ($intervalprice['intervalnum'] <= $data['goodsalltotal']) {
						$data['marketprice'] = $intervalprice['intervalprice'];
					}
				}

				if ($data['marketprice'] == 0) {
					show_json(0, $data['title'] . '<br/> ' . $data['minbuy'] . $unit . '起批!');
				}
			}

			$data['diyformdataid'] = 0;
			$data['diyformdata'] = iserializer(array());
			$data['diyformfields'] = iserializer(array());

			if (intval($_GPC['fromcart']) == 1) {
				if ($diyform_plugin) {
					$cartdata = pdo_fetch('select id,diyformdataid,diyformfields,diyformdata from ' . tablename('ewei_shop_member_cart') . ' ' . ' where goodsid=:goodsid and optionid=:optionid and openid=:openid and deleted=0 order by id desc limit 1', array(':goodsid' => $data['goodsid'], ':optionid' => intval($data['optionid']), ':openid' => $openid));

					if (!empty($cartdata)) {
						$data['diyformdataid'] = $cartdata['diyformdataid'];
						$data['diyformdata'] = $cartdata['diyformdata'];
						$data['diyformfields'] = $cartdata['diyformfields'];
					}
				}
			}
			else {
				if (!empty($data['diyformtype']) && $diyform_plugin) {
					$temp_data = $diyform_plugin->getOneDiyformTemp($_GPC['gdid'], 0);
					$data['diyformfields'] = $temp_data['diyformfields'];
					$data['diyformdata'] = $temp_data['diyformdata'];

					if ($data['diyformtype'] == 2) {
						$data['diyformid'] = 0;
					}
					else {
						$data['diyformid'] = $data['diyformid'];
					}
				}
			}

			if ($data['seckillinfo'] && ($data['seckillinfo']['status'] == 0)) {
				$data['ggprice'] = $gprice = $data['seckillinfo']['price'] * $goodstotal;
				$seckill_payprice += $gprice;
				$seckill_price += ($data['marketprice'] * $goodstotal) - $gprice;
				$goodsprice += $data['marketprice'] * $goodstotal;
				$data['taskdiscountprice'] = 0;
				$data['lotterydiscountprice'] = 0;
				$data['discountprice'] = 0;
				$data['discountprice'] = 0;
				$data['discounttype'] = 0;
				$data['isdiscountunitprice'] = 0;
				$data['discountunitprice'] = 0;
				$data['price0'] = 0;
				$data['price1'] = 0;
				$data['price2'] = 0;
				$data['buyagainprice'] = 0;
			}
			else {
				$gprice = $data['marketprice'] * $goodstotal;
				$goodsprice += $gprice;
				$prices = m('order')->getGoodsDiscountPrice($data, $level);
				$data['ggprice'] = $prices['price'];
				$data['taskdiscountprice'] = $prices['taskdiscountprice'];
				$data['lotterydiscountprice'] = $prices['lotterydiscountprice'];
				$data['discountprice'] = $prices['discountprice'];
				$data['discountprice'] = $prices['discountprice'];
				$data['discounttype'] = $prices['discounttype'];
				$data['isdiscountunitprice'] = $prices['isdiscountunitprice'];
				$data['discountunitprice'] = $prices['discountunitprice'];
				$data['price0'] = $prices['price0'];
				$data['price1'] = $prices['price1'];
				$data['price2'] = $prices['price2'];
				$data['buyagainprice'] = $prices['buyagainprice'];
				$buyagainprice += $prices['buyagainprice'];
				$taskdiscountprice += $prices['taskdiscountprice'];
				$lotterydiscountprice += $prices['lotterydiscountprice'];

				if ($prices['discounttype'] == 1) {
					$isdiscountprice += $prices['isdiscountprice'];
					$discountprice += $prices['discountprice'];

					if (!empty($data['merchsale'])) {
						$merchisdiscountprice += $prices['isdiscountprice'];
						$discountprice_array[$merchid]['merchisdiscountprice'] += $prices['isdiscountprice'];
					}

					$discountprice_array[$merchid]['isdiscountprice'] += $prices['isdiscountprice'];
				}
				else {
					if ($prices['discounttype'] == 2) {
						$discountprice += $prices['discountprice'];
						$discountprice_array[$merchid]['discountprice'] += $prices['discountprice'];
					}
				}

				$discountprice_array[$merchid]['ggprice'] += $prices['ggprice'];
			}

			$threenprice = json_decode($data['threen'], 1);
			if ($threenprice && !empty($threenprice['price'])) {
				$data['ggprice'] -= $data['price0'] - $threenprice['price'];
			}
			else {
				if ($threenprice && !empty($threenprice['discount'])) {
					$data['ggprice'] -= ((10 - $threenprice['discount']) / 10) * $data['price0'];
				}
			}

			$merch_array[$merchid]['ggprice'] += $data['ggprice'];
			$totalprice += $data['ggprice'];

			if ($data['isverify'] == 2) {
				$isverify = true;
				$verifytype = $data['verifytype'];
				$isendtime = $data['isendtime'];

				if ($isendtime == 0) {
					if (0 < $data['usetime']) {
						$endtime = time() + (3600 * 24 * intval($data['usetime']));
					}
					else {
						$endtime = 0;
					}
				}
				else {
					$endtime = $data['endtime'];
				}
			}

			if (!empty($data['virtual']) || ($data['type'] == 2) || ($data['type'] == 3) || ($data['type'] == 20)) {
				$isvirtual = true;
				if (($data['type'] == 20) && p('ccard')) {
					$ccard = 1;
				}

				if ($data['virtualsend']) {
					$isvirtualsend = true;
				}
			}

			if ($data['seckillinfo'] && ($data['seckillinfo']['status'] == 0)) {
			}
			else {
				if ((0 < floatval($data['buyagain'])) && empty($data['buyagain_sale'])) {
					if (m('goods')->canBuyAgain($data)) {
						$data['deduct'] = 0;
						$saleset = false;
					}
				}

				if ($open_redis) {
					if ($data['manydeduct']) {
						$deductprice += $data['deduct'] * $data['total'];
					}
					else {
						$deductprice += $data['deduct'];
					}

					if ($data['deduct2'] == 0) {
						$deductprice2 += $data['ggprice'];
					}
					else {
						if (0 < $data['deduct2']) {
							if ($data['ggprice'] < $data['deduct2']) {
								$deductprice2 += $data['ggprice'];
							}
							else {
								$deductprice2 += $data['deduct2'];
							}
						}
					}
				}
			}

			$virtualsales += $data['sales'];
			$allgoods[] = $data;
		}

		$grprice = $totalprice;
		if ((1 < count($goods)) && !empty($tgoods)) {
			show_json(0, '任务活动优惠商品' . $tgoods['title'] . '不能放入购物车下单,请单独购买');
		}

		if (empty($allgoods)) {
			show_json(0, '未找到任何商品');
		}

		if ($_GPC['couponid']) {
			if (is_numeric($_GPC['couponid'])) {
				$couponid = intval($_GPC['couponid']);
			}else{
				$couponid = array(
					'totalcoupon'=>$_GPC['totalcoupon'],
					'couponids'=>$_GPC['couponid'],
				);
			}
		}
		$contype = intval($_GPC['contype']);
		$wxid = intval($_GPC['wxid']);
		$wxcardid = $_GPC['wxcardid'];
		$wxcode = $_GPC['wxcode'];

		if ($contype == 1) {
			$ref = com_run('wxcard::wxCardGetCodeInfo', $wxcode, $wxcardid);

			if (!is_wxerror($ref)) {
				$ref = com_run('wxcard::wxCardConsume', $wxcode, $wxcardid);

				if (is_wxerror($ref)) {
					show_json(0, '您的卡券未到使用日期或已经超出使用次数限制!');
				}
			}
			else {
				show_json(0, '您的卡券未到使用日期或已经超出使用次数限制!');
			}
		}

		if ($is_openmerch == 1) {
			foreach ($merch_array as $key => $value) {
				if (0 < $key) {
					$merch_array[$key]['set'] = $merch_plugin->getSet('sale', $key);
					$merch_array[$key]['enoughs'] = $merch_plugin->getEnoughs($merch_array[$key]['set']);
				}
			}

			if ($allow_sale) {
				$merch_enough = m('order')->getMerchEnough($merch_array);
				$merch_array = $merch_enough['merch_array'];
				$merch_enough_total = $merch_enough['merch_enough_total'];
				$merch_saleset = $merch_enough['merch_saleset'];

				if (0 < $merch_enough_total) {
					$totalprice -= $merch_enough_total;
				}
			}
		}

		$deductenough = 0;

		if ($saleset) {
			foreach ($saleset['enoughs'] as $e) {
				if ((floatval($e['enough']) <= $totalprice - $seckill_payprice) && (0 < floatval($e['money']))) {
					$deductenough = floatval($e['money']);

					if (($totalprice - $seckill_payprice) < $deductenough) {
						$deductenough = $totalprice - $seckill_payprice;
					}

					break;
				}
			}
		}

		$goodsdata_coupon = array();
		$goodsdata_coupon_temp = array();

		foreach ($allgoods as $g) {
			if ($g['seckillinfo'] && ($g['seckillinfo']['status'] == 0)) {
				$goodsdata_coupon_temp[] = $g;
			}
			else if (0 < floatval($g['buyagain'])) {
				if (!m('goods')->canBuyAgain($g) || !empty($g['buyagain_sale'])) {
					$goodsdata_coupon[] = $g;
				}
				else {
					$goodsdata_coupon_temp[] = $g;
				}
			}
			else {
				$goodsdata_coupon[] = $g;
			}
		}

		$return_array = $this->caculatecoupon($contype, $couponid, $wxid, $wxcardid, $wxcode, $goodsdata_coupon, $totalprice, $discountprice, $isdiscountprice, 1, $discountprice_array, $merchisdiscountprice);
		$couponprice = 0;
		$coupongoodprice = 0;

		if (!empty($return_array)) {
			$isdiscountprice = $return_array['isdiscountprice'];
			$discountprice = $return_array['discountprice'];
			$couponprice = $return_array['deductprice'];
			$totalprice = $return_array['totalprice'];
			$discountprice_array = $return_array['discountprice_array'];
			$merchisdiscountprice = $return_array['merchisdiscountprice'];
			$coupongoodprice = $return_array['coupongoodprice'];
			$couponmerchid = $return_array['couponmerchid'];
			$allgoods = $return_array['$goodsarr'];
			$allgoods = array_merge($allgoods, $goodsdata_coupon_temp);
		}

		$addressid = intval($_GPC['addressid']);
		$address = false;
		if (!empty($addressid) && ($dispatchtype == 0) && !$isonlyverifygoods) {
			$address = pdo_fetch('select * from ' . tablename('ewei_shop_member_address') . ' where id=:id and openid=:openid and uniacid=:uniacid   limit 1', array(':uniacid' => $uniacid, ':openid' => $openid, ':id' => $addressid));
		}

		if (!$isvirtual && !$isverify && !$isonlyverifygoods && ($dispatchtype == 0) && !$isonlyverifygoods) {

			$dispatch_array = m('order')->getOrderDispatchPrice($allgoods, $member, $address, $saleset, $merch_array, 2);
			$dispatch_price = $dispatch_array['dispatch_price'] - $dispatch_array['seckill_dispatch_price'];
			$seckill_dispatchprice = $dispatch_array['seckill_dispatch_price'];
			$nodispatch_array = $dispatch_array['nodispatch_array'];

			if (!empty($nodispatch_array['isnodispatch'])) {
				show_json(0, $nodispatch_array['nodispatch']);
			}
		}

		if ($isonlyverifygoods) {
			$addressid = 0;
		}

		$totalprice -= $deductenough;
		$totalprice += $dispatch_price + $seckill_dispatchprice;
		if ($saleset && empty($saleset['dispatchnodeduct'])) {
			$deductprice2 += $dispatch_price;
		}

		if (empty($goods[0]['bargain_id'])) {
			$deductcredit = 0;
			$deductmoney = 0;
			$deductcredit2 = 0;

			if ($sale_plugin) {
				if (!empty($_GPC['deduct'])) {
					$credit = $member['credit1'];

					if (0 < $credit) {
						$credit = floor($credit);
					}

					if (!empty($saleset['creditdeduct'])) {
						$pcredit = intval($saleset['credit']);
						$pmoney = round(floatval($saleset['money']), 2);
						if ((0 < $pcredit) && (0 < $pmoney)) {
							if (($credit % $pcredit) == 0) {
								$deductmoney = round(intval($credit / $pcredit) * $pmoney, 2);
							}
							else {
								$deductmoney = round((intval($credit / $pcredit) + 1) * $pmoney, 2);
							}
						}

						if ($deductprice < $deductmoney) {
							$deductmoney = $deductprice;
						}

						if (($totalprice - $seckill_payprice) < $deductmoney) {
							$deductmoney = $totalprice - $seckill_payprice;
						}

						$deductcredit = floor(($deductmoney / $pmoney) * $pcredit);
					}
				}

				$totalprice -= $deductmoney;
			}

			if (!empty($saleset['moneydeduct'])) {
				if (!empty($_GPC['deduct2'])) {
					$deductcredit2 = $member['credit2'];

					if (($totalprice - $seckill_payprice) < $deductcredit2) {
						$deductcredit2 = $totalprice - $seckill_payprice;
					}

					if ($deductprice2 < $deductcredit2) {
						$deductcredit2 = $deductprice2;
					}
				}

				$totalprice -= $deductcredit2;
			}
		}

		$verifyinfo = array();
		$verifycode = '';
		$verifycodes = array();
		if ($isverify || $dispatchtype) {
			if ($isverify) {
				if (($verifytype == 0) || ($verifytype == 1)) {
					$verifycode = random(8, true);

					while (1) {
						$count = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_order') . ' where verifycode=:verifycode and uniacid=:uniacid limit 1', array(':verifycode' => $verifycode, ':uniacid' => $_W['uniacid']));

						if ($count <= 0) {
							break;
						}

						$verifycode = random(8, true);
					}
				}
				else {
					if ($verifytype == 2) {
						$totaltimes = intval($allgoods[0]['total']);

						if ($totaltimes <= 0) {
							$totaltimes = 1;
						}

						$i = 1;

						while ($i <= $totaltimes) {
							$verifycode = random(8, true);

							while (1) {
								$count = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_order') . ' where concat(verifycodes,\'|\' + verifycode +\'|\' ) like :verifycodes and uniacid=:uniacid limit 1', array(':verifycodes' => '%' . $verifycode . '%', ':uniacid' => $_W['uniacid']));

								if ($count <= 0) {
									break;
								}

								$verifycode = random(8, true);
							}

							$verifycodes[] = '|' . $verifycode . '|';
							$verifyinfo[] = array('verifycode' => $verifycode, 'verifyopenid' => '', 'verifytime' => 0, 'verifystoreid' => 0);
							++$i;
						}
					}
				}
			}
			else {
				if ($dispatchtype) {
					$verifycode = random(8, true);

					while (1) {
						$count = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_order') . ' where verifycode=:verifycode and uniacid=:uniacid limit 1', array(':verifycode' => $verifycode, ':uniacid' => $_W['uniacid']));

						if ($count <= 0) {
							break;
						}

						$verifycode = random(8, true);
					}
				}
			}
		}

		$carrier = $_GPC['carriers'];
		$carriers = (is_array($carrier) ? iserializer($carrier) : iserializer(array()));

		if ($totalprice <= 0) {
			$totalprice = 0;
		}

		if (($ismerch == 0) || (($ismerch == 1) && (count($merch_array) == 1))) {
			$multiple_order = 0;
		}
		else {
			$multiple_order = 1;
		}

		if (0 < $ismerch) {
			$ordersn = m('common')->createNO('order', 'ordersn', 'ME');
		}
		else {
			$ordersn = m('common')->createNO('order', 'ordersn', 'SH');
		}

		if (!empty($goods[0]['bargain_id']) && p('bargain')) {
			$bargain_act = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_bargain_actor') . ' WHERE id = :id AND openid = :openid ', array(':id' => $goods[0]['bargain_id'], ':openid' => $_W['openid']));

			if (empty($bargain_act)) {
				exit('没有这个商品');
			}

			$totalprice = $bargain_act['now_price'] + $dispatch_price;
			$goodsprice = $bargain_act['now_price'];

			if (!pdo_update('ewei_shop_bargain_actor', array('status' => 1), array('id' => $goods[0]['bargain_id'], 'openid' => $_W['openid']))) {
				exit('下单失败');
			}

			$ordersn = substr_replace($ordersn, 'KJ', 0, 2);
		}

		$is_package = 0;

		if (!empty($packageid)) {
			$goodsprice = $packageprice;
			$dispatch_price = $package['freight'];
			$totalprice = $packageprice + $package['freight'];
			$is_package = 1;
		}

		if ($taskgoodsprice) {
			$totalprice = $taskgoodsprice;
			$goodsprice = $taskgoodsprice;

			if ($taskGoodsInfo0['goods'][$goodsid]['num'] <= 1) {
				unset($taskGoodsInfo0['goods'][$goodsid]);
			}
			else {
				--$taskGoodsInfo0['goods'][$goodsid]['num'];
			}

			pdo_update('ewei_shop_task_extension_join', array('rewarded' => serialize($taskGoodsInfo0)), array('id' => $task_id, 'uniacid' => $_W['uniacid'], 'openid' => $_W['openid']));
			unset($_SESSION[$goodsid . '_task_id']);
		}
		// 订单表数据
		$order = array();
		
		$order['ismerch'] = 1;
		$order['merchid'] = $_GPC['merchid'];
		// 如果开启了is_opensynchro，获取的值就为1
		$order['parentid'] = 0;
		$order['uniacid'] = $uniacid;
		$order['openid'] = $openid;
		$order['ordersn'] = $ordersn;
		$order['price'] = $totalprice;
		$order['oldprice'] = $totalprice;
		$order['grprice'] = $grprice;
		$order['taskdiscountprice'] = $taskdiscountprice;
		$order['lotterydiscountprice'] = $lotterydiscountprice;
		$order['discountprice'] = $discountprice;
		if (!empty($goods[0]['bargain_id']) && p('bargain')) {
			$order['discountprice'] = 0;
		}

		$order['isdiscountprice'] = $isdiscountprice;
		$order['merchisdiscountprice'] = $merchisdiscountprice;
		$order['cash'] = $cash;
		$order['status'] = 0;
		$order['remark'] = trim($_GPC['remark']);
		$order['addressid'] = empty($dispatchtype) ? $addressid : 0;
		$order['goodsprice'] = $goodsprice;
		$order['dispatchprice'] = $dispatch_price + $seckill_dispatchprice;
		if (!is_null($_SESSION['exchangeprice']) && !empty($_SESSION['exchange']) && p('exchange')) {
			$order['price'] = $_SESSION['exchangeprice'] + $_SESSION['exchangepostage'];
			$order['ordersn'] = m('common')->createNO('order', 'ordersn', 'DH');
			$order['goodsprice'] = $_SESSION['exchangeprice'];
			$order['dispatchprice'] = $_SESSION['exchangepostage'];
		}

		$order['dispatchtype'] = $dispatchtype;
		$order['dispatchid'] = $dispatchid;
		$order['storeid'] = $carrierid;
		$order['carrier'] = $carriers;
		$order['createtime'] = time();
		$order['olddispatchprice'] = $dispatch_price + $seckill_dispatchprice;
		$order['contype'] = $contype;
		// var_dump($_GPC['couponid']);die;
		$order['couponid'] = $_GPC['couponid'];
		$order['wxid'] = $wxid;
		$order['wxcardid'] = $wxcardid;
		$order['wxcode'] = $wxcode;
		$order['couponmerchid'] = $couponmerchid;
		$order['paytype'] = 0;
		$order['deductprice'] = $deductmoney;
		$order['deductcredit'] = $deductcredit;
		$order['deductcredit2'] = $deductcredit2;
		$order['deductenough'] = $deductenough;
		$order['merchdeductenough'] = $merch_enough_total;
		$order['couponprice'] = $couponprice;
		$order['merchshow'] = 0;
		$order['buyagainprice'] = $buyagainprice;
		$order['ispackage'] = $is_package;
		$order['packageid'] = $packageid;
		$order['seckilldiscountprice'] = $seckill_price;
		$order['quickid'] = intval($_GPC['fromquick']);
		$order['liveid'] = $liveid;
		$order['machineid'] = empty($_GPC['machineid']) ? 0 : $_GPC['machineid'];
		if (!empty($ccard)) {
			$order['ccard'] = 1;
		}

		$author = p('author');

		if ($author) {
			$author_set = $author->getSet();
			if (!empty($member['agentid']) && !empty($member['authorid'])) {
				$order['authorid'] = $member['authorid'];
			}

			if (!empty($author_set['selfbuy']) && !empty($member['isauthor']) && !empty($member['authorstatus'])) {
				$order['authorid'] = $member['id'];
			}
		}
		// 找到多商户的merchid，然后把订单的merchid值改为多商户的值
		$set = $this->getset();
		// var_dump($set);die;
		if ($multiple_order == 0) {
			$order_merchid = current(array_keys($merch_array));
			$order['merchid'] = intval($order_merchid);
			if ($merch_data['is_opensynchro'] == 1) {
			$order['merchid'] = $_GPC['merchid'];
		}
			$order['isparent'] = 0;
			$order['transid'] = '';
			$order['isverify'] = $isverify ? 1 : 0;
			$order['verifytype'] = $verifytype;
			$order['verifyendtime'] = $endtime;
			$order['verifycode'] = $verifycode;
			$order['verifycodes'] = implode('', $verifycodes);
			$order['verifyinfo'] = iserializer($verifyinfo);
			$order['virtual'] = $virtualid;
			$order['isvirtual'] = $isvirtual ? 1 : 0;
			$order['isvirtualsend'] = $isvirtualsend ? 1 : 0;
			$order['invoicename'] = trim($_GPC['invoicename']);
		}
		else {
			$order['isparent'] = 1;
			$order['merchid'] = 0;
		}

		if ($diyform_plugin) {
			if (is_array($_GPC['diydata']) && !empty($order_formInfo)) {
				$diyform_data = $diyform_plugin->getInsertData($fields, $_GPC['diydata']);
				$idata = $diyform_data['data'];
				$order['diyformfields'] = iserializer($fields);
				$order['diyformdata'] = $idata;
				$order['diyformid'] = $order_formInfo['id'];
			}
		}

		if (!empty($address)) {
			$order['address'] = iserializer($address);
		}

		if (!empty($_GPC['isinvoice'])) {
			$order['invoice']=$_GPC['isinvoice'];
			$order['invoiceprice']=floatval($_GPC['invoicePrice']);
			$order['type']=$_GPC['invoiceType'];
			$order['price']=$order['price']+$order['invoiceprice'];
		}

		pdo_insert('ewei_shop_order', $order);
		$orderid = pdo_insertid();

		if (!empty($goods[0]['bargain_id']) && p('bargain')) {
			pdo_update('ewei_shop_bargain_actor', array('order' => $orderid), array('id' => $goods[0]['bargain_id'], 'openid' => $_W['openid']));
		}

		if ($multiple_order == 0) {
			$exchangepriceset = $_SESSION['exchangepriceset'];
			$exchangetitle = '';

			foreach ($allgoods as $goods) {
				$order_goods = array();
				if (!empty($bargain_act) && p('bargain')) {
					$goods['total'] = 1;
					$goods['ggprice'] = $bargain_act['now_price'];
					pdo_query('UPDATE ' . tablename('ewei_shop_goods') . ' SET sales = sales + 1 WHERE id = :id AND uniacid = :uniacid', array(':id' => $goods['goodsid'], ':uniacid' => $uniacid));
				}
				if (!empty($_GPC['experienceFee'])) {//体验费
					// $goods['marketprice'] = $_GPC['experienceFee'];
					// $goods['ggprice'] = $_GPC['experienceFee'];
				}
				$order_goods['merchid'] = $goods['merchid'];
				$order_goods['merchsale'] = $goods['merchsale'];
				$order_goods['uniacid'] = $uniacid;
				$order_goods['orderid'] = $orderid;
				$order_goods['goodsid'] = $goods['goodsid'];
				$order_goods['price'] = $goods['marketprice'] * $goods['total'];
				$order_goods['total'] = $goods['total'];
				$order_goods['optionid'] = $goods['optionid'];
				$order_goods['createtime'] = time();
				$order_goods['optionname'] = $goods['optiontitle'];
				$order_goods['goodssn'] = $goods['goodssn'];
				$order_goods['productsn'] = $goods['productsn'];
				$order_goods['realprice'] = $goods['ggprice'];
				$exchangetitle .= $goods['title'];
				if (p('exchange') && is_array($exchangepriceset)) {
					$order_goods['realprice'] = 0;

					foreach ($exchangepriceset as $ke => $va) {
						if (empty($goods['optionid']) && is_array($va) && ($goods['goodsid'] == $va[0]) && ($va[1] == 0)) {
							$order_goods['realprice'] = $va[2];
							break;
						}

						if (!empty($goods['optionid']) && is_array($va) && ($goods['optionid'] == $va[0]) && ($va[1] == 1)) {
							$order_goods['realprice'] = $va[2];
							break;
						}
					}
				}

				$order_goods['oldprice'] = $goods['ggprice'];

				if ($goods['discounttype'] == 1) {
					$order_goods['isdiscountprice'] = $goods['isdiscountprice'];
				}
				else {
					$order_goods['isdiscountprice'] = 0;
				}

				$order_goods['openid'] = $openid;

				if ($diyform_plugin) {
					if ($goods['diyformtype'] == 2) {
						$order_goods['diyformid'] = 0;
					}
					else {
						$order_goods['diyformid'] = $goods['diyformid'];
					}

					$order_goods['diyformdata'] = $goods['diyformdata'];
					$order_goods['diyformfields'] = $goods['diyformfields'];
				}

				if (0 < floatval($goods['buyagain'])) {
					if (!m('goods')->canBuyAgain($goods)) {
						$order_goods['canbuyagain'] = 1;
					}
				}

				if ($goods['seckillinfo'] && ($goods['seckillinfo']['status'] == 0)) {
					$order_goods['seckill'] = 1;
					$order_goods['seckill_taskid'] = $goods['seckillinfo']['taskid'];
					$order_goods['seckill_roomid'] = $goods['seckillinfo']['roomid'];
					$order_goods['seckill_timeid'] = $goods['seckillinfo']['timeid'];
				}

				pdo_insert('ewei_shop_order_goods', $order_goods);
				if ($goods['seckillinfo'] && ($goods['seckillinfo']['status'] == 0)) {
					plugin_run('seckill::setSeckill', $goods['seckillinfo'], $goods, $_W['openid'], $orderid, 0, $order['createtime']);
				}
			}
		}
		else {
			$og_array = array();
			$ch_order_data = m('order')->getChildOrderPrice($order, $allgoods, $dispatch_array, $merch_array, $sale_plugin, $discountprice_array);

			foreach ($merch_array as $key => $value) {
				$merchid = $key;

				if (!empty($merchid)) {
					$order_head = 'ME';
				}
				else {
					$order_head = 'SH';
				}

				$order['ordersn'] = m('common')->createNO('order', 'ordersn', $order_head);
				$order['merchid'] = $merchid;
				$order['parentid'] = $orderid;
				$order['isparent'] = 0;
				$order['merchshow'] = 1;
				$order['dispatchprice'] = $dispatch_array['dispatch_merch'][$merchid];
				$order['olddispatchprice'] = $dispatch_array['dispatch_merch'][$merchid];
				$order['merchisdiscountprice'] = $discountprice_array[$merchid]['merchisdiscountprice'];
				$order['isdiscountprice'] = $discountprice_array[$merchid]['isdiscountprice'];
				$order['discountprice'] = $discountprice_array[$merchid]['discountprice'];
				$order['price'] = $ch_order_data[$merchid]['price'];
				$order['grprice'] = $ch_order_data[$merchid]['grprice'];
				$order['goodsprice'] = $ch_order_data[$merchid]['goodsprice'];
				$order['deductprice'] = $ch_order_data[$merchid]['deductprice'];
				$order['deductcredit'] = $ch_order_data[$merchid]['deductcredit'];
				$order['deductcredit2'] = $ch_order_data[$merchid]['deductcredit2'];
				$order['merchdeductenough'] = $ch_order_data[$merchid]['merchdeductenough'];
				$order['deductenough'] = $ch_order_data[$merchid]['deductenough'];
				$order['coupongoodprice'] = $discountprice_array[$merchid]['coupongoodprice'];
				$order['couponprice'] = $discountprice_array[$merchid]['deduct'];

				if (empty($order['couponprice'])) {
					$order['couponid'] = 0;
					$order['couponmerchid'] = 0;
				}
				else {
					if (0 < $couponmerchid) {
						if ($merchid == $couponmerchid) {
							$order['couponid'] = $couponid;
							$order['couponmerchid'] = $couponmerchid;
						}
						else {
							$order['couponid'] = 0;
							$order['couponmerchid'] = 0;
						}
					}
				}

				pdo_insert('ewei_shop_order', $order);
				$ch_orderid = pdo_insertid();
				$merch_array[$merchid]['orderid'] = $ch_orderid;

				if (0 < $couponmerchid) {
					if ($merchid == $couponmerchid) {
						$couponorderid = $ch_orderid;
					}
				}

				foreach ($value['goods'] as $k => $v) {
					$og_array[$v] = $ch_orderid;
				}
			}

			foreach ($allgoods as $goods) {
				$goodsid = $goods['goodsid'];
				$order_goods = array();
				$order_goods['parentorderid'] = $orderid;
				$order_goods['merchid'] = $goods['merchid'];
				$order_goods['merchsale'] = $goods['merchsale'];
				$order_goods['orderid'] = $og_array[$goodsid];
				$order_goods['uniacid'] = $uniacid;
				$order_goods['goodsid'] = $goodsid;
				$order_goods['price'] = $goods['marketprice'] * $goods['total'];
				$order_goods['total'] = $goods['total'];
				$order_goods['optionid'] = $goods['optionid'];
				$order_goods['createtime'] = time();
				$order_goods['optionname'] = $goods['optiontitle'];
				$order_goods['goodssn'] = $goods['goodssn'];
				$order_goods['productsn'] = $goods['productsn'];
				$order_goods['realprice'] = $goods['ggprice'];
				$order_goods['oldprice'] = $goods['ggprice'];
				$order_goods['isdiscountprice'] = $goods['isdiscountprice'];
				$order_goods['openid'] = $openid;

				if ($diyform_plugin) {
					if ($goods['diyformtype'] == 2) {
						$order_goods['diyformid'] = 0;
					}
					else {
						$order_goods['diyformid'] = $goods['diyformid'];
					}

					$order_goods['diyformdata'] = $goods['diyformdata'];
					$order_goods['diyformfields'] = $goods['diyformfields'];
				}

				if (0 < floatval($goods['buyagain'])) {
					if (!m('goods')->canBuyAgain($goods)) {
						$order_goods['canbuyagain'] = 1;
					}
				}

				pdo_insert('ewei_shop_order_goods', $order_goods);
			}
		}

		if (!empty($_SESSION['exchange']) && p('exchange')) {
			$codeResult = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_exchange_code') . ' WHERE `key` = :key AND uniacid =:uniacid', array(':key' => $_SESSION['exchange_key'], ':uniacid' => $_W['uniacid']));

			if ($codeResult['status'] == 2) {
				show_json(0, '兑换失败:此兑换码已兑换');
			}

			$groupResult = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_exchange_group') . ' WHERE id = :id AND uniacid = :uniacid ', array(':uniacid' => $_W['uniacid'], ':id' => $codeResult['groupid']));
			$record_exsit = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_exchange_record') . ' WHERE `key`=:key AND uniacid = :uniacid', array(':key' => $_SESSION['exchange_key'], ':uniacid' => $_W['uniacid']));

			if (empty($record_exsit)) {
				$data = array('key' => $_SESSION['exchange_key'], 'uniacid' => $_W['uniacid'], 'title' => $_SESSION['exchangetitle'], 'serial' => $_SESSION['exchangeserial']);
				pdo_insert('ewei_shop_exchange_record', $data);
			}

			if (empty($_SESSION['exchange_key']) || is_null($_SESSION['exchangeprice']) || is_null($_SESSION['exchangepostage'])) {
				show_json(0, '兑换超时,请重试');
			}
			else {
				$checkSubmit = $this->checkSubmit('exchange_plugin');

				if (is_error($checkSubmit)) {
					show_json(0, $checkSubmit['message']);
				}

				$checkSubmit = $this->checkSubmitGlobal('exchange_key_' . $_SESSION['exchange_key']);

				if (is_error($checkSubmit)) {
					show_json(0, $checkSubmit['message']);
				}

				if ($groupResult['mode'] != 6) {
					$exchange_res = pdo_update('ewei_shop_exchange_code', array('status' => 2), array('key' => $_SESSION['exchange_key'], 'status' => 1, 'uniacid' => $_W['uniacid']));
					pdo_query('UPDATE ' . tablename('ewei_shop_exchange_group') . ' SET `use` = `use` + 1 WHERE id = :id AND uniacid = :uniacid', array(':id' => $_SESSION['groupid'], ':uniacid' => $_W['uniacid']));
				}
				else {
					pdo_update('ewei_shop_exchange_code', array('goodsstatus' => 2), array('key' => $_SESSION['exchange_key'], 'uniacid' => $_W['uniacid']));
					if ((($codeResult['balancestatus'] == 2) || empty($groupResult['balance_type'])) && (($codeResult['scorestatus'] == 2) || empty($groupResult['score_type'])) && (($codeResult['redstatus'] == 2) || empty($groupResult['red_type'])) && (($codeResult['couponstatus'] == 2) || empty($groupResult['coupon_type']))) {
						pdo_update('ewei_shop_exchange_code', array('status' => 2), array('key' => $_SESSION['exchange_key'], 'status' => 1, 'uniacid' => $_W['uniacid']));
					}
				}
			}
		}

		if (!is_null($_SESSION['exchangeprice']) && !empty($_SESSION['exchange']) && p('exchange')) {
			$exchangeinfo = m('member')->getInfo($_W['openid']);

			if ($groupResult['mode'] != 6) {
				$exchangedata = array('key' => $_SESSION['exchange_key'], 'uniacid' => $_W['uniacid'], 'goods' => $_SESSION['exchangegoods'], 'orderid' => $orderid, 'time' => time(), 'openid' => $_W['openid'], 'mode' => 1, 'nickname' => $exchangeinfo['nickname'], 'groupid' => $_SESSION['groupid'], 'title' => $_SESSION['exchangetitle'], 'serial' => $_SESSION['exchangeserial'], 'ordersn' => $order['ordersn'], 'goods_title' => $exchangetitle);
				pdo_update('ewei_shop_exchange_record', $exchangedata, array('key' => $_SESSION['exchange_key'], 'uniacid' => $_W['uniacid']));
			}
			else {
				$exchangedata = array('goods' => $_SESSION['exchangegoods'], 'orderid' => $orderid, 'time' => time(), 'openid' => $_W['openid'], 'mode' => 6, 'nickname' => $exchangeinfo['nickname'], 'ordersn' => $order['ordersn'], 'goods_title' => $exchangetitle);
				pdo_update('ewei_shop_exchange_record', $exchangedata, array('key' => $_SESSION['exchange_key'], 'uniacid' => $_W['uniacid']));
			}

			pdo_update('ewei_shop_exchange_cart', array('selected' => 0), array('uniacid' => $_W['uniacid'], 'openid' => $_W['openid'], 'selected' => 1));
		}

		if (com('coupon') && !empty($orderid)) {
			com('coupon')->addtaskdata($orderid);
		}

		if (is_array($carrier)) {
			$up = array('realname' => $carrier['carrier_realname'], 'carrier_mobile' => $carrier['carrier_mobile']);

			pdo_update('ewei_shop_member', $up, array('id' => $member['id'], 'uniacid' => $_W['uniacid']));

			if (!empty($member['uid'])) {
				load()->model('mc');
				mc_update($member['uid'], $up);
			}
		}

		if ($_GPC['fromcart'] == 1) {
			pdo_query('update ' . tablename('ewei_shop_member_cart') . ' set deleted=1 where  openid=:openid and uniacid=:uniacid and selected=1 ', array(':uniacid' => $uniacid, ':openid' => $openid));
		}

		if (p('quick') && !empty($_GPC['fromquick'])) {
			pdo_update('ewei_shop_quick_cart', array('deleted' => 1), array('quickid' => intval($_GPC['fromquick']), 'uniacid' => $_W['uniacid'], 'openid' => $_W['openid']));
		}

		if (0 < $deductcredit) {
			m('member')->setCredit($openid, 'credit1', 0 - $deductcredit, array('0', $_W['shopset']['shop']['name'] . '购物'.$_W['shopset']['trade']['credittext'].'抵扣 消费'.$_W['shopset']['trade']['credittext'].': ' . $deductcredit . ' 抵扣金额: ' . $deductmoney . ' 订单号: ' . $ordersn));
		}

		if (0 < $buyagainprice) {
			m('goods')->useBuyAgain($orderid);
		}

		if (0 < $deductcredit2) {
			m('member')->setCredit($openid, 'credit2', 0 - $deductcredit2, array('0', $_W['shopset']['shop']['name'] . '购物余额抵扣: ' . $deductcredit2 . ' 订单号: ' . $ordersn));
		}

		if (empty($virtualid)) {
			m('order')->setStocksAndCredits($orderid, 0);
		}
		else {
			if (isset($allgoods[0])) {
				$vgoods = $allgoods[0];
				pdo_update('ewei_shop_goods', array('sales' => $vgoods['sales'] + $vgoods['total']), array('id' => $vgoods['goodsid']));
			}
		}

		$plugincoupon = com('coupon');

		if ($plugincoupon) {
			if ((0 < $couponmerchid) && ($multiple_order == 1)) {
				$oid = $couponorderid;
			}
			else {
				$oid = $orderid;
			}

			$plugincoupon->useConsumeCoupon($oid);
		}

		if (!empty($tgoods)) {
			$rank = intval($_SESSION[$tgoods['goodsid'] . '_rank']);
			$log_id = intval($_SESSION[$tgoods['goodsid'] . '_log_id']);
			$join_id = intval($_SESSION[$tgoods['goodsid'] . '_join_id']);
			m('goods')->getTaskGoods($tgoods['openid'], $tgoods['goodsid'], $rank, $log_id, $join_id, $tgoods['optionid'], $tgoods['total']);
			$_SESSION[$tgoods['goodsid'] . '_rank'] = 0;
			$_SESSION[$tgoods['goodsid'] . '_log_id'] = 0;
			$_SESSION[$tgoods['goodsid'] . '_join_id'] = 0;
		}

		m('notice')->sendOrderMessage($orderid);
		com_run('printer::sendOrderMessage', $orderid);

		if (p('exchange')) {
			@session_destroy();
		}

		$wechat = array('success' => false);

		if (is_weixin()) {
			$log = pdo_fetch('SELECT * FROM ' . tablename('core_paylog') . ' WHERE `uniacid`=:uniacid AND `module`=:module AND `tid`=:tid limit 1', array(':uniacid' => $uniacid, ':module' => 'ewei_shopv2', ':tid' => $order['ordersn']));

			if (!empty($log) && ($log['status'] == '0')) {
				pdo_delete('core_paylog', array('plid' => $log['plid']));
				$log = NULL;
			}

			if (empty($log)) {
				$log = array('uniacid' => $uniacid, 'openid' => $member['uid'], 'module' => 'ewei_shopv2', 'tid' => $order['ordersn'], 'fee' => $order['price'], 'status' => 0);
				pdo_insert('core_paylog', $log);
				$plid = pdo_insertid();
			}

			$set = m('common')->getSysset(array('shop', 'pay'));
			$set['pay']['weixin'] = !empty($set['pay']['weixin_sub']) ? 1 : $set['pay']['weixin'];
			$set['pay']['weixin_jie'] = !empty($set['pay']['weixin_jie_sub']) ? 1 : $set['pay']['weixin_jie'];
			$param_title = $set['shop']['name'] . '订单';
			$credit = array('success' => false);

			load()->model('payment');
			$setting = uni_setting($_W['uniacid'], array('payment'));
			$sec = m('common')->getSec();
			$sec = iunserializer($sec['sec']);

			$params = array();
			$params['tid'] = $log['tid'];

			if (!empty($order['ordersn2'])) {
				$var = sprintf('%02d', $order['ordersn2']);
				$params['tid'] .= 'GJ' . $var;
			}

			$params['user'] = $openid;
			$params['fee'] = $order['price'];

			
			$params['title'] = $param_title;
			if (isset($set['pay']) && ($set['pay']['weixin'] == 1) && ($jie !== 1)) {
				$options = array();
				if (is_array($setting['payment']['wechat']) && $setting['payment']['wechat']['switch']) {
					load()->model('payment');
					$setting = uni_setting($_W['uniacid'], array('payment'));

					if (is_array($setting['payment'])) {
						$options = $setting['payment']['wechat'];
						$options['appid'] = $_W['account']['key'];
						$options['secret'] = $_W['account']['secret'];
					}
				}

				$wechat = m('common')->wechat_build($params, $options, 0);

				if (!is_error($wechat)) {
					$wechat['success'] = true;

					if (!empty($wechat['code_url'])) {
						$wechat['weixin_jie'] = true;
					}
					else {
						$wechat['weixin'] = true;
					}
				}
			}

	
		}

		/*如果开启了返利奖励模式*/

		$pluginc = p('commission');
		if ($pluginc) {
			if ($multiple_order == 0) {
				$pluginc->checkOrderConfirm($orderid);
			}
			else {
				if (!empty($merch_array)) {
					foreach ($merch_array as $key => $value) {
						$pluginc->checkOrderConfirm($value['orderid']);
					}
				}
			}
		}

		unset($_SESSION[$openid . '_order_create']);

		if (p('exchange')) {
			@session_destroy();
		}

		show_json(1, array('orderid' => $orderid,'wechat' => $wechat));
	}

	protected function merchData()
	{
		$merch_plugin = p('merch');
		$merch_data = m('common')->getPluginset('merch');
		// var_dump($merch_data);die;
		// 可以拿到is_opensynchro的值

		if ($merch_plugin && $merch_data['is_openmerch']) {
			$is_openmerch = 1;
		}
		else {
			$is_openmerch = 0;
		}

		return array('is_openmerch' => $is_openmerch, 'merch_plugin' => $merch_plugin, 'merch_data' => $merch_data);
	}

	protected function diyformData($member)
	{
		global $_W;
		global $_GPC;

		$diyform_plugin = p('diyform');
		$order_formInfo = false;
		$diyform_set = false;
		$orderdiyformid = 0;
		$fields = array();
		$f_data = array();

		if ($diyform_plugin) {
			$diyform_set = $_W['shopset']['diyform'];

			if (!empty($diyform_set['order_diyform_open'])) {
				$orderdiyformid = intval($diyform_set['order_diyform']);

				if (!empty($orderdiyformid)) {
					$order_formInfo = $diyform_plugin->getDiyformInfo($orderdiyformid);
					$fields = $order_formInfo['fields'];
					$f_data = $diyform_plugin->getLastOrderData($orderdiyformid, $member);
				}
			}
		}

		return array('diyform_plugin' => $diyform_plugin, 'order_formInfo' => $order_formInfo, 'diyform_set' => $diyform_set, 'orderdiyformid' => $orderdiyformid, 'fields' => $fields, 'f_data' => $f_data);
	}

	public function orderstatus()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$orderid = intval($_GPC['id']);
		$order = pdo_fetch('select status from ' . tablename('ewei_shop_order') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $orderid, ':uniacid' => $uniacid));

		if (1 <= $order['status']) {
			@session_start();
			$_SESSION[EWEI_SHOPV2_PREFIX . '_order_pay_complete'] = 1;
			show_json(1);
		}

		show_json(0);
	}

	public function complete()
	{
		global $_W;
		global $_GPC;
		$orderid = intval($_GPC['id']);
		$uniacid = $_W['uniacid'];
		$openid = $_W['openid'];
		$type = $_GPC['paytype'];
		$order = pdo_fetch('select * from ' . tablename('ewei_shop_order') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $orderid, ':uniacid' => $uniacid));

		$member = m('member')->getMember($openid, true);
		$peerPaySwi = m('common')->getPluginset('sale');
		$peerPaySwi = $peerPaySwi['peerpay']['open'];
		$ispeerpay = m('order')->checkpeerpay($orderid);
		if (!empty($order['istrade'])) {
			$ispeerpay = 0;
		}

		$og_array = m('order')->checkOrderGoods($orderid);

		$tradestatus = $order['tradestatus'];

		// if (empty($order['istrade'])) {
		// 	if ($order['status'] == -1) {
		// 		header('location: ' . mobileUrl('order/detail', array('id' => $order['id'])));
		// 		exit();
		// 	}
		// 	else {
		// 		if (1 <= $order['status']) {
		// 			header('location: ' . mobileUrl('order/detail', array('id' => $order['id'])));
		// 			exit();
		// 		}
		// 	}
		// }
		// else {
		// 	if (($order['status'] == 1) && ($order['tradestatus'] == 1)) {
		// 		$order['ordersn'] = $order['ordersn_trade'];
		// 		$order['price'] = $order['betweenprice'];
		// 	}
		// 	else {
		// 		if (($order['status'] == 1) && ($order['tradestatus'] == 2)) {
		// 			header('location: ' . mobileUrl('newstore/norder/detail', array('id' => $order['id'])));
		// 			exit();
		// 		}
		// 		else {
		// 			if ($order['status'] == 0) {
		// 				$order['price'] = $order['dowpayment'];
		// 			}
		// 		}
		// 	}
		// }

		$log = pdo_fetch('SELECT * FROM ' . tablename('core_paylog') . ' WHERE `uniacid`=:uniacid AND `module`=:module AND `tid`=:tid limit 1', array(':uniacid' => $uniacid, ':module' => 'ewei_shopv2', ':tid' => $order['ordersn']));

		$seckill_goods = pdo_fetchall('select goodsid,optionid,seckill from  ' . tablename('ewei_shop_order_goods') . ' where orderid=:orderid and uniacid=:uniacid and seckill=1 ', array(':uniacid' => $_W['uniacid'], ':orderid' => $orderid));

		if (!empty($log) && ($log['status'] == '0')) {
			pdo_delete('core_paylog', array('plid' => $log['plid']));
			$log = NULL;
		}

		if (empty($log)) {
			$log = array('uniacid' => $uniacid, 'openid' => $member['uid'], 'module' => 'ewei_shopv2', 'tid' => $order['ordersn'], 'fee' => $order['price'], 'status' => 0);

			pdo_insert('core_paylog', $log);
			$plid = pdo_insertid();
		}

		$set = m('common')->getSysset(array('shop', 'pay'));
		$set['pay']['weixin'] = !empty($set['pay']['weixin_sub']) ? 1 : $set['pay']['weixin'];
		$set['pay']['weixin_jie'] = !empty($set['pay']['weixin_jie_sub']) ? 1 : $set['pay']['weixin_jie'];
		$param_title = $set['shop']['name'] . '订单';
		$credit = array('success' => false);

		$goods = pdo_fetchall('select g.* from ' .tablename('ewei_shop_goods')."as g LEFT JOIN " .tablename('ewei_shop_order_goods') . ' as og  on g.id=og.goodsid where og.orderid=:orderid and g.uniacid=:uniacid ', array(':uniacid' => $_W['uniacid'], ':orderid' => $orderid));

		$cancredit=true;
		foreach ($goods as $g) {
			if ($g['deduct2']<0) {
				$set['pay']['credit']=0;
			}
		}

		if (isset($set['pay']) && ($set['pay']['credit'] == 1)) {
			$credit = array('success' => true, 'current' => $member['credit2']);
		}

		$order['price'] = floatval($order['price']);

		if (empty($order['price']) && !$credit['success']) {
			header('location: ' . mobileUrl('order/pay/complete', array('id' => $order['id'], 'type' => 'credit', 'ordersn' => $order['ordersn'])));
			exit();
		}

		load()->model('payment');
		$setting = uni_setting($_W['uniacid'], array('payment'));
		$sec = m('common')->getSec();
		$sec = iunserializer($sec['sec']);
		$wechat = array('success' => false);
		$jie = intval($_GPC['jie']);

		if (is_weixin()) {
			$params = array();
			$params['tid'] = $log['tid'];

			if (!empty($order['ordersn2'])) {
				$var = sprintf('%02d', $order['ordersn2']);
				$params['tid'] .= 'GJ' . $var;
			}

			$params['user'] = $openid;
			$params['fee'] = $order['price'];

			if (!empty($ispeerpay)) {
				$params['fee'] = $peerprice;
				$params['tid'] = $params['tid'] . $member['id'] . str_replace('.', '', $params['fee']);
				@session_start();
				$_SESSION['peerpaytid'] = $params['tid'];
			}

			$params['title'] = $param_title;
			if (isset($set['pay']) && ($set['pay']['weixin'] == 1) && ($jie !== 1)) {
				$options = array();
				if (is_array($setting['payment']['wechat']) && $setting['payment']['wechat']['switch']) {
					load()->model('payment');
					$setting = uni_setting($_W['uniacid'], array('payment'));

					if (is_array($setting['payment'])) {
						$options = $setting['payment']['wechat'];
						$options['appid'] = $_W['account']['key'];
						$options['secret'] = $_W['account']['secret'];
					}
				}

				$wechat = m('common')->wechat_build($params, $options, 0);

				if (!is_error($wechat)) {
					$wechat['success'] = true;

					if (!empty($wechat['code_url'])) {
						$wechat['weixin_jie'] = true;
					}
					else {
						$wechat['weixin'] = true;
					}
				}
			}

			if ((isset($set['pay']) && ($set['pay']['weixin_jie'] == 1) && !$wechat['success']) || ($jie === 1)) {
				if (!empty($order['ordersn2'])) {
					$params['tid'] = $params['tid'] . '_B';
				}
				else {
					$params['tid'] = $params['tid'] . '_borrow';
				}

				$options = array();
				$options['appid'] = $sec['appid'];
				$options['mchid'] = $sec['mchid'];
				$options['apikey'] = $sec['apikey'];
				if (!empty($set['pay']['weixin_jie_sub']) && !empty($sec['sub_secret_jie_sub'])) {
					$wxuser = m('member')->wxuser($sec['sub_appid_jie_sub'], $sec['sub_secret_jie_sub']);
					$params['openid'] = $wxuser['openid'];
				}
				else {
					if (!empty($sec['secret'])) {
						$wxuser = m('member')->wxuser($sec['appid'], $sec['secret']);
						$params['openid'] = $wxuser['openid'];
					}
				}

				$wechat = m('common')->wechat_native_build($params, $options, 0);
			}
		}

		$alipay = array('success' => false);
		if (empty($seckill_goods) && empty($ispeerpay)) {
		}
		else {
			$cash = array('success' => false);
		}

		$payinfo = array('orderid' => $orderid, 'ordersn' => $log['tid'], 'credit' => $credit, 'alipay' => $alipay, 'wechat' => $wechat, 'cash' => $cash, 'money' => $order['price'],'backurl'=>$backurl);

		if (is_h5app()) {
			$payinfo = array('wechat' => !empty($sec['app_wechat']['merchname']) && !empty($set['pay']['app_wechat']) && !empty($sec['app_wechat']['appid']) && !empty($sec['app_wechat']['appsecret']) && !empty($sec['app_wechat']['merchid']) && !empty($sec['app_wechat']['apikey']) && (0 < $order['price']) ? true : false, 'alipay' => !empty($set['pay']['app_alipay']) && !empty($sec['app_alipay']['public_key']) ? true : false, 'mcname' => $sec['app_wechat']['merchname'], 'aliname' => empty($_W['shopset']['shop']['name']) ? $sec['app_wechat']['merchname'] : $_W['shopset']['shop']['name'], 'ordersn' => $log['tid'], 'money' => $order['price'], 'attach' => $_W['uniacid'] . ':0', 'type' => 0, 'orderid' => $orderid, 'credit' => $credit, 'cash' => $cash,'backurl'=>$backurl);

			if (!empty($order['ordersn2'])) {
				$var = sprintf('%02d', $order['ordersn2']);
				$payinfo['ordersn'] .= 'GJ' . $var;
			}
		}

		if (p('seckill')) {
			foreach ($seckill_goods as $data) {
				plugin_run('seckill::getSeckill', $data['goodsid'], $data['optionid'], true, $_W['openid']);
			}
		}
		$ispeerpay = m('order')->checkpeerpay($orderid);
		
		if(!$ispeerpay){
			$_SESSION['peerpay'] = NULL;
		}


		if (is_h5app() && empty($orderid)) {
			if (strexists($gpc_ordersn, 'GJ')) {
				$ordersns = explode('GJ', $gpc_ordersn);
				$ordersn = $ordersns[0];
			}
			else {
				$ordersn = $gpc_ordersn;
			}

			$ordersn = rtrim($ordersn, 'TR');
			$orderid = pdo_fetchcolumn('select id from ' . tablename('ewei_shop_order') . ' where ordersn=:ordersn and uniacid=:uniacid and openid=:openid limit 1', array(':ordersn' => $ordersn, ':uniacid' => $uniacid, ':openid' => $openid));
		}

		if (empty($orderid)) {
			if ($_W['ispost']) {
				show_json(0, '参数错误');
			}
			else {
				$this->message('参数错误', mobileUrl('order'));
			}
		}

		$set = m('common')->getSysset(array('shop', 'pay'));
		$set['pay']['weixin'] = !empty($set['pay']['weixin_sub']) ? 1 : $set['pay']['weixin'];
		$set['pay']['weixin_jie'] = !empty($set['pay']['weixin_jie_sub']) ? 1 : $set['pay']['weixin_jie'];
		$member = m('member')->getMember($openid, true);

		if (!empty($gpc_ordersn)) {
			$order['ordersn'] = $gpc_ordersn;
		}

		$go_flag = 0;
		if (empty($order['istrade']) && (1 <= $order['status'])) {
			$go_flag = 1;
		}

		if (!empty($order['istrade'])) {
			if ((1 < $order['status']) || (($order['status'] == 1) && ($order['tradestatus'] == 2))) {
				$go_flag = 1;
			}
		}


		if ($go_flag == 1) {
			$pay_result = true;

			if ($_W['ispost']) {
				$_SESSION[EWEI_SHOPV2_PREFIX . '_order_pay_complete'] = 1;
				show_json(1, array('result' => $pay_result));
			}
			else {
				header('location:' . mobileUrl('paymentcode/success', array('id' => $order['id'], 'result' => $pay_result)));
				exit();
			}
		}

		if (empty($order)) {
			if ($_W['ispost']) {
				show_json(0, '订单未找到');
			}
			else {
				$this->message('订单未找到', mobileUrl('order'));
			}
		}

		

		if (!in_array($type, array('wechat', 'alipay', 'credit', 'cash','qukuailian'))) {
			if ($_W['ispost']) {
				show_json(0, '未找到支付方式');
			}
			else {
				$this->message('未找到支付方式', mobileUrl('order'));
			}
		}

		$log = pdo_fetch('SELECT * FROM ' . tablename('core_paylog') . ' WHERE `uniacid`=:uniacid AND `module`=:module AND `tid`=:tid limit 1', array(':uniacid' => $uniacid, ':module' => 'ewei_shopv2', ':tid' => $order['ordersn']));

		if (empty($log) && empty($ispeerpay)) {
			if ($_W['ispost']) {
				show_json(0, '支付出错,请重试!');
			}
			else {
				$this->message('支付出错,请重试!', mobileUrl('order'));
			}
		}

		if ((empty($order['isnewstore']) || empty($order['storeid'])) && empty($order['istrade'])) {
			$order_goods = pdo_fetchall('select og.id,g.title, og.goodsid,og.optionid,g.total as stock,og.total as buycount,g.status,g.deleted,g.maxbuy,g.usermaxbuy,g.istime,g.timestart,g.timeend,g.buylevels,g.buygroups,g.totalcnf,og.seckill from  ' . tablename('ewei_shop_order_goods') . ' og ' . ' left join ' . tablename('ewei_shop_goods') . ' g on og.goodsid = g.id ' . ' where og.orderid=:orderid and og.uniacid=:uniacid ', array(':uniacid' => $_W['uniacid'], ':orderid' => $orderid));

			foreach ($order_goods as $data) {
				if (empty($data['status']) || !empty($data['deleted'])) {
					if ($_W['ispost']) {
						show_json(0, $data['title'] . '<br/> 已下架!');
					}
					else {
						$this->message($data['title'] . '<br/> 已下架!', mobileUrl('order'));
					}
				}

				$unit = (empty($data['unit']) ? '件' : $data['unit']);
				$seckillinfo = plugin_run('seckill::getSeckill', $data['goodsid'], $data['optionid'], true, $_W['openid']);
			}
		}
		else if (p('newstore')) {
			$sql = 'select og.id,g.title, og.goodsid,og.optionid,ng.stotal as stock,og.total as buycount,ng.gstatus as status,ng.deleted,g.maxbuy,g.usermaxbuy,g.istime,g.timestart,g.timeend,g.buylevels,g.buygroups,g.totalcnf,og.seckill,og.storeid ' . ' from ' . tablename('ewei_shop_order_goods') . ' og left join  ' . tablename('ewei_shop_goods') . ' g  on g.id=og.goodsid and g.uniacid=og.uniacid' . ' inner join ' . tablename('ewei_shop_newstore_goods') . ' ng on g.id=ng.goodsid   ' . ' where og.orderid=:orderid and og.uniacid=:uniacid and ng.storeid=:storeid';
			$order_goods = pdo_fetchall($sql, array(':uniacid' => $uniacid, ':orderid' => $orderid, ':storeid' => $order['storeid']));
		}
		else if ($_W['ispost']) {
			show_json(0, '门店歇业,不能付款!');
		}
		else {
			$this->message('门店歇业,不能付款!', mobileUrl('order'));
		}

		$ps = array();
		$ps['tid'] = $log['tid'];
		$ps['user'] = $openid;
		$ps['fee'] = $log['fee'];
		$ps['title'] = $log['title'];
		if ($type == 'credit') {
			if (empty($set['pay']['credit']) && (0 < $ps['fee'])) {
				if ($_W['ispost']) {
					show_json(0, '未开启余额支付!');
				}
				else {
					$this->message('未开启余额支付', mobileUrl('order'));
				}
			}

			if ($ps['fee'] < 0) {
				if ($_W['ispost']) {
					show_json(0, '金额错误');
				}
				else {
					$this->message('金额错误', mobileUrl('order'));
				}
			}

			$credits = m('member')->getCredit($openid, 'credit2');

			if ($credits < $ps['fee']) {
				if ($_W['ispost']) {
					show_json(0, '余额不足,请充值');
				}
				else {
					$this->message('余额不足,请充值', mobileUrl('order'));
				}
			}

			$fee = floatval($ps['fee']);
			$result = m('member')->setCredit($openid, 'credit2', 0 - $fee, array($_W['member']['uid'], $_W['shopset']['shop']['name'] . '消费' . $fee));

			if (is_error($result)) {
				if ($_W['ispost']) {
					show_json(0, $result['message']);
				}
				else {
					$this->message($result['message'], mobileUrl('order'));
				}
			}

			$record = array();
			$record['status'] = '1';
			$record['type'] = 'cash';
			pdo_update('core_paylog', $record, array('plid' => $log['plid']));

			m('order')->setOrderPayType($order['id'], 1, $gpc_ordersn);
			$ret = array();
			$ret['result'] = 'success';
			$ret['type'] = $log['type'];
			$ret['from'] = 'return';
			$ret['tid'] = $log['tid'];
			$ret['user'] = $log['openid'];
			$ret['fee'] = $log['fee'];
			$ret['weid'] = $log['weid'];
			$ret['uniacid'] = $log['uniacid'];
			@session_start();
			$_SESSION[EWEI_SHOPV2_PREFIX . '_order_pay_complete'] = 1;
			if (!empty($ispeerpay)) {
				$peerheadimg = m('member')->getInfo($member['openid']);

				if (empty($peerheadimg['avatar'])) {
					$peerheadimg['avatar'] = 'http://of6odhdq1.bkt.clouddn.com/d7fd47dc6163ec00abfe644ab3c33ac6.jpg';
				}
				m('order')->peerStatus(array('pid' => $ispeerpay['id'], 'uid' => $member['id'], 'uname' => $member['nickname'], 'usay' => '', 'price' => $log['fee'], 'createtime' => time(), 'headimg' => $peerheadimg['avatar'], 'openid' => $peerheadimg['openid'], 'usay' => trim($_GPC['peerpaymessage'])));
			}

			$pay_result = m('order')->payResult($ret);

			if ($order['invoice']==1) {
				$orderID = $order["ordersn"];
				$item['payment']=$order['status'];
				pdo_update('ewei_shop_invoice', array('payment'=>$record['status']), array('orderID' => $orderID, 'uniacid' => $_W['uniacid']));
			}

			if ($_W['ispost']) {
				show_json(1, array('result' => $pay_result));
			}
			else {		
				header('location:' . mobileUrl('paymentcode/success', array('id' => $order['id'], 'result' => $pay_result)));
			}
		}
		else {
			if ($type == 'wechat') {

				if (!is_weixin() && empty($_W['shopset']['wap']['open'])) {
					if ($_W['ispost']) {
						show_json(0, is_h5app() ? 'APP正在维护' : '非微信环境!');
					}
					else {
						$this->message(is_h5app() ? 'APP正在维护' : '非微信环境!', mobileUrl('order'));
					}
				}

				if ((empty($set['pay']['weixin']) && empty($set['pay']['weixin_jie']) && is_weixin()) || (empty($set['pay']['app_wechat']) && is_h5app())) {
					if ($_W['ispost']) {
						show_json(0, '未开启微信支付!');
					}
					else {
						$this->message('未开启微信支付!', mobileUrl('order'));
					}
				}

				$ordersn = $order['ordersn'];

				if (!empty($order['ordersn2'])) {
					$ordersn .= 'GJ' . sprintf('%02d', $order['ordersn2']);
				}

				if (!empty($ispeerpay)) {
					$payquery = m('finance')->isWeixinPay($_SESSION['peerpaytid'], $order['price'], is_h5app() ? true : false);
					$payquery_jie = m('finance')->isWeixinPayBorrow($_SESSION['peerpaytid'], $order['price']);
				}
				else {
					$payquery = m('finance')->isWeixinPay($ordersn, $order['price'], is_h5app() ? true : false);
					$payquery_jie = m('finance')->isWeixinPayBorrow($ordersn, $order['price']);
				}

				// var_dump($payquery_jie);die;
				if (!is_error($payquery) || !is_error($payquery_jie) || !empty($ispeerpay)) {
					$record = array();
					$record['status'] = '1';
					$record['type'] = 'wechat';
					pdo_update('core_paylog', $record, array('plid' => $log['plid']));
					m('order')->setOrderPayType($order['id'], 21, $gpc_ordersn);

					if (is_h5app()) {
						pdo_update('ewei_shop_order', array('apppay' => 1), array('id' => $order['id']));
					}

					$ret = array();
					$ret['result'] = 'success';
					$ret['type'] = 'wechat';
					$ret['from'] = 'return';
					$ret['tid'] = $log['tid'];
					$ret['user'] = $log['openid'];
					$ret['fee'] = $log['fee'];
					$ret['weid'] = $log['weid'];
					$ret['uniacid'] = $log['uniacid'];
					$ret['deduct'] = intval($_GPC['deduct']) == 1;

					$pay_result = m('order')->payResult($ret);
					
					if ($order['invoice']==1) {
						$orderID = $order["ordersn"];
						$item['payment']=$order['status'];
						pdo_update('ewei_shop_invoice', array('payment'=>$record['status']), array('orderID' => $orderID, 'uniacid' => $_W['uniacid']));
					}
					@session_start();
					$_SESSION[EWEI_SHOPV2_PREFIX . '_order_pay_complete'] = 1;

					if ($_W['ispost']) {
						show_json(1, array('result' => $pay_result));
					}
					else {
						header('location:' . mobileUrl('paymentcode/success', array('id' => $order['id'], 'result' => $pay_result)));
					}

					exit();
				}

				if ($_W['ispost']) {
					show_json(0, '支付出错,请重试!');
				}
				else {
					$this->message('支付出错,请重试!', mobileUrl('order'));
				}
			}
		}
	}

	protected function str($str)
	{
		$str = str_replace('"', '', $str);
		$str = str_replace('\'', '', $str);
		return $str;
	}

	public function check()
	{
		global $_W;
		global $_GPC;
		$orderid = intval($_GPC['id']);
		$og_array = m('order')->checkOrderGoods($orderid);

		if (!empty($og_array['flag'])) {
			show_json(0, $og_array['msg']);
		}

		show_json(1);
	}

	public function message($msg, $redirect = '', $type = '')
	{
		global $_W;
		$title = '';
		$buttontext = '';
		$message = $msg;

		if (is_array($msg)) {
			$message = (isset($msg['message']) ? $msg['message'] : '');
			$title = (isset($msg['title']) ? $msg['title'] : '');
			$buttontext = (isset($msg['buttontext']) ? $msg['buttontext'] : '');
		}

		if (empty($redirect)) {
			$redirect = 'javascript:history.back(-1);';
		}
		else {
			if ($redirect == 'close') {
				$redirect = 'javascript:WeixinJSBridge.call("closeWindow")';
			}
		}

		include $this->template('_message');
		exit();
	}

	public function backurl(){
		global $_W;
		global $_GPC;
		$openid = $_W['openid'];
		$ordersn = $_GPC['ordersn'];
		if(empty($_GPC['ordersn'])){
			echo "<script>history.back(1);</script>";exit;
		}
		$order = pdo_fetch('select * from ' . tablename('ewei_shop_order') . ' where ordersn=:ordersn and uniacid=:uniacid and openid=:openid limit 1', array(':ordersn' => $ordersn,':uniacid' => $_W['uniacid'],':openid' => $openid));
		$log = pdo_fetch('SELECT * FROM ' . tablename('core_paylog') . ' WHERE `uniacid`=:uniacid AND `module`=:module AND `tid`=:tid limit 1', array(':uniacid' => $_W['uniacid'], ':module' => 'ewei_shopv2', ':tid' => $order['ordersn']));
		$record = array();
		$record['status'] = '1';
		$record['type'] = 'qukuailian';
		pdo_update('core_paylog', $record, array('plid' => $log['plid']));
		m('order')->setOrderPayType($order['id'], 23, $order['ordersn']);
		if (is_h5app()) {
			pdo_update('ewei_shop_order', array('apppay' => 1), array('id' => $order['id']));
		}

		$ret = array();
		$ret['result'] = 'success';
		$ret['type'] = 'qukuailian';
		$ret['from'] = 'return';
		$ret['tid'] = $log['tid'];
		$ret['user'] = $log['openid'];
		$ret['fee'] = $log['fee'];
		$ret['weid'] = $log['weid'];
		$ret['uniacid'] = $log['uniacid'];
		$ret['deduct'] = intval($_GPC['deduct']) == 1;
		@session_start();
		$_SESSION[EWEI_SHOPV2_PREFIX . '_order_pay_complete'] = 1;
		$pay_result = m('order')->payResult($ret);
		if ($order['invoice']==1) {
			$orderID = $order["ordersn"];
			$item['payment']=$order['status'];
			pdo_update('ewei_shop_invoice', array('payment'=>$record['status']), array('orderID' => $orderID, 'uniacid' => $_W['uniacid']));
		}

		header('location:' . mobileUrl('paymentcode/success', array('id' => $order['id'], 'result' => $pay_result)));		


	}

}