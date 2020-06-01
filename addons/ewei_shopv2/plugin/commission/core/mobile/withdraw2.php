<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

require EWEI_SHOPV2_PLUGIN . 'commission/core/page_login_mobile.php';
class withdraw2_EweiShopV2Page extends CommissionMobileLoginPage
{
	public function main()
	{
		global $_W;
		global $_GPC;
		$openid = $_W['openid'];
		$uniacid = $_W['uniacid'];
		$charge = $this->set['withdrawcharge'];	
		$member = m('member')->getMember($openid);
		$credit3 = $member['credit3'];
		$set = $this->set;
		if (empty($set['paytitle'])) {
			$apply_set['paytitle'] = '分销商申请协议';
		}
		else {
			$apply_set['applytitle'] = $set['applytitle'];
		}
		$openid = $_W['openid'];
		$level = $this->set['level'];
		$become_reg = $this->set['become_reg'];
		// if (empty($become_reg)) {
		// 	if (empty($member['realname'])) {
		// 		$returnurl = urlencode(mobileUrl('commission/withdraw2'));
		// 		$this->message('需要您完善资料才能继续操作!', mobileUrl('member/info', array('returnurl' => $returnurl)), 'info');
		// 	}
		// }

		$time = time();
		$day_times = intval($this->set['settledays']) * 3600 * 24;
		$agentLevel = $this->model->getLevel($openid);
		$commission_ok = 0;
		// $credit = pdo_fetch("select credit3 from".tablename("ewei_shop_member")."where openid=:openid and uniacid=:uniacid",array(":openid" => $openid,":uniacid" => $_W["uniacid"]));	
		$ismultiple = $this->set['ismultiple'];
		$withdrawtype = $this->set['withdrawtype'];

		$withdraw = floatval($this->set['withdraw']);

		if ($withdraw <= 0) {
			$withdraw = 1;
		}
		
		$cansettle = $withdraw <= $credit3;
		$set_array = array();
		$set_array['charge'] = $this->set['withdrawcharge'];
		if($set_array['charge']){
			$charge_num = $set_array['charge'];
		}else{
			$charge_num = 100;
		}
		$set_array['begin'] = floatval($this->set['withdrawbegin']);
		$set_array['end'] = floatval($this->set['withdrawend']);
		$realmoney = $_GPC['input'];
		$deductionmoney = 0;
		if (!empty($set_array['charge'])) {
			$money_array = m('member')->getCalculateMoney($realmoney, $set_array);
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
			
			$type = intval($_GPC['type']);
			if (!array_key_exists($type, $type_array)) {
				show_json(0, '未选择提现方式，请您选择提现方式后重试!');
			}

			if($member['credit3']<$realmoney){
				show_json(0, '可提现金额不足，无法申请提现!');
			}

			//查询提现申请记录
			$start = mktime(0,0,0,date('m'),1,date('Y'));
			$end = mktime(23,59,59,date('m'),date('t'),date('Y'));
			$applynum = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_commission_apply') . ' where uniacid=:uniacid and mid=:mid and applytime>=:start and applytime<=:end ',array(':uniacid'=>$_W['uniacid'],':mid'=>$member['id'],':start'=>$start,':end'=>$end));
			if($applynum>=$set['cashnum']){
				show_json(0, '本月可提现次数不足，无法申请提现!');
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
			$applyno = m('common')->createNO('commission_apply', 'applyno', 'CA');
			$apply['uniacid'] = $_W['uniacid'];
			$apply['applyno'] = $applyno;
			$apply['mid'] = $member['id'];
			$apply['commission'] = round($_GPC['input'],2);
			$apply['type'] = $type;
			$apply['status'] = 1;
			$apply['applytime'] = $time;
			$apply['realmoney'] = $realmoney;
			$apply['deductionmoney'] = $deductionmoney;
			$apply['charge'] = $set_array['charge'];
			$apply['beginmoney'] = $set_array['begin'];
			$apply['endmoney'] = $set_array['end'];
			pdo_insert('ewei_shop_commission_apply', $apply);
			$applyid = pdo_insertid();
			if($_GPC['input']&&$applyid){
				$log['uniacid'] = $apply['uniacid'];
				$log['money'] =0- ($apply['realmoney']+$apply['deductionmoney']);
				$log['openid'] = $member['openid'];
				$log['remark'] = "提现".$apply['commission']."手续费:".$apply['deductionmoney'];
				m('member')->setCredit3($log);
			}
			$apply_type = array('余额', '微信钱包', '支付宝', '银行卡');
			$mcommission = $realmoney;
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
