<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

require EWEI_SHOPV2_PLUGIN . 'commission/core/page_login_mobile.php';
class Apply_EweiShopV2Page extends CommissionMobileLoginPage
{
	public function main()
	{
		global $_W;
		global $_GPC;
		$pluginset = m('common')->getPluginset();
		$set = $this->set;
		if (empty($set['paytitle'])) {
			$apply_set['paytitle'] = '分销商申请协议';
		}
		else {
			$apply_set['applytitle'] = $set['applytitle'];
		}
		$openid = $_W['openid'];
		$level = $this->set['level'];
		$member = $this->model->getInfo($openid, array());
		$become_reg = $this->set['become_reg'];
		$record_ok = m("common")->record($_W['openid'],'getsum',0)['total'];
		if (empty($become_reg)) {
			if (empty($member['realname'])) {
				$returnurl = urlencode(mobileUrl('commission/apply'));
				$this->message('需要您完善资料才能继续操作!', mobileUrl('member/info', array('returnurl' => $returnurl)), 'info');
			}
		}

		$commissionsettletype = $this->set['commissionsettletype'];
		if (!empty($commissionsettletype)) {
			$orderstatus=1;
		}else{
			$orderstatus=3;
		}

		$time = time();
		$day_times = intval($this->set['settledays']) * 3600 * 24;
		$agentLevel = $this->model->getLevel($openid);
		$commission_ok = 0;
		$orderids = array();
		if (1 <= $level) {
			$level1_orders = pdo_fetchall('select distinct o.id from ' . tablename('ewei_shop_order') . ' o ' . ' left join  ' . tablename('ewei_shop_order_goods') . ' og on og.orderid=o.id ' . ' where o.agentid=:agentid and o.status>=:status and og.status1=0 and og.nocommission=0 and (' . $time . ' - o.finishtime > ' . $day_times . ') and o.uniacid=:uniacid  group by o.id', array(':uniacid' => $_W['uniacid'], ':agentid' => $member['id'],':status'=>$orderstatus));

			foreach ($level1_orders as $o) {
				if (empty($o['id'])) {
					continue;
				}

				$hasorder = false;

				foreach ($orderids as $or) {
					if ($or['orderid'] == $o['id']) {
						$hasorder = true;
						break;
					}
				}

				if ($hasorder) {
					continue;
				}

				$orderids[] = array('orderid' => $o['id'], 'level' => 1);
			}
		}

		if (2 <= $level) {
			if (0 < $member['level1']) {
				$level2_orders = pdo_fetchall('select distinct o.id from ' . tablename('ewei_shop_order') . ' o ' . ' left join  ' . tablename('ewei_shop_order_goods') . ' og on og.orderid=o.id ' . ' where o.agentid in( ' . implode(',', array_keys($member['level1_agentids'])) . ')  and o.status>=:status  and og.status2=0 and og.nocommission=0 and (' . $time . ' - o.finishtime > ' . $day_times . ') and o.uniacid=:uniacid  group by o.id', array(':uniacid' => $_W['uniacid'],':status'=>$orderstatus));

				foreach ($level2_orders as $o) {
					if (empty($o['id'])) {
						continue;
					}

					$hasorder = false;

					foreach ($orderids as $or) {
						if ($or['orderid'] == $o['id']) {
							$hasorder = true;
							break;
						}
					}

					if ($hasorder) {
						continue;
					}

					$orderids[] = array('orderid' => $o['id'], 'level' => 2);
				}
			}
		}

		if (3 <= $level) {
			if (0 < $member['level2']) {
				$level3_orders = pdo_fetchall('select distinct o.id from ' . tablename('ewei_shop_order') . ' o ' . ' left join  ' . tablename('ewei_shop_order_goods') . ' og on og.orderid=o.id ' . ' where o.agentid in( ' . implode(',', array_keys($member['level2_agentids'])) . ')  and o.status>=:status  and  og.status3=0 and og.nocommission=0 and (' . $time . ' - o.finishtime > ' . $day_times . ')   and o.uniacid=:uniacid  group by o.id', array(':uniacid' => $_W['uniacid'],':status'=>$orderstatus));

				foreach ($level3_orders as $o) {
					if (empty($o['id'])) {
						continue;
					}

					$hasorder = false;

					foreach ($orderids as $or) {
						if ($or['orderid'] == $o['id']) {
							$hasorder = true;
							break;
						}
					}

					if ($hasorder) {
						continue;
					}

					$orderids[] = array('orderid' => $o['id'], 'level' => 3);
				}
			}
		}

		// 查找代理订单 返利金额 开始
		$mLevel = m('member')->getLevel($openid);
		$commissiona_ok =0;
		$agentOrderids = array();
		if (90 < $mLevel['level']) {
				$levela_orders = pdo_fetchall('select distinct o.id,og.commissions,og.status100,og.status99,og.status98,og.status97,og.status96,og.status95,og.status94,og.status93,og.status92,og.status91 from ' . tablename('ewei_shop_order') . ' o ' . ' left join  ' . tablename('ewei_shop_order_goods') . ' og on og.orderid=o.id ' . ' where (o.agent100='.$member['id'].' or o.agent99='.$member['id'].' or o.agent98='.$member['id'].' or o.agent97='.$member['id'].' or o.agent96='.$member['id'].' or o.agent95='.$member['id'].' or o.agent94='.$member['id'].' or o.agent93='.$member['id'].' or o.agent92='.$member['id'].' or o.agent91='.$member['id'].')  and o.status>=:status and og.nocommission=0 and (' . $time . ' - o.finishtime > ' . $day_times . ') and o.uniacid=:uniacid  group by o.id', array(':uniacid' => $_W['uniacid'],':status'=>$orderstatus));
				foreach ($levela_orders as $o) {
					if (empty($o['id'])) {
						continue;
					}
					$commissionAgent = iunserializer($o['commissions']);
					foreach ($commissionAgent as $cid => $ca) {
						$aid = array_keys($ca);
						$isok=false;
						if($member['id']==$aid[0]){
							switch ($cid) {
								case 'agent100':
										if($o['status100']==0){
											$isok=true;
											$o['alevel']=100;
										} 
									break;
								case 'agent99':
										if($o['status99']==0){
											$isok=true;
											$o['alevel']=99;
										} 
									break;
								case 'agent98':
										if($o['status98']==0){
											$isok=true;
											$o['alevel']=98;
										} 
									break;
								case 'agent97':
										if($o['status97']==0){
											$isok=true;
											$o['alevel']=97;
										} 
									break;
								case 'agent96':
										if($o['status96']==0){
											$isok=true;
											$o['alevel']=96;
										}
									break;
								case 'agent95':
										if($o['status95']==0){
											$isok=true;
											$o['alevel']=95;
										}
									break;
								case 'agent94':
										if($o['status94']==0){
											$isok=true;
											$o['alevel']=94;
										}
									break;
								case 'agent93':
										if($o['status93']==0){
											$isok=true;
											$o['alevel']=93;
										}
									break;
								case 'agent92':
										if($o['status92']==0){
											$isok=true;
											$o['alevel']=92;
										}
									break;
								case 'agent91':
										if($o['status91']==0){
											$isok=true;
											$o['alevel']=91;
										}
									break;
								default:
										$isok=false;
									break;
							}
						}
						if($isok) $agentOrderids[] = array('orderid' => $o['id'], 'agent' => $member['id'],'alevel'=>$o['alevel']);
					}
				}

			}
		//可提现金额

		foreach ($agentOrderids as $o) {
			$goods = pdo_fetchall('SELECT ' . 'og.commissions,' . 'og.status100,og.status99,og.status98,og.status97,og.status96,og.status95,og.status94,og.status93,og.status92,og.status91,' . 'og.content100,og.content99,og.content98 ,og.content97,og.content96,og.content95,og.content94,og.content93,og.content92,og.content91 from ' . tablename('ewei_shop_order_goods') . ' og' . ' left join ' . tablename('ewei_shop_goods') . ' g on g.id=og.goodsid  ' . ' where og.orderid=:orderid and og.nocommission=0 and og.uniacid = :uniacid order by og.createtime  desc ', array(':uniacid' => $_W['uniacid'], ':orderid' => $o['orderid']));
			foreach ($goods as $g) {
				$commissionAgent = iunserializer($g['commissions']);
				foreach ($commissionAgent as $cid => $ca) {
					$aid = array_keys($ca);
					$isok=false;
					if($member['id']==$aid[0]){
						switch ($cid) {
							case 'agent100':
									if($g['status100']==0) $isok=true;
								break;
							case 'agent99':
									if($g['status99']==0) $isok=true;
								break;
							case 'agent98':
									if($g['status98']==0) $isok=true;
								break;
							case 'agent97':
									if($g['status97']==0) $isok=true;
								break;
							case 'agent96':
									if($g['status96']==0) $isok=true;
								break;
							case 'agent95':
									if($g['status95']==0) $isok=true;
								break;
							case 'agent94':
									if($g['status94']==0) $isok=true;
								break;
							case 'agent93':
									if($g['status93']==0) $isok=true;
								break;
							case 'agent92':
									if($g['status92']==0) $isok=true;
								break;
							case 'agent91':
									if($g['status91']==0) $isok=true;
								break;
							default:
									$isok=false;
								break;
						}
					}
					if($isok) $commissiona_ok += (isset($ca[$member['id']]) ? $ca[$member['id']] : 0);
				}
			}
		}
		// 查找代理订单结束
		foreach ($orderids as $o) {
			$goods = pdo_fetchall('SELECT ' . 'og.commission1,og.commission2,og.commission3,og.commissions,' . 'og.status1,og.status2,og.status3,' . 'og.content1,og.content2,og.content3 from ' . tablename('ewei_shop_order_goods') . ' og' . ' left join ' . tablename('ewei_shop_goods') . ' g on g.id=og.goodsid  ' . ' where og.orderid=:orderid and og.nocommission=0 and og.uniacid = :uniacid order by og.createtime  desc ', array(':uniacid' => $_W['uniacid'], ':orderid' => $o['orderid']));

			foreach ($goods as $g) {
				$commissions = iunserializer($g['commissions']);
				if (($o['level'] == 1) && ($g['status1'] == 0)) {
					$commission1 = iunserializer($g['commission1']);
					if (empty($commissions)) {
						$commission_ok += (isset($commission1['level' . $agentLevel['id']]) ? $commission1['level' . $agentLevel['id']] : $commission1['default']);
					}
					else {
						$commission_ok += (isset($commissions['level1']) ? floatval($commissions['level1']) : 0);
					}
				}

				if (($o['level'] == 2) && ($g['status2'] == 0)) {
					$commission2 = iunserializer($g['commission2']);

					if (empty($commissions)) {
						$commission_ok += (isset($commission2['level' . $agentLevel['id']]) ? $commission2['level' . $agentLevel['id']] : $commission2['default']);
					}
					else {
						$commission_ok += (isset($commissions['level2']) ? floatval($commissions['level2']) : 0);
					}
				}

				if (($o['level'] == 3) && ($g['status3'] == 0)) {
					$commission3 = iunserializer($g['commission3']);

					if (empty($commissions)) {
						$commission_ok += (isset($commission3['level' . $agentLevel['id']]) ? $commission3['level' . $agentLevel['id']] : $commission3['default']);
					}
					else {
						$commission_ok += (isset($commissions['level3']) ? floatval($commissions['level3']) : 0);
					}
				}
			}
		}

		$withdraw = floatval($this->set['withdraw']);

		if ($withdraw <= 0) {
			$withdraw = 1;
		}
		$orderidsList = array_merge($orderids,$agentOrderids);
		
		$cansettle = $withdraw <= $commission_ok+$commissiona_ok;
		$member['commission_ok'] = number_format($commission_ok+$commissiona_ok, 2);
		$set_array = array();
		$set_array['charge'] = $this->set['withdrawcharge'];
		$set_array['begin'] = floatval($this->set['withdrawbegin']);
		$set_array['end'] = floatval($this->set['withdrawend']);
		$realmoney = $commission_ok+$commissiona_ok+$record_ok;
		$totalpay = $realmoney;
		$deductionmoney = 0;

		if (!empty($set_array['charge'])) {
			$money_array = m('member')->getCalculateMoney($totalpay, $set_array);

			if ($money_array['flag']) {
				$realmoney = $money_array['realmoney'];
				$deductionmoney = $money_array['deductionmoney'];
			}
		}

		$last_data = $this->model->getLastApply($member['id']);
		$canusewechat = !strexists($openid, 'wap_user_') && !strexists($openid, 'sns_qq_') && !strexists($openid, 'sns_wx_') && !strexists($openid, 'sns_wa_');
		$type_array = array();

		if ($this->set['cashcredit'] == 1) {
			$type_array[0]['title'] = $this->set['texts']['withdraw'] . '到' . $_W['shopset']['trade']['moneytext'];
		}

		if (($this->set['cashweixin'] == 1) && $canusewechat) {
			$type_array[1]['title'] = $this->set['texts']['withdraw'] . '到微信钱包';
		}

		if ($this->set['cashother'] == 1) {
			if ($this->set['cashalipay'] == 1) {
				$type_array[2]['title'] = $this->set['texts']['withdraw'] . '到支付宝';

				if (!empty($last_data)) {
					if ($last_data['type'] != 2) {
						$type_last = $this->model->getLastApply($member['id'], 2);

						if (!empty($type_last)) {
							$last_data['realname'] = $type_last['realname'];
							$last_data['alipay'] = $type_last['alipay'];
						}
					}
				}
			}

			if ($this->set['cashcard'] == 1) {
				$type_array[3]['title'] = $this->set['texts']['withdraw'] . '到银行卡';

				if (!empty($last_data)) {
					if ($last_data['type'] != 3) {
						$type_last = $this->model->getLastApply($member['id'], 3);

						if (!empty($type_last)) {
							$last_data['realname'] = $type_last['realname'];
							$last_data['bankname'] = $type_last['bankname'];
							$last_data['bankcard'] = $type_last['bankcard'];
						}
					}
				}

				$condition = ' and uniacid=:uniacid';
				$params = array(':uniacid' => $_W['uniacid']);
				$banklist = pdo_fetchall('SELECT * FROM ' . tablename('ewei_shop_commission_bank') . ' WHERE 1 ' . $condition . '  ORDER BY displayorder DESC', $params);
			}
		}

		if (!empty($last_data)) {
			if (array_key_exists($last_data['type'], $type_array)) {
				$type_array[$last_data['type']]['checked'] = 1;
			}
		}

		if ($_W['ispost']) {
			if (empty($_SESSION['commission_apply_token'])) {
				show_json(0, '不要短时间重复下提交!');
			}

			unset($_SESSION['commission_apply_token']);
			if ((($commission_ok+$commissiona_ok+$record_ok) <= 0) || (empty($orderidsList)&&empty($record_ok))) {
				show_json(0, '参数错误,请刷新页面后重新提交!');
			}

			$type = intval($_GPC['type']);
			if (!array_key_exists($type, $type_array)) {
				show_json(0, '未选择体现方式2，请您选择提现方式后重试!');
			}

			$apply = array();

			if ($type == 2) {
				$realname = trim($_GPC['realname']);
				$alipay = trim($_GPC['alipay']);
				$alipay1 = trim($_GPC['alipay1']);

				if (empty($realname)) {
					show_json(0, '请填写姓名!');
				}

				if (empty($alipay)) {
					show_json(0, '请填写支付宝帐号!');
				}

				if (empty($alipay1)) {
					show_json(0, '请填写确认帐号!');
				}

				if ($alipay != $alipay1) {
					show_json(0, '支付宝帐号与确认帐号不一致!');
				}

				$apply['realname'] = $realname;
				$apply['alipay'] = $alipay;
			}
			else {
				if ($type == 3) {
					$realname = trim($_GPC['realname']);
					$bankname = trim($_GPC['bankname']);
					$bankcard = trim($_GPC['bankcard']);
					$bankcard1 = trim($_GPC['bankcard1']);

					if (empty($realname)) {
						show_json(0, '请填写姓名!');
					}

					if (empty($bankname)) {
						show_json(0, '请选择银行!');
					}

					if (empty($bankcard)) {
						show_json(0, '请填写银行卡号!');
					}

					if (empty($bankcard1)) {
						show_json(0, '请填写确认卡号!');
					}

					if ($bankcard != $bankcard1) {
						show_json(0, '银行卡号与确认卡号不一致!');
					}

					$apply['realname'] = $realname;
					$apply['bankname'] = $bankname;
					$apply['bankcard'] = $bankcard;
				}
			}

			foreach ($orderidsList as $o) {
				if ($o['level']) {
					
					pdo_update('ewei_shop_order_goods', array('status' . $o['level'] => 1, 'applytime' . $o['level'] => $time), array('orderid' => $o['orderid'], 'uniacid' => $_W['uniacid']));
				}
				if ($o['alevel']) {
					pdo_update('ewei_shop_order_goods', array('status' . $o['alevel'] => 1, 'applytime' . $o['alevel'] => $time), array('orderid' => $o['orderid'], 'uniacid' => $_W['uniacid']));
				}
			}

			$recordok = m("common")->record($_W['openid'],"getstatus",0);
			$recordid = "";
			foreach($recordok as $item){
				$recordid .= $item['id'].",";
			}
			$apply['recordid'] = $recordid;
			$applyno = m('common')->createNO('commission_apply', 'applyno', 'CA');
			$apply['uniacid'] = $_W['uniacid'];
			$apply['applyno'] = $applyno;
			$apply['orderids'] = iserializer($orderidsList);
			$apply['recordamount'] = round($record_ok,2);
			$apply['mid'] = $member['id'];
			$apply['commission'] = $commission_ok+$commissiona_ok+$record_ok;
			$apply['type'] = $type;
			$apply['status'] = 1;
			$apply['applytime'] = $time;
			$apply['realmoney'] = $realmoney;
			$apply['deductionmoney'] = $deductionmoney;
			$apply['charge'] = $set_array['charge'];
			$apply['beginmoney'] = $set_array['begin'];
			$apply['endmoney'] = $set_array['end'];
			$result = pdo_update("ewei_shop_commission_record",array("status"=>1),array("openid"=>$_W['openid'],"status"=>0));
			pdo_insert('ewei_shop_commission_apply', $apply);
			$applyid = pdo_insertid();
			$apply_type = array('余额', '微信钱包', '支付宝', '银行卡');
			$mcommission = $commission_ok;
			if (!empty($deductionmoney)) {
				$mcommission .= ',实际到账金额:' . $realmoney . ',提现手续费金额:' . $deductionmoney;
			}

			if(!empty($this->set['ischeck1'])&&$type==0){
				$result = $this->pay($applyid,$apply['commission'],$totalpay,$apply['realmoney'],$apply['deductionmoney'],$apply['charge'],$apply_type[$apply['type']]);
	    	}else{
	    		$this->model->sendMessage($openid, array('commission' => $mcommission, 'type' => $apply_type[$apply['type']]), TM_COMMISSION_APPLY);
	    		show_json(1,"提交审核成功");
			}
		}

		$token = md5(microtime());
		$_SESSION['commission_apply_token'] = $token;
		include $this->template();
	}


