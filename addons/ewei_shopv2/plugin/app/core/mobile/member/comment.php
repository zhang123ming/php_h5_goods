<?php
if (!(defined('IN_IA'))) {
	exit('Access Denied');
}


require EWEI_SHOPV2_PLUGIN . 'app/core/page_mobile.php';
class Comment_EweiShopV2Page extends AppMobilePage
{
	public function get_list()
	{
		global $_W;
		global $_GPC;

		if (empty($_W['openid'])) {
			app_error(AppError::$ParamsError);
		}


		$limit = '';
		$page = intval($_GPC['page']);

		if (1 < $page) {
			$pindex = max(1, $page);
			$psize = 20;
			$limit = ' LIMIT ' . (($pindex - 1) * $psize) . ',' . $psize;
		}

		if($_W['shopset']['trade']['commentchecked'] == '1'){
			$ischecked = 1;
		} else {
			$ischecked = 0;
		}

		$condition = ' and openid=:openid and  `uniacid` = :uniacid ';
		$params = array(':uniacid' => $_W['uniacid'], ':openid' => $_W['openid']);
		$sql = 'SELECT COUNT(*) FROM ' . tablename('ewei_shop_order_comment') . ' where 1 ' . $condition;
		$total = pdo_fetchcolumn($sql, $params);
		$sql = 'SELECT * FROM ' . tablename('ewei_shop_order_comment') . ' where 1 ' . $condition . ' ORDER BY `createtime` DESC ' . $limit;
		$list = pdo_fetchall($sql, $params);
		foreach ($list as $key => $value) {
			$order = pdo_fetch('SELECT ordersn FROM ' . tablename('ewei_shop_order') . ' where openid=:openid and  `uniacid` = :uniacid and id =:id ',array(':uniacid'=>$_W['uniacid'],':openid'=>$_W['openid'],":id"=>$value['orderid']));
			$goods = pdo_fetch('SELECT title,thumb FROM ' . tablename('ewei_shop_goods') . ' where `uniacid` = :uniacid and id =:id ',array(':uniacid'=>$_W['uniacid'],":id"=>$value['goodsid']));
			$num = pdo_fetch('SELECT total,optionname,price FROM ' . tablename('ewei_shop_order_goods') . ' where `uniacid` = :uniacid and orderid =:orderid and goodsid=:goodsid ',array(':uniacid'=>$_W['uniacid'],":orderid"=>$value['orderid'],':goodsid'=>$value['goodsid']));
			$list[$key]['createtime'] = date('Y-m-d H:i:s', $value['createtime']);
			$list[$key]['images'] = set_medias(iunserializer($value['images']));
			$list[$key]['reply_images'] = set_medias(iunserializer($value['reply_images']));
			$list[$key]['append_images'] = set_medias(iunserializer($value['append_images']));
			$list[$key]['append_reply_images'] = set_medias(iunserializer($value['append_reply_images']));
			$list[$key]['ordersn'] = $order['ordersn'];
			$list[$key]['goodstitle'] = $goods['title'];
			$list[$key]['goodsthumb'] = $goods['thumb'];
			$list[$key]['total'] = $num['total'];
			$list[$key]['optionname'] = $num['optionname'];
			$list[$key]['price'] = $num['price'];
		}

		app_json(array('page' => $pindex, 'pagesize' => $psize, 'total' => $total, 'list' => $list,'ischecked' => $ischecked));
	}
}


?>