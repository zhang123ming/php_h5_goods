<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

require EWEI_SHOPV2_PLUGIN . 'commission/core/page_login_mobile.php';
class Register_EweiShopV2Page extends CommissionMobileLoginPage
{
	public function main()
	{
		global $_W;
		global $_GPC;
		global $_S;
		$openid = $_W['openid'];
		$set = set_medias($this->set, 'regbg');
		$datas = m('common')->getPluginset('commission');

		$area_set = m('util')->get_area_config_set();
		$new_area = intval($area_set['new_area']);
		$member = m('member')->getMember($openid);
		$shopset = m('common')->getSysset('shop');
		if ($member['isnotlogin']&&$shopset['isopenlogin']) {
			$url = urlencode(base64_encode($_SERVER['QUERY_STRING']));
			$loginurl = mobileUrl('account/login', array('mid' => $_GPC['mid'], 'backurl' => $_W['isajax'] ? '' : $url));
			header('location: ' . $loginurl);
		}
		if (($member['isagent'] == 1) && ($member['status'] == 1)) {
			header('location: ' . mobileUrl('commission'));
			exit();
		}

		if (!empty($set['no_commission_url'])) {
			$set['no_commission_url'] = strpos(trim($set['no_commission_url']), 'http') === 0 ? $set['no_commission_url'] : 'http://' . $set['no_commission_url'];
			header('location: ' . $set['no_commission_url']);
			exit();
		}


		if ($member['agentblack']) {
			include $this->template();
			exit();
		}

		if(!empty($_W['uniacid'])){

			$set = $_S['commission'];
			$aglevels = pdo_fetchall("SELECT id,levelname,level FROM " . tablename("ewei_shop_commission_level") . " WHERE uniacid=:uniacid and level>92" , array(':uniacid' => $_W['uniacid']));
			$userlevels = pdo_fetchall("SELECT id,levelname,level FROM " . tablename("ewei_shop_member_level") . " WHERE uniacid=:uniacid" , array(':uniacid' => $_W['uniacid']));
			$ulevel = $_W['shopset']['shop'];
			$shoplevel = array('id' => '0', 'levelname' => $ulevel['levelname'],'level' => '0');
		}

		$apply_set = array();
		$apply_set['open_protocol'] = $set['open_withdrawprotocol'];

		if (empty($set['applytitle'])) {
			$apply_set['applytitle'] = '分销商申请协议';
		}
		else {
			$apply_set['applytitle'] = $set['applytitle'];
		}

		$template_flag = 0;
		$diyform_plugin = p('diyform');

		if ($diyform_plugin) {
			$set_config = $diyform_plugin->getSet();
			$commission_diyform_open = $set_config['commission_diyform_open'];

			if ($commission_diyform_open == 1) {
				$template_flag = 1;
				$diyform_id = $set_config['commission_diyform'];

				if (!empty($diyform_id)) {
					$formInfo = $diyform_plugin->getDiyformInfo($diyform_id);
					$fields = $formInfo['fields'];
					$diyform_data = iunserializer($member['diycommissiondata']);
					$f_data = $diyform_plugin->getDiyformData($diyform_data, $fields, $member);
				}
			}
		}

		$mid = intval($_GPC['mid']);
		$agent = false;

		if (!empty($member['fixagentid'])) {
			$mid = $member['agentid'];

			if (!empty($mid)) {
				$agent = m('member')->getMember($member['agentid']);
			}
		}
		else if (!empty($member['agentid'])) {
			$mid = $member['agentid'];
			$agent = m('member')->getMember($member['agentid']);
		}
		else if (!empty($member['inviter'])) {
			$mid = $member['inviter'];
			$agent = m('member')->getMember($member['inviter']);
		}
		else {
			if (!empty($mid)) {
				$agent = m('member')->getMember($mid);
			}
		}
		if(!empty($agent['agentlevel'])){
			$aagent = pdo_fetch("SELECT id,levelname,level FROM " . tablename("ewei_shop_commission_level") . " WHERE id=:id and uniacid=:uniacid" , array(':id'=>$agent['agentlevel'], ':uniacid' => $_W['uniacid']));
		}
		if(!empty($agent['level'])){
			$alevel = pdo_fetch("SELECT id,levelname,level FROM " . tablename("ewei_shop_member_level") . " WHERE id=:id and uniacid=:uniacid" , array(':id'=>$agent['level'], ':uniacid' => $_W['uniacid']));
		}
		// var_dump($aglevels,$userlevels);die;
		if ($_W['ispost']) {

			if ($set['become'] != '1') {
				show_json(0, '未开启' . $set['texts']['agent'] . '注册!');
			}

			$icode = intval($_GPC['icode']);

			if (0 < $icode) {
				$iagent = m('member')->getMember($icode);
				if (!empty($iagent) && ($iagent['isagent'] == 1)) {
					$mid = $icode;
				}
			}

			$become_check = intval($set['become_check']);
			$ret['status'] = $become_check;
			if ($template_flag == 1) {

				$memberdata = $_GPC['memberdata'];
				$insert_data = $diyform_plugin->getInsertData($fields, $memberdata);
				$data = $insert_data['data'];
				
				$m_data = $insert_data['m_data'];
				$mc_data = $insert_data['mc_data'];
				$m_data['diycommissionid'] = $diyform_id;
				$m_data['diycommissionfields'] = iserializer($fields);
				$m_data['diycommissiondata'] = $data;
				$m_data['isagent'] = 1;
				$m_data['agentid'] = $mid;
				$m_data['status'] = $become_check;
				$m_data['agenttime'] = $become_check == 1 ? time() : 0;
				$temp_data = iunserializer($data);
				foreach ($temp_data as $key => $v) {
					if(!empty($v['province'])) $m_data['province'] = $v['province'];
					if(!empty($v['city'])) $m_data['city'] = $v['city'];
					if(!empty($v['area'])) $m_data['area'] = $v['area'];
				}

				unset($m_data['credit1']);
				unset($m_data['credit2']);
				pdo_update('ewei_shop_member', $m_data, array('id' => $member['id']));

				if ($become_check == 1) {
					$this->model->sendMessage($member['openid'], array('nickname' => $member['nickname'], 'agenttime' => $m_data['agenttime']), TM_COMMISSION_BECOME);
				}

				if (!empty($member['uid'])) {
					if (!empty($mc_data)) {
						unset($mc_data['credit1']);
						unset($mc_data['credit2']);
						m('member')->mc_update($member['uid'], $mc_data);
					}
				}
			}
			else {
				if ($datas['checkpower'] == 1 ) {
					$agent = !empty($_GPC['agentlevel']) ? $_GPC['agentlevel'] : 0;
					$user = !empty($_GPC['userlevel']) ? $_GPC['userlevel'] : 0;
					$data = array('isagent' => 1, 'agentid' => $mid, 'status' => $become_check, 'realname' => $_GPC['realname'], 'mobile' => $_GPC['mobile'], 'weixin' => $_GPC['weixin'], 'agenttime' => $become_check == 1 ? time() : 0,'agentlevel'=>$agent,'level'=>$user);
					$data['agent_address'] = iserializer(array('province'=>$_GPC['province'],'city'=>$_GPC['city'],'district'=>$_GPC['district']));
					pdo_update('ewei_shop_member', $data, array('id' => $member['id']));
				} else {
					$data = array('isagent' => 1, 'agentid' => $mid, 'status' => $become_check, 'realname' => $_GPC['realname'], 'mobile' => $_GPC['mobile'], 'weixin' => $_GPC['weixin'], 'agenttime' => $become_check == 1 ? time() : 0);
					
					pdo_update('ewei_shop_member', $data, array('id' => $member['id']));
				}

				if ($become_check == 1) {
					$this->model->sendMessage($member['openid'], array('nickname' => $member['nickname'], 'agenttime' => $data['agenttime']), TM_COMMISSION_BECOME);

					if (!empty($mid)) {
						$this->model->upgradeLevelByAgent($mid);

						if (p('globonus')) {
							p('globonus')->upgradeLevelByAgent($mid);
						}

						if (p('author')) {
							p('author')->upgradeLevelByAgent($mid);
						}
					}
				}

				if (!empty($member['uid'])) {
					m('member')->mc_update($member['uid'], array('realname' => $data['realname'], 'mobile' => $data['mobile']));
				}
			}

			show_json(1, array('check' => $become_check));
		}

		$order_status = (intval($set['become_order']) == 0 ? 1 : 3);
		$become_check = intval($set['become_check']);
		$to_check_agent = false;

		if (empty($set['become'])) {
			if (empty($member['status']) || empty($member['isagent'])) {
				$data = array('isagent' => 1, 'agentid' => $mid, 'status' => $become_check, 'realname' => trim($_GPC['realname']), 'mobile' => trim($_GPC['mobile']), 'weixin' => trim($_GPC['weixin']), 'agenttime' => $become_check == 1 ? time() : 0);
				pdo_update('ewei_shop_member', $data, array('id' => $member['id']));

				if ($become_check == 1) {
					$this->model->sendMessage($member['openid'], array('nickname' => $member['nickname'], 'agenttime' => $data['agenttime']), TM_COMMISSION_BECOME);
					$this->model->upgradeLevelByAgent($member['id']);

					if (p('globonus')) {
						p('globonus')->upgradeLevelByAgent($member['id']);
					}

					if (p('author')) {
						p('author')->upgradeLevelByAgent($member['id']);
					}
				}

				if (!empty($member['uid'])) {
					m('member')->mc_update($member['uid'], array('realname' => $data['realname'], 'mobile' => $data['mobile']));
				}

				$member['isagent'] = 1;
				$member['status'] = $become_check;
			}
		}
		else if ($set['become'] == '2') {
			$status = 1;
			$ordercount = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_order') . ' where uniacid=:uniacid and openid=:openid and status>=' . $order_status . ' limit 1', array(':uniacid' => $_W['uniacid'], ':openid' => $openid));

			if ($ordercount < intval($set['become_ordercount'])) {
				$status = 0;
				$order_count = number_format($ordercount, 0);
				$order_totalcount = number_format($set['become_ordercount'], 0);
			}
			else {
				$to_check_agent = true;
			}
		}
		else if ($set['become'] == '3') {
			$status = 1;
			$moneycount = pdo_fetchcolumn('select sum(goodsprice) from ' . tablename('ewei_shop_order') . ' where uniacid=:uniacid and openid=:openid and status>=' . $order_status . ' limit 1', array(':uniacid' => $_W['uniacid'], ':openid' => $openid));
			
				
			if ($set['become3']['indiana']) {
				//一元夺宝记录
				$task_sql = 'SELECT SUM(num)  FROM '.tablename('weliam_indiana_consumerecord').' WHERE uniacid=:uniacid AND openid=:openid';
				$indianacount = pdo_fetchcolumn($task_sql,array(':uniacid'=>$_W['uniacid'],':openid'=>$_W['openid']));
				
		
			}

			if ($set['become3']['lottery']) {
				//转盘记录
				$task_sql = 'SELECT count(*) as num FROM '.tablename('ewei_shop_lottery_join').' AS j LEFT JOIN '.tablename('ewei_shop_lottery').' AS l ON j.lottery_id=l.lottery_id WHERE j.uniacid=:uniacid AND j.`join_user`=:join_user AND j.lottery_num=0 AND l.lottery_type=1 AND l.task_type=5'; 

				$lotterycount = pdo_fetchcolumn($task_sql,array(':uniacid'=>$_W['uniacid'],':join_user'=>$_W['openid']));
	
			}

		
			if ($moneycount < floatval($set['become_moneycount'])&&empty($lotterycount)&&($indianacount<floatval($set['become_moneycount']))) {
				$status = 0;
				$money_count = number_format($moneycount, 2);
				$money_totalcount = number_format($set['become_moneycount'], 2);
			}
			else {

				$to_check_agent = true;
			}
		}
		else {
			if ($set['become'] == 4) {
				$goods = pdo_fetch('select id,title,thumb,marketprice from' . tablename('ewei_shop_goods') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $set['become_goodsid'], ':uniacid' => $_W['uniacid']));
				$goodscount = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_order_goods') . ' og ' . '  left join ' . tablename('ewei_shop_order') . ' o on o.id = og.orderid' . ' where og.goodsid=:goodsid and o.openid=:openid and o.status>=' . $order_status . '  limit 1', array(':goodsid' => $set['become_goodsid'], ':openid' => $openid));

				if ($goodscount <= 0) {
					$status = 0;
					$buy_goods = $goods;
				}
				else {
					$to_check_agent = true;
					$status = 1;
				}

				if (p('cmember')) {
					$status = 0;
					$to_check_agent = false;
				}
			}
		}

		if ($to_check_agent) {
			if (empty($member['isagent'])) {
				$data = array('isagent' => 1, 'status' => $become_check, 'agenttime' => time());
				$member['isagent'] = 1;
				$member['status'] = $become_check;
				pdo_update('ewei_shop_member', $data, array('id' => $member['id']));

				if ($become_check == 1) {
					$this->model->sendMessage($member['openid'], array('nickname' => $member['nickname'], 'agenttime' => $data['agenttime']), TM_COMMISSION_BECOME);

					if (!empty($member['agentid'])) {
						$parent = m('member')->getMember($member['agentid']);
						if (!empty($parent) && !empty($parent['status']) && !empty($parent['isagent'])) {
							$this->model->upgradeLevelByAgent($parent['id']);
						}
					}
				}
			}
		}

		include $this->template();
	}
}

?>
