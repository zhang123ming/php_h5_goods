<?php

if (!defined('IN_IA')) {
	exit('Access Denied');
}

require EWEI_SHOPV2_PLUGIN . 'app/core/page_mobile.php';
class Invoicelog_EweiShopV2Page extends AppMobilePage
{
	public function get_invoicelist()
	{
		global $_W;
		global $_GPC;
		
		// var_dump($_GPC);die;
		$type = intval($_GPC['type']);
		$pindex = max(1, intval($_GPC['page']));
		$psize = 10;
		// $apply_type = array(0 => '微信钱包', 2 => '支付宝', 3 => '银行卡');
		$condition = ' and openid=:openid and uniacid=:uniacid and type=:type';
		$params = array(':uniacid' => $_W['uniacid'], ':openid' => $_W['openid'], ':type' => intval($_GPC['type']));

		$list = pdo_fetchall('select * from ' . tablename('ewei_shop_invoice') . ' where 1 ' . $condition . ' order by createTime desc LIMIT ' . (($pindex - 1) * $psize) . ',' . $psize, $params);
		$total = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_invoice') . ' where 1 ' . $condition, $params);
		$newList = array();
		if (is_array($list) && !empty($list)) {
			foreach ($list as $row) {
				$newList[] = array('id' => $row['id'], 'type' => $row['type'],'amount' => $row['amount'],  'raised' => $row['raised'], 'status' => $row['status'],'createTime' => date('Y-m-d H:i', $row['createTime']));
			}
		}

		app_json(array('list' => $newList, 'total' => $total, 'pagesize' => $psize, 'page' => $pindex, 'type' => $type));
	}
}

?>
