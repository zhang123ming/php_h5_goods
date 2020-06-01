<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Transfer_EweiShopV2Page extends MobileLoginPage
{
	public function main()
	{
		global $_W;
		global $_GPC;
		$set = $_W['shopset'];
		$credit = m('member')->getCredit($_W['openid'], 'credit2');
		include $this->template();
	}
	public function submit()
	{
		global $_W;
		global $_GPC;
		$credit = m('member')->getCredit($_W['openid'], 'credit2');
		$mobile = trim($_GPC['tomobile']);
		$num = trim($_GPC['num']);
		$agentInfo = pdo_fetch('select id,uid,openid,credit2,nickname from ' . tablename('ewei_shop_member') . ' where mobile=:mobile and mobileverify = 1 and uniacid=:uniacid limit 1',array(':mobile'=>$mobile,':uniacid'=>$_W['uniacid']));
		
		if(empty($agentInfo)) exit(json_encode(array('code'=>100,'msg' =>'用户不存在')));
		if($agentInfo['openid'] == $_W['openid']) exit(json_encode(array('code'=>100,'msg' =>'不能给自己转账')));
		if($credit<$num) exit(json_encode(array('code'=>101,'msg' =>'帐户余额不够')));
		if (!empty($agentInfo)&&$agentInfo['openid'] != $_W['openid']) {
			m('member')->setCredit($agentInfo['openid'], 'credit2', $num, array($_W['uid'], '余额转入:'.$_W['ewei_shopv2_member']['nickname']));
			m('member')->setCredit($_W['openid'], 'credit2', -$num, array($_W['uid'], '余额转出:'));
			
			$data = array('openid' => $agentInfo['openid'], 'logno' => $logno, 'uniacid' => $_W['uniacid'], 'type' => '0', 'createtime' => TIMESTAMP, 'status' => '1', 'title' => '余额转入:'.$_W['ewei_shopv2_member']['nickname'], 'money' => $num, 'remark' => '', 'rechargetype' => 'system');
			pdo_insert('ewei_shop_member_log', $data);
			$logid = pdo_insertid();
			m('notice')->sendMemberLogMessage($logid, 0, true);
			$data['openid'] = $_W['openid'];
			$data['logno'] = $logno;
			$data['money'] = -$num;
			$data['title'] = '余额转出:'.$agentInfo['nickname'];
			pdo_insert('ewei_shop_member_log', $data);
			$logid = pdo_insertid();
			m('notice')->sendMemberLogMessage($logid, 0, true);
			exit(json_encode(array('code'=>102,'msg' =>'成功转账')));
		}
		
	}
}
?>