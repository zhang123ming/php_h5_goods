<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Index_EweiShopV2Page extends MobileLoginPage
{
	public function main()
	{
		global $_W;
		global $_GPC;
		$type = intval($_GPC['type'])?'0':'1';
		// var_dump($_GPC['type']);die;
		include $this->template();
	}


	public function get_list()
	{	
		global $_W;
		global $_GPC;

		$type = intval($_GPC['type']);
		$pindex = max(1, intval($_GPC['page']));
		$psize = 10;

		$condition = ' and openid=:openid and uniacid=:uniacid and type=:type';
		$params = array(':uniacid' => $_W['uniacid'], ':openid' => $_W['openid'], ':type' => intval($_GPC['type']));
		$list = pdo_fetchall('select * from ' . tablename('ewei_shop_invoice') . ' where 1 ' . $condition . ' order by createtime desc LIMIT ' . (($pindex - 1) * $psize) . ',' . $psize, $params);
		$total = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_invoice') . ' where 1 ' . $condition, $params);
		foreach ($list as &$row) {
			$row['createTime'] = date('Y-m-d H:i', $row['createTime']);
			
		}

		unset($row);
		show_json(1, array('list' => $list, 'total' => $total, 'pagesize' => $psize));
	}
}

?>
