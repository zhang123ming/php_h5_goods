<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Log_EweiShopV2Page extends MobileLoginPage
{
	public function main()
	{
		global $_W;
		global $_GPC;
		$_GPC['type'] = intval($_GPC['type']);
		include $this->template();
	}

	public function get_list()
	{
		global $_W;
		global $_GPC;
		$type = intval($_GPC['type']);
		$pindex = max(1, intval($_GPC['page']));
		$psize = 10;
		$apply_type = array(0 => '微信钱包', 2 => '支付宝', 3 => '银行卡');
		if ($_GPC['type']) {
			$condition = ' and openid=:openid and uniacid=:uniacid and type=:type';
			$params = array(':uniacid' => $_W['uniacid'], ':openid' => $_W['openid'], ':type' => intval($_GPC['type']));
			$tablename = 'ewei_shop_member_log';
		}else{
			$condition = ' and openid=:openid and uniacid=:uniacid and credittype="credit2"';
			$params = array(':uniacid' => $_W['uniacid'], ':openid' => $_W['openid']);
			$tablename = 'mc_credits_record';
		}
		
		$list = pdo_fetchall('select * from ' . tablename($tablename) . ' where 1 ' . $condition . ' order by createtime desc LIMIT ' . (($pindex - 1) * $psize) . ',' . $psize, $params);
		$total = pdo_fetchcolumn('select count(*) from ' . tablename($tablename) . ' where 1 ' . $condition, $params);

		foreach ($list as &$row) {
			$row['createtime'] = date('Y-m-d H:i', $row['createtime']);
			$row['typestr'] = $apply_type[$row['applytype']];
		}

		unset($row);
		show_json(1, array('list' => $list, 'total' => $total, 'pagesize' => $psize));
	}
}

?>
