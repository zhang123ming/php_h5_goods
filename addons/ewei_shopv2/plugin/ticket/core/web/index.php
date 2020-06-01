<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Index_EweiShopV2Page extends PluginWebPage
{
	public function main()
	{
		global $_W;
		global $_GPC;
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		$params = array(':uniacid' => $_W['uniacid']);
		$condition = 'and t.uniacid=:uniacid ';

		if (empty($starttime) || empty($endtime)) {
			$starttime = strtotime('-1 month');
			$endtime = time();
		}

		if (!empty($_GPC['time']['start']) && !empty($_GPC['time']['end'])) {
			$starttime = strtotime($_GPC['time']['start']);
			$endtime = strtotime($_GPC['time']['end']);
			$condition .= ' AND t.create_time >= :starttime AND t.create_time <= :endtime ';
			$params[':starttime'] = $starttime;
			$params[':endtime'] = $endtime;
		}
		if(!empty($_GPC['is_back'])){
			$condition .= ' AND t.is_back = 1 ';
		}
		if(!empty($_GPC['is_check'])){
			$condition .= ' AND t.is_check = 1 ';
		}
		if(!empty($_GPC['followed'])){
			$condition .= ' AND f.follow=:follow ';
			$params[':follow'] = $_GPC['followed'];
		}

		if (!empty($_GPC['keyword'])) {
			$_GPC['keyword'] = trim($_GPC['keyword']);
			$condition .= ' AND ( t.`number` LIKE :keyword OR t.openid LIKE :keyword OR m.nickname LIKE :keyword OR m.mobile LIKE :keyword ) ';
			$params[':keyword'] = '%' . trim($_GPC['keyword']) . '%';
		}

		$sql = 'select t.*,m.id as uid,m.mobile,m.nickname,m.realname,m.avatar,m.nickname as parentname,m.avatar as parentavatar,m.isblack,m.agentid,m.isagent,f.follow as followed,f.unfollowtime from' . tablename('ewei_shop_member_ticket') . ' t left join ' . tablename('ewei_shop_member') . ' m on t.openid = m.openid  left join ' . tablename('mc_mapping_fans') . ' f on f.openid = m.openid and f.uniacid = m.uniacid ' . ' where  1 ' . $condition . ' ORDER BY t.create_time desc  limit ' . (($pindex - 1) * $psize) . ',' . $psize;

		$list = pdo_fetchall($sql,$params);
		$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('ewei_shop_member_ticket') . ' t left join ' . tablename('ewei_shop_member') . ' m on t.openid = m.openid left join ' . tablename('mc_mapping_fans') . ' f on f.openid = m.openid and f.uniacid = m.uniacid ' . ' where  1 ' . $condition . ' ', $params);
		$pager = pagination2($total, $pindex, $psize);


		include $this->template();
		
	}

	public function check()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		$operator = pdo_fetch('SELECT username FROM ' . tablename('users') . ' WHERE uid=:uid ',array(':uid'=>$_W['uid']));
		if (empty($id)) {
			$id = (is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0);
		}

		$is_check = intval($_GPC['is_check']);
		$tickets = pdo_fetchall('SELECT id,openid,is_check,`number` FROM ' . tablename('ewei_shop_member_ticket') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);
		$time = time();
		foreach ($tickets as $ticket) {
			if ($ticket['is_check'] === $is_check) {
				continue;
			}
			$member = pdo_fetch('SELECT id,openid,nickname,realname,mobile FROM ' . tablename('ewei_shop_member') . ' WHERE openid=:openid AND uniacid=:uniacid',array(':openid'=>$ticket['openid'],':uniacid'=>$_W['uniacid']));
			if ($is_check == 1) {
				pdo_update('ewei_shop_member_ticket', array('is_check' => 1, 'update_time' => $time), array('id' => $ticket['id'], 'uniacid' => $_W['uniacid']));
				plog('ticket.index.check', '审核小票 <br/>小票信息:  小票流水号: '. $ticket['number'] . ' /  ' . $member['openid'] . '/' . $member['nickname'] . '/' . $member['realname'] . '/' . $member['mobile']);
				// $this->model->sendMessage($ticket['openid'], array('nickname' => $member['nickname'], 'update_time' => $time), TM_COMMISSION_BECOME);
				$data = array();
				$data['tid'] = $id;
				$data['number'] = $ticket['number'];
				$data['uniacid'] = $_W['uniacid'];
				$data['remark'] = '审核信息:  小票流水号: '. $ticket['number'] . ' ;用户名: ' . $member['nickname'] .';用户手机:'.$member['mobile'] ;
				$data['create_time'] = time();
				$data['operator'] = $operator['username'];
				pdo_insert('ewei_shop_member_ticket_log',$data);
			}
			else {
				pdo_update('ewei_shop_member_ticket', array('is_check' => 0, 'update_time' => 0), array('id' => $ticket['id'], 'uniacid' => $_W['uniacid']));
				plog('ticket.idnex.check', '取消审核 <br/>小票信息:  小票流水号: '. $ticket['number'] . ' /  ' . $member['openid'] . '/' . $member['nickname'] . '/' . $member['realname'] . '/' . $member['mobile'] );
				$data = array();
				$data['tid'] = $id;
				$data['number'] = $ticket['number'];
				$data['uniacid'] = $_W['uniacid'];
				$data['remark'] = '取消审核: 小票流水号: '. $ticket['number'] . ' ;用户名: ' . $member['nickname'] .';用户手机:'.$member['mobile'] ;
				$data['create_time'] = time();
				$data['operator'] = $operator['username'];
				pdo_insert('ewei_shop_member_ticket_log',$data);
			}
		}
		

		show_json(1, array('url' => referer()));
	}

	public function delete()
	{
		global $_W;
		global $_GPC;
		$id = $_GPC['id'];
		$operator = pdo_fetch('SELECT username FROM ' . tablename('users') . ' WHERE uid=:uid ',array(':uid'=>$_W['uid']));
		if (empty($id)) {
			$id = (is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0);
		}

		$tickets = pdo_fetchall('SELECT id,openid,`number` FROM ' . tablename('ewei_shop_member_ticket') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);
		
		foreach ($tickets as $ticket) {
			$member = pdo_fetch('SELECT id,openid,nickname,realname,mobile FROM ' . tablename('ewei_shop_member') . ' WHERE openid=:openid AND uniacid=:uniacid',array(':openid'=>$ticket['openid'],':uniacid'=>$_W['uniacid']));
			pdo_delete('ewei_shop_member_ticket',  array('id' => $ticket['id']));
			plog('ticket.index.delete', '删除用户小票 <br/>小票信息:  小票流水号: '. $ticket['number'] . ' /  ' . $member['openid'] . '/' . $member['nickname'] . '/' . $member['realname'] . '/' . $member['mobile']);
			$data = array();
			$data['tid'] = $id;
			$data['number'] = $ticket['number'];
			$data['uniacid'] = $_W['uniacid'];
			$data['remark'] = '删除用户小票:  小票流水号: '. $ticket['number'] . ' ;用户名: ' . $member['nickname'] .';用户手机:'.$member['mobile'] ;
			$data['create_time'] = time();
			$data['operator'] = $operator['username'];
			pdo_insert('ewei_shop_member_ticket_log',$data);

		}

		show_json(1, array('url' => referer()));
	}

	public function back()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);
		$is_back = intval($_GPC['is_back']);
		$operator = pdo_fetch('SELECT username FROM ' . tablename('users') . ' WHERE uid=:uid ',array(':uid'=>$_W['uid']));
		$ticket = pdo_fetch('SELECT id,openid,is_back,`number` FROM ' . tablename('ewei_shop_member_ticket') . ' WHERE id = ' . $id . '  AND uniacid=' . $_W['uniacid']);
		$member = pdo_fetch('SELECT id,openid,nickname,realname,mobile FROM ' . tablename('ewei_shop_member') . ' WHERE openid=:openid AND uniacid=:uniacid',array(':openid'=>$ticket['openid'],':uniacid'=>$_W['uniacid']));
		
		if ($is_back == 1) {
			pdo_update('ewei_shop_member_ticket', array('is_back' => 1), array('id' => $ticket['id']));
			plog('ticket.index.back', '驳回小票 <br/>驳回小票信息:  小票流水号: '. $ticket['number'] . ' /  ' . $member['openid'] . '/' . $member['nickname'] . '/' . $member['realname'] . '/' . $member['mobile']);
			$data = array();
			$data['tid'] = $id;
			$data['number'] = $ticket['number'];
			$data['uniacid'] = $_W['uniacid'];
			$data['remark'] = '驳回信息:  小票流水号: '. $ticket['number'] . ' ;用户名: ' . $member['nickname'] .';用户手机:'.$member['mobile'] ;
			$data['create_time'] = time();
			$data['operator'] = $operator['username'];
			pdo_insert('ewei_shop_member_ticket_log',$data);
		}
		else {
			pdo_update('ewei_shop_member_ticket', array('is_back' => 0), array('id' => $ticket['id']));
			plog('ticket.index.back', '取消驳回 <br/>小票信息:  小票流水号: '. $ticket['number'] . ' /  ' . $member['openid'] . '/' . $member['nickname'] . '/' . $member['realname'] . '/' . $member['mobile']);
			$data = array();
			$data['tid'] = $id;
			$data['number'] = $ticket['number'];
			$data['uniacid'] = $_W['uniacid'];
			$data['remark'] = '取消驳回:  小票流水号: '. $ticket['number'] . ' ;用户名: ' . $member['nickname'] .';用户手机:'.$member['mobile'] ;
			$data['create_time'] = time();
			$data['operator'] = $operator['username'];
			pdo_insert('ewei_shop_member_ticket_log',$data);
		}

		show_json(1, array('url' => referer()));
		
	}

	
}

?>
