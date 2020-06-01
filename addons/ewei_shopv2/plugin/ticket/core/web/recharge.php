<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Recharge_EweiShopV2Page extends PluginWebPage
{
	public function main()
	{
		global $_W;
		global $_GPC;
		$type = trim($_GPC['type']);

		if (!cv('ticket.recharge.' . $type)) {
			$this->message('你没有相应的权限查看');
		}

		$uid = intval($_GPC['uid']);
		$id = intval($_GPC['id']);
		$ticket = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_member_ticket') . ' WHERE id =' . $id);
		$operator = pdo_fetch('SELECT username FROM ' . tablename('users') . ' WHERE uid=:uid ',array(':uid'=>$_W['uid']));
		$profile = m('member')->getMember($uid, true);
		if ($_W['ispost']) {
			$typestr = $_W['shopset']['trade']['credittext'];
			$num = intval($_GPC['num']);
			$remark = trim($_GPC['remark']);

			if ($num <= 0) {
				show_json(0, array('message' => '请填写大于0的数字!'));
			}

			m('member')->setCredit($profile['openid'], $type, $num, array($_W['uid'], '后台小票充值' . $typestr . ' ' . $remark));
			$changetype = 0;
			// if ($type == 'credit1') {
			// 	m('notice')->sendMemberPointChange($profile['openid'], $changenum, $changetype);
			// }

			plog('finance.recharge.' . $type, '充值' . $typestr . ': ' . $_GPC['num'] . ' <br/>会员信息: ID: ' . $profile['uid'] . ' /  ' . $profile['openid'] . '/' . $profile['nickname'] . '/' . $profile['realname'] . '/' . $profile['mobile']);
			$data = array();
			$data['tid'] = $id;
			$data['number'] = $ticket['number'];
			$data['uniacid'] = $_W['uniacid'];
			$data['remark'] = $remark;
			$data['create_time'] = time();
			$data['credit1'] = $num;
			$data['operator'] = $operator['username'];
			pdo_insert('ewei_shop_member_ticket_record',$data);
			pdo_update('ewei_shop_member_ticket',array('is_recharge' => 1),array('id'=>$id));
			$row = array();
			$row['tid'] = $id;
			$data['number'] = $ticket['number'];
			$row['uniacid'] = $_W['uniacid'];
			$row['remark'] = '小票充值积分:  小票流水号:' . $ticket['number'] . ' ;用户名: ' . $profile['nickname'] .';用户手机:'.$profile['mobile'] . ';充值积分: ' . $_GPC['num'] ;
			$row['create_time'] = time();
			$row['operator'] = $operator['username'];
			pdo_insert('ewei_shop_member_ticket_log',$row);
			show_json(1, array('url' => referer()));
		}

		include $this->template();
	}
}

?>
