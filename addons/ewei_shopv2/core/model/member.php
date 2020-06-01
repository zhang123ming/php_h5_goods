<?php
if (!(defined('IN_IA'))) 
{
	exit('Access Denied');
}
class Member_EweiShopV2Model 
{
	public function getInfo($openid = '') 
	{
		global $_W;
		$uid = intval($openid);
		if ($uid == 0) 
		{
			$info = pdo_fetch('select * from ' . tablename('ewei_shop_member') . ' where openid=:openid and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':openid' => $openid));
			if (empty($info)) 
			{
				if (strexists($openid, 'sns_qq_')) 
				{
					$openid = str_replace('sns_qq_', '', $openid);
					$condition = ' openid_qq=:openid ';
					$bindsns = 'qq';
				}
				else if (strexists($openid, 'sns_wx_')) 
				{
					$openid = str_replace('sns_wx_', '', $openid);
					$condition = ' openid_wx=:openid ';
					$bindsns = 'wx';
				}
				else if (strexists($openid, 'sns_wa_')) 
				{
					$openid = str_replace('sns_wa_', '', $openid);
					$condition = ' openid_wa=:openid and comefrom=`sns_wa` ';
					$bindsns = 'wa';
				}
				if (!(empty($condition))&&!empty($openid)) 
				{
					$info = pdo_fetch('select * from ' . tablename('ewei_shop_member') . ' where ' . $condition . '  and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':openid' => $openid));
					if (!(empty($info))) 
					{
						$info['bindsns'] = $bindsns;
					}
				}
			}
		}
		else 
		{
			$info = pdo_fetch('select * from ' . tablename('ewei_shop_member') . ' where id=:id  and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':id' => $uid));
		}
		if (!(empty($info['uid']))) 
		{
			load()->model('mc');
			$uid = mc_openid2uid($info['openid']);
			$fans = mc_fetch($uid, array('credit1', 'credit2', 'birthyear', 'birthmonth', 'birthday', 'gender', 'avatar', 'resideprovince', 'residecity', 'nickname'));
			$info['credit1'] = $fans['credit1'];
			$info['credit2'] = $fans['credit2'];
			$info['birthyear'] = ((empty($info['birthyear']) ? $fans['birthyear'] : $info['birthyear']));
			$info['birthmonth'] = ((empty($info['birthmonth']) ? $fans['birthmonth'] : $info['birthmonth']));
			$info['birthday'] = ((empty($info['birthday']) ? $fans['birthday'] : $info['birthday']));
			$info['nickname'] = ((empty($info['nickname']) ? $fans['nickname'] : $info['nickname']));
			$info['gender'] = ((empty($info['gender']) ? $fans['gender'] : $info['gender']));
			$info['sex'] = $info['gender'];
			$info['avatar'] = ((empty($info['avatar']) ? $fans['avatar'] : $info['avatar']));
			$info['headimgurl'] = $info['avatar'];
			$info['province'] = ((empty($info['province']) ? $fans['resideprovince'] : $info['province']));
			$info['city'] = ((empty($info['city']) ? $fans['residecity'] : $info['city']));
		}
		if (!(empty($info['birthyear'])) && !(empty($info['birthmonth'])) && !(empty($info['birthday']))) 
		{
			$info['birthday'] = $info['birthyear'] . '-' . ((strlen($info['birthmonth']) <= 1 ? '0' . $info['birthmonth'] : $info['birthmonth'])) . '-' . ((strlen($info['birthday']) <= 1 ? '0' . $info['birthday'] : $info['birthday']));
		}
		if (empty($info['birthday'])) 
		{
			$info['birthday'] = '';
		}
		if (!(empty($info))) 
		{
			if (!(strexists($info['avatar'], 'http://')) && !(strexists($info['avatar'], 'https://'))) 
			{
				$info['avatar'] = tomedia($info['avatar']);
			}
			if ($_W['ishttps']) 
			{
				$info['avatar'] = str_replace('http://', 'https://', $info['avatar']);
			}
		}
		return $info;
	}
	public function getMember($openid = '') 
	{
		global $_W;
		$uid = (int) $openid;
		if ($uid == 0) 
		{
			$info = pdo_fetch('select * from ' . tablename('ewei_shop_member') . ' where  openid=:openid and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':openid' => $openid));
			if (empty($info)) 
			{
				if (strexists($openid, 'sns_qq_')) 
				{
					$openid = str_replace('sns_qq_', '', $openid);
					$condition = ' openid_qq=:openid ';
					$bindsns = 'qq';
				}
				else if (strexists($openid, 'sns_wx_')) 
				{
					$openid = str_replace('sns_wx_', '', $openid);
					$condition = ' openid_wx=:openid ';
					$bindsns = 'wx';
				}
				else if (strexists($openid, 'sns_wa_')) 
				{
					$openid = str_replace('sns_wa_', '', $openid);
					$condition = ' openid_wa=:openid ';
					$bindsns = 'wa';
				}
				if (!(empty($condition))&&!empty($openid)) 
				{
					$info = pdo_fetch('select * from ' . tablename('ewei_shop_member') . ' where ' . $condition . '  and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':openid' => $openid));
					if (!(empty($info))) 
					{
						$info['bindsns'] = $bindsns;
					}
				}
			}
		}
		else 
		{
			$info = pdo_fetch('select * from ' . tablename('ewei_shop_member') . ' where id=:id and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':id' => $openid));
		}
		if (!(empty($info))) 
		{
			if (!(strexists($info['avatar'], 'http://')) && !(strexists($info['avatar'], 'https://'))) 
			{
				$info['avatar'] = tomedia($info['avatar']);
			}
			if ($_W['ishttps']) 
			{
				$info['avatar'] = str_replace('http://', 'https://', $info['avatar']);
			}
				if(strpos($info['avatar'],'132132')){
				$upgrade2=array();
				$upgrade2['avatar'] = str_replace('132132', '132', $info['avatar']);
				pdo_update('ewei_shop_member', $upgrade2, array('id' => $info['id']));
			}
			
			$info = $this->updateCredits($info);
		}
		return $info;
	}
	public function updateCredits($info) 
	{
		global $_W;
		$openid = $info['openid'];
		if (empty($info['uid'])) 
		{
			$followed = m('user')->followed($openid);
			if ($followed) 
			{
				load()->model('mc');
				$uid = mc_openid2uid($openid);
				if (!(empty($uid))) 
				{
					$info['uid'] = $uid;
					$upgrade = array('uid' => $uid);
					if (0 < $info['credit1']) 
					{
						mc_credit_update($uid, 'credit1', $info['credit1']);
						$upgrade['credit1'] = 0;
					}
					if (0 < $info['credit2']) 
					{
						mc_credit_update($uid, 'credit2', $info['credit2']);
						$upgrade['credit2'] = 0;
					}
					if (!(empty($upgrade))) 
					{
						pdo_update('ewei_shop_member', $upgrade, array('id' => $info['id']));
					}
				}
			}
		}
		$credits = $this->getCredits($openid);
		$info['credit1'] = $credits['credit1'];
		$info['credit2'] = $credits['credit2'];
		return $info;
	}
	public function getMobileMember($mobile) 
	{
		global $_W;
		$info = pdo_fetch('select * from ' . tablename('ewei_shop_member') . ' where mobile=:mobile and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':mobile' => $mobile));
		if (!(empty($info))) 
		{
			$info = $this->updateCredits($info);
		}
		return $info;
	}
	public function getMidMember($mid)
	{
		global $_W;
		$info = pdo_fetch('select * from ' . tablename('ewei_shop_member') . ' where id=:id and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':id' => $mid));
		if (!(empty($info))) 
		{
			$info = $this->updateCredits($info);
		}
		return $info;
	}

	public function getMid($openid='') 
	{
		global $_W;
		if(empty($openid)){
			$openid = $_W['openid'];
			$member = $this->getMember($openid);
			return $member['id'];			
		}else{
			$member = pdo_fetch('select id from '.tablename('ewei_shop_member').' where uniacid = :uniacid and openid = :openid',array(':uniacid'=>$_W['uniacid'],':openid'=>$openid))['id'];
			return $member;
		}
	}

	public function setCredit($openid = '', $credittype = 'credit1', $credits = 0, $log = array()) 
	{
		global $_W;
		load()->model('mc');
		$uid = mc_openid2uid($openid);
		$member = $this->getMember($openid);
		if (empty($uid)) 
		{
			$uid = intval($member['uid']);
		}
		if (empty($log)) 
		{
			$log = array($uid, '未记录');
		}
		else if (!(is_array($log))) 
		{
			$log = array(0, $log);
		}
		if (($credittype == 'credit1') && empty($log[0]) && (0 < $credits)) 
		{
			$shopset = m('common')->getSysset('trade');
			if (empty($member['diymaxcredit'])) 
			{
				if (0 < $shopset['maxcredit']) 
				{
					if ($shopset['maxcredit'] <= $member['credit1']) 
					{
						return error(-1, '用户'.$_W['shopset']['trade']['credittext'].'已达上限');
					}
					if ($shopset['maxcredit'] < ($member['credit1'] + $credits)) 
					{
						$credits = $shopset['maxcredit'] - $member['credit1'];
					}
				}
			}
			else if (0 < $member['maxcredit']) 
			{
				if ($member['maxcredit'] <= $member['credit1']) 
				{
					return error(-1, '用户'.$_W['shopset']['trade']['credittext'].'已达上限');
				}
				if ($member['maxcredit'] < ($member['credit1'] + $credits)) 
				{
					$credits = $member['maxcredit'] - $member['credit1'];
				}
			}
		}
		if (empty($log)) 
		{
			$log = array($uid, '未记录');
		}
		else if (!(is_array($log))) 
		{
			$log = array(0, $log);
		}
		$log_data = array('uid' => intval($uid), 'credittype' => $credittype, 'uniacid' => $_W['uniacid'], 'num' => $credits, 'createtime' => TIMESTAMP, 'module' => 'ewei_shopv2', 'operator' => intval($log[0]), 'remark' => $log[1],'openid' => $member['openid']);
		if (!(empty($uid))) 
		{
			$value = pdo_fetchcolumn('SELECT ' . $credittype . ' FROM ' . tablename('mc_members') . ' WHERE `uid` = :uid', array(':uid' => $uid));
			$newcredit = $credits + $value;
			if ($newcredit <= 0) 
			{
			    $newcredit = 0;
			}
			pdo_update('mc_members', array($credittype => $newcredit), array('uid' => $uid));
		}
		else 
		{
			$value = pdo_fetchcolumn('SELECT ' . $credittype . ' FROM ' . tablename('ewei_shop_member') . ' WHERE  uniacid=:uniacid and openid=:openid limit 1', array(':uniacid' => $_W['uniacid'], ':openid' => $openid));
			$newcredit = $credits + $value;
			if ($newcredit <= 0) 
			{
				$newcredit = 0;
			}
			pdo_update('ewei_shop_member', array($credittype => $newcredit), array('uniacid' => $_W['uniacid'], 'openid' => $openid));
			$log_data['remark'] = $log_data['remark'] . ' OPENID: ' . $openid;
		}
		pdo_insert('mc_credits_record', $log_data);
		if (p('task')) 
		{
			if ($credittype == 'credit1') 
			{
			}
			else 
			{
				p('task')->checkTaskReward('cost_rechargeenough', $credits, $openid);
				p('task')->checkTaskReward('cost_rechargetotal', $credits, $openid);
			}
		}
		if (p('task')) 
		{
			p('task')->checkTaskProgress($credits, 'recharge_full', 0, $openid);
			p('task')->checkTaskProgress($credits, 'recharge_count', 0, $openid);
		}
		com_run('wxcard::updateMemberCardByOpenid', $openid);
	}
	//发放佣金
	public function sendcommission($openid='')
	{
		global $_W;
		if(empty($openid)){
			return false;
		}

		$comset = m('common')->getPluginset('commission');
		if(empty($comset['successtime'])){
			return false;
		}

		$sendtime = time()-$comset['successtime']*86400;

		$log = pdo_fetchall('select * from ' . tablename('ewei_shop_commission_order_log') . ' where uniacid=:uniacid and openid=:openid and sendtime<=:sendtime and sendtime>0 and issend=0 ',array(':uniacid'=>$_W['uniacid'],':openid'=>$openid,':sendtime'=>$sendtime));
		if(!empty($log)){
			foreach ($log as &$v) {
				$res = $this->setCredit3($v);
				if ($res) {
	 				pdo_update('ewei_shop_commission_order_log',array('issend'=>1),array('id'=>$v['id']));
	 			}
			}
		}
	}
	//取消佣金
	public function cancelcommission($orderid='')
	{
		global $_W;
		if(empty($orderid)){
			return false;
		}
		$order = pdo_fetch('select * from ' . tablename('ewei_shop_order') . ' where uniacid=:uniacid and id=:id and status<3 limit 1',array(':uniacid'=>$_W['uniacid'],':id'=>$orderid));
		if(empty($order)){
			return false;
		}
		$log = pdo_fetchall('select * from ' . tablename('ewei_shop_commission_order_log') . ' where uniacid=:uniacid and orderid=:orderid and issend=0 ',array(':uniacid'=>$_W['uniacid'],':orderid'=>$orderid));
		if(!empty($log)){
			pdo_update('ewei_shop_commission_order_log',array('issend'=>-1),array('uniacid'=>$_W['uniacid'],'orderid'=>$orderid));
		}
	}
	//发放 其他佣金
	public function setCredit3($log = array(), $credittype = 'credit3') 
	{
		global $_W;
		if (empty($log)) {
			return false;
		}


		$log_data = array('uniacid' =>$log['uniacid'], 'credittype' => $credittype,  'amount' => $log['money'], 'createtime' => TIMESTAMP, 'remark' => $log['remark'], 'openid' => $log['openid'],'orderid'=>$log['orderid']?$log['orderid']:0,'where_from'=>$log['from_type']?$log['from_type']:'');
		
		$value = pdo_fetchcolumn('SELECT ' . $credittype . ' FROM ' . tablename('ewei_shop_member') . ' WHERE  uniacid=:uniacid and openid=:openid limit 1', array(':uniacid' => $log['uniacid'], ':openid' => $log['openid']));
		$newcredit = $log['money'] + $value;
		if ($newcredit <= 0) 
		{
			$newcredit = 0;
		}
		$re1 = pdo_update('ewei_shop_member', array($credittype => $newcredit), array('uniacid' => $log['uniacid'], 'openid' => $log['openid']));
		
		$res = pdo_insert('ewei_shop_commission_record', $log_data);
		return $res;
	}
	public function getCredit($openid = '', $credittype = 'credit1') 
	{
		global $_W;
		$openid = str_replace('sns_wa_', '', $openid);
		load()->model('mc');
		$uid = mc_openid2uid($openid);
		if (!(empty($uid))) 
		{
			return pdo_fetchcolumn('SELECT ' . $credittype . ' FROM ' . tablename('mc_members') . ' WHERE `uid` = :uid', array(':uid' => $uid));
		}

		$item = pdo_fetch('SELECT ' . $credittype . ' FROM ' . tablename('ewei_shop_member') . ' WHERE openid=:openid and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':openid' => $openid));
		
		if (empty($item)) 
		{
			$item = pdo_fetch('SELECT ' . $credittype . ' FROM ' . tablename('ewei_shop_member') . ' WHERE openid_wa=:openid and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':openid' => $openid));
		}
		return (empty($item[$credittype]) ? 0 : $item[$credittype]);
	}
	public function getCredits($openid = '', $credittypes = array('credit1', 'credit2')) 
	{
		global $_W;
		$openid = str_replace('sns_wa_', '', $openid);
		load()->model('mc');
		$uid = mc_openid2uid($openid);
		$types = implode(',', $credittypes);
		if (!(empty($uid))) 
		{
			return pdo_fetch('SELECT ' . $types . ' FROM ' . tablename('mc_members') . ' WHERE `uid` = :uid limit 1', array(':uid' => $uid));
		}
		$item = pdo_fetch('SELECT ' . $types . ' FROM ' . tablename('ewei_shop_member') . ' WHERE openid=:openid and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':openid' => $openid));
		if (empty($item)) 
		{
			$item = pdo_fetch('SELECT ' . $types . ' FROM ' . tablename('ewei_shop_member') . ' WHERE openid_wa=:openid and uniacid=:uniacid limit 1', array(':uniacid' => $_W['uniacid'], ':openid' => $openid));
		}
		if (empty($item)) 
		{
			return array('credit1' => 0, 'credit2' => 0);
		}
		return $item;
	}
	
	public function checkMember() 
	{
		global $_W;
		global $_GPC;
		$member = array();
		$shopset = m('common')->getSysset(array('shop', 'wap'));
		$openid = $_W['openid'];
		if (($_W['routes'] == 'order.pay_alipay') || ($_W['routes'] == 'creditshop.log.dispatch_complete') || ($_W['routes'] == 'threen.register.threen_complete') || ($_W['routes'] == 'creditshop.detail.creditshop_complete') || ($_W['routes'] == 'order.pay_alipay.recharge_complete') || ($_W['routes'] == 'order.pay_alipay.complete') || ($_W['routes'] == 'newmr.alipay') || ($_W['routes'] == 'newmr.callback.gprs') || ($_W['routes'] == 'newmr.callback.bill') || ($_W['routes'] == 'account.sns') || ($_W['plugin'] == 'mmanage') || ($_W['routes'] == 'live.send.credit') || ($_W['routes'] == 'live.send.coupon')) 
		{
			return;
		}
		if ($shopset['wap']['open']) 
		{
			if (($shopset['wap']['inh5app'] && is_h5app()) || (empty($shopset['wap']['inh5app']) && empty($openid))) 
			{
				return;
			}
		}
		if (empty($openid) && !(EWEI_SHOPV2_DEBUG)) 
		{
			$diemsg = ((is_h5app() ? 'APP正在维护, 请到公众号中访问' : '请在微信客户端打开链接'));
			exit('<!DOCTYPE html>' . "\r\n" . '                <html>' . "\r\n" . '                    <head>' . "\r\n" . '                        <meta name=\'viewport\' content=\'width=device-width, initial-scale=1, user-scalable=0\'>' . "\r\n" . '                        <title>抱歉，出错了</title><meta charset=\'utf-8\'><meta name=\'viewport\' content=\'width=device-width, initial-scale=1, user-scalable=0\'><link rel=\'stylesheet\' type=\'text/css\' href=\'https://res.wx.qq.com/connect/zh_CN/htmledition/style/wap_err1a9853.css\'>' . "\r\n" . '                    </head>' . "\r\n" . '                    <body>' . "\r\n" . '                    <div class=\'page_msg\'><div class=\'inner\'><span class=\'msg_icon_wrp\'><i class=\'icon80_smile\'></i></span><div class=\'msg_content\'><h4>' . $diemsg . '</h4></div></div></div>' . "\r\n" . '                    </body>' . "\r\n" . '                </html>');
		}
		$isnotlogin = 0;
		if (!empty($shopset['shop']['isopenlogin'])) {
			$isnotlogin = 1;
		}
		$member = $this->getMember($openid);
		
		$followed = m('user')->followed($openid);
		$uid = 0;
		$needupdate = false;
		$mc = array();
		load()->model('mc');
		$routeArr = array('order.create','member','member.cart','commission.register','commission');
		if ($followed || empty($shopset['shop']['getinfo']) || ($shopset['shop']['getinfo'] == 1)||in_array($_W['routes'], $routeArr)||$_GPC['checkinfo']==1) 
		{
			$uid = mc_openid2uid($openid);
			if (!(EWEI_SHOPV2_DEBUG)) 
			{
				$userinfo = mc_oauth_userinfo();
				$mexit = array();
				if(!empty($userinfo['unionid'])&&empty($member)){
					$mexit = pdo_get('ewei_shop_member',array('unionid'=>$userinfo['unionid'],'uniacid'=>$_W['uniacid']));
				}
				
				if(!empty($mexit)&&empty($member)){
					$member = $mexit;
					$needupdate = true;
				}
			}
			else 
			{
				$userinfo = array('openid' => $member['openid'], 'nickname' => $member['nickname'], 'headimgurl' => $member['avatar'], 'gender' => $member['gender'], 'province' => $member['province'], 'city' => $member['city'],'unionid' => $member['unionid']);
			}
			$mc = array();
			$mc['nickname'] = $userinfo['nickname'];
			$mc['avatar'] = $userinfo['headimgurl'];
			$mc['gender'] = $userinfo['sex'];
			$mc['resideprovince'] = $userinfo['province'];
			$mc['residecity'] = $userinfo['city'];
		}
		if (empty($member) && !(empty($openid))) 
		{
			$member = array('uniacid' => $_W['uniacid'], 'uid' => $uid, 'openid' => $openid, 'realname' => (!(empty($mc['realname'])) ? $mc['realname'] : ''), 'mobile' => (!(empty($mc['mobile'])) ? $mc['mobile'] : ''), 'nickname' => (!(empty($mc['nickname'])) ? $mc['nickname'] : ''), 'nickname_wechat' => (!(empty($mc['nickname'])) ? $mc['nickname'] : ''), 'avatar' => (!(empty($mc['avatar'])) ? $mc['avatar'] : ''), 'avatar_wechat' => (!(empty($mc['avatar'])) ? $mc['avatar'] : ''), 'gender' => (!(empty($mc['gender'])) ? $mc['gender'] : '-1'), 'province' => (!(empty($mc['resideprovince'])) ? $mc['resideprovince'] : ''), 'city' => (!(empty($mc['residecity'])) ? $mc['residecity'] : ''), 'area' => (!(empty($mc['residedist'])) ? $mc['residedist'] : ''), 'createtime' => time(), 'status' => 0,'isnotlogin'=>$isnotlogin,'unionid'=>(!(empty($userinfo['unionid'])) ? $userinfo['unionid'] : ''));
			pdo_insert('ewei_shop_member', $member);
			if (method_exists(m('member'), 'memberRadisCountDelete')) 
			{
				m('member')->memberRadisCountDelete();
			}
			$member['id'] = pdo_insertid();
		}
		else 
		{
			if (empty($member['isnotlogin'])) {
				$upgrade['isnotlogin'] = $isnotlogin;
			}
			if ($member['isblack'] == 1) 
			{
				show_message('暂时无法访问，请稍后再试!');
			}
			$upgrade = array('uid' => $uid);
			if($needupdate){
				$upgrade['openid'] = $openid;
			}
			if (isset($mc['nickname']) && ($member['nickname_wechat'] != $mc['nickname'])) 
			{
				$upgrade['nickname_wechat'] = $mc['nickname'];
			}
			if (isset($mc['nickname']) && empty($member['nickname'])) 
			{
				$upgrade['nickname'] = $mc['nickname'];
			}
			if (isset($mc['avatar']) && ($member['avatar_wechat'] != $mc['avatar'])) 
			{
				$upgrade['avatar_wechat'] = $mc['avatar'];
			}
			if (isset($mc['avatar']) && empty($member['avatar'])) 
			{
				$upgrade['avatar'] = $mc['avatar'];
			}
			if (isset($mc['gender']) && ($member['gender'] != $mc['gender'])) 
			{
				$upgrade['gender'] = $mc['gender'];
			}
			if (!(empty($upgrade))) 
			{
				pdo_update('ewei_shop_member', $upgrade, array('id' => $member['id']));
			}
			if($needupdate){
				// 同步更新其他表的openid
				$tables = pdo_fetchall('SHOW TABLES like \'%_ewei_shop_%\'');

				foreach ($tables as $k => $v) {
					$v = array_values($v);
					$tablename = str_replace($_W['config']['db']['tablepre'], '', $v[0]);
					if (pdo_fieldexists($tablename, 'openid') && pdo_fieldexists($tablename, 'uniacid')) {
						pdo_update($tablename, array('openid' => $openid), array('uniacid' => $_W['uniacid'], 'openid' => $mexit['openid']));
					}
		
					if (pdo_fieldexists($tablename, 'openid') && pdo_fieldexists($tablename, 'acid')) {
						pdo_update($tablename, array('openid' => $openid), array('acid' => $_W['acid'], 'openid' => $mexit['openid']));
					}
		
				}
				
			}
		}
		if (p('commission')) 
		{
			p('commission')->checkAgent($openid);
		}
		if (p('poster')) 
		{
			p('poster')->checkScan($openid);
		}
		if (empty($member)) 
		{
			return false;
		}
		return array('id' => $member['id'], 'openid' => $member['openid']);
	}
	public function getLevels($all = true) 
	{
		global $_W;
		$condition = '';
		if (!($all)) 
		{
			$condition = ' and enabled=1';
		}
		return pdo_fetchall('select * from ' . tablename('ewei_shop_member_level') . ' where uniacid=:uniacid' . $condition . ' order by level asc', array(':uniacid' => $_W['uniacid']));
	}
	public function getmerch_Levels($merchid,$all = true) 
	{
		global $_W;
		$condition = '';
		if (!($all)) 
		{
			$condition = ' and enabled=1';
		}
		if(empty($merchid)){
			return false;
		}
		return pdo_fetchall('select ml.*,mu.merchname from ' . tablename('ewei_shop_merch_member_level') . ' as ml,'.tablename('ewei_shop_merch_user').' as mu where ml.uniacid=:uniacid' . $condition . ' and ml.merchid = :merchid and ml.merchid = mu.id and mu.id = :merchid  order by level asc', array(':uniacid' => $_W['uniacid'],':merchid'=>$merchid));
	}
	public function getLevel($openid) 
	{
		global $_W;
		global $_S;
		if (empty($openid)) 
		{
			return false;
		}
		$member = m('member')->getMember($openid);
		if (!(empty($member)) && !(empty($member['level']))) 
		{
			$level = pdo_fetch('select * from ' . tablename('ewei_shop_member_level') . ' where id=:id and uniacid=:uniacid limit 1', array(':id' => $member['level'], ':uniacid' => $_W['uniacid']));
			if (!(empty($level))) 
			{
				return $level;
			}
		}
		return array('levelname' => (empty($_S['shop']['levelname']) ? '普通会员' : $_S['shop']['levelname']), 'discount' => (empty($_S['shop']['leveldiscount']) ? 10 : $_S['shop']['leveldiscount']));
	}
	public function getOneGoodsLevel($openid, $goodsid) 
	{
		global $_W;
		$uniacid = $_W['uniacid'];
		$level_info = $this->getLevel($openid);
		$level = intval($level_info['level']);
		$data = array();
		$levels = pdo_fetchall('select * from ' . tablename('ewei_shop_member_level') . ' where uniacid=:uniacid and buygoods=1 and level and level > :level order by level asc', array(':uniacid' => $uniacid, ':level' => $level));
		if (!(empty($levels))) 
		{
			foreach ($levels as $k => $v ) 
			{
				$goodsids = iunserializer($v['goodsids']);
				if (!(empty($goodsids))) 
				{
					if (in_array($goodsid, $goodsids)) 
					{
						$data = $v;
					}
				}
			}
		}
		return $data;
	}
	public function getGoodsLevel($openid, $orderid) 
	{
		global $_W;
		$uniacid = $_W['uniacid'];
		$order_goods = pdo_fetchall('select goodsid from ' . tablename('ewei_shop_order_goods') . ' where orderid=:orderid and uniacid=:uniacid', array(':uniacid' => $uniacid, ':orderid' => $orderid));
		$levels = array();
		$data = array();
		if (!(empty($order_goods))) 
		{
			foreach ($order_goods as $k => $v ) 
			{
				$item = $this->getOneGoodsLevel($openid, $v['goodsid']);
				if (!(empty($item))) 
				{
					$levels[$item['level']] = $item;
				}
			}
		}
		if (!(empty($levels))) 
		{
			$level = max(array_keys($levels));
			$data = $levels[$level];
		}
		return $data;
	}
	public function upgradeLevel($openid, $orderid = 0) 
	{
		global $_W;
		if (empty($openid)) 
		{
			return;
		}
		$shopset = m('common')->getSysset('shop');
		$leveltype = intval($shopset['leveltype']);
		$member = m('member')->getMember($openid);
		if (empty($member)) 
		{
			return;
		}
		$level = false;
		if (empty($leveltype)) 
		{
			$ordermoney = pdo_fetchcolumn('select ifnull( sum(og.realprice),0) from ' . tablename('ewei_shop_order_goods') . ' og ' . ' left join ' . tablename('ewei_shop_order') . ' o on o.id=og.orderid ' . ' where o.openid=:openid and o.status=3 and o.uniacid=:uniacid ', array(':uniacid' => $_W['uniacid'], ':openid' => $member['openid']));
			$level = pdo_fetch('select * from ' . tablename('ewei_shop_member_level') . ' where uniacid=:uniacid  and enabled=1 and ' . $ordermoney . ' >= ordermoney and ordermoney>0  order by level desc limit 1', array(':uniacid' => $_W['uniacid']));
		}
		else if ($leveltype == 1) 
		{
			$ordercount = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_order') . ' where  openid=:openid and status=3 and uniacid=:uniacid ', array(':uniacid' => $_W['uniacid'], ':openid' => $member['openid']));
			$level = pdo_fetch('select * from ' . tablename('ewei_shop_member_level') . ' where uniacid=:uniacid and enabled=1 and ' . $ordercount . ' >= ordercount and ordercount>0  order by level desc limit 1', array(':uniacid' => $_W['uniacid']));
		}
		if (!(empty($orderid))) 
		{
			$goods_level = $this->getGoodsLevel($openid, $orderid);
			if (empty($level)) 
			{
				$level = $goods_level;
			}
			else if (!(empty($goods_level))) 
			{
				if ($level['level'] < $goods_level['level']) 
				{
					$level = $goods_level;
				}
			}
		}
		if (empty($level)) 
		{
			return;
		}
		if ($level['id'] == $member['level']) 
		{
			return;
		}
		$oldlevel = $this->getLevel($openid);
		$canupgrade = false;
		if (empty($oldlevel['id'])) 
		{
			$canupgrade = true;
		}
		else if ($oldlevel['level'] < $level['level']) 
		{
			$canupgrade = true;
		}
		if ($canupgrade) 
		{
			$shopset = m('common')->getSysset('member');
			$timelimit = intval($shopset['timelimit']);
			if ($timelimit) {
				$unit = $shopset['unit'];
				$time = " + ".$timelimit." ".$unit;
			}

			pdo_update('ewei_shop_member', array('level' => $level['id'],'leveldeadline'=>strtotime($time)), array('id' => $member['id']));
			com_run('wxcard::updateMemberCardByOpenid', $openid);
			m('notice')->sendMemberUpgradeMessage($openid, $oldlevel, $level);

			set_time_limit(0);
			$member['level'] = $level['id'];
			$ainviter = p('commission')->getAgentId($member['agentid'],$member);

			pdo_update('ewei_shop_member',$ainviter,array('id'=>$member['id']));
			$teamlist = pdo_fetchall('select * from '.tablename('ewei_shop_member').' where uniacid=:uniacid and find_in_set('.$member['id'].',fids) order by fids asc',array(':uniacid'=>$_W['uniacid']));
			foreach ($teamlist as $k => $v) {
				$agentdata1 = p('commission')->getAgentId($v['agentid'],$v);
				
				pdo_update('ewei_shop_member',$agentdata1,array('id'=>$v['id']));
			}
		}
	}

	/*根据充值金额升级*/
	public function upgradeLevelByRercharge($openid, $recharge_money = 0) 
	{
		global $_W;
		if (empty($openid)) 
		{
			return;
		}
		$shopset = m('common')->getSysset('member');
		$chargetype = intval($shopset['chargetype']);
		$member = m('member')->getMember($openid);

		if (empty($member)) 
		{
			return;
		}
		$level = false;
		if (!empty($chargetype)) 
		{
			$level = pdo_fetch('select * from ' . tablename('ewei_shop_member_level') . ' where uniacid=:uniacid  and enabled=1 and ' . $recharge_money . ' >= rechargemoney and rechargemoney>0  order by level desc limit 1', array(':uniacid' => $_W['uniacid']));
		}
		
		
		if (empty($level)) 
		{
			return;
		}
		if ($level['id'] == $member['level']) 
		{
			return;
		}
		$oldlevel = $this->getLevel($openid);
		$canupgrade = false;
		if (empty($oldlevel['id'])) 
		{
			$canupgrade = true;
		}
		else if ($oldlevel['level'] < $level['level']) 
		{
			$canupgrade = true;
		}
		if ($canupgrade) 
		{
			$timelimit = intval($shopset['timelimit']);
			if ($timelimit) {
				$unit = $shopset['unit'];
				$time = " + ".$timelimit." ".$unit;
			}else{
				$time = "+ 1 year";
			}
			
			$leveldeadline = strtotime($time);
			pdo_update('ewei_shop_member', array('level' => $level['id'],'leveldeadline'=>$leveldeadline), array('id' => $member['id']));
			com_run('wxcard::updateMemberCardByOpenid', $openid);
			m('notice')->sendMemberUpgradeMessage($openid, $oldlevel, $level);
		}
	}
	public function upgradeLevelByLevelId($openid, $LevelID) 
	{
		global $_W;
		if (empty($openid)) 
		{
			return;
		}
		$member = m('member')->getMember($openid);
		if (empty($member)) 
		{
			return;
		}
		$level = pdo_fetch('select *  from ' . tablename('ewei_shop_member_level') . ' where uniacid=:uniacid and enabled=1 and id=:id', array(':uniacid' => $_W['uniacid'], ':id' => $LevelID));
		if (empty($level)) 
		{
			return;
		}
		if ($level['id'] == $member['level']) 
		{
			return;
		}
		$oldlevel = $this->getLevel($openid);
		$canupgrade = false;
		if (empty($oldlevel['id'])) 
		{
			$canupgrade = true;
		}
		else if ($oldlevel['level'] < $level['level']) 
		{
			$canupgrade = true;
		}
		if ($canupgrade) 
		{
			pdo_update('ewei_shop_member', array('level' => $level['id']), array('id' => $member['id']));
			com_run('wxcard::updateMemberCardByOpenid', $openid);
			m('notice')->sendMemberUpgradeMessage($openid, $oldlevel, $level);
		}
	}
	public function getGroups() 
	{
		global $_W;
		return pdo_fetchall('select * from ' . tablename('ewei_shop_member_group') . ' where uniacid=:uniacid order by id asc', array(':uniacid' => $_W['uniacid']));
	}
	public function getGroup($openid) 
	{
		if (empty($openid)) 
		{
			return false;
		}
		$member = m('member')->getMember($openid);
		return $member['groupid'];
	}
	public function setGroups($openid, $group_ids, $reason = '') 
	{
		$is_id = false;
		if (0 < intval($openid)) 
		{
			$openid = m('member')->getInfo($openid);
			if (empty($openid['openid'])) 
			{
				return false;
			}
			$openid = $openid['openid'];
		}
		$condition = array('openid' => $openid);
		if (is_array($group_ids)) 
		{
			$group_arr = $group_ids;
			$group_ids = implode(',', $group_ids);
		}
		else 
		{
			if (is_string($group_ids) || is_numeric($group_ids)) 
			{
				$group_arr = explode(',', $group_ids);
			}
			else 
			{
				return false;
			}
		}
		$old_group_ids = pdo_getcolumn('ewei_shop_member', $condition, 'groupid');
		$diff_ids = explode(',', $group_ids);
		if (!(empty($old_group_ids))) 
		{
			$old_group_ids = explode(',', $old_group_ids);
			$group_ids = array_merge($old_group_ids, $diff_ids);
			$group_ids = array_flip(array_flip($group_ids));
			$group_ids = implode(',', $group_ids);
			$diff_ids = array_diff($diff_ids, $old_group_ids);
		}
		pdo_update('ewei_shop_member', array('groupid' => $group_ids), $condition);
		foreach ($diff_ids as $groupid ) 
		{
			pdo_insert('ewei_shop_member_group_log', array('add_time' => date('Y-m-d H:i:s'), 'group_id' => $groupid, 'content' => $reason, 'mid' => intval($openid), 'openid' => ($is_id ? '' : $openid)));
		}
		return true;
	}
	public function setRechargeCredit($openid = '', $money = 0) 
	{
		if (empty($openid)) 
		{
			return;
		}
		global $_W;
		$credit = 0;
		$set = m('common')->getSysset(array('trade', 'shop'));
		if ($set['trade']) 
		{
			$tmoney = floatval($set['trade']['money']);
			if (!(empty($tmoney))) 
			{
				$tcredit = intval($set['trade']['credit']);
				if ($tmoney <= $money) 
				{
					if (($money % $tmoney) == 0) 
					{
						$credit = intval($money / $tmoney) * $tcredit;
					}
					else 
					{
						$credit = (intval($money / $tmoney) + 1) * $tcredit;
					}
				}
			}
		}
		if (0 < $credit) 
		{
			$this->setCredit($openid, 'credit1', $credit, array(0, $set['shop']['name'] . '会员充值'.$_W['shopset']['trade']['credittext'].':credit2:' . $credit));
		}
	}
	public function setRechargeTimes($openid = '', $money = 0) 
	{
		if (empty($openid)) 
		{
			return;
		}
		global $_W;
		$memberlimit = 0;
		$set = m('common')->getPluginset('machine');
		arsort($set['recharge_num']);
		if ($set['machineMode']==2) 
		{
			foreach ($set['recharge_num'] as $key => $value) {
				if ($money>=$value&&!empty($value)) {
					$memberlimit = $set['sendfreetimes'][$key];
					break;
				}
			}
		}
		if (0 < $memberlimit) 
		{
			pdo_update('ewei_shop_member',array('memberlimits +='=>$memberlimit),array('openid'=>$openid,'uniacid'=>$_W['uniacid']));
		}
	}
	public function getCalculateMoney($money, $set_array) 
	{
		$charge = $set_array['charge'];
		$begin = $set_array['begin'];
		$end = $set_array['end'];
		$array = array();
		$array['deductionmoney'] = round(($money * $charge) / 100, 2);
		if (($begin <= $array['deductionmoney']) && ($array['deductionmoney'] <= $end)) 
		{
			$array['deductionmoney'] = 0;
		}
		$array['realmoney'] = round($money - $array['deductionmoney'], 2);
		if ($money == $array['realmoney']) 
		{
			$array['flag'] = 0;
		}
		else 
		{
			$array['flag'] = 1;
		}
		return $array;
	}
	public function checkMemberFromPlatform($openid = '', $acc = '') 
	{
		global $_W;
		if (empty($acc)) 
		{
			$acc = WeiXinAccount::create();
		}
		$userinfo = $acc->fansQueryInfo($openid);
		$userinfo['avatar'] = $userinfo['headimgurl'];
		$redis = redis();
		if (!(is_error($redis))) 
		{
			$member = $redis->get($openid . '_checkMemberFromPlatform');
			if (!(empty($member))) 
			{
				return json_decode($member, true);
			}
		}
		load()->model('mc');
		$uid = mc_openid2uid($openid);
		if (!(empty($uid))) 
		{
			pdo_update('mc_members', array('nickname' => $userinfo['nickname'], 'gender' => $userinfo['sex'], 'nationality' => $userinfo['country'], 'resideprovince' => $userinfo['province'], 'residecity' => $userinfo['city'], 'avatar' => $userinfo['headimgurl']), array('uid' => $uid));
		}
		pdo_update('mc_mapping_fans', array('nickname' => $userinfo['nickname']), array('uniacid' => $_W['uniacid'], 'openid' => $openid));
		$member = $this->getMember($openid);
		if (empty($member)) 
		{
			$mc = mc_fetch($uid, array('realname', 'nickname', 'mobile', 'avatar', 'resideprovince', 'residecity', 'residedist'));
			$member = array('uniacid' => $_W['uniacid'], 'uid' => $uid, 'openid' => $openid, 'realname' => $mc['realname'], 'mobile' => $mc['mobile'], 'nickname' => (!(empty($mc['nickname'])) ? $mc['nickname'] : $userinfo['nickname']), 'avatar' => (!(empty($mc['avatar'])) ? $mc['avatar'] : $userinfo['avatar']), 'gender' => (!(empty($mc['gender'])) ? $mc['gender'] : $userinfo['sex']), 'province' => (!(empty($mc['resideprovince'])) ? $mc['resideprovince'] : $userinfo['province']), 'city' => (!(empty($mc['residecity'])) ? $mc['residecity'] : $userinfo['city']), 'area' => $mc['residedist'], 'createtime' => time(), 'status' => 0);
			pdo_insert('ewei_shop_member', $member);
			if (method_exists(m('member'), 'memberRadisCountDelete')) 
			{
				m('member')->memberRadisCountDelete();
			}
			$member['id'] = pdo_insertid();
			$member['isnew'] = true;
		}
		else 
		{
			$member['nickname'] = $userinfo['nickname'];
			$member['avatar'] = $userinfo['headimgurl'];
			$member['province'] = $userinfo['province'];
			$member['city'] = $userinfo['city'];
			pdo_update('ewei_shop_member', $member, array('id' => $member['id']));
			if ((time() - $member['createtime']) < 60) 
			{
				$member['isnew'] = true;
			}
			else 
			{
				$member['isnew'] = false;
			}
		}
		if (!(is_error($redis))) 
		{
			$redis->set($openid . '_checkMemberFromPlatform', json_encode($member), 20);
		}
		return $member;
	}
	public function mc_update($mid, $data) 
	{
		global $_W;
		if (empty($mid) || empty($data)) 
		{
			return;
		}
		$wapset = m('common')->getSysset('wap');
		$member = $this->getMember($mid);
		if (!(empty($wapset['open'])) && isset($data['mobile']) && ($data['mobile'] != $member['mobile'])) 
		{
			unset($data['mobile']);
		}
		load()->model('mc');
		mc_update($this->member['uid'], $data);
	}
	public function checkMemberSNS($sns) 
	{
		global $_W;
		global $_GPC;
		if (empty($sns)) 
		{
			$sns = $_GPC['sns'];
		}
		if (empty($sns)) 
		{
			return;
		}
		if ($sns == 'wx') 
		{
			load()->func('communication');
			$token = trim($_GPC['token']);
			$openid = trim($_GPC['openid']);
			$appid = 'wxc3d9d8efae0ae858';
			$secret = '93d4f6085f301c405b5812217e6d5025';
			if (empty($token) && !(empty($_GPC['code']))) 
			{
				$codeurl = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . $appid . '&secret=' . $secret . '&code=' . trim($_GPC['code']) . '&grant_type=authorization_code';
				$coderesult = $userinfo = ihttp_request($codeurl);
				$coderesult = json_decode($coderesult['content'], true);
				if (empty($coderesult['access_token']) || empty($coderesult['openid'])) 
				{
					return;
				}
				$token = $coderesult['access_token'];
				$openid = $coderesult['openid'];
			}
			if (empty($token) || empty($openid)) 
			{
				return;
			}
			$snsurl = 'https://api.weixin.qq.com/sns/userinfo?access_token=' . $token . '&openid=' . $openid . '&lang=zh_CN';
			$userinfo = ihttp_request($snsurl);
			$userinfo = json_decode($userinfo['content'], true);
			if (empty($userinfo['openid'])) 
			{
				return;
			}
			$userinfo['openid'] = 'sns_wx_' . $userinfo['openid'];
		}
		else if ($sns == 'qq') 
		{
			$userinfo = htmlspecialchars_decode($_GPC['userinfo']);
			$userinfo = json_decode($userinfo, true);
			$userinfo['openid'] = 'sns_qq_' . $_GPC['openid'];
			$userinfo['headimgurl'] = $userinfo['figureurl_qq_2'];
			(($userinfo['gender'] == '男' ? 1 : 2));
		}
		$data = array('nickname' => $userinfo['nickname'], 'avatar' => $userinfo['headimgurl'], 'province' => $userinfo['province'], 'city' => $userinfo['city'], 'gender' => $userinfo['sex'], 'comefrom' => 'h5app_sns_' . $sns);
		$openid = trim($_GPC['openid']);
		if ($sns == 'qq') 
		{
			$data['openid_qq'] = trim($_GPC['openid']);
			$openid = 'sns_qq_' . trim($_GPC['openid']);
		}
		if ($sns == 'wx') 
		{
			$data['openid_wx'] = trim($_GPC['openid']);
			$openid = 'sns_wx_' . trim($_GPC['openid']);
		}
		$member = $this->getMember($openid);
		if (empty($member)) 
		{
			$data['openid'] = $userinfo['openid'];
			$data['uniacid'] = $_W['uniacid'];
			$data['comefrom'] = 'sns_' . $sns;
			$data['createtime'] = time();
			$data['salt'] = m('account')->getSalt();
			$data['pwd'] = rand(10000, 99999) . $data['salt'];
			pdo_insert('ewei_shop_member', $data);
			if (method_exists(m('member'), 'memberRadisCountDelete')) 
			{
				m('member')->memberRadisCountDelete();
			}
			return pdo_insertid();
		}
		if (empty($member['bindsns']) || ($member['bindsns'] == $sns)) 
		{
			pdo_update('ewei_shop_member', $data, array('id' => $member['id'], 'uniacid' => $_W['uniacid']));
			return $member['id'];
		}
	}
	public function compareLevel(array $level, array $levels = array()) 
	{
		global $_W;
		$levels = ((!(empty($levels)) ? $levels : $this->getLevels()));
		$old_key = -1;
		$new_key = -1;
		foreach ($levels as $kk => $vv ) 
		{
			if ($vv['id'] == $level[0]) 
			{
				$old_key = $vv['level'];
			}
			if ($vv['id'] == $level[1]) 
			{
				$new_key = $vv['level'];
			}
		}
		return $old_key < $new_key;
	}
	public function wxuser($appid, $secret, $snsapi = 'snsapi_base', $expired = '600') 
	{
		global $_W;
		$wxuser = $_COOKIE[$_W['config']['cookie']['pre'] . $appid];
		if ($wxuser === NULL) 
		{
			$code = ((isset($_GET['code']) ? $_GET['code'] : ''));
			if (!($code)) 
			{
				$url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
				$oauth_url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=' . $appid . '&redirect_uri=' . urlencode($url) . '&response_type=code&scope=' . $snsapi . '&state=wxbase#wechat_redirect';
				header('Location: ' . $oauth_url);
				exit();
			}
			load()->func('communication');
			$getOauthAccessToken = ihttp_get('https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . $appid . '&secret=' . $secret . '&code=' . $code . '&grant_type=authorization_code');
			$json = json_decode($getOauthAccessToken['content'], true);
			if (!(empty($json['errcode'])) && (($json['errcode'] == '40029') || ($json['errcode'] == '40163'))) 
			{
				$url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . ((strpos($_SERVER['REQUEST_URI'], '?') ? '' : '?'));
				$parse = parse_url($url);
				if (isset($parse['query'])) 
				{
					parse_str($parse['query'], $params);
					unset($params['code'], $params['state']);
					$url = 'http://' . $_SERVER['HTTP_HOST'] . $parse['path'] . '?' . http_build_query($params);
				}
				$oauth_url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=' . $appid . '&redirect_uri=' . urlencode($url) . '&response_type=code&scope=' . $snsapi . '&state=wxbase#wechat_redirect';
				header('Location: ' . $oauth_url);
				exit();
			}
			if ($snsapi == 'snsapi_userinfo') 
			{
				$userinfo = ihttp_get('https://api.weixin.qq.com/sns/userinfo?access_token=' . $json['access_token'] . '&openid=' . $json['openid'] . '&lang=zh_CN');
				$userinfo = $userinfo['content'];
			}
			else if ($snsapi == 'snsapi_base') 
			{
				$userinfo = array();
				$userinfo['openid'] = $json['openid'];
			}
			$userinfostr = json_encode($userinfo);
			isetcookie($appid, $userinfostr, $expired);
			return $userinfo;
		}
		return json_decode($wxuser, true);
	}
	public function memberRadisCount($key, $value) 
	{
		global $_W;
		$redis = redis();
		if (!(is_error($redis))) 
		{
			if (empty($value)) 
			{
				if ($redis->get($key) != false) 
				{
					return $redis->get($key);
				}
				return false;
			}
			$redis->set($key, $value, array(0 => 'nx', 'ex' => '3600'));
		}
	}
	public function memberRadisCountDelete() 
	{
		global $_W;
		$open_redis = function_exists('redis') && !(is_error(redis()));
		if ($open_redis) 
		{
			$redis = redis();
			$keysArr = $redis->keys('ewei_' . $_W['uniacid'] . '_member*');
			if (!(empty($keysArr)) && is_array($keysArr)) 
			{
				foreach ($keysArr as $k => $v ) 
				{
					$redis->del($v);
				}
			}
		}
	}

	/*
	openid 降级用户
	levelid 降级对应等级id
	*/
	public function memberDegrade($openid,$levelid='')
	{	
		global $_W;

		if (empty($openid)) 
		{
			return;
		}

		$member = $this->getMember($openid);

		if ($member['level']==$levelid) {
			return ;
		}

		$candegrade=false;
		$currentlevel = $this->getlevel($openid);

		$level = pdo_fetch("SELECT * from ".tablename('ewei_shop_member_level')." WHERE uniacid=:uniacid AND id=:id", array(':uniacid'=>$_W['uniacid'],':id'=>$levelid));

		if ($currentlevel['level']>$level['level']) {
			$candegrade=true;
		}

		if ($candegrade) {
			if (empty($level)) {

				pdo_update('ewei_shop_member',array('level' =>0), array('id' => $member['id']));
			}else{

				pdo_update('ewei_shop_member',array('level' =>$level[id]), array('id' => $member['id']));
			}
		}
		

	}
}
?>