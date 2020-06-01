<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Log_EweiShopV2Page extends PluginWebPage
{
	public function main()
	{
		global $_W;
		global $_GPC;
		$pindex = max(1, intval($_GPC['page']));
		$psize = 10;
		$params = array(':uniacid' => $_W['uniacid']);
		$condition = ' and rj.uniacid=:uniacid ';
		$keyword = trim($_GPC['keyword']);

		if (!empty($keyword)) {
			$condition .= ' AND ( m.nickname LIKE :keyword or m.realname LIKE :keyword or m.mobile LIKE :keyword ) ';
			$params[':keyword'] = '%' . $keyword . '%';
		}

		if (!empty($_GPC['time']['start']) && !empty($_GPC['time']['end'])) {
			$starttime = strtotime($_GPC['time']['start']);
			$endtime = strtotime($_GPC['time']['end']);
			$condition .= ' AND rj.addtime >= :starttime AND rj.addtime <= :endtime ';
			$params[':starttime'] = $starttime;
			$params[':endtime'] = $endtime;
		}

		$list = pdo_fetchall('SELECT rj.*, m.avatar,m.nickname,m.realname,m.mobile FROM ' . tablename('ewei_shop_rebate_join') . ' rj ' . ' left join ' . tablename('ewei_shop_member') . ' m on m.openid = rj.popenid  and m.uniacid = rj.uniacid' . ' WHERE 1 ' . $condition . ' ORDER BY rj.addtime desc ' . '  LIMIT ' . (($pindex - 1) * $psize) . ',' . $psize, $params);
		$total = pdo_fetchcolumn('SELECT count(*)  FROM ' . tablename('ewei_shop_rebate_join') . ' rj ' . ' left join ' . tablename('ewei_shop_member') . ' m on m.openid = rj.popenid  and m.uniacid = rj.uniacid' . ' where 1 ' . $condition . '  ', $params);
		$pager = pagination2($total, $pindex, $psize);
		$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('ewei_shop_lottery_join') . ' where lottery_id=:lottery_id ', array(':lottery_id' => intval($_GPC['id'])));
		load()->func('tpl');
		include $this->template();
	}

	public function detail()
	{
		global $_W;
		global $_GPC;


		$pindex = max(1, intval($_GPC['page']));
		$psize = 10;
		$params = array(':uniacid' => $_W['uniacid']);

		$condition  = " and log.join_id = '{$_GPC[id]}'";
		$sql="SELECT log.*,m.avatar,m.nickname,m.realname,m.mobile FROM".tablename('ewei_shop_rebate_log')." log ". ' left join '.tablename('ewei_shop_order')." o on o.id= log.orderid " . ' left join ' . tablename('ewei_shop_member') . ' m on m.openid=o.openid and m.uniacid =  o.uniacid ' . ' WHERE 1 '.$condition .' ORDER BY log.addtime DESC '. ' LIMIT '.(($pindex - 1) * $psize) . ',' . $psize;
		$list = pdo_fetchall($sql,$params);
		$total = pdo_fetchcolumn("SELECT count(*) FROM ".tablename('ewei_shop_rebate_log')." log ". ' left join '.tablename('ewei_shop_order')." o on o.id= log.orderid " . ' left join ' . tablename('ewei_shop_member') . ' m on m.openid=o.openid and m.uniacid =  o.uniacid ' . ' WHERE 1 '.$condition .' ', $params);
		$pager = pagination2($total, $pindex, $psize);

		include $this->template('rebate/detail');

	}
}

?>
