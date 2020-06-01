<?php
if (!(defined('IN_IA'))) {
	exit('Access Denied');
}

require EWEI_SHOPV2_PLUGIN . 'app/core/page_mobile.php';
class Cates_EweiShopV2Page extends AppMobilePage
{
	public function index()
	{
		global $_W;
		global $_GPC;
		$uniacid = $_W['uniacid'];
		$id = $_GPC['id'];

		$category = pdo_fetchall('select id,`name`,isrecommand from ' . tablename('ewei_shop_sns_category') . ' where uniacid=:uniacid and enabled=1 order by displayorder desc', array(':uniacid' => $uniacid));
		$recommands = pdo_fetchall('select sb.cid,sb.isrecommand,sb.id,sb.title,sb.logo,sb.`desc`  from ' . tablename('ewei_shop_sns_board') . " as sb\r\n\t\t\t\t\t\tleft join " . tablename('ewei_shop_sns_category') . " as sc on sc.id = sb.cid\r\n\t\t\t\t\t\twhere sb.uniacid=:uniacid and sb.status=1 and sc.enabled = 1 order by sb.displayorder desc", array(':uniacid' => $uniacid));
		foreach ($recommands as $k => $v) {
			$recommands[$k]['followcount'] = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_sns_board_follow') . ' where uniacid=:uniacid and bid=:bid ', array(':bid' => $v['id'],':uniacid' => $uniacid));
			$recommands[$k]['postcount'] = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_sns_post') . ' where uniacid=:uniacid and bid=:bid and pid = 0 and deleted=0 and checked=1 ', array(':bid' => $v['id'],'uniacid' => $uniacid));
		}
		app_json(array('cate' => $category,'rec' => $recommands,'id' => $id));
	}
}