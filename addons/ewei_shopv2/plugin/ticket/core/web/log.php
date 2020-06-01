<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Log_EweiShopV2Page extends WebPage
{
	public function main()
	{
		global $_W;
		global $_GPC;
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		$condition = ' and l.uniacid=:uniacid';
		$params = array(':uniacid' => $_W['uniacid']);
		if (empty($starttime) || empty($endtime)) {
			$starttime = strtotime('-1 month');
			$endtime = time();
		}

		if (!empty($_GPC['time']['start']) && !empty($_GPC['time']['end'])) {
			$starttime = strtotime($_GPC['time']['start']);
			$endtime = strtotime($_GPC['time']['end']);
			$condition .= ' AND rd.create_time >= :starttime AND rd.create_time <= :endtime ';
			$params[':starttime'] = $starttime;
			$params[':endtime'] = $endtime;
		}
		

		if (!empty($_GPC['keyword'])) {
			$_GPC['keyword'] = trim($_GPC['keyword']);
			$condition .= ' AND ( l.`number` LIKE :keyword OR t.openid LIKE :keyword OR m.nickname LIKE :keyword OR m.mobile LIKE :keyword ) ';
			$params[':keyword'] = '%' . trim($_GPC['keyword']) . '%';
		}
		
		$list = pdo_fetchall("SELECT l.*,m.id as uid,t.content,m.mobile,m.nickname,m.realname,m.avatar,m.nickname as parentname,m.avatar as parentavatar,m.isblack,m.agentid,m.isagent,f.follow as followed,f.unfollowtime FROM "  . tablename('ewei_shop_member_ticket_log') . ' l left join ' . tablename('ewei_shop_member_ticket') . ' t on t.id = l.tid left join '  . tablename('ewei_shop_member') . ' m on t.openid = m.openid  left join ' . tablename('mc_mapping_fans') . ' f on f.openid = m.openid and f.uniacid = m.uniacid ' . ' where  1 ' . $condition . ' ORDER BY l.create_time desc limit ' . (($pindex - 1) * $psize) . ',' . $psize,$params);
		$total = pdo_fetchcolumn('SELECT count(*) FROM ' . tablename('ewei_shop_member_ticket_log') . ' l left join ' . tablename('ewei_shop_member_ticket') . ' t on t.id = l.tid left join '  . tablename('ewei_shop_member') . ' m on t.openid = m.openid  left join ' . tablename('mc_mapping_fans') . ' f on f.openid = m.openid and f.uniacid = m.uniacid ' . ' where  1 ' . $condition ,$params);

		$pager = pagination2($total, $pindex, $psize);
		
		include $this->template();
	}

	public function delete()
	{
		global $_W;
		global $_GPC;
		$id = intval($_GPC['id']);

		if (empty($id)) {
			$id = (is_array($_GPC['ids']) ? implode(',', $_GPC['ids']) : 0);
		}

		$items = pdo_fetchall('SELECT id,`number`,openid FROM ' . tablename('ewei_shop_member_ticket_log') . ' WHERE id in( ' . $id . ' ) AND uniacid=' . $_W['uniacid']);
		foreach ($items as $item) {
			$member = pdo_fetch('SELECT id,openid,nickname,realname,mobile FROM ' . tablename('ewei_shop_member') . ' WHERE openid=:openid AND uniacid=:uniacid',array(':openid'=>$ticket['openid'],':uniacid'=>$_W['uniacid']));
			pdo_delete('ewei_shop_member_ticket_log', array('id' => $item['id']));
			plog('ticket.log.delete', '删除操作小票记录 <br/>小票信息:  小票流水号: '. $ticket['number'] . ' /  ' . $member['openid'] . '/' . $member['nickname'] . '/' . $member['realname'] . '/' . $member['mobile']);
		}

		show_json(1, array('url' => referer()));
	}

}

?>
