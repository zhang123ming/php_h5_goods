<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}
require EWEI_SHOPV2_PLUGIN . 'commission/core/page_login_mobile.php';
class CashBe_EweiShopV2Page extends CommissionMobileLoginPage
{
	public function main()
	{
		global $_W;
		global $_GPC;
		$pluginset = m('common')->getPluginset();
		$_GPC['type'] = intval($_GPC['type']);
		// $record_totalall = m("common")->record($_W['openid'],'getsum',array('total'))['total'];
		$member = m('member')->getMember($_W['openid']);
		$other = $_GPC['other_commission'];
		$condition = ' and openid=:openid and uniacid=:uniacid ';
		$params = array(':uniacid' => $_W['uniacid'], ':openid' => $_W['openid']);
		$total = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_commission_record') . ' where 1 ' . $condition, $params);
		include $this->template();
	}

	public function get_list()
	{
		global $_W;
		global $_GPC;
		$type = intval($_GPC['type']);
		$pindex = max(1, intval($_GPC['page']));
		$psize = 10;
		$condition = ' and openid=:openid and uniacid=:uniacid ';
		$params = array(
			':uniacid'=>$_W['uniacid'],
			':openid'=>$_W['openid']
		);
		$list = pdo_fetchall('select * from ' . tablename('ewei_shop_commission_record') . ' where 1 ' . $condition . ' order by createtime desc LIMIT ' . (($pindex - 1) * $psize) . ',' . $psize, $params);
		$total = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_commission_record') . ' where 1 ' . $condition, $params);
		foreach ($list as &$row) {
			$row['createtime'] = date('Y-m-d H:i', $row['createtime']);
		}

		unset($row);
		show_json(1, array('list' => $list, 'total' => $total, 'pagesize' => $psize));
	}

	public function team()
	{
		global $_W,$_GPC;
		$member = m('member')->getLevel($_W['openid']);
		$total = pdo_fetchcolumn('select sum(amount) from '.tablename('ewei_shop_commission_record').' where uniacid=:uniacid and openid=:openid and status=0 and where_from = :where',array(':uniacid'=>$_W['uniacid'],':openid'=>$_W['openid'],':where'=>'dayBonus'));
		$total = empty($total)?number_format(0,2):$total;
		include $this->template();

	}

	public function get_team_list()
	{
		global $_W,$_GPC;
		$pindex = max(1, intval($_GPC['page']));
		$psize = 10;
		$condition = ' and openid=:openid and uniacid=:uniacid and amount>0 and where_from=:where';
		$params = array(':uniacid' => $_W['uniacid'], ':openid' => $_W['openid'],':where'=>'dayBonus');
		$list = pdo_fetchall('select amount,createtime from ' . tablename('ewei_shop_commission_record') . ' where 1 ' . $condition . ' order by createtime desc LIMIT ' . (($pindex - 1) * $psize) . ',' . $psize, $params);
		$total = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_commission_record') . ' where 1 ' . $condition, $params);
		foreach ($list as &$row) {
			$row['createtime'] = date('Y-m-d H:i', $row['createtime']);
		}
		unset($row);
		show_json(1, array('list' => $list, 'total' => $total, 'pagesize' => $psize));
	}


}

?>
