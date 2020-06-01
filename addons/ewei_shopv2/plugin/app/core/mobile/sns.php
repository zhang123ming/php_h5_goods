<?php
if (!(defined('IN_IA'))) {
	exit('Access Denied');
}

require EWEI_SHOPV2_PLUGIN . 'app/core/page_mobile.php';
class Sns_EweiShopV2Page extends AppMobilePage
{
	public function main()
	{
		global $_GPC;
		global $_W;

		$openid = $_W['openid'];
		$uniacid = $_W['uniacid'];
		$shop = m('common')->getSysset('shop');
		$advs = pdo_fetchall('select id,advname,link,thumb from ' . tablename('ewei_shop_sns_adv') . ' where uniacid=:uniacid and enabled=1 order by displayorder desc', array(':uniacid' => $uniacid));
		$member = pdo_fetchall('select * from ' . tablename('ewei_shop_sns_member') . ' where uniacid=:uniacid and openid=:openid ', array(':uniacid' => $uniacid,':openid' => $openid));
		if(empty($member)){
			$data['openid'] = $openid;
			$data['uniacid'] = $uniacid;
			$data['createtime'] = time();
			$data['isblack'] = 0;
			pdo_insert('ewei_shop_sns_member', $data);

		}
		$credit = m('member')->getCredit($openid, 'credit1');
		$category = pdo_fetchall('select id,`name`,thumb,isrecommand from ' . tablename('ewei_shop_sns_category') . ' where uniacid=:uniacid and isrecommand = 1 and enabled=1 order by displayorder desc', array(':uniacid' => $uniacid));
		$recommands = pdo_fetchall('select sb.id,sb.title,sb.logo,sb.`desc`  from ' . tablename('ewei_shop_sns_board') . " as sb\r\n\t\t\t\t\t\tleft join " . tablename('ewei_shop_sns_category') . " as sc on sc.id = sb.cid\r\n\t\t\t\t\t\twhere sb.uniacid=:uniacid and sb.isrecommand=1 and sb.status=1 and sc.enabled = 1 order by sb.displayorder desc", array(':uniacid' => $uniacid));
		foreach ($recommands as $k => $v) {
			$recommands[$k]['followcount'] = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_sns_board_follow') . ' where uniacid=:uniacid and bid=:bid ', array(':bid' => $v['id'],':uniacid' => $uniacid));
			$recommands[$k]['postcount'] = pdo_fetchcolumn('select count(*) from ' . tablename('ewei_shop_sns_post') . ' where uniacid=:uniacid and bid=:bid and pid = 0 and deleted=0 and checked=1 ', array(':bid' => $v['id'],'uniacid' => $uniacid));
		}
		app_json(array('rec' => $recommands,'cate' => $category,'advs' => $advs));
	}
}