	// 免审核提现
	public function pay($applyid,$totalcommission,$totalpay,$realmoney,$deductionmoney,$charge,$applytype)
	{
		global $_W;
		global $_GPC;

		$set = $this->getSet();
		$time = time();
		$pay = round($realmoney, 2);

		$apply = pdo_fetch('select * from ' . tablename('ewei_shop_commission_apply') . ' where uniacid=:uniacid and id=:id limit 1', array(':uniacid' => $_W['uniacid'], ':id' => $applyid));


		// 查询审核中的所有其他佣金id
		$orderids = iunserializer($apply['orderids']);

		if ((!is_array($orderids) || (count($orderids) <= 0))&&(strlen($apply['recordid'])<=0||$apply['recordamount']<=0)) {
			show_json(0, '无任何订单，无法查看!');
		}
		
		$applyOrder = array();

		//合并相同订单号
		foreach($orderids as $item){
		    extract($item);
		    $key = $orderid;
		    if(!isset($applyOrder[$key])){
		        $applyOrder[$key] = $item;
		    }else{ 
		        if($orderid!=$applyOrder[$key]['orderid']){
		            $applyOrder[$key]= $item;
		        }else{
		        	$applyOrder[$key] = $applyOrder[$key]+$item;
		        }
		    }
		}
		// 提取数组
		
		$orderids = $applyOrder;
		$ids = array();

		foreach ($orderids as $o) {
			$ids[] = $o['orderid'];
		}

		$list = pdo_fetchall('select id,agentid,agent100,agent99,agent98,agent97,agent96,agent95,agent94,agent93,agent92,agent91, ordersn,price,goodsprice, dispatchprice,createtime, paytype from ' . tablename('ewei_shop_order') . ' where  id in ( ' . implode(',', $ids) . ' );');

		foreach ($list as &$value) {
			foreach ($orderids as $o) {
				if ($o['orderid'] == $value['id']) {
					$value['level'] = $o['level'];
					$value['alevel'] = $o['alevel'];
					$value['agent'] = $o['agent'];
					break;
				}
			}
			$asql = " ,og.status100,og.status99,og.status98,og.status97,og.status96,og.status95,og.status94,og.status93,og.status92,og.status91,og.content100,og.content99,og.content98,og.content97,og.content96";
			$goods = pdo_fetchall('SELECT og.id,g.thumb,og.price,og.realprice, og.total,g.title,o.paytype,og.optionname,og.commission1,og.commission2,og.commission3,og.commissions,og.status1,og.status2,og.status3,og.content1,og.content2,og.content3'.$asql.' from ' . tablename('ewei_shop_order_goods') . ' og' . ' left join ' . tablename('ewei_shop_goods') . ' g on g.id=og.goodsid  ' . ' left join ' . tablename('ewei_shop_order') . ' o on o.id=og.orderid  ' . ' where og.uniacid = :uniacid and og.orderid=:orderid and og.nocommission=0 order by og.createtime  desc ', array(':uniacid' => $_W['uniacid'], ':orderid' => $value['id']));

			
			foreach ($goods as &$g) {
				$g['level'] = $value['level'];
				if($value['alevel']>90) $g['alevel'] = $value['alevel'];
			}
			unset($g);
			$value['goods'] = $goods;
		}

		unset($value);

		if ($apply['type'] < 2) {
			if ($apply['type'] == 1) {
				$pay *= 100;
			}

			$data = m('common')->getSysset('pay');
			if (!empty($data['paytype']['commission']) && ($apply['type'] == 1)) {
				$result = m('finance')->payRedPack($_W['openid'], $pay, $apply['applyno'], $apply, $set['texts']['commission'] . '打款', $data['paytype']);
				pdo_update('ewei_shop_commission_apply', array('sendmoney' => $result['sendmoney'], 'senddata' => json_encode($result['senddata'])), array('id' => $apply['id']));

				if ($result['sendmoney'] == $realmoney) {
					$result = true;
				}
				else {
					$result = $result['error'];
				}
			}
			else {
				$result = m('finance')->pay($_W['openid'], $apply['type'], $pay, $apply['applyno'], $set['texts']['commission'] . '打款');
			}

			if (is_error($result)) {
				show_json(0, $result['message']);
			}
		}
		// //新提现方法
		// if ($apply['type'] == 2) {
		// 	$sec = m('common')->getSec();
		// 	$sec = iunserializer($sec['sec']);
		// 	if(empty($sec['alipay_pay']['appid']||empty($sec['alipay_pay']['appprikey'])||empty($sec['alipay_pay']['public_key']))){
		// 		show_json(0,"请配置好支付参数");
		// 	}
		// 	if (!empty($set['texts']['commission'])) {
		// 		$batch_no = 'D' . date('Ymd') . 'CP' . $apply['id'] . 'MONEY' . $batch_no_money;
		// 		require_once EWEI_SHOPV2_PLUGIN."api/core/mobile/alipayconfig/alipay.php";
		// 		$data = array(
		// 			'out_biz_no'=>$batch_no,
		// 			'payee_account'=>$apply['alipay'],
		// 			'amount'=>$pay,
		// 			'remark'=>'佣金提现'
		// 		);
		// 		$arr = new alipay;
		// 		$res = $arr->ali($data,$sec['alipay_pay']);
		// 		$resfail = $res['alipay_fund_trans_toaccount_transfer_response']['sub_msg'];
		// 		if($res['alipay_fund_trans_toaccount_transfer_response']['code']!='10000'){
		// 			show_json(0,$resfail);	
		// 		}
		// 	}
		// }	

		foreach ($list as $row) {
			$update = array();

			foreach ($row['goods'] as $g) {
				$update = array();
				$aupdate = array();
				$gupdate = array();
				if (($row['level'] == 1) && ($g['status1'] == 1)) {
					$update = array('paytime1' => $time,'checktime1'=>$time,'status1' => 3);//打款状态
				}
				else {
					if (($row['level'] == 2) && ($g['status2'] == 1)) {
						$update = array('paytime2' => $time,'checktime2'=>$time, 'status2' => 3);
					}
					else {
						if (($row['level'] == 3) && ($g['status3'] == 1)) {
							$update = array('paytime3' => $time,'checktime3'=>$time, 'status3' => 3);
						}
					}
				}
				if($row['alevel']>90&&($g['status'.$g['alevel']]==1)){//代理打款状态
					$aupdate = array('paytime'.$row['alevel']=>$time,'checktime'.$row['alevel']=>$time,'status'.$row['alevel']=>3);
				}
				$update = array_merge($update,$aupdate,$gupdate);
				if (!empty($update)) { 
					$count = pdo_update('ewei_shop_order_goods', $update, array('id' => $g['id']));
				}
			}
		}
		if(!empty($apply['recordid'])){
			$list = pdo_fetchall("select id from".tablename('ewei_shop_commission_record')."where status = 1 and id in (".rtrim($apply['recordid'],",").")");
			$str = 'id in(';
			foreach($list as $kk=>$vv){
				$str .= $vv['id'].",";
			}
			$str = rtrim($str,',');
			$str .=")";
			if($list){
				$sql = "update".tablename("ewei_shop_commission_record")."set status = 3 where ".$str;
				pdo_query($sql);
			}
		}

		pdo_update('ewei_shop_commission_apply', array('status' => 3, 'paytime' => $time, 'checktime'=>$time, 'commission_pay' => $totalpay, 'realmoney' => $realmoney, 'deductionmoney' => $deductionmoney), array('id' => $applyid, 'uniacid' => $_W['uniacid']));

		$log = array('uniacid' => $_W['uniacid'], 'applyid' => $apply['id'], 'mid' => $member['id'], 'commission' => $totalcommission, 'commission_pay' => $totalpay, 'realmoney' => $realmoney, 'deductionmoney' => $deductionmoney, 'charge' => $charge, 'createtime' => $time, 'type' => $apply['type']);
		pdo_insert('ewei_shop_commission_log', $log);
		$mcommission = $totalpay;

		if (!empty($deductionmoney)) {
			$mcommission .= ',实际到账金额:' . $realmoney . ',提现手续费金额:' . $deductionmoney;
		}

		$this->model->sendMessage($member['openid'], array('commission' => $mcommission, 'type' => $apply_type[$apply['type']]), TM_COMMISSION_PAY);
		$this->model->upgradeLevelByCommissionOK($member['openid']);

		if (p('globous')) {
			p('globous')->upgradeLevelByCommissionOK($member['openid']);
		}

		plog('commission.apply.pay', '(秒提)佣金打款 ID: ' . $applyid . ' 申请编号: ' . $apply['applyno'] . ' 打款方式: ' . $applytype . ' 总佣金: ' . $totalcommission . ' 审核通过佣金: ' . $totalcommission . ' 实际到账金额: ' . $realmoney . ' 提现手续费金额: ' . $deductionmoney . ' 提现手续费税率: ' . $charge . '%');
		show_json(1,"提款成功");
	}
}

?>
