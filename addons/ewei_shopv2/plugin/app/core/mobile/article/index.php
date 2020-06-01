<?php 
if (!defined('IN_IA')) {
	exit('Access Denied');
}

/**
 * 文章类
 */
require EWEI_SHOPV2_PLUGIN . 'app/core/page_mobile.php';
class Index_EweiShopV2Page extends AppMobilePage
{
	
	public function main()
	{
		global $_W,$_GPC;

		$page = intval($_GPC['page']);
	
		$pindex = max(1, $page);
		$psize = 10;

		$articles = pdo_fetchall('SELECT a.id, a.article_title, a.resp_img, a.article_rule_credit, a.article_rule_money, a.resp_desc, a.article_category FROM ' . tablename('ewei_shop_article') . ' a left join ' . tablename('ewei_shop_article_category') . ' c on c.id=a.article_category  WHERE a.article_state=1 and article_visit=0 and c.isshow=1 and a.uniacid= :uniacid order by a.displayorder desc, a.article_date desc LIMIT ' . (($pindex - 1) * $psize) . ',' . $psize, array(':uniacid' => $_W['uniacid']));

		$total =pdo_fetchcolumn('SELECT count(*) FROM '.tablename('ewei_shop_article') . ' a left join ' . tablename('ewei_shop_article_category') . ' c on c.id=a.article_category  WHERE a.article_state=1 and article_visit=0 and c.isshow=1 and a.uniacid= :uniacid', array(':uniacid' => $_W['uniacid']));

		$result=array(
			'total'=>$total,
			'list'=>$articles
		);
		app_json($result);
	}

	public function getDetail()
	{
		global $_W,$_GPC;

		$id =intval($_GPC[id]);
		if (empty($id)) {
			app_error('-2000',"参数不正确");
		}

		$article = pdo_fetch('SELECT * FROM ' . tablename('ewei_shop_article') . ' WHERE id=:aid and article_state=1 and uniacid=:uniacid limit 1 ', array(':aid' => $id, ':uniacid' => $_W['uniacid']));

		if (empty($article)) {
			app_error('-2002','文章不存在!');
		}

		$result=array('article' =>$article);
		app_json($result);

	}
}




 ?